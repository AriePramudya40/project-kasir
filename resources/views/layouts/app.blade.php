<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Sumber Bangunan')</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">

    {{-- Script Midtrans (Tetap biarkan eksternal karena ini gateway pembayaran) --}}
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>

    {{-- Load CSS & JS via Vite --}}
    {{-- HAPUS CDN bootstrap-icons yang ada sebelumnya --}}
    @vite(['resources/css/app.scss', 'resources/js/app.js'])

    <style>
        /* Variabel Global */
        :root {
            --brand-red: #9A1B1F;
            --brand-gold: #FFD700;
        }

        body {
            background-color: #f8f9fa;
            font-family: sans-serif;
        }

        .text-brand {
            color: var(--brand-red);
        }

        .bg-brand {
            background-color: var(--brand-red);
        }

        .btn-brand {
            background-color: var(--brand-red);
            color: white;
            font-weight: bold;
            transition: 0.3s;
        }

        .btn-brand:hover {
            background-color: #7a1518;
            color: #fff;
        }
    </style>
    @stack('styles')
</head>

<body class="@yield('body-class')">

    @yield('content')

    @stack('scripts')
</body>

</html>
