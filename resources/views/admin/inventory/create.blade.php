@extends('layouts.app')

@section('title', 'Add New Inventory Item - AgriSys')
@section('page-title', 'Add New Inventory Item')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Add New Inventory Item</h6>
                    <a href="{{ route('admin.inventory.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to Inventory
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.inventory.store') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="item_name" class="form-label">Item Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('item_name') is-invalid @enderror" 
                                           id="item_name" name="item_name" value="{{ old('item_name') }}" required>
                                    @error('item_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                    <select class="form-control @error('category') is-invalid @enderror" 
                                            id="category" name="category" required>
                                        <option value="">Select Category</option>
                                        <option value="vegetables" {{ old('category') == 'vegetables' ? 'selected' : '' }}>Vegetables</option>
                                        <option value="fruits" {{ old('category') == 'fruits' ? 'selected' : '' }}>Fruits</option>
                                        <option value="fertilizers" {{ old('category') == 'fertilizers' ? 'selected' : '' }}>Fertilizers</option>
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="variety" class="form-label">Variety</label>
                                    <input type="text" class="form-control @error('variety') is-invalid @enderror" 
                                           id="variety" name="variety" value="{{ old('variety') }}" 
                                           placeholder="e.g., Siling Haba, Eggplant">
                                    @error('variety')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="unit" class="form-label">Unit <span class="text-danger">*</span></label>
                                    <select class="form-control @error('unit') is-invalid @enderror" 
                                            id="unit" name="unit" required>
                                        <option value="">Select Unit</option>
                                        <option value="pieces" {{ old('unit') == 'pieces' ? 'selected' : '' }}>Pieces</option>
                                        <option value="kg" {{ old('unit') == 'kg' ? 'selected' : '' }}>Kilograms</option>
                                        <option value="grams" {{ old('unit') == 'grams' ? 'selected' : '' }}>Grams</option>
                                        <option value="sacks" {{ old('unit') == 'sacks' ? 'selected' : '' }}>Sacks</option>
                                        <option value="liters" {{ old('unit') == 'liters' ? 'selected' : '' }}>Liters</option>
                                        <option value="bottles" {{ old('unit') == 'bottles' ? 'selected' : '' }}>Bottles</option>
                                        <option value="packs" {{ old('unit') == 'packs' ? 'selected' : '' }}>Packs</option>
                                    </select>
                                    @error('unit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" 
                                      placeholder="Brief description of the item">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="current_stock" class="form-label">Current Stock <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('current_stock') is-invalid @enderror" 
                                           id="current_stock" name="current_stock" value="{{ old('current_stock', 0) }}" 
                                           min="0" required>
                                    @error('current_stock')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="minimum_stock" class="form-label">Minimum Stock <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('minimum_stock') is-invalid @enderror" 
                                           id="minimum_stock" name="minimum_stock" value="{{ old('minimum_stock', 10) }}" 
                                           min="0" required>
                                    @error('minimum_stock')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Alert threshold for low stock</small>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="maximum_stock" class="form-label">Maximum Stock <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('maximum_stock') is-invalid @enderror" 
                                           id="maximum_stock" name="maximum_stock" value="{{ old('maximum_stock', 1000) }}" 
                                           min="1" required>
                                    @error('maximum_stock')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="last_restocked" class="form-label">Last Restocked</label>
                                    <input type="date" class="form-control @error('last_restocked') is-invalid @enderror" 
                                           id="last_restocked" name="last_restocked" value="{{ old('last_restocked', date('Y-m-d')) }}">
                                    @error('last_restocked')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active Item
                                </label>
                                <small class="form-text text-muted d-block">Uncheck to deactivate this item</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Inventory Item
                            </button>
                            <a href="{{ route('admin.inventory.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Auto-suggest varieties based on category
    document.getElementById('category').addEventListener('change', function() {
        const category = this.value;
        const varietyInput = document.getElementById('variety');
        
        const suggestions = {
            'vegetables': ['Sampaguita', 'Siling Haba', 'Siling Labuyo', 'Eggplant', 'Kamatis', 'Okra', 'Kalabasa', 'Upo', 'Pipino'],
            'fruits': ['Kalamansi', 'Guyabano', 'Lanzones', 'Mangga'],
            'fertilizers': ['Pre-processed Chicken Manure', 'Humic Acid', 'Vermicast']
        };
        
        if (suggestions[category]) {
            varietyInput.placeholder = 'e.g., ' + suggestions[category].slice(0, 3).join(', ');
        }
    });

    // Validate minimum stock is less than maximum
    document.getElementById('maximum_stock').addEventListener('input', function() {
        const minStock = document.getElementById('minimum_stock').value;
        const maxStock = this.value;
        
        if (minStock && maxStock && parseInt(minStock) >= parseInt(maxStock)) {
            this.setCustomValidity('Maximum stock must be greater than minimum stock');
        } else {
            this.setCustomValidity('');
        }
    });
</script>
@endsection
