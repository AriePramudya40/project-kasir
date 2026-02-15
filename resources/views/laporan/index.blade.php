@extends('layouts.app')

@section('title', 'Laporan Keuangan')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .flatpickr-day.selected,
        .flatpickr-day.startRange,
        .flatpickr-day.endRange,
        .flatpickr-day.selected.inRange,
        .flatpickr-day.selected:focus,
        .flatpickr-day.selected:hover {
            background: var(--brand-red, #9A1B1F) !important;
            border-color: var(--brand-red, #9A1B1F) !important;
        }

        .flatpickr-input {
            background-color: transparent !important;
            color: inherit !important;
        }

        body.dark-mode .table thead {
            background-color: rgba(255, 255, 255, 0.05) !important;
            color: var(--text-main) !important;
        }

        body.dark-mode .table thead th {
            border-bottom-color: rgba(255, 255, 255, 0.1);
        }

        body.dark-mode .flatpickr-input {
            color: var(--text-main) !important;
        }

        /* Style Select Custom agar menyatu */
        .form-select-pill {
            border: none;
            background-color: transparent;
            font-size: 0.875rem;
            color: var(--text-main);
            padding-right: 2rem;
            cursor: pointer;
        }

        .form-select-pill:focus {
            box-shadow: none;
        }

        body.dark-mode .form-select-pill {
            color: var(--text-main);
            background-color: var(--bg-card);
        }

        body.dark-mode .form-select-pill option {
            background-color: var(--bg-card);
            color: var(--text-main);
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h4 class="fw-bold text-dark mb-0"><i class="bi bi-bar-chart-fill text-danger me-2"></i>Laporan Keuangan</h4>
                <small class="text-muted">Pantau arus kas masuk (Cashflow) & Piutang.</small>
            </div>

            <form action="{{ route('laporan.index') }}" method="GET"
                class="d-flex gap-2 bg-white p-2 rounded-pill shadow-sm border align-items-center">

                <div class="input-group input-group-sm" style="width: 140px;">
                    <span class="input-group-text bg-transparent border-0 pe-0"><i
                            class="bi bi-calendar3 text-muted"></i></span>
                    <input type="text" name="start_date" class="form-control border-0 bg-transparent datepicker ps-2"
                        value="{{ $startDate }}" placeholder="Mulai">
                </div>
                <span class="text-muted fw-bold">-</span>
                <div class="input-group input-group-sm" style="width: 140px;">
                    <span class="input-group-text bg-transparent border-0 pe-0"><i
                            class="bi bi-calendar3 text-muted"></i></span>
                    <input type="text" name="end_date" class="form-control border-0 bg-transparent datepicker ps-2"
                        value="{{ $endDate }}" placeholder="Akhir">
                </div>

                <div class="vr mx-1 opacity-25"></div>

                <div class="input-group input-group-sm" style="width: 130px;">
                    <span class="input-group-text bg-transparent border-0 pe-0"><i
                            class="bi bi-funnel text-muted"></i></span>
                    <select name="payment_method" class="form-select form-select-pill ps-2 fw-bold"
                        onchange="this.form.submit()">
                        <option value="all" {{ $selectedMethod == 'all' ? 'selected' : '' }}>Semua</option>
                        <option value="cash" {{ $selectedMethod == 'cash' ? 'selected' : '' }}>Tunai</option>
                        <option value="online" {{ $selectedMethod == 'online' ? 'selected' : '' }}>Online</option>
                        <option value="utang" {{ $selectedMethod == 'utang' ? 'selected' : '' }}>Utang</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-brand btn-sm rounded-pill px-3 ms-1"><i
                        class="bi bi-search me-1"></i></button>
            </form>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 bg-success text-white"
                    style="background: linear-gradient(135deg, #198754, #20c997);">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-2 text-white-50">
                            <i class="bi bi-wallet2 fs-4 me-2"></i>
                            <span class="small fw-bold text-uppercase">Total Uang Masuk</span>
                        </div>
                        <h2 class="fw-bold mb-0">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h2>
                        <small class="text-white-50" style="font-size: 0.75rem;">(Tunai + Online)</small>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 bg-warning text-dark"
                    style="background: linear-gradient(135deg, #ffc107, #ffdb72);">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-2 text-dark opacity-75">
                            <i class="bi bi-clock-history fs-4 me-2"></i>
                            <span class="small fw-bold text-uppercase">Total Piutang</span>
                        </div>
                        <h2 class="fw-bold mb-0">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</h2>
                        <small class="text-dark opacity-75" style="font-size: 0.75rem;">(Menunggu Pembayaran)</small>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-muted mb-3 text-uppercase small">Rincian Kas Periode Ini</h6>
                        <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                            <span class="text-muted small">Tunai</span>
                            <span class="fw-bold text-success">Rp {{ number_format($totalTunai, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                            <span class="text-muted small">Online</span>
                            <span class="fw-bold text-info">Rp {{ number_format($totalOnline, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Total Transaksi</span>
                            <span class="badge bg-secondary rounded-pill">{{ $totalTransaksi }} Struk</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <h6 class="fw-bold m-0 text-dark">Riwayat Transaksi</h6>
                    @if ($selectedMethod != 'all')
                        <span class="badge bg-danger-subtle text-danger rounded-pill px-3">
                            Filter: {{ ucfirst($selectedMethod) }}
                        </span>
                    @endif
                </div>
                <button onclick="window.print()" class="btn btn-sm btn-light border rounded-pill"><i
                        class="bi bi-printer me-1"></i> Print</button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase text-center">
                        <tr>
                            <th class="ps-4 py-3 text-start">Tanggal</th>
                            <th>No. Faktur</th>
                            <th>Pelanggan</th>
                            <th>Kasir</th>
                            <th>Metode</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Total</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0 text-center">
                        @forelse($sales as $sale)
                            <tr>
                                <td class="ps-4 text-muted small text-start">
                                    <div class="fw-bold text-dark">{{ $sale->created_at->format('d/m/Y') }}</div>
                                    <div>{{ $sale->created_at->format('H:i') }} WIB</div>
                                </td>
                                <td class="fw-bold text-primary">{{ $sale->no_faktur }}</td>
                                <td>{{ $sale->customer_name ?? '-' }}</td>
                                <td>{{ $sale->user->name ?? 'Unknown' }}</td>
                                <td>
                                    @if ($sale->payment_method == 'cash')
                                        <span
                                            class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3">Tunai</span>
                                    @elseif(in_array($sale->payment_method, ['online', 'qris']))
                                        <span
                                            class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-3">Online</span>
                                    @else
                                        <span
                                            class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3">Piutang</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($sale->status == 'lunas' || $sale->status == 'paid')
                                        <i class="bi bi-check-circle-fill text-success fs-5" title="Lunas"></i>
                                    @else
                                        <i class="bi bi-clock-history text-warning fs-5" title="Belum Lunas"></i>
                                    @endif
                                </td>
                                <td class="text-end pe-4 fw-bold text-dark">Rp
                                    {{ number_format($sale->grand_total, 0, ',', '.') }}</td>
                                <td class="text-end pe-3">
                                    <a href="javascript:void(0)" onclick="cetakStruk({{ $sale->id }})"
                                        class="btn btn-sm btn-light text-muted rounded-circle shadow-sm"
                                        style="width: 32px; height: 32px;">
                                        <i class="bi bi-printer-fill"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">Tidak ada data transaksi
                                    {{ $selectedMethod != 'all' ? 'untuk metode ini' : '' }}.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function cetakStruk(id) {
            window.open(`/transaksi/struk/${id}`, '_blank', 'width=400,height=600');
        }
    </script>
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
                locale: "id",
                allowInput: true,
                disableMobile: "true"
            });
        });
    </script>
@endpush
