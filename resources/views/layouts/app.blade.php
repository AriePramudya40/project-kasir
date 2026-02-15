<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sumber Bangunan')</title>
    <link rel="apple-touch-icon" href="/logo.png">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* --- VARIABLE --- */
        :root {
            --brand-red: #9A1B1F;
            --brand-gold: #FFD700;
            --bg-body: #f4f6f8;
            --bg-card: #ffffff;
            --text-main: #2c3e50;
            --text-muted: #6c757d;
            --border-color: rgba(0, 0, 0, 0.08);

            /* Navbar Variables */
            --nav-bg: rgba(255, 255, 255, 0.85);
            --nav-blur: 12px;
        }

        body.dark-mode {
            --bg-body: #121212;
            --bg-card: #1e1e1e;
            --text-main: #e0e0e0;
            --text-muted: #a0a0a0;
            --border-color: rgba(255, 255, 255, 0.1);
            --nav-bg: rgba(30, 30, 30, 0.85);
        }

        body {
            background-color: var(--bg-body);
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: var(--text-main);
            transition: background 0.3s, color 0.3s;
        }

        /* --- NAVBAR FLOATING CANTIK --- */
        .navbar-floating {
            position: sticky;
            /* Agar tetap nempel saat scroll */
            top: 1rem;
            /* Jarak floating dari atas */
            z-index: 1030;

            background: var(--nav-bg);
            backdrop-filter: blur(var(--nav-blur));
            /* Efek Kaca Buram */
            -webkit-backdrop-filter: blur(var(--nav-blur));

            border-radius: 20px;
            /* Sudut membulat modern */
            padding: 0.7rem 1.5rem;
            margin-bottom: 1.5rem;

            border: 1px solid var(--border-color);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
            /* Bayangan lembut */

            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.3s ease;
        }

        /* Menu Links Style */
        .nav-link-item {
            text-decoration: none;
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .nav-link-item:hover {
            color: var(--brand-red);
            background: rgba(154, 27, 31, 0.05);
        }

        .nav-link-item.active {
            background: var(--brand-red);
            color: white;
            box-shadow: 0 4px 10px rgba(154, 27, 31, 0.2);
        }

        /* Tools Button */
        .btn-tool {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--border-color);
            background: transparent;
            color: var(--text-muted);
            transition: 0.2s;
            cursor: pointer;
        }

        .btn-tool:hover {
            background: var(--bg-body);
            color: var(--brand-red);
            border-color: var(--brand-red);
        }

        /* Dark Mode Overrides */
        body.dark-mode .bg-white {
            background-color: var(--bg-card) !important;
        }

        body.dark-mode .bg-light {
            background-color: #2d2d2d !important;
        }

        body.dark-mode .text-dark {
            color: var(--text-main) !important;
        }

        body.dark-mode .text-muted {
            color: #a0a0a0 !important;
        }

        body.dark-mode .card {
            background-color: var(--bg-card);
            border-color: var(--border-color);
        }

        body.dark-mode .form-control {
            background-color: #2d2d2d;
            border-color: var(--border-color);
            color: var(--text-main);
        }

        body.dark-mode .swal2-popup {
            background: var(--bg-card);
            color: var(--text-main);
        }

        /* Utility */
        .btn-brand {
            background-color: var(--brand-red);
            color: white;
            font-weight: bold;
        }

        .btn-brand:hover {
            background-color: #7a1518;
            color: white;
        }

        body.dark-mode .table {
            --bs-table-bg: transparent;
            --bs-table-color: var(--text-main);
            color: var(--text-main);
            border-color: var(--border-color);
        }

        /* Header Tabel jadi Gelap */
        body.dark-mode .table thead th {
            background-color: var(--bg-input) !important;
            color: var(--text-main) !important;
            border-bottom-color: var(--border-color);
        }

        /* Hover Row jadi agak terang dikit, bukan putih */
        body.dark-mode .table-hover tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.05) !important;
            color: var(--text-main);
        }

        /* Striped Row (jika dipakai) */
        body.dark-mode .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(255, 255, 255, 0.02) !important;
            color: var(--text-main);
        }
    </style>
    @stack('styles')
</head>

<body class="@yield('body-class')">

    @auth
        <div class="container-fluid px-4 pt-3"> @include('layouts.navbar')
            @yield('content')
        </div>
    @else
        @yield('content')
    @endauth

    @stack('scripts')

    <script>
        function updateClock() {
            const clockEl = document.getElementById('digital-clock');
            if (clockEl) {
                const now = new Date();
                clockEl.innerText = now.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }
        }
        setInterval(updateClock, 1000);
        if (document.getElementById('digital-clock')) updateClock();

        const themeBtn = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-icon');
        const body = document.body;
        if (localStorage.getItem('theme') === 'dark') {
            body.classList.add('dark-mode');
            if (themeIcon) themeIcon.classList.replace('bi-moon-stars-fill', 'bi-sun-fill');
        }
        if (themeBtn) {
            themeBtn.addEventListener('click', () => {
                body.classList.toggle('dark-mode');
                if (body.classList.contains('dark-mode')) {
                    localStorage.setItem('theme', 'dark');
                    themeIcon.classList.replace('bi-moon-stars-fill', 'bi-sun-fill');
                } else {
                    localStorage.setItem('theme', 'light');
                    themeIcon.classList.replace('bi-sun-fill', 'bi-moon-stars-fill');
                }
            });
        }

        function updateSemuaStatusOnline() {
            if (typeof Swal === 'undefined') return;
            Swal.fire({
                title: 'Sync Payment?',
                text: 'Cek status pembayaran online...',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Cek',
                confirmButtonColor: '#28a745'
            }).then((r) => {
                if (r.isConfirmed) {
                    Swal.showLoading();
                    fetch('/transaksi/update-semua-online').then(res => res.json()).then(d => {
                        Swal.fire('Selesai', `Update: ${d.updated} Transaksi`, 'success').then(() => {
                            if (d.updated > 0) location.reload();
                        });
                    });
                }
            });
        }
    </script>
</body>

</html>
