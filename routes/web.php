<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\StockCardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Authentication Routes (using Laravel Breeze by default)
Auth::routes();

// Protected Routes (require authentication)
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [ReportController::class, 'dashboard'])->name('dashboard');

    // POS (Point of Sale) - Main transaction interface
    Route::prefix('pos')->group(function () {
        Route::get('/', [TransactionController::class, 'pos'])->name('pos');
        Route::post('/sales', [TransactionController::class, 'store'])->name('pos.sale');
        Route::get('/batches/{medicineId}', [TransactionController::class, 'getBatches'])->name('pos.batches');
        Route::get('/search', [TransactionController::class, 'searchMedicine'])->name('pos.search');
    });

    // Medicines Management
    Route::resource('medicines', MedicineController::class);
    Route::get('/medicines-low-stock', [MedicineController::class, 'lowStock'])->name('medicines.low-stock');
    Route::get('/medicines-near-expiry', [MedicineController::class, 'nearExpiry'])->name('medicines.near-expiry');

    // Suppliers Management
    Route::resource('suppliers', SupplierController::class);

    // Transactions (Sales History)
    Route::prefix('transactions')->group(function () {
        Route::get('/', [TransactionController::class, 'index'])->name('transactions.index');
        Route::get('/{id}', [TransactionController::class, 'show'])->name('transactions.show');
        Route::post('/{id}/cancel', [TransactionController::class, 'cancel'])->name('transactions.cancel');
        Route::get('/{id}/receipt', [TransactionController::class, 'receipt'])->name('transactions.receipt');
    });

    // Purchases Management
    Route::resource('purchases', PurchaseController::class)->only(['index', 'show', 'create', 'store']);
    Route::post('/purchases/{id}/receive', [PurchaseController::class, 'receive'])->name('purchases.receive');
    Route::post('/purchases/{id}/cancel', [PurchaseController::class, 'cancel'])->name('purchases.cancel');

    // Reports
    Route::prefix('reports')->group(function () {
        Route::get('/sales', [ReportController::class, 'sales'])->name('reports.sales');
        Route::get('/stock', [ReportController::class, 'stock'])->name('reports.stock');
        Route::get('/stock-movement', [ReportController::class, 'stockMovement'])->name('reports.stock-movement');
        
        // Export routes
        Route::get('/export/sales', [ReportController::class, 'exportSales'])->name('reports.export.sales');
        Route::get('/export/stock', [ReportController::class, 'exportStock'])->name('reports.export.stock');
    });

    // Stock Opname
    Route::prefix('stock-opname')->group(function () {
        Route::get('/', [StockOpnameController::class, 'index'])->name('stock-opname.index');
        Route::get('/create', [StockOpnameController::class, 'create'])->name('stock-opname.create');
        Route::post('/', [StockOpnameController::class, 'store'])->name('stock-opname.store');
        Route::get('/{id}', [StockOpnameController::class, 'show'])->name('stock-opname.show');
        Route::post('/{id}/update-item', [StockOpnameController::class, 'updateItem'])->name('stock-opname.update-item');
        Route::post('/{id}/apply', [StockOpnameController::class, 'apply'])->name('stock-opname.apply');
        Route::post('/{id}/cancel', [StockOpnameController::class, 'cancel'])->name('stock-opname.cancel');
    });

    // Stock Card (Kartu Stok)
    Route::prefix('stock-card')->group(function () {
        Route::get('/', [StockCardController::class, 'index'])->name('stock-card.index');
        Route::get('/{medicineId}', [StockCardController::class, 'show'])->name('stock-card.show');
    });

    // API Routes (for AJAX calls)
    Route::prefix('api')->group(function () {
        Route::get('/medicines/search', function (\Illuminate\Http\Request $request) {
            $search = $request->get('q', '');
            $medicines = \App\Models\Medicine::active()
                ->where('name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%")
                ->with(['category', 'unit', 'batches' => function($q) {
                    $q->active()->notExpired()->where('quantity', '>', 0);
                }])
                ->limit(20)
                ->get();
            
            return response()->json($medicines);
        })->name('api.medicines.search');

        Route::get('/stock/{medicineId}', function ($medicineId) {
            $medicine = \App\Models\Medicine::with(['batches' => function($q) {
                $q->active()->notExpired()->where('quantity', '>', 0);
            }])->find($medicineId);
            
            if (!$medicine) {
                return response()->json(['error' => 'Medicine not found'], 404);
            }
            
            return response()->json([
                'total_stock' => $medicine->total_stock,
                'batches' => $medicine->batches->map(fn($b) => [
                    'id' => $b->id,
                    'batch_number' => $b->batch_number,
                    'expired_date' => $b->expired_date->format('Y-m-d'),
                    'quantity' => $b->quantity,
                    'selling_price' => $b->selling_price,
                ]),
            ]);
        })->name('api.stock.show');
    });
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
