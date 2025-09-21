@extends('layouts.app')

@section('title', 'Detail Customer')

@section('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endsection

@section('content')
    <div class="row g-3">
        <!-- Customer Info Card -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i>Informasi Customer</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-3">
                        <tr>
                            <th class="w-50">Nama</th>
                            <td>{{ $customer->customer_name }}</td>
                        </tr>
                        <tr>
                            <th>Telepon</th>
                            <td>{{ $customer->phone }}</td>
                        </tr>
                        <tr>
                            <th>Alamat</th>
                            <td>{{ $customer->address }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Daftar</th>
                            <td>{{ $customer->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>

                    <div class="d-flex gap-2">
                        <a href="{{ route('customers.edit', $customer) }}" class="btn btn-warning btn-sm flex-grow-1">
                            <i class="bi bi-pencil-square me-1"></i>Edit
                        </a>
                        <a href="{{ route('customers.index') }}" class="btn btn-secondary btn-sm flex-grow-1">
                            <i class="bi bi-arrow-left-circle me-1"></i>Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transaction History Card -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Riwayat Transaksi</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive mt-3">
                        <table id="transactionsTable" class="table table-striped table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Kode Order</th>
                                    <th>Tanggal Pemesanan</th>
                                    <th>Tanggal Pengembalian</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($customer->transOrders as $order)
                                    <tr>
                                        <td>{{ $order->order_code }}</td>
                                        <td>{{ $order->order_date->format('d/m/Y') }}</td>
                                        <td>{{ $order->order_end_date->format('d/m/Y') }}</td>
                                        <td>Rp. {{ number_format($order->total, 0, ',', '.') }}</td>
                                        <td>
                                            <span
                                                class="badge {{ $order->status_class }} py-2 px-3">{{ $order->status_text }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('orders.show', $order) }}" class="btn btn-info btn-sm">
                                                <i class="bi bi-eye"></i> Lihat
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#transactionsTable').DataTable({
                responsive: true,
                pageLength: 5,
                lengthChange: false,
                searching: false,
                ordering: true,
                order: [
                    [1, 'desc']
                ],
                language: {
                    emptyTable: "Belum ada transaksi"
                }
            });

        });
    </script>
@endpush
