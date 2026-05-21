<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\MedicineService;
use App\Models\Medicine;

class MedicineTable extends Component
{
    use WithPagination;

    public $search = '';
    public $category_id = '';
    public $showLowStock = false;
    public $perPage = 15;

    protected $queryString = [
        'search' => ['except' => ''],
        'category_id' => ['except' => ''],
        'showLowStock' => ['except' => false],
    ];

    protected MedicineService $medicineService;

    public function boot(MedicineService $medicineService)
    {
        $this->medicineService = $medicineService;
    }

    public function render()
    {
        $filters = [
            'search' => $this->search,
            'category_id' => $this->category_id,
            'low_stock' => $this->showLowStock,
            'per_page' => $this->perPage,
        ];

        $medicines = $this->medicineService->getMedicines($filters);
        $categories = $this->medicineService->getCategories();

        return view('livewire.medicine-table', [
            'medicines' => $medicines,
            'categories' => $categories,
        ]);
    }

    public function deleteMedicine($id)
    {
        $medicine = Medicine::find($id);
        
        if ($medicine) {
            $this->medicineService->deleteMedicine($medicine);
            session()->flash('success', 'Obat berhasil dihapus');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategoryId()
    {
        $this->resetPage();
    }

    public function updatingShowLowStock()
    {
        $this->resetPage();
    }
}
