@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-4">Tambah Jenis Service</h2>

        <form action="{{ route('services.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="service_name" class="form-label">Nama Service</label>
                <input type="text" name="service_name" id="service_name"
                    class="form-control @error('service_name') is-invalid @enderror" value="{{ old('service_name') }}"
                    required>
                @error('service_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">Harga</label>
                <input type="number" name="price" id="price"
                    class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}" required>
                @error('price')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Deskripsi</label>
                <textarea name="description" id="description" rows="3"
                    class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-success">Simpan</button>
            <a href="{{ route('services.index') }}" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
@endsection
