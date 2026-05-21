<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaleRequest;
use App\Services\TransactionService;
use App\Services\StockService;
use App\Services\MedicineService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    protected TransactionService $transactionService;
    protected StockService $stockService;
    protected MedicineService $medicineService;

    public function __construct(
        TransactionService $transactionService,
        StockService $stockService,
        MedicineService $medicineService
    ) {
        $this->transactionService = $transactionService;
        $this->stockService = $stockService;
        $this->medicineService = $medicineService;
    }

    /**
     * Display POS page
     */
    public function pos()
    {
        return view('pos.index');
    }

    /**
     * Get available batches for a medicine (for POS)
     */
    public function getBatches(int $medicineId)
    {
        $batches = $this->stockService->getAvailableBatches($medicineId);
        
        return response()->json([
            'batches' => $batches->map(fn($b) => [
                'id' => $b->id,
                'batch_number' => $b->batch_number,
                'expired_date' => $b->expired_date->format('Y-m-d'),
                'quantity' => $b->quantity,
                'selling_price' => $b->selling_price,
            ]),
        ]);
    }

    /**
     * Store a new sale
     */
    public function store(SaleRequest $request)
    {
        try {
            $sale = $this->transactionService->createSale(
                $request->validated(),
                auth()->id()
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil',
                'data' => $sale,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Display list of sales
     */
    public function index(Request $request)
    {
        $filters = $request->only(['status', 'from_date', 'to_date', 'per_page']);
        $sales = $this->transactionService->getSales($filters);

        return view('transactions.index', compact('sales'));
    }

    /**
     * Display sale details
     */
    public function show(int $id)
    {
        $sale = $this->transactionService->getSaleById($id);
        
        if (!$sale) {
            return redirect()->route('transactions.index')
                ->with('error', 'Transaksi tidak ditemukan');
        }

        return view('transactions.show', compact('sale'));
    }

    /**
     * Cancel a sale
     */
    public function cancel(int $id)
    {
        $sale = $this->transactionService->getSaleById($id);
        
        if (!$sale) {
            return redirect()->route('transactions.index')
                ->with('error', 'Transaksi tidak ditemukan');
        }

        try {
            $this->transactionService->cancelSale($sale, auth()->id());
            
            return redirect()->route('transactions.index')
                ->with('success', 'Transaksi berhasil dibatalkan');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Search medicine for POS
     */
    public function searchMedicine(Request $request)
    {
        $search = $request->get('search', '');
        $medicines = $this->medicineService->getMedicines([
            'search' => $search,
            'per_page' => 20,
        ]);

        return response()->json([
            'medicines' => $medicines->map(fn($m) => [
                'id' => $m->id,
                'code' => $m->code,
                'name' => $m->name,
                'price' => $m->price,
                'unit' => $m->unit->symbol ?? '',
                'total_stock' => $m->total_stock,
            ]),
        ]);
    }

    /**
     * Print receipt
     */
    public function receipt(int $id)
    {
        $sale = $this->transactionService->getSaleById($id);
        
        if (!$sale) {
            return redirect()->route('transactions.index')
                ->with('error', 'Transaksi tidak ditemukan');
        }

        return view('transactions.receipt', compact('sale'));
    }
}
