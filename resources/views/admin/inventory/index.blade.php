@extends('layouts.app')

@section('title', 'Inventory Management - AgriSys')
@section('page-title')
    <div class="d-flex align-items-center">
        <i class="fas fa-warehouse me-2 text-primary"></i>
        <span class="text-primary fw-bold">Inventory Management</span>
    </div>
@endsection

{{-- External CSS --}}
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/inventory/index.css') }}">
@endpush

{{-- External JavaScript --}}
@push('scripts')
    <script src="{{ asset('js/inventory/index.js') }}"></script>
@endpush

@section('content')


    <!-- Enhanced Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-lg h-100 stats-card stats-primary">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Items</div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $stats['total_items'] }}</div>
                            <small class="text-muted">Active inventory items</small>
                        </div>
                        <div class="col-auto">
                            <div class="icon-circle bg-primary">
                                <i class="fas fa-seedling fa-2x text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-lg h-100 stats-card stats-warning">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Low Stock</div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $stats['low_stock_items'] }}</div>
                            <small class="text-muted">Items need restocking</small>
                        </div>
                        <div class="col-auto">
                            <div class="icon-circle bg-warning">
                                <i class="fas fa-exclamation-triangle fa-2x text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-lg h-100 stats-card stats-danger">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Out of Stock</div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $stats['out_of_stock_items'] }}</div>
                            <small class="text-muted">Items unavailable</small>
                        </div>
                        <div class="col-auto">
                            <div class="icon-circle bg-danger">
                                <i class="fas fa-times-circle fa-2x text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-lg h-100 stats-card stats-success">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Stock</div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_stock']) }}
                            </div>
                            <small class="text-muted">Total units in stock</small>
                        </div>
                        <div class="col-auto">
                            <div class="icon-circle bg-success">
                                <i class="fas fa-boxes fa-2x text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Filters and Actions -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter me-2"></i>Filters & Search
            </h6>
        </div>
        <div class="card-body p-3">
            <form method="GET" id="filterForm">
                <input type="hidden" name="date_from" value="{{ request('date_from') }}" id="dateFromHidden">
                <input type="hidden" name="date_to" value="{{ request('date_to') }}" id="dateToHidden">

                <div class="row">
                    <div class="col-md-2">
                        <select name="category" class="form-select form-select-sm" onchange="submitFilterForm()">
                            <option value="">All Categories</option>
                            <option value="vegetables" {{ request('category') == 'vegetables' ? 'selected' : '' }}>
                                Vegetables
                            </option>
                            <option value="fruits" {{ request('category') == 'fruits' ? 'selected' : '' }}>
                                Fruits
                            </option>
                            <option value="fertilizers" {{ request('category') == 'fertilizers' ? 'selected' : '' }}>
                                Fertilizers
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="stock_status" class="form-select form-select-sm" onchange="submitFilterForm()">
                            <option value="">All Stock Levels</option>
                            <option value="available" {{ request('stock_status') == 'available' ? 'selected' : '' }}>
                                Available
                            </option>
                            <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>
                                Low Stock
                            </option>
                            <option value="out" {{ request('stock_status') == 'out' ? 'selected' : '' }}>
                                Out of Stock
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control form-control-sm"
                                placeholder="Search inventory items..." value="{{ request('search') }}"
                                oninput="autoSearch()" id="searchInput">
                            <button class="btn btn-outline-secondary btn-sm" type="submit" title="Search"
                                id="searchButton">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-info btn-sm w-100" data-bs-toggle="modal"
                            data-bs-target="#dateFilterModal">
                            <i class="fas fa-calendar-alt me-1"></i>Date Filter
                        </button>
                    </div>
                    <div class="col-md-1">
                        <a href="{{ route('admin.inventory.index') }}" class="btn btn-secondary btn-sm w-100">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#addItemModal">
                            <i class="fas fa-plus"></i> Add Item
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Inventory Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-warehouse me-2"></i>Inventory Items
            </h6>

        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="inventoryTable">
                    <thead class="table-dark">
                        <tr>
                            <th>Date Added</th>
                            <th>Item Name</th>
                            <th>Category</th>
                            <th>Stock Level</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inventories as $inventory)
                            <tr>
                                <td>{{ $inventory->created_at->format('M d, Y g:i A') }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i
                                                class="fas fa-{{ $inventory->category == 'vegetables' ? 'leaf' : ($inventory->category == 'fruits' ? 'apple-alt' : 'flask') }} text-{{ $inventory->category == 'vegetables' ? 'success' : ($inventory->category == 'fruits' ? 'warning' : 'info') }}"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $inventory->item_name }}</div>
                                            <small class="text-muted">{{ $inventory->unit }} per unit</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span
                                        class="badge bg-{{ $inventory->category == 'vegetables' ? 'success' : ($inventory->category == 'fruits' ? 'warning' : 'info') }} fs-6">
                                        {{ ucfirst($inventory->category) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span
                                            class="fw-bold me-2">{{ $inventory->current_stock }}/{{ $inventory->maximum_stock }}</span>
                                        @php
                                            $percentage =
                                                $inventory->maximum_stock > 0
                                                    ? ($inventory->current_stock / $inventory->maximum_stock) * 100
                                                    : 0;
                                        @endphp
                                        <div class="progress flex-grow-1" style="height: 8px;">
                                            <div class="progress-bar bg-{{ $inventory->isOutOfStock() ? 'danger' : ($inventory->isLowStock() ? 'warning' : 'success') }}"
                                                style="width: {{ min($percentage, 100) }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if ($inventory->isOutOfStock())
                                        <span class="badge bg-danger fs-6">
                                            <i class="fas fa-times-circle me-1"></i>Out of Stock
                                        </span>
                                    @elseif($inventory->isLowStock())
                                        <span class="badge bg-warning fs-6">
                                            <i class="fas fa-exclamation-triangle me-1"></i>Low Stock
                                        </span>
                                    @else
                                        <span class="badge bg-success fs-6">
                                            <i class="fas fa-check-circle me-1"></i>Available
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button type="button" class="btn btn-sm btn-primary"
                                            onclick="viewInventoryItem(this)" title="View Details"
                                            data-id="{{ $inventory->id }}" data-item-name="{{ $inventory->item_name }}"
                                            data-category="{{ $inventory->category }}"
                                            data-variety="{{ $inventory->variety }}" data-unit="{{ $inventory->unit }}"
                                            data-description="{{ $inventory->description }}"
                                            data-current-stock="{{ $inventory->current_stock }}"
                                            data-minimum-stock="{{ $inventory->minimum_stock }}"
                                            data-maximum-stock="{{ $inventory->maximum_stock }}"
                                            data-is-active="{{ $inventory->is_active }}"
                                            data-last-restocked="{{ $inventory->last_restocked ? $inventory->last_restocked->format('M d, Y') : 'Never' }}"
                                            data-created-at="{{ $inventory->created_at->format('M d, Y h:i A') }}"
                                            data-updated-at="{{ $inventory->updated_at->format('M d, Y h:i A') }}"
                                            data-created-by="{{ $inventory->creator->name ?? 'System' }}"
                                            data-updated-by="{{ $inventory->updater->name ?? 'System' }}">
                                            <i class="fas fa-eye me-1"></i>View
                                        </button>
                                        <a href="#" class="btn btn-sm btn-warning"
                                            onclick="editInventoryItem(this)" title="Edit Item"
                                            data-id="{{ $inventory->id }}" data-item-name="{{ $inventory->item_name }}"
                                            data-category="{{ $inventory->category }}"
                                            data-variety="{{ $inventory->variety }}" data-unit="{{ $inventory->unit }}"
                                            data-description="{{ $inventory->description }}"
                                            data-current-stock="{{ $inventory->current_stock }}"
                                            data-minimum-stock="{{ $inventory->minimum_stock }}"
                                            data-maximum-stock="{{ $inventory->maximum_stock }}"
                                            data-is-active="{{ $inventory->is_active }}"
                                            data-last-restocked="{{ $inventory->last_restocked ? $inventory->last_restocked->format('Y-m-d') : '' }}">
                                            <i class="fas fa-edit me-1"></i>Edit
                                        </a>
                                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                            data-bs-target="#adjustStockModal{{ $inventory->id }}" title="Adjust Stock">
                                            <i class="fas fa-plus-minus me-1"></i>Stock
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Enhanced Stock Adjustment Modal -->
                            <div class="modal fade" id="adjustStockModal{{ $inventory->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-lg">
                                        <form method="POST"
                                            action="{{ route('admin.inventory.adjust-stock', $inventory) }}">
                                            @csrf
                                            <div class="modal-header bg-gradient-primary text-white">
                                                <h5 class="modal-title">
                                                    <i class="fas fa-plus-minus me-2"></i>
                                                    Adjust Stock - {{ $inventory->item_name }}
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <div class="alert alert-info border-0 mb-3">
                                                    <i class="fas fa-info-circle me-2"></i>
                                                    Current Stock: <strong>{{ $inventory->current_stock }}
                                                        {{ $inventory->unit }}</strong>
                                                </div>

                                                <div class="row g-3">
                                                    <div class="col-12">
                                                        <label class="form-label fw-bold">Adjustment
                                                            Type</label>
                                                        <select name="adjustment_type" class="form-select" required>
                                                            <option value="add">➕ Add Stock</option>
                                                            <option value="subtract">➖ Subtract Stock
                                                            </option>
                                                            <option value="set">🔄 Set Exact Stock
                                                            </option>
                                                        </select>
                                                    </div>

                                                    <div class="col-12">
                                                        <label class="form-label fw-bold">Quantity</label>
                                                        <input type="number" name="quantity" class="form-control"
                                                            min="0" required>
                                                    </div>

                                                    <div class="col-12">
                                                        <label class="form-label fw-bold">Reason
                                                            (Optional)
                                                        </label>
                                                        <input type="text" name="reason" class="form-control"
                                                            placeholder="Reason for adjustment">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    <i class="fas fa-times me-2"></i>Cancel
                                                </button>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-save me-2"></i>Adjust Stock
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="empty-state">
                                        <div class="empty-icon mb-3">
                                            <i class="fas fa-warehouse fa-3x text-gray-300"></i>
                                        </div>
                                        <h5 class="text-muted mb-2">No Inventory Items Found</h5>
                                        <p class="text-muted mb-3">
                                            @if (request()->has('search') || request()->has('category') || request()->has('stock_status'))
                                                No items match your current filters. Try adjusting your search criteria.
                                            @else
                                                You haven't added any inventory items yet.
                                            @endif
                                        </p>
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#addItemModal">
                                            <i class="fas fa-plus me-2"></i>Add Your First Item
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Modern Pagination -->
            @if ($inventories->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    <nav aria-label="Pagination">
                        <ul class="pagination pagination-modern mb-0">
                            {{-- Previous Page Link --}}
                            @if ($inventories->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link">Back</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $inventories->previousPageUrl() }}"
                                        rel="prev">Back</a>
                                </li>
                            @endif

                            {{-- Pagination Elements --}}
                            @php
                                $start = max(1, $inventories->currentPage() - 2);
                                $end = min($inventories->lastPage(), $inventories->currentPage() + 2);
                            @endphp

                            @for ($page = $start; $page <= $end; $page++)
                                @if ($page == $inventories->currentPage())
                                    <li class="page-item active">
                                        <span class="page-link">{{ $page }}</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link"
                                            href="{{ $inventories->url($page) }}">{{ $page }}</a>
                                    </li>
                                @endif
                            @endfor

                            {{-- Next Page Link --}}
                            @if ($inventories->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $inventories->nextPageUrl() }}"
                                        rel="next">Next</a>
                                </li>
                            @else
                                <li class="page-item disabled">
                                    <span class="page-link">Next</span>
                                </li>
                            @endif
                        </ul>
                    </nav>
                </div>

                <!-- Page Info -->
                <div class="d-flex justify-content-center mt-2">
                    <small class="text-muted">
                        {{ $inventories->firstItem() }} - {{ $inventories->lastItem() }} of
                        {{ $inventories->total() }} items
                    </small>
                </div>
            @endif
        </div>
    </div>
    </div>
    </div>

    <!-- Add Item Modal -->
    <div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.inventory.store') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addItemModalLabel">
                            <i class="fas fa-plus me-2"></i>Add New Inventory Item
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="itemName" class="form-label">Item Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="itemName" name="item_name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category" class="form-label">Category <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="category" name="category" required>
                                        <option value="">Select Category</option>
                                        <option value="vegetables">Vegetables</option>
                                        <option value="fruits">Fruits</option>
                                        <option value="fertilizers">Fertilizers</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="currentStock" class="form-label">Current Stock <span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="currentStock" name="current_stock"
                                        min="0" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="maximumStock" class="form-label">Maximum Stock <span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="maximumStock" name="maximum_stock"
                                        min="1" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="unit" class="form-label">Unit <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="unit" name="unit" required>
                                        <option value="">Select Unit</option>
                                        <option value="pieces">Pieces</option>
                                        <option value="kg">Kilograms</option>
                                        <option value="lbs">Pounds</option>
                                        <option value="bags">Bags</option>
                                        <option value="boxes">Boxes</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="variety" class="form-label">Variety (Optional)</label>
                                    <input type="text" class="form-control" id="variety" name="variety">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="lowStockThreshold" class="form-label">Low Stock Threshold</label>
                                    <input type="number" class="form-control" id="lowStockThreshold"
                                        name="low_stock_threshold" min="0" value="10">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description (Optional)</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Add Item
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Date Filter Modal -->
    <div class="modal fade" id="dateFilterModal" tabindex="-1" aria-labelledby="dateFilterModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="dateFilterModalLabel">
                        <i class="fas fa-calendar-alt me-2"></i>Filter by Date Range
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="dateFrom" class="form-label">From Date</label>
                            <input type="date" class="form-control" id="dateFrom"
                                value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-6">
                            <label for="dateTo" class="form-label">To Date</label>
                            <input type="date" class="form-control" id="dateTo" value="{{ request('date_to') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Quick Select:</label>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="setDateRange(0)">
                                Today
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="setDateRange(7)">
                                Last 7 Days
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="setDateRange(30)">
                                Last 30 Days
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="clearDates()">
                        <i class="fas fa-times me-1"></i>Clear Filter
                    </button>
                    <button type="button" class="btn btn-primary" onclick="applyDateFilter()">
                        <i class="fas fa-filter me-1"></i>Apply Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Date filtering functions
        function submitFilterForm() {
            document.getElementById('filterForm').submit();
        }

        function autoSearch() {
            clearTimeout(window.searchTimeout);
            window.searchTimeout = setTimeout(function() {
                document.getElementById('filterForm').submit();
            }, 500);
        }

        function applyDateFilter() {
            const dateFrom = document.getElementById('dateFrom').value;
            const dateTo = document.getElementById('dateTo').value;

            if (dateFrom) {
                document.getElementById('dateFromHidden').value = dateFrom;
            }
            if (dateTo) {
                document.getElementById('dateToHidden').value = dateTo;
            }

            // Close modal and submit form
            const modal = bootstrap.Modal.getInstance(document.getElementById('dateFilterModal'));
            modal.hide();

            submitFilterForm();
        }

        function setDateRange(days) {
            const today = new Date();
            const fromDate = new Date();

            if (days === 0) {
                // Today
                fromDate.setDate(today.getDate());
            } else if (days === 7) {
                // This week
                fromDate.setDate(today.getDate() - 7);
            } else if (days === 30) {
                // This month
                fromDate.setDate(today.getDate() - 30);
            }

            document.getElementById('dateFrom').value = fromDate.toISOString().split('T')[0];
            document.getElementById('dateTo').value = today.toISOString().split('T')[0];
        }

        function clearDates() {
            document.getElementById('dateFrom').value = '';
            document.getElementById('dateTo').value = '';
            document.getElementById('dateFromHidden').value = '';
            document.getElementById('dateToHidden').value = '';

            // Close modal and submit form
            const modal = bootstrap.Modal.getInstance(document.getElementById('dateFilterModal'));
            modal.hide();

            submitFilterForm();
        }

        // View inventory item function
        function viewInventoryItem(button) {
            // Get data from button attributes
            const data = {
                id: button.dataset.id,
                item_name: button.dataset.itemName,
                category: button.dataset.category,
                variety: button.dataset.variety || 'Not specified',
                unit: button.dataset.unit,
                description: button.dataset.description,
                current_stock: parseInt(button.dataset.currentStock),
                minimum_stock: parseInt(button.dataset.minimumStock),
                maximum_stock: parseInt(button.dataset.maximumStock),
                is_active: button.dataset.isActive === '1',
                last_restocked: button.dataset.lastRestocked,
                created_at: button.dataset.createdAt,
                updated_at: button.dataset.updatedAt,
                created_by: button.dataset.createdBy,
                updated_by: button.dataset.updatedBy
            };

            // Populate modal with data
            document.getElementById('modalItemName').textContent = data.item_name;
            document.getElementById('modalBasicItemName').textContent = data.item_name;
            document.getElementById('modalCategory').textContent = data.category.charAt(0).toUpperCase() + data.category
                .slice(1);
            document.getElementById('modalVariety').textContent = data.variety;
            document.getElementById('modalUnit').textContent = data.unit.charAt(0).toUpperCase() + data.unit.slice(1);

            // Status badge
            const statusBadge = document.getElementById('modalStatus');
            if (data.is_active) {
                statusBadge.textContent = 'Active';
                statusBadge.className = 'badge badge-success';
            } else {
                statusBadge.textContent = 'Inactive';
                statusBadge.className = 'badge badge-secondary';
            }

            // Description
            if (data.description && data.description.trim() !== '') {
                document.getElementById('modalDescription').textContent = data.description;
                document.getElementById('modalDescriptionRow').style.display = 'block';
            } else {
                document.getElementById('modalDescriptionRow').style.display = 'none';
            }

            // Stock information
            const currentStockEl = document.getElementById('modalCurrentStock');
            const stockIcon = document.getElementById('modalStockIcon');
            const isOutOfStock = data.current_stock <= 0;
            const isLowStock = data.current_stock <= data.minimum_stock && !isOutOfStock;

            currentStockEl.textContent = `${data.current_stock.toLocaleString()} ${data.unit}`;

            if (isOutOfStock) {
                currentStockEl.className = 'h5 text-danger';
                stockIcon.className = 'fas fa-times-circle text-danger ms-1';
                stockIcon.style.display = 'inline';
                stockIcon.title = 'Out of Stock';
            } else if (isLowStock) {
                currentStockEl.className = 'h5 text-warning';
                stockIcon.className = 'fas fa-exclamation-triangle text-warning ms-1';
                stockIcon.style.display = 'inline';
                stockIcon.title = 'Low Stock';
            } else {
                currentStockEl.className = 'h5 text-success';
                stockIcon.style.display = 'none';
            }

            document.getElementById('modalMinStock').textContent = `${data.minimum_stock.toLocaleString()} ${data.unit}`;
            document.getElementById('modalMaxStock').textContent = `${data.maximum_stock.toLocaleString()} ${data.unit}`;

            // Stock status badge
            const stockStatusBadge = document.getElementById('modalStockStatus');
            if (isOutOfStock) {
                stockStatusBadge.textContent = 'Out of Stock';
                stockStatusBadge.className = 'badge badge-danger';
            } else if (isLowStock) {
                stockStatusBadge.textContent = 'Low Stock';
                stockStatusBadge.className = 'badge badge-warning';
            } else {
                stockStatusBadge.textContent = 'Available';
                stockStatusBadge.className = 'badge badge-success';
            }

            document.getElementById('modalLastRestocked').textContent = data.last_restocked;

            // Progress bar
            const percentage = data.maximum_stock > 0 ? Math.min(100, Math.round((data.current_stock / data.maximum_stock) *
                100)) : 0;
            const minPercentage = data.maximum_stock > 0 ? Math.round((data.minimum_stock / data.maximum_stock) * 100) : 0;

            const progressBar = document.getElementById('modalProgressBar');
            let progressClass = 'progress-bar';
            if (isOutOfStock) {
                progressClass += ' bg-danger';
            } else if (isLowStock) {
                progressClass += ' bg-warning';
            } else {
                progressClass += ' bg-success';
            }

            progressBar.className = progressClass;
            progressBar.style.width = percentage + '%';

            document.getElementById('modalStockPercentage').textContent = percentage + '%';
            document.getElementById('modalProgressText').textContent =
                `${data.current_stock.toLocaleString()} / ${data.maximum_stock.toLocaleString()}`;
            document.getElementById('modalMinThreshold').textContent = data.minimum_stock.toLocaleString();
            document.getElementById('modalMaxThreshold').textContent = data.maximum_stock.toLocaleString();

            // Tracking information
            document.getElementById('modalCreatedAt').textContent = data.created_at;
            document.getElementById('modalUpdatedAt').textContent = data.updated_at;
            document.getElementById('modalCreatedBy').textContent = data.created_by;
            document.getElementById('modalUpdatedBy').textContent = data.updated_by;

            // Edit button - store inventory ID
            document.getElementById('modalEditButton').dataset.inventoryId = data.id;

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('viewInventoryModal'));
            modal.show();
        }

        // Function to edit from view modal
        function editFromViewModal() {
            // Get the current inventory ID from the edit button
            const inventoryId = document.getElementById('modalEditButton').dataset.inventoryId;

            // Find the corresponding view button to get all data
            const viewButton = document.querySelector(`button[data-id="${inventoryId}"]`);

            if (viewButton) {
                // Use the existing edit function
                editInventoryItem(viewButton);
            }
        }

        // Edit inventory item function
        function editInventoryItem(button) {
            // Get data from button attributes
            const data = {
                id: button.dataset.id,
                item_name: button.dataset.itemName,
                category: button.dataset.category,
                variety: button.dataset.variety || '',
                unit: button.dataset.unit,
                description: button.dataset.description || '',
                current_stock: button.dataset.currentStock,
                minimum_stock: button.dataset.minimumStock,
                maximum_stock: button.dataset.maximumStock,
                is_active: button.dataset.isActive === '1',
                last_restocked: button.dataset.lastRestocked || ''
            };

            // Populate form fields
            document.getElementById('edit_item_name').value = data.item_name;
            document.getElementById('edit_category').value = data.category;
            document.getElementById('edit_variety').value = data.variety;
            document.getElementById('edit_unit').value = data.unit;
            document.getElementById('edit_description').value = data.description;
            document.getElementById('edit_current_stock').value = data.current_stock;
            document.getElementById('edit_minimum_stock').value = data.minimum_stock;
            document.getElementById('edit_maximum_stock').value = data.maximum_stock;
            document.getElementById('edit_is_active').checked = data.is_active;
            document.getElementById('edit_last_restocked').value = data.last_restocked;

            // Set form action
            document.getElementById('editInventoryForm').action = `/admin/inventory/${data.id}`;

            // Update modal title
            document.getElementById('editInventoryModalLabel').innerHTML = `
                <i class="fas fa-edit me-2"></i>Edit Inventory Item - ${data.item_name}
            `;

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('editInventoryModal'));
            modal.show();
        }

        function getStockPercentage(inventory) {
            if (inventory.maximum_stock <= 0) return 0;
            return Math.min(100, Math.round((inventory.current_stock / inventory.maximum_stock) * 100));
        }
    </script>

    <!-- View Inventory Modal -->
    <div class="modal fade" id="viewInventoryModal" tabindex="-1" aria-labelledby="viewInventoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewInventoryModalLabel">
                        <i class="fas fa-eye me-2"></i><span id="modalItemName">Inventory Item Details</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Basic Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-4"><strong>Item Name:</strong></div>
                                        <div class="col-sm-8" id="modalBasicItemName">-</div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-4"><strong>Category:</strong></div>
                                        <div class="col-sm-8">
                                            <span class="badge badge-info" id="modalCategory">-</span>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-4"><strong>Variety:</strong></div>
                                        <div class="col-sm-8" id="modalVariety">-</div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-4"><strong>Unit:</strong></div>
                                        <div class="col-sm-8" id="modalUnit">-</div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-4"><strong>Status:</strong></div>
                                        <div class="col-sm-8">
                                            <span class="badge" id="modalStatus">-</span>
                                        </div>
                                    </div>
                                    <div class="row" id="modalDescriptionRow" style="display: none;">
                                        <hr>
                                        <div class="col-sm-4"><strong>Description:</strong></div>
                                        <div class="col-sm-8" id="modalDescription">-</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Stock Information -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-boxes me-2"></i>Stock Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-5"><strong>Current Stock:</strong></div>
                                        <div class="col-sm-7">
                                            <span class="h5" id="modalCurrentStock">-</span>
                                            <i class="fas" id="modalStockIcon" style="display: none;"></i>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-5"><strong>Minimum Stock:</strong></div>
                                        <div class="col-sm-7" id="modalMinStock">-</div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-5"><strong>Maximum Stock:</strong></div>
                                        <div class="col-sm-7" id="modalMaxStock">-</div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-5"><strong>Stock Status:</strong></div>
                                        <div class="col-sm-7">
                                            <span class="badge" id="modalStockStatus">-</span>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-5"><strong>Last Restocked:</strong></div>
                                        <div class="col-sm-7" id="modalLastRestocked">-</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stock Progress Bar -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Stock Level Visualization</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>Stock Level</span>
                                            <span id="modalStockPercentage">0%</span>
                                        </div>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar" id="modalProgressBar" role="progressbar"
                                                style="width: 0%"></div>
                                            <div class="position-absolute w-100 text-center">
                                                <small id="modalProgressText">0 / 0</small>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between mt-1">
                                            <small class="text-muted">0</small>
                                            <small class="text-muted">Min: <span id="modalMinThreshold">0</span></small>
                                            <small class="text-muted">Max: <span id="modalMaxThreshold">0</span></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tracking Information -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-clock me-2"></i>Tracking Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-sm-4"><strong>Created:</strong></div>
                                                <div class="col-sm-8" id="modalCreatedAt">-</div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-sm-4"><strong>Created By:</strong></div>
                                                <div class="col-sm-8" id="modalCreatedBy">-</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-sm-4"><strong>Updated:</strong></div>
                                                <div class="col-sm-8" id="modalUpdatedAt">-</div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-sm-4"><strong>Updated By:</strong></div>
                                                <div class="col-sm-8" id="modalUpdatedBy">-</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm me-2" id="modalEditButton"
                        onclick="editFromViewModal()" data-bs-dismiss="modal">
                        <i class="fas fa-edit"></i> Edit Item
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Inventory Modal -->
    <div class="modal fade" id="editInventoryModal" tabindex="-1" aria-labelledby="editInventoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editInventoryModalLabel">
                        <i class="fas fa-edit me-2"></i>Edit Inventory Item
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" id="editInventoryForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <!-- Basic Information Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="fas fa-info-circle me-2"></i>Basic Information
                                </h6>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="edit_item_name" name="item_name"
                                        placeholder="Item Name" required>
                                    <label for="edit_item_name">
                                        <i class="fas fa-tag me-1"></i>Item Name <span class="text-danger">*</span>
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <select class="form-select" id="edit_category" name="category" required>
                                        <option value="">Select Category</option>
                                        <option value="vegetables">🌱 Vegetables</option>
                                        <option value="fruits">🍎 Fruits</option>
                                        <option value="fertilizers">🌿 Fertilizers</option>
                                    </select>
                                    <label for="edit_category">
                                        <i class="fas fa-layer-group me-1"></i>Category <span class="text-danger">*</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="edit_variety" name="variety"
                                        placeholder="Variety">
                                    <label for="edit_variety">
                                        <i class="fas fa-seedling me-1"></i>Variety
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <select class="form-select" id="edit_unit" name="unit" required>
                                        <option value="">Select Unit</option>
                                        <option value="pieces">Pieces</option>
                                        <option value="kg">Kilograms (kg)</option>
                                        <option value="grams">Grams</option>
                                        <option value="sacks">Sacks</option>
                                        <option value="liters">Liters</option>
                                        <option value="bottles">Bottles</option>
                                        <option value="packs">Packs</option>
                                    </select>
                                    <label for="edit_unit">
                                        <i class="fas fa-balance-scale me-1"></i>Unit <span class="text-danger">*</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <textarea class="form-control" id="edit_description" name="description" style="height: 100px"
                                        placeholder="Description"></textarea>
                                    <label for="edit_description">
                                        <i class="fas fa-align-left me-1"></i>Description
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Stock Management Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="fas fa-boxes me-2"></i>Stock Management
                                </h6>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card border-success h-100">
                                    <div class="card-body text-center">
                                        <div class="form-floating mb-3">
                                            <input type="number" class="form-control" id="edit_current_stock"
                                                name="current_stock" min="0" required placeholder="Current Stock">
                                            <label for="edit_current_stock">
                                                <i class="fas fa-warehouse me-1"></i>Current Stock <span
                                                    class="text-danger">*</span>
                                            </label>
                                        </div>
                                        <div class="text-muted small">
                                            <i class="fas fa-info-circle me-1"></i>Available quantity
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card border-warning h-100">
                                    <div class="card-body text-center">
                                        <div class="form-floating mb-3">
                                            <input type="number" class="form-control" id="edit_minimum_stock"
                                                name="minimum_stock" min="0" required placeholder="Minimum Stock">
                                            <label for="edit_minimum_stock">
                                                <i class="fas fa-exclamation-triangle me-1"></i>Minimum Stock <span
                                                    class="text-danger">*</span>
                                            </label>
                                        </div>
                                        <div class="text-muted small">
                                            <i class="fas fa-bell me-1"></i>Low stock alert threshold
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card border-primary h-100">
                                    <div class="card-body text-center">
                                        <div class="form-floating mb-3">
                                            <input type="number" class="form-control" id="edit_maximum_stock"
                                                name="maximum_stock" min="1" required placeholder="Maximum Stock">
                                            <label for="edit_maximum_stock">
                                                <i class="fas fa-chart-line me-1"></i>Maximum Stock <span
                                                    class="text-danger">*</span>
                                            </label>
                                        </div>
                                        <div class="text-muted small">
                                            <i class="fas fa-arrow-up me-1"></i>Maximum capacity
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="fas fa-cog me-2"></i>Additional Information
                                </h6>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="date" class="form-control" id="edit_last_restocked"
                                        name="last_restocked" placeholder="Last Restocked">
                                    <label for="edit_last_restocked">
                                        <i class="fas fa-calendar me-1"></i>Last Restocked
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6 d-flex align-items-center">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active"
                                        value="1">
                                    <label class="form-check-label fw-bold" for="edit_is_active">
                                        <i class="fas fa-toggle-on me-2 text-success"></i>Active Item
                                    </label>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>Uncheck to deactivate this item
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update Inventory Item
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
