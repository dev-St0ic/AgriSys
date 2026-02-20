{{-- resources/views/admin/analytics/partials/filter.blade.php --}}
{{--
    USAGE: Include this partial in any analytics blade file:
    @include('admin.analytics.partials.filter', [
        'filterRoute'  => 'admin.analytics.seedlings',
        'exportRoute'  => 'admin.analytics.seedlings.export',
    ])

    Requires $startDate and $endDate to be passed from the controller.
--}}

<div class="analytics-filter-wrapper mb-4">
    <form method="GET" action="{{ route($filterRoute) }}" class="analytics-filter-form">
        <div class="filter-group">
            <label for="start_date" class="filter-label">
                <i class="fas fa-calendar-alt"></i>
                Start Date
            </label>
            <input type="date" class="filter-input" id="start_date" name="start_date"
                   value="{{ $startDate }}">
        </div>

        <div class="filter-divider">
            <i class="fas fa-arrow-right"></i>
        </div>

        <div class="filter-group">
            <label for="end_date" class="filter-label">
                <i class="fas fa-calendar-check"></i>
                End Date
            </label>
            <input type="date" class="filter-input" id="end_date" name="end_date"
                   value="{{ $endDate }}">
        </div>

        <div class="filter-actions">
            <button type="submit" class="filter-btn filter-btn-apply">
                <i class="fas fa-filter"></i>
                Apply Filter
            </button>
            <a href="{{ route($exportRoute) }}?start_date={{ $startDate }}&end_date={{ $endDate }}"
               class="filter-btn filter-btn-export">
                <i class="fas fa-download"></i>
                Export CSV
            </a>
        </div>
    </form>
</div>

<style>
.analytics-filter-wrapper {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    box-shadow: 0 1px 6px rgba(0,0,0,0.06);
    padding: 16px 20px;
}

.analytics-filter-form {
    display: flex;
    align-items: flex-end;
    gap: 12px;
    flex-wrap: wrap;
    justify-content: flex-start;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.filter-label {
    font-size: 0.8rem;
    font-weight: 600;
    color: #6b7280;
    letter-spacing: 0.03em;
    text-transform: uppercase;
    display: flex;
    align-items: center;
    gap: 5px;
    margin: 0;
}

.filter-label i {
    color: #40916c;
    font-size: 0.75rem;
}

.filter-input {
    border: 1.5px solid #e5e7eb;
    border-radius: 10px;
    padding: 8px 12px;
    font-size: 0.875rem;
    font-weight: 500;
    color: #1f2937;
    background: #f9fafb;
    transition: border-color 0.18s ease, box-shadow 0.18s ease, background 0.18s ease;
    outline: none;
    min-width: 150px;
}

.filter-input:focus {
    border-color: #40916c;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(64, 145, 108, 0.12);
}

.filter-divider {
    color: #d1d5db;
    font-size: 0.75rem;
    padding-bottom: 10px;
    flex-shrink: 0;
}

.filter-actions {
    display: flex;
    gap: 8px;
    margin-left: auto;
    flex-wrap: wrap;
}

.filter-btn {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 9px 18px;
    border-radius: 10px;
    font-size: 0.845rem;
    font-weight: 600;
    border: none;
    cursor: pointer;
    text-decoration: none;
    transition: transform 0.18s ease, box-shadow 0.18s ease, background 0.18s ease;
    white-space: nowrap;
}

.filter-btn:hover {
    transform: translateY(-1px);
    text-decoration: none;
}

.filter-btn-apply {
    background: linear-gradient(135deg, #2d6a4f 0%, #52b788 100%);
    color: #ffffff;
    box-shadow: 0 2px 8px rgba(45, 106, 79, 0.25);
}

.filter-btn-apply:hover {
    background: linear-gradient(135deg, #1e4d38 0%, #40916c 100%);
    box-shadow: 0 4px 14px rgba(45, 106, 79, 0.35);
    color: #ffffff;
}

.filter-btn-export {
    background: #f0faf4;
    color: #2d6a4f;
    border: 1.5px solid #b7e4c7;
    box-shadow: 0 1px 4px rgba(45, 106, 79, 0.08);
}

.filter-btn-export:hover {
    background: #d8f3dc;
    box-shadow: 0 3px 10px rgba(45, 106, 79, 0.15);
    color: #1e4d38;
}

@media (max-width: 768px) {
    .analytics-filter-form {
        flex-direction: column;
        align-items: stretch;
    }

    .filter-divider {
        display: none;
    }

    .filter-actions {
        margin-left: 0;
    }

    .filter-btn {
        justify-content: center;
    }
}
</style>