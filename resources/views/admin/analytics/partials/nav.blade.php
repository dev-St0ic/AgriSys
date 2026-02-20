{{-- resources/views/admin/analytics/partials/nav.blade.php --}}
{{-- 
    USAGE: Include this partial in any analytics blade file:
    @include('admin.analytics.partials.nav')
    
    No props needed - it auto-detects the active route.
--}}

<div class="analytics-nav-wrapper mb-4">
    <div class="analytics-nav-scroll">
        <nav class="analytics-nav">

            <a href="{{ route('admin.analytics.seedlings') }}"
               class="nav-pill {{ request()->routeIs('admin.analytics.seedlings') ? 'active' : '' }}">
                <span class="nav-pill-icon">
                    <i class="fas fa-seedling"></i>
                </span>
                <span class="nav-pill-label">Supply Request</span>
                @if(request()->routeIs('admin.analytics.seedlings'))
                    <span class="nav-pill-dot"></span>
                @endif
            </a>

            <a href="{{ route('admin.analytics.rsbsa') }}"
               class="nav-pill {{ request()->routeIs('admin.analytics.rsbsa') ? 'active' : '' }}">
                <span class="nav-pill-icon">
                    <i class="fas fa-user-check"></i>
                </span>
                <span class="nav-pill-label">RSBSA</span>
                @if(request()->routeIs('admin.analytics.rsbsa'))
                    <span class="nav-pill-dot"></span>
                @endif
            </a>

            <a href="{{ route('admin.analytics.fishr') }}"
               class="nav-pill {{ request()->routeIs('admin.analytics.fishr') ? 'active' : '' }}">
                <span class="nav-pill-icon">
                    <i class="fas fa-fish"></i>
                </span>
                <span class="nav-pill-label">FISHR</span>
                @if(request()->routeIs('admin.analytics.fishr'))
                    <span class="nav-pill-dot"></span>
                @endif
            </a>

            <a href="{{ route('admin.analytics.boatr') }}"
               class="nav-pill {{ request()->routeIs('admin.analytics.boatr') ? 'active' : '' }}">
                <span class="nav-pill-icon">
                    <i class="fas fa-ship"></i>
                </span>
                <span class="nav-pill-label">BOATR</span>
                @if(request()->routeIs('admin.analytics.boatr'))
                    <span class="nav-pill-dot"></span>
                @endif
            </a>

            <a href="{{ route('admin.analytics.training') }}"
               class="nav-pill {{ request()->routeIs('admin.analytics.training') ? 'active' : '' }}">
                <span class="nav-pill-icon">
                    <i class="fas fa-graduation-cap"></i>
                </span>
                <span class="nav-pill-label">Training</span>
                @if(request()->routeIs('admin.analytics.training'))
                    <span class="nav-pill-dot"></span>
                @endif
            </a>

            <a href="{{ route('admin.analytics.supply-management') }}"
               class="nav-pill {{ request()->routeIs('admin.analytics.supply-management') ? 'active' : '' }}">
                <span class="nav-pill-icon">
                    <i class="fas fa-boxes"></i>
                </span>
                <span class="nav-pill-label">Supply Management</span>
                @if(request()->routeIs('admin.analytics.supply-management'))
                    <span class="nav-pill-dot"></span>
                @endif
            </a>

            <a href="{{ route('admin.analytics.user-registration') }}"
               class="nav-pill {{ request()->routeIs('admin.analytics.user-registration') ? 'active' : '' }}">
                <span class="nav-pill-icon">
                    <i class="fas fa-user-plus"></i>
                </span>
                <span class="nav-pill-label">User Management</span>
                @if(request()->routeIs('admin.analytics.user-registration'))
                    <span class="nav-pill-dot"></span>
                @endif
            </a>

        </nav>
    </div>
</div>

<style>
/* ─── Wrapper ─────────────────────────────────────────────── */
.analytics-nav-wrapper {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    box-shadow: 0 1px 6px rgba(0,0,0,0.06);
    padding: 6px 8px;
    overflow: hidden;          /* hides the scrollbar track visually */
}

/* ─── Horizontal scroll container ────────────────────────── */
.analytics-nav-scroll {
    overflow-x: auto;
    overflow-y: hidden;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;     /* Firefox */
}
.analytics-nav-scroll::-webkit-scrollbar {
    display: none;             /* Chrome / Safari */
}

/* ─── The nav row itself ──────────────────────────────────── */
.analytics-nav {
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: space-between; /* fills full width, no gap on right */
    gap: 4px;
    white-space: nowrap;
    padding: 2px 0;
    width: 100%;
}

/* ─── Individual pill ─────────────────────────────────────── */
.nav-pill {
    position: relative;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 8px 16px;
    border-radius: 10px;
    border: 1.5px solid transparent;
    text-decoration: none;
    color: #6b7280;
    font-size: 0.845rem;
    font-weight: 600;
    letter-spacing: 0.01em;
    transition: color 0.18s ease, background 0.18s ease,
                border-color 0.18s ease, box-shadow 0.18s ease,
                transform 0.18s ease;
    flex-shrink: 0;
    white-space: nowrap;
}

.nav-pill:hover {
    color: #1a7a4a;
    background: #f0faf4;
    border-color: #b7e4c7;
    text-decoration: none;
    transform: translateY(-1px);
    box-shadow: 0 3px 10px rgba(40, 167, 69, 0.12);
}

/* ─── Active pill ─────────────────────────────────────────── */
.nav-pill.active {
    color: #ffffff;
    background: linear-gradient(135deg, #2d6a4f 0%, #52b788 100%);
    border-color: #2d6a4f;
    box-shadow: 0 3px 12px rgba(45, 106, 79, 0.35);
    transform: translateY(-1px);
}

.nav-pill.active:hover {
    background: linear-gradient(135deg, #1e4d38 0%, #40916c 100%);
    box-shadow: 0 5px 16px rgba(45, 106, 79, 0.4);
}

/* ─── Icon inside pill ────────────────────────────────────── */
.nav-pill-icon {
    display: flex;
    align-items: center;
    font-size: 0.9rem;
    flex-shrink: 0;
}

.nav-pill.active .nav-pill-icon {
    color: rgba(255,255,255,0.95);
}

/* ─── Subtle fade edges (optional visual cue for scroll) ─── */
.analytics-nav-wrapper {
    position: relative;
}
</style>