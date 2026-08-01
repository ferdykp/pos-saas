<x-app-layout>
    @section('title', 'Dashboard')

    <div class="px-4 py-6 mx-auto md:px-6 lg:px-8 max-w-desktop">

        <!-- ==================== HEADER & QUICK ACTIONS ==================== -->
        <div class="flex flex-col justify-between gap-4 mb-8 xl:flex-row xl:items-center">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span
                        class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-primary-100 text-primary-700">
                        <span class="w-1.5 h-1.5 rounded-full bg-primary-600 animate-pulse"></span>
                        Status Toko: Buka
                    </span>
                    <span class="text-xs text-ink-400">•
                        {{ auth()->user()->tenant->business_type ?? 'Retail & UMKM' }}</span>
                </div>
                <h1 class="font-heading font-bold text-2xl md:text-[28px] text-ink-900 leading-tight">
                    Selamat Datang, {{ auth()->user()->name }}! 👋
                </h1>
                <p class="font-body text-xs md:text-sm text-ink-700 mt-0.5">
                    Ringkasan performa bisnis <span
                        class="font-semibold text-primary-600">{{ auth()->user()->tenant->name ?? 'Usaha Anda' }}</span>
                    hari ini.
                </p>
            </div>

            <!-- Quick Action Bar & Live Time -->
            <div class="flex flex-wrap items-center gap-3 sm:flex-nowrap">
                <!-- Live Clock Widget -->
                <div class="flex items-center gap-2.5 bg-surface-0 border border-border-200 px-3 py-2 rounded-md shadow-sm"
                    x-data="{
                        time: '',
                        date: '',
                        updateClock() {
                            const now = new Date();
                            this.date = now.toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric', month: 'short' });
                            this.time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false });
                        }
                    }" x-init="updateClock();
                    setInterval(() => updateClock(), 1000)">
                    <i class="text-sm fa-solid fa-clock text-primary-600"></i>
                    <div class="text-left">
                        <div class="font-mono text-xs font-semibold leading-none text-ink-900" x-text="time">00:00
                        </div>
                        <div class="font-body text-[10px] text-ink-400 mt-0.5 leading-none" x-text="date">-</div>
                    </div>
                </div>

                <!-- Primary Action: Open POS -->
                <a href="{{ route('pos.index') }}"
                    class="inline-flex items-center h-10 gap-2 px-4 text-xs font-semibold text-white transition-colors rounded-md shadow-sm bg-primary-600 hover:bg-primary-700 font-body">
                    <i class="fa-solid fa-cash-register"></i>
                    <span>Terminal Kasir</span>
                </a>

                <!-- Secondary Action: Add Product -->
                <a href="{{ route('products.create') }}"
                    class="inline-flex items-center gap-2 h-10 px-3.5 bg-surface-0 hover:bg-surface-100 text-ink-900 border border-border-200 font-body font-semibold text-xs rounded-md transition-colors">
                    <i class="fa-solid fa-plus text-primary-600"></i>
                    <span class="hidden sm:inline">Tambah Produk</span>
                </a>
            </div>
        </div>

        <!-- ==================== 1. AI BUSINESS INSIGHT CARD ==================== -->
        <div
            class="relative p-5 mb-8 overflow-hidden border rounded-lg shadow-sm md:p-6 bg-gradient-to-br from-primary-50/80 via-surface-0 to-accent-100/30 border-primary-100">
            <div class="flex flex-col justify-between gap-5 lg:flex-row lg:items-center">
                <div class="flex items-start gap-4">
                    <div
                        class="w-12 h-12 rounded-lg bg-primary-600 text-white flex items-center justify-center shrink-0 shadow-sm mt-0.5">
                        <i class="text-xl fa-solid fa-wand-magic-sparkles"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span
                                class="font-heading font-bold text-[11px] uppercase tracking-wider text-primary-700 bg-primary-100 px-2.5 py-0.5 rounded-full">
                                GrowPOS AI Insight
                            </span>
                            <span class="text-[11px] text-ink-400">• Asisten Bisnis Anda</span>
                        </div>
                        <h3 class="text-base font-bold font-heading text-ink-900">
                            "Omzet Anda stabil minggu ini, tapi ada 3 barang yang stoknya menipis!"
                        </h3>
                        <p class="max-w-3xl mt-1 text-xs leading-relaxed font-body md:text-sm text-ink-700">
                            Penjualan produk kategori terlaris meningkat 15% dibanding hari kemarin. Pertimbangkan untuk
                            restock barang sebelum jam sibuk nanti sore.
                        </p>

                        <!-- Quick Chat Suggestion Chips -->
                        <div class="flex flex-wrap items-center gap-2 mt-3.5">
                            <span class="text-[11px] font-semibold text-ink-400">Tanya AI cepat:</span>
                            <a href="{{ route('reports.ai', ['prompt' => 'Kenapa omzet turun?']) }}"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-surface-0 border border-primary-100 hover:border-primary-600 text-primary-700 font-body text-xs rounded-full shadow-sm transition-all hover:scale-[1.02]">
                                <i class="fa-regular fa-lightbulb text-accent-500"></i>
                                <span>Rekomendasi Restock</span>
                            </a>
                            <a href="{{ route('reports.ai', ['prompt' => 'Produk apa yang paling laris?']) }}"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-surface-0 border border-primary-100 hover:border-primary-600 text-primary-700 font-body text-xs rounded-full shadow-sm transition-all hover:scale-[1.02]">
                                <i class="fa-solid fa-chart-line text-semantic-success"></i>
                                <span>Produk Terlaris</span>
                            </a>
                        </div>
                    </div>
                </div>

                <a href="{{ route('reports.ai') }}"
                    class="inline-flex items-center self-start justify-center gap-2 px-5 text-xs font-semibold text-white transition-colors rounded-md shadow-sm h-11 bg-accent-500 hover:bg-accent-700 font-body shrink-0 lg:self-center">
                    <i class="fa-solid fa-comments"></i>
                    <span>Buka Chat AI</span>
                </a>
            </div>
        </div>

        <!-- ==================== STATISTIC CARDS ==================== -->
        <div class="grid grid-cols-1 gap-4 mb-8 sm:grid-cols-2 lg:grid-cols-4 md:gap-6">

            <!-- Card 1: Total Pendapatan -->
            <div
                class="p-5 transition-shadow border rounded-lg shadow-sm bg-surface-0 border-border-200 hover:shadow-md">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold tracking-wider uppercase font-body text-ink-700">Total
                        Pendapatan</span>
                    <div class="flex items-center justify-center rounded-md w-9 h-9 bg-primary-50 text-primary-600">
                        <i class="text-base fa-solid fa-sack-dollar"></i>
                    </div>
                </div>
                <div class="font-mono text-xl font-semibold md:text-2xl text-ink-900">
                    Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}
                </div>
                <p class="font-body text-[11px] text-semantic-success flex items-center gap-1 mt-2">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>Omzet bersih transaksi lunas</span>
                </p>
            </div>

            <!-- Card 2: Piutang / Bon -->
            <div
                class="p-5 transition-shadow border rounded-lg shadow-sm bg-surface-0 border-border-200 hover:shadow-md">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold tracking-wider uppercase font-body text-ink-700">Total Piutang
                        (Bon)</span>
                    <div class="flex items-center justify-center rounded-md w-9 h-9 bg-red-50 text-semantic-danger">
                        <i class="text-base fa-solid fa-hand-holding-dollar"></i>
                    </div>
                </div>
                <div class="font-mono text-xl font-semibold md:text-2xl text-semantic-danger">
                    Rp {{ number_format($totalDebt ?? 0, 0, ',', '.') }}
                </div>
                <p class="font-body text-[11px] text-ink-400 mt-2">
                    Belum dibayar oleh pelanggan
                </p>
            </div>

            <!-- Card 3: Jumlah Transaksi -->
            <div
                class="p-5 transition-shadow border rounded-lg shadow-sm bg-surface-0 border-border-200 hover:shadow-md">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold tracking-wider uppercase font-body text-ink-700">Total
                        Transaksi</span>
                    <div class="flex items-center justify-center rounded-md w-9 h-9 bg-accent-100 text-accent-700">
                        <i class="text-base fa-solid fa-cart-shopping"></i>
                    </div>
                </div>
                <div class="font-mono text-xl font-semibold md:text-2xl text-ink-900">
                    {{ number_format($totalOrders ?? 0, 0, ',', '.') }}
                </div>
                <p class="font-body text-[11px] text-ink-400 mt-2">
                    Struk berhasil dicetak
                </p>
            </div>

            <!-- Card 4: Stok Produk -->
            <div
                class="p-5 transition-shadow border rounded-lg shadow-sm bg-surface-0 border-border-200 hover:shadow-md">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold tracking-wider uppercase font-body text-ink-700">Total
                        Produk</span>
                    <div class="flex items-center justify-center rounded-md w-9 h-9 bg-primary-50 text-primary-500">
                        <i class="text-base fa-solid fa-boxes-stacked"></i>
                    </div>
                </div>
                <div class="font-mono text-xl font-semibold md:text-2xl text-ink-900">
                    {{ number_format($totalProducts ?? 0, 0, ',', '.') }}
                </div>
                <p class="font-body text-[11px] text-ink-400 mt-2">
                    SKU aktif di sistem
                </p>
            </div>
        </div>

        <!-- ==================== CHARTS SECTION ==================== -->
        <div class="grid grid-cols-1 gap-6 mb-8 lg:grid-cols-3">

            <!-- Tren Grafik Penjualan Mingguan/Bulanan -->
            <div class="p-5 border rounded-lg shadow-sm lg:col-span-2 bg-surface-0 md:p-6 border-border-200">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-semibold font-heading text-ink-900">Tren Grafik Penjualan</h3>
                        <p class="text-xs font-body text-ink-700">Pergerakan omzet toko dalam periode berjalan</p>
                    </div>
                    <a href="{{ route('reports.index') }}"
                        class="flex items-center gap-1 text-xs font-semibold font-body text-primary-600 hover:text-primary-700">
                        <span>Laporan Detail</span>
                        <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </a>
                </div>
                <div class="w-full h-64 md:h-72">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>

            <!-- Proporsi Kanal Pembayaran -->
            <div
                class="flex flex-col justify-between p-5 border rounded-lg shadow-sm lg:col-span-1 bg-surface-0 md:p-6 border-border-200">
                <div>
                    <h3 class="text-lg font-semibold font-heading text-ink-900">Metode Pembayaran</h3>
                    <p class="mb-4 text-xs font-body text-ink-700">Perbandingan transaksi Tunai vs QRIS/Transfer</p>
                </div>
                <div class="relative flex items-center justify-center w-full h-56 my-auto">
                    <canvas id="paymentChart"></canvas>
                </div>
            </div>
        </div>

        <!-- ==================== RECENT TRANSACTIONS TABLE ==================== -->
        <div class="p-5 border rounded-lg shadow-sm bg-surface-0 border-border-200 md:p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold font-heading text-ink-900">Transaksi Terbaru</h3>
                    <p class="text-xs font-body text-ink-700">Daftar transaksi kasir yang baru saja diproses</p>
                </div>
                <a href="{{ route('orders.index') }}"
                    class="inline-flex items-center gap-1.5 h-9 px-3.5 bg-surface-100 hover:bg-border-200 text-ink-900 font-body font-semibold text-xs rounded-md transition-colors">
                    <span>Lihat Semua</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            <div class="space-y-3">
                @forelse($recentOrders as $order)
                    <div
                        class="flex flex-col sm:flex-row items-start sm:items-center justify-between p-3.5 bg-surface-100 hover:bg-primary-50/50 border border-border-200 rounded-md transition-colors gap-3">
                        <div class="flex items-center gap-3.5">
                            <div
                                class="flex items-center justify-center w-10 h-10 font-mono text-xs font-semibold border rounded-md bg-surface-0 border-border-200 text-primary-600 shrink-0">
                                <i class="text-base fa-solid fa-receipt"></i>
                            </div>
                            <div>
                                <p class="font-mono text-sm font-semibold text-ink-900">
                                    {{ $order->invoice_number }}
                                </p>
                                <p class="font-body text-xs text-ink-700 mt-0.5">
                                    {{ $order->customer->name ?? 'Pelanggan Umum' }} •
                                    <span>{{ $order->created_at->diffForHumans() }}</span>
                                </p>
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-between w-full pt-2 border-t sm:flex-col sm:items-end sm:w-auto sm:pt-0 sm:border-t-0 border-border-200">
                            <div class="font-mono text-sm font-semibold text-ink-900">
                                Rp {{ number_format($order->grand_total, 0, ',', '.') }}
                            </div>
                            <div class="mt-1">
                                @if ($order->payment_status === 'paid')
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full font-body text-[11px] font-semibold bg-primary-100 text-primary-700">
                                        Lunas
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full font-body text-[11px] font-semibold bg-accent-100 text-accent-700">
                                        Belum Lunas
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <!-- Warm Friendly Empty State -->
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <div
                            class="flex items-center justify-center mb-3 rounded-full w-14 h-14 bg-primary-50 text-primary-600">
                            <i class="text-xl fa-solid fa-store-slash"></i>
                        </div>
                        <h4 class="text-base font-semibold font-heading text-ink-900">Belum ada transaksi hari ini</h4>
                        <p class="max-w-sm mt-1 mb-4 text-xs font-body text-ink-700">
                            Yuk, mulai layani pelanggan pertama Anda hari ini dan catat penjualannya!
                        </p>
                        <a href="{{ route('pos.index') }}"
                            class="inline-flex items-center h-10 gap-2 px-5 text-xs font-semibold text-white transition-colors rounded-md shadow-sm bg-primary-600 hover:bg-primary-700 font-body">
                            <i class="fa-solid fa-cash-register"></i>
                            <span>Buka Terminal Kasir</span>
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Chart.js Integration -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const colorPrimary600 = '#16805F';
            const colorPrimary100 = 'rgba(220, 243, 233, 0.5)';
            const colorAccent500 = '#F0932B';
            const colorInk900 = '#1A2421';
            const colorInk400 = '#8B9994';
            const colorBorder200 = '#E3E9E6';

            // 1. TREN PENJUALAN CHART
            const ctxTrend = document.getElementById('trendChart').getContext('2d');
            const trendLabels = {!! json_encode($chartLabels ?? []) !!};
            const trendData = {!! json_encode($chartValues ?? []) !!};

            new Chart(ctxTrend, {
                type: 'bar',
                data: {
                    labels: trendLabels.length > 0 ? trendLabels : ['Belum Ada Data'],
                    datasets: [{
                        label: 'Omzet Penjualan',
                        data: trendData.length > 0 ? trendData : [0],
                        backgroundColor: colorPrimary100,
                        borderColor: colorPrimary600,
                        borderWidth: 2,
                        borderRadius: 6,
                        barPercentage: 0.5,
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
                                color: colorBorder200
                            },
                            ticks: {
                                font: {
                                    family: 'IBM Plex Mono',
                                    size: 10
                                },
                                color: colorInk400,
                                callback: function(value) {
                                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    family: 'Inter',
                                    size: 11
                                },
                                color: colorInk900
                            }
                        }
                    }
                }
            });

            // 2. METODE PEMBAYARAN CHART
            const ctxPayment = document.getElementById('paymentChart').getContext('2d');
            let pmLabels = [];
            let pmValues = [];

            @if (isset($paymentMethods) && count($paymentMethods) > 0)
                @foreach ($paymentMethods as $pm)
                    pmLabels.push("{{ $pm->payment_method == 'cash' ? 'Tunai' : 'QRIS / Non-Tunai' }}");
                    pmValues.push({{ $pm->total_amount }});
                @endforeach
            @endif

            new Chart(ctxPayment, {
                type: 'doughnut',
                data: {
                    labels: pmLabels.length > 0 ? pmLabels : ['Belum Ada Transaksi'],
                    datasets: [{
                        data: pmValues.length > 0 ? pmValues : [1],
                        backgroundColor: pmValues.length > 0 ? [colorPrimary600, colorAccent500] : [
                            colorBorder200
                        ],
                        borderWidth: 3,
                        borderColor: '#FFFFFF',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 10,
                                padding: 16,
                                font: {
                                    family: 'Inter',
                                    size: 11,
                                    weight: '500'
                                },
                                color: colorInk900
                            }
                        }
                    },
                    cutout: '75%'
                }
            });
        });
    </script>
</x-app-layout>
