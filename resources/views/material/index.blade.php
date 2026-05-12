<x-app-layout>
    <div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-black tracking-tight text-gray-900">Bahan Baku (Inventori)</h1>
                <p class="font-medium text-gray-500">Kelola stok bahan mentah, bumbu, dan perlengkapan toko.</p>
            </div>
            <div class="flex gap-3">
                {{-- <a href="{{ route('inventory.history') }}"
                    class="px-6 py-3 font-bold text-gray-600 transition bg-white border border-gray-100 rounded-2xl hover:bg-gray-50">
                    Riwayat
                </a> --}}
                <button onclick="document.getElementById('addMaterialModal').classList.remove('hidden')"
                    class="px-6 py-3 font-bold text-white transition bg-blue-600 shadow-xl rounded-2xl shadow-blue-100 hover:bg-blue-700 active:scale-95">
                    + Tambah Bahan
                </button>
            </div>
        </div>

        <div class="overflow-hidden bg-white border border-gray-100 shadow-sm rounded-[2.5rem]">
            <table class="w-full text-left">
                <thead class="border-b border-gray-100 bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-black tracking-widest text-gray-400 uppercase">Bahan Baku</th>
                        <th class="px-6 py-4 text-xs font-black tracking-widest text-center text-gray-400 uppercase">
                            Stok</th>
                        <th class="px-6 py-4 text-xs font-black tracking-widest text-center text-gray-400 uppercase">
                            Min. Stok</th>
                        <th class="px-6 py-4 text-xs font-black tracking-widest text-right text-gray-400 uppercase">
                            Status</th>
                        <th class="px-6 py-4 text-xs font-black tracking-widest text-right text-gray-400 uppercase">
                            Aksi</th>

                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($materials as $item)
                        <tr class="transition-colors hover:bg-gray-50/50">
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-gray-900">{{ $item->name }}</p>
                                <p class="text-[10px] font-bold text-blue-500 uppercase tracking-tight">
                                    {{ $item->sku }}</p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="text-lg font-black {{ $item->stock <= $item->min_stock ? 'text-red-600' : 'text-gray-900' }}">
                                    {{ $item->stock }}
                                </span>
                                <span class="ml-1 text-xs font-bold text-gray-400">{{ $item->unit }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-center text-gray-500">
                                {{ $item->min_stock }} {{ $item->unit }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if ($item->stock <= $item->min_stock)
                                    <span
                                        class="inline-flex items-center px-3 py-1 text-[10px] font-black text-red-600 bg-red-100 rounded-lg uppercase">
                                        <span class="w-1.5 h-1.5 mr-1.5 bg-red-500 rounded-full animate-pulse"></span>
                                        Stok Kritis
                                    </span>
                                @else
                                    <span
                                        class="px-3 py-1 text-[10px] font-black text-green-600 bg-green-100 rounded-lg uppercase">
                                        Aman
                                    </span>
                                @endif
                            </td>
                            <td class="flex justify-end gap-3 px-6 py-4 text-right">
                                <button onclick="openHistoryModal('{{ $item->id }}', '{{ $item->name }}')"
                                    class="text-xs font-bold text-gray-500 uppercase hover:text-blue-600">
                                    Riwayat
                                </button>
                                <button
                                    onclick="openStockModal('{{ $item->id }}', '{{ $item->name }}', '{{ $item->unit }}')"
                                    class="text-xs font-bold tracking-widest text-blue-600 uppercase hover:underline">
                                    Update Stok
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 italic text-center text-gray-400 bg-gray-50/30">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 mb-3 text-gray-200" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    <p class="font-bold">Belum ada bahan baku yang terdaftar</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            {{ $materials->links() }}
        </div>
    </div>

    <div id="addMaterialModal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 bg-gray-900/60 backdrop-blur-sm">
        <div class="bg-white rounded-[2.5rem] p-8 w-full max-w-md shadow-2xl">
            <h3 class="mb-6 text-2xl font-black text-gray-900">Tambah Bahan</h3>
            <form action="{{ route('materials.store') }}" method="POST">
                @csrf
                <div class="space-y-5">
                    <div>
                        <label class="block mb-2 text-xs font-black tracking-widest text-gray-400 uppercase">Nama
                            Bahan</label>
                        <input type="text" name="name" placeholder="Misal: Biji Kopi Arabica" required
                            class="w-full px-4 py-3 font-bold transition border-gray-100 rounded-2xl bg-gray-50 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block mb-2 text-xs font-black tracking-widest text-gray-400 uppercase">Satuan</label>
                            <select name="unit"
                                class="w-full px-4 py-3 font-bold border-gray-100 rounded-2xl bg-gray-50 focus:ring-blue-500">
                                <option value="kg">Kilogram (kg)</option>
                                <option value="gr">Gram (gr)</option>
                                <option value="liter">Liter (l)</option>
                                <option value="ml">Mililiter (ml)</option>
                                <option value="pcs">Pcs / Biji</option>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-2 text-xs font-black tracking-widest text-gray-400 uppercase">Stok
                                Awal</label>
                            <input type="number" name="stock" value="0" min="0" required
                                class="w-full px-4 py-3 font-black text-right text-blue-600 border-gray-100 rounded-2xl bg-gray-50">
                        </div>
                    </div>

                    <div>
                        <label class="block mb-2 text-xs font-black tracking-widest text-gray-400 uppercase">Min. Stok
                            (Peringatan)</label>
                        <input type="number" name="min_stock" value="5" min="1" required
                            class="w-full px-4 py-3 font-bold border-gray-100 rounded-2xl bg-gray-50">
                        <p class="mt-1 text-[10px] text-gray-400 italic">Sistem akan memberi tahu jika stok di bawah
                            angka ini.</p>
                    </div>
                </div>

                <div class="flex gap-3 mt-8">
                    <button type="button" onclick="document.getElementById('addMaterialModal').classList.add('hidden')"
                        class="flex-1 py-4 font-bold text-gray-500 transition bg-gray-100 rounded-2xl hover:bg-gray-200">Batal</button>
                    <button type="submit"
                        class="flex-1 py-4 font-black text-white transition bg-blue-600 shadow-xl rounded-2xl shadow-blue-100 hover:bg-blue-700 active:scale-95">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div id="updateStockModal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 bg-gray-900/60 backdrop-blur-sm">
        <div class="bg-white rounded-[2.5rem] p-8 w-full max-w-md shadow-2xl">
            <h3 id="stockModalTitle" class="mb-6 text-2xl font-black text-gray-900">Update Stok</h3>
            <form action="{{ route('materials.update-stock') }}" method="POST">
                @csrf
                <input type="hidden" name="material_id" id="stockMaterialId">

                <div class="space-y-5">
                    <div>
                        <label class="block mb-2 text-xs font-black text-gray-400 uppercase">Tipe</label>
                        <select name="type" id="typeSelect" onchange="toggleSupplierFields()"
                            class="w-full px-4 py-3 font-bold border-gray-100 rounded-2xl bg-gray-50 focus:ring-blue-500">
                            <option value="stock_in">Barang Masuk (+)</option>
                            <option value="stock_out">Barang Keluar (-)</option>
                            <option value="adjustment">Penyesuaian / Rusak (-)</option>
                        </select>
                    </div>

                    <div id="supplierFields"
                        class="p-4 mt-4 space-y-4 border border-blue-50 rounded-3xl bg-blue-50/30">
                        <div>
                            <label
                                class="block mb-2 text-[10px] font-black text-blue-400 uppercase tracking-widest">Supplier</label>
                            <select name="supplier_id"
                                class="w-full px-4 py-3 font-bold bg-white border-white shadow-sm rounded-2xl focus:ring-blue-500">
                                <option value="">-- Pilih Supplier --</option>
                                @foreach ($suppliers as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label
                                class="block mb-2 text-[10px] font-black text-blue-400 uppercase tracking-widest">Harga
                                Beli Satuan (Rp)</label>
                            <input type="number" name="purchase_price" placeholder="0"
                                class="w-full px-4 py-3 font-black bg-white border-white shadow-sm rounded-2xl focus:ring-blue-500">
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between mb-2">
                            <label class="text-xs font-black text-gray-400 uppercase">Jumlah</label>
                            <span id="displayUnit"
                                class="text-xs font-black tracking-tighter text-blue-500 uppercase"></span>
                        </div>
                        <div class="relative">
                            <input type="number" name="quantity" required min="1" placeholder="0"
                                class="w-full px-4 py-3 text-lg font-black border-gray-100 rounded-2xl bg-gray-50 focus:ring-blue-500">
                        </div>
                    </div>

                    <div>
                        <label class="block mb-2 text-xs font-black text-gray-400 uppercase">Catatan</label>
                        <textarea name="note" rows="2" placeholder="Contoh: Belanja mingguan atau Telur pecah"
                            class="w-full px-4 py-3 text-sm font-medium border-gray-100 rounded-2xl bg-gray-50 focus:ring-blue-500"></textarea>
                    </div>
                </div>

                <div class="flex gap-3 mt-8">
                    <button type="button" onclick="closeStockModal()"
                        class="flex-1 py-4 font-bold text-gray-500 transition bg-gray-100 rounded-2xl hover:bg-gray-200">Batal</button>
                    <button type="submit"
                        class="flex-1 py-4 font-black text-white transition bg-blue-600 shadow-xl rounded-2xl shadow-blue-100 hover:bg-blue-700 active:scale-95">Update
                        Stok</button>
                </div>
            </form>
        </div>
    </div>
    <div id="historyModal"
        class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 bg-gray-900/60 backdrop-blur-sm">
        <div class="bg-white rounded-[2.5rem] p-8 w-full max-w-2xl shadow-2xl max-h-[80vh] flex flex-col">
            <div class="flex items-center justify-between mb-6">
                <h3 id="historyModalTitle" class="text-2xl font-black text-gray-900">Riwayat Stok</h3>
                <button onclick="closeHistoryModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="sticky top-0 bg-white">
                        <tr class="border-b border-gray-100">
                            <th class="py-3 text-[10px] font-black text-gray-400 uppercase">Waktu</th>
                            <th class="py-3 text-[10px] font-black text-gray-400 uppercase">Tipe</th>
                            <th class="py-3 text-[10px] font-black text-gray-400 uppercase text-center">Qty</th>
                            <th class="py-3 text-[10px] font-black text-gray-400 uppercase text-center">Saldo</th>
                            <th class="py-3 text-[10px] font-black text-gray-400 uppercase">Catatan</th>
                        </tr>
                    </thead>
                    <tbody id="historyContent" class="text-sm divide-y divide-gray-50">
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function openHistoryModal(id, name) {
            const content = document.getElementById('historyContent');
            document.getElementById('historyModalTitle').innerText = 'Riwayat: ' + name;
            document.getElementById('historyModal').classList.remove('hidden');

            // Loading state
            content.innerHTML = '<tr><td colspan="5" class="py-10 text-center text-gray-400">Memuat data...</td></tr>';

            fetch(`/materials/${id}/history`)
                .then(response => response.json())
                .then(data => {
                    if (data.length === 0) {
                        content.innerHTML =
                            '<tr><td colspan="5" class="py-10 italic text-center text-gray-400">Belum ada riwayat mutasi.</td></tr>';
                        return;
                    }

                    content.innerHTML = data.map(item => {
                        const date = new Date(item.created_at).toLocaleDateString('id-ID', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric'
                        });
                        const time = new Date(item.created_at).toLocaleTimeString('id-ID', {
                            hour: '2-digit',
                            minute: '2-digit'
                        });

                        let typeClass = 'bg-gray-100 text-gray-600';
                        if (item.type === 'stock_in') typeClass = 'bg-green-100 text-green-600';
                        if (item.type === 'stock_out') typeClass = 'bg-red-100 text-red-600';

                        return `
                        <tr class="hover:bg-gray-50/50">
                            <td class="py-4">
                                <span class="font-bold text-gray-700">${date}</span><br>
                                <span class="text-[10px] text-gray-400 uppercase">${time}</span>
                            </td>
                            <td class="py-4 font-black">
                                <span class="px-2 py-0.5 rounded text-[10px] uppercase ${typeClass}">${item.type.replace('_', ' ')}</span>
                            </td>
                            <td class="py-4 text-center font-bold ${item.type === 'stock_in' ? 'text-green-600' : 'text-red-600'}">
                                ${item.type === 'stock_in' ? '+' : '-'}${item.quantity}
                            </td>
                            <td class="py-4 font-black text-center text-gray-900">${item.after_stock}</td>
                            <td class="py-4 text-xs italic text-gray-500">${item.note ?? '-'}</td>
                        </tr>
                    `;
                    }).join('');
                });
        }

        function closeHistoryModal() {
            document.getElementById('historyModal').classList.add('hidden');
        }
    </script>

    <script>
        // Format Rupiah Helper
        const formatRupiah = (number) => {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(number);
        }

        function toggleSupplierFields() {
            const type = document.getElementById('typeSelect').value;
            const fields = document.getElementById('supplierFields');
            // Tampilkan supplier & harga hanya jika Barang Masuk
            fields.style.display = (type === 'stock_in') ? 'block' : 'none';
        }

        function openStockModal(id, name, unit) {
            document.getElementById('stockMaterialId').value = id;
            document.getElementById('stockModalTitle').innerText = 'Stok: ' + name;
            document.getElementById('displayUnit').innerText = 'Satuan: ' + unit; // Tampilkan unit
            document.getElementById('updateStockModal').classList.remove('hidden');
            toggleSupplierFields(); // Jalankan pengecekan tipe awal
        }

        function closeStockModal() {
            document.getElementById('updateStockModal').classList.add('hidden');
        }

        function openHistoryModal(id, name) {
            const content = document.getElementById('historyContent');
            document.getElementById('historyModalTitle').innerText = 'Riwayat: ' + name;
            document.getElementById('historyModal').classList.remove('hidden');

            content.innerHTML = '<tr><td colspan="5" class="py-10 text-center text-gray-400">Memuat data...</td></tr>';

            fetch(`/materials/${id}/history`)
                .then(response => response.json())
                .then(data => {
                    if (data.length === 0) {
                        content.innerHTML =
                            '<tr><td colspan="5" class="py-10 italic text-center text-gray-400">Belum ada riwayat mutasi.</td></tr>';
                        return;
                    }

                    content.innerHTML = data.map(item => {
                        const date = new Date(item.created_at).toLocaleDateString('id-ID', {
                            day: '2-digit',
                            month: 'short'
                        });
                        const time = new Date(item.created_at).toLocaleTimeString('id-ID', {
                            hour: '2-digit',
                            minute: '2-digit'
                        });

                        let typeClass = 'bg-gray-100 text-gray-600';
                        if (item.type === 'stock_in') typeClass = 'bg-green-100 text-green-600';
                        if (item.type === 'stock_out' || item.type === 'adjustment') typeClass =
                            'bg-red-100 text-red-600';

                        // Info Supplier & Harga jika ada
                        const supplierInfo = item.supplier ?
                            `<p class="text-[10px] text-blue-500 font-black uppercase mt-1">S: ${item.supplier.name}</p>` :
                            '';
                        const priceInfo = item.purchase_price > 0 ?
                            `<p class="text-[10px] text-gray-400 font-bold italic">${formatRupiah(item.purchase_price)}</p>` :
                            '';

                        return `
                    <tr class="hover:bg-gray-50/50">
                        <td class="py-4">
                            <span class="font-bold text-gray-700">${date}</span><br>
                            <span class="text-[10px] text-gray-400 uppercase">${time}</span>
                        </td>
                        <td class="py-4">
                            <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase ${typeClass}">${item.type.replace('_', ' ')}</span>
                            ${supplierInfo}
                        </td>
                        <td class="py-4 text-center font-bold ${item.type === 'stock_in' ? 'text-green-600' : 'text-red-600'}">
                            ${item.type === 'stock_in' ? '+' : '-'}${item.quantity}
                            ${priceInfo}
                        </td>
                        <td class="py-4 font-black text-center text-gray-900">${item.after_stock}</td>
                        <td class="py-4 text-xs italic text-gray-500">${item.note ?? '-'}</td>
                    </tr>
                `;
                    }).join('');
                });
        }

        function closeHistoryModal() {
            document.getElementById('historyModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
