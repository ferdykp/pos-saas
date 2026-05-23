<x-app-layout>
    <div class="min-h-screen p-6 mx-auto max-w-7xl bg-gray-50">

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-black tracking-tight text-gray-900">Laci Unduhan Laporan</h1>
                <p class="text-xs font-medium text-gray-400">Daftar laporan Excel yang diproses otomatis oleh sistem di
                    latar belakang.</p>
            </div>
            <a href="{{ route('reports.index') }}"
                class="px-4 py-2 text-xs font-bold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50">
                <i class="mr-1 fa-solid fa-arrow-left"></i> Kembali ke Laporan
            </a>
        </div>

        <div class="overflow-hidden bg-white border border-gray-100 shadow-sm rounded-2xl">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-xs font-black text-gray-400 uppercase border-b border-gray-100 bg-gray-50">
                        <th class="p-4">Jenis Laporan</th>
                        <th class="p-4">Periode Data</th>
                        <th class="p-4">Tanggal Permintaan</th>
                        <th class="p-4 text-center">Status Pemrosesan</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-50" id="exports-table-body">
                    @forelse($exports as $export)
                        <tr data-id="{{ $export->id }}">
                            <td class="p-4 font-bold text-gray-900">
                                <i class="mr-2 text-emerald-600 fa-solid fa-file-excel"></i>{{ $export->report_type }}
                            </td>
                            <td class="p-4 text-xs font-medium text-gray-500">
                                {{ \Carbon\Carbon::parse($export->start_date)->format('d M Y') }} s/d
                                {{ \Carbon\Carbon::parse($export->end_date)->format('d M Y') }}
                            </td>
                            <td class="p-4 text-xs text-gray-400">
                                {{ $export->created_at->translatedFormat('d M Y, H:i') }}
                            </td>
                            <td class="p-4 text-center status-badge-area">
                                @if ($export->status === 'pending')
                                    <span
                                        class="px-3 py-1 text-[10px] font-black uppercase rounded-full bg-amber-50 text-amber-600 animate-pulse">Antrean</span>
                                @elseif($export->status === 'processing')
                                    <span
                                        class="px-3 py-1 text-[10px] font-black uppercase rounded-full bg-blue-50 text-blue-600 animate-pulse">Dianalisis
                                        AI</span>
                                @elseif($export->status === 'completed')
                                    <span
                                        class="px-3 py-1 text-[10px] font-black uppercase rounded-full bg-emerald-50 text-emerald-600">Selesai</span>
                                @else
                                    <span
                                        class="px-3 py-1 text-[10px] font-black uppercase rounded-full bg-rose-50 text-rose-600">Gagal</span>
                                @endif
                            </td>
                            <td class="p-4 text-right action-button-area">
                                @if ($export->status === 'completed')
                                    <a href="{{ route('reports.download-file', $export->id) }}"
                                        class="px-4 py-1.5 text-xs font-black text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition-colors">
                                        <i class="mr-1 fa-solid fa-download"></i> Unduh Berkas
                                    </a>
                                @else
                                    <button disabled
                                        class="px-4 py-1.5 text-xs font-black text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                                        Belum Siap
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-xs italic text-center text-gray-400">Belum ada riwayat
                                permintaan ekspor laporan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $exports->links() }}
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Jalankan pengecekan otomatis setiap 3000 ms (3 detik)
            const checkInterval = setInterval(function() {
                // Hanya jalankan pengecekan jika masih ada badge 'pending' atau 'processing' di halaman
                const activeJobs = document.querySelectorAll('.animate-pulse');
                if (activeJobs.length === 0) {
                    clearInterval(checkInterval); // Berhenti jika semua laporan sudah 'Selesai' / 'Gagal'
                    return;
                }

                // Ambil status terbaru via AJAX Fetch API
                fetch("{{ route('reports.exports-status-json') }}")
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(item => {
                            // Cari baris tabel berdasarkan data-id
                            const row = document.querySelector(`tr[data-id="${item.id}"]`);
                            if (row) {
                                const badgeArea = row.querySelector('.status-badge-area');
                                const buttonArea = row.querySelector('.action-button-area');

                                // Jika status berubah menjadi completed di database, update tampilan HTML-nya langsung
                                if (item.status === 'completed' && !buttonArea.querySelector(
                                        'a')) {
                                    badgeArea.innerHTML =
                                        `<span class="px-3 py-1 text-[10px] font-black uppercase rounded-full bg-emerald-50 text-emerald-600">Selesai</span>`;
                                    buttonArea.innerHTML =
                                        `<a href="${item.download_url}" class="px-4 py-1.5 text-xs font-black text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition-colors"><i class="mr-1 fa-solid fa-download"></i> Unduh Berkas</a>`;
                                } else if (item.status === 'processing') {
                                    badgeArea.innerHTML =
                                        `<span class="px-3 py-1 text-[10px] font-black uppercase rounded-full bg-blue-50 text-blue-600 animate-pulse">Dianalisis AI</span>`;
                                } else if (item.status === 'failed') {
                                    badgeArea.innerHTML =
                                        `<span class="px-3 py-1 text-[10px] font-black uppercase rounded-full bg-rose-50 text-rose-600">Gagal</span>`;
                                    buttonArea.innerHTML =
                                        `<button disabled class="px-4 py-1.5 text-xs font-black text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">Gagal</button>`;
                                }
                            }
                        });
                    })
                    .catch(error => console.error('Gagal memperbarui status antrean:', error));
            }, 3000);
        });
    </script>
</x-app-layout>
