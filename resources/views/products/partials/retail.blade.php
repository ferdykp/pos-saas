@php
    $unitRows = old('units', isset($product) ? $product->units->map->only(['id', 'name', 'factor', 'price'])->all() : []);
    $tierRows = old('price_tiers', $product->price_tiers ?? []);
@endphp
<section class="p-6 my-6 space-y-5 bg-white border rounded-lg border-border-200" x-data="{ units: @js($unitRows), tiers: @js($tierRows) }">
    <h2 class="text-lg font-bold text-ink-900">Satuan, kemasan & harga grosir</h2>
    <p class="text-sm text-ink-700">Stok dan harga modal dicatat dalam satuan dasar. Contoh: pcs, kg, meter, atau jam. Harga grosir berlaku per baris pesanan dalam satuan dasar; kemasan memiliki harga tersendiri.</p>
    <input type="hidden" name="units_present" value="1">
    <div class="grid gap-4 md:grid-cols-2">
        <label class="text-sm font-semibold">Satuan dasar<input name="base_unit" maxlength="20" required value="{{ old('base_unit', $product->base_unit ?? 'pcs') }}" class="block w-full mt-2 border rounded-md border-border-200"></label>
        <label class="flex items-center gap-3 text-sm"><input type="hidden" name="allow_fraction" value="0"><input type="checkbox" name="allow_fraction" value="1" @checked(old('allow_fraction', $product->allow_fraction ?? false))> Izinkan jumlah pecahan (hingga 3 angka desimal)</label>
    </div>
    <div class="space-y-3">
        <h3 class="font-semibold">Kemasan jual</h3>
        <p class="text-xs text-ink-700">Contoh: nama Dus, isi 12 pcs, harga Rp110.000. Kemasan dan varian tidak dipilih bersamaan.</p>
        <template x-for="(unit, index) in units" :key="index">
            <div class="grid items-end gap-3 sm:grid-cols-4">
                <input type="hidden" :name="`units[${index}][id]`" :value="unit.id || ''">
                <label class="text-xs">Nama<input class="w-full mt-1 border rounded-md border-border-200" :name="`units[${index}][name]`" x-model="unit.name" maxlength="30" required></label>
                <label class="text-xs">Isi satuan dasar<input class="w-full mt-1 border rounded-md border-border-200" type="number" min="0.001" max="1000000" step="0.001" :name="`units[${index}][factor]`" x-model="unit.factor" required></label>
                <label class="text-xs">Harga kemasan (Rp)<input class="w-full mt-1 border rounded-md border-border-200" type="text" data-rupiah-input :name="`units[${index}][price]`" x-model="unit.price" required></label>
                <button type="button" class="px-3 py-2 text-sm text-red-700 border rounded-md" @click="units.splice(index, 1)">Hapus kemasan</button>
            </div>
        </template>
        <button type="button" class="px-4 py-2 text-sm border rounded-md text-primary-700 disabled:opacity-50" :disabled="units.length >= 10" @click="units.push({name:'',factor:1,price:0})">+ Kemasan</button>
    </div>
    <div class="space-y-3">
        <h3 class="font-semibold">Harga grosir satuan dasar</h3>
        <template x-for="(tier, index) in tiers" :key="index">
            <div class="grid items-end gap-3 sm:grid-cols-3">
                <label class="text-xs">Minimal jumlah<input class="w-full mt-1 border rounded-md border-border-200" type="number" min="0.001" max="100000" step="0.001" :name="`price_tiers[${index}][min_quantity]`" x-model="tier.min_quantity" required></label>
                <label class="text-xs">Harga per satuan (Rp)<input class="w-full mt-1 border rounded-md border-border-200" type="text" data-rupiah-input :name="`price_tiers[${index}][price]`" x-model="tier.price" required></label>
                <button type="button" class="px-3 py-2 text-sm text-red-700 border rounded-md" @click="tiers.splice(index, 1)">Hapus harga</button>
            </div>
        </template>
        <button type="button" class="px-4 py-2 text-sm border rounded-md text-primary-700 disabled:opacity-50" :disabled="tiers.length >= 10" @click="tiers.push({min_quantity:10,price:0})">+ Harga grosir</button>
    </div>
    @foreach (['base_unit', 'allow_fraction', 'units', 'price_tiers'] as $field)
        @foreach ($errors->get($field.'*') as $messages)
            @foreach ((array) $messages as $message)<p class="text-sm text-red-700">{{ $message }}</p>@endforeach
        @endforeach
    @endforeach
</section>
