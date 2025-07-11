        <!-- RSBSA Registration Choice -->
        <section class="application-section" id="rsbsa-choice" style="display: none;">
        <div class="form-header">
            <h2>Choose Registration Type</h2>
            <p>Are you a new registrant or already registered in RSBSA?</p>
        </div>
        <div style="text-align: center; margin-top: 20px;">
            <button class="btn btn-choice" onclick="openNewRSBSA()">New Registration</button>
            <button class="btn btn-choice" onclick="openOldRSBSA()">Old Registration</button>
        </div>
        </section>

        <!-- RSBSA Request Form -->
        <section class="application-section" id="new-rsbsa" style="display: none;">
            <div class="form-header">
                <h2>RSBSA Registration</h2>
                <p>Registry System for Basic Sectors in Agriculture - Register as a farmer, fisherfolk, or agricultural worker.</p>
            </div>

            <div class="form-tabs">
                <button class="tab-btn active" onclick="showTab('form', event)">Application Form</button>
                <button class="tab-btn" onclick="showTab('requirements', event)">Requirements</button>
                <button class="tab-btn" onclick="showTab('information', event)">Information</button>
            </div>

            <div class="tab-content" id="form" style="display: block;">
                <form>
                    <label>First Name</label>
                    <input type="text" placeholder="Enter your first name" required>

                    <label>Middle Name</label>
                    <input type="text" placeholder="Enter your middle name">

                    <label>Last Name</label>
                    <input type="text" placeholder="Enter your last name" required>

                    <label>Sex</label>
                    <select required>
                        <option value="">Select sex</option>
                        <option>Male</option>
                        <option>Female</option>
                        <option>Preferred not to say</option>
                    </select>

                    <label>Barangay</label>
                    <select required>
                        <option value="">Select barangay</option>
                        <option>Bagong Silang</option>
                        <option>Cuyab</option>
                        <option>Estrella</option>
                        <option>G.S.I.S.</option>
                        <option>Landayan</option>
                        <option>Langgam</option>
                        <option>Laram</option>
                        <option>Magsaysay</option>
                        <option>Nueva</option>
                        <option>Poblacion</option>
                        <option>Riverside</option>
                        <option>San Antonio</option>
                        <option>San Roque</option>
                        <option>San Vicente</option>
                        <option>Santo Niño</option>
                        <option>United Bayanihan</option>
                        <option>United Better Living</option>
                        <option>Sampaguita Village</option>
                        <option>Calendola</option>
                        <option>Narra</option>
                        <option>Chrysanthemum</option>
                        <option>Fatima</option>
                        <option>Maharlika</option>
                        <option>Pacita 1</option>
                        <option>Pacita 2</option>
                        <option>Rosario</option>
                        <option>San Lorenzo Ruiz</option>
                    </select>

                    <label>Mobile Number</label>
                    <input type="tel" placeholder="Enter your mobile number" required>

                    <label>Main Livelihood</label>
                    <select required>
                        <option value="">Select livelihood</option>
                        <option>Farmer</option>
                        <option>Farmworker/Laborer</option>
                        <option>Fisherfolk</option>
                        <option>Agri-youth</option>
                    </select>

                    <label>Land Area (in hectares)</label>
                    <input type="number" step="0.01" placeholder="Enter land area">

                    <label>Farm Location</label>
                    <input type="text" placeholder="Enter farm location">

                    <label>Commodity (Crops/Livestock)</label>
                    <input type="text" placeholder="Enter what you grow or raise">

                    <label>Supporting Document</label>
                    <input type="file">
                    <small>
                        For farmers: Upload a picture of the farm area.<br>
                        For fisherfolk: Upload a photo of your aquaculture setup (e.g., fishpond, fish cage, fish pen).
                    </small>

                    <div class="form-buttons">
                        <button type="button" class="cancel-btn" onclick="closeFormRSBSA()">Cancel</button>
                        <button type="submit" class="submit-btn">Submit Application</button>
                    </div>
                </form>
            </div>

            <div class="tab-content" id="requirements">
                <h3>Required Documents</h3>
                <ul>
                    <li>Valid government-issued ID</li>
                    <li>Proof of residency in San Pedro, Laguna</li>
                    <li>Recent 1x1 ID picture</li>
                    <li>Land title or proof of land tenancy (if applicable)</li>
                    <li>Barangay Certificate</li>
                </ul>
            </div>

            <div class="tab-content" id="information">
                <h3>Important Information</h3>
                <p>All applications are subject to review and approval by the City Agriculture Office. Processing time is typically 3–5 working days. You may be contacted for additional information or verification.</p>
                <p>All information provided must be accurate and truthful. Submission of incomplete or incorrect information may result in delays or rejection.</p>
                <p>For assistance with your application, please contact the City Agriculture Office at (123) 456-7890 or email agriculture@sanpedro.gov.ph</p>
            </div>
        </section>

        <!-- Old RSBSA Registration -->
        <section class="application-section" id="old-rsbsa" style="display: none;">
            <div class="form-header">
                <h2>Old RSBSA Registration</h2>
                <p>For individuals already registered in the RSBSA system with a reference number.</p>
            </div>

            <div class="form-tabs">
                <button class="tab-btn active" onclick="showTab('old-form', event)">Application Form</button>
                <button class="tab-btn" onclick="showTab('old-requirements', event)">Requirements</button>
                <button class="tab-btn" onclick="showTab('old-information', event)">Information</button>
            </div>

            <div class="tab-content" id="old-form" style="display: block;">
                <form>
                    <label>First Name</label>
                    <input type="text" placeholder="Enter your first name" required>

                    <label>Middle Name</label>
                    <input type="text" placeholder="Enter your middle name">

                    <label>Last Name</label>
                    <input type="text" placeholder="Enter your last name" required>

                    <label>Date of Birth</label>
                    <input type="date" required>

                    <label>RSBSA Reference Number</label>
                    <input type="text" placeholder="Enter your RSBSA Reference Number" required>

                    <div class="form-buttons">
                        <button type="button" class="cancel-btn" onclick="closeFormRSBSA()">Cancel</button>
                        <button type="submit" class="submit-btn">Submit Application</button>
                    </div>
                </form>
            </div>

            <div class="tab-content" id="old-requirements">
                <h3>Required Documents</h3>
                <ul>
                    <li>Valid government-issued ID</li>
                    <li>Proof of existing RSBSA record (if available)</li>
                </ul>
            </div>

            <div class="tab-content" id="old-information">
                <h3>Important Information</h3>
                <p>Please ensure that your information matches the existing RSBSA record. If you have misplaced your reference number, contact the City Agriculture Office for verification assistance.</p>
                <p>For help, call (123) 456-7890 or email agriculture@sanpedro.gov.ph</p>
            </div>
        </section>

