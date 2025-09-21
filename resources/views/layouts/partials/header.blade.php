<nav class="navbar navbar-expand-lg px-3">
    <div class="container-fluid">
        <!-- Sidebar Toggle -->
        <button class="btn btn-outline-primary me-3" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>

        <!-- Page Title -->
        <h5 class="mb-0 fw-bold page-title">@yield('title')</h5>

        <!-- User Dropdown -->
        <ul class="navbar-nav ms-auto align-items-center">
            <li class="nav-item dropdown user-info">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown"
                    role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="{{ asset('assets/img/user.png') }}" alt="User" class="user-avatar me-2">
                    <span class="d-none d-md-inline fw-semibold">
                        {{ auth()->user()->name ?? 'Super Admin' }}
                    </span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="userDropdown">
                    <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i> Profile</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i> Settings</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="dropdown-item text-danger" type="submit">
                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
