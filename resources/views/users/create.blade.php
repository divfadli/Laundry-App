@extends('layouts.app')

@section('title', 'Tambah User')

@section('content')
    <div class="container mt-4">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center rounded-top-4">
                <h5 class="mb-0"><i class="bi bi-person-plus-fill"></i> Tambah User Baru</h5>
                <a href="{{ route('users.index') }}" class="btn btn-light btn-sm">
                    <i class="bi bi-arrow-left-circle"></i> Kembali
                </a>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf

                    {{-- Pilih Level --}}
                    <div class="mb-3">
                        <label for="id_level" class="form-label fw-semibold">Level <span
                                class="text-danger">*</span></label>
                        <select name="id_level" id="id_level" class="form-select @error('id_level') is-invalid @enderror"
                            required>
                            <option value="">-- Pilih Level --</option>
                            @foreach ($levels as $level)
                                <option value="{{ $level->id }}" {{ old('id_level') == $level->id ? 'selected' : '' }}>
                                    {{ $level->level_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_level')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Nama --}}
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Nama <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name"
                            class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                            placeholder="Masukkan nama lengkap" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Email <span
                                class="text-danger">*</span></label>
                        <input type="email" name="email" id="email"
                            class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}"
                            placeholder="contoh@email.com" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Password & Konfirmasi --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label fw-semibold">Password <span
                                    class="text-danger">*</span></label>
                            <input type="password" name="password" id="password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="Minimal 6 karakter" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label fw-semibold">Konfirmasi Password <span
                                    class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="form-control" placeholder="Ulangi password" required>
                        </div>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="d-flex justify-content-end mt-4">
                        <button type="reset" class="btn btn-outline-secondary me-2">
                            <i class="bi bi-arrow-clockwise"></i> Reset
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-save-fill"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
