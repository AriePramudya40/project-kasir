@extends('layouts.app')
@section('content')
    <style>
        /* --- VARIABEL TEMA (LIGHT MODE DEFAULT) --- */
        :root {
            --brand-red: #9A1B1F;
            --brand-gold: #FFD700;

            /* Warna Dasar Light */
            --bg-body: #f4f6f8;
            --bg-card: #ffffff;
            --bg-input: #f8f9fa;
            --text-main: #2c3e50;
            --text-muted: #6c757d;
            --border-color: rgba(0, 0, 0, 0.08);
            --shadow-color: rgba(0, 0, 0, 0.05);
            --modal-bg: #ffffff;
        }

        /* --- DARK MODE OVERRIDES --- */
        body.dark-mode {
            /* Warna Dasar Dark */
            --bg-body: #121212;
            --bg-card: #1e1e1e;
            --bg-input: #2d2d2d;
            --text-main: #e0e0e0;
            --text-muted: #a0a0a0;
            --border-color: rgba(255, 255, 255, 0.1);
            --shadow-color: rgba(0, 0, 0, 0.5);
            --modal-bg: #242424;
        }

        /* Override Bootstrap Classes untuk Dark Mode */
        body.dark-mode .bg-white {
            background-color: var(--bg-card) !important;
        }

        body.dark-mode .bg-light {
            background-color: var(--bg-input) !important;
        }

        body.dark-mode .text-dark {
            color: var(--text-main) !important;
        }

        body.dark-mode .text-muted {
            color: var(--text-muted) !important;
        }

        body.dark-mode .card {
            background-color: var(--bg-card);
            border-color: var(--border-color);
        }

        body.dark-mode .table {
            color: var(--text-main);
        }

        body.dark-mode .table-hover tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }

        body.dark-mode .form-control {
            background-color: var(--bg-input);
            border-color: var(--border-color);
            color: var(--text-main);
        }

        body.dark-mode .form-control:focus {
            background-color: #333;
            color: #fff;
        }

        body.dark-mode .form-select {
            background-color: var(--bg-input);
            border-color: var(--border-color);
            color: var(--text-main);
        }

        body.dark-mode .input-group-text {
            background-color: var(--bg-card);
            border-color: var(--border-color);
            color: var(--text-muted);
        }

        body.dark-mode .modal-content {
            background-color: var(--modal-bg);
            color: var(--text-main);
        }

        body.dark-mode .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        /* SweetAlert Dark Mode Fix */
        body.dark-mode .swal2-popup {
            background: var(--bg-card);
            color: var(--text-main);
        }

        /* --- GLOBAL STYLES --- */
        body {
            background-color: var(--bg-body);
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: var(--text-main);
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* --- NAVBAR --- */
        .navbar-custom {
            background: var(--bg-card);
            border-radius: 16px;
            padding: 0.8rem 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 20px var(--shadow-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.3s ease;
        }

        .navbar-brand-text {
            line-height: 1.1;
        }

        .brand-title {
            color: var(--brand-red);
            font-weight: 800;
            letter-spacing: 0.5px;
            font-size: 1.25rem;
        }

        .brand-subtitle {
            color: var(--text-muted);
            font-size: 0.75rem;
            font-weight: 500;
        }

        /* Widget Jam */
        .clock-widget {
            background: var(--bg-input);
            border: 1px solid var(--border-color);
            border-radius: 50px;
            padding: 8px 25px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--text-main);
            font-variant-numeric: tabular-nums;
            transition: all 0.3s ease;
        }

        /* User & Theme Toggle */
        .user-pill {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 6px 6px 6px 15px;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 50px;
            transition: all 0.2s;
        }

        .user-pill:hover {
            border-color: var(--brand-red);
            box-shadow: 0 2px 10px rgba(154, 27, 31, 0.1);
        }

        .user-avatar {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, var(--brand-red), #9A1B1F);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1rem;
        }

        .theme-toggle-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 1px solid var(--border-color);
            background: var(--bg-card);
            color: var(--text-main);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .theme-toggle-btn:hover {
            background: var(--bg-input);
            transform: rotate(15deg);
        }

        /* --- PRODUCT CARD --- */
        .product-card {
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            overflow: hidden;
            background: var(--bg-card);
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px var(--shadow-color) !important;
            border-color: rgba(154, 27, 31, 0.3);
        }

        .admin-actions {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 10;
            opacity: 0;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }

        .product-card:hover .admin-actions {
            opacity: 1;
            transform: translateY(0);
        }

        .btn-action-icon {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            margin-left: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(4px);
        }

        /* Layout & Scroll */
        .scroll-area-products {
            height: 75vh;
            overflow-y: auto;
            padding-right: 5px;
        }

        .scroll-area-cart {
            height: 42vh;
            overflow-y: auto;
        }

        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #bbb;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #999;
        }

        .price-tag {
            color: var(--brand-red);
            font-weight: 700;
            font-size: 1.1rem;
        }

        .form-control:focus {
            border-color: var(--brand-red);
            box-shadow: 0 0 0 0.25rem rgba(154, 27, 31, 0.15);
        }

        body.dark-mode #modalEditBarang .modal-header {
            color: #212529 !important;
            /* hitam Bootstrap */
        }

        body.dark-mode #modalEditBarang .modal-header .modal-title {
            color: #212529 !important;
        }

        body.dark-mode #modalEditBarang .btn-close {
            filter: none !important;
            /* biar icon close tetap normal */
        }

        body.dark-mode #search::placeholder {
            color: #ffffff !important;
            opacity: 0.9 !important;
        }

        body.dark-mode #search:-ms-input-placeholder {
            color: #ffffff !important;
        }

        body.dark-mode #search::-ms-input-placeholder {
            color: #ffffff !important;
        }

        body.dark-mode #input-bayar {
            color: #ffffff !important;
            background-color: var(--bg-input);
        }

        body.dark-mode #input-bayar::placeholder {
            color: rgba(255, 255, 255, 0.7) !important;
        }

        body.dark-mode #customer-name::placeholder {
            color: #888888 !important;
            background-color: var(--bg-input);
        }

        /* Tombol Aksi Utama */
        .btn-primary-action {
            background: var(--brand-red);
            border: none;
            color: #fff !important;
            font-weight: 700;
            letter-spacing: 0.3px;
            transition:
                transform 0.18s ease,
                box-shadow 0.18s ease;
        }

        .btn-primary-action i {
            color: inherit;
        }

        /* Hover – timbul tanpa glow */
        .btn-primary-action:hover,
        .btn-primary-action:focus {
            color: #fff !important;
            transform: translateY(-3px);
            box-shadow: 0 10px 22px rgba(0, 0, 0, 0.35);
            background: var(--brand-red);
        }

        /* Active – ditekan */
        .btn-primary-action:active {
            color: #fff !important;
            transform: translateY(0);
            box-shadow: 0 5px 12px rgba(0, 0, 0, 0.4);
        }

        /* ===== MODAL TAMBAH BARANG – BRAND STYLE ===== */
        #modalTambahBarang .modal-content {
            background: var(--modal-bg);
            border-radius: 18px;
        }

        /* Header */
        #modalTambahBarang .modal-header {
            background: linear-gradient(135deg,
                    var(--brand-red),
                    #7f1417);
            border: none;
        }

        #modalTambahBarang .modal-title {
            color: #fff;
            letter-spacing: 0.3px;
        }

        /* Close button */
        #modalTambahBarang .btn-close {
            filter: invert(1) brightness(200%);
        }

        /* Input */
        #modalTambahBarang .form-control {
            background: var(--bg-input);
            color: var(--text-main);
            border: 1px solid var(--border-color);
            border-radius: 12px;
        }

        #modalTambahBarang .form-control:focus {
            border-color: var(--brand-red);
            box-shadow: 0 0 0 0.2rem rgba(154, 27, 31, 0.25);
        }

        /* Footer button */
        #modalTambahBarang .modal-footer .btn {
            background: var(--brand-red);
            border: none;
            color: #fff;
            font-weight: 700;
            letter-spacing: 0.4px;
            transition: all 0.2s ease;
        }

        #modalTambahBarang .modal-footer .btn:hover {
            background: #7f1417;
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.35);
        }
    </style>

    <div class="container-fluid px-4 py-3">
        <div class="navbar-custom">
            <div class="d-flex align-items-center">
                <img src="{{ asset('logo.png') }}" alt="Logo" style="height: 42px; width: auto; margin-right: 15px;">
                <div class="d-flex flex-column navbar-brand-text">
                    <span class="brand-title">SUMBER BANGUNAN</span>
                    <span class="brand-subtitle"><i class="bi bi-bricks me-1"></i>Material & Konstruksi</span>
                </div>
            </div>

            <div class="d-none d-lg-flex clock-widget shadow-sm">
                <i class="bi bi-clock text-danger"></i>
                <div class="d-flex flex-column align-items-start" style="line-height: 1.2;">
                    <span id="digital-clock" class="fw-bold fs-5">00:00</span>
                    <small id="date-text" class="text-muted" style="font-size: 0.7rem;">...</small>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">

                <!-- Tombol Update Status Online -->
                <button class="btn btn-sm btn-outline-success shadow-sm" onclick="updateSemuaStatusOnline()"
                    title="Update Status Pembayaran Online" style="border-radius: 50px; padding: 8px 20px;">
                    <i class="bi bi-arrow-repeat"></i>
                    <span class="d-none d-md-inline ms-1">Sync Payment</span>
                </button>

                <button class="theme-toggle-btn shadow-sm" id="theme-toggle" title="Ganti Mode (Terang/Gelap)">
                    <i class="bi bi-moon-stars-fill" id="theme-icon"></i>
                </button>

                <div class="user-pill shadow-sm">
                    <div class="text-end lh-1">
                        <div class="fw-bold" style="font-size: 0.9rem; color: var(--text-main);">{{ Auth::user()->name }}
                        </div>
                        <small class="text-uppercase text-muted"
                            style="font-size: 0.65rem; letter-spacing: 0.5px;">{{ Auth::user()->role }}</small>
                    </div>
                    <div class="user-avatar shadow-sm">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                </div>

                <a href="{{ route('logout') }}"
                    class="btn btn-light text-danger btn-sm rounded-circle shadow-sm d-flex align-items-center justify-content-center"
                    style="width: 40px; height: 40px; background: var(--bg-card); border: 1px solid var(--border-color);"
                    title="Logout">
                    <i class="bi bi-power fs-5"></i>
                </a>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8 col-xl-9">
                <div class="card shadow-sm border-0 h-100 rounded-4 overflow-hidden">
                    <div
                        class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                        <div class="input-group shadow-sm" style="max-width: 400px;">
                            <span class="input-group-text bg-white border-end-0 ps-3"><i
                                    class="bi bi-search text-muted"></i></span>
                            <input type="text" id="search" class="form-control border-start-0 py-2"
                                placeholder="Cari nama barang..." onkeyup="filterProduk()">
                        </div>
                        @if (Auth::user()->role == 'admin')
                            <button type="button" class="btn btn-primary-action rounded-pill px-4 shadow-sm"
                                data-bs-toggle="modal" data-bs-target="#modalTambahBarang">
                                <i class="bi bi-plus-lg me-2"></i>Barang Baru
                            </button>
                        @endif
                    </div>

                    <div class="card-body bg-light scroll-area-products p-4">
                        <div class="row g-3" id="product-list">
                            @foreach ($products as $p)
                                <div class="col-6 col-md-4 col-xl-3 product-item" data-name="{{ strtolower($p->nama) }}"
                                    data-kode="{{ strtolower($p->kode) }}">
                                    <div class="card product-card h-100 position-relative"
                                        onclick="addToCart({{ $p->id }}, '{{ $p->nama }}', {{ $p->harga }})"
                                        style="cursor: pointer;">

                                        @if (Auth::user()->role == 'admin')
                                            <div class="admin-actions">
                                                <button class="btn btn-action-icon text-primary"
                                                    onclick="editProduk(event, {{ $p->id }}, '{{ $p->kode }}', '{{ $p->nama }}', {{ $p->harga }}, {{ $p->stok }})">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </button>
                                                <button class="btn btn-action-icon text-danger"
                                                    onclick="hapusProduk(event, {{ $p->id }}, '{{ $p->nama }}')">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </div>
                                        @endif

                                        <div class="card-img-top bg-white d-flex align-items-center justify-content-center p-3"
                                            style="height: 130px;">
                                            @if (!empty($p->image_url))
                                                <img src="{{ $p->image_url }}" class="w-100 h-100"
                                                    style="object-fit: contain;"
                                                    onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\'bi bi-box-seam text-secondary opacity-25\' style=\'font-size: 3rem;\'></i>';">
                                            @else
                                                <i class="bi bi-box-seam text-secondary opacity-25"
                                                    style="font-size: 3rem;"></i>
                                            @endif
                                        </div>

                                        <div class="card-body p-3 text-center border-top">
                                            <h6 class="fw-bold text-dark mb-1 text-truncate">{{ $p->nama }}</h6>
                                            <div class="d-flex justify-content-center gap-2 mb-2">
                                                <span class="badge bg-light text-dark border">{{ $p->kode }}</span>
                                                <span
                                                    class="badge {{ $p->stok > 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                                    Stok: {{ $p->stok }}
                                                </span>
                                            </div>
                                            <div class="price-tag">Rp {{ number_format($p->harga, 0, ',', '.') }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-xl-3">
                <div class="card shadow-sm border-0 h-100 rounded-4 d-flex flex-column overflow-hidden">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-dark"><i class="bi bi-cart3 me-2 text-danger"></i>Keranjang</span>
                        <button class="btn btn-sm text-muted hover-text-danger" onclick="resetCart()"><i
                                class="bi bi-trash"></i> Reset</button>
                    </div>

                    <div class="card-body p-0 scroll-area-cart bg-white position-relative flex-grow-1">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light sticky-top small text-muted text-uppercase">
                                <tr>
                                    <th class="ps-3 border-0 py-2">Item</th>
                                    <th class="text-center border-0 py-2">Qty</th>
                                    <th class="text-end pe-3 border-0 py-2">Total</th>
                                    <th class="border-0 py-2"></th>
                                </tr>
                            </thead>
                            <tbody id="cart-items"></tbody>
                        </table>
                        <div id="empty-cart"
                            class="text-center position-absolute top-50 start-50 translate-middle w-100 p-4">
                            <div class="bg-light rounded-circle d-inline-flex p-3 mb-3">
                                <i class="bi bi-basket2 text-muted" style="font-size: 2rem;"></i>
                            </div>
                            <p class="text-muted small fw-bold mb-0">Belum ada barang dipilih</p>
                        </div>
                    </div>

                    <div class="card-footer bg-white p-3 border-top shadow-lg" style="z-index: 20;">
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Subtotal</span>
                            <span class="fw-bold text-dark" id="label-subtotal">Rp 0</span>
                        </div>

                        <div class="input-group input-group-sm mb-2">
                            <span class="input-group-text bg-light border-end-0 text-muted fw-bold-white">Diskon</span>
                            <input type="number" id="input-diskon" class="form-control bg-light border-start-0 text-end"
                                placeholder="0" oninput="hitungTotal()">
                        </div>

                        <div id="box-admin" class="alert alert-warning py-1 px-2 d-none mb-2 border-warning"
                            style="font-size: 0.8rem;">
                            <i class="bi bi-lock-fill me-1"></i> Admin Password:
                            <input type="password" id="admin-pass" class="form-control form-control-sm mt-1"
                                placeholder="...">
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <select id="payment-method" class="form-select form-select-sm fw-bold"
                                    onchange="cekMetodeBayar()">
                                    <option value="cash">Tunai</option>
                                    <option value="online">QRIS/TF</option>
                                    <option value="utang">Utang</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <input type="text" id="customer-name" class="form-control form-control-sm"
                                    placeholder="Pelanggan">
                            </div>
                        </div>

                        <div class="p-3 bg-light rounded-3 mb-3 border border-dashed">
                            <div class="d-flex justify-content-between align-items-end mb-2">
                                <span class="small fw-bold text-uppercase text-muted">Total Bayar</span>
                                <span class="h4 fw-bold text-danger mb-0" id="label-total">Rp 0</span>
                            </div>
                            <input type="number" id="input-bayar" class="form-control text-end fw-bold"
                                placeholder="Input (Rp)">
                        </div>

                        <button onclick="prosesBayar()" class="btn btn-primary-action w-100 py-2 rounded-pill shadow-sm">
                            BAYAR SEKARANG
                        </button>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTambahBarang" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <!-- HEADER -->
                <div class="modal-header bg-danger text-white px-4">
                    <h6 class="modal-title fw-bold">Tambah Barang</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form action="{{ route('produk.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">

                        <div class="mb-3">
                            <label class="small text-muted fw-bold">Kode Barang (Barcode)</label>
                            <input type="text" name="kode" class="form-control bg-light border-0" required>
                        </div>

                        <div class="mb-3">
                            <label class="small text-muted fw-bold">Nama Barang</label>
                            <input type="text" name="nama" class="form-control bg-light border-0" required>
                        </div>

                        <div class="row g-2">
                            <div class="col-7">
                                <label class="small text-muted fw-bold">Harga Jual (Rp)</label>
                                <input type="number" name="harga" class="form-control bg-light border-0" required>
                            </div>
                            <div class="col-5">
                                <label class="small text-muted fw-bold">Stok Awal</label>
                                <input type="number" name="stok" class="form-control bg-light border-0" required>
                            </div>
                        </div>

                    </div>

                    <!-- FOOTER -->
                    <div class="modal-footer border-0 px-4 pb-4">
                        <button type="submit" class="btn btn-danger w-100 rounded-pill fw-bold">
                            Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditBarang" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header bg-warning text-dark px-4">
                    <h6 class="modal-title fw-bold">Edit Barang</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formEditBarang" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-body p-4">
                        <div class="alert alert-light border border-warning d-flex align-items-center p-2 mb-3 rounded-3">
                            <i class="bi bi-info-circle text-warning me-2"></i>
                            <small class="text-muted lh-1">Edit nama akan mereset gambar produk.</small>
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted fw-bold">Kode Barang</label>
                            <input type="text" name="kode" id="edit-kode" class="form-control bg-light border-0"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted fw-bold">Nama Barang</label>
                            <input type="text" name="nama" id="edit-nama" class="form-control bg-light border-0"
                                required>
                        </div>
                        <div class="row g-2">
                            <div class="col-7">
                                <label class="small text-muted fw-bold">Harga</label>
                                <input type="number" name="harga" id="edit-harga"
                                    class="form-control bg-light border-0" required>
                            </div>
                            <div class="col-5">
                                <label class="small text-muted fw-bold">Stok</label>
                                <input type="number" name="stok" id="edit-stok"
                                    class="form-control bg-light border-0" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4">
                        <button type="submit" class="btn btn-warning w-100 rounded-pill fw-bold">Update Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <form id="formDeleteBarang" method="POST" class="d-none"> @csrf @method('DELETE') </form>
    <div id="flash-data" data-login-success="{{ session('login_success') }}" data-success="{{ session('success') }}"
        data-error="{{ session('error') }}"></div>

    <script>
        // --- JAM DIGITAL ---
        function updateClock() {
            const now = new Date();
            document.getElementById('digital-clock').innerText = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit'
            });
            document.getElementById('date-text').innerText = now.toLocaleDateString('id-ID', {
                weekday: 'long',
                day: 'numeric',
                month: 'short'
            });
        }
        setInterval(updateClock, 1000);
        updateClock();

        // --- THEME / DARK MODE LOGIC ---
        const themeBtn = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-icon');
        const body = document.body;

        // Cek Local Storage saat Load
        if (localStorage.getItem('theme') === 'dark') {
            body.classList.add('dark-mode');
            themeIcon.classList.replace('bi-moon-stars-fill', 'bi-sun-fill');
        }

        // Toggle Click Handler
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

        // --- CART LOGIC ---
        let cart = [];
        let subtotal = 0;
        const userRole = "{{ Auth::user()->role ?? 'kasir' }}";

        function addToCart(id, name, price) {
            let existingItem = cart.find(item => item.id === id);
            if (existingItem) {
                existingItem.qty++;
                showToast('success', `+1 ${name}`);
            } else {
                cart.push({
                    id,
                    name,
                    price,
                    qty: 1
                });
                showToast('success', `${name} ditambahkan`);
            }
            renderCart();
        }

        function renderCart() {
            let tbody = document.getElementById('cart-items');
            tbody.innerHTML = '';
            subtotal = 0;
            document.getElementById('empty-cart').classList.toggle('d-none', cart.length > 0);

            cart.forEach((item, index) => {
                let totalItem = item.price * item.qty;
                subtotal += totalItem;
                tbody.innerHTML += `
                <tr>
                    <td class="ps-3 border-0"><div class="fw-bold text-truncate" style="max-width:120px">${item.name}</div><small class="text-muted">@ ${item.price.toLocaleString('id-ID')}</small></td>
                    <td class="border-0"><input type="number" class="form-control form-control-sm text-center bg-light border-0" value="${item.qty}" onchange="updateQty(${index}, this.value)" min="1"></td>
                    <td class="text-end pe-3 border-0 fw-bold">${totalItem.toLocaleString('id-ID')}</td>
                    <td class="text-end pe-2 border-0"><button class="btn btn-sm text-danger p-0" onclick="hapusItem(${index})"><i class="bi bi-x-circle-fill"></i></button></td>
                </tr>`;
            });
            hitungTotal();
        }

        function updateQty(index, qty) {
            cart[index].qty = Math.max(1, parseInt(qty));
            renderCart();
        }

        function hapusItem(index) {
            cart.splice(index, 1);
            renderCart();
        }

        function resetCart() {
            if (cart.length === 0) return;
            Swal.fire({
                    title: 'Hapus Keranjang?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Batal'
                })
                .then((r) => {
                    if (r.isConfirmed) {
                        cart = [];
                        document.getElementById('input-diskon').value = '';
                        renderCart();
                    }
                });
        }

        function hitungTotal() {
            let diskon = parseInt(document.getElementById('input-diskon').value) || 0;
            let grandTotal = Math.max(0, subtotal - diskon);
            document.getElementById('label-subtotal').innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
            document.getElementById('label-total').innerText = 'Rp ' + grandTotal.toLocaleString('id-ID');

            let boxAdmin = document.getElementById('box-admin');
            (userRole !== 'admin' && diskon > 50000) ? boxAdmin.classList.remove('d-none'): boxAdmin.classList.add(
                'd-none');
        }

        function filterProduk() {
            let key = document.getElementById('search').value.toLowerCase();
            document.querySelectorAll('.product-item').forEach(item => {
                item.classList.toggle('d-none', !(item.dataset.name.includes(key) || item.dataset.kode.includes(
                    key)));
            });
        }

        function cekMetodeBayar() {
            let method = document.getElementById('payment-method').value;
            let inputBayar = document.getElementById('input-bayar');
            let inputCust = document.getElementById('customer-name');
            if (method === 'utang') {
                inputCust.placeholder = "Wajib Isi Nama";
                inputBayar.value = 0;
                inputBayar.disabled = true;
            } else {
                inputCust.placeholder = "Pelanggan";
                inputBayar.disabled = false;
            }
        }

        function prosesBayar() {
            if (cart.length === 0) return showToast('error', 'Keranjang kosong');
            let diskon = parseInt(document.getElementById('input-diskon').value) || 0;
            let grandTotal = Math.max(0, subtotal - diskon);
            let bayar = parseInt(document.getElementById('input-bayar').value) || 0;
            let method = document.getElementById('payment-method').value;
            let cust = document.getElementById('customer-name').value.trim();
            let adminPass = document.getElementById('admin-pass').value;

            if (method === 'utang' && cust === '') return Swal.fire('Nama Wajib', 'Isi nama untuk utang', 'warning');
            if (method !== 'utang' && bayar < grandTotal) return Swal.fire('Uang Kurang',
                `Kurang Rp ${(grandTotal - bayar).toLocaleString('id-ID')}`, 'error');

            Swal.fire({
                    title: 'Proses Bayar?',
                    text: `Total: Rp ${grandTotal.toLocaleString('id-ID')}`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#9A1B1F',
                    confirmButtonText: 'Bayar'
                })
                .then((result) => {
                    if (result.isConfirmed) {
                        Swal.showLoading();
                        fetch('{{ route('transaksi.bayar') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                cart,
                                diskon,
                                bayar,
                                payment_method: method,
                                customer_name: cust,
                                admin_password: adminPass
                            })
                        }).then(res => res.json()).then(data => {
                            if (data.status === 'success') {
                                // JIKA METODE PEMBAYARAN ONLINE (XENDIT)
                                if (data.invoice_url) {
                                    // Redirect browser ke halaman pembayaran Xendit
                                    window.location.href = data.invoice_url;
                                }
                                // JIKA TUNAI / UTANG
                                else {
                                    Swal.fire('Berhasil', 'Transaksi Disimpan', 'success').then(() =>
                                        cetakStruk(data.sale_id));
                                }
                            } else {
                                Swal.fire('Gagal', data.msg, 'error');
                            }
                        }).catch(() => Swal.fire('Error', 'Koneksi Gagal', 'error'));
                    }
                });
        }

        function cetakStruk(id) {
            window.open(`/transaksi/struk/${id}`, '_blank');
            setTimeout(() => location.reload(), 1000);
        }

        function updateSemuaStatusOnline() {
            Swal.fire({
                title: 'Update Status Pembayaran?',
                text: 'Akan mengecek semua transaksi online yang belum lunas',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Update',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Mengecek status dari Xendit',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch('/transaksi/update-semua-online')
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'success') {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    html: `
                                        <p>Total Dicek: <strong>${data.total_checked}</strong></p>
                                        <p>Berhasil Diupdate: <strong>${data.updated}</strong></p>
                                    `,
                                    icon: 'success',
                                    confirmButtonColor: '#28a745'
                                }).then(() => {
                                    if (data.updated > 0) {
                                        location.reload();
                                    }
                                });
                            } else {
                                Swal.fire('Gagal', data.message, 'error');
                            }
                        })
                        .catch(() => {
                            Swal.fire('Error', 'Koneksi gagal', 'error');
                        });
                }
            });
        }

        function editProduk(e, id, kode, nama, harga, stok) {
            e.stopPropagation();
            document.getElementById('edit-kode').value = kode;
            document.getElementById('edit-nama').value = nama;
            document.getElementById('edit-harga').value = harga;
            document.getElementById('edit-stok').value = stok;
            document.getElementById('formEditBarang').action = `/produk/update/${id}`;
            new bootstrap.Modal(document.getElementById('modalEditBarang')).show();
        }

        function hapusProduk(e, id, nama) {
            e.stopPropagation();
            Swal.fire({
                    title: 'Hapus?',
                    text: nama,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Ya'
                })
                .then((r) => {
                    if (r.isConfirmed) {
                        let f = document.getElementById('formDeleteBarang');
                        f.action = `/produk/hapus/${id}`;
                        f.submit();
                    }
                });
        }

        function showToast(icon, title) {
            Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1500,
                timerProgressBar: true
            }).fire({
                icon,
                title
            });
        }
        document.addEventListener('DOMContentLoaded', () => {
            const f = document.getElementById('flash-data');
            if (f.dataset.success) Swal.fire({
                title: 'Berhasil',
                text: f.dataset.success,
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
            if (f.dataset.error) Swal.fire('Gagal', f.dataset.error, 'error');

            // CEK PARAMETER URL UNTUK AUTO CETAK STRUK SETELAH PEMBAYARAN ONLINE
            const urlParams = new URLSearchParams(window.location.search);
            const saleId = urlParams.get('sale_id');
            const paymentSuccess = urlParams.get('payment_success');
            const paymentFailed = urlParams.get('payment_failed');

            if (paymentSuccess && saleId) {
                // Tampilkan loading
                Swal.fire({
                    title: 'Memeriksa Status Pembayaran...',
                    text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Cek status pembayaran dari Xendit
                fetch(`/transaksi/cek-status/${saleId}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            if (data.payment_status === 'lunas') {
                                Swal.fire({
                                    title: 'Pembayaran Berhasil!',
                                    text: 'Transaksi telah lunas',
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    // Cetak struk otomatis
                                    cetakStruk(saleId);
                                    // Bersihkan URL parameter
                                    window.history.replaceState({}, document.title, window.location
                                        .pathname);
                                });
                            } else if (data.payment_status === 'batal') {
                                Swal.fire({
                                    title: 'Pembayaran Dibatalkan',
                                    text: 'Invoice sudah expired atau dibatalkan',
                                    icon: 'warning'
                                }).then(() => {
                                    window.history.replaceState({}, document.title, window.location
                                        .pathname);
                                });
                            } else {
                                // Masih pending
                                Swal.fire({
                                    title: 'Pembayaran Belum Selesai',
                                    text: 'Silakan selesaikan pembayaran terlebih dahulu',
                                    icon: 'info'
                                }).then(() => {
                                    window.history.replaceState({}, document.title, window.location
                                        .pathname);
                                });
                            }
                        } else {
                            Swal.fire('Error', 'Gagal memeriksa status pembayaran', 'error');
                        }
                    })
                    .catch(() => {
                        Swal.fire('Error', 'Koneksi gagal', 'error');
                    });
            } else if (paymentFailed) {
                Swal.fire({
                    title: 'Pembayaran Gagal',
                    text: 'Silakan coba lagi',
                    icon: 'error'
                }).then(() => {
                    // Bersihkan URL parameter
                    window.history.replaceState({}, document.title, window.location.pathname);
                });
            }
        });
    </script>
@endsection
