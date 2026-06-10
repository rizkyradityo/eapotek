<?php

namespace App\Http\Controllers;

use App\Services\StockOpnameService;
use Illuminate\Http\Request;

class StockOpnameController extends Controller
{
    protected StockOpnameService $stockOpnameService;

    public function __construct(StockOpnameService $stockOpnameService)
    {
        $this->stockOpnameService = $stockOpnameService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['status', 'from_date', 'to_date', 'per_page']);
        $opnames = $this->stockOpnameService->getOpnames($filters);

        return view('stock-opname.index', compact('opnames', 'filters'));
    }

    public function create()
    {
        return view('stock-opname.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'opname_date' => 'required|date',
            'description' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $opname = $this->stockOpnameService->createOpname($data, auth()->id());

            return redirect()->route('stock-opname.show', $opname->id)
                ->with('success', 'Stock opname berhasil dibuat');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function show(int $id)
    {
        try {
            $opname = $this->stockOpnameService->getOpnameById($id);

            return view('stock-opname.show', compact('opname'));
        } catch (\Exception $e) {
            return redirect()->route('stock-opname.index')
                ->with('error', 'Stock opname tidak ditemukan');
        }
    }

    public function updateItem(Request $request, int $id)
    {
        $data = $request->validate([
            'item_id' => 'required|exists:stock_opname_items,id',
            'actual_quantity' => 'required|integer|min:0',
            'notes' => 'nullable|string|max:255',
        ]);

        try {
            $this->stockOpnameService->updateItem(
                $data['item_id'],
                $data['actual_quantity'],
                $data['notes'] ?? null
            );

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function apply(int $id)
    {
        try {
            $this->stockOpnameService->applyAdjustments($id, auth()->id());

            return redirect()->route('stock-opname.show', $id)
                ->with('success', 'Adjustment stok berhasil diterapkan');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function cancel(int $id)
    {
        try {
            $this->stockOpnameService->cancelOpname($id);

            return redirect()->route('stock-opname.index')
                ->with('success', 'Stock opname berhasil dibatalkan');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
