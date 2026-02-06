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
            width: 58mm;
            font-size: 10px;
            padding: 2mm;
            margin: 0;
            color: #000;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
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

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            vertical-align: top;
            padding: 2px 0;
        }
    </style>
</head>

<body onload="window.print(); window.onafterprint = function(){ window.close(); }">

    <div class="text-center">
        <img src="{{ asset('logo.png') }}" alt="Logo">
        <div class="fw-bold" style="font-size: 12px;">SUMBER BANGUNAN</div>
        <div>Jl. Sudirman, Air Molek I, Kec. Pasir Penyu, Kabupaten Indragiri Hulu, Riau 29353</div>
    </div>

    <div class="line"></div>

    <table>
        <tr>
            <td>Faktur</td>
            <td class="text-right">{{ $sale->no_faktur }}</td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td class="text-right">{{ $sale->created_at->format('d/m/y H:i') }}</td>
        </tr>
        <tr>
            <td>Kasir</td>
            <td class="text-right">{{ strtoupper($sale->user->name ?? 'Admin') }}</td>
        </tr>
    </table>

    <div class="line"></div>

    <table>
        @foreach ($sale->items as $item)
            <tr>
                <td colspan="2" class="fw-bold">{{ $item->product->nama }}</td>
            </tr>
            <tr>
                <td>{{ $item->qty }} x {{ number_format($item->harga_saat_itu, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($item->subtotal_line, 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </table>

    <div class="line"></div>

    <table>
        <tr>
            <td>Subtotal</td>
            <td class="text-right">{{ number_format($sale->subtotal, 0, ',', '.') }}</td>
        </tr>
        @if ($sale->diskon > 0)
            <tr>
                <td>Diskon</td>
                <td class="text-right">-{{ number_format($sale->diskon, 0, ',', '.') }}</td>
            </tr>
        @endif
        <tr class="fw-bold" style="font-size: 11px;">
            <td>TOTAL</td>
            <td class="text-right">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="2" class="line"></td>
        </tr>
        <tr>
            <td>Tunai</td>
            <td class="text-right">{{ number_format($sale->bayar, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Kembali</td>
            <td class="text-right">{{ number_format($sale->bayar - $sale->grand_total, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="line" style="margin-top: 10px;"></div>
    <div class="text-center" style="margin-top: 5px;">
        TERIMA KASIH<br>
        <small>Barang yang sudah dibeli<br>tidak dapat ditukar/dikembalikan</small>
    </div>

</body>

</html>
