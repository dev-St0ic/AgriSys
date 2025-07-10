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
