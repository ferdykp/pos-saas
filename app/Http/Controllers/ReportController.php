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
        // ... (Kode index bawaan Anda tetap utuh, tidak perlu ada yang diubah)
        $startDate = $request->get('start_date', Carbon::today()->toDateString());
        $endDate = $request->get('end_date', Carbon::today()->toDateString());
        $startDateTime = Carbon::parse($startDate)->startOfDay();
        $endDateTime = Carbon::parse($endDate)->endOfDay();

        $salesSummary = Order::whereBetween('created_at', [$startDateTime, $endDateTime])
            ->where('payment_status', 'paid')
            ->select(
                DB::raw('SUM(subtotal) as total_gross'),
                DB::raw('SUM(discount) as total_discount'),
                DB::raw('SUM(tax) as total_tax'),
                DB::raw('SUM(grand_total) as total_net'),
                DB::raw('COUNT(id) as total_transactions')
            )->first();

        $paymentMethods = Order::whereBetween('created_at', [$startDateTime, $endDateTime])
            ->where('payment_status', 'paid')
            ->groupBy('payment_method')
            ->select('payment_method', DB::raw('SUM(grand_total) as total_amount'))
            ->get();

        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDateTime, $endDateTime])
            ->where('orders.payment_status', 'paid')
            ->groupBy('products.id', 'products.product_name')
            ->orderBy('total_qty', 'desc')
            ->select(
                'products.product_name',
                DB::raw('SUM(order_items.quantity) as total_qty'),
                DB::raw('SUM(order_items.subtotal) as total_sales')
            )
            ->take(5)
            ->get();

        $shifts = Shift::whereBetween('start_time', [$startDateTime, $endDateTime])
            ->with('user')
            ->orderBy('id', 'desc')
            ->get();

        $dailySalesData = Order::whereBetween('created_at', [$startDateTime, $endDateTime])
            ->where('payment_status', 'paid')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(grand_total) as total_sales'))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'asc')
            ->get();

        $chartLabels = $dailySalesData->pluck('date')->map(function ($date) {
            return \Carbon\Carbon::parse($date)->translatedFormat('d M');
        })->toArray();
        $chartValues = $dailySalesData->pluck('total_sales')->toArray();

        return view('reports.index', compact(
            'salesSummary',
            'paymentMethods',
            'topProducts',
            'shifts',
            'startDate',
            'endDate',
            'chartLabels',
            'chartValues'
        ));
    }

    // public function exportExcel(Request $request)
    // {
    //     $startDate = $request->get('start_date', now()->toDateString());
    //     $endDate = $request->get('end_date', now()->toDateString());
    //     $tenant = auth()->user()->tenant;

    //     // 1. Tarik ringkasan data finansial & produk untuk asupan Prompt AI
    //     $salesSummary = Order::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
    //         ->where('payment_status', 'paid')
    //         ->select(DB::raw('SUM(grand_total) as total_net'), DB::raw('COUNT(id) as total_tx'))->first();

    //     $topProduct = DB::table('order_items')
    //         ->join('products', 'order_items.product_id', '=', 'products.id')
    //         ->join('orders', 'order_items.order_id', '=', 'orders.id')
    //         ->whereBetween('orders.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
    //         ->where('orders.payment_status', 'paid')
    //         ->groupBy('products.product_name')
    //         ->orderBy('total_qty', 'desc')
    //         ->select('products.product_name', DB::raw('SUM(order_items.quantity) as total_qty'))->first();

    //     // 2. Susun data kompresi ringkas
    //     $dataBisnis = [
    //         'nama_toko' => $tenant->name ?? 'Toko Saya',
    //         'periode' => $startDate . ' s/d ' . $endDate,
    //         'total_omset_periode_ini' => $salesSummary->total_net ?? 0,
    //         'total_transaksi' => $salesSummary->total_tx ?? 0,
    //         'produk_paling_laris' => $topProduct->product_name ?? 'Belum ada transaksi'
    //     ];

    //     // 3. Instruksikan AI memproduksi teks laporan murni tanpa tag HTML agar elok di Excel
    //     $prompt = "Anda adalah Chief Business Analyst POS. Analisis data ringkas periode ini: " . json_encode($dataBisnis) . ". 
    //     Tulis analisis profesional dalam bentuk POIN BARIS (tanpa format markdown, tanpa bintang-bintang, tanpa tag HTML, murni baris teks narasi terpisah menggunakan baris baru \\n). 
    //     Buat struktur 4 baris kalimat utama:
    //     Baris 1: Evaluasi performa finansial toko.
    //     Baris 2: Analisis dominasi produk juara.
    //     Baris 3: Rekomendasi taktis promosi bundling bulan depan.
    //     Baris 4: Taktik menaikkan kepuasan loyalitas pelanggan.";

    //     // 4. Lakukan pemanggilan AI dengan pengaman Try-Catch (Fallback)
    //     try {
    //         $aiTextResult = $this->gemini->generateAnalytic($prompt);
    //     } catch (\Exception $e) {
    //         $aiTextResult = "Evaluasi Finansial: Performa transaksi berjalan dengan stabil sepanjang rentang periode laporan.\nAnalisis Produk: Produk utama berhasil mempertahankan tingkat rotasi persediaan yang sehat.\nRekomendasi Promosi: Naikkan penjualan item pelengkap lewat peluncuran kupon khusus transaksi berulang.\nTaktik Loyalitas: Terapkan penawaran diskon langsung bagi transaksi non-tunai di jam-jam sepi pengunjung.";
    //     }

    //     $fileName = 'Laporan_POS_' . $startDate . '_s_d_' . $endDate . '.xlsx';

    //     // 5. Lempar teks hasil AI ($aiTextResult) ke dalam class Export Maatwebsite
    //     return Excel::download(new OrdersReportExport($startDate, $endDate, $aiTextResult), $fileName);
    // }

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
