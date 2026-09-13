<x-app-layout>
@section('title', 'Varian & pilihan')
<div class="gp-page"><div class="gp-eyebrow">PENGATURAN KATALOG</div><h1 class="gp-title">Pilihan produk dan layanan.</h1><p class="gp-muted">Varian memakai harga dan stoknya sendiri. Topping menambah harga. Resep dasar dipakai untuk setiap porsi, termasuk varian; gunakan satuan kecil seperti gram atau ml.</p>
<div class="gp-row mt-4"><a href="{{ route('products.create') }}" class="gp-btn gp-primary">Tambah item</a>@if(auth()->user()->tenant->hasBusinessModule('food'))<a href="{{ route('materials.index') }}" class="gp-btn">Kelola bahan baku</a>@endif<a href="{{ route('getting-started') }}" class="gp-btn">Impor / ekspor item</a></div>
<form method="get" class="gp-row gp-section">
    <label class="gp-field">Cari item<input name="search" value="{{ $search }}" maxlength="100" placeholder="Nama item"></label>
    <button class="gp-btn">Cari</button>
    @if($search !== '')<a class="gp-btn" href="{{ route('menu.configure') }}">Semua item</a>@endif
</form>
@forelse($products as $product)
<details class="gp-card gp-section"><summary class="cursor-pointer"><strong>{{ $product->product_name }}</strong><span class="gp-muted ml-3">Rp{{ number_format($product->sell_price,0,',','.') }} · {{ $product->variants->count() }} varian · {{ $product->addons->count() }} topping</span></summary>
<div class="gp-grid mt-5" style="grid-template-columns:repeat(auto-fit,minmax(215px,1fr))">
@foreach(['variant' => ['Varian / ukuran', $product->variants], 'addon' => ['Topping / tambahan', $product->addons], 'recipe' => ['Resep per porsi', $product->materials]] as $kind => [$label,$entries])
@continue($kind === 'recipe' && !auth()->user()->tenant->hasBusinessModule('food'))
<section><h2 class="font-semibold">{{ $label }}</h2><div class="gp-list">@foreach($entries as $entry)<div class="gp-row gp-between text-sm"><span>{{ $entry->name }}<small class="gp-muted block">{{ $kind === 'recipe' ? $entry->pivot->quantity.' '.$entry->unit : 'Rp'.number_format($entry->price,0,',','.') }}</small></span><form action="{{ route('menu.configure.destroy', $product) }}" method="post">@csrf @method('DELETE')<input type="hidden" name="kind" value="{{ $kind }}"><input type="hidden" name="id" value="{{ $entry->id }}"><button class="gp-btn" aria-label="Hapus {{ $entry->name }}">×</button></form></div>@endforeach</div>
<form action="{{ route('menu.configure.store', $product) }}" method="post" class="gp-grid mt-4" style="gap:10px">@csrf<input type="hidden" name="kind" value="{{ $kind }}">
@if($kind === 'recipe')<label class="gp-field">Bahan<select name="material_id" required><option value="">Pilih bahan</option>@foreach($materials as $material)<option value="{{ $material->id }}">{{ $material->name }} ({{ $material->unit }})</option>@endforeach</select></label><label class="gp-field">Jumlah per porsi<input type="number" min="1" name="quantity" required></label>
@else<label class="gp-field">Nama<input name="name" placeholder="{{ $kind === 'variant' ? 'Mis. Large' : 'Mis. Extra shot' }}" maxlength="100" required></label><label class="gp-field">{{ $kind === 'variant' ? 'Harga jual varian (Rp)' : 'Tambahan harga (Rp)' }}<input type="number" name="price" min="0" required></label><label class="gp-field">{{ $kind === 'variant' ? 'Stok varian' : 'Modal tambahan (Rp), opsional' }}<input type="number" name="{{ $kind === 'variant' ? 'stock' : 'cost' }}" min="0"></label>@endif
<button class="gp-btn">{{ $kind === 'recipe' ? 'Simpan takaran bahan' : 'Tambah pilihan' }}</button></form></section>
@endforeach
</div><a class="gp-btn mt-5" href="{{ route('products.edit',$product) }}">Edit harga, modal & pelacakan stok</a></details>
@empty<div class="gp-empty gp-card gp-section">Belum ada item. Tambahkan item terlebih dahulu.</div>@endforelse
<div class="gp-section">{{ $products->links() }}</div>
</div></x-app-layout>
