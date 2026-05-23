<x-app-layout>
    <div class="max-w-xl px-4 py-12 mx-auto no-print-area">
        <div class="flex justify-between mb-6">
            <a href="{{ route('pos.index') }}"
                class="flex items-center text-sm font-bold text-gray-500 transition hover:text-gray-900">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke POS
            </a>

            <button id="btnPrintReceipt"
                class="px-6 py-2 text-xs font-black text-white transition bg-blue-600 shadow-lg rounded-xl hover:bg-blue-700">
                <i class="mr-2 fa-solid fa-print"></i> CETAK STRUK
            </button>
        </div>
    </div>

    <div id="receipt" class="mx-auto bg-white shadow-2xl print:shadow-none print:m-0 print:w-full">
        <div class="receipt-content p-8 print:p-2 w-full max-w-[400px] mx-auto">

            <div class="mb-6 text-center">
                <h2 class="text-2xl font-black tracking-tighter uppercase print:text-lg">
                    {{ auth()->user()->tenant->name ?? 'Toko Saya' }}
                </h2>
                <p class="text-[10px] font-bold text-gray-500 leading-tight uppercase">
                    {{ auth()->user()->tenant->address ?? 'Alamat Belum Diatur' }}<br>
                    Telp/WA: {{ auth()->user()->tenant->phone ?? '-' }}
                </p>
            </div>

            <div class="my-4 border-b border-black border-dashed opacity-20 print:opacity-100"></div>

            <div class="space-y-1 text-[11px] font-mono uppercase">
                <div class="flex justify-between">
                    <span>Invoice:</span>
                    <span class="font-bold">{{ $order->invoice_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Tanggal:</span>
                    <span>{{ $order->created_at->format('d/m/y H:i') }}</span>
                </div>
                <div class="flex justify-between pt-2 mt-2 text-gray-600 border-t border-black border-dashed">
                    <span>Kasir:</span>
                    <span>{{ auth()->user()->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Pelanggan:</span>
                    <span class="ml-4 truncate">{{ $order->customer->name ?? 'Umum' }}</span>
                </div>
            </div>

            <div class="my-4 border-b border-black border-dashed opacity-20 print:opacity-100"></div>

            <div class="space-y-3 font-mono text-xs">
                @foreach ($order->items as $item)
                    <div>
                        <div class="flex justify-between leading-tight">
                            <span class="uppercase">{{ $item->product_name }}</span>
                            <span class="font-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="text-[10px] text-gray-500 italic">
                            {{ $item->quantity }} x {{ number_format($item->price, 0, ',', '.') }}
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="my-4 border-b border-black border-dashed opacity-20 print:opacity-100"></div>

            <div class="space-y-1 font-mono text-xs">
                <div class="flex justify-between text-gray-600">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>
                @if ($order->discount > 0)
                    <div class="flex justify-between text-gray-600">
                        <span>Diskon</span>
                        <span>-Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                    </div>
                @endif
                @if ($order->tax > 0)
                    <div class="flex justify-between text-gray-600">
                        <span>Pajak</span>
                        <span>+Rp {{ number_format($order->tax, 0, ',', '.') }}</span>
                    </div>
                @endif

                <div
                    class="flex justify-between pt-2 mt-2 text-base font-black uppercase border-t border-black border-double">
                    <span>Total</span>
                    <span>Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                </div>

                <div class="flex justify-between pt-2">
                    <span>Bayar ({{ strtoupper($order->payment_method) }})</span>
                    <span>Rp {{ number_format($order->paid_amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Kembali</span>
                    <span>Rp {{ number_format($order->change_amount, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="mt-8 text-center">
                <div class="inline-block px-4 py-1 border border-black mb-4 font-black text-[10px] uppercase">
                    {{ $order->payment_status === 'paid' ? 'LUNAS' : 'PIUTANG' }}
                </div>
                <p class="text-[10px] font-bold uppercase tracking-tight">
                    Terima Kasih Atas Kunjungan Anda!<br>
                    Barang yang sudah dibeli tidak dapat ditukar/dikembalikan.
                </p>
                <div class="mt-4 text-[9px] text-gray-400 font-mono italic">
                    Powered by {{ config('app.name') }}
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-xl px-4 py-6 mx-auto no-print-area">
        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->customer->phone ?? '') }}?text=Terima%20kasih%20sudah%20berbelanja!%20Ini%20struk%20Anda:%20{{ urlencode(route('orders.show', $order->id)) }}"
            target="_blank"
            class="flex items-center justify-center w-full py-4 font-black text-white transition bg-green-500 shadow-xl rounded-2xl hover:bg-green-600 active:scale-95">
            <i class="mr-2 text-xl fa-brands fa-whatsapp"></i>
            KIRIM STRUK KE WHATSAPP
        </a>
    </div>

    <style>
        /* Sembunyikan elemen tertentu saat print */
        @media print {
            .no-print-area {
                display: none !important;
            }

            body,
            html {
                background: white;
                margin: 0;
                padding: 0;
            }

            #receipt {
                width: 100% !important;
                max-width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
                box-shadow: none !important;
                border: none !important;
            }

            .receipt-content {
                width: 100% !important;
                max-width: 100% !important;
                padding: 0px 10px !important;
            }

            .font-mono {
                font-family: 'Courier New', Courier, monospace !important;
            }
        }

        /* Tampilan di Layar agar mirip struk fisik */
        #receipt {
            max-width: 400px;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
        }
    </style>

    <!-- Iframe Tersembunyi -->
    {{-- <iframe id="printFrame" style="position: absolute; width: 0; height: 0; border: 0; opacity: 0;"></iframe> --}}
    <iframe id="printFrame" style="display:none;"></iframe>


    <script>
        const btnPrint = document.getElementById('btnPrintReceipt');

        btnPrint.onclick = function() {
            const url = "{{ route('orders.print', $order->id) }}";

            // 1. Ubah tombol ke mode loading sebentar
            btnPrint.disabled = true;
            btnPrint.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i> MENYIAPKAN...';

            // 2. Buka jendela pop-up kecil di latar belakang
            const printWindow = window.open(url, '_blank', 'width=400,height=600,top=100,left=100');

            // 3. Kembalikan tombol ke normal setelah 2 detik
            setTimeout(() => {
                btnPrint.disabled = false;
                btnPrint.innerHTML = '<i class="mr-2 fa-solid fa-print"></i> CETAK STRUK';
            }, 2000);
        };
    </script>
</x-app-layout>
