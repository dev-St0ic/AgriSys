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
        }

        .sidebar .nav-link {
            color: #ecf0f1;
            border-radius: 0.5rem;
            margin: 0.2rem 0;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link:hover {
            background-color: rgba(52, 152, 219, 0.2);
            color: #3498db;
        }

        .sidebar .nav-link.active {
            background-color: #3498db;
            color: white;
        }

        .main-content {
            background-color: #f8f9fa;
            min-height: 100vh;
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
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            @auth
                @if(auth()->check() && (request()->routeIs('admin.*') || request()->routeIs('dashboard')))
                    <!-- Sidebar - Only show if authenticated AND on admin routes -->
                    <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                        <div class="position-sticky pt-3">
                            <div class="text-center mb-4">
                                <h4 class="text-white">
                                    <i class="fas fa-seedling me-2"></i>AgriSys
                                </h4>
                                <small class="text-muted">
                                    {{ auth()->user()->isSuperAdmin() ? 'Super Admin' : 'Admin' }}
                                </small>
                            </div>

                            <ul class="nav flex-column">
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                                        href="{{ route('admin.dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-2"></i>
                                        Dashboard
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.rsbsa.*') ? 'active' : '' }}"
                                        href="{{ route('admin.rsbsa.applications') }}">
                                        <i class="fas fa-file-alt me-2"></i>
                                        RSBSA Applications
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.seedling.requests') ? 'active' : '' }}"
                                        href="{{ route('admin.seedling.requests') }}">
                                        <i class="fas fa-seedling me-2"></i>
                                        Seedling Requests
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.fishr.requests') ? 'active' : '' }}"
                                        href="{{ route('admin.fishr.requests') }}">
                                        <i class="fas fa-fish me-2"></i>
                                        FishR Registrations
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.boatr.requests') ? 'active' : '' }}"
                                        href="{{ route('admin.boatr.requests') }}">
                                        <i class="fas fa-ship me-2"></i>
                                        BoatR Registrations
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}"
                                        href="{{ route('admin.inventory.index') }}">
                                        <i class="fas fa-warehouse me-2"></i>
                                        Inventory Management
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.analytics.analytics') ? 'active' : '' }}"
                                        href="{{ route('admin.analytics.seedlings') }}">
                                        <i class="fas fa-chart-bar me-2"></i>
                                         Analytics
                                    </a>
                                </li>
                                @if (auth()->user()->isSuperAdmin())
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('admin.admins.*') ? 'active' : '' }}"
                                            href="{{ route('admin.admins.index') }}">
                                            <i class="fas fa-users-cog me-2"></i>
                                            Manage Admins
                                        </a>
                                    </li>
                                @endif
                            </ul>

                            <hr class="text-white">

                            <div class="nav-item">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="nav-link btn btn-link text-start">
                                        <i class="fas fa-sign-out-alt me-2"></i>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </nav>

                    <!-- Main content with sidebar -->
                    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
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
                                                <button type="submit" class="dropdown-item">
                                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
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
                    </main>
                @else
                    <!-- Authenticated but not on admin routes - full width -->
                    <main class="col-12 main-content">
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
                <main class="col-12 main-content">
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
</body>

</html>