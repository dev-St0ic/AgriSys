<!-- Seedlings Choice Section -->
<section class="application-section" id="seedlings-choice" style="display: none;">
    <div class="form-header">
        <h2>Seedlings Request</h2>
        <p>Select the seedlings and/or fertilizer you want to request, then click Next.</p>
    </div>
    <form id="seedlings-choice-form">
        <div style="display: flex; gap: 30px; justify-content: center; flex-wrap: wrap;">
            <!-- Vegetable Column -->
            <div style="flex:1; min-width:200px;">
                <strong>Vegetable Seedlings</strong><br>
                <div class="seedling-option">
                    <div class="seedling-checkbox">
                        <input type="checkbox" name="vegetables" value="sampaguita" onchange="toggleQuantity(this, 'sampaguita-qty')">
                        <span>Sampaguita</span>
                    </div>
                    <div class="quantity-control" id="sampaguita-qty">
                        <label>Quantity:</label>
                        <input type="number" name="sampaguita_quantity" min="1" value="1">
                    </div>
                </div>
                <div class="seedling-option">
                    <div class="seedling-checkbox">
                        <input type="checkbox" name="vegetables" value="siling haba" onchange="toggleQuantity(this, 'siling-haba-qty')">
                        <span>Siling Haba</span>
                    </div>
                    <div class="quantity-control" id="siling-haba-qty">
                        <label>Quantity:</label>
                        <input type="number" name="siling_haba_quantity" min="1" value="1">
                    </div>
                </div>
                <div class="seedling-option">
                    <div class="seedling-checkbox">
                        <input type="checkbox" name="vegetables" value="siling labuyo" onchange="toggleQuantity(this, 'siling-labuyo-qty')">
                        <span>Siling Labuyo</span>
                    </div>
                    <div class="quantity-control" id="siling-labuyo-qty">
                        <label>Quantity:</label>
                        <input type="number" name="siling_labuyo_quantity" min="1" value="1">
                    </div>
                </div>
                <div class="seedling-option">
                    <div class="seedling-checkbox">
                        <input type="checkbox" name="vegetables" value="eggplant" onchange="toggleQuantity(this, 'eggplant-qty')">
                        <span>Eggplant</span>
                    </div>
                    <div class="quantity-control" id="eggplant-qty">
                        <label>Quantity:</label>
                        <input type="number" name="eggplant_quantity" min="1" value="1">
                    </div>
                </div>
                <div class="seedling-option">
                    <div class="seedling-checkbox">
                        <input type="checkbox" name="vegetables" value="kamatis" onchange="toggleQuantity(this, 'kamatis-qty')">
                        <span>Kamatis</span>
                    </div>
                    <div class="quantity-control" id="kamatis-qty">
                        <label>Quantity:</label>
                        <input type="number" name="kamatis_quantity" min="1" value="1">
                    </div>
                </div>
                <div class="seedling-option">
                    <div class="seedling-checkbox">
                        <input type="checkbox" name="vegetables" value="okra" onchange="toggleQuantity(this, 'okra-qty')">
                        <span>Okra</span>
                    </div>
                    <div class="quantity-control" id="okra-qty">
                        <label>Quantity:</label>
                        <input type="number" name="okra_quantity" min="1" value="1">
                    </div>
                </div>
                <div class="seedling-option">
                    <div class="seedling-checkbox">
                        <input type="checkbox" name="vegetables" value="kalabasa" onchange="toggleQuantity(this, 'kalabasa-qty')">
                        <span>Kalabasa</span>
                    </div>
                    <div class="quantity-control" id="kalabasa-qty">
                        <label>Quantity:</label>
                        <input type="number" name="kalabasa_quantity" min="1" value="1">
                    </div>
                </div>
                <div class="seedling-option">
                    <div class="seedling-checkbox">
                        <input type="checkbox" name="vegetables" value="upo" onchange="toggleQuantity(this, 'upo-qty')">
                        <span>Upo</span>
                    </div>
                    <div class="quantity-control" id="upo-qty">
                        <label>Quantity:</label>
                        <input type="number" name="upo_quantity" min="1" value="1">
                    </div>
                </div>
                <div class="seedling-option">
                    <div class="seedling-checkbox">
                        <input type="checkbox" name="vegetables" value="pipino" onchange="toggleQuantity(this, 'pipino-qty')">
                        <span>Pipino</span>
                    </div>
                    <div class="quantity-control" id="pipino-qty">
                        <label>Quantity:</label>
                        <input type="number" name="pipino_quantity" min="1" value="1">
                    </div>
                </div>
            </div>
            <!-- Fruit Column -->
            <div style="flex:1; min-width:200px;">
                <strong>Fruit-bearing Seedlings</strong><br>
                <div class="seedling-option">
                    <div class="seedling-checkbox">
                        <input type="checkbox" name="fruits" value="kalamansi" onchange="toggleQuantity(this, 'kalamansi-qty')">
                        <span>Kalamansi</span>
                    </div>
                    <div class="quantity-control" id="kalamansi-qty">
                        <label>Quantity:</label>
                        <input type="number" name="kalamansi_quantity" min="1" value="1">
                    </div>
                </div>
                <div class="seedling-option">
                    <div class="seedling-checkbox">
                        <input type="checkbox" name="fruits" value="guyabano" onchange="toggleQuantity(this, 'guyabano-qty')">
                        <span>Guyabano</span>
                    </div>
                    <div class="quantity-control" id="guyabano-qty">
                        <label>Quantity:</label>
                        <input type="number" name="guyabano_quantity" min="1" value="1">
                    </div>
                </div>
                <div class="seedling-option">
                    <div class="seedling-checkbox">
                        <input type="checkbox" name="fruits" value="lanzones" onchange="toggleQuantity(this, 'lanzones-qty')">
                        <span>Lanzones</span>
                    </div>
                    <div class="quantity-control" id="lanzones-qty">
                        <label>Quantity:</label>
                        <input type="number" name="lanzones_quantity" min="1" value="1">
                    </div>
                </div>
                <div class="seedling-option">
                    <div class="seedling-checkbox">
                        <input type="checkbox" name="fruits" value="mangga" onchange="toggleQuantity(this, 'mangga-qty')">
                        <span>Mangga</span>
                    </div>
                    <div class="quantity-control" id="mangga-qty">
                        <label>Quantity:</label>
                        <input type="number" name="mangga_quantity" min="1" value="1">
                    </div>
                </div>
            </div>
            <!-- Fertilizer Column -->
            <div style="flex:1; min-width:200px;">
                <strong>Organic Fertilizer</strong><br>
                <div class="seedling-option">
                    <div class="seedling-checkbox">
                        <input type="checkbox" name="fertilizers" value="chicken manure" onchange="toggleQuantity(this, 'chicken-manure-qty')">
                        <span>Pre-processed Chicken Manure</span>
                    </div>
                    <div class="quantity-control" id="chicken-manure-qty">
                        <label>Quantity:</label>
                        <input type="number" name="chicken_manure_quantity" min="1" value="1">
                    </div>
                </div>
                <div class="seedling-option">
                    <div class="seedling-checkbox">
                        <input type="checkbox" name="fertilizers" value="humic acid" onchange="toggleQuantity(this, 'humic-acid-qty')">
                        <span>Humic Acid</span>
                    </div>
                    <div class="quantity-control" id="humic-acid-qty">
                        <label>Quantity:</label>
                        <input type="number" name="humic_acid_quantity" min="1" value="1">
                    </div>
                </div>
                <div class="seedling-option">
                    <div class="seedling-checkbox">
                        <input type="checkbox" name="fertilizers" value="vermicast" onchange="toggleQuantity(this, 'vermicast-qty')">
                        <span>Vermicast</span>
                    </div>
                    <div class="quantity-control" id="vermicast-qty">
                        <label>Quantity:</label>
                        <input type="number" name="vermicast_quantity" min="1" value="1">
                    </div>
                </div>
            </div>
        </div>
        <div style="text-align:center; margin-top:30px;">
            <button type="button" class="seedling-next-btn" onclick="proceedToSeedlingsForm()">Next</button>
        </div>
    </form>
</section>

<!-- Seedlings Application Form Section -->
<section class="application-section" id="seedlings-form" style="display: none;">
    <div id="seedlings-summary" style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #40916c; display: none;">
        <!-- Summary will be populated here -->
    </div>
    
    <div class="form-header">
        <h2>Seedlings Application Form</h2>
        <p>Please fill out your personal information below.</p>
    </div>
    
    <div class="form-tabs">
        <button type="button" class="tab-btn active" onclick="showTab('seedlings-form-tab', event)">Application Form</button>
        <button type="button" class="tab-btn" onclick="showTab('seedlings-requirements-tab', event)">Requirements</button>
    </div>

    <div id="seedlings-form-tab" class="tab-content" style="display: block;">
       <form id="seedlings-request-form" action="{{ route('apply.seedlings') }}" method="POST" enctype="multipart/form-data">
    @csrf
            <!-- Add hidden fields for the selected seedlings -->
            <input type="hidden" id="selected_seedlings" name="selected_seedlings" value="">
            <label for="first_name">First Name</label>
            <input type="text" id="first_name" name="first_name" required>

            <label for="middle_name">Middle Name (Optional)</label>
            <input type="text" id="middle_name" name="middle_name">

            <label for="last_name">Last Name</label>
            <input type="text" id="last_name" name="last_name" required>

            <label for="mobile">Mobile Number</label>
            <input type="tel" id="mobile" name="mobile" required>
            <small>Please provide a valid mobile number for SMS notifications.</small>

            <label for="barangay">Barangay</label>
            <select id="barangay" name="barangay" required>
                <option value="" disabled>Select Barangay</option>
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

            <label for="address">Complete Address</label>
            <input type="text" id="address" name="address" required>
            <small>Include house number, street, subdivision if applicable.</small>

            <div id="supporting-docs-field">
                <label for="seedlings-docs">Supporting Documents</label>
                <input type="file" id="seedlings-docs" name="supporting_documents" accept=".pdf,.jpg,.jpeg,.png" multiple>
                <small>Upload supporting documents (proof of planting area).</small>
            </div>

            <div class="form-buttons">
                <button type="button" class="cancel-btn" onclick="backToSeedlingsChoice()">Back</button>
                <button type="submit" class="submit-btn">Submit Request</button>
            </div>
        </form>
    </div>

    <div id="seedlings-requirements-tab" class="tab-content" style="display: none;">
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
        <p>Seedlings will be distributed at the City Agriculture Office. You will receive an SMS notification with the pickup date and time once your request is approved.</p>
    </div>
</section>