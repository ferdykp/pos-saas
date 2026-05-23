<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Struk - {{ $order->invoice_number }}</title>
    <style>
        /* Pengaturan Ukuran Kertas Thermal (Misal 58mm) */
        @page {
            margin: 0;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            width: 58mm;
            /* Sesuaikan ke 80mm jika menggunakan printer besar */
            margin: 0;
            padding: 5mm;
            background-color: #fff;
            font-size: 12px;
            line-height: 1.2;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: bold;
        }

        .uppercase {
            text-transform: uppercase;
        }

        .header {
            margin-bottom: 10px;
        }

        .header h2 {
            margin: 0;
            font-size: 16px;
        }

        .header p {
            margin: 2px 0;
            font-size: 10px;
        }

        .divider {
            border-bottom: 1px dashed #000;
            margin: 10px 0;
        }

        .info-table,
        .item-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            font-size: 10px;
            padding: 1px 0;
        }

        .item-table td {
            padding: 3px 0;
            vertical-align: top;
        }

        .price-col {
            text-align: right;
            white-space: nowrap;
        }

        .total-section {
            margin-top: 10px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
        }

        .grand-total {
            margin-top: 5px;
            padding-top: 5px;
            border-top: 1px double #000;
            font-size: 14px;
        }

        .footer {
            margin-top: 15px;
            font-size: 10px;
        }

        /* Tombol Cetak (Hanya tampil di layar) */
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>

    <div class="no-print" style="margin-bottom: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer;">KLIK CETAK</button>
        <button onclick="window.close()" style="padding: 10px 20px; cursor: pointer;">TUTUP</button>
    </div>

    <div class="text-center header">
        <h2 class="uppercase">{{ auth()->user()->tenant->name ?? 'TOKO KAMI' }}</h2>
        <p>{{ auth()->user()->tenant->address ?? 'Alamat' }}</p>
        <p>Telp: {{ auth()->user()->tenant->phone ?? '-' }}</p>
    </div>

    <div class="divider"></div>

    <table class="info-table">
        <tr>
            <td>No: {{ $order->invoice_number }}</td>
            <td class="text-right">{{ $order->created_at->format('d/m/y H:i') }}</td>
        </tr>
        <tr>
            <td>Kasir: {{ auth()->user()->name }}</td>
            <td class="text-right">{{ $order->customer->name ?? 'Umum' }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    <table class="item-table">
        @foreach ($order->items as $item)
            <tr>
                <td colspan="2" class="uppercase">{{ $item->product_name }}</td>
            </tr>
            <tr>
                <td style="font-size: 10px;">{{ $item->quantity }} x {{ number_format($item->price, 0, ',', '.') }}
                </td>
                <td class="font-bold price-col">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </table>

    <div class="divider"></div>

    <div class="total-section">
        <div class="total-row">
            <span>Subtotal:</span>
            <span>Rp{{ number_format($order->subtotal, 0, ',', '.') }}</span>
        </div>
        @if ($order->discount > 0)
            <div class="total-row">
                <span>Diskon:</span>
                <span>-Rp{{ number_format($order->discount, 0, ',', '.') }}</span>
            </div>
        @endif
        @if ($order->tax > 0)
            <div class="total-row">
                <span>Pajak:</span>
                <span>+Rp{{ number_format($order->tax, 0, ',', '.') }}</span>
            </div>
        @endif

        <div class="font-bold total-row grand-total">
            <span>TOTAL:</span>
            <span>Rp{{ number_format($order->grand_total, 0, ',', '.') }}</span>
        </div>

        <div class="divider"></div>

        <div class="total-row">
            <span>Bayar ({{ strtoupper($order->payment_method) }}):</span>
            <span>Rp{{ number_format($order->paid_amount, 0, ',', '.') }}</span>
        </div>
        <div class="total-row">
            <span>Kembali:</span>
            <span>Rp{{ number_format($order->change_amount, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="text-center footer">
        <p class="font-bold">*** {{ $order->payment_status === 'paid' ? 'LUNAS' : 'PIUTANG' }} ***</p>
        <p>Terima Kasih Atas Kunjungan Anda</p>
        <p>Struk ini adalah bukti pembayaran sah</p>
    </div>

    {{-- <script>
        // Otomatis buka dialog print saat halaman dimuat
        window.onload = function() {
            window.print();
            // window.onafterprint = function() { window.close(); }
        }
    </script> --}}
    <script>
        window.focus();

        // Beri jeda sedikit agar CSS thermal termuat sempurna di Chrome
        setTimeout(function() {
            window.print();

            // Otomatis menutup pop-up window setelah dialog print ditutup (di-print atau di-cancel)
            window.close();
        }, 500);
    </script>
</body>

</html>
