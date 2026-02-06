@extends('layouts.app')

@section('title', 'Selamat Datang - Sumber Bangunan')

@push('styles')
    <style>
        .hero-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .welcome-logo {
            width: 180px;
            margin-bottom: 2rem;
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));
        }
    </style>
@endpush

@section('content')
    <div class="hero-section text-center px-4">
        <img src="{{ asset('logo.png') }}" alt="Sumber Bangunan" class="welcome-logo">

        <h1 class="display-4 fw-bold text-brand mb-2">SUMBER BANGUNAN</h1>
        <p class="lead text-muted mb-5">Sistem Informasi Point of Sales & Manajemen Stok</p>

        <div class="d-flex gap-3 justify-content-center">
            @auth
                <a href="{{ url('/dashboard') }}" class="btn btn-brand btn-lg px-5 shadow">
                    <i class="bi bi-speedometer2 me-2"></i> Ke Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-brand btn-lg px-4 shadow">
                    <i class="bi bi-box-arrow-in-right me-2"></i> Masuk Aplikasi
                </a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn btn-outline-secondary btn-lg px-4">
                        Daftar Baru
                    </a>
                @endif
            @endauth
        </div>

        <footer class="mt-5 text-muted small">
            &copy; {{ date('Y') }} CV. Sumber Bangunan. All rights reserved.
        </footer>
    </div>
@endsection
