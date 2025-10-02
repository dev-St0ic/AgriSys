{{-- resources/views/admin/seedlings/categories.blade.php --}}

@extends('layouts.app')

@section('title', 'Manage Seedling Categories & Items')

@section('content')
<div class="container-fluid">
     <!-- Back Button Here -->
    <div class="mb-3">
        <a href="{{ route('admin.seedlings.requests') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Seedling Requests
        </a>
    </div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-layer-group me-2"></i>Manage Categories & Items</h2>
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
                                                <td>{{ $item->name }}</td>
                                                <td><span class="badge bg-secondary">{{ $item->unit }}</span></td>
                                                <td>
                                                    <span class="badge bg-{{ $item->is_active ? 'success' : 'danger' }}">
                                                        {{ $item->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-outline-primary" onclick="editItem({{ $item->id }})">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-outline-{{ $item->is_active ? 'warning' : 'success' }}" 
                                                                onclick="toggleItem({{ $item->id }})">
                                                            <i class="fas fa-power-off"></i>
                                                        </button>
                                                        <button class="btn btn-outline-danger" onclick="deleteItem({{ $item->id }})">
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
</div>

<!-- Create Category Modal -->
<div class="modal fade" id="createCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createCategoryForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name *</label>
                        <input type="text" name="name" class="form-control" required>
                        <small class="text-muted">Internal name (lowercase, no spaces)</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Display Name *</label>
                        <input type="text" name="display_name" class="form-control" required>
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
                        <div class="mt-2">
                            <small class="text-muted">Preview: </small>
                            <i id="create_icon_preview" class="fas fa-leaf fa-2x"></i>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="alert alert-info">
                        <small><i class="fas fa-info-circle me-1"></i> Display order will be set automatically. You can adjust it later by editing the category.</small>
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
            <form id="editCategoryForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_category_id" name="category_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name *</label>
                        <input type="text" id="edit_category_name" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Display Name *</label>
                        <input type="text" id="edit_category_display_name" name="display_name" class="form-control" required>
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
                        <div class="mt-2">
                            <small class="text-muted">Preview: </small>
                            <i id="edit_icon_preview" class="fas fa-leaf fa-2x"></i>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea id="edit_category_description" name="description" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Display Order</label>
                        <input type="number" id="edit_category_display_order" name="display_order" class="form-control" min="0">
                        <small class="text-muted">Lower numbers appear first</small>
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
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createItemForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="item_category_id" name="category_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Unit *</label>
                        <select name="unit" class="form-select" required>
                            <option value="">Select unit...</option>
                            <option value="pcs" selected>Pieces (pcs)</option>
                            <option value="kg">Kilogram (kg)</option>
                            <option value="g">Gram (g)</option>
                            <option value="lbs">Pound (lbs)</option>
                            <option value="L">Liter (L)</option>
                            <option value="mL">Milliliter (mL)</option>
                            <option value="gal">Gallon (gal)</option>
                            <option value="m">Meter (m)</option>
                            <option value="cm">Centimeter (cm)</option>
                            <option value="ft">Feet (ft)</option>
                            <option value="box">Box</option>
                            <option value="bag">Bag</option>
                            <option value="bundle">Bundle</option>
                            <option value="pack">Pack</option>
                            <option value="set">Set</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Min Quantity</label>
                            <input type="number" name="min_quantity" class="form-control" value="1" min="1">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Max Quantity</label>
                            <input type="number" name="max_quantity" class="form-control" min="1">
                        </div>
                    </div>
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
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editItemForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="edit_item_id" name="item_id">
                <input type="hidden" id="edit_item_category_id" name="category_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name *</label>
                        <input type="text" id="edit_item_name" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea id="edit_item_description" name="description" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Unit *</label>
                        <select id="edit_item_unit" name="unit" class="form-select" required>
                            <option value="">Select unit...</option>
                            <option value="pcs">Pieces (pcs)</option>
                            <option value="kg">Kilogram (kg)</option>
                            <option value="g">Gram (g)</option>
                            <option value="lbs">Pound (lbs)</option>
                            <option value="L">Liter (L)</option>
                            <option value="mL">Milliliter (mL)</option>
                            <option value="gal">Gallon (gal)</option>
                            <option value="m">Meter (m)</option>
                            <option value="cm">Centimeter (cm)</option>
                            <option value="ft">Feet (ft)</option>
                            <option value="box">Box</option>
                            <option value="bag">Bag</option>
                            <option value="bundle">Bundle</option>
                            <option value="pack">Pack</option>
                            <option value="set">Set</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Min Quantity</label>
                            <input type="number" id="edit_item_min_quantity" name="min_quantity" class="form-control" min="1">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Max Quantity</label>
                            <input type="number" id="edit_item_max_quantity" name="max_quantity" class="form-control" min="1">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Current Image</label>
                        <div id="current_item_image"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Image (optional)</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <small class="text-muted">Leave empty to keep current image</small>
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

<script>
// CSRF Token
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

// Icon preview function
function updateIconPreview(type) {
    const select = document.getElementById(`${type}_icon`);
    const preview = document.getElementById(`${type}_icon_preview`);
    const iconClass = select.value;
    
    if (iconClass) {
        preview.className = `fas ${iconClass} fa-2x`;
    } else {
        preview.className = 'fas fa-leaf fa-2x';
    }
}

// Helper function for fetch requests with error handling
async function makeRequest(url, options) {
    try {
        const response = await fetch(url, options);
        const data = await response.json();
        
        if (!response.ok) {
            throw new Error(data.message || 'Request failed');
        }
        
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
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });
        
        document.getElementById('edit_category_id').value = category.id;
        document.getElementById('edit_category_name').value = category.name;
        document.getElementById('edit_category_display_name').value = category.display_name;
        document.getElementById('edit_icon').value = category.icon || 'fa-leaf';
        document.getElementById('edit_category_description').value = category.description || '';
        document.getElementById('edit_category_display_order').value = category.display_order;
        
        // Update icon preview
        updateIconPreview('edit');
        
        new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
    } catch (error) {
        alert('Error loading category: ' + error.message);
    }
}

async function toggleCategory(categoryId) {
    if (!confirm('Are you sure you want to toggle this category status?')) {
        return;
    }
    
    try {
        const data = await makeRequest(`/admin/seedlings/categories/${categoryId}/toggle`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });
        
        alert(data.message);
        location.reload();
    } catch (error) {
        alert('Error toggling category: ' + error.message);
    }
}

async function deleteCategory(categoryId) {
    if (!confirm('Are you sure? This will delete the category permanently.')) {
        return;
    }
    
    try {
        const data = await makeRequest(`/admin/seedlings/categories/${categoryId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });
        
        alert(data.message);
        location.reload();
    } catch (error) {
        alert('Error deleting category: ' + error.message);
    }
}

// Item Functions
async function editItem(itemId) {
    try {
        const item = await makeRequest(`/admin/seedlings/items/${itemId}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });
        
        document.getElementById('edit_item_id').value = item.id;
        document.getElementById('edit_item_category_id').value = item.category_id;
        document.getElementById('edit_item_name').value = item.name;
        document.getElementById('edit_item_description').value = item.description || '';
        document.getElementById('edit_item_unit').value = item.unit;
        document.getElementById('edit_item_min_quantity').value = item.min_quantity || '';
        document.getElementById('edit_item_max_quantity').value = item.max_quantity || '';
        
        const imageDiv = document.getElementById('current_item_image');
        if (item.image_path) {
            imageDiv.innerHTML = `<img src="/storage/${item.image_path}" class="img-thumbnail" style="max-width: 200px;">`;
        } else {
            imageDiv.innerHTML = '<p class="text-muted">No image</p>';
        }
        
        new bootstrap.Modal(document.getElementById('editItemModal')).show();
    } catch (error) {
        alert('Error loading item: ' + error.message);
    }
}

async function toggleItem(itemId) {
    if (!confirm('Are you sure you want to toggle this item status?')) {
        return;
    }
    
    try {
        const data = await makeRequest(`/admin/seedlings/items/${itemId}/toggle`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });
        
        alert(data.message);
        location.reload();
    } catch (error) {
        alert('Error toggling item: ' + error.message);
    }
}

async function deleteItem(itemId) {
    if (!confirm('Are you sure? This will delete the item permanently.')) {
        return;
    }
    
    try {
        const data = await makeRequest(`/admin/seedlings/items/${itemId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });
        
        alert(data.message);
        location.reload();
    } catch (error) {
        alert('Error deleting item: ' + error.message);
    }
}

// Form Submissions
document.getElementById('createCategoryForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    try {
        const data = await makeRequest('/admin/seedlings/categories', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });
        
        alert(data.message);
        location.reload();
    } catch (error) {
        alert('Error creating category: ' + error.message);
    }
});

document.getElementById('editCategoryForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const categoryId = document.getElementById('edit_category_id').value;
    const formData = new FormData(this);
    formData.append('_method', 'PUT');
    
    try {
        const data = await makeRequest(`/admin/seedlings/categories/${categoryId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });
        
        alert(data.message);
        location.reload();
    } catch (error) {
        alert('Error updating category: ' + error.message);
    }
});

document.getElementById('createItemForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    try {
        const data = await makeRequest('/admin/seedlings/items', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });
        
        alert(data.message);
        location.reload();
    } catch (error) {
        alert('Error creating item: ' + error.message);
    }
});

document.getElementById('editItemForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const itemId = document.getElementById('edit_item_id').value;
    const formData = new FormData(this);
    formData.append('_method', 'PUT');
    
    try {
        const data = await makeRequest(`/admin/seedlings/items/${itemId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });
        
        alert(data.message);
        location.reload();
    } catch (error) {
        alert('Error updating item: ' + error.message);
    }
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
</style>
@endsection