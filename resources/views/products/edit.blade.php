<x-app-layout>
    <div class="max-w-4xl px-4 pb-12 mx-auto sm:px-6 lg:px-8">
        <div class="mb-8">
            <a href="{{ route('products.index') }}"
                class="flex items-center mb-4 text-sm font-bold text-blue-600 hover:underline">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke Daftar
            </a>
            <h1 class="text-3xl font-black tracking-tight text-gray-900">Edit Produk</h1>
            <p class="font-medium text-gray-500">Memperbarui informasi: {{ $product->product_name }}</p>
        </div>

        <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data"
            class="space-y-8">
            @csrf
            @method('PUT')

            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
                <h3 class="flex items-center mb-6 text-lg font-bold text-gray-900">
                    <span
                        class="flex items-center justify-center w-8 h-8 mr-3 text-sm text-blue-600 rounded-lg bg-blue-50">01</span>
                    Informasi Produk
                </h3>

                <div class="flex flex-col items-center justify-center w-full mb-6">
                    <label id="preview-container"
                        class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-100 border-dashed rounded-[2rem] cursor-pointer bg-gray-50 hover:bg-gray-100 transition-all overflow-hidden relative">

                        <div id="upload-placeholder"
                            class="flex flex-col items-center justify-center pt-5 pb-6 {{ $product->image ? 'hidden' : '' }}">
                            <svg class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <p class="mb-2 text-sm font-bold text-gray-500">Klik untuk ganti foto</p>
                        </div>

                        <img id="image-preview" src="{{ $product->image ? asset('storage/' . $product->image) : '' }}"
                            class="absolute inset-0 object-cover w-full h-full {{ $product->image ? '' : 'hidden' }}" />

                        <input type="file" name="image" id="image-input" class="hidden" accept="image/*"
                            onchange="previewImage(this)" />
                    </label>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label class="block mb-2 text-sm font-bold text-gray-700">Nama Produk</label>
                        <input type="text" name="product_name" id="product_name"
                            value="{{ old('product_name', $product->product_name) }}" required
                            class="w-full px-4 py-3 border-gray-100 rounded-2xl bg-gray-50 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700">Kategori</label>
                        <select name="category_id" id="category_id" required
                            class="w-full px-4 py-3 border-gray-100 rounded-2xl bg-gray-50 focus:ring-blue-500">
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700">Tipe</label>
                        <select name="type" id="type_select"
                            class="w-full px-4 py-3 border-gray-100 rounded-2xl bg-gray-50 focus:ring-blue-500">
                            <option value="product" {{ $product->type == 'product' ? 'selected' : '' }}>Barang (Fisik)
                            </option>
                            <option value="service" {{ $product->type == 'service' ? 'selected' : '' }}>Jasa (Non-Stok)
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700">SKU</label>
                        <div class="relative">
                            <input type="text" name="sku" id="sku_input" value="{{ old('sku', $product->sku) }}"
                                required
                                class="w-full px-4 py-3 border-gray-100 rounded-2xl bg-gray-50 focus:ring-blue-500">
                            <button type="button" onclick="generateSKU()"
                                class="absolute px-3 py-1 text-[10px] font-bold text-blue-600 bg-blue-100 rounded-lg right-2 top-2">RE-GENERATE</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
                <h3 class="flex items-center mb-6 text-lg font-bold text-gray-900">
                    <span
                        class="flex items-center justify-center w-8 h-8 mr-3 text-sm text-green-600 rounded-lg bg-green-50">02</span>
                    Harga & <span id="section_title">Inventaris</span>
                </h3>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700">Harga Modal</label>
                        <input type="text" id="display_cost_price"
                            value="Rp {{ number_format($product->cost_price, 0, ',', '.') }}"
                            class="w-full px-4 py-3 font-bold text-right border-gray-100 rounded-2xl bg-gray-50">
                        <input type="hidden" name="cost_price" id="cost_price" value="{{ $product->cost_price }}">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700">Harga Jual</label>
                        <input type="text" id="display_sell_price"
                            value="Rp {{ number_format($product->sell_price, 0, ',', '.') }}" required
                            class="w-full px-4 py-3 font-black text-right text-blue-600 border-blue-100 rounded-2xl bg-blue-50/50">
                        <input type="hidden" name="sell_price" id="sell_price" value="{{ $product->sell_price }}">
                    </div>

                    <div id="manage_stock_container"
                        class="p-4 mb-4 border border-gray-100 md:col-span-2 bg-gray-50 rounded-2xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-bold text-gray-700">Lacak Stok Produk</h4>
                                <p class="text-[10px] text-gray-400">Matikan jika produk ini dibuat by-order.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="manage_stock" id="manage_stock" value="1"
                                    {{ $product->manage_stock ? 'checked' : '' }} class="sr-only peer">
                                <div
                                    class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:bg-blue-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all">
                                </div>
                            </label>
                        </div>
                    </div>

                    <div id="inventory_fields" class="grid grid-cols-1 gap-6 md:col-span-2 md:grid-cols-2">
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-700">Stok Saat Ini</label>
                            <input type="number" name="stock" id="stock_input" value="{{ $product->stock }}"
                                class="w-full px-4 py-3 border-gray-100 rounded-2xl bg-gray-50">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-700">Stok Minimum</label>
                            <input type="number" name="min_stock" id="min_stock_input"
                                value="{{ $product->min_stock }}"
                                class="w-full px-4 py-3 border-gray-100 rounded-2xl bg-gray-50">
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm flex justify-between items-center">
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                        {{ $product->is_active ? 'checked' : '' }} class="mr-2 text-blue-600 rounded">
                    <label for="is_active" class="text-sm font-bold text-gray-700 cursor-pointer">Produk Aktif</label>
                </div>
                <button type="submit"
                    class="px-10 py-4 font-black text-white bg-blue-600 shadow-xl rounded-2xl hover:bg-blue-700">Update
                    Produk</button>
            </div>
        </form>
    </div>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('image-preview');
            const placeholder = document.getElementById('upload-placeholder');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function generateSKU() {
            const name = document.getElementById('product_name').value;
            const cat = document.getElementById('category_id').value;
            if (!name) return alert('Isi nama produk dulu');
            const res =
                `${name.substring(0,3).toUpperCase()}-${cat.padStart(2,'0')}-${Math.floor(1000+Math.random()*9000)}`;
            document.getElementById('sku_input').value = res;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('type_select');
            const manageStock = document.getElementById('manage_stock');
            const inventoryFields = document.getElementById('inventory_fields');
            const manageStockContainer = document.getElementById('manage_stock_container');
            const sectionTitle = document.getElementById('section_title');

            function updateUI() {
                if (typeSelect.value === 'service') {
                    sectionTitle.innerText = 'Layanan';
                    manageStockContainer.classList.add('hidden');
                    inventoryFields.classList.add('hidden');
                } else {
                    sectionTitle.innerText = 'Inventaris';
                    manageStockContainer.classList.remove('hidden');
                    if (manageStock.checked) {
                        inventoryFields.classList.remove('hidden');
                    } else {
                        inventoryFields.classList.add('hidden');
                    }
                }
            }

            typeSelect.addEventListener('change', updateUI);
            manageStock.addEventListener('change', updateUI);

            const formatRupiah = (angka) => {
                let number_string = angka.replace(/[^0-9]/g, '');
                let sisa = number_string.length % 3;
                let rupiah = number_string.substr(0, sisa);
                let ribuan = number_string.substr(sisa).match(/\d{3}/gi);
                if (ribuan) {
                    let separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }
                return rupiah ? 'Rp ' + rupiah : '';
            };

            ['cost', 'sell'].forEach(key => {
                const display = document.getElementById(`display_${key}_price`);
                const real = document.getElementById(`${key}_price`);
                display.addEventListener('input', function() {
                    real.value = this.value.replace(/[^0-9]/g, '');
                    this.value = formatRupiah(this.value);
                });
            });

            updateUI();
        });
    </script>
</x-app-layout>
