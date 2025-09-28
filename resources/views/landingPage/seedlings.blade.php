
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
                        <img src="{{ asset('images/seedlings/seeds/Emerald Bitter Gourd Seeds.jpg') }}" alt="Emerald Bitter Gourd Seeds" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="seeds" value="Emerald Bitter Gourd Seeds" onchange="toggleQuantity(this, 'emerald-bitter-gourd-seeds-qty')">
                            <span>Emerald Bitter Gourd Seeds</span>
                        </div>
                        <div class="seedlings-quantity-control" id="emerald-bitter-gourd-seeds-qty">
                            <label>Qty:</label>
                            <input type="number" name="emerald_bitter_gourd_seeds_quantity" min="1" value="1">
                        </div>
                    </div>
                    <div class="seedlings-option">
                        <img src="{{ asset('images/seedlings/seeds/Golden Harvest Rice Seeds.jpg') }}" alt="Golden Harvest Rice Seeds" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="seeds" value="Golden Harvest Rice Seeds" onchange="toggleQuantity(this, 'golden-harvest-rice-seeds-qty')">
                            <span>Golden Harvest Rice Seeds</span>
                        </div>
                        <div class="seedlings-quantity-control" id="golden-harvest-rice-seeds-qty">
                            <label>Qty:</label>
                            <input type="number" name="golden_harvest_rice_seeds_quantity" min="1" value="1">
                        </div>
                    </div>
                    <div class="seedlings-option">
                        <img src="{{ asset('images/seedlings/seeds/Green Gem String Bean Seeds.jpg') }}" alt="Green Gem String Bean Seeds" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="seeds" value="Green Gem String Bean Seeds" onchange="toggleQuantity(this, 'green-gem-string-bean-seeds-qty')">
                            <span>Green Gem String Bean Seeds</span>
                        </div>
                        <div class="seedlings-quantity-control" id="green-gem-string-bean-seeds-qty">
                            <label>Qty:</label>
                            <input type="number" name="green_gem_string_bean_seeds_quantity" min="1" value="1">
                        </div>
                    </div>
                    <div class="seedlings-option">
                        <img src="{{ asset('images/seedlings/seeds/Okra Seeds.jpg') }}" alt="Okra Seeds" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="seeds" value="Okra Seeds" onchange="toggleQuantity(this, 'okra-seeds-qty')">
                            <span>Okra Seeds</span>
                        </div>
                        <div class="seedlings-quantity-control" id="okra-seeds-qty">
                            <label>Qty:</label>
                            <input type="number" name="okra_seeds_quantity" min="1" value="1">
                        </div>
                    </div>
                    <div class="seedlings-option">
                        <img src="{{ asset('images/seedlings/seeds/Pioneer Hybrid Corn Seeds.jpg') }}" alt="Pioneer Hybrid Corn Seeds" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="seeds" value="Pioneer Hybrid Corn Seeds" onchange="toggleQuantity(this, 'pioneer-hybrid-corn-seeds-qty')">
                            <span>Pioneer Hybrid Corn Seeds</span>
                        </div>
                        <div class="seedlings-quantity-control" id="pioneer-hybrid-corn-seeds-qty">
                            <label>Qty:</label>
                            <input type="number" name="pioneer_hybrid_corn_seeds_quantity" min="1" value="1">
                        </div>
                    </div>
                    <div class="seedlings-option">
                        <img src="{{ asset('images/seedlings/seeds/Red Ruby Tomato Seeds.jpg') }}" alt="Red Ruby Tomato Seeds" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="seeds" value="Red Ruby Tomato Seeds" onchange="toggleQuantity(this, 'red-ruby-tomato-seeds-qty')">
                            <span>Red Ruby Tomato Seeds</span>
                        </div>
                        <div class="seedlings-quantity-control" id="red-ruby-tomato-seeds-qty">
                            <label>Qty:</label>
                            <input type="number" name="red_ruby_tomato_seeds_quantity" min="1" value="1">
                        </div>
                    </div>
                    <div class="seedlings-option">
                        <img src="{{ asset('images/seedlings/seeds/Sunshine Carrot Seeds.jpg') }}" alt="Sunshine Carrot Seeds" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="seeds" value="Sunshine Carrot Seeds" onchange="toggleQuantity(this, 'sunshine-carrot-seeds-qty')">
                            <span>Sunshine Carrot Seeds</span>
                        </div>
                        <div class="seedlings-quantity-control" id="sunshine-carrot-seeds-qty">
                            <label>Qty:</label>
                            <input type="number" name="sunshine_carrot_seeds_quantity" min="1" value="1">
                        </div>
                    </div>
                    <div class="seedlings-option">
                        <img src="{{ asset('images/seedlings/seeds/Yellow Pearl Squash Seeds.jpg') }}" alt="Yellow Pearl Squash Seeds" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="seeds" value="Yellow Pearl Squash Seeds" onchange="toggleQuantity(this, 'yellow-pearl-squash-seeds-qty')">
                            <span>Yellow Pearl Squash Seeds</span>
                        </div>
                        <div class="seedlings-quantity-control" id="yellow-pearl-squash-seeds-qty">
                            <label>Qty:</label>
                            <input type="number" name="yellow_pearl_squash_seeds_quantity" min="1" value="1">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Seedlings Category -->
            <div class="seedlings-column">
                <strong>Seedlings</strong>
                <div class="seedlings-items-grid">
                    <div class="seedlings-option">
                        <img src="{{ asset('images/seedlings/seedlings/Avocado Seedling.jpg') }}" alt="Avocado Seedling" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="seedlings" value="Avocado Seedling" onchange="toggleQuantity(this, 'avocado-seedling-qty')">
                            <span>Avocado Seedling</span>
                        </div>
                        <div class="seedlings-quantity-control" id="avocado-seedling-qty">
                            <label>Qty:</label>
                            <input type="number" name="avocado_seedling_quantity" min="1" value="1">
                        </div>
                    </div>
                    <div class="seedlings-option">
                        <img src="{{ asset('images/seedlings/seedlings/Calamansi Seedling.jpg') }}" alt="Calamansi Seedling" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="seedlings" value="Calamansi Seedling" onchange="toggleQuantity(this, 'calamansi-seedling-qty')">
                            <span>Calamansi Seedling</span>
                        </div>
                        <div class="seedlings-quantity-control" id="calamansi-seedling-qty">
                            <label>Qty:</label>
                            <input type="number" name="calamansi_seedling_quantity" min="1" value="1">
                        </div>
                    </div>
                    <div class="seedlings-option">
                        <img src="{{ asset('images/seedlings/seedlings/Guava Seedling.jpg') }}" alt="Guava Seedling" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="seedlings" value="Guava Seedling" onchange="toggleQuantity(this, 'guava-seedling-qty')">
                            <span>Guava Seedling</span>
                        </div>
                        <div class="seedlings-quantity-control" id="guava-seedling-qty">
                            <label>Qty:</label>
                            <input type="number" name="guava_seedling_quantity" min="1" value="1">
                        </div>
                    </div>
                    <div class="seedlings-option">
                        <img src="{{ asset('images/seedlings/seedlings/Guyabano Seedling.jpg') }}" alt="Guyabano Seedling" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="seedlings" value="Guyabano Seedling" onchange="toggleQuantity(this, 'guyabano-seedling-qty')">
                            <span>Guyabano Seedling</span>
                        </div>
                        <div class="seedlings-quantity-control" id="guyabano-seedling-qty">
                            <label>Qty:</label>
                            <input type="number" name="guyabano_seedling_quantity" min="1" value="1">
                        </div>
                    </div>
                    <div class="seedlings-option">
                        <img src="{{ asset('images/seedlings/seedlings/Mango Seedling.jpg') }}" alt="Mango Seedling" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="seedlings" value="Mango Seedling" onchange="toggleQuantity(this, 'mango-seedling-qty')">
                            <span>Mango Seedling</span>
                        </div>
                        <div class="seedlings-quantity-control" id="mango-seedling-qty">
                            <label>Qty:</label>
                            <input type="number" name="mango_seedling_quantity" min="1" value="1">
                        </div>
                    </div>
                    <div class="seedlings-option">
                        <img src="{{ asset('images/seedlings/seedlings/Papaya Seedling.jpg') }}" alt="Papaya Seedling" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="seedlings" value="Papaya Seedling" onchange="toggleQuantity(this, 'papaya-seedling-qty')">
                            <span>Papaya Seedling</span>
                        </div>
                        <div class="seedlings-quantity-control" id="papaya-seedling-qty">
                            <label>Qty:</label>
                            <input type="number" name="papaya_seedling_quantity" min="1" value="1">
                        </div>
                    </div>
                    <div class="seedlings-option">
                        <img src="{{ asset('images/seedlings/seedlings/Santol Seedling.jpg') }}" alt="Santol Seedling" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="seedlings" value="Santol Seedling" onchange="toggleQuantity(this, 'santol-seedling-qty')">
                            <span>Santol Seedling</span>
                        </div>
                        <div class="seedlings-quantity-control" id="santol-seedling-qty">
                            <label>Qty:</label>
                            <input type="number" name="santol_seedling_quantity" min="1" value="1">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fruit-Bearing Trees Category -->
            <div class="seedlings-column">
                <strong>Fruit-Bearing Trees</strong>
                <div class="seedlings-items-grid">
                    <div class="seedlings-option">
                        <img src="{{ asset('images/seedlings/fruit-bearing-trees/Dwarf Coconut Tree.jpg') }}" alt="Drawf Coconut Tree" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="fruits" value="Dwarf Coconut Tree" onchange="toggleQuantity(this, 'drawf-coconut-tree-qty')">
                            <span>Dwarf Coconut Tree</span>
                        </div>
                        <div class="seedlings-quantity-control" id="dwarf-coconut-tree-qty">
                            <label>Qty:</label>
                            <input type="number" name="dwarf_coconut_tree_quantity" min="1" value="1">
                        </div>
                    </div>
                    <div class="seedlings-option">
                        <img src="{{ asset('images/seedlings/fruit-bearing-trees/Lakatan Banana Tree.jpg') }}" alt="Lakatan Banana Tree" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="fruits" value="Lakatan Banana Tree" onchange="toggleQuantity(this, 'lakatan-banana-tree-qty')">
                            <span>Lakatan Banana Tree</span>
                        </div>
                        <div class="seedlings-quantity-control" id="lakatan-banana-tree-qty">
                            <label>Qty:</label>
                            <input type="number" name="lakatan_banana_tree_quantity" min="1" value="1">
                        </div>
                    </div>
                    <div class="seedlings-option">
                        <img src="{{ asset('images/seedlings/fruit-bearing-trees/Rambutan Tree.jpg') }}" alt="Rambutan Tree" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="fruits" value="Rambutan Tree" onchange="toggleQuantity(this, 'rambutan-tree-qty')">
                            <span>Rambutan Tree</span>
                        </div>
                        <div class="seedlings-quantity-control" id="rambutan-tree-qty">
                            <label>Qty:</label>
                            <input type="number" name="rambutan_tree_quantity" min="1" value="1">
                        </div>
                    </div>
                    <div class="seedlings-option">
                        <img src="{{ asset('images/seedlings/fruit-bearing-trees/Star Apple Tree.jpg') }}" alt="Star Apple Tree" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="fruits" value="Star Apple Tree" onchange="toggleQuantity(this, 'star-apple-tree-qty')">
                            <span>Star Apple Tree</span>
                        </div>
                        <div class="seedlings-quantity-control" id="star-apple-tree-qty">
                            <label>Qty:</label>
                            <input type="number" name="star_apple_tree_quantity" min="1" value="1">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ornamentals Category -->
            <div class="seedlings-column">
                <strong>Ornamentals</strong>
                <div class="seedlings-items-grid">
                    <div class="seedlings-option">
                        <img src="{{ asset('images/seedlings/ornamentals/Anthurium.jpg') }}" alt="Anthurium" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="ornamentals" value="Anthurium" onchange="toggleQuantity(this, 'anthurium-qty')">
                            <span>Anthurium</span>
                        </div>
                        <div class="seedlings-quantity-control" id="anthurium-qty">
                            <label>Qty:</label>
                            <input type="number" name="anthurium_quantity" min="1" value="1">
                        </div>
                    </div>
                    <div class="seedlings-option">
                        <img src="{{ asset('images/seedlings/ornamentals/Bougainvillea.jpg') }}" alt="Bougainvillea" class="seedling-image">
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
                        <img src="{{ asset('images/seedlings/ornamentals/Fortune Plant.jpg') }}" alt="Fortune Plant" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="ornamentals" value="Fortune Plant" onchange="toggleQuantity(this, 'fortune-plant-qty')">
                            <span>Fortune Plant</span>
                        </div>
                        <div class="seedlings-quantity-control" id="fortune-plant-qty">
                            <label>Qty:</label>
                            <input type="number" name="fortune_plant_quantity" min="1" value="1">
                        </div>
                    </div>
                    <div class="seedlings-option">
                        <img src="{{ asset('images/seedlings/ornamentals/Gumamela (Hibiscus).jpg') }}" alt="Gumamela (Hibiscus)" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="ornamentals" value="Gumamela (Hibiscus)" onchange="toggleQuantity(this, 'gumamela-qty')">
                            <span>Gumamela (Hibiscus)</span>
                        </div>
                        <div class="seedlings-quantity-control" id="gumamela-qty">
                            <label>Qty:</label>
                            <input type="number" name="gumamela_quantity" min="1" value="1">
                        </div>
                    </div>
                    <div class="seedlings-option">
                        <img src="{{ asset('images/seedlings/ornamentals/Sansevieria (Snake Plant).jpg') }}" alt="Sansevieria (Snake Plant)" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="ornamentals" value="Sansevieria (Snake Plant)" onchange="toggleQuantity(this, 'sansevieria-qty')">
                            <span>Sansevieria (Snake Plant)</span>
                        </div>
                        <div class="seedlings-quantity-control" id="sansevieria-qty">
                            <label>Qty:</label>
                            <input type="number" name="sansevieria_quantity" min="1" value="1">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fingerlings Category -->
            <div class="seedlings-column">
                <strong>Fingerlings</strong>
                <div class="seedlings-items-grid">
                    <div class="seedlings-option">
                        <img src="{{ asset('images/seedlings/fingerlings/Catfish Fingerlings.jpg') }}" alt="Catfish Fingerling" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="fingerlings" value="Catfish Fingerling" onchange="toggleQuantity(this, 'catfish-fingerling-qty')">
                            <span>Catfish Fingerling</span>
                        </div>
                        <div class="seedlings-quantity-control" id="catfish-fingerling-qty">
                            <label>Qty:</label>
                            <input type="number" name="catfish_fingerling_quantity" min="1" value="1">
                        </div>
                    </div>
                    <div class="seedlings-option">
                        <img src="{{ asset('images/seedlings/fingerlings/Milkfish (Bangus) Fingerlings.jpg') }}" alt="Milkfish (Bangus) Fingerling" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="fingerlings" value="Milkfish (Bangus) Fingerling" onchange="toggleQuantity(this, 'milkfish-fingerling-qty')">
                            <span>Milkfish (Bangus) Fingerling</span>
                        </div>
                        <div class="seedlings-quantity-control" id="milkfish-fingerling-qty">
                            <label>Qty:</label>
                            <input type="number" name="milkfish_fingerling_quantity" min="1" value="1">
                        </div>
                    </div>
                    <div class="seedlings-option">
                        <img src="{{ asset('images/seedlings/fingerlings/Tilapia Fingerlings.jpg') }}" alt="Tilapia Fingerlings" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="fingerlings" value="Tilapia Fingerlings" onchange="toggleQuantity(this, 'tilapia-fingerlings-qty')">
                            <span>Tilapia Fingerlings</span>
                        </div>
                        <div class="seedlings-quantity-control" id="tilapia-fingerlings-qty">
                            <label>Qty:</label>
                            <input type="number" name="tilapia_fingerlings_quantity" min="1" value="1">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fertilizers Category -->
            <div class="seedlings-column">
                <strong>Fertilizers</strong>
                <div class="seedlings-items-grid">
                    <div class="seedlings-option">
                        <img src="{{ asset('images/seedlings/fertilizers/Ammonium Sulfate (21-0-0).jpg') }}" alt="Ammonium Sulfate (21-0-0)" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="fertilizers" value="Ammonium Sulfate (21-0-0)" onchange="toggleQuantity(this, 'ammonium-sulfate-qty')">
                            <span>Ammonium Sulfate (21-0-0)</span>
                        </div>
                        <div class="seedlings-quantity-control" id="ammonium-sulfate-qty">
                            <label>Qty:</label>
                            <input type="number" name="ammonium_sulfate_quantity" min="1" value="1">
                        </div>
                    </div>
                    <div class="seedlings-option">
                        <img src="{{ asset('images/seedlings/fertilizers/Humic Acid.jpg') }}" alt="Humic Acid" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="fertilizers" value="Humic Acid" onchange="toggleQuantity(this, 'humic-acid-qty')">
                            <span>Humic Acid</span>
                        </div>
                        <div class="seedlings-quantity-control" id="humic-acid-qty">
                            <label>Qty:</label>
                            <input type="number" name="humic_acid_quantity" min="1" value="1">
                        </div>
                    </div>
                    <div class="seedlings-option">
                        <img src="{{ asset('images/seedlings/fertilizers/Pre-processed Chicken Manure.jpg') }}" alt="Pre-processed Chicken Manure" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="fertilizers" value="Pre-processed Chicken Manure" onchange="toggleQuantity(this, 'pre-processed-chicken-manure-qty')">
                            <span>Pre-processed Chicken Manure</span>
                        </div>
                        <div class="seedlings-quantity-control" id="pre-processed-chicken-manure-qty">
                            <label>Qty:</label>
                            <input type="number" name="pre_processed_chicken_manure_quantity" min="1" value="1">
                        </div>
                    </div>
                    <div class="seedlings-option">
                        <img src="{{ asset('images/seedlings/fertilizers/Urea (46-0-0).jpg') }}" alt="Urea (46-0-0)" class="seedling-image">
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
                        <img src="{{ asset('images/seedlings/fertilizers/Vermicast Fertilizer.jpg') }}" alt="Vermicast Fertilizer" class="seedling-image">
                        <div class="seedlings-checkbox">
                            <input type="checkbox" name="fertilizers" value="Vermicast Fertilizer" onchange="toggleQuantity(this, 'vermicast-fertilizer-qty')">
                            <span>Vermicast Fertilizer</span>
                        </div>
                        <div class="seedlings-quantity-control" id="vermicast-fertilizer-qty">
                            <label>Qty:</label>
                            <input type="number" name="vermicast_fertilizer_quantity" min="1" value="1">
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
