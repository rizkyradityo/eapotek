<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MedicineService;
use Illuminate\Http\Request;

class MedicineApiController extends Controller
{
    protected MedicineService $medicineService;

    public function __construct(MedicineService $medicineService)
    {
        $this->medicineService = $medicineService;
    }

    /**
     * Get medicines with filters
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'category_id', 'low_stock', 'per_page']);
        $medicines = $this->medicineService->getMedicines($filters);

        return response()->json([
            'success' => true,
            'data' => $medicines->items(),
            'meta' => [
                'current_page' => $medicines->currentPage(),
                'per_page' => $medicines->perPage(),
                'total' => $medicines->total(),
                'last_page' => $medicines->lastPage(),
            ],
        ]);
    }

    /**
     * Get medicine by ID
     */
    public function show($id)
    {
        $medicine = $this->medicineService->getMedicineById($id);

        if (!$medicine) {
            return response()->json([
                'success' => false,
                'message' => 'Medicine not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $medicine,
        ]);
    }

    /**
     * Get low stock medicines
     */
    public function lowStock()
    {
        $medicines = $this->medicineService->getLowStockMedicines();

        return response()->json([
            'success' => true,
            'data' => $medicines,
            'count' => $medicines->count(),
        ]);
    }

    /**
     * Get near expiry medicines
     */
    public function nearExpiry(Request $request)
    {
        $days = $request->get('days', 30);
        $medicines = $this->medicineService->getNearExpiryMedicines($days);

        return response()->json([
            'success' => true,
            'data' => $medicines,
            'days' => $days,
        ]);
    }
}
