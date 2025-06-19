<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - Findit@TLU</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }

        .sidebar {
            background-color: #1c3d72;
            /* TLU Deep Blue */
            color: #fff;
            min-height: 100vh;
            padding-top: 15px;
        }

        .sidebar .logo {
            font-size: 1.5rem;
            font-weight: bold;
            padding: 4px 0;
            color: #fff;
            display: block;
            text-decoration: none;
        }

        .sidebar .logo img {
            max-width: 40px;
            margin-right: 10px;
            vertical-align: middle;
        }

        .sidebar .admin-panel-text {
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            margin-bottom: 20px;
            color: rgba(255, 255, 255, 0.7);
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            font-size: 0.95rem;
            border-left: 3px solid transparent;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
            border-left-color: #f0ad4e;
            /* TLU Orange for active/hover */
        }

        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            /* Ensure icons align */
            text-align: center;
        }

        .top-navbar {
            background-color: #fff;
            border-bottom: 1px solid #dee2e6;
            padding: 0.75rem 1rem;
        }

        .navbar-brand-mobile {
            color: #1c3d72;
            font-weight: bold;
        }

        .content-wrapper {
            padding: 20px;
        }

        .main-content {
            background-color: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .breadcrumb {
            margin-bottom: 1.5rem;
        }

        .page-title {
            color: #1c3d72;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }

        .footer {
            text-align: center;
            padding: 15px 0;
            font-size: 0.9rem;
            color: #6c757d;
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
        }

        @media (max-width: 768px) {
            .sidebar {
                /* Temporary hide sidebar on mobile, or implement offcanvas */
                /* display: none; */
                position: fixed;
                top: 0;
                bottom: 0;
                left: 0;
                z-index: 100;
                /* Behind normal content */
                padding: 0;
                box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
                transform: translateX(-100%);
                transition: transform .3s ease-in-out;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .content-wrapper {
                width: 100%;
            }

            .sidebar-toggler {
                display: block !important;
            }
        }

        .sidebar-toggler {
            display: none;
            /* Hidden by default on larger screens */
            border: none;
            background: transparent;
            color: #1c3d72;
            font-size: 1.5rem;
        }

        .sidebar-divider {
            border: none;
            border-top: 2px solid rgba(255, 255, 255, 0.2);
            /* Màu trắng mờ */
            margin: 15px 0;
            /* Khoảng cách trên và dưới */
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block sidebar">
                <div class="position-sticky">
                    <a href="{{ route('admin.dashboard') }}" class="logo">
                        Findit@TLU
                    </a>
                    <div class="admin-panel-text">Admin Panel</div>
                    <hr class="sidebar-divider">  
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                <i class="bi bi-speedometer2"></i> Tổng quan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.items.*') ? 'active' : '' }}" href="{{ route('admin.items.index') }}">
                                <i class="bi bi-card-list"></i> Quản lý bài đăng
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                                <i class="bi bi-tags"></i> Quản lý danh mục
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                                <i class="bi bi-people"></i> Quản lý người dùng
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content area -->
            <main class="position-relative col-md-9 ms-sm-auto col-lg-10 px-md-4 content-wrapper">
                <!-- Top Navbar -->
                <nav class="navbar navbar-expand-lg top-navbar" style="background-color: #f8f9fa;     border-bottom: none !important; /* Bỏ viền dưới */">
                    <div class="container-fluid">
                        <button class="sidebar-toggler" type="button" id="sidebarToggler">
                            <i class="bi bi-list"></i>
                        </button>
                        <a class="navbar-brand-mobile d-md-none" href="#">Findit@TLU</a>

                        <div class=" collapse navbar-collapse" id="navbarNavDropdown">
                            <ul class="position-absolute top-0 end-0 navbar-nav ms-auto">
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-person-circle"></i> {{ Auth::user()->full_name ?? 'Admin' }}
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownMenuLink">
                                        <li><a class="dropdown-item" href="#">Thông tin tài khoản</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                Đăng xuất
                                            </a>
                                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                                @csrf
                                            </form>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>

                <!-- Page Content -->
                <div class="pt-3">
                    @yield('content')
                </div>

                <!-- Footer -->
                <footer class="footer mt-auto py-3">
                    <div class="container-fluid">
                        <span class="text-muted">&copy; {{ date('Y') }} Findit@TLU, All rights reserved.</span>
                    </div>
                </footer>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar toggler for mobile
        const sidebarToggler = document.getElementById('sidebarToggler');
        const sidebar = document.getElementById('sidebar');
        if (sidebarToggler && sidebar) {
            sidebarToggler.addEventListener('click', () => {
                sidebar.classList.toggle('active');
            });
        }
    </script>
    @stack('scripts')
</body>

</html>