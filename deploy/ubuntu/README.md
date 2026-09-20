# GrowPOS — paket deployment Ubuntu untuk growpos.com

Kontak bantuan: **kurferdy@gmail.com**. Target SSH yang diberikan: `fdev@ssh.fdevsite.cloud`. Deployment ditunda atas permintaan pemilik; tidak ada berkas server yang diubah.

## Prasyarat

- Ubuntu dengan PHP 8.3 atau versi yang sesuai composer.lock, PHP-FPM, ekstensi MySQL, mbstring, XML, curl, zip, gd, bcmath, intl; MySQL; Nginx; Composer; Node yang memenuhi package-lock/Vite.
- DNS growpos.com mengarah ke server dan sertifikat TLS valid untuk domain tersebut.
- User MySQL aplikasi khusus database growpos. Hak membuat/menghapus database hanya diperlukan oleh akun operator untuk uji isolasi, bukan oleh user aplikasi produksi.
- SMTP nyata, kredensial Midtrans produksi, dan konfigurasi callback OAuth diisi melalui kanal rahasia pada server. Jangan commit `.env`.
- Direktori contoh: `/srv/growpos/current`, dimiliki user deployment. Hanya `storage` dan `bootstrap/cache` yang dapat ditulis proses web. Gunakan storage persisten/shared bila menggunakan direktori release bergantian.

## Instalasi aplikasi

1. Salin release kode yang sudah diuji ke server. Jangan menyalin `.env` lokal, sesi, cache, database preview atau backup ke public root.
2. Salin `growpos.env.example` menjadi `.env` di direktori aplikasi dan isi nilai kosong. Tambahkan APP_KEY baru untuk instalasi baru; **pertahankan APP_KEY lama untuk upgrade**.
3. Jalankan `composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction`, `npm ci`, dan `npm run build`. Alternatif: kirim artefak build dari mesin build terpercaya.
4. Buat backup sebelum upgrade dengan `php artisan growpos:backup`. Salin backup dan checksum ke penyimpanan privat terpisah dari server aplikasi.
5. Jalankan `php artisan migrate --force`, `php artisan storage:link`, `php artisan config:cache`, `php artisan route:cache`, `php artisan view:cache`. Jangan jalankan `migrate:fresh` atau seeder demo pada database usaha.
6. Sesuaikan path aplikasi/socket PHP pada `growpos.nginx.conf`, pasang di Nginx, jalankan `nginx -t`, lalu reload setelah pemeriksaan berhasil.
7. Pasang unit systemd worker dan scheduler dari folder ini. Aktifkan worker dan timer. Worker harus dimuat ulang setelah release berubah (`php artisan queue:restart`).
8. Jalankan `php artisan growpos:launch-check`. Kegagalan harus diselesaikan sebelum membuka pendaftaran umum. Hasil lulus tidak menggantikan uji pembayaran, email, TLS dan perangkat.

## Worker, jadwal dan monitoring

- Scheduler menjalankan rekonsiliasi QRIS setiap menit dan backup database pukul 02:00 menurut zona waktu aplikasi. Pastikan `php artisan schedule:list` menunjukkan jadwal dan timer systemd benar-benar berjalan.
- Rekonsiliasi hanya memeriksa referensi transaksi yang sudah ada; tidak membuat charge baru. Periksa menu **Pemeriksaan pembayaran** untuk transaksi yang belum pasti.
- Pantau `/up` dari luar server untuk kesehatan proses aplikasi/TLS. Endpoint ini tidak membuktikan database, email, atau payment gateway sehat.
- Pantau status `growpos-worker.service`, timer scheduler, `php artisan queue:failed`, log aplikasi, ruang disk dan usia backup. Integrasi alert eksternal belum dikonfigurasi karena akses server ditunda.
- Uji restore dengan `php artisan growpos:verify-mysql-backup storage/app/private/backups/NAMA.sql` memakai akun operator. Perintah hanya menerima backup privat dengan checksum valid, memulihkan ke database bernama acak `growpos_verify_*`, memeriksa tabel/stok/referensi, lalu membersihkan database uji. Dump dengan perintah ganti database atau stored program ditolak; gunakan server restore terpisah untuk kasus tersebut.
- `php artisan growpos:verify-concurrency` membuat fixture sintetis di database sementara dan menjalankan proses checkout/callback/tutup shift bersamaan. Jalankan di staging atau mesin uji dengan MySQL dan izin operator, bukan menggunakan akun web produksi.

## Uji sebelum membuka pendaftaran umum

- HTTPS tanpa kesalahan, cookie secure, APP_DEBUG=false, halaman utama/login/register tidak kosong.
- Daftar akun, verifikasi email dan reset password benar-benar terkirim melalui SMTP produksi.
- Katalog, transaksi tunai, pembayaran bon, retur, tutup shift dan laporan sesuai hitungan kas.
- Dengan transaksi uji yang disetujui pemilik: QRIS live, callback, timeout/retry, saldo wallet, proses pencairan dan kebijakan refund. Tes otomatis menggunakan mock gateway, tidak memindahkan uang nyata.
- Cetak minimal satu nota panjang dan satu retur pada printer sesungguhnya; barcode scanner diuji pada kolom pencarian dengan akhiran Enter.
- Tinjau isi paket/marketing: dukungan 24/7, account manager dan integrasi API hanya boleh dipasarkan jika benar-benar tersedia.
- Kebijakan privasi, syarat layanan, retensi data dan tanggung jawab merchant ditinjau/disetujui pemilik sebelum publikasi. Paket ini tidak mengarang persetujuan legal atau ketersediaan tim support.

## Printer awal

Rekomendasi: Epson TM-T82IV, pilih varian USB untuk PC/laptop tunggal atau varian Ethernet jika diperlukan. Pastikan varian antarmuka sebelum pembelian. Sumber resmi: https://www.epson.co.id/For-Work/Printers/POS-Printers/Epson-TM-T82IV-Thermal-Receipt-Printer/p/C31CL47412

GrowPOS memakai driver OS dan dialog cetak browser. Di detail nota tersedia cetak 58 mm dan 80 mm; sesuaikan ukuran driver, skala 100%, dan matikan header/footer browser. Lebar CSS sudah mencakup padding. Belum ada sertifikasi cetak langsung Bluetooth/Android, silent printing atau drawer kick otomatis.

## Rollback

Simpan release lama dan backup sebelum migrasi. Jika hanya kode yang gagal, kembalikan release yang kompatibel dengan skema saat ini dan restart worker. Jangan otomatis menjalankan migration down untuk rollback keuangan: tabel kas/retur menyimpan audit. Pemulihan database memerlukan jendela pemeliharaan dan rencana menangani transaksi yang masuk setelah backup; uji restore selalu di target terpisah terlebih dahulu.
