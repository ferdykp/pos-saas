# Migrasi styling GrowPOS ke Tailwind

> Pembaruan 20 September 2026: sesuai permintaan terbaru, `orders/print.blade.php` menjadi pengecualian CSS khusus cetak. Rincian terbaru ada di `docs/styling-dan-cetak-2026-09-20.md`.

Migrasi ini dikerjakan sebelum melanjutkan tahap launching berikutnya. Tidak ada deployment server atau transaksi payment gateway dalam pekerjaan ini.

| Bagian | Sebelumnya | Sesudah | Alasan dan dampak jika tetap memakai pola lama |
|---|---|---|---|
| Halaman aplikasi dan landing | Kelas `gp-*`, utility Tailwind, dan inline style bercampur | Utility Tailwind untuk layout, warna, ukuran, state, dan breakpoint | Styling tersebar membuat perubahan mudah berbenturan dan sulit ditelusuri. |
| Layout dan dialog | Blok `<style>`, selector JavaScript `.gp-dialog-panel` | Utility cloak/scroll dan hook `data-dialog-panel` | Menghapus kelas styling tanpa mengganti hook dapat merusak fokus keyboard dialog kasir. |
| Ikon | Font Awesome melalui stylesheet CDN | Komponen Blade `x-icon` dengan SVG lokal dan utility Tailwind | Ikon tidak lagi bergantung pada ketersediaan stylesheet/font ikon eksternal. |
| Select2 dan animasi AOS | Asset tambahan dimuat global; AOS membawa CSS sendiri | Select native/Alpine dan utility Tailwind; dependensi AOS/Select2 dihapus | Mengurangi asset yang tidak diperlukan dan risiko konten tersembunyi saat inisialisasi animasi gagal. Animasi masuk berbasis AOS tidak lagi dijalankan. |
| Grafik dashboard | Lebar progress memakai inline style dinamis | Elemen `progress` dengan value numerik dan utility Tailwind | Class Tailwind yang dibentuk dinamis dari angka tidak selalu masuk hasil build. |
| Struk | Stylesheet mandiri pada template | Utility lebar 58/80 mm, print visibility, dan named page dari konfigurasi Tailwind | Ukuran kertas harus tetap konsisten. Cetak menunggu halaman/font selesai dimuat. Aturan margin hanya berlaku untuk halaman struk. |
| Email | Style ditulis manual pada setiap elemen | Template utility + build Tailwind khusus email + inliner Laravel | Sebagian aplikasi email mengabaikan stylesheet eksternal; menghapus inline style tanpa pengganti membuat email rusak. |
| Pagination | Template Bootstrap/Semantic UI cadangan | Alias ke template Tailwind | Penggunaan nama template lama tetap menghasilkan tampilan Tailwind. |

## Struktur dan pengembangan

- `resources/css/app.css`: hanya directive Tailwind. Browser tetap menerima CSS hasil kompilasi karena Tailwind adalah generator CSS.
- `tailwind.config.js`: token desain, sumber pemindaian Blade/JavaScript, plugin form, dan aturan halaman cetak bernama `receipt`.
- `resources/css/mail.css` dan `tailwind.mail.config.js`: utility email tanpa browser reset. Nilai warna literal menghindari ketergantungan CSS variables pada aplikasi email.
- `resources/views/mail/growpos.blade.php` dan `config/mail.php`: Laravel membaca `public/build/mail.css` dan menghasilkan inline style otomatis saat email dirender. Tidak mengirim email saat pengujian.
- `resources/views/components/icon.blade.php`: komponen SVG bersama. Atribut `fa-*` pada pemanggilan adalah nama aset yang dipetakan dan dibuang sebelum HTML dikirim; bukan kelas stylesheet. Ukuran, warna, dan animasi memakai Tailwind.
- `resources/icons/fontawesome.json`: hanya ikon yang digunakan, beserta lisensi lokal. Jalankan `npm run build:icons` setelah menambahkan nama ikon baru.
- Utility arbitrary dipakai untuk ukuran/warna yang harus mempertahankan desain sebelumnya. Jangan membuat class Tailwind lewat penyambungan string runtime; tulis alternatif class lengkap agar terdeteksi saat build.
- Google Fonts masih menjadi sumber font di layout yang sebelumnya memakainya. Tidak digunakan untuk styling komponen.
- Alpine dapat menghasilkan inline state sementara saat `x-show`/transisi berjalan. Sumber styling aplikasi tidak lagi berisi inline style buatan tangan.

## Build dan pemeriksaan

Jalankan `npm ci`, `npm run build`, lalu pengujian PHP. `npm run build` juga menghasilkan stylesheet email. Sertakan seluruh `public/build` pada release; email sengaja gagal dengan pesan build yang jelas bila asetnya hilang, agar tidak terkirim tanpa styling.

Pemeriksaan mencakup build produksi, kompilasi semua Blade, suite PHP/JavaScript, format, dan pemeriksaan whitespace. Test baru menolak style manual/stylesheet lama pada view serta memeriksa hasil inline email dan render SVG. Struk diuji untuk kedua ukuran dan penolakan ukuran tidak valid.

Pemeriksaan browser menggunakan landing lokal dan preview kasir/struk dari data sintetis SQLite terpisah: landing terlihat, dialog produk dapat dibuka/ditutup dengan keyboard, grid desktop tersusun, layar ponsel tidak overflow horizontal, dan lebar struk 58 mm terjaga. Printer fisik serta kompatibilitas visual semua aplikasi email belum diuji.
