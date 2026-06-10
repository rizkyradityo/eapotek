<?php

namespace App\Services;

use App\Models\Medicine;
use App\Models\MedicineBatch;
use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockOpnameService
{
    public function __construct(
        protected StockService $stockService
    ) {}

    public function generateOpnameNumber(): string
    {
        $prefix = 'OPN-' . now()->format('Ymd');
        $last = StockOpname::where('opname_number', 'like', $prefix . '-%')
            ->orderByDesc('id')
            ->first();

        $number = $last ? (int) explode('-', $last->opname_number)[2] + 1 : 1;

        return $prefix . '-' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    public function createOpname(array $data, int $userId): StockOpname
    {
        return DB::transaction(function () use ($data, $userId) {
            $opname = StockOpname::create([
                'opname_number' => $this->generateOpnameNumber(),
                'opname_date' => $data['opname_date'],
                'description' => $data['description'] ?? null,
                'status' => 'draft',
                'user_id' => $userId,
                'notes' => $data['notes'] ?? null,
            ]);

            $medicines = Medicine::with(['batches' => function ($q) {
                $q->where('quantity', '>', 0)->orWhere(function ($q) {
                    $q->where('is_active', true);
                });
            }])->active()->orderBy('name')->get();

            $items = [];
            foreach ($medicines as $medicine) {
                foreach ($medicine->batches as $batch) {
                    $items[] = [
                        'stock_opname_id' => $opname->id,
                        'medicine_id' => $medicine->id,
                        'medicine_batch_id' => $batch->id,
                        'system_quantity' => $batch->quantity,
                        'actual_quantity' => $batch->quantity,
                        'difference' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if (!empty($items)) {
                StockOpnameItem::insert($items);
            }

            Log::info('Stock opname created', [
                'opname_id' => $opname->id,
                'items' => count($items),
                'user_id' => $userId,
            ]);

            return $opname->fresh();
        });
    }

    public function updateItem(int $itemId, int $actualQuantity, ?string $notes = null): StockOpnameItem
    {
        $item = StockOpnameItem::findOrFail($itemId);
        $opname = $item->stockOpname;

        if (!$opname->isDraft()) {
            throw new \InvalidArgumentException('Cannot update items in a non-draft opname');
        }

        $difference = $actualQuantity - $item->system_quantity;

        $item->update([
            'actual_quantity' => $actualQuantity,
            'difference' => $difference,
            'notes' => $notes,
        ]);

        return $item->fresh();
    }

    public function applyAdjustments(int $opnameId, int $userId): StockOpname
    {
        return DB::transaction(function () use ($opnameId, $userId) {
            $opname = StockOpname::with('items')->findOrFail($opnameId);

            if (!$opname->isDraft()) {
                throw new \InvalidArgumentException('Only draft opname can be applied');
            }

            $adjustmentCount = 0;
            foreach ($opname->items as $item) {
                if ($item->difference === 0) {
                    continue;
                }

                $batch = MedicineBatch::find($item->medicine_batch_id);
                if (!$batch) {
                    continue;
                }

                $previousStock = $batch->quantity;
                $newStock = $item->actual_quantity;

                $batch->update([
                    'quantity' => $newStock,
                    'is_active' => $newStock > 0,
                ]);

                StockMovement::create([
                    'medicine_id' => $item->medicine_id,
                    'medicine_batch_id' => $item->medicine_batch_id,
                    'type' => $item->difference > 0 ? 'in' : 'out',
                    'quantity' => abs($item->difference),
                    'previous_stock' => $previousStock,
                    'new_stock' => $newStock,
                    'reference_type' => 'opname',
                    'reference_id' => $opname->id,
                    'notes' => 'Stock opname: ' . ($item->notes ?? 'Penyesuaian stok opname'),
                    'user_id' => $userId,
                ]);

                $adjustmentCount++;
            }

            $opname->update(['status' => 'completed']);

            Log::info('Stock opname applied', [
                'opname_id' => $opname->id,
                'adjustments' => $adjustmentCount,
                'user_id' => $userId,
            ]);

            return $opname->fresh();
        });
    }

    public function cancelOpname(int $opnameId): StockOpname
    {
        $opname = StockOpname::findOrFail($opnameId);

        if (!$opname->isDraft()) {
            throw new \InvalidArgumentException('Only draft opname can be cancelled');
        }

        $opname->update(['status' => 'cancelled']);

        return $opname->fresh();
    }

    public function getOpnames(array $filters = [])
    {
        return StockOpname::with('user')
            ->when($filters['status'] ?? null, fn($q, $s) => $q->where('status', $s))
            ->when($filters['from_date'] ?? null, fn($q, $d) => $q->whereDate('opname_date', '>=', $d))
            ->when($filters['to_date'] ?? null, fn($q, $d) => $q->whereDate('opname_date', '<=', $d))
            ->orderByDesc('created_at')
            ->paginate($filters['per_page'] ?? 20);
    }

    public function getOpnameById(int $id): StockOpname
    {
        return StockOpname::with(['items.medicine.category', 'items.medicine.unit', 'items.batch', 'user'])
            ->findOrFail($id);
    }
}
