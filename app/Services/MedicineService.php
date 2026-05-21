<?php

namespace App\Services;

use App\Models\Medicine;
use App\Models\MedicineBatch;
use App\Models\Category;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MedicineService
{
    /**
     * Get paginated medicines with filters
     */
    public function getMedicines(array $filters = []): LengthAwarePaginator
    {
        $query = Medicine::with(['category', 'unit'])
            ->active()
            ->search($filters['search'] ?? null)
            ->when($filters['category_id'] ?? null, fn($q, $cat) => 
                $q->where('category_id', $cat)
            )
            ->when($filters['low_stock'] ?? false, fn($q) => 
                $q->whereRaw('(
                    SELECT COALESCE(SUM(quantity), 0) 
                    FROM medicine_batches 
                    WHERE medicine_batches.medicine_id = medicines.id
                ) < medicines.min_stock')
            );

        return $query->orderBy('name')->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Get medicine by ID with relations
     */
    public function getMedicineById(int $id): ?Medicine
    {
        return Medicine::with(['category', 'unit', 'batches' => function($q) {
            $q->active()->notExpired()->orderBy('expired_date');
        }])->find($id);
    }

    /**
     * Create new medicine
     */
    public function createMedicine(array $data): Medicine
    {
        return DB::transaction(function () use ($data) {
            $medicine = Medicine::create([
                'code' => $data['code'],
                'name' => $data['name'],
                'category_id' => $data['category_id'],
                'unit_id' => $data['unit_id'],
                'generic_name' => $data['generic_name'] ?? null,
                'manufacturer' => $data['manufacturer'] ?? null,
                'price' => $data['price'],
                'min_stock' => $data['min_stock'] ?? 10,
                'description' => $data['description'] ?? null,
                'composition' => $data['composition'] ?? null,
                'side_effects' => $data['side_effects'] ?? null,
                'usage_instruction' => $data['usage_instruction'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ]);

            Log::info('Medicine created', ['medicine_id' => $medicine->id, 'code' => $medicine->code]);

            return $medicine;
        });
    }

    /**
     * Update medicine
     */
    public function updateMedicine(Medicine $medicine, array $data): Medicine
    {
        return DB::transaction(function () use ($medicine, $data) {
            $medicine->update(array_filter([
                'name' => $data['name'] ?? null,
                'category_id' => $data['category_id'] ?? null,
                'unit_id' => $data['unit_id'] ?? null,
                'generic_name' => $data['generic_name'] ?? null,
                'manufacturer' => $data['manufacturer'] ?? null,
                'price' => $data['price'] ?? null,
                'min_stock' => $data['min_stock'] ?? null,
                'description' => $data['description'] ?? null,
                'composition' => $data['composition'] ?? null,
                'side_effects' => $data['side_effects'] ?? null,
                'usage_instruction' => $data['usage_instruction'] ?? null,
                'is_active' => $data['is_active'] ?? null,
            ], fn($v) => $v !== null));

            Log::info('Medicine updated', ['medicine_id' => $medicine->id]);

            return $medicine->fresh();
        });
    }

    /**
     * Delete medicine (soft delete)
     */
    public function deleteMedicine(Medicine $medicine): bool
    {
        return DB::transaction(function () use ($medicine) {
            // Check if medicine has stock
            if ($medicine->total_stock > 0) {
                throw new \InvalidArgumentException('Cannot delete medicine with existing stock');
            }

            $medicine->update(['is_active' => false]);
            Log::info('Medicine deleted (deactivated)', ['medicine_id' => $medicine->id]);

            return true;
        });
    }

    /**
     * Get medicines with low stock
     */
    public function getLowStockMedicines(): \Illuminate\Database\Eloquent\Collection
    {
        return Medicine::active()
            ->with(['category', 'unit'])
            ->get()
            ->filter(fn($medicine) => $medicine->isBelowMinStock());
    }

    /**
     * Get medicines near expiry
     */
    public function getNearExpiryMedicines(int $days = 30): \Illuminate\Database\Eloquent\Collection
    {
        return MedicineBatch::with(['medicine' => function($q) {
            $q->active();
        }])
            ->active()
            ->notExpired()
            ->where('expired_date', '<=', now()->addDays($days)->toDateString())
            ->where('quantity', '>', 0)
            ->get()
            ->filter(fn($batch) => $batch->medicine !== null);
    }

    /**
     * Get all active categories
     */
    public function getCategories(): \Illuminate\Database\Eloquent\Collection
    {
        return Category::active()->orderBy('name')->get();
    }

    /**
     * Get all active units
     */
    public function getUnits(): \Illuminate\Database\Eloquent\Collection
    {
        return Unit::active()->orderBy('name')->get();
    }

    /**
     * Generate unique medicine code
     */
    public function generateMedicineCode(): string
    {
        $prefix = 'MED';
        $lastMedicine = Medicine::orderBy('id', 'desc')->first();
        $nextNumber = $lastMedicine ? $lastMedicine->id + 1 : 1;
        
        return $prefix . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Check if medicine code exists
     */
    public function isCodeUnique(string $code, ?int $excludeId = null): bool
    {
        $query = Medicine::where('code', $code);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return !$query->exists();
    }
}
