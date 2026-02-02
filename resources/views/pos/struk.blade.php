<!DOCTYPE html>
<html>

<head>
    <title>Struk #{{ $sale->no_faktur }}</title>
    <style>
        @page {
            size: 58mm auto;
            margin: 0;
        }

        body {
            font-family: 'Courier New', monospace;
            width: 48mm;
            font-size: 10px;
            padding: 5mm;
            margin: 0;
        }

        .text-center {
            text-align: center;
        }

        .fw-bold {
            font-weight: bold;
        }

        .line {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        img {
            width: 40px;
            display: block;
            margin: 0 auto 5px;
        }
    </style>
</head>

<body onload="window.print(); window.onafterprint = function(){ window.close(); }">
    <div class="text-center">
        <img src="{{ asset('logo.png') }}">
        <div class="fw-bold">SUMBER BANGUNAN</div>
        <div style="font-size: 8px;">Material & Konstruksi</div>
    </div>
    <div class="line"></div>
    <div>Tgl: {{ $sale->created_at->format('d/m/y H:i') }}</div>
    <div>Faktur: {{ $sale->no_faktur }}</div>
    <div class="line"></div>
    <table style="width: 100%;">
        @foreach ($sale->items as $item)
            <tr>
                <td colspan="2" class="fw-bold">{{ $item->product->nama }}</td>
            </tr>
            <tr>
                <td>{{ $item->qty }} x {{ number_format($item->harga_saat_itu) }}</td>
                <td style="text-align: right;">{{ number_format($item->subtotal_line) }}</td>
            </tr>
        @endforeach
    </table>
    <div class="line"></div>
    <table style="width: 100%;">
        <tr>
            <td>Total</td>
            <td style="text-align: right;">{{ number_format($sale->grand_total) }}</td>
        </tr>
    </table>
    <div class="line" style="margin-top: 10px;"></div>
    <div class="text-center">TERIMA KASIH</div>
</body>

</html>
