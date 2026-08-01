<x-app-layout>
    @section('title', 'Detail Struk Transaksi')

    <div class="px-4 py-6 mx-auto md:px-6 lg:px-8 max-w-modal-lg">

        <!-- Navigation Top Bar -->
        <div class="flex items-center justify-between mb-6 no-print-area">
            <a href="{{ route('orders.index') }}"
                class="inline-flex items-center gap-2 text-xs font-semibold transition-colors font-body text-ink-700 hover:text-primary-600">
                <i class="text-xs fa-solid fa-arrow-left"></i>
                <span>Kembali ke Riwayat Transaksi</span>
            </a>

            <button id="btnPrintReceipt"
                class="inline-flex items-center h-10 gap-2 px-5 text-xs font-semibold text-white transition-colors rounded-md shadow-sm bg-primary-600 hover:bg-primary-700 font-body">
                <i class="fa-solid fa-print"></i>
                <span>Cetak Struk Belanja</span>
            </button>
        </div>

        <!-- Digital Receipt Card Simulation -->
        <div id="receipt"
            class="mx-auto bg-surface-0 border border-border-200 rounded-lg shadow-sm p-6 md:p-8 max-w-[420px]">

            <!-- Store Brand Header -->
            <div class="mb-6 text-center">
                <h2 class="text-xl font-bold tracking-tight uppercase font-heading text-ink-900">
                    {{ auth()->user()->tenant->name ?? 'GROWPOS STORE' }}
                </h2>
                <p class="font-body text-[11px] text-ink-700 mt-1 leading-snug">
                    {{ auth()->user()->tenant->address ?? 'Alamat Operasional Toko' }}<br>
                    Telp/WA: {{ auth()->user()->tenant->phone ?? '-' }}
                </p>
            </div>

            <div class="my-4 border-b border-dashed border-border-200"></div>

            <!-- Receipt Meta Info -->
            <div class="space-y-1.5 font-mono text-xs text-ink-900">
                <div class="flex justify-between">
                    <span class="text-ink-400">No. Invoice:</span>
                    <span class="font-semibold text-ink-900">{{ $order->invoice_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-ink-400">Waktu:</span>
                    <span>{{ $order->created_at->format('d/m/Y H:i') }} WIB</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-ink-400">Kasir:</span>
                    <span>{{ auth()->user()->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-ink-400">Pelanggan:</span>
                    <span class="font-semibold text-ink-900">{{ $order->customer->name ?? 'Pelanggan Umum' }}</span>
                </div>
            </div>

            <div class="my-4 border-b border-dashed border-border-200"></div>

            <!-- Purchased Item Details -->
            <div class="space-y-2.5 font-mono text-xs">
                @foreach ($order->items as $item)
                    <div>
                        <div class="flex justify-between font-semibold leading-tight text-ink-900">
                            <span class="pr-2 uppercase truncate">{{ $item->product_name }}</span>
                            <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="text-[11px] text-ink-400 mt-0.5">
                            {{ $item->quantity }}x @ Rp {{ number_format($item->price, 0, ',', '.') }}
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="my-4 border-b border-dashed border-border-200"></div>

            <!-- Totals & Payment Calculations -->
            <div class="space-y-1.5 font-mono text-xs text-ink-900">
                <div class="flex justify-between text-ink-700">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>
                @if ($order->discount > 0)
                    <div class="flex justify-between text-semantic-danger">
                        <span>Diskon</span>
                        <span>-Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                    </div>
                @endif
                @if ($order->tax > 0)
                    <div class="flex justify-between text-ink-700">
                        <span>Pajak Outlet</span>
                        <span>+Rp {{ number_format($order->tax, 0, ',', '.') }}</span>
                    </div>
                @endif

                <div
                    class="flex justify-between pt-2 mt-2 text-sm font-bold border-t border-double border-border-200 text-primary-600">
                    <span>TOTAL</span>
                    <span>Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                </div>

                <div class="flex justify-between pt-2 text-ink-700">
                    <span>Bayar ({{ strtoupper($order->payment_method) }})</span>
                    <span>Rp {{ number_format($order->paid_amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-ink-700">
                    <span>Kembali</span>
                    <span>Rp {{ number_format($order->change_amount, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Status Badge & Thank You Note -->
            <div class="mt-6 text-center">
                @if ($order->payment_status === 'paid')
                    <span
                        class="inline-flex items-center px-3 py-1 mb-3 text-xs font-bold rounded-full font-heading bg-primary-100 text-primary-700">
                        ✓ LUNAS
                    </span>
                @else
                    <span
                        class="inline-flex items-center px-3 py-1 mb-3 text-xs font-bold rounded-full font-heading bg-accent-100 text-accent-700">
                        ! PIUTANG / BON
                    </span>
                @endif

                <p class="font-body text-[11px] text-ink-700 leading-tight">
                    Terima kasih atas kunjungan Anda!<br>
                    Barang yang sudah dibeli tidak dapat ditukar/dikembalikan.
                </p>
                <div class="mt-3 font-mono text-[10px] text-ink-400">
                    Powered by GrowPOS SaaS
                </div>
            </div>
        </div>

        <!-- WhatsApp Share CTA Action -->
        <div class="max-w-[420px] mx-auto mt-4 no-print-area">
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->customer->phone ?? '') }}?text=Terima%20kasih%20sudah%20berbelanja%20di%20{{ urlencode(auth()->user()->tenant->name ?? 'Toko Kami') }}!%20Berikut%20rincian%20struk%20Anda:%20{{ urlencode(route('orders.show', $order->id)) }}"
                target="_blank"
                class="inline-flex items-center justify-center w-full gap-2 px-5 text-xs font-semibold text-white transition-colors rounded-md shadow-sm h-11 bg-semantic-success hover:bg-emerald-600 font-body">
                <i class="text-base fa-brands fa-whatsapp"></i>
                <span>Kirim Struk Digital ke WhatsApp</span>
            </a>
        </div>
    </div>

    <iframe id="printFrame" class="hidden"></iframe>

    <script>
        const btnPrint = document.getElementById('btnPrintReceipt');

        btnPrint.onclick = function() {
            const url = "{{ route('orders.print', $order->id) }}";

            btnPrint.disabled = true;
            btnPrint.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i> Menyiapkan...';

            const printWindow = window.open(url, '_blank', 'width=400,height=600,top=100,left=100');

            setTimeout(() => {
                btnPrint.disabled = false;
                btnPrint.innerHTML = '<i class="fa-solid fa-print"></i> Cetak Struk Belanja';
            }, 1800);
        };
    </script>
</x-app-layout>
