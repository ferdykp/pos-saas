<x-app-layout>
@section('title', 'Pengaturan usaha')
<div class="gp-page">
    <div class="gp-eyebrow">GROWPOS UNTUK USAHAMU</div>
    <h1 class="gp-title">Satu usaha, fitur sesuai kebutuhan.</h1>
    <p class="gp-muted">Jenis usaha mengatur istilah dan contoh katalog. Pilih fitur operasional secara terpisah; barang dan jasa tetap bisa dijual bersama. Perubahan ini tidak menghapus data lama.</p>
    <form method="post" action="{{ route('business.update') }}" class="gp-card gp-section gp-grid">
        @csrf @method('PUT')
        <label class="gp-field">Jenis usaha<select name="business_type" required>
            @foreach($types as $value => $label)<option value="{{ $value }}" @selected(old('business_type', $tenant->businessType()) === $value)>{{ $label }}</option>@endforeach
        </select></label>
        <fieldset class="gp-grid"><legend class="font-semibold mb-3">Fitur operasional</legend>
            @foreach(['goods' => ['Barang & persediaan', 'Stok, pengadaan dan supplier untuk barang fisik.'], 'services' => ['Layanan jasa', 'Katalog jasa tanpa pemotongan stok barang.'], 'food' => ['Kuliner & dapur', 'Meja, topping, resep bahan, dan antrean dapur.']] as $key => [$label, $description])
            <label class="gp-card"><input type="checkbox" name="business_modules[]" value="{{ $key }}" @checked(in_array($key, old('business_modules', $tenant->businessModules())))> <strong>{{ $label }}</strong><p class="gp-muted mt-2">{{ $description }}</p></label>
            @endforeach
        </fieldset>
        <p class="gp-muted">Fitur pilihan tidak mengubah batas atau hak akses paket langganan.</p>
        <button class="gp-btn gp-primary">Simpan pengaturan usaha</button>
    </form>
</div>
</x-app-layout>
