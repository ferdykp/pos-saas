# Perbaikan POS — 12 September 2026

Dokumen ini membandingkan implementasi awal dengan perubahan pada working tree. Fokus: tujuh temuan evaluasi sebelumnya dan delapan kegagalan test. Kode belum dideploy dan database operasional belum dimigrasikan.

## 1. Pisahkan admin platform dari admin toko

**Mengapa:** `role=admin` adalah peran toko (bahkan default registrasi), tetapi sebelumnya digunakan untuk akses persetujuan penarikan seluruh tenant.

**Jika dibiarkan:** admin toko dapat melihat permintaan penarikan tenant lain dan mengubah statusnya tanpa kewenangan platform.

**Sebelum:** route `/withdrawals/*` memakai middleware `admin`; controller memakai `withoutTenantScope()`.

**Sesudah:** route memakai `can:platform-admin`. Kolom baru `users.is_platform_admin` default `false`, dicast ke boolean dan tidak berada di `$fillable`. Admin platform tidak perlu langganan toko untuk menjalankan tugas platform. Filter tenant sengaja tetap dilepas di controller ini karena lintas toko memang kewenangannya, setelah gate memverifikasi akses.

**Bagian diperbaiki:** `routes/web.php`, `app/Providers/AppServiceProvider.php`, `app/Models/User.php`, sidebar, migrasi `2026_09_11_000001_add_platform_admin_to_users.php`.

**Contoh:** admin Toko A membuka `/withdrawals` → dahulu dapat melihat semua pengajuan; sekarang HTTP 403. Akun dengan izin platform eksplisit tetap bisa menyetujui pengajuan.

## 2. Batasi rekening dan penarikan toko

**Mengapa:** login dan langganan aktif tidak berarti pengguna berhak mengelola rekening penerima.

**Jika dibiarkan:** kasir dapat mengganti rekening tujuan lalu mengajukan penarikan saldo toko.

**Sebelum:** semua pengguna tenant aktif dapat memanggil `/finance/settings` dan `/finance/withdraw`.

**Sesudah:** seluruh route keuangan toko memakai `can:manage-finance`, dengan syarat peran admin dan tenant aktif pada akun. Menu mengikuti gate yang sama. Proteksi saldo/transaction pada controller keuangan tetap dipertahankan.

**Bagian diperbaiki:** `routes/web.php`, `app/Providers/AppServiceProvider.php`, `resources/views/layouts/sidebar.blade.php`.

**Contoh:** request kasir untuk rekening baru → HTTP 403; admin toko masih bisa menyimpan rekening dan mengajukan penarikan, dengan saldo berkurang sesuai nominal.

## 3. Hitung checkout di server

**Mengapa:** harga dan total dari JavaScript dapat diubah oleh pengirim request.

**Jika dibiarkan:** transaksi berharga murah palsu, penambahan stok melalui kuantitas negatif, dan referensi produk/customer tenant lain dapat masuk ke database.

**Sebelum:**

```php
$grandTotal = (int) $request->grand_total;
'price' => $item['price'];
$product->decrement('stock', $item['quantity']);
```

**Sesudah:**

- Validasi item tidak kosong, ID tidak duplikat, kuantitas bilangan bulat positif, metode pembayaran, uang diterima dan tipe pesanan.
- Produk harus aktif dan milik tenant; customer juga harus milik tenant.
- `OrderPricing` mengambil harga dan promo yang masih berlaku dari database. Diskon dibatasi 0 sampai harga produk.
- Pajak berasal dari pengaturan toko. Unit harga, diskon dan pajak memakai pembulatan rupiah utuh yang sama pada layar dan backend.
- `grand_total` browser hanya menjadi pemeriksaan kesesuaian jika dikirim; selisih ditolak HTTP 422, bukan dipakai sebagai nilai order. Harga/nama item dan subtotal browser diabaikan.
- Checkout wajib punya shift terbuka milik kasir. Nomor invoice baru menggunakan UUID.
- Kuota paket diperiksa setelah penguncian tenant, agar dua request tidak sama-sama melewati kuota terakhir.
- Katalog/settings POS dibaca saat membuka halaman; promo yang dihitung pada saat lama tidak lagi tersimpan di cache 24 jam.

**Bagian diperbaiki:** `app/Http/Controllers/PosController.php`, `app/Services/OrderPricing.php`, `resources/views/pos/index.blade.php`.

**Contoh:** kopi Rp10.000, diskon 10%, pajak 10% → total Rp9.900. Request berisi `price=1` tetap memakai harga database. Request mengklaim total Rp1 ditolak dan tidak memotong stok.

## 4. Settlement pembayaran atomik dan idempotent

**Mengapa:** polling sebelumnya memakai `lockForUpdate()` di luar transaksi; status paid dan kredit saldo tidak disimpan sebagai satu kesatuan. Webhook dan polling memiliki implementasi terpisah.

**Jika dibiarkan:** request bersamaan dapat menggandakan saldo/poin; kegagalan di tengah proses dapat menghasilkan order paid tanpa saldo.

**Sebelum:** polling membaca unpaid → update paid → menambah saldo sendiri. Webhook melakukan rangkaian serupa dengan kode lain.

**Sesudah:** keduanya memanggil `OrderPaymentService::apply()`. Service membuka transaksi, mengunci order, memverifikasi invoice dan nominal, lalu memeriksa status terbaru. Jika sudah paid, tidak ada kredit ulang. Status paid, `paid_amount`, `withdrawal_status`, saldo, dan poin diubah dalam transaksi yang sama. `capture` hanya diterima jika fraud status `accept`.

Wallet baru dibuat dengan saldo nol menggunakan `insertOrIgnore()`, kemudian saldo di-increment. Ini memperbaiki ekspresi `balance + nominal` pada INSERT wallet yang sebelumnya belum ada. Komisi memakai konfigurasi yang sama, dengan pembulatan rupiah utuh.

`CustomerPoints` menyatukan aturan poin. `MidtransGateway` memisahkan panggilan SDK agar test dapat mensimulasikan pembayaran tanpa uang sungguhan. Route alias cek pembayaran yang merujuk method tidak tersedia juga diarahkan ke method yang benar.

**Bagian diperbaiki:** `app/Services/OrderPaymentService.php`, `CustomerPoints.php`, `MidtransGateway.php`, `PosController.php`, `PaymentCallbackController.php`, `app/Models/Order.php`, `routes/web.php`.

**Contoh:** pembayaran Rp10.000, komisi 1,5% → saldo bertambah Rp9.850. Dua webhook dan satu polling untuk order tersebut tetap menghasilkan Rp9.850, bukan Rp19.700/Rp29.550.

## 5. Pulihkan stok pembayaran batal tepat sekali

**Mengapa:** checkout memotong stok tetapi status cancel/deny/expire sebelumnya tidak memulihkannya. Selain itu `manage_stock` dipakai controller tetapi belum ada dalam migrasi/model.

**Jika dibiarkan:** QRIS yang tidak dibayar menghabiskan stok secara semu, atau pilihan pelacakan stok tidak pernah tersimpan.

**Sebelum:** callback pembatalan hanya mengubah `order_status` menjadi cancelled.

**Sesudah:** produk menyimpan `manage_stock`. Item mencatat `reserved_stock`, yaitu jumlah yang benar-benar dipotong. Pembatalan mengembalikan jumlah tersebut di transaksi yang mengunci order; callback berikutnya tidak mengembalikannya lagi. Produk tanpa pelacakan stok tidak disentuh. Pembatalan terlambat tidak membatalkan order yang sudah paid. UI menutup QRIS ketika status cancelled diterima.

Settlement yang datang setelah cancelled mencoba mengambil kembali stok yang sempat dipulihkan. Jika stok tidak mencukupi, transaksi dibatalkan, masalah dicatat di log dan respons 503 diberikan untuk rekonsiliasi/retry; sistem tidak mengarang stok atau diam-diam mengakui pembayaran telah selesai diproses.

**Bagian diperbaiki:** `OrderPaymentService.php`, `PosController.php`, model `Product` dan `OrderItem`, migrasi `2026_09_11_000002_track_reserved_stock.php`, tampilan POS.

**Contoh:** stok 10 → checkout QRIS 1 item menjadi 9 → expire menjadi 10 → expire kedua tetap 10.

## 6. Pisahkan penjualan setiap shift

**Mengapa:** checkout tidak mengisi `shift_id`, sedangkan rekap memakai `shift_id = X OR created_at BETWEEN ...` tanpa filter kasir.

**Jika dibiarkan:** dua kasir yang bekerja bersamaan masing-masing bisa dibebani penjualan kasir lainnya dan terlihat memiliki selisih uang palsu.

**Sebelum:** semua penjualan tunai tenant dalam rentang waktu shift berpotensi dihitung.

**Sesudah:** hanya order completed/paid/cash dengan tenant, user dan shift yang sesuai yang dihitung. Shift aktif dibaca berdasarkan identitas kasir, bukan mempercayai ID dalam session. Buka/tutup shift dan checkout memakai penguncian yang konsisten. Membuka dua kali ditolak; checkout setelah tutup shift ditolak. Endpoint `shifts/current` kini memiliki implementasi.

**Bagian diperbaiki:** `ShiftController.php`, `PosController.php`, `$fillable` pada `app/Models/Order.php`.

**Contoh:** kasir A dan B masing-masing menjual Rp10.000. Modal A Rp100.000 → kas yang diharapkan A Rp110.000, bukan Rp120.000.

## 7. Benahi penerimaan webhook

**Mengapa:** pengecualian CSRF tidak cocok dengan route web; exception database dikembalikan sebagai HTTP 200.

**Jika dibiarkan:** callback web dapat tertolak CSRF, atau pembayaran gagal dicatat tetapi pengirim menganggap notifikasi sudah diterima.

**Sebelum:** pengecualian `midtrans-callback`; route `/midtrans/callback`. Exception → 200 dan isi error internal dikirim ke klien.

**Sesudah:** pengecualian tepat `midtrans/callback`. Endpoint API tetap tersedia. Payload divalidasi; server key wajib terkonfigurasi; signature dibandingkan dengan `hash_equals`. Data pembayaran tidak sesuai → 422; signature salah → 403; kegagalan internal atau order belum tersedia → 503 agar bisa dikirim ulang. Pesan internal tidak dibocorkan pada respons. CSRF route lain tetap aktif.

**Bagian diperbaiki:** `bootstrap/app.php`, `PaymentCallbackController.php`.

**Bukti khusus:** test memaksa middleware CSRF berjalan seperti request biasa; webhook mencapai verifikasi signature sedangkan POST login tanpa token tetap ditolak 419.

## 8. Perbaiki pengujian lama dan tambahkan regresi POS

Delapan kegagalan awal tidak semuanya bug runtime:

- Empat assertion redirect masih mengharapkan dashboard/profile lama. Test disesuaikan ke alur yang sudah ada: setup bisnis untuk pengguna baru dan profile/edit setelah perubahan profil.
- Tiga test reset password mencari class notifikasi bawaan, sedangkan aplikasi mengirim subclass anonim. Notifikasi kustom dipindahkan ke class bernama, branding email dipertahankan, dan test memeriksa class yang benar beserta token resetnya.
- Test landing tidak menyiapkan tabel plans. `RefreshDatabase` ditambahkan.

Test baru meliputi penolakan admin tenant di route platform, kasir di route keuangan, privilege mass assignment, alur admin yang sah, validasi checkout, produk/customer lintas tenant, stok, shift, settlement berulang melalui webhook dan polling, rollback saat exception, signature, nominal, fraud status, poin, dan CSRF.

**Bagian diperbaiki:** `tests/Feature/Auth`, `tests/Feature/ProfileTest.php`, `tests/Feature/ExampleTest.php`, `tests/Feature/Pos`, `app/Notifications`, `app/Models/User.php`.

## Hasil verifikasi

- Sebelum: 25 test, 17 lulus, 8 gagal.
- Sesudah: 48 test, 48 lulus, 159 assertion, SQLite in-memory.
- Build frontend (`npm run build`) berhasil; format PHP dibenahi dengan Pint; `git diff --check` bersih.
- Panggilan Midtrans pada test menggunakan mock; tidak ada pembayaran atau penarikan sungguhan.
- Test pengiriman ulang mencakup kedua urutan webhook/polling. Konkurensi proses MySQL sungguhan dan integrasi Midtrans sandbox belum diuji; SQLite tidak membuktikan perilaku row lock MySQL.

## Penerapan pada lingkungan operasional

1. Deploy kode bersama dua migrasi baru lalu jalankan `php artisan migrate`. Migrasi belum dijalankan pada database operasional oleh perubahan ini.
2. Berikan `is_platform_admin=true` hanya pada akun operator platform yang telah ditentukan, melalui administrasi server/database tepercaya. Tidak ada akun yang otomatis dipromosikan dan atribut ini tidak dapat diisi lewat mass assignment/form biasa.
3. Produk lama default `manage_stock=false` untuk mempertahankan perilaku yang sebelumnya tidak menyimpan flag tersebut. Verifikasi saldo stok fisik lalu aktifkan pelacakan melalui edit produk yang memang memerlukan stok. Produk baru mengikuti pilihan formulir.
4. Order lama dengan `shift_id=NULL` tidak dimasukkan secara tebakan ke shift; order item lama memakai `reserved_stock=0`, sehingga callback tidak menambahkan stok historis yang belum terbukti pernah dipotong. Rekonsiliasi data lama perlu memakai catatan operasional yang sah.
5. Arahkan notifikasi Midtrans ke `/api/midtrans/callback` atau `/midtrans/callback` pada domain aplikasi. Keduanya memeriksa signature.
6. Jalankan skenario staging dengan dua kasir dan Midtrans sandbox sebelum menerapkan ke transaksi nyata. Checkout QRIS masih melakukan charge SDK dalam transaksi database seperti alur awal; timeout jaringan dengan hasil charge yang tidak pasti tetap memerlukan pencocokan ke gateway sebelum transaksi diulang.

Perubahan ini menyelesaikan temuan kode yang disebut dalam evaluasi; bukan audit seluruh fitur, uji beban, atau jaminan bahwa data historis sudah terkoreksi.
