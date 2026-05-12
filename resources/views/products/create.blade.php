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
            <h1 class="text-3xl font-black tracking-tight text-gray-900">Tambah Produk Baru</h1>
        </div>

        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf

            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
                <h3 class="flex items-center mb-6 text-lg font-bold text-gray-900">
                    <span
                        class="flex items-center justify-center w-8 h-8 mr-3 text-sm text-blue-600 rounded-lg bg-blue-50">01</span>
                    Informasi Produk
                </h3>
                <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm mt-8">
                    <h3 class="flex items-center mb-6 text-lg font-bold text-gray-900">
                        <span
                            class="flex items-center justify-center w-8 h-8 mr-3 text-sm text-purple-600 rounded-lg bg-purple-50">02</span>
                        Foto Produk (Opsional)
                    </h3>

                    <div class="flex flex-col items-center justify-center w-full">
                        <label id="preview-container"
                            class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-100 border-dashed rounded-[2rem] cursor-pointer bg-gray-50 hover:bg-gray-100 transition-all overflow-hidden relative">
                            <div id="upload-placeholder" class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <p class="mb-2 text-sm font-bold text-gray-500">Klik atau drag foto produk ke sini</p>
                                <p class="text-xs text-gray-400">PNG, JPG atau WEBP (Max. 2MB)</p>
                            </div>
                            <img id="image-preview" class="absolute inset-0 hidden object-cover w-full h-full" />
                            <input type="file" name="image" id="image-input" class="hidden" accept="image/*"
                                onchange="previewImage(this)" />
                        </label>
                    </div>
                </div>

                <script>
                    function previewImage(input) {
                        const preview = document.getElementById('image-preview');
                        const placeholder = document.getElementById('upload-placeholder');

                        if (input.files && input.files[0]) {
                            const reader = new FileReader();

                            reader.onload = function(e) {
                                preview.src = e.target.result;
                                preview.classList.remove('hidden');
                                placeholder.classList.add('hidden');
                            }

                            reader.readAsDataURL(input.files[0]);
                        }
                    }
                </script>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label class="block mb-2 text-sm font-bold text-gray-700">Nama Produk</label>
                        <input type="text" name="product_name" id="product_name"
                            placeholder="Misal: Kopi Susu Gula Aren" required
                            class="w-full px-4 py-3 transition border-gray-100 rounded-2xl bg-gray-50 focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700">Kategori</label>
                        <select name="category_id" id="category_id" required
                            class="w-full px-4 py-3 transition border-gray-100 rounded-2xl bg-gray-50 focus:border-blue-500">
                            <option value="">Pilih Kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700">Tipe</label>
                        <select name="type"
                            class="w-full px-4 py-3 transition border-gray-100 rounded-2xl bg-gray-50 focus:border-blue-500">
                            <option value="product">Barang (Fisik)</option>
                            <option value="service">Jasa (Non-Stok)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700">SKU / Kode Barang</label>
                        <div class="relative">
                            <input type="text" name="sku" id="sku_input" placeholder="Contoh: KOP-01-1234"
                                required
                                class="w-full px-4 py-3 transition border-gray-100 rounded-2xl bg-gray-50 focus:border-blue-500">
                            <button type="button" onclick="generateSKU()"
                                class="absolute px-3 py-1 text-[10px] font-bold text-blue-600 bg-blue-100 rounded-lg right-2 top-2 hover:bg-blue-200 transition">
                                GENERATE
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700">Barcode (Opsional)</label>
                        <input type="text" name="barcode" placeholder="Scan barcode di sini"
                            class="w-full px-4 py-3 transition border-gray-100 rounded-2xl bg-gray-50 focus:border-blue-500">
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
                        <input type="text" id="display_cost_price" placeholder="Rp 0"
                            class="w-full px-4 py-3 font-bold text-right transition border-gray-100 rounded-2xl bg-gray-50 focus:border-blue-500">
                        <input type="hidden" name="cost_price" id="cost_price" value="0">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700">Harga Jual</label>
                        <input type="text" id="display_sell_price" placeholder="Rp 0" required
                            class="w-full px-4 py-3 font-black text-right text-blue-600 transition border-gray-100 border-blue-100 rounded-2xl bg-blue-50/50 focus:border-blue-500">
                        <input type="hidden" name="sell_price" id="sell_price" value="0">
                    </div>
                    <div id="inventory_fields" class="grid grid-cols-1 gap-6 md:col-span-2 md:grid-cols-2">
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-700">Stok Awal</label>
                            <input type="number" name="stock" value="0"
                                class="w-full px-4 py-3 transition border-gray-100 rounded-2xl bg-gray-50 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-700">Stok Minimum (Alert)</label>
                            <input type="number" name="min_stock" value="5"
                                class="w-full px-4 py-3 transition border-gray-100 rounded-2xl bg-gray-50 focus:border-blue-500">
                            <p class="text-[10px] text-gray-400 mt-1 font-medium italic">*Sistem akan memberi
                                peringatan
                                jika stok di bawah angka ini.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm text-right flex justify-between items-center">
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" checked value="1"
                        class="mr-2 text-blue-600 rounded focus:ring-blue-500">
                    <label for="is_active" class="text-sm font-bold text-gray-700 cursor-pointer">Produk Langsung
                        Aktif
                        & Bisa Dijual</label>
                </div>
                <button type="submit"
                    class="px-10 py-4 font-black text-white transition bg-blue-600 shadow-xl rounded-2xl shadow-blue-100 hover:bg-blue-700 active:scale-95">
                    Simpan Produk
                </button>
            </div>
        </form>
    </div>

    <script>
        function generateSKU() {
            const productName = document.getElementById('product_name').value;
            const categoryId = document.getElementById('category_id').value;
            const skuInput = document.getElementById('sku_input');

            let namePart = productName.trim().substring(0, 3).toUpperCase();
            let catPart = categoryId ? categoryId.padStart(2, '0') : '00';
            let randomPart = Math.floor(1000 + Math.random() * 9000);

            if (namePart.length >= 1) {
                skuInput.value = `${namePart}-${catPart}-${randomPart}`;
            } else {
                alert('Isi nama produk terlebih dahulu');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const productName = document.getElementById('product_name');
            const skuInput = document.getElementById('sku_input');

            // --- Logika Mata Uang ---
            const priceInputs = [{
                    display: 'display_cost_price',
                    real: 'cost_price'
                },
                {
                    display: 'display_sell_price',
                    real: 'sell_price'
                }
            ];

            priceInputs.forEach(inputPair => {
                const displayInput = document.getElementById(inputPair.display);
                const realInput = document.getElementById(inputPair.real);

                displayInput.addEventListener('input', function() {
                    let rawValue = this.value.replace(/[^0-9]/g, '');
                    realInput.value = rawValue;

                    if (rawValue) {
                        this.value = formatRupiah(rawValue, 'Rp ');
                    } else {
                        this.value = '';
                    }
                });
            });

            function formatRupiah(angka, prefix) {
                let number_string = angka.toString(),
                    sisa = number_string.length % 3,
                    rupiah = number_string.substr(0, sisa),
                    ribuan = number_string.substr(sisa).match(/\d{3}/gi);

                if (ribuan) {
                    let separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }
                return prefix + rupiah;
            }

            // --- Logika SKU Otomatis ---
            productName.addEventListener('keyup', function() {
                if (skuInput.value === '' && productName.value.length >= 3) {
                    generateSKU();
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.querySelector('select[name="type"]');
            const inventoryFields = document.getElementById('inventory_fields');
            const sectionTitle = document.getElementById('section_title');

            function toggleFields() {
                if (typeSelect.value === 'service') {
                    // Sembunyikan field stok untuk Jasa
                    inventoryFields.classList.add('hidden');
                    sectionTitle.innerText = 'Layanan';

                    // Opsional: Reset nilai stok ke 0 jika jasa
                    inventoryFields.querySelectorAll('input').forEach(input => input.value = 0);
                } else {
                    // Tampilkan kembali untuk Barang
                    inventoryFields.classList.remove('hidden');
                    sectionTitle.innerText = 'Inventaris';
                }
            }

            // Jalankan saat halaman pertama dimuat
            toggleFields();

            // Jalankan setiap kali dropdown tipe berubah
            typeSelect.addEventListener('change', toggleFields);
        });
    </script>
</x-app-layout>
