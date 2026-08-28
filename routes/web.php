<?php

use Illuminate\Support\Facades\Route;

// Auth & Google Controllers
use App\Http\Controllers\Auth\GoogleController;

// Core & Tenant Management Controllers
use App\Http\Controllers\TenantController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\PaymentCallbackController;

// Operational POS & Catalog Controllers
use App\Http\Controllers\PosController;
use App\Http\Controllers\PosApiController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ServiceOrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductVariantController;
use App\Http\Controllers\CategoryController;

// Inventory, Materials & Suppliers Controllers
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\ShiftController;

// CRM, Marketing & Employee Controllers
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\EmployeeController;

// Finance, Reports, Settings & AI Controllers
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AiReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ActivityLogController;

// Admin Controllers
use App\Http\Controllers\Admin\WithdrawalApprovalController;

// Middlewares
use App\Http\Middleware\EnsureSubscriptionIsActive;
use App\Models\Plan;

/*
|--------------------------------------------------------------------------
| 1. PUBLIC & WEBHOOK ROUTES (Bebas dari Auth & CSRF)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    $plans = Plan::where('is_active', true)->where('is_public', true)->get();
    return view('landing', compact('plans'));
})->name('landing');

// Webhook Callback dari Midtrans
Route::post('/midtrans/callback', [PaymentCallbackController::class, 'handleNotification'])
    ->name('midtrans.callback');


/*
|--------------------------------------------------------------------------
| 2. GUEST ROUTES (Google OAuth)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);
});


/*
|--------------------------------------------------------------------------
| 3. AUTHENTICATED & EMAIL VERIFIED ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // ==========================================
    // A. SETUP TENANT & PROFILE MANAGEMENT
    // (Bisa diakses walau belum punya tenant_id aktif)
    // ==========================================
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

    // User Profile Settings
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // API Check Status Verifikasi (Auto-Polling Client)
    Route::get('/api/check-email-verification', function () {
        return response()->json([
            'verified'     => auth()->user()->hasVerifiedEmail(),
            'has_tenant'   => !is_null(auth()->user()->tenant_id),
            'redirect_url' => auth()->user()->tenant_id ? route('dashboard') : route('tenants.create'),
        ]);
    })->name('api.check-verification');


    // ==========================================
    // B. BILLING & SAAS SUBSCRIPTION MODULE
    // (Bisa diakses walau paket langganan mati/habis)
    // ==========================================
    Route::prefix('billing')->name('billing.')->group(function () {
        Route::get('/', [SubscriptionController::class, 'index'])->name('index');
        Route::post('/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscribe');
        Route::get('/invoices/{invoice}', [SubscriptionController::class, 'showInvoice'])->name('invoice');
        Route::get('/invoices/{invoice}/check-status', [SubscriptionController::class, 'checkStatus'])->name('check-status');
    });


    // ==========================================
    // C. CORE OPERATIONAL ROUTES (TERPROTEKSI KETAT)
    // Wajib: (1) Memiliki Tenant ID & (2) Langganan Aktif
    // ==========================================
    Route::middleware(['check.tenant', EnsureSubscriptionIsActive::class])->group(function () {

        // --- DASHBOARD UTAMA ---
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // --- ADMIN ONLY ROUTES ---
        Route::middleware(['admin'])->group(function () {
            Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
            Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
            Route::delete('/employees/{id}', [EmployeeController::class, 'destroy'])->name('employees.destroy');

            Route::get('/withdrawals', [WithdrawalApprovalController::class, 'index'])->name('admin.withdrawals.index');
            Route::post('/withdrawals/{withdrawal}/approve', [WithdrawalApprovalController::class, 'approve'])->name('admin.withdrawals.approve');
            Route::post('/withdrawals/{withdrawal}/reject', [WithdrawalApprovalController::class, 'reject'])->name('admin.withdrawals.reject');
        });

        // --- MASTER DATA: PRODUK, KATEGORI & VARIAN ---
        Route::resource('categories', CategoryController::class);
        Route::resource('products', ProductController::class);
        Route::resource('products.variants', ProductVariantController::class)->except(['index', 'show']);

        // --- MASTER DATA: BAHAN BAKU & INVENTORI ---
        Route::resource('materials', MaterialController::class);
        Route::post('/materials/update-stock', [MaterialController::class, 'updateStock'])->name('materials.update-stock');
        Route::get('/materials/{id}/history', [MaterialController::class, 'getHistory'])->name('materials.history-json');

        // --- MASTER DATA: SUPPLIER & PEMASOK ---
        Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
        Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
        Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');

        // --- INVENTORY ADJUSTMENT & MUTASI STOK ---
        Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::post('/inventory/adjust', [InventoryController::class, 'adjust'])->name('inventory.adjust');
        Route::get('/inventory/history', [InventoryController::class, 'history'])->name('inventory.history');
        Route::resource('stock-movements', StockMovementController::class)->only(['index', 'create', 'store']);

        // --- CRM & CUSTOMERS ---
        Route::resource('customers', CustomerController::class);
        Route::post('/customers/api', [CustomerController::class, 'storeApi'])->name('customers.storeApi');

        // --- DISCOUNTS & PROMOS ---
        Route::resource('discounts', DiscountController::class);

        // --- KASIR & POS TERMINAL ---
        Route::resource('pos', PosController::class);
        Route::resource('orders', OrderController::class);
        Route::resource('service-orders', ServiceOrderController::class);
        Route::get('/orders/{id}/print', [OrderController::class, 'print'])->name('orders.print');
        Route::get('/orders/{id}/check-status', [PosController::class, 'checkStatus'])->name('orders.checkStatus');
        Route::get('/pos/check-payment-status/{order}', [PosController::class, 'checkPaymentStatus'])->name('pos.check-payment');

        // --- AUDIT SHIFT KASIR ---
        Route::post('/shifts/open', [ShiftController::class, 'open'])->name('shifts.open');
        Route::get('/shifts/summary', [ShiftController::class, 'summary'])->name('shifts.summary');
        Route::post('/shifts/close', [ShiftController::class, 'close'])->name('shifts.close');
        Route::get('/shifts/current', [ShiftController::class, 'current'])->name('shifts.current');
        Route::resource('shifts', ShiftController::class);

        // --- KEUANGAN & DOMPET TOKO ---
        Route::get('/finance', [FinanceController::class, 'index'])->name('finance.index');
        Route::post('/finance/withdraw', [FinanceController::class, 'withdraw'])->name('finance.withdraw');
        Route::post('/finance/settings', [FinanceController::class, 'updateSettings'])->name('finance.settings');

        // --- LAPORAN OPERASIONAL & EXPORT ---
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export-excel', [ReportController::class, 'exportExcel'])->name('reports.export-excel');
        Route::get('/reports/exports', [ReportController::class, 'exportList'])->name('reports.exports-list');
        Route::get('/reports/exports/download/{id}', [ReportController::class, 'downloadFile'])->name('reports.download-file');
        Route::get('/reports/exports/status-json', [ReportController::class, 'getExportsStatusJson'])->name('reports.exports-status-json');

        // --- AI ADVISOR ENGINE & CHAT REAL-TIME ---
        Route::get('/reports/ai-analysis', [AiReportController::class, 'index'])->name('reports.ai');
        Route::post('/reports/ai-chat', [AiReportController::class, 'chat'])->name('reports.ai-chat');
        Route::get('/api/pos/recommendation', [PosApiController::class, 'getRecommendation'])->name('api.pos.recommendation');

        // --- AUDIT TRAIL ACTIVITY LOG ---
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');

        // --- SISTEM PENGATURAN ---
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
