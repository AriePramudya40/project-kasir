@extends('layouts.app')
@section('content')
    <style>
        :root {
            --brand-red: #9A1B1F;
            --brand-gold: #FFD700;
            --brand-dark: #2c3e50;
        }

        body {
            background-color: #f8f9fa;
        }

        /* --- Navbar & Brand --- */
        .bg-brand {
            background: linear-gradient(135deg, var(--brand-red) 0%, #7a1518 100%);
            border-bottom: 4px solid var(--brand-gold);
        }

        .navbar-logo {
            height: 45px;
            width: auto;
            margin-right: 12px;
            filter: drop-shadow(0px 2px 2px rgba(0, 0, 0, 0.3));
        }

        /* --- Product Card --- */
        .product-card {
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 12px;
            overflow: hidden;
            background: #fff;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.12) !important;
            border-color: var(--brand-gold);
        }

        /* Tombol Admin (Edit/Delete) Floating */
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
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            margin-left: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        /* --- Layout Areas --- */
        .scroll-area-products {
            height: 78vh;
            overflow-y: auto;
            padding-right: 5px;
        }

        .scroll-area-cart {
            height: 45vh;
            overflow-y: auto;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #aaa;
        }

        /* --- Typography --- */
        #digital-clock {
            font-family: 'Courier New', monospace;
            letter-spacing: 1px;
        }

        .price-tag {
            color: var(--brand-red);
            font-weight: 800;
        }

        /* --- Modern Form Styles --- */
        .form-floating>label {
            color: #6c757d;
        }

        .input-group-text {
            background-color: #fff;
            border-right: 0;
            color: var(--brand-red);
        }

        .form-control:focus {
            border-color: var(--brand-red);
            box-shadow: 0 0 0 0.25rem rgba(154, 27, 31, 0.15);
        }

        .border-left-brand {
            border-left: 1px solid #ced4da !important;
        }
    </style>

    <nav class="navbar navbar-expand-lg navbar-dark bg-brand shadow-sm sticky-top mb-4">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="#">
                <img src="{{ asset('logo.png') }}" alt="Logo" class="navbar-logo">
                <div class="d-flex flex-column" style="line-height: 1.2;">
                    <span class="fs-5 text-uppercase" style="letter-spacing: 1px;">SUMBER BANGUNAN</span>
                    <small style="font-size: 0.7rem; color: var(--brand-gold); opacity: 0.9;">Material & Konstruksi
                        Terpercaya</small>
                </div>
            </a>

            <div class="d-flex align-items-center justify-content-center flex-grow-1">
                <div class="text-white text-center d-none d-lg-block bg-white bg-opacity-10 rounded px-3 py-1">
                    <div id="digital-clock" class="fw-bold fs-5">00:00:00</div>
                    <small id="date-text" class="text-white-50" style="font-size: 0.75rem;">...</small>
                </div>
            </div>

            <div class="d-flex text-white align-items-center gap-3">
                <div class="text-end d-none d-md-block">
                    <span class="d-block fw-bold" style="font-size: 0.9rem;">{{ Auth::user()->name }}</span>
                    <span class="badge bg-warning text-dark shadow-sm"
                        style="font-size: 0.7rem;">{{ strtoupper(Auth::user()->role) }}</span>
                </div>
                <div class="vr bg-white opacity-50"></div>
                <a href="{{ route('logout') }}"
                    class="btn btn-sm btn-light text-danger fw-bold shadow-sm rounded-pill px-3">
                    <i class="bi bi-box-arrow-right me-1"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4">
        <div class="row g-4">
            <div class="col-lg-7 col-xl-8">
                <div class="card shadow-sm border-0 h-100 rounded-3">
                    <div
                        class="card-header bg-white py-3 border-bottom-0 d-flex justify-content-between align-items-center">
                        <div class="input-group shadow-sm w-50">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i
                                    class="bi bi-search"></i></span>
                            <input type="text" id="search" class="form-control border-start-0 ps-0"
                                placeholder="Cari nama barang atau kode..." onkeyup="filterProduk()">
                        </div>
                        @if (Auth::user()->role == 'admin')
                            <button type="button" class="btn btn-primary fw-bold shadow-sm rounded-pill px-3"
                                style="background-color: var(--brand-red); border:none;" data-bs-toggle="modal"
                                data-bs-target="#modalTambahBarang">
                                <i class="bi bi-plus-lg me-1"></i> Tambah Barang
                            </button>
                        @endif
                    </div>

                    <div class="card-body bg-light scroll-area-products p-3 rounded-bottom">
                        <div class="row g-3" id="product-list">
                            @foreach ($products as $p)
                                <div class="col-6 col-md-4 col-xl-3 product-item" data-name="{{ strtolower($p->nama) }}"
                                    data-kode="{{ strtolower($p->kode) }}">
                                    <div class="card product-card h-100"
                                        onclick="addToCart({{ $p->id }}, '{{ $p->nama }}', {{ $p->harga }})"
                                        style="cursor: pointer;">

                                        {{-- Admin Actions --}}
                                        @if (Auth::user()->role == 'admin')
                                            <div class="admin-actions">
                                                <button class="btn btn-light btn-action-icon text-primary"
                                                    onclick="editProduk(event, {{ $p->id }}, '{{ $p->kode }}', '{{ $p->nama }}', {{ $p->harga }}, {{ $p->stok }})"
                                                    title="Edit Data">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </button>
                                                <button class="btn btn-light btn-action-icon text-danger"
                                                    onclick="hapusProduk(event, {{ $p->id }}, '{{ $p->nama }}')"
                                                    title="Hapus Barang">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </div>
                                        @endif

                                        {{-- Image Area --}}
                                        <div class="card-img-top bg-white d-flex align-items-center justify-content-center p-3"
                                            style="height: 140px;">
                                            @if (!empty($p->image_url))
                                                <img src="{{ $p->image_url }}" alt="{{ $p->nama }}"
                                                    class="w-100 h-100"
                                                    style="object-fit: contain; transition: transform 0.3s;"
                                                    onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\'bi bi-box-seam text-secondary opacity-25\' style=\'font-size: 3rem;\'></i>';">
                                            @else
                                                <i class="bi bi-box-seam text-secondary opacity-25"
                                                    style="font-size: 3rem;"></i>
                                            @endif
                                        </div>

                                        {{-- Info Area --}}
                                        <div class="card-body p-3 text-center border-top">
                                            <h6 class="fw-bold text-dark mb-1 text-truncate" title="{{ $p->nama }}">
                                                {{ $p->nama }}</h6>
                                            <div class="d-flex justify-content-center align-items-center gap-2 mb-2">
                                                <span
                                                    class="badge bg-secondary bg-opacity-10 text-secondary border">{{ $p->kode }}</span>
                                                <span
                                                    class="badge {{ $p->stok > 0 ? 'bg-success' : 'bg-danger' }} bg-opacity-10 {{ $p->stok > 0 ? 'text-success' : 'text-danger' }} border">Stok:
                                                    {{ $p->stok }}</span>
                                            </div>
                                            <span class="d-block price-tag fs-5">Rp
                                                {{ number_format($p->harga, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 col-xl-4">
                <div class="card shadow-sm border-0 h-100 rounded-3 d-flex flex-column">
                    <div
                        class="card-header bg-white fw-bold py-3 d-flex justify-content-between align-items-center border-bottom">
                        <span class="text-uppercase text-muted small letter-spacing-1"><i
                                class="bi bi-basket2-fill me-2 text-warning"></i>Keranjang Belanja</span>
                        <button class="btn btn-sm btn-outline-danger border-0" onclick="resetCart()"><i
                                class="bi bi-trash"></i> Bersihkan</button>
                    </div>

                    <div class="card-body p-0 scroll-area-cart bg-white position-relative flex-grow-1">
                        <table class="table table-hover w-100 align-middle mb-0">
                            <thead class="table-light sticky-top" style="top: 0; z-index: 5;">
                                <tr class="text-muted small text-uppercase">
                                    <th class="ps-3 border-bottom-0">Item</th>
                                    <th width="20%" class="text-center border-bottom-0">Qty</th>
                                    <th class="text-end pe-3 border-bottom-0">Total</th>
                                    <th width="5%" class="border-bottom-0"></th>
                                </tr>
                            </thead>
                            <tbody id="cart-items"></tbody>
                        </table>

                        {{-- Empty State --}}
                        <div id="empty-cart" class="text-center position-absolute top-50 start-50 translate-middle w-100">
                            <img src="https://cdn-icons-png.flaticon.com/512/2038/2038854.png" width="80"
                                class="opacity-25 mb-3" alt="Empty">
                            <p class="text-muted small fw-bold">Keranjang belanja masih kosong</p>
                        </div>
                    </div>

                    {{-- Footer Cart --}}
                    <div class="card-footer bg-white p-4 border-top shadow-lg rounded-bottom" style="z-index: 10;">

                        {{-- Kalkulasi --}}
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Subtotal</span>
                            <span class="fw-bold" id="label-subtotal">Rp 0</span>
                        </div>

                        <div class="input-group input-group-sm mb-3">
                            <span class="input-group-text bg-white text-danger border-end-0"><i
                                    class="bi bi-tag-fill"></i></span>
                            <input type="number" id="input-diskon" class="form-control border-start-0"
                                placeholder="Diskon (Rp)" oninput="hitungTotal()">
                        </div>

                        {{-- Admin Auth for Discount --}}
                        <div id="box-admin"
                            class="alert alert-warning py-2 px-3 d-none mb-3 border-start border-4 border-warning shadow-sm">
                            <div class="d-flex align-items-center mb-1">
                                <i class="bi bi-shield-lock-fill text-warning me-2"></i>
                                <small class="fw-bold text-dark">Otorisasi Admin Diperlukan</small>
                            </div>
                            <input type="password" id="admin-pass" class="form-control form-control-sm"
                                placeholder="Masukkan Password Admin...">
                        </div>

                        {{-- Input Customer & Payment --}}
                        <div class="row g-2 mb-3">
                            <div class="col-5">
                                <select id="payment-method" class="form-select form-select-sm fw-bold text-dark"
                                    style="background-color: #f8f9fa;" onchange="cekMetodeBayar()">
                                    <option value="cash">💵 Cash</option>
                                    <option value="online">💳 Transfer/QR</option>
                                    <option value="utang">📝 Utang</option>
                                </select>
                            </div>
                            <div class="col-7">
                                <input type="text" id="customer-name" class="form-control form-control-sm"
                                    placeholder="Nama Pelanggan (Opsional)">
                            </div>
                        </div>

                        {{-- Grand Total & Pay --}}
                        <div
                            class="d-flex justify-content-between align-items-end mb-3 p-3 bg-light rounded-3 border border-dashed">
                            <div>
                                <small class="text-muted d-block text-uppercase" style="font-size: 0.7rem;">Total
                                    Pembayaran</small>
                                <span class="h2 fw-bold text-dark mb-0" id="label-total" style="letter-spacing: -1px;">Rp
                                    0</span>
                            </div>
                            <div class="w-50">
                                <label class="small text-muted mb-1 d-block text-end">Uang Diterima</label>
                                <input type="number" id="input-bayar"
                                    class="form-control form-control-lg text-end fw-bold text-success border-success"
                                    placeholder="0">
                            </div>
                        </div>

                        <button onclick="prosesBayar()" class="btn w-100 py-3 fw-bold text-uppercase shadow text-white"
                            style="background: linear-gradient(45deg, var(--brand-red), #c0392b); border-radius: 12px; transition: transform 0.2s;">
                            <i class="bi bi-wallet2 me-2"></i> Proses Transaksi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTambahBarang" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header text-white px-4 py-3"
                    style="background: linear-gradient(135deg, var(--brand-red), #7a1518);">
                    <h5 class="modal-title fw-bold"><i class="bi bi-box-seam me-2"></i>Tambah Barang Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('produk.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4 bg-light">
                        <div class="card border-0 shadow-sm rounded-3 bg-white p-3 mb-3">
                            <div class="form-floating mb-3">
                                <input type="text" name="kode"
                                    class="form-control border-0 border-bottom rounded-0 bg-transparent" id="addKode"
                                    placeholder="Kode" required>
                                <label for="addKode"><i class="bi bi-upc-scan me-1"></i>Kode Barang (Barcode)</label>
                            </div>
                            <div class="form-floating">
                                <input type="text" name="nama"
                                    class="form-control border-0 border-bottom rounded-0 bg-transparent" id="addNama"
                                    placeholder="Nama" required>
                                <label for="addNama"><i class="bi bi-tag me-1"></i>Nama Produk</label>
                            </div>
                        </div>

                        <div class="row g-2">
                            <div class="col-7">
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 text-success fw-bold">Rp</span>
                                    <div class="form-floating flex-grow-1">
                                        <input type="number" name="harga" class="form-control border-left-brand"
                                            id="addHarga" placeholder="0" required>
                                        <label for="addHarga">Harga Jual</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-5">
                                <div class="form-floating">
                                    <input type="number" name="stok" class="form-control" id="addStok"
                                        placeholder="0" required>
                                    <label for="addStok">Stok Awal</label>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 text-center">
                            <small class="text-muted fst-italic"><i class="bi bi-info-circle me-1"></i>Gambar produk akan
                                dicari otomatis dari Google.</small>
                        </div>
                    </div>
                    <div class="modal-footer bg-white border-top-0 px-4 py-3">
                        <button type="button" class="btn btn-light text-muted fw-bold rounded-pill px-4"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger fw-bold rounded-pill px-4"
                            style="background-color: var(--brand-red);">
                            <i class="bi bi-save me-1"></i> Simpan Produk
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditBarang" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-0 pb-0 pt-4 px-4 bg-white">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-25 text-warning rounded-circle p-2 me-3 d-flex align-items-center justify-content-center"
                            style="width: 48px; height: 48px;">
                            <i class="bi bi-pencil-square fs-4 text-warning text-opacity-100"
                                style="color: #d35400 !important;"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0" style="letter-spacing: -0.5px;">Edit Produk
                            </h5>
                            <small class="text-muted">Perbarui informasi stok dan harga</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="formEditBarang" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">

                        <div class="alert alert-light border-start border-4 border-info shadow-sm d-flex align-items-center p-2 mb-4"
                            role="alert">
                            <i class="bi bi-info-circle-fill text-info fs-5 me-2"></i>
                            <div class="small text-muted lh-sm">
                                Jika <b>Nama Barang</b> diubah, sistem akan otomatis mencari gambar baru.
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i
                                        class="bi bi-upc text-secondary"></i></span>
                                <div class="form-floating flex-grow-1">
                                    <input type="text" name="kode" id="edit-kode"
                                        class="form-control border-start-0 bg-light" placeholder="Kode">
                                    <label for="edit-kode">Kode Barang / Barcode</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i
                                        class="bi bi-box text-secondary"></i></span>
                                <div class="form-floating flex-grow-1">
                                    <input type="text" name="nama" id="edit-nama"
                                        class="form-control border-start-0 bg-light" placeholder="Nama">
                                    <label for="edit-nama">Nama Produk</label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-7">
                                <label class="small fw-bold text-muted ms-1 mb-1">HARGA JUAL</label>
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text bg-white text-dark fw-bold border-end-0">Rp</span>
                                    <input type="number" name="harga" id="edit-harga"
                                        class="form-control form-control-lg border-start-0 text-dark fw-bold"
                                        placeholder="0">
                                </div>
                            </div>
                            <div class="col-5">
                                <label class="small fw-bold text-muted ms-1 mb-1">STOK SAAT INI</label>
                                <div class="input-group shadow-sm">
                                    <input type="number" name="stok" id="edit-stok"
                                        class="form-control form-control-lg border-end-0 text-center fw-bold text-primary"
                                        placeholder="0">
                                    <span class="input-group-text bg-white text-muted border-start-0">Unit</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 pt-0 px-4 pb-4">
                        <button type="button" class="btn btn-light btn-lg fw-bold rounded-3 px-4 w-25"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit"
                            class="btn btn-warning btn-lg fw-bold text-white rounded-3 px-4 flex-grow-1 shadow-sm"
                            style="background-color: #f39c12; border-color: #e67e22;">
                            <i class="bi bi-check-circle-fill me-2"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <form id="formDeleteBarang" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>

    <div id="flash-data" data-login-success="{{ session('login_success') }}" data-success="{{ session('success') }}"
        data-error="{{ session('error') }}">
    </div>

    <script>
        // --- JAM DIGITAL ---
        function updateClock() {
            const now = new Date();
            document.getElementById('digital-clock').innerText = now.toLocaleTimeString('id-ID', {
                hour12: false
            });
            document.getElementById('date-text').innerText = now.toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }
        setInterval(updateClock, 1000);
        updateClock();

        let cart = [];
        let subtotal = 0;
        const userRole = "{{ Auth::user()->role ?? 'kasir' }}";

        // --- KERANJANG LOGIC ---
        function addToCart(id, name, price) {
            let existingItem = cart.find(item => item.id === id);
            if (existingItem) {
                existingItem.qty++;
                showToast('success', `+1 ${name}`);
            } else {
                cart.push({
                    id: id,
                    name: name,
                    price: price,
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
                let row = `
                <tr>
                    <td class="ps-3 border-bottom-0">
                        <div class="fw-bold text-dark text-truncate" style="max-width:130px">${item.name}</div>
                        <small class="text-muted" style="font-size:0.75rem;">@ ${item.price.toLocaleString('id-ID')}</small>
                    </td>
                    <td class="border-bottom-0">
                        <input type="number" class="form-control form-control-sm text-center fw-bold bg-light" 
                               value="${item.qty}" onchange="updateQty(${index}, this.value)" min="1">
                    </td>
                    <td class="text-end pe-3 border-bottom-0 fw-bold text-dark">${totalItem.toLocaleString('id-ID')}</td>
                    <td class="text-end pe-2 border-bottom-0">
                        <button class="btn btn-sm text-danger opacity-50 hover-opacity-100 p-0" onclick="hapusItem(${index})">
                            <i class="bi bi-x-circle-fill fs-6"></i>
                        </button>
                    </td>
                </tr>`;
                tbody.innerHTML += row;
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
                title: 'Kosongkan Keranjang?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#aaa',
                confirmButtonText: 'Ya, Kosongkan',
                cancelButtonText: 'Batal'
            }).then((r) => {
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

            // Logic Diskon Admin
            let boxAdmin = document.getElementById('box-admin');
            if (userRole !== 'admin' && diskon > 50000) { // Limit diskon kasir 50rb (contoh)
                boxAdmin.classList.remove('d-none');
            } else {
                boxAdmin.classList.add('d-none');
            }
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
                inputCust.placeholder = "Wajib Isi Nama Pelanggan";
                inputBayar.value = 0;
                inputBayar.disabled = true;
                inputBayar.classList.add('bg-secondary', 'bg-opacity-10');
            } else {
                inputCust.placeholder = "Nama Pelanggan (Opsional)";
                inputBayar.disabled = false;
                inputBayar.classList.remove('bg-secondary', 'bg-opacity-10');
            }
        }

        function prosesBayar() {
            if (cart.length === 0) return showToast('error', 'Keranjang masih kosong');

            let diskon = parseInt(document.getElementById('input-diskon').value) || 0;
            let grandTotal = Math.max(0, subtotal - diskon);
            let bayar = parseInt(document.getElementById('input-bayar').value) || 0;
            let method = document.getElementById('payment-method').value;
            let cust = document.getElementById('customer-name').value.trim();
            let adminPass = document.getElementById('admin-pass').value;

            if (method === 'utang' && cust === '') return Swal.fire('Data Kurang', 'Nama Pelanggan wajib diisi untuk Utang',
                'warning');
            if (method !== 'utang' && bayar < grandTotal) return Swal.fire('Uang Kurang',
                `Pembayaran kurang Rp ${(grandTotal - bayar).toLocaleString('id-ID')}`, 'error');

            Swal.fire({
                title: 'Proses Pembayaran?',
                html: `<h3 class="text-danger fw-bold">Rp ${grandTotal.toLocaleString('id-ID')}</h3><p class="text-muted">Metode: ${method.toUpperCase()}</p>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#9A1B1F',
                confirmButtonText: 'Bayar Sekarang',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        didOpen: () => Swal.showLoading()
                    });

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
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'success') {
                                if (data.snap_token) {
                                    window.snap.pay(data.snap_token, {
                                        onSuccess: () => {
                                            Swal.fire('Lunas!', 'Pembayaran Berhasil', 'success')
                                                .then(() => cetakStruk(data.sale_id));
                                        },
                                        onPending: () => location.reload(),
                                        onError: () => location.reload()
                                    });
                                } else {
                                    Swal.fire('Berhasil!', 'Transaksi Telah Disimpan', 'success').then(() =>
                                        cetakStruk(data.sale_id));
                                }
                            } else {
                                Swal.fire('Gagal', data.msg, 'error');
                            }
                        }).catch(() => Swal.fire('Error', 'Terjadi kesalahan sistem', 'error'));
                }
            });
        }

        function cetakStruk(id) {
            window.open(`/transaksi/struk/${id}`, '_blank');
            setTimeout(() => location.reload(), 1000);
        }

        // --- ADMIN ACTIONS (Edit & Delete) ---
        function editProduk(e, id, kode, nama, harga, stok) {
            e.stopPropagation();

            document.getElementById('edit-kode').value = kode;
            document.getElementById('edit-nama').value = nama;
            document.getElementById('edit-harga').value = harga;
            document.getElementById('edit-stok').value = stok;

            let form = document.getElementById('formEditBarang');
            form.action = `/produk/update/${id}`;

            var myModal = new bootstrap.Modal(document.getElementById('modalEditBarang'));
            myModal.show();
        }

        function hapusProduk(e, id, nama) {
            e.stopPropagation();

            Swal.fire({
                title: 'Hapus Produk?',
                text: `Anda akan menghapus "${nama}".`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#ccc',
                confirmButtonText: 'Ya, Hapus Permanen'
            }).then((result) => {
                if (result.isConfirmed) {
                    let form = document.getElementById('formDeleteBarang');
                    form.action = `/produk/hapus/${id}`;
                    form.submit();
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
            if (f.dataset.loginSuccess) showToast('success', f.dataset.loginSuccess);
            if (f.dataset.success) Swal.fire({
                title: 'Berhasil',
                text: f.dataset.success,
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
            if (f.dataset.error) Swal.fire('Gagal', f.dataset.error, 'error');
        });
    </script>
@endsection
