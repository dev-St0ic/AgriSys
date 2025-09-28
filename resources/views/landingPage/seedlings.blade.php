<!-- Seedlings Choice Section -->
<section class="seedlings-application-section" id="seedlings-choice" style="display: none;">
    <div class="seedlings-form-header">
        <h2>Seedlings Request</h2>
        <p>Select the seedlings and/or fertilizer you want to request, then click Next.</p>
    </div>
    <form id="seedlings-choice-form">
        <div class="seedlings-categories">
            <!-- Seeds Category -->
            <div class="seedlings-column">
                <strong>Seeds</strong>
                <div class="seedlings-items-grid">
                    <div class="seedlings-option">
                        <img src="{{ asset('images/seedlings/seeds/Emerald Bitter Gourd.jpg') }}" alt="Emerald Bitter Gourd" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="seeds" value="Emerald Bitter Gourd Seeds" onchange="toggleQuantity(this, 'rice-seeds-qty')">
                            <span>Emerald Bitter Gourd</span>
                        </div>
                        <div class="seedlings-quantity-control" id="rice-seeds-qty">
                            <label>Qty:</label>
                            <input type="number" name="emerald_bitter_gourd_seeds_quantity" min="1" value="1">
                        </div>
                        <img src="{{ asset('images/seedlings/seeds/Golden Harvest Rice Seeds.jpg') }}" alt="Golden Harvest Rice Seeds" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="seeds" value="Golden Harvest Rice Seeds" onchange="toggleQuantity(this, 'rice-seeds-qty')">
                            <span>Golden Harvest Rice Seeds</span>
                        </div>
                        <div class="seedlings-quantity-control" id="rice-seeds-qty">
                            <label>Qty:</label>
                            <input type="number" name="golden_harvest_rice_seeds_quantity" min="1" value="1">
                        </div>
                    </div>
                    <div class="seedlings-option">
                        <img src="{{ asset('images/seedlings/seeds/corn.jpg') }}" alt="Pioneer Hybrid Corn Seeds" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="seeds" value="Pioneer Hybrid Corn Seeds" onchange="toggleQuantity(this, 'corn-seeds-qty')">
                            <span>Pioneer Hybrid Corn Seeds</span>
                        </div>
                        <div class="seedlings-quantity-control" id="corn-seeds-qty">
                            <label>Qty:</label>
                            <input type="number" name="pioneer_hybrid_corn_seeds_quantity" min="1" value="1">
                        </div>
                    </div>
                    <div class="seedlings-option">
                        <img src="{{ asset('images/seedlings/seeds/stringbean.jpg') }}" alt="Green Gem String Bean Seeds" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="seeds" value="Green Gem String Bean Seeds" onchange="toggleQuantity(this, 'stringbean-seeds-qty')">
                            <span>Green Gem String Bean Seeds</span>
                        </div>
                        <div class="seedlings-quantity-control" id="stringbean-seeds-qty">
                            <label>Qty:</label>
                            <input type="number" name="green_gem_string_bean_seeds_quantity" min="1" value="1">
                        </div>
                    </div>
                    <div class="seedlings-option">
                        <img src="{{ asset('images/seedlings/seeds/carrot.jpg') }}" alt="Sunshine Carrot Seeds" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="seeds" value="Sunshine Carrot Seeds" onchange="toggleQuantity(this, 'carrot-seeds-qty')">
                            <span>Sunshine Carrot Seeds</span>
                        </div>
                        <div class="seedlings-quantity-control" id="carrot-seeds-qty">
                            <label>Qty:</label>
                            <input type="number" name="sunshine_carrot_seeds_quantity" min="1" value="1">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Seedlings Category -->
            <div class="seedlings-column">
                <strong>Seedlings</strong>
                <div class="seedlings-items-grid">
                    <div class="seedlings-option">
                        <img src="{{ asset('images/seedlings/vegetableSeedlings/sampaguita.jpg') }}" alt="Sampaguita" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="vegetables" value="sampaguita" onchange="toggleQuantity(this, 'sampaguita-qty')">
                            <span>Sampaguita</span>
                        </div>
                        <div class="seedlings-quantity-control" id="sampaguita-qty">
                            <label>Qty:</label>
                            <input type="number" name="sampaguita_quantity" min="1" value="1">
                        </div>
                    </div>
                    <div class="seedlings-option">
                        <img src="{{ asset('images/seedlings/vegetableSeedlings/greenChili.jpg') }}" alt="siling haba" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="vegetables" value="siling haba" onchange="toggleQuantity(this, 'siling-haba-qty')">
                            <span>Siling Haba</span>
                        </div>
                        <div class="seedlings-quantity-control" id="siling-haba-qty">
                            <label>Qty:</label>
                            <input type="number" name="siling_haba_quantity" min="1" value="1">
                        </div>
                    </div>
                    <div class="seedlings-option">
                        <img src="{{ asset('images/seedlings/vegetableSeedlings/eggplant.jpg') }}" alt="eggplant" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="vegetables" value="eggplant" onchange="toggleQuantity(this, 'eggplant-qty')">
                            <span>Eggplant</span>
                        </div>
                        <div class="seedlings-quantity-control" id="eggplant-qty">
                            <label>Qty:</label>
                            <input type="number" name="eggplant_quantity" min="1" value="1">
                        </div>
                    </div>
                    <div class="seedlings-option">
                        <img src="{{ asset('images/seedlings/vegetableSeedlings/tomato.jpg') }}" alt="kamatis" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="vegetables" value="kamatis" onchange="toggleQuantity(this, 'kamatis-qty')">
                            <span>Kamatis</span>
                        </div>
                        <div class="seedlings-quantity-control" id="kamatis-qty">
                            <label>Qty:</label>
                            <input type="number" name="kamatis_quantity" min="1" value="1">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fruit-Bearing Trees Category -->
            <div class="seedlings-column">
                <strong>Fruit-Bearing Trees</strong>
                <div class="seedlings-items-grid">
                    <div class="seedlings-option">
                        <img src="{{ asset('images/seedlings/fruit-bearingSeedlings/calamansi.jpg') }}" alt="calamansi" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="fruits" value="kalamansi" onchange="toggleQuantity(this, 'kalamansi-qty')">
                            <span>Kalamansi</span>
                        </div>
                        <div class="seedlings-quantity-control" id="kalamansi-qty">
                            <label>Qty:</label>
                            <input type="number" name="kalamansi_quantity" min="1" value="1">
                        </div>
                    </div>
                    <div class="seedlings-option">
                        <img src="{{ asset('images/seedlings/fruit-bearingSeedlings/mango.jpg') }}" alt="mangga" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="fruits" value="mangga" onchange="toggleQuantity(this, 'mangga-qty')">
                            <span>Mangga</span>
                        </div>
                        <div class="seedlings-quantity-control" id="mangga-qty">
                            <label>Qty:</label>
                            <input type="number" name="mangga_quantity" min="1" value="1">
                        </div>
                    </div>
                    <div class="seedlings-option">
                        <img src="{{ asset('images/seedlings/fruit-bearingSeedlings/guyabano.jpg') }}" alt="guyabano" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="fruits" value="guyabano" onchange="toggleQuantity(this, 'guyabano-qty')">
                            <span>Guyabano</span>
                        </div>
                        <div class="seedlings-quantity-control" id="guyabano-qty">
                            <label>Qty:</label>
                            <input type="number" name="guyabano_quantity" min="1" value="1">
                        </div>
                    </div>
                    <div class="seedlings-option">
                        <img src="{{ asset('images/seedlings/fruit-bearingSeedlings/lanzones.jpg') }}" alt="lanzones" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="fruits" value="lanzones" onchange="toggleQuantity(this, 'lanzones-qty')">
                            <span>Lanzones</span>
                        </div>
                        <div class="seedlings-quantity-control" id="lanzones-qty">
                            <label>Qty:</label>
                            <input type="number" name="lanzones_quantity" min="1" value="1">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ornamentals Category -->
            <div class="seedlings-column">
                <strong>Ornamentals</strong>
                <div class="seedlings-items-grid">
                    <div class="seedlings-option">
                        <img src="{{ asset('images/seedlings/ornamentals/bougainvillea.jpg') }}" alt="Bougainvillea" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="ornamentals" value="Bougainvillea" onchange="toggleQuantity(this, 'bougainvillea-qty')">
                            <span>Bougainvillea</span>
                        </div>
                        <div class="seedlings-quantity-control" id="bougainvillea-qty">
                            <label>Qty:</label>
                            <input type="number" name="bougainvillea_quantity" min="1" value="1">
                        </div>
                    </div>
                    <div class="seedlings-option">
                        <img src="{{ asset('images/seedlings/ornamentals/gumamela.jpg') }}" alt="Gumamela" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="ornamentals" value="Gumamela" onchange="toggleQuantity(this, 'gumamela-qty')">
                            <span>Gumamela (Hibiscus)</span>
                        </div>
                        <div class="seedlings-quantity-control" id="gumamela-qty">
                            <label>Qty:</label>
                            <input type="number" name="gumamela_quantity" min="1" value="1">
                        </div>
                    </div>
                    <div class="seedlings-option">
                        <img src="{{ asset('images/seedlings/ornamentals/anthurium.jpg') }}" alt="Anthurium" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="ornamentals" value="Anthurium" onchange="toggleQuantity(this, 'anthurium-qty')">
                            <span>Anthurium</span>
                        </div>
                        <div class="seedlings-quantity-control" id="anthurium-qty">
                            <label>Qty:</label>
                            <input type="number" name="anthurium_quantity" min="1" value="1">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fingerlings Category -->
            <div class="seedlings-column">
                <strong>Fingerlings</strong>
                <div class="seedlings-items-grid">
                    <div class="seedlings-option">
                        <img src="{{ asset('images/seedlings/fingerlings/tilapia.jpg') }}" alt="Tilapia Fingerlings" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="fingerlings" value="Tilapia Fingerlings" onchange="toggleQuantity(this, 'tilapia-qty')">
                            <span>Tilapia Fingerlings</span>
                        </div>
                        <div class="seedlings-quantity-control" id="tilapia-qty">
                            <label>Qty:</label>
                            <input type="number" name="tilapia_fingerlings_quantity" min="1" value="1">
                        </div>
                    </div>
                    <div class="seedlings-option">
                        <img src="{{ asset('images/seedlings/fingerlings/bangus.jpg') }}" alt="Milkfish Fingerlings" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="fingerlings" value="Milkfish Fingerlings" onchange="toggleQuantity(this, 'bangus-qty')">
                            <span>Milkfish (Bangus) Fingerlings</span>
                        </div>
                        <div class="seedlings-quantity-control" id="bangus-qty">
                            <label>Qty:</label>
                            <input type="number" name="milkfish_fingerlings_quantity" min="1" value="1">
                        </div>
                    </div>
                    <div class="seedlings-option">
                        <img src="{{ asset('images/seedlings/fingerlings/catfish.jpg') }}" alt="Catfish Fingerlings" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="fingerlings" value="Catfish Fingerlings" onchange="toggleQuantity(this, 'catfish-qty')">
                            <span>Catfish Fingerlings</span>
                        </div>
                        <div class="seedlings-quantity-control" id="catfish-qty">
                            <label>Qty:</label>
                            <input type="number" name="catfish_fingerlings_quantity" min="1" value="1">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fertilizers Category -->
            <div class="seedlings-column">
                <strong>Fertilizers</strong>
                <div class="seedlings-items-grid">
                    <div class="seedlings-option">
                        <img src="{{ asset('images/seedlings/organicFertilizer/urea.jpg') }}" alt="Urea" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="fertilizers" value="Urea (46-0-0)" onchange="toggleQuantity(this, 'urea-qty')">
                            <span>Urea (46-0-0)</span>
                        </div>
                        <div class="seedlings-quantity-control" id="urea-qty">
                            <label>Qty:</label>
                            <input type="number" name="urea_quantity" min="1" value="1">
                        </div>
                    </div>
                    <div class="seedlings-option">
                        <img src="{{ asset('images/seedlings/organicFertilizer/complete.jpg') }}" alt="Complete Fertilizer" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="fertilizers" value="Complete Fertilizer (14-14-14)" onchange="toggleQuantity(this, 'complete-qty')">
                            <span>Complete Fertilizer (14-14-14)</span>
                        </div>
                        <div class="seedlings-quantity-control" id="complete-qty">
                            <label>Qty:</label>
                            <input type="number" name="complete_fertilizer_quantity" min="1" value="1">
                        </div>
                    </div>
                    <div class="seedlings-option">
                        <img src="{{ asset('images/seedlings/organicFertilizer/vermicast.jpg') }}" alt="vermicast" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="fertilizers" value="Vermicast Fertilizer" onchange="toggleQuantity(this, 'vermicast-qty')">
                            <span>Vermicast Fertilizer</span>
                        </div>
                        <div class="seedlings-quantity-control" id="vermicast-qty">
                            <label>Qty:</label>
                            <input type="number" name="vermicast_quantity" min="1" value="1">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div style="text-align:center; margin-top:30px;">
            <button type="button" class="seedlings-next-btn" onclick="proceedToSeedlingsForm()">Next</button>
        </div>
    </form>
</section>

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
