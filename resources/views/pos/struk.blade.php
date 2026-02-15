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
            font-weight: bold;
            padding: 2mm;
            margin: 0 auto;
            color: #000;
            line-height: 1.2;
        }

        .text-center {
            text-align: center;
            margin: 0 auto;
        }

        .text-right {
            text-align: right;
        }

        .fw-bold {
            font-weight: bold;
        }

        .line {
            border-top: 1px dashed #000;
            margin: 3px 0;
        }

        .double-line {
            border-top: 2px dashed #000;
            margin: 3px 0;
        }

        /* LOGO STYLE */
        img {
            width: 40px;
            display: block;
            margin: 0 auto 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }

        td {
            vertical-align: top;
            padding: 1px 0;
            font-size: 10px;
        }

        .header-title {
            font-size: 13px;
            font-weight: 900;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }

        .header-address {
            font-size: 9px;
            line-height: 1.1;
            margin-bottom: 5px;
        }

        .product-name {
            font-weight: 900;
            font-size: 9px;
        }

        .total-row {
            font-size: 11px;
            font-weight: 900;
        }

        .status-box {
            border: 2px solid #000;
            padding: 4px;
            text-align: center;
            margin-top: 5px;
            font-weight: 900;
            font-size: 12px;
        }
    </style>
</head>

<body onload="window.print()">

    <div class="text-center">
        <img src="{{ asset('logo.png') }}" alt="Logo">
        <div class="header-title">SUMBER BANGUNAN</div>
        <div class="header-address">Jl. Sudirman, Air Molek I<br>Kec. Pasir Penyu, Riau</div>
    </div>

    <div class="double-line"></div>

    <table>
        <tr>
            <td>Faktur</td>
            <td class="text-right fw-bold">{{ $sale->no_faktur }}</td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td class="text-right">{{ $sale->created_at->format('d/m/y H:i') }}</td>
        </tr>
        <tr>
            <td>Kasir</td>
            <td class="text-right">{{ strtoupper($sale->user->name ?? 'Admin') }}</td>
        </tr>
        <tr>
            <td>Pelanggan</td>
            <td class="text-right">{{ strtoupper($sale->customer_name) }}</td>
        </tr>
    </table>

    <div class="line"></div>

    <table>
        @foreach ($sale->items as $item)
            <tr>
                <td colspan="2" class="product-name">{{ strtoupper($item->product->nama) }}</td>
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
            <td class="text-right">Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</td>
        </tr>
        @if ($sale->diskon > 0)
            <tr>
                <td>Diskon</td>
                <td class="text-right">-Rp {{ number_format($sale->diskon, 0, ',', '.') }}</td>
            </tr>
        @endif

        <tr>
            <td colspan="2" class="line"></td>
        </tr>

        <tr class="total-row">
            <td>TOTAL TAGIHAN</td>
            <td class="text-right">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</td>
        </tr>
    </table>

    @if (isset($sale->payments) && $sale->payments->count() > 0)
        <div class="line"></div>
        <div class="text-center fw-bold" style="font-size:9px; margin:2px 0;">RIWAYAT PEMBAYARAN</div>
        <table>
            @foreach ($sale->payments as $pay)
                <tr style="font-size: 9px; color: #333;">
                    <td>{{ $pay->created_at->format('d/m H:i') }} [{{ strtoupper($pay->payment_method) }}]</td>
                    <td class="text-right">{{ number_format($pay->nominal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </table>
    @endif

    <div class="line"></div>

    <table>
        <tr>
            <td class="fw-bold">Total Dibayar</td>
            <td class="text-right fw-bold">Rp {{ number_format($sale->bayar, 0, ',', '.') }}</td>
        </tr>

        <tr class="total-row">
            @if ($sale->bayar >= $sale->grand_total)
                <td>KEMBALI</td>
                <td class="text-right">Rp {{ number_format($sale->bayar - $sale->grand_total, 0, ',', '.') }}</td>
            @else
                <td>SISA UTANG</td>
                <td class="text-right">Rp {{ number_format($sale->grand_total - $sale->bayar, 0, ',', '.') }}</td>
            @endif
        </tr>
    </table>

    <div class="status-box">
        @if ($sale->status == 'lunas')
            LUNAS
        @elseif($sale->status == 'batal')
            DIBATALKAN
        @else
            BELUM LUNAS
        @endif
    </div>

    <div class="text-center" style="margin-top: 10px;">
        <div style="font-size: 10px; font-weight:900;">TERIMA KASIH</div>
        <div style="font-size: 8px;">Barang yang dibeli tidak dapat ditukar</div>
    </div>

</body>

</html>
