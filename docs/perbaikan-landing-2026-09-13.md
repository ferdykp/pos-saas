# Perbaikan halaman putih dan ketahanan GrowPOS

## Penyebab yang terkonfirmasi

Browser menerima HTML halaman utama, tetapi `body` masih memiliki `x-cloak`. CSS global `[x-cloak] { display: none !important }` menyembunyikan seluruh halaman. Elemen body berada di luar komponen Alpine yang menghapus atribut tersebut. Pemeriksaan browser sebelum perbaikan menunjukkan `bodyDisplay: none`, `bodyCloaked: true`, tanpa error JavaScript. Tes HTTP 200 lama tidak mampu mendeteksi konten yang tersembunyi.

## Perbandingan perubahan

| Bagian | Sebelum | Sesudah | Alasan / dampak jika dibiarkan | Lokasi |
|---|---|---|---|---|
| Landing page | Seluruh body memakai x-cloak | Konten utama langsung dirender, x-cloak hanya untuk UI bersyarat | Pengunjung melihat halaman putih meskipun respons server sukses | `resources/views/landing.blade.php` |
| Animasi | Hanya menunggu DOMContentLoaded; CSS bisa menyembunyikan konten sebelum animasi siap | Helper menangani DOM yang sudah siap; konten terlihat sampai inisialisasi berhasil; menghormati reduced motion | Modul yang terlambat atau gagal dapat menyembunyikan fitur/harga | `resources/js/app.js`, `resources/js/dom-ready.js`, `resources/css/app.css` |
| Navigasi publik | Footer berisi tautan # tanpa tujuan; tombol mobile bergantung ikon eksternal | Tautan nyata, tujuan Tentang Kami unik, tombol menu dengan expanded state dan fallback noscript | Pengunjung tidak dapat mengakses tujuan dan tidak mendapat umpan balik | `resources/views/components/landing/navbar.blade.php`, `footer.blade.php`, `footer-column.blade.php` |
| Bahasa pemasaran | Klaim No.1 tanpa bukti dan label Mulai Gratis yang terlalu luas | Deskripsi fungsi dan CTA Buat akun | Membentuk ekspektasi yang tidak sesuai; rincian paket tetap berasal dari database | Komponen navbar/footer landing |
| Kode frontend | CSS/JS padat, komponen penghitung angka lama tidak digunakan | Formatter Prettier, perintah format/check, penghitung tidak terpakai dihapus, helper kesiapan DOM terpisah | Sulit ditinjau dan perubahan lebih mudah menimbulkan regresi | `resources/js`, `resources/css`, `package.json` |
| Konfigurasi menu | Seluruh menu beserta relasi dimuat sekaligus | Pencarian dan pagination 24 menu dengan urutan stabil | Waktu render dan memori meningkat mengikuti jumlah menu | `MenuConfigurationController`, `resources/views/menu/configure.blade.php` |
| Dashboard | Semua shift tertutup dimuat ke memori, query harian dan relasi item tidak digunakan | Agregasi kas/jumlah masalah di database, hanya tiga detail shift bermasalah dimuat | Beban memori/query bertambah tanpa manfaat; referensi view tetap dipertahankan | `DashboardController`, `resources/views/dashboard/index.blade.php` |
| Impor/ekspor | Pembacaan struktur workbook belum ditangani; cursor tidak melakukan eager loading kategori | Kesalahan struktur menjadi validasi, lembar kosong ditolak, ekspor per batch 200 dengan kategori | File rusak dapat menghasilkan 500; ekspor dapat membuat query kategori per produk | `GettingStartedController` |
| Pengujian | Cache konfigurasi lokal dapat mengalahkan setting SQLite | Jalur cache tes terpisah, env tes dipaksa, guard menolak selain SQLite in-memory sebelum RefreshDatabase | Tes berisiko mengakses database usaha; percobaan yang ditemukan berhenti karena koneksi MySQL ditolak | `phpunit.xml`, `tests/TestCase.php` |
| Dependensi npm | Audit melaporkan 10 kerentanan termasuk transitif | Pembaruan kompatibel di lockfile; audit sesudah pembaruan melaporkan 0 | Dependensi rentan tetap terbawa proses build/runtime | `package-lock.json`, `package.json` |

## Bukti validasi akhir

- 59 tes PHP lulus, 246 assertions, menggunakan SQLite in-memory.
- 9 tes JavaScript lulus, termasuk inisialisasi sebelum/sesudah DOM siap.
- `npm run format:check`, `npm run build`, dan `git diff --check` berhasil.
- Tes landing kini memeriksa body/main tidak disembunyikan x-cloak, heading utama, tujuan anchor, dan pemilahan paket aktif/publik; bukan hanya HTTP 200.
- Browser sesudah perbaikan: `bodyDisplay: block`, `bodyCloaked: false`, heading tampil dan tidak ada error JavaScript tercatat.
- Lebar mobile 390 px: lebar konten 390 px, menu bisa dibuka, tautan Harga berpindah ke #harga dan menu menutup.
- Pemeriksaan browser sempat terhenti karena batas penggunaan alat; sudah berhasil dilanjutkan pada 13 September.

## Cara menjaga perubahan

Jalankan `vendor/bin/phpunit`, `npm test`, `npm run format:check`, dan `npm run build` sebelum rilis. Jalankan `npm run format` ketika format frontend perlu dibetulkan. Gunakan `npm ci` untuk memasang versi dari lockfile.

Tidak ada migrasi database usaha pada tahap ini. Audit npm yang bersih bukan jaminan seluruh aplikasi bebas bug atau audit keamanan PHP. Validasi Midtrans nyata, printer fisik, konkurensi MySQL, dan batas offline pada laporan implementasi sebelumnya masih berlaku. Perubahan skalabilitas di atas mengurangi pemuatan data; belum merupakan hasil load test produksi.
