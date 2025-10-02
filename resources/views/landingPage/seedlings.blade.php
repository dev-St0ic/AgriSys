@php
    $categories = \App\Models\RequestCategory::with(['items' => function($query) {
        $query->where('is_active', true)->orderBy('display_order');
    }])->where('is_active', true)->orderBy('display_order')->get();
@endphp

<!-- Seedlings Choice Section -->
<section class="seedlings-application-section" id="seedlings-choice" style="display: none;">
    <div class="seedlings-form-header">
        <h2>Seedlings Request</h2>
        <p>Select the seedlings and/or fertilizer you want to request, then click Next.</p>
    </div>
    <form id="seedlings-choice-form">
        <div class="seedlings-categories">
            @foreach($categories as $category)
                <!-- Dynamic Category -->
                <div class="seedlings-column">
                    <strong>
                        <i class="fas {{ $category->icon ?? 'fa-leaf' }} me-2"></i>
                        {{ $category->display_name }}
                    </strong>
                    @if($category->description)
                        <small class="text-muted d-block mb-2">{{ $category->description }}</small>
                    @endif
                    
                    <div class="seedlings-items-grid">
                        @foreach($category->items as $item)
                            <div class="seedlings-option">
                                @if($item->image_path)
                                    <img src="{{ Storage::url($item->image_path) }}" 
                                         alt="{{ $item->name }}" 
                                         class="seedling-image">
                                @else
                                    <div class="seedling-image d-flex align-items-center justify-content-center bg-light">
                                        <i class="fas fa-image fa-2x text-muted"></i>
                                    </div>
                                @endif
                                
                                <div class="seedlings-checkbox">
                                    <input type="checkbox" 
                                           name="{{ $category->name }}" 
                                           value="{{ $item->name }}" 
                                           data-item-id="{{ $item->id }}"
                                           onchange="toggleQuantity(this, 'qty-{{ $category->name }}-{{ $item->id }}')">
                                    <span>{{ $item->name }}</span>
                                </div>
                                
                                <div class="seedlings-quantity-control" id="qty-{{ $category->name }}-{{ $item->id }}">
                                    <label>Qty:</label>
                                    <input type="number" 
                                           name="quantity_{{ $item->id }}" 
                                           min="{{ $item->min_quantity ?? 1 }}" 
                                           max="{{ $item->max_quantity ?? 999 }}"
                                           value="{{ $item->min_quantity ?? 1 }}">
                                    <span class="text-muted ms-1">{{ $item->unit }}</span>
                                </div>
                                
                                @if($item->description)
                                    <small class="text-muted mt-1">{{ $item->description }}</small>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
        
        <div style="text-align:center; margin-top:30px;">
            <button type="button" class="seedlings-next-btn" onclick="proceedToSeedlingsForm()">Next</button>
        </div>
    </form>
</section>

<!-- Rest of the form (application section) stays the same -->

<!-- Seedlings Application Form Section -->
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
            <!-- Add hidden fields for the selected seedlings -->
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