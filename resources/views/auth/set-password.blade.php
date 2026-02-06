@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card card-setup p-4"
                    style="border-top: 5px solid #FFD700; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                    <div class="card-body">

                        <div class="text-center mb-4">
                            <img src="{{ asset('logo.png') }}" alt="Logo"
                                style="width: 100px; display: block; margin: 0 auto 10px auto;">
                            <h5 class="fw-bold" style="color: #9A1B1F;">KEAMANAN AKUN</h5>
                            <p class="text-muted small">
                                Halo <strong>{{ Auth::user()->name }}</strong>,<br>
                                Sebagai Admin, Anda wajib membuat password untuk keamanan.
                            </p>
                        </div>

                        <form action="/auth/set-password" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Password Baru</label>
                                <input type="password" name="password" class="form-control" placeholder="••••••" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" class="form-control"
                                    placeholder="••••••" required>
                            </div>
                            <button class="btn w-100 py-2 text-white fw-bold" style="background-color: #9A1B1F;">SIMPAN
                                PASSWORD</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="module">
        import Swal from 'sweetalert2';
        window.Swal = Swal;

        // Menampilkan Error Validasi (Misal: Password tidak cocok)
        @if ($errors->any())
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                html: `
                <ul style="text-align: left;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            `,
                confirmButtonColor: '#9A1B1F'
            });
        @endif

        // Menampilkan Info (Misal: "Wajib buat password")
        @if (session('info'))
            Swal.fire({
                icon: 'info',
                title: 'Info',
                text: "{{ session('info') }}",
                confirmButtonColor: '#9A1B1F'
            });
        @endif
    </script>
@endsection
