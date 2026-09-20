# Pemeriksaan lanjutan GrowPOS — 13 September 2026

## Titik awal dan hasil

Pemeriksaan dimulai dari working tree bersih pada commit `7ac5fe8` (README), dengan implementasi utama pada commit sebelumnya. Perubahan pengguna dipertahankan. Pengujian awal: 79 tes PHP, tiga gagal; JavaScript, format frontend, dan build lulus.

Setelah perbaikan: **83 tes PHP / 406 assertion, 12 tes JavaScript**, pemeriksaan format, build Vite, dan `git diff --check` lulus. Seluruh migrasi sampai `2026_09_13_000004_add_operational_item_details` berstatus Ran; pemeriksaan lanjutan ini tidak menambahkan migrasi. Landing page pada `http://127.0.0.1:8765/` tampil dan log browser yang diperiksa tidak mencatat error JavaScript. Audit `growpos:secure-legacy-exports` menemukan nol berkas lama yang cocok dengan catatan ekspor publik.

## Perbandingan perbaikan

| Bagian | Sebelum | Sesudah | Mengapa / dampak jika dibiarkan |
|---|---|---|---|
| `app/Services/OrderStock.php`, `OrderPaymentService.php`, `CashOperationController.php` | Pembatalan bon dan expiry mengembalikan stok tanpa seluruh riwayat mutasi produk; pembayaran terlambat mengurangi lagi tanpa jejak lengkap. | Satu layanan untuk pelepasan dan pencadangan kembali stok produk/varian beserta bahan; jumlah sebelum/sesudah dan referensi item dicatat. | Stok fisik dapat benar tetapi riwayat audit tidak dapat menjelaskan perubahannya. Transisi status dan lock mencegah mutasi ulang pada callback duplikat. |
| `app/Http/Controllers/PosController.php` | Permintaan charge dilakukan dalam transaksi database. Timeout menghapus nota melalui rollback, padahal gateway mungkin sudah menerima charge. | Nota dan reservasi di-commit sebelum charge. Jika respons charge gagal, nota tetap tersimpan, reservasi dilepas dan respons 503 tidak membocorkan pesan exception. Konfirmasi pembayaran terlambat tetap dapat dicocokkan. | Referensi pembayaran bisa hilang; lock database tertahan selama jaringan menunggu. Pembayaran yang sudah dikonfirmasi tidak dibatalkan oleh timeout sesudahnya. |
| Validasi checkout QRIS dan fixture tes | Referensi checkout opsional; tes retry baru menyebut “key yang sama” tetapi belum mengirim key. | QRIS wajib UUID `checkout_key`; frontend memang sudah mengirimnya. Fixture tes sekarang mengirim UUID, dan retry memakai payload yang sama. | Mengirim ulang permintaan tanpa referensi stabil berisiko membuat nota/charge baru. Integrasi QRIS selain frontend GrowPOS perlu mengirim UUID dan mempertahankannya saat retry. |
| `resources/js/grow-pos.js`, `resources/views/pos/index.blade.php` | Respons QRIS unpaid tanpa gambar QR dapat masuk ke tampilan struk. | Tetap masuk panel menunggu pembayaran, tanpa gambar rusak; nota pending tanpa QR dapat ditemukan lagi setelah reload. | Kasir dapat mengira pembayaran telah selesai hanya karena respons berhasil diterima. |
| `app/Http/Controllers/ReportController.php`, `resources/views/reports/index.blade.php` | Modal tidak diketahui diperlakukan nol; pajak ikut dihitung sebagai laba; Blade menghitung ulang komisi dengan angka tetap. | HPP/laba ditandai belum tersedia ketika snapshot modal kurang. Estimasi laba mengecualikan pajak. Komisi QRIS dihitung per nota dengan tarif konfigurasi dan ditampilkan sebagai estimasi. | Laba terlihat terlalu tinggi atau berubah ketika harga modal katalog berubah. Nilai sebelum retur/biaya tetap diberi label; ini belum merupakan laporan laba bersih akuntansi lengkap. |
| `.gitignore` dan indeks Git | Cache, sesi login, serta view kompilasi sempat terlacak. | Runtime dan backup privat diabaikan; 11 berkas runtime dilepas dari indeks menggunakan `git rm --cached`, tanpa menghapus berkas lokal. | Data sementara dan sesi dapat ikut commit, serta diff dipenuhi hasil kompilasi. Riwayat commit lama tidak ditulis ulang oleh perubahan ini. |

## Pengujian penting

- Pembatalan bon dan callback expiry berulang: stok pulih sekali dan mutasi tercatat.
- Settlement setelah expiry: stok dicadangkan kembali dan wallet dikredit sekali.
- Timeout charge: nota cancelled/unpaid dipertahankan untuk rekonsiliasi, stok pulih, retry dengan UUID sama tidak memanggil gateway lagi.
- Settlement yang terjadi sebelum respons charge mengalami timeout: nota tetap paid dan stok tidak dilepas.
- Gateway dipanggil sesudah transaksi checkout selesai; diuji terhadap level transaksi dasar test runner.
- QRIS tanpa UUID ditolak sebelum charge.
- QRIS unpaid tanpa gambar tidak membuka struk pembayaran.
- Pajak 10% pada penjualan Rp10.000, modal Rp4.000: estimasi laba sebelum biaya/retur Rp6.000, bukan Rp7.000. Mengubah modal katalog sesudahnya tidak mengubah snapshot transaksi.
- Modal tanpa snapshot menghasilkan status belum tersedia, bukan laba palsu.

Perubahan tes checkout lama disengaja: kontrak lama “hapus semua nota saat gateway gagal” diganti kontrak “pertahankan referensi untuk rekonsiliasi”. Tes tidak sekadar dihapus; assertion ditambah untuk status, stok, retry, wallet, dan batas transaksi.

## Batas kesiapan launching yang masih perlu ditutup

Pekerjaan berikut belum dinyatakan selesai oleh pemeriksaan ini:

1. Uji bersamaan pada MySQL sebenarnya untuk dua kasir membeli stok terakhir, callback dan tutup shift. Tes SQLite di memori belum membuktikan perilaku lock MySQL lintas proses.
2. Penanganan proses mati sesudah nota di-commit tetapi sebelum charge dimulai: nota dapat dibuka/poll ulang, tetapi pemulihan otomatis terjadwal dan antrean rekonsiliasi pemilik belum lengkap.
3. Refund QRIS melalui penyedia pembayaran, pencatatan komisi historis, rekonsiliasi refund/pajak/HPP dalam laporan lengkap dan ekspor, serta pembayaran uang muka non-tunai.
4. Harga grosir, konversi kemasan/satuan dan kuantitas pecahan untuk barang timbang; pencetakan label barcode.
5. Transfer stok antaroutlet, otorisasi outlet yang lebih rinci dan laporan gabungan; pengingat jasa otomatis.
6. Restore backup MySQL ke database isolasi beserta pemeriksaan saldo/stok; monitoring produksi, uji perangkat printer/scanner, dan pilot pengguna.
7. Kesiapan operasional pemasaran: kontak bantuan nyata, kebijakan yang disetujui pemilik, serta pembuktian janji paket seperti dukungan 24/7/account manager. Data paket yang tampil belum menjadi bukti bahwa layanan manusia tersebut tersedia.

Tidak ada charge/refund nyata, pengiriman pesan ke pihak lain, deployment produksi, atau commit baru pada pemeriksaan ini. Penghapusan berkas runtime dari indeks sudah berada di staging; perubahan kode lainnya belum di-stage.
