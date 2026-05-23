<?php

use App\Http\Controllers\AiReportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\PosApiController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ShiftController;

// use App\Http\Controllers\ReportController;

Route::get('/', function () {
    return view('landing');
});

// Route yang dilindungi otentikasi
Route::middleware(['auth', 'verified'])->group(function () {

    Route::middleware(['admin'])->group(function () {
        Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
        Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
        Route::delete('/employees/{id}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    });

    // ERROR FIX: Definisikan route dashboard di sini
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Tenants (UMKM/SaaS)
    Route::get('/setup-business', [TenantController::class, 'create'])->name('tenants.create');
    Route::post('/setup-business', [TenantController::class, 'store'])->name('tenants.store');

    // Rute yang butuh tenant_id (tambahkan middleware check.tenant)
    Route::middleware(['check.tenant'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('products', ProductController::class);
        // ... rute lainnya
    });

    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('materials', MaterialController::class);
    Route::post('/materials/update-stock', [MaterialController::class, 'updateStock'])->name('materials.update-stock');
    Route::get('/materials/{id}/history', [MaterialController::class, 'getHistory'])->name('materials.history-json');

    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
    Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');

    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::post('/inventory/adjust', [InventoryController::class, 'adjust'])->name('inventory.adjust');
    Route::get('/inventory/history', [InventoryController::class, 'history'])->name('inventory.history');

    // Tenant Management
    Route::get('/tenants', [TenantController::class, 'index'])->name('tenants.index');
    Route::get('/tenants/create', [TenantController::class, 'create'])->name('tenants.create');
    Route::post('/tenants', [TenantController::class, 'store'])->name('tenants.store');
    Route::get('/tenants/{tenant}/edit', [TenantController::class, 'edit'])->name('tenants.edit');
    Route::put('/tenants/{tenant}', [TenantController::class, 'update'])->name('tenants.update');
    Route::delete('/tenants/{tenant}', [TenantController::class, 'destroy'])->name('tenants.destroy');

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Switch Tenant (Logika pindah toko)
    Route::post('/tenants/switch/{tenant}', function ($tenantId) {
        $tenant = auth()->user()->tenants()->findOrFail($tenantId);
        auth()->user()->update(['tenant_id' => $tenant->id]);
        return back()->with('status', 'Berhasil pindah ke bisnis: ' . $tenant->name);
    })->name('tenants.switch');

    Route::post('/customers/api', [CustomerController::class, 'storeApi'])->name('customers.storeApi');
    // Master Data
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('pos', PosController::class);
    Route::resource('discounts', DiscountController::class);
    Route::resource('settings', SettingController::class);
    Route::post('/settings/update-points', [SettingController::class, 'updatePoints'])->name('settings.update-points');

    Route::post('/shifts/open', [ShiftController::class, 'open']);
    // Route::post('/shifts/open', [ShiftController::class, 'openStore'])->name('shifts.openStore');
    Route::get('/shifts/summary', [ShiftController::class, 'summary']);
    Route::post('/shifts/close', [ShiftController::class, 'close']);
    Route::resource('shifts', ShiftController::class);


    // POS / Orders
    Route::resource('orders', OrderController::class);
    // Route untuk melihat tampilan cantik di web
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');

    // Route khusus untuk struk thermal polos
    Route::get('/orders/{id}/print', [OrderController::class, 'print'])->name('orders.print');    // Route::get('/pos', [OrderController::class, 'create'])->name('pos.create'); // Alias jika perlu

    // Inventory
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/inventory/history', [InventoryController::class, 'history'])->name('inventory.history');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-excel', [ReportController::class, 'exportExcel'])->name('reports.export-excel');
    Route::get('/reports/exports', [ReportController::class, 'exportList'])->name('reports.exports-list');
    Route::get('/reports/exports/download/{id}', [ReportController::class, 'downloadFile'])->name('reports.download-file');
    Route::get('/reports/exports/status-json', [ReportController::class, 'getExportsStatusJson'])->name('reports.exports-status-json');

    Route::get('/reports/ai-analysis', [AiReportController::class, 'index'])->name('reports.ai');
    Route::get('/api/pos/recommendation', [PosApiController::class, 'getRecommendation'])->name('api.pos.recommendation');
});

require __DIR__ . '/auth.php';
