<?php

namespace App\Services;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchaseService
{
    protected StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Create a new purchase order
     */
    public function createPurchase(array $data, int $userId): Purchase
    {
        return DB::transaction(function () use ($data, $userId) {
            // Generate invoice number
            $invoiceNumber = $this->generateInvoiceNumber();

            // Create purchase record
            $purchase = Purchase::create([
                'invoice_number' => $invoiceNumber,
                'user_id' => $userId,
                'supplier_id' => $data['supplier_id'] ?? null,
                'subtotal' => 0,
                'discount_amount' => $data['discount_amount'] ?? 0,
                'tax_amount' => $data['tax_amount'] ?? 0,
                'total' => 0,
                'paid_amount' => $data['paid_amount'] ?? 0,
                'payment_status' => ($data['paid_amount'] ?? 0) >= ($data['total'] ?? 0) ? 'paid' : 'pending',
                'status' => 'pending',
                'purchase_date' => $data['purchase_date'] ?? today(),
                'notes' => $data['notes'] ?? null,
            ]);

            // Create purchase items
            foreach ($data['items'] as $item) {
                $unitPrice = $item['unit_price'] ?? 0;
                $quantity = $item['quantity'];
                
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'medicine_id' => $item['medicine_id'],
                    'batch_number' => $item['batch_number'],
                    'expired_date' => $item['expired_date'],
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $unitPrice * $quantity,
                ]);
            }

            // Calculate totals
            $purchase->load('items');
            $purchase->calculateTotals();
            $purchase->save();

            Log::info('Purchase created', [
                'purchase_id' => $purchase->id,
                'invoice_number' => $invoiceNumber,
                'total' => $purchase->total,
                'user_id' => $userId,
            ]);

            return $purchase->load(['items.medicine', 'supplier', 'user']);
        });
    }

    /**
     * Receive purchase (update stock)
     */
    public function receivePurchase(Purchase $purchase, int $userId): Purchase
    {
        return DB::transaction(function () use ($purchase, $userId) {
            if ($purchase->status === 'received') {
                throw new \InvalidArgumentException('Purchase is already received');
            }

            if ($purchase->status === 'cancelled') {
                throw new \InvalidArgumentException('Cannot receive cancelled purchase');
            }

            // Add stock for each item
            foreach ($purchase->items as $item) {
                $this->stockService->addStock(
                    $item->medicine_id,
                    $item->quantity,
                    $item->batch_number,
                    $item->expired_date->toDateString(),
                    $item->unit_price,
                    $this->calculateSellingPrice($item->unit_price),
                    $userId,
                    'purchase',
                    $purchase->id,
                    "Purchase {$purchase->invoice_number}"
                );
            }

            // Update purchase status
            $purchase->update([
                'status' => 'received',
                'receive_date' => today(),
            ]);

            Log::info('Purchase received', [
                'purchase_id' => $purchase->id,
                'invoice_number' => $purchase->invoice_number,
                'user_id' => $userId,
            ]);

            return $purchase->fresh();
        });
    }

    /**
     * Cancel purchase
     */
    public function cancelPurchase(Purchase $purchase, int $userId): Purchase
    {
        return DB::transaction(function () use ($purchase, $userId) {
            if ($purchase->status === 'cancelled') {
                throw new \InvalidArgumentException('Purchase is already cancelled');
            }

            if ($purchase->status === 'received') {
                throw new \InvalidArgumentException('Cannot cancel received purchase');
            }

            $purchase->update([
                'status' => 'cancelled',
                'notes' => ($purchase->notes ?? '') . "\nCancelled by user ID: {$userId}",
            ]);

            Log::info('Purchase cancelled', [
                'purchase_id' => $purchase->id,
                'invoice_number' => $purchase->invoice_number,
                'user_id' => $userId,
            ]);

            return $purchase->fresh();
        });
    }

    /**
     * Get purchase by ID
     */
    public function getPurchaseById(int $id): ?Purchase
    {
        return Purchase::with(['items.medicine', 'supplier', 'user'])
            ->find($id);
    }

    /**
     * Get purchases with filters
     */
    public function getPurchases(array $filters = [])
    {
        $query = Purchase::with(['supplier', 'user'])
            ->when($filters['status'] ?? null, fn($q, $status) => 
                $q->where('status', $status)
            )
            ->when($filters['payment_status'] ?? null, fn($q, $status) => 
                $q->where('payment_status', $status)
            )
            ->when($filters['from_date'] ?? null, fn($q, $date) => 
                $q->whereDate('purchase_date', '>=', $date)
            )
            ->when($filters['to_date'] ?? null, fn($q, $date) => 
                $q->whereDate('purchase_date', '<=', $date)
            )
            ->when($filters['supplier_id'] ?? null, fn($q, $supplierId) => 
                $q->where('supplier_id', $supplierId)
            );

        return $query->orderByDesc('created_at')->paginate($filters['per_page'] ?? 20);
    }

    /**
     * Calculate selling price (markup 20% default)
     */
    protected function calculateSellingPrice(float $purchasePrice): float
    {
        return round($purchasePrice * 1.20, 2);
    }

    /**
     * Generate unique invoice number
     */
    protected function generateInvoiceNumber(): string
    {
        $prefix = 'PO';
        $date = now()->format('Ymd');
        $lastPurchase = Purchase::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastPurchase 
            ? (int) substr($lastPurchase->invoice_number, -4) + 1 
            : 1;
        
        return $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
