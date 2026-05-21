<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Medicine;
use App\Models\MedicineBatch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionService
{
    protected StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Create a new sale transaction
     */
    public function createSale(array $data, int $userId): Sale
    {
        return DB::transaction(function () use ($data, $userId) {
            // Validate stock availability for all items
            $this->validateStockAvailability($data['items']);

            // Generate invoice number
            $invoiceNumber = $this->generateInvoiceNumber();

            // Create sale record
            $sale = Sale::create([
                'invoice_number' => $invoiceNumber,
                'user_id' => $userId,
                'customer_id' => $data['customer_id'] ?? null,
                'subtotal' => 0, // Will be calculated
                'discount_percent' => $data['discount_percent'] ?? 0,
                'discount_amount' => $data['discount_amount'] ?? 0,
                'tax_percent' => $data['tax_percent'] ?? 0,
                'tax_amount' => 0, // Will be calculated
                'total' => 0, // Will be calculated
                'paid_amount' => $data['paid_amount'] ?? 0,
                'change_amount' => 0, // Will be calculated
                'payment_method' => $data['payment_method'] ?? 'cash',
                'status' => 'completed',
                'notes' => $data['notes'] ?? null,
            ]);

            // Create sale items and reduce stock
            $itemsData = $this->prepareSaleItems($sale, $data['items']);

            foreach ($itemsData as $itemData) {
                // Create sale item
                $saleItem = SaleItem::create($itemData);

                // Reduce stock
                $this->stockService->reduceStock(
                    $itemData['medicine_id'],
                    $itemData['quantity'],
                    $itemData['medicine_batch_id'],
                    $userId,
                    'sale',
                    $sale->id,
                    "Sale {$invoiceNumber}"
                );
            }

            // Calculate totals
            $sale->load('items');
            $sale->calculateTotals();
            $sale->save();

            Log::info('Sale created', [
                'sale_id' => $sale->id,
                'invoice_number' => $invoiceNumber,
                'total' => $sale->total,
                'items_count' => $sale->items->count(),
                'user_id' => $userId,
            ]);

            return $sale->load(['items.medicine', 'items.batch', 'user']);
        });
    }

    /**
     * Cancel a sale transaction
     */
    public function cancelSale(Sale $sale, int $userId): Sale
    {
        return DB::transaction(function () use ($sale, $userId) {
            if ($sale->status === 'cancelled') {
                throw new \InvalidArgumentException('Sale is already cancelled');
            }

            // Restore stock for each item
            foreach ($sale->items as $item) {
                // Find the batch and restore stock
                $batch = MedicineBatch::find($item->medicine_batch_id);
                
                if ($batch && $batch->is_active) {
                    $batch->update(['quantity' => $batch->quantity + $item->quantity]);
                } else {
                    // Reactivate batch if it was deactivated
                    MedicineBatch::create([
                        'medicine_id' => $item->medicine_id,
                        'batch_number' => $batch ? $batch->batch_number : 'CANCELLED-' . time(),
                        'expired_date' => $batch ? $batch->expired_date : now()->addYear(),
                        'quantity' => $item->quantity,
                        'purchase_price' => $batch ? $batch->purchase_price : 0,
                        'selling_price' => $item->unit_price,
                        'is_active' => true,
                    ]);
                }

                // Record stock movement for restoration
                \App\Models\StockMovement::create([
                    'medicine_id' => $item->medicine_id,
                    'medicine_batch_id' => $item->medicine_batch_id,
                    'type' => 'return',
                    'quantity' => $item->quantity,
                    'previous_stock' => $batch ? $batch->quantity - $item->quantity : 0,
                    'new_stock' => $batch ? $batch->quantity : $item->quantity,
                    'reference_type' => 'sale_cancelled',
                    'reference_id' => $sale->id,
                    'notes' => "Sale cancelled - Invoice: {$sale->invoice_number}",
                    'user_id' => $userId,
                ]);
            }

            // Update sale status
            $sale->update([
                'status' => 'cancelled',
                'notes' => ($sale->notes ?? '') . "\nCancelled by user ID: {$userId}",
            ]);

            Log::info('Sale cancelled', [
                'sale_id' => $sale->id,
                'invoice_number' => $sale->invoice_number,
                'user_id' => $userId,
            ]);

            return $sale->fresh();
        });
    }

    /**
     * Get sale by ID
     */
    public function getSaleById(int $id): ?Sale
    {
        return Sale::with(['items.medicine', 'items.batch', 'user', 'customer'])
            ->find($id);
    }

    /**
     * Get sales with filters
     */
    public function getSales(array $filters = [])
    {
        $query = Sale::with(['user', 'customer'])
            ->when($filters['status'] ?? null, fn($q, $status) => 
                $q->where('status', $status)
            )
            ->when($filters['from_date'] ?? null, fn($q, $date) => 
                $q->whereDate('created_at', '>=', $date)
            )
            ->when($filters['to_date'] ?? null, fn($q, $date) => 
                $q->whereDate('created_at', '<=', $date)
            )
            ->when($filters['user_id'] ?? null, fn($q, $userId) => 
                $q->where('user_id', $userId)
            );

        return $query->orderByDesc('created_at')->paginate($filters['per_page'] ?? 20);
    }

    /**
     * Validate stock availability for all items
     */
    protected function validateStockAvailability(array $items): void
    {
        foreach ($items as $item) {
            $batch = MedicineBatch::find($item['batch_id']);
            
            if (!$batch) {
                throw new \InvalidArgumentException("Batch not found for medicine ID: {$item['medicine_id']}");
            }

            if ($batch->quantity < $item['quantity']) {
                $medicine = Medicine::find($item['medicine_id']);
                throw new \InvalidArgumentException(
                    "Insufficient stock for {$medicine->name}. Available: {$batch->quantity}, Requested: {$item['quantity']}"
                );
            }
        }
    }

    /**
     * Prepare sale items data
     */
    protected function prepareSaleItems(Sale $sale, array $items): array
    {
        $result = [];

        foreach ($items as $item) {
            $batch = MedicineBatch::findOrFail($item['batch_id']);
            $medicine = Medicine::findOrFail($item['medicine_id']);
            
            $unitPrice = $item['unit_price'] ?? $batch->selling_price;
            $quantity = $item['quantity'];
            $discountPercent = $item['discount_percent'] ?? 0;
            $discountAmount = $item['discount_amount'] ?? 0;
            
            $subtotal = ($unitPrice * $quantity) - 
                (($unitPrice * $quantity * $discountPercent / 100) + $discountAmount);

            $result[] = [
                'sale_id' => $sale->id,
                'medicine_id' => $item['medicine_id'],
                'medicine_batch_id' => $item['batch_id'],
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'discount_percent' => $discountPercent,
                'discount_amount' => $discountAmount,
                'subtotal' => $subtotal,
            ];
        }

        return $result;
    }

    /**
     * Generate unique invoice number
     */
    protected function generateInvoiceNumber(): string
    {
        $prefix = 'INV';
        $date = now()->format('Ymd');
        $lastSale = Sale::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastSale 
            ? (int) substr($lastSale->invoice_number, -4) + 1 
            : 1;
        
        return $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
