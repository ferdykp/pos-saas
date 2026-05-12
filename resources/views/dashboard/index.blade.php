<x-app-layout>
    <div class="py-6">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex flex-col justify-between gap-4 mb-10 md:flex-row md:items-end">
                <div>
                    <h1 class="text-3xl font-black tracking-tight text-gray-900">Dashboard</h1>
                    <p class="mt-1 italic font-medium text-gray-500">Data Bisnis: {{ auth()->user()->tenant->name }}</p>
                </div>

                <div class="text-right" x-data="{
                    time: '',
                    date: '',
                    updateClock() {
                        const now = new Date();
                        this.date = now.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
                        this.time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false });
                    }
                }" x-init="updateClock();
                setInterval(() => updateClock(), 1000)">
                    <div
                        class="px-4 py-1 mb-1 text-[10px] font-black tracking-widest text-blue-600 uppercase rounded-lg bg-blue-50 inline-block">
                        Live Report</div>
                    <div class="flex items-center justify-end gap-3">
                        <div class="hidden sm:block">
                            <p class="text-sm font-bold text-gray-900" x-text="date"></p>
                            <p class="text-[10px] font-medium text-gray-400">Waktu Server (WIB)</p>
                        </div>
                        <div class="px-4 py-2 text-2xl font-black tracking-tighter text-white bg-gray-900 shadow-xl rounded-2xl min-w-[120px] text-center"
                            x-text="time">00:00:00</div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 mb-10 md:grid-cols-2 lg:grid-cols-4">
                <div
                    class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm group hover:shadow-xl transition-all">
                    <div
                        class="flex items-center justify-center w-12 h-12 mb-4 text-blue-600 transition-colors bg-blue-50 rounded-2xl group-hover:bg-blue-600 group-hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h2 class="text-xs font-black tracking-widest text-gray-400 uppercase">Total Pendapatan</h2>
                    <p class="mt-1 text-2xl font-black text-gray-900">Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                    </p>
                </div>

                <div
                    class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm group hover:shadow-xl transition-all">
                    <div
                        class="flex items-center justify-center w-12 h-12 mb-4 text-red-600 transition-colors bg-red-50 rounded-2xl group-hover:bg-red-600 group-hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h2 class="text-xs font-black tracking-widest text-gray-400 uppercase">Total Piutang (Bon)</h2>
                    <p class="mt-1 text-2xl font-black text-red-600">Rp {{ number_format($totalDebt, 0, ',', '.') }}</p>
                </div>

                <div
                    class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm group hover:shadow-xl transition-all">
                    <div
                        class="flex items-center justify-center w-12 h-12 mb-4 text-orange-600 transition-colors bg-orange-50 rounded-2xl group-hover:bg-orange-600 group-hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <h2 class="text-xs font-black tracking-widest text-gray-400 uppercase">Transaksi</h2>
                    <p class="mt-1 text-2xl font-black text-gray-900">{{ $totalOrders }}</p>
                </div>

                <div
                    class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm group hover:shadow-xl transition-all">
                    <div
                        class="flex items-center justify-center w-12 h-12 mb-4 text-green-600 transition-colors bg-green-50 rounded-2xl group-hover:bg-green-600 group-hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <h2 class="text-xs font-black tracking-widest text-gray-400 uppercase">Stok Produk</h2>
                    <p class="mt-1 text-2xl font-black text-gray-900">{{ $totalProducts }}</p>
                </div>
            </div>

            <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm p-8 transition-all hover:shadow-lg">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xl font-bold text-gray-900">Transaksi Terbaru</h3>
                    <a href="{{ route('orders.index') }}"
                        class="px-4 py-2 text-sm font-bold text-blue-600 transition hover:bg-blue-50 rounded-xl">Lihat
                        Semua</a>
                </div>

                <div class="space-y-4">
                    @forelse($recentOrders as $order)
                        <div
                            class="flex items-center justify-between p-4 transition-all border border-transparent bg-gray-50 rounded-2xl hover:border-blue-100 hover:bg-white group">
                            <div class="flex items-center space-x-4">
                                <div
                                    class="flex items-center justify-center w-12 h-12 bg-white shadow-sm rounded-xl font-black text-[10px] text-blue-600 uppercase">
                                    {{ substr($order->payment_status, 0, 4) }}
                                </div>
                                <div>
                                    <p class="text-sm font-black text-gray-900 uppercase">{{ $order->invoice_number }}
                                    </p>
                                    <p class="text-[10px] font-medium text-gray-400">
                                        {{ $order->customer->name ?? 'Guest' }} •
                                        {{ $order->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-black text-gray-900">Rp
                                    {{ number_format($order->grand_total, 0, ',', '.') }}</p>
                                <span
                                    class="text-[9px] px-2 py-0.5 {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-600' : 'bg-amber-100 text-amber-600' }} font-black rounded-md uppercase">
                                    {{ $order->payment_status }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="py-20 text-center">
                            <p class="italic font-medium text-gray-400">Belum ada transaksi hari ini.</p>
                            <a href="{{ route('pos.index') }}"
                                class="inline-block mt-4 text-sm font-bold text-blue-600 hover:underline">+ Buka Kasir
                                (POS)
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
