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

            <label>Middle Name (Optional)</label>
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
