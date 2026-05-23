{{-- <x-app-layout>
    <div class="max-w-4xl px-4 py-12 mx-auto">
        <h1 class="mb-8 text-3xl font-black text-gray-900">Sistem Pengaturan</h1>

        <form action="{{ route('settings.store') }}" method="POST" class="space-y-6">
            @csrf
            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
                <h3 class="flex items-center mb-6 text-lg font-bold text-gray-900">
                    <i class="mr-3 text-blue-500 fa-solid fa-receipt"></i> Konfigurasi Pajak (PPN)
                </h3>

                <div class="flex items-center justify-between p-6 mb-6 bg-gray-50 rounded-2xl">
                    <div>
                        <p class="font-bold text-gray-700">Aktifkan Pajak Restoran</p>
                        <p class="text-xs text-gray-400">Jika aktif, total belanja akan otomatis ditambah pajak.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="tax_active" value="1"
                            {{ ($settings['tax_active'] ?? '0') == '1' ? 'checked' : '' }} class="sr-only peer">
                        <div
                            class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-blue-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full">
                        </div>
                    </label>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-bold text-gray-700">Persentase Pajak (%)</label>
                    <input type="number" name="tax_percentage" value="{{ $settings['tax_percentage'] ?? '10' }}"
                        class="w-full px-4 py-3 border-gray-100 rounded-2xl bg-gray-50 focus:ring-blue-500">
                </div>
            </div>


            <div class="p-6 bg-white shadow-xl rounded-2xl">
                <h3 class="mb-4 text-lg font-black text-gray-900">Konfigurasi Poin Loyalitas Pelanggan</h3>

                <form action="{{ route('settings.update-points') }}" method="POST" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="block text-sm font-bold text-gray-700">Metode Perhitungan Poin</label>
                            <select name="point_mode" id="point_mode"
                                class="w-full mt-1 border-gray-200 rounded-xl focus:ring-blue-500">
                                <option value="disabled"
                                    {{ ($settings['point_mode'] ?? '') == 'disabled' ? 'selected' : '' }}>Nonaktifkan
                                    Poin</option>
                                <option value="per_investment"
                                    {{ ($settings['point_mode'] ?? '') == 'per_investment' ? 'selected' : '' }}>
                                    Kelipatan Total Belanja (Rekomendasi)</option>
                                <option value="flat"
                                    {{ ($settings['point_mode'] ?? '') == 'flat' ? 'selected' : '' }}>Poin Tetap Per
                                    Transaksi</option>
                                <option value="percentage"
                                    {{ ($settings['point_mode'] ?? '') == 'percentage' ? 'selected' : '' }}>Persentase
                                    dari Total Belanja</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700" id="value_label">Nilai Aturan</label>
                            <input type="number" name="point_rule_value"
                                value="{{ $settings['point_rule_value'] ?? 0 }}"
                                class="w-full mt-1 border-gray-200 rounded-xl focus:ring-blue-500">
                            <p class="text-[11px] text-gray-400 mt-1" id="value_help">Contoh: Isi 10000 jika ingin 1
                                poin setiap belanja Rp10.000.</p>
                        </div>
                    </div>

                    <div class="flex items-center p-4 mt-2 bg-gray-50 rounded-2xl">
                        <input type="checkbox" name="point_member_only" id="point_member_only" value="1"
                            {{ ($settings['point_member_only'] ?? '0') == '1' ? 'checked' : '' }}
                            class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="point_member_only" class="ml-3 text-sm font-bold text-gray-700">Hanya berikan poin
                            kepada pelanggan dengan status "Member Active"</label>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="px-6 py-3 font-black text-white bg-blue-600 rounded-xl hover:bg-blue-700">Simpan
                            Aturan Poin</button>
                    </div>
                </form>
            </div>

            <script>
                // Script sederhana untuk mengubah teks bantuan agar user tidak bingung saat memilih opsi
                document.getElementById('point_mode').addEventListener('change', function() {
                    const mode = this.value;
                    const label = document.getElementById('value_label');
                    const help = document.getElementById('value_help');

                    if (mode === 'per_investment') {
                        label.innerText = "Kelipatan Belanja (Rp)";
                        help.innerText = "Contoh: Isi 10000 artinya kelipatan kelipatan Rp10.000 dapat 1 poin.";
                    } else if (mode === 'flat') {
                        label.innerText = "Jumlah Poin Tetap";
                        help.innerText =
                            "Contoh: Isi 5 artinya berapapun total belanjanya, pelanggan otomatis dapat 5 poin.";
                    } else if (mode === 'percentage') {
                        label.innerText = "Persentase Poin (%)";
                        help.innerText =
                            "Contoh: Isi 1 artinya pelanggan dapat poin sebesar 1% dari total nilai transaksinya.";
                    } else {
                        label.innerText = "Nilai Aturan";
                        help.innerText = "-";
                    }
                });
            </script>

            <div class="flex justify-end">
                <button type="submit"
                    class="px-10 py-4 font-black text-white transition bg-blue-600 shadow-xl rounded-2xl hover:bg-blue-700">
                    Simpan Semua Perubahan
                </button>
            </div>
        </form>
    </div>
</x-app-layout> --}}
<x-app-layout>
    <div class="max-w-4xl px-4 py-12 mx-auto">
        <h1 class="mb-8 text-3xl font-black text-gray-900">Sistem Pengaturan</h1>

        <form action="{{ route('settings.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
                <h3 class="flex items-center mb-6 text-lg font-bold text-gray-900">
                    <i class="mr-3 text-blue-500 fa-solid fa-receipt"></i> Konfigurasi Pajak (PPN)
                </h3>

                <div class="flex items-center justify-between p-6 mb-6 bg-gray-50 rounded-2xl">
                    <div>
                        <p class="font-bold text-gray-700">Aktifkan Pajak Restoran</p>
                        <p class="text-xs text-gray-400">Jika aktif, total belanja akan otomatis ditambah pajak.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="tax_active" value="1"
                            {{ ($settings['tax_active'] ?? '0') == '1' ? 'checked' : '' }} class="sr-only peer">
                        <div
                            class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-blue-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full">
                        </div>
                    </label>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-bold text-gray-700">Persentase Pajak (%)</label>
                    <input type="number" name="tax_percentage" value="{{ $settings['tax_percentage'] ?? '10' }}"
                        class="w-full px-4 py-3 border-gray-100 rounded-2xl bg-gray-50 focus:ring-blue-500">
                </div>
            </div>

            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
                <h3 class="flex items-center mb-6 text-lg font-bold text-gray-900">
                    <i class="mr-3 text-blue-500 fa-solid fa-star"></i> Konfigurasi Poin Loyalitas Pelanggan
                </h3>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-bold text-gray-700">Metode Perhitungan Poin</label>
                        <select name="point_mode" id="point_mode"
                            class="w-full mt-1 border-gray-200 rounded-xl focus:ring-blue-500">
                            <option value="disabled"
                                {{ ($settings['point_mode'] ?? '') == 'disabled' ? 'selected' : '' }}>
                                Nonaktifkan Poin
                            </option>
                            <option value="per_investment"
                                {{ ($settings['point_mode'] ?? '') == 'per_investment' ? 'selected' : '' }}>
                                Kelipatan Total Belanja (Rekomendasi)
                            </option>
                            <option value="flat" {{ ($settings['point_mode'] ?? '') == 'flat' ? 'selected' : '' }}>
                                Poin Tetap Per Transaksi
                            </option>
                            <option value="percentage"
                                {{ ($settings['point_mode'] ?? '') == 'percentage' ? 'selected' : '' }}>
                                Persentase dari Total Belanja
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700" id="value_label">Nilai Aturan</label>
                        <input type="number" name="point_rule_value" value="{{ $settings['point_rule_value'] ?? 0 }}"
                            class="w-full mt-1 border-gray-200 rounded-xl focus:ring-blue-500">
                        <p class="text-[11px] text-gray-400 mt-1" id="value_help">Contoh: Isi 10000 jika ingin 1 poin
                            setiap belanja Rp10.000.</p>
                    </div>
                </div>

                <div class="flex items-center p-4 mt-4 bg-gray-50 rounded-2xl">
                    <input type="checkbox" name="point_member_only" id="point_member_only" value="1"
                        {{ ($settings['point_member_only'] ?? '0') == '1' ? 'checked' : '' }}
                        class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <label for="point_member_only" class="ml-3 text-sm font-bold text-gray-700">Hanya berikan poin
                        kepada pelanggan dengan status "Member Active"</label>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="px-10 py-4 font-black text-white transition bg-blue-600 shadow-xl rounded-2xl hover:bg-blue-700">
                    Simpan Semua Perubahan
                </button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('point_mode').addEventListener('change', function() {
            const mode = this.value;
            const label = document.getElementById('value_label');
            const help = document.getElementById('value_help');

            if (mode === 'per_investment') {
                label.innerText = "Kelipatan Belanja (Rp)";
                help.innerText = "Contoh: Isi 10000 artinya kelipatan kelipatan Rp10.000 dapat 1 poin.";
            } else if (mode === 'flat') {
                label.innerText = "Jumlah Poin Tetap";
                help.innerText =
                    "Contoh: Isi 5 artinya berapapun total belanjanya, pelanggan otomatis dapat 5 poin.";
            } else if (mode === 'percentage') {
                label.innerText = "Persentase Poin (%)";
                help.innerText =
                    "Contoh: Isi 1 artinya pelanggan dapat poin sebesar 1% dari total nilai transaksinya.";
            } else {
                label.innerText = "Nilai Aturan";
                help.innerText = "-";
            }
        });
    </script>
</x-app-layout>
