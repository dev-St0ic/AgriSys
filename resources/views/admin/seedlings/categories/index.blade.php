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
</div>

<!-- Create Category Modal -->
<div class="modal fade" id="createCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createCategoryForm">
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
                            <option value="fa-fish">🐟 Fish</option>
                            <option value="fa-flask">🧪 Flask/Chemical</option>
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Category</button>
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
            <form id="createItemForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="item_category_id" name="category_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Name *</label>
                            <input type="text" name="name" class="form-control" required>
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
                                <form id="addSupplyForm">
                                    <input type="hidden" id="add_supply_item_id" name="item_id">
                                    <div class="mb-2">
                                        <label class="form-label small">Quantity</label>
                                        <input type="number" name="quantity" class="form-control form-control-sm" required min="1">
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
                                <form id="adjustSupplyForm">
                                    <input type="hidden" id="adjust_supply_item_id" name="item_id">
                                    <div class="mb-2">
                                        <label class="form-label small">New Supply</label>
                                        <input type="number" name="new_supply" class="form-control form-control-sm" required min="0">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small">Reason *</label>
                                        <textarea name="reason" class="form-control form-control-sm" rows="3" required placeholder="Explain the adjustment..."></textarea>
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
                                <form id="recordLossForm">
                                    <input type="hidden" id="loss_supply_item_id" name="item_id">
                                    <div class="mb-2">
                                        <label class="form-label small">Quantity Lost</label>
                                        <input type="number" name="quantity" class="form-control form-control-sm" required min="1">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small">Reason *</label>
                                        <select name="reason_type" class="form-select form-select-sm mb-2">
                                            <option value="Expired">Expired</option>
                                            <option value="Damaged">Damaged</option>
                                            <option value="Lost">Lost</option>
                                            <option value="Other">Other</option>
                                        </select>
                                        <textarea name="reason" class="form-control form-control-sm" rows="2" required placeholder="Additional details..."></textarea>
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

// Icon preview
function updateIconPreview(type) {
    const select = document.getElementById(`${type}_icon`);
    const preview = document.getElementById(`${type}_icon_preview`);
    const iconClass = select.value;
    preview.className = iconClass ? `fas ${iconClass} fa-2x` : 'fas fa-leaf fa-2x';
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
    // Implementation as before...
}

async function toggleCategory(categoryId) {
    if (!confirm('Toggle category status?')) return;
    try {
        const data = await makeRequest(`/admin/seedlings/categories/${categoryId}/toggle`, {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json'}
        });
        alert(data.message);
        location.reload();
    } catch (error) {
        alert('Error: ' + error.message);
    }
}

async function deleteCategory(categoryId) {
    if (!confirm('Delete this category permanently?')) return;
    try {
        const data = await makeRequest(`/admin/seedlings/categories/${categoryId}`, {
            method: 'DELETE',
            headers: {'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json'}
        });
        alert(data.message);
        location.reload();
    } catch (error) {
        alert('Error: ' + error.message);
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
        
        // Load supply logs
        loadSupplyLogs(itemId);
        
        // Show modal
        new bootstrap.Modal(document.getElementById('supplyModal')).show();
    } catch (error) {
        alert('Error loading item: ' + error.message);
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

// Form Submissions
document.getElementById('createCategoryForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    try {
        const data = await makeRequest('/admin/seedlings/categories', {
            method: 'POST',
            body: formData,
            headers: {'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json'}
        });
        alert(data.message);
        location.reload();
    } catch (error) {
        alert('Error: ' + error.message);
    }
});

document.getElementById('createItemForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    try {
        const data = await makeRequest('/admin/seedlings/items', {
            method: 'POST',
            body: formData,
            headers: {'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json'}
        });
        alert(data.message);
        location.reload();
    } catch (error) {
        alert('Error: ' + error.message);
    }
});

// Supply Management Forms
document.getElementById('addSupplyForm').addEventListener('submit', async function(e) {
    e.preventDefault();
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
        alert(data.message);
        this.reset();
        manageSupply(itemId); // Refresh the modal
        location.reload();
    } catch (error) {
        alert('Error: ' + error.message);
    }
});

document.getElementById('adjustSupplyForm').addEventListener('submit', async function(e) {
    e.preventDefault();
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
        alert(data.message);
        this.reset();
        manageSupply(itemId);
        location.reload();
    } catch (error) {
        alert('Error: ' + error.message);
    }
});

document.getElementById('recordLossForm').addEventListener('submit', async function(e) {
    e.preventDefault();
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
        alert(data.message);
        this.reset();
        manageSupply(itemId);
        location.reload();
    } catch (error) {
        alert('Error: ' + error.message);
    }
});

async function toggleItem(itemId) {
    if (!confirm('Toggle item status?')) return;
    try {
        const data = await makeRequest(`/admin/seedlings/items/${itemId}/toggle`, {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json'}
        });
        alert(data.message);
        location.reload();
    } catch (error) {
        alert('Error: ' + error.message);
    }
}

async function deleteItem(itemId) {
    if (!confirm('Delete this item permanently?')) return;
    try {
        const data = await makeRequest(`/admin/seedlings/items/${itemId}`, {
            method: 'DELETE',
            headers: {'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json'}
        });
        alert(data.message);
        location.reload();
    } catch (error) {
        alert('Error: ' + error.message);
    }
}

async function editItem(itemId) {
    // Similar to editCategory - load item data and populate edit form
    // Implementation left for brevity
}
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
</style>
@endsection