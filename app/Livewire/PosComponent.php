<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\TransactionService;
use App\Services\StockService;
use App\Models\Medicine;
use App\Models\MedicineBatch;
use Illuminate\Support\Facades\DB;

class PosComponent extends Component
{
    public $search = '';
    public $cart = [];
    public $discount_percent = 0;
    public $discount_amount = 0;
    public $paid_amount = 0;
    public $payment_method = 'cash';
    public $notes = '';
    public $customer_id = null;

    protected TransactionService $transactionService;
    protected StockService $stockService;

    public function boot(TransactionService $transactionService, StockService $stockService)
    {
        $this->transactionService = $transactionService;
        $this->stockService = $stockService;
    }

    public function render()
    {
        $medicines = Medicine::active()
            ->when($this->search, fn($q) => $q->search($this->search))
            ->with(['category', 'unit', 'batches' => fn($q) => 
                $q->active()->notExpired()->where('quantity', '>', 0)
            ])
            ->limit(20)
            ->get();

        $subtotal = $this->calculateSubtotal();
        $discountTotal = $this->calculateDiscountTotal($subtotal);
        $total = $this->calculateTotal($subtotal, $discountTotal);
        $change = $this->calculateChange($total);

        return view('livewire.pos-component', [
            'medicines' => $medicines,
            'subtotal' => $subtotal,
            'discountTotal' => $discountTotal,
            'total' => $total,
            'change' => $change,
        ]);
    }

    public function addToCart($medicineId, $batchId = null)
    {
        $medicine = Medicine::with(['batches' => fn($q) => 
            $q->active()->notExpired()->where('quantity', '>', 0)
        ])->find($medicineId);

        if (!$medicine) {
            session()->flash('error', 'Obat tidak ditemukan');
            return;
        }

        // Get first available batch if not specified
        if (!$batchId && $medicine->batches->count() > 0) {
            $batchId = $medicine->batches->first()->id;
        }

        if (!$batchId) {
            session()->flash('error', 'Stok obat tidak tersedia');
            return;
        }

        $batch = MedicineBatch::find($batchId);
        
        if (!$batch || $batch->quantity <= 0) {
            session()->flash('error', 'Stok batch tidak tersedia');
            return;
        }

        // Check if item already in cart
        $cartKey = "{$medicineId}_{$batchId}";
        
        if (isset($this->cart[$cartKey])) {
            if ($this->cart[$cartKey]['quantity'] >= $batch->quantity) {
                session()->flash('error', 'Stok tidak mencukupi');
                return;
            }
            $this->cart[$cartKey]['quantity']++;
        } else {
            $this->cart[$cartKey] = [
                'medicine_id' => $medicine->id,
                'medicine_name' => $medicine->name,
                'batch_id' => $batch->id,
                'batch_number' => $batch->batch_number,
                'quantity' => 1,
                'unit_price' => $batch->selling_price,
                'discount_percent' => 0,
                'discount_amount' => 0,
            ];
        }
    }

    public function removeFromCart($key)
    {
        unset($this->cart[$key]);
    }

    public function updateQuantity($key, $quantity)
    {
        if ($quantity < 1) {
            $this->removeFromCart($key);
            return;
        }

        $item = $this->cart[$key];
        $batch = MedicineBatch::find($item['batch_id']);

        if ($batch && $quantity > $batch->quantity) {
            session()->flash('error', 'Stok tidak mencukupi');
            return;
        }

        $this->cart[$key]['quantity'] = $quantity;
    }

    public function clearCart()
    {
        $this->cart = [];
        $this->discount_percent = 0;
        $this->discount_amount = 0;
        $this->paid_amount = 0;
    }

    private function calculateSubtotal()
    {
        return collect($this->cart)->sum(function($item) {
            return ($item['unit_price'] * $item['quantity']) - 
                   (($item['unit_price'] * $item['quantity'] * $item['discount_percent'] / 100) + $item['discount_amount']);
        });
    }

    private function calculateDiscountTotal($subtotal)
    {
        $percent = (float) ($this->discount_percent ?: 0);
        $amount = (float) ($this->discount_amount ?: 0);
        $discountFromPercent = $subtotal * ($percent / 100);
        return $discountFromPercent + $amount;
    }

    private function calculateTotal($subtotal, $discountTotal)
    {
        return $subtotal - $discountTotal;
    }

    private function calculateChange($total)
    {
        $paid = (float) ($this->paid_amount ?: 0);
        return max(0, $paid - $total);
    }

    public function processPayment()
    {
        if (empty($this->cart)) {
            session()->flash('error', 'Keranjang kosong');
            return;
        }

        $subtotal = $this->calculateSubtotal();
        $discountTotal = $this->calculateDiscountTotal($subtotal);
        $total = $this->calculateTotal($subtotal, $discountTotal);
        $paid = (float) ($this->paid_amount ?: 0);

        if ($paid < $total) {
            session()->flash('error', 'Jumlah pembayaran kurang');
            return;
        }

        try {
            DB::beginTransaction();

            // Prepare items data
            $itemsData = [];
            foreach ($this->cart as $item) {
                $subtotal = ($item['unit_price'] * $item['quantity']) - 
                           (($item['unit_price'] * $item['quantity'] * $item['discount_percent'] / 100) + $item['discount_amount']);
                
                $itemsData[] = [
                    'medicine_id' => $item['medicine_id'],
                    'batch_id' => $item['batch_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount_percent' => $item['discount_percent'],
                    'discount_amount' => $item['discount_amount'],
                ];
            }

            $saleData = [
                'customer_id' => $this->customer_id,
                'items' => $itemsData,
                'discount_percent' => $this->discount_percent,
                'discount_amount' => $this->discount_amount,
                'paid_amount' => $this->paid_amount,
                'payment_method' => $this->payment_method,
                'notes' => $this->notes,
            ];

            $sale = $this->transactionService->createSale($saleData, auth()->id());

            DB::commit();

            session()->flash('success', 'Transaksi berhasil! No: ' . $sale->invoice_number);
            
            // Clear cart
            $this->clearCart();
            $this->notes = '';
            $this->customer_id = null;

            // Redirect to receipt
            return redirect()->route('transactions.show', $sale->id);

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', $e->getMessage());
        }
    }
}
