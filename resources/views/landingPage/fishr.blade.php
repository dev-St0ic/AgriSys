<!-- FishR Registration Form -->
<section class="fishr-application-section" id="fishr-form">
    <div class="fishr-form-header">
        <h2>FishR Registration</h2>
        <p>National Program for Municipal Fisherfolk Registration - Register as a municipal fisherfolk.</p>
    </div>

    <div class="fishr-form-tabs">
        <button class="fishr-tab-btn active" onclick="showFishrTab('fishr-form-tab', event)">Application
            Form</button>
        <button class="fishr-tab-btn" onclick="showFishrTab('fishr-requirements-tab', event)">Requirements</button>
        <button class="fishr-tab-btn" onclick="showFishrTab('fishr-info-tab', event)">Information</button>
    </div>

    <div class="fishr-tab-content tab-content" id="fishr-form-tab">
        <!-- Success/Error Messages -->
        <div id="fishr-messages" style="display: none;">
            <div id="fishr-success-message" class="fishr-alert fishr-alert-success" style="display: none;"></div>
            <div id="fishr-error-message" class="fishr-alert fishr-alert-danger" style="display: none;"></div>
        </div>

        <form id="fishr-registration-form" method="POST" action="{{ route('apply.fishr') }}"
            enctype="multipart/form-data">
            @csrf

            <div class="fishr-form-group">
                <label for="fishr-first_name">First Name <span
                        style="color: #dc3545; font-weight: bold;">*</span></label>
                <input type="text" id="fishr-first_name" name="first_name" placeholder="Example: Juan"
                    pattern="[a-zA-Z\s'\-]+"
                    title="First name can only contain letters, spaces, hyphens, and apostrophes"
                    value="{{ old('first_name') }}" required>
                <span class="validation-warning" id="fishr-first_name-warning"
                    style="color: #ff6b6b; font-size: 0.875rem; display: none; margin-top: 4px;">Only letters,
                    spaces, hyphens, and apostrophes are allowed</span>
                @error('first_name')
                    <span class="fishr-error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="fishr-form-group">
                <label for="fishr-middle_name">Middle Name (Optional)</label>
                <input type="text" id="fishr-middle_name" name="middle_name" placeholder="Example: Santos"
                    pattern="[a-zA-Z\s'\-]+"
                    title="Middle name can only contain letters, spaces, hyphens, and apostrophes"
                    value="{{ old('middle_name') }}">
                <span class="validation-warning" id="fishr-middle_name-warning"
                    style="color: #ff6b6b; font-size: 0.875rem; display: none; margin-top: 4px;">Only letters,
                    spaces, hyphens, and apostrophes are allowed</span>
                @error('middle_name')
                    <span class="fishr-error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="fishr-form-group">
                <label for="fishr-last_name">Last Name <span style="color: #dc3545; font-weight: bold;">*</span></label>
                <input type="text" id="fishr-last_name" name="last_name" placeholder="Example: Dela Cruz"
                    pattern="[a-zA-Z\s'\-]+"
                    title="Last name can only contain letters, spaces, hyphens, and apostrophes"
                    value="{{ old('last_name') }}" required>
                <span class="validation-warning" id="fishr-last_name-warning"
                    style="color: #ff6b6b; font-size: 0.875rem; display: none; margin-top: 4px;">Only letters,
                    spaces, hyphens, and apostrophes are allowed</span>
                @error('last_name')
                    <span class="fishr-error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="fishr-form-group">
                <label for="fishr-name_extension">Name Extension (Optional)</label>
                <select id="fishr-name_extension" name="name_extension">
                    <option value="" selected>Select Extension</option>
                    <option value="Jr." {{ old('name_extension') == 'Jr.' ? 'selected' : '' }}>Jr.</option>
                    <option value="Sr." {{ old('name_extension') == 'Sr.' ? 'selected' : '' }}>Sr.</option>
                    <option value="II" {{ old('name_extension') == 'II' ? 'selected' : '' }}>II</option>
                    <option value="III" {{ old('name_extension') == 'III' ? 'selected' : '' }}>III</option>
                    <option value="IV" {{ old('name_extension') == 'IV' ? 'selected' : '' }}>IV</option>
                    <option value="V" {{ old('name_extension') == 'V' ? 'selected' : '' }}>V</option>
                </select>
                @error('name_extension')
                    <span class="fishr-error-text">{{ $message }}</span>
                @enderror
            </div>


            <div class="fishr-form-group">
                <label for="fishr-sex">Sex <span style="color: #dc3545; font-weight: bold;">*</span></label>
                <select id="fishr-sex" name="sex" required>
                    <option value="" disabled selected>Select Sex</option>
                    <option value="Male" {{ old('sex') == 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ old('sex') == 'Female' ? 'selected' : '' }}>Female</option>
                    <option value="Preferred not to say" {{ old('sex') == 'Preferred not to say' ? 'selected' : '' }}>
                        Preferred not to say</option>
                </select>
                @error('sex')
                    <span class="fishr-error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="fishr-form-group">
                <label for="fishr-barangay">Barangay <span style="color: #dc3545; font-weight: bold;">*</span></label>
                <select id="fishr-barangay" name="barangay" required>
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
                @error('barangay')
                    <span class="fishr-error-text">{{ $message }}</span>
                @enderror
            </div>


            <div class="fishr-form-group">
                <label for="fishr-contact_number">Contact Number <span
                        style="color: #dc3545; font-weight: bold;">*</span></label>
                <input type="tel" id="fishr-contact_number" name="contact_number"
                    placeholder="Example: 09123456789" value="{{ old('contact_number') }}" pattern="^09\d{9}$"
                    title="Contact number must be in the format 09XXXXXXXXX (e.g., 09123456789)" required>
                <span class="validation-warning" id="fishr-contact_number-warning"
                    style="color: #ff6b6b; font-size: 0.875rem; display: none; margin-top: 4px;">Contact number must be
                    in the format 09XXXXXXXXX (11 digits starting with 09)</span>
                @error('contact_number')
                    <span class="fishr-error-text">{{ $message }}</span>
                @enderror
            </div>


            <div class="fishr-form-group">
                <label for="fishr-main_livelihood">Main Livelihood <span
                        style="color: #dc3545; font-weight: bold;">*</span></label>
                <select id="fishr-main_livelihood" name="main_livelihood" required
                    onchange="toggleOtherLivelihood(this)">
                    <option value="" disabled selected>Select Livelihood</option>
                    <option value="capture">Capture Fishing</option>
                    <option value="aquaculture">Aquaculture</option>
                    <option value="vending">Fish Vending</option>
                    <option value="processing">Fish Processing</option>
                    <option value="others">Others</option>
                </select>
                @error('main_livelihood')
                    <span class="fishr-error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="fishr-form-group" id="fishr-other-livelihood-field"
                style="display: {{ old('main_livelihood') == 'others' ? 'block' : 'none' }};">
                <label for="fishr-other_livelihood">Please specify (if others) <span
                        style="color: #dc3545; font-weight: bold;">*</span></label>
                <input type="text" id="fishr-other_livelihood" name="other_livelihood"
                    placeholder="Specify other livelihood" value="{{ old('other_livelihood') }}">
                @error('other_livelihood')
                    <span class="fishr-error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="fishr-form-group">
                <label for="fishr-secondary_livelihood">Secondary Livelihood (Optional)</label>
                <select id="fishr-secondary_livelihood" name="secondary_livelihood"
                    onchange="toggleOtherSecondaryLivelihood(this); validateSecondaryLivelihoodMatch()">
                    <option value="" selected>Select Livelihood</option>
                    <option value="capture">Capture Fishing</option>
                    <option value="aquaculture">Aquaculture</option>
                    <option value="vending">Fish Vending</option>
                    <option value="processing">Fish Processing</option>
                    <option value="others">Others</option>
                </select>
                <span class="validation-warning" id="fishr-secondary_livelihood-warning"
                    style="color: #ff6b6b; font-size: 0.875rem; display: none; margin-top: 4px;">Secondary livelihood
                    cannot be the same as main livelihood</span>
                @error('secondary_livelihood')
                    <span class="fishr-error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="fishr-form-group" id="fishr-other-secondary-livelihood-field" style="display: none;">
                <label for="fishr-other_secondary_livelihood">Please specify (if others) <span
                        style="color: #dc3545; font-weight: bold;">*</span></label>
                <input type="text" id="fishr-other_secondary_livelihood" name="other_secondary_livelihood"
                    placeholder="Specify other secondary livelihood" value="{{ old('other_secondary_livelihood') }}">
                @error('other_secondary_livelihood')
                    <span class="fishr-error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="fishr-form-group">
                <label for="supporting_document">
                    <span class="label-text">Supporting Document (Optional)</span>
                </label>
                <input type="file" id="supporting_document" name="supporting_document"
                    accept=".pdf,.jpg,.jpeg,.png">
                <small class="fishr-form-text">Upload Government ID or Barangay Certificate (PDF, JPG, PNG - Max
                    10MB)</small>
                @error('supporting_documents')
                    <span class="fishr-error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="fishr-form-buttons">
                <button type="button" class="fishr-cancel-btn" onclick="closeFormFishR()">Cancel</button>
                <button type="submit" class="fishr-submit-btn" id="fishr-submit-btn">
                    <span class="fishr-btn-text">Submit Application</span>
                    <span class="fishr-btn-loading" style="display: none;">
                        <i class="fas fa-spinner fa-spin"></i> Submitting...
                    </span>
                </button>
            </div>
        </form>
    </div>

    <div class="fishr-tab-content tab-content" id="fishr-requirements-tab">
        <h4>Who may avail:</h4>
        <ul>
            <li>Municipal Fisherfolk residing in the City of San Pedro, Laguna</li>
        </ul>

        <h4>Standard Requirements</h4>
        <ul>
            <li>
                <strong>Accomplished FishR Application Form</strong><br>
                <small>Provided by the City Agriculture Office or through the online system.</small>
            </li>
            <li>
                <strong>Barangay Certificate</strong><br>
                <small>Proof of residency and engagement in fishery-related livelihood.</small>
                <small>Issued by the client’s Barangay Hall.</small>
            </li>
            <li>
                <strong>One (1) recent 1×1 ID Picture</strong><br>
                <small>White background</small>
            </li>
        </ul>

        <h4>Additional Supporting Documents (If Applicable / For Validation Only)</h4>
        <h5>(Required only depending on the type of livelihood)</h5>
        <ul>
            <li><strong>Aquaculture</strong> – Proof of fishpond, aquaculture facility, or certification</li>
            <li><strong>Fish Vending</strong> – Proof of vending activity or barangay certification</li>
            <li><strong>Fish Processing</strong> – Proof of processing activity or facility</li>
            <li><strong>Others</strong> – Any relevant document supporting fishery-related livelihood</li>
        </ul>
        <h5><strong>Note:</strong> Capture fishing does not require additional documents beyond the standard
            requirements.</h5>
    </div>

    <div class="fishr-tab-content tab-content" id="fishr-info-tab">

        <!-- DSS Report Information -->
        @if (isset($fishrReport) && $fishrReport['exists'])
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
                            {{ \Carbon\Carbon::parse($fishrReport['generated_at'])->format('M d, Y H:i:s') }}</div>
                    </div>
                    <div
                        style="background: rgba(255,255,255,0.1); padding: 12px; border-radius: 6px; backdrop-filter: blur(10px);">
                        <div style="font-size: 0.85rem; opacity: 0.9; margin-bottom: 5px;">🤖 Analysis Source</div>
                        <div style="font-weight: 600; font-size: 0.95rem;">{{ ucfirst($fishrReport['source']) }}</div>
                    </div>
                    <div
                        style="background: rgba(255,255,255,0.1); padding: 12px; border-radius: 6px; backdrop-filter: blur(10px);">
                        <div style="font-size: 0.85rem; opacity: 0.9; margin-bottom: 5px;">📊 Data Period</div>
                        <div style="font-weight: 600; font-size: 0.95rem;">{{ $fishrReport['period_label'] }}</div>
                    </div>
                </div>
            </div>
        @endif

        <h4>FishR Registration</h4>
        <p>FishR (Fisherfolk Registration) is a national registry for municipal fisherfolk implemented in compliance
            with
            Republic Act No. 8550, Executive Order No. 305, and Section 32 of City Ordinance No. 2023-21 (Fishing
            Regulations Code of the City of San Pedro).</p>

        <h4>Purpose of FishR Registration</h4>
        <ul>
            <li>Establish an official database of municipal fisherfolk</li>
            <li>Ensure fair and transparent access to fisheries programs</li>
            <li>Support planning, policy formulation, and resource management</li>
        </ul>

        <h4>Benefits of FishR Registration</h4>
        <p>Registered fisherfolk may be eligible for:</p>
        <ul>
            <li>Fisheries and livelihood assistance programs</li>
            <li>Training, seminars, and capacity-building activities</li>
            <li>Insurance and disaster assistance programs</li>
            <li>Priority access to government support</li>
            <li>Legal recognition as municipal fisherfolk</li>
        </ul>
    </div>
</section>

<script>
    // Real-time validation for name fields
    document.addEventListener('DOMContentLoaded', function() {
        const nameFields = [{
                id: 'fishr-first_name',
                pattern: /^[a-zA-Z\s\'-]*$/
            },
            {
                id: 'fishr-middle_name',
                pattern: /^[a-zA-Z\s\'-]*$/
            },
            {
                id: 'fishr-last_name',
                pattern: /^[a-zA-Z\s\'-]*$/
            },
            {
                id: 'fishr-name_extension',
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

                // Also validate on blur
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
