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
            border-top: 2px dashed #000;
            margin: 3px 0;
        }

        img {
            width: 35px;
            display: block;
            margin: 0 auto 4px;
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
            font-weight: bold;
        }

        .header-title {
            font-size: 13px;
            font-weight: 900;
            letter-spacing: 1px;
            margin: 3px 0;
        }

        .header-address {
            font-size: 9px;
            font-weight: bold;
            line-height: 1.2;
            margin-bottom: 2px;
        }

        .info-label {
            font-weight: 900;
            min-width: 50px;
            display: inline-block;
        }

        .product-name {
            font-weight: 900;
            font-size: 9px;
            margin-bottom: 1px;
        }

        .product-detail {
            font-size: 8px;
            font-weight: bold;
            color: #000;
        }

        .total-section {
            font-size: 11px;
            font-weight: 900;
            padding: 2px 0;
        }

        .footer-thanks {
            font-size: 11px;
            font-weight: 900;
            margin-top: 3px;
            letter-spacing: 0.5px;
        }

        .footer-note {
            font-size: 8px;
            font-weight: bold;
            line-height: 1.2;
            margin-top: 2px;
        }
    </style>
</head>

<body onload="window.print(); window.onafterprint = function(){ window.close(); }">

    <div class="text-center">
        <img src="{{ asset('logo.png') }}" alt="Logo">
        <div class="header-title">SUMBER BANGUNAN</div>
        <div class="header-address">Jl. Sudirman, Air Molek I<br>Kec. Pasir Penyu, Riau</div>
    </div>

    <div class="line"></div>

    <table>
        <tr>
            <td class="info-label">Faktur</td>
            <td class="text-right fw-bold">{{ $sale->no_faktur }}</td>
        </tr>
        <tr>
            <td class="info-label">Tanggal</td>
            <td class="text-right">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td class="info-label">Kasir</td>
            <td class="text-right">{{ strtoupper($sale->user->name ?? 'Admin') }}</td>
        </tr>
        @if ($sale->customer_name && $sale->customer_name != 'Umum')
            <tr>
                <td class="info-label">Pelanggan</td>
                <td class="text-right">{{ strtoupper($sale->customer_name) }}</td>
            </tr>
        @endif
        <tr>
            <td class="info-label">Metode</td>
            <td class="text-right fw-bold">{{ strtoupper($sale->payment_method) }}</td>
        </tr>
    </table>

    <div class="line"></div>

    <table>
        @foreach ($sale->items as $item)
            <tr>
                <td colspan="2" class="product-name">{{ strtoupper($item->product->nama) }}</td>
            </tr>
            <tr>
                <td class="product-detail">{{ $item->qty }} x Rp
                    {{ number_format($item->harga_saat_itu, 0, ',', '.') }}</td>
                <td class="text-right fw-bold">Rp {{ number_format($item->subtotal_line, 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </table>

    <div class="line"></div>

    <table>
        <tr>
            <td>Subtotal</td>
            <td class="text-right fw-bold">Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</td>
        </tr>
        @if ($sale->diskon > 0)
            <tr>
                <td>Diskon</td>
                <td class="text-right fw-bold">-Rp {{ number_format($sale->diskon, 0, ',', '.') }}</td>
            </tr>
        @endif
        <tr>
            <td colspan="2" class="line"></td>
        </tr>
        <tr class="total-section">
            <td>TOTAL</td>
            <td class="text-right">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="2" class="line"></td>
        </tr>

        @if ($sale->payment_method == 'cash')
            <tr>
                <td>Tunai</td>
                <td class="text-right fw-bold">Rp {{ number_format($sale->bayar, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Kembali</td>
                <td class="text-right fw-bold">Rp {{ number_format($sale->bayar - $sale->grand_total, 0, ',', '.') }}
                </td>
            </tr>
        @elseif($sale->payment_method == 'utang')
            <tr>
                <td>Sisa Utang</td>
                <td class="text-right fw-bold">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="2" class="text-center fw-bold" style="padding-top:4px; font-size:10px;">** BELUM LUNAS
                    **</td>
            </tr>
        @elseif($sale->payment_method == 'transfer')
            <tr>
                <td>Transfer</td>
                <td class="text-right fw-bold">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</td>
            </tr>
        @elseif($sale->payment_method == 'online')
            <tr>
                <td>Pembayaran Online</td>
                <td class="text-right fw-bold">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="2" class="text-center fw-bold" style="padding-top:4px; color:#28a745; font-size:10px;">✓
                    LUNAS</td>
            </tr>
        @endif
    </table>

    <div class="line" style="margin-top: 5px;"></div>
    <div class="text-center">
        <div class="footer-thanks">TERIMA KASIH</div>
        <div class="footer-note">Barang yang sudah dibeli<br>tidak dapat ditukar/dikembalikan</div>
    </div>

</body>

</html>
