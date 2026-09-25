# Perbaikan hasil review — 25 September 2026

| Temuan | Perbaikan | Dampak jika dibiarkan |
|---|---|---|
| Modul rupiah mengakses `document` saat diimpor | Fungsi rupiah tidak lagi memiliki efek samping ketika diimpor. Inisialisasi elemen dipanggil dari entry browser melalui `onDomReady`. | Suite pengujian kasir gagal dimuat sehingga regresi alur transaksi tidak terdeteksi. |
| Hemat grosir membandingkan harga tanpa tambahan dengan harga bertambahan | Tambahan yang sama ikut diperhitungkan pada harga normal. Helper penghematan juga memperhitungkan diskon tier. | Informasi penghematan menyesatkan: contoh 10 item, harga 10.000 menjadi 8.000 dengan tambahan 3.000 kini menampilkan penghematan 20.000, bukan 0. |
| CSS manual dan font ikon kembali masuk | Utility Tailwind menggantikan shortcut CSS; ikon dikembalikan ke komponen SVG lokal. Stylesheet aplikasi hanya memuat directive Tailwind. Tes kembali memeriksa invariant ini secara ketat. | Dua sistem styling dan aset ikon yang berlebihan menyulitkan pemeliharaan dan melanggar ketentuan styling proyek. Template cetak mandiri yang sudah ada tetap dipertahankan. |
| Retur pecahan diterima untuk barang bulat | Checkout menyimpan snapshot `allow_fraction` pada item nota, lalu retur memvalidasi aturan tersebut sebelum mencatat pengembalian uang/stok. | Stok barang satuan dapat menjadi pecahan. Mengubah katalog setelah transaksi juga tidak boleh mengubah aturan retur nota baru. |

Untuk nota lama tanpa snapshot, sistem memakai bukti jumlah penjualan pecahan atau aturan produk yang tersedia saat ini. Aturan historis yang belum pernah disimpan tidak dapat direkonstruksi dengan pasti.

Validasi: 92 tes PHP (556 assertion), 17 tes JavaScript, build produksi, kompilasi Blade, pemeriksaan format dan whitespace. Tes baru memeriksa impor helper tanpa DOM, tambahan pada harga grosir, penolakan retur pecahan barang bulat, retur barang timbang setelah perubahan katalog, dan retry retur yang tidak menggandakan uang/stok.

Migrasi `2026_09_25_000001_snapshot_order_item_fraction_rule` sudah diterapkan pada database lokal setelah backup privat beserta checksum berhasil dibuat. Deployment produksi dan cetak fisik tidak dilakukan pada pekerjaan ini.
