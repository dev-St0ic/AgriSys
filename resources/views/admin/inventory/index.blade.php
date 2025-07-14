@extends('layouts.app')

@section('title', 'Inventory Management - AgriSys')
@section('page-title', 'Inventory Management')

@section('content')
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Items</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_items'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-seedling fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Low Stock</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['low_stock_items'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Out of Stock</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['out_of_stock_items'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Stock</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_stock']) }} pieces</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Inventory Items</h6>
                    <a href="{{ route('admin.inventory.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Add New Item
                    </a>
                </div>
                <div class="card-body">
                    <!-- Search and Filter Form -->
                    <form method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control" placeholder="Search items..." 
                                       value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="category" class="form-control">
                                    <option value="">All Categories</option>
                                    <option value="vegetables" {{ request('category') == 'vegetables' ? 'selected' : '' }}>Vegetables</option>
                                    <option value="fruits" {{ request('category') == 'fruits' ? 'selected' : '' }}>Fruits</option>
                                    <option value="fertilizers" {{ request('category') == 'fertilizers' ? 'selected' : '' }}>Fertilizers</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="stock_status" class="form-control">
                                    <option value="">All Stock</option>
                                    <option value="available" {{ request('stock_status') == 'available' ? 'selected' : '' }}>Available</option>
                                    <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Low Stock</option>
                                    <option value="out" {{ request('stock_status') == 'out' ? 'selected' : '' }}>Out of Stock</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-outline-primary me-2">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="{{ route('admin.inventory.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Inventory Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Item Name</th>
                                    <th>Category</th>
                                    <th>Variety</th>
                                    <th>Current Stock</th>
                                    <th>Unit</th>
                                    <th>Status</th>
                                    <th>Last Restocked</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($inventories as $inventory)
                                    <tr>
                                        <td class="font-weight-bold">{{ $inventory->item_name }}</td>
                                        <td>
                                            <span class="badge badge-info">{{ ucfirst($inventory->category) }}</span>
                                        </td>
                                        <td>{{ $inventory->variety ?? '-' }}</td>
                                        <td>
                                            <span class="font-weight-bold 
                                                @if($inventory->isOutOfStock()) text-danger
                                                @elseif($inventory->isLowStock()) text-warning
                                                @else text-success @endif">
                                                {{ number_format($inventory->current_stock) }}
                                            </span>
                                            @if($inventory->isLowStock() && !$inventory->isOutOfStock())
                                                <i class="fas fa-exclamation-triangle text-warning ms-1" title="Low Stock"></i>
                                            @elseif($inventory->isOutOfStock())
                                                <i class="fas fa-times-circle text-danger ms-1" title="Out of Stock"></i>
                                            @endif
                                        </td>
                                        <td>{{ $inventory->unit }}</td>
                                        <td>
                                            @if($inventory->isOutOfStock())
                                                <span class="badge badge-danger">Out of Stock</span>
                                            @elseif($inventory->isLowStock())
                                                <span class="badge badge-warning">Low Stock</span>
                                            @else
                                                <span class="badge badge-success">Available</span>
                                            @endif
                                        </td>
                                        <td>{{ $inventory->last_restocked ? $inventory->last_restocked->format('M d, Y') : '-' }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.inventory.show', $inventory) }}" 
                                                   class="btn btn-info btn-sm" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.inventory.edit', $inventory) }}" 
                                                   class="btn btn-primary btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-success btn-sm" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#adjustStockModal{{ $inventory->id }}" 
                                                        title="Adjust Stock">
                                                    <i class="fas fa-plus-minus"></i>
                                                </button>
                                            </div>

                                            <!-- Stock Adjustment Modal -->
                                            <div class="modal fade" id="adjustStockModal{{ $inventory->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form method="POST" action="{{ route('admin.inventory.adjust-stock', $inventory) }}">
                                                            @csrf
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Adjust Stock - {{ $inventory->item_name }}</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Current Stock: {{ $inventory->current_stock }} {{ $inventory->unit }}</label>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Adjustment Type</label>
                                                                    <select name="adjustment_type" class="form-control" required>
                                                                        <option value="add">Add Stock</option>
                                                                        <option value="subtract">Subtract Stock</option>
                                                                        <option value="set">Set Exact Stock</option>
                                                                    </select>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Quantity</label>
                                                                    <input type="number" name="quantity" class="form-control" min="0" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Reason (Optional)</label>
                                                                    <input type="text" name="reason" class="form-control" placeholder="Reason for adjustment">
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-primary">Adjust Stock</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-box-open fa-3x mb-3"></i>
                                                <p>No inventory items found. <a href="{{ route('admin.inventory.create') }}">Add your first item</a>.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($inventories->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $inventories->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        .border-left-primary { border-left: 0.25rem solid #4e73df !important; }
        .border-left-success { border-left: 0.25rem solid #1cc88a !important; }
        .border-left-info { border-left: 0.25rem solid #36b9cc !important; }
        .border-left-warning { border-left: 0.25rem solid #f6c23e !important; }
        .border-left-danger { border-left: 0.25rem solid #e74a3b !important; }
        .text-xs { font-size: 0.7rem; }
        .text-gray-300 { color: #dddfeb !important; }
        .text-gray-800 { color: #5a5c69 !important; }
    </style>
@endsection

@section('scripts')
<script>
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
</script>
@endsection
