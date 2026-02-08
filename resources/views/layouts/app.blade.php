<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sumber Bangunan - Material & Konstruksi')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">

    <!-- Midtrans Snap (untuk pembayaran online) -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}">
    </script>

    <!-- Vite Assets (CSS & JS dari NPM) -->
    @vite(['resources/css/app.scss', 'resources/js/app.js'])

    <!-- Stack untuk CSS tambahan per halaman -->
    @stack('styles')
</head>

<body class="@yield('body-class', '')">
    <!-- Main Content -->
    @yield('content')

    <!-- Stack untuk JavaScript tambahan per halaman -->
    @stack('scripts')
</body>

</html>
