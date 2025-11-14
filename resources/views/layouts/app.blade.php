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
            background: #ffffff;
            transition: all 0.3s ease;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            width: 280px;
            overflow-x: hidden;
            overflow-y: auto;
            border-right: 1px solid rgba(0, 0, 0, 0.1);
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
        }

        .sidebar.collapsed {
            width: 75px;
        }

        /* Cabinet drawer effect */
        ` .sidebar.collapsed::after {
            content: '';
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            width: 1px;
            background: linear-gradient(to bottom,
                    transparent 0%,
                    rgba(255, 255, 255, 0.1) 10%,
                    rgba(255, 255, 255, 0.1) 90%,
                    transparent 100%);
        }

        /* Minimalist scrollbar */
        .sidebar::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(100, 181, 246, 0.3);
            border-radius: 2px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(100, 181, 246, 0.5);
        }

        /* Update toggle icon rotation */
        .toggle-icon {
            transition: transform 0.3s ease;
        }

        .sidebar.collapsed .toggle-icon {
            transform: rotate(180deg);
        }

        html.sidebar-collapsed-state .toggle-icon {
            transform: rotate(180deg);
        }

        /* Better toggle button positioning */
        .toggle-btn-container {
            display: flex;
            justify-content: center;
            margin-bottom: 1rem;
            padding: 0 1rem;
        }

        /* Apply collapsed state from HTML class */
        html.sidebar-collapsed-state .sidebar {
            width: 75px;
        }

        /* Override collapsed state when no sidebar exists */
        html.sidebar-collapsed-state .main-content.no-sidebar {
            margin-left: 0 !important;
            width: 100vw !important;
            max-width: 100vw !important;
        }

        html.sidebar-collapsed-state .main-content {
            margin-left: 75px !important;
            width: calc(100vw - 75px) !important;
            max-width: calc(100vw - 75px) !important;
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

        html.sidebar-collapsed-state .toggle-icon {
            transform: rotate(180deg);
        }

        .nav-link {
            color: #6b7280 !important;
            padding: 0.75rem 1.25rem;
            border-radius: 10px;
            margin: 0.25rem 0.75rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            font-weight: 500;
            border: 1px solid transparent;
        }

        .nav-link:hover {
            background: rgba(107, 114, 128, 0.1);
            color: #374151 !important;
            transform: translateX(5px);
            border-color: rgba(107, 114, 128, 0.2);
            box-shadow: 0 2px 8px rgba(107, 114, 128, 0.2);
        }

        .nav-link:hover .nav-link-text {
            color: #374151 !important;
        }

        .nav-link:hover i {
            color: #374151 !important;
        }

        .sidebar .nav-link.active {
            background: #10b981;
            color: white !important;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            border-color: #059669;
        }

        .sidebar .nav-link.active .nav-link-text {
            color: white !important;
        }

        .sidebar .nav-link.active i {
            color: white !important;
        }

        .sidebar.collapsed .nav-link {
            justify-content: center;
            text-align: center;
            padding: 0.75rem;
            margin: 0.2rem 0.5rem;
        }

        .sidebar.collapsed .nav-link:hover {
            background: rgba(100, 181, 246, 0.2);
        }

        .sidebar.collapsed .nav-link.active {
            background: #10b981;
        }

        .sidebar.collapsed .nav-link.active i {
            color: white !important;
        }

        .sidebar.collapsed .nav-link-text {
            display: none;
        }

        .sidebar .nav-link-text {
            transition: all 0.3s ease;
            opacity: 1;
            margin-left: 0.5rem;
            flex: 1;
            color: #6b7280 !important;
        }

        .sidebar .nav-link i {
            min-width: 20px;
            text-align: center;
            font-size: 1.1rem;
            flex-shrink: 0;
            transition: all 0.3s ease;
            color: #6b7280 !important;
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
            margin-left: 280px;
            transition: margin-left 0.3s ease;
            position: relative;
            overflow-x: auto;
            width: calc(100vw - 280px);
            max-width: calc(100vw - 280px);
        }

        .main-content.sidebar-collapsed {
            margin-left: 75px;
            width: calc(100vw - 75px);
            max-width: calc(100vw - 75px);
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
            padding: 1.5rem 1rem;
            border-bottom: 1px solid rgba(100, 181, 246, 0.15);
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 1rem;
        }

        /* New sidebar header styling */
        .sidebar-header {
            padding: 2rem 1.5rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            background: #ffffff;
            margin-bottom: 1rem;
        }

        .brand-text {
            line-height: 1.2;
        }

        .sidebar.collapsed .sidebar-header {
            padding: 1rem 0.5rem;
            justify-content: center !important;
        }

        .sidebar.collapsed .sidebar-brand-content {
            display: none !important;
        }

        .sidebar-logo {
            transition: all 0.3s ease;
            max-width: 100%;
            object-fit: contain;
        }

        .sidebar.collapsed .sidebar-logo {
            height: 60px;
            width: auto;
        }

        .sidebar.collapsed .toggle-sidebar-btn {
            margin: 0 auto;
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

        .sidebar-brand h4 {
            transition: all 0.3s ease;
            opacity: 1;
            transform: scale(1);
            margin-bottom: 0.5rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .sidebar-brand h4 i {
            color: #64B5F6;
        }

        .sidebar-brand small {
            font-size: 0.75rem;
            font-weight: 400;
            color: rgba(227, 242, 253, 0.7);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .toggle-sidebar-btn {
            background: rgba(0, 0, 0, 0.05);
            border: none;
            color: #374151;
            border-radius: 8px;
            transition: all 0.3s ease;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.6rem;
            width: 40px;
            height: 40px;
            position: relative;
            overflow: hidden;
        }

        .toggle-sidebar-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(0, 0, 0, 0.1);
            border-radius: 50%;
            transition: all 0.3s ease;
            transform: translate(-50%, -50%);
        }

        .toggle-sidebar-btn:hover {
            background: rgba(0, 0, 0, 0.1);
            color: #1f2937;
            transform: scale(1.05);
        }

        .toggle-sidebar-btn:hover::before {
            width: 60px;
            height: 60px;
        }

        .toggle-sidebar-btn:active {
            transform: scale(0.95);
        }

        .toggle-sidebar-btn i {
            font-size: 1.2rem;
            transition: all 0.3s ease;
            z-index: 1;
            position: relative;
        }

        /* Hamburger Menu Styles */
        .hamburger-menu {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 18px;
            height: 18px;
            position: relative;
            z-index: 1;
        }

        .hamburger-line {
            width: 18px;
            height: 2px;
            background-color: #374151;
            border-radius: 2px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin: 1.5px 0;
            transform-origin: center;
        }

        .hamburger-line:nth-child(1) {
            margin-top: 0;
        }

        .hamburger-line:nth-child(3) {
            margin-bottom: 0;
        }

        /* Hover effects for hamburger */
        .toggle-sidebar-btn:hover .hamburger-line {
            background-color: #ffffff;
        }

        .toggle-sidebar-btn .nav-link-text {
            display: none;
        }

        .sidebar.collapsed .toggle-sidebar-btn {
            justify-content: center;
            text-align: center;
            padding: 0.75rem;
            margin: 0 auto 1rem auto;
            width: 45px;
            height: 45px;
        }

        /* Improved collapsed state visibility */
        html.sidebar-collapsed-state .toggle-sidebar-btn {
            background: rgba(100, 181, 246, 0.15);
        }

        html.sidebar-collapsed-state .toggle-sidebar-btn:hover {
            background: rgba(100, 181, 246, 0.25);
        }

        .sidebar.collapsed .toggle-sidebar-btn .nav-link-text {
            opacity: 0;
            width: 0;
            margin-left: 0;
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
            background: rgba(30, 58, 95, 0.95);
            color: white;
            padding: 8px 12px;
            border-radius: 4px;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: all 0.3s ease;
            margin-left: 15px;
            font-size: 13px;
            font-weight: 400;
            z-index: 1002;
        }

        .tooltip-custom::before {
            content: '';
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%);
            border: 6px solid transparent;
            border-right-color: rgba(30, 58, 95, 0.95);
            opacity: 0;
            transition: all 0.3s ease;
            margin-left: 9px;
            z-index: 1001;
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

        /* Navigation section divider */
        .nav-section-divider {
            display: flex;
            align-items: center;
            margin: 1.5rem 1rem 1rem 1rem;
            opacity: 0.6;
        }

        .divider-line {
            flex: 1;
            height: 1px;
            background: rgba(0, 0, 0, 0.1);
        }

        .divider-text {
            padding: 0 0.75rem;
            font-size: 0.7rem;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 500;
        }

        .sidebar.collapsed .nav-section-divider {
            margin: 1rem 0.5rem;
        }

        .sidebar.collapsed .divider-text {
            display: none;
        }

        .sidebar.collapsed .divider-line {
            background: rgba(100, 181, 246, 0.2);
        }

        /* Enhanced mobile responsiveness */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                z-index: 1050;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1040;
                backdrop-filter: blur(2px);
            }

            .sidebar-overlay.show {
                display: block;
            }

            .main-content {
                margin-left: 0;
                width: 100%;
                max-width: 100%;
            }

            .toggle-sidebar-btn {
                margin: 0.25rem;
            }
        }

        /* Enhanced focus states for accessibility */
        .sidebar .nav-link:focus,
        .toggle-sidebar-btn:focus {
            outline: 2px solid #64B5F6;
            outline-offset: 2px;
            box-shadow: 0 0 0 4px rgba(100, 181, 246, 0.2);
        }

        /* Add a subtle pulse effect for the toggle button */
        .toggle-sidebar-btn {
            box-shadow: 0 2px 8px rgba(100, 181, 246, 0.15);
        }

        .toggle-sidebar-btn:hover {
            box-shadow: 0 4px 12px rgba(100, 181, 246, 0.25);
        }

        /* Tooltip positioning for toggle button */
        .toggle-sidebar-btn.tooltip-custom::after {
            left: 120%;
            margin-left: 10px;
        }

        .toggle-sidebar-btn.tooltip-custom::before {
            left: 120%;
            margin-left: 4px;
        }

        /* Improved collapsed state styles */
        .sidebar.collapsed .nav-link {
            position: relative;
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
                            <!-- Header with Brand and Toggle -->
                            <div class="sidebar-header d-flex align-items-center justify-content-between px-3 mb-3">
                                <div class="sidebar-brand-content d-flex align-items-center justify-content-center">
                                    <img src="{{ asset('images/logos/agrii-removebg.png') }}" alt="AgriSys Logo"
                                        class="sidebar-logo"
                                        style="height: 80px; width: auto;
                                                filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));">
                                </div>

                                <!-- Hamburger Toggle Button on Right -->
                                <div class="d-none d-md-block">
                                    <button class="toggle-sidebar-btn tooltip-custom" id="toggleSidebar"
                                        onclick="toggleSidebar()" data-tooltip="Toggle Menu"
                                        aria-label="Toggle navigation menu" aria-expanded="true" type="button">
                                        <!-- Hamburger Lines -->
                                        <div class="hamburger-menu" id="hamburgerMenu">
                                            <span class="hamburger-line"></span>
                                            <span class="hamburger-line"></span>
                                            <span class="hamburger-line"></span>
                                        </div>
                                    </button>
                                </div>
                            </div>

                            <!-- Navigation Section Separator -->
                            <div class="nav-section-divider">
                                <div class="divider-line"></div>
                                <span class="divider-text">Navigation</span>
                                <div class="divider-line"></div>
                            </div>

                            <ul class="nav flex-column" role="navigation" aria-label="Main navigation">
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('landing.page') ? 'active' : '' }} tooltip-custom"
                                        href="{{ route('landing.page') }}" data-tooltip="Landing Page"
                                        aria-label="Landing Page" role="menuitem">
                                        <i class="fas fa-home" aria-hidden="true"></i>
                                        <span class="nav-link-text">Home Page</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }} tooltip-custom"
                                        href="{{ route('admin.dashboard') }}" data-tooltip="Dashboard"
                                        aria-label="Dashboard" role="menuitem">
                                        <i class="fas fa-tachometer-alt" aria-hidden="true"></i>
                                        <span class="nav-link-text">Dashboard</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.event.*') ? 'active' : '' }} tooltip-custom"
                                        href="{{ route('admin.event.index') }}" data-tooltip="Event Management"
                                        aria-label="Event Management" role="menuitem">
                                        <i class="fas fa-calendar-alt" aria-hidden="true"></i>
                                        <span class="nav-link-text">Events</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.slideshow.*') ? 'active' : '' }} tooltip-custom"
                                        href="{{ route('admin.slideshow.index') }}" data-tooltip="Slideshow Management"
                                        aria-label="Slideshow Management" role="menuitem">
                                        <i class="fas fa-images" aria-hidden="true"></i>
                                        <span class="nav-link-text">Slideshow Management</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.registrations.*') ? 'active' : '' }} tooltip-custom"
                                        href="{{ route('admin.registrations.index') }}" data-tooltip="User Registrations"
                                        aria-label="User Registrations" role="menuitem">
                                        <i class="fas fa-user-edit" aria-hidden="true"></i>
                                        <span class="nav-link-text">User Registration</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.rsbsa.*') ? 'active' : '' }} tooltip-custom"
                                        href="{{ route('admin.rsbsa.applications') }}" data-tooltip="RSBSA Applications"
                                        aria-label="RSBSA Applications" role="menuitem">
                                        <i class="fas fa-file-alt" aria-hidden="true"></i>
                                        <span class="nav-link-text">RSBSA Applications</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.seedlings.requests') ? 'active' : '' }} tooltip-custom"
                                        href="{{ route('admin.seedlings.requests') }}" data-tooltip="Seedling Requests"
                                        aria-label="Seedling Requests" role="menuitem">
                                        <i class="fas fa-seedling" aria-hidden="true"></i>
                                        <span class="nav-link-text">Seedling Requests</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.seedlings.supply-management.index') ? 'active' : '' }} tooltip-custom"
                                        href="{{ route('admin.seedlings.supply-management.index') }}"
                                        data-tooltip="Supply Management" aria-label="Supply Management" role="menuitem">
                                        <i class="fas fa-layer-group me-2" aria-hidden="true"></i>
                                        <span class="nav-link-text">Supply Management</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.fishr.requests') ? 'active' : '' }} tooltip-custom"
                                        href="{{ route('admin.fishr.requests') }}" data-tooltip="FishR Registrations"
                                        aria-label="FishR Registrations" role="menuitem">
                                        <i class="fas fa-fish" aria-hidden="true"></i>
                                        <span class="nav-link-text">FishR Registrations</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.boatr.requests') ? 'active' : '' }} tooltip-custom"
                                        href="{{ route('admin.boatr.requests') }}" data-tooltip="BoatR Registrations"
                                        aria-label="BoatR Registrations" role="menuitem">
                                        <i class="fas fa-ship" aria-hidden="true"></i>
                                        <span class="nav-link-text">BoatR Registrations</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.training.requests') ? 'active' : '' }} tooltip-custom"
                                        href="{{ route('admin.training.requests') }}"
                                        data-tooltip="Training Registrations" aria-label="Training Registrations"
                                        role="menuitem">
                                        <i class="fas fa-chalkboard-teacher" aria-hidden="true"></i>
                                        <span class="nav-link-text">Training Registrations</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.analytics.analytics') ? 'active' : '' }} tooltip-custom"
                                        href="{{ route('admin.analytics.seedlings') }}" data-tooltip="Analytics"
                                        aria-label="Analytics" role="menuitem">
                                        <i class="fas fa-chart-bar" aria-hidden="true"></i>
                                        <span class="nav-link-text">Analytics</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.dss.*') ? 'active' : '' }} tooltip-custom"
                                        href="{{ route('admin.dss.preview') }}" data-tooltip="DSS Report Preview"
                                        aria-label="DSS Report Preview" role="menuitem">
                                        <i class="fas fa-brain" aria-hidden="true"></i>
                                        <span class="nav-link-text">DSS Report Preview</span>
                                    </a>
                                </li>

                                @if (auth()->user()->isSuperAdmin())
                                    <!-- Admin Section Separator -->
                                    <div class="nav-section-divider">
                                        <div class="divider-line"></div>
                                        <span class="divider-text">Admin</span>
                                        <div class="divider-line"></div>
                                    </div>

                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }} tooltip-custom"
                                            href="{{ route('admin.activity-logs.index') }}" data-tooltip="Activity Logs"
                                            aria-label="Activity Logs" role="menuitem">
                                            <i class="fas fa-history" aria-hidden="true"></i>
                                            <span class="nav-link-text">Activity Logs</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.admins.*') ? 'active' : '' }} tooltip-custom"
                                            href="{{ route('admin.admins.index') }}" data-tooltip="Manage Admins"
                                            aria-label="Manage Admins" role="menuitem">
                                            <i class="fas fa-users-cog" aria-hidden="true"></i>
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
                                <h1 class="h2">@yield('page-title', '')</h1>
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

    <!-- jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>

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

            if (!sidebar || !mainContent) return;

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

    <!-- Real-time Activities JavaScript -->
    <script>
        // Add auth info for JavaScript
        window.auth = {
            isAdmin: @json(auth()->check() && auth()->user()->isAdmin()),
            user: @json(auth()->user() ? ['id' => auth()->user()->id, 'name' => auth()->user()->name] : null)
        };
    </script>

    <!-- Demo content to test horizontal scrolling -->
    <style>
        .demo-wide-table {
            min-width: 1200px;
        }
    </style>
</body>

</html>
