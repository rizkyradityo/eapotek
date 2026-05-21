<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Medicine;
use App\Models\MedicineBatch;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Get sales report
     */
    public function getSalesReport(array $filters = []): array
    {
        $query = Sale::completed()
            ->when($filters['from_date'] ?? null, fn($q, $date) => 
                $q->whereDate('created_at', '>=', $date)
            )
            ->when($filters['to_date'] ?? null, fn($q, $date) => 
                $q->whereDate('created_at', '<=', $date)
            );

        $totalSales = $query->count();
        $totalRevenue = $query->sum('total');
        $totalDiscount = $query->sum('discount_amount');
        $totalTax = $query->sum('tax_amount');

        // Daily breakdown
        $dailySales = $query->clone()
            ->select(DB::raw('DATE(created_at) as date'))
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('SUM(total) as revenue')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        // Payment method breakdown
        $paymentMethods = $query->clone()
            ->select('payment_method')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('SUM(total) as total')
            ->groupBy('payment_method')
            ->get();

        return [
            'summary' => [
                'total_sales' => $totalSales,
                'total_revenue' => $totalRevenue,
                'total_discount' => $totalDiscount,
                'total_tax' => $totalTax,
                'average_sale' => $totalSales > 0 ? $totalRevenue / $totalSales : 0,
            ],
            'daily_sales' => $dailySales,
            'payment_methods' => $paymentMethods,
        ];
    }

    /**
     * Get top selling medicines
     */
    public function getTopSellingMedicines(array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        return SaleItem::select('medicine_id')
            ->selectRaw('SUM(quantity) as total_quantity')
            ->selectRaw('SUM(subtotal) as total_revenue')
            ->when($filters['from_date'] ?? null, fn($q, $date) => 
                $q->whereHas('sale', fn($sq) => $sq->whereDate('created_at', '>=', $date))
            )
            ->when($filters['to_date'] ?? null, fn($q, $date) => 
                $q->whereHas('sale', fn($sq) => $sq->whereDate('created_at', '<=', $date))
            )
            ->whereHas('sale', fn($q) => $q->completed())
            ->groupBy('medicine_id')
            ->orderByDesc('total_quantity')
            ->limit($filters['limit'] ?? 10)
            ->with('medicine:id,name,code,price')
            ->get();
    }

    /**
     * Get stock report
     */
    public function getStockReport(): array
    {
        $medicines = Medicine::active()
            ->with(['category', 'unit', 'batches' => fn($q) => 
                $q->active()->where('quantity', '>', 0)
            ])
            ->get();

        $totalStockValue = 0;
        $totalItems = 0;
        $lowStockItems = [];
        $nearExpiryItems = [];

        foreach ($medicines as $medicine) {
            $totalStock = $medicine->batches->sum('quantity');
            $totalItems += $totalStock;
            
            // Calculate stock value (using purchase price)
            foreach ($medicine->batches as $batch) {
                $totalStockValue += $batch->quantity * $batch->purchase_price;
            }

            // Low stock
            if ($medicine->isBelowMinStock()) {
                $lowStockItems[] = [
                    'id' => $medicine->id,
                    'code' => $medicine->code,
                    'name' => $medicine->name,
                    'current_stock' => $totalStock,
                    'min_stock' => $medicine->min_stock,
                    'category' => $medicine->category->name ?? null,
                ];
            }

            // Near expiry (within 30 days)
            foreach ($medicine->batches as $batch) {
                if ($batch->isNearExpiry()) {
                    $nearExpiryItems[] = [
                        'id' => $medicine->id,
                        'code' => $medicine->code,
                        'name' => $medicine->name,
                        'batch_number' => $batch->batch_number,
                        'expired_date' => $batch->expired_date->format('Y-m-d'),
                        'quantity' => $batch->quantity,
                        'days_until_expiry' => now()->diffInDays($batch->expired_date),
                    ];
                }
            }
        }

        return [
            'summary' => [
                'total_medicines' => $medicines->count(),
                'total_items' => $totalItems,
                'total_stock_value' => $totalStockValue,
                'low_stock_count' => count($lowStockItems),
                'near_expiry_count' => count($nearExpiryItems),
            ],
            'low_stock_items' => $lowStockItems,
            'near_expiry_items' => $nearExpiryItems,
        ];
    }

    /**
     * Get stock movement report
     */
    public function getStockMovementReport(array $filters = []): array
    {
        $query = StockMovement::with(['medicine', 'user'])
            ->when($filters['medicine_id'] ?? null, fn($q, $id) => 
                $q->where('medicine_id', $id)
            )
            ->when($filters['type'] ?? null, fn($q, $type) => 
                $q->where('type', $type)
            )
            ->when($filters['from_date'] ?? null, fn($q, $date) => 
                $q->whereDate('created_at', '>=', $date)
            )
            ->when($filters['to_date'] ?? null, fn($q, $date) => 
                $q->whereDate('created_at', '<=', $date)
            );

        $stockIn = $query->clone()->stockIn()->sum('quantity');
        $stockOut = $query->clone()->stockOut()->sum('quantity');

        // Movement by type
        $byType = $query->clone()
            ->select('type')
            ->selectRaw('SUM(quantity) as total_quantity')
            ->groupBy('type')
            ->get();

        return [
            'summary' => [
                'stock_in' => $stockIn,
                'stock_out' => $stockOut,
                'net_movement' => $stockIn - $stockOut,
            ],
            'by_type' => $byType,
            'movements' => $query->orderByDesc('created_at')->paginate(20),
        ];
    }

    /**
     * Get dashboard summary
     */
    public function getDashboardSummary(): array
    {
        $today = now()->toDateString();

        // Today's sales
        $todaySales = Sale::completed()
            ->whereDate('created_at', $today)
            ->count();
        
        $todayRevenue = Sale::completed()
            ->whereDate('created_at', $today)
            ->sum('total');

        // Low stock count
        $lowStockCount = Medicine::active()
            ->get()
            ->filter(fn($m) => $m->isBelowMinStock())
            ->count();

        // Near expiry count
        $nearExpiryCount = MedicineBatch::active()
            ->notExpired()
            ->where('expired_date', '<=', now()->addDays(30)->toDateString())
            ->where('quantity', '>', 0)
            ->count();

        // Total medicines
        $totalMedicines = Medicine::active()->count();

        // Recent sales
        $recentSales = Sale::with('user')
            ->completed()
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return [
            'today_sales' => $todaySales,
            'today_revenue' => $todayRevenue,
            'low_stock_count' => $lowStockCount,
            'near_expiry_count' => $nearExpiryCount,
            'total_medicines' => $totalMedicines,
            'recent_sales' => $recentSales,
        ];
    }
}
