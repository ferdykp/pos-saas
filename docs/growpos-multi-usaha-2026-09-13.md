# GrowPOS untuk toko, jasa, kuliner, dan usaha campuran

## Perubahan yang diimplementasikan

| Bagian | Sebelum | Sesudah | Alasan dan dampak | Lokasi utama |
|---|---|---|---|---|
| Jenis usaha | Teks jenis usaha belum mengatur pengalaman produk | Profil retail, kelontong, jasa, kuliner, campuran; kompatibel dengan nama jenis usaha lama | Pengguna memperoleh istilah dan contoh yang relevan tanpa kehilangan data lama | `app/Support/BusinessProfile.php`, `app/Models/Tenant.php` |
| Pengaturan | Fitur kedai muncul untuk semua pengguna | Admin memilih modul barang, jasa, dan kuliner secara terpisah | Mengurangi navigasi yang tidak relevan; usaha campuran tetap dimungkinkan | `BusinessProfileController`, `resources/views/business/edit.blade.php` |
| Katalog | Barang/jasa tersedia di schema, tetapi validasi dan UI tidak konsisten | Barang dan layanan dalam satu katalog; tipe jasa selalu tanpa stok barang | Jasa tidak lagi bisa menyimpan pelacakan stok yang menyesatkan | `ProductRequest`, `ProductController` |
| Barcode | SKU ditampilkan seolah sekaligus barcode; barcode tidak tersimpan dari formulir | Barcode terpisah, pencarian dan Enter untuk memilih kode yang tepat; kode ambigu ditolak | Mempermudah pemindaian barang retail tanpa menambahkan produk yang salah | Form produk, `grow-pos.js`, tampilan kasir |
| Transaksi campuran | Pengalaman didominasi menu F&B | Barang dan jasa pada satu nota, filter jenis item, pengurangan stok hanya barang | Mendukung toko yang juga menjual pemasangan atau layanan lainnya | `PosController`, `resources/views/pos/index.blade.php` |
| Jasa | Belum ada antrean pengerjaan umum pada kasir baru | Menunggu → dikerjakan → siap diserahkan → selesai; petugas dari toko sendiri | Pengerjaan dapat dipantau tanpa mengubah status pembayaran | `ServiceJobController`, `resources/views/services/index.blade.php`, `Order` |
| Dapur | Semua transaksi baru diberi status antrean dapur | Transaksi kuliner yang berisi barang dapat masuk dapur; transaksi jasa saja tidak | Toko non-F&B tidak lagi otomatis membuat pekerjaan dapur | `PosController`, sidebar dan kasir |
| Onboarding | Semua contoh berupa kopi dan makanan | Contoh sesuai jenis usaha; campuran menyertakan barang dan jasa | Pengguna baru tidak harus menghapus contoh yang tidak relevan | `BusinessProfile::samples`, `GettingStartedController` |
| Impor/ekspor | Enam kolom tanpa tipe item | Kolom type product/service dipertahankan saat ekspor/impor; enam kolom lama tetap diterima | Impor jasa tidak berubah menjadi barang; kompatibilitas template lama dijaga | `GettingStartedController` |
| Akses data | Validasi kategori produk dan API jasa lama menerima data terlalu luas | Kategori/order/petugas dibatasi pada tenant; API jasa lama tidak dapat memindahkan order atau tenant lewat request | Menghindari hubungan data antartoko yang salah | `ProductRequest`, `ServiceOrderController`, `ServiceJobController` |
| Identitas | Landing, login, dashboard berorientasi kedai | Pesan toko, jasa, kuliner; istilah usaha dan katalog | GrowPOS tidak lagi diposisikan hanya untuk kafe | Landing components, login, dashboard, panduan |

Modul operasional mengatur pengalaman penggunaan, bukan menggantikan hak akses peran atau batas paket. Menyembunyikan modul tidak menghapus produk maupun transaksi. Nama usaha lama juga tidak diganti otomatis.

## Migrasi yang diperlukan

`database/migrations/2026_09_13_000001_add_business_modules_to_tenants.php` menambah:

- `tenants.business_modules`: JSON nullable untuk pilihan fitur.
- `orders.service_status`: string nullable dan index untuk status pengerjaan.
- `orders.assigned_user_id`: foreign key nullable ke users; jika petugas dihapus, penugasan menjadi null.

Migrasi telah berhasil diuji di SQLite pratinjau. Setelah pengguna memberikan persetujuan eksplisit untuk aktivasi pada database GrowPOS, perintah migrasi dijalankan kembali dan mengembalikan Nothing to migrate. Pemeriksaan status menunjukkan migrasi ini sudah tercatat pada batch 3 (Ran). Cache rute dan tampilan berhasil dibersihkan, rute business/services terdaftar, dan build aset berhasil. Tidak ada migrasi ulang atau penghapusan data yang dilakukan.

Perintah aktivasi yang digunakan (untuk rilis di lingkungan lain, periksa target dan backup terlebih dahulu):

```sh
php artisan migrate --force --path=database/migrations/2026_09_13_000001_add_business_modules_to_tenants.php
php artisan route:clear
php artisan view:clear
npm run build
```

Kode checkout baru memerlukan kolom migrasi tersebut. Jangan merilis checkout baru ke lingkungan lain sebelum schema siap.

## Validasi

Tes mencakup pemetaan jenis usaha lama, perubahan profil admin tanpa kehilangan katalog, penolakan perubahan oleh kasir, transaksi campuran dan stok jasa, sampel jasa, impor campuran, validasi kategori lintas tenant, pengerjaan jasa dan petugas, isolasi API jasa lama, barcode ambigu, dan filter barang/jasa. Hasil jumlah tes terakhir dicatat pada respons penyelesaian.

Uji browser pada SQLite pratinjau: profil kuliner berhasil diganti menjadi campuran; tautan dapur hilang, pengerjaan jasa muncul, katalog lama tetap ada; kasir menampilkan filter Barang & jasa serta opsi transaksi non-meja.

## Batas cakupan

Fondasi lintas jenis usaha, barang/jasa, barcode, serta pengerjaan umum sudah diimplementasikan. Fitur khusus lanjutan seperti konversi satuan ecer/grosir, harga bertingkat, kuantitas desimal untuk barang timbang, retur terpadu, jadwal reservasi, uang muka/pelunasan parsial belum ditambahkan oleh perubahan ini. Checkout saat ini tetap mengikuti aturan pembayaran yang sudah ada; status pekerjaan tidak mewakili pelunasan.

Untuk usaha campuran yang juga mengaktifkan kuliner, transaksi berisi barang mengikuti antrean dapur tingkat nota; belum ada penandaan per produk untuk memilih barang mana yang perlu disiapkan. API laundry lama tetap dipertahankan untuk kompatibilitas, sedangkan antrean umum baru menggunakan status pada order. Riwayat lama tidak otomatis diubah menjadi pekerjaan jasa baru.
