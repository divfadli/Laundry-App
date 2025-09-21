@extends('layouts.app')

@section('title', 'Detail Transaksi Laundry')

@section('content')
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
        {{-- Header --}}
        <div class="card-header bg-gradient bg-primary text-white d-flex justify-content-between align-items-center py-3">
            <h4 class="mb-0 fw-bold">
                <i class="bi bi-receipt-cutoff me-2"></i>
                Detail Transaksi <span class="fw-light">#{{ $order->order_code }}</span>
            </h4>
            <span class="badge rounded-pill {{ $order->status_class }} px-3 py-2 fs-6 d-flex align-items-center">
                @switch($order->order_status)
                    @case(0)
                        <i class="bi bi-hourglass-split me-1"></i>
                    @break

                    @case(1)
                        <i class="bi bi-gear-fill me-1"></i>
                    @break

                    @case(2)
                        <i class="bi bi-check-circle-fill me-1"></i>
                    @break

                    @default
                        <i class="bi bi-x-circle-fill me-1"></i>
                @endswitch
                {{ $order->status_text }}
            </span>
        </div>

        <div class="card-body p-4 fs-5">
            {{-- Informasi Customer & Tanggal --}}
            <div class="row g-4 mb-4">
                {{-- Customer --}}
                <div class="col-md-10">
                    <div class="p-3 h-100">
                        <h5 class="fw-bold mb-3 text-primary">
                            <i class="bi bi-person-circle me-2"></i> Customer
                        </h5>
                        <p class="mb-2 fw-semibold">{{ $order->customer->customer_name }}</p>
                        <p class="text-muted mb-0 fs-6">
                            <i class="bi bi-telephone me-1"></i> {{ $order->customer->phone ?? '-' }}
                        </p>
                    </div>
                </div>

                {{-- Tanggal --}}
                <div class="col-md-2">
                    <div class="p-3 h-100">
                        <h5 class="fw-bold mb-3 text-primary">
                            <i class="bi bi-calendar-event me-2"></i> Tanggal
                        </h5>
                        <ul class="list-unstyled mb-0 fs-6">
                            <li class="mb-2">
                                <i class="bi bi-calendar-check me-1 text-primary"></i>
                                Order: <strong>{{ $order->order_date->format('d/m/Y') }}</strong>
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-calendar-check-fill me-1 text-success"></i>
                                Selesai: <strong>{{ optional($order->order_end_date)->format('d/m/Y') ?? '-' }}</strong>
                            </li>
                            <li>
                                <i class="bi bi-truck me-1 text-warning"></i>
                                Ambil:
                                <strong>
                                    {{ optional($order->transLaundryPickups)->pickup_date
                                        ? \Carbon\Carbon::parse($order->transLaundryPickups->pickup_date)->format('d/m/Y')
                                        : '-' }}
                                </strong>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Detail Layanan --}}
            <h5 class="text-uppercase text-muted fw-bold mb-3">
                <i class="bi bi-list-ul me-2"></i> Detail Layanan
            </h5>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle shadow-sm fs-6 rounded-3 overflow-hidden">
                    <thead class="table-primary text-center">
                        <tr>
                            <th>Layanan</th>
                            <th width="8%">Qty</th>
                            <th width="15%">Harga</th>
                            <th width="15%">Subtotal</th>
                            <th width="25%">Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($order->transOrderDetails as $detail)
                            <tr>
                                <td class="fw-semibold">{{ $detail->typeOfService->service_name }}</td>
                                <td class="text-center">{{ $detail->qty }}</td>
                                <td class="text-end">Rp. {{ number_format($detail->typeOfService->price, 0, ',', '.') }}
                                </td>
                                <td class="text-end fw-bold text-success">Rp.
                                    {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                <td>{{ $detail->notes ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Tidak ada layanan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Ringkasan Pembayaran --}}
            <div class="mt-4">
                <form id="complete-form-{{ $order->id }}" action="{{ route('orders.complete', $order) }}"
                    method="POST">
                    @csrf
                    @method('PATCH')

                    {{-- Box Ringkasan --}}
                    <div class="p-3 w-25 w-md-25 w-lg-25 ms-auto border rounded shadow-sm bg-light">
                        {{-- Total --}}
                        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                            <span class="fw-bold fs-5">Total</span>
                            <span class="fw-bold text-primary fs-4">
                                Rp. {{ number_format($order->total, 0, ',', '.') }}
                            </span>
                        </div>

                        {{-- Jika Belum Lunas --}}
                        @if ($order->order_status == 0)
                            <div class="mb-3">
                                <label for="order_pay" class="fw-bold mb-2">Uang Bayar</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="order_pay" id="order_pay"
                                        class="form-control form-control-lg text-end" min="{{ $order->total }}"
                                        placeholder="Masukkan nominal..." required>
                                </div>
                            </div>

                            <div>
                                <label class="fw-bold mb-2">Kembalian</label>
                                <input type="text" id="order_change" class="form-control form-control-lg text-end"
                                    readonly>
                            </div>
                        @else
                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-bold">Dibayar</span>
                                <span class="text-success">
                                    Rp. {{ number_format($order->order_pay, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="fw-bold">Kembalian</span>
                                <span class="text-muted">
                                    Rp. {{ number_format($order->order_change, 0, ',', '.') }}
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary rounded-pill px-4 py-2">
                            <i class="bi bi-arrow-left-circle me-1"></i> Kembali
                        </a>
                        @if ($order->order_status == 0 && (auth()->user()->isAdmin() || auth()->user()->isOperator()))
                            <button type="submit" class="btn btn-success rounded-pill px-4 py-2">
                                <i class="bi bi-check-circle-fill me-1"></i> Selesaikan
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const total = {{ $order->total }};
            const payInput = document.getElementById("order_pay");
            const changeInput = document.getElementById("order_change");

            if (payInput && changeInput) {
                payInput.addEventListener("input", () => {
                    const pay = parseFloat(payInput.value) || 0;
                    const change = pay - total;
                    changeInput.value = change >= 0 ?
                        "Rp. " + new Intl.NumberFormat('id-ID').format(change) :
                        "-";
                });
            }
        });
    </script>
@endpush
