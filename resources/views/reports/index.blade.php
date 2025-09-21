@extends('layouts.app')

@section('title', 'Laporan Penjualan')

@push('styles')
    <style>
        /* Center "Tidak ada data transaksi" */
        table.dataTable td.dataTables_empty {
            text-align: center !important;
            vertical-align: middle !important;
            font-weight: 500;
            color: #6c757d;
        }

        /* Statistik cards */
        .stats-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        /* Table hover effect */
        #reportTable tbody tr:hover {
            background-color: #f1f3f5;
        }

        /* Badge styling */
        .badge-status {
            font-size: 0.85rem;
            padding: 0.35em 0.6em;
        }

        /* Filter & Export gap */
        .filter-export {
            gap: 1rem;
        }

        /* Responsive typography */
        .card h4,
        .card h6 {
            margin: 0;
        }
    </style>
@endpush

@section('content')

    {{-- Filter & Export --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-body">

            {{-- Filter --}}
            <form method="GET" action="{{ route('reports.index') }}" class="row g-3 align-items-end mb-3">
                <div class="col-md-3">
                    <label for="start_date" class="form-label fw-semibold">Tanggal Mulai</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label fw-semibold">Tanggal Akhir</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                </div>
            </form>

            {{-- Export --}}
            <div class="d-flex filter-export">
                <a id="exportPdf" target="_blank" class="btn btn-danger" data-bs-toggle="tooltip" title="Download PDF">
                    <i class="bi bi-file-earmark-pdf me-1"></i> PDF
                </a>
                <a id="exportExcel" class="btn btn-success" data-bs-toggle="tooltip" title="Download Excel">
                    <i class="bi bi-file-earmark-excel me-1"></i> Excel
                </a>
            </div>

        </div>
    </div>

    {{-- Statistik --}}
    <div class="row mb-4 stats-section">
        @php
            $stats = [
                [
                    'icon' => 'bi-currency-dollar',
                    'title' => 'Total Pendapatan',
                    'value' => $totalRevenue,
                    'color' => 'success',
                    'is_currency' => true,
                ],
                ['icon' => 'bi-receipt', 'title' => 'Total Order', 'value' => $totalOrders, 'color' => 'primary'],
                [
                    'icon' => 'bi-check-circle',
                    'title' => 'Order Selesai',
                    'value' => $completedOrders,
                    'color' => 'success',
                ],
                [
                    'icon' => 'bi-clock-history',
                    'title' => 'Order Pending',
                    'value' => $pendingOrders,
                    'color' => 'warning',
                ],
            ];
        @endphp

        @foreach ($stats as $stat)
            <div class="col-md-3 mb-3">
                <div class="card stats-card text-center shadow-sm">
                    <div class="card-body">
                        <i class="bi {{ $stat['icon'] }} display-5 text-{{ $stat['color'] }}"></i>
                        <h6 class="mt-2">{{ $stat['title'] }}</h6>
                        <h4 class="fw-bold text-{{ $stat['color'] }} stat-number" data-target="{{ $stat['value'] }}"
                            data-currency="{{ !empty($stat['is_currency']) ? 'true' : 'false' }}">
                            @if (!empty($stat['is_currency']))
                                Rp. {{ number_format($stat['value'], 0, ',', '.') }}
                            @else
                                {{ number_format($stat['value'], 0, ',', '.') }}
                            @endif
                        </h4>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Detail Transaksi --}}
    <div class="card
                            shadow-sm">
        <div class="card-header">
            <h5 class="mb-0 fw-semibold"><i class="bi bi-table me-1"></i> Detail Transaksi</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="reportTable" class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Kode Order</th>
                            <th>Customer</th>
                            <th>Tanggal Order</th>
                            <th>Estimasi Selesai</th>
                            <th>Tanggal Pengambilan</th>
                            <th>Layanan</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $order->order_code }}</td>
                                <td>{{ $order->customer->customer_name }}</td>
                                <td>{{ $order->order_date->format('d/m/Y') }}</td>
                                <td>{{ $order->order_end_date->format('d/m/Y') }}</td>
                                <td>{{ optional($order->transLaundryPickups)->pickup_date?->format('d/m/Y') ?? '-' }}
                                </td>
                                <td>
                                    @foreach ($order->transOrderDetails as $detail)
                                        <small class="d-block">
                                            {{ $detail->typeOfService->service_name }}
                                            ({{ $detail->qty }}kg)
                                        </small>
                                    @endforeach
                                </td>
                                <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge badge-status {{ $order->status_class }}">
                                        {{ $order->status_text }}
                                    </span>
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
            // DataTables
            const table = $('#reportTable').DataTable({
                responsive: true,
                pageLength: 10,
                lengthChange: false,
                ordering: true,
                order: [
                    [1, 'desc']
                ],
                language: {
                    emptyTable: "Tidak ada data transaksi"
                }
            });

            // Tooltip
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(el => new bootstrap.Tooltip(el));

            // Export links
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            const exportPdf = document.getElementById('exportPdf');
            const exportExcel = document.getElementById('exportExcel');

            function updateExportLinks() {
                const start = startDateInput.value;
                const end = endDateInput.value;
                exportPdf.href = `{{ url('reports/export/pdf') }}?start_date=${start}&end_date=${end}`;
                exportExcel.href = `{{ url('reports/export/excel') }}?start_date=${start}&end_date=${end}`;
            }

            startDateInput.addEventListener('change', updateExportLinks);
            endDateInput.addEventListener('change', updateExportLinks);

            // Initialize links on page load
            updateExportLinks();
        });
    </script>

    <script src="{{ asset('js/animateCounter.js') }}"></script>
@endpush
