# Tailwind untuk aplikasi, CSS mandiri untuk cetak

Seluruh tampilan aplikasi tetap menggunakan Tailwind, termasuk halaman invoice billing, formulir, dashboard, kasir, dialog, dan email. Pengecualian CSS manual hanya `resources/views/orders/print.blade.php`, yaitu halaman cetak struk/nota transaksi. Tailwind mendukung print, tetapi pemisahan ini mengikuti pilihan pemilik proyek dan menghilangkan ketergantungan aset aplikasi pada struk.

| Bagian yang diubah | Sebelum | Sesudah | Alasan dan dampak |
|---|---|---|---|
| `resources/views/orders/print.blade.php` | Memuat stylesheet Tailwind lewat Vite | CSS cetak tertanam, font monospace sistem, kelas ukuran 58/80 mm | Struk tetap memiliki ukuran, garis, dan tata letak saat aset aplikasi/CDN tidak tersedia. Tanpa pemisahan, stylesheet gagal dimuat dapat membuat hasil cetak kehilangan format. |
| Tabel dan teks struk | Layout mengikuti utility aplikasi | Tabel berukuran tetap, teks panjang dapat terbungkus, nominal rata kanan | Mengurangi risiko invoice atau nama produk panjang keluar batas kertas. |
| Tombol dan halaman cetak | Aturan halaman berada dalam konfigurasi Tailwind global | Tombol disembunyikan saat print, margin halaman hanya didefinisikan di dokumen struk | Aturan cetak tidak ikut terbawa ke seluruh halaman aplikasi. |
| `tailwind.config.js` | Memuat aturan `@page receipt` | Hanya konfigurasi styling aplikasi | Memisahkan tanggung jawab styling aplikasi dan dokumen cetak. |
| Tes styling dan printer | Semua view dilarang memakai CSS manual | Satu pengecualian dengan path spesifik; tes struk memeriksa dua ukuran, border-box, tombol print, dan tidak adanya link aset | Pengecualian tidak melebar ke view lain; regresi ketergantungan Vite pada struk dapat terdeteksi. |

Ukuran kertas tetap divalidasi server: hanya 58 dan 80 yang diterima, dengan default 58 mm. Otorisasi nota tetap mengikuti tenant. Dialog cetak otomatis tetap menunggu halaman selesai dimuat dan font siap.

Email tetap ditulis menggunakan utility Tailwind dan dikonversi ke inline style otomatis oleh Laravel demi kompatibilitas email. `resources/css/app.css` hanya berisi directive Tailwind; CSS hasil build tetap diperlukan browser. Alpine juga dapat menulis state tampilan sementara ketika menjalankan `x-show` atau transisi.

Pilih ukuran kertas yang sesuai pada driver printer, gunakan margin minimum, dan matikan header/footer browser. Pengujian otomatis tidak menggantikan uji cetak pada printer fisik.
