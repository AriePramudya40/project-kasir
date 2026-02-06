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

        /* --- TAMBAHAN BARU: TOMBOL BRAND --- */
        .btn-brand {
            background-color: var(--brand-red);
            border-color: var(--brand-red);
            color: white;
            transition: all 0.3s;
        }

        .btn-brand:hover {
            background-color: #7a1518;
            /* Warna merah yang sedikit lebih gelap untuk efek hover */
            border-color: #7a1518;
            color: var(--brand-gold);
            /* Teks berubah jadi emas saat di-hover */
        }

        .product-card:hover {
            border-color: var(--brand-red);
            cursor: pointer;
            transform: scale(1.02);
            transition: 0.2s;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        /* --- PERUBAHAN DISINI --- */

        /* 1. Area Produk (Kiri) - Tetap Tinggi Full agar rapi */
        .scroll-area-products {
            height: 75vh;
            overflow-y: auto;
        }

        /* 2. Area Keranjang (Kanan) - Tinggi Fleksibel */
        .scroll-area-cart {
            min-height: 150px;
            /* Tinggi minimal saat kosong (pendek) */
            max-height: 60vh;
            /* Tinggi maksimal (nanti muncul scroll jika penuh) */
            overflow-y: auto;
            /* Scroll otomatis jika item banyak */
            height: auto;
            /* Tinggi mengikuti konten */
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

            <div class="col-md-7">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white py-3">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                            <input type="text" id="search" class="form-control border-start-0"
                                placeholder="Cari nama barang atau kode..." onkeyup="filterProduk()">
                        </div>
                    </div>
                    <div class="card-body bg-light scroll-area-products">
                        <div class="row g-3" id="product-list">
                            @foreach ($products as $p)
                                <div class="col-md-4 product-item" data-name="{{ strtolower($p->nama) }}"
                                    data-kode="{{ strtolower($p->kode) }}">
                                    <div class="card product-card h-100 text-center p-2"
                                        onclick="addToCart({{ $p->id }}, '{{ $p->nama }}', {{ $p->harga }})">
                                        <div class="card-body">
                                            <h6 class="fw-bold text-dark mb-1">{{ $p->nama }}</h6>
                                            <small class="text-muted d-block mb-2">{{ $p->kode }}</small>
                                            <span class="badge bg-success fs-6">Rp
                                                {{ number_format($p->harga, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white fw-bold py-3 d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-cart-fill me-2"></i> Keranjang Belanja</span>
                        <button class="btn btn-sm btn-outline-secondary" onclick="resetCart()">Reset</button>
                    </div>

                    <div class="card-body p-0 scroll-area-cart bg-white position-relative">
                        <table class="table table-striped mb-0 table-hover">
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

                        <div id="empty-cart" class="text-center py-4 text-muted">
                            <i class="bi bi-basket display-4 text-light"></i>
                            <p class="mt-2 small">Belum ada barang dipilih.</p>
                        </div>
                    </div>

                    <div class="card-footer bg-white p-3 border-top shadow-lg" style="z-index: 20;">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Subtotal</span>
                            <span class="fw-bold" id="label-subtotal">Rp 0</span>
                        </div>

                        <div class="input-group input-group-sm mb-2">
                            <span class="input-group-text bg-white">Diskon (Rp)</span>
                            <input type="number" id="input-diskon" class="form-control text-end fw-bold text-danger"
                                value="0">
                        </div>

                        <div id="box-admin" class="alert alert-warning p-2 d-none mb-2 border-warning">
                            <div class="d-flex align-items-center mb-1">
                                <i class="bi bi-lock-fill me-2"></i><small class="fw-bold">Butuh Approval Admin:</small>
                            </div>
                            <input type="password" id="admin-pass" class="form-control form-control-sm"
                                placeholder="Password Admin...">
                        </div>

                        <hr class="my-2">

                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="small fw-bold text-muted">Metode</label>
                                <select id="payment-method" class="form-select form-select-sm" onchange="cekMetodeBayar()">
                                    <option value="cash">💵 Cash</option>
                                    <option value="qris">📱 QRIS / Transfer (Midtrans)</option>
                                    <option value="utang">📝 Utang</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="small fw-bold text-muted">Pelanggan</label>
                                <input type="text" id="customer-name" class="form-control form-control-sm"
                                    placeholder="Umum">
                            </div>
                        </div>

                        <div class="input-group mb-3">
                            <span class="input-group-text bg-success text-white fw-bold">Bayar</span>
                            <input type="number" id="input-bayar" class="form-control text-end fw-bold fs-5"
                                placeholder="0">
                        </div>

                        <div class="d-flex justify-content-between mb-3 p-2 bg-light rounded border">
                            <span class="h4 fw-bold text-dark mb-0 align-self-center">TOTAL</span>
                            <span class="h3 fw-bold text-danger mb-0" id="label-total">Rp 0</span>
                        </div>

                        <button onclick="prosesBayar()" class="btn btn-brand w-100 py-3 fw-bold text-uppercase shadow">
                            <i class="bi bi-cash-coin me-2"></i> Proses Pembayaran
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        // Logika Jam
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

        // Logika Keranjang
        let cart = [];
        let subtotal = 0;
        const role = "{{ Auth::user()->role }}";

        function addToCart(id, name, price) {
            let existingItem = cart.find(item => item.id === id);
            if (existingItem) existingItem.qty++;
            else cart.push({
                id: id,
                name: name,
                price: price,
                qty: 1
            });
            renderCart();
        }

        function renderCart() {
            let tbody = document.getElementById('cart-items');
            tbody.innerHTML = '';
            subtotal = 0;
            if (cart.length === 0) document.getElementById('empty-cart').classList.remove('d-none');
            else document.getElementById('empty-cart').classList.add('d-none');

            cart.forEach((item, index) => {
                let totalItem = item.price * item.qty;
                subtotal += totalItem;
                let row = `<tr>
                        <td class="align-middle ps-3"><div class="fw-bold text-dark">${item.name}</div><small class="text-muted">@ Rp ${item.price.toLocaleString('id-ID')}</small></td>
                        <td class="align-middle"><input type="number" class="form-control form-control-sm text-center fw-bold" value="${item.qty}" onchange="updateQty(${index}, this.value)" min="1"></td>
                        <td class="text-end align-middle pe-3 fw-bold">Rp ${totalItem.toLocaleString('id-ID')}</td>
                        <td class="align-middle text-end"><button class="btn btn-sm text-danger" onclick="hapusItem(${index})"><i class="bi bi-x-circle-fill fs-5"></i></button></td>
                    </tr>`;
                tbody.innerHTML += row;
            });
            hitungTotal();
        }

        function hitungTotal() {
            let diskon = parseInt(document.getElementById('input-diskon').value) || 0;
            let grandTotal = subtotal - diskon;
            document.getElementById('label-subtotal').innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
            document.getElementById('label-total').innerText = 'Rp ' + grandTotal.toLocaleString('id-ID');

            let boxAdmin = document.getElementById('box-admin');
            if (role === 'kasir' && diskon > 10000) boxAdmin.classList.remove('d-none');
            else boxAdmin.classList.add('d-none');
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
            cart = [];
            document.getElementById('input-diskon').value = 0;
            renderCart();
        }

        function filterProduk() {
            let keyword = document.getElementById('search').value.toLowerCase();
            let items = document.querySelectorAll('.product-item');
            items.forEach(item => {
                let name = item.getAttribute('data-name');
                let kode = item.getAttribute('data-kode');
                if (name.includes(keyword) || kode.includes(keyword)) item.classList.remove('d-none');
                else item.classList.add('d-none');
            });
        }

        document.getElementById('input-diskon').addEventListener('input', function() {
            hitungTotal();
        });

        function cekMetodeBayar() {
            let method = document.getElementById('payment-method').value;
            let inputBayar = document.getElementById('input-bayar');

            if (method === 'utang') {
                document.getElementById('customer-name').placeholder = "Wajib nama pengutang";
                inputBayar.value = 0; // Kalau utang, anggap bayar 0
                inputBayar.disabled = true;
            } else if (method === 'transfer') {
                // Kalau transfer, biasanya uang pas
                // Kita bisa auto-fill dengan grand total (opsional, perlu hitung ulang dulu)
                inputBayar.disabled = false;
            } else {
                document.getElementById('customer-name').placeholder = "Umum";
                inputBayar.disabled = false;
                inputBayar.focus();
            }
        }

        function prosesBayar() {
            if (cart.length === 0) {
                alert("Keranjang kosong!");
                return;
            }

            // ... variable definition (diskon, grandTotal, dll) ...
            let paymentMethod = document.getElementById('payment-method').value;
            let customerName = document.getElementById('customer-name').value;
            let bayar = document.getElementById('input-bayar').value;

            // Validasi Utang (Sama)
            if (paymentMethod === 'utang' && customerName.trim() === '') {
                alert("⚠️ Nama Pelanggan wajib diisi!");
                return;
            }

            let btn = document.querySelector('button[onclick="prosesBayar()"]');
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Memproses...';
            btn.disabled = true;

            fetch('/transaksi/bayar', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        cart: cart,
                        diskon: document.getElementById('input-diskon').value,
                        bayar: bayar,
                        payment_method: paymentMethod,
                        customer_name: customerName,
                        admin_password: document.getElementById('admin-pass').value
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {

                        // JIKA ADA SNAP TOKEN (QRIS)
                        if (data.snap_token) {
                            window.snap.pay(data.snap_token, {
                                onSuccess: function(result) {
                                    // Sukses bayar via popup
                                    alert("✅ Pembayaran Berhasil!");
                                    cetakDanReset(data.sale_id);
                                },
                                onPending: function(result) {
                                    alert("⏳ Menunggu pembayaran...");
                                    location.reload(); // Reload agar status terupdate nanti
                                },
                                onError: function(result) {
                                    alert("❌ Pembayaran Gagal!");
                                    location.reload();
                                },
                                onClose: function() {
                                    alert('⚠️ Anda menutup popup tanpa menyelesaikan pembayaran');
                                    // Opsional: Reload atau biarkan
                                }
                            });
                        } else {
                            // TRANSAKSI BIASA (CASH/UTANG)
                            let msg = "✅ Transaksi Berhasil!";
                            if (paymentMethod == 'utang') msg += "\nDicatat di Buku Utang.";

                            alert(msg);
                            cetakDanReset(data.sale_id);
                        }

                    } else {
                        alert("❌ Gagal: " + data.msg);
                        btn.innerHTML = 'Proses Pembayaran';
                        btn.disabled = false;
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert("Error Sistem");
                    btn.innerHTML = 'Proses Pembayaran';
                    btn.disabled = false;
                });
        }

        // Fungsi Pembantu
        function cetakDanReset(saleId) {
            if (confirm("Cetak Struk sekarang?")) {
                window.open("/transaksi/struk/" + saleId, '_blank');
            }
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }
    </script>

    <div id="flash-data" data-login-success="{{ session('login_success') }}" data-success="{{ session('success') }}"
        data-error="{{ session('error') }}">
    </div>
@endsection
