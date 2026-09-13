<section class="overflow-hidden" style="background:#f3f5ec;padding:75px 0">
    <div class="gp-page gp-grid items-center" style="grid-template-columns:repeat(auto-fit,minmax(min(100%,340px),1fr));gap:50px">
        <div>
            <span class="gp-chip">UNTUK TOKO, JASA, DAN KULINER</span>
            <h1 class="gp-title" style="font-size:clamp(38px,5vw,64px);line-height:1.09;margin:24px 0">Jual barang.<br>Layani pelanggan.<br><span style="color:#237252">Kelola usaha.</span></h1>
            <p class="gp-muted" style="font-size:17px;max-width:470px">Kasir dan pengelolaan usaha untuk UMKM. Dari toko kelontong dan retail, hingga layanan jasa serta kuliner, sesuaikan GrowPOS dengan cara usahamu berjalan.</p>
            <div class="gp-row mt-7"><a class="gp-btn gp-primary" href="{{ route('register') }}">Siapkan usahamu →</a><a class="gp-btn" href="#harga">Lihat paket</a></div>
            <p class="gp-muted mt-4">Barang & jasa satu nota · Barcode · Stok · Laporan</p>
        </div>
        <div class="gp-card" style="padding:26px;box-shadow:0 18px 50px #254a3210">
            <div class="gp-row gp-between"><strong>Sesuai cara kamu berusaha</strong><span class="gp-chip">SATU GROWPOS</span></div>
            <div class="gp-list mt-4">
                @foreach([['01', 'Toko & retail', 'Cari barang dengan barcode, kelola stok, dan lihat penjualan.'], ['02', 'Dagang & kelontong', 'Catat barang kemasan, persediaan, pelanggan, dan supplier.'], ['03', 'Jasa & layanan', 'Jual layanan tanpa stok barang, termasuk bersama produk fisik.'], ['04', 'Kuliner & kafe', 'Aktifkan meja, topping, resep, dan antrean dapur sesuai kebutuhan.']] as [$number, $title, $description])
                <div class="gp-step"><span class="gp-step-number">{{ $number }}</span><div><h2 class="font-semibold">{{ $title }}</h2><p class="gp-muted mt-1">{{ $description }}</p></div></div>
                @endforeach
            </div>
            <a class="gp-btn w-full mt-4" href="{{ route('register') }}">Pilih jenis usahamu ↗</a>
        </div>
    </div>
</section>
