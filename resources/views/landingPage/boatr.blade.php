<!-- BoatR Registration Form - Clean HTML (No Styles) -->
<section class="boatr-application-section" id="boatr-form">
    <div class="boatr-form-header">
        <h2>BoatR Registration</h2>
        <p>Boat Registration System - Register your fishing vessel with the City Agriculture Office.</p>
    </div>

    <div class="boatr-form-tabs">
        <button class="boatr-tab-btn active" onclick="showTab('boatr-form-tab', event)">Application Form</button>
        <button class="boatr-tab-btn" onclick="showTab('boatr-requirements-tab', event)">Requirements</button>
        <button class="boatr-tab-btn" onclick="showTab('boatr-info-tab', event)">Information</button>
    </div>

    <div class="boatr-tab-content" id="boatr-form-tab" style="display: block;">
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

        <form id="boatr-registration-form" onsubmit="submitBoatRForm(event)" enctype="multipart/form-data">
            @csrf

            <div class="boatr-form-group">
                <label for="boatr_first_name">First Name <span class="required">*</span></label>
                <input type="text" id="boatr_first_name" name="first_name" placeholder="Example: Juan"
                    pattern="[a-zA-Z\s'\-]+"
                    title="First name can only contain letters, spaces, hyphens, and apostrophes" required>
                <span class="validation-warning" id="boatr_first_name-warning"
                    style="color: #ff6b6b; font-size: 0.875rem; display: none; margin-top: 4px;">Only letters,
                    spaces, hyphens, and apostrophes are allowed</span>
            </div>

            <div class="boatr-form-group">
                <label for="boatr_middle_name">Middle Name (Optional)</label>
                <input type="text" id="boatr_middle_name" name="middle_name" placeholder="Example: Santos"
                    pattern="[a-zA-Z\s'\-]+"
                    title="Middle name can only contain letters, spaces, hyphens, and apostrophes">
                <span class="validation-warning" id="boatr_middle_name-warning"
                    style="color: #ff6b6b; font-size: 0.875rem; display: none; margin-top: 4px;">Only letters,
                    spaces, hyphens, and apostrophes are allowed</span>
            </div>

            <div class="boatr-form-group">
                <label for="boatr_last_name">Last Name <span class="required">*</span></label>
                <input type="text" id="boatr_last_name" name="last_name" placeholder="Example: Dela Cruz"
                    pattern="[a-zA-Z\s'\-]+"
                    title="Last name can only contain letters, spaces, hyphens, and apostrophes" required>
                <span class="validation-warning" id="boatr_last_name-warning"
                    style="color: #ff6b6b; font-size: 0.875rem; display: none; margin-top: 4px;">Only letters,
                    spaces, hyphens, and apostrophes are allowed</span>
            </div>

            <div class="boatr-form-group">
                <label for="boatr_name_extension">Name Extension (Optional)</label>
                <select id="boatr_name_extension" name="name_extension">
                    <option value="" selected>Select Extension</option>
                    <option value="Jr.">Jr.</option>
                    <option value="Sr.">Sr.</option>
                    <option value="II">II</option>
                    <option value="III">III</option>
                    <option value="IV">IV</option>
                    <option value="V">V</option>
                </select>
            </div>

            <div class="boatr-form-group">
                <label for="boatr_contact_number">Contact Number <span class="required">*</span></label>
                <input type="tel" id="boatr_contact_number" name="contact_number" placeholder="Example: 09123456789"
                    pattern="^09\d{9}$" title="Contact number must be in the format 09XXXXXXXXX (e.g., 09123456789)"
                    required>
            </div>

            <div class="boatr-form-group">
                <label for="boatr_barangay">Barangay <span class="required">*</span></label>
                <select id="boatr_barangay" name="barangay" required>
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
            </div>

            <div class="boatr-form-group">
                <label for="boatr_fishr_number">FishR Number <span class="required">*</span></label>
                <input type="text" id="boatr_fishr_number" name="fishr_number"
                    placeholder="Example: FISHR-ABC12345" required>
                <small class="boatr-form-help">Your approved FishR registration number</small>
            </div>

            <div class="boatr-form-group">
                <label for="boatr_vessel_name">Vessel Name <span class="required">*</span></label>
                <input type="text" id="boatr_vessel_name" name="vessel_name" placeholder="Example: Blessed Catch"
                    required>
                <small class="boatr-form-help">Official name of your fishing boat</small>
            </div>

            <div class="boatr-form-group">
                <label for="boatr_boat_type">Boat Type <span class="required">*</span></label>
                <select id="boatr_boat_type" name="boat_type" required onchange="handleBoatTypeChange(this)">
                    <option value="" disabled selected>Select Boat Type</option>
                    <option value="Spoon">Spoon</option>
                    <option value="Plumb">Plumb</option>
                    <option value="Banca">Banca</option>
                    <option value="Rake Stem - Rake Stern">Rake Stem - Rake Stern</option>
                    <option value="Rake Stem - Transom/Spoon/Plumb Stern">Rake Stem - Transom/Spoon/Plumb Stern
                    </option>
                    <option value="Skiff (Typical Design)">Skiff (Typical Design)</option>
                </select>
            </div>

            <div class="boatr-form-row">
                <div class="boatr-form-group">
                    <label for="boatr_boat_length">Length (feet) <span class="required">*</span></label>
                    <input type="number" id="boatr_boat_length" name="boat_length" step="0.01" min="1"
                        max="200" placeholder="Example: 15.5" required>
                </div>
                <div class="boatr-form-group">
                    <label for="boatr_boat_width">Width (feet) <span class="required">*</span></label>
                    <input type="number" id="boatr_boat_width" name="boat_width" step="0.01" min="1"
                        max="50" placeholder="Example: 4.2" required>
                </div>
                <div class="boatr-form-group">
                    <label for="boatr_boat_depth">Depth (feet) <span class="required">*</span></label>
                    <input type="number" id="boatr_boat_depth" name="boat_depth" step="0.01" min="1"
                        max="30" placeholder="Example: 2.8" required>
                </div>
            </div>

            <div class="boatr-form-group">
                <label for="boatr_engine_type">Engine Type <span class="required">*</span></label>
                <input type="text" id="boatr_engine_type" name="engine_type"
                    placeholder="Example: Yamaha 40HP Outboard" required>
                <small class="boatr-form-help">Brand and model of your boat engine</small>
            </div>

            <div class="boatr-form-group">
                <label for="boatr_engine_horsepower">Engine Horsepower <span class="required">*</span></label>
                <input type="number" id="boatr_engine_horsepower" name="engine_horsepower" step="1"
                    min="1" max="500" placeholder="Example: 40" required>
            </div>

            <div class="boatr-form-group">
                <label for="boatr_primary_fishing_gear">Primary Fishing Gear Used <span
                        class="required">*</span></label>
                <select id="boatr_primary_fishing_gear" name="primary_fishing_gear" required>
                    <option value="" disabled selected>Select Primary Gear</option>
                    <option value="Hook and Line">Hook and Line</option>
                    <option value="Bottom Set Gill Net">Bottom Set Gill Net</option>
                    <option value="Fish Trap">Fish Trap</option>
                    <option value="Fish Coral">Fish Coral</option>
                </select>
            </div>

            <div class="boatr-form-group">
                <label for="boatr_supporting_documents">Supporting Document (Optional)</label>
                <input type="file" id="boatr_supporting_documents" name="supporting_documents"
                    accept=".pdf,.jpg,.jpeg,.png" onchange="previewSingleFile(this)">
                <small class="boatr-form-help">
                    Upload Proof of Boat Ownership or Government ID (PDF, JPG, PNG - Max 10MB).
                    Additional documents will be collected during on-site inspection.
                </small>
            </div>


            <!-- <div class="boatr-alert boatr-alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>Document Submission Process:</strong>
                <p>You may upload one initial document now to expedite your application. Additional supporting documents will be collected and verified during the mandatory on-site inspection by the City Agriculture Office.
                Please prepare the following for inspection:</p>
                <ul>
                    <li>Valid government-issued ID (original)</li>
                    <li>Proof of boat ownership (original)</li>
                    <li>FishR registration certificate (original)</li>
                    <li>Engine specifications and receipt (original)</li>
                    <li>Physical boat for inspection</li>
                </ul>
            </div> -->

            <!-- <div id="required-docs-list"> -->
            <!-- Dynamic content will be inserted here by JavaScript -->
            <!-- </div> -->

            <div class="boatr-form-buttons">
                <button type="button" class="boatr-cancel-btn" onclick="closeFormBoatR()">Cancel</button>
                <button type="submit" class="boatr-submit-btn">Submit Application</button>
            </div>
        </form>
    </div>

    <div class="boatr-tab-content" id="boatr-requirements-tab">
        <h3>Required Documents</h3>
        <ul>
            <li>Valid government-issued ID of boat owner (original for inspection)</li>
            <li>Proof of boat ownership (original for inspection)</li>
            <li>Clear photos of the boat (can be uploaded or taken during inspection)</li>
            <li>Engine details/receipt (original for motorized boats)</li>
            <li>FishR registration certificate (original for inspection)</li>
            <li>On-site inspection approval (scheduled after application submission)</li>
        </ul>

        <h3>Boat Specifications</h3>
        <ul>
            <li>Must be used for municipal fishing only</li>
            <li>Length must not exceed 200 feet</li>
            <li>Width must not exceed 50 feet</li>
            <li>Depth must not exceed 30 feet</li>
            <li>Engine horsepower must not exceed 500 HP</li>
        </ul>
    </div>

    <div class="boatr-tab-content" id="boatr-info-tab">
        <h4>BoatR Registration</h4>
        <p>
            BoatR (Boat Registration) is a service of the City Agriculture Office that facilitates the
            registration of municipal fishing vessels in compliance with Republic Act No. 8550,
            Executive Order No. 305, and Section 32 of City Ordinance No. 2023-21.
        </p>

        <h4>Important Information</h4>
        <ul>
            <li>This service is for fishing vessel owners within the City of San Pedro.</li>
            <li>BoatR Registration is required for all municipal fishing vessels.</li>
            <li>An on-site inspection and measurement of the fishing vessel is mandatory.</li>
            <li>Submission of complete and accurate information is required for approval.</li>
            <li>Payment of applicable fees shall be made at the City Treasury Office after inspection.</li>
            <li>The BoatR Registration Certificate of Number (CN) shall be issued upon completion of all requirements.</li>
        </ul>

        <h4>Application Process</h4>
        <ol>
            <li>Client fills out the BoatR Application Form and submits the requirements.</li>
            <li>City Agriculture Office reviews and validates the submitted documents.</li>
            <li>Boat inspection and measurement are scheduled and conducted.</li>
            <li>Client pays the corresponding fees at the City Treasury Office.</li>
            <li>Client submits a photocopy of the Official Receipt (OR).</li>
            <li>BoatR Registration Certificate of Number (CN) is prepared and released.</li>
        </ol>
    </div>
</section>

<script>
    // Real-time validation for name fields
    document.addEventListener('DOMContentLoaded', function() {
        const nameFields = [{
                id: 'boatr_first_name',
                pattern: /^[a-zA-Z\s\'-]*$/
            },
            {
                id: 'boatr_middle_name',
                pattern: /^[a-zA-Z\s\'-]*$/
            },
            {
                id: 'boatr_last_name',
                pattern: /^[a-zA-Z\s\'-]*$/
            },
            {
                id: 'boatr_name_extension',
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
