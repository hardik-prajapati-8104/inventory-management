<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Barcode Labels</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
    <style>
        :root { --vsp-primary: #aa8038; }
        * { box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; margin: 0; padding: 20px; background: #f7f5f0; }

        .toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .toolbar button {
            background: var(--vsp-primary); color: #fff; border: none; padding: .6rem 1.2rem;
            border-radius: 8px; font-family: inherit; cursor: pointer; font-size: .9rem;
        }
        .toolbar a { color: var(--vsp-primary); text-decoration: none; font-size: .9rem; }

        .labels-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }
        .label {
            border: 1px dashed #ccc;
            border-radius: 6px;
            padding: 10px;
            text-align: center;
            background: #fff;
            page-break-inside: avoid;
        }
        .label .part-name { font-size: .78rem; font-weight: 600; margin-bottom: 2px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .label .part-number { font-size: .68rem; color: #666; margin-bottom: 6px; }
        .label svg { max-width: 100%; }
        .label .price { font-size: .82rem; font-weight: 700; margin-top: 4px; color: var(--vsp-primary); }

        @media print {
            body { background: #fff; padding: 0; }
            .toolbar { display: none; }
            .labels-grid { grid-template-columns: repeat(3, 1fr); }
            .label { border: 1px solid #ddd; }
        }
    </style>
</head>
<body>

    <div class="toolbar">
        <a href="{{ route('admin.spare-parts.index') }}">&larr; Back to Spare Parts</a>
        <button onclick="window.print()">🖨️ Print Labels</button>
    </div>

    <div class="labels-grid">
        @foreach ($parts as $part)
        <div class="label">
            <div class="part-name">{{ $part->name }}</div>
            <div class="part-number">{{ $part->part_number }} · {{ $part->sku }}</div>
            <svg class="barcode"
                 data-value="{{ $part->barcode ?: $part->sku ?: $part->part_number }}"></svg>
            <div class="price">₹{{ number_format($part->retail_price, 2) }}</div>
        </div>
        @endforeach
    </div>

    <script>
        document.querySelectorAll('.barcode').forEach(function (el) {
            JsBarcode(el, el.dataset.value, {
                format: 'CODE128',
                width: 1.6,
                height: 40,
                fontSize: 11,
                margin: 4,
            });
        });
    </script>
</body>
</html>
