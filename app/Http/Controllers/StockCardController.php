<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class StockCardController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $categoryId = $request->get('category_id');

        $medicines = Medicine::with(['category', 'unit'])
            ->active()
            ->when($search, fn($q, $s) => $q->search($s))
            ->when($categoryId, fn($q, $c) => $q->where('category_id', $c))
            ->orderBy('name')
            ->paginate(20);

        $categories = \App\Models\Category::active()->orderBy('name')->get();

        return view('stock-card.index', compact('medicines', 'categories', 'search', 'categoryId'));
    }

    public function show(Request $request, int $medicineId)
    {
        $medicine = Medicine::with(['category', 'unit'])->findOrFail($medicineId);

        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');

        $query = StockMovement::with('user')
            ->where('medicine_id', $medicineId)
            ->when($fromDate, fn($q, $d) => $q->whereDate('created_at', '>=', $d))
            ->when($toDate, fn($q, $d) => $q->whereDate('created_at', '<=', $d));

        $stockIn = (clone $query)->where('type', 'in')->sum('quantity');
        $stockOut = (clone $query)->where('type', 'out')->sum('quantity');

        $movements = $query->orderBy('created_at')->paginate(50);

        $totalStock = $medicine->total_stock;

        $batches = $medicine->batches()
            ->where('quantity', '>', 0)
            ->orWhere(function ($q) {
                $q->where('is_active', true);
            })
            ->orderBy('expired_date')
            ->get();

        return view('stock-card.show', compact(
            'medicine', 'movements', 'stockIn', 'stockOut',
            'totalStock', 'batches', 'fromDate', 'toDate'
        ));
    }
}
