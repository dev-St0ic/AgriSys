<!-- RSBSA Registration Form (Updated with proper spacing) -->
<section class="rsbsa-application-section" id="new-rsbsa" style="display: none;">
    <div class="rsbsa-form-header">
        <h2>RSBSA Registration</h2>
        <p>Enrollment of farmers, fisherfolk, livestock and poultry raisers under the Registry System for Basic Sectors in Agriculture (RSBSA).</p>
    </div>

    <div class="rsbsa-form-tabs">
        <button class="rsbsa-tab-btn active" onclick="showRSBSATab('form', event)">Application Form</button>
        <button class="rsbsa-tab-btn" onclick="showRSBSATab('requirements', event)">Requirements</button>
        <button class="rsbsa-tab-btn" onclick="showRSBSATab('information', event)">Information</button>
    </div>

    <div class="rsbsa-tab-content" id="form" style="display: block;">
        <!-- Success/Error Messages -->
        @if (session('success'))
            <div class="alert alert-success" style="padding: 15px; margin-bottom: 20px; background-color: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; color: #155724;">
                <strong>✓ Success!</strong> {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger" style="padding: 15px; margin-bottom: 20px; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; color: #721c24;">
                <strong>✗ Error!</strong> {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger" style="padding: 15px; margin-bottom: 20px; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; color: #721c24;">
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

            <div class="rsbsa-form-group">
                <label>First Name <span style="color: #dc3545; font-weight: bold;">*</span></label>
                <input type="text" id="rsbsa-first_name" name="first_name" placeholder="Example: Juan"
                    pattern="[a-zA-Z\s'\-]+" title="First name can only contain letters, spaces, hyphens, and apostrophes"
                    value="{{ old('first_name') }}" required>
                <span class="validation-warning" id="rsbsa-first_name-warning"
                    style="color: #ff6b6b; font-size: 0.875rem; display: none; margin-top: 4px;">Only letters, spaces, hyphens, and apostrophes are allowed</span>
                @error('first_name')
                    <span style="color: #dc3545; font-size: 0.875rem; display: block; margin-top: 4px;">{{ $message }}</span>
                @enderror
            </div>

            <div class="rsbsa-form-group">
                <label>Middle Name (Optional)</label>
                <input type="text" id="rsbsa-middle_name" name="middle_name" placeholder="Example: Santos"
                    pattern="[a-zA-Z\s'\-]+" title="Middle name can only contain letters, spaces, hyphens, and apostrophes"
                    value="{{ old('middle_name') }}">
                <span class="validation-warning" id="rsbsa-middle_name-warning"
                    style="color: #ff6b6b; font-size: 0.875rem; display: none; margin-top: 4px;">Only letters, spaces, hyphens, and apostrophes are allowed</span>
                @error('middle_name')
                    <span style="color: #dc3545; font-size: 0.875rem; display: block; margin-top: 4px;">{{ $message }}</span>
                @enderror
            </div>

            <div class="rsbsa-form-group">
                <label>Last Name <span style="color: #dc3545; font-weight: bold;">*</span></label>
                <input type="text" id="rsbsa-last_name" name="last_name" placeholder="Example: Dela Cruz"
                    pattern="[a-zA-Z\s'\-]+" title="Last name can only contain letters, spaces, hyphens, and apostrophes"
                    value="{{ old('last_name') }}" required>
                <span class="validation-warning" id="rsbsa-last_name-warning"
                    style="color: #ff6b6b; font-size: 0.875rem; display: none; margin-top: 4px;">Only letters, spaces, hyphens, and apostrophes are allowed</span>
                @error('last_name')
                    <span style="color: #dc3545; font-size: 0.875rem; display: block; margin-top: 4px;">{{ $message }}</span>
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
                    <span style="color: #dc3545; font-size: 0.875rem; display: block; margin-top: 4px;">{{ $message }}</span>
                @enderror
            </div>

            <div class="rsbsa-form-group">
                <label>Sex <span style="color: #dc3545; font-weight: bold;">*</span></label>
                <select name="sex" required>
                    <option value="" disabled {{ old('sex') ? '' : 'selected' }}>Select Sex</option>
                    <option value="Male" {{ old('sex') == 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ old('sex') == 'Female' ? 'selected' : '' }}>Female</option>
                    <option value="Preferred not to say" {{ old('sex') == 'Preferred not to say' ? 'selected' : '' }}>Preferred not to say</option>
                </select>
                @error('sex')
                    <span style="color: #dc3545; font-size: 0.875rem; display: block; margin-top: 4px;">{{ $message }}</span>
                @enderror
            </div>

            <div class="rsbsa-form-group">
                <label>Barangay <span style="color: #dc3545; font-weight: bold;">*</span></label>
                <select name="barangay" required>
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
                    <span style="color: #dc3545; font-size: 0.875rem; display: block; margin-top: 4px;">{{ $message }}</span>
                @enderror
            </div>

            <div class="rsbsa-form-group">
                <label>Contact Number <span style="color: #dc3545; font-weight: bold;">*</span></label>
                <input type="tel" name="mobile" placeholder="Example: 09123456789" pattern="^09\d{9}$"
                    title="Mobile number must be in the format 09XXXXXXXXX (e.g., 09123456789)"
                    value="{{ old('mobile') }}" required>
                @error('mobile')
                    <span style="color: #dc3545; font-size: 0.875rem; display: block; margin-top: 4px;">{{ $message }}</span>
                @enderror
            </div>

            <div class="rsbsa-form-group">
                <label>Main Livelihood <span style="color: #dc3545; font-weight: bold;">*</span></label>
                <select name="main_livelihood" required>
                    <option value="" disabled {{ old('main_livelihood') ? '' : 'selected' }}>Select Livelihood</option>
                    <option value="Farmer" {{ old('main_livelihood') == 'Farmer' ? 'selected' : '' }}>Farmer</option>
                    <option value="Farmworker/Laborer" {{ old('main_livelihood') == 'Farmworker/Laborer' ? 'selected' : '' }}>Farmworker/Laborer</option>
                    <option value="Fisherfolk" {{ old('main_livelihood') == 'Fisherfolk' ? 'selected' : '' }}>Fisherfolk</option>
                    <option value="Agri-youth" {{ old('main_livelihood') == 'Agri-youth' ? 'selected' : '' }}>Agri-youth</option>
                </select>
                <small class="rsbsa-form-help">Subject to validation under RSBSA guidelines.</small>
                @error('main_livelihood')
                    <span style="color: #dc3545; font-size: 0.875rem; display: block; margin-top: 4px;">{{ $message }}</span>
                @enderror
            </div>

            <div class="rsbsa-form-group">
                <label>Land Area in hectares (Optoional)</label>
                <input type="number" name="land_area" step="0.01" min="0" max="1000"
                    placeholder="Example: 2.5" value="{{ old('land_area') }}">
                @error('land_area')
                    <span style="color: #dc3545; font-size: 0.875rem; display: block; margin-top: 4px;">{{ $message }}</span>
                @enderror
            </div>

            <div class="rsbsa-form-group">
                <label>Farm Location <span style="color: #dc3545; font-weight: bold;">*</span></label>
                <input type="text" name="farm_location" placeholder="Example: Barangay Landayan, San Pedro"
                    value="{{ old('farm_location') }}" required>
                @error('farm_location')
                    <span style="color: #dc3545; font-size: 0.875rem; display: block; margin-top: 4px;">{{ $message }}</span>
                @enderror
            </div>

            <div class="rsbsa-form-group">
                <label>Commodity (Crops/Livestock) <span style="color: #dc3545; font-weight: bold;">*</span></label>
                <input type="text" name="commodity" placeholder="Example: Rice, Corn, Tilapia"
                    value="{{ old('commodity') }}" required>
                @error('commodity')
                    <span style="color: #dc3545; font-size: 0.875rem; display: block; margin-top: 4px;">{{ $message }}</span>
                @enderror
            </div>

            <div class="rsbsa-form-group">
                <label>Supporting Document (Optional)</label>
                <input type="file" name="supporting_docs" accept=".pdf,.jpg,.jpeg,.png">
                <small class="rsbsa-form-help">
                    Upload proof of livelihood status (e.g., farm photo, barangay certificate, ID). Accepted formats: JPG, PNG, PDF (Max 10MB).
                </small>
                @error('supporting_docs')
                    <span style="color: #dc3545; font-size: 0.875rem; display: block; margin-top: 4px;">{{ $message }}</span>
                @enderror
            </div>

            <div class="rsbsa-form-buttons">
                <button type="button" class="rsbsa-cancel-btn" onclick="closeFormRSBSA()">Cancel</button>
                <button type="submit" class="rsbsa-submit-btn" id="rsbsa-submit-btn">Submit Application</button>
            </div>
        </form>
    </div>

    <div class="rsbsa-tab-content" id="requirements" style="display: none;">
        <h4>Required Documents</h4>
        <ol>
            <li>
                <strong>Accomplished RSBSA Enrollment Form (Original Copy)</strong><br>
                Available at the City Agriculture Office (CAgO) or downloadable online: 
                <a href="https://www.da.gov.ph/wp-content/uploads/2021/05/RSBSA_Enrollment-Form_032021.pdf" target="_blank">Download Form</a>
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

    <div class="rsbsa-tab-content" id="information" style="display: none;">
        <h4>RSBSA Registration</h4>
        <p>
        RSBSA (Registry System for Basic Sectors in Agriculture) is a national registry for farmers, fisherfolk, 
        livestock raisers, and poultry raisers, implemented in compliance with Republic Act No. 11203 
        and relevant Department of Agriculture guidelines to document and support the agricultural sector in the City of San Pedro.
        </p>

        <h4>Important Information</h4>
        <ul>
            <li>All applications shall undergo review and approval by the City Agriculture Office. Processing time may vary depending on the completeness of submitted documents.</li>
            <li>The office may contact the applicant for additional information or verification.</li>
            <li>All information provided must be accurate and truthful. Incomplete or incorrect submissions may result in delays or rejection.</li>
        </ul>`
        
    </div>
</section>

<script>
    // Real-time validation for name fields
    document.addEventListener('DOMContentLoaded', function() {
        const nameFields = [{
                id: 'rsbsa-first_name',
                pattern: /^[a-zA-Z\s\'-]*$/
            },
            {
                id: 'rsbsa-middle_name',
                pattern: /^[a-zA-Z\s\'-]*$/
            },
            {
                id: 'rsbsa-last_name',
                pattern: /^[a-zA-Z\s\'-]*$/
            },
            {
                id: 'rsbsa-name_extension',
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

        // File preview functionality removed - no longer needed
    });
</script>