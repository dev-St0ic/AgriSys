@php
    $categories = \App\Models\RequestCategory::with([
        'items' => function ($query) {
            $query->where('is_active', true)->orderBy('name', 'asc');
        },
    ])
        ->where('is_active', true)
        ->orderBy('display_order')
        ->get();
@endphp

<!-- Seedlings Choice Section -->
<section class="seedlings-application-section" id="seedlings-choice" style="display: none;">
    <div class="seedlings-form-header">
        <h2>Supplies & Garden Tools Request</h2>
        <p>Browse and select the items you want to request, then proceed to checkout.</p>
    </div>

    <!-- Category Tabs -->
    <div class="seedlings-category-tabs" id="category-tabs-container">
        <button type="button" class="seedlings-category-tab active" data-category="all">
            <i class="fas fa-th-large"></i> All Items
        </button>
        @foreach ($categories as $category)
            <button type="button" class="seedlings-category-tab" data-category="{{ $category->name }}">
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
        <div class="seedlings-cart-actions">
            <button type="button" class="filter-view-cart-btn" onclick="openCartModal()">
                <i class="fas fa-shopping-cart"></i> <span id="filter-count">0</span>
            </button>
            <button type="button" class="filter-clear-btn" onclick="clearAllSelections()">
                <i></i> Clear All
            </button>
        </div>
    </div>

    <!-- Selected Items Counter -->
    <div style="padding: 0 20px;">
        <div class="seedlings-selection-summary" id="selection-summary" style="display: none;">
            <div class="selection-header" onclick="openCartModal()">
                <div class="selection-count">
                    <i class="fas fa-shopping-cart"></i>
                    <span id="selected-count">0</span> items selected
                </div>
                <button type="button" class="cart-view-btn" onclick="openCartModal(); event.stopPropagation();">
                    <i class="fas fa-eye"></i> View Cart
                </button>
            </div>

            <div class="selection-actions" style="display: flex; align-items: center; gap: 10px;">
                <button type="button" class="seedlings-clear-btn" onclick="clearAllSelections()">
                    <i></i> Clear All
                </button>
                <button type="button" class="seedlings-proceed-btn-mini" onclick="proceedToSeedlingsForm()"
                    style="padding: 8px 20px; background: #ffffff; border: 2px solid #ffffff; border-radius: 6px; color: #40916c; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-arrow-right"></i> Proceed to Application
                </button>
            </div>
        </div>
    </div>

    <!-- Cart Modal -->
    <div class="cart-modal-overlay" id="cartModalOverlay" style="display: none;" onclick="closeCartModal(event)">
        <div class="cart-modal-content" onclick="event.stopPropagation()">
            <div class="cart-modal-header">
                <h3><i class="fas fa-shopping-cart"></i> Your Selected Items</h3>
                <button class="cart-modal-close" onclick="closeCartModal(event)">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="cart-modal-body" id="cart-modal-items">
                <!-- Selected items will be populated here by JavaScript -->
            </div>
            <div class="cart-modal-footer">
                <button type="button" class="cart-modal-clear-btn"
                    onclick="clearAllSelections(); closeCartModal(event);">
                    <i class="fas fa-trash-alt"></i> Clear All
                </button>
                <button type="button" class="cart-modal-proceed-btn" onclick="proceedToSeedlingsForm()">
                    Proceed to Application <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>
    </div>

    <form id="seedlings-choice-form">
        <!-- Items Grid -->
        <div class="seedlings-items-grid" id="items-grid">
            @foreach ($categories as $category)
                @foreach ($category->items as $item)
                    <div class="seedlings-item-card" data-category="{{ $category->name }}"
                        data-item-name="{{ strtolower($item->name) }}" data-stock="{{ $item->current_supply }}"
                        data-stock-status="{{ $item->stock_status }}">

                        <!-- Stock Badge -->
                        <div class="seedlings-stock-badge badge-{{ $item->stock_status }}">
                            @if ($item->stock_status === 'in_stock')
                                <i class="fas fa-check-circle"></i> In Stock
                            @elseif($item->stock_status === 'low_stock')
                                <i class="fas fa-exclamation-triangle"></i> Low Stock
                            @else
                                <i class="fas fa-times-circle"></i> Out of Stock
                            @endif
                        </div>

                        <!-- Item Image -->
                        <div class="seedlings-item-image">
                            @if ($item->image_path)
                                <img src="{{ Storage::url($item->image_path) }}" alt="{{ $item->name }}">
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
                            @if ($item->description)
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
                                <input type="checkbox" name="{{ $category->name }}" value="{{ $item->name }}"
                                    data-item-id="{{ $item->id }}"
                                    onchange="toggleItemSelection(this, '{{ $item->id }}')"
                                    {{ $item->current_supply <= 0 ? 'disabled' : '' }}>
                                <span class="checkbox-custom"></span>
                                <span class="checkbox-text">Select</span>
                            </label>

                            <div class="seedlings-quantity-input" id="qty-wrapper-{{ $item->id }}"
                                style="display: none;">
                                <button type="button" class="qty-btn"
                                    onclick="decrementQty('{{ $item->id }}')">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" id="qty-{{ $item->id }}"
                                    name="quantity_{{ $item->id }}" min="{{ $item->min_quantity ?? 1 }}"
                                    max="{{ $item->current_supply }}" value="1" class="qty-input"
                                    onchange="updateQuantity('{{ $item->id }}')">
                                <button type="button" class="qty-btn"
                                    onclick="incrementQty('{{ $item->id }}')">
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

        <!-- Pagination Controls -->
        <div class="seedlings-pagination" id="pagination" style="display: none;">
            <button type="button" class="pagination-btn" id="prev-page" onclick="changePage(-1)">
                <i class="fas fa-chevron-left"></i> Previous
            </button>
            <div class="pagination-info">
                <span id="current-page">1</span> / <span id="total-pages">1</span>
            </div>
            <button type="button" class="pagination-btn" id="next-page" onclick="changePage(1)">
                Next <i class="fas fa-chevron-right"></i>
            </button>
        </div>

        <!-- Proceed Button -->
        <div class="seedlings-proceed-section">
            <button type="button" class="seedlings-proceed-btn" onclick="proceedToSeedlingsForm()" disabled
                id="proceed-btn">
                <i class="fas fa-arrow-right"></i> Proceed to Application Form
            </button>
        </div>
    </form>

    <!-- Quick View Modal -->
    <div class="seedlings-quick-view-modal" id="quickViewModal" style="display: none;"
        onclick="closeQuickView(event)">
        <div class="quick-view-content" onclick="event.stopPropagation()">
            <button class="quick-view-close" onclick="closeQuickView(event)">
                <i class="fas fa-times"></i>
            </button>
            <div class="quick-view-image">
                <img id="qv-image" src="" alt="">
            </div>
            <div class="quick-view-info">
                <div class="qv-category" id="qv-category"></div>
                <h3 class="qv-name" id="qv-name"></h3>
                <div class="qv-stock-badge" id="qv-stock-badge"></div>
                <p class="qv-description" id="qv-description"></p>
                <div class="qv-availability">
                    <span class="qv-label">Available:</span>
                    <span class="qv-value" id="qv-stock"></span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Application Form Section -->
<section class="application-section" id="seedlings-form" style="display: none;">
    <div class="seedlings-form-header">
        <h2>Supplies & Garden Tools Application Form</h2>
        <p>Please fill out your personal information below.</p>
    </div>

    <div class="seedlings-form-tabs">
        <button type="button" class="seedlings-tab-btn active"
            onclick="showSeedlingsTab('seedlings-form-tab', event)">
            Application Form
        </button>
        <button type="button" class="seedlings-tab-btn" onclick="showSeedlingsTab('seedlings-summary-tab', event)">
            Selected Items <span class="tab-badge" id="selected-count">0</span>
        </button>
        <button type="button" class="seedlings-tab-btn"
            onclick="showSeedlingsTab('seedlings-requirements-tab', event)">
            Information
        </button>
    </div>

    <div id="seedlings-form-tab" class="seedlings-tab-content" style="display: block;">
        <form id="seedlings-request-form" action="{{ route('apply.seedlings') }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="selected_seedlings" name="selected_seedlings" value="">

            <div class="seedlings-form-group">
                <label for="seedlings-first_name">First Name <span class="required-asterisk">*</span></label>
                <input type="text" id="seedlings-first_name" name="first_name" pattern="[a-zA-Z\s'\-]+"
                    title="First name can only contain letters, spaces, hyphens, and apostrophes"
                    placeholder="Example: Juan" required>
                <span class="validation-warning" id="seedlings-first_name-warning"
                    style="color: #ff6b6b; font-size: 0.875rem; display: none; margin-top: 4px;">Only letters, spaces,
                    hyphens, and apostrophes are allowed</span>
            </div>

            <div class="seedlings-form-group">
                <label for="seedlings-middle_name">Middle Name (Optional)</label>
                <input type="text" id="seedlings-middle_name" name="middle_name" pattern="[a-zA-Z\s'\-]+"
                    placeholder="Example: Santos"
                    title="Middle name can only contain letters, spaces, hyphens, and apostrophes">
                <span class="validation-warning" id="seedlings-middle_name-warning"
                    style="color: #ff6b6b; font-size: 0.875rem; display: none; margin-top: 4px;">Only letters, spaces,
                    hyphens, and apostrophes are allowed</span>
            </div>

            <div class="seedlings-form-group">
                <label for="seedlings-last_name">Last Name <span class="required-asterisk">*</span></label>
                <input type="text" id="seedlings-last_name" name="last_name" pattern="[a-zA-Z\s'\-]+"
                    placeholder="Example: Dela Cruz"
                    title="Last name can only contain letters, spaces, hyphens, and apostrophes" required>
                <span class="validation-warning" id="seedlings-last_name-warning"
                    style="color: #ff6b6b; font-size: 0.875rem; display: none; margin-top: 4px;">Only letters, spaces,
                    hyphens, and apostrophes are allowed</span>
            </div>

            <div class="seedlings-form-group">
                <label for="seedlings-extension_name">Name Extension (Optional)</label>
                <select id="seedlings-extension_name" name="extension_name">
                    <option value="" selected>Select Extension</option>
                    <option value="Jr.">Jr.</option>
                    <option value="Sr.">Sr.</option>
                    <option value="II">II</option>
                    <option value="III">III</option>
                    <option value="IV">IV</option>
                    <option value="V">V</option>
                </select>
            </div>

            <div class="seedlings-form-group">
                <label for="seedlings-mobile">Contact Number <span class="required-asterisk">*</span></label>
                <input type="tel" id="seedlings-mobile" name="mobile" placeholder="Example: 09123456789"
                    pattern="^09\d{9}$" title="Contact number must be in the format 09XXXXXXXXX (e.g., 09123456789)"
                    required>
                <span class="validation-warning" id="seedlings-mobile-warning"
                    style="color: #ff6b6b; font-size: 0.875rem; display: none; margin-top: 4px;">Contact number must be
                    in format 09XXXXXXXXX (11 digits)</span>
            </div>

            <div class="seedlings-form-group">
                <label for="seedlings-barangay">Barangay <span class="required-asterisk">*</span></label>
                <select id="seedlings-barangay" name="barangay" required>
                    <option value="" disabled selected>Select Barangay</option>
                    <option value="Bagong Silang">Bagong Silang</option>
                    <option value="Calendola">Calendola</option>
                    <option value="Chrysanthemum">Chrysanthemum</option>
                    <option value="Cuyab">Cuyab</option>
                    <option value="Estrella">Estrella</option>
                    <option value="Fatima">Fatima</option>
                    <option value="G.S.I.S.">G.S.I.S.</option>
                    <option value="Landayan">Landayan</option>
                    <option value="Langgam">Langgam</option>
                    <option value="Laram">Laram</option>
                    <option value="Magsaysay">Magsaysay</option>
                    <option value="Maharlika">Maharlika</option>
                    <option value="Narra">Narra</option>
                    <option value="Nueva">Nueva</option>
                    <option value="Pacita 1">Pacita 1</option>
                    <option value="Pacita 2">Pacita 2</option>
                    <option value="Poblacion">Poblacion</option>
                    <option value="Riverside">Riverside</option>
                    <option value="Rosario">Rosario</option>
                    <option value="Sampaguita Village">Sampaguita Village</option>
                    <option value="San Antonio">San Antonio</option>
                    <option value="San Lorenzo Ruiz">San Lorenzo Ruiz</option>
                    <option value="San Roque">San Roque</option>
                    <option value="San Vicente">San Vicente</option>
                    <option value="Santo Niño">Santo Niño</option>
                    <option value="United Bayanihan">United Bayanihan</option>
                    <option value="United Better Living">United Better Living</option>
                </select>
            </div>

            <div class="seedlings-form-group" id="supporting-docs-field">
                <label for="seedlings-docs">Supporting Documents (Optional)</label>
                <input type="file" id="seedlings-docs" name="supporting_documents" accept=".pdf,.jpg,.jpeg,.png">
                <small>Upload Government ID, Barangay Certificate, or proof of planting area (PDF, JPG, PNG - Max 10MB).
                    Photos of your farm or planting area are very helpful.</small>
            </div>

            <div class="seedlings-form-buttons">
                <button type="button" class="seedlings-cancel-btn" onclick="backToSeedlingsChoice()">Back</button>
                <button type="submit" class="seedlings-submit-btn">Submit Request</button>
            </div>
        </form>
    </div>

    <div id="seedlings-requirements-tab" class="seedlings-tab-content" style="display: none;">
        <!-- DSS Report Information -->
        @if (isset($seedlingReport) && $seedlingReport['exists'])
            <div
                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px; border-radius: 8px; margin-bottom: 25px; color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <h4 style="margin: 0 0 15px 0; color: white; font-size: 1.1rem; display: flex; align-items: center;">
                    <svg style="width: 24px; height: 24px; margin-right: 10px;" fill="currentColor"
                        viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                        <path fill-rule="evenodd"
                            d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"
                            clip-rule="evenodd"></path>
                    </svg>
                    Latest DSS Analytics Report
                </h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                    <div
                        style="background: rgba(255,255,255,0.1); padding: 12px; border-radius: 6px; backdrop-filter: blur(10px);">
                        <div style="font-size: 0.85rem; opacity: 0.9; margin-bottom: 5px;">📅 Report Generated</div>
                        <div style="font-weight: 600; font-size: 0.95rem;">
                            {{ \Carbon\Carbon::parse($seedlingReport['generated_at'])->format('M d, Y H:i:s') }}</div>
                    </div>
                    <div
                        style="background: rgba(255,255,255,0.1); padding: 12px; border-radius: 6px; backdrop-filter: blur(10px);">
                        <div style="font-size: 0.85rem; opacity: 0.9; margin-bottom: 5px;">🤖 Analysis Source</div>
                        <div style="font-weight: 600; font-size: 0.95rem;">{{ ucfirst($seedlingReport['source']) }}
                        </div>
                    </div>
                    <div
                        style="background: rgba(255,255,255,0.1); padding: 12px; border-radius: 6px; backdrop-filter: blur(10px);">
                        <div style="font-size: 0.85rem; opacity: 0.9; margin-bottom: 5px;">📊 Data Period</div>
                        <div style="font-weight: 600; font-size: 0.95rem;">{{ $seedlingReport['period_label'] }}</div>
                    </div>
                </div>
            </div>
        @endif

        <h3>Requirements for Supplies & Garden Tools Request</h3>

        <h4>For Walk-in Individual Clients:</h4>
        <ul>
            <li>No documentary requirements.</li>
            <li>Client must fill out:</li>
            <ul>
                <li>Seedling/Seeds/Fertilizer Dispersal Masterlist</li>
                <li>Client Feedback Form</li>
            </ul>
        </ul>

        <h4>For Institutional Clients (Barangay, Schools, NGOs, Associations):</h4>
        <ul>
            <li>Request Letter addressed to the Honorable City Mayor, signed by the requesting party.</li>
        </ul>

        <h4>For Seeds and Organic Fertilizer Requests:</h4>
        <ul>
            <li>One (1) photocopy of a valid government-issued ID.</li>
        </ul>

        <h4>For Tilapia Fingerlings Requests:</h4>
        <ul>
            <li>Request Letter addressed to the Honorable City Mayor.</li>
            <li>Subject to inspection of the proposed grow-out area by City Agriculture Office personnel.</li>
        </ul>

        <h4>Important Notes:</h4>
        <ul>
            <li>AgriSys registration is provided to facilitate online requests, tracking, and record-keeping. Walk-in
                clients without an account may still avail of the service.</li>
            <li>Distribution is free and subject to availability.</li>
            <li>All requests are processed through the City Agriculture Office following approval procedures outlined in
                the Citizen’s Charter.</li>
        </ul>

        <h4>Distribution Information:</h4>
        <p>You may pick up the requested supplies at the City Agriculture Office. You will receive an SMS notification
            once your request is approved.</p>
    </div>

    <div id="seedlings-summary-tab" class="seedlings-tab-content" style="display: none;">
        <!-- Summary will be populated here by JavaScript -->
    </div>
</section>

<script>
    // Real-time validation for name fields
    document.addEventListener('DOMContentLoaded', function() {
        const nameFields = [{
                id: 'seedlings-first_name',
                pattern: /^[a-zA-Z\s\'-]*$/
            },
            {
                id: 'seedlings-middle_name',
                pattern: /^[a-zA-Z\s\'-]*$/
            },
            {
                id: 'seedlings-last_name',
                pattern: /^[a-zA-Z\s\'-]*$/
            },
            {
                id: 'seedlings-extension_name',
                pattern: /^[a-zA-Z.\s]*$/
            }
        ];

        nameFields.forEach(field => {
            const input = document.getElementById(field.id);
            const warning = document.getElementById(field.id + '-warning');

            if (input && warning) {
                input.addEventListener('input', function(e) {
                    const value = e.target.value;

                    if (!field.pattern.test(value)) {
                        warning.style.display = 'block';
                        input.style.borderColor = '#ff6b6b';
                    } else {
                        warning.style.display = 'none';
                        input.style.borderColor = '';
                    }
                });

                input.addEventListener('blur', function(e) {
                    if (!field.pattern.test(e.target.value) && e.target.value !== '') {
                        warning.style.display = 'block';
                        input.style.borderColor = '#ff6b6b';
                    }
                });
            }
        });
    });
</script>
