{{-- resources/views/admin/supply-management/index.blade.php --}}

@extends('layouts.app')

@section('title', 'Supply Management')

@section('page-title')
    <i class="fas fa-boxes text-primary me-2"></i><span class="text-primary">Supply Management</span>
@endsection

@section('content')
    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Supply Statistics Cards -->
        <div class="row mb-4 g-3">
            <!-- Total Items -->
            <div class="col-lg-3 col-md-6">
                <div class="card metric-card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-boxes text-primary mb-3" style="font-size: 2.5rem;"></i>
                        <h2 class="metric-value mb-2">{{ number_format($totalItems) }}</h2>
                        <p class="metric-label text-primary mb-0">TOTAL ITEMS</p>
                    </div>
                </div>
            </div>

            <!-- Total Supply -->
            <div class="col-lg-3 col-md-6">
                <div class="card metric-card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-warehouse text-success mb-3" style="font-size: 2.5rem;"></i>
                        <h2 class="metric-value mb-2">{{ number_format($totalSupply) }}</h2>
                        <p class="metric-label text-success mb-0">TOTAL SUPPLY</p>
                    </div>
                </div>
            </div>

            <!-- Low Supply -->
            <div class="col-lg-3 col-md-6">
                <div class="card metric-card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-exclamation-triangle text-warning mb-3" style="font-size: 2.5rem;"></i>
                        <h2 class="metric-value mb-2">{{ number_format($lowSupplyItems) }}</h2>
                        <p class="metric-label text-warning mb-0">LOW SUPPLY</p>
                    </div>
                </div>
            </div>

            <!-- Out of Supply -->
            <div class="col-lg-3 col-md-6">
                <div class="card metric-card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-times-circle text-danger mb-3" style="font-size: 2.5rem;"></i>
                        <h2 class="metric-value mb-2">{{ number_format($outOfSupplyItems) }}</h2>
                        <p class="metric-label text-danger mb-0">OUT OF SUPPLY</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Navigation Tabs - SEPARATED -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <!-- First row: Category tabs -->
                    <div class="category-tabs-nav d-flex flex-wrap gap-2 align-items-center" id="categoryTabsNav">
                        <button class="category-tab-btn active" data-category="all" onclick="switchCategory('all', event)">
                            <i class="fas fa-th-large"></i> All Categories
                        </button>
                        @foreach ($categories as $cat)
                            <button class="category-tab-btn category-btn" data-category="{{ $cat->id }}"
                                onclick="switchCategory('{{ $cat->id }}', event)"
                                style="display: {{ $loop->index < 6 ? '' : 'none' }};">
                                <i class="fas {{ $cat->icon ?? 'fa-leaf' }}"></i> {{ $cat->display_name }}
                            </button>
                        @endforeach
                        @if ($categories->count() > 6)
                            <button class="category-tab-btn" id="showMoreBtn" onclick="toggleShowMore()">
                                <i class="fas fa-chevron-down"></i> Show More
                            </button>
                            <button class="category-tab-btn" id="showLessBtn" onclick="toggleShowLess()" style="display: none;">
                                <i class="fas fa-chevron-up"></i> Show Less
                            </button>
                        @endif
                    </div>

                    <!-- Second row: Add Category button (right aligned) -->
                    <div style="display: flex; justify-content: flex-end;">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCategoryModal" style="white-space: nowrap;">
                            <i class="fas fa-plus me-2"></i>Add Category
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters & Search Card - UPDATED -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-filter me-2"></i>Filters & Search
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <!-- Status Filter -->
                    <div class="col-md-2">
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <!-- Stock Status Filter -->
                    <div class="col-md-2">
                        <select name="stock_status" class="form-select form-select-sm">
                            <option value="">All Stock Status</option>
                            <option value="in_stock">In Stock</option>
                            <option value="low_supply">Low Supply</option>
                            <option value="out_of_stock">Out of Stock</option>
                        </select>
                    </div>

                    <!-- Search -->
                    <div class="col-md-5">
                        <div class="input-group input-group-sm">
                            <input type="text" id="searchInput" class="form-control"
                                placeholder="Search item name, description..." value="">
                            <button class="btn btn-outline-secondary" type="button" title="Search">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Clear Button -->
                    <div class="col-md-3">
                        <button onclick="clearFilters()" class="btn btn-secondary btn-sm w-100">
                            <i></i>Clear
                        </button>
                    </div>
                </div>
            </div>
        </div>


        <!-- All Categories View -->
        <div class="category-content active" id="category-all">
            <div class="row">
                @foreach ($categories as $category)
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-0">
                                        <i class="fas {{ $category->icon ?? 'fa-leaf' }} me-2"></i>
                                        <span class="category-name" title="{{ $category->display_name }}">
                                            {{ $category->display_name }}
                                        </span>
                                        @if (!$category->is_active)
                                            <span class="badge bg-warning">Inactive</span>
                                        @endif
                                        <span class="badge bg-secondary ms-1">{{ $category->items->count() }}</span>
                                        @php
                                            $lowSupplyCount = $category->items
                                                ->filter(function ($item) {
                                                    return $item->needsReorder();
                                                })
                                                ->count();
                                        @endphp
                                        @if ($lowSupplyCount > 0)
                                            <span class="badge bg-danger ms-1" title="Items need attention">
                                                <i class="fas fa-exclamation-triangle"></i> {{ $lowSupplyCount }}
                                            </span>
                                        @endif
                                    </h5>
                                    <small class="text-muted">{{ $category->description }}</small>
                                </div>
                                <div class="d-flex gap-2 align-items-center">
                                    <button class="btn btn-sm btn-success" data-bs-toggle="modal"
                                        data-bs-target="#createItemModal" onclick="setItemCategory({{ $category->id }})"
                                        title="Add new item to this category">
                                        <i class="fas fa-plus me-1"></i><span class="d-none d-sm-inline">Add Item</span>
                                    </button>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                            data-bs-toggle="dropdown" aria-expanded="false" title="More actions">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="#"
                                                    onclick="event.preventDefault(); editCategory({{ $category->id }})">
                                                    <i class="fas fa-edit text-primary me-2"></i>Edit Category
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="#"
                                                    onclick="event.preventDefault(); toggleCategory({{ $category->id }})">
                                                    <i
                                                        class="fas fa-{{ $category->is_active ? 'eye-slash' : 'eye' }} text-{{ $category->is_active ? 'warning' : 'success' }} me-2"></i>
                                                    {{ $category->is_active ? 'Deactivate' : 'Activate' }}
                                                </a>
                                            </li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <a class="dropdown-item text-danger" href="#"
                                                    onclick="event.preventDefault(); deleteCategory({{ $category->id }})">
                                                    <i class="fas fa-trash me-2"></i>Delete Category
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                @if ($category->items->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Image</th>
                                                    <th>Name</th>
                                                    <th>Unit</th>
                                                    <th>Supply</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($category->items->sortBy('name') as $item)
                                                    <tr class="item-row">
                                                        <td style="width: 70px;">
                                                            @if ($item->image_path)
                                                                <img src="{{ Storage::url($item->image_path) }}"
                                                                    alt="{{ $item->name }}" class="rounded"
                                                                    style="width: 50px; height: 50px; object-fit: cover;">
                                                            @else
                                                                <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                                    style="width: 50px; height: 50px;">
                                                                    <i class="fas fa-image text-muted"></i>
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td style="max-width: 200px; width: 200px;">
                                                            <strong class="item-name" title="{{ $item->name }}">{{ $item->name }}</strong>
                                                            @if ($item->needsReorder())
                                                                <br><small class="text-warning"><i class="fas fa-exclamation-triangle"></i> Needs Reorder</small>
                                                            @endif
                                                            <small class="text-muted description-truncate" title="{{ $item->description }}">
                                                                {{ $item->description ? Str::limit($item->description, 50) : 'N/A' }}
                                                            </small>
                                                        </td>
                                                        <td><span class="badge bg-secondary">{{ $item->unit }}</span>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-{{ $item->supply_status_color }}">
                                                                {{ $item->current_supply }}
                                                            </span>
                                                            @if ($item->reorder_point)
                                                                <br><small class="text-muted">Min:
                                                                    {{ $item->reorder_point }}</small>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <span
                                                                class="badge bg-{{ $item->is_active ? 'success' : 'danger' }}">
                                                                {{ $item->is_active ? 'Active' : 'Inactive' }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex gap-1 justify-content-center flex-wrap">
                                                                <button class="btn btn-sm btn-success position-relative"
                                                                    onclick="manageSupply({{ $item->id }})"
                                                                    title="Manage Supply" data-bs-toggle="tooltip"
                                                                    data-bs-placement="top">
                                                                    <i class="fas fa-warehouse"></i>
                                                                    @if ($item->needsReorder())
                                                                        <span
                                                                            class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                                                                            <span class="visually-hidden">Needs
                                                                                reorder</span>
                                                                        </span>
                                                                    @endif
                                                                </button>
                                                                <button class="btn btn-sm btn-outline-primary"
                                                                    onclick="editItem({{ $item->id }})"
                                                                    title="Edit Item" data-bs-toggle="tooltip"
                                                                    data-bs-placement="top">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                                <div class="btn-group btn-group-sm">
                                                                    <button type="button"
                                                                        class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split"
                                                                        data-bs-toggle="dropdown" aria-expanded="false"
                                                                        title="More actions">
                                                                        <span class="visually-hidden">Toggle
                                                                            Dropdown</span>
                                                                    </button>
                                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                                        <li>
                                                                            <a class="dropdown-item" href="#"
                                                                                onclick="event.preventDefault(); toggleItem({{ $item->id }})">
                                                                                <i
                                                                                    class="fas fa-{{ $item->is_active ? 'eye-slash' : 'eye' }} text-{{ $item->is_active ? 'warning' : 'success' }} me-2"></i>
                                                                                {{ $item->is_active ? 'Deactivate' : 'Activate' }}
                                                                            </a>
                                                                        </li>
                                                                        <li>
                                                                            <hr class="dropdown-divider">
                                                                        </li>
                                                                        <li>
                                                                            <a class="dropdown-item text-danger"
                                                                                href="#"
                                                                                onclick="event.preventDefault(); deleteItem({{ $item->id }})">
                                                                                <i class="fas fa-trash me-2"></i>Delete
                                                                                Item
                                                                            </a>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-muted text-center py-3">No items in this category yet.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Individual Category Views (Full Table) -->
        @foreach ($categories as $category)
            <div class="category-content" id="category-{{ $category->id }}">
                <div class="card shadow-sm">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">
                                <i class="fas {{ $category->icon ?? 'fa-leaf' }} me-2"></i>
                                <span class="category-name" title="{{ $category->display_name }}">
                                    {{ $category->display_name }}
                                </span>
                                @if (!$category->is_active)
                                    <span class="badge bg-warning">Inactive</span>
                                @endif
                                <span class="badge bg-secondary ms-1">{{ $category->items->count() }}</span>
                                @php
                                    $lowSupplyCount = $category->items
                                        ->filter(function ($item) {
                                            return $item->needsReorder();
                                        })
                                        ->count();
                                @endphp
                                @if ($lowSupplyCount > 0)
                                    <span class="badge bg-danger ms-1" title="Items need attention">
                                        <i class="fas fa-exclamation-triangle"></i> {{ $lowSupplyCount }}
                                    </span>
                                @endif
                            </h4>
                            <p class="text-muted mb-0"><small>{{ $category->description }}</small></p>
                        </div>
                        <div class="d-flex gap-2 align-items-center flex-wrap">
                            <button class="btn btn-sm btn-success" data-bs-toggle="modal"
                                data-bs-target="#createItemModal" onclick="setItemCategory({{ $category->id }})"
                                title="Add new item to this category">
                                <i class="fas fa-plus me-1"></i><span class="d-none d-md-inline">Add Item</span>
                            </button>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                    data-bs-toggle="dropdown" aria-expanded="false" title="More actions">
                                    <i class="fas fa-ellipsis-v me-1"></i><span class="d-none d-md-inline">More</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="#"
                                            onclick="event.preventDefault(); editCategory({{ $category->id }})">
                                            <i class="fas fa-edit text-primary me-2"></i>Edit Category
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#"
                                            onclick="event.preventDefault(); toggleCategory({{ $category->id }})">
                                            <i
                                                class="fas fa-{{ $category->is_active ? 'eye-slash' : 'eye' }} text-{{ $category->is_active ? 'warning' : 'success' }} me-2"></i>
                                            {{ $category->is_active ? 'Deactivate' : 'Activate' }} Category
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <a class="dropdown-item text-danger" href="#"
                                            onclick="event.preventDefault(); deleteCategory({{ $category->id }})">
                                            <i class="fas fa-trash me-2"></i>Delete Category
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if ($category->items->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 80px;">Image</th>
                                            <th>Item Name</th>
                                            <th style="width: 150px;">Description</th>
                                            <th style="width: 100px;" class="text-center">Unit</th>
                                            <th style="width: 120px;" class="text-center">Current Supply</th>
                                            <th style="width: 100px;" class="text-center">Min/Max</th>
                                            <th style="width: 100px;" class="text-center">Status</th>
                                            <th style="width: 200px;" class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($category->items->sortBy('name') as $item)
                                            <tr class="item-row">
                                                <td>
                                                    @if ($item->image_path)
                                                        <img src="{{ Storage::url($item->image_path) }}"
                                                            alt="{{ $item->name }}" class="rounded shadow-sm"
                                                            style="width: 60px; height: 60px; object-fit: cover;">
                                                    @else
                                                        <div class="bg-light rounded d-flex align-items-center justify-content-center shadow-sm"
                                                            style="width: 60px; height: 60px;">
                                                            <i class="fas fa-image text-muted fa-2x"></i>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td style="max-width: 200px; width: 200px;">
                                                    <strong class="item-name" title="{{ $item->name }}">{{ $item->name }}</strong>
                                                   @if ($item->needsReorder())
                                                        <br><small class="text-warning"><i class="fas fa-exclamation-triangle"></i> Needs Reorder</small>
                                                    @endif
                                                    <small class="text-muted description-truncate" title="{{ $item->description }}">
                                                        {{ $item->description ? Str::limit($item->description, 50) : 'N/A' }}
                                                    </small>
                                                </td>
                                                <td style="max-width: 150px; width: 150px;">
                                                    <small class="text-muted description-truncate" title="{{ $item->description }}">
                                                        {{ $item->description ? Str::limit($item->description, 50) : 'N/A' }}
                                                    </small>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-secondary">{{ $item->unit }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span
                                                        class="badge bg-{{ $item->supply_status_color }} fs-6 px-3 py-2">
                                                        {{ $item->current_supply }}
                                                    </span>
                                                    @if ($item->reorder_point)
                                                        <br><small class="text-muted">Reorder:
                                                            {{ $item->reorder_point }}</small>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <small class="text-muted">
                                                        @if ($item->min_quantity)
                                                            Min: {{ $item->min_quantity }}
                                                        @endif
                                                        @if ($item->min_quantity && $item->max_quantity)
                                                            <br>
                                                        @endif
                                                        @if ($item->max_quantity)
                                                            Max: {{ $item->max_quantity }}
                                                        @endif
                                                        @if (!$item->min_quantity && !$item->max_quantity)
                                                            -
                                                        @endif
                                                    </small>
                                                </td>
                                                <td class="text-center">
                                                    <span
                                                        class="badge bg-{{ $item->is_active ? 'success' : 'secondary' }}">
                                                        {{ $item->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-1 justify-content-center flex-wrap">
                                                        <button class="btn btn-sm btn-success position-relative"
                                                            onclick="manageSupply({{ $item->id }})"
                                                            title="Manage Supply" data-bs-toggle="tooltip"
                                                            data-bs-placement="top">
                                                            <i class="fas fa-warehouse"></i>
                                                            @if ($item->needsReorder())
                                                                <span
                                                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                                                    !
                                                                    <span class="visually-hidden">needs reorder</span>
                                                                </span>
                                                            @endif
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-primary"
                                                            onclick="editItem({{ $item->id }})" title="Edit Item"
                                                            data-bs-toggle="tooltip" data-bs-placement="top">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <div class="btn-group btn-group-sm">
                                                            <button type="button"
                                                                class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split"
                                                                data-bs-toggle="dropdown" aria-expanded="false"
                                                                title="More actions">
                                                                <span class="visually-hidden">Toggle Dropdown</span>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                <li>
                                                                    <a class="dropdown-item" href="#"
                                                                        onclick="event.preventDefault(); toggleItem({{ $item->id }})">
                                                                        <i
                                                                            class="fas fa-{{ $item->is_active ? 'eye-slash' : 'eye' }} text-{{ $item->is_active ? 'warning' : 'success' }} me-2"></i>
                                                                        {{ $item->is_active ? 'Deactivate' : 'Activate' }}
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <hr class="dropdown-divider">
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item text-danger" href="#"
                                                                        onclick="event.preventDefault(); deleteItem({{ $item->id }})">
                                                                        <i class="fas fa-trash me-2"></i>Delete Item
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">No items in this category yet</h5>
                                <p class="text-muted">Click "Add Item" to create your first item.</p>
                                <button class="btn btn-primary mt-2" data-bs-toggle="modal"
                                    data-bs-target="#createItemModal" onclick="setItemCategory({{ $category->id }})">
                                    <i class="fas fa-plus me-2"></i>Add First Item
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
         <!-- Toast Container for Notifications -->
        <div id="toastContainer" class="toast-container"></div>
    </div>

     <!-- Create Category Modal - IMPROVED with Word Count -->
    <div class="modal fade" id="createCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-0 d-flex justify-content-center bg-primary text-white">
                    <h5 class="modal-title">Create New Category</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" style="position: absolute; right: 1rem;"></button>
                </div>
                <form id="createCategoryForm" novalidate>
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name <span style="color: #dc3545;">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                            <input type="hidden" name="display_name" id="display_name_hidden">
                            <div class="invalid-feedback">Please provide a category name.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Icon <span style="color: #dc3545;">*</span></label>
                            <select name="icon" id="create_icon" class="form-select" required
                                onchange="updateIconPreview('create')">
                                <option value="">Select an icon...</option>
                                <option value="fa-seedling">Seedling</option>
                                <option value="fa-leaf">Leaf</option>
                                <option value="fa-tree">Tree</option>
                                <option value="fa-spa">Herbs/Spa</option>
                                <option value="fa-cannabis">Cannabis/Plant</option>
                                <option value="fa-pepper-hot">Pepper</option>
                                <option value="fa-carrot">Carrot/Vegetable</option>
                                <option value="fa-apple-alt">Apple/Fruit</option>
                                <option value="fa-lemon">Lemon/Citrus</option>
                                <option value="fa-wheat-awn">Wheat/Grain/Corn</option>
                                <option value="fa-flask">Flask/Chemical</option>
                                <option value="fa-tint">Tint/Water</option>
                                <option value="fa-sun">Sun</option>
                                <option value="fa-cloud-rain">Rain</option>
                                <option value="fa-hand-holding-heart">Hand Holding Heart</option>
                                <option value="fa-tractor">Tractor/Farm</option>
                                <option value="fa-warehouse">Warehouse</option>
                                <option value="fa-tools">Tools</option>
                                <option value="fa-person-digging">Shovel</option>
                                <option value="fa-recycle">Recycle</option>
                                <option value="fa-boxes">Boxes</option>
                                <option value="fa-box-open">Box Open</option>
                            </select>
                            <div class="invalid-feedback">Please select an icon.</div>
                            <div class="mt-2">
                                <small class="text-muted">Preview: </small>
                                <i id="create_icon_preview" class="fas fa-leaf fa-2x"></i>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description <span style="color: #dc3545;">*</span></label>
                            <textarea name="description" class="form-control" rows="2" required maxlength="500"
                                onchange="updateCategoryDescriptionCounter()"
                                oninput="updateCategoryDescriptionCounter()"></textarea>
                            <div class="invalid-feedback">Please provide a description.</div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>Describe what this category contains
                                </small>
                                <small class="text-muted" id="categoryDescriptionCounter">
                                    <span id="categoryCharCount">0</span>/500
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Edit Category Modal -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-0 d-flex justify-content-center bg-primary text-white">
                    <h5 class="modal-title">Edit Category</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" style="position: absolute; right: 1rem;"></button>
                </div>
                <form id="editCategoryForm" novalidate>
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_category_id" name="category_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name <span style="color: #dc3545;">*</span></label>
                            <input type="text" id="edit_category_name" name="name" class="form-control" required>
                            <input type="hidden" id="edit_display_name_hidden" name="display_name">
                            <div class="invalid-feedback">Please provide a category name.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Icon <span style="color: #dc3545;">*</span></label>
                            <select name="icon" id="edit_icon" class="form-select" required onchange="updateIconPreview('edit'); checkForCategoryChanges()">
                                <option value="">Select an icon...</option>
                                <option value="fa-seedling">Seedling</option>
                                <option value="fa-leaf">Leaf</option>
                                <option value="fa-tree">Tree</option>
                                <option value="fa-spa">Herbs/Spa</option>
                                <option value="fa-cannabis">Cannabis/Plant</option>
                                <option value="fa-pepper-hot">Pepper</option>
                                <option value="fa-carrot">Carrot/Vegetable</option>
                                <option value="fa-apple-alt">Apple/Fruit</option>
                                <option value="fa-lemon">Lemon/Citrus</option>
                                <option value="fa-wheat-awn">Wheat/Grain/Corn</option>
                                <option value="fa-flask">Flask/Chemical</option>
                                <option value="fa-tint">Tint/Water</option>
                                <option value="fa-sun">Sun</option>
                                <option value="fa-cloud-rain">Rain</option>
                                <option value="fa-hand-holding-heart">Hand Holding Heart</option>
                                <option value="fa-tractor">Tractor/Farm</option>
                                <option value="fa-warehouse">Warehouse</option>
                                <option value="fa-tools">Tools</option>
                                <option value="fa-person-digging">Shovel</option>
                                <option value="fa-recycle">Recycle</option>
                                <option value="fa-boxes">Boxes</option>
                                <option value="fa-box-open">Box Open</option>
                            </select>
                            <div class="invalid-feedback">Please select an icon.</div>
                            <div class="mt-2">
                                <small class="text-muted">Preview: </small>
                                <i id="edit_icon_preview" class="fas fa-leaf fa-2x"></i>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description <span style="color: #dc3545;">*</span></label>
                            <textarea id="edit_category_description" name="description" class="form-control" rows="2" required></textarea>
                            <div class="invalid-feedback">Please provide a description.</div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>Describe what this category contains
                            </small>
                            <small class="text-muted" id="editCategoryDescriptionCounter">
                                <span id="editCategoryCharCount">0</span>/500
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="editCategorySubmitBtn">Update Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Item Modal with Helper Text -->
    <div class="modal fade" id="createItemModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0 d-flex justify-content-center bg-primary text-white">
                    <h5 class="modal-title">Add New Item</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" style="position: absolute; right: 1rem;"></button>
                </div>
                <form id="createItemForm" enctype="multipart/form-data" novalidate>
                    @csrf
                    <input type="hidden" id="item_category_id" name="category_id" required>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Name <span style="color: #dc3545;">*</span></label>
                                <input type="text" name="name" class="form-control" required>
                                <small class="text-muted d-block mt-2">
                                    <i class="fas fa-info-circle me-1"></i>Enter a clear, descriptive name for the item
                                </small>
                                <div class="invalid-feedback">Please provide an item name.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Unit <span style="color: #dc3545;">*</span></label>
                                <select name="unit" class="form-select" required>
                                    <option value="">Select unit...</option>
                                    <option value="pcs" selected>Pieces (pcs)</option>
                                    <option value="kg">Kilogram (kg)</option>
                                    <option value="L">Liter (L)</option>
                                    <option value="pack">Pack</option>
                                    <option value="bag">Bag</option>
                                </select>
                                <small class="text-muted d-block mt-2">
                                    <i class="fas fa-info-circle me-1"></i>Select the measurement unit for this item
                                </small>
                                <div class="invalid-feedback">Please select a unit.</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description <span style="color: #dc3545;">*</span></label>
                            <textarea name="description" class="form-control" rows="3" required maxlength="500"
                                onchange="updateItemDescriptionCounter()"
                                oninput="updateItemDescriptionCounter()"></textarea>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <small class="text-muted d-block">
                                    <i class="fas fa-info-circle me-1"></i>Provide details about the item, its uses, or specifications
                                </small>
                                <small class="text-muted" id="itemDescriptionCounter">
                                    <span id="itemCharCount">0</span>/500
                                </small>
                            </div>
                            <div class="invalid-feedback">Please provide a description.</div>
                        </div>

                        <hr>
                        <h6 class="text-primary mb-3"><i class="fas fa-warehouse me-2"></i>Supply Management</h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Initial Supply <span style="color: #dc3545;">*</span></label>
                                <input type="number" name="current_supply" class="form-control" placeholder="0" value="0"
                                    min="0" required>
                                <small class="text-muted d-block mt-2">
                                    <i class="fas fa-boxes me-1"></i>Current quantity of this item in stock
                                </small>
                                <div class="invalid-feedback">Please provide initial supply.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Minimum Supply <span style="color: #dc3545;">*</span></label>
                                <input type="number" name="minimum_supply" class="form-control" placeholder="0" value="0"
                                    min="0" required>
                                <small class="text-muted d-block mt-2">
                                    <i class="fas fa-arrow-down me-1"></i>Lowest acceptable stock level before restocking
                                </small>
                                <div class="invalid-feedback">Please provide minimum supply.</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Maximum Supply <span style="color: #dc3545;">*</span></label>
                                <input type="number" name="maximum_supply" class="form-control" placeholder="e.g., 100" min="0" required>
                                <small class="text-muted d-block mt-2">
                                    <i class="fas fa-arrow-up me-1"></i>Maximum storage capacity or recommended stock level
                                </small>
                                <div class="invalid-feedback">Please provide maximum supply.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Reorder Point <span style="color: #dc3545;">*</span></label>
                                <input type="number" name="reorder_point" class="form-control" placeholder="e.g., 20" min="0" required>
                                <small class="text-muted d-block mt-2">
                                    <i class="fas fa-bell me-1"></i>Stock level that triggers a reorder alert/notification
                                </small>
                                <div class="invalid-feedback">Please provide reorder point.</div>
                            </div>
                        </div>

                        <hr>
                        <h6 class="text-primary mb-3"><i class="fas fa-image me-2"></i>Item Image</h6>

                        <div class="mb-3">
                            <label class="form-label">Image <span style="color: #dc3545;">*</span></label>
                            <input type="file" name="image" class="form-control" accept="image/*" required>
                            <small class="text-muted d-block mt-2">
                                <i class="fas fa-info-circle me-1"></i>Recommended size: 300x300px | Max file size: 10MB
                            </small>
                            <div class="invalid-feedback">Please select an image file.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add Item
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Edit Item Modal -->
    <div class="modal fade" id="editItemModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0 d-flex justify-content-center bg-primary text-white">
                    <h5 class="modal-title">Edit Item</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" style="position: absolute; right: 1rem;"></button>
                </div>
                <form id="editItemForm" enctype="multipart/form-data" novalidate>
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_item_id" name="item_id">
                    <input type="hidden" id="edit_item_category_id" name="category_id" required>
                    <div class="modal-body">
                        <!-- Basic Information Section -->
                        <h6 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Basic Information</h6>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Name <span style="color: #dc3545;">*</span></label>
                                <input type="text" id="edit_item_name" name="name" class="form-control" required>
                                <small class="text-muted d-block mt-2">
                                    <i class="fas fa-info-circle me-1"></i>Enter a clear, descriptive name for the item
                                </small>
                                <div class="invalid-feedback">Please provide an item name.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Unit <span style="color: #dc3545;">*</span></label>
                                <select id="edit_item_unit" name="unit" class="form-select" required>
                                    <option value="">Select unit...</option>
                                    <option value="pcs">Pieces (pcs)</option>
                                    <option value="kg">Kilogram (kg)</option>
                                    <option value="L">Liter (L)</option>
                                    <option value="pack">Pack</option>
                                    <option value="bag">Bag</option>
                                </select>
                                <small class="text-muted d-block mt-2">
                                    <i class="fas fa-info-circle me-1"></i>Select the measurement unit for this item
                                </small>
                                <div class="invalid-feedback">Please select a unit.</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description <span style="color: #dc3545;">*</span></label>
                            <textarea id="edit_item_description" name="description" class="form-control" rows="3" required maxlength="500"
                                onchange="updateEditItemDescriptionCounter()"
                                oninput="updateEditItemDescriptionCounter()"></textarea>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <small class="text-muted d-block">
                                    <i class="fas fa-info-circle me-1"></i>Provide details about the item, its uses, or specifications
                                </small>
                                <small class="text-muted" id="editItemDescriptionCounter">
                                    <span id="editItemCharCount">0</span>/500
                                </small>
                            </div>
                            <div class="invalid-feedback">Please provide a description.</div>
                        </div>

                        <hr>

                        <!-- Supply Management Section -->
                        <h6 class="text-primary mb-3"><i class="fas fa-warehouse me-2"></i>Supply Management</h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Minimum Supply <span style="color: #dc3545;">*</span></label>
                                <input type="number" id="edit_item_minimum_supply" name="minimum_supply" class="form-control" value="0" min="0" required>
                                <small class="text-muted d-block mt-2">
                                    <i class="fas fa-arrow-down me-1"></i>Lowest acceptable stock level before restocking
                                </small>
                                <div class="invalid-feedback">Please provide minimum supply.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Maximum Supply <span style="color: #dc3545;">*</span></label>
                                <input type="number" id="edit_item_maximum_supply" name="maximum_supply" class="form-control" min="0" required>
                                <small class="text-muted d-block mt-2">
                                    <i class="fas fa-arrow-up me-1"></i>Maximum storage capacity or recommended stock level
                                </small>
                                <div class="invalid-feedback">Please provide maximum supply.</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Reorder Point <span style="color: #dc3545;">*</span></label>
                            <input type="number" id="edit_item_reorder_point" name="reorder_point" class="form-control" min="0" required>
                            <small class="text-muted d-block mt-2">
                                <i class="fas fa-bell me-1"></i>Stock level that triggers a reorder alert/notification
                            </small>
                            <div class="invalid-feedback">Please provide reorder point.</div>
                        </div>

                        <hr>

                        <!-- Item Image Section -->
                        <h6 class="text-primary mb-3"><i class="fas fa-image me-2"></i>Item Image</h6>

                        <div class="mb-3">
                            <div id="current_image_preview" class="mb-3"></div>
                            <label class="form-label">Change Image (Optional)</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <small class="text-muted d-block mt-2">
                                <i class="fas fa-info-circle me-1"></i>Recommended size: 300x300px | Max file size: 10MB
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="editItemSubmitBtn">
                            <i class="fas fa-save me-2"></i>Update Item
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Supply Management Modal - ENLARGED -->
    <div class="modal fade" id="supplyModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-success text-white w-100 text-center">
                    <h5 class="modal-title"><i></i>Supply Management</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Supply Info -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6 id="supply_item_name" class="mb-3"></h6>
                            <div class="row text-center">
                                <div class="col-md-3">
                                    <div class="text-muted small">Current Supply</div>
                                    <div class="h4 fw-bold text-primary" id="supply_current">0</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-muted small">Minimum</div>
                                    <div class="h4 fw-bold text-secondary" id="supply_minimum">0</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-muted small">Maximum</div>
                                    <div class="h4 fw-bold text-secondary" id="supply_maximum">-</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-muted small">Reorder Point</div>
                                    <div class="h4 fw-bold text-warning" id="supply_reorder">-</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Supply Actions -->
                    <div class="row">
                        <div class="col-lg-4 mb-3">
                            <div class="card h-100">
                                <div class="card-header bg-success text-white">
                                    <i class="fas fa-arrow-up me-2"></i>Add Supply
                                </div>
                                <div class="card-body">
                                    <form id="addSupplyForm" novalidate>
                                        <input type="hidden" id="add_supply_item_id" name="item_id">
                                        <div class="mb-3">
                                            <label class="form-label">Quantity <span style="color: #dc3545;">*</span></label>
                                            <input type="number" name="quantity" class="form-control"
                                                required min="1">
                                            <div class="invalid-feedback">Please enter a quantity.</div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Source</label>
                                            <input type="text" name="source" class="form-control"
                                                placeholder="e.g., Supplier name">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Notes</label>
                                            <textarea name="notes" class="form-control" rows="5"
                                                placeholder="Add detailed notes about this supply addition..."></textarea>
                                            <small class="text-muted d-block mt-2">
                                                <span id="addSupplyNoteCount">0</span>/500 characters
                                            </small>
                                        </div>
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="fas fa-plus-circle me-1"></i>Add Supply
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 mb-3">
                            <div class="card h-100">
                                <div class="card-header bg-warning text-dark">
                                    <i class="fas fa-edit me-2"></i>Adjust Supply
                                </div>
                                <div class="card-body">
                                    <form id="adjustSupplyForm" novalidate>
                                        <input type="hidden" id="adjust_supply_item_id" name="item_id">
                                        <div class="mb-3">
                                            <label class="form-label">New Supply <span style="color: #dc3545;">*</span></label>
                                            <input type="number" name="new_supply" class="form-control"
                                                required min="0">
                                            <div class="invalid-feedback">Please enter new supply amount.</div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Reason <span style="color: #dc3545;">*</span></label>
                                            <textarea name="reason" class="form-control" rows="5" required
                                                placeholder="Explain the adjustment in detail..."></textarea>
                                            <small class="text-muted d-block mt-2">
                                                <span id="adjustSupplyReasonCount">0</span>/500 characters
                                            </small>
                                            <div class="invalid-feedback">Please provide a reason.</div>
                                        </div>
                                        <button type="submit" class="btn btn-warning w-100">
                                            <i class="fas fa-sync-alt me-1"></i>Adjust Supply
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 mb-3">
                            <div class="card h-100">
                                <div class="card-header bg-danger text-white">
                                    <i class="fas fa-exclamation-triangle me-2"></i>Record Loss
                                </div>
                                <div class="card-body">
                                    <form id="recordLossForm" novalidate>
                                        <input type="hidden" id="loss_supply_item_id" name="item_id">
                                        <div class="mb-3">
                                            <label class="form-label">Quantity Lost <span style="color: #dc3545;">*</span></label>
                                            <input type="number" name="quantity" class="form-control"
                                                required min="1">
                                            <div class="invalid-feedback">Please enter quantity lost.</div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Reason Type <span style="color: #dc3545;">*</span></label>
                                            <select name="reason_type" class="form-select mb-2" required>
                                                <option value="">Select reason type...</option>
                                                <option value="Expired">Expired</option>
                                                <option value="Damaged">Damaged</option>
                                                <option value="Lost">Lost</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Additional Details <span style="color: #dc3545;">*</span></label>
                                            <textarea name="reason" class="form-control" rows="5" required
                                                placeholder="Provide detailed information about the loss..."></textarea>
                                            <small class="text-muted d-block mt-2">
                                                <span id="lossReasonCount">0</span>/500 characters
                                            </small>
                                            <div class="invalid-feedback">Please provide details.</div>
                                        </div>
                                        <button type="submit" class="btn btn-danger w-100">
                                            <i class="fas fa-minus-circle me-1"></i>Record Loss
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Supply Logs -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <i class="fas fa-history me-2"></i>Recent Supply Movements
                        </div>
                        <div class="card-body p-0">
                            <div id="supply_logs" style="max-height: 400px; overflow-y: auto;">
                                <div class="text-center py-3">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Auto-fill display_name when name changes
        document.addEventListener('DOMContentLoaded', function() {
            const createCategoryForm = document.getElementById('createCategoryForm');
            const nameInput = createCategoryForm.querySelector('input[name="name"]');
            const displayNameHidden = document.getElementById('display_name_hidden');
            
            nameInput.addEventListener('change', function() {
                displayNameHidden.value = this.value;
            });
            
            // Set it on form submission as well
            createCategoryForm.addEventListener('submit', function() {
                const nameValue = nameInput.value;
                displayNameHidden.value = nameValue;
            });
        });

        // Auto-fill display_name when name changes in Edit Category Modal
        document.addEventListener('DOMContentLoaded', function() {
        const nameInput = document.getElementById('edit_category_name');
        const iconSelect = document.getElementById('edit_icon');
        const descriptionInput = document.getElementById('edit_category_description');
        
        if (nameInput) {
            nameInput.addEventListener('input', function() {
                document.getElementById('edit_display_name_hidden').value = this.value;
                checkForCategoryChanges();
            });
        }
        
        if (iconSelect) {
            iconSelect.addEventListener('change', checkForCategoryChanges);
        }
        
        if (descriptionInput) {
            descriptionInput.addEventListener('input', checkForCategoryChanges);
        }
    });

         // Show More/Less functionality
        function toggleShowMore() {
            const categoryBtns = document.querySelectorAll('.category-btn');
            categoryBtns.forEach((btn, index) => {
                if (index >= 6) {
                    btn.style.display = '';
                }
            });
            document.getElementById('showMoreBtn').style.display = 'none';
            document.getElementById('showLessBtn').style.display = 'inline-block';
        }

        function toggleShowLess() {
            const categoryBtns = document.querySelectorAll('.category-btn');
            categoryBtns.forEach((btn, index) => {
                if (index >= 6) {
                    btn.style.display = 'none';
                }
            });
            document.getElementById('showMoreBtn').style.display = 'inline-block';
            document.getElementById('showLessBtn').style.display = 'none';
        }

        // Show error modal (closes any open modal first)
       function showError(message) {
            showToast('error', message);
        }

        // FIXED Show success modal (closes any open modal first)
        function showSuccess(message, shouldReload = true) {
            showToast('success', message);
            
            if (shouldReload) {
                // Get the currently active category BEFORE reload
                const activeCategory = document.querySelector('.category-content.active');
                const activeCategoryId = activeCategory ? activeCategory.id.replace('category-', '') : 'all';
                
                setTimeout(() => {
                    // Store the category ID in sessionStorage temporarily
                    sessionStorage.setItem('pendingCategorySwitch', activeCategoryId);
                    location.reload();
                }, 1500);
            }
        }

        // create toast container 
        function createToastContainer() {
            let container = document.getElementById('toastContainer');
            if (!container) {
                container = document.createElement('div');
                container.id = 'toastContainer';
                container.className = 'toast-container';
                document.body.appendChild(container);
            }
            return container;
        }

        // Toast notification function
        function showToast(type, message) {
            const toastContainer = document.getElementById('toastContainer') || createToastContainer();

            const iconMap = {
                'success': {
                    icon: 'fas fa-check-circle',
                    color: 'success'
                },
                'error': {
                    icon: 'fas fa-exclamation-circle',
                    color: 'danger'
                },
                'warning': {
                    icon: 'fas fa-exclamation-triangle',
                    color: 'warning'
                },
                'info': {
                    icon: 'fas fa-info-circle',
                    color: 'info'
                }
            };

            const config = iconMap[type] || iconMap['info'];

            const toast = document.createElement('div');
            toast.className = `toast-notification toast-${type}`;
            toast.innerHTML = `
                <div class="toast-content">
                    <i class="${config.icon} me-2" style="color: var(--bs-${config.color});"></i>
                    <span>${message}</span>
                    <button type="button" class="btn-close btn-close-toast ms-auto" onclick="removeToast(this.closest('.toast-notification'))"></button>
                </div>
            `;

            toastContainer.appendChild(toast);
            setTimeout(() => toast.classList.add('show'), 10);

            // Auto-dismiss after 5 seconds
            setTimeout(() => {
                if (document.contains(toast)) {
                    removeToast(toast);
                }
            }, 5000);
        }

        // Confirmation toast function
        function showConfirmationToast(title, message, onConfirm) {
            const toastContainer = document.getElementById('toastContainer') || createToastContainer();

            const toast = document.createElement('div');
            toast.className = 'toast-notification confirmation-toast';

            // Store the callback function on the toast element
            toast.dataset.confirmCallback = Math.random().toString(36);
            window[toast.dataset.confirmCallback] = onConfirm;

            toast.innerHTML = `
                <div class="toast-header">
                    <i class="fas fa-question-circle me-2 text-warning"></i>
                    <strong class="me-auto">${title}</strong>
                    <button type="button" class="btn-close btn-close-toast" onclick="removeToast(this.closest('.toast-notification'))"></button>
                </div>
                <div class="toast-body">
                    <p class="mb-3" style="white-space: pre-wrap;">${message}</p>
                    <div class="d-flex gap-2 justify-content-end">
                        <button type="button" class="btn btn-sm btn-secondary" onclick="removeToast(this.closest('.toast-notification'))">
                            <i class="fas fa-times me-1"></i>Cancel
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" onclick="confirmToastAction(this)">
                            <i class="fas fa-check me-1"></i>Confirm
                        </button>
                    </div>
                </div>
            `;

            toastContainer.appendChild(toast);
            setTimeout(() => toast.classList.add('show'), 10);

            // Auto-dismiss after 10 seconds
            setTimeout(() => {
                if (document.contains(toast)) {
                    removeToast(toast);
                }
            }, 10000);
        }

        // Execute confirmation action
        function confirmToastAction(button) {
            const toast = button.closest('.toast-notification');
            const callbackId = toast.dataset.confirmCallback;
            const callback = window[callbackId];

            if (typeof callback === 'function') {
                try {
                    callback();
                } catch (error) {
                    console.error('Error executing confirmation callback:', error);
                }
            }

            // Clean up the callback reference
            delete window[callbackId];
            removeToast(toast);
        }

        // Remove toast notification
        function removeToast(toastElement) {
            toastElement.classList.remove('show');
            setTimeout(() => {
                if (toastElement.parentElement) {
                    toastElement.remove();
                }
            }, 300);
        }

        // Get CSRF token utility function
        function getCSRFToken() {
            const metaTag = document.querySelector('meta[name="csrf-token"]');
            return metaTag ? metaTag.getAttribute('content') : '';
        }

        // Icon preview
        function updateIconPreview(type) {
            const select = document.getElementById(`${type}_icon`);
            const preview = document.getElementById(`${type}_icon_preview`);
            const iconClass = select.value;
            preview.className = iconClass ? `fas ${iconClass} fa-2x` : 'fas fa-leaf fa-2x';
        }

        // Form validation helper
        function validateForm(form) {
            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                return false;
            }
            return true;
        }

        // Helper function
        async function makeRequest(url, options) {
            try {
                const response = await fetch(url, options);
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Request failed');
                return data;
            } catch (error) {
                console.error('Error:', error);
                throw error;
            }
        }

        // Category Functions
        function setItemCategory(categoryId) {
            document.getElementById('item_category_id').value = categoryId;
        }

    // Load category and initialize change detection
 async function editCategory(categoryId) {
    try {
        const category = await makeRequest(`/admin/seedlings/supply-management/${categoryId}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        });

        // Get form fields
        const nameInput = document.getElementById('edit_category_name');
        const iconSelect = document.getElementById('edit_icon');
        const descriptionInput = document.getElementById('edit_category_description');
        const submitBtn = document.getElementById('editCategorySubmitBtn');

        // Populate form fields
        document.getElementById('edit_category_id').value = category.id;
        nameInput.value = category.name;
        iconSelect.value = category.icon || 'fa-leaf';
        descriptionInput.value = category.description || '';
        document.getElementById('edit_display_name_hidden').value = category.display_name;

        // Store original values in data attributes
        nameInput.dataset.originalValue = category.name;
        iconSelect.dataset.originalValue = category.icon || 'fa-leaf';
        descriptionInput.dataset.originalValue = category.description || '';

        // Update icon preview
        updateIconPreview('edit');

        // Reset validation
        document.getElementById('editCategoryForm').classList.remove('was-validated');

        // Clear any previous change styling
        nameInput.classList.remove('form-changed');
        iconSelect.classList.remove('form-changed');
        descriptionInput.classList.remove('form-changed');

        // FIXED: Set submit button with FULL TEXT initially
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Update Category';
        submitBtn.classList.remove('no-changes');
        submitBtn.title = 'Update category';

        // Initialize change detection
        checkForCategoryChanges();

        // Show modal
        new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
    } catch (error) {
        showError('Error loading category: ' + error.message);
    }
}

// CHANGE: Updated form submission with confirmation toast
document.getElementById('editCategoryForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const nameInput = document.getElementById('edit_category_name');
    const iconSelect = document.getElementById('edit_icon');
    const descriptionInput = document.getElementById('edit_category_description');

    const originalName = nameInput.dataset.originalValue || '';
    const originalIcon = iconSelect.dataset.originalValue || '';
    const originalDescription = descriptionInput.dataset.originalValue || '';

    const nameChanged = nameInput.value.trim() !== originalName;
    const iconChanged = iconSelect.value !== originalIcon;
    const descriptionChanged = descriptionInput.value.trim() !== originalDescription;

    // If no changes, show warning and return
    if (!nameChanged && !iconChanged && !descriptionChanged) {
        showToast('warning', 'No changes detected. Please modify the category before updating.');
        return;
    }

    if (!validateForm(this)) {
        return;
    }

    // Show confirmation toast instead of direct submission
    showConfirmationToast(
        'Update Category',
        `Confirm updating category changes?\n\n${nameChanged ? '• Category name updated\n' : ''}${iconChanged ? '• Icon updated\n' : ''}${descriptionChanged ? '• Description updated' : ''}`,
        () => proceedUpdateCategory()
    );
});
// handle category update after confirmation
async function proceedUpdateCategory() {
    const categoryId = document.getElementById('edit_category_id').value;
    const formData = new FormData(document.getElementById('editCategoryForm'));
    const submitBtn = document.querySelector('#editCategoryModal .btn-primary');
    const originalHTML = submitBtn.innerHTML;

    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    submitBtn.disabled = true;

    try {
        const data = await makeRequest(`/admin/seedlings/supply-management/${categoryId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });
        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('editCategoryModal'));
        modal.hide();
        showSuccess(data.message);
    } catch (error) {
        showError(error.message);
        submitBtn.innerHTML = originalHTML;
        submitBtn.disabled = false;
    }
}


        async function toggleCategory(categoryId) {
            showConfirmationToast(
                'Toggle Category Status',
                'Are you sure you want to toggle this category status?',
                () => proceedToggleCategory(categoryId)
            );
        }

        async function proceedToggleCategory(categoryId) {
            try {
                const data = await makeRequest(`/admin/seedlings/supply-management/${categoryId}/toggle`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json'
                    }
                });
                showSuccess(data.message);
            } catch (error) {
                showError(error.message);
            }
        }

        async function deleteCategory(categoryId) {
            showConfirmationToast(
                'Delete Category',
                'Are you sure you want to delete this category permanently?\n\nAll items in this category will also be deleted.',
                () => proceedDeleteCategory(categoryId)
            );
        }

        async function proceedDeleteCategory(categoryId) {
            try {
                const data = await makeRequest(`/admin/seedlings/supply-management/${categoryId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json'
                    }
                });
                showSuccess(data.message);
            } catch (error) {
                showError(error.message);
            }
        }

        // Supply Management Functions
        async function manageSupply(itemId) {
            try {
                const item = await makeRequest(`/admin/seedlings/items/${itemId}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                // Populate modal
                document.getElementById('supply_item_name').textContent = item.name;
                document.getElementById('supply_current').textContent = item.current_supply;
                document.getElementById('supply_minimum').textContent = item.minimum_supply || 0;
                document.getElementById('supply_maximum').textContent = item.maximum_supply || '-';
                document.getElementById('supply_reorder').textContent = item.reorder_point || '-';

                // Set item IDs in forms
                document.getElementById('add_supply_item_id').value = itemId;
                document.getElementById('adjust_supply_item_id').value = itemId;
                document.getElementById('loss_supply_item_id').value = itemId;

                // Reset forms and validation
                document.getElementById('addSupplyForm').reset();
                document.getElementById('adjustSupplyForm').reset();
                document.getElementById('recordLossForm').reset();
                document.getElementById('addSupplyForm').classList.remove('was-validated');
                document.getElementById('adjustSupplyForm').classList.remove('was-validated');
                document.getElementById('recordLossForm').classList.remove('was-validated');

                // Load supply logs
                loadSupplyLogs(itemId);

                // Show modal
                new bootstrap.Modal(document.getElementById('supplyModal')).show();
            } catch (error) {
                showError('Error loading item: ' + error.message);
            }
        }

        async function loadSupplyLogs(itemId) {
            const logsDiv = document.getElementById('supply_logs');
            logsDiv.innerHTML =
                '<div class="text-center py-3"><div class="spinner-border spinner-border-sm"></div></div>';

            try {
                const data = await makeRequest(`/admin/seedlings/items/${itemId}/supply/logs`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                if (data.data.length === 0) {
                    logsDiv.innerHTML = '<div class="text-center py-3 text-muted">No supply movements yet</div>';
                    return;
                }

                let html = '<div class="list-group list-group-flush">';
                data.data.forEach(log => {
                    const icon = getTransactionIcon(log.transaction_type);
                    const color = getTransactionColor(log.transaction_type);
                    const change = log.new_supply - log.old_supply;
                    const changeSign = change > 0 ? '+' : '';

                    html += `
                <div class="list-group-item">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <i class="fas ${icon} text-${color} me-2"></i>
                            <strong>${log.transaction_type.replace('_', ' ').toUpperCase()}</strong>
                            <br>
                            <small class="text-muted">${log.notes || 'No notes'}</small>
                            ${log.source ? `<br><small class="text-info"><i class="fas fa-truck me-1"></i>${log.source}</small>` : ''}
                        </div>
                        <div class="text-end">
                            <span class="badge bg-${color}">${changeSign}${Math.abs(change)}</span>
                            <br>
                            <small class="text-muted">${log.old_supply} → ${log.new_supply}</small>
                            <br>
                            <small class="text-muted">${new Date(log.created_at).toLocaleString()}</small>
                        </div>
                    </div>
                </div>
            `;
                });
                html += '</div>';
                logsDiv.innerHTML = html;
            } catch (error) {
                logsDiv.innerHTML = '<div class="alert alert-danger m-3">Failed to load logs</div>';
            }
        }

        function getTransactionIcon(type) {
            const icons = {
                'received': 'fa-arrow-up',
                'distributed': 'fa-hand-holding',
                'returned': 'fa-undo',
                'adjustment': 'fa-edit',
                'loss': 'fa-exclamation-triangle',
                'initial_supply': 'fa-plus-circle'
            };
            return icons[type] || 'fa-circle';
        }

        function getTransactionColor(type) {
            const colors = {
                'received': 'success',
                'distributed': 'primary',
                'returned': 'info',
                'adjustment': 'warning',
                'loss': 'danger',
                'initial_supply': 'secondary'
            };
            return colors[type] || 'secondary';
        }

        // Form Submissions with Validation
        document.getElementById('createCategoryForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            if (!validateForm(this)) {
                return;
            }

            const formData = new FormData(this);
            const submitBtn = document.querySelector('#createCategoryModal .btn-primary');
            const originalText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Creating...';
            submitBtn.disabled = true;

            try {
                const data = await makeRequest('/admin/seedlings/supply-management', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('createCategoryModal'));
                modal.hide();
                showSuccess(data.message);
            } catch (error) {
                showError(error.message);
            } finally {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        });

        document.getElementById('createItemForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            if (!validateForm(this)) {
                return;
            }

            const formData = new FormData(this);
            const submitBtn = document.querySelector('#createItemModal .btn-primary');
            const originalText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Adding...';
            submitBtn.disabled = true;

            try {
                const data = await makeRequest('/admin/seedlings/items', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('createItemModal'));
                modal.hide();
                showSuccess(data.message);
            } catch (error) {
                if (error.message.includes('name')) {
                    showError(
                        'An item with this name already exists in this category. Please use a different name.'
                    );
                } else {
                    showError(error.message);
                }
            } finally {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        });

        // Supply Management Forms with Validation

        // add supply form submission
        document.getElementById('addSupplyForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            if (!validateForm(this)) {
                return;
            }

            const itemId = document.getElementById('add_supply_item_id').value;
            const formData = new FormData(this);
            const quantity = formData.get('quantity');
            const source = formData.get('source');
            const notes = formData.get('notes');

            showConfirmationToast(
                'Add Supply',
                `Confirm adding supply?\n\nQuantity: ${quantity}\nSource: ${source || 'Not specified'}\nNotes: ${notes || 'None'}`,
                () => proceedAddSupply(itemId, formData)
            );
        });

        async function proceedAddSupply(itemId, formData) {
            try {
                const data = await makeRequest(`/admin/seedlings/items/${itemId}/supply/add`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(Object.fromEntries(formData))
                });

                showToast('success', data.message);
                document.getElementById('supply_current').textContent = data.new_supply || data.current_supply;
                loadSupplyLogs(itemId);
                document.getElementById('addSupplyForm').reset();
                document.getElementById('addSupplyForm').classList.remove('was-validated');
            } catch (error) {
                showError(error.message);
            }
        }
        
        // adjust supply form submission
        document.getElementById('adjustSupplyForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            if (!validateForm(this)) {
                return;
            }

            const itemId = document.getElementById('adjust_supply_item_id').value;
            const formData = new FormData(this);
            const newSupply = formData.get('new_supply');
            const reason = formData.get('reason');

            showConfirmationToast(
                'Adjust Supply',
                `Confirm manual supply adjustment?\n\nNew Supply: ${newSupply}\nReason: ${reason}`,
                () => proceedAdjustSupply(itemId, formData)
            );
        });
    
        async function proceedAdjustSupply(itemId, formData) {
            try {
                const data = await makeRequest(`/admin/seedlings/items/${itemId}/supply/adjust`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(Object.fromEntries(formData))
                });

                showToast('success', data.message);
                document.getElementById('supply_current').textContent = data.new_supply || data.current_supply;
                loadSupplyLogs(itemId);
                document.getElementById('adjustSupplyForm').reset();
                document.getElementById('adjustSupplyForm').classList.remove('was-validated');
            } catch (error) {
                showError(error.message);
            }
        }
        // record loss form submission
        document.getElementById('recordLossForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            if (!validateForm(this)) {
                return;
            }

            const itemId = document.getElementById('loss_supply_item_id').value;
            const formData = new FormData(this);
            const reasonType = formData.get('reason_type');
            const reason = formData.get('reason');
            const quantity = formData.get('quantity');

            showConfirmationToast(
                'Record Supply Loss',
                `Confirm recording supply loss?\n\nQuantity Lost: ${quantity}\nReason: ${reasonType}\nDetails: ${reason}`,
                () => proceedRecordLoss(itemId, formData, reasonType, reason, quantity)
            );
        });

        async function proceedRecordLoss(itemId, formData, reasonType, reason, quantity) {
            const fullReason = `${reasonType}: ${reason}`;

            try {
                const data = await makeRequest(`/admin/seedlings/items/${itemId}/supply/loss`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        quantity: quantity,
                        reason: fullReason
                    })
                });

                showToast('success', data.message);
                document.getElementById('supply_current').textContent = data.new_supply || data.current_supply;
                loadSupplyLogs(itemId);
                document.getElementById('recordLossForm').reset();
                document.getElementById('recordLossForm').classList.remove('was-validated');
            } catch (error) {
                showError(error.message);
            }
        }

        // RELOAD PAGE WHEN SUPPLY MODAL CLOSES - This ensures the main page shows updated supply counts
        document.getElementById('supplyModal').addEventListener('hidden.bs.modal', function() {
            location.reload();
        });

     async function toggleItem(itemId) {
            showConfirmationToast(
                'Toggle Item Status',
                'Are you sure you want to toggle this item status?',
                () => proceedToggleItem(itemId)
            );
        }

        async function proceedToggleItem(itemId) {
            try {
                const data = await makeRequest(`/admin/seedlings/items/${itemId}/toggle`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json'
                    }
                });
                showSuccess(data.message);
            } catch (error) {
                showError(error.message);
            }
        }

        async function deleteItem(itemId) {
            showConfirmationToast(
                'Delete Item',
                'Are you sure you want to delete this item permanently?\n\nThis action cannot be undone.',
                () => proceedDeleteItem(itemId)
            );
        }

        async function proceedDeleteItem(itemId) {
            try {
                const data = await makeRequest(`/admin/seedlings/items/${itemId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json'
                    }
                });
                showSuccess(data.message);
            } catch (error) {
                showError(error.message);
            }
        }
// Load item and initialize change detection
async function editItem(itemId) {
    try {
        const item = await makeRequest(`/admin/seedlings/items/${itemId}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        });

        // Get form fields
        const nameInput = document.getElementById('edit_item_name');
        const unitSelect = document.getElementById('edit_item_unit');
        const descriptionInput = document.getElementById('edit_item_description');
        const minSupplyInput = document.getElementById('edit_item_minimum_supply');
        const maxSupplyInput = document.getElementById('edit_item_maximum_supply');
        const reorderPointInput = document.getElementById('edit_item_reorder_point');
        const submitBtn = document.getElementById('editItemSubmitBtn');

        // Populate form fields
        document.getElementById('edit_item_id').value = item.id;
        document.getElementById('edit_item_category_id').value = item.category_id;
        nameInput.value = item.name;
        unitSelect.value = item.unit;
        descriptionInput.value = item.description || '';
        minSupplyInput.value = item.minimum_supply || 0;
        maxSupplyInput.value = item.maximum_supply || '';
        reorderPointInput.value = item.reorder_point || '';

        // Store original values in data attributes
        nameInput.dataset.originalValue = item.name;
        unitSelect.dataset.originalValue = item.unit;
        descriptionInput.dataset.originalValue = item.description || '';
        minSupplyInput.dataset.originalValue = item.minimum_supply || 0;
        maxSupplyInput.dataset.originalValue = item.maximum_supply || '';
        reorderPointInput.dataset.originalValue = item.reorder_point || '';

        // Show current image if exists
        const imagePreview = document.getElementById('current_image_preview');
        if (item.image_path) {
            imagePreview.innerHTML = `
                <label class="form-label">Current Image:</label><br>
                <img src="/storage/${item.image_path}" alt="${item.name}"
                    class="rounded" style="width: 100px; height: 100px; object-fit: cover;">
            `;
        } else {
            imagePreview.innerHTML = '';
        }

        // Reset validation
        document.getElementById('editItemForm').classList.remove('was-validated');

        // Clear any previous change styling
        nameInput.classList.remove('form-changed');
        unitSelect.classList.remove('form-changed');
        descriptionInput.classList.remove('form-changed');
        minSupplyInput.classList.remove('form-changed');
        maxSupplyInput.classList.remove('form-changed');
        reorderPointInput.classList.remove('form-changed');

        // FIXED: Set submit button with FULL TEXT initially
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Update Item';
        submitBtn.classList.remove('no-changes');
        submitBtn.title = 'Update item';

        // Initialize change detection
        checkForItemChanges();

        // Show modal
        new bootstrap.Modal(document.getElementById('editItemModal')).show();
    } catch (error) {
        showError('Error loading item: ' + error.message);
    }
}

// CHANGE: Updated form submission with confirmation toast
document.getElementById('editItemForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const nameInput = document.getElementById('edit_item_name');
    const unitSelect = document.getElementById('edit_item_unit');
    const descriptionInput = document.getElementById('edit_item_description');
    const minSupplyInput = document.getElementById('edit_item_minimum_supply');
    const maxSupplyInput = document.getElementById('edit_item_maximum_supply');
    const reorderPointInput = document.getElementById('edit_item_reorder_point');
    const imageInput = document.querySelector('#editItemForm input[name="image"]');

    const originalName = nameInput.dataset.originalValue || '';
    const originalUnit = unitSelect.dataset.originalValue || '';
    const originalDescription = descriptionInput.dataset.originalValue || '';
    const originalMinSupply = minSupplyInput.dataset.originalValue || '0';
    const originalMaxSupply = maxSupplyInput.dataset.originalValue || '';
    const originalReorderPoint = reorderPointInput.dataset.originalValue || '';

    const nameChanged = nameInput.value.trim() !== originalName;
    const unitChanged = unitSelect.value !== originalUnit;
    const descriptionChanged = descriptionInput.value.trim() !== originalDescription;
    const minSupplyChanged = minSupplyInput.value.trim() !== originalMinSupply.toString().trim();
    const maxSupplyChanged = maxSupplyInput.value.trim() !== originalMaxSupply.toString().trim();
    const reorderPointChanged = reorderPointInput.value.trim() !== originalReorderPoint.toString().trim();
    const imageChanged = imageInput.files && imageInput.files.length > 0;

    // If no changes, show warning and return
    if (!nameChanged && !unitChanged && !descriptionChanged && !minSupplyChanged && !maxSupplyChanged && !reorderPointChanged && !imageChanged) {
        showToast('warning', 'No changes detected. Please modify the item before updating.');
        return;
    }

    if (!validateForm(this)) {
        return;
    }

    // Show confirmation toast with list of changes
    const changesList = [];
    if (nameChanged) changesList.push('• Item name updated');
    if (unitChanged) changesList.push('• Unit updated');
    if (descriptionChanged) changesList.push('• Description updated');
    if (minSupplyChanged) changesList.push('• Minimum supply updated');
    if (maxSupplyChanged) changesList.push('• Maximum supply updated');
    if (reorderPointChanged) changesList.push('• Reorder point updated');
    if (imageChanged) changesList.push('• Item image updated');

    showConfirmationToast(
        'Update Item',
        `Confirm updating item changes?\n\n${changesList.join('\n')}`,
        () => proceedUpdateItem()
    );
});

// Handle item update after confirmation
async function proceedUpdateItem() {
    const itemId = document.getElementById('edit_item_id').value;
    const formData = new FormData(document.getElementById('editItemForm'));
    const submitBtn = document.querySelector('#editItemModal .btn-primary');
    const originalHTML = submitBtn.innerHTML;

    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    submitBtn.disabled = true;

    try {
        const data = await makeRequest(`/admin/seedlings/items/${itemId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });
        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('editItemModal'));
        modal.hide();
        showSuccess(data.message);
    } catch (error) {
        showError(error.message);
        submitBtn.innerHTML = originalHTML;
        submitBtn.disabled = false;
    }
}


        // Reset form validation on modal close
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('hidden.bs.modal', function() {
                const forms = this.querySelectorAll('form');
                forms.forEach(form => {
                    form.classList.remove('was-validated');
                    form.reset();
                });
            });
        });

        // Initialize Bootstrap tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        // File size validation for Add Item Modal
        document.getElementById('createItemForm').addEventListener('change', function(e) {
            if (e.target.name === 'image') {
                const file = e.target.files[0];
                const maxSize = 10 * 1024 * 1024; // 10MB in bytes
                
                if (file && file.size > maxSize) {
                    showError(`File size must not exceed 10MB. Your file is ${(file.size / (1024 * 1024)).toFixed(2)}MB`);
                    e.target.value = ''; // Clear the input
                }
            }
        });

        // File size validation for Edit Item Modal
        document.getElementById('editItemForm').addEventListener('change', function(e) {
            if (e.target.name === 'image') {
                const file = e.target.files[0];
                const maxSize = 10 * 1024 * 1024; // 10MB in bytes
                
                if (file && file.size > maxSize) {
                    showError(`File size must not exceed 10MB. Your file is ${(file.size / (1024 * 1024)).toFixed(2)}MB`);
                    e.target.value = ''; // Clear the input
                }
            }
        });

// Check for changes in Edit Category Modal
function checkForCategoryChanges() {
    const nameInput = document.getElementById('edit_category_name');
    const iconSelect = document.getElementById('edit_icon');
    const descriptionInput = document.getElementById('edit_category_description');
    const submitBtn = document.getElementById('editCategorySubmitBtn');
    
    const originalName = nameInput.dataset.originalValue || '';
    const originalIcon = iconSelect.dataset.originalValue || '';
    const originalDescription = descriptionInput.dataset.originalValue || '';
    
    const nameChanged = nameInput.value.trim() !== originalName;
    const iconChanged = iconSelect.value !== originalIcon;
    const descriptionChanged = descriptionInput.value.trim() !== originalDescription;
    
    // Update visual feedback for each field
    nameInput.classList.toggle('form-changed', nameChanged);
    iconSelect.classList.toggle('form-changed', iconChanged);
    descriptionInput.classList.toggle('form-changed', descriptionChanged);

    // TRAINING PATTERN: Icon appears beside text when changes exist
    if (nameChanged || iconChanged || descriptionChanged) {
        // Changes detected - show icon beside text
        submitBtn.classList.remove('no-changes');
        submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Update Category';
        submitBtn.title = 'Changes detected - Click to update';
    } else {
        // No changes - plain text only
        submitBtn.classList.add('no-changes');
        submitBtn.innerHTML = 'Update Category';
        submitBtn.title = 'No changes yet';
    }
}



// Add event listeners for real-time change detection
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('edit_category_name');
    const iconSelect = document.getElementById('edit_icon');
    const descriptionInput = document.getElementById('edit_category_description');
    
    if (nameInput) {
        nameInput.addEventListener('input', function() {
            document.getElementById('edit_display_name_hidden').value = this.value;
            checkForCategoryChanges();
        });
    }
    
    if (iconSelect) {
        iconSelect.addEventListener('change', checkForCategoryChanges);
    }
    
    if (descriptionInput) {
        descriptionInput.addEventListener('input', checkForCategoryChanges);
    }
});

// CHANGE: Updated function to show/hide button changes
function checkForItemChanges() {
    const nameInput = document.getElementById('edit_item_name');
    const unitSelect = document.getElementById('edit_item_unit');
    const descriptionInput = document.getElementById('edit_item_description');
    const minSupplyInput = document.getElementById('edit_item_minimum_supply');
    const maxSupplyInput = document.getElementById('edit_item_maximum_supply');
    const reorderPointInput = document.getElementById('edit_item_reorder_point');
    const imageInput = document.querySelector('#editItemForm input[name="image"]');
    const submitBtn = document.getElementById('editItemSubmitBtn');

    const originalName = nameInput.dataset.originalValue || '';
    const originalUnit = unitSelect.dataset.originalValue || '';
    const originalDescription = descriptionInput.dataset.originalValue || '';
    const originalMinSupply = minSupplyInput.dataset.originalValue || '0';
    const originalMaxSupply = maxSupplyInput.dataset.originalValue || '';
    const originalReorderPoint = reorderPointInput.dataset.originalValue || '';

    const nameChanged = nameInput.value.trim() !== originalName;
    const unitChanged = unitSelect.value !== originalUnit;
    const descriptionChanged = descriptionInput.value.trim() !== originalDescription;
    const minSupplyChanged = minSupplyInput.value.trim() !== originalMinSupply.toString().trim();
    const maxSupplyChanged = maxSupplyInput.value.trim() !== originalMaxSupply.toString().trim();
    const reorderPointChanged = reorderPointInput.value.trim() !== originalReorderPoint.toString().trim();
    const imageChanged = imageInput.files && imageInput.files.length > 0;

    // Update visual feedback for each field
    nameInput.classList.toggle('form-changed', nameChanged);
    unitSelect.classList.toggle('form-changed', unitChanged);
    descriptionInput.classList.toggle('form-changed', descriptionChanged);
    minSupplyInput.classList.toggle('form-changed', minSupplyChanged);
    maxSupplyInput.classList.toggle('form-changed', maxSupplyChanged);
    reorderPointInput.classList.toggle('form-changed', reorderPointChanged);
    imageInput.classList.toggle('form-changed', imageChanged);

    // TRAINING PATTERN: Icon appears beside text when changes exist
    if (nameChanged || unitChanged || descriptionChanged || minSupplyChanged || 
        maxSupplyChanged || reorderPointChanged || imageChanged) {
        // Changes detected - show icon beside text
        submitBtn.classList.remove('no-changes');
        submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Update Item';
        submitBtn.title = 'Changes detected - Click to update';
    } else {
        // No changes - plain text only
        submitBtn.classList.add('no-changes');
        submitBtn.innerHTML = 'Update Item';
        submitBtn.title = 'No changes yet';
    }
}

    // Add event listeners for real-time change detection in Edit Item Modal
document.addEventListener('DOMContentLoaded', function() {
    const editItemForm = document.getElementById('editItemForm');
    
    if (editItemForm) {
        // Get all input fields
        const nameInput = document.getElementById('edit_item_name');
        const unitSelect = document.getElementById('edit_item_unit');
        const descriptionInput = document.getElementById('edit_item_description');
        const minSupplyInput = document.getElementById('edit_item_minimum_supply');
        const maxSupplyInput = document.getElementById('edit_item_maximum_supply');
        const reorderPointInput = document.getElementById('edit_item_reorder_point');
        const imageInput = editItemForm.querySelector('input[name="image"]');

        // Add listeners to all fields
        if (nameInput) {
            nameInput.addEventListener('input', checkForItemChanges);
            nameInput.addEventListener('change', checkForItemChanges);
        }
        
        if (unitSelect) {
            unitSelect.addEventListener('change', checkForItemChanges);
        }
        
        if (descriptionInput) {
            descriptionInput.addEventListener('input', checkForItemChanges);
            descriptionInput.addEventListener('change', checkForItemChanges);
        }
        
        if (minSupplyInput) {
            minSupplyInput.addEventListener('input', checkForItemChanges);
            minSupplyInput.addEventListener('change', checkForItemChanges);
        }
        
        if (maxSupplyInput) {
            maxSupplyInput.addEventListener('input', checkForItemChanges);
            maxSupplyInput.addEventListener('change', checkForItemChanges);
        }
        
        if (reorderPointInput) {
            reorderPointInput.addEventListener('input', checkForItemChanges);
            reorderPointInput.addEventListener('change', checkForItemChanges);
        }
        
        if (imageInput) {
            imageInput.addEventListener('change', checkForItemChanges);
        }
    }
});
        // ==========================================
        // FIXED CATEGORY SWITCHING + FILTERING + PAGINATION
        // ==========================================

        const ITEMS_PER_PAGE = 10;
        let currentPage = {};
        let isFilteringApplied = false;

        // Store filtered results to prevent re-filtering on pagination
        let filteredRowsCache = {};

        // Switch between categories
        function switchCategory(categoryId, event) {
            if (event) {
                event.preventDefault();
            }
            
            // Update active tab button
            document.querySelectorAll('.category-tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            const activeBtn = event ? event.target.closest('.category-tab-btn') : null;
            if (activeBtn) {
                activeBtn.classList.add('active');
            }

            // Hide all category content
            document.querySelectorAll('.category-content').forEach(content => {
                content.classList.remove('active');
            });

            // Show selected category content
            const targetContent = document.getElementById(`category-${categoryId}`);
            if (targetContent) {
                targetContent.classList.add('active');
            }

            // Apply filters and pagination
            applyFiltersToActiveCategory();
        }

        // Apply filters to the currently active category
        function applyFiltersToActiveCategory() {
            const activeCategory = document.querySelector('.category-content.active');
            if (!activeCategory) return;

            const categoryId = activeCategory.id.replace('category-', '');
            const statusFilter = document.querySelector('select[name="status"]').value;
            const stockStatusFilter = document.querySelector('select[name="stock_status"]').value;
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();

            // FIXED: Handle 'All Categories' view differently
            if (categoryId === 'all') {
                handleAllCategoriesFilter(statusFilter, stockStatusFilter, searchTerm);
                return;
            }

            // Original logic for individual categories
            const rows = activeCategory.querySelectorAll('.item-row');
            const filteredRows = [];
            let visibleCount = 0;

            rows.forEach(row => {
                let isVisible = true;

                // Get item name
                const nameElement = row.querySelector('strong');
                const name = nameElement ? nameElement.textContent.toLowerCase() : '';

                // Get item description
                const descriptionElements = row.querySelectorAll('small.text-muted');
                let description = '';
                descriptionElements.forEach(el => {
                    if (!el.textContent.includes('Needs Reorder') &&
                        !el.textContent.includes('Min:') &&
                        !el.textContent.includes('Max:') &&
                        !el.textContent.includes('Reorder:')) {
                        description += el.textContent.toLowerCase() + ' ';
                    }
                });

                // Get status
                const statusBadges = row.querySelectorAll('span.badge');
                let isActive = false;
                statusBadges.forEach(badge => {
                    const text = badge.textContent.trim().toLowerCase();
                    if (text === 'active') {
                        isActive = true;
                    }
                });

                // Get supply status
                const supplyBadge = row.querySelector('span.badge.bg-success, span.badge.bg-warning, span.badge.bg-danger');
                const currentSupply = supplyBadge ? parseInt(supplyBadge.textContent) : 0;

                let stockStatus = 'in_stock';
                if (currentSupply === 0) {
                    stockStatus = 'out_of_stock';
                } else if (row.textContent.includes('Needs Reorder')) {
                    stockStatus = 'low_supply';
                }

                // Apply status filter
                if (statusFilter) {
                    const filterActive = statusFilter === 'active';
                    if (isActive !== filterActive) {
                        isVisible = false;
                    }
                }

                // Apply stock status filter
                if (stockStatusFilter && isVisible) {
                    if (stockStatusFilter !== stockStatus) {
                        isVisible = false;
                    }
                }

                // Apply search filter
                if (searchTerm && isVisible) {
                    if (!name.includes(searchTerm) && !description.includes(searchTerm)) {
                        isVisible = false;
                    }
                }

                if (isVisible) {
                    filteredRows.push(row);
                    visibleCount++;
                }
            });

            // Cache the filtered rows for this category
            filteredRowsCache[categoryId] = filteredRows;

            // Show all rows first (for proper display)
            rows.forEach(row => {
                row.style.display = 'none';
            });

            // Show filtered rows
            filteredRows.forEach(row => {
                row.style.display = '';
            });

            // Handle empty state
            handleEmptyState(activeCategory, visibleCount);

            // Reset pagination and show first page
            currentPage[categoryId] = 1;
            displayPageItems(categoryId, 1, filteredRows);
        }

        // NEW: Handle filtering for "All Categories" view
        function handleAllCategoriesFilter(statusFilter, stockStatusFilter, searchTerm) {
            const container = document.getElementById('category-all');
            if (!container) return;

            const categoryCards = container.querySelectorAll('.col-md-6');
            let totalVisibleItems = 0;

            categoryCards.forEach(card => {
                const rows = card.querySelectorAll('.item-row');
                let categoryVisibleCount = 0;

                rows.forEach(row => {
                    let isVisible = true;

                    // Get item name
                    const nameElement = row.querySelector('strong');
                    const name = nameElement ? nameElement.textContent.toLowerCase() : '';

                    // Get item description
                    const descriptionElements = row.querySelectorAll('small.text-muted');
                    let description = '';
                    descriptionElements.forEach(el => {
                        if (!el.textContent.includes('Needs Reorder') &&
                            !el.textContent.includes('Min:') &&
                            !el.textContent.includes('Max:') &&
                            !el.textContent.includes('Reorder:')) {
                            description += el.textContent.toLowerCase() + ' ';
                        }
                    });

                    // Get status
                    const statusBadges = row.querySelectorAll('span.badge');
                    let isActive = false;
                    statusBadges.forEach(badge => {
                        const text = badge.textContent.trim().toLowerCase();
                        if (text === 'active') {
                            isActive = true;
                        }
                    });

                    // Get supply status
                    const supplyBadge = row.querySelector('span.badge.bg-success, span.badge.bg-warning, span.badge.bg-danger');
                    const currentSupply = supplyBadge ? parseInt(supplyBadge.textContent) : 0;

                    let stockStatus = 'in_stock';
                    if (currentSupply === 0) {
                        stockStatus = 'out_of_stock';
                    } else if (row.textContent.includes('Needs Reorder')) {
                        stockStatus = 'low_supply';
                    }

                    // Apply status filter
                    if (statusFilter) {
                        const filterActive = statusFilter === 'active';
                        if (isActive !== filterActive) {
                            isVisible = false;
                        }
                    }

                    // Apply stock status filter
                    if (stockStatusFilter && isVisible) {
                        if (stockStatusFilter !== stockStatus) {
                            isVisible = false;
                        }
                    }

                    // Apply search filter
                    if (searchTerm && isVisible) {
                        if (!name.includes(searchTerm) && !description.includes(searchTerm)) {
                            isVisible = false;
                        }
                    }

                    row.style.display = isVisible ? '' : 'none';
                    if (isVisible) {
                        categoryVisibleCount++;
                    }
                });

                // Hide empty category cards
                const table = card.querySelector('table');
                if (table) {
                    const emptyMessage = card.querySelector('.no-results-message');
                    if (categoryVisibleCount === 0) {
                        table.style.display = 'none';
                        if (!emptyMessage) {
                            const noResults = document.createElement('div');
                            noResults.className = 'no-results-message text-muted text-center py-3';
                            noResults.innerHTML = '<p>No items match your filters.</p>';
                            table.parentElement.appendChild(noResults);
                        }
                    } else {
                        table.style.display = '';
                        if (emptyMessage) emptyMessage.remove();
                    }
                }

                totalVisibleItems += categoryVisibleCount;
            });

            // Show/hide the "No results" message for entire All Categories view
            let noResultsMessage = container.querySelector('.no-results-message');
            if (totalVisibleItems === 0) {
                if (!noResultsMessage) {
                    noResultsMessage = document.createElement('div');
                    noResultsMessage.className = 'no-results-message text-center py-5';
                    noResultsMessage.innerHTML = `
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No items found</h5>
                        <p class="text-muted">Try adjusting your filters or search terms.</p>
                    `;
                    container.appendChild(noResultsMessage);
                }
            } else {
                if (noResultsMessage) noResultsMessage.remove();
            }
        }

        // Handle empty state message
        function handleEmptyState(container, visibleCount) {
            const existingMessage = container.querySelector('.no-results-message');
            if (existingMessage) {
                existingMessage.remove();
            }

            if (visibleCount === 0) {
                const noResults = document.createElement('div');
                noResults.className = 'no-results-message text-center py-5';
                noResults.innerHTML = `
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No items found</h5>
                    <p class="text-muted">Try adjusting your filters or search terms.</p>
                `;

                if (container.id === 'category-all') {
                    const rowContainer = container.querySelector('.row');
                    if (rowContainer) {
                        rowContainer.parentElement.insertBefore(noResults, rowContainer.nextSibling);
                    }
                } else {
                    const table = container.querySelector('table');
                    if (table) {
                        table.parentElement.parentElement.insertBefore(noResults, table.parentElement.nextSibling);
                    }
                }
            }
        }

        // Display paginated items - FIXED: Uses cached filtered rows
        function displayPageItems(categoryId, page = 1, filteredRows = null) {
            // Use cached filtered rows or retrieve them
            if (!filteredRows) {
                filteredRows = filteredRowsCache[categoryId] || [];
            }

            const totalPages = Math.ceil(filteredRows.length / ITEMS_PER_PAGE) || 1;

            // Validate page number
            if (page < 1) page = 1;
            if (page > totalPages) page = totalPages;

            currentPage[categoryId] = page;

            const startIndex = (page - 1) * ITEMS_PER_PAGE;
            const endIndex = startIndex + ITEMS_PER_PAGE;

            // Hide all rows in this category
            const container = document.getElementById(`category-${categoryId}`);
            if (!container) return;

            const allRows = Array.from(container.querySelectorAll('.item-row'));
            allRows.forEach(row => {
                row.style.display = 'none';
            });

            // Show only current page rows
            filteredRows.slice(startIndex, endIndex).forEach(row => {
                row.style.display = '';
            });

            // Update pagination controls
            updatePaginationControls(categoryId, page, totalPages);
        }

        // Update pagination controls
        function updatePaginationControls(categoryId, currentPageNum, totalPages) {
            const container = document.getElementById(`category-${categoryId}`);
            if (!container) return;

            // Remove old pagination
            const oldPagination = container.querySelector('.pagination-wrapper');
            if (oldPagination) {
                oldPagination.remove();
            }

            if (totalPages <= 1) return;

            const startPage = Math.max(1, currentPageNum - 2);
            const endPage = Math.min(totalPages, currentPageNum + 2);

            let paginationItems = '';

            // Previous Button
            if (currentPageNum === 1) {
                paginationItems += `<li class="page-item disabled"><span class="page-link">Back</span></li>`;
            } else {
                paginationItems += `<li class="page-item"><a class="page-link pagination-link" href="#" data-category="${categoryId}" data-page="${currentPageNum - 1}" rel="prev">Back</a></li>`;
            }

            // Page Numbers
            for (let page = startPage; page <= endPage; page++) {
                if (page === currentPageNum) {
                    paginationItems += `<li class="page-item active"><span class="page-link bg-primary border-primary">${page}</span></li>`;
                } else {
                    paginationItems += `<li class="page-item"><a class="page-link pagination-link" href="#" data-category="${categoryId}" data-page="${page}">${page}</a></li>`;
                }
            }

            // Next Button
            if (currentPageNum === totalPages) {
                paginationItems += `<li class="page-item disabled"><span class="page-link">Next</span></li>`;
            } else {
                paginationItems += `<li class="page-item"><a class="page-link pagination-link" href="#" data-category="${categoryId}" data-page="${currentPageNum + 1}" rel="next">Next</a></li>`;
            }

            const paginationHTML = `
                <div class="pagination-wrapper d-flex justify-content-center mt-4 mb-3">
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm">
                            ${paginationItems}
                        </ul>
                    </nav>
                </div>
            `;

            // Find the table-responsive div and insert pagination AFTER it
            const tableResponsive = container.querySelector('.table-responsive');
            if (tableResponsive) {
                tableResponsive.insertAdjacentHTML('afterend', paginationHTML);
            } else {
                // Fallback: insert at end of card-body
                const cardBody = container.querySelector('.card-body');
                if (cardBody) {
                    cardBody.insertAdjacentHTML('beforeend', paginationHTML);
                }
            }
        }

        // Auto search with debounce
        let searchTimeout;
        function autoSearch() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                applyFiltersToActiveCategory();
            }, 300);
        }

        // Clear filters
        function clearFilters() {
            document.querySelector('select[name="status"]').value = '';
            document.querySelector('select[name="stock_status"]').value = '';
            document.getElementById('searchInput').value = '';
            applyFiltersToActiveCategory();
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            
            // Check if we need to switch back to a category after reload
            const pendingCategory = sessionStorage.getItem('pendingCategorySwitch');
            if (pendingCategory) {
                setTimeout(() => {
                    switchCategory(pendingCategory);
                    // Set active button
                    document.querySelectorAll('.category-tab-btn').forEach(btn => {
                        btn.classList.remove('active');
                        if (btn.dataset.category === pendingCategory) {
                            btn.classList.add('active');
                        }
                    });
                    sessionStorage.removeItem('pendingCategorySwitch');
                }, 100);
            }

            const statusSelect = document.querySelector('select[name="status"]');
            const stockStatusSelect = document.querySelector('select[name="stock_status"]');
            const searchInput = document.getElementById('searchInput');

            if (statusSelect) {
                statusSelect.addEventListener('change', applyFiltersToActiveCategory);
            }

            if (stockStatusSelect) {
                stockStatusSelect.addEventListener('change', applyFiltersToActiveCategory);
            }

            if (searchInput) {
                searchInput.addEventListener('input', autoSearch);
            }

            // Apply initial filters
            applyFiltersToActiveCategory();

            // Initialize pagination for all individual categories
            document.querySelectorAll('.category-content[id^="category-"]').forEach(container => {
                const categoryId = container.id.replace('category-', '');
                if (categoryId !== 'all') {
                    const filteredRows = filteredRowsCache[categoryId] || [];
                    displayPageItems(categoryId, 1, filteredRows);
                }
            });
        });

        // CRITICAL: Add event delegation for pagination links
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('pagination-link')) {
                e.preventDefault();
                const catId = e.target.dataset.category;
                const pageNum = parseInt(e.target.dataset.page);
                if (catId && !isNaN(pageNum)) {
                    // Use cached filtered rows for this category
                    const filteredRows = filteredRowsCache[catId] || [];
                    displayPageItems(catId, pageNum, filteredRows);
                    // Scroll to category
                    const container = document.getElementById(`category-${catId}`);
                    if (container) {
                        container.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }
            }
        });
          // Update category description character counter
    function updateCategoryDescriptionCounter() {
        const textarea = document.querySelector('#createCategoryModal textarea[name="description"]');
        const charCount = document.getElementById('categoryCharCount');
        
        if (textarea && charCount) {
            const currentLength = textarea.value.length;
            
            // Update character count only
            charCount.textContent = currentLength;
        }
    }

    // Update icon preview
    function updateIconPreview(type) {
        const select = document.getElementById(`${type}_icon`);
        const preview = document.getElementById(`${type}_icon_preview`);
        const iconClass = select.value;
        preview.className = iconClass ? `fas ${iconClass} fa-2x` : 'fas fa-leaf fa-2x';
    }

    // Initialize description counter when modal opens
    document.addEventListener('DOMContentLoaded', function() {
        const createCategoryModal = document.getElementById('createCategoryModal');
        if (createCategoryModal) {
            createCategoryModal.addEventListener('show.bs.modal', function() {
                updateCategoryDescriptionCounter();
            });
        }

        const descriptionTextarea = document.querySelector('#createCategoryModal textarea[name="description"]');
        if (descriptionTextarea) {
            descriptionTextarea.addEventListener('input', updateCategoryDescriptionCounter);
            descriptionTextarea.addEventListener('change', updateCategoryDescriptionCounter);
        }
    });
    // Update edit category description character counter
    function updateEditCategoryDescriptionCounter() {
        const textarea = document.querySelector('#editCategoryModal textarea[name="description"]');
        const charCount = document.getElementById('editCategoryCharCount');
        
        if (textarea && charCount) {
            const currentLength = textarea.value.length;
            
            // Update character count only
            charCount.textContent = currentLength;
        }
    }

    // Initialize edit category description counter when modal opens
    document.addEventListener('DOMContentLoaded', function() {
        const editCategoryModal = document.getElementById('editCategoryModal');
        if (editCategoryModal) {
            editCategoryModal.addEventListener('show.bs.modal', function() {
                updateEditCategoryDescriptionCounter();
            });
        }

        const editDescriptionTextarea = document.querySelector('#editCategoryModal textarea[name="description"]');
        if (editDescriptionTextarea) {
            editDescriptionTextarea.addEventListener('input', updateEditCategoryDescriptionCounter);
            editDescriptionTextarea.addEventListener('change', updateEditCategoryDescriptionCounter);
        }
    });

    // Update item description character counter
    function updateItemDescriptionCounter() {
        const textarea = document.querySelector('#createItemModal textarea[name="description"]');
        const charCount = document.getElementById('itemCharCount');
        
        if (textarea && charCount) {
            const currentLength = textarea.value.length;
            
            // Update character count
            charCount.textContent = currentLength;
            
        }
    }

    // Initialize description counter when Add Item modal opens
    document.addEventListener('DOMContentLoaded', function() {
        const createItemModal = document.getElementById('createItemModal');
        if (createItemModal) {
            createItemModal.addEventListener('show.bs.modal', function() {
                updateItemDescriptionCounter();
            });
        }

        const itemDescriptionTextarea = document.querySelector('#createItemModal textarea[name="description"]');
        if (itemDescriptionTextarea) {
            itemDescriptionTextarea.addEventListener('input', updateItemDescriptionCounter);
            itemDescriptionTextarea.addEventListener('change', updateItemDescriptionCounter);
        }
    });
    // Update edit item description character counter
    function updateEditItemDescriptionCounter() {
        const textarea = document.querySelector('#editItemModal textarea[name="description"]');
        const charCount = document.getElementById('editItemCharCount');
        
        if (textarea && charCount) {
            const currentLength = textarea.value.length;
            charCount.textContent = currentLength;
        }
    }

    // Initialize edit item description counter when modal opens
    document.addEventListener('DOMContentLoaded', function() {
        const editItemModal = document.getElementById('editItemModal');
        if (editItemModal) {
            editItemModal.addEventListener('show.bs.modal', function() {
                updateEditItemDescriptionCounter();
            });
        }

        const editDescriptionTextarea = document.querySelector('#editItemModal textarea[name="description"]');
        if (editDescriptionTextarea) {
            editDescriptionTextarea.addEventListener('input', updateEditItemDescriptionCounter);
            editDescriptionTextarea.addEventListener('change', updateEditItemDescriptionCounter);
        }
    });
    // Character counters for supply modal
    document.getElementById('addSupplyForm').addEventListener('input', function(e) {
        if (e.target.name === 'notes') {
            document.getElementById('addSupplyNoteCount').textContent = e.target.value.length;
        }
    });

    document.getElementById('adjustSupplyForm').addEventListener('input', function(e) {
        if (e.target.name === 'reason') {
            document.getElementById('adjustSupplyReasonCount').textContent = e.target.value.length;
        }
    });

    document.getElementById('recordLossForm').addEventListener('input', function(e) {
        if (e.target.name === 'reason') {
            document.getElementById('lossReasonCount').textContent = e.target.value.length;
        }
    });
    </script>

    <style>

        /* Toast Notification Container */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 12px;
            pointer-events: none;
        }

        /* Individual Toast Notification */
        .toast-notification {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            min-width: 380px;
            max-width: 600px;
            overflow: hidden;
            opacity: 0;
            transform: translateX(400px);
            transition: all 0.3s cubic-bezier(0.23, 1, 0.320, 1);
            pointer-events: auto;
        }

        .toast-notification.show {
            opacity: 1;
            transform: translateX(0);
        }

        /* Toast Content */
        .toast-notification .toast-content {
            display: flex;
            align-items: center;
            padding: 20px;
            font-size: 1.05rem;
        }

        .toast-notification .toast-content i {
            font-size: 1.5rem;
        }

        .toast-notification .toast-content span {
            flex: 1;
            color: #333;
        }

        /* Type-specific styles */
        .toast-notification.toast-success {
            border-left: 4px solid #28a745;
        }

        .toast-notification.toast-success .toast-content i {
            color: #28a745;
        }

        .toast-notification.toast-error {
            border-left: 4px solid #dc3545;
        }

        .toast-notification.toast-error .toast-content i {
            color: #dc3545;
        }

        .toast-notification.toast-warning {
            border-left: 4px solid #ffc107;
        }

        .toast-notification.toast-warning .toast-content i {
            color: #ffc107;
        }

        .toast-notification.toast-info {
            border-left: 4px solid #17a2b8;
        }

        .toast-notification.toast-info .toast-content i {
            color: #17a2b8;
        }

        /* Confirmation Toast */
        .confirmation-toast {
            min-width: 420px;
            max-width: 650px;
        }

        .confirmation-toast .toast-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            font-weight: 600;
        }

        .confirmation-toast .toast-body {
            padding: 16px;
            background: #f8f9fa;
        }

        .confirmation-toast .toast-body p {
            margin: 0;
            font-size: 0.95rem;
            color: #333;
            line-height: 1.5;
        }

        .btn-close-toast {
            width: auto;
            height: auto;
            padding: 0;
            font-size: 1.2rem;
            opacity: 0.5;
            transition: opacity 0.2s;
            background: none;
            border: none;
            cursor: pointer;
        }

        .btn-close-toast:hover {
            opacity: 1;
        }

        /* Responsive */
        @media (max-width: 576px) {
            .toast-container {
                top: 10px;
                right: 10px;
                left: 10px;
            }

            .toast-notification,
            .confirmation-toast {
                min-width: auto;
                max-width: 100%;
            }
        }
        /* Metric card styles */
        .metric-card {
            border-radius: 12px;
            overflow: hidden;
        }

        .metric-card .card-body {
            padding: 1.25rem 1rem;
        }

        .metric-icon-circle {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .metric-icon-circle i {
            font-size: 1.5rem;
        }

        .metric-label {
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .metric-value {
            font-size: 2rem;
            font-weight: 700;
            line-height: 1;
            color: #1f2937;
        }

        /* Soft background colors */
        .bg-primary-soft {
            background-color: rgba(13, 110, 253, 0.1);
        }

        .bg-success-soft {
            background-color: rgba(16, 185, 129, 0.1);
        }

        .bg-warning-soft {
            background-color: rgba(245, 158, 11, 0.1);
        }

        .bg-danger-soft {
            background-color: rgba(239, 68, 68, 0.1);
        }

        .bg-purple-soft {
            background-color: rgba(139, 92, 246, 0.1);
        }

        /* Badge soft colors */
        .badge-primary-soft {
            background-color: rgba(13, 110, 253, 0.15);
            color: #0d6efd;
        }

        .badge-success-soft {
            background-color: rgba(16, 185, 129, 0.15);
            color: #10b981;
        }

        /* Card hover effect */
        .metric-card:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1) !important;
        }

        .card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
        }

        /* Table styling with grid lines */
        .table {
            border-collapse: collapse;
        }

        .table td,
        .table th {
            vertical-align: middle;
            border: 1px solid #dee2e6;
        }

        .table thead th {
            border-bottom: 2px solid #dee2e6;
        }

        .table-bordered {
            border: 1px solid #dee2e6;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        .text-xs {
            font-size: 0.75rem;
        }

        /* Category Tab Navigation */
        .category-tabs-nav {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .category-tab-btn {
            background: #ffffff;
            border: 2px solid #e0e0e0;
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            font-weight: 500;
            color: #495057;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
        }

        .category-tab-btn:hover {
            background: #f8f9fa;
            border-color: #0d6efd;
            color: #0d6efd;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .category-tab-btn.active {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            border-color: #0d6efd;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
        }

        .category-tab-btn i {
            font-size: 1rem;
        }

        /* Search Box */
        .search-box {
            min-width: 250px;
        }

        .search-box .input-group {
            border-radius: 50px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .search-box .input-group-text {
            border: 1px solid #dee2e6;
            border-right: none;
        }

        .search-box .form-control {
            border: 1px solid #dee2e6;
            border-left: none;
        }

        .search-box .form-control:focus {
            box-shadow: none;
            border-color: #0d6efd;
        }

        .search-box .input-group-text {
            background: #ffffff;
        }

        /* Category Content Sections */
        .category-content {
            display: none;
        }

        .category-content.active {
            display: block;
        }

        .item-row:hover {
            background-color: #f8f9fa;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .item-row td {
            padding: 1rem 0.75rem;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .category-tabs-nav {
                width: 100%;
                justify-content: center;
                margin-bottom: 1rem;
            }

            .search-box {
                width: auto;
                min-width: 200px;
            }

            .card-body>.d-flex {
                justify-content: flex-end !important;
            }
        }

        @media (max-width: 768px) {
            .category-tabs-nav {
                justify-content: center;
            }

            .search-box {
                width: 100%;
                min-width: auto;
            }

            .table {
                font-size: 0.85rem;
            }

            .category-tab-btn {
                font-size: 0.8rem;
                padding: 0.5rem 1rem;
            }

            .btn-primary {
                width: 100%;
            }
        }

        /* Fix modal z-index issues */
        .modal-backdrop {
            z-index: 1040;
        }

        .modal {
            z-index: 1050;
        }

        .modal.show~.modal {
            z-index: 1060;
        }

        .modal.show~.modal-backdrop {
            z-index: 1055;
        }

        /* Validation styles */
        .was-validated .form-control:invalid,
        .was-validated .form-select:invalid {
            border-color: #dc3545;
            padding-right: calc(1.5em + 0.75rem);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }

        .was-validated .form-control:valid,
        .was-validated .form-select:valid {
            border-color: #198754;
            padding-right: calc(1.5em + 0.75rem);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }

        .invalid-feedback {
            display: none;
            font-size: 0.875em;
            color: #dc3545;
        }

        .was-validated .form-control:invalid~.invalid-feedback,
        .was-validated .form-select:invalid~.invalid-feedback,
        .was-validated textarea:invalid~.invalid-feedback {
            display: block;
        }

        /* Dropdown menu improvements */
        .dropdown-menu {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 0.5rem;
        }

        .dropdown-item {
            padding: 0.5rem 1rem;
        }

        .dropdown-item:hover {
            background-color: #f8f9fa;
        }

        .dropdown-item.text-danger:hover {
            background-color: #fff5f5;
        }

        .dropdown-item i {
            width: 20px;
            text-align: center;
        }

        /* Tooltip custom styles */
        .tooltip-inner {
            max-width: 200px;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }

        /* Button group responsive */
        @media (max-width: 576px) {
            .btn-group-sm>.btn {
                padding: 0.375rem 0.5rem;
                font-size: 0.875rem;
            }
        }

        /* Change Detection Styles */
        .form-changed {
            border-left: 3px solid #ffc107 !important;
            background-color: #fff3cd !important;
            transition: all 0.3s ease;
        }

        .no-changes {
            opacity: 0.7;
            transition: all 0.3s ease;
        }

        /* Change indicator */
        .change-indicator {
            position: relative;
        }

        .change-indicator::after {
            content: "●";
            color: #ffc107;
            font-size: 12px;
            position: absolute;
            right: -15px;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .change-indicator.changed::after {
            opacity: 1;
        }

        #editCategorySubmitBtn {
            transition: all 0.3s ease;
        }

        #editCategorySubmitBtn.no-changes:hover {
            background-color: #6c757d;
            border-color: #6c757d;
        }

     /* Edit Item Modal - Change Detection Styles */
        #editItemModal .form-control.form-changed,
        #editItemModal .form-select.form-changed {
            border-left: 3px solid #ffc107 !important;
            background-color: #fff3cd !important;
            transition: all 0.3s ease;
        }

        #editItemModal .form-control.form-changed:focus,
        #editItemModal .form-select.form-changed:focus {
            border-color: #ffc107 !important;
            box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25) !important;
        }

        #editItemSubmitBtn {
            transition: all 0.3s ease;
        }

        #editItemSubmitBtn.no-changes {
            opacity: 0.7;
            cursor: not-allowed;
        }

        #editItemSubmitBtn.no-changes:hover {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        /* Filters & Search Card */
        .card[id*="filterForm"].shadow {
            border: none;
            border-radius: 8px;
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        .card-header h6 {
            color: #0d6efd;
            font-weight: 600;
            font-size: 1rem;
            letter-spacing: 0.3px;
        }

        .form-select-sm, 
        .form-control-sm {
            height: calc(1.5em + 0.5rem + 2px);
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            border-radius: 6px;
            border: 1px solid #dee2e6;
        }

        .form-select-sm:focus,
        .form-control-sm:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        .input-group-sm .input-group-text {
            border: 1px solid #dee2e6;
            background-color: #ffffff;
        }

        .input-group-sm .form-control {
            border: 1px solid #dee2e6;
        }
        /* Pagination Controls */
        .pagination-controls {
            margin: 2rem 0 1rem 0;
            padding: 1rem;
            background-color: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }

        .pagination-info {
            font-size: 0.95rem;
            color: #495057;
            padding: 0.5rem 1rem;
            background-color: white;
            border-radius: 6px;
            border: 1px solid #dee2e6;
            min-width: 150px;
            text-align: center;
        }

        .pagination-controls .btn-outline-secondary {
            border-color: #dee2e6;
            color: #495057;
            transition: all 0.3s ease;
        }

        .pagination-controls .btn-outline-secondary:hover:not(:disabled) {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: white;
        }

        .pagination-controls .btn-outline-secondary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .pagination-controls .btn-outline-secondary i {
            margin: 0 0.25rem;
        }

        /* Responsive pagination */
        @media (max-width: 768px) {
            .pagination-controls {
                flex-wrap: wrap;
                gap: 0.5rem !important;
            }

            .pagination-controls .btn-sm {
                padding: 0.4rem 0.6rem;
                font-size: 0.8rem;
            }

            .pagination-controls .btn-sm i {
                margin: 0;
            }

            .pagination-controls .btn-sm span {
                display: none;
            }

            .pagination-info {
                min-width: auto;
                flex: 1 1 100%;
                order: -1;
            }
        }

        @media (max-width: 576px) {
            .pagination-controls {
                padding: 0.75rem;
                margin: 1.5rem 0 0.75rem 0;
            }

            .pagination-controls .btn-sm {
                padding: 0.35rem 0.5rem;
                font-size: 0.75rem;
            }

            .pagination-info {
                font-size: 0.85rem;
                padding: 0.4rem 0.6rem;
            }
        }
        /* Create Category Modal Styles */
            #createCategoryModal .form-control,
            #createCategoryModal .form-select {
                border-radius: 8px;
                border: 1px solid #e9ecef;
                transition: all 0.3s ease;
            }

            #createCategoryModal .form-control:focus,
            #createCategoryModal .form-select:focus {
                border-color: #0d6efd;
                box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
            }

            #createCategoryModal .form-label {
                color: #495057;
                font-weight: 500;
                font-size: 0.95rem;
            }

            /* Item Description Counter Styling */
            #itemDescriptionCounter {
                font-weight: 500;
                transition: color 0.3s ease;
            }

            #createItemModal textarea[name="description"] {
                resize: vertical;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                font-size: 0.95rem;
                line-height: 1.5;
            }

            #createItemModal textarea[name="description"]:focus {
                border-color: #0d6efd;
                box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
            }

            /* Character count indicator */
            .text-muted #itemCharCount {
                font-weight: 600;
            }
        /* Text Truncation for Tables */
        .truncate-text {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
            display: block;
        }

        .truncate-text-long {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 200px;
            display: block;
        }

        .truncate-text-medium {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 150px;
            display: block;
        }

        .truncate-text-short {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100px;
            display: block;
        }
/* ================================
   COMPREHENSIVE TEXT OVERFLOW FIXES
   ================================ */

/* Category Name - Primary Fix - AGGRESSIVE */
.category-name {
    display: inline-block !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    max-width: 150px !important;
    width: 150px !important;
    vertical-align: middle;
    flex-shrink: 1 !important;
    min-width: 0 !important;
    line-height: 1.4;
}

/* Span wrapper for category */
h5 .category-name,
h4 .category-name {
    max-width: 150px !important;
    width: 150px !important;
}

/* Item Name in Tables */
.item-name {
    display: block !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    max-width: 100% !important;
    min-width: 0;
}

/* Description Truncation - Works in any context */
.description-truncate {
    display: block !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    width: 100% !important;
    max-width: 100% !important;
    min-width: 0 !important;
    word-break: break-all;
}

/* Table Headers - Fix Truncation */
.table thead th {
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    max-width: 100%;
    padding: 1rem 0.75rem;
    background: #0a58ca;
    color: #ffffff;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #0a58ca;
    word-break: break-word;
}

/* Category Header Container - Fix Wrapping */
.card-header h5,
.card-header h4 {
    display: flex !important;
    align-items: center !important;
    gap: 0.3rem !important;
    flex-wrap: nowrap !important;
    overflow: hidden !important;
    word-break: normal;
    margin: 0 !important;
    width: auto !important;
    min-width: 0 !important;
    flex: 1;
}

/* Card Header with Category Title */
.card-header .d-flex {
    flex-wrap: wrap !important;
    gap: 1rem !important;
}

/* Category description in header */
.card-header small.text-muted {
    display: block !important;
    width: 100% !important;
    white-space: normal !important;
    word-break: break-word !important;
    overflow-wrap: break-word !important;
    max-width: 100% !important;
}

/* Badge spacing fix */
.card-header .badge {
    flex-shrink: 0;
    white-space: nowrap;
}

/* Icon in header */
.card-header i {
    flex-shrink: 0;
}

/* Modal Dialog Text */
.modal-body label,
.modal-body .form-label {
    white-space: normal !important;
    word-wrap: break-word;
    max-width: 100%;
}

/* Form Control Labels */
.form-label {
    display: block;
    word-break: break-word;
    overflow-wrap: break-word;
    max-width: 100%;
}

/* Button Text Fix */
.btn {
    word-break: break-word;
    overflow-wrap: break-word;
    white-space: normal;
    max-width: 100%;
}

.btn span {
    display: inline;
    word-break: break-word;
}

/* Search Input Fix */
.input-group .form-control {
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Select dropdown Fix */
.form-select {
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
}

/* Table Cell Content */
.table td {
    overflow: visible !important;
    word-break: break-word;
    overflow-wrap: break-word;
}

.table td strong {
    display: block;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    max-width: 100%;
    min-width: 0;
}

.table td small {
    display: block;
    width: 100%;
    min-width: 0;
}

.table td small.description-truncate {
    display: block !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    width: 100% !important;
    max-width: 100% !important;
    min-width: 0 !important;
}

/* Tooltip Text */
.tooltip-inner {
    max-width: 250px;
    word-break: break-word;
    white-space: normal !important;
    text-align: left;
    overflow-wrap: break-word;
}

/* Alert Messages */
.alert {
    word-break: break-word;
    overflow-wrap: break-word;
    overflow: hidden;
    max-width: 100%;
}

/* Toast Notifications */
.toast-notification .toast-content span {
    word-break: break-word;
    overflow-wrap: break-word;
    overflow: hidden;
    max-width: 100%;
}

.confirmation-toast .toast-body p {
    word-break: break-word;
    overflow-wrap: break-word;
    white-space: pre-wrap;
    max-width: 100%;
}

/* Card Title Fix */
.card-title,
.modal-title {
    word-break: break-word;
    overflow-wrap: break-word;
    max-width: 100%;
}

/* Paragraph Text */
p, small, span {
    word-break: break-word;
    overflow-wrap: break-word;
}

/* ================================
   RESPONSIVE ADJUSTMENTS
   ================================ */

/* Large Screens (lg) */
@media (min-width: 1200px) {
    .category-name {
        max-width: 300px !important;
    }
    
    .item-name {
        max-width: 200px !important;
    }
    
    .description-truncate {
        max-width: 150px !important;
    }
}

/* Medium Screens (md) */
@media (max-width: 1199px) {
    .category-name {
        max-width: 180px !important;
        width: 180px !important;
    }
    
    .item-name {
        max-width: 160px !important;
    }
    
    .description-truncate {
        max-width: 120px !important;
    }

    .card-header h5,
    .card-header h4 {
        flex-direction: row !important;
        align-items: center !important;
    }
}

/* Small Screens (sm) */
@media (max-width: 768px) {
    .category-name {
        max-width: 120px !important;
        width: 120px !important;
    }
    
    .item-name {
        max-width: 120px !important;
    }
    
    .description-truncate {
        max-width: 80px !important;
    }

    .card-header {
        flex-direction: column;
        align-items: flex-start !important;
    }

    .card-header h5,
    .card-header h4 {
        flex-direction: row !important;
        width: 100%;
    }

    .card-header .d-flex {
        flex-direction: column !important;
        align-items: stretch !important;
        width: 100%;
    }

    .btn {
        white-space: normal !important;
        word-break: break-word;
    }

    .modal-body {
        word-break: break-word;
        overflow-wrap: break-word;
    }

    .table {
        font-size: 0.85rem;
    }

    .table td {
        padding: 0.75rem 0.5rem !important;
    }
}

/* Extra Small Screens (xs) */
@media (max-width: 576px) {
    .category-name {
        max-width: 100px !important;
    }
    
    .item-name {
        max-width: 90px !important;
    }
    
    .description-truncate {
        max-width: 60px !important;
    }

    .table {
        font-size: 0.75rem;
    }

    .table td, 
    .table th {
        padding: 0.5rem 0.25rem !important;
        font-size: 0.7rem;
    }

    .table thead th {
        font-size: 0.65rem !important;
    }

    .btn-sm {
        font-size: 0.65rem !important;
        padding: 0.25rem 0.5rem !important;
        white-space: normal;
    }

    .card-header {
        padding: 0.75rem !important;
    }

    .card-header h5,
    .card-header h4 {
        font-size: 0.95rem !important;
        margin-bottom: 0.5rem !important;
        flex-wrap: nowrap;
    }

    .badge {
        font-size: 0.6rem !important;
        padding: 0.25rem 0.4rem !important;
        white-space: nowrap;
    }

    .toast-notification,
    .confirmation-toast {
        min-width: 95vw !important;
        max-width: 95vw !important;
    }

    .modal-dialog {
        margin: 0.5rem !important;
    }

    .form-label {
        font-size: 0.9rem;
    }

    .modal-title {
        font-size: 1.1rem;
    }

    /* Ensure buttons don't overflow */
    .btn-group,
    .dropdown-menu {
        max-width: 100%;
        overflow: hidden;
    }
}

/* Ultra Small Screens (xs and smaller) */
@media (max-width: 360px) {
    .category-name {
        max-width: 80px !important;
    }
    
    .item-name {
        max-width: 70px !important;
    }
    
    .description-truncate {
        max-width: 50px !important;
    }

    .badge {
        font-size: 0.55rem !important;
        padding: 0.2rem 0.3rem !important;
    }

    .table {
        font-size: 0.65rem;
    }

    .btn-sm {
        font-size: 0.6rem !important;
        padding: 0.2rem 0.4rem !important;
    }
}
    /* Supply Management Modal Header - Centered */
    #supplyModal .modal-header {
        justify-content: center !important;
        text-align: center !important;
        position: relative;
    }

    #supplyModal .modal-title {
        flex: 1;
        text-align: center;
        font-weight: 600;
        font-size: 1.3rem;
        color: #ffffff;
    }

    #supplyModal .modal-header .btn-close {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        margin: 0;
    }

    /* Ensure the header has proper styling */
    #supplyModal .modal-header {
        background: linear-gradient(135deg, #198754 0%, #146c43 100%);
        border: none;
        padding: 1.25rem;
    }
    </style>
@endsection