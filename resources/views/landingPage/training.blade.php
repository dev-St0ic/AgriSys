<!-- Training Request Form -->
<section class="training-application-section" id="training-form" style="display: none;">
    <div class="training-form-header">
        <h2>Training Request</h2>
        <p>Apply for agricultural training programs offered by the City Agriculture Office of San Pedro.</p>
    </div>

    <div class="training-form-tabs">
        <button class="training-tab-btn active" onclick="showTrainingTab('training-form-tab', event)">Application Form</button>
        <button class="training-tab-btn" onclick="showTrainingTab('training-requirements-tab', event)">Requirements</button>
        <button class="training-tab-btn" onclick="showTrainingTab('training-info-tab', event)">Information</button>
    </div>

    <div class="training-tab-content" id="training-form-tab" style="display: block;">
        <form id="training-request-form" enctype="multipart/form-data">
            @csrf  
            
            <div class="training-form-group">
                <label for="training_first_name">First Name *</label>
                <input type="text" id="training_first_name" name="first_name" placeholder="Enter your first name" required>
            </div>

            <div class="training-form-group">
                <label for="training_middle_name">Middle Name (Optional)</label>
                <input type="text" id="training_middle_name" name="middle_name" placeholder="Enter your middle name">
            </div>

            <div class="training-form-group">
                <label for="training_last_name">Last Name *</label>
                <input type="text" id="training_last_name" name="last_name" placeholder="Enter your last name" required>
            </div>

            <div class="training-form-group">
                <label for="training_mobile_number">Mobile Number *</label>
                <input type="tel" id="training_mobile_number" name="mobile_number" placeholder="Enter your mobile number (e.g., 09123456789)" 
                       pattern="[0-9]{11}" required>
            </div>

            <div class="training-form-group">
                <label for="training_email">Email Address *</label>
                <input type="email" id="training_email" name="email" placeholder="Enter your email address" required>
            </div>

            <div class="training-form-group">
                <label for="training_type">Training Program *</label>
                <select id="training_type" name="training_type" required>
                    <option value="" disabled selected>Select Training Program</option>
                    <option value="tilapia_hito">Tilapia and Hito Training</option>
                    <option value="hydroponics">Hydroponics Training</option>
                    <option value="aquaponics">Aquaponics Training</option>
                    <option value="mushrooms">Mushrooms Production Training</option>
                    <option value="livestock_poultry">Livestock and Poultry Training</option>
                    <option value="high_value_crops">High Value Crops Training</option>
                    <option value="sampaguita_propagation">Sampaguita Propagation Training</option>
                </select>
            </div>

            <div class="training-form-group">
                <label for="training_documents">Supporting Documents (PDF, JPG, PNG - Max 5MB each)</label>
                <input type="file" id="training_documents" name="documents[]" multiple accept=".pdf,.jpg,.jpeg,.png">
                <small>Please upload relevant documents such as ID, certificates, or other supporting files.</small>
            </div>

            <div class="training-form-actions">
                <button type="button" class="training-btn-secondary" onclick="goHome(event)">Cancel</button>
                <button type="submit" class="training-btn-primary">Submit Application</button>
            </div>
        </form>
    </div>

    <div class="training-tab-content" id="training-requirements-tab" style="display: none;">
        <div class="training-requirements-content">
            <h3>Required Documents</h3>
            <ul>
                <li>Valid ID (Government issued ID, Driver's License, Passport, etc.)</li>
                <li>Proof of residency in San Pedro City</li>
                <li>Certificate of completion (if applying for advanced training)</li>
                <li>Any relevant agricultural experience certificates (optional)</li>
            </ul>
            
            <h3>Eligibility Requirements</h3>
            <ul>
                <li>Must be a resident of San Pedro City</li>
                <li>Must be at least 18 years old</li>
                <li>Interest in agricultural development</li>
                <li>Commitment to attend the full training program</li>
            </ul>
            
            <h3>Important Notes</h3>
            <ul>
                <li>Training schedules will be announced after application approval</li>
                <li>All training programs are FREE of charge</li>
                <li>Participants will receive certificates upon completion</li>
                <li>Some programs may include starter kits or materials</li>
            </ul>
        </div>
    </div>

    <div class="training-tab-content" id="training-info-tab" style="display: none;">
        <div class="training-info-content">
            <h3>Training Program Details</h3>
            
            <div class="training-program">
                <h4>Tilapia and Hito Training</h4>
                <p>Learn fish farming techniques, pond management, feeding practices, and disease prevention for tilapia and catfish production.</p>
            </div>
            
            <div class="training-program">
                <h4>Hydroponics Training</h4>
                <p>Master soilless cultivation methods, nutrient solution preparation, and hydroponic system setup and maintenance.</p>
            </div>
            
            <div class="training-program">
                <h4>Aquaponics Training</h4>
                <p>Combine fish farming with hydroponic plant cultivation in a sustainable, integrated system.</p>
            </div>
            
            <div class="training-program">
                <h4>Mushrooms Production Training</h4>
                <p>Learn mushroom cultivation techniques, substrate preparation, and post-harvest handling.</p>
            </div>
            
            <div class="training-program">
                <h4>Livestock and Poultry Training</h4>
                <p>Cover animal husbandry, feeding management, breeding techniques, and disease prevention for livestock and poultry.</p>
            </div>
            
            <div class="training-program">
                <h4>High Value Crops Training</h4>
                <p>Focus on profitable crop production including vegetables, herbs, and specialty crops with high market value.</p>
            </div>
            
            <div class="training-program">
                <h4>Sampaguita Propagation Training</h4>
                <p>Learn propagation techniques, care, and cultivation of sampaguita flowers for commercial or personal use.</p>
            </div>
            
            <h3>Training Duration</h3>
            <p>Most training programs run for 3-5 days, depending on the complexity of the subject matter. Advanced programs may extend to 1-2 weeks.</p>
            
            <h3>Contact Information</h3>
            <p>For questions about training programs, contact the City Agriculture Office at:</p>
            <p>Phone: (123) 456-7890<br>
            Email: training@sanpedro.gov.ph</p>
        </div>
    </div>
</section>
