<?php

namespace App\Services;

use App\Models\Medicine;
use App\Models\MedicineBatch;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockService
{
    /**
     * Add stock (purchase/stock in)
     */
    public function addStock(
        int $medicineId,
        int $quantity,
        string $batchNumber,
        string $expiredDate,
        float $purchasePrice,
        float $sellingPrice,
        int $userId,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $notes = null
    ): MedicineBatch {
        return DB::transaction(function () use (
            $medicineId, $quantity, $batchNumber, $expiredDate,
            $purchasePrice, $sellingPrice, $userId, $referenceType, $referenceId, $notes
        ) {
            // Get or create batch
            $batch = MedicineBatch::where('medicine_id', $medicineId)
                ->where('batch_number', $batchNumber)
                ->first();

            $previousStock = $batch ? $batch->quantity : 0;

            if ($batch) {
                $batch->update([
                    'quantity' => $batch->quantity + $quantity,
                    'purchase_price' => $purchasePrice,
                    'selling_price' => $sellingPrice,
                ]);
            } else {
                $batch = MedicineBatch::create([
                    'medicine_id' => $medicineId,
                    'batch_number' => $batchNumber,
                    'expired_date' => $expiredDate,
                    'quantity' => $quantity,
                    'purchase_price' => $purchasePrice,
                    'selling_price' => $sellingPrice,
                ]);
            }

            // Record stock movement
            $this->recordMovement(
                $medicineId,
                $batch->id,
                'in',
                $quantity,
                $previousStock,
                $previousStock + $quantity,
                $userId,
                $referenceType,
                $referenceId,
                $notes ?? 'Stock addition'
            );

            Log::info('Stock added', [
                'medicine_id' => $medicineId,
                'batch' => $batchNumber,
                'quantity' => $quantity,
                'user_id' => $userId,
            ]);

            return $batch;
        });
    }

    /**
     * Reduce stock (sale/stock out)
     */
    public function reduceStock(
        int $medicineId,
        int $quantity,
        int $batchId,
        int $userId,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $notes = null
    ): MedicineBatch {
        return DB::transaction(function () use (
            $medicineId, $quantity, $batchId, $userId, $referenceType, $referenceId, $notes
        ) {
            $batch = MedicineBatch::findOrFail($batchId);
            
            if ($batch->quantity < $quantity) {
                throw new \InvalidArgumentException(
                    "Insufficient stock. Available: {$batch->quantity}, Requested: {$quantity}"
                );
            }

            $previousStock = $batch->quantity;
            $newStock = $previousStock - $quantity;

            $batch->update(['quantity' => $newStock]);

            // Deactivate batch if empty
            if ($newStock === 0) {
                $batch->update(['is_active' => false]);
            }

            // Record stock movement
            $this->recordMovement(
                $medicineId,
                $batchId,
                'out',
                $quantity,
                $previousStock,
                $newStock,
                $userId,
                $referenceType,
                $referenceId,
                $notes ?? 'Stock reduction'
            );

            Log::info('Stock reduced', [
                'medicine_id' => $medicineId,
                'batch_id' => $batchId,
                'quantity' => $quantity,
                'user_id' => $userId,
            ]);

            return $batch->fresh();
        });
    }

    /**
     * Adjust stock manually
     */
    public function adjustStock(
        int $medicineId,
        int $newQuantity,
        int $userId,
        string $reason
    ): MedicineBatch {
        return DB::transaction(function () use ($medicineId, $newQuantity, $userId, $reason) {
            // Get first active batch or create new one
            $batch = MedicineBatch::where('medicine_id', $medicineId)
                ->active()
                ->notExpired()
                ->orderBy('expired_date')
                ->first();

            if (!$batch) {
                throw new \InvalidArgumentException('No active batch found for this medicine');
            }

            $previousStock = $batch->quantity;
            $difference = $newQuantity - $previousStock;

            $batch->update(['quantity' => $newQuantity]);

            if ($newQuantity === 0) {
                $batch->update(['is_active' => false]);
            }

            // Record stock movement
            $this->recordMovement(
                $medicineId,
                $batch->id,
                $difference > 0 ? 'in' : 'out',
                abs($difference),
                $previousStock,
                $newQuantity,
                $userId,
                'adjustment',
                null,
                $reason
            );

            Log::info('Stock adjusted', [
                'medicine_id' => $medicineId,
                'previous' => $previousStock,
                'new' => $newQuantity,
                'user_id' => $userId,
            ]);

            return $batch->fresh();
        });
    }

    /**
     * Get total stock for a medicine
     */
    public function getTotalStock(int $medicineId): int
    {
        return MedicineBatch::where('medicine_id', $medicineId)
            ->where('is_active', true)
            ->sum('quantity');
    }

    /**
     * Get available batches for a medicine
     */
    public function getAvailableBatches(int $medicineId): \Illuminate\Database\Eloquent\Collection
    {
        return MedicineBatch::where('medicine_id', $medicineId)
            ->active()
            ->notExpired()
            ->where('quantity', '>', 0)
            ->orderBy('expired_date')
            ->get();
    }

    /**
     * Get stock movements with filters
     */
    public function getStockMovements(array $filters = [])
    {
        $query = StockMovement::with(['medicine', 'batch', 'user'])
            ->when($filters['medicine_id'] ?? null, fn($q, $id) => 
                $q->where('medicine_id', $id)
            )
            ->when($filters['type'] ?? null, fn($q, $type) => 
                $q->where('type', $type)
            )
            ->when($filters['from_date'] ?? null, fn($q, $date) => 
                $q->whereDate('created_at', '>=', $date)
            )
            ->when($filters['to_date'] ?? null, fn($q, $date) => 
                $q->whereDate('created_at', '<=', $date)
            );

        return $query->orderByDesc('created_at')->paginate($filters['per_page'] ?? 20);
    }

    /**
     * Record stock movement
     */
    private function recordMovement(
        int $medicineId,
        ?int $batchId,
        string $type,
        int $quantity,
        int $previousStock,
        int $newStock,
        int $userId,
        ?string $referenceType,
        ?int $referenceId,
        string $notes
    ): StockMovement {
        return StockMovement::create([
            'medicine_id' => $medicineId,
            'medicine_batch_id' => $batchId,
            'type' => $type,
            'quantity' => $quantity,
            'previous_stock' => $previousStock,
            'new_stock' => $newStock,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'notes' => $notes,
            'user_id' => $userId,
        ]);
    }
}
