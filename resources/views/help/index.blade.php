<x-app-layout>
@section('title', 'Panduan usaha')
<div class="gp-page"><div class="gp-eyebrow">TEMAN OPERASIONALMU</div><h1 class="gp-title">Panduan singkat GrowPOS.</h1><p class="gp-muted">Jawaban yang bisa langsung dipakai saat usaha sedang beroperasi.</p>
<div class="gp-grid gp-section" style="max-width:850px">
@foreach([
['Mulai berjualan', 'Masukkan produk/layanan atau impor Excel dari halaman Mulai berjualan. Periksa harga, modal, pajak, dan stok. Di kasir, buka shift dengan jumlah uang awal di laci.'],
['Varian, topping, dan resep', 'Admin membuka Menu & resep untuk menambahkan pilihan ukuran, harga topping, dan jumlah bahan per porsi. Stok bahan memakai satuan integer: gunakan gram atau ml. Resep dasar berlaku juga untuk varian. Modal varian belum dihitung; estimasi laba ditandai belum lengkap.'],
['Transaksi tunai saat koneksi putus', 'Selama layar kasir masih terbuka, transaksi tunai dapat disimpan sebagai antrean di perangkat. Nota lokal belum merupakan transaksi server. Jangan hapus data browser atau keluar akun sebelum antrean tersinkron. Buka kembali kasir dengan akun dan toko yang sama untuk mencoba sinkronisasi.'],
['Antrean gagal disinkronkan', 'Harga, stok, dan shift tetap diperiksa server. Perubahan harga, stok habis, atau shift yang sudah ditutup dapat menahan transaksi. Jangan menerima pembayaran kedua kali. Simpan unduhan antrean dan minta admin memeriksa penyebab, kemudian coba lagi dengan referensi yang sama.'],
['Pembayaran QRIS', 'QRIS membutuhkan koneksi dan konfirmasi server. Jangan menganggap pembayaran lunas hanya berdasarkan layar pelanggan. Setelah status lunas diterima, cetak struk. Jika terjadi gangguan, periksa transaksi yang sama; jangan membuat pembayaran baru sebelum statusnya jelas.'],
['Simpan pesanan sementara', 'Tombol Simpan pesanan menyimpan keranjang di perangkat tanpa memotong stok atau menerima pembayaran. Buka kembali pesanan, periksa harganya, lalu lanjutkan pembayaran. Draft tidak berpindah antarperangkat.'],
['Jenis usaha dan fitur', 'Admin dapat membuka Pengaturan usaha untuk memilih retail, kelontong, jasa, kuliner, atau campuran. Fitur dapur bersifat pilihan. Mengubah pengaturan tidak menghapus katalog atau transaksi.'],
['Barang dan jasa', 'Pilih tipe Barang Fisik atau Jasa saat mengisi katalog. Jasa tidak mengurangi stok barang. Keduanya bisa berada dalam satu nota. Untuk barcode, fokuskan kolom pencarian kasir lalu pindai kode dan Enter.'],
['Antrean dapur (kuliner)', 'Jika fitur kuliner diaktifkan, pesanan lunas masuk ke dapur. Gunakan tombol Mulai buat → Tandai siap → Sudah disajikan. Muat ulang untuk melihat pesanan terbaru.'],
['Cetak struk', 'Hubungkan printer melalui perangkat dan driver yang tersedia. Klik Cetak struk untuk membuka dialog cetak browser. Gunakan kertas 58 mm, margin minimum, dan matikan header/footer browser. Lakukan satu uji cetak sebelum membuka toko.'],
['Tutup shift', 'Selesaikan sinkronisasi di setiap perangkat terlebih dahulu. Hitung uang fisik, masukkan ke dialog tutup shift, dan periksa selisih. Rekap kas memakai transaksi tunai lunas pada shift tersebut.'],
['Data dan biaya langganan', 'Katalog dapat diekspor dari Mulai berjualan; laporan tersedia di menu Laporan. Harga, masa berlaku, dan batas paket tercantum pada halaman Langganan. Nilai penjualan QRIS dan saldo pencairan berbeda jika ada komisi platform.']
] as [$title,$body])<details class="gp-card"><summary class="font-semibold cursor-pointer">{{ $title }}</summary><p class="gp-muted mt-3">{{ $body }}</p></details>@endforeach
</div><div class="gp-row gp-section"><a class="gp-btn gp-primary" href="{{ route('getting-started') }}">Mulai berjualan</a><a class="gp-btn" href="{{ route('billing.index') }}">Lihat paket & batas penggunaan</a></div></div></x-app-layout>
