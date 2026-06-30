<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\Purchase;
use App\Models\Sale;
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
            ->whereIn('reference_type', ['purchase', 'sale'])
            ->when($fromDate, fn($q, $d) => $q->whereDate('created_at', '>=', $d))
            ->when($toDate, fn($q, $d) => $q->whereDate('created_at', '<=', $d));

        $stockIn = (clone $query)->where('reference_type', 'purchase')->sum('quantity');
        $stockOut = (clone $query)->where('reference_type', 'sale')->sum('quantity');

        $movements = $query->orderBy('created_at', 'desc')->paginate(20);

        $totalStock = $medicine->total_stock;

        $purchaseIds = $movements->where('reference_type', 'purchase')->pluck('reference_id')->unique();
        $saleIds = $movements->where('reference_type', 'sale')->pluck('reference_id')->unique();

        $purchases = Purchase::with('supplier')->whereIn('id', $purchaseIds)->get()->keyBy('id');
        $sales = Sale::whereIn('id', $saleIds)->get()->keyBy('id');

        return view('stock-card.show', compact(
            'medicine', 'movements', 'stockIn', 'stockOut',
            'totalStock', 'fromDate', 'toDate', 'purchases', 'sales'
        ));
    }
}
