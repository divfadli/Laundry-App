<!-- Modal Form Customer (Tambah & Edit) -->
<div class="modal fade" id="modalFormCustomer" tabindex="-1" aria-labelledby="modalFormCustomerLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form id="customerForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <!-- Header -->
                <div class="modal-header bg-primary text-white rounded-top-4">
                    <h5 class="modal-title fw-bold d-flex align-items-center" id="modalFormCustomerLabel">
                        <i class="bi bi-person-plus me-2"></i>
                        <span id="modalTitle">Tambah Customer</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <!-- Body -->
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="customer_name" class="form-label fw-semibold">
                            <i class="bi bi-person-circle text-primary me-1"></i> Nama
                        </label>
                        <input type="text" name="customer_name" id="customer_name" class="form-control rounded-3"
                            placeholder="Masukkan nama customer" required>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label fw-semibold">
                            <i class="bi bi-telephone text-success me-1"></i> No. Telepon
                        </label>
                        <input type="number" name="phone" id="phone" class="form-control rounded-3"
                            placeholder="08xxxxxxxxxx" required>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label fw-semibold">
                            <i class="bi bi-geo-alt text-danger me-1"></i> Alamat
                        </label>
                        <textarea name="address" id="address" class="form-control rounded-3" rows="3"
                            placeholder="Masukkan alamat lengkap" required></textarea>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer border-0 p-3">
                    <button type="button" class="btn btn-light border rounded-3" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4" id="btnSubmitCustomer">
                        <i class="bi bi-save me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
