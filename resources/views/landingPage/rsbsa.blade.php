<!-- RSBSA Registration Form - CORRECTED: Farm Location ONLY for Farmers -->
<section class="rsbsa-application-section" id="rsbsa-form" style="display: none;">
    <div class="rsbsa-form-header">
        <h2>RSBSA Registration</h2>
        <p>Enrollment of farmers, fisherfolk, livestock and poultry raisers under the Registry System for Basic Sectors
            in Agriculture (RSBSA).</p>
    </div>

    <div class="rsbsa-form-tabs">
        <button class="rsbsa-tab-btn active" onclick="showRSBSATab('form', event)">Application Form</button>
        <button class="rsbsa-tab-btn" onclick="showRSBSATab('requirements', event)">Requirements</button>
        <button class="rsbsa-tab-btn" onclick="showRSBSATab('information', event)">Information</button>
    </div>

    <div class="rsbsa-tab-content" id="form" style="display: block;">
        <!-- Success/Error Messages -->
        @if (session('success'))
            <div class="alert alert-success"
                style="padding: 15px; margin-bottom: 20px; background-color: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; color: #155724;">
                <strong>✓ Success!</strong> {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger"
                style="padding: 15px; margin-bottom: 20px; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; color: #721c24;">
                <strong>✗ Error!</strong> {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger"
                style="padding: 15px; margin-bottom: 20px; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; color: #721c24;">
                <strong>✗ Please fix the following errors:</strong>
                <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="/apply/rsbsa" enctype="multipart/form-data" id="rsbsa-form">
            @csrf
            <input type="hidden" name="_token" value="{{ csrf_token() }}">

            <!-- BASIC INFORMATION -->
            <div class="rsbsa-form-group">
                <label>First Name <span style="color: #dc3545; font-weight: bold;">*</span></label>
                <input type="text" id="rsbsa-first_name" name="first_name" placeholder="Example: Juan"
                    pattern="[a-zA-Z\s'.\-]*"
                    title="First name can only contain letters, spaces, hyphens, and apostrophes"
                    value="{{ old('first_name') }}" required>
                <span class="validation-warning" id="rsbsa-first_name-warning"
                    style="color: #ff6b6b; font-size: 0.875rem; display: none; margin-top: 4px;">Only letters, spaces,
                    hyphens, and apostrophes are allowed</span>
                @error('first_name')
                    <span
                        style="color: #dc3545; font-size: 0.875rem; display: block; margin-top: 4px;">{{ $message }}</span>
                @enderror
            </div>

            <div class="rsbsa-form-group">
                <label>Middle Name (Optional)</label>
                <input type="text" id="rsbsa-middle_name" name="middle_name" placeholder="Example: Santos"
                    pattern="[a-zA-Z\s'\-]+"
                    title="Middle name can only contain letters, spaces, hyphens, and apostrophes"
                    value="{{ old('middle_name') }}">
                <span class="validation-warning" id="rsbsa-middle_name-warning"
                    style="color: #ff6b6b; font-size: 0.875rem; display: none; margin-top: 4px;">Only letters, spaces,
                    hyphens, and apostrophes are allowed</span>
                @error('middle_name')
                    <span
                        style="color: #dc3545; font-size: 0.875rem; display: block; margin-top: 4px;">{{ $message }}</span>
                @enderror
            </div>

            <div class="rsbsa-form-group">
                <label>Last Name <span style="color: #dc3545; font-weight: bold;">*</span></label>
                <input type="text" id="rsbsa-last_name" name="last_name" placeholder="Example: Dela Cruz"
                    pattern="[a-zA-Z\s'\-]+"
                    title="Last name can only contain letters, spaces, hyphens, and apostrophes"
                    value="{{ old('last_name') }}" required>
                <span class="validation-warning" id="rsbsa-last_name-warning"
                    style="color: #ff6b6b; font-size: 0.875rem; display: none; margin-top: 4px;">Only letters, spaces,
                    hyphens, and apostrophes are allowed</span>
                @error('last_name')
                    <span
                        style="color: #dc3545; font-size: 0.875rem; display: block; margin-top: 4px;">{{ $message }}</span>
                @enderror
            </div>

            <div class="rsbsa-form-group">
                <label>Name Extension (Optional)</label>
                <select id="rsbsa-name_extension" name="name_extension">
                    <option value="" selected>Select Extension</option>
                    <option value="Jr." {{ old('name_extension') == 'Jr.' ? 'selected' : '' }}>Jr.</option>
                    <option value="Sr." {{ old('name_extension') == 'Sr.' ? 'selected' : '' }}>Sr.</option>
                    <option value="II" {{ old('name_extension') == 'II' ? 'selected' : '' }}>II</option>
                    <option value="III" {{ old('name_extension') == 'III' ? 'selected' : '' }}>III</option>
                    <option value="IV" {{ old('name_extension') == 'IV' ? 'selected' : '' }}>IV</option>
                    <option value="V" {{ old('name_extension') == 'V' ? 'selected' : '' }}>V</option>
                </select>
                @error('name_extension')
                    <span
                        style="color: #dc3545; font-size: 0.875rem; display: block; margin-top: 4px;">{{ $message }}</span>
                @enderror
            </div>

            <div class="rsbsa-form-group">
                <label>Sex <span style="color: #dc3545; font-weight: bold;">*</span></label>
                <select name="sex" id="rsbsa-sex" required>
                    <option value="" disabled {{ old('sex') ? '' : 'selected' }}>Select Sex</option>
                    <option value="Male" {{ old('sex') == 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ old('sex') == 'Female' ? 'selected' : '' }}>Female</option>
                    <option value="Preferred not to say" {{ old('sex') == 'Preferred not to say' ? 'selected' : '' }}>
                        Preferred not to say</option>
                </select>
                @error('sex')
                    <span
                        style="color: #dc3545; font-size: 0.875rem; display: block; margin-top: 4px;">{{ $message }}</span>
                @enderror
            </div>

            <div class="rsbsa-form-group">
                <label>Barangay <span style="color: #dc3545; font-weight: bold;">*</span></label>
                <select name="barangay" id="rsbsa-barangay" required>
                    <option value="" disabled selected>Select Barangay</option>
                    <option value="Bagong Silang">Bagong Silang</option>
                    <option value="Cuyab">Cuyab</option>
                    <option value="Estrella">Estrella</option>
                    <option value="G.S.I.S.">G.S.I.S.</option>
                    <option value="Landayan">Landayan</option>
                    <option value="Langgam">Langgam</option>
                    <option value="Laram">Laram</option>
                    <option value="Magsaysay">Magsaysay</option>
                    <option value="Nueva">Nueva</option>
                    <option value="Poblacion">Poblacion</option>
                    <option value="Riverside">Riverside</option>
                    <option value="San Antonio">San Antonio</option>
                    <option value="San Roque">San Roque</option>
                    <option value="San Vicente">San Vicente</option>
                    <option value="Santo Niño">Santo Niño</option>
                    <option value="United Bayanihan">United Bayanihan</option>
                    <option value="United Better Living">United Better Living</option>
                    <option value="Sampaguita Village">Sampaguita Village</option>
                    <option value="Calendola">Calendola</option>
                    <option value="Narra">Narra</option>
                    <option value="Chrysanthemum">Chrysanthemum</option>
                    <option value="Fatima">Fatima</option>
                    <option value="Maharlika">Maharlika</option>
                    <option value="Pacita 1">Pacita 1</option>
                    <option value="Pacita 2">Pacita 2</option>
                    <option value="Rosario">Rosario</option>
                    <option value="San Lorenzo Ruiz">San Lorenzo Ruiz</option>
                </select>
                @error('barangay')
                    <span
                        style="color: #dc3545; font-size: 0.875rem; display: block; margin-top: 4px;">{{ $message }}</span>
                @enderror
            </div>

            <!-- NEW: COMPLETE ADDRESS FIELD (Like Seedlings) -->
            <div class="rsbsa-form-group">
                <label>Complete Address <span style="color: #dc3545; font-weight: bold;">*</span></label>
                <input type="text" id="rsbsa-address" name="address"
                    placeholder="Example: 123 Main Street, Poblacion" value="{{ old('address') }}" required
                    pattern="[a-zA-Z0-9\s,'.\-]*">
                <small style="color: #666; font-size: 0.875rem; display: block; margin-top: 4px;">Include house number,
                    street, subdivision if applicable. This helps us locate your area for services.</small>
                @error('address')
                    <span
                        style="color: #dc3545; font-size: 0.875rem; display: block; margin-top: 4px;">{{ $message }}</span>
                @enderror
            </div>

            <div class="rsbsa-form-group">
                <label>Contact Number <span style="color: #dc3545; font-weight: bold;">*</span></label>
                <input type="tel" id="contact_number" name="contact_number" placeholder="Example: 09123456789"
                    title="Contact number must be in the format 09XXXXXXXXX (e.g., 09123456789)"
                    value="{{ old('contact_number') }}" required>
                <span class="validation-warning" id="rsbsa-mobile-warning"
                    style="color: #ff6b6b; font-size: 0.875rem; display: none; margin-top: 4px;">Mobile number must be:
                    09XXXXXXXXX (11 digits total)</span>
                @error('contact_number')
                    <span
                        style="color: #dc3545; font-size: 0.875rem; display: block; margin-top: 4px;">{{ $message }}</span>
                @enderror
            </div>

            <!-- MAIN LIVELIHOOD SELECT -->
            <div class="rsbsa-form-group">
                <label>Main Livelihood <span style="color: #dc3545; font-weight: bold;">*</span></label>
                <select name="main_livelihood" id="rsbsa-main_livelihood" required
                    onchange="toggleRSBSALivelihoodFields(this)">
                    <option value="" disabled {{ old('main_livelihood') ? '' : 'selected' }}>Select Livelihood
                    </option>
                    <option value="Farmer" {{ old('main_livelihood') == 'Farmer' ? 'selected' : '' }}>Farmer</option>
                    <option value="Farmworker/Laborer"
                        {{ old('main_livelihood') == 'Farmworker/Laborer' ? 'selected' : '' }}>Farmworker/Laborer
                    </option>
                    <option value="Fisherfolk" {{ old('main_livelihood') == 'Fisherfolk' ? 'selected' : '' }}>
                        Fisherfolk</option>
                    <option value="Agri-youth" {{ old('main_livelihood') == 'Agri-youth' ? 'selected' : '' }}>
                        Agri-youth</option>
                </select>
                <small class="rsbsa-form-help">Subject to validation under RSBSA guidelines.</small>
                @error('main_livelihood')
                    <span
                        style="color: #dc3545; font-size: 0.875rem; display: block; margin-top: 4px;">{{ $message }}</span>
                @enderror
            </div>

            <!-- FARMER FIELDS -->
            <div id="rsbsa-farmer-fields"
                style="display: {{ old('main_livelihood') == 'Farmer' ? 'block' : 'none' }};">
                <div class="rsbsa-form-group">
                    <label>Crops/Commodity <span style="color: #dc3545; font-weight: bold;">*</span></label>
                    <select name="farmer_crops" id="rsbsa-farmer_crops" onchange="toggleRSBSAFarmerOtherCrops()">
                        <option value="" disabled {{ old('farmer_crops') ? '' : 'selected' }}>Select
                            Crops/Commodity</option>
                        <option value="Rice" {{ old('farmer_crops') == 'Rice' ? 'selected' : '' }}>Rice</option>
                        <option value="Corn" {{ old('farmer_crops') == 'Corn' ? 'selected' : '' }}>Corn</option>
                        <option value="HVC" {{ old('farmer_crops') == 'HVC' ? 'selected' : '' }}>HVC (High Value
                            Crops)</option>
                        <option value="Livestock" {{ old('farmer_crops') == 'Livestock' ? 'selected' : '' }}>Livestock
                        </option>
                        <option value="Poultry" {{ old('farmer_crops') == 'Poultry' ? 'selected' : '' }}>Poultry
                        </option>
                        <option value="Agri-fishery" {{ old('farmer_crops') == 'Agri-fishery' ? 'selected' : '' }}>
                            Agri-fishery</option>
                        <option value="Other Crops" {{ old('farmer_crops') == 'Other Crops' ? 'selected' : '' }}>Other
                            Crops (specify)</option>
                    </select>
                    @error('farmer_crops')
                        <span
                            style="color: #dc3545; font-size: 0.875rem; display: block; margin-top: 4px;">{{ $message }}</span>
                    @enderror
                </div>

                <div id="rsbsa-farmer-other-crops-field"
                    style="display: {{ old('farmer_crops') == 'Other Crops' ? 'block' : 'none' }};">
                    <div class="rsbsa-form-group">
                        <label>Specify Other Crops/Commodity <span
                                style="color: #dc3545; font-weight: bold;">*</span></label>
                        <input type="text" id="rsbsa-farmer_other_crops" name="farmer_other_crops"
                            placeholder="Example: Vegetables, Fruits" pattern="[a-zA-Z\s,'.\-]*"
                            title="Only letters, spaces, commas, hyphens, and apostrophes allowed"
                            value="{{ old('farmer_other_crops') }}">
                        <span class="validation-warning" id="rsbsa-farmer_other_crops-warning"
                            style="color: #ff6b6b; font-size: 0.875rem; display: none; margin-top: 4px;">Only letters,
                            spaces, commas, hyphens, and apostrophes are allowed</span>
                        @error('farmer_other_crops')
                            <span
                                style="color: #dc3545; font-size: 0.875rem; display: block; margin-top: 4px;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="rsbsa-form-group">
                    <label>Livestock/Poultry (Type & Number)</label>
                    <input type="text" id="rsbsa-farmer_livestock" name="farmer_livestock"
                        placeholder="Example: Chickens (50), Pigs (5)"
                        title="Only alphanumeric, spaces, parentheses, commas, hyphens, and apostrophes allowed"
                        value="{{ old('farmer_livestock') }}">
                    <small class="rsbsa-form-help">Enter type and number of livestock/poultry heads (leave blank if not
                        applicable)</small>
                    @error('farmer_livestock')
                        <span
                            style="color: #dc3545; font-size: 0.875rem; display: block; margin-top: 4px;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="rsbsa-form-group">
                    <label>Land Area in hectares (Optional)</label>
                    <input type="number" id="rsbsa-farmer_land_area" name="farmer_land_area" step="0.01"
                        min="0" max="1000" placeholder="Example: 2.5"
                        value="{{ old('farmer_land_area') }}">
                    <small class="rsbsa-form-help">Land area dedicated to farming</small>
                    @error('farmer_land_area')
                        <span
                            style="color: #dc3545; font-size: 0.875rem; display: block; margin-top: 4px;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="rsbsa-form-group">
                    <label>Type of Farm <span style="color: #dc3545; font-weight: bold;">*</span></label>
                    <select name="farmer_type_of_farm" id="rsbsa-farmer_type_of_farm">
                        <option value="" disabled {{ old('farmer_type_of_farm') ? '' : 'selected' }}>Select Farm
                            Type</option>
                        <option value="Irrigated" {{ old('farmer_type_of_farm') == 'Irrigated' ? 'selected' : '' }}>
                            Irrigated</option>
                        <option value="Rainfed Upland"
                            {{ old('farmer_type_of_farm') == 'Rainfed Upland' ? 'selected' : '' }}>Rainfed Upland
                        </option>
                        <option value="Rainfed Lowland"
                            {{ old('farmer_type_of_farm') == 'Rainfed Lowland' ? 'selected' : '' }}>Rainfed Lowland
                        </option>
                    </select>
                    @error('farmer_type_of_farm')
                        <span
                            style="color: #dc3545; font-size: 0.875rem; display: block; margin-top: 4px;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="rsbsa-form-group">
                    <label>Land Ownership <span style="color: #dc3545; font-weight: bold;">*</span></label>
                    <select name="farmer_land_ownership" id="rsbsa-farmer_land_ownership">
                        <option value="" disabled {{ old('farmer_land_ownership') ? '' : 'selected' }}>Select
                            Ownership</option>
                        <option value="Owner" {{ old('farmer_land_ownership') == 'Owner' ? 'selected' : '' }}>Owner
                        </option>
                        <option value="Tenant" {{ old('farmer_land_ownership') == 'Tenant' ? 'selected' : '' }}>Tenant
                        </option>
                        <option value="Lessee" {{ old('farmer_land_ownership') == 'Lessee' ? 'selected' : '' }}>Lessee
                        </option>
                    </select>
                    @error('farmer_land_ownership')
                        <span
                            style="color: #dc3545; font-size: 0.875rem; display: block; margin-top: 4px;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="rsbsa-form-group">
                    <label>Ancestral Domain/Agrarian Reform Beneficiary</label>
                    <select name="farmer_special_status" id="rsbsa-farmer_special_status">
                        <option value="" selected>Select (Optional)</option>
                        <option value="Ancestral Domain"
                            {{ old('farmer_special_status') == 'Ancestral Domain' ? 'selected' : '' }}>Ancestral Domain
                        </option>
                        <option value="Agrarian Reform Beneficiary"
                            {{ old('farmer_special_status') == 'Agrarian Reform Beneficiary' ? 'selected' : '' }}>
                            Agrarian Reform Beneficiary</option>
                        <option value="None" {{ old('farmer_special_status') == 'None' ? 'selected' : '' }}>None
                        </option>
                    </select>
                    @error('farmer_special_status')
                        <span
                            style="color: #dc3545; font-size: 0.875rem; display: block; margin-top: 4px;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- FARM LOCATION - ONLY FOR FARMERS (REQUIRED FIELD) -->
                <div class="rsbsa-form-group">
                    <label>Farm Location <span style="color: #dc3545; font-weight: bold;">*</span></label>
                    <input type="text" id="rsbsa-farm_location" name="farm_location"
                        placeholder="Example: Barangay Landayan, San Pedro" pattern="[a-zA-Z0-9\s,'.\-]*"
                        title="Only alphanumeric, spaces, commas, hyphens, and apostrophes allowed"
                        value="{{ old('farm_location') }}">
                    <span class="validation-warning" id="rsbsa-farm_location-warning"
                        style="color: #ff6b6b; font-size: 0.875rem; display: none; margin-top: 4px;">Only alphanumeric,
                        spaces, commas, hyphens, and apostrophes are allowed</span>
                    @error('farm_location')
                        <span
                            style="color: #dc3545; font-size: 0.875rem; display: block; margin-top: 4px;">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- FARMWORKER/LABORER FIELDS -->
            <div id="rsbsa-farmworker-fields"
                style="display: {{ old('main_livelihood') == 'Farmworker/Laborer' ? 'block' : 'none' }};">
                <div class="rsbsa-form-group">
                    <label>Type of Farm Work <span style="color: #dc3545; font-weight: bold;">*</span></label>
                    <select name="farmworker_type" id="rsbsa-farmworker_type"
                        onchange="toggleRSBSAFarmworkerOtherType()">
                        <option value="" disabled {{ old('farmworker_type') ? '' : 'selected' }}>Select Farm
                            Work Type</option>
                        <option value="Land preparation"
                            {{ old('farmworker_type') == 'Land preparation' ? 'selected' : '' }}>Land preparation
                        </option>
                        <option value="Planting/Transplanting"
                            {{ old('farmworker_type') == 'Planting/Transplanting' ? 'selected' : '' }}>Planting /
                            Transplanting</option>
                        <option value="Cultivation" {{ old('farmworker_type') == 'Cultivation' ? 'selected' : '' }}>
                            Cultivation</option>
                        <option value="Harvesting" {{ old('farmworker_type') == 'Harvesting' ? 'selected' : '' }}>
                            Harvesting</option>
                        <option value="Others" {{ old('farmworker_type') == 'Others' ? 'selected' : '' }}>Others
                            (specify)</option>
                    </select>
                    @error('farmworker_type')
                        <span
                            style="color: #dc3545; font-size: 0.875rem; display: block; margin-top: 4px;">{{ $message }}</span>
                    @enderror
                </div>

                <div id="rsbsa-farmworker-other-type-field"
                    style="display: {{ old('farmworker_type') == 'Others' ? 'block' : 'none' }};">
                    <div class="rsbsa-form-group">
                        <label>Specify Other Farm Work <span
                                style="color: #dc3545; font-weight: bold;">*</span></label>
                        <input type="text" id="rsbsa-farmworker_other_type" name="farmworker_other_type"
                            placeholder="Specify other farm work" pattern="[a-zA-Z\s,'.\-]*"
                            title="Only letters, spaces, commas, hyphens, and apostrophes allowed"
                            value="{{ old('farmworker_other_type') }}">
                        <span class="validation-warning" id="rsbsa-farmworker_other_type-warning"
                            style="color: #ff6b6b; font-size: 0.875rem; display: none; margin-top: 4px;">Only letters,
                            spaces, commas, hyphens, and apostrophes are allowed</span>
                        @error('farmworker_other_type')
                            <span
                                style="color: #dc3545; font-size: 0.875rem; display: block; margin-top: 4px;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- FISHERFOLK FIELDS -->
            <div id="rsbsa-fisherfolk-fields"
                style="display: {{ old('main_livelihood') == 'Fisherfolk' ? 'block' : 'none' }};">
                <div class="rsbsa-form-group">
                    <label>Fishing Activity <span style="color: #dc3545; font-weight: bold;">*</span></label>
                    <select name="fisherfolk_activity" id="rsbsa-fisherfolk_activity"
                        onchange="toggleRSBSAFisherfolfOtherActivity()">
                        <option value="" disabled {{ old('fisherfolk_activity') ? '' : 'selected' }}>Select
                            Fishing Activity</option>
                        <option value="Fish capture"
                            {{ old('fisherfolk_activity') == 'Fish capture' ? 'selected' : '' }}>Fish capture</option>
                        <option value="Aquaculture"
                            {{ old('fisherfolk_activity') == 'Aquaculture' ? 'selected' : '' }}>Aquaculture</option>
                        <option value="Gleaning" {{ old('fisherfolk_activity') == 'Gleaning' ? 'selected' : '' }}>
                            Gleaning</option>
                        <option value="Processing" {{ old('fisherfolk_activity') == 'Processing' ? 'selected' : '' }}>
                            Processing</option>
                        <option value="Vending" {{ old('fisherfolk_activity') == 'Vending' ? 'selected' : '' }}>
                            Vending</option>
                        <option value="Others" {{ old('fisherfolk_activity') == 'Others' ? 'selected' : '' }}>Others
                            (specify)</option>
                    </select>
                    @error('fisherfolk_activity')
                        <span
                            style="color: #dc3545; font-size: 0.875rem; display: block; margin-top: 4px;">{{ $message }}</span>
                    @enderror
                </div>

                <div id="rsbsa-fisherfolk-other-activity-field"
                    style="display: {{ old('fisherfolk_activity') == 'Others' ? 'block' : 'none' }};">
                    <div class="rsbsa-form-group">
                        <label>Specify Other Fishing Activity <span
                                style="color: #dc3545; font-weight: bold;">*</span></label>
                        <input type="text" id="rsbsa-fisherfolk_other_activity" name="fisherfolk_other_activity"
                            placeholder="Specify other fishing activity" pattern="[a-zA-Z\s,'.\-]*"
                            title="Only letters, spaces, commas, hyphens, and apostrophes allowed"
                            value="{{ old('fisherfolk_other_activity') }}">
                        <span class="validation-warning" id="rsbsa-fisherfolk_other_activity-warning"
                            style="color: #ff6b6b; font-size: 0.875rem; display: none; margin-top: 4px;">Only letters,
                            spaces, commas, hyphens, and apostrophes are allowed</span>
                        @error('fisherfolk_other_activity')
                            <span
                                style="color: #dc3545; font-size: 0.875rem; display: block; margin-top: 4px;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- AGRI-YOUTH FIELDS -->
            <div id="rsbsa-agriyouth-fields"
                style="display: {{ old('main_livelihood') == 'Agri-youth' ? 'block' : 'none' }};">
                <div class="rsbsa-form-group">
                    <label>Part of Farming Household <span style="color: #dc3545; font-weight: bold;">*</span></label>
                    <select name="agriyouth_farming_household" id="rsbsa-agriyouth_farming_household">
                        <option value="" disabled {{ old('agriyouth_farming_household') ? '' : 'selected' }}>
                            Select</option>
                        <option value="Yes" {{ old('agriyouth_farming_household') == 'Yes' ? 'selected' : '' }}>Yes
                        </option>
                        <option value="No" {{ old('agriyouth_farming_household') == 'No' ? 'selected' : '' }}>No
                        </option>
                    </select>
                    @error('agriyouth_farming_household')
                        <span
                            style="color: #dc3545; font-size: 0.875rem; display: block; margin-top: 4px;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="rsbsa-form-group">
                    <label>Training/Education <span style="color: #dc3545; font-weight: bold;">*</span></label>
                    <select name="agriyouth_training" id="rsbsa-agriyouth_training">
                        <option value="" disabled {{ old('agriyouth_training') ? '' : 'selected' }}>Select
                        </option>
                        <option value="Formal agri-fishery course"
                            {{ old('agriyouth_training') == 'Formal agri-fishery course' ? 'selected' : '' }}>Formal
                            agri-fishery course</option>
                        <option value="Non-formal agri-fishery course"
                            {{ old('agriyouth_training') == 'Non-formal agri-fishery course' ? 'selected' : '' }}>
                            Non-formal agri-fishery course</option>
                        <option value="None" {{ old('agriyouth_training') == 'None' ? 'selected' : '' }}>None
                        </option>
                    </select>
                    @error('agriyouth_training')
                        <span
                            style="color: #dc3545; font-size: 0.875rem; display: block; margin-top: 4px;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="rsbsa-form-group">
                    <label>Agricultural Activity/Program Participation <span
                            style="color: #dc3545; font-weight: bold;">*</span></label>
                    <select name="agriyouth_participation" id="rsbsa-agriyouth_participation">
                        <option value="" disabled {{ old('agriyouth_participation') ? '' : 'selected' }}>Select
                        </option>
                        <option value="Participated"
                            {{ old('agriyouth_participation') == 'Participated' ? 'selected' : '' }}>Participated in
                            agricultural activity/program</option>
                        <option value="Not Participated"
                            {{ old('agriyouth_participation') == 'Not Participated' ? 'selected' : '' }}>Not
                            participated yet</option>
                    </select>
                    @error('agriyouth_participation')
                        <span
                            style="color: #dc3545; font-size: 0.875rem; display: block; margin-top: 4px;">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- SUPPORTING DOCUMENTS (Not location-specific, all livelihoods) -->
            <div class="rsbsa-form-group">
                <label>Supporting Document (Optional)</label>
                <input type="file" id="rsbsa-supporting_docs" name="supporting_docs"
                    accept=".pdf,.jpg,.jpeg,.png">
                <small class="rsbsa-form-help">
                    Upload proof of livelihood status (e.g., farm photo, barangay certificate, ID). Accepted formats:
                    JPG, PNG, PDF (Max 10MB).
                </small>
                @error('supporting_docs')
                    <span
                        style="color: #dc3545; font-size: 0.875rem; display: block; margin-top: 4px;">{{ $message }}</span>
                @enderror
            </div>

            <div class="rsbsa-form-buttons">
                <button type="button" class="rsbsa-cancel-btn" onclick="closeFormRSBSA()">Cancel</button>
                <button type="submit" class="rsbsa-submit-btn" id="rsbsa-submit-btn">Submit Application</button>
            </div>
        </form>
    </div>

    <!-- REQUIREMENTS TAB -->
    <div class="rsbsa-tab-content" id="requirements" style="display: none;">
        <h4>Required Documents</h4>
        <ol>
            <li>
                <strong>Accomplished RSBSA Enrollment Form (Original Copy)</strong><br>
                Available at the City Agriculture Office (CAgO) or downloadable online:
                <a href="https://www.da.gov.ph/wp-content/uploads/2021/05/RSBSA_Enrollment-Form_032021.pdf"
                    target="_blank">Download Form</a>
            </li>
            <li>
                <strong>2×2 ID Picture (Recent, within the last 6 months)</strong><br>
                Can be taken at any photo studio.
            </li>
        </ol>
        <h4>Who May Register</h4>
        <ul>
            <li>Agricultural Workers</li>
            <li>Farmers</li>
            <li>Fisherfolk</li>
            <li>Livestock Raisers</li>
            <li>Poultry Raisers</li>
        </ul>
    </div>

    <!-- INFORMATION TAB -->
    <div class="rsbsa-tab-content" id="information" style="display: none;">
        <h4>RSBSA Registration</h4>
        <p>
            RSBSA (Registry System for Basic Sectors in Agriculture) is a national registry for farmers, fisherfolk,
            livestock raisers, and poultry raisers, implemented in compliance with Republic Act No. 11203
            and relevant Department of Agriculture guidelines to document and support the agricultural sector in the
            City of San Pedro.
        </p>

        <h4>Important Information</h4>
        <ul>
            <li>All applications shall undergo review and approval by the City Agriculture Office. Processing time may
                vary depending on the completeness of submitted documents.</li>
            <li>The office may contact the applicant for additional information or verification.</li>
            <li>All information provided must be accurate and truthful. Incomplete or incorrect submissions may result
                in delays or rejection.</li>
        </ul>
    </div>
</section>
