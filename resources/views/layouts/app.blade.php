<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="{{ asset('assets/img/logo-laundry.png') }}" type="image/png" width="100px">
    <title>@yield('title') | Laundry System</title>

    <!-- CSS -->
    @include('layouts.sections.styles')

    @stack('styles')
</head>

<body>
    <div id="wrapper" class="d-flex">
        <!-- Sidebar -->
        @include('layouts.partials.sidebar')

        <!-- Page Content -->
        <div id="page-content-wrapper" class="d-flex flex-column min-vh-100">
            <!-- Header -->
            @include('layouts.partials.header')

            <!-- Main Content -->
            <div class="row">
                <main class="col-md-12">
                    <div class="content-wrapper">
                        @yield('content')
                    </div>
                </main>
            </div>


            <!-- Footer -->
            @include('layouts.partials.footer')

        </div>

    </div>
    @yield('modals')

    <!-- Bootstrap JS -->
    @include('layouts.sections.scripts')


    <!-- Sidebar Toggle Script -->
    <script>
        const wrapper = document.getElementById('wrapper');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarCloseBtn = document.querySelector('.sidebar-close-btn');
        const sidebar = document.querySelector('.sidebar');

        // Function to open sidebar on mobile
        const openSidebar = () => {
            wrapper.classList.add('sidebar-show');
            document.body.classList.add('sidebar-open');
        };

        // Function to close sidebar on mobile
        const closeSidebar = () => {
            wrapper.classList.remove('sidebar-show');
            document.body.classList.remove('sidebar-open');
        };

        // Toggle sidebar
        sidebarToggle?.addEventListener('click', (e) => {
            e.stopPropagation();
            if (window.innerWidth <= 991) {
                openSidebar();
            } else {
                wrapper.classList.toggle('sidebar-collapsed');
            }
        });

        // Close button inside sidebar
        sidebarCloseBtn?.addEventListener('click', (e) => {
            e.stopPropagation();
            closeSidebar();
        });

        // Close sidebar when click outside
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 991) {
                if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                    closeSidebar();
                }
            }
        });

        // Optional: Close sidebar on window resize to reset states
        window.addEventListener('resize', () => {
            if (window.innerWidth > 991) {
                closeSidebar(); // remove mobile classes
            }
        });
    </script>
    @stack('scripts')
</body>

</html>
