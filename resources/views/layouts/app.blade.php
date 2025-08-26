<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AgriSys Admin')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            transition: all 0.3s ease;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            width: 250px;
            overflow-x: hidden;
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar .nav-link {
            color: #ecf0f1;
            border-radius: 0.5rem;
            margin: 0.2rem 0;
            transition: all 0.3s ease;
            white-space: nowrap;
            overflow: hidden;
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            text-align: left;
        }

        .sidebar .nav-link:hover {
            background-color: rgba(52, 152, 219, 0.2);
            color: #3498db;
        }

        .sidebar .nav-link.active {
            background-color: #3498db;
            color: white;
        }

        /* Original centered style for collapsed sidebar */
        .sidebar.collapsed .nav-link {
            justify-content: center;
            text-align: center;
            padding: 0.75rem;
        }

        .sidebar.collapsed .nav-link-text {
            opacity: 0;
            width: 0;
            margin-left: 0;
        }

        .sidebar .nav-link-text {
            transition: all 0.3s ease;
            opacity: 1;
            margin-left: 0.5rem;
            flex: 1;
        }

        .sidebar .nav-link i {
            min-width: 20px;
            text-align: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        /* Ensure icons are centered when collapsed */
        .sidebar.collapsed .nav-link i {
            margin: 0;
        }

        .sidebar.collapsed .sidebar-brand h4 {
            opacity: 0;
            transform: scale(0);
        }

        .sidebar .sidebar-brand h4 {
            transition: all 0.3s ease;
            opacity: 1;
            transform: scale(1);
        }

        .sidebar.collapsed .sidebar-brand-text {
            opacity: 0;
            width: 0;
            margin: 0;
        }

        .sidebar .sidebar-brand-text {
            transition: all 0.3s ease;
            opacity: 1;
        }

        .main-content {
            background-color: #f8f9fa;
            min-height: 100vh;
            margin-left: 250px;
            transition: all 0.3s ease;
        }

        .main-content.expanded {
            margin-left: 80px;
        }

        .main-content.full-width {
            margin-left: 0;
        }

        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .btn-primary {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #2980b9 0%, #3498db 100%);
        }

        /* Enhanced sidebar brand */
        .sidebar-brand {
            padding: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .sidebar.collapsed .sidebar-brand {
            padding: 1rem 0.5rem;
        }

        .sidebar-brand-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }

        /* Hamburger toggle button - positioned before AgriSys but aligns with nav icons when collapsed */
        .toggle-sidebar-btn {
            background: transparent;
            border: none;
            color: #ecf0f1;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
            cursor: pointer;
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            width: 100%;
            margin: 0.2rem 0;
            text-align: left;
            white-space: nowrap;
            overflow: hidden;
        }

        .toggle-sidebar-btn:hover {
            background-color: rgba(52, 152, 219, 0.2);
            color: #3498db;
        }

        .toggle-sidebar-btn i {
            font-size: 1.1rem;
            transition: all 0.3s ease;
            min-width: 20px;
            text-align: center;
            flex-shrink: 0;
        }

        .toggle-sidebar-btn .nav-link-text {
            transition: all 0.3s ease;
            opacity: 1;
            margin-left: 0.5rem;
            flex: 1;
        }

        /* When collapsed, center the hamburger icon like other nav items */
        .sidebar.collapsed .toggle-sidebar-btn {
            justify-content: center;
            text-align: center;
            padding: 0.75rem;
        }

        .sidebar.collapsed .toggle-sidebar-btn .nav-link-text {
            opacity: 0;
            width: 0;
            margin-left: 0;
        }

        .sidebar.collapsed .toggle-sidebar-btn i {
            margin: 0;
        }

        /* Tooltip for collapsed sidebar */
        .tooltip-custom {
            position: relative;
        }

        .tooltip-custom::after {
            content: attr(data-tooltip);
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.9);
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: all 0.3s ease;
            margin-left: 15px;
            font-size: 14px;
            z-index: 1002;
        }

        .tooltip-custom::before {
            content: '';
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%);
            border: 6px solid transparent;
            border-right-color: rgba(0, 0, 0, 0.9);
            opacity: 0;
            transition: all 0.3s ease;
            margin-left: 9px;
        }

        .sidebar.collapsed .tooltip-custom:hover::after,
        .sidebar.collapsed .tooltip-custom:hover::before {
            opacity: 1;
        }

        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: 250px !important;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0 !important;
            }
            
            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 999;
                display: none;
            }
            
            .sidebar-overlay.show {
                display: block;
            }

            .mobile-toggle-btn {
                position: fixed;
                top: 20px;
                left: 15px;
                z-index: 1001;
                background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
                border: 2px solid rgba(52, 152, 219, 0.3);
                border-radius: 0.25rem;
                padding: 0.5rem;
                width: 45px;
                height: 45px;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 2px 10px rgba(0,0,0,0.2);
                color: #ecf0f1;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .mobile-toggle-btn:hover {
                background: linear-gradient(135deg, #34495e 0%, #2c3e50 100%);
                color: #3498db;
            }

            .sidebar-brand {
                justify-content: center;
            }

            .toggle-sidebar-btn {
                display: none;
            }
        }

        .nav-item {
            margin-bottom: 0.25rem;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            @auth
                @if(auth()->check() && (request()->routeIs('admin.*') || request()->routeIs('dashboard')))
                    <!-- Sidebar Overlay for mobile -->
                    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebarMobile()"></div>

                    <!-- Sidebar -->
                    <nav class="sidebar" id="sidebar">
                        <div class="position-sticky pt-3">
                            <!-- Hamburger Toggle Button - positioned before AgriSys -->
                            <div class="d-none d-md-block px-2">
                                <button class="toggle-sidebar-btn tooltip-custom" id="toggleSidebar" onclick="toggleSidebar()" data-tooltip="Toggle Menu">
                                    <i class="fas fa-bars" id="toggleIcon"></i>
                                    <span class="nav-link-text"></span>
                                </button>
                            </div>

                            <div class="sidebar-brand">
                                <div class="sidebar-brand-content">
                                    <h4 class="text-white mb-0">
                                        <i class="fas fa-seedling me-2"></i>
                                        <span class="sidebar-brand-text">AgriSys</span>
                                    </h4>
                                    <small class="text-muted sidebar-brand-text">
                                        {{ auth()->user()->isSuperAdmin() ? 'Super Admin' : 'Admin' }}
                                    </small>
                                </div>
                            </div>

                            <ul class="nav flex-column px-2">
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }} tooltip-custom"
                                            href="{{ route('admin.dashboard') }}" data-tooltip="Dashboard">
                                            <i class="fas fa-tachometer-alt"></i>
                                            <span class="nav-link-text">Dashboard</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.rsbsa.*') ? 'active' : '' }} tooltip-custom"
                                            href="{{ route('admin.rsbsa.applications') }}" data-tooltip="RSBSA Applications">
                                            <i class="fas fa-file-alt"></i>
                                            <span class="nav-link-text">RSBSA Applications</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.seedlings.requests') ? 'active' : '' }} tooltip-custom"
                                            href="{{ route('admin.seedlings.requests') }}" data-tooltip="Seedling Requests">
                                            <i class="fas fa-seedling"></i>
                                            <span class="nav-link-text">Seedling Requests</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.fishr.requests') ? 'active' : '' }} tooltip-custom"
                                            href="{{ route('admin.fishr.requests') }}" data-tooltip="FishR Registrations">
                                            <i class="fas fa-fish"></i>
                                            <span class="nav-link-text">FishR Registrations</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.boatr.requests') ? 'active' : '' }} tooltip-custom"
                                            href="{{ route('admin.boatr.requests') }}" data-tooltip="BoatR Registrations">
                                            <i class="fas fa-ship"></i>
                                            <span class="nav-link-text">BoatR Registrations</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.training.requests') ? 'active' : '' }} tooltip-custom"
                                            href="{{ route('admin.training.requests') }}" data-tooltip="Training Registrations">
                                            <i class="fas fa-chalkboard-teacher"></i>
                                            <span class="nav-link-text">Training Registrations</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }} tooltip-custom"
                                            href="{{ route('admin.inventory.index') }}" data-tooltip="Inventory Management">
                                            <i class="fas fa-warehouse"></i>
                                            <span class="nav-link-text">Inventory Management</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.analytics.analytics') ? 'active' : '' }} tooltip-custom"
                                            href="{{ route('admin.analytics.seedlings') }}" data-tooltip="Analytics">
                                            <i class="fas fa-chart-bar"></i>
                                            <span class="nav-link-text">Analytics</span>
                                        </a>
                                    </li>
                                    @if (auth()->user()->isSuperAdmin())
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('admin.admins.*') ? 'active' : '' }} tooltip-custom"
                                                href="{{ route('admin.admins.index') }}" data-tooltip="Manage Admins">
                                                <i class="fas fa-users-cog"></i>
                                                <span class="nav-link-text">Manage Admins</span>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                    </nav>

                    <!-- Mobile Toggle Button -->
                    <button class="mobile-toggle-btn d-md-none" id="mobileToggleSidebar" onclick="toggleSidebar()">
                        <i class="fas fa-bars" id="mobileToggleIcon"></i>
                    </button>

                    <!-- Main content with sidebar -->
                    <main class="main-content" id="mainContent">
                        <div class="container-fluid px-4">
                            <!-- Top navbar -->
                            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                                <h1 class="h2">@yield('page-title', 'Dashboard')</h1>
                                <div class="btn-toolbar mb-2 mb-md-0">
                                    <div class="dropdown">
                                        <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-user me-2"></i>{{ auth()->user()->name }}
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <form method="POST" action="{{ route('logout') }}">
                                                    @csrf
                                                    <button type="button" class="dropdown-item" onclick="confirmLogout()">
                                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                    <!-- Hidden logout form -->
                                    <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </div>

                            <!-- Flash messages -->
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <!-- Page content -->
                            @yield('content')
                        </div>
                    </main>
                @else
                    <!-- Authenticated but not on admin routes - full width -->
                    <main class="col-12 main-content full-width">
                        <!-- Flash messages -->
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Page content -->
                        @yield('content')
                    </main>
                @endif
            @else
                <!-- Not authenticated - full width, no sidebar -->
                <main class="col-12 main-content full-width">
                    <!-- Flash messages -->
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Page content -->
                    @yield('content')
                </main>
            @endauth
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
    
    <script>
        function confirmLogout() {
            if (confirm('Are you sure you want to log out?')) {
                document.getElementById('logout-form').submit();
            }
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const toggleIcon = document.getElementById('toggleIcon');
            const mobileToggleIcon = document.getElementById('mobileToggleIcon');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            
            // Check if mobile view
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('show');
                sidebarOverlay.classList.toggle('show');
                
                if (sidebar.classList.contains('show')) {
                    mobileToggleIcon.className = 'fas fa-times';
                } else {
                    mobileToggleIcon.className = 'fas fa-bars';
                }
            } else {
                // Desktop view - toggle collapsed state but keep hamburger icon
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
                
                // Always keep hamburger icon in desktop view
                toggleIcon.className = 'fas fa-bars';
            }
        }

        function closeSidebarMobile() {
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const mobileToggleIcon = document.getElementById('mobileToggleIcon');
            
            sidebar.classList.remove('show');
            sidebarOverlay.classList.remove('show');
            mobileToggleIcon.className = 'fas fa-bars';
        }

        // Handle window resize
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const toggleIcon = document.getElementById('toggleIcon');
            const mobileToggleIcon = document.getElementById('mobileToggleIcon');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            
            if (window.innerWidth > 768) {
                // Desktop view - remove mobile classes
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
                
                // Always use hamburger icon in desktop
                toggleIcon.className = 'fas fa-bars';
                mobileToggleIcon.className = 'fas fa-bars';
            } else {
                // Mobile view - remove desktop classes
                sidebar.classList.remove('collapsed');
                mainContent.classList.remove('expanded');
                toggleIcon.className = 'fas fa-bars';
            }
        });

        // Initialize sidebar state on page load
        document.addEventListener('DOMContentLoaded', function() {
            const toggleIcon = document.getElementById('toggleIcon');
            const mobileToggleIcon = document.getElementById('mobileToggleIcon');
            
            // Always start with hamburger icons
            if (toggleIcon) toggleIcon.className = 'fas fa-bars';
            if (mobileToggleIcon) mobileToggleIcon.className = 'fas fa-bars';
        });
    </script>
</body>

</html>