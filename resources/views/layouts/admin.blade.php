<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - Findit@TLU</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body,
        html {
            height: 100vh;
            min-height: 100vh;
            background-color: #f8f9fa;
        }

        .container-fluid {
            height: 100vh;
            padding: 0;
        }

        .row {
            height: 100%;
            margin: 0;
        }

        .sidebar {
            background-color: #1c3d72;
            color: #fff;
            height: 100vh;
            padding-top: 15px;
            overflow-y: auto;
        }

        main.position-relative {
            display: flex;
            flex-direction: column;
            height: 100vh;
            min-height: 0;
            padding: 0 !important;
        }

        .top-navbar {
            background-color: #fff;
            border-bottom: 1px solid #dee2e6;
            padding: 0.75rem 1rem;
            height: 70px;
            flex-shrink: 0;
        }

        .content-area {
            flex: 1 1 0%;
            min-height: 0;
            overflow-y: auto;
            padding: 24px 18px 18px 18px;
        }

        .footer {
            text-align: center;
            padding: 10px 0;
            font-size: 0.8rem;
            color: #6c757d;
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
            width: 100%;
            flex-shrink: 0;
        }

        .dashboard-page body {
            height: 100vh;
            overflow: hidden;
        }

        .dashboard-page .container-fluid {
            height: 100vh;
        }

        .dashboard-page .row {
            height: 100%;
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

        .navbar-brand-mobile {
            color: #1c3d72;
            font-weight: bold;
        }

        .content-wrapper {
            padding: 15px;
        }

        .dashboard-page .content-wrapper {
            height: calc(100vh - 70px);
            overflow-y: auto;
        }

        .main-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            height: 100%;
        }

        .breadcrumb {
            margin-bottom: 1rem;
        }

        .page-title {
            color: #1c3d72;
            margin-bottom: 1rem;
            font-weight: 600;
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
            margin: 15px 0;
        }

        /* Custom Flash Message Styles */
        .flash-message-container {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 1050;
            width: auto;
            max-width: 400px;
        }

        .flash-message {
            display: flex;
            align-items: flex-start;
            padding: 16px;
            margin-bottom: 1rem;
            border-radius: 8px;
            color: #fff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            animation: slideInRight 0.3s ease-out;
        }

        .flash-message.success {
            background-color: #28a745;
            /* Green */
        }

        .flash-message.error {
            background-color: #dc3545;
            /* Red */
        }

        .flash-icon {
            font-size: 24px;
            margin-right: 16px;
            flex-shrink: 0;
        }

        .flash-content {
            flex-grow: 1;
        }

        .flash-title {
            font-weight: 700;
            display: block;
            margin-bottom: 4px;
        }

        .flash-text {
            margin: 0;
            font-size: 0.95rem;
            line-height: 1.4;
        }

        .flash-close {
            background: none;
            border: none;
            color: inherit;
            font-size: 20px;
            opacity: 0.8;
            padding: 0;
            margin-left: 24px;
            cursor: pointer;
        }

        .flash-close:hover {
            opacity: 1;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>
    @stack('styles')
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
                    <div class="admin-panel-text">
                        @if(request()->routeIs('admin.items.show'))
                        Chi tiết bài đăng
                        @else
                        Admin Panel
                        @endif
                    </div>
                    <hr class="sidebar-divider">
                    <ul class="nav flex-column">
                        @if(Auth::user()->role_id == 1)
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
                        @elseif(Auth::user()->role_id == 2)
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.items.*') ? 'active' : '' }}" href="{{ route('admin.items.index') }}">
                                <i class="bi bi-card-list"></i> Quản lý bài đăng
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </nav>

            <!-- Main content area -->
            <main class="position-relative col-md-9 ms-sm-auto col-lg-10 px-0 d-flex flex-column">
                <!-- Top Navbar -->
                <nav class="navbar navbar-expand-lg top-navbar d-flex justify-content-between align-items-center" style="background-color: #f8f9fa; border-bottom: none !important;">
                    <div class="d-flex align-items-center">
                        <button class="sidebar-toggler me-3" type="button" id="sidebarToggler">
                            <i class="bi bi-list"></i>
                        </button>
                        <h2 class="fw-bold mb-0" style="color:#22223b;">@yield('title', 'Admin Panel')</h2>
                    </div>

                    <div class="navbar-nav">
                        <div class="nav-item dropdown">
                            <a class="nav-link d-flex align-items-center gap-2 p-2 dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); min-width: 180px;">
                                <span style="display:inline-flex;align-items:center;justify-content:center;width:40px;height:40px;border-radius:50%;background:#1356a4;color:#fff;font-weight:600;font-size:1.3rem;">
                                    {{ strtoupper(mb_substr(Auth::user()->full_name ?? 'A', 0, 1)) }}
                                </span>
                                <span style="font-weight:600;color:#333;">{{ Auth::user()->full_name ?? 'Admin' }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownMenuLink">
                                <li>
                                    <a class="dropdown-item" href="#" onclick="handleLogout(event)">
                                        Đăng xuất
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>

                <!-- Flash Message Area -->
                <div class="flash-message-container">
                    @if (session('success'))
                    <div class="flash-message success" role="alert">
                        <div class="flash-icon">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                        <div class="flash-content">
                            <strong class="flash-title">{{ session('success_title', 'Thành công!') }}</strong>
                            <p class="flash-text">{{ session('success') }}</p>
                        </div>
                        <button type="button" class="flash-close" data-dismiss="alert" aria-label="Close">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    @endif

                    @if (session('error'))
                    <div class="flash-message error" role="alert">
                        <div class="flash-icon">
                            <i class="fa-solid fa-circle-xmark"></i>
                        </div>
                        <div class="flash-content">
                            <strong class="flash-title">{{ session('error_title', 'Lỗi!') }}</strong>
                            <p class="flash-text">{{ session('error') }}</p>
                        </div>
                        <button type="button" class="flash-close" data-dismiss="alert" aria-label="Close">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    @endif
                </div>

                <!-- Page Content -->
                <div class="content-area">
                    <div class="container-fluid">
                        @yield('content')
                    </div>
                </div>

                <!-- Footer -->
                <footer class="text-center py-2" style="background-color: #f8f9fa; font-size: 0.85rem; color: #6c757d;">
                    <span>&copy; {{ date('Y') }} Findit@TLU. All rights reserved.</span>
                </footer>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Function xử lý logout với ghi nhớ email
        function handleLogout(event) {
            event.preventDefault();
            
            // Lấy thông tin từ session (sẽ được truyền qua meta tag hoặc data attribute)
            const userEmail = '{{ session("user_email", "") }}';
            const rememberEmailChecked = '{{ session("remember_email_checked", false) }}';
            
            // Nếu checkbox "Ghi nhớ email" đã được check, lưu email vào localStorage
            if (rememberEmailChecked === '1' && userEmail) {
                localStorage.setItem('rememberedEmail', userEmail);
                localStorage.setItem('rememberEmailChecked', 'true');
            } else {
                // Nếu không check, xóa email đã lưu
                localStorage.removeItem('rememberedEmail');
                localStorage.removeItem('rememberEmailChecked');
            }
            
            // Submit form logout
            document.getElementById('logout-form').submit();
        }

        document.addEventListener('DOMContentLoaded', function() {
            const flashMessages = document.querySelectorAll('.flash-message');

            // --- NEW: Ensure only the latest message is shown ---
            if (flashMessages.length > 1) {
                for (let i = 0; i < flashMessages.length - 1; i++) {
                    flashMessages[i].style.display = 'none';
                }
            }

            // Find the single visible message
            const activeMessage = Array.from(flashMessages).find(el => el.style.display !== 'none');

            // Helper function for a smooth fade-out effect
            function fadeOut(element) {
                if (!element) return;
                element.style.transition = 'opacity 0.4s ease-out';
                element.style.opacity = '0';
                setTimeout(() => {
                    element.style.display = 'none';
                }, 400); // Wait for transition to finish
            }

            // Auto-hide the active message after 5 seconds
            if (activeMessage) {
                setTimeout(function() {
                    fadeOut(activeMessage);
                }, 5000); // 5 seconds
            }

            // Handle manual closing
            const closeButtons = document.querySelectorAll('.flash-close');
            closeButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    fadeOut(this.closest('.flash-message'));
                });
            });

            // Sidebar toggler for mobile
            const sidebarToggler = document.getElementById('sidebarToggler');
            const sidebar = document.getElementById('sidebar');
            if (sidebarToggler && sidebar) {
                sidebarToggler.addEventListener('click', function() {
                    sidebar.classList.toggle('active');
                });
            }
        });
    </script>
    @stack('scripts')
</body>

</html>