<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Sumber Bangunan')</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>

    {{-- CSS & JS Global --}}
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        /* Variabel Global - Konsisten di semua halaman */
        :root {
            --brand-red: #9A1B1F;
            --brand-gold: #FFD700;
        }

        body {
            background-color: #f8f9fa;
            font-family: sans-serif;
            /* Sesuaikan jika ada font khusus */
        }

        /* Helper Classes */
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
