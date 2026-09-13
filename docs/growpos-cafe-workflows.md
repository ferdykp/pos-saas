# Implementasi GrowPOS untuk kedai kecil

Tanggal: 12 September 2026. Perubahan diterapkan pada kode lokal. Database usaha dan pembayaran nyata tidak digunakan dalam pengujian.

## Perbandingan dan alasan perubahan

| Bagian | Sebelum | Sesudah | Alasan dan efek jika tidak diperbaiki | Lokasi utama |
|---|---|---|---|---|
| Kasir | Alur kasir bercampur navigasi administrasi | Terminal khusus, pencarian, kategori, favorit, tombol sentuh, keranjang responsif | Mengurangi langkah saat antrean ramai; navigasi padat memperlambat kasir | `resources/views/pos/index.blade.php`, `resources/js/grow-pos.js`, `resources/css/app.css` |
| Pesanan | Pilihan racikan belum menyatu dengan checkout | Varian ukuran, topping berbayar, catatan per item, jenis pesanan dan meja | Harga dan instruksi dapur mudah salah jika dicatat terpisah | `PosController`, `MenuConfigurationController`, `OrderPricing`, `menu/configure.blade.php` |
| Pesanan sementara | Belum ada pemulihan draft terminal | Simpan dan buka draft beserta racikan setelah reload | Kasir bisa kehilangan pesanan ketika melayani pelanggan lain | `grow-pos.js`, `pos-state.js` |
| Koneksi putus | Pengiriman checkout berisiko diulang sebagai transaksi baru | Payload tunai disimpan dahulu, referensi tetap, sinkronisasi ulang, konflik terlihat, unduh cadangan antrean | Menghindari kehilangan catatan dan pemotongan stok ganda akibat pengiriman ulang | `grow-pos.js`, `PosController`, migration `add_cafe_workflows` |
| QRIS menunggu | QR sulit dibuka kembali setelah halaman ditutup | Daftar QR tertunda milik kasir dimuat dari server; status diperiksa sebelum selesai | Kasir berisiko membuat pembayaran kedua saat transaksi pertama masih tertunda | `PosController::index`, `receiptResponse`, `grow-pos.js` |
| Waktu transaksi | Waktu ISO UTC perangkat dibandingkan dengan jam shift lokal | Perbandingan zona waktu eksplisit, waktu jual disimpan dalam zona aplikasi | Transaksi sah dapat ditolak sebagai sebelum shift; laporan harian dapat bergeser | `PosController::store` |
| Mulai berjualan | Pengguna harus mencari sendiri urutan setup | Panduan menu → shift → transaksi pertama, empat contoh menu, impor CSV/XLSX dan ekspor | Setup panjang membuat pengguna baru berhenti sebelum merasakan manfaat | `GettingStartedController`, `getting-started/index.blade.php`, `TenantController` |
| Impor menu | Belum tersedia alur impor ini | Template enam kolom, maksimal 500 menu/2 MB, validasi semua baris, penyimpanan atomik | File salah tidak boleh meninggalkan setengah katalog; batas paket tetap dihormati | `GettingStartedController` |
| Dapur | Belum ada antrean kerja terstruktur | Pesanan lunas masuk antrean → diproses → siap → disajikan, beserta topping dan catatan | Pesanan dapat terlewat atau dikerjakan tanpa instruksi racikan | `KitchenController`, `kitchen/index.blade.php` |
| Bahan baku | Penjualan belum terhubung resep | Resep per menu memotong bahan secara atomik; pembatalan QR memulihkan snapshot bahan | Stok bahan menyimpang dari penjualan dan bisa menjadi negatif | `RecipeStock`, `OrderPaymentService`, relasi `Product::materials` |
| Ringkasan pemilik | Ringkasan belum mengikat seluruh metrik ke periode dan modal historis | Periode konsisten, pembanding periode sebelumnya, menu terlaris, metode bayar, selisih shift, stok rendah | Keputusan usaha salah bila angka berasal dari periode berbeda atau modal hari ini dipakai untuk nota lama | `DashboardController`, `dashboard/index.blade.php` |
| Estimasi laba | Modal historis belum dibekukan pada item | Snapshot modal saat checkout; data tidak lengkap ditampilkan belum tersedia | Angka laba terlihat pasti meskipun modal tidak diketahui | `OrderItem::unit_cost`, `PosController`, `DashboardController` |
| Hak akses | Varian belum sepenuhnya dikunci pada produk/toko dan peran | Pengaturan menu admin saja, varian/topping/bahan harus milik toko dan produk terkait | Pengguna dapat mengubah menu yang bukan kewenangannya | `ProductVariantController`, `MenuConfigurationController`, `routes/web.php` |
| Pemasaran | Klaim generik dan angka contoh berpotensi dianggap bukti nyata | Fokus kedai, manfaat operasional, harga/durasi/kapasitas dari paket yang tersimpan | Ekspektasi calon pengguna dapat melampaui kemampuan produk | `landing.blade.php`, komponen `landing`, `auth/login.blade.php` |
| Bantuan | Panduan operasional belum terpusat | FAQ draft, antrean, konflik stok, QR, printer, shift dan paket | Kasir tidak tahu tindakan aman ketika transaksi tertahan | `help/index.blade.php` |

Perbaikan keamanan sebelumnya dijelaskan terpisah di `docs/perbaikan-pos-2026-09-12.md`.

## Contoh perilaku baru

Kopi Susu reguler Rp22.000, varian Large Rp27.000, topping Extra shot Rp5.000: total satu gelas Large + Extra shot adalah Rp32.000 sebelum pajak. Catatan “Less ice” ikut tersimpan di item dan ditampilkan di dapur/struk. Harga yang dikirim perangkat tidak dipercaya: server menghitung ulang dari katalog.

Jika respons checkout hilang, antrean menyimpan payload dengan `checkout_key` yang sama. Pengiriman ulang yang cocok mengembalikan nota lama, tidak membuat order atau mengurangi stok lagi. Payload berbeda dengan kunci sama ditolak. Konflik stok tidak disamarkan sebagai sukses.

## Validasi

- Backend: 57 tes, 212 assertions lulus pada SQLite terisolasi; termasuk otorisasi, harga server, replay pembayaran, pemulihan stok, varian/topping/resep, periode dashboard, impor atomik, UTC shift, dan isolasi daftar QR tertunda.
- JavaScript: 7 tes lulus, termasuk antrean setelah gangguan koneksi, konflik stok, larangan charge QR otomatis di latar belakang, serta pemulihan status QR.
- Build produksi Vite berhasil; PHP dirapikan dengan Pint.
- Browser pada lebar 390 px: terminal dan dashboard tidak memiliki overflow horizontal; racikan Large + Extra shot, draft setelah reload, dan checkout tunai Rp32.000 berhasil pada data contoh.
- Pengujian awal desktop juga menemukan dan memverifikasi perbaikan perbandingan waktu UTC dengan shift lokal.

Perintah pengujian aman:

```sh
APP_ENV=testing DB_CONNECTION=sqlite DB_DATABASE=:memory: DB_URL='' CACHE_STORE=array SESSION_DRIVER=array MAIL_MAILER=array QUEUE_CONNECTION=sync php artisan test
node --test tests/js/*.test.js
npm run build
```

## Menjalankan pada lingkungan tujuan

Cadangkan database, uji migrasi di staging, lalu jalankan `php artisan migrate --force` dan build aset pada proses rilis yang biasa digunakan. Ada tiga migrasi baru bertanggal 11–12 September. Migrasi belum dijalankan pada database usaha. Jangan jalankan `migrate:fresh` untuk data usaha.

`CafePreviewSeeder` hanya menerima lingkungan local/testing dan database SQLite dengan nama mengandung `growpos-preview`. Ia memerlukan variabel `GROWPOS_DEMO_PASSWORD`, membuat data sintetis untuk `preview@growpos.test`, dan tidak dipanggil seeder utama. Gunakan hanya database pratinjau kosong; jangan menjalankannya sebagai seed bisnis.

## Batas kemampuan yang perlu diketahui

- Offline saat ini berlaku untuk transaksi tunai pada halaman kasir yang sudah terbuka. Bukan PWA penuh: membuka ulang halaman tanpa jaringan belum didukung. Draft/antrean tersimpan dalam browser perangkat ini, terpisah per toko dan pengguna; menghapus data browser menghapusnya. Gunakan satu tab kasir per akun/perangkat untuk mencegah penulisan localStorage bersamaan.
- Antrean belum masuk laporan sampai server menerima transaksi. Stok, paket aktif, dan shift diperiksa ulang. Konflik memerlukan pemeriksaan pemilik; jangan menagih pelanggan lagi. Sinkronkan semua perangkat sebelum menutup shift.
- QRIS memerlukan internet. Pemulihan QR yang sudah tercatat tersedia; respons charge gateway yang ambigu masih perlu dicocokkan dengan Midtrans sebelum tindakan manual. Integrasi Midtrans nyata, termasuk timeout/duplikasi charge, belum diuji di sandbox penyedia.
- Resep memakai satuan bulat terkecil, misalnya gram/ml, dan berlaku sama untuk seluruh varian produk. Resep terpisah untuk topping/ukuran belum didukung.
- Modal varian dan nota lama tanpa snapshot tidak ditebak. Estimasi laba kotor belum menghitung biaya operasional; bukan laba bersih.
- Dapur dan dashboard diperbarui saat halaman dimuat/di-refresh; belum memakai pembaruan real-time.
- Cetak menggunakan dialog browser. Printer fisik belum diuji. Penguncian transaksi perlu uji konkurensi pada MySQL staging karena SQLite tidak membuktikan perilaku lock produksi.
- Tidak ada klaim jumlah pengguna/pelanggan baru. Daya tarik dan retensi harus dibuktikan melalui pilot kedai: ukur waktu setup, durasi checkout, transaksi gagal, pemakaian ulang mingguan, dan tiket bantuan.
