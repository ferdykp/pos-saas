<x-app-layout>
    <div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8" x-data="{
        showFilterDropdown: false,
        showExportDropdown: false
    }">

        <div
            class="flex flex-col items-start justify-between gap-4 pb-6 mb-8 border-b border-gray-100 sm:flex-row sm:items-center">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-gray-900">Manajemen Shift</h1>
                <p class="mt-1.5 text-sm text-gray-500">Pantau perputaran kas, performa staf, dan riwayat operasional
                    toko Anda.</p>
            </div>

            <div class="flex flex-wrap items-center w-full gap-3 sm:w-auto">
                <button @click="showExportDropdown = !showExportDropdown"
                    class="inline-flex relative justify-center items-center py-2.5 px-4 text-sm font-semibold text-gray-700 bg-white rounded-xl border border-gray-200 transition-all hover:bg-gray-50 active:scale-95">
                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4 4m4 4V4" />
                    </svg>
                    Ekspor Data
                </button>

                @php
                    $activeShift = $shifts->where('status', 'open')->first();
                @endphp

                @if ($activeShift)
                    <a href="{{ route('pos.index') }}"
                        class="inline-flex justify-center items-center py-2.5 px-4 text-sm font-semibold text-white bg-red-600 rounded-xl shadow-sm transition-all hover:bg-red-700 active:scale-95">
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Tutup Shift Aktif (Ke POS)
                    </a>
                @else
                    <a href="{{ route('pos.index') }}"
                        class="inline-flex justify-center items-center py-2.5 px-4 text-sm font-semibold text-white bg-blue-600 rounded-xl shadow-sm hover:bg-blue-700 transition-all active:scale-95">
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Mulai Shift Baru (Buka Kasir)
                    </a>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 gap-5 mb-8 sm:grid-cols-2 lg:grid-cols-4">
            <div class="relative p-6 overflow-hidden bg-white border border-gray-100 shadow-sm rounded-2xl">
                @if ($activeShift)
                    <div
                        class="absolute top-0 right-0 p-4 text-xs font-bold tracking-wider text-green-700 uppercase bg-green-50 rounded-bl-xl">
                        Aktif
                    </div>
                    <p class="text-xs font-semibold tracking-wider text-gray-400 uppercase">Shift Berjalan</p>
                    <p class="mt-2 text-lg font-bold text-gray-900">{{ $activeShift->user->name ?? 'Kasir' }}</p>
                    <p class="mt-1 text-xs text-gray-500">Mulai:
                        {{ \Carbon\Carbon::parse($activeShift->start_time)->format('H:i') }} WIB</p>
                @else
                    <div
                        class="absolute top-0 right-0 p-4 text-xs font-bold tracking-wider text-gray-500 uppercase bg-gray-50 rounded-bl-xl">
                        Kosong
                    </div>
                    <p class="text-xs font-semibold tracking-wider text-gray-400 uppercase">Shift Berjalan</p>
                    <p class="mt-2 text-lg italic font-bold text-gray-400">Tidak ada aktif</p>
                    <p class="mt-1 text-xs text-gray-400">—</p>
                @endif
            </div>

            <div class="p-6 bg-white border border-gray-100 shadow-sm rounded-2xl">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold tracking-wider text-gray-400 uppercase">Total Kas Awal</p>
                    <div class="p-2 text-blue-600 rounded-lg bg-blue-50">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <p class="mt-2 text-2xl font-bold text-gray-900">Rp
                    {{ number_format($shifts->sum('cash_start'), 0, ',', '.') }}</p>
                <p class="mt-1 text-xs text-gray-400">Akumulasi modal awal riwayat</p>
            </div>

            @php
                $totalCashSales = $shifts->sum(function ($s) {
                    return $s->cash_expected - $s->cash_start;
                });
            @endphp
            <div class="p-6 bg-white border border-gray-100 shadow-sm rounded-2xl">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold tracking-wider text-gray-400 uppercase">Penjualan Tunai</p>
                    <div class="p-2 text-green-600 rounded-lg bg-green-50">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 11l3-3m0 0l3 3m-3-3v8m0-13a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <p class="mt-2 text-2xl font-bold text-green-600">Rp {{ number_format($totalCashSales, 0, ',', '.') }}
                </p>
                <p class="mt-1 text-xs text-gray-400">Total omset bersih laci tunai</p>
            </div>

            @php
                $totalDifference = $shifts->where('status', 'closed')->sum('cash_difference');
            @endphp
            <div class="p-6 bg-white border border-gray-100 shadow-sm rounded-2xl">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold tracking-wider text-gray-400 uppercase">Total Selisih Laci</p>
                    <div
                        class="p-2 {{ $totalDifference < 0 ? 'text-red-600 bg-red-50' : 'text-indigo-600 bg-indigo-50' }} rounded-lg">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 002-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
                <p
                    class="mt-2 text-2xl font-bold {{ $totalDifference < 0 ? 'text-red-600' : ($totalDifference > 0 ? 'text-amber-500' : 'text-gray-900') }}">
                    {{ $totalDifference > 0 ? '+' : '' }}Rp {{ number_format($totalDifference, 0, ',', '.') }}
                </p>
                <p class="mt-1 text-xs text-gray-400">Evaluasi kecocokan fisik vs sistem</p>
            </div>
        </div>

        <div class="bg-white border border-gray-100 shadow-[0_4px_20px_rgba(0,0,0,0.02)] rounded-2xl overflow-hidden">

            <form method="GET" action="{{ url()->current() }}"
                class="flex flex-col items-center justify-between gap-4 p-5 border-b border-gray-100 md:flex-row">
                <div class="relative w-full md:w-80">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama kasir..."
                        class="py-2.5 pr-4 pl-10 w-full text-sm bg-gray-50 border border-gray-200 rounded-xl transition-all focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 placeholder-gray-400">
                </div>

                <div class="flex items-center w-full gap-3 md:w-auto">
                    <select name="status" onchange="this.form.submit()"
                        class="py-2.5 px-4 text-sm bg-gray-50 border border-gray-200 rounded-xl transition-all focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-600 font-medium">
                        <option value="">Semua Status</option>
                        <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Aktif/Berjalan
                        </option>
                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Selesai/Ditutup
                        </option>
                    </select>

                    <input type="date" name="date" value="{{ request('date') }}"
                        onchange="this.form.submit()"
                        class="py-2.5 px-4 text-sm bg-gray-50 border border-gray-200 rounded-xl transition-all focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 text-gray-600 font-medium">

                    @if (request()->filled('search') || request()->filled('status') || request()->filled('date'))
                        <a href="{{ url()->current() }}"
                            class="text-xs font-semibold text-red-500 hover:underline">Reset</a>
                    @endif
                </div>
            </form>

            <div class="w-full overflow-x-auto">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead>
                        <tr
                            class="text-xs font-bold tracking-wider text-gray-400 uppercase border-b border-gray-100 bg-gray-50/70">
                            <th class="px-6 py-4">Nama Kasir</th>
                            <th class="px-6 py-4">Waktu Mulai</th>
                            <th class="px-6 py-4">Waktu Selesai</th>
                            <th class="px-6 py-4 text-right">Kas Awal</th>
                            <th class="px-6 py-4 text-right">Kas Akhir (Sistem/Fisik)</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm font-medium text-gray-600 divide-y divide-gray-50">
                        @forelse($shifts as $shift)
                            <tr class="transition-colors hover:bg-gray-50/50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex items-center justify-center w-8 h-8 text-xs font-bold text-blue-600 uppercase rounded-full bg-blue-50">
                                            {{ substr($shift->user->name ?? 'KS', 0, 2) }}
                                        </div>
                                        <span
                                            class="font-semibold text-gray-900">{{ $shift->user->name ?? 'User Dihapus' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-500">
                                    {{ \Carbon\Carbon::parse($shift->start_time)->format('d M Y, H:i') }}
                                </td>
                                <td class="px-6 py-4 text-gray-500">
                                    {{ $shift->end_time ? \Carbon\Carbon::parse($shift->end_time)->format('d M Y, H:i') : '—' }}
                                </td>
                                <td class="px-6 py-4 font-semibold text-right text-gray-900">
                                    Rp {{ number_format($shift->cash_start, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if ($shift->status === 'open')
                                        <span class="italic text-gray-400">Berjalan...</span>
                                    @else
                                        <div class="font-semibold text-gray-900">
                                            Rp {{ number_format($shift->cash_actual, 0, ',', '.') }}
                                        </div>
                                        <span class="block text-xs font-normal text-gray-400">Sistem: Rp
                                            {{ number_format($shift->cash_expected, 0, ',', '.') }}</span>

                                        @if ($shift->cash_difference < 0)
                                            <span class="block text-xs font-normal text-red-500">(Selisih -Rp
                                                {{ number_format(abs($shift->cash_difference), 0, ',', '.') }})</span>
                                        @elseif($shift->cash_difference > 0)
                                            <span class="block text-xs font-normal text-amber-500">(Surplus +Rp
                                                {{ number_format($shift->cash_difference, 0, ',', '.') }})</span>
                                        @else
                                            <span class="block text-xs font-normal text-green-500">(Cocok)</span>
                                        @endif
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($shift->status === 'open')
                                        <span
                                            class="inline-flex py-1 px-2.5 text-xs font-bold text-green-700 bg-green-50 rounded-md uppercase animate-pulse">Aktif</span>
                                    @else
                                        <span
                                            class="inline-flex py-1 px-2.5 text-xs font-bold text-gray-500 bg-gray-100 rounded-md uppercase">Ditutup</span>
                                    @endif
                                </td>
                                <td class="max-w-xs px-6 py-4 text-xs italic text-gray-400 truncate"
                                    title="{{ $shift->notes }}">
                                    {{ $shift->notes ?? '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 italic text-center text-gray-400">
                                    Tidak ada data riwayat shift ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($shifts->hasPages())
                <div class="p-5 bg-white border-t border-gray-100">
                    {{ $shifts->appends(request()->query())->links() }}
                </div>
            @else
                <div class="p-5 text-xs font-semibold text-gray-400 border-t border-gray-100">
                    Menampilkan {{ $shifts->count() }} total entri shift milik toko Anda.
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
