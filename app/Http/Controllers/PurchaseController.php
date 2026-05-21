<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use App\Services\PurchaseService;
use App\Services\MedicineService;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    protected PurchaseService $purchaseService;
    protected MedicineService $medicineService;

    public function __construct(PurchaseService $purchaseService, MedicineService $medicineService)
    {
        $this->purchaseService = $purchaseService;
        $this->medicineService = $medicineService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['status', 'payment_status', 'from_date', 'to_date', 'per_page']);
        $purchases = $this->purchaseService->getPurchases($filters);

        return view('purchases.index', compact('purchases'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $medicines = $this->medicineService->getMedicines(['per_page' => 100]);
        $suppliers = \App\Models\Supplier::active()->orderBy('name')->get();

        return view('purchases.create', compact('medicines', 'suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PurchaseRequest $request)
    {
        try {
            $purchase = $this->purchaseService->createPurchase(
                $request->validated(),
                auth()->id()
            );
            
            return redirect()->route('purchases.index')
                ->with('success', 'Pembelian berhasil dibuat');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $purchase = $this->purchaseService->getPurchaseById($id);
        
        if (!$purchase) {
            return redirect()->route('purchases.index')
                ->with('error', 'Pembelian tidak ditemukan');
        }

        return view('purchases.show', compact('purchase'));
    }

    /**
     * Receive purchase (update stock)
     */
    public function receive(int $id)
    {
        $purchase = $this->purchaseService->getPurchaseById($id);
        
        if (!$purchase) {
            return redirect()->route('purchases.index')
                ->with('error', 'Pembelian tidak ditemukan');
        }

        try {
            $this->purchaseService->receivePurchase($purchase, auth()->id());
            
            return redirect()->route('purchases.index')
                ->with('success', 'Pembelian berhasil diterima dan stok diperbarui');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Cancel purchase
     */
    public function cancel(int $id)
    {
        $purchase = $this->purchaseService->getPurchaseById($id);
        
        if (!$purchase) {
            return redirect()->route('purchases.index')
                ->with('error', 'Pembelian tidak ditemukan');
        }

        try {
            $this->purchaseService->cancelPurchase($purchase, auth()->id());
            
            return redirect()->route('purchases.index')
                ->with('success', 'Pembelian berhasil dibatalkan');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
