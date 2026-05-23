<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;



// use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Filter Tanggal (Default ke Hari Ini jika kosong)
        $startDate = $request->get('start_date', Carbon::today()->toDateString());
        $endDate = $request->get('end_date', Carbon::today()->toDateString());

        // Format ke timestamp penuh untuk query database
        $startDateTime = Carbon::parse($startDate)->startOfDay();
        $endDateTime = Carbon::parse($endDate)->endOfDay();

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

        // 6. Query untuk Grafik Tren Penjualan Harian
        $dailySalesData = Order::where('tenant_id', $tenantId) // Ditambahkan proteksi tenant_id
            ->whereBetween('created_at', [$startDateTime, $endDateTime])
            ->where('payment_status', 'paid')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(grand_total) as total_sales')
            )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'asc')
            ->get();

        // Format data agar siap dibaca oleh JavaScript Chart.js
        $chartLabels = $dailySalesData->pluck('date')->map(function ($date) {
            return \Carbon\Carbon::parse($date)->translatedFormat('d M');
        })->toArray();

        $chartValues = $dailySalesData->pluck('total_sales')->toArray();

        // === TAMBAHKAN QUERY INI UNTUK GRAFIK PROPORSI PEMBAYARAN ===
        $paymentMethods = Order::where('tenant_id', $tenantId)
            ->whereBetween('created_at', [$startDateTime, $endDateTime])
            ->where('payment_status', 'paid')
            ->groupBy('payment_method')
            ->select('payment_method', DB::raw('SUM(grand_total) as total_amount'))
            ->get();


        // Jangan lupa masukkan 'paymentMethods' ke dalam fungsi compact() di bawah ini
        return view('dashboard.index', compact(
            'totalRevenue',
            'totalOrders',
            'totalCustomers',
            'totalProducts',
            'recentOrders',
            'totalDebt',
            'chartLabels',
            'chartValues',
            'paymentMethods' // <-- Kirim variabel ini ke view dashboard
        ));
    }
}
