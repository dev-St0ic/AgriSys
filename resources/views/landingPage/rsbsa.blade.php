<!-- RSBSA Registration Form (Updated and Streamlined) -->
<section class="rsbsa-application-section" id="new-rsbsa" style="display: none;">
    <div class="rsbsa-form-header">
        <h2>RSBSA Registration</h2>
        <p>Registry System for Basic Sectors in Agriculture - Register as a farmer, fisherfolk, or agricultural worker.
        </p>
    </div>

    <div class="rsbsa-form-tabs">
        <button class="rsbsa-tab-btn active" onclick="showRSBSATab('form', event)">Application Form</button>
        <button class="rsbsa-tab-btn" onclick="showRSBSATab('requirements', event)">Requirements</button>
        <button class="rsbsa-tab-btn" onclick="showRSBSATab('information', event)">Information</button>
    </div>

    <div class="rsbsa-tab-content" id="form" style="display: block;">
        <form method="POST" action="/apply/rsbsa" enctype="multipart/form-data" id="rsbsa-form">
            @csrf
            <input type="hidden" name="_token" value="{{ csrf_token() }}">

            <label>First Name <span style="color: #dc3545; font-weight: bold;">*</span></label>
            <input type="text" id="rsbsa-first_name" name="first_name" placeholder="Enter your first name"
                pattern="[a-zA-Z\s\'-]+" title="First name can only contain letters, spaces, hyphens, and apostrophes"
                required>
            <span class="validation-warning" id="rsbsa-first_name-warning"
                style="color: #ff6b6b; font-size: 0.875rem; display: none; margin-top: 4px;">⚠️ Only letters, spaces,
                hyphens, and apostrophes are allowed</span>

            <label>Middle Name (Optional)</label>
            <input type="text" id="rsbsa-middle_name" name="middle_name" placeholder="Enter your middle name"
                pattern="[a-zA-Z\s\'-]+" title="Middle name can only contain letters, spaces, hyphens, and apostrophes">
            <span class="validation-warning" id="rsbsa-middle_name-warning"
                style="color: #ff6b6b; font-size: 0.875rem; display: none; margin-top: 4px;">⚠️ Only letters, spaces,
                hyphens, and apostrophes are allowed</span>

            <label>Last Name <span style="color: #dc3545; font-weight: bold;">*</span></label>
            <input type="text" id="rsbsa-last_name" name="last_name" placeholder="Enter your last name"
                pattern="[a-zA-Z\s\'-]+" title="Last name can only contain letters, spaces, hyphens, and apostrophes"
                required>
            <span class="validation-warning" id="rsbsa-last_name-warning"
                style="color: #ff6b6b; font-size: 0.875rem; display: none; margin-top: 4px;">⚠️ Only letters, spaces,
                hyphens, and apostrophes are allowed</span>

            <label>Name Extension (Optional)</label>
            <select id="rsbsa-name_extension" name="name_extension">
                <option value="" selected>Select Extension</option>
                <option value="Jr.">Jr.</option>
                <option value="Sr.">Sr.</option>
                <option value="II">II</option>
                <option value="III">III</option>
                <option value="IV">IV</option>
                <option value="V">V</option>
            </select>

            <label>Sex <span style="color: #dc3545; font-weight: bold;">*</span></label>
            <select name="sex" required>
                <option value="" disabled selected>Select Sex</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Preferred not to say">Preferred not to say</option>
            </select>

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

            <label>Mobile Number <span style="color: #dc3545; font-weight: bold;">*</span></label>
            <input type="tel" name="mobile" placeholder="09XXXXXXXXX" pattern="^09\d{9}$"
                title="Mobile number must be in the format 09XXXXXXXXX (e.g., 09123456789)" required>

            <label>Main Livelihood <span style="color: #dc3545; font-weight: bold;">*</span></label>
            <select name="main_livelihood" required>
                <option value="" disabled selected>Select Livelihood</option>
                <option value="Farmer">Farmer</option>
                <option value="Farmworker/Laborer">Farmworker/Laborer</option>
                <option value="Fisherfolk">Fisherfolk</option>
                <option value="Agri-youth">Agri-youth</option>
            </select>

            <label>Land Area (in hectares)</label>
            <input type="number" name="land_area" step="0.01" min="0" max="1000"
                placeholder="Enter land area (optional)">

            <label>Farm Location</label>
            <input type="text" name="farm_location" placeholder="Enter farm location (optional)">

            <label>Commodity (Crops/Livestock)</label>
            <input type="text" name="commodity" placeholder="Enter what you grow or raise (optional)">

            <label>Supporting Document</label>
            <input type="file" name="supporting_docs" accept="image/*,.pdf" onchange="previewFile(this)">
            <small>
                For farmers: Upload a picture of the farm area.<br>
                For fisherfolk: Upload a photo of your aquaculture setup (e.g., fishpond, fish cage, fish pen).<br>
                Accepted formats: JPG, PNG, PDF (Max size: 5MB)
            </small>

            <!-- File preview area -->
            <div id="file-preview"
                style="display: none; margin-top: 10px; padding: 10px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px;">
                <p style="margin: 0 0 10px 0; color: #495057;"><strong>Selected file:</strong> <span
                        id="file-name"></span></p>
                <button type="button" onclick="removeFile()"
                    style="background: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer; font-size: 12px;">Remove
                    File</button>
            </div>

            <div class="rsbsa-form-buttons">
                <button type="button" class="rsbsa-cancel-btn" onclick="closeFormRSBSA()">Cancel</button>
                <button type="submit" class="rsbsa-submit-btn" id="rsbsa-submit-btn">Submit Application</button>
            </div>
        </form>
    </div>

    <div class="rsbsa-tab-content" id="requirements" style="display: none;">
        <h3>Required Documents</h3>
        <ul>
            <li>Valid government-issued ID</li>
            <li>Proof of residency in San Pedro, Laguna</li>
            <li>Recent 1x1 ID picture</li>
            <li>Land title or proof of land tenancy (if applicable)</li>
            <li>Barangay Certificate</li>
        </ul>
    </div>

    <div class="rsbsa-tab-content" id="information" style="display: none;">
        <h3>Important Information</h3>
        <p>All applications are subject to review and approval by the City Agriculture Office. Processing time is
            typically 3–5 working days. You may be contacted for additional information or verification.</p>

        <p>All information provided must be accurate and truthful. Submission of incomplete or incorrect information may
            result in delays or rejection.</p>

        <h3>Contact Information</h3>
        <p>For assistance with your application, please contact:</p>
        <ul>
            <li><strong>Phone:</strong> (123) 456-7890</li>
            <li><strong>Email:</strong> agriculture@sanpedro.gov.ph</li>
            <li><strong>Office Hours:</strong> Monday - Friday, 8:00 AM - 5:00 PM</li>
            <li><strong>Location:</strong> City Agriculture Office, San Pedro City Hall</li>
        </ul>

        <h3>What Happens Next?</h3>
        <ol>
            <li>Your application will be reviewed by our agriculture office</li>
            <li>We may contact you for additional verification</li>
            <li>Once approved, you will receive your RSBSA certificate</li>
            <li>You can pick up your certificate at the City Agriculture Office</li>
        </ol>
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
    });
</script>
