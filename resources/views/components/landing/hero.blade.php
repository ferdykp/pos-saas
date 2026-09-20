<section class="overflow-hidden [background:#f3f5ec] [padding:75px_0]">
    <div class="[max-width:1280px] [margin:auto] p-7 [color:#18372d] max-[701px]:[padding:20px_16px] grid items-center [grid-template-columns:repeat(auto-fit,minmax(min(100%,340px),1fr))] [gap:50px]">
        <div>
            <span class="inline-block [padding:5px_10px] rounded-full [background:#edf5ee] [color:#356649] [font-size:11px] font-semibold">UNTUK TOKO, JASA, DAN KULINER</span>
            <h1 class="font-bold [letter-spacing:-0.04em] [font-size:clamp(38px,5vw,64px)] [line-height:1.09] [margin:24px_0]">Jual barang.<br>Layani pelanggan.<br><span class="[color:#237252]">Kelola usaha.</span></h1>
            <p class="[color:#65796f] [line-height:1.6] [font-size:17px] [max-width:470px]">Kasir dan pengelolaan usaha untuk UMKM. Dari toko kelontong dan retail, hingga layanan jasa serta kuliner, sesuaikan GrowPOS dengan cara usahamu berjalan.</p>
            <div class="flex gap-3 items-center flex-wrap mt-7"><a class="inline-flex items-center justify-center gap-2 min-h-11 [padding:10px_17px] [border:1px_solid_#dae4dd] rounded-md bg-white [color:#234a3b] [font-size:13px] font-semibold cursor-pointer hover:[background:#f1f7f3] disabled:[opacity:0.45] disabled:cursor-wait focus-visible:[outline:3px_solid_#90b9a5] focus-visible:[outline-offset:3px] ![background:#17694d] !text-white ![border-color:#17694d] hover:![background:#12523d]" href="{{ route('register') }}">Siapkan usahamu →</a><a class="inline-flex items-center justify-center gap-2 min-h-11 [padding:10px_17px] [border:1px_solid_#dae4dd] rounded-md bg-white [color:#234a3b] [font-size:13px] font-semibold cursor-pointer hover:[background:#f1f7f3] disabled:[opacity:0.45] disabled:cursor-wait focus-visible:[outline:3px_solid_#90b9a5] focus-visible:[outline-offset:3px]" href="#harga">Lihat paket</a></div>
            <p class="[color:#65796f] [font-size:13px] [line-height:1.6] mt-4">Barang & jasa satu nota · Barcode · Stok · Laporan</p>
        </div>
        <div class="bg-white [border:1px_solid_#e1e9e4] rounded-lg max-[701px]:[padding:18px] [padding:26px] [box-shadow:0_18px_50px_#254a3210]">
            <div class="flex gap-3 items-center flex-wrap justify-between"><strong>Sesuai cara kamu berusaha</strong><span class="inline-block [padding:5px_10px] rounded-full [background:#edf5ee] [color:#356649] [font-size:11px] font-semibold">SATU GROWPOS</span></div>
            <div class="[&_>_div]:[padding:13px_0] [&_>_div]:[border-bottom:1px_solid_#edf1ee] [&>a:not(.flex):not(.inline-flex)]:block [&_>_a]:[padding:13px_0] [&_>_a]:[border-bottom:1px_solid_#edf1ee] [&_>_:last-child]:[border-bottom:0] mt-4">
                @foreach([['01', 'Toko & retail', 'Cari barang dengan barcode, kelola stok, dan lihat penjualan.'], ['02', 'Dagang & kelontong', 'Catat barang kemasan, persediaan, pelanggan, dan supplier.'], ['03', 'Jasa & layanan', 'Jual layanan tanpa stok barang, termasuk bersama produk fisik.'], ['04', 'Kuliner & kafe', 'Aktifkan meja, topping, resep, dan antrean dapur sesuai kebutuhan.']] as [$number, $title, $description])
                <div class="flex [gap:15px] [align-items:flex-start]"><span class="w-9 h-9 grid place-items-center rounded-full [background:#edf5ee] [color:#17694d] font-bold shrink-0">{{ $number }}</span><div><h2 class="font-semibold">{{ $title }}</h2><p class="[color:#65796f] [font-size:13px] [line-height:1.6] mt-1">{{ $description }}</p></div></div>
                @endforeach
            </div>
            <a class="inline-flex items-center justify-center gap-2 min-h-11 [padding:10px_17px] [border:1px_solid_#dae4dd] rounded-md bg-white [color:#234a3b] [font-size:13px] font-semibold cursor-pointer hover:[background:#f1f7f3] disabled:[opacity:0.45] disabled:cursor-wait focus-visible:[outline:3px_solid_#90b9a5] focus-visible:[outline-offset:3px] w-full mt-4" href="{{ route('register') }}">Pilih jenis usahamu ↗</a>
        </div>
    </div>
</section>
