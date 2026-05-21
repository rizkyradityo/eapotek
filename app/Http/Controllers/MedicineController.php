<?php

namespace App\Http\Controllers;

use App\Http\Requests\MedicineRequest;
use App\Services\MedicineService;
use Illuminate\Http\Request;

class MedicineController extends Controller
{
    protected MedicineService $medicineService;

    public function __construct(MedicineService $medicineService)
    {
        $this->medicineService = $medicineService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return view('medicines.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = $this->medicineService->getCategories();
        $units = $this->medicineService->getUnits();
        $nextCode = $this->medicineService->generateMedicineCode();

        return view('medicines.create', compact('categories', 'units', 'nextCode'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MedicineRequest $request)
    {
        try {
            $medicine = $this->medicineService->createMedicine($request->validated());
            
            return redirect()->route('medicines.index')
                ->with('success', 'Obat berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $medicine = $this->medicineService->getMedicineById($id);
        
        if (!$medicine) {
            return redirect()->route('medicines.index')
                ->with('error', 'Obat tidak ditemukan');
        }

        return view('medicines.show', compact('medicine'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $medicine = $this->medicineService->getMedicineById($id);
        
        if (!$medicine) {
            return redirect()->route('medicines.index')
                ->with('error', 'Obat tidak ditemukan');
        }

        $categories = $this->medicineService->getCategories();
        $units = $this->medicineService->getUnits();

        return view('medicines.edit', compact('medicine', 'categories', 'units'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MedicineRequest $request, int $id)
    {
        $medicine = $this->medicineService->getMedicineById($id);
        
        if (!$medicine) {
            return redirect()->route('medicines.index')
                ->with('error', 'Obat tidak ditemukan');
        }

        try {
            $this->medicineService->updateMedicine($medicine, $request->validated());
            
            return redirect()->route('medicines.index')
                ->with('success', 'Obat berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $medicine = $this->medicineService->getMedicineById($id);
        
        if (!$medicine) {
            return redirect()->route('medicines.index')
                ->with('error', 'Obat tidak ditemukan');
        }

        try {
            $this->medicineService->deleteMedicine($medicine);
            
            return redirect()->route('medicines.index')
                ->with('success', 'Obat berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Get low stock medicines
     */
    public function lowStock()
    {
        $medicines = $this->medicineService->getLowStockMedicines();
        
        return view('medicines.low-stock', compact('medicines'));
    }

    /**
     * Get near expiry medicines
     */
    public function nearExpiry(Request $request)
    {
        $days = $request->get('days', 30);
        $medicines = $this->medicineService->getNearExpiryMedicines($days);
        
        return view('medicines.near-expiry', compact('medicines', 'days'));
    }
}
