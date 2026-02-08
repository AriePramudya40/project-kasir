@extends('layouts.app')
@section('content')
    <style>
        :root {
            --brand-red: #9A1B1F;
            --brand-gold: #FFD700;
            --brand-dark: #2c3e50;
            --bg-light: #f4f6f8;
        }

        body {
            background-color: var(--bg-light);
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }

        /* --- MODERN NAVBAR --- */
        .navbar-custom {
            background: #ffffff;
            border-radius: 16px;
            padding: 0.8rem 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            display: flex;
            align-items: center;
            justify-content: space-between;
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
            color: #888;
            font-size: 0.75rem;
            font-weight: 500;
        }

        /* Widget Jam */
        .clock-widget {
            background: #f8f9fa;
            border: 1px solid #eee;
            border-radius: 50px;
            padding: 8px 25px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--brand-dark);
            font-variant-numeric: tabular-nums;
        }

        /* User Profile */
        .user-pill {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 6px 6px 6px 15px;
            background: #fff;
            border: 1px solid #eee;
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
            background: linear-gradient(135deg, var(--brand-red), #c0392b);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1rem;
        }

        /* --- Product Card & Layout --- */
        .product-card {
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            border: 1px solid rgba(0, 0, 0, 0.04);
            border-radius: 16px;
            overflow: hidden;
            background: #fff;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08) !important;
            border-color: rgba(154, 27, 31, 0.2);
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
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(4px);
        }

        .scroll-area-products {
            height: 75vh;
            overflow-y: auto;
            padding-right: 5px;
        }

        .scroll-area-cart {
            height: 42vh;
            overflow-y: auto;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 5px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #dcdcdc;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #bbb;
        }

        .price-tag {
            color: var(--brand-red);
            font-weight: 700;
            font-size: 1.1rem;
        }

        .form-control:focus {
            border-color: var(--brand-red);
            box-shadow: 0 0 0 0.25rem rgba(154, 27, 31, 0.1);
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
                <i class="bi bi-clock text-muted"></i>
                <div class="d-flex flex-column align-items-start" style="line-height: 1.2;">
                    <span id="digital-clock" class="fw-bold fs-5">00:00</span>
                    <small id="date-text" class="text-muted" style="font-size: 0.7rem;">...</small>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <div class="user-pill">
                    <div class="text-end lh-1">
                        <div class="fw-bold text-dark" style="font-size: 0.9rem;">{{ Auth::user()->name }}</div>
                        <small class="text-uppercase text-muted"
                            style="font-size: 0.65rem; letter-spacing: 0.5px;">{{ Auth::user()->role }}</small>
                    </div>
                    <div class="user-avatar shadow-sm">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                </div>
                <a href="{{ route('logout') }}"
                    class="btn btn-light text-danger btn-sm rounded-circle shadow-sm d-flex align-items-center justify-content-center"
                    style="width: 40px; height: 40px;" title="Logout">
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
                            <span class="input-group-text bg-white border-end-0 text-muted ps-3"><i
                                    class="bi bi-search"></i></span>
                            <input type="text" id="search" class="form-control border-start-0 py-2"
                                placeholder="Cari nama barang..." onkeyup="filterProduk()">
                        </div>
                        @if (Auth::user()->role == 'admin')
                            <button type="button" class="btn btn-dark fw-bold rounded-pill px-4 shadow-sm"
                                style="background: var(--brand-red); border:none;" data-bs-toggle="modal"
                                data-bs-target="#modalTambahBarang">
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

                                        <div class="card-body p-3 text-center border-top bg-white">
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
                            <span class="input-group-text bg-light border-end-0 text-muted">Diskon</span>
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
                                placeholder="Input Pembayaran (Rp)">
                        </div>

                        <button onclick="prosesBayar()" class="btn btn-danger w-100 py-2 fw-bold shadow-sm rounded-pill"
                            style="background: var(--brand-red);">
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
                <div class="modal-header bg-danger text-white px-4">
                    <h6 class="modal-title fw-bold">Tambah Barang</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('produk.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="form-floating mb-3">
                            <input type="text" name="kode" class="form-control bg-light border-0" id="addKode"
                                placeholder="Kode" required>
                            <label for="addKode">Kode Barang (Barcode)</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" name="nama" class="form-control bg-light border-0" id="addNama"
                                placeholder="Nama" required>
                            <label for="addNama">Nama Barang</label>
                        </div>
                        <div class="row g-2">
                            <div class="col-7">
                                <div class="form-floating">
                                    <input type="number" name="harga" class="form-control bg-light border-0"
                                        id="addHarga" placeholder="0" required>
                                    <label for="addHarga">Harga Jual (Rp)</label>
                                </div>
                            </div>
                            <div class="col-5">
                                <div class="form-floating">
                                    <input type="number" name="stok" class="form-control bg-light border-0"
                                        id="addStok" placeholder="0" required>
                                    <label for="addStok">Stok Awal</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4">
                        <button type="submit" class="btn btn-danger w-100 rounded-pill fw-bold">Simpan Data</button>
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
                                if (data.snap_token) window.snap.pay(data.snap_token, {
                                    onSuccess: () => {
                                        Swal.fire('Berhasil', 'Lunas', 'success').then(() =>
                                            cetakStruk(data.sale_id));
                                    },
                                    onPending: () => location.reload(),
                                    onError: () => location.reload()
                                });
                                else Swal.fire('Berhasil', 'Transaksi Disimpan', 'success').then(() =>
                                    cetakStruk(data.sale_id));
                            } else Swal.fire('Gagal', data.msg, 'error');
                        }).catch(() => Swal.fire('Error', 'Koneksi Gagal', 'error'));
                    }
                });
        }

        function cetakStruk(id) {
            window.open(`/transaksi/struk/${id}`, '_blank');
            setTimeout(() => location.reload(), 1000);
        }

        // --- ADMIN ACTIONS ---
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
        });
    </script>
@endsection
