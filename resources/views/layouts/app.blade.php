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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <!-- Apply sidebar state IMMEDIATELY to prevent flash -->
    <script>
        // Inline script to apply state before any rendering
        (function() {
            const SIDEBAR_STORAGE_KEY = 'agrisys_sidebar_collapsed';

            function getSavedState() {
                try {
                    return localStorage.getItem(SIDEBAR_STORAGE_KEY) === '1';
                } catch (e) {
                    return false;
                }
            }

            // Apply CSS class to html element immediately
            if (getSavedState()) {
                document.documentElement.classList.add('sidebar-collapsed-state');
            }
        })();
    </script>

    <style>
        body {
            overflow-x: auto;
        }

        .sidebar {
            min-height: 100vh;
            max-height: 100vh;
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            transition: all 0.3s ease;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            width: 250px;
            overflow-x: hidden;
            overflow-y: auto;
        }

        .sidebar.collapsed {
            width: 80px;
        }

        /* Apply collapsed state from HTML class */
        html.sidebar-collapsed-state .sidebar {
            width: 80px;
        }

        /* Override collapsed state when no sidebar exists */
        html.sidebar-collapsed-state .main-content.no-sidebar {
            margin-left: 0 !important;
            width: 100vw !important;
            max-width: 100vw !important;
        }

        html.sidebar-collapsed-state .main-content {
            margin-left: 80px !important;
            width: calc(100vw - 80px) !important;
            max-width: calc(100vw - 80px) !important;
        }

        html.sidebar-collapsed-state .nav-link {
            justify-content: center !important;
            text-align: center !important;
            padding: 0.75rem !important;
        }

        html.sidebar-collapsed-state .nav-link-text,
        html.sidebar-collapsed-state .sidebar-brand-text {
            display: none !important;
        }

        html.sidebar-collapsed-state .sidebar-brand h4 {
            opacity: 0 !important;
            transform: scale(0) !important;
        }

        html.sidebar-collapsed-state .nav-link i,
        html.sidebar-collapsed-state .toggle-sidebar-btn i {
            margin: 0 !important;
            min-width: auto !important;
        }

        html.sidebar-collapsed-state .toggle-sidebar-btn {
            justify-content: center !important;
            text-align: center !important;
            padding: 0.75rem !important;
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

        .sidebar.collapsed .nav-link {
            justify-content: center;
            text-align: center;
            padding: 0.75rem;
        }

        .sidebar.collapsed .nav-link-text {
            display: none;
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

        .sidebar.collapsed .nav-link i {
            margin: 0;
            min-width: auto;
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
            transition: margin-left 0.3s ease;
            position: relative;
            overflow-x: auto;
            width: calc(100vw - 250px);
            max-width: calc(100vw - 250px);
        }

        .main-content.sidebar-collapsed {
            margin-left: 80px;
            width: calc(100vw - 80px);
            max-width: calc(100vw - 80px);
        }

        .main-content.no-sidebar {
            margin-left: 0;
            width: 100vw;
            max-width: 100vw;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .container-fluid {
            min-width: 0;
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

        .nav-item {
            margin-bottom: 0.25rem;
        }

        .main-content::-webkit-scrollbar {
            height: 8px;
        }

        .main-content::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .main-content::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        .main-content::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        .table {
            white-space: nowrap;
        }

        .container-fluid {
            padding-right: 15px;
            padding-left: 15px;
        }

        .profile-section {
            gap: 1.5rem;
        }

        .profile-section .btn-link {
            text-decoration: none;
        }

        .profile-section .dropdown button:hover {
            opacity: 0.8;
        }
    </style>
</head>

<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            @auth
                @if (auth()->check() && (request()->routeIs('admin.*') || request()->routeIs('dashboard')))
                    <!-- Sidebar Overlay for mobile -->
                    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebarMobile()"></div>

                    <!-- Sidebar -->
                    <nav class="sidebar" id="sidebar">
                        <div class="position-sticky pt-3">
                            <!-- Hamburger Toggle Button -->
                            <div class="d-none d-md-block px-2">
                                <button class="toggle-sidebar-btn tooltip-custom" id="toggleSidebar"
                                    onclick="toggleSidebar()" data-tooltip="Toggle Menu">
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

                            <ul class="nav flex-column">
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }} tooltip-custom"
                                        href="{{ route('admin.dashboard') }}" data-tooltip="Dashboard">
                                        <i class="fas fa-tachometer-alt"></i>
                                        <span class="nav-link-text">Dashboard</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.event.*') ? 'active' : '' }} tooltip-custom"
                                        href="{{ route('admin.event.index') }}" data-tooltip="Event Management">
                                        <i class="fas fa-calendar-alt"></i>
                                        <span class="nav-link-text">Events</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.registrations.*') ? 'active' : '' }} tooltip-custom"
                                        href="{{ route('admin.registrations.index') }}" data-tooltip="User Registrations">
                                        <i class="fas fa-user-edit"></i>
                                        <span class="nav-link-text">User Registration</span>
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
                                    <a class="nav-link {{ request()->routeIs('admin.seedlings.categories.index') ? 'active' : '' }} tooltip-custom"
                                        href="{{ route('admin.seedlings.categories.index') }}"
                                        data-tooltip="Supply Management">
                                        <i class="fas fa-layer-group me-2"></i>
                                        <span class="nav-link-text">Supply Management</span>
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
                                        href="{{ route('admin.training.requests') }}"
                                        data-tooltip="Training Registrations">
                                        <i class="fas fa-chalkboard-teacher"></i>
                                        <span class="nav-link-text">Training Registrations</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.analytics.analytics') ? 'active' : '' }} tooltip-custom"
                                        href="{{ route('admin.analytics.seedlings') }}" data-tooltip="Analytics">
                                        <i class="fas fa-chart-bar"></i>
                                        <span class="nav-link-text">Analytics</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.dss.*') ? 'active' : '' }} tooltip-custom"
                                        href="{{ route('admin.dss.preview') }}" data-tooltip="DSS Report Preview">
                                        <i class="fas fa-brain"></i>
                                        <span class="nav-link-text">DSS Report Preview</span>
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

                    <!-- Main content with sidebar -->
                    <main class="main-content" id="mainContent">
                        <div class="container-fluid px-4">
                            <!-- Top navbar -->
                            <div
                                class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                                <h1 class="h2">@yield('page-title', 'Dashboard')</h1>
                                <div class="btn-toolbar mb-2 mb-md-0">
                                    <div class="d-flex align-items-center profile-section">
                                        <!-- Notification Bell -->
                                        <button class="btn btn-link text-dark position-relative p-0" type="button">
                                            <i class="fas fa-bell fs-5"></i>
                                            <span
                                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                                style="font-size: 0.6rem;">
                                                3
                                            </span>
                                        </button>

                                        <!-- Profile Dropdown -->
                                        <div class="dropdown d-flex align-items-center" style="gap: 0.75rem;">
                                            <!-- Profile Picture (non-clickable) -->
                                            @if (auth()->user()->profile_photo_url)
                                                <img src="{{ auth()->user()->profile_photo_url }}" alt="Profile"
                                                    class="rounded-circle" width="40" height="40"
                                                    style="object-fit: cover;">
                                            @else
                                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold"
                                                    style="width: 40px; height: 40px; font-size: 16px;">
                                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <!-- Name (non-clickable) -->
                                            <span class="fw-semibold text-dark"
                                                style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ auth()->user()->name }}</span>
                                            <!-- Three Dots Button (clickable dropdown) -->
                                            <button class="btn btn-link text-dark p-0" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false"
                                                style="text-decoration: none;">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center"
                                                        href="{{ route('admin.profile.edit') }}">
                                                        <i class="fas fa-user-edit me-2"></i>Edit Profile
                                                    </a>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <button type="button" class="dropdown-item d-flex align-items-center"
                                                        onclick="confirmLogout()">
                                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <!-- Hidden logout form -->
                                    <form id="logout-form" method="POST" action="{{ route('logout') }}"
                                        style="display: none;">
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
                    <main class="main-content no-sidebar">
                        <div class="container-fluid px-4">
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
                @endif
            @else
                <!-- Not authenticated - full width, no sidebar -->
                <main class="main-content no-sidebar">
                    <div class="container-fluid px-4">
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
                                <button type="button" class="bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Page content -->
                        @yield('content')
                    </div>
                </main>
            @endauth
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')

    <script>
        // Enhanced persistent sidebar state management
        const SIDEBAR_STORAGE_KEY = 'agrisys_sidebar_collapsed';

        function saveSidebarState(isCollapsed) {
            try {
                localStorage.setItem(SIDEBAR_STORAGE_KEY, isCollapsed ? '1' : '0');
                // Also update HTML class immediately
                if (isCollapsed) {
                    document.documentElement.classList.add('sidebar-collapsed-state');
                } else {
                    document.documentElement.classList.remove('sidebar-collapsed-state');
                }
            } catch (e) {
                console.warn('Could not save sidebar state to localStorage');
            }
        }

        function getSavedSidebarState() {
            try {
                const saved = localStorage.getItem(SIDEBAR_STORAGE_KEY);
                return saved === '1';
            } catch (e) {
                return false;
            }
        }

        function confirmLogout() {
            if (confirm('Are you sure you want to log out?')) {
                document.getElementById('logout-form').submit();
            }
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const toggleIcon = document.getElementById('toggleIcon');

            if (!sidebar || !mainContent || !toggleIcon) return;

            // Toggle collapsed state
            const isCurrentlyCollapsed = sidebar.classList.contains('collapsed');
            const willBeCollapsed = !isCurrentlyCollapsed;

            if (willBeCollapsed) {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('sidebar-collapsed');
                mainContent.classList.remove('no-sidebar');
            } else {
                sidebar.classList.remove('collapsed');
                mainContent.classList.remove('sidebar-collapsed');
                mainContent.classList.remove('no-sidebar');
            }

            // Save the new state (this also updates HTML class)
            saveSidebarState(willBeCollapsed);
        }

        function applySidebarState() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');

            if (!sidebar || !mainContent) return;

            // Get saved state
            const shouldBeCollapsed = getSavedSidebarState();

            // Remove any existing transition temporarily
            sidebar.style.transition = 'none';
            mainContent.style.transition = 'none';

            if (shouldBeCollapsed) {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('sidebar-collapsed');
                mainContent.classList.remove('no-sidebar');
                document.documentElement.classList.add('sidebar-collapsed-state');
            } else {
                sidebar.classList.remove('collapsed');
                mainContent.classList.remove('sidebar-collapsed');
                mainContent.classList.remove('no-sidebar');
                document.documentElement.classList.remove('sidebar-collapsed-state');
            }

            // Re-enable transitions after a short delay
            setTimeout(() => {
                sidebar.style.transition = '';
                mainContent.style.transition = '';
            }, 50);
        }

        // Apply state immediately when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                applySidebarState();
                // Apply again after a small delay to ensure it sticks
                setTimeout(applySidebarState, 100);
            });
        } else {
            applySidebarState();
            setTimeout(applySidebarState, 100);
        }

        // Apply when page is fully loaded
        window.addEventListener('load', function() {
            applySidebarState();
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            applySidebarState();
        });

        // Apply state multiple times to ensure reliability
        setTimeout(applySidebarState, 250);
        setTimeout(applySidebarState, 500);
    </script>

    <!-- Demo content to test horizontal scrolling -->
    <style>
        .demo-wide-table {
            min-width: 1200px;
        }
    </style>
</body>

</html>
