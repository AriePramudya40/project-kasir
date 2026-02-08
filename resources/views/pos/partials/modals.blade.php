<div class="modal fade" id="modalTambahBarang" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-brand text-white border-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-box-seam me-2"></i>Tambah Barang Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('produk.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-upc-scan text-muted me-1"></i> Kode Barang
                        </label>
                        <input type="text" name="kode" class="form-control" placeholder="Contoh: BRG005" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-box-seam text-muted me-1"></i> Nama Barang
                        </label>
                        <input type="text" name="nama" class="form-control" placeholder="Contoh: Palu Godam"
                            required>
                        <small class="text-muted fst-italic d-block mt-1">
                            <i class="bi bi-info-circle"></i> Gambar akan dicari otomatis dari Google.
                        </small>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="bi bi-cash text-muted me-1"></i> Harga Jual (Rp)
                            </label>
                            <input type="number" name="harga" class="form-control" placeholder="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="bi bi-stack text-muted me-1"></i> Stok Awal
                            </label>
                            <input type="number" name="stok" class="form-control" placeholder="0" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 p-3">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-brand fw-bold px-4 shadow-sm">
                        <i class="bi bi-check-circle-fill me-1"></i> Simpan Barang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditBarang" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-brand text-white border-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-pencil-square me-2"></i>Edit Barang
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditBarang" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-upc-scan text-muted me-1"></i> Kode Barang
                        </label>
                        <input type="text" name="kode" id="edit-kode" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-box-seam text-muted me-1"></i> Nama Barang
                        </label>
                        <input type="text" name="nama" id="edit-nama" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="bi bi-cash text-muted me-1"></i> Harga Jual (Rp)
                            </label>
                            <input type="number" name="harga" id="edit-harga" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="bi bi-stack text-muted me-1"></i> Stok
                            </label>
                            <input type="number" name="stok" id="edit-stok" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-check p-3 bg-light rounded">
                        <input class="form-check-input" type="checkbox" name="refresh_image" id="edit-refresh-image"
                            value="1">
                        <label class="form-check-label fw-bold" for="edit-refresh-image">
                            <i class="bi bi-arrow-clockwise text-primary"></i> Refresh Gambar Otomatis dari Google
                        </label>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 p-3">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-brand fw-bold px-4 shadow-sm">
                        <i class="bi bi-check-circle-fill me-1"></i> Update Barang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
