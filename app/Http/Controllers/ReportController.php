<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Dashboard
     */
    public function dashboard()
    {
        $summary = $this->reportService->getDashboardSummary();
        
        return view('dashboard', compact('summary'));
    }

    /**
     * Sales report
     */
    public function sales(Request $request)
    {
        $filters = $request->only(['from_date', 'to_date', 'per_page']);
        $report = $this->reportService->getSalesReport($filters);
        $topMedicines = $this->reportService->getTopSellingMedicines($filters + ['limit' => 10]);

        return view('reports.sales', compact('report', 'topMedicines', 'filters'));
    }

    /**
     * Stock report
     */
    public function stock()
    {
        $report = $this->reportService->getStockReport();
        
        return view('reports.stock', compact('report'));
    }

    /**
     * Stock movement report
     */
    public function stockMovement(Request $request)
    {
        $filters = $request->only(['medicine_id', 'type', 'from_date', 'to_date', 'per_page']);
        $report = $this->reportService->getStockMovementReport($filters);
        $medicines = \App\Models\Medicine::active()->orderBy('name')->get();

        return view('reports.stock-movement', compact('report', 'medicines', 'filters'));
    }

    /**
     * Export sales report
     */
    public function exportSales(Request $request)
    {
        $filters = $request->only(['from_date', 'to_date']);
        $report = $this->reportService->getSalesReport($filters);
        
        // Return as JSON for now - can be extended to Excel/PDF
        return response()->json($report);
    }

    /**
     * Export stock report
     */
    public function exportStock()
    {
        $report = $this->reportService->getStockReport();
        
        return response()->json($report);
    }
}
