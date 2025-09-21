@extends('layouts.app')

@section('title', 'Buat Transaksi Laundry')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Buat Transaksi Laundry Baru</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('orders.store') }}" method="POST" id="orderForm">
                @csrf

                {{-- Header Data --}}
                <div class="row mb-4 mt-3">
                    <div class="col-md-3">
                        <label for="order_code" class="form-label">Kode Transaksi</label>
                        <input type="text" class="form-control" id="order_code" name="order_code"
                            value="{{ old('order_code', $order_code ?? '') }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="id_customer" class="form-label">Customer</label>
                        <select class="form-select @error('id_customer') is-invalid @enderror" id="id_customer"
                            name="id_customer">
                            <option value="">Pilih Customer</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}"
                                    {{ old('id_customer') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->customer_name }} - {{ $customer->phone }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_customer')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <input type="hidden" name="order_date" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                    <div class="col-md-3">
                        <label for="order_end_date" class="form-label">Tanggal Selesai</label>
                        <input type="date" class="form-control @error('order_end_date') is-invalid @enderror"
                            id="order_end_date" name="order_end_date" value="{{ old('order_end_date') }}" required>
                        @error('order_end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <hr>
                <h6>Detail Service</h6>

                <div id="serviceContainer">
                    @php
                        $oldServices = old('services', [['id_service' => '', 'qty' => 1, 'notes' => '']]);
                    @endphp

                    @foreach ($oldServices as $i => $srv)
                        <div class="row service-item g-3 align-items-end mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Jenis Service</label>
                                <select class="form-select service-select" name="services[{{ $i }}][id_service]"
                                    required>
                                    <option value="">Pilih Service</option>
                                    @foreach ($services as $service)
                                        <option value="{{ $service->id }}" data-price="{{ $service->price }}"
                                            {{ $srv['id_service'] == $service->id ? 'selected' : '' }}>
                                            {{ $service->service_name }} - Rp
                                            {{ number_format($service->price, 0, ',', '.') }}/kg
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-1">
                                <label class="form-label">Qty (kg)</label>
                                <input type="number" class="form-control qty-input"
                                    name="services[{{ $i }}][qty]" step="0.1" min="0.1"
                                    value="{{ $srv['qty'] ?? 1 }}" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Subtotal</label>
                                <input type="text" class="form-control subtotal-display" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Catatan</label>
                                <input type="text" class="form-control" name="services[{{ $i }}][notes]"
                                    value="{{ $srv['notes'] ?? '' }}" placeholder="Opsional">
                            </div>
                            <div class="col-md-1 d-grid">
                                <button type="button" class="btn btn-outline-danger btn-sm remove-service"
                                    style="{{ $loop->first && count($oldServices) == 1 ? 'display:none;' : '' }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mb-3">
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="addService">
                        <i class="bi bi-plus-lg"></i> Tambah Service
                    </button>
                </div>

                <div class="d-flex justify-content-end mb-4">
                    <div class="w-25">
                        <hr>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <h5 class="mb-0">Total:</h5>
                            <h4 class="mb-0 text-primary" id="grandTotal">Rp 0</h4>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('orders.index') }}" class="btn btn-secondary">Kembali</a>
                    <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/orders.js') }}"></script>
@endpush
