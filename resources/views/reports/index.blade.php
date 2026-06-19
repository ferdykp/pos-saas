<x-app-layout>
    <div class="min-h-screen p-6 mx-auto max-w-7xl bg-gray-50">

        <div class="flex flex-col items-start justify-between gap-4 mb-8 md:flex-row md:items-center">
            <div>
                <h1 class="text-3xl font-black tracking-tight text-gray-900">Laporan Operasional</h1>
                <p class="text-sm font-medium text-gray-500">Pantau seluruh performa penjualan dan laci kasir Anda di
                    sini.</p>
            </div>

            <form method="GET" action="{{ route('reports.index') }}"
                class="flex flex-wrap items-center gap-3 p-3 bg-white border border-gray-100 shadow-sm rounded-2xl">
                <div>
                    <input type="date" name="start_date" value="{{ $startDate }}"
                        class="px-4 py-2 text-sm font-bold border-none bg-gray-50 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
                <span class="text-xs font-bold text-gray-400 uppercase">s/d</span>
                <div>
                    <input type="date" name="end_date" value="{{ $endDate }}"
                        class="px-4 py-2 text-sm font-bold border-none bg-gray-50 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
                <button type="submit"
                    class="px-5 py-2 text-sm font-black text-white transition-colors bg-blue-600 rounded-xl hover:bg-blue-700">
                    <i class="mr-1 fa-solid fa-filter"></i> Filter
                </button>
                <a href="{{ route('reports.exports-list') }}"
                    class="px-4 py-2 text-xs font-bold text-blue-600 transition-colors bg-blue-50 hover:bg-blue-100 rounded-xl">
                    <i class="mr-1 fa-solid fa-folder-open"></i> Lihat Laci Unduhan
                </a>

                <a href="{{ route('reports.export-excel', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                    class="px-5 py-2 text-sm font-black text-white transition-colors bg-emerald-600 rounded-xl hover:bg-emerald-700">
                    <i class="mr-1 fa-solid fa-file-excel"></i> Export Excel
                </a>
            </form>
        </div>

        <div class="grid grid-cols-1 gap-5 mb-8 sm:grid-cols-2 lg:grid-cols-4">
            <div class="p-6 bg-white border border-gray-100 shadow-sm rounded-[2rem]">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-black tracking-wider text-gray-400 uppercase">Pendapatan Toko</span>
                    <div class="flex items-center justify-center w-10 h-10 text-emerald-600 bg-emerald-50 rounded-xl">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                </div>
                {{-- Menghitung pendapatan bersih setelah dikurangi komisi QRIS 1.5% --}}
                @php
                    $totalQrisOmzet = 0;
                    foreach ($paymentMethods as $pm) {
                        if ($pm->payment_method !== 'cash') {
                            $totalQrisOmzet += $pm->total_amount;
                        }
                    }
                    $totalPlatformFee = ($totalQrisOmzet * 1.5) / 100;
                    $storeNetIncome = ($salesSummary->total_net ?? 0) - $totalPlatformFee;
                @endphp
                <h3 class="text-2xl font-black text-emerald-600">Rp {{ number_format($storeNetIncome, 0, ',', '.') }}
                </h3>
                <p class="mt-1 text-xs font-medium text-gray-400">Bersih (Sudah dipotong komisi platform)</p>
            </div>

            <div class="p-6 bg-white border border-gray-100 shadow-sm rounded-[2rem]">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-black tracking-wider text-gray-400 uppercase">Total Transaksi</span>
                    <div class="flex items-center justify-center w-10 h-10 text-blue-600 bg-blue-50 rounded-xl">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                </div>
                <h3 class="text-2xl font-black text-gray-900">{{ $salesSummary->total_transactions ?? 0 }} Nota</h3>
                <p class="mt-1 text-xs font-medium text-gray-400">Transaksi berstatus lunas</p>
            </div>

            <div class="p-6 bg-white border border-gray-100 shadow-sm rounded-[2rem]">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-black tracking-wider text-gray-400 uppercase">Komisi Platform</span>
                    <div class="flex items-center justify-center w-10 h-10 text-amber-600 bg-amber-50 rounded-xl">
                        <i class="fa-solid fa-percent"></i>
                    </div>
                </div>
                <h3 class="text-2xl font-black text-amber-600">Rp {{ number_format($totalPlatformFee, 0, ',', '.') }}
                </h3>
                <p class="mt-1 text-xs font-medium text-gray-400">Potongan 1.5% khusus transaksi QRIS</p>
            </div>

            <div class="p-6 bg-white border border-gray-100 shadow-sm rounded-[2rem]">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-black tracking-wider text-gray-400 uppercase">Total Diskon</span>
                    <div class="flex items-center justify-center w-10 h-10 text-rose-600 bg-rose-50 rounded-xl">
                        <i class="fa-solid fa-tags"></i>
                    </div>
                </div>
                <h3 class="text-2xl font-black text-rose-600">-Rp
                    {{ number_format($salesSummary->total_discount ?? 0, 0, ',', '.') }}</h3>
                <p class="mt-1 text-xs font-medium text-gray-400">Jumlah subsidi promo produk</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-8 mb-8 lg:grid-cols-3">
            <div class="p-6 bg-white border border-gray-100 shadow-sm lg:col-span-1 rounded-[2.5rem]">
                <h3 class="mb-4 text-lg font-black text-gray-900">Aliran Dana Masuk</h3>
                <div class="space-y-3">
                    @forelse($paymentMethods as $pm)
                        @if ($pm->payment_method == 'cash')
                            <div class="p-4 border border-gray-100 bg-gray-50 rounded-2xl">
                                <div class="flex items-center justify-between mb-1">
                                    <div class="flex items-center gap-3">
                                        <div class="w-2 h-6 bg-blue-500 rounded-full"></div>
                                        <span class="text-sm font-black text-gray-800 uppercase">Tunai / Cash</span>
                                    </div>
                                    <span class="text-sm font-black text-gray-900">Rp
                                        {{ number_format($pm->total_amount, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between text-[11px] text-gray-400 font-medium px-5">
                                    <span>Potongan Platform:</span>
                                    <span>Rp 0</span>
                                </div>
                            </div>
                        @else
                            @php
                                $feeThisMethod = ($pm->total_amount * 1.5) / 100;
                                $netThisMethod = $pm->total_amount - $feeThisMethod;
                            @endphp
                            <div class="p-4 border bg-purple-50/40 rounded-2xl border-purple-100/50">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-3">
                                        <div class="w-2 h-6 bg-purple-500 rounded-full"></div>
                                        <span class="text-sm font-black text-purple-900 uppercase">QRIS /
                                            Midtrans</span>
                                    </div>
                                    <span class="text-sm font-black text-purple-900">Rp
                                        {{ number_format($pm->total_amount, 0, ',', '.') }}</span>
                                </div>
                                <div
                                    class="space-y-0.5 text-[11px] font-medium text-gray-500 px-5 border-t border-purple-100/30 pt-1.5">
                                    <div class="flex justify-between">
                                        <span>Potongan Admin (1.5%):</span>
                                        <span class="text-rose-600">-Rp
                                            {{ number_format($feeThisMethod, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between font-bold text-gray-700">
                                        <span>Masuk Dompet Finance:</span>
                                        <span class="text-emerald-600">Rp
                                            {{ number_format($netThisMethod, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <p class="py-10 text-sm italic text-center text-gray-400">Belum ada aliran dana masuk</p>
                    @endforelse
                </div>
            </div>

            <div class="p-6 bg-white border border-gray-100 shadow-sm lg:col-span-2 rounded-[2.5rem]">
                <h3 class="mb-4 text-lg font-black text-gray-900">5 Produk Terlaris (Top Selling)</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-xs font-black text-gray-400 uppercase border-b border-gray-100">
                                <th class="pb-3">Nama Produk</th>
                                <th class="pb-3 text-center">Jumlah Terjual</th>
                                <th class="pb-3 text-right">Subtotal Omzet</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($topProducts as $idx => $tp)
                                <tr class="text-sm">
                                    <td class="flex items-center gap-2 py-4 font-bold text-gray-900">
                                        <span
                                            class="flex justify-center items-center w-5 h-5 text-[10px] text-blue-600 bg-blue-50 rounded-full font-black">{{ $idx + 1 }}</span>
                                        {{ $tp->product_name }}
                                    </td>
                                    <td class="py-4 font-black text-center text-gray-600">{{ $tp->total_qty }}x</td>
                                    <td class="py-4 font-black text-right text-blue-600">Rp
                                        {{ number_format($tp->total_sales, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-10 text-sm italic text-center text-gray-400">Belum ada
                                        data penjualan produk</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="p-6 bg-white border border-gray-100 shadow-sm rounded-[2.5rem]">
            <h3 class="mb-2 text-lg font-black text-gray-900">Audit Shift & Laci Kasir</h3>
            <p class="mb-4 text-xs font-medium text-gray-400">Daftar rekonsiliasi kas kasir untuk mencocokkan saldo
                fisik laci dengan hitungan sistem.</p>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-xs font-black text-gray-400 uppercase border-b border-gray-100">
                            <th class="pb-3">Nama Kasir</th>
                            <th class="pb-3">Waktu Buka / Tutup</th>
                            <th class="pb-3 text-right">Modal Awal</th>
                            <th class="pb-3 text-right">Fisik Laci</th>
                            <th class="pb-3 text-center">Status</th>
                            <th class="pb-3">Catatan Audit</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-50">
                        @forelse($shifts as $s)
                            <tr>
                                <td class="py-4 font-bold text-gray-900">{{ $s->user->name ?? 'Tidak Diketahui' }}
                                </td>
                                <td class="py-4 font-medium leading-tight text-gray-500">
                                    <span class="block text-xs text-emerald-600">Buka: {{ $s->start_time }}</span>
                                    <span class="block text-xs text-rose-600">Tutup:
                                        {{ $s->end_time ?? 'Sedang Aktif' }}</span>
                                </td>
                                <td class="py-4 font-bold text-right text-gray-700">Rp
                                    {{ number_format($s->cash_start, 0, ',', '.') }}</td>
                                <td class="py-4 font-bold text-right text-gray-900">
                                    {{ $s->cash_actual ? 'Rp ' . number_format($s->cash_actual, 0, ',', '.') : '-' }}
                                </td>
                                <td class="py-4 text-center">
                                    <span
                                        class="px-3 py-1 text-[10px] font-black uppercase rounded-full {{ $s->status === 'open' ? 'bg-blue-50 text-blue-600 animate-pulse' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $s->status }}
                                    </span>
                                </td>
                                <td class="max-w-xs py-4 text-xs font-medium text-gray-400 truncate"
                                    title="{{ $s->notes }}">
                                    {{ $s->notes ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-10 text-sm italic text-center text-gray-400">Tidak ada
                                    aktivitas shift kasir pada tanggal ini</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 p-6 lg:grid-cols-3">
            <div class="p-6 bg-white border border-gray-100 shadow-sm lg:col-span-2 rounded-[2.5rem]">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-black text-gray-900">Tren Grafik Penjualan</h3>
                        <p class="text-xs font-medium text-gray-400">Melihat pergerakan omzet pada periode terpilih.
                        </p>
                    </div>
                </div>
                <div class="w-full h-64">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>

            <div class="p-6 bg-white border border-gray-100 shadow-sm lg:col-span-1 rounded-[2.5rem]">
                <div>
                    <h3 class="text-lg font-black text-gray-900">Proporsi Pembayaran</h3>
                    <p class="mb-4 text-xs font-medium text-gray-400">Perbandingan kanal transaksi masuk.</p>
                </div>
                <div class="relative flex items-center justify-center w-full h-64">
                    <canvas id="paymentChart"></canvas>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // --- 1. GRAFIK TREN PENJUALAN ---
            const ctxTrend = document.getElementById('trendChart').getContext('2d');
            const trendLabels = {!! json_encode($chartLabels) !!};
            const trendData = {!! json_encode($chartValues) !!};

            new Chart(ctxTrend, {
                type: 'bar',
                data: {
                    labels: trendLabels.length > 0 ? trendLabels : ['Tidak Ada Data'],
                    datasets: [{
                        label: 'Omzet Penjualan (Rp)',
                        data: trendData.length > 0 ? trendData : [0],
                        backgroundColor: 'rgba(37, 99, 235, 0.1)',
                        borderColor: '#2563eb',
                        borderWidth: 3,
                        borderRadius: 8,
                        barPercentage: 0.5,
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.03)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                },
                                font: {
                                    weight: 'bold',
                                    size: 10
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    weight: 'bold',
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });

            // --- 2. GRAFIK PROPORSI PEMBAYARAN ---
            const ctxPayment = document.getElementById('paymentChart').getContext('2d');
            let pmLabels = [];
            let pmValues = [];
            @foreach ($paymentMethods as $pm)
                pmLabels.push("{{ $pm->payment_method == 'cash' ? 'Tunai' : 'QRIS/TF' }}");
                pmValues.push({{ $pm->total_amount }});
            @endforeach

            new Chart(ctxPayment, {
                type: 'doughnut',
                data: {
                    labels: pmLabels.length > 0 ? pmLabels : ['Belum Ada Transaksi'],
                    datasets: [{
                        data: pmValues.length > 0 ? pmValues : [1],
                        backgroundColor: pmValues.length > 0 ? ['#2563eb', '#a855f7'] : ['#e2e8f0'],
                        borderWidth: 4,
                        borderColor: '#ffffff',
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                padding: 20,
                                font: {
                                    weight: 'bold',
                                    size: 12
                                }
                            }
                        }
                    },
                    cutout: '70%'
                }
            });
        });
    </script>
</x-app-layout>
