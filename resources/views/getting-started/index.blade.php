<x-app-layout>
@section('title', 'Mulai berjualan')
@php($tenant = auth()->user()->tenant)
<div class="gp-page">
    <div class="gp-eyebrow">SELAMAT DATANG DI GROWPOS</div>
    <h1 class="gp-title">Dari katalog pertama,<br>ke pelanggan pertama.</h1>
    <p class="gp-muted">Siapkan {{ $tenant->name }} sesuai kebutuhan {{ \App\Support\BusinessProfile::TYPES[$tenant->businessType()] }}.</p>
    @if(auth()->user()->role === 'admin')<a class="gp-btn mt-4" href="{{ route('business.edit') }}">Atur jenis usaha & fitur</a>@endif
    @if(!$tenant->currentPlan())<div class="gp-alert gp-section">Pilih paket untuk mengaktifkan katalog dan transaksi. <a class="underline" href="{{ route('billing.index') }}">Lihat paket →</a></div>@endif
    <div class="gp-grid gp-section" style="grid-template-columns:repeat(auto-fit,minmax(230px,1fr))">
        @foreach([[$tenant->catalogLabel(), 'Tambahkan barang atau layanan beserta harga yang benar.', $hasMenu, route('products.create'), 'Isi katalog'], ['Buka shift', 'Catat modal tunai awal di laci kasir.', $hasShift, route('pos.index'), 'Buka kasir'], ['Transaksi pertama', 'Pilih barang atau jasa, terima pembayaran, lalu cetak struk.', $hasSale, route('pos.index'), 'Mulai transaksi']] as $step)
        <div class="gp-card gp-step"><div class="gp-step-number">{{ $step[2] ? '✓' : $loop->iteration }}</div><div><h2 class="font-semibold">{{ $step[0] }}</h2><p class="gp-muted mt-2">{{ $step[1] }}</p><a class="gp-btn mt-4" href="{{ $step[3] }}">{{ $step[4] }} →</a></div></div>
        @endforeach
    </div>
    @if(auth()->user()->role === 'admin')
    <div class="gp-grid gp-two gp-section">
        <section class="gp-card">
            <h2 class="font-semibold text-lg">Pindahkan katalog dalam satu file.</h2>
            <p class="gp-muted mt-2">Excel atau CSV, maksimal 500 baris. Kolom type: product untuk barang, service untuk jasa. Jasa tidak memotong stok. Template lama enam kolom tetap diterima sebagai barang.</p>
            <div class="gp-row mt-4"><a class="gp-btn" href="{{ route('menu.template') }}">Unduh template</a><a class="gp-btn" href="{{ route('menu.export') }}">Ekspor katalog</a></div>
            <form action="{{ route('getting-started.import') }}" method="post" enctype="multipart/form-data" class="mt-4">@csrf<label class="gp-field">File katalog<input type="file" name="file" accept=".xlsx,.csv" required></label><button class="gp-btn gp-primary mt-4">Impor katalog</button></form>
        </section>
        <section class="gp-card">
            <h2 class="font-semibold text-lg">Contoh sesuai jenis usahamu</h2>
            <p class="gp-muted mt-2">{{ implode(', ', array_column(\App\Support\BusinessProfile::samples($tenant->businessType()), 'name')) }}. Contoh menjadi katalog sungguhan; sesuaikan harga, modal, dan stok sebelum menjual.</p>
            @if(!$hasMenu)<form method="post" action="{{ route('getting-started.sample') }}" class="mt-4">@csrf<button class="gp-btn">Tambahkan 4 contoh</button></form>@else<p class="gp-chip mt-4">Katalog sudah terisi</p>@endif
            <a class="gp-btn mt-4" href="{{ route('menu.configure') }}">{{ $tenant->hasBusinessModule('food') ? 'Varian, tambahan & resep' : 'Varian & pilihan' }} →</a>
        </section>
    </div>
    @endif
    <a class="gp-btn mt-6" href="{{ route('help') }}">Panduan operasional →</a>
</div>
</x-app-layout>
