import Swal from 'sweetalert2';

// ================= STATE VARIABLES =================
let cart = [];
let subtotal = 0;
// Mengambil role user dari meta tag (pastikan ada di layout) atau default ke 'kasir'
const userRole = document.querySelector('meta[name="user-role"]')?.content || 'kasir'; 

$(function() {
    // 1. Inisialisasi Jam
    setInterval(updateClock, 1000);
    updateClock();

    // 2. Event Listeners
    
    // --- Search Produk ---
    $('#search-input').on('keyup', function() {
        const keyword = $(this).val().toLowerCase();
        $('.product-item').each(function() {
            const name = $(this).data('name-lower');
            const code = $(this).data('code');
            // Show jika nama ATAU kode cocok
            $(this).toggleClass('d-none', !(name.includes(keyword) || code.includes(keyword)));
        });
    });

    // --- Tambah ke Keranjang ---
    // Menggunakan delegation juga agar aman jika produk di-load via AJAX nanti
    $(document).on('click', '.btn-add-cart', function() {
        const item = $(this).closest('.product-item');
        addToCart(
            item.data('id'),
            item.data('name'),
            item.data('price')
        );
    });

    // --- Reset Cart ---
    $('#btn-reset-cart').on('click', resetCart);

    // --- Update Qty di Cart ---
    $('#cart-items').on('change', '.qty-input', function() {
        const index = $(this).data('index');
        const newQty = parseInt($(this).val());
        updateQty(index, newQty);
    });

    // --- Hapus Item di Cart ---
    $('#cart-items').on('click', '.btn-remove-item', function() {
        const index = $(this).data('index');
        hapusItem(index);
    });

    // --- Hitung Total saat Diskon berubah ---
    $('#input-diskon').on('input', renderCartCalculations);

    // --- Proses Bayar ---
    $('#btn-process-pay').on('click', prosesBayar);

    // --- Edit Barang (Admin) [PERBAIKAN UTAMA] ---
    // Menggunakan $(document).on() untuk menangani event delegation
    $(document).on('click', '.btn-edit-barang', function(e) {
        e.preventDefault();
        e.stopPropagation(); // Mencegah trigger 'Add to Cart'

        const productItem = $(this).closest('.product-item');
        const id = productItem.data('id');

        console.log('Edit clicked for ID:', id); // Debugging

        if (id) {
            editBarang(id);
        } else {
            Swal.fire('Error', 'ID Produk tidak ditemukan', 'error');
        }
    });
    
    // --- Hapus Barang (Admin) ---
    $(document).on('click', '.btn-delete-barang', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const parent = $(this).closest('.product-item');
        hapusBarang(parent.data('id'), parent.data('name'));
    });
});

// ================= LOGIC FUNCTIONS =================

function updateClock() {
    const now = new Date();
    $('#digital-clock').text(now.toLocaleTimeString('id-ID', { hour12: false }));
    $('#date-text').text(now.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }));
}

function addToCart(id, name, price) {
    let existingItem = cart.find(item => item.id === id);
    if (existingItem) {
        existingItem.qty++;
    } else {
        cart.push({ id, name, price, qty: 1 });
    }
    
    // Toast Notification
    const Toast = Swal.mixin({
        toast: true, position: 'top-end', showConfirmButton: false, timer: 1000, timerProgressBar: true
    });
    Toast.fire({ icon: 'success', title: `${name} masuk keranjang` });

    renderCart();
}

function renderCart() {
    const tbody = $('#cart-items');
    tbody.empty();
    subtotal = 0;

    if (cart.length === 0) {
        $('#empty-cart').removeClass('d-none');
    } else {
        $('#empty-cart').addClass('d-none');
        
        cart.forEach((item, index) => {
            const totalItem = item.price * item.qty;
            subtotal += totalItem;

            const row = `
                <tr>
                    <td class="align-middle ps-3">
                        <div class="fw-bold text-dark text-truncate" style="max-width: 150px;">${item.name}</div>
                        <small class="text-muted">@ ${formatRupiah(item.price)}</small>
                    </td>
                    <td class="align-middle">
                        <input type="number" class="form-control form-control-sm text-center fw-bold qty-input" 
                               value="${item.qty}" data-index="${index}" min="1">
                    </td>
                    <td class="text-end align-middle pe-3 fw-bold">${formatRupiah(totalItem)}</td>
                    <td class="align-middle text-end pe-2">
                        <button class="btn btn-sm text-danger p-0 btn-remove-item" data-index="${index}">
                            <i class="bi bi-x-circle-fill fs-5"></i>
                        </button>
                    </td>
                </tr>`;
            tbody.append(row);
        });
    }
    renderCartCalculations();
}

function renderCartCalculations() {
    const diskon = parseInt($('#input-diskon').val()) || 0;
    const grandTotal = subtotal - diskon;

    $('#label-subtotal').text(formatRupiah(subtotal));
    $('#label-total').text(formatRupiah(grandTotal));

    // Logic Admin Approval (jika diskon terlalu besar)
    if (userRole === 'kasir' && diskon > 10000) {
        $('#box-admin').removeClass('d-none');
    } else {
        $('#box-admin').addClass('d-none');
    }
}

function updateQty(index, qty) {
    if (qty < 1) qty = 1;
    cart[index].qty = qty;
    renderCart();
}

function hapusItem(index) {
    cart.splice(index, 1);
    renderCart();
}

function resetCart() {
    if (cart.length === 0) return;
    Swal.fire({
        title: 'Reset Keranjang?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Ya, Kosongkan'
    }).then((result) => {
        if (result.isConfirmed) {
            cart = [];
            $('#input-diskon').val(0);
            renderCart();
        }
    });
}

function prosesBayar() {
    if (cart.length === 0) return Swal.fire('Oops', 'Keranjang kosong', 'warning');

    const diskon = parseInt($('#input-diskon').val()) || 0;
    const grandTotal = subtotal - diskon;
    const bayar = parseInt($('#input-bayar').val()) || 0;
    const method = $('#payment-method').val();
    
    // Validasi Pembayaran
    if (method !== 'utang' && bayar < grandTotal) {
        return Swal.fire('Kurang Bayar', `Kurang: ${formatRupiah(grandTotal - bayar)}`, 'error');
    }

    Swal.fire({
        title: 'Proses Pembayaran?',
        text: `Total: ${formatRupiah(grandTotal)}`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#9A1B1F',
        confirmButtonText: 'Bayar',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return axios.post('/transaksi/bayar', {
                cart: cart,
                diskon: diskon,
                bayar: bayar,
                payment_method: method,
                customer_name: $('#customer-name').val(),
                admin_password: $('#admin-pass').val()
            }).then(response => {
                return response.data;
            }).catch(error => {
                const msg = error.response ? error.response.data.message : error.message;
                Swal.showValidationMessage(`Gagal: ${msg}`);
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const data = result.value;
            if (data && data.status === 'success') {
                Swal.fire('Berhasil', 'Transaksi sukses!', 'success').then(() => {
                    window.open(`/transaksi/struk/${data.sale_id}`, '_blank');
                    location.reload();
                });
            }
        }
    });
}

// ================= ADMIN FUNCTIONS =================

function editBarang(id) {
    // Tampilkan Loading
    Swal.fire({
        title: 'Memuat Data...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    axios.get(`/produk/${id}`)
        .then(res => {
            Swal.close();
            
            if (res.data.status === 'success') {
                const p = res.data.data;
                
                // 1. Isi Form di Modal
                $('#edit-kode').val(p.kode);
                $('#edit-nama').val(p.nama);
                $('#edit-harga').val(p.harga);
                $('#edit-stok').val(p.stok);
                $('#edit-refresh-image').prop('checked', false);
                
                // 2. Set Action URL pada Form agar update ke ID yang benar
                const form = $('#formEditBarang');
                form.attr('action', `/produk/${p.id}`); // PENTING: Update URL Action
                
                // 3. Pastikan method spoofing (PUT) ada
                if (form.find('input[name="_method"]').length === 0) {
                    form.append('<input type="hidden" name="_method" value="PUT">');
                }
                
                // 4. Tampilkan Modal
                $('#modalEditBarang').modal('show');
            } else {
                Swal.fire('Error', res.data.message || 'Data tidak ditemukan', 'error');
            }
        })
        .catch(err => {
            console.error('Error Edit:', err);
            let msg = 'Gagal mengambil data.';
            if (err.response) msg = err.response.data.message || msg;
            Swal.fire('Error', msg, 'error');
        });
}

function hapusBarang(id, nama) {
    Swal.fire({
        title: `Hapus ${nama}?`,
        text: "Data tidak bisa dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Ya, Hapus'
    }).then((result) => {
        if (result.isConfirmed) {
            axios.delete(`/produk/${id}`)
                .then(res => {
                    Swal.fire('Terhapus', res.data.message, 'success')
                        .then(() => location.reload());
                })
                .catch(err => {
                    Swal.fire('Gagal', 'Terjadi kesalahan saat menghapus', 'error');
                });
        }
    });
}

function formatRupiah(number) {
    return 'Rp ' + number.toLocaleString('id-ID');
}