<x-app-layout>
    <div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-black tracking-tight text-gray-900">Manajemen Stok</h1>
                <p class="font-medium text-gray-500">Pantau ketersediaan barang di gudang Anda.</p>
            </div>
            <a href="{{ route('inventory.history') }}"
                class="px-6 py-3 font-bold text-gray-700 transition bg-gray-100 rounded-2xl hover:bg-gray-200">
                Riwayat Mutasi
            </a>
        </div>

        <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
            <table class="w-full text-left">
                <thead class="border-b border-gray-100 bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-black tracking-widest text-gray-400 uppercase">Produk</th>
                        <th class="px-6 py-4 text-xs font-black tracking-widest text-center text-gray-400 uppercase">
                            Stok Saat Ini</th>
                        <th class="px-6 py-4 text-xs font-black tracking-widest text-center text-gray-400 uppercase">
                            Status</th>
                        <th class="px-6 py-4 text-xs font-black tracking-widest text-right text-gray-400 uppercase">Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach ($products as $product)
                        <tr class="transition hover:bg-gray-50/50">
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-gray-900">{{ $product->product_name }}</p>
                                <p class="text-xs text-gray-400">SKU: {{ $product->sku }}</p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="text-lg font-black {{ ($product->inventory->quantity ?? 0) <= $product->min_stock ? 'text-red-600' : 'text-gray-900' }}">
                                    {{ $product->inventory->quantity ?? 0 }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if (($product->inventory->quantity ?? 0) <= $product->min_stock)
                                    <span
                                        class="px-3 py-1 bg-red-100 text-red-600 rounded-lg text-[10px] font-black uppercase">Stok
                                        Menipis</span>
                                @else
                                    <span
                                        class="px-3 py-1 bg-green-100 text-green-600 rounded-lg text-[10px] font-black uppercase">Aman</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button onclick="openModal('{{ $product->id }}', '{{ $product->product_name }}')"
                                    class="text-sm font-bold text-blue-600 hover:underline">
                                    Update Stok
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div id="stockModal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden bg-gray-900/50 backdrop-blur-sm">
        <div class="bg-white rounded-[2.5rem] p-8 w-full max-w-md shadow-2xl">
            <h3 id="modalTitle" class="mb-6 text-xl font-black text-gray-900">Update Stok</h3>
            <form action="{{ route('inventory.adjust') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" id="modalProductId">

                <div class="space-y-4">
                    <div>
                        <label class="block mb-2 text-xs font-black text-gray-400 uppercase">Tipe Mutasi</label>
                        <select name="type"
                            class="w-full px-4 py-3 font-bold border-gray-100 rounded-2xl bg-gray-50 focus:border-blue-500">
                            <option value="stock_in">Stok Masuk (+)</option>
                            <option value="stock_out">Stok Keluar (-)</option>
                            <option value="adjustment">Penyesuaian (Minus)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-black text-gray-400 uppercase">Jumlah</label>
                        <input type="number" name="quantity" required min="1"
                            class="w-full px-4 py-3 font-bold border-gray-100 rounded-2xl bg-gray-50 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-black text-gray-400 uppercase">Catatan</label>
                        <textarea name="note" class="w-full px-4 py-3 text-sm border-gray-100 rounded-2xl bg-gray-50 focus:border-blue-500"
                            placeholder="Contoh: Restock bulanan atau barang rusak"></textarea>
                    </div>
                </div>

                <div class="flex gap-3 mt-8">
                    <button type="button" onclick="closeModal()"
                        class="flex-1 py-4 font-bold text-gray-500 transition bg-gray-100 rounded-2xl hover:bg-gray-200">Batal</button>
                    <button type="submit"
                        class="flex-1 py-4 font-black text-white transition bg-blue-600 shadow-lg rounded-2xl shadow-blue-100 hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id, name) {
            document.getElementById('modalProductId').value = id;
            document.getElementById('modalTitle').innerText = 'Update: ' + name;
            document.getElementById('stockModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('stockModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
