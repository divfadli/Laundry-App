@extends('layouts.app')

@section('title', 'Data Service')

@push('styles')
    <style>
        /* Hover effect */
        #servicesTable tbody tr:hover {
            background-color: #f1f5f9;
        }

        /* Warna header tabel */
        .table-primary th {
            background-color: #4f46e5;
            color: #fff;
        }

        .dropdown-toggle::after {
            margin-left: 0.25rem;
        }

        .dropdown-menu button,
        .dropdown-menu a {
            width: 100%;
            text-align: left;
        }

        .modal {
            z-index: 2000 !important;
        }

        .modal-backdrop {
            z-index: 1999 !important;
        }
    </style>
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Data Jenis Service</h4>
        <button type="button" class="btn btn-primary" id="btnAddService">
            <i class="bi bi-plus-lg me-1"></i> Tambah Service
        </button>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-3">
            <div class="table-responsive">
                <table id="servicesTable" class="table table-striped table-hover table-bordered align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            <th>Nama Service</th>
                            <th>Harga</th>
                            <th>Deskripsi</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($services as $service)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $service->service_name }}</td>
                                <td>Rp. {{ number_format($service->price, 2, ',', '.') }}</td>
                                <td>{{ $service->description ?? '-' }}</td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-primary dropdown-toggle" type="button"
                                            id="dropdownMenuButton{{ $service->id }}" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $service->id }}">
                                            <li>
                                                <button type="button" class="dropdown-item btnEditService"
                                                    data-id="{{ $service->id }}" data-name="{{ $service->service_name }}"
                                                    data-price="{{ $service->price }}"
                                                    data-description="{{ $service->description }}">
                                                    <i class="bi bi-pencil me-1"></i> Edit
                                                </button>
                                            </li>
                                            <li>
                                                <a href="{{ route('services.destroy', $service->id) }}"
                                                    class="btn btn-danger" data-confirm-delete="true">
                                                    <i class="bi bi-trash me-1"></i> Hapus
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('modals')
    @include('services.modalFormService')
@endsection

@push('scripts')
    <script>
        const modalService = new bootstrap.Modal(document.getElementById('modalFormService'));
        const serviceForm = document.getElementById('serviceForm');
        const formMethod = document.getElementById('formMethod');
        const modalTitle = document.getElementById('modalTitle');
        const nameInput = document.getElementById('service_name');
        const priceInput = document.getElementById('price');
        const descInput = document.getElementById('description');

        function openServiceModal(mode, data = null) {
            if (mode === "create") {
                modalTitle.textContent = "Tambah Service";
                serviceForm.action = "{{ route('services.store') }}";
                formMethod.value = "POST";
                nameInput.value = "";
                priceInput.value = "";
                descInput.value = "";
            } else if (mode === "edit" && data) {
                modalTitle.textContent = "Edit Service";
                serviceForm.action = "{{ url('services') }}/" + data.id;
                formMethod.value = "PUT";
                nameInput.value = data.name;
                priceInput.value = data.price;
                descInput.value = data.description;
            }
            modalService.show();
        }

        // Tambah Service
        document.getElementById('btnAddService').addEventListener('click', function() {
            openServiceModal("create");
        });

        // Edit Service
        document.querySelectorAll('.btnEditService').forEach(button => {
            button.addEventListener('click', function() {
                const data = {
                    id: this.getAttribute('data-id'),
                    name: this.getAttribute('data-name'),
                    price: this.getAttribute('data-price'),
                    description: this.getAttribute('data-description'),
                };
                openServiceModal("edit", data);
            });
        });

        // DataTable
        $(document).ready(function() {
            $('#servicesTable').DataTable({
                paging: true,
                lengthChange: true,
                searching: true,
                ordering: true,
                info: true,
                autoWidth: false,
                responsive: true,
                columnDefs: [{
                    orderable: false,
                    targets: 4
                }],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Cari service...",
                    lengthMenu: "Tampilkan _MENU_ data",
                    // info: "Menampilkan _START_-_END_ dari _TOTAL_ data",
                    // infoEmpty: "Tidak ada data tersedia",
                    // zeroRecords: "Data tidak ditemukan",
                    emptyTable: "Tidak ada data tersedia"
                }
            });
        });
    </script>
@endpush
