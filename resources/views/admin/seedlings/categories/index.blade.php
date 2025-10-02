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
                                        @foreach($category->items->sortBy('display_order') as $item)
                                            <tr>
                                                <td>
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
                        <label class="form-label">Icon (Font Awesome class)</label>
                        <input type="text" name="icon" class="form-control" placeholder="fa-seedling">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Display Order</label>
                        <input type="number" name="display_order" class="form-control" value="0" min="0">
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
                        <label class="form-label">Icon (Font Awesome class)</label>
                        <input type="text" id="edit_category_icon" name="icon" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea id="edit_category_description" name="description" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Display Order</label>
                        <input type="number" id="edit_category_display_order" name="display_order" class="form-control" min="0">
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
                        <input type="text" name="unit" class="form-control" value="pcs" required>
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
                        <label class="form-label">Display Order</label>
                        <input type="number" name="display_order" class="form-control" value="0" min="0">
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
                        <input type="text" id="edit_item_unit" name="unit" class="form-control" required>
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
                        <label class="form-label">Display Order</label>
                        <input type="number" id="edit_item_display_order" name="display_order" class="form-control" min="0">
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

// Category Functions
function setItemCategory(categoryId) {
    document.getElementById('item_category_id').value = categoryId;
}

function editCategory(categoryId) {
    fetch(`/admin/seedlings/categories/${categoryId}`)
        .then(response => response.json())
        .then(category => {
            document.getElementById('edit_category_id').value = category.id;
            document.getElementById('edit_category_name').value = category.name;
            document.getElementById('edit_category_display_name').value = category.display_name;
            document.getElementById('edit_category_icon').value = category.icon || '';
            document.getElementById('edit_category_description').value = category.description || '';
            document.getElementById('edit_category_display_order').value = category.display_order;
            
            new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
        });
}

function toggleCategory(categoryId) {
    if (confirm('Are you sure you want to toggle this category status?')) {
        fetch(`/admin/seedlings/categories/${categoryId}/toggle`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

function deleteCategory(categoryId) {
    if (confirm('Are you sure? This will delete the category permanently.')) {
        fetch(`/admin/seedlings/categories/${categoryId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.success) {
                location.reload();
            }
        });
    }
}

// Item Functions
function editItem(itemId) {
    fetch(`/admin/seedlings/items/${itemId}`)
        .then(response => response.json())
        .then(item => {
            document.getElementById('edit_item_id').value = item.id;
            document.getElementById('edit_item_category_id').value = item.category_id;
            document.getElementById('edit_item_name').value = item.name;
            document.getElementById('edit_item_description').value = item.description || '';
            document.getElementById('edit_item_unit').value = item.unit;
            document.getElementById('edit_item_min_quantity').value = item.min_quantity;
            document.getElementById('edit_item_max_quantity').value = item.max_quantity || '';
            document.getElementById('edit_item_display_order').value = item.display_order;
            
            const imageDiv = document.getElementById('current_item_image');
            if (item.image_path) {
                imageDiv.innerHTML = `<img src="/storage/${item.image_path}" class="img-thumbnail" style="max-width: 200px;">`;
            } else {
                imageDiv.innerHTML = '<p class="text-muted">No image</p>';
            }
            
            new bootstrap.Modal(document.getElementById('editItemModal')).show();
        });
}

function toggleItem(itemId) {
    if (confirm('Are you sure you want to toggle this item status?')) {
        fetch(`/admin/seedlings/items/${itemId}/toggle`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

function deleteItem(itemId) {
    if (confirm('Are you sure? This will delete the item permanently.')) {
        fetch(`/admin/seedlings/items/${itemId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.success) {
                location.reload();
            }
        });
    }
}

// Form Submissions
document.getElementById('createCategoryForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('/admin/seedlings/categories', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            location.reload();
        }
    });
});

document.getElementById('editCategoryForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const categoryId = document.getElementById('edit_category_id').value;
    const formData = new FormData(this);
    
    fetch(`/admin/seedlings/categories/${categoryId}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-HTTP-Method-Override': 'PUT'
        }
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            location.reload();
        }
    });
});

document.getElementById('createItemForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('/admin/seedlings/items', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            location.reload();
        }
    });
});

document.getElementById('editItemForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const itemId = document.getElementById('edit_item_id').value;
    const formData = new FormData(this);
    
    fetch(`/admin/seedlings/items/${itemId}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            location.reload();
        }
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
</style>
@endsection