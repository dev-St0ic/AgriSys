<!-- Training Request Form -->
<section class="training-application-section" id="training-form" style="display: none;">
    <div class="training-form-header">
        <h2>Training Request</h2>
        <p>Apply for agricultural training programs offered by the City Agriculture Office of San Pedro.</p>
    </div>

    <!-- Message container for success/error messages -->
    <div id="training-messages" class="training-messages" style="display: none;">
        <div id="training-message-content" class="training-message-content"></div>
    </div>

    <div class="training-form-tabs">
        <button class="training-tab-btn active" onclick="showTrainingTab('training-form-tab', event)">Application
            Form</button>
        <button class="training-tab-btn"
            onclick="showTrainingTab('training-requirements-tab', event)">Requirements</button>
        <button class="training-tab-btn" onclick="showTrainingTab('training-info-tab', event)">Information</button>
    </div>

    <div class="training-tab-content" id="training-form-tab" style="display: block;">
        <!-- Success/Error Messages -->
        @if (session('success'))
            <div class="alert alert-success"
                style="padding: 15px; margin-bottom: 20px; background-color: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; color: #155724;">
                <strong>✓ Success!</strong> {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger"
                style="padding: 15px; margin-bottom: 20px; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; color: #721c24;">
                <strong>✗ Error!</strong> {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger"
                style="padding: 15px; margin-bottom: 20px; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; color: #721c24;">
                <strong>✗ Please fix the following errors:</strong>
                <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="training-request-form" action="{{ route('apply.training') }}" method="POST"
            enctype="multipart/form-data">
            @csrf

            <div class="training-form-group">
                <label for="training_first_name">First Name <span class="required-asterisk">*</span></label>
                <input type="text" id="training_first_name" name="first_name" placeholder="From your profile"
                    pattern="[a-zA-Z\s'\-]+" title="First name from your registered profile" readonly required
                    style="background-color: #f5f5f5; cursor: not-allowed;">
                <small style="color: #666; font-size: 0.875rem;">Auto-filled from your profile</small>
            </div>

            <div class="training-form-group">
                <label for="training_middle_name">Middle Name (Optional)</label>
                <input type="text" id="training_middle_name" name="middle_name" placeholder="From your profile"
                    pattern="[a-zA-Z\s'\-]+" title="Middle name from your registered profile" readonly
                    style="background-color: #f5f5f5; cursor: not-allowed;">
            </div>

            <div class="training-form-group">
                <label for="training_last_name">Last Name <span class="required-asterisk">*</span></label>
                <input type="text" id="training_last_name" name="last_name" placeholder="From your profile"
                    pattern="[a-zA-Z\s'\-]+" title="Last name from your registered profile" readonly required
                    style="background-color: #f5f5f5; cursor: not-allowed;">
                <small style="color: #666; font-size: 0.875rem;">Auto-filled from your profile</small>
            </div>

            <div class="training-form-group">
                <label for="training_name_extension">Name Extension (Optional)</label>
                <select id="training_name_extension" name="name_extension" disabled
                    style="background-color: #f5f5f5; cursor: not-allowed;">
                    <option value="" selected>Select Extension</option>
                    <option value="Jr.">Jr.</option>
                    <option value="Sr.">Sr.</option>
                    <option value="II">II</option>
                    <option value="III">III</option>
                    <option value="IV">IV</option>
                    <option value="V">V</option>
                </select>
            </div>

            <div class="training-form-group">
                <label for="training_contact_number">Contact Number <span class="required-asterisk">*</span></label>
                <input type="tel" id="training_contact_number" name="contact_number" placeholder="From your profile"
                    pattern="^09\d{9}$" title="Contact number from your registered profile" readonly required
                    style="background-color: #f5f5f5; cursor: not-allowed;">
                <small style="color: #666; font-size: 0.875rem;">Auto-filled from your profile</small>
            </div>

            <div class="training-form-group">
                <label for="training_barangay">Barangay <span class="required-asterisk">*</span></label>
                <select id="training_barangay" name="barangay" required>
                    <option value="Bagong Silang">Bagong Silang</option>
                    <option value="Calendola">Calendola</option>
                    <option value="Chrysanthemum">Chrysanthemum</option>
                    <option value="Cuyab">Cuyab</option>
                    <option value="Estrella">Estrella</option>
                    <option value="Fatima">Fatima</option>
                    <option value="G.S.I.S.">G.S.I.S.</option>
                    <option value="Landayan">Landayan</option>
                    <option value="Langgam">Langgam</option>
                    <option value="Laram">Laram</option>
                    <option value="Magsaysay">Magsaysay</option>
                    <option value="Maharlika">Maharlika</option>
                    <option value="Narra">Narra</option>
                    <option value="Nueva">Nueva</option>
                    <option value="Pacita 1">Pacita 1</option>
                    <option value="Pacita 2">Pacita 2</option>
                    <option value="Poblacion">Poblacion</option>
                    <option value="Riverside">Riverside</option>
                    <option value="Rosario">Rosario</option>
                    <option value="Sampaguita Village">Sampaguita Village</option>
                    <option value="San Antonio">San Antonio</option>
                    <option value="San Lorenzo Ruiz">San Lorenzo Ruiz</option>
                    <option value="San Roque">San Roque</option>
                    <option value="San Vicente">San Vicente</option>
                    <option value="Santo Niño">Santo Niño</option>
                    <option value="United Bayanihan">United Bayanihan</option>
                    <option value="United Better Living">United Better Living</option>
                </select>
            </div>

            <div class="training-form-group">
                <label for="training_type">Training Program <span class="required-asterisk">*</span></label>
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
                <label for="training_document">Supporting Document (Optional)</label>
                <input type="file" id="training_document" name="supporting_document"
                    accept=".pdf,.jpg,.jpeg,.png">
                <small>Upload Government ID, Driver's License, or Barangay Certificate (PDF, JPG, PNG - Max
                    10MB)</small>
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
            <!-- DSS Report Information -->
            @if (isset($trainingReport) && $trainingReport['exists'])
                <div
                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px; border-radius: 8px; margin-bottom: 25px; color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <h4
                        style="margin: 0 0 15px 0; color: white; font-size: 1.1rem; display: flex; align-items: center;">
                        <svg style="width: 24px; height: 24px; margin-right: 10px;" fill="currentColor"
                            viewBox="0 0 20 20">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                            <path fill-rule="evenodd"
                                d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"
                                clip-rule="evenodd"></path>
                        </svg>
                        Latest DSS Analytics Report
                    </h4>
                    <div
                        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                        <div
                            style="background: rgba(255,255,255,0.1); padding: 12px; border-radius: 6px; backdrop-filter: blur(10px);">
                            <div style="font-size: 0.85rem; opacity: 0.9; margin-bottom: 5px;">📅 Report Generated
                            </div>
                            <div style="font-weight: 600; font-size: 0.95rem;">
                                {{ \Carbon\Carbon::parse($trainingReport['generated_at'])->format('M d, Y H:i:s') }}
                            </div>
                        </div>
                        <div
                            style="background: rgba(255,255,255,0.1); padding: 12px; border-radius: 6px; backdrop-filter: blur(10px);">
                            <div style="font-size: 0.85rem; opacity: 0.9; margin-bottom: 5px;">🤖 Analysis Source</div>
                            <div style="font-weight: 600; font-size: 0.95rem;">{{ ucfirst($trainingReport['source']) }}
                            </div>
                        </div>
                        <div
                            style="background: rgba(255,255,255,0.1); padding: 12px; border-radius: 6px; backdrop-filter: blur(10px);">
                            <div style="font-size: 0.85rem; opacity: 0.9; margin-bottom: 5px;">📊 Data Period</div>
                            <div style="font-weight: 600; font-size: 0.95rem;">{{ $trainingReport['period_label'] }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <h3>Training Program Details</h3>

            <div class="training-program">
                <h4>Tilapia and Hito Training</h4>
                <p>Learn fish farming techniques, pond management, feeding practices, and disease prevention for tilapia
                    and catfish production.</p>
            </div>

            <div class="training-program">
                <h4>Hydroponics Training</h4>
                <p>Master soilless cultivation methods, nutrient solution preparation, and hydroponic system setup and
                    maintenance.</p>
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
                <p>Cover animal husbandry, feeding management, breeding techniques, and disease prevention for livestock
                    and poultry.</p>
            </div>

            <div class="training-program">
                <h4>High Value Crops Training</h4>
                <p>Focus on profitable crop production including vegetables, herbs, and specialty crops with high market
                    value.</p>
            </div>

            <div class="training-program">
                <h4>Sampaguita Propagation Training</h4>
                <p>Learn propagation techniques, care, and cultivation of sampaguita flowers for commercial or personal
                    use.</p>
            </div>

            <h3>Training Duration</h3>
            <p>Most training programs run for 1-3 days, depending on the complexity of the subject matter.</p>
        </div>
    </div>
</section>

<script>
    // Real-time validation for name fields
    document.addEventListener('DOMContentLoaded', function() {
        const nameFields = [{
                id: 'training_first_name',
                pattern: /^[a-zA-Z\s\'-]*$/
            },
            {
                id: 'training_middle_name',
                pattern: /^[a-zA-Z\s\'-]*$/
            },
            {
                id: 'training_last_name',
                pattern: /^[a-zA-Z\s\'-]*$/
            },
            {
                id: 'training_name_extension',
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
