<?php

namespace App\Jobs;

use App\Exports\OrdersReportExport;
use App\Services\GeminiService;
use App\Models\Order;
use App\Models\ReportExport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ExportReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $startDate;
    protected $endDate;
    protected $reportExportId;

    public function __construct($startDate, $endDate, $reportExportId)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->reportExportId = $reportExportId;
    }

    public function handle(GeminiService $gemini)
    {
        // 1. Ambil baris rekam ekspor dan ubah status menjadi processing
        $reportExport = ReportExport::find($this->reportExportId);
        if (!$reportExport) return;

        $reportExport->update(['status' => 'processing']);

        $user = $reportExport->user;
        $tenant = $user->tenant;

        // 2. Tarik data finansial untuk Prompt AI
        $salesSummary = Order::whereBetween('created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59'])
            ->where('payment_status', 'paid')
            ->select(DB::raw('SUM(grand_total) as total_net'), DB::raw('COUNT(id) as total_tx'))->first();

        $topProduct = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59'])
            ->where('orders.payment_status', 'paid')
            ->groupBy('products.product_name')
            ->orderBy('total_qty', 'desc')
            ->select('products.product_name', DB::raw('SUM(order_items.quantity) as total_qty'))->first();

        $dataBisnis = [
            'nama_toko' => $tenant->name ?? 'Toko Saya',
            'periode' => $this->startDate . ' s/d ' . $this->endDate,
            'total_omset_periode_ini' => $salesSummary->total_net ?? 0,
            'total_transaksi' => $salesSummary->total_tx ?? 0,
            'produk_paling_laris' => $topProduct->product_name ?? 'Belum ada transaksi'
        ];

        $prompt = "Anda adalah Chief Business Analyst POS. Analisis data ringkas periode ini: " . json_encode($dataBisnis) . ". 
        Tulis analisis profesional dalam bentuk POIN BARIS (tanpa format markdown, tanpa bintang-bintang, tanpa tag HTML, murni baris teks narasi terpisah menggunakan baris baru \\n). 
        Buat struktur 4 baris kalimat utama.";

        try {
            $aiTextResult = $gemini->generateAnalytic($prompt);
        } catch (\Exception $e) {
            $aiTextResult = "Evaluasi Finansial: Performa transaksi stabil.\nAnalisis Produk: Rotasi produk sehat.";
        }

        // 3. Nama file unik disimpan di direktori publik
        $fileName = 'exports/Laporan_POS_' . $this->startDate . '_s_d_' . $this->endDate . '_' . time() . '.xlsx';

        // 4. Generate & simpan ke storage
        Excel::store(new OrdersReportExport($this->startDate, $this->endDate, $aiTextResult), $fileName, 'public');

        // 5. Perbarui status menjadi completed dan pasang path file untuk diunduh
        $reportExport->update([
            'status' => 'completed',
            'file_path' => $fileName
        ]);
    }

    public function failed(\Throwable $exception)
    {
        ReportExport::find($this->reportExportId)?->update(['status' => 'failed']);
    }
}
