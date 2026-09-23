<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Label {{ $product->product_name }}</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; color: #111; background: #f3f4f6; }
        .toolbar { display: flex; justify-content: center; gap: 8px; padding: 16px; }
        button { border: 0; border-radius: 6px; background: #18372d; color: white; padding: 10px 16px; font-weight: 700; cursor: pointer; }
        .sheet { display: grid; place-items: center; min-height: calc(100vh - 72px); padding: 24px; }
        .label { width: 60mm; min-height: 35mm; padding: 4mm; background: white; display: flex; flex-direction: column; justify-content: center; text-align: center; }
        .name { font-size: 11pt; font-weight: 700; line-height: 1.2; margin-bottom: 2mm; }
        .barcode svg { width: 100%; height: 17mm; display: block; }
        .code { margin-top: 1.5mm; font: 9pt monospace; letter-spacing: .08em; }
        .meta { margin-top: 1.5mm; font-size: 8pt; color: #555; }
        @media print {
            @page { size: 60mm 35mm; margin: 0; }
            body { background: white; }
            .toolbar { display: none; }
            .sheet { min-height: 0; padding: 0; }
            .label { width: 60mm; height: 35mm; }
        }
    </style>
</head>
<body>
    <div class="toolbar"><button type="button" onclick="window.print()">Cetak Label</button></div>
    <main class="sheet">
        <section class="label">
            <div class="name">{{ $product->product_name }}</div>
            <div class="barcode">{!! $barcodeSvg !!}</div>
            <div class="code">{{ $product->barcode }}</div>
            <div class="meta">{{ $product->sku }} · Rp {{ number_format($product->sell_price, 0, ',', '.') }}</div>
        </section>
    </main>
</body>
</html>
