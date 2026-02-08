@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card card-register p-4"
                    style="border-top: 5px solid #FFD700; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                    <div class="card-body">

                        <div class="text-center mb-4">
                            <img src="{{ asset('logo.png') }}" alt="Logo"
                                style="width: 100px; display: block; margin: 0 auto 10px auto;">
                            <h5 class="fw-bold" style="color: #9A1B1F;">REGISTRASI KASIR</h5>
                            <p class="text-muted small">Daftar manual khusus staf tanpa Email</p>
                        </div>

                        <form action="/register" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Nama Lengkap</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                                    placeholder="Contoh: Budi Santoso" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Buat Username</label>
                                <input type="text" name="username" class="form-control" value="{{ old('username') }}"
                                    placeholder="Contoh: budi123" required>
                                <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Gunakan huruf & angka
                                    tanpa spasi untuk login.</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Jabatan / Role</label>
                                <input type="text" class="form-control bg-light" value="Kasir (Staff Toko)" readonly>
                                <input type="hidden" name="role" value="kasir">
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>

                            <button class="btn w-100 py-2 text-white fw-bold" style="background-color: #9A1B1F;">DAFTAR
                                SEKARANG</button>
                        </form>

                        <div class="text-center mt-4">
                            <p class="small text-muted mb-2">Punya Email / Admin? Daftar lewat Google:</p>
                            <div class="d-flex gap-2 justify-content-center">
                                <a href="/auth/google?role=kasir" class="btn btn-outline-danger btn-sm w-50">
                                    <i class="bi bi-google me-1"></i> Sbg Kasir
                                </a>
                                <a href="/auth/google?role=admin" class="btn btn-outline-dark btn-sm w-50">
                                    <i class="bi bi-shield-lock me-1"></i> Sbg Admin
                                </a>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <a href="/login" class="text-decoration-none small text-muted">Sudah punya akun? <span
                                    class="fw-bold" style="color: #9A1B1F;">Login disini</span></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="module">
        @if ($errors->any())
            Swal.fire({
                icon: 'warning',
                title: 'Gagal Daftar',
                html: `<ul style="text-align: left;">@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>`,
                confirmButtonColor: '#9A1B1F'
            });
        @endif
    </script>
@endsection
