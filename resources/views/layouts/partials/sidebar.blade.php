<nav class="sidebar">
    {{-- Close Button (Mobile) --}}
    <div class="sidebar-close-btn d-lg-none">
        <i class="bi bi-x-lg"></i>
    </div>

    {{-- Sidebar Header --}}
    <div class="sidebar-header text-center mb-4">
        <h4 class="brand-title text-white mb-0">
            <i class="bi bi-water"></i>
            <span class="sidebar-text">Laundry System</span>
        </h4>
        <small class="role-label text-white-50 d-block mt-1">
            {{ auth()->user()->level->level_name ?? '' }}
        </small>
    </div>

    {{-- Sidebar Menu --}}
    <ul class="nav flex-column sidebar-menu">

        {{-- Dashboard --}}
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <i class="bi bi-speedometer2"></i>
                <span class="sidebar-text">Dashboard</span>
            </a>
        </li>

        {{-- Master Data --}}
        @if (auth()->user()->isAdmin())
            <li class="nav-item sidebar-dropdown">
                <a href="#" class="nav-link dropdown-toggle-menu">
                    <i class="bi bi-folder"></i>
                    <span class="sidebar-text">Master Data</span>
                    <i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul class="sidebar-dropdown-menu">
                    <li class="nav-item">
                        <a class="nav-link dropdown-item {{ request()->routeIs('customers.*') ? 'active' : '' }}"
                            href="{{ route('customers.index') }}">
                            <i class="bi bi-people"></i> Customer
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link dropdown-item {{ request()->routeIs('users.*') ? 'active' : '' }}"
                            href="{{ route('users.index') }}">
                            <i class="bi bi-person-gear"></i> User
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link dropdown-item {{ request()->routeIs('services.*') ? 'active' : '' }}"
                            href="{{ route('services.index') }}">
                            <i class="bi bi-gear"></i> Jenis Service
                        </a>
                    </li>
                </ul>
            </li>
        @elseif(auth()->user()->isOperator())
            {{-- Operator hanya tambah customer --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('customers.create') ? 'active' : '' }}"
                    href="{{ route('customers.create') }}">
                    <i class="bi bi-people"></i> Tambah Customer
                </a>
            </li>
        @endif

        {{-- Transaksi Laundry --}}
        @if (auth()->user()->isOperator() || auth()->user()->isAdmin())
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}"
                    href="{{ route('orders.index') }}">
                    <i class="bi bi-receipt"></i> Transaksi Laundry
                </a>
            </li>
        @endif

        {{-- Laporan --}}
        @if (auth()->user()->isPimpinan() || auth()->user()->isAdmin())
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}"
                    href="{{ route('reports.index') }}">
                    <i class="bi bi-bar-chart"></i> Laporan
                </a>
            </li>
        @endif

        <hr class="divider">

        {{-- Logout --}}
        <li class="nav-item">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-link btn btn-link text-start w-100 logout-btn">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>
        </li>
    </ul>
</nav>

{{-- Dropdown Script --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const dropdowns = document.querySelectorAll('.sidebar-dropdown');

        dropdowns.forEach(drop => {
            const toggle = drop.querySelector('.dropdown-toggle-menu');
            const menu = drop.querySelector('.sidebar-dropdown-menu');
            const icon = toggle.querySelector('i.ms-auto');

            // Buka otomatis jika ada item aktif
            if (menu.querySelector('.dropdown-item.active')) {
                drop.classList.add('open');
                menu.style.maxHeight = menu.scrollHeight + 'px';
                icon.className = 'bi bi-chevron-up ms-auto';
            }

            // Toggle dropdown
            toggle.addEventListener('click', e => {
                e.preventDefault();
                const isOpen = drop.classList.contains('open');

                dropdowns.forEach(d => {
                    if (d !== drop) {
                        d.classList.remove('open');
                        d.querySelector('.sidebar-dropdown-menu').style.maxHeight =
                            null;
                        const otherIcon = d.querySelector(
                            '.dropdown-toggle-menu i.ms-auto');
                        if (otherIcon) otherIcon.className =
                            'bi bi-chevron-down ms-auto';
                    }
                });

                if (!isOpen) {
                    drop.classList.add('open');
                    menu.style.maxHeight = menu.scrollHeight + 'px';
                    icon.className = 'bi bi-chevron-up ms-auto';
                } else {
                    drop.classList.remove('open');
                    menu.style.maxHeight = null;
                    icon.className = 'bi bi-chevron-down ms-auto';
                }
            });
        });
    });
</script>
