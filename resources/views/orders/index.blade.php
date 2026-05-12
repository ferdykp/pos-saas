<x-app-layout>
    <div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="flex flex-col justify-between gap-4 mb-8 md:flex-row md:items-center">
            <div>
                <h1 class="text-3xl font-black tracking-tight text-gray-900">Riwayat Transaksi</h1>
                <p class="font-medium text-gray-500">Pantau semua penjualan dan status pembayaran kasir Anda.</p>
            </div>
            <a href="{{ route('pos.index') }}"
                class="px-6 py-3 font-bold text-center text-white transition bg-blue-600 shadow-xl rounded-2xl shadow-blue-100 hover:bg-blue-700 active:scale-95">
                + Transaksi Baru (POS)
            </a>
        </div>

        <div class="overflow-hidden bg-white border border-gray-100 shadow-sm rounded-[2.5rem]">
            <table class="w-full text-left">
                <thead class="border-b border-gray-100 bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-black tracking-widest text-gray-400 uppercase">Invoice</th>
                        <th class="px-6 py-4 text-xs font-black tracking-widest text-gray-400 uppercase">Pelanggan</th>
                        <th class="px-6 py-4 text-xs font-black tracking-widest text-center text-gray-400 uppercase">
                            Status</th>
                        <th class="px-6 py-4 text-xs font-black tracking-widest text-right text-gray-400 uppercase">
                            Total</th>
                        <th class="px-6 py-4 text-xs font-black tracking-widest text-right text-gray-400 uppercase">Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($orders as $o)
                        <tr class="transition-colors hover:bg-gray-50/50">
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-gray-900">{{ $o->invoice_number }}</p>
                                <p class="text-[10px] font-medium text-gray-400">
                                    {{ $o->created_at->format('d M Y, H:i') }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-gray-900">{{ $o->customer->name ?? 'Guest' }}</p>
                                <p class="text-[10px] font-medium text-gray-400">Oleh: {{ $o->user->name }}</p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if ($o->payment_status === 'paid')
                                    <span
                                        class="px-3 py-1 text-[10px] font-black text-green-600 bg-green-100 rounded-lg uppercase">Lunas</span>
                                @else
                                    <span
                                        class="px-3 py-1 text-[10px] font-black text-amber-600 bg-amber-100 rounded-lg uppercase">Bon
                                        (Hutang)
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-black text-right text-gray-900">
                                Rp {{ number_format($o->grand_total, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('orders.show', $o->id) }}"
                                        class="text-[10px] font-black text-blue-600 bg-blue-50 px-3 py-1 rounded-md uppercase hover:bg-blue-100 transition">Detail
                                        & Struk</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 italic text-center text-gray-400">Belum ada transaksi
                                hari ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $orders->links() }}</div>
    </div>
</x-app-layout>
