{{-- resources/views/admin/seedlings/categories/index.blade.php --}}

@extends('layouts.app')

@section('title', 'Manage Categories & Supply')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-layer-group me-2"></i>Manage Categories & Supply</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
            <i class="fas fa-plus me-2"></i>Add Category
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Supply Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">Total Items</div>
                            <div class="h5 mb-0 fw-bold">{{ $totalItems }}</div>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-boxes fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">Total Supply</div>
                            <div class="h5 mb-0 fw-bold">{{ number_format($totalSupply) }}</div>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-warehouse fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">Low Supply</div>
                            <div class="h5 mb-0 fw-bold">{{ $lowSupplyItems }}</div>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-xs fw-bold text-danger text-uppercase mb-1">Out of Supply</div>
                            <div class="h5 mb-0 fw-bold">{{ $outOfSupplyItems }}</div>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-times-circle fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Categories Grid -->
    <div class="row">
        @foreach($categories as $category)
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">
                                <i class="fas {{ $category->icon ?? 'fa-leaf' }} me-2"></i>
                                {{ $category->display_name }}
                                @if(!$category->is_active)
                                    <span class="badge bg-warning">Inactive</span>
                                @endif
                            </h5>
                            <small class="text-muted">{{ $category->description }}</small>
                        </div>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-primary" onclick="editCategory({{ $category->id }})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-{{ $category->is_active ? 'warning' : 'success' }}" 
                                    onclick="toggleCategory({{ $category->id }})">
                                <i class="fas fa-power-off"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" 
                                    data-bs-target="#createItemModal" onclick="setItemCategory({{ $category->id }})">
                                <i class="fas fa-plus"></i> Item
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteCategory({{ $category->id }})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($category->items->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
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
                                        @foreach($category->items->sortBy('name') as $item)
                                            <tr>
                                                <td style="width: 70px;">
                                                    @if($item->image_path)
                                                        <img src="{{ Storage::url($item->image_path) }}" 
                                                             alt="{{ $item->name }}" 
                                                             class="rounded" 
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
                                                    @if($item->needsReorder())
                                                        <br><small class="text-warning"><i class="fas fa-exclamation-triangle"></i> Needs Reorder</small>
                                                    @endif
                                                </td>
                                                <td><span class="badge bg-secondary">{{ $item->unit }}</span></td>
                                                <td>
                                                    <span class="badge bg-{{ $item->supply_status_color }}">
                                                        {{ $item->current_supply }}
                                                    </span>
                                                    @if($item->reorder_point)
                                                        <br><small class="text-muted">Min: {{ $item->reorder_point }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $item->is_active ? 'success' : 'danger' }}">
                                                        {{ $item->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-outline-success" 
                                                                onclick="manageSupply({{ $item->id }})" 
                                                                title="Manage Supply">
                                                            <i class="fas fa-warehouse"></i>
                                                        </button>
                                                        <button class="btn btn-outline-primary" 
                                                                onclick="editItem({{ $item->id }})">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-outline-{{ $item->is_active ? 'warning' : 'success' }}" 
                                                                onclick="toggleItem({{ $item->id }})">
                                                            <i class="fas fa-power-off"></i>
                                                        </button>
                                                        <button class="btn btn-outline-danger" 
                                                                onclick="deleteItem({{ $item->id }})">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
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

    <!-- Error Modal -->
    <div class="modal fade" id="errorModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-circle me-2"></i>Error
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="errorMessage" class="mb-0"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-check-circle me-2"></i>Success
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="successMessage" class="mb-0"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="location.reload()">OK</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Category Modal -->
<div class="modal fade" id="createCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createCategoryForm" novalidate>
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name *</label>
                        <input type="text" name="name" class="form-control" required>
                        <small class="text-muted">Internal name (lowercase, no spaces)</small>
                        <div class="invalid-feedback">Please provide a category name.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Display Name *</label>
                        <input type="text" name="display_name" class="form-control" required>
                        <div class="invalid-feedback">Please provide a display name.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Icon *</label>
                        <select name="icon" id="create_icon" class="form-select" required onchange="updateIconPreview('create')">
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
                            <i id="create_icon_preview" class="fas fa-leaf fa-2x"></i>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
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
                        <input type="text" id="edit_category_display_name" name="display_name" class="form-control" required>
                        <div class="invalid-feedback">Please provide a display name.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Icon *</label>
                        <select name="icon" id="edit_icon" class="form-select" required onchange="updateIconPreview('edit')">
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
                            <input type="number" name="current_supply" class="form-control" value="0" min="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Minimum Supply</label>
                            <input type="number" name="minimum_supply" class="form-control" value="0" min="0">
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
                            <input type="number" id="edit_item_minimum_supply" name="minimum_supply" class="form-control" value="0" min="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Maximum Supply</label>
                            <input type="number" id="edit_item_maximum_supply" name="maximum_supply" class="form-control" min="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reorder Point</label>
                        <input type="number" id="edit_item_reorder_point" name="reorder_point" class="form-control" min="0">
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
                                        <input type="number" name="quantity" class="form-control form-control-sm" required min="1">
                                        <div class="invalid-feedback">Please enter a quantity.</div>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small">Source</label>
                                        <input type="text" name="source" class="form-control form-control-sm" placeholder="e.g., Supplier name">
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
                                        <input type="number" name="new_supply" class="form-control form-control-sm" required min="0">
                                        <div class="invalid-feedback">Please enter new supply amount.</div>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small">Reason *</label>
                                        <textarea name="reason" class="form-control form-control-sm" rows="3" required placeholder="Explain the adjustment..."></textarea>
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
                                        <input type="number" name="quantity" class="form-control form-control-sm" required min="1">
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
                                        <textarea name="reason" class="form-control form-control-sm" rows="2" required placeholder="Additional details..."></textarea>
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

// Show error modal (closes any open modal first)
function showError(message) {
    // Close all open modals first
    const openModals = document.querySelectorAll('.modal.show');
    openModals.forEach(modal => {
        const bsModal = bootstrap.Modal.getInstance(modal);
        if (bsModal) bsModal.hide();
    });
    
    // Small delay to ensure previous modal is closed
    setTimeout(() => {
        document.getElementById('errorMessage').textContent = message;
        const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
        errorModal.show();
    }, 300);
}

// Show success modal (closes any open modal first)
function showSuccess(message, shouldReload = true) {
    // Close all open modals first
    const openModals = document.querySelectorAll('.modal.show');
    openModals.forEach(modal => {
        const bsModal = bootstrap.Modal.getInstance(modal);
        if (bsModal) bsModal.hide();
    });
    
    // Small delay to ensure previous modal is closed
    setTimeout(() => {
        document.getElementById('successMessage').textContent = message;
        const successModal = new bootstrap.Modal(document.getElementById('successModal'));
        successModal.show();
        
        if (shouldReload) {
            document.getElementById('successModal').addEventListener('hidden.bs.modal', function() {
                location.reload();
            }, { once: true });
        }
    }, 300);
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
        const category = await makeRequest(`/admin/seedlings/categories/${categoryId}`, {
            method: 'GET',
            headers: {'X-CSRF-TOKEN': csrfToken}
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
    
    try {
        const data = await makeRequest(`/admin/seedlings/categories/${categoryId}`, {
            method: 'POST',
            body: formData,
            headers: {'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json'}
        });
        showSuccess(data.message);
    } catch (error) {
        showError(error.message);
    }
});

async function toggleCategory(categoryId) {
    if (!confirm('Toggle category status?')) return;
    try {
        const data = await makeRequest(`/admin/seedlings/categories/${categoryId}/toggle`, {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json'}
        });
        showSuccess(data.message);
    } catch (error) {
        showError(error.message);
    }
}

async function deleteCategory(categoryId) {
    if (!confirm('Delete this category permanently?')) return;
    try {
        const data = await makeRequest(`/admin/seedlings/categories/${categoryId}`, {
            method: 'DELETE',
            headers: {'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json'}
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
            headers: {'X-CSRF-TOKEN': csrfToken}
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
    logsDiv.innerHTML = '<div class="text-center py-3"><div class="spinner-border spinner-border-sm"></div></div>';
    
    try {
        const data = await makeRequest(`/admin/seedlings/items/${itemId}/supply/logs`, {
            method: 'GET',
            headers: {'X-CSRF-TOKEN': csrfToken}
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
    
    try {
        const data = await makeRequest('/admin/seedlings/categories', {
            method: 'POST',
            body: formData,
            headers: {'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json'}
        });
        showSuccess(data.message);
    } catch (error) {
        showError(error.message);
    }
});

document.getElementById('createItemForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    if (!validateForm(this)) {
        return;
    }
    
    const formData = new FormData(this);
    
    try {
        const data = await makeRequest('/admin/seedlings/items', {
            method: 'POST',
            body: formData,
            headers: {'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json'}
        });
        showSuccess(data.message);
    } catch (error) {
        if (error.message.includes('name')) {
            showError('An item with this name already exists in this category. Please use a different name.');
        } else {
            showError(error.message);
        }
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
        
        // Show success message without closing modal
        const toast = document.createElement('div');
        toast.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
        toast.style.zIndex = '9999';
        toast.innerHTML = `
            ${data.message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
        
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
        
        const toast = document.createElement('div');
        toast.className = 'alert alert-warning alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
        toast.style.zIndex = '9999';
        toast.innerHTML = `
            ${data.message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
        
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
        
        const toast = document.createElement('div');
        toast.className = 'alert alert-danger alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
        toast.style.zIndex = '9999';
        toast.innerHTML = `
            ${data.message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
        
        // Update display without reopening modal
        document.getElementById('supply_current').textContent = data.new_supply || data.current_supply;
        loadSupplyLogs(itemId);
        this.reset();
        this.classList.remove('was-validated');
    } catch (error) {
        showError(error.message);
    }
});

async function toggleItem(itemId) {
    if (!confirm('Toggle item status?')) return;
    try {
        const data = await makeRequest(`/admin/seedlings/items/${itemId}/toggle`, {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json'}
        });
        showSuccess(data.message);
    } catch (error) {
        showError(error.message);
    }
}

async function deleteItem(itemId) {
    if (!confirm('Delete this item permanently?')) return;
    try {
        const data = await makeRequest(`/admin/seedlings/items/${itemId}`, {
            method: 'DELETE',
            headers: {'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json'}
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
            headers: {'X-CSRF-TOKEN': csrfToken}
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
    
    try {
        const data = await makeRequest(`/admin/seedlings/items/${itemId}`, {
            method: 'POST',
            body: formData,
            headers: {'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json'}
        });
        showSuccess(data.message);
    } catch (error) {
        showError(error.message);
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
</script>

<style>
.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
}

.table td {
    vertical-align: middle;
}

.text-xs {
    font-size: 0.75rem;
}

/* Fix modal z-index issues */
.modal-backdrop {
    z-index: 1040;
}

.modal {
    z-index: 1050;
}

.modal.show ~ .modal {
    z-index: 1060;
}

.modal.show ~ .modal-backdrop {
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

.was-validated .form-control:invalid ~ .invalid-feedback,
.was-validated .form-select:invalid ~ .invalid-feedback,
.was-validated textarea:invalid ~ .invalid-feedback {
    display: block;
}
</style>
@endsection