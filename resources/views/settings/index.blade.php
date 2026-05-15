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

            <div class="flex justify-end">
                <button type="submit"
                    class="px-10 py-4 font-black text-white transition bg-blue-600 shadow-xl rounded-2xl hover:bg-blue-700">
                    Simpan Semua Perubahan
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
