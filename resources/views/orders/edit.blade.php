@extends('layouts.app')

@section('title', 'Edit Transaksi Laundry')

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Edit Transaksi #{{ $order->order_code }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('orders.update', $order) }}" method="POST" id="orderForm">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="id_customer" class="form-label">Customer</label>
                            <select class="form-select @error('id_customer') is-invalid @enderror" id="id_customer"
                                name="id_customer">
                                <option value="">Pilih Customer</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}"
                                        {{ old('id_customer', $order->id_customer ?? '') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->customer_name }} - {{ $customer->phone }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_customer')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="order_date" class="form-label">Tanggal Order</label>
                            <input type="date" class="form-control @error('order_date') is-invalid @enderror"
                                id="order_date" name="order_date"
                                value="{{ old('order_date', $order->order_date ?? date('Y-m-d')) }}">
                            @error('order_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="order_end_date" class="form-label">Tanggal Selesai</label>
                            <input type="date" class="form-control" id="order_end_date" name="order_end_date"
                                value="{{ old('order_end_date', $order->order_end_date ?? '') }}">
                        </div>
                    </div>
                </div>

                <hr>

                <h6>Detail Service</h6>
                <div id="serviceContainer">
                    @php $index = 0; @endphp
                    @foreach (old('services', $order->transOrderDetails ?? [[]]) as $detail)
                        <div class="row service-item mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Jenis Service</label>
                                <select class="form-select service-select"
                                    name="services[{{ $index }}][id_service]">
                                    <option value="">Pilih Service</option>
                                    @foreach ($services as $service)
                                        <option value="{{ $service->id }}" data-price="{{ $service->price }}"
                                            {{ old("services.$index.id_service", $detail->id_service ?? '') == $service->id ? 'selected' : '' }}>
                                            {{ $service->service_name }} - Rp
                                            {{ number_format($service->price, 0, ',', '.') }}/kg
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Qty (kg)</label>
                                <input type="number" class="form-control qty-input"
                                    name="services[{{ $index }}][qty]" step="0.1" min="0.1"
                                    value="{{ old("services.$index.qty", $detail->qty ?? 1) }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Subtotal</label>
                                <input type="text" class="form-control subtotal-display" readonly
                                    value="Rp {{ number_format($detail->subtotal ?? 0, 0, ',', '.') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Catatan</label>
                                <input type="text" class="form-control" name="services[{{ $index }}][notes]"
                                    value="{{ old("services.$index.notes", $detail->notes ?? '') }}"
                                    placeholder="Catatan (opsional)">
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="button" class="btn btn-danger btn-sm remove-service"
                                    style="{{ $index == 0 ? 'display:none;' : '' }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        @php $index++; @endphp
                    @endforeach
                </div>

                <button type="button" class="btn btn-secondary btn-sm" id="addService">
                    <i class="bi bi-plus-lg"></i> Tambah Service
                </button>

                <hr>

                <div class="row">
                    <div class="col-md-8"></div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h6>Total: <span id="grandTotal">
                                        Rp {{ number_format($order->total ?? 0, 0, ',', '.') }}
                                    </span></h6>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="d-flex justify-content-between mt-3">
                    <a href="{{ route('orders.index') }}" class="btn btn-secondary">Kembali</a>
                    <button type="submit" class="btn btn-primary">Update Transaksi</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/orders.js') }}"></script>
@endsection
