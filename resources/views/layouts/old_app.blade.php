<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - @yield('title')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            transition: all 0.3s;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
        }

        .main-content {
            background-color: #f8f9fa;
            min-height: 100vh;
        }

        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border-radius: 0.5rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6c5ce7 100%);
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar p-3">
                <div class="text-center mb-4">
                    <h4 class="text-white"><i class="bi bi-water"></i> Laundry System</h4>
                    <small class="text-white-50">{{ auth()->user()->level->level_name }}</small>
                </div>

                <ul class="nav flex-column">
                    <li class="nav-item mb-1">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                            href="{{ route('dashboard') }}">
                            <i class="bi bi-speedometer2 me-2"></i>Dashboard
                        </a>
                    </li>

                    @if (auth()->user()->isAdmin())
                        <li class="nav-item mb-1">
                            <a class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}"
                                href="{{ route('customers.index') }}">
                                <i class="bi bi-people me-2"></i>Customers
                            </a>
                        </li>
                        <li class="nav-item mb-1">
                            <a class="nav-link {{ request()->routeIs('services.*') ? 'active' : '' }}"
                                href="{{ route('services.index') }}">
                                <i class="bi bi-gear me-2"></i>Jenis Service
                            </a>
                        </li>
                        <li class="nav-item mb-1">
                            <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}"
                                href="{{ route('users.index') }}">
                                <i class="bi bi-person-gear me-2"></i>Users
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->isAdmin() || auth()->user()->isOperator())
                        <li class="nav-item mb-1">
                            <a class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}"
                                href="{{ route('orders.index') }}">
                                <i class="bi bi-receipt me-2"></i>Transaksi Laundry
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->isPimpinan() || auth()->user()->isAdmin())
                        <li class="nav-item mb-1">
                            <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}"
                                href="{{ route('reports.index') }}">
                                <i class="bi bi-bar-chart me-2"></i>Laporan
                            </a>
                        </li>
                    @endif

                    <hr class="text-white-50">
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="nav-link btn btn-link text-start w-100 text-white-50">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <div
                    class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">@yield('title')</h1>
                    <div class="text-muted">
                        <i class="bi bi-person-circle me-1"></i>{{ auth()->user()->name }}
                    </div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>

</html>
