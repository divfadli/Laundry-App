<div class="card shadow-sm border-0 mt-4">
    <div class="card-header bg-white border-bottom-0 d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i class="bi bi-bag-check text-primary"></i> Order Terbaru
        </h5>
        <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua <i class="bi bi-arrow-right"></i></a>
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
                            <td><span class="fw-semibold">{{ $order->order_code }}</span></td>
                            <td>{{ $order->customer->customer_name ?? '-' }}</td>
                            <td>{{ $order->order_date->format('d/m/Y') }}</td>
                            <td>Rp {{ number_format($order->total,0,',','.') }}</td>
                            <td><span class="badge rounded-pill {{ $order->status_class }}">{{ $order->status_text }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
