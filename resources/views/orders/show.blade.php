<x-app-layout>
    <div class="max-w-xl px-4 py-12 mx-auto">
        <div class="flex justify-between mb-6">
            <a href="{{ route('orders.index') }}"
                class="flex items-center text-sm font-bold text-gray-500 transition hover:text-gray-900">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali
            </a>
            <button onclick="window.print()"
                class="px-4 py-2 text-xs font-black text-white transition bg-blue-600 shadow-lg rounded-xl hover:bg-blue-700">
                CETAK STRUK
            </button>
        </div>

        <div id="receipt"
            class="bg-white p-8 shadow-2xl rounded-[2.5rem] border border-gray-100 relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 flex justify-between px-2 -mt-2">
                @for ($i = 0; $i < 20; $i++)
                    <div class="w-4 h-4 rounded-full bg-gray-50"></div>
                @endfor
            </div>

            <div class="mt-4 text-center">
                <h2 class="text-2xl font-black tracking-tighter text-gray-900 uppercase">
                    {{ auth()->user()->tenant->name ?? 'Toko Saya' }}</h2>
                <p class="text-[10px] font-bold text-gray-400 leading-tight">
                    {{ auth()->user()->tenant->address ?? 'Alamat Belum Diatur' }}<br>
                    WA: {{ auth()->user()->tenant->phone ?? '-' }}
                </p>
            </div>

            <div class="my-8 border-b border-gray-200 border-dashed"></div>

            <div class="flex justify-between mb-6">
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">No. Invoice</p>
                    <p class="text-sm font-bold text-gray-900">{{ $order->invoice_number }}</p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Tanggal</p>
                    <p class="text-sm font-bold text-gray-900">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            <div class="p-4 mb-8 bg-gray-50 rounded-2xl">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Pelanggan</p>
                <p class="text-sm font-bold text-gray-900">{{ $order->customer->name ?? 'Pelanggan Umum' }}</p>
                <p class="text-[10px] font-medium text-gray-400">{{ $order->customer->phone ?? '-' }}</p>
            </div>

            <div class="space-y-4">
                @foreach ($order->items as $item)
                    <div class="flex items-start justify-between">
                        <div class="flex-1 pr-4">
                            <p class="text-sm font-bold text-gray-900">{{ $item->product_name }}</p>
                            <p class="text-xs font-medium text-gray-400">{{ $item->quantity }}x Rp
                                {{ number_format($item->price, 0, ',', '.') }}</p>
                        </div>
                        <p class="text-sm font-black text-gray-900">Rp
                            {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                    </div>
                @endforeach
            </div>

            <div class="my-8 border-b border-gray-200 border-dashed"></div>

            <div class="space-y-2">
                <div class="flex justify-between text-sm font-bold text-gray-500">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between pt-2 text-xl font-black text-gray-900">
                    <span>TOTAL</span>
                    <span class="text-blue-600">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="pt-8 mt-8 text-center border-t border-gray-50">
                <div
                    class="inline-block px-4 py-1 rounded-full {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-600' : 'bg-amber-100 text-amber-600' }} text-[10px] font-black uppercase mb-4">
                    {{ $order->payment_status === 'paid' ? 'LUNAS' : 'BON (BELUM LUNAS)' }}
                </div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-relaxed">Terima kasih
                    atas kunjungan Anda!<br>Barang yang sudah dibeli tidak dapat ditukar.</p>
            </div>
        </div>

        <div class="mt-6">
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->customer->phone ?? '') }}?text=Halo%20{{ $order->customer->name ?? '' }},%20ini%20adalah%20struk%20transaksi%20Anda%20dengan%20nomor%20{{ $order->invoice_number }}.%20Total:%20Rp%20{{ number_format($order->grand_total, 0, ',', '.') }}"
                target="_blank"
                class="flex items-center justify-center w-full py-4 font-black text-white transition bg-green-500 shadow-xl rounded-2xl hover:bg-green-600">
                <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M12.031 6.172c-2.315 0-4.391 1.253-5.467 3.149a4.89 4.89 0 0 0-.583 2.31c0 .884.225 1.745.654 2.509l-.693 2.535 2.597-.68a4.887 4.887 0 0 0 2.492.678h.001c2.315 0 4.391-1.253 5.467-3.149a4.891 4.891 0 0 0 .583-2.31c0-2.698-2.193-4.891-4.891-4.891zm3.181 6.843c-.233.518-.787.893-1.391.893-.243 0-.484-.047-.714-.143a4.01 4.01 0 0 1-1.442-1.047 4.012 4.012 0 0 1-.84-1.572c-.07-.311-.035-.636.096-.921.233-.518.787-.893 1.391-.893.111 0 .221.01.328.031l.161.034c.162.035.298.146.36.295l.407 1.012c.061.15.045.32-.043.456l-.234.363a.406.406 0 0 0 .041.503c.277.306.63.535 1.02.662.15.049.314.032.45-.047l.363-.234a.407.407 0 0 1 .503.041c.21.19.467.332.744.417.151.045.281.144.346.284l.436 1.082a.41.41 0 0 1-.027.38z" />
                </svg>
                KIRIM VIA WHATSAPP
            </a>
        </div>
    </div>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            #receipt,
            #receipt * {
                visibility: visible;
            }

            #receipt {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                box-shadow: none;
                border: none;
            }
        }
    </style>
</x-app-layout>
