@extends('layouts.app')

@section('content')
    <nav class="navbar navbar-expand-lg navbar-dark bg-brand shadow-sm sticky-top mb-3">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="#">
                <img src="{{ asset('logo.png') }}" alt="Logo" class="navbar-logo">
                <div class="d-flex flex-column lh-1"> <span class="fs-5 text-uppercase tracking-wide">SUMBER BANGUNAN</span>
                    <small class="text-brand-gold fs-xs">Material & Konstruksi</small>
                </div>
            </a>

            <div class="d-flex align-items-center justify-content-center flex-grow-1">
                <div class="text-white text-center d-none d-md-block">
                    <div id="digital-clock" class="fw-bold fs-4">00:00:00</div>
                    <small id="date-text" class="text-white-50 small">...</small>
                </div>
            </div>

            <div class="d-flex text-white align-items-center">
                <div class="text-end me-3 d-none d-md-block">
                    <span class="d-block fw-bold">{{ Auth::user()->name }}</span>
                    <small class="badge bg-warning text-dark">{{ ucfirst(Auth::user()->role) }}</small>
                </div>
                <form action="/logout" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-light fw-bold">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4">
        <div class="row">
            <div class="col-md-7 mb-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white py-3">
                        <div class="input-group mb-3">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                            <input type="text" id="search-input" class="form-control border-start-0"
                                placeholder="Cari nama barang atau kode...">
                        </div>
                        @if (Auth::user()->role == 'admin')
                            <button type="button" class="btn btn-warning fw-bold w-100 shadow-sm" data-bs-toggle="modal"
                                data-bs-target="#modalTambahBarang">
                                <i class="bi bi-plus-circle-fill me-1"></i> Tambah Barang
                            </button>
                        @endif
                    </div>

                    <div class="card-body bg-light scroll-area-products p-3">
                        <div class="row g-3" id="product-list">
                            @foreach ($products as $p)
                                <div class="col-md-4 col-sm-6 product-item" data-id="{{ $p->id }}"
                                    data-name="{{ $p->nama }}" data-price="{{ $p->harga }}"
                                    data-stock="{{ $p->stok }}" data-code="{{ strtolower($p->kode) }}"
                                    data-name-lower="{{ strtolower($p->nama) }}">

                                    <div class="card product-card h-100 border-0 shadow-sm" style="cursor: pointer;">
                                        @if (Auth::user()->role == 'admin')
                                            <div class="product-actions">
                                                <button class="btn btn-sm btn-primary btn-action btn-edit-barang">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger btn-action btn-delete-barang">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </div>
                                        @endif

                                        <div class="btn-add-cart h-100">
                                            <div class="card-img-top bg-white d-flex align-items-center justify-content-center position-relative"
                                                style="height: 120px; overflow: hidden;">
                                                @if (!empty($p->image_url))
                                                    <img src="{{ $p->image_url }}" class="w-100 h-100 object-fit-contain">
                                                @else
                                                    <i class="bi bi-box-seam text-secondary" style="font-size: 3rem;"></i>
                                                @endif
                                            </div>
                                            <div class="card-body p-2 text-center">
                                                <h6 class="fw-bold text-dark mb-1 text-truncate">{{ $p->nama }}</h6>
                                                <small class="text-muted d-block mb-1">{{ $p->kode }}</small>
                                                <small class="text-muted d-block mb-2">Stok: {{ $p->stok }}</small>
                                                <span class="badge bg-success fs-6">Rp
                                                    {{ number_format($p->harga, 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-5 mb-3">
                <div class="card shadow-sm border-0 h-100">
                    <div
                        class="card-header bg-white fw-bold py-3 d-flex justify-content-between align-items-center border-bottom">
                        <span><i class="bi bi-cart-fill me-2"></i> Keranjang</span>
                        <button class="btn btn-sm btn-outline-danger" id="btn-reset-cart">
                            <i class="bi bi-trash"></i> Reset
                        </button>
                    </div>

                    <div class="card-body p-0 scroll-area-cart bg-white position-relative">
                        <table class="table table-striped mb-0 table-hover w-100">
                            <thead class="table-light sticky-top" style="top: 0; z-index: 10;">
                                <tr>
                                    <th class="ps-3">Barang</th>
                                    <th width="20%" class="text-center">Qty</th>
                                    <th class="text-end pe-3">Total</th>
                                    <th width="5%"></th>
                                </tr>
                            </thead>
                            <tbody id="cart-items"></tbody>
                        </table>
                        <div id="empty-cart"
                            class="text-center py-5 text-muted position-absolute top-50 start-50 translate-middle w-100">
                            <i class="bi bi-basket display-1 text-light mb-3"></i>
                            <p class="mb-0 fw-bold">Keranjang Kosong</p>
                        </div>
                    </div>

                    <div class="card-footer bg-white p-3 border-top shadow-lg" style="z-index: 20;">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal</span>
                            <span class="fw-bold fs-5" id="label-subtotal">Rp 0</span>
                        </div>
                        <div class="input-group input-group-sm mb-2">
                            <span class="input-group-text bg-white">Diskon</span>
                            <input type="number" id="input-diskon" class="form-control text-end fw-bold text-danger"
                                value="0">
                        </div>

                        <div id="box-admin" class="alert alert-warning p-2 d-none mb-2 border-warning shadow-sm">
                            <small class="fw-bold text-danger d-block mb-1"><i class="bi bi-lock-fill"></i> Butuh Approval
                                Admin:</small>
                            <input type="password" id="admin-pass" class="form-control form-control-sm"
                                placeholder="Password Admin...">
                        </div>

                        <hr class="my-2">

                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <select id="payment-method" class="form-select form-select-sm">
                                    <option value="cash">💵 Cash</option>
                                    <option value="online">💳 QRIS / Transfer</option>
                                    <option value="utang">📝 Utang</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <input type="text" id="customer-name" class="form-control form-control-sm"
                                    placeholder="Pelanggan (Umum)">
                            </div>
                        </div>

                        <div class="input-group mb-3 shadow-sm">
                            <span class="input-group-text bg-success text-white fw-bold">Bayar</span>
                            <input type="number" id="input-bayar" class="form-control text-end fw-bold fs-5"
                                placeholder="0">
                        </div>

                        <div class="d-flex justify-content-between mb-3 p-2 bg-light rounded border">
                            <span class="h5 fw-bold text-dark mb-0 align-self-center">TOTAL</span>
                            <span class="h3 fw-bold text-danger mb-0" id="label-total">Rp 0</span>
                        </div>

                        <button id="btn-process-pay"
                            class="btn btn-brand w-100 py-3 fw-bold text-uppercase shadow-sm hover-shadow">
                            <i class="bi bi-cash-coin me-2"></i> Proses Pembayaran
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('pos.partials.modals')

    <div id="flash-data" data-login-success="{{ session('login_success') }}" data-success="{{ session('success') }}"
        data-error="{{ session('error') }}">
    </div>
@endsection

@push('scripts')
    @vite('resources/js/pos.js')
@endpush
