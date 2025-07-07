<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AgriSys - San Pedro City Agriculture Office</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    <script src="{{ asset('js/landing.js') }}"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <header>
        <div class="header-left">
            <div class="logo">AgriSys</div>
            <div class="location">
                <div class="city">San Pedro</div>
                <div>City Agriculture Office</div>
            </div>
        </div>
        <div class="buttons">
            <button type="button" class="btn" onclick="goHome(event)">Home</button>
            <button type="button" class="btn" onclick="openFormRSBSA(event)">RSBSA</button>
            <button type="button" class="btn" onclick="openFormSeedlings(event)">Seedlings</button>
            <button type="button" class="btn" onclick="openFormFishR(event)">FishR</button>
            <button type="button" class="btn" onclick="openFormBoatR(event)">BoatR</button>

        </div>
    </header>

    <section class="welcome"  id="home">
        <h2>Welcome to AgriSys</h2>
        <p>The Agricultural Service System of the City Agriculture Office of San Pedro, Laguna</p>
        <button class="btn-services" onclick="document.getElementById('services').scrollIntoView({ behavior: 'smooth' })">Explore Services</button>
    </section>

    <section class="announcement">
        <p><strong>📢 Announcement:</strong> Seedling distribution starts July 1, 2025. Visit the Seedlings section for more info.</p>
    </section>

    <!-- Services -->
    <section class="services" id="services">
        <h2>OUR SERVICES</h2>
        <div class="card-container">
            <div class="card">
                <h3>RSBSA Registration</h3>
                <p>Register your details for the Registry System for Basic Sectors in Agriculture (RSBSA).</p>
                <button class="btn" onclick="openFormRSBSA(event)">Apply Now</button>
            </div>
            <div class="card">
                <h3>Seedlings Request</h3>
                <p>Request free seedlings to support your agricultural livelihood.</p>
                <button class="btn" onclick="openFormSeedlings(event)">Apply Now</button>
            </div>
            <div class="card">
                <h3>FishR Registration</h3>
                <p>Register in the FishR system for fisherfolk support and services.</p>
                <button class="btn" onclick="openFormFishR(event)">Apply Now</button>
            </div>
            <div class="card">
                <h3>BoatR Registration</h3>
                <p>Apply for registration and assistance for your fishing boats.</p>
                <button class="btn" onclick="openFormBoatR(event)">Apply Now</button>
            </div>
        </div>
    </section>

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


        <!-- Seedlings Choice Section -->
        <section class="application-section" id="seedlings-choice" style="display: none;">
            <div class="form-header">
                <h2>Seedlings Request</h2>
                <p>Select the seedlings and/or fertilizer you want to request, then click Next.</p>
            </div>
            <form id="seedlings-choice-form">
                <div class="seedlings-columns">
    <div class="seedlings-col">
        <strong>Vegetable Seedlings</strong>
        <label class="seedling-option"><input type="checkbox" name="vegetables" value="sampaguita"> Sampaguita</label>
        <label class="seedling-option"><input type="checkbox" name="vegetables" value="siling haba"> Siling Haba</label>
        <label class="seedling-option"><input type="checkbox" name="vegetables" value="siling labuyo"> Siling Labuyo</label>
        <label class="seedling-option"><input type="checkbox" name="vegetables" value="eggplant"> Eggplant</label>
        <label class="seedling-option"><input type="checkbox" name="vegetables" value="kamatis"> Kamatis</label>
        <label class="seedling-option"><input type="checkbox" name="vegetables" value="okra"> Okra</label>
        <label class="seedling-option"><input type="checkbox" name="vegetables" value="kalabasa"> Kalabasa</label>
        <label class="seedling-option"><input type="checkbox" name="vegetables" value="upo"> Upo</label>
        <label class="seedling-option"><input type="checkbox" name="vegetables" value="pipino"> Pipino</label>
    </div>
    <div class="seedlings-col">
        <strong>Fruit-bearing Seedlings</strong>
        <label class="seedling-option"><input type="checkbox" name="fruits" value="kalamansi"> Kalamansi</label>
        <label class="seedling-option"><input type="checkbox" name="fruits" value="guyabano"> Guyabano</label>
        <label class="seedling-option"><input type="checkbox" name="fruits" value="lanzones"> Lanzones</label>
        <label class="seedling-option"><input type="checkbox" name="fruits" value="mangga"> Mangga</label>
    </div>
    <div class="seedlings-col">
        <strong>Organic Fertilizer</strong>
        <label class="seedling-option"><input type="radio" name="fertilizer" value="chicken manure"> Pre-processed Chicken Manure</label>
        <label class="seedling-option"><input type="radio" name="fertilizer" value="humic acid"> Humic Acid</label>
        <label class="seedling-option"><input type="radio" name="fertilizer" value="vermicast"> Vermicast</label>
    </div>
</div>
                <div style="text-align:center; margin-top:30px;">
                    <button type="button" class="btn" onclick="proceedToSeedlingsForm()">Next</button>
                </div>
            </form>
        </section>

        <!--  Seedlings Request Form -->
        <section class="application-section" id="seedlings-form" style="display: none;">
            <div class="form-header">
                <h2>Seedlings Request - Applicant Details</h2>
                <p>Fill in your personal information to complete your request.</p>
            </div>
            <form id="seedlings-request-form" onsubmit="return submitSeedlingsRequest(event)">
                <label>First Name</label>
                <input type="text" name="first_name" placeholder="Enter your first name" required>

                <label>Middle Name (Optional)</label>
                <input type="text" name="middle_name" placeholder="Enter your middle name">

                <label>Last Name</label>
                <input type="text" name="last_name" placeholder="Enter your last name" required>

                <label>Mobile Number</label>
                <input type="tel" name="mobile" placeholder="Enter your mobile number" required>

                <label>Barangay</label>
                <select name="barangay" required>
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

                <label>Address</label>
                <input type="text" name="address" placeholder="Enter your address" required>

                <div class="form-buttons">
                    <button type="button" class="cancel-btn" onclick="backToSeedlingsChoice()">Back</button>
                    <button type="submit" class="submit-btn">Submit Application</button>
                </div>
            </form>
        </section>

      <!-- FishR Registration Form -->
        <section class="application-section" id="fishr-form" style="display: none;">
            <div class="form-header">
                <h2>FishR Registration</h2>
                <p>National Program for Municipal Fisherfolk Registration - Register as a municipal fisherfolk.</p>
            </div>

            <div class="form-tabs">
                <button class="tab-btn active" onclick="showTab('fishr-form-tab', event)">Application Form</button>
                <button class="tab-btn" onclick="showTab('fishr-requirements-tab', event)">Requirements</button>
                <button class="tab-btn" onclick="showTab('fishr-info-tab', event)">Information</button>
            </div>

            <div class="tab-content" id="fishr-form-tab" style="display: block;">
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
                    <select id="main-livelihood" name="main_livelihood" required onchange="toggleOtherLivelihood(this)">
                        <option value="">Select livelihood</option>
                        <option value="capture">Capture Fishing</option>
                        <option value="aquaculture">Aquaculture</option>
                        <option value="vending">Fish Vending</option>
                        <option value="processing">Fish Processing</option>
                        <option value="others">Others</option>
                    </select>

                    <div id="other-livelihood-field" style="display: none;">
                        <label>Please specify (if others)</label>
                        <input type="text" placeholder="Specify other livelihood">
                    </div>

                    <label>Supporting Documents</label>
                    <input type="file" id="fishr-docs" required>
                    <small>(Required for all except Capture Fishing)</small>

                    <div class="form-buttons">
                        <button type="button" class="cancel-btn" onclick="closeFormFishR()">Cancel</button>
                        <button type="submit" class="submit-btn">Submit Application</button>
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
            </div>

            <div class="tab-content" id="fishr-info-tab">
                <h3>Important Information</h3>
                <p>Applications are reviewed within 3–5 business days. Contact the City Agriculture Office for inquiries.</p>
            </div>
        </section>

       <!-- BoatR Registration Form -->
        <section class="application-section" id="boatr-form" style="display: none;">
            <div class="form-header">
                <h2>BoatR Registration</h2>
                <p>Boat Registration System - Register your fishing vessel with the City Agriculture Office.</p>
            </div>

            <div class="form-tabs">
                <button class="tab-btn active" onclick="showTab('boatr-form-tab', event)">Application Form</button>
                <button class="tab-btn" onclick="showTab('boatr-requirements-tab', event)">Requirements</button>
                <button class="tab-btn" onclick="showTab('boatr-info-tab', event)">Information</button>
            </div>

            <div class="tab-content" id="boatr-form-tab" style="display: block;">
                <form>
                    <label>First Name</label>
                    <input type="text" placeholder="Enter first name" required>

                    <label>Middle Name</label>
                    <input type="text" placeholder="Enter middle name">

                    <label>Last Name</label>
                    <input type="text" placeholder="Enter last name" required>

                    <label>FishR Number</label>
                    <input type="text" placeholder="Enter FishR Number" required>

                    <label>Vessel Name</label>
                    <input type="text" placeholder="Enter vessel name" required>

                    <label>Boat Type</label>
                    <select required>
                        <option value="">Select boat type</option>
                        <option>Spoon</option>
                        <option>Plumb</option>
                        <option>Banca</option>
                        <option>Rake Stem - Rake Stern</option>
                        <option>Rake Stem - Transom/Spoon/Plumb Stern</option>
                        <option>Skiff (Typical Design)</option>
                    </select>

                    <label>Vessel Dimensions (in feet)</label>
                    <input type="number" step="0.01" placeholder="Length" required>
                    <input type="number" step="0.01" placeholder="Width" required>
                    <input type="number" step="0.01" placeholder="Depth" required>

                    <label>Engine Type</label>
                    <input type="text" placeholder="Enter engine type" required>

                    <label>Engine Horsepower</label>
                    <input type="number" step="1" placeholder="Enter engine horsepower" required>

                    <label>Primary Fishing Gear Used</label>
                    <select required>
                        <option value="">Select primary gear</option>
                        <option>Hook and Line</option>
                        <option>Bottom Set Gill Net</option>
                        <option>Fish Trap</option>
                        <option>Fish Coral</option>
                    </select>

                    <label>Supporting Document</label>
                    <input type="file" disabled>
                    <small>To be uploaded by admin only upon on-site boat inspection.</small>

                    <div class="form-buttons">
                        <button type="button" class="cancel-btn" onclick="closeFormBoatR()">Cancel</button>
                        <button type="submit" class="submit-btn">Submit Application</button>
                    </div>
                </form>
            </div>

            <div class="tab-content" id="boatr-requirements-tab">
                <h3>Required Documents</h3>
                <ul>
                    <li>Valid government-issued ID of boat owner</li>
                    <li>Proof of boat ownership</li>
                    <li>Clear photos of the boat (front, side, back views)</li>
                    <li>Engine details/receipt (for motorized boats)</li>
                    <li>FishR registration certificate</li>
                    <li>On-site inspection approval (City Agriculture Office)</li>
                </ul>
            </div>

            <div class="tab-content" id="boatr-info-tab">
                <h3>Important Information</h3>
                <p>Please review the following before submitting:</p>
                <ul>
                    <li>Only municipal fishing boats will be registered under BoatR.</li>
                    <li>On-site inspection is required before document upload.</li>
                    <li>Ensure accuracy of all information submitted.</li>
                    <li>Contact City Agriculture Office at (123) 456-7890 or email agriculture@sanpedro.gov.ph</li>
                </ul>
            </div>
        </section>


    <section class="how-it-works" id="how-it-works">
        <h2>HOW IT WORKS</h2>
        <div class="steps">
            <div class="step">
                <h3>1. Fill Out the Form</h3>
                <p>Select a service and complete the required online application form with your details.</p>
            </div>
            <div class="step">
                <h3>2. Submit Documents</h3>
                <p>Upload any required supporting documents or provide them to the City Agriculture Office.</p>
            </div>
            <div class="step">
                <h3>3. Receive Approval</h3>
                <p>Once approved, you will be notified and can access the requested agricultural service.</p>
            </div>
        </div>
    </section>

    <section class="help-section">
        <h2>Need Help?</h2>
        <p>If you have any questions or need assistance with your application, please don't hesitate to contact our support team.</p>
        <div class="help-buttons">
            <button class="btn-help">Contact Us</button>
            <button class="btn-help">Visit Office</button>
        </div>
    </section>

    <footer class="footer" id="main-footer">
        <div class="footer-container">
            <div class="footer-column">
                <h3>About AgriSys</h3>
                <p>The Agricultural Service System (AgriSys) is designed to optimize service delivery for the City Agriculture Office of San Pedro, Laguna. We aim to streamline agricultural services and support local farmers.</p>
            </div>
            <div class="footer-column">
                <h3>Our Services</h3>
                <ul>
                    <li><a href="#rsbsa">RSBSA Registration</a></li>
                    <li><a href="#seedlings">Seedlings Request</a></li>
                    <li><a href="#fishr">FishR Registration</a></li>
                    <li><a href="#boatr">BoatR Registration</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h3>Contact Us</h3>
                <p>City Agriculture Office<br>
                San Pedro City Hall<br>
                Laguna, Philippines</p>
                <p>Phone: (123) 456-7890<br>
                Email: <a href="mailto:agriculture@sanpedro.gov.ph">agriculture@sanpedro.gov.ph</a></p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2025 City Agriculture Office of San Pedro. All rights reserved.</p>
        </div>
    </footer>



</body>

</html>
