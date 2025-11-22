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

        <!-- Category Navigation Tabs -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
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
                    <div class="d-flex gap-2 align-items-center flex-wrap"  style="margin-left: auto;">
                        <div class="search-box">
                            <div class="input-group">
                                <span class="input-group-text bg-white">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" class="form-control" id="itemSearch" placeholder="Search items..."
                                    onkeyup="searchItems()">
                            </div>
                        </div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCategoryModal"
                            style="white-space: nowrap;">
                            <i class="fas fa-plus me-2"></i>Add Category
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
                                        {{ $category->display_name }}
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
                                                        <td>
                                                            <strong>{{ $item->name }}</strong>
                                                            @if ($item->needsReorder())
                                                                <br><small class="text-warning"><i
                                                                        class="fas fa-exclamation-triangle"></i> Needs
                                                                    Reorder</small>
                                                            @endif
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
                                {{ $category->display_name }}
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
                                                <td>
                                                    <strong class="d-block">{{ $item->name }}</strong>
                                                    @if ($item->needsReorder())
                                                        <small class="text-warning">
                                                            <i class="fas fa-exclamation-triangle"></i> Needs Reorder
                                                        </small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <small class="text-muted">
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

    <!-- Create Category Modal -->
    <div class="modal fade" id="createCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-0 d-flex justify-content-center">
                    <h5 class="modal-title">Create New Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" style="position: absolute; right: 1rem;"></button>
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
                            <textarea name="description" class="form-control" rows="2" required></textarea>
                            <div class="invalid-feedback">Please provide a description.</div>
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
                <div class="modal-header">
                    <h5 class="modal-title">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editCategoryForm" novalidate>
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_category_id" name="category_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name *</label>
                            <input type="text" id="edit_category_name" name="name" class="form-control" required>
                            <small class="text-muted">Internal name (lowercase, no spaces)</small>
                            <div class="invalid-feedback">Please provide a category name.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Display Name *</label>
                            <input type="text" id="edit_category_display_name" name="display_name"
                                class="form-control" required>
                            <div class="invalid-feedback">Please provide a display name.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Icon *</label>
                            <select name="icon" id="edit_icon" class="form-select" required
                                onchange="updateIconPreview('edit')">
                                <option value="">Select an icon...</option>
                                <option value="fa-seedling">🌱 Seedling</option>
                                <option value="fa-leaf">🍃 Leaf</option>
                                <option value="fa-tree">🌲 Tree</option>
                                <option value="fa-spa">🌿 Herbs/Spa</option>
                                <option value="fa-cannabis">🌿 Cannabis/Plant</option>
                                <option value="fa-pepper-hot">🌶️ Pepper</option>
                                <option value="fa-carrot">🥕 Carrot/Vegetable</option>
                                <option value="fa-apple-alt">🍎 Apple/Fruit</option>
                                <option value="fa-lemon">🍋 Lemon/Citrus</option>
                                <option value="fa-wheat-awn">🌾 Wheat/Grain/Corn</option>
                                <option value="fa-flask">🧪 Flask/Chemical</option>
                                <option value="fa-tint">💧 Tint/Water</option>
                                <option value="fa-sun">☀️ Sun</option>
                                <option value="fa-cloud-rain">🌧️ Rain</option>
                                <option value="fa-hand-holding-heart">💚 Hand Holding Heart</option>
                                <option value="fa-tractor">🚜 Tractor/Farm</option>
                                <option value="fa-warehouse">🏭 Warehouse</option>
                                <option value="fa-tools">🔧 Tools</option>
                                <option value="fa-person-digging">🔨 Shovel</option>
                                <option value="fa-recycle">♻️ Recycle</option>
                                <option value="fa-boxes">📦 Boxes</option>
                                <option value="fa-box-open">📤 Box Open</option>
                            </select>
                            <div class="invalid-feedback">Please select an icon.</div>
                            <div class="mt-2">
                                <small class="text-muted">Preview: </small>
                                <i id="edit_icon_preview" class="fas fa-leaf fa-2x"></i>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea id="edit_category_description" name="description" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Create Item Modal -->
    <div class="modal fade" id="createItemModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="createItemForm" enctype="multipart/form-data" novalidate>
                    @csrf
                    <input type="hidden" id="item_category_id" name="category_id" required>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Name *</label>
                                <input type="text" name="name" class="form-control" required>
                                <div class="invalid-feedback">Please provide an item name.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Unit *</label>
                                <select name="unit" class="form-select" required>
                                    <option value="">Select unit...</option>
                                    <option value="pcs" selected>Pieces (pcs)</option>
                                    <option value="kg">Kilogram (kg)</option>
                                    <option value="L">Liter (L)</option>
                                    <option value="pack">Pack</option>
                                    <option value="bag">Bag</option>
                                </select>
                                <div class="invalid-feedback">Please select a unit.</div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="2"></textarea>
                        </div>

                        <hr>
                        <h6 class="text-primary"><i class="fas fa-warehouse me-2"></i>Supply Management</h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Initial Supply</label>
                                <input type="number" name="current_supply" class="form-control" value="0"
                                    min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Minimum Supply</label>
                                <input type="number" name="minimum_supply" class="form-control" value="0"
                                    min="0">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Maximum Supply</label>
                                <input type="number" name="maximum_supply" class="form-control" min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Reorder Point</label>
                                <input type="number" name="reorder_point" class="form-control" min="0">
                                <small class="text-muted">Alert when supply reaches this level</small>
                            </div>
                        </div>

                        <hr>
                        <h6 class="text-primary"><i class="fas fa-image me-2"></i>Item Image</h6>

                        <div class="mb-3">
                            <label class="form-label">Image</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <small class="text-muted">Recommended: 300x300px, max 2MB</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Edit Item Modal -->
    <div class="modal fade" id="editItemModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editItemForm" enctype="multipart/form-data" novalidate>
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_item_id" name="item_id">
                    <input type="hidden" id="edit_item_category_id" name="category_id" required>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Name *</label>
                                <input type="text" id="edit_item_name" name="name" class="form-control" required>
                                <div class="invalid-feedback">Please provide an item name.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Unit *</label>
                                <select id="edit_item_unit" name="unit" class="form-select" required>
                                    <option value="">Select unit...</option>
                                    <option value="pcs">Pieces (pcs)</option>
                                    <option value="kg">Kilogram (kg)</option>
                                    <option value="L">Liter (L)</option>
                                    <option value="pack">Pack</option>
                                    <option value="bag">Bag</option>
                                </select>
                                <div class="invalid-feedback">Please select a unit.</div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea id="edit_item_description" name="description" class="form-control" rows="2"></textarea>
                        </div>

                        <hr>
                        <h6 class="text-primary"><i class="fas fa-warehouse me-2"></i>Supply Settings</h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Minimum Supply</label>
                                <input type="number" id="edit_item_minimum_supply" name="minimum_supply"
                                    class="form-control" value="0" min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Maximum Supply</label>
                                <input type="number" id="edit_item_maximum_supply" name="maximum_supply"
                                    class="form-control" min="0">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Reorder Point</label>
                            <input type="number" id="edit_item_reorder_point" name="reorder_point" class="form-control"
                                min="0">
                            <small class="text-muted">Alert when supply reaches this level</small>
                        </div>

                        <hr>
                        <h6 class="text-primary"><i class="fas fa-image me-2"></i>Item Image</h6>

                        <div class="mb-3">
                            <div id="current_image_preview" class="mb-2"></div>
                            <label class="form-label">Change Image</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <small class="text-muted">Recommended: 300x300px, max 2MB</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Supply Management Modal -->
    <div class="modal fade" id="supplyModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fas fa-warehouse me-2"></i>Supply Management</h5>
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
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-header bg-success text-white">
                                    <i class="fas fa-arrow-up me-2"></i>Add Supply
                                </div>
                                <div class="card-body">
                                    <form id="addSupplyForm" novalidate>
                                        <input type="hidden" id="add_supply_item_id" name="item_id">
                                        <div class="mb-2">
                                            <label class="form-label small">Quantity *</label>
                                            <input type="number" name="quantity" class="form-control form-control-sm"
                                                required min="1">
                                            <div class="invalid-feedback">Please enter a quantity.</div>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small">Source</label>
                                            <input type="text" name="source" class="form-control form-control-sm"
                                                placeholder="e.g., Supplier name">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small">Notes</label>
                                            <textarea name="notes" class="form-control form-control-sm" rows="2"></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-success btn-sm w-100">
                                            <i class="fas fa-plus-circle me-1"></i>Add Supply
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-header bg-warning text-dark">
                                    <i class="fas fa-edit me-2"></i>Adjust Supply
                                </div>
                                <div class="card-body">
                                    <form id="adjustSupplyForm" novalidate>
                                        <input type="hidden" id="adjust_supply_item_id" name="item_id">
                                        <div class="mb-2">
                                            <label class="form-label small">New Supply *</label>
                                            <input type="number" name="new_supply" class="form-control form-control-sm"
                                                required min="0">
                                            <div class="invalid-feedback">Please enter new supply amount.</div>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small">Reason *</label>
                                            <textarea name="reason" class="form-control form-control-sm" rows="3" required
                                                placeholder="Explain the adjustment..."></textarea>
                                            <div class="invalid-feedback">Please provide a reason for adjustment.</div>
                                        </div>
                                        <button type="submit" class="btn btn-warning btn-sm w-100">
                                            <i class="fas fa-sync-alt me-1"></i>Adjust Supply
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-header bg-danger text-white">
                                    <i class="fas fa-exclamation-triangle me-2"></i>Record Loss
                                </div>
                                <div class="card-body">
                                    <form id="recordLossForm" novalidate>
                                        <input type="hidden" id="loss_supply_item_id" name="item_id">
                                        <div class="mb-2">
                                            <label class="form-label small">Quantity Lost *</label>
                                            <input type="number" name="quantity" class="form-control form-control-sm"
                                                required min="1">
                                            <div class="invalid-feedback">Please enter quantity lost.</div>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small">Reason *</label>
                                            <select name="reason_type" class="form-select form-select-sm mb-2" required>
                                                <option value="">Select reason type...</option>
                                                <option value="Expired">Expired</option>
                                                <option value="Damaged">Damaged</option>
                                                <option value="Lost">Lost</option>
                                                <option value="Other">Other</option>
                                            </select>
                                            <textarea name="reason" class="form-control form-control-sm" rows="2" required
                                                placeholder="Additional details..."></textarea>
                                            <div class="invalid-feedback">Please provide a reason.</div>
                                        </div>
                                        <button type="submit" class="btn btn-danger btn-sm w-100">
                                            <i class="fas fa-minus-circle me-1"></i>Record Loss
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Supply Logs -->
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-history me-2"></i>Recent Supply Movements
                        </div>
                        <div class="card-body p-0">
                            <div id="supply_logs" style="max-height: 300px; overflow-y: auto;">
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

        // Category Tab Switching
        function switchCategory(categoryId, event) {
            // Update active tab button
            document.querySelectorAll('.category-tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.closest('.category-tab-btn').classList.add('active');

            // Hide all category content
            document.querySelectorAll('.category-content').forEach(content => {
                content.classList.remove('active');
            });

            // Show selected category content
            const targetContent = document.getElementById(`category-${categoryId}`);
            if (targetContent) {
                targetContent.classList.add('active');
            }

            // Clear search
            document.getElementById('itemSearch').value = '';
            searchItems();
        }

        // Search Items
        function searchItems() {
            const searchTerm = document.getElementById('itemSearch').value.toLowerCase();
            const activeContent = document.querySelector('.category-content.active');

            if (!activeContent) return;

            const rows = activeContent.querySelectorAll('.item-row');
            let visibleCount = 0;

            rows.forEach(row => {
                // Get item name from <strong> tag or direct text
                const nameElement = row.querySelector('strong') || row.querySelector('td:nth-child(2)');
                const name = nameElement ? nameElement.textContent.toLowerCase() : '';

                // Get description - may be in small.text-muted (individual view) or not present (all view)
                const descriptionElements = row.querySelectorAll('small.text-muted');
                let description = '';
                descriptionElements.forEach(el => {
                    // Skip reorder warnings and min/max text
                    if (!el.textContent.includes('Needs Reorder') &&
                        !el.textContent.includes('Min:') &&
                        !el.textContent.includes('Max:') &&
                        !el.textContent.includes('Reorder:')) {
                        description += el.textContent.toLowerCase() + ' ';
                    }
                });

                // Also check unit and supply values
                const unit = row.querySelector('.badge.bg-secondary')?.textContent.toLowerCase() || '';

                if (name.includes(searchTerm) || description.includes(searchTerm) || unit.includes(searchTerm)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // If in "All Categories" view, hide category cards with no visible items
            if (activeContent.id === 'category-all') {
                const categoryCards = activeContent.querySelectorAll('.col-md-6');
                categoryCards.forEach(card => {
                    const visibleRows = card.querySelectorAll(
                        '.item-row[style=""], .item-row:not([style*="display: none"])');
                    const actualVisibleRows = Array.from(card.querySelectorAll('.item-row')).filter(row => row.style
                        .display !== 'none');

                    if (actualVisibleRows.length === 0 && searchTerm) {
                        card.style.display = 'none';
                    } else {
                        card.style.display = '';
                    }
                });
            }

            // Handle empty state for search
            const tables = activeContent.querySelectorAll('table');
            const hasVisibleItems = visibleCount > 0;

            if (!hasVisibleItems && searchTerm && tables.length > 0) {
                // Show no results message
                if (!activeContent.querySelector('.no-results-message')) {
                    const noResults = document.createElement('div');
                    noResults.className = 'no-results-message text-center py-5';
                    noResults.innerHTML = `
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No items found</h5>
                <p class="text-muted">Try adjusting your search terms.</p>
            `;

                    // Add after the row container in All view, or after table in single view
                    if (activeContent.id === 'category-all') {
                        const rowContainer = activeContent.querySelector('.row');
                        if (rowContainer) {
                            rowContainer.after(noResults);
                            rowContainer.style.display = 'none';
                        }
                    } else {
                        const table = activeContent.querySelector('table');
                        if (table) {
                            table.parentElement.after(noResults);
                            table.parentElement.style.display = 'none';
                        }
                    }
                }
            } else {
                const noResults = activeContent.querySelector('.no-results-message');
                if (noResults) {
                    noResults.remove();

                    // Restore visibility
                    if (activeContent.id === 'category-all') {
                        const rowContainer = activeContent.querySelector('.row');
                        if (rowContainer) rowContainer.style.display = '';
                    } else {
                        const table = activeContent.querySelector('table');
                        if (table) table.parentElement.style.display = '';
                    }
                }
            }
        }

        // Show error modal (closes any open modal first)
       function showError(message) {
            showToast('error', message);
        }

        // Show success modal (closes any open modal first)
        function showSuccess(message, shouldReload = true) {
            showToast('success', message);
            
            if (shouldReload) {
                setTimeout(() => {
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

        async function editCategory(categoryId) {
            try {
                const category = await makeRequest(`/admin/seedlings/supply-management/${categoryId}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                // Populate form fields
                document.getElementById('edit_category_id').value = category.id;
                document.getElementById('edit_category_name').value = category.name;
                document.getElementById('edit_category_display_name').value = category.display_name;
                document.getElementById('edit_icon').value = category.icon || 'fa-leaf';
                document.getElementById('edit_category_description').value = category.description || '';

                // Update icon preview
                updateIconPreview('edit');

                // Reset validation
                document.getElementById('editCategoryForm').classList.remove('was-validated');

                // Show modal
                new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
            } catch (error) {
                showError('Error loading category: ' + error.message);
            }
        }

        document.getElementById('editCategoryForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        if (!validateForm(this)) {
            return;
        }

        const categoryId = document.getElementById('edit_category_id').value;
        const formData = new FormData(this);
        const submitBtn = document.querySelector('#editCategoryModal .btn-primary');
        const originalText = submitBtn.innerHTML;

        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Updating...';
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
        } finally {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    });

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
        document.getElementById('addSupplyForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            if (!validateForm(this)) {
                return;
            }

            const itemId = document.getElementById('add_supply_item_id').value;
            const formData = new FormData(this);

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

                // Show success message using toast
                showToast('success', data.message);

                // Update display without reopening modal
                document.getElementById('supply_current').textContent = data.new_supply || data.current_supply;
                loadSupplyLogs(itemId);
                this.reset();
                this.classList.remove('was-validated');
            } catch (error) {
                showError(error.message);
            }
        });

        document.getElementById('adjustSupplyForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            if (!validateForm(this)) {
                return;
            }

            const itemId = document.getElementById('adjust_supply_item_id').value;
            const formData = new FormData(this);

            if (!confirm('Are you sure you want to adjust the supply manually?')) return;

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

                // Show success message using toast
                showToast('success', data.message);

                // Update display without reopening modal
                document.getElementById('supply_current').textContent = data.new_supply || data.current_supply;
                loadSupplyLogs(itemId);
                this.reset();
                this.classList.remove('was-validated');
            } catch (error) {
                showError(error.message);
            }
        });

        document.getElementById('recordLossForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            if (!validateForm(this)) {
                return;
            }

            const itemId = document.getElementById('loss_supply_item_id').value;
            const formData = new FormData(this);
            const reasonType = formData.get('reason_type');
            const reason = formData.get('reason');
            const fullReason = `${reasonType}: ${reason}`;

            if (!confirm('Are you sure you want to record this supply loss?')) return;

            try {
                const data = await makeRequest(`/admin/seedlings/items/${itemId}/supply/loss`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        quantity: formData.get('quantity'),
                        reason: fullReason
                    })
                });

                // Show success message using toast
                showToast('success', data.message);

                // Update display without reopening modal
                document.getElementById('supply_current').textContent = data.new_supply || data.current_supply;
                loadSupplyLogs(itemId);
                this.reset();
                this.classList.remove('was-validated');
            } catch (error) {
                showError(error.message);
            }
        });
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

        async function editItem(itemId) {
            try {
                const item = await makeRequest(`/admin/seedlings/items/${itemId}`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                // Populate form fields
                document.getElementById('edit_item_id').value = item.id;
                document.getElementById('edit_item_category_id').value = item.category_id;
                document.getElementById('edit_item_name').value = item.name;
                document.getElementById('edit_item_unit').value = item.unit;
                document.getElementById('edit_item_description').value = item.description || '';
                document.getElementById('edit_item_minimum_supply').value = item.minimum_supply || 0;
                document.getElementById('edit_item_maximum_supply').value = item.maximum_supply || '';
                document.getElementById('edit_item_reorder_point').value = item.reorder_point || '';

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

                // Show modal
                new bootstrap.Modal(document.getElementById('editItemModal')).show();
            } catch (error) {
                showError('Error loading item: ' + error.message);
            }
        }

        document.getElementById('editItemForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            if (!validateForm(this)) {
                return;
            }

            const itemId = document.getElementById('edit_item_id').value;
            const formData = new FormData(this);
            const submitBtn = document.querySelector('#editItemModal .btn-primary');
            const originalText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Updating...';
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
            } finally {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        });

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

        /* Table Improvements */
        .table thead th {
            background: #f8f9fa;
            color: #495057;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #dee2e6;
            padding: 1rem 0.75rem;
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
    </style>
@endsection
