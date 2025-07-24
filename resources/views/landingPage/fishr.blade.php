<!-- FishR Registration Form -->
<section class="application-section" id="fishr-form">
    <div class="form-header">
        <h2>FishR Registration</h2>
        <p>National Program for Municipal Fisherfolk Registration - Register as a municipal fisherfolk.</p>
    </div>

    <div class="form-tabs">
        <button class="tab-btn active" onclick="showTab('fishr-form-tab', event)">Application Form</button>
        <button class="tab-btn" onclick="showTab('fishr-requirements-tab', event)">Requirements</button>
        <button class="tab-btn" onclick="showTab('fishr-info-tab', event)">Information</button>
    </div>

    <div class="tab-content" id="fishr-form-tab">
        <!-- Success/Error Messages -->
        <div id="fishr-messages" style="display: none;">
            <div id="fishr-success-message" class="alert alert-success" style="display: none;"></div>
            <div id="fishr-error-message" class="alert alert-danger" style="display: none;"></div>
        </div>

        <form id="fishr-registration-form" method="POST" action="{{ route('apply.fishr') }}" enctype="multipart/form-data">
            @csrf
            
            <div class="form-group">
                <label for="first_name">First Name *</label>
                <input type="text" id="first_name" name="first_name" placeholder="Enter your first name" 
                       value="{{ old('first_name') }}" required>
                @error('first_name')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="middle_name">Middle Name (Optional)</label>
                <input type="text" id="middle_name" name="middle_name" placeholder="Enter your middle name"
                       value="{{ old('middle_name') }}">
                @error('middle_name')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="last_name">Last Name *</label>
                <input type="text" id="last_name" name="last_name" placeholder="Enter your last name" 
                       value="{{ old('last_name') }}" required>
                @error('last_name')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="sex">Sex *</label>
                <select id="sex" name="sex" required>
                    <option value="">Select sex</option>
                    <option value="Male" {{ old('sex') == 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ old('sex') == 'Female' ? 'selected' : '' }}>Female</option>
                    <option value="Preferred not to say" {{ old('sex') == 'Preferred not to say' ? 'selected' : '' }}>Preferred not to say</option>
                </select>
                @error('sex')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="barangay">Barangay *</label>
                <select id="barangay" name="barangay" required>
                    <option value="">Select barangay</option>
                    <option value="Bagong Silang" {{ old('barangay') == 'Bagong Silang' ? 'selected' : '' }}>Bagong Silang</option>
                    <option value="Cuyab" {{ old('barangay') == 'Cuyab' ? 'selected' : '' }}>Cuyab</option>
                    <option value="Estrella" {{ old('barangay') == 'Estrella' ? 'selected' : '' }}>Estrella</option>
                    <option value="G.S.I.S." {{ old('barangay') == 'G.S.I.S.' ? 'selected' : '' }}>G.S.I.S.</option>
                    <option value="Landayan" {{ old('barangay') == 'Landayan' ? 'selected' : '' }}>Landayan</option>
                    <option value="Langgam" {{ old('barangay') == 'Langgam' ? 'selected' : '' }}>Langgam</option>
                    <option value="Laram" {{ old('barangay') == 'Laram' ? 'selected' : '' }}>Laram</option>
                    <option value="Magsaysay" {{ old('barangay') == 'Magsaysay' ? 'selected' : '' }}>Magsaysay</option>
                    <option value="Nueva" {{ old('barangay') == 'Nueva' ? 'selected' : '' }}>Nueva</option>
                    <option value="Poblacion" {{ old('barangay') == 'Poblacion' ? 'selected' : '' }}>Poblacion</option>
                    <option value="Riverside" {{ old('barangay') == 'Riverside' ? 'selected' : '' }}>Riverside</option>
                    <option value="San Antonio" {{ old('barangay') == 'San Antonio' ? 'selected' : '' }}>San Antonio</option>
                    <option value="San Roque" {{ old('barangay') == 'San Roque' ? 'selected' : '' }}>San Roque</option>
                    <option value="San Vicente" {{ old('barangay') == 'San Vicente' ? 'selected' : '' }}>San Vicente</option>
                    <option value="Santo Niño" {{ old('barangay') == 'Santo Niño' ? 'selected' : '' }}>Santo Niño</option>
                    <option value="United Bayanihan" {{ old('barangay') == 'United Bayanihan' ? 'selected' : '' }}>United Bayanihan</option>
                    <option value="United Better Living" {{ old('barangay') == 'United Better Living' ? 'selected' : '' }}>United Better Living</option>
                    <option value="Sampaguita Village" {{ old('barangay') == 'Sampaguita Village' ? 'selected' : '' }}>Sampaguita Village</option>
                    <option value="Calendola" {{ old('barangay') == 'Calendola' ? 'selected' : '' }}>Calendola</option>
                    <option value="Narra" {{ old('barangay') == 'Narra' ? 'selected' : '' }}>Narra</option>
                    <option value="Chrysanthemum" {{ old('barangay') == 'Chrysanthemum' ? 'selected' : '' }}>Chrysanthemum</option>
                    <option value="Fatima" {{ old('barangay') == 'Fatima' ? 'selected' : '' }}>Fatima</option>
                    <option value="Maharlika" {{ old('barangay') == 'Maharlika' ? 'selected' : '' }}>Maharlika</option>
                    <option value="Pacita 1" {{ old('barangay') == 'Pacita 1' ? 'selected' : '' }}>Pacita 1</option>
                    <option value="Pacita 2" {{ old('barangay') == 'Pacita 2' ? 'selected' : '' }}>Pacita 2</option>
                    <option value="Rosario" {{ old('barangay') == 'Rosario' ? 'selected' : '' }}>Rosario</option>
                    <option value="San Lorenzo Ruiz" {{ old('barangay') == 'San Lorenzo Ruiz' ? 'selected' : '' }}>San Lorenzo Ruiz</option>
                </select>
                @error('barangay')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="mobile_number">Mobile Number *</label>
                <input type="tel" id="mobile_number" name="mobile_number" placeholder="Enter your mobile number (e.g., 09123456789)" 
                       value="{{ old('mobile_number') }}" required>
                @error('mobile_number')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="main_livelihood">Main Livelihood *</label>
                <select id="main_livelihood" name="main_livelihood" required onchange="toggleOtherLivelihood(this)">
                    <option value="">Select livelihood</option>
                    <option value="capture" {{ old('main_livelihood') == 'capture' ? 'selected' : '' }}>Capture Fishing</option>
                    <option value="aquaculture" {{ old('main_livelihood') == 'aquaculture' ? 'selected' : '' }}>Aquaculture</option>
                    <option value="vending" {{ old('main_livelihood') == 'vending' ? 'selected' : '' }}>Fish Vending</option>
                    <option value="processing" {{ old('main_livelihood') == 'processing' ? 'selected' : '' }}>Fish Processing</option>
                    <option value="others" {{ old('main_livelihood') == 'others' ? 'selected' : '' }}>Others</option>
                </select>
                @error('main_livelihood')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group" id="other-livelihood-field" style="display: {{ old('main_livelihood') == 'others' ? 'block' : 'none' }};">
                <label for="other_livelihood">Please specify (if others) *</label>
                <input type="text" id="other_livelihood" name="other_livelihood" placeholder="Specify other livelihood"
                       value="{{ old('other_livelihood') }}">
                @error('other_livelihood')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="supporting_documents">Supporting Documents</label>
                <input type="file" id="supporting_documents" name="supporting_documents" accept=".pdf,.jpg,.jpeg,.png">
                <small class="form-text">Required for all livelihood types except Capture Fishing. Max size: 10MB</small>
                @error('supporting_documents')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-buttons">
                <button type="button" class="cancel-btn" onclick="closeFormFishR()">Cancel</button>
                <button type="submit" class="submit-btn" id="fishr-submit-btn">
                    <span class="btn-text">Submit Application</span>
                    <span class="btn-loading" style="display: none;">
                        <i class="fas fa-spinner fa-spin"></i> Submitting...
                    </span>
                </button>
            </div>
        </form>
    </div>

    <div class="tab-content" id="fishr-requirements-tab">
        <h3>Required Documents</h3>
        <ul>
            <li>Valid government-issued ID</li>
            <li>Proof of residency in San Pedro, Laguna</li>
            <li>Recent 1x1 ID picture</li>
            <li>Proof of fishing activity (photo evidence)</li>
            <li>Barangay Certificate of Residency</li>
        </ul>

        <h3>Livelihood-Specific Requirements</h3>
        <ul>
            <li><strong>Capture Fishing:</strong> No additional documents required</li>
            <li><strong>Aquaculture:</strong> Proof of fishpond/aquaculture facility</li>
            <li><strong>Fish Vending:</strong> Business permit or proof of vending activity</li>
            <li><strong>Fish Processing:</strong> Processing facility photos or permit</li>
            <li><strong>Others:</strong> Relevant proof of fishing-related livelihood</li>
        </ul>
    </div>

    <div class="tab-content" id="fishr-info-tab">
        <h3>Important Information</h3>
        <p><strong>Processing Time:</strong> Applications are reviewed within 3–5 business days.</p>
        <p><strong>Contact Information:</strong> For inquiries, contact the City Agriculture Office.</p>
        
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