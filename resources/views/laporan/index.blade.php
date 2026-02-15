@extends('layouts.app')

@section('title', 'Laporan Keuangan')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .flatpickr-day.selected {
            background: var(--brand-red, #9A1B1F) !important;
            border-color: var(--brand-red, #9A1B1F) !important;
        }

        .form-select-pill {
            border: none;
            background: transparent;
            cursor: pointer;
        }

        .modal-modern {
            border-radius: 24px;
            border: none;
            overflow: hidden;
        }

        .modal-header-modern {
            background: linear-gradient(135deg, #1e293b, #0f172a);
            color: white;
            padding: 20px 30px;
        }

        .stat-card {
            background: #f8f9fa;
            border-radius: 16px;
            padding: 15px;
            border: 1px solid #e9ecef;
        }

        .stat-card.active {
            background: #fff5f5;
            border: 1px solid #fc8181;
        }

        .btn-metode {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 10px;
            text-align: center;
            cursor: pointer;
            background: white;
        }

        .btn-metode.selected {
            border-color: #3182ce;
            background: #ebf8ff;
            color: #2b6cb0;
            font-weight: bold;
        }

        .big-amount {
            font-family: monospace;
            font-weight: bold;
            letter-spacing: -1px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold"><i class="bi bi-bar-chart-fill text-danger me-2"></i>Laporan Keuangan</h4>
            <form action="{{ route('laporan.index') }}" method="GET"
                class="d-flex gap-2 bg-white p-2 rounded-pill shadow-sm border">
                <input type="text" name="start_date" class="form-control border-0 bg-transparent datepicker ps-2"
                    value="{{ $startDate }}" placeholder="Mulai" style="width: 120px;">
                <span class="align-self-center">-</span>
                <input type="text" name="end_date" class="form-control border-0 bg-transparent datepicker ps-2"
                    value="{{ $endDate }}" placeholder="Akhir" style="width: 120px;">
                <div class="vr mx-1"></div>
                <select name="payment_method" class="form-select form-select-pill fw-bold" onchange="this.form.submit()">
                    <option value="all" {{ $selectedMethod == 'all' ? 'selected' : '' }}>Semua</option>
                    <option value="cash" {{ $selectedMethod == 'cash' ? 'selected' : '' }}>Tunai</option>
                    <option value="online" {{ $selectedMethod == 'online' ? 'selected' : '' }}>Online</option>
                    <option value="utang" {{ $selectedMethod == 'utang' ? 'selected' : '' }}>Utang</option>
                </select>
                <button type="submit" class="btn btn-dark rounded-pill"><i class="bi bi-search"></i></button>
            </form>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card bg-success text-white h-100 border-0 shadow-sm p-3">
                    <h2 class="fw-bold">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h2><small>Uang Masuk</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-dark h-100 border-0 shadow-sm p-3">
                    <h2 class="fw-bold">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</h2><small>Total Piutang</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-white h-100 border-0 shadow-sm p-3">
                    <h5 class="fw-bold">{{ $totalTransaksi }}</h5><small>Total Transaksi</small>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between">
                <h6 class="fw-bold m-0">Riwayat Transaksi</h6>
                <button onclick="window.print()" class="btn btn-sm btn-light border rounded-pill"><i
                        class="bi bi-printer"></i> Print</button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-center">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th>Tanggal</th>
                            <th>Faktur</th>
                            <th>Pelanggan</th>
                            <th>Metode</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                            <tr>
                                <td class="text-start ps-4">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                                <td class="fw-bold text-primary">{{ $sale->no_faktur }}</td>
                                <td>{{ $sale->customer_name }}</td>
                                <td><span
                                        class="badge bg-secondary rounded-pill">{{ ucfirst($sale->payment_method) }}</span>
                                </td>
                                <td>
                                    @if ($sale->status == 'lunas')
                                        <span
                                            class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2">LUNAS</span>
                                    @else
                                        <button
                                            onclick="lunasiUtang(event, {{ $sale->id }}, '{{ $sale->customer_name }}', {{ $sale->grand_total }}, {{ $sale->bayar }})"
                                            class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
                                            Bayar <i class="bi bi-chevron-right"></i>
                                        </button>
                                        <div class="small text-danger fw-bold mt-1">Sisa: Rp
                                            {{ number_format($sale->grand_total - $sale->bayar, 0, ',', '.') }}</div>
                                    @endif
                                </td>
                                <td class="text-end fw-bold">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</td>
                                <td><a href="javascript:void(0)" onclick="cetakStruk({{ $sale->id }})"
                                        class="btn btn-sm btn-light rounded-circle"><i class="bi bi-printer-fill"></i></a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-5 text-muted">Tidak ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalPelunasan" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-modern shadow-lg">
                <div class="modal-header-modern d-flex justify-content-between">
                    <h5 class="mb-0 fw-bold">Pelunasan Tagihan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <div class="text-center mb-4">
                        <h5 class="fw-bold text-dark mb-0" id="text-pelanggan">-</h5>
                        <small class="text-muted">Pelanggan</small>
                    </div>
                    <div class="row g-2 mb-4">
                        <div class="col-4">
                            <div class="stat-card text-center"><small>TOTAL</small><span class="d-block fw-bold"
                                    id="text-total">0</span></div>
                        </div>
                        <div class="col-4">
                            <div class="stat-card text-center"><small>DIBAYAR</small><span
                                    class="d-block fw-bold text-success" id="text-bayar">0</span></div>
                        </div>
                        <div class="col-4">
                            <div class="stat-card active text-center"><small class="text-danger">SISA</small><span
                                    class="d-block fw-bold text-danger" id="text-sisa">0</span></div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="btn-metode selected" onclick="pilihMetode('cash', this)">Tunai</div>
                        </div>
                        <div class="col-6">
                            <div class="btn-metode" onclick="pilihMetode('online', this)">Online / QRIS</div>
                        </div>
                    </div>
                    <div id="box-input-bayar">
                        <label class="fw-bold small text-muted">Nominal Bayar (Rp)</label>
                        <input type="number" id="input_bayar_pelunasan"
                            class="form-control border-0 fs-4 fw-bold text-dark big-amount" placeholder="0">
                        <small class="text-muted fst-italic" id="hint-nominal">*Masukkan nominal cicilan</small>
                    </div>
                    <input type="hidden" id="sale_id_pelunasan">
                    <input type="hidden" id="sisa_tagihan_raw">
                    <input type="hidden" id="metode_pelunasan" value="cash">
                </div>
                <div class="modal-footer border-0 p-4 bg-white">
                    <button type="button" onclick="submitPelunasan()"
                        class="btn btn-primary rounded-pill w-100 fw-bold py-2">PROSES BAYAR</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            flatpickr(".datepicker", {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d/m/Y",
                locale: "id"
            });

            // --- LOGIKA VERIFIKASI SEPERTI DASHBOARD ---
            const urlParams = new URLSearchParams(window.location.search);
            // Cek parameter 'status_cicilan=success' dan 'external_id'
            const statusCicilan = urlParams.get('status_cicilan');
            const externalId = urlParams.get('external_id');

            if (statusCicilan === 'success' && externalId) {
                // Bersihkan URL
                window.history.replaceState({}, document.title, window.location.pathname);

                // Tampilkan Loading
                Swal.fire({
                    title: 'Memverifikasi Pembayaran...',
                    text: 'Mohon tunggu, sedang menghubungi server pembayaran.',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                // Panggil API Verifikasi
                fetch(`/transaksi/cek-cicilan/${externalId}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            // SUKSES: Update Tampilan
                            Swal.fire({
                                title: 'Pembayaran Diterima!',
                                text: `Cicilan Rp ${new Intl.NumberFormat('id-ID').format(data.amount)} berhasil ditambahkan.`,
                                icon: 'success'
                            }).then(() => {
                                location.reload();
                            });
                        } else if (data.status === 'pending') {
                            Swal.fire('Belum Lunas', 'Pembayaran belum terdeteksi di Xendit.', 'info');
                        } else {
                            // Tampilkan Pesan Error Server jika Gagal
                            Swal.fire('Gagal Verifikasi', data.message, 'error');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire('Error', 'Gagal koneksi ke server.', 'error');
                    });
            }
        });

        function cetakStruk(id) {
            window.open(`/transaksi/struk/${id}`, '_blank', 'width=400,height=600');
        }

        function lunasiUtang(e, id, nama, total, sudahBayar) {
            e.preventDefault();
            let sisa = total - sudahBayar;
            const fmt = new Intl.NumberFormat('id-ID');

            document.getElementById('sale_id_pelunasan').value = id;
            document.getElementById('sisa_tagihan_raw').value = sisa;
            document.getElementById('text-pelanggan').innerText = nama;
            document.getElementById('text-total').innerText = fmt.format(total);
            document.getElementById('text-bayar').innerText = fmt.format(sudahBayar);
            document.getElementById('text-sisa').innerText = fmt.format(sisa);
            document.getElementById('input_bayar_pelunasan').value = sisa;

            pilihMetode('cash', document.querySelectorAll('.btn-metode')[0]);
            new bootstrap.Modal(document.getElementById('modalPelunasan')).show();
        }

        function pilihMetode(metode, element) {
            document.querySelectorAll('.btn-metode').forEach(el => el.classList.remove('selected'));
            if (element) element.classList.add('selected');
            document.getElementById('metode_pelunasan').value = metode;

            // Input SELALU ditampilkan
            let box = document.getElementById('box-input-bayar');
            box.style.display = 'block';
            document.getElementById('hint-nominal').innerText = (metode === 'online') ?
                '*Invoice QRIS akan dibuat sesuai nominal ini' :
                '*Masukkan uang diterima';
        }

        function submitPelunasan() {
            let id = document.getElementById('sale_id_pelunasan').value;
            let metode = document.getElementById('metode_pelunasan').value;
            let bayar = parseInt(document.getElementById('input_bayar_pelunasan').value) || 0;
            let sisa = parseInt(document.getElementById('sisa_tagihan_raw').value) || 0;

            if (bayar <= 0) return Swal.fire('Error', 'Nominal wajib diisi', 'warning');
            if (bayar > sisa) return Swal.fire('Error', 'Melebihi sisa hutang', 'warning');

            bootstrap.Modal.getInstance(document.getElementById('modalPelunasan')).hide();
            Swal.fire({
                title: 'Memproses...',
                didOpen: () => Swal.showLoading()
            });

            let token = document.querySelector('meta[name="csrf-token"]') ? document.querySelector(
                'meta[name="csrf-token"]').getAttribute('content') : '{{ csrf_token() }}';

            fetch(`/transaksi/lunasi/${id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({
                        payment_method: metode,
                        bayar_nominal: bayar
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        if (data.type === 'online') window.location.href = data.invoice_url;
                        else Swal.fire('Berhasil', data.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Gagal', data.message, 'error');
                    }
                })
                .catch(err => Swal.fire('Error', 'Terjadi kesalahan sistem', 'error'));
        }
    </script>
@endpush
