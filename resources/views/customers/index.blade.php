@extends('layouts.app')

@section('title', 'Data Customer')

@push('styles')
    <style>
        /* Hover effect */
        #customersTable tbody tr:hover {
            background-color: #f1f5f9;
        }

        /* Truncate alamat */
        td.address {
            max-width: 250px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Warna header tabel */
        .table-primary th {
            background-color: #4f46e5;
            color: #fff;
        }

        /* Dropdown */
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
        <h4 class="mb-0">Data Customer</h4>
        <button type="button" class="btn btn-primary" id="btnAddCustomer">
            <i class="bi bi-plus-lg me-1"></i> Tambah Customer
        </button>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-3">
            <div class="table-responsive">
                <table id="customersTable" class="table table-striped table-hover table-bordered align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            <th>Nama Customer</th>
                            <th>No. Telepon</th>
                            <th>Alamat</th>
                            <th>Tanggal Daftar</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customers as $customer)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $customer->customer_name }}</td>
                                <td>{{ $customer->phone }}</td>
                                <td class="address" title="{{ $customer->address }}">
                                    {{ Str::limit($customer->address, 50) }}
                                </td>
                                <td>{{ $customer->created_at->format('d/m/Y') }}</td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-primary dropdown-toggle" type="button"
                                            id="dropdownMenuButton{{ $customer->id }}" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $customer->id }}">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('customers.show', $customer) }}">
                                                    <i class="bi bi-eye me-1"></i> Detail
                                                </a>
                                            </li>
                                            <li>
                                                <button type="button" class="dropdown-item btnEditCustomer"
                                                    data-id="{{ $customer->id }}"
                                                    data-name="{{ $customer->customer_name }}"
                                                    data-phone="{{ $customer->phone }}"
                                                    data-address="{{ $customer->address }}">
                                                    <i class="bi bi-pencil me-1"></i> Edit
                                                </button>
                                            </li>
                                            <li>
                                                <a href="{{ route('customers.destroy', $customer->id) }}"
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
    @include('customers.modalFormCustomer')
@endsection

@push('scripts')
    <script>
        const modalCustomer = new bootstrap.Modal(document.getElementById('modalFormCustomer'));
        const customerForm = document.getElementById('customerForm');
        const formMethod = document.getElementById('formMethod');
        const modalTitle = document.getElementById('modalTitle');
        const nameInput = document.getElementById('customer_name');
        const phoneInput = document.getElementById('phone');
        const addressInput = document.getElementById('address');

        function openCustomerModal(mode, data = null) {
            if (mode === "create") {
                modalTitle.textContent = "Tambah Customer";
                customerForm.action = "{{ route('customers.store') }}";
                formMethod.value = "POST";
                nameInput.value = "";
                phoneInput.value = "";
                addressInput.value = "";
            } else if (mode === "edit" && data) {
                modalTitle.textContent = "Edit Customer";
                customerForm.action = "{{ url('customers') }}/" + data.id;
                formMethod.value = "PUT";
                nameInput.value = data.name;
                phoneInput.value = data.phone;
                addressInput.value = data.address;
            }
            modalCustomer.show();
        }

        // Tambah customer
        document.getElementById('btnAddCustomer').addEventListener('click', function() {
            openCustomerModal("create");
        });

        // Edit customer
        document.querySelectorAll('.btnEditCustomer').forEach(button => {
            button.addEventListener('click', function() {
                const data = {
                    id: this.getAttribute('data-id'),
                    name: this.getAttribute('data-name'),
                    phone: this.getAttribute('data-phone'),
                    address: this.getAttribute('data-address'),
                };
                openCustomerModal("edit", data);
            });
        });

        // DataTable
        $(document).ready(function() {
            $('#customersTable').DataTable({
                paging: true,
                lengthChange: true,
                searching: true,
                ordering: true,
                info: true,
                autoWidth: false,
                responsive: true,
                columnDefs: [{
                    orderable: false,
                    searchable: false,
                    targets: 5
                }],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Cari customer...",
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
