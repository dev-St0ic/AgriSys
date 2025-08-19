<!-- FishR Registration Form -->
<section class="fishr-application-section" id="fishr-form">
    <div class="fishr-form-header">
        <h2>FishR Registration</h2>
        <p>National Program for Municipal Fisherfolk Registration - Register as a municipal fisherfolk.</p>
    </div>

    <div class="fishr-form-tabs">
        <button class="fishr-tab-btn tab-btn active" onclick="showFishrTab('fishr-form-tab', event)">Application Form</button>
        <button class="fishr-tab-btn tab-btn" onclick="showFishrTab('fishr-requirements-tab', event)">Requirements</button>
        <button class="fishr-tab-btn tab-btn" onclick="showFishrTab('fishr-info-tab', event)">Information</button>
    </div>

    <div class="fishr-tab-content tab-content" id="fishr-form-tab">
        <!-- Success/Error Messages -->
        <div id="fishr-messages" style="display: none;">
            <div id="fishr-success-message" class="fishr-alert fishr-alert-success" style="display: none;"></div>
            <div id="fishr-error-message" class="fishr-alert fishr-alert-danger" style="display: none;"></div>
        </div>

        <form id="fishr-registration-form" method="POST" action="{{ route('apply.fishr') }}" enctype="multipart/form-data">
            @csrf
            
            <div class="fishr-form-group">
                <label for="first_name">First Name *</label>
                <input type="text" id="first_name" name="first_name" placeholder="Enter your first name" 
                       value="{{ old('first_name') }}" required>
                @error('first_name')
                    <span class="fishr-error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="fishr-form-group">
                <label for="middle_name">Middle Name (Optional)</label>
                <input type="text" id="middle_name" name="middle_name" placeholder="Enter your middle name"
                       value="{{ old('middle_name') }}">
                @error('middle_name')
                    <span class="fishr-error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="fishr-form-group">
                <label for="last_name">Last Name *</label>
                <input type="text" id="last_name" name="last_name" placeholder="Enter your last name" 
                       value="{{ old('last_name') }}" required>
                @error('last_name')
                    <span class="fishr-error-text">{{ $message }}</span>
                @enderror
            </div>

           
                <div class="fishr-form-group">
                    <label for="sex">Sex *</label>
                    <select id="sex" name="sex" required>
                        <option value="" disabled>Select sex</option>
                        <option value="Male" {{ old('sex') == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('sex') == 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Preferred not to say" {{ old('sex') == 'Preferred not to say' ? 'selected' : '' }}>Preferred not to say</option>
                    </select>
                    @error('sex')
                        <span class="fishr-error-text">{{ $message }}</span>
                    @enderror
                </div>

                <div class="fishr-form-group">
                    <label for="barangay">Barangay *</label>
                    <select id="barangay" name="barangay" required>
                        <option value="" disabled>Select barangay</option>
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
                <label for="mobile_number">Mobile Number *</label>
                <input type="tel" id="mobile_number" name="mobile_number" placeholder="Enter your mobile number (e.g., 09123456789)" 
                        value="{{ old('mobile_number') }}" required>
                @error('mobile_number')
                    <span class="fishr-error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="fishr-form-group">
                <label for="email">Email Address *</label>
                <input type="email" id="email" name="email" placeholder="Enter your email address" 
                        value="{{ old('email') }}" required>
                @error('email')
                    <span class="fishr-error-text">{{ $message }}</span>
                @enderror
            </div>
            

            <div class="fishr-form-group">
                <label for="main_livelihood">Main Livelihood *</label>
                <select id="main_livelihood" name="main_livelihood" required onchange="toggleOtherLivelihood(this)">
                    <option value="" disabled>Select livelihood</option>
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

            <div class="fishr-form-group" id="other-livelihood-field" style="display: {{ old('main_livelihood') == 'others' ? 'block' : 'none' }};">
                <label for="other_livelihood">Please specify (if others) *</label>
                <input type="text" id="other_livelihood" name="other_livelihood" placeholder="Specify other livelihood"
                       value="{{ old('other_livelihood') }}">
                <small class="fishr-form-text">Please provide specific details about your livelihood activity</small>
                @error('other_livelihood')
                    <span class="fishr-error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="fishr-form-group">
                <label for="supporting_documents">Supporting Documents</label>
                <input type="file" id="supporting_documents" name="supporting_documents" accept=".pdf,.jpg,.jpeg,.png">
                <small class="fishr-form-text">Required for all livelihood types except Capture Fishing. Max size: 10MB</small>
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