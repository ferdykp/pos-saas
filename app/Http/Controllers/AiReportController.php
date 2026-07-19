<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;
use App\Services\GeminiService;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class AiReportController extends Controller
{
    protected GeminiService $gemini;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    public function index()
    {
        $tenantId = auth()->user()->tenant_id;

        if (!$tenantId) {
            $aiAnalysis = "<p class='italic text-center text-gray-400'>Silakan pilih atau daftarkan bisnis Anda terlebih dahulu untuk memulai analisis AI.</p>";
            return view('reports.ai-analysis', compact('aiAnalysis'));
        }

        // --- 1. DATA PRODUK TERLARIS (TOP 5) ---
        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.tenant_id', $tenantId)
            ->where('orders.created_at', '>=', now()->subDays(30))
            ->select('products.product_name', DB::raw('SUM(order_items.quantity) as total_terjual'))
            ->groupBy('products.product_name')
            ->orderBy('total_terjual', 'desc')
            ->take(5)
            ->get();

        // --- 2. DATA PRODUK KURANG LAKU (SLOW MOVING - BOTTOM 3) ---
        $bottomProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.tenant_id', $tenantId)
            ->where('orders.created_at', '>=', now()->subDays(30))
            ->select('products.product_name', DB::raw('SUM(order_items.quantity) as total_terjual'))
            ->groupBy('products.product_name')
            ->orderBy('total_terjual', 'asc')
            ->take(3)
            ->get();

        // --- 3. TREN OMSET HARIAN & JUMLAH TRANSAKSI ---
        $salesTrend = Order::where('tenant_id', $tenantId)
            ->where('created_at', '>=', now()->subDays(30))
            ->select(
                DB::raw('DATE(created_at) as tanggal'),
                DB::raw('SUM(grand_total) as total_omset'),
                DB::raw('COUNT(id) as total_nota'),
                DB::raw('DAYNAME(created_at) as nama_hari')
            )
            ->groupBy('tanggal', 'nama_hari')
            ->get();

        // --- 4. KALKULASI METRIK UTAMA KASIR (UNTUK DIKIRIM KE AI) ---
        $totalOmset = $salesTrend->sum('total_omset');
        $totalTransaksi = $salesTrend->sum('total_nota');
        $jumlahHariAktif = $salesTrend->count() > 0 ? $salesTrend->count() : 1;

        $rataRataOmsetHarian = $totalOmset / $jumlahHariAktif;
        $rataRataNilaiPerNota = $totalTransaksi > 0 ? ($totalOmset / $totalTransaksi) : 0;

        // Cari hari teramai berdasarkan performa nama hari
        $dayAnalysis = Order::where('tenant_id', $tenantId)
            ->where('created_at', '>=', now()->subDays(30))
            ->select(DB::raw('DAYNAME(created_at) as hari'), DB::raw('SUM(grand_total) as omset_hari'))
            ->groupBy('hari')
            ->orderBy('omset_hari', 'desc')
            ->get();

        $hariTeramai = $dayAnalysis->first()->hari ?? 'Tidak Terdeteksi';
        $hariTersepi = $dayAnalysis->last()->hari ?? 'Tidak Terdeteksi';

        // --- 5. KEMAS DATA BISNIS ---
        $dataBisnis = [
            'nama_toko' => auth()->user()->tenant->name,
            'tipe_bisnis' => auth()->user()->tenant->business_type,
            'ringkasan_30_hari_terakhir' => [
                'total_omset' => $totalOmset,
                'total_transaksi_nota' => $totalTransaksi,
                'rata_rata_omset_per_hari' => $rataRataOmsetHarian,
                'rata_rata_belanja_per_pelanggan_nota' => $rataRataNilaiPerNota,
                'hari_kinerja_tertinggi' => $hariTeramai,
                'hari_kinerja_terendah' => $hariTersepi,
            ],
            'produk_terlaris' => $topProducts,
            'produk_kurang_laku' => $bottomProducts
        ];

        // --- 6. ATUR PROMPT AI YANG LEBIH DETAIL DAN KAYA ELEMEN HTML ---
        $prompt = "Anda adalah Chief Business Analyst dan Konsultan Bisnis SaaS POS berskala Nasional.
        Analisis data keuangan dan inventaris dari toko berikut secara mendalam: " . json_encode($dataBisnis) . "

        Berikan laporan analisis profesional langsung dalam format HTML bersih. Gunakan tag pembungkus seperti <h3> untuk sub-judul, <p> untuk narasi, <strong> untuk penekanan data penting, <ul><li> untuk poin-poin, dan buatkan sebuah <table> statis HTML yang rapi untuk membandingkan metrik penjualan atau rangkuman data. Jangan gunakan format markdown (seperti ** atau ###) atau membungkus jawaban dengan tanda petik backtick ```html.

        Struktur laporan wajib memiliki bagian ini:
        1. Ringkasan Eksekutif Finansial (Sebutkan total omset, rata-rata harian, rata-rata per nota, dan analisis hari transaksi).
        2. Analisis Pergerakan Produk (Bahas korelasi antara produk terlaris dan produk yang kurang laku/slow-moving).
        3. Identifikasi Masalah & Peluang Tersembunyi (Misal: Mengapa nilai per nota rendah atau mengapa hari tertentu sepi).
        4. Taktik Aksi & Rekomendasi Promosi Spesifik (Berikan minimal 2 rekomendasi bundling menu inovatif, strategi pendorong transaksi di hari sepi, dan cara melikuidasi produk slow moving).";

        // --- ERROR HANDLING DENGAN FALLBACK YANG SAMA DETAILNYA ---
        try {
            $aiAnalysis = $this->gemini->generateAnalytic($prompt);
        } catch (\Exception $e) {
            // Ubah nama hari ke Bahasa Indonesia untuk Fallback
            $kamusHari = ['Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'];
            $hariIndoRamai = $kamusHari[$hariTeramai] ?? $hariTeramai;
            $hariIndoSepi = $kamusHari[$hariTersepi] ?? $hariTersepi;

            $produkJuara = $topProducts->first()->product_name ?? 'Produk Utama';
            $produkSepi = $bottomProducts->first()->product_name ?? 'Produk Tertentu';

            $aiAnalysis = "
                <div class='flex items-center gap-2 p-4 mb-6 text-xs border text-amber-800 bg-amber-50 rounded-xl border-amber-200'>
                    <i class='fa-solid fa-triangle-exclamation text-amber-500'></i>
                    <span>Koneksi AI sedang sibuk. Menampilkan Laporan Analisis Komputasi Lokal Sistem POS.</span>
                </div>

                <h3>Rangkuman Eksekutif Finansial</h3>
                <p>Berdasarkan rekaman performa 30 hari terakhir, <strong>" . auth()->user()->tenant->name . "</strong> berhasil membukukan total omset keseluruhan sebesar <strong>Rp " . number_format($totalOmset, 0, ',', '.') . "</strong> dari total <strong>" . $totalTransaksi . " kali transaksi</strong> sukses.</p>

                <table>
                    <thead>
                        <tr>
                            <th>Metrik Operasional</th>
                            <th>Nilai Statistik</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Rata-rata Omset Harian</td>
                            <td><strong>Rp " . number_format($rataRataOmsetHarian, 0, ',', '.') . "</strong> /hari</td>
                        </tr>
                        <tr>
                            <td>Rata-rata Nilai per Struk (Basket Size)</td>
                            <td><strong>Rp " . number_format($rataRataNilaiPerNota, 0, ',', '.') . "</strong> /transaksi</td>
                        </tr>
                        <tr>
                            <td>Hari dengan Penjualan Tertinggi</td>
                            <td>Hari " . $hariIndoRamai . "</td>
                        </tr>
                        <tr>
                            <td>Hari dengan Penjualan Terendah</td>
                            <td>Hari " . $hariIndoSepi . "</td>
                        </tr>
                    </tbody>
                </table>

                <h3>Analisis Produk & Inventaris</h3>
                <p>Produk penggerak utama omset Anda saat ini adalah <strong>" . $produkJuara . "</strong>. Di sisi lain, terdapat produk dengan perputaran stok yang cenderung lambat (*slow-moving*), salah satunya yaitu <strong>" . $produkSepi . "</strong>. Pola ini mengindikasikan adanya ketimpangan minat konsumen yang bisa memicu penumpukan modal mati pada gudang bahan baku.</p>

                <h3>Rekomendasi Aksi Sistem POS</h3>
                <ul>
                    <li><strong>Paket Bundling Lintas Kategori (*Cross-Selling*):</strong> Buat paket menu kombo yang menyatukan produk terlaris Anda (<em>" . $produkJuara . "</em>) dengan produk yang kurang laku (<em>" . $produkSepi . "</em>) dengan potongan harga tipis. Ini akan menaikkan nilai rata-rata per transaksi (*Basket Size*) sekaligus menghabiskan stok produk *slow-moving*.</li>
                    <li><strong>Stimulus Khusus Hari " . $hariIndoSepi . ":</strong> Mengingat hari " . $hariIndoSepi . " mencatatkan performa terendah, Anda bisa meluncurkan program promosi berkala seperti <em>'Happy Hour Diskon 15%'</em> atau promosi loyalitas khusus pelanggan pada hari tersebut untuk memicu lonjakan kunjungan.</li>
                </ul>
            ";
        }

        // Kamus penerjemah hari ke bahasa Indonesia
        $kamusHari = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu'
        ];
        $hariTeramaiIndo = $kamusHari[$hariTeramai] ?? $hariTeramai;
        $produkJuara = $topProducts->first()->product_name ?? 'Belum Ada';

        // Kirim semua variabel ke Blade agar Stat Cards menjadi Real-time
        return view('reports.ai-analysis', compact(
            'aiAnalysis',
            'rataRataOmsetHarian',
            'rataRataNilaiPerNota',
            'hariTeramaiIndo',
            'produkJuara'
        ));
    }
}
