<x-app-layout>
    <div class="max-w-4xl p-6 mx-auto bg-white shadow-xl rounded-2xl">
        <h2 class="mb-6 text-2xl font-black text-gray-900">Buat Event Diskon Baru</h2>

        <form action="{{ route('discounts.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-bold text-gray-700">Nama Diskon / Event</label>
                    <input type="text" name="name" placeholder="Contoh: Morning Promo, Imlek Sale" required
                        class="w-full mt-1 border-gray-200 rounded-xl focus:ring-blue-500">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-sm font-bold text-gray-700">Tipe</label>
                        <select name="type" class="w-full mt-1 border-gray-200 rounded-xl focus:ring-blue-500">
                            <option value="percentage">Persentase (%)</option>
                            <option value="fixed">Potongan Harga (Rp)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700">Nilai</label>
                        <input type="number" name="value" step="0.01" placeholder="10 atau 15000" required
                            class="w-full mt-1 border-gray-200 rounded-xl focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <hr class="border-gray-100">

            <div class="p-4 bg-gray-50 rounded-xl">
                <h3 class="mb-4 text-sm font-black tracking-wider text-gray-700 uppercase">Pengaturan Waktu Aktif
                    (Opsional)</h3>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-xs font-bold text-gray-500">Tanggal Mulai</label>
                        <input type="date" name="start_date"
                            class="w-full mt-1 border-gray-200 rounded-xl focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500">Tanggal Selesai</label>
                        <input type="date" name="end_date"
                            class="w-full mt-1 border-gray-200 rounded-xl focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500">Jam Mulai</label>
                        <input type="time" name="start_time" placeholder="08:00"
                            class="w-full mt-1 border-gray-200 rounded-xl focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500">Jam Selesai</label>
                        <input type="time" name="end_time" placeholder="11:00"
                            class="w-full mt-1 border-gray-200 rounded-xl focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="text-sm font-bold text-gray-700">Pilih Menu / Produk Yang Terkena Diskon</label>

                    <label
                        class="flex items-center space-x-2 text-xs font-bold text-blue-600 cursor-pointer select-none hover:text-blue-700">
                        <input type="checkbox" id="selectAllProducts" onclick="toggleSelectAll(this)"
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span>Pilih Semua</span>
                    </label>
                </div>

                <div
                    class="grid grid-cols-2 gap-3 p-4 overflow-y-auto border border-gray-100 max-h-60 rounded-xl bg-gray-50">
                    @foreach ($products as $product)
                        <label
                            class="flex items-center p-2 space-x-3 bg-white border border-transparent rounded-lg shadow-sm cursor-pointer hover:border-blue-500">
                            <input type="checkbox" name="product_ids[]" value="{{ $product->id }}"
                                class="text-blue-600 border-gray-300 rounded product-checkbox focus:ring-blue-500">
                            <span class="text-sm font-medium text-gray-700">{{ $product->product_name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center justify-between p-4 border border-blue-100 bg-blue-50 rounded-xl">
                <div>
                    <h4 class="text-sm font-bold text-blue-900">Aktifkan Diskon Sekarang?</h4>
                    <p class="text-xs font-medium text-blue-700">Jika tidak dicentang, diskon tidak akan muncul di POS.
                    </p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked class="sr-only peer">
                    <div
                        class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                    </div>
                </label>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('discounts.index') }}"
                    class="px-6 py-3 font-bold text-gray-500 bg-gray-100 rounded-xl hover:bg-gray-200">Batal</a>
                <button type="submit"
                    class="px-6 py-3 font-black text-white bg-blue-600 shadow-md rounded-xl hover:bg-blue-700">Simpan
                    Event Diskon</button>
            </div>
        </form>
    </div>

    <script>
        function toggleSelectAll(master) {
            // Ambil semua checkbox produk yang memiliki class 'product-checkbox'
            const checkboxes = document.querySelectorAll('.product-checkbox');

            checkboxes.forEach(cb => {
                cb.checked = master.checked;
            });
        }

        // Opsional: Batalkan centang 'Pilih Semua' jika salah satu produk manual di-uncheck oleh user
        document.addEventListener('DOMContentLoaded', () => {
            const masterCheckbox = document.getElementById('selectAllProducts');
            const checkboxes = document.querySelectorAll('.product-checkbox');

            checkboxes.forEach(cb => {
                cb.addEventListener('change', () => {
                    const totalCheckboxes = checkboxes.length;
                    const totalChecked = document.querySelectorAll('.product-checkbox:checked')
                        .length;

                    // Jika jumlah yang dicentang sama dengan total produk, centang master. Jika tidak, matikan.
                    masterCheckbox.checked = (totalCheckboxes === totalChecked);
                });
            });
        });
    </script>
</x-app-layout>
