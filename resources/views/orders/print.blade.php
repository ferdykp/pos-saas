<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk - {{ $order->invoice_number }}</title>
    <style>
        @page {
            margin: 0;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            width: 58mm;
            /* Dapat disesuaikan ke 80mm */
            margin: 0 auto;
            padding: 4mm;
            background-color: #ffffff;
            font-size: 11px;
            line-height: 1.25;
            color: #000000;
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
            margin-bottom: 8px;
        }

        .header h2 {
            margin: 0;
            font-size: 14px;
            font-weight: bold;
        }

        .header p {
            margin: 2px 0;
            font-size: 10px;
        }

        .divider {
            border-bottom: 1px dashed #000000;
            margin: 8px 0;
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
            padding: 2px 0;
            vertical-align: top;
        }

        .price-col {
            text-align: right;
            white-space: nowrap;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
        }

        .grand-total {
            margin-top: 4px;
            padding-top: 4px;
            border-top: 1px double #000000;
            font-size: 13px;
        }

        .footer {
            margin-top: 12px;
            font-size: 10px;
        }

        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>

    <div class="no-print" style="margin-bottom: 16px; text-align: center;">
        <button onclick="window.print()" style="padding: 8px 16px; font-weight: bold; cursor: pointer;">CETAK</button>
        <button onclick="window.close()" style="padding: 8px 16px; cursor: pointer;">TUTUP</button>
    </div>

    <div class="text-center header">
        <h2 class="uppercase">{{ auth()->user()->tenant->name ?? 'GROWPOS STORE' }}</h2>
        <p>{{ auth()->user()->tenant->address ?? 'Alamat Operasional' }}</p>
        <p>Telp: {{ auth()->user()->tenant->phone ?? '-' }}</p>
    </div>
    {{-- <div class="receipt-header">
        <p>No. Invoice: {{ $order->invoice_number }}</p>
        <p>Kasir: {{ $order->user->name ?? 'Kasir' }}</p>
        <p>Tipe:
            <strong>
                {{ strtoupper($order->order_type ?? 'DINE_IN') }}
                @if ($order->order_type === 'dine_in' && $order->table_number)
                    (Meja {{ $order->table_number }})
                @endif
            </strong>
        </p>
    </div> --}}

    <div class="divider"></div>

    <table class="info-table">
        <tr>
            <td>No: {{ $order->invoice_number }}</td>
            <td class="text-right">{{ $order->created_at->format('d/m/y H:i') }}</td>
        </tr>
        <tr>
            <td>Kasir: {{ auth()->user()->name }}</td>
            <td class="text-right">{{ $order->customer->name ?? 'Pelanggan Umum' }}</td>
        </tr>
        <p>Tipe:
            <strong>
                {{ strtoupper($order->order_type ?? 'DINE_IN') }}
                @if ($order->order_type === 'dine_in' && $order->table_number)
                    (Meja {{ $order->table_number }})
                @endif
            </strong>
        </p>
    </table>

    <div class="divider"></div>

    <table class="item-table">
        @foreach ($order->items as $item)
            <tr>
                <td colspan="2" class="font-bold uppercase">{{ $item->product_name }}</td>
            </tr>
            <tr>
                <td style="font-size: 10px;">{{ $item->quantity }}x @ Rp{{ number_format($item->price, 0, ',', '.') }}
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
                <span>Diskon Promo:</span>
                <span>-Rp{{ number_format($order->discount, 0, ',', '.') }}</span>
            </div>
        @endif
        @if ($order->tax > 0)
            <div class="total-row">
                <span>Pajak Outlet:</span>
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
            <span>Kembalian:</span>
            <span>Rp{{ number_format($order->change_amount, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="text-center footer">
        <p class="font-bold">*** {{ $order->payment_status === 'paid' ? 'LUNAS' : 'PIUTANG / BON' }} ***</p>
        <p>Terima Kasih Atas Kunjungan Anda!</p>
        <p>Powered by GrowPOS SaaS</p>
    </div>

    <script>
        window.focus();
        setTimeout(function() {
            window.print();
            window.close();
        }, 400);
    </script>
</body>

</html>
