<x-app-layout>
    @section('title', 'Tambah Produk Baru')

    <div class="px-4 py-6 mx-auto md:px-6 lg:px-8 max-w-modal-lg">

        <!-- Header & Navigation -->
        <div class="mb-6">
            <a href="{{ route('products.index') }}"
                class="inline-flex items-center gap-2 mb-2 text-xs font-semibold font-body text-primary-600 hover:text-primary-700">
                <i class="text-xs fa-solid fa-arrow-left"></i>
                <span>Kembali ke Inventaris Produk</span>
            </a>
            <h1 class="font-heading font-bold text-2xl md:text-[28px] text-ink-900 leading-tight">
                Tambah Produk Baru
            </h1>
            <p class="mt-1 text-xs font-body md:text-sm text-ink-700">
                Lengkapi rincian informasi barang, SKU, harga modal & jual, serta pengaturan stok.
            </p>
        </div>

        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Section 1: Informasi Dasar Produk -->
            <div class="p-6 border rounded-lg shadow-sm bg-surface-0 border-border-200">
                <div class="flex items-center gap-2.5 pb-4 mb-6 border-b border-border-200">
                    <div
                        class="flex items-center justify-center text-xs font-bold rounded-md w-7 h-7 bg-primary-50 text-primary-600 font-heading">
                        01
                    </div>
                    <h3 class="text-base font-semibold font-heading text-ink-900">Informasi Utama Produk</h3>
                </div>

                <!-- Upload Image Zone -->
                <div class="mb-6">
                    <label class="block font-body text-xs font-semibold text-ink-900 mb-1.5">Foto Produk
                        (Opsional)</label>
                    <label id="preview-container"
                        class="relative flex flex-col items-center justify-center w-full overflow-hidden transition-colors border-2 border-dashed rounded-md cursor-pointer h-44 border-border-200 bg-surface-100/50 hover:bg-surface-100">

                        <div id="upload-placeholder" class="flex flex-col items-center justify-center p-4 text-center">
                            <i class="mb-2 text-2xl fa-solid fa-cloud-arrow-up text-ink-400"></i>
                            <p class="text-xs font-semibold font-body text-ink-900">Klik atau drag foto produk ke sini
                            </p>
                            <p class="font-body text-[11px] text-ink-400 mt-0.5">Format PNG, JPG max 2MB</p>
                        </div>

                        <img id="image-preview" class="absolute inset-0 hidden object-cover w-full h-full" />
                        <input type="file" name="image" id="image-input" class="hidden" accept="image/*"
                            onchange="previewImage(this)" />
                    </label>
                </div>

                <!-- Inputs Grid -->
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label class="block font-body text-xs font-semibold text-ink-900 mb-1.5">
                            Nama Produk <span class="text-semantic-danger">*</span>
                        </label>
                        <input type="text" name="product_name" id="product_name"
                            placeholder="Contoh: Kopi Susu Aren 250ml" required
                            class="w-full px-3 text-xs transition-all border rounded-sm outline-none h-11 font-body text-ink-900 placeholder-ink-400 bg-surface-0 border-border-200 focus:border-primary-600 focus:ring-2 focus:ring-primary-100">
                    </div>

                    <div>
                        <label class="block font-body text-xs font-semibold text-ink-900 mb-1.5">
                            Kategori <span class="text-semantic-danger">*</span>
                        </label>
                        <select name="category_id" id="category_id" required
                            class="w-full px-3 text-xs transition-all border rounded-sm outline-none h-11 font-body text-ink-900 bg-surface-0 border-border-200 focus:border-primary-600 focus:ring-2 focus:ring-primary-100">
                            <option value="">Pilih Kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-body text-xs font-semibold text-ink-900 mb-1.5">Tipe Produk</label>
                        <select name="type" id="type_select"
                            class="w-full px-3 text-xs transition-all border rounded-sm outline-none h-11 font-body text-ink-900 bg-surface-0 border-border-200 focus:border-primary-600">
                            <option value="product">Barang Fisik (Stok Dihitung)</option>
                            <option value="service">Jasa / Layanan (Non-Stok)</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block font-body text-xs font-semibold text-ink-900 mb-1.5">Kode SKU /
                            Barcode</label>
                        <div class="relative">
                            <input type="text" name="sku" id="sku_input" required
                                class="w-full pl-3 pr-24 font-mono text-xs font-semibold transition-all border rounded-sm outline-none h-11 text-ink-900 bg-surface-0 border-border-200 focus:border-primary-600">
                            <button type="button" onclick="generateSKU()"
                                class="absolute right-1.5 top-1.5 h-8 px-3 text-[11px] font-body font-semibold text-primary-600 bg-primary-50 hover:bg-primary-100 rounded-sm transition-colors">
                                Generate
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Pricing & Stock Control -->
            <div class="p-6 border rounded-lg shadow-sm bg-surface-0 border-border-200">
                <div class="flex items-center gap-2.5 pb-4 mb-6 border-b border-border-200">
                    <div
                        class="flex items-center justify-center text-xs font-bold rounded-md w-7 h-7 bg-primary-50 text-primary-600 font-heading">
                        02
                    </div>
                    <h3 class="text-base font-semibold font-heading text-ink-900">
                        Harga & <span id="section_title">Inventaris Stok</span>
                    </h3>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block font-body text-xs font-semibold text-ink-900 mb-1.5">Harga Modal
                            (HPP)</label>
                        <input type="text" id="display_cost_price" placeholder="Rp 0"
                            class="w-full px-3 font-mono text-xs font-semibold text-right transition-all border rounded-sm outline-none h-11 text-ink-900 bg-surface-0 border-border-200 focus:border-primary-600">
                        <input type="hidden" name="cost_price" id="cost_price" value="0">
                    </div>

                    <div>
                        <label class="block font-body text-xs font-semibold text-ink-900 mb-1.5">
                            Harga Jual <span class="text-semantic-danger">*</span>
                        </label>
                        <input type="text" id="display_sell_price" placeholder="Rp 0" required
                            class="w-full px-3 font-mono text-xs font-bold text-right transition-all border rounded-sm outline-none h-11 text-primary-600 bg-primary-50/40 border-primary-200 focus:border-primary-600">
                        <input type="hidden" name="sell_price" id="sell_price" value="0">
                    </div>

                    <!-- Manage Stock Toggle Box -->
                    <div id="manage_stock_container"
                        class="p-4 border rounded-md md:col-span-2 bg-surface-100 border-border-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-xs font-semibold font-heading text-ink-900">Lacak Ketersediaan Stok</h4>
                                <p class="font-body text-[11px] text-ink-400 mt-0.5">Nonaktifkan jika barang diproduksi
                                    langsung saat ada pesanan.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                <input type="checkbox" name="manage_stock" id="manage_stock" value="1" checked
                                    class="sr-only peer">
                                <div
                                    class="w-11 h-6 bg-border-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-border-200 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600">
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Inventory Stock Fields -->
                    <div id="inventory_fields" class="grid grid-cols-1 gap-4 md:col-span-2 md:grid-cols-2">
                        <div>
                            <label class="block font-body text-xs font-semibold text-ink-900 mb-1.5">Jumlah Stok
                                Awal</label>
                            <input type="number" name="stock" id="stock_input" value="0"
                                class="w-full px-3 font-mono text-xs font-semibold transition-all border rounded-sm outline-none h-11 text-ink-900 bg-surface-0 border-border-200 focus:border-primary-600">
                        </div>
                        <div>
                            <label class="block font-body text-xs font-semibold text-ink-900 mb-1.5">Batas Minimum
                                Warning</label>
                            <input type="number" name="min_stock" id="min_stock_input" value="5"
                                class="w-full px-3 font-mono text-xs font-semibold transition-all border rounded-sm outline-none h-11 text-ink-900 bg-surface-0 border-border-200 focus:border-primary-600">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Section -->
            <div
                class="flex items-center justify-between p-4 border rounded-lg shadow-sm bg-surface-0 border-border-200">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" name="is_active" id="is_active" checked value="1"
                        class="w-4 h-4 rounded text-primary-600 border-border-200 focus:ring-primary-600">
                    <span class="text-xs font-semibold font-body text-ink-900">Langsung Aktifkan di POS</span>
                </label>

                <div class="flex items-center gap-2">
                    <a href="{{ route('products.index') }}"
                        class="inline-flex items-center justify-center px-5 text-xs font-semibold transition-colors rounded-md h-11 bg-surface-100 hover:bg-border-200 text-ink-900 font-body">
                        Batal
                    </a>
                    <button type="submit"
                        class="inline-flex items-center justify-center px-6 text-xs font-semibold text-white transition-colors rounded-md shadow-sm h-11 bg-primary-600 hover:bg-primary-700 active:bg-primary-900 font-body md:text-sm">
                        Simpan Produk
                    </button>
                </div>
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
            if (!name) return alert('Ketik nama produk terlebih dahulu');
            const res =
                `${name.substring(0,3).toUpperCase()}-${(cat || '0').padStart(2,'0')}-${Math.floor(1000+Math.random()*9000)}`;
            document.getElementById('sku_input').value = res;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('type_select');
            const manageStock = document.getElementById('manage_stock');
            const inventoryFields = document.getElementById('inventory_fields');
            const manageStockContainer = document.getElementById('manage_stock_container');
            const sectionTitle = document.getElementById('section_title');
            const stockInput = document.getElementById('stock_input');
            const minStockInput = document.getElementById('min_stock_input');

            function updateUI() {
                if (typeSelect.value === 'service') {
                    sectionTitle.innerText = 'Layanan Jasa';
                    manageStockContainer.classList.add('hidden');
                    inventoryFields.classList.add('hidden');
                    stockInput.value = 0;
                    minStockInput.value = 0;
                } else {
                    sectionTitle.innerText = 'Inventaris Stok';
                    manageStockContainer.classList.remove('hidden');

                    if (manageStock.checked) {
                        inventoryFields.classList.remove('hidden');
                    } else {
                        inventoryFields.classList.add('hidden');
                        stockInput.value = 0;
                        minStockInput.value = 0;
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
