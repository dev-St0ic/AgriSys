<!-- FishR Registration Form -->
<section class="fishr-application-section" id="fishr-form">
    <div class="fishr-form-header">
        <h2>FishR Registration</h2>
        <p>National Program for Municipal Fisherfolk Registration - Register as a municipal fisherfolk.</p>
    </div>

    <div class="fishr-form-tabs">
        <button class="fishr-tab-btn tab-btn active" onclick="showFishrTab('fishr-form-tab', event)">Application
            Form</button>
        <button class="fishr-tab-btn tab-btn"
            onclick="showFishrTab('fishr-requirements-tab', event)">Requirements</button>
        <button class="fishr-tab-btn tab-btn" onclick="showFishrTab('fishr-info-tab', event)">Information</button>
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
                <label for="fishr-first_name">First Name *</label>
                <input type="text" id="fishr-first_name" name="first_name" placeholder="Enter your first name"
                    pattern="[a-zA-Z\s\'-]+"
                    title="First name can only contain letters, spaces, hyphens, and apostrophes"
                    value="{{ old('first_name') }}" required>
                <span class="validation-warning" id="fishr-first_name-warning"
                    style="color: #ff6b6b; font-size: 0.875rem; display: none; margin-top: 4px;">⚠️ Only letters,
                    spaces, hyphens, and apostrophes are allowed</span>
                @error('first_name')
                    <span class="fishr-error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="fishr-form-group">
                <label for="fishr-middle_name">Middle Name (Optional)</label>
                <input type="text" id="fishr-middle_name" name="middle_name" placeholder="Enter your middle name"
                    pattern="[a-zA-Z\s\'-]+"
                    title="Middle name can only contain letters, spaces, hyphens, and apostrophes"
                    value="{{ old('middle_name') }}">
                <span class="validation-warning" id="fishr-middle_name-warning"
                    style="color: #ff6b6b; font-size: 0.875rem; display: none; margin-top: 4px;">⚠️ Only letters,
                    spaces, hyphens, and apostrophes are allowed</span>
                @error('middle_name')
                    <span class="fishr-error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="fishr-form-group">
                <label for="fishr-last_name">Last Name *</label>
                <input type="text" id="fishr-last_name" name="last_name" placeholder="Enter your last name"
                    pattern="[a-zA-Z\s\'-]+"
                    title="Last name can only contain letters, spaces, hyphens, and apostrophes"
                    value="{{ old('last_name') }}" required>
                <span class="validation-warning" id="fishr-last_name-warning"
                    style="color: #ff6b6b; font-size: 0.875rem; display: none; margin-top: 4px;">⚠️ Only letters,
                    spaces, hyphens, and apostrophes are allowed</span>
                @error('last_name')
                    <span class="fishr-error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="fishr-form-group">
                <label for="fishr-name_extension">Name Extension (Optional)</label>
                <input type="text" id="fishr-name_extension" name="name_extension" placeholder="Jr., Sr., III, etc."
                    pattern="[a-zA-Z.\s]+" title="Name extension can only contain letters, periods, and spaces"
                    value="{{ old('name_extension') }}">
                <span class="validation-warning" id="fishr-name_extension-warning"
                    style="color: #ff6b6b; font-size: 0.875rem; display: none; margin-top: 4px;">⚠️ Only letters,
                    periods, and spaces are allowed</span>
                @error('name_extension')
                    <span class="fishr-error-text">{{ $message }}</span>
                @enderror
            </div>


            <div class="fishr-form-group">
                <label for="fishr-sex">Sex *</label>
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
                <label for="fishr-barangay">Barangay *</label>
                <select id="fishr-barangay" name="barangay" required>
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
                    <span class="fishr-error-text">{{ $message }}</span>
                @enderror
            </div>


            <div class="fishr-form-group">
                <label for="fishr-contact_number">Contact Number *</label>
                <input type="tel" id="fishr-contact_number" name="contact_number"
                    placeholder="+639XXXXXXXXX or 09XXXXXXXXX" value="{{ old('contact_number') }}"
                    pattern="^(\+639|09)\d{9}$"
                    title="Contact number must be in the format +639XXXXXXXXX or 09XXXXXXXXX (e.g., +639123456789 or 09123456789)"
                    required>
                @error('contact_number')
                    <span class="fishr-error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="fishr-form-group">
                <label for="fishr-email">Email Address *</label>
                <input type="email" id="fishr-email" name="email" placeholder="Enter your email address"
                    value="{{ old('email') }}" required>
                @error('email')
                    <span class="fishr-error-text">{{ $message }}</span>
                @enderror
            </div>


            <div class="fishr-form-group">
                <label for="fishr-main_livelihood">Main Livelihood *</label>
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
                <label for="fishr-other_livelihood">Please specify (if others) *</label>
                <input type="text" id="fishr-other_livelihood" name="other_livelihood"
                    placeholder="Specify other livelihood" value="{{ old('other_livelihood') }}">
                <small class="fishr-form-text">Please provide specific details about your livelihood activity</small>
                @error('other_livelihood')
                    <span class="fishr-error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="fishr-form-group">
                <label for="supporting_documents">Supporting Documents</label>
                <input type="file" id="supporting_documents" name="supporting_documents"
                    accept=".pdf,.jpg,.jpeg,.png">
                <small class="fishr-form-text">Required for all livelihood types except Capture Fishing. Max size:
                    10MB</small>
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
        <h4>Required Documents</h4>
        <ul>
            <li>Valid government-issued ID</li>
            <li>Proof of residency in San Pedro, Laguna</li>
            <li>Recent 1x1 ID picture</li>
            <li>Proof of fishing activity (photo evidence)</li>
            <li>Barangay Certificate of Residency</li>
        </ul>

        <h4>Livelihood-Specific Requirements</h4>
        <ul>
            <li><strong>Capture Fishing:</strong> No additional documents required</li>
            <li><strong>Aquaculture:</strong> Proof of fishpond/aquaculture facility</li>
            <li><strong>Fish Vending:</strong> Business permit or proof of vending activity</li>
            <li><strong>Fish Processing:</strong> Processing facility photos or permit</li>
            <li><strong>Others:</strong> Relevant proof of fishing-related livelihood</li>
        </ul>
    </div>

    <div class="fishr-tab-content tab-content" id="fishr-info-tab">

        <h4>Benefits of FishR Registration</h4>
        <ul>
            <li>Access to government fisheries programs</li>
            <li>Eligibility for livelihood assistance</li>
            <li>Priority in training and seminars</li>
            <li>Insurance coverage eligibility</li>
            <li>Legal recognition as municipal fisherfolk</li>
        </ul>

        <h4>Status Updates</h4>
        <p>You will receive SMS notifications for:</p>
        <ul>
            <li>Application receipt confirmation</li>
            <li>Status updates (under review, approved, rejected)</li>
            <li>Additional requirements (if needed)</li>
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
