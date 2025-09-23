@extends('layouts.app')

@section('title', 'Daftar Transaksi Laundry')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <h4 class="mb-2 mb-md-0">
            <i class="bi bi-basket2 me-2"></i> Daftar Transaksi Laundry
        </h4>

        @if (auth()->user()->isAdmin() || auth()->user()->isOperator())
            <a href="{{ route('orders.transaction') }}" class="btn btn-primary btn-sm shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Tambah Transaksi
            </a>
        @endif
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive p-3">
                <table id="table-transactions" class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Kode Transaksi</th>
                            <th>Nama Customer</th>
                            <th>Tanggal</th>
                            <th>Total Bayar</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="fw-bold text-primary">
                                    <a href="{{ route('orders.show', $order) }}">
                                        {{ $order->order_code }}
                                    </a>
                                </td>
                                <td>{{ $order->customer->customer_name ?? 'Customer Dihapus' }}</td>
                                <td>{{ $order->order_date->format('d/m/Y') }}</td>
                                <td class="text-success fw-semibold">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge rounded-pill {{ $order->status_class }} py-2 px-3">
                                        {{ $order->order_status == 0 ? 'Process' : 'Completed' }}

                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group gap-1" role="group">
                                        <a href="{{ route('orders.print', $order) }}" class="btn btn-sm btn-secondary"
                                            data-bs-toggle="tooltip" data-bs-title="Print Struk">
                                            <i class="bi bi-printer-fill"></i>
                                        </a>
                                        <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-info"
                                            data-bs-toggle="tooltip" data-bs-title="Lihat Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
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

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#table-transactions').DataTable({
                responsive: true,
                pageLength: 5,
                lengthChange: false,
                ordering: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Cari transaksi laundry..."
                }
            });

            // Bootstrap Tooltip
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(el) {
                return new bootstrap.Tooltip(el);
            });
        });
    </script>
@endpush

