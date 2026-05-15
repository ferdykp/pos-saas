<x-app-layout>
    <div class="max-w-4xl p-6 mx-auto bg-white shadow-xl rounded-2xl">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-black text-gray-900">Edit Event Diskon</h2>
            <span class="px-3 py-1 text-xs font-bold text-blue-600 rounded-lg bg-blue-50">ID: #{{ $discount->id }}</span>
        </div>

        <form action="{{ route('discounts.update', $discount->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-bold text-gray-700">Nama Diskon / Event</label>
                    <input type="text" name="name" value="{{ $discount->name }}" required
                        class="w-full mt-1 border-gray-200 rounded-xl focus:ring-blue-500">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-sm font-bold text-gray-700">Tipe</label>
                        <select name="type" class="w-full mt-1 border-gray-200 rounded-xl focus:ring-blue-500">
                            <option value="percentage" {{ $discount->type == 'percentage' ? 'selected' : '' }}>
                                Persentase (%)</option>
                            <option value="fixed" {{ $discount->type == 'fixed' ? 'selected' : '' }}>Potongan (Rp)
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700">Nilai</label>
                        <input type="number" name="value" value="{{ $discount->value }}" required
                            class="w-full mt-1 border-gray-200 rounded-xl focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <div class="p-4 bg-gray-50 rounded-xl">
                <h3 class="mb-4 text-sm font-black text-gray-700 uppercase">Pengaturan Waktu</h3>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <input type="date" name="start_date" value="{{ $discount->start_date }}"
                        class="border-gray-200 rounded-xl">
                    <input type="date" name="end_date" value="{{ $discount->end_date }}"
                        class="border-gray-200 rounded-xl">
                    <input type="time" name="start_time" value="{{ $discount->start_time }}"
                        class="border-gray-200 rounded-xl">
                    <input type="time" name="end_time" value="{{ $discount->end_time }}"
                        class="border-gray-200 rounded-xl">
                </div>
            </div>

            <div>
                <label class="block mb-2 text-sm font-bold text-gray-700">Pilih Menu</label>
                <div
                    class="grid grid-cols-2 gap-3 p-4 overflow-y-auto border border-gray-100 max-h-60 rounded-xl bg-gray-50">
                    @foreach ($products as $product)
                        <label
                            class="flex items-center p-2 space-x-3 bg-white border rounded-lg shadow-sm cursor-pointer hover:border-blue-500">
                            <input type="checkbox" name="product_ids[]" value="{{ $product->id }}"
                                {{ in_array($product->id, $selectedProductIds) ? 'checked' : '' }}
                                class="text-blue-600 border-gray-300 rounded">
                            <span class="text-sm font-medium text-gray-700">{{ $product->product_name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center justify-between p-4 bg-blue-50 rounded-xl">
                <span class="text-sm font-bold text-blue-900">Status Aktif Diskon</span>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ $discount->is_active ? 'checked' : '' }}
                        class="sr-only peer">
                    <div
                        class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:bg-blue-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all">
                    </div>
                </label>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('discounts.index') }}" class="px-6 py-3 font-bold text-gray-500">Batal</a>
                <button type="submit" class="px-6 py-3 font-black text-white bg-blue-600 rounded-xl">Simpan
                    Perubahan</button>
            </div>
        </form>
    </div>
</x-app-layout>
