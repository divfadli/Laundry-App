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
                'label' => 'Orders Hari Ini',
                'color' => 'success',
                'value' => $data['orders_today'],
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
