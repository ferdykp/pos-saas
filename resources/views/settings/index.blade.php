<x-app-layout>
    @section('title', 'Sistem Pengaturan')

    <div class="px-4 py-6 mx-auto md:px-6 lg:px-8 max-w-modal-lg">

        <!-- Flash Session Notification -->
        @if (session('success'))
            <div
                class="flex items-center gap-3 p-4 mb-6 text-sm font-medium border-l-4 rounded-md shadow-sm bg-primary-50 border-primary-600 text-ink-900">
                <i class="text-base fa-solid fa-circle-check text-primary-600"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('warning'))
            <div
                class="flex items-center gap-3 p-4 mb-6 text-sm font-medium border-l-4 rounded-md shadow-sm bg-amber-50 border-amber-500 text-ink-900">
                <i class="text-base fa-solid fa-triangle-exclamation text-amber-600"></i>
                <span>{{ session('warning') }}</span>
            </div>
        @endif

        <!-- Header Halaman -->
        <div class="pb-6 mb-8 border-b border-border-200">
            <h1 class="font-heading font-bold text-2xl md:text-[28px] text-ink-900 leading-tight">
                Sistem Pengaturan Toko
            </h1>
            <p class="mt-1 text-xs font-body md:text-sm text-ink-700">
                Atur konfigurasi Pajak Restoran (PB1/PPN), kalkulasi poin loyalitas pelanggan, dan kebijakan operasional
                outlet.
            </p>
        </div>

        <form action="{{ route('settings.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Section 1: Konfigurasi Pajak (PPN/PB1) -->
            <div class="p-6 border rounded-lg shadow-sm bg-surface-0 border-border-200">
                <div class="flex items-center gap-2.5 pb-4 mb-6 border-b border-border-200">
                    <div
                        class="flex items-center justify-center w-8 h-8 text-xs font-bold rounded-md bg-primary-50 text-primary-600 font-heading">
                        <i class="text-xs fa-solid fa-receipt"></i>
                    </div>
                    <h3 class="text-base font-semibold font-heading text-ink-900">
                        Konfigurasi Pajak Restoran / PPN
                    </h3>
                </div>

                <!-- Toggle Aktifkan Pajak -->
                <div
                    class="flex items-center justify-between p-4 mb-4 border rounded-md bg-surface-100 border-border-200">
                    <div>
                        <h4 class="text-xs font-semibold font-heading text-ink-900">Aktifkan Pajak Otomatis</h4>
                        <p class="font-body text-[11px] text-ink-400 mt-0.5">
                            Jika diaktifkan, kalkulasi total belanja di POS Terminal otomatis menambahkan tarif pajak.
                        </p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer shrink-0">
                        <input type="checkbox" name="tax_active" value="1"
                            {{ ($settings['tax_active'] ?? '0') == '1' ? 'checked' : '' }} class="sr-only peer">
                        <div
                            class="w-11 h-6 bg-border-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-border-200 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600">
                        </div>
                    </label>
                </div>

                <!-- Persentase Pajak -->
                <div>
                    <label class="block font-body text-xs font-semibold text-ink-900 mb-1.5">
                        Tarif Persentase Pajak (%)
                    </label>
                    <div class="relative max-w-xs">
                        <input type="number" name="tax_percentage" value="{{ $settings['tax_percentage'] ?? '10' }}"
                            min="0" max="100" step="0.1"
                            class="w-full pl-3 pr-8 font-mono text-xs font-semibold transition-all border rounded-sm outline-none h-11 text-ink-900 bg-surface-0 border-border-200 focus:border-primary-600 focus:ring-2 focus:ring-primary-100">
                        <span class="absolute font-mono text-xs right-3 top-3 text-ink-400">%</span>
                    </div>
                </div>
            </div>

            <!-- Section 2: Konfigurasi Poin Loyalitas Pelanggan (CRM) -->
            <div class="relative p-6 overflow-hidden border rounded-lg shadow-sm bg-surface-0 border-border-200">

                <div class="flex items-center justify-between pb-4 mb-6 border-b border-border-200">
                    <div class="flex items-center gap-2.5">
                        <div
                            class="flex items-center justify-center w-8 h-8 text-xs font-bold rounded-md bg-accent-100 text-accent-700 font-heading">
                            <i class="text-xs fa-solid fa-star"></i>
                        </div>
                        <h3 class="text-base font-semibold font-heading text-ink-900">
                            Konfigurasi Poin Loyalitas Pelanggan (CRM)
                        </h3>
                    </div>

                    @cannot('feature-crm')
                        <span
                            class="px-2.5 py-1 text-[10px] font-extrabold text-amber-900 bg-amber-100 border border-amber-300 rounded-full uppercase tracking-wider">
                            <i class="fa-solid fa-lock text-[9px] mr-1"></i> Paket Growth & Scale
                        </span>
                    @endcannot
                </div>

                @can('feature-crm')
                    <!-- TAMPILAN AKTIF (USER DENGAN PAKET GROWTH / SCALE) -->
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <!-- Point Mode Select -->
                        <div>
                            <label class="block font-body text-xs font-semibold text-ink-900 mb-1.5">
                                Skema Perhitungan Poin
                            </label>
                            <select name="point_mode" id="point_mode"
                                class="w-full px-3 text-xs transition-all border rounded-sm outline-none h-11 font-body text-ink-900 bg-surface-0 border-border-200 focus:border-primary-600">
                                <option value="disabled"
                                    {{ ($settings['point_mode'] ?? '') == 'disabled' ? 'selected' : '' }}>
                                    Nonaktifkan Poin Loyalitas
                                </option>
                                <option value="per_investment"
                                    {{ ($settings['point_mode'] ?? '') == 'per_investment' ? 'selected' : '' }}>
                                    Kelipatan Total Belanja (Rekomendasi)
                                </option>
                                <option value="flat" {{ ($settings['point_mode'] ?? '') == 'flat' ? 'selected' : '' }}>
                                    Poin Tetap Per Transaksi Nota
                                </option>
                                <option value="percentage"
                                    {{ ($settings['point_mode'] ?? '') == 'percentage' ? 'selected' : '' }}>
                                    Persentase dari Total Belanja
                                </option>
                            </select>
                        </div>

                        <!-- Rule Value Input -->
                        <div>
                            <label class="block font-body text-xs font-semibold text-ink-900 mb-1.5" id="value_label">
                                Nilai Aturan Poin
                            </label>
                            <input type="number" name="point_rule_value" value="{{ $settings['point_rule_value'] ?? 0 }}"
                                class="w-full px-3 font-mono text-xs font-semibold transition-all border rounded-sm outline-none h-11 text-ink-900 bg-surface-0 border-border-200 focus:border-primary-600">
                            <p class="font-body text-[11px] text-ink-400 mt-1" id="value_help">
                                Contoh: Isi 10000 jika ingin 1 poin setiap belanja Rp 10.000.
                            </p>
                        </div>
                    </div>

                    <!-- Member Only Checkbox -->
                    <div class="flex items-center gap-3 p-4 mt-4 border rounded-md bg-surface-100 border-border-200">
                        <input type="checkbox" name="point_member_only" id="point_member_only" value="1"
                            {{ ($settings['point_member_only'] ?? '0') == '1' ? 'checked' : '' }}
                            class="w-4 h-4 rounded text-primary-600 border-border-200 focus:ring-primary-600">
                        <label for="point_member_only"
                            class="text-xs font-semibold cursor-pointer select-none font-body text-ink-900">
                            Hanya berikan poin transaksi kepada pelanggan berstatus "Member Aktif"
                        </label>
                    </div>
                @else
                    <!-- TAMPILAN TERKUNCI (USER DENGAN PAKET STARTER) -->
                    <div
                        class="flex flex-col justify-between gap-4 p-4 border rounded-lg bg-amber-50/60 border-amber-200 sm:flex-row sm:items-center">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex items-center justify-center w-10 h-10 rounded-full bg-amber-100 text-amber-700 shrink-0">
                                <i class="text-base fa-solid fa-crown"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-amber-900">Fitur CRM & Poin Pelanggan Terkunci</h4>
                                <p class="text-[11px] text-amber-800 mt-0.5">
                                    Fitur otomatisasi poin loyalitas dan manajemen member toko hanya tersedia pada
                                    <strong>Paket Growth</strong> dan <strong>Scale</strong>.
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('billing.index') }}"
                            class="inline-flex items-center justify-center gap-1.5 px-4 py-2 text-xs font-bold text-white bg-amber-600 hover:bg-amber-700 rounded-md transition shadow-sm shrink-0">
                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                            <span>Upgrade Paket</span>
                        </a>
                    </div>

                    <!-- Input Dummy Disabled (Visual Overlay) -->
                    <div class="grid grid-cols-1 gap-4 mt-4 pointer-events-none select-none md:grid-cols-2 opacity-40">
                        <div>
                            <label class="block font-body text-xs font-semibold text-ink-900 mb-1.5">Skema Perhitungan
                                Poin</label>
                            <select disabled class="w-full px-3 text-xs border rounded-sm h-11 bg-surface-100 text-ink-400">
                                <option>Nonaktifkan Poin Loyalitas</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-body text-xs font-semibold text-ink-900 mb-1.5">Nilai Aturan
                                Poin</label>
                            <input type="number" disabled value="0"
                                class="w-full px-3 text-xs border rounded-sm h-11 bg-surface-100 text-ink-400">
                        </div>
                    </div>
                @endcan

            </div>

            <!-- Submit Button Bar -->
            <div class="flex items-center justify-end p-4 border rounded-lg shadow-sm bg-surface-0 border-border-200">
                <button type="submit"
                    class="inline-flex items-center justify-center gap-2 px-6 text-xs font-semibold text-white transition-colors rounded-md shadow-sm h-11 bg-primary-600 hover:bg-primary-700 active:bg-primary-900 font-body md:text-sm">
                    <i class="text-xs fa-solid fa-check"></i>
                    <span>Simpan Semua Pengaturan</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Script Dynamic Help Label -->
    @can('feature-crm')
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const pointModeSelect = document.getElementById('point_mode');
                const label = document.getElementById('value_label');
                const help = document.getElementById('value_help');

                if (!pointModeSelect) return;

                function updatePointHelp() {
                    const mode = pointModeSelect.value;
                    if (mode === 'per_investment') {
                        label.innerText = "Kelipatan Belanja (Rp)";
                        help.innerText = "Contoh: Isi 10000 artinya kelipatan Rp 10.000 akan mendapatkan 1 poin.";
                    } else if (mode === 'flat') {
                        label.innerText = "Jumlah Poin Tetap";
                        help.innerText =
                            "Contoh: Isi 5 artinya berapapun total belanjanya, pelanggan otomatis dapat 5 poin.";
                    } else if (mode === 'percentage') {
                        label.innerText = "Persentase Poin (%)";
                        help.innerText =
                            "Contoh: Isi 1 artinya pelanggan dapat poin sebesar 1% dari total nilai transaksi.";
                    } else {
                        label.innerText = "Nilai Aturan Poin";
                        help.innerText = "Poin loyalitas pelanggan saat ini dalam posisi nonaktif.";
                    }
                }

                pointModeSelect.addEventListener('change', updatePointHelp);
                updatePointHelp();
            });
        </script>
    @endcan
</x-app-layout>
