{{-- resources/views/admin/analytics/partials/nav.blade.php --}}
{{--
    USAGE: Include this partial in any analytics blade file:
    @include('admin.analytics.partials.nav')

    No props needed - it auto-detects the active route.
--}}

<style>
    .analytics-nav-bar .btn {
        padding: 12px 24px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.95rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: background 0.2s ease, border-color 0.2s ease, color 0.2s ease;
        transform: none !important;
    }

    .analytics-nav-bar .btn:hover,
    .analytics-nav-bar .btn:focus,
    .analytics-nav-bar .btn:active,
    .analytics-nav-bar .btn:focus-visible,
    .analytics-nav-bar .btn:focus-within {
        transform: none !important;
        box-shadow: none !important;
    }

    .analytics-nav-bar .btn-outline-secondary {
        background: #ffffff !important;
        border: 2px solid #e0e0e0 !important;
        color: #555 !important;
    }

    .analytics-nav-bar .btn-outline-secondary:hover {
        background: #f8f9fa !important;
        border-color: #40916c !important;
        color: #40916c !important;
    }

    .analytics-nav-bar .btn-outline-secondary:focus,
    .analytics-nav-bar .btn-outline-secondary:active,
    .analytics-nav-bar .btn-outline-secondary:focus-visible {
        background: #f8f9fa !important;
        border-color: #40916c !important;
        color: #40916c !important;
    }

    .analytics-nav-bar .btn-success {
        background: linear-gradient(135deg, #40916c 0%, #52b788 100%) !important;
        color: #ffffff !important;
        border: 2px solid #40916c !important;
        box-shadow: 0 4px 12px rgba(64, 145, 108, 0.3) !important;
    }

    .analytics-nav-bar .btn-success:hover,
    .analytics-nav-bar .btn-success:active,
    .analytics-nav-bar .btn-success:focus {
        background: linear-gradient(135deg, #2d6a4f 0%, #40916c 100%) !important;
        border-color: #2d6a4f !important;
        color: #ffffff !important;
    }
</style>

<div class="analytics-nav-bar d-flex flex-wrap gap-2 justify-content-center">

    <a href="{{ route('admin.analytics.seedlings') }}"
        class="btn {{ request()->routeIs('admin.analytics.seedlings') ? 'btn-success' : 'btn-outline-secondary' }}">
        <i class="fas fa-seedling me-1"></i> Supply Request
    </a>

    <a href="{{ route('admin.analytics.rsbsa') }}"
        class="btn {{ request()->routeIs('admin.analytics.rsbsa') ? 'btn-success' : 'btn-outline-secondary' }}">
        <i class="fas fa-user-check me-1"></i> RSBSA
    </a>

    <a href="{{ route('admin.analytics.fishr') }}"
        class="btn {{ request()->routeIs('admin.analytics.fishr') ? 'btn-success' : 'btn-outline-secondary' }}">
        <i class="fas fa-fish me-1"></i> FISHR
    </a>

    <a href="{{ route('admin.analytics.boatr') }}"
        class="btn {{ request()->routeIs('admin.analytics.boatr') ? 'btn-success' : 'btn-outline-secondary' }}">
        <i class="fas fa-ship me-1"></i> BOATR
    </a>

    <a href="{{ route('admin.analytics.training') }}"
        class="btn {{ request()->routeIs('admin.analytics.training') ? 'btn-success' : 'btn-outline-secondary' }}">
        <i class="fas fa-graduation-cap me-1"></i> Training
    </a>

    <a href="{{ route('admin.analytics.supply-management') }}"
        class="btn {{ request()->routeIs('admin.analytics.supply-management') ? 'btn-success' : 'btn-outline-secondary' }}">
        <i class="fas fa-boxes me-1"></i> Supply Management
    </a>

    <a href="{{ route('admin.analytics.user-registration') }}"
        class="btn {{ request()->routeIs('admin.analytics.user-registration') ? 'btn-success' : 'btn-outline-secondary' }}">
        <i class="fas fa-user-plus me-1"></i> User Registration
    </a>

</div>
