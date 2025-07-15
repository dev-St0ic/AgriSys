@extends('layouts.app')

@section('title', 'Inventory Management - AgriSys')
@section('page-title', 'Inventory Management')

{{-- External CSS --}}
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/inventory/index.css') }}">
@endpush

{{-- External JavaScript --}}
@push('scripts')
    <script src="{{ asset('js/inventory/index.js') }}"></script>
@endpush

@section('content')
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-warehouse me-2 text-primary"></i>Inventory Management
            </h1>
            <p class="mb-0 text-muted">Manage your agricultural inventory and stock levels</p>
        </div>
        <a href="{{ route('admin.inventory.create') }}" class="btn btn-primary btn-lg shadow-sm">
            <i class="fas fa-plus me-2"></i>Add New Item
        </a>
    </div>

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
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-gradient-primary text-white py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1 text-white">
                                <i class="fas fa-list me-2"></i>Inventory Items
                            </h5>
                            <p class="mb-0 text-white-75">Manage and monitor your inventory</p>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-cog me-2"></i>Actions
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('admin.inventory.create') }}">
                                        <i class="fas fa-plus me-2"></i>Add New Item
                                    </a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="#" onclick="exportInventory()">
                                        <i class="fas fa-download me-2"></i>Export CSV
                                    </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <!-- Enhanced Search and Filter Form -->
                    <div class="filter-section mb-4">
                        <form method="GET" id="filterForm">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-search text-muted"></i>
                                        </span>
                                        <input type="text" name="search" class="form-control border-start-0"
                                            placeholder="🔍 Search inventory items..." value="{{ request('search') }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <select name="category" class="form-select">
                                        <option value="">📋 All Categories</option>
                                        <option value="vegetables"
                                            {{ request('category') == 'vegetables' ? 'selected' : '' }}>
                                            🥬 Vegetables
                                        </option>
                                        <option value="fruits" {{ request('category') == 'fruits' ? 'selected' : '' }}>
                                            🍎 Fruits
                                        </option>
                                        <option value="fertilizers"
                                            {{ request('category') == 'fertilizers' ? 'selected' : '' }}>
                                            🧪 Fertilizers
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="stock_status" class="form-select">
                                        <option value="">📊 All Stock Levels</option>
                                        <option value="available"
                                            {{ request('stock_status') == 'available' ? 'selected' : '' }}>
                                            ✅ Available
                                        </option>
                                        <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>
                                            ⚠️ Low Stock
                                        </option>
                                        <option value="out" {{ request('stock_status') == 'out' ? 'selected' : '' }}>
                                            ❌ Out of Stock
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <div class="btn-group w-100" role="group">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-filter me-1"></i>Filter
                                        </button>
                                        <a href="{{ route('admin.inventory.index') }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-refresh me-1"></i>Reset
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Enhanced Inventory Table -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th class="border-0">
                                        <i class="fas fa-tag me-2"></i>Item Details
                                    </th>
                                    <th class="border-0">
                                        <i class="fas fa-layer-group me-2"></i>Category
                                    </th>
                                    <th class="border-0">
                                        <i class="fas fa-seedling me-2"></i>Variety
                                    </th>
                                    <th class="border-0">
                                        <i class="fas fa-boxes me-2"></i>Stock Level
                                    </th>
                                    <th class="border-0">
                                        <i class="fas fa-chart-bar me-2"></i>Status
                                    </th>
                                    <th class="border-0">
                                        <i class="fas fa-calendar me-2"></i>Last Updated
                                    </th>
                                    <th class="border-0 text-center">
                                        <i class="fas fa-cogs me-2"></i>Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($inventories as $inventory)
                                    <tr class="inventory-row">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div
                                                    class="category-icon me-3 bg-{{ $inventory->category == 'vegetables' ? 'success' : ($inventory->category == 'fruits' ? 'warning' : 'info') }}">
                                                    <i
                                                        class="fas fa-{{ $inventory->category == 'vegetables' ? 'leaf' : ($inventory->category == 'fruits' ? 'apple-alt' : 'flask') }}"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark">{{ $inventory->item_name }}</div>
                                                    <small class="text-muted">{{ $inventory->unit }} per unit</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $inventory->category == 'vegetables' ? 'success' : ($inventory->category == 'fruits' ? 'warning' : 'info') }} fs-6">
                                                @if ($inventory->category == 'vegetables')
                                                    🥬 {{ ucfirst($inventory->category) }}
                                                @elseif($inventory->category == 'fruits')
                                                    🍎 {{ ucfirst($inventory->category) }}
                                                @else
                                                    🧪 {{ ucfirst($inventory->category) }}
                                                @endif
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ $inventory->variety ?? '-' }}</span>
                                        </td>
                                        <td>
                                            <div class="stock-info">
                                                <span
                                                    class="h5 mb-0 fw-bold
                                                    @if ($inventory->isOutOfStock()) text-danger
                                                    @elseif($inventory->isLowStock()) text-warning
                                                    @else text-success @endif">
                                                    {{ number_format($inventory->current_stock) }}
                                                </span>
                                                <small class="text-muted d-block">{{ $inventory->unit }}</small>

                                                <!-- Stock Progress Bar -->
                                                @php
                                                    $percentage =
                                                        $inventory->maximum_stock > 0
                                                            ? ($inventory->current_stock / $inventory->maximum_stock) *
                                                                100
                                                            : 0;
                                                @endphp
                                                <div class="progress mt-1" style="height: 4px;">
                                                    <div class="progress-bar bg-{{ $inventory->isOutOfStock() ? 'danger' : ($inventory->isLowStock() ? 'warning' : 'success') }}"
                                                        style="width: {{ min($percentage, 100) }}%"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($inventory->isOutOfStock())
                                                <span class="badge bg-danger fs-6 px-3 py-2">
                                                    <i class="fas fa-times-circle me-1"></i>Out of Stock
                                                </span>
                                            @elseif($inventory->isLowStock())
                                                <span class="badge bg-warning fs-6 px-3 py-2">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>Low Stock
                                                </span>
                                            @else
                                                <span class="badge bg-success fs-6 px-3 py-2">
                                                    <i class="fas fa-check-circle me-1"></i>Available
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="text-muted">
                                                <small>
                                                    <i class="fas fa-clock me-1"></i>
                                                    {{ $inventory->last_restocked ? $inventory->last_restocked->format('M d, Y') : 'Never' }}
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.inventory.show', $inventory) }}"
                                                    class="btn btn-outline-info btn-sm" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.inventory.edit', $inventory) }}"
                                                    class="btn btn-outline-primary btn-sm" title="Edit Item">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-outline-success btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#adjustStockModal{{ $inventory->id }}"
                                                    title="Adjust Stock">
                                                    <i class="fas fa-plus-minus"></i>
                                                </button>
                                            </div>

                                            <!-- Enhanced Stock Adjustment Modal -->
                                            <div class="modal fade" id="adjustStockModal{{ $inventory->id }}"
                                                tabindex="-1">
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
                                                                        <select name="adjustment_type" class="form-select"
                                                                            required>
                                                                            <option value="add">➕ Add Stock</option>
                                                                            <option value="subtract">➖ Subtract Stock
                                                                            </option>
                                                                            <option value="set">🔄 Set Exact Stock
                                                                            </option>
                                                                        </select>
                                                                    </div>

                                                                    <div class="col-12">
                                                                        <label class="form-label fw-bold">Quantity</label>
                                                                        <input type="number" name="quantity"
                                                                            class="form-control" min="0" required>
                                                                    </div>

                                                                    <div class="col-12">
                                                                        <label class="form-label fw-bold">Reason
                                                                            (Optional)</label>
                                                                        <input type="text" name="reason"
                                                                            class="form-control"
                                                                            placeholder="Reason for adjustment">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer bg-light">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">
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
                                        <td colspan="7" class="text-center py-5">
                                            <div class="empty-state">
                                                <div class="empty-icon mb-3">
                                                    <i class="fas fa-box-open fa-4x text-muted"></i>
                                                </div>
                                                <h5 class="text-muted mb-2">No Inventory Items Found</h5>
                                                <p class="text-muted mb-3">
                                                    @if (request()->has('search') || request()->has('category') || request()->has('stock_status'))
                                                        No items match your current filters. Try adjusting your search
                                                        criteria.
                                                    @else
                                                        You haven't added any inventory items yet.
                                                    @endif
                                                </p>
                                                <a href="{{ route('admin.inventory.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus me-2"></i>Add Your First Item
                                                </a>
                                            </div>
                                        </td>
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
@endsection
