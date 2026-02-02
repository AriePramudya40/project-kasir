<!DOCTYPE html>
<html lang="id">

<head>
    <title>Login - Sumber Bangunan</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
    <style>
        :root {
            --brand-red: #9A1B1F;
            --brand-gold: #FFD700;
        }

        body {
            background-color: #f8f9fa;
        }

        .card-login {
            border: none;
            border-top: 5px solid var(--brand-gold);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        /* --- PERBAIKAN LOGO LOGIN --- */
        .logo-img {
            width: 140px;
            /* Ukuran pas */
            height: auto;
            display: block;
            /* Wajib agar bisa di-tengah */
            margin: 0 auto 15px auto;
            /* Kanan-Kiri Auto = Tengah */
            filter: drop-shadow(0px 2px 2px rgba(0, 0, 0, 0.2));
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

        .text-brand {
            color: var(--brand-red);
        }
    </style>
</head>

<body class="d-flex align-items-center min-vh-100">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card card-login p-4">
                    <div class="card-body text-center">

                        <img src="{{ asset('logo.png') }}" alt="Logo Sumber Bangunan" class="logo-img">

                        <h4 class="fw-bold text-brand text-uppercase mb-1">SUMBER BANGUNAN</h4>
                        <small class="text-muted d-block mb-4" style="letter-spacing: 1px;">Material &
                            Konstruksi</small>

                        @if (session('success'))
                            <div class="alert alert-success py-2 small">{{ session('success') }}</div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger py-2 small">{{ session('error') }}</div>
                        @endif

                        <form action="/login" method="POST" class="text-start mt-3">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Email</label>
                                <input type="email" name="email" class="form-control" placeholder="nama@toko.com"
                                    required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="••••••"
                                    required>
                            </div>

                            <button class="btn btn-brand w-100 py-2 mb-3">MASUK APLIKASI</button>
                        </form>

                        <div class="d-flex align-items-center mb-3">
                            <hr class="flex-grow-1">
                            <span class="mx-2 text-muted small">atau</span>
                            <hr class="flex-grow-1">
                        </div>

                        <a href="/auth/google" class="btn btn-outline-danger w-100 py-2 mb-3">
                            <i class="bi bi-google me-2"></i> Masuk dengan Google
                        </a>

                        <div class="text-center mt-2">
                            <a href="/register" class="text-decoration-none small text-muted">Belum punya akun? <span
                                    class="text-brand fw-bold">Daftar disini</span></a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
