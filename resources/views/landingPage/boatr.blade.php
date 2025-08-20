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
        <form id="boatr-registration-form" onsubmit="submitBoatRForm(event)" enctype="multipart/form-data">
            @csrf
            
            <div class="boatr-form-group">
                <label for="boatr_first_name">First Name *</label>
                <input type="text" id="boatr_first_name" name="first_name" placeholder="Enter first name" required>
            </div>

            <div class="boatr-form-group">
                <label for="boatr_middle_name">Middle Name (Optional)</label>
                <input type="text" id="boatr_middle_name" name="middle_name" placeholder="Enter middle name">
            </div>

            <div class="boatr-form-group">
                <label for="boatr_last_name">Last Name *</label>
                <input type="text" id="boatr_last_name" name="last_name" placeholder="Enter last name" required>
            </div>

            <div class="boatr-form-group">
                <label for="boatr_mobile">Mobile Number *</label>
                <input type="tel" id="boatr_mobile" name="mobile" placeholder="Enter your mobile number (e.g., 09123456789)" required>
                <small class="boatr-form-help">Please provide a valid mobile number for SMS notifications.</small>
            </div>

            <div class="boatr-form-group">
                <label for="boatr_email">Email Address *</label>
                <input type="email" id="boatr_email" name="email" placeholder="Enter your email address" required>
                <small class="boatr-form-help">Please provide a valid email address for notifications.</small>
            </div>

            <div class="boatr-form-group">
                <label for="boatr_fishr_number">FishR Number *</label>
                <input type="text" id="boatr_fishr_number" name="fishr_number" placeholder="Enter FishR Number (e.g., FISHR-ABC12345)" required>
                <small class="boatr-form-help">Enter your approved FishR registration number</small>
            </div>

            <div class="boatr-form-group">
                <label for="boatr_vessel_name">Vessel Name *</label>
                <input type="text" id="boatr_vessel_name" name="vessel_name" placeholder="Enter vessel name" required>
            </div>

            <div class="boatr-form-group">
                <label for="boatr_boat_type">Boat Type *</label>
                <select id="boatr_boat_type" name="boat_type" required onchange="handleBoatTypeChange(this)">
                    <option value="" disabled selected>Select Boat Type</option>
                    <option value="Spoon">Spoon</option>
                    <option value="Plumb">Plumb</option>
                    <option value="Banca">Banca</option>
                    <option value="Rake Stem - Rake Stern">Rake Stem - Rake Stern</option>
                    <option value="Rake Stem - Transom/Spoon/Plumb Stern">Rake Stem - Transom/Spoon/Plumb Stern</option>
                    <option value="Skiff (Typical Design)">Skiff (Typical Design)</option>
                </select>
            </div>

            <div class="boatr-form-row">
                <div class="boatr-form-group">
                    <label for="boatr_boat_length">Length (feet) *</label>
                    <input type="number" id="boatr_boat_length" name="boat_length" step="0.01" min="1" max="200" placeholder="Length" required>
                </div>
                <div class="boatr-form-group">
                    <label for="boatr_boat_width">Width (feet) *</label>
                    <input type="number" id="boatr_boat_width" name="boat_width" step="0.01" min="1" max="50" placeholder="Width" required>
                </div>
                <div class="boatr-form-group">
                    <label for="boatr_boat_depth">Depth (feet) *</label>
                    <input type="number" id="boatr_boat_depth" name="boat_depth" step="0.01" min="1" max="30" placeholder="Depth" required>
                </div>
            </div>

            <div class="boatr-form-group">
                <label for="boatr_engine_type">Engine Type *</label>
                <input type="text" id="boatr_engine_type" name="engine_type" placeholder="Enter engine type (e.g., Yamaha Outboard Motor)" required>
            </div>

            <div class="boatr-form-group">
                <label for="boatr_engine_horsepower">Engine Horsepower *</label>
                <input type="number" id="boatr_engine_horsepower" name="engine_horsepower" step="1" min="1" max="500" placeholder="Enter engine horsepower" required>
            </div>

            <div class="boatr-form-group">
                <label for="boatr_primary_fishing_gear">Primary Fishing Gear Used *</label>
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
                    Upload one relevant document (PDF, JPG, JPEG, PNG - Max 10MB). 
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
        <h3>Important Information</h3>
        <p>Please review the following before submitting:</p>
        <ul>
            <li>Only municipal fishing boats will be registered under BoatR.</li>
            <li>On-site inspection is required and will be scheduled after your application submission.</li>
            <li>You may upload one document now, but originals must be presented during inspection.</li>
            <li>Ensure accuracy of all information submitted.</li>
            <li>Processing time is typically 5-10 business days after inspection.</li>
            <li>You will receive SMS notifications for status updates.</li>
            <li>Contact City Agriculture Office at (123) 456-7890 or email agriculture@sanpedro.gov.ph</li>
        </ul>
        
        <h3>Application Process</h3>
        <ol>
            <li><strong>Submit Application:</strong> Complete and submit this form with required information</li>
            <li><strong>Initial Review:</strong> Application reviewed by City Agriculture Office</li>
            <li><strong>Inspection Scheduled:</strong> On-site boat inspection appointment set</li>
            <li><strong>Physical Inspection:</strong> Boat and documents verified by inspector</li>
            <li><strong>Final Review:</strong> Application processed for approval/rejection</li>
            <li><strong>Certificate Issued:</strong> BoatR certificate issued if approved</li>
        </ol>
    </div>
</section>