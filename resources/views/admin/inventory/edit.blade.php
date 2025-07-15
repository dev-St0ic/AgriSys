@extends('layouts.app')

@section('title', 'Edit Inventory Item - AgriSys')
@section('page-title', 'Edit Inventory Item')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient-primary text-white py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1 text-white">
                                <i class="fas fa-edit me-2"></i>Edit Inventory Item
                            </h5>
                            <p class="mb-0 text-white-50">{{ $inventory->item_name }}</p>
                        </div>
                        <a href="{{ route('admin.inventory.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back to Inventory
                        </a>
                    </div>
                </div>
                <div class="card-body p-5">
                    <!-- Alert Messages -->
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Please fix the following errors:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.inventory.update', $inventory) }}" id="editInventoryForm">
                        @csrf
                        @method('PUT')

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
                                    <input type="text" class="form-control @error('item_name') is-invalid @enderror"
                                        id="item_name" name="item_name"
                                        value="{{ old('item_name', $inventory->item_name) }}" placeholder="Item Name"
                                        required>
                                    <label for="item_name">
                                        <i class="fas fa-tag me-1"></i>Item Name <span class="text-danger">*</span>
                                    </label>
                                    @error('item_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <select class="form-select @error('category') is-invalid @enderror" id="category"
                                        name="category" required>
                                        <option value="">Select Category</option>
                                        <option value="vegetables"
                                            {{ old('category', $inventory->category) == 'vegetables' ? 'selected' : '' }}>
                                            🌱 Vegetables
                                        </option>
                                        <option value="fruits"
                                            {{ old('category', $inventory->category) == 'fruits' ? 'selected' : '' }}>
                                            🍎 Fruits
                                        </option>
                                        <option value="fertilizers"
                                            {{ old('category', $inventory->category) == 'fertilizers' ? 'selected' : '' }}>
                                            🌿 Fertilizers
                                        </option>
                                    </select>
                                    <label for="category">
                                        <i class="fas fa-layer-group me-1"></i>Category <span class="text-danger">*</span>
                                    </label>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control @error('variety') is-invalid @enderror"
                                        id="variety" name="variety" value="{{ old('variety', $inventory->variety) }}"
                                        placeholder="Variety">
                                    <label for="variety">
                                        <i class="fas fa-seedling me-1"></i>Variety
                                    </label>
                                    @error('variety')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <select class="form-select @error('unit') is-invalid @enderror" id="unit"
                                        name="unit" required>
                                        <option value="">Select Unit</option>
                                        <option value="pieces"
                                            {{ old('unit', $inventory->unit) == 'pieces' ? 'selected' : '' }}>Pieces
                                        </option>
                                        <option value="kg"
                                            {{ old('unit', $inventory->unit) == 'kg' ? 'selected' : '' }}>Kilograms (kg)
                                        </option>
                                        <option value="grams"
                                            {{ old('unit', $inventory->unit) == 'grams' ? 'selected' : '' }}>Grams</option>
                                        <option value="sacks"
                                            {{ old('unit', $inventory->unit) == 'sacks' ? 'selected' : '' }}>Sacks</option>
                                        <option value="liters"
                                            {{ old('unit', $inventory->unit) == 'liters' ? 'selected' : '' }}>Liters
                                        </option>
                                        <option value="bottles"
                                            {{ old('unit', $inventory->unit) == 'bottles' ? 'selected' : '' }}>Bottles
                                        </option>
                                        <option value="packs"
                                            {{ old('unit', $inventory->unit) == 'packs' ? 'selected' : '' }}>Packs</option>
                                    </select>
                                    <label for="unit">
                                        <i class="fas fa-balance-scale me-1"></i>Unit <span class="text-danger">*</span>
                                    </label>
                                    @error('unit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                        style="height: 100px" placeholder="Description">{{ old('description', $inventory->description) }}</textarea>
                                    <label for="description">
                                        <i class="fas fa-align-left me-1"></i>Description
                                    </label>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
                                            <input type="number"
                                                class="form-control @error('current_stock') is-invalid @enderror"
                                                id="current_stock" name="current_stock"
                                                value="{{ old('current_stock', $inventory->current_stock) }}"
                                                min="0" required placeholder="Current Stock">
                                            <label for="current_stock">
                                                <i class="fas fa-warehouse me-1"></i>Current Stock <span
                                                    class="text-danger">*</span>
                                            </label>
                                            @error('current_stock')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
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
                                            <input type="number"
                                                class="form-control @error('minimum_stock') is-invalid @enderror"
                                                id="minimum_stock" name="minimum_stock"
                                                value="{{ old('minimum_stock', $inventory->minimum_stock) }}"
                                                min="0" required placeholder="Minimum Stock">
                                            <label for="minimum_stock">
                                                <i class="fas fa-exclamation-triangle me-1"></i>Minimum Stock <span
                                                    class="text-danger">*</span>
                                            </label>
                                            @error('minimum_stock')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
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
                                            <input type="number"
                                                class="form-control @error('maximum_stock') is-invalid @enderror"
                                                id="maximum_stock" name="maximum_stock"
                                                value="{{ old('maximum_stock', $inventory->maximum_stock) }}"
                                                min="1" required placeholder="Maximum Stock">
                                            <label for="maximum_stock">
                                                <i class="fas fa-chart-line me-1"></i>Maximum Stock <span
                                                    class="text-danger">*</span>
                                            </label>
                                            @error('maximum_stock')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
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
                                    <input type="date"
                                        class="form-control @error('last_restocked') is-invalid @enderror"
                                        id="last_restocked" name="last_restocked"
                                        value="{{ old('last_restocked', $inventory->last_restocked ? $inventory->last_restocked->format('Y-m-d') : '') }}"
                                        placeholder="Last Restocked">
                                    <label for="last_restocked">
                                        <i class="fas fa-calendar me-1"></i>Last Restocked
                                    </label>
                                    @error('last_restocked')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 d-flex align-items-center">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                        value="1" {{ old('is_active', $inventory->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="is_active">
                                        <i class="fas fa-toggle-on me-2 text-success"></i>Active Item
                                    </label>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>Uncheck to deactivate this item
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <a href="{{ route('admin.inventory.index') }}"
                                        class="btn btn-secondary btn-lg me-md-2">
                                        <i class="fas fa-times me-2"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save me-2"></i>Update Inventory Item
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endsection

    @section('styles')
        <style>
            .bg-gradient-primary {
                background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            }

            .card {
                border-radius: 15px;
                overflow: hidden;
            }

            .form-floating>.form-control,
            .form-floating>.form-select {
                height: calc(3.5rem + 2px);
                border-radius: 10px;
                border: 2px solid #e3e6f0;
                transition: all 0.3s ease;
            }

            .form-floating>.form-control:focus,
            .form-floating>.form-select:focus {
                border-color: #4e73df;
                box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
                transform: translateY(-2px);
            }

            .form-floating>label {
                color: #6c757d;
                font-weight: 500;
            }

            .card.border-success {
                border-left: 4px solid #28a745 !important;
                box-shadow: 0 0 10px rgba(40, 167, 69, 0.1);
                transition: all 0.3s ease;
            }

            .card.border-success:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 25px rgba(40, 167, 69, 0.15);
            }

            .card.border-warning {
                border-left: 4px solid #ffc107 !important;
                box-shadow: 0 0 10px rgba(255, 193, 7, 0.1);
                transition: all 0.3s ease;
            }

            .card.border-warning:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 25px rgba(255, 193, 7, 0.15);
            }

            .card.border-primary {
                border-left: 4px solid #007bff !important;
                box-shadow: 0 0 10px rgba(0, 123, 255, 0.1);
                transition: all 0.3s ease;
            }

            .card.border-primary:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 25px rgba(0, 123, 255, 0.15);
            }

            .btn-lg {
                padding: 0.75rem 2rem;
                font-size: 1.1rem;
                border-radius: 10px;
                transition: all 0.3s ease;
                font-weight: 600;
            }

            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(78, 115, 223, 0.4);
            }

            .btn-secondary:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(108, 117, 125, 0.4);
            }

            .form-check-input:checked {
                background-color: #28a745;
                border-color: #28a745;
            }

            .text-primary {
                color: #4e73df !important;
            }

            .border-bottom {
                border-bottom: 2px solid #e3e6f0 !important;
            }

            .alert {
                border-radius: 10px;
                border: none;
            }

            .invalid-feedback {
                font-size: 0.875rem;
                font-weight: 500;
            }

            .stock-warning {
                background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
                border: 1px solid #ffeaa7;
                border-radius: 8px;
                padding: 0.5rem;
                margin-top: 0.5rem;
                font-size: 0.875rem;
                animation: slideIn 0.3s ease;
            }

            @keyframes slideIn {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .form-text {
                font-size: 0.875rem;
                color: #6c757d;
            }

            .shadow-lg {
                box-shadow: 0 15px 35px rgba(50, 50, 93, 0.1), 0 5px 15px rgba(0, 0, 0, 0.07) !important;
            }

            .text-white-50 {
                opacity: 0.7;
            }

            .form-check-label.fw-bold {
                font-weight: 600 !important;
            }
        </style>
    @endsection

    @section('scripts')
        <script>
            // Auto-suggest varieties based on category with enhanced UX
            document.getElementById('category').addEventListener('change', function() {
                const category = this.value;
                const varietyInput = document.getElementById('variety');

                const suggestions = {
                    'vegetables': ['Sampaguita', 'Siling Haba', 'Siling Labuyo', 'Eggplant', 'Kamatis', 'Okra',
                        'Kalabasa', 'Upo', 'Pipino'
                    ],
                    'fruits': ['Kalamansi', 'Guyabano', 'Lanzones', 'Mangga'],
                    'fertilizers': ['Pre-processed Chicken Manure', 'Humic Acid', 'Vermicast']
                };

                if (suggestions[category]) {
                    varietyInput.placeholder = 'e.g., ' + suggestions[category].slice(0, 3).join(', ');
                    // Add a subtle animation to draw attention
                    varietyInput.style.transform = 'scale(1.05)';
                    setTimeout(() => {
                        varietyInput.style.transform = 'scale(1)';
                    }, 200);
                }
            });

            // Enhanced stock level validation with visual feedback
            function validateStockLevels() {
                const minStock = parseInt(document.getElementById('minimum_stock').value) || 0;
                const maxStock = parseInt(document.getElementById('maximum_stock').value) || 0;
                const currentStock = parseInt(document.getElementById('current_stock').value) || 0;

                const maxStockInput = document.getElementById('maximum_stock');
                const currentStockInput = document.getElementById('current_stock');
                const minStockInput = document.getElementById('minimum_stock');

                // Reset classes
                [maxStockInput, currentStockInput, minStockInput].forEach(input => {
                    input.classList.remove('border-success', 'border-warning', 'border-danger');
                });

                // Validate maximum > minimum
                if (minStock >= maxStock && maxStock > 0) {
                    maxStockInput.setCustomValidity('Maximum stock must be greater than minimum stock');
                    maxStockInput.classList.add('border-danger');
                } else {
                    maxStockInput.setCustomValidity('');
                    if (maxStock > 0) maxStockInput.classList.add('border-success');
                }

                // Validate current <= maximum
                if (currentStock > maxStock && maxStock > 0) {
                    currentStockInput.setCustomValidity('Current stock cannot exceed maximum stock');
                    currentStockInput.classList.add('border-danger');
                } else {
                    currentStockInput.setCustomValidity('');
                    if (currentStock >= 0) {
                        if (currentStock <= minStock && minStock > 0) {
                            currentStockInput.classList.add('border-warning');
                        } else {
                            currentStockInput.classList.add('border-success');
                        }
                    }
                }

                // Visual feedback for minimum stock
                if (minStock >= 0 && minStock < maxStock) {
                    minStockInput.classList.add('border-success');
                }
            }

            // Add event listeners with enhanced feedback
            document.getElementById('maximum_stock').addEventListener('input', validateStockLevels);
            document.getElementById('minimum_stock').addEventListener('input', validateStockLevels);
            document.getElementById('current_stock').addEventListener('input', function() {
                validateStockLevels();

                // Enhanced stock change warning
                const newValue = parseInt(this.value) || 0;
                const originalStock = {{ $inventory->current_stock }};

                if (newValue !== originalStock) {
                    if (!document.getElementById('stock-warning')) {
                        const warning = document.createElement('div');
                        warning.id = 'stock-warning';
                        warning.className = 'stock-warning';
                        warning.innerHTML = `
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                    <strong>Stock Change:</strong> ${originalStock} → ${newValue}
                    <small class="d-block mt-1">
                        ${newValue > originalStock ? 
                            '<i class="fas fa-arrow-up text-success"></i> Increase' : 
                            '<i class="fas fa-arrow-down text-danger"></i> Decrease'
                        } of ${Math.abs(newValue - originalStock)} units
                    </small>
                `;
                        this.parentNode.appendChild(warning);
                    } else {
                        document.getElementById('stock-warning').innerHTML = `
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                    <strong>Stock Change:</strong> ${originalStock} → ${newValue}
                    <small class="d-block mt-1">
                        ${newValue > originalStock ? 
                            '<i class="fas fa-arrow-up text-success"></i> Increase' : 
                            '<i class="fas fa-arrow-down text-danger"></i> Decrease'
                        } of ${Math.abs(newValue - originalStock)} units
                    </small>
                `;
                    }
                } else {
                    const warning = document.getElementById('stock-warning');
                    if (warning) {
                        warning.style.animation = 'slideOut 0.3s ease';
                        setTimeout(() => warning.remove(), 300);
                    }
                }
            });

            // Form submission with loading state
            document.getElementById('editInventoryForm').addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;

                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
                submitBtn.disabled = true;

                // Re-enable if form validation fails
                setTimeout(() => {
                    if (submitBtn.disabled) {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }
                }, 3000);
            });

            // Initial validation on page load
            document.addEventListener('DOMContentLoaded', function() {
                validateStockLevels();

                // Add smooth scrolling to form on validation errors
                if (document.querySelector('.is-invalid')) {
                    document.querySelector('.is-invalid').scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            });

            // Enhanced tooltips for stock cards
            const stockCards = document.querySelectorAll('.card[class*="border-"]');
            stockCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)';
                });

                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        </script>

        <style>
            @keyframes slideOut {
                from {
                    opacity: 1;
                    transform: translateY(0);
                }

                to {
                    opacity: 0;
                    transform: translateY(-10px);
                }
            }

            .border-success {
                border-color: #28a745 !important;
            }

            .border-warning {
                border-color: #ffc107 !important;
            }

            .border-danger {
                border-color: #dc3545 !important;
            }
        </style>
    @endsection
