@extends('layouts.app')
@section('content')
    <style>
        :root {
            --brand-red: #9A1B1F;
            --brand-gold: #FFD700;
        }

        /* Override warna navbar */
        .bg-brand {
            background-color: var(--brand-red) !important;
            border-bottom: 4px solid var(--brand-gold);
        }

        .btn-brand {
            background-color: var(--brand-red);
            border-color: var(--brand-red);
            color: white;
            transition: all 0.3s;
        }

        .btn-brand:hover {
            background-color: #7a1518;
            border-color: #7a1518;
            color: var(--brand-gold);
        }

        .product-card:hover {
            border-color: var(--brand-red);
            cursor: pointer;
            transform: scale(1.02);
            transition: 0.2s;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        /* Scroll Area Produk */
        .scroll-area-products {
            height: 78vh;
            /* Sedikit dipertinggi */
            overflow-y: auto;
        }

        /* Scroll Area Keranjang */
        .scroll-area-cart {
            height: 45vh;
            /* Fixed height agar footer keranjang selalu terlihat */
            overflow-y: auto;
        }

        #digital-clock {
            font-family: 'Courier New', Courier, monospace;
            letter-spacing: 2px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }

        .navbar-logo {
            height: 45px;
            width: auto;
            margin-right: 10px;
            filter: drop-shadow(0px 0px 1px rgba(255, 255, 255, 0.5));
        }
    </style>

    <nav class="navbar navbar-expand-lg navbar-dark bg-brand shadow-sm sticky-top mb-3">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="#">
                <img src="{{ asset('logo.png') }}" alt="Logo" class="navbar-logo">
                <div class="d-flex flex-column" style="line-height: 1.2;">
                    <span class="fs-5 text-uppercase" style="letter-spacing: 1px;">SUMBER BANGUNAN</span>
                    <small style="font-size: 0.75rem; color: var(--brand-gold);">Material & Konstruksi</small>
                </div>
            </a>

            <div class="d-flex align-items-center justify-content-center flex-grow-1">
                <div class="text-white text-center d-none d-md-block">
                    <div id="digital-clock" class="fw-bold fs-4">00:00:00</div>
                    <small id="date-text" class="text-white-50" style="font-size: 0.8rem;">...</small>
                </div>
            </div>

            <div class="d-flex text-white align-items-center">
                <div class="text-end me-3 d-none d-md-block">
                    <span class="d-block fw-bold">{{ Auth::user()->name }}</span>
                    <small class="badge bg-warning text-dark">{{ ucfirst(Auth::user()->role) }}</small>
                </div>
                <a href="/logout" class="btn btn-sm btn-outline-light fw-bold">Logout</a>
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
                            <input type="text" id="search" class="form-control border-start-0"
                                placeholder="Cari nama barang atau kode..." onkeyup="filterProduk()">
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
                                <div class="col-md-4 col-sm-6 product-item" data-name="{{ strtolower($p->nama) }}"
                                    data-kode="{{ strtolower($p->kode) }}">
                                    <div class="card product-card h-100 border-0 shadow-sm"
                                        onclick="addToCart({{ $p->id }}, '{{ $p->nama }}', {{ $p->harga }})"
                                        style="cursor: pointer; transition: 0.2s;">

                                        <div class="card-img-top bg-white d-flex align-items-center justify-content-center position-relative"
                                            style="height: 120px; overflow: hidden;">
                                            @if (!empty($p->image_url))
                                                <img src="{{ $p->image_url }}" alt="{{ $p->nama }}" class="w-100 h-100"
                                                    style="object-fit: contain;"
                                                    onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.classList.remove('d-none');">
                                                <div class="d-none text-center w-100"><i
                                                        class="bi bi-box-seam text-secondary" style="font-size: 3rem;"></i>
                                                </div>
                                            @else
                                                <i class="bi bi-box-seam text-secondary" style="font-size: 3rem;"></i>
                                            @endif
                                        </div>

                                        <div class="card-body p-2 text-center">
                                            <h6 class="fw-bold text-dark mb-1 text-truncate" title="{{ $p->nama }}">
                                                {{ $p->nama }}</h6>
                                            <small class="text-muted d-block mb-2">{{ $p->kode }}</small>
                                            <span class="badge bg-success fs-6">
                                                Rp {{ number_format($p->harga, 0, ',', '.') }}
                                            </span>
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
                        <span><i class="bi bi-cart-fill me-2"></i> Keranjang Belanja</span>
                        <button class="btn btn-sm btn-outline-danger" onclick="resetCart()">
                            <i class="bi bi-trash"></i> Reset
                        </button>
                    </div>

                    <div class="card-body p-0 scroll-area-cart bg-white position-relative">
                        <table class="table table-striped mb-0 table-hover w-100">
                            <thead class="table-light sticky-top" style="top: 0; z-index: 10;">
                                <tr>
                                    <th class="ps-3">Barang</th>
                                    <th width="15%" class="text-center">Qty</th>
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
                            <small>Pilih barang di sebelah kiri</small>
                        </div>
                    </div>

                    <div class="card-footer bg-white p-3 border-top shadow-lg" style="z-index: 20;">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal</span>
                            <span class="fw-bold fs-5" id="label-subtotal">Rp 0</span>
                        </div>

                        <div class="input-group input-group-sm mb-2">
                            <span class="input-group-text bg-white">Diskon (Rp)</span>
                            <input type="number" id="input-diskon" class="form-control text-end fw-bold text-danger"
                                value="0">
                        </div>

                        <div id="box-admin" class="alert alert-warning p-2 d-none mb-2 border-warning shadow-sm">
                            <div class="d-flex align-items-center mb-1 text-danger">
                                <i class="bi bi-lock-fill me-2"></i><small class="fw-bold">Butuh Approval Admin:</small>
                            </div>
                            <input type="password" id="admin-pass" class="form-control form-control-sm"
                                placeholder="Masukkan Password Admin...">
                        </div>

                        <hr class="my-2">

                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="small fw-bold text-muted">Metode</label>
                                <select id="payment-method" class="form-select form-select-sm"
                                    onchange="cekMetodeBayar()">
                                    <option value="cash">💵 Cash</option>
                                    <option value="online">💳 QRIS / Transfer</option>
                                    <option value="utang">📝 Utang</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="small fw-bold text-muted">Pelanggan</label>
                                <input type="text" id="customer-name" class="form-control form-control-sm"
                                    placeholder="Umum">
                            </div>
                        </div>

                        <div class="input-group mb-3 shadow-sm">
                            <span class="input-group-text bg-success text-white fw-bold">Bayar</span>
                            <input type="number" id="input-bayar" class="form-control text-end fw-bold fs-5"
                                placeholder="0">
                        </div>

                        <div class="d-flex justify-content-between mb-3 p-2 bg-light rounded border">
                            <span class="h5 fw-bold text-dark mb-0 align-self-center">GRAND TOTAL</span>
                            <span class="h3 fw-bold text-danger mb-0" id="label-total">Rp 0</span>
                        </div>

                        <button onclick="prosesBayar()"
                            class="btn btn-brand w-100 py-3 fw-bold text-uppercase shadow-sm hover-shadow">
                            <i class="bi bi-cash-coin me-2"></i> Proses Pembayaran
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTambahBarang" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-brand text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-box-seam me-2"></i>Tambah Barang Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{ route('produk.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Kode Barang</label>
                            <input type="text" name="kode" class="form-control" placeholder="Contoh: BRG005"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Barang</label>
                            <input type="text" name="nama" class="form-control" placeholder="Contoh: Palu Godam"
                                required>
                            <small class="text-muted fst-italic d-block mt-1"><i class="bi bi-info-circle"></i> Gambar
                                akan dicari otomatis dari Google.</small>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Harga Jual (Rp)</label>
                                <input type="number" name="harga" class="form-control" placeholder="0" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Stok Awal</label>
                                <input type="number" name="stok" class="form-control" placeholder="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-brand fw-bold">Simpan Barang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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

        // --- VARIABEL GLOBAL ---
        let cart = [];
        let subtotal = 0;
        // Ambil role dari blade dengan aman
        const userRole = "{{ Auth::user()->role ?? 'kasir' }}";

        // --- KERANJANG LOGIC ---
        function addToCart(id, name, price) {
            let existingItem = cart.find(item => item.id === id);
            if (existingItem) {
                existingItem.qty++;
                showToast('success', `${name} (+1)`);
            } else {
                cart.push({
                    id: id,
                    name: name,
                    price: price,
                    qty: 1
                });
                showToast('success', `${name} masuk keranjang`);
            }
            renderCart();
        }

        function renderCart() {
            let tbody = document.getElementById('cart-items');
            tbody.innerHTML = '';
            subtotal = 0;

            // Toggle State Kosong
            const emptyState = document.getElementById('empty-cart');
            if (cart.length === 0) {
                emptyState.classList.remove('d-none');
            } else {
                emptyState.classList.add('d-none');
            }

            cart.forEach((item, index) => {
                let totalItem = item.price * item.qty;
                subtotal += totalItem;

                let row = `
                    <tr>
                        <td class="align-middle ps-3">
                            <div class="fw-bold text-dark text-truncate" style="max-width: 150px;">${item.name}</div>
                            <small class="text-muted">@ Rp ${item.price.toLocaleString('id-ID')}</small>
                        </td>
                        <td class="align-middle">
                            <input type="number" class="form-control form-control-sm text-center fw-bold" 
                                   value="${item.qty}" onchange="updateQty(${index}, this.value)" min="1">
                        </td>
                        <td class="text-end align-middle pe-3 fw-bold">
                            Rp ${totalItem.toLocaleString('id-ID')}
                        </td>
                        <td class="align-middle text-end pe-2">
                            <button class="btn btn-sm text-danger p-0" onclick="hapusItem(${index})">
                                <i class="bi bi-x-circle-fill fs-5"></i>
                            </button>
                        </td>
                    </tr>`;
                tbody.innerHTML += row;
            });

            hitungTotal();
        }

        function updateQty(index, qty) {
            if (qty < 1) qty = 1;
            cart[index].qty = parseInt(qty);
            renderCart();
        }

        function hapusItem(index) {
            cart.splice(index, 1);
            renderCart();
        }

        function resetCart() {
            if (cart.length === 0) return;
            Swal.fire({
                title: 'Reset?',
                text: "Kosongkan keranjang?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#aaa',
                confirmButtonText: 'Ya'
            }).then((result) => {
                if (result.isConfirmed) {
                    cart = [];
                    document.getElementById('input-diskon').value = 0;
                    renderCart();
                }
            });
        }

        function hitungTotal() {
            let diskon = parseInt(document.getElementById('input-diskon').value) || 0;
            let grandTotal = subtotal - diskon;

            // Update UI
            document.getElementById('label-subtotal').innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
            document.getElementById('label-total').innerText = 'Rp ' + grandTotal.toLocaleString('id-ID');

            // Cek Admin Password
            let boxAdmin = document.getElementById('box-admin');
            if (userRole === 'kasir' && diskon > 10000) {
                boxAdmin.classList.remove('d-none');
            } else {
                boxAdmin.classList.add('d-none');
            }
        }

        // Event Listener Diskon
        document.getElementById('input-diskon').addEventListener('input', hitungTotal);

        // Filter Produk
        function filterProduk() {
            let keyword = document.getElementById('search').value.toLowerCase();
            let items = document.querySelectorAll('.product-item');
            items.forEach(item => {
                let name = item.getAttribute('data-name');
                let kode = item.getAttribute('data-kode');
                if (name.includes(keyword) || kode.includes(keyword)) {
                    item.classList.remove('d-none');
                } else {
                    item.classList.add('d-none');
                }
            });
        }

        // Cek Metode Bayar
        function cekMetodeBayar() {
            let method = document.getElementById('payment-method').value;
            let inputBayar = document.getElementById('input-bayar');
            let inputCust = document.getElementById('customer-name');

            if (method === 'utang') {
                inputCust.placeholder = "Wajib Isi Nama";
                inputBayar.value = 0;
                inputBayar.disabled = true;
            } else {
                inputCust.placeholder = "Umum";
                inputBayar.disabled = false;
            }
        }

        // --- PROSES BAYAR ---
        function prosesBayar() {
            if (cart.length === 0) {
                return Swal.fire('Keranjang Kosong', 'Pilih barang dulu', 'warning');
            }

            let diskon = parseInt(document.getElementById('input-diskon').value) || 0;
            let grandTotal = subtotal - diskon;
            let paymentMethod = document.getElementById('payment-method').value;
            let customerName = document.getElementById('customer-name').value.trim();
            let bayar = parseInt(document.getElementById('input-bayar').value) || 0;
            let adminPass = document.getElementById('admin-pass').value;

            // Validasi
            if (paymentMethod === 'utang' && customerName === '') {
                return Swal.fire('Nama Wajib', 'Isi nama pelanggan untuk utang', 'error');
            }
            if (paymentMethod !== 'utang' && bayar < grandTotal) {
                return Swal.fire('Uang Kurang', `Kurang Rp ${(grandTotal - bayar).toLocaleString('id-ID')}`, 'error');
            }

            // Konfirmasi
            Swal.fire({
                title: 'Proses Bayar?',
                text: `Total: Rp ${grandTotal.toLocaleString('id-ID')}`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#9A1B1F',
                confirmButtonText: 'Bayar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Loading
                    Swal.fire({
                        title: 'Memproses...',
                        didOpen: () => Swal.showLoading()
                    });

                    // Fetch API
                    fetch('/transaksi/bayar', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                cart: cart,
                                diskon: diskon,
                                bayar: bayar,
                                payment_method: paymentMethod,
                                customer_name: customerName,
                                admin_password: adminPass
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'success') {
                                if (data.snap_token) {
                                    // Midtrans
                                    window.snap.pay(data.snap_token, {
                                        onSuccess: function(result) {
                                            Swal.fire('Berhasil!', 'Pembayaran Online Sukses',
                                                'success').then(() => cetakStruk(data.sale_id));
                                        },
                                        onPending: function(result) {
                                            location.reload();
                                        },
                                        onError: function(result) {
                                            location.reload();
                                        }
                                    });
                                } else {
                                    // Cash/Utang
                                    Swal.fire('Berhasil!', 'Transaksi Tersimpan', 'success').then(() =>
                                        cetakStruk(data.sale_id));
                                }
                            } else {
                                Swal.fire('Gagal', data.msg, 'error');
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            Swal.fire('Error', 'Gagal koneksi server', 'error');
                        });
                }
            });
        }

        function cetakStruk(id) {
            window.open(`/transaksi/struk/${id}`, '_blank');
            setTimeout(() => location.reload(), 1000);
        }

        // Toast Helper
        function showToast(icon, title) {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1500,
                timerProgressBar: true
            });
            Toast.fire({
                icon: icon,
                title: title
            });
        }

        // --- FLASH MESSAGE HANDLING ---
        document.addEventListener('DOMContentLoaded', () => {
            const flash = document.getElementById('flash-data');
            if (flash.dataset.loginSuccess) showToast('success', flash.dataset.loginSuccess);
            if (flash.dataset.success) Swal.fire('Sukses', flash.dataset.success, 'success');
            if (flash.dataset.error) Swal.fire('Error', flash.dataset.error, 'error');
        });
    </script>
@endsection
