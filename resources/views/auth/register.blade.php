<!DOCTYPE html>
<html lang="id">

<head>
    <title>Daftar - Sumber Bangunan</title>
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

        .card-register {
            border: none;
            border-top: 5px solid var(--brand-gold);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        /* --- PERBAIKAN LOGO REGISTER --- */
        .logo-register {
            width: 100px;
            /* Diperbesar agar jelas */
            height: auto;
            display: block;
            margin: 0 auto 10px auto;
            /* Posisi Tengah */
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
            <div class="col-md-5">
                <div class="card card-register p-4">
                    <div class="card-body">

                        <div class="text-center mb-4">
                            <img src="{{ asset('logo.png') }}" alt="Logo" class="logo-register">

                            <h5 class="fw-bold text-brand">REGISTRASI KARYAWAN</h5>
                            <p class="text-muted small">Buat akun baru untuk akses sistem POS</p>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger py-2">
                                <ul class="mb-0 small ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="/register" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Nama Lengkap</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Jabatan / Role</label>
                                <select name="role" class="form-select">
                                    <option value="kasir">Kasir (Staff Toko)</option>
                                    <option value="admin">Admin (Manager/Pemilik)</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted">Password</label>
                                <input type="password" name="password" class="form-control" required>
                                <small class="text-muted" style="font-size: 0.7rem;">Minimal 6 karakter</small>
                            </div>

                            <button class="btn btn-brand w-100 py-2">DAFTAR SEKARANG</button>
                        </form>

                        <div class="text-center mt-3">
                            <a href="/login" class="text-decoration-none small text-muted">Sudah punya akun? <span
                                    class="text-brand fw-bold">Login disini</span></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
