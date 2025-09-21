@extends('layouts.app')

@section('title', 'Tambah Customer')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Tambah Customer Baru</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('customers.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="customer_name" class="form-label fw-semibold">
                                <i class="bi bi-person-circle text-primary me-1"></i> Nama
                            </label>
                            <input type="text" name="customer_name" id="customer_name" class="form-control rounded-3"
                                placeholder="Masukkan nama customer" required>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label fw-semibold">
                                <i class="bi bi-telephone text-success me-1"></i> No. Telepon
                            </label>
                            <input type="number" name="phone" id="phone" class="form-control rounded-3"
                                placeholder="08xxxxxxxxxx" required>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label fw-semibold">
                                <i class="bi bi-geo-alt text-danger me-1"></i> Alamat
                            </label>
                            <textarea name="address" id="address" class="form-control rounded-3" rows="3"
                                placeholder="Masukkan alamat lengkap" required></textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ url()->previous() }}" class="btn btn-secondary">Kembali</a>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
