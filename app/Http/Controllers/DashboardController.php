<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
// use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $tenantId = auth()->user()->tenant_id;

        // 1. Total Penjualan (Uang masuk dari semua pesanan yang lunas/paid)
        $totalRevenue = Order::where('tenant_id', $tenantId)
            ->where('payment_status', 'paid')
            ->sum('grand_total');

        // 2. Total Pesanan (Jumlah invoice)
        $totalOrders = Order::where('tenant_id', $tenantId)->count();

        // 3. Total Pelanggan
        $totalCustomers = Customer::where('tenant_id', $tenantId)->count();

        // 4. Total Produk
        $totalProducts = Product::where('tenant_id', $tenantId)->count();

        // 5. Transaksi Terbaru
        $recentOrders = Order::with(['user', 'customer'])
            ->where('tenant_id', $tenantId)
            ->latest()
            ->take(5)
            ->get();

        // Tambahan: Total Piutang (Bon yang belum dibayar)
        $totalDebt = Customer::where('tenant_id', $tenantId)->sum('total_debt');

        return view('dashboard.index', compact(
            'totalRevenue',
            'totalOrders',
            'totalCustomers',
            'totalProducts',
            'recentOrders',
            'totalDebt'
        ));
    }
}
