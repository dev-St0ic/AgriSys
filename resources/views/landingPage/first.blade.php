<!-- Seedlings Choice Section -->
<section class="application-section" id="seedlings-choice" style="display: none;">
    <div class="form-header">
        <h2>Seedlings Request</h2>
        <p>Select the seedlings and/or fertilizer you want to request, then click Next.</p>
    </div>
    <form id="seedlings-choice-form">
        <div style="display: flex; gap: 30px; justify-content: center; flex-wrap: wrap;">
            <!-- Vegetable Column -->
            <div style="flex:1; min-width:200px;">
                <strong>Vegetable Seedlings</strong><br>
                <label class="seedling-option"><input type="checkbox" name="vegetables" value="sampaguita">
                    Sampaguita</label>
                <label class="seedling-option"><input type="checkbox" name="vegetables" value="siling haba"> Siling
                    Haba</label>
                <label class="seedling-option"><input type="checkbox" name="vegetables" value="siling labuyo"> Siling
                    Labuyo</label>
                <label class="seedling-option"><input type="checkbox" name="vegetables" value="eggplant">
                    Eggplant</label>
                <label class="seedling-option"><input type="checkbox" name="vegetables" value="kamatis"> Kamatis</label>
                <label class="seedling-option"><input type="checkbox" name="vegetables" value="okra"> Okra</label>
                <label class="seedling-option"><input type="checkbox" name="vegetables" value="kalabasa">
                    Kalabasa</label>
                <label class="seedling-option"><input type="checkbox" name="vegetables" value="upo"> Upo</label>
                <label class="seedling-option"><input type="checkbox" name="vegetables" value="pipino"> Pipino</label>
            </div>
            <!-- Fruit Column -->
            <div style="flex:1; min-width:200px;">
                <strong>Fruit-bearing Seedlings</strong><br>
                <label class="seedling-option"><input type="checkbox" name="fruits" value="kalamansi">
                    Kalamansi</label>
                <label class="seedling-option"><input type="checkbox" name="fruits" value="guyabano"> Guyabano</label>
                <label class="seedling-option"><input type="checkbox" name="fruits" value="lanzones"> Lanzones</label>
                <label class="seedling-option"><input type="checkbox" name="fruits" value="mangga"> Mangga</label>
            </div>
            <!-- Fertilizer Column -->
            <div style="flex:1; min-width:200px;">
                <strong>Organic Fertilizer</strong><br>
                <label class="seedling-option"><input type="radio" name="fertilizer" value="chicken manure">
                    Pre-processed Chicken Manure</label>
                <label class="seedling-option"><input type="radio" name="fertilizer" value="humic acid"> Humic
                    Acid</label>
                <label class="seedling-option"><input type="radio" name="fertilizer" value="vermicast">
                    Vermicast</label>
            </div>
        </div>
        <div style="text-align:center; margin-top:30px;">
            <button type="button" class="btn" onclick="proceedToSeedlingsForm()">Next</button>
        </div>
    </form>
</section>

<!-- Seedlings Request Form -->
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
