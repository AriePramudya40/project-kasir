<div class="navbar-floating">

    <div class="d-flex align-items-center gap-4">

        <a href="{{ url('/dashboard') }}" class="d-flex align-items-center text-decoration-none gap-3">
            <img src="{{ asset('logo.png') }}" alt="Logo" style="height: 40px; width: auto;">
            <div class="d-flex flex-column lh-1 d-none d-sm-block">
                <span class="fw-bold" style="color: var(--brand-red); letter-spacing: 0.5px;">SUMBER BANGUNAN</span>
                <small class="text-muted" style="font-size: 0.7rem;">POS System</small>
            </div>
        </a>

        <div class="vr opacity-25 d-none d-md-block" style="height: 25px;"></div>

        <div class="d-none d-md-flex align-items-center gap-2">
            <a href="{{ url('/dashboard') }}" class="nav-link-item {{ Request::is('dashboard') ? 'active' : '' }}">
                <i class="bi bi-shop"></i> Kasir
            </a>

            @if (Auth::user()->role == 'admin')
                <a href="{{ route('laporan.index') }}"
                    class="nav-link-item {{ Request::is('laporan*') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart-fill"></i> Laporan
                </a>
            @endif
        </div>

    </div>

    <div class="d-flex align-items-center gap-3">

        <div class="d-none d-lg-block text-muted small fw-bold">
            <i class="bi bi-clock me-1 text-danger"></i> <span id="digital-clock">00:00</span>
        </div>

        <div class="d-flex align-items-center gap-2">
            <button class="btn-tool" onclick="updateSemuaStatusOnline()" title="Sync Pembayaran">
                <i class="bi bi-arrow-repeat"></i>
            </button>
            <button class="btn-tool" id="theme-toggle" title="Mode Gelap/Terang">
                <i class="bi bi-moon-stars-fill" id="theme-icon"></i>
            </button>
        </div>

        <div class="d-flex align-items-center gap-2 ps-2 border-start">
            <div class="text-end lh-1 d-none d-sm-block">
                <span class="d-block fw-bold small"
                    style="color: var(--text-main);">{{ Str::limit(Auth::user()->name, 10) }}</span>
                <small class="text-uppercase"
                    style="font-size: 0.6rem; color: var(--text-muted);">{{ Auth::user()->role }}</small>
            </div>

            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white shadow-sm"
                style="width: 38px; height: 38px; background: linear-gradient(135deg, var(--brand-red), #b92b2f);">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>

            <a href="{{ route('logout') }}"
                class="btn btn-light text-danger rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                style="width: 32px; height: 32px; border: 1px solid rgba(0,0,0,0.1);" title="Keluar">
                <i class="bi bi-power"></i>
            </a>
        </div>

    </div>
</div>
