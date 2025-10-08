@php
    $categories = \App\Models\RequestCategory::with(['items' => function($query) {
        $query->where('is_active', true)->orderBy('name', 'asc');
    }])->where('is_active', true)->orderBy('display_order')->get();
@endphp

<!-- Seedlings Choice Section -->
<section class="seedlings-application-section" id="seedlings-choice" style="display: none;">
    <div class="seedlings-form-header">
        <h2>🌱 Seedlings & Agricultural Supplies Request</h2>
        <p>Browse and select the items you want to request, then proceed to checkout.</p>
    </div>

    <!-- Category Tabs -->
    <div class="seedlings-category-tabs">
        <button class="seedlings-category-tab active" data-category="all" onclick="filterByCategory('all')">
            <i class="fas fa-th-large"></i> All Items
        </button>
        @foreach($categories as $category)
            <button class="seedlings-category-tab" data-category="{{ $category->name }}" onclick="filterByCategory('{{ $category->name }}')">
                <i class="fas {{ $category->icon ?? 'fa-leaf' }}"></i> {{ $category->display_name }}
            </button>
        @endforeach
    </div>

    <!-- Search and Filter Bar -->
    <div class="seedlings-filter-bar">
        <div class="seedlings-search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="seedlings-search" placeholder="Search items..." onkeyup="searchItems()">
        </div>
        <div class="seedlings-filter-group">
            <select id="stock-filter" class="seedlings-filter-select" onchange="filterByStock()">
                <option value="all">All Stock Status</option>
                <option value="in-stock">In Stock</option>
                <option value="low-stock">Low Stock</option>
                <option value="out-of-stock">Out of Stock</option>
            </select>
            <select id="sort-by" class="seedlings-filter-select" onchange="sortItems()">
                <option value="name-asc">Name (A-Z)</option>
                <option value="name-desc">Name (Z-A)</option>
                <option value="stock-high">Stock (High to Low)</option>
                <option value="stock-low">Stock (Low to High)</option>
            </select>
        </div>
    </div>

    <!-- Selected Items Counter -->
    <div class="seedlings-selection-summary" id="selection-summary" style="display: none;">
        <div class="selection-count">
            <i class="fas fa-shopping-cart"></i>
            <span id="selected-count">0</span> items selected
        </div>
        <button type="button" class="seedlings-clear-btn" onclick="clearAllSelections()">
            <i class="fas fa-times"></i> Clear All
        </button>
    </div>

    <form id="seedlings-choice-form">
        <!-- Items Grid -->
        <div class="seedlings-items-grid" id="items-grid">
            @foreach($categories as $category)
                @foreach($category->items as $item)
                    <div class="seedlings-item-card" 
                         data-category="{{ $category->name }}"
                         data-item-name="{{ strtolower($item->name) }}"
                         data-stock="{{ $item->current_supply }}"
                         data-stock-status="{{ $item->stock_status }}">
                        
                        <!-- Stock Badge -->
                        <div class="seedlings-stock-badge badge-{{ $item->stock_status }}">
                            @if($item->stock_status === 'in_stock')
                                <i class="fas fa-check-circle"></i> In Stock
                            @elseif($item->stock_status === 'low_stock')
                                <i class="fas fa-exclamation-triangle"></i> Low Stock
                            @else
                                <i class="fas fa-times-circle"></i> Out of Stock
                            @endif
                        </div>

                        <!-- Item Image -->
                        <div class="seedlings-item-image">
                            @if($item->image_path)
                                <img src="{{ Storage::url($item->image_path) }}" 
                                     alt="{{ $item->name }}">
                            @else
                                <div class="seedlings-placeholder-image">
                                    <i class="fas {{ $category->icon ?? 'fa-leaf' }} fa-3x"></i>
                                </div>
                            @endif
                        </div>

                        <!-- Item Info -->
                        <div class="seedlings-item-info">
                            <h3 class="seedlings-item-name">{{ $item->name }}</h3>
                            <p class="seedlings-item-category">
                                <i class="fas {{ $category->icon ?? 'fa-leaf' }}"></i>
                                {{ $category->display_name }}
                            </p>
                            @if($item->description)
                                <p class="seedlings-item-description">{{ Str::limit($item->description, 80) }}</p>
                            @endif
                            <div class="seedlings-item-stock">
                                <span class="stock-label">Available:</span>
                                <span class="stock-value">{{ $item->current_supply }} {{ $item->unit }}</span>
                            </div>
                        </div>

                        <!-- Selection Controls -->
                        <div class="seedlings-item-actions">
                            <label class="seedlings-checkbox-label">
                                <input type="checkbox" 
                                       name="{{ $category->name }}" 
                                       value="{{ $item->name }}" 
                                       data-item-id="{{ $item->id }}"
                                       onchange="toggleItemSelection(this, '{{ $item->id }}')"
                                       {{ $item->current_supply <= 0 ? 'disabled' : '' }}>
                                <span class="checkbox-custom"></span>
                                <span class="checkbox-text">Select</span>
                            </label>

                            <div class="seedlings-quantity-input" id="qty-wrapper-{{ $item->id }}" style="display: none;">
                                <button type="button" class="qty-btn" onclick="decrementQty('{{ $item->id }}')">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" 
                                       id="qty-{{ $item->id }}"
                                       name="quantity_{{ $item->id }}" 
                                       min="{{ $item->min_quantity ?? 1 }}" 
                                       max="{{ min($item->max_quantity ?? 999, $item->current_supply) }}"
                                       value="{{ $item->min_quantity ?? 1 }}"
                                       class="qty-input"
                                       onchange="updateQuantity('{{ $item->id }}')">
                                <button type="button" class="qty-btn" onclick="incrementQty('{{ $item->id }}')">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endforeach
        </div>

        <!-- No Results Message -->
        <div id="no-results" class="seedlings-no-results" style="display: none;">
            <i class="fas fa-search fa-3x"></i>
            <p>No items found matching your search criteria.</p>
        </div>

        <!-- Proceed Button -->
        <div class="seedlings-proceed-section">
            <button type="button" class="seedlings-proceed-btn" onclick="proceedToSeedlingsForm()" disabled id="proceed-btn">
                <i class="fas fa-arrow-right"></i> Proceed to Application Form
            </button>
        </div>
    </form>
</section>

<!-- Application Form Section (Keep existing form) -->
<section class="application-section" id="seedlings-form" style="display: none;">
    <div id="seedlings-summary" class="seedlings-summary" style="display: none;">
        <!-- Summary will be populated here -->
    </div>

    <div class="seedlings-form-header">
        <h2>Seedlings Application Form</h2>
        <p>Please fill out your personal information below.</p>
    </div>

    <div class="seedlings-form-tabs">
        <button type="button" class="seedlings-tab-btn active"
            onclick="showSeedlingsTab('seedlings-form-tab', event)">Application Form</button>
        <button type="button" class="seedlings-tab-btn"
            onclick="showSeedlingsTab('seedlings-requirements-tab', event)">Requirements</button>
    </div>

    <div id="seedlings-form-tab" class="seedlings-tab-content" style="display: block;">
        <form id="seedlings-request-form" action="{{ route('apply.seedlings') }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="selected_seedlings" name="selected_seedlings" value="">
            
            <label for="first_name">First Name *</label>
            <input type="text" id="first_name" name="first_name" required>

            <label for="middle_name">Middle Name (Optional)</label>
            <input type="text" id="middle_name" name="middle_name">

            <label for="last_name">Last Name *</label>
            <input type="text" id="last_name" name="last_name" required>

            <label for="mobile">Mobile Number *</label>
            <input type="tel" id="mobile" name="mobile" required>
            <small>Please provide a valid mobile number for SMS notifications.</small>

            <label for="email">Email Address *</label>
            <input type="email" id="email" name="email" required>
            <small>Please provide a valid email address for notifications.</small>

            <label for="barangay">Barangay *</label>
            <select id="barangay" name="barangay" required>
                <option value="" disabled selected>Select Barangay</option>
                <option value="Bagong Silang">Bagong Silang</option>
                <option value="Calendola">Calendola</option>
                <option value="Chrysanthemum">Chrysanthemum</option>
                <option value="Cuyab">Cuyab</option>
                <option value="Fatima">Fatima</option>
                <option value="G.S.I.S.">G.S.I.S.</option>
                <option value="Landayan">Landayan</option>
                <option value="Laram">Laram</option>
                <option value="Magsaysay">Magsaysay</option>
                <option value="Maharlika">Maharlika</option>
                <option value="Narra">Narra</option>
                <option value="Nueva">Nueva</option>
                <option value="Pacita 1">Pacita 1</option>
                <option value="Pacita 2">Pacita 2</option>
                <option value="Poblacion">Poblacion</option>
                <option value="Rosario">Rosario</option>
                <option value="Riverside">Riverside</option>
                <option value="Sampaguita Village">Sampaguita Village</option>
                <option value="San Antonio">San Antonio</option>
                <option value="San Lorenzo Ruiz">San Lorenzo Ruiz</option>
                <option value="San Roque">San Roque</option>
                <option value="San Vicente">San Vicente</option>
                <option value="United Bayanihan">United Bayanihan</option>
                <option value="United Better Living">United Better Living</option>
            </select>

            <label for="address">Complete Address *</label>
            <input type="text" id="address" name="address" required>
            <small>Include house number, street, subdivision if applicable.</small>

            <div id="supporting-docs-field">
                <label for="seedlings-docs">Supporting Documents *</label>
                <input type="file" id="seedlings-docs" name="supporting_documents" accept=".pdf,.jpg,.jpeg,.png"
                    multiple>
                <small>Upload supporting documents (proof of planting area).</small>
            </div>

            <div class="seedlings-form-buttons">
                <button type="button" class="seedlings-cancel-btn" onclick="backToSeedlingsChoice()">Back</button>
                <button type="submit" class="seedlings-submit-btn">Submit Request</button>
            </div>
        </form>
    </div>

    <div id="seedlings-requirements-tab" class="seedlings-tab-content" style="display: none;">
        <h3>Requirements for Seedlings Request</h3>
        <ul>
            <li>Valid ID (any government-issued ID)</li>
            <li>Barangay Certificate or Residency Certificate</li>
            <li>Proof of available planting area (optional but recommended)</li>
        </ul>

        <h4>Important Notes:</h4>
        <ul>
            <li>Seedlings are distributed on a first-come, first-served basis</li>
            <li>Each household is limited to one request per distribution period</li>
            <li>Recipients are expected to provide updates on seedling growth</li>
            <li>Distribution schedule will be announced via SMS</li>
        </ul>

        <h4>Distribution Information:</h4>
        <p>Seedlings will be distributed at the City Agriculture Office. You will receive an SMS notification with the
            pickup date and time once your request is approved.</p>
    </div>
</section>