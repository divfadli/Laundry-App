@extends('layouts.blankLayout')

@section('title', 'Login')

@section('content')
    <div class="login-page">
        <main>
            <div class="container">
                <section
                    class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
                    <div class="row justify-content-center">
                        <div class="col-lg-8 col-md-10">
                            <div class="card login-card shadow-lg">

                                <!-- LEFT SIDE (Logo + Text) -->
                                <div class="login-header p-4 d-flex flex-column justify-content-center align-items-center">
                                    <img src="{{ asset('assets/img/machine_wash.png') }}" alt="machine wash"
                                        class="img-fluid mb-3 logo-icon">

                                    <h4 class="fs-4 fw-bold mb-2 text-white">Sistem Informasi Laundry</h4>
                                    <p class="small opacity-75 mb-0 text-white">Silakan login untuk melanjutkan</p>
                                </div>

                                <!-- RIGHT SIDE (Form) -->
                                <div class="card-body p-4">
                                    @if (session('error'))
                                        <div class="alert alert-danger">{{ session('error') }}</div>
                                    @endif

                                    <form method="POST" action="{{ route('login') }}" class="row g-3 needs-validation"
                                        novalidate>
                                        @csrf

                                        <!-- Email -->
                                        <div class="col-12">
                                            <label for="email" class="form-label">Email</label>
                                            <div class="input-group has-validation">
                                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                                <input type="email" name="email" id="email"
                                                    value="{{ old('email') }}"
                                                    class="form-control @error('email') is-invalid @enderror"
                                                    placeholder="Masukkan email Anda" required>
                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @else
                                                    <div class="invalid-feedback">Please enter your email.</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Password -->
                                        <div class="col-12">
                                            <label for="password" class="form-label">Password</label>
                                            <div class="input-group has-validation">
                                                <span class="input-group-text"><i class="bi bi-key"></i></span>
                                                <input type="password" name="password" id="password"
                                                    class="form-control @error('password') is-invalid @enderror"
                                                    placeholder="Masukkan password Anda" required>
                                                @error('password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @else
                                                    <div class="invalid-feedback">Please enter your password.</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Remember Me -->
                                        <div class="col-12">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="remember"
                                                    id="rememberMe" {{ old('remember') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="rememberMe">Remember me</label>
                                            </div>
                                        </div>

                                        <!-- Submit Button -->
                                        <div class="col-12">
                                            <button class="btn btn-primary w-100" type="submit">
                                                <i class="bi bi-box-arrow-in-right me-2"></i> Login
                                            </button>
                                        </div>
                                    </form>

                                    <hr>

                                    <!-- Demo Accounts -->
                                    <div class="text-center small text-muted demo-accounts">
                                        <p class="fw-semibold mb-2">Demo Accounts</p>
                                        <p><strong>Admin:</strong> admin@laundry.com / password</p>
                                        <p><strong>Operator:</strong> operator@laundry.com / password</p>
                                        <p><strong>Pimpinan:</strong> pimpinan@laundry.com / password</p>
                                    </div>
                                </div>
                                <!-- END BODY -->
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <!-- Back To Top -->
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
        <i class="bi bi-arrow-up-short"></i>
    </a>
@endsection

@push('scripts')
    @if (session('success_message'))
        <script>
            Swal.fire({
                title: 'Berhasil!',
                text: '{{ session('success_message') }}',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.href = '{{ session('redirect_to') }}';
            });
        </script>
    @endif
@endpush
