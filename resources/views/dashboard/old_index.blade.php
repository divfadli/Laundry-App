@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    @php
        $user = auth()->user();
    @endphp

    @if ($user->isAdmin())
        {{-- ==================== ADMIN DASHBOARD ==================== --}}
        <div class="row g-4 stat-card">
            @php
                $stats = [
                    [
                        'icon' => 'bi-people',
                        'label' => 'Total Customers',
                        'color' => 'primary',
                        'value' => $data['total_customers'],
                    ],
                    [
                        'icon' => 'bi-receipt',
                        'label' => 'Total Orders',
                        'color' => 'success',
                        'value' => $data['total_orders'],
                    ],
                    [
                        'icon' => 'bi-clock-history',
                        'label' => 'Pending Orders',
                        'color' => 'warning',
                        'value' => $data['pending_orders'],
                    ],
                    [
                        'icon' => 'bi-currency-dollar',
                        'label' => 'Pendapatan Hari Ini',
                        'color' => 'info',
                        'value' => $data['today_revenue'],
                        'is_currency' => true,
                    ],
                ];
            @endphp
            @foreach ($stats as $stat)
                <div class="col-md-3">
                    <div class="card hover-card">
                        <div class="card-body text-center mt-4 stats-section">
                            <i class="bi {{ $stat['icon'] }} display-4 text-{{ $stat['color'] }}"></i>
                            <h6 class="mt-3 text-muted">{{ $stat['label'] }}</h6>
                            <h2 class="fw-bold stat-number text-{{ $stat['color'] }}" data-target="{{ $stat['value'] }}"
                                data-currency="{{ !empty($stat['is_currency']) ? 'true' : 'false' }}">
                                @if (!empty($stat['is_currency']))
                                    Rp. {{ number_format($stat['value'], 0, ',', '.') }}
                                @else
                                    {{ number_format($stat['value'], 0, ',', '.') }}
                                @endif
                            </h2>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row g-4 mt-2">
            {{-- Order Terbaru --}}
            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom-0 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="bi bi-bag-check text-primary"></i> Order Terbaru</h5>
                        <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-primary">
                            Lihat Semua <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Kode Order</th>
                                        <th>Customer</th>
                                        <th>Tanggal</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data['recent_orders'] as $order)
                                        <tr>
                                            <td>{{ $order->order_code }}</td>
                                            <td>{{ $order->customer->customer_name ?? '-' }}</td>
                                            <td>{{ $order->order_date->format('d/m/Y') }}</td>
                                            <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                            <td><span
                                                    class="badge rounded-pill {{ $order->status_class }}">{{ $order->status_text }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Statistik Quick --}}
            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white border-bottom-0">
                        <h5 class="card-title mb-0"><i class="bi bi-graph-up text-success"></i> Statistik Quick</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center stats-section">
                            <div class="col-12 mb-3">
                                <h6 class="text-muted">Orders Hari Ini</h6>
                                <h3 class="fw-bold text-primary stat-number" data-target="{{ $data['orders_today'] }}">
                                    {{ number_format($data['orders_today'], 0, ',', '.') }}
                                </h3>
                            </div>
                            <div class="col-6 mb-3">
                                <h6 class="text-muted">Selesai</h6>
                                <h4 class="fw-bold text-success stat-number" data-target="{{ $data['completed_orders'] }}">
                                    {{ number_format($data['completed_orders'], 0, ',', '.') }}
                                </h4>
                            </div>
                            <div class="col-6 mb-3">
                                <h6 class="text-muted">Pending</h6>
                                <h4 class="fw-bold text-warning stat-number" data-target="{{ $data['pending_orders'] }}">
                                    {{ number_format($data['pending_orders'], 0, ',', '.') }}
                                </h4>
                            </div>
                            <div class="col-12">
                                <h6 class="text-muted">Total Services</h6>
                                <h4 class="fw-bold text-info stat-number" data-target="{{ $data['total_services'] }}">
                                    {{ number_format($data['total_services'], 0, ',', '.') }}
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @elseif($user->isOperator())
        {{-- ==================== OPERATOR DASHBOARD ==================== --}}
        <div class="row g-4 stat-card">
            <div class="col-md-4">
                <div class="card hover-card text-center">
                    <div class="card-body mt-4">
                        <i class="bi bi-people display-4 text-primary"></i>
                        <h6 class="mt-3 text-muted">Tambah Customer</h6>
                        <a href="{{ route('customers.create') }}" class="btn btn-primary btn-sm mt-2">Tambah</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card hover-card text-center">
                    <div class="card-body mt-4">
                        <i class="bi bi-receipt display-4 text-success"></i>
                        <h6 class="mt-3 text-muted">Total Orders</h6>
                        <h2 class="fw-bold">{{ number_format($data['total_orders'], 0, ',', '.') }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card hover-card text-center">
                    <div class="card-body mt-4">
                        <i class="bi bi-clock-history display-4 text-warning"></i>
                        <h6 class="mt-3 text-muted">Pending Orders</h6>
                        <h2 class="fw-bold">{{ number_format($data['pending_orders'], 0, ',', '.') }}</h2>
                    </div>
                </div>
            </div>
        </div>

        {{-- Order Terbaru --}}
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom-0 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="bi bi-bag-check text-primary"></i> Order Terbaru</h5>
                        <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua <i
                                class="bi bi-arrow-right"></i></a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Kode Order</th>
                                        <th>Customer</th>
                                        <th>Tanggal</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data['recent_orders'] as $order)
                                        <tr>
                                            <td>{{ $order->order_code }}</td>
                                            <td>{{ $order->customer->customer_name ?? '-' }}</td>
                                            <td>{{ $order->order_date->format('d/m/Y') }}</td>
                                            <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                            <td><span
                                                    class="badge rounded-pill {{ $order->status_class }}">{{ $order->status_text }}</span>
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
    @elseif($user->isPimpinan())
        {{-- ==================== PIMPINAN DASHBOARD ==================== --}}
        <div class="row g-4 stat-card">
            <div class="col-md-3">
                <div class="card hover-card text-center">
                    <div class="card-body mt-4">
                        <i class="bi bi-receipt display-4 text-success"></i>
                        <h6 class="mt-3 text-muted">Total Orders</h6>
                        <h2 class="fw-bold">{{ number_format($data['total_orders'], 0, ',', '.') }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card hover-card text-center">
                    <div class="card-body mt-4">
                        <i class="bi bi-clock-history display-4 text-warning"></i>
                        <h6 class="mt-3 text-muted">Pending Orders</h6>
                        <h2 class="fw-bold">{{ number_format($data['pending_orders'], 0, ',', '.') }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card hover-card text-center">
                    <div class="card-body mt-4">
                        <i class="bi bi-currency-dollar display-4 text-info"></i>
                        <h6 class="mt-3 text-muted">Pendapatan Hari Ini</h6>
                        <h2 class="fw-bold">Rp {{ number_format($data['today_revenue'], 0, ',', '.') }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card hover-card text-center">
                    <div class="card-body mt-4">
                        <i class="bi bi-graph-up display-4 text-primary"></i>
                        <h6 class="mt-3 text-muted">Total Services</h6>
                        <h2 class="fw-bold">{{ number_format($data['total_services'], 0, ',', '.') }}</h2>
                    </div>
                </div>
            </div>
        </div>

        {{-- Ringkasan Laporan --}}
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom-0">
                        <h5 class="card-title mb-0"><i class="bi bi-bar-chart text-success"></i> Laporan Ringkas</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Total Orders</th>
                                    <th>Selesai</th>
                                    <th>Pending</th>
                                    <th>Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data['daily_summary'] as $summary)
                                    <tr>
                                        <td>{{ $summary['date'] }}</td>
                                        <td>{{ number_format($summary['total_orders'], 0, ',', '.') }}</td>
                                        <td>{{ number_format($summary['completed_orders'], 0, ',', '.') }}</td>
                                        <td>{{ number_format($summary['pending_orders'], 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($summary['revenue'], 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    @endif
@endsection

@push('styles')
    <style>
        .hover-card {
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .stat-card {
            border: none;
            border-radius: 12px;
            background: #fff;
        }

        .badge {
            font-size: 0.8rem;
            padding: 0.45em 0.9em;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('js/animateCounter.js') }}"></script>
@endpush
