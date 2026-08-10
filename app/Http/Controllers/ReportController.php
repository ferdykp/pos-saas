<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Shift;
use Carbon\Carbon;
use App\Models\ReportExport;
use Illuminate\Support\Facades\Storage;
use App\Exports\OrdersReportExport;
use Maatwebsite\Excel\Facades\Excel;
// Tambahkan import service AI Anda
use App\Services\GeminiService;

class ReportController extends Controller
{
    protected $gemini;

    // Tambahkan constructor untuk inject GeminiService
    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    public function index(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        $startDate = $request->start_date ?? now()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?? now()->endOfMonth()->toDateString();

        // 1. Query Dasar Order Lunas
        $ordersQuery = Order::where('tenant_id', $tenantId)
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        // 2. Sales Summary (Total Transaksi, Net Sales, Diskon, Pajak)
        $salesSummary = (object) [
            'total_gross'        => (float) $ordersQuery->sum('subtotal'),
            'total_discount'     => (float) $ordersQuery->sum('discount'),
            'total_tax'          => (float) $ordersQuery->sum('tax'),
            'total_net'          => (float) $ordersQuery->sum('grand_total'),
            'total_transactions' => $ordersQuery->count(),
        ];

        // 3. Breakdown Metode Pembayaran (Cash vs QRIS) -> Solusi Error $paymentMethods
        $paymentMethods = Order::select('payment_method', DB::raw('SUM(grand_total) as total_amount'))
            ->where('tenant_id', $tenantId)
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->groupBy('payment_method')
            ->get();

        // 4. Hitung Total HPP (COGS) & Laba Bersih
        $totalHpp = (float) DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->leftJoin('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.tenant_id', $tenantId)
            ->where('orders.payment_status', 'paid')
            ->whereBetween('orders.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->sum(DB::raw('order_items.quantity * COALESCE(products.cost_price, 0)'));

        $totalQrisOmzet = $paymentMethods->where('payment_method', '!=', 'cash')->sum('total_amount');
        $totalPlatformFee = ($totalQrisOmzet * 1.5) / 100;
        $storeNetSales = $salesSummary->total_net - $totalPlatformFee;
        $netProfit = $storeNetSales - $totalHpp;

        // 5. Top 5 Produk Terlaris
        $topProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->select('order_items.product_name', DB::raw('SUM(order_items.quantity) as total_qty'), DB::raw('SUM(order_items.subtotal) as total_sales'))
            ->where('orders.tenant_id', $tenantId)
            ->where('orders.payment_status', 'paid')
            ->whereBetween('orders.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->groupBy('order_items.product_name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // 6. Audit Shift Kasir
        $shifts = Shift::where('tenant_id', $tenantId)
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->with('user')
            ->latest()
            ->get();

        // 7. Data Grafik Tren Penjualan
        $dailySales = Order::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(grand_total) as total'))
            ->where('tenant_id', $tenantId)
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'asc')
            ->get();

        $chartLabels = $dailySales->pluck('date')->map(fn($d) => date('d M', strtotime($d)))->toArray();
        $chartValues = $dailySales->pluck('total')->toArray();

        // 8. Tabel Transaksi Lunas dengan Pagination
        $orders = $ordersQuery->with(['customer', 'user'])->latest()->paginate(15);

        return view('reports.index', compact(
            'startDate',
            'endDate',
            'salesSummary',
            'paymentMethods',
            'totalHpp',
            'netProfit',
            'topProducts',
            'shifts',
            'chartLabels',
            'chartValues',
            'orders'
        ));
    }

    public function exportExcel(Request $request)
    {
        $startDate = $request->get('start_date', now()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());

        // Buat data rekam berkas berstatus pending di database
        $reportExport = ReportExport::create([
            'user_id' => auth()->id(),
            'report_type' => 'Laporan Ringkasan Finansial & AI',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'pending'
        ]);

        // Kirim ID ke antrean Job
        \App\Jobs\ExportReportJob::dispatch($startDate, $endDate, $reportExport->id);

        // Alihkan langsung ke halaman daftar unduhan laporan
        return redirect()->route('reports.exports-list')->with('success', 'Permintaan laporan berhasil dibuat dan sedang dianalisis oleh AI di latar belakang.');
    }

    // 2. Tambahkan fungsi baru untuk melihat riwayat daftar unduhan
    public function exportList()
    {
        $exports = ReportExport::where('user_id', auth()->id())
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('reports.exports-list', compact('exports'));
    }

    // 3. Tambahkan fungsi unduh file fisik dari folder storage aman
    public function downloadFile($id)
    {
        $export = ReportExport::where('id', $id)->where('user_id', auth()->id())->firstOrFail();

        if ($export->status !== 'completed' || !Storage::disk('public')->exists($export->file_path)) {
            return redirect()->back()->with('error', 'File laporan belum selesai diproses atau tidak ditemukan.');
        }

        return Storage::disk('public')->download($export->file_path);
    }

    public function getExportsStatusJson()
    {
        // Ambil data id, status, dan link download untuk laporan milik user aktif
        $exports = ReportExport::where('user_id', auth()->id())
            ->orderBy('id', 'desc')
            ->take(10) // Cek 10 data teratas saja demi efisiensi performa server
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'status' => $item->status,
                    'download_url' => route('reports.download-file', $item->id)
                ];
            });

        return response()->json($exports);
    }
}
