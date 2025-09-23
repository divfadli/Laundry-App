@extends('layouts.app')

@section('title', 'Detail Transaksi Laundry')

@section('content')
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden animate__animated animate__fadeIn">
        {{-- Header --}}
        <div class="card-header bg-gradient bg-primary text-white d-flex justify-content-between align-items-center py-3">
            <h4 class="mb-0 fw-bold">
                <i class="bi bi-receipt-cutoff me-2"></i>
                Detail Transaksi <span class="fw-light">#{{ $order->order_code }}</span>
            </h4>
            <span class="badge rounded-pill {{ $order->status_class }} px-3 py-2 fs-6 d-flex align-items-center shadow-sm">
                @switch($order->order_status)
                    @case(0)
                        <i class="bi bi-hourglass-split me-1"></i>
                    @break

                    @case(1)
                        <i class="bi bi-check-circle-fill me-1"></i>
                    @break

                    @case(2)
                        <i class="bi bi-gear-fill me-1"></i>
                    @break

                    @default
                        <i class="bi bi-x-circle-fill me-1"></i>
                @endswitch
                {{ $order->status_text }}
            </span>
        </div>

        <div class="card-body p-4 fs-5 bg-light">
            {{-- Informasi Customer & Tanggal --}}
            <div class="row g-4 mb-4">
                {{-- Customer --}}
                <div class="col-md-8">
                    <div class="p-4 bg-white rounded-3 shadow-sm h-100 border-start border-4 border-primary">
                        <h5 class="fw-bold mb-3 text-primary">
                            <i class="bi bi-person-circle me-2"></i> Customer
                        </h5>
                        <p class="mb-1 fw-semibold fs-5">{{ $order->customer->customer_name }}</p>
                        <p class="text-muted mb-0 fs-6">
                            <i class="bi bi-telephone me-1"></i> {{ $order->customer->phone ?? '-' }}
                        </p>
                    </div>
                </div>

                {{-- Tanggal --}}
                <div class="col-md-4">
                    <div class="p-4 bg-white rounded-3 shadow-sm h-100 border-start border-4 border-success">
                        <h5 class="fw-bold mb-3 text-success">
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
            <h5 class="text-uppercase text-muted fw-bold mb-3 border-bottom pb-2">
                <i class="bi bi-list-ul me-2 text-primary"></i> Detail Layanan
            </h5>
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle shadow-sm fs-6 rounded-3 overflow-hidden">
                    <thead class="table-primary text-center">
                        <tr>
                            <th>Layanan</th>
                            <th width="8%">Qty</th>
                            <th width="15%" class="text-end">Harga</th>
                            <th width="15%" class="text-end">Subtotal</th>
                            <th width="25%">Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($order->transOrderDetails as $detail)
                            <tr>
                                <td class="fw-semibold text-center">{{ $detail->typeOfService->service_name }}</td>
                                <td class="text-center">{{ $detail->qty }}</td>
                                <td class="text-end">Rp. {{ number_format($detail->typeOfService->price, 0, ',', '.') }}
                                </td>
                                <td class="text-end fw-bold text-success">Rp.
                                    {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                <td class="text-center">{{ $detail->notes ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="bi bi-info-circle me-2"></i> Tidak ada layanan
                                </td>
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
                    <div class="p-4 w-100 w-md-50 ms-auto border rounded-3 shadow-sm bg-white">
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

                            <div class="mb-3">
                                <label class="fw-bold mb-2">Kembalian</label>
                                <input type="text" id="order_change_display"
                                    class="form-control form-control-lg text-end bg-light" readonly>
                                <input type="hidden" id="order_change" name="order_change">
                            </div>


                            {{-- Notes --}}
                            <div class="mb-3">
                                <label for="notes" class="fw-bold mb-2">Catatan</label>
                                <textarea name="notes" id="notes" rows="3" class="form-control" placeholder="Tambahkan catatan jika ada..."></textarea>
                            </div>
                        @else
                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-bold">Dibayar</span>
                                <span class="text-success fw-semibold">
                                    Rp. {{ number_format($order->order_pay, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-bold">Kembalian</span>
                                <span class="text-muted fw-semibold">
                                    Rp. {{ number_format($order->order_change, 0, ',', '.') }}
                                </span>
                            </div>

                            @if ($order->transLaundryPickups && $order->transLaundryPickups->notes)
                                <div class="mt-3">
                                    <label class="fw-bold mb-2">Catatan</label>
                                    <div class="p-3 bg-light rounded-3 border">
                                        {{ $order->transLaundryPickups->notes }}
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('orders.index') }}"
                            class="btn btn-outline-secondary rounded-pill px-4 py-2 shadow-sm">
                            <i class="bi bi-arrow-left-circle me-1"></i> Kembali
                        </a>
                        @if ($order->order_status == 0 && (auth()->user()->isAdmin() || auth()->user()->isOperator()))
                            <button type="submit" class="btn btn-success rounded-pill px-4 py-2 shadow-sm">
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
            const changeDisplay = document.getElementById("order_change_display");

            if (payInput && changeInput && changeDisplay) {
                payInput.addEventListener("input", () => {
                    const pay = parseFloat(payInput.value) || 0;
                    const change = pay - total;

                    if (change >= 0) {
                        changeDisplay.value = "Rp. " + new Intl.NumberFormat('id-ID').format(change);
                        changeInput.value = change; // numeric ke backend
                    } else {
                        changeDisplay.value = "-";
                        changeInput.value = 0;
                    }
                });
            }
        });
    </script>
@endpush
