@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card card-login p-4"
                    style="border-top: 5px solid #FFD700; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                    <div class="card-body text-center">

                        <img src="{{ asset('logo.png') }}" alt="Logo"
                            style="width: 140px; display: block; margin: 0 auto 15px auto; filter: drop-shadow(0px 2px 2px rgba(0,0,0,0.2));">

                        <h4 class="fw-bold text-uppercase mb-1" style="color: #9A1B1F;">SUMBER BANGUNAN</h4>
                        <small class="text-muted d-block mb-4" style="letter-spacing: 1px;">Material & Konstruksi</small>

                        <form action="/login" method="POST" class="text-start mt-3">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Email / Username</label>
                                <input type="text" name="login_id" class="form-control"
                                    placeholder="Masukan email atau username..." required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="••••••" required>
                            </div>

                            <button class="btn w-100 py-2 mb-3 text-white fw-bold" style="background-color: #9A1B1F;">MASUK
                                APLIKASI</button>
                        </form>

                        <div class="text-center mt-2">
                            <a href="/register" class="text-decoration-none small text-muted">Belum punya akun? <span
                                    class="fw-bold" style="color: #9A1B1F;">Daftar disini</span></a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="flash-data" data-success="{{ session('success') }}" data-error="{{ session('error') }}">
    </div>

    @if ($errors->any())
        <script type="module">
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: "{{ $errors->first() }}",
                confirmButtonColor: '#9A1B1F'
            });
        </script>
    @endif
@endsection
