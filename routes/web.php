<?php

use App\Http\Controllers\Admin\WithdrawalApprovalController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\PosApiController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AiReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\WithdrawalController;

/*
|--------------------------------------------------------------------------
| Landing Page
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('landing');
});

/*
|--------------------------------------------------------------------------
| Authenticated & Verified Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // -------------------------------------------------------------------
    // 1. Setup & Multi-Tenant Management (Bisa diakses tanpa tenant_id aktif)
    // -------------------------------------------------------------------
    Route::get('/setup-business', [TenantController::class, 'create'])->name('tenants.create');
    Route::post('/setup-business', [TenantController::class, 'store'])->name('tenants.store');

    Route::get('/tenants', [TenantController::class, 'index'])->name('tenants.index');
    Route::get('/tenants/{tenant}/edit', [TenantController::class, 'edit'])->name('tenants.edit');
    Route::put('/tenants/{tenant}', [TenantController::class, 'update'])->name('tenants.update');
    Route::delete('/tenants/{tenant}', [TenantController::class, 'destroy'])->name('tenants.destroy');

    // Switch Tenant Action
    Route::post('/tenants/switch/{tenant}', function ($tenantId) {
        $tenant = auth()->user()->tenants()->findOrFail($tenantId);
        auth()->user()->update(['tenant_id' => $tenant->id]);
        return back()->with('status', 'Berhasil pindah ke bisnis: ' . $tenant->name);
    })->name('tenants.switch');

    // Profile & User Account Settings
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    // -------------------------------------------------------------------
    // 2. Core Operational Routes (Membutuhkan Tenant ID Aktif)
    // -------------------------------------------------------------------
    Route::middleware(['check.tenant'])->group(function () {

        // Dashboard Utama
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Admin Only Routes
        Route::middleware(['admin'])->group(function () {
            Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
            Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
            Route::delete('/employees/{id}', [EmployeeController::class, 'destroy'])->name('employees.destroy');

            Route::get('/withdrawals', [WithdrawalApprovalController::class, 'index'])->name('admin.withdrawals.index');
            Route::post('/withdrawals/{withdrawal}/approve', [WithdrawalApprovalController::class, 'approve'])->name('admin.withdrawals.approve');
            Route::post('/withdrawals/{withdrawal}/reject', [WithdrawalApprovalController::class, 'reject'])->name('admin.withdrawals.reject');
        });

        // Master Data: Produk & Kategori
        Route::resource('categories', CategoryController::class);
        Route::resource('products', ProductController::class);

        // Master Data: Bahan Baku & Inventori
        Route::resource('materials', MaterialController::class);
        Route::post('/materials/update-stock', [MaterialController::class, 'updateStock'])->name('materials.update-stock');
        Route::get('/materials/{id}/history', [MaterialController::class, 'getHistory'])->name('materials.history-json');

        // Master Data: Supplier & Pemasok
        Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
        Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
        Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');

        // Stock Inventory Adjustment
        Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::post('/inventory/adjust', [InventoryController::class, 'adjust'])->name('inventory.adjust');
        Route::get('/inventory/history', [InventoryController::class, 'history'])->name('inventory.history');

        // CRM & Customers
        Route::resource('customers', CustomerController::class);
        Route::post('/customers/api', [CustomerController::class, 'storeApi'])->name('customers.storeApi');

        // Discounts & Promos
        Route::resource('discounts', DiscountController::class);

        // Kasir & POS Terminal
        Route::resource('pos', PosController::class);
        Route::resource('orders', OrderController::class);
        // Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
        Route::get('/orders/{id}/print', [OrderController::class, 'print'])->name('orders.print');
        Route::get('/orders/{id}/check-status', [PosController::class, 'checkStatus'])->name('orders.checkStatus');

        // Audit Shift Kasir (DIPINDAHKAN KE ATAS ROUTE RESOURCE)
        Route::post('/shifts/open', [ShiftController::class, 'open']);
        Route::get('/shifts/summary', [ShiftController::class, 'summary']);
        Route::post('/shifts/close', [ShiftController::class, 'close']);
        Route::resource('shifts', ShiftController::class);

        // Keuangan & Dompet Toko
        Route::get('/finance', [FinanceController::class, 'index'])->name('finance.index');
        Route::post('/finance/withdraw', [FinanceController::class, 'withdraw'])->name('finance.withdraw');
        Route::post('/finance/settings', [FinanceController::class, 'updateSettings'])->name('finance.settings');

        // Laporan Operasional & Export
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export-excel', [ReportController::class, 'exportExcel'])->name('reports.export-excel');
        Route::get('/reports/exports', [ReportController::class, 'exportList'])->name('reports.exports-list');
        Route::get('/reports/exports/download/{id}', [ReportController::class, 'downloadFile'])->name('reports.download-file');
        Route::get('/reports/exports/status-json', [ReportController::class, 'getExportsStatusJson'])->name('reports.exports-status-json');

        // AI Advisor Engine & Chat Real-Time
        Route::get('/reports/ai-analysis', [AiReportController::class, 'index'])->name('reports.ai');
        Route::post('/reports/ai-chat', [AiReportController::class, 'chat'])->name('reports.ai-chat');
        Route::get('/api/pos/recommendation', [PosApiController::class, 'getRecommendation'])->name('api.pos.recommendation');

        // Sistem Pengaturan
        Route::resource('settings', SettingController::class);
        Route::post('/settings/update-points', [SettingController::class, 'updatePoints'])->name('settings.update-points');
    });
});

/*
|--------------------------------------------------------------------------
| Authentication Routes (Breeze/Fortify)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
