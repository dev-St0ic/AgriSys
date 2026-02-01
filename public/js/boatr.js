// ==============================================
// COMPLETE WORKING BOAT REGISTRATION MODULE
// Single File Upload Version - WITH STYLES AND FUNCTIONS
// ==============================================

console.log('Loading BoatR module...');

// ==============================================
// CSRF TOKEN MANAGEMENT
// ==============================================

/**
 * Get CSRF token from meta tag
 */
function getCSRFToken() {
    const token = document.querySelector('meta[name="csrf-token"]');
    if (token) {
        return token.getAttribute('content');
    }

    console.error('CSRF token not found in meta tag!');
    return null;
}

/**
 * Ensure CSRF token is present
 */
function ensureCSRFToken() {
    const token = getCSRFToken();
    if (!token) {
        console.error('CSRF token is missing! Please refresh the page.');
        agrisysModal.error('Security token is missing. Please refresh the page and try again.', { title: 'Security Error' });
        return false;
    }
    return token;
}

// ==============================================
// MAIN NAVIGATION FUNCTIONS
// ==============================================

/**
 * Opens the Boat Registration form
 */
function openFormBoatR(event) {
    if (event) event.preventDefault();

    // Check authentication before allowing access
    if (!showAuthRequired('BoatR Registration')) {
        return false;
    }

    // Hide other sections (implement these functions as needed)
    if (typeof hideAllMainSections === 'function') hideAllMainSections();
    if (typeof hideAllForms === 'function') hideAllForms();

    const formElement = document.getElementById('boatr-form');
    if (formElement) {
        formElement.style.display = 'block';

        // Activate tab if function exists
        if (typeof activateApplicationTab === 'function') {
            activateApplicationTab('boatr-form');
        }

        // Initialize form after showing
        initializeBoatRForm();
    } else {
        console.error('Boat Registration form not found');
        return;
    }

    // Scroll to top with proper timing and multiple fallbacks
    setTimeout(() => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
        document.documentElement.scrollTop = 0;
        document.body.scrollTop = 0;
    }, 50);

    // Update URL without page reload
    if (window.history && window.history.pushState) {
        history.pushState(null, '', '/services/boatr');
    }
}

/**
 * Closes Boat Registration form and returns to main services
 */
function closeFormBoatR() {
    const formElement = document.getElementById('boatr-form');
    if (formElement) formElement.style.display = 'none';

    // Show main sections if function exists
    if (typeof showAllMainSections === 'function') showAllMainSections();

    // Update URL
    if (window.history && window.history.pushState) {
        history.pushState(null, '', '/services');
    }
}

// ==============================================
// BOAT TYPE MANAGEMENT
// ==============================================

/**
 * Handles boat type selection changes
 */
function handleBoatTypeChange(select) {
    if (!select) {
        console.error('Select element not provided');
        return;
    }

    const boatType = select.value;
    console.log("Selected Boat Type:", boatType);

    // Update required documents based on boat type
    updateRequiredDocuments(boatType);
}

/**
 * Updates required documents list based on boat type
 */
function updateRequiredDocuments(boatType) {
    const docsList = document.getElementById('required-docs-list');
    if (!docsList) return;

    let docsHTML = '<h4>Required Documents for ' + boatType + ':</h4><ul>';

    // Base documents for all boat types
    docsHTML += '<li>Valid Government-issued ID</li>';
    docsHTML += '<li>Proof of Boat Ownership (Receipt/Invoice)</li>';
    docsHTML += '<li>FishR Registration Certificate</li>';
    docsHTML += '<li>Engine Specifications and Receipt</li>';
    docsHTML += '<li>Boat Photos (Front, Side, Back views)</li>';

    // Add specific requirements based on boat type
    switch (boatType) {
        case 'Banca':
            docsHTML += '<li>Outrigger Safety Certificate</li>';
            break;
        case 'Rake Stem - Rake Stern':
        case 'Rake Stem - Transom/Spoon/Plumb Stern':
            docsHTML += '<li>Hull Construction Details</li>';
            break;
        case 'Skiff (Typical Design)':
            docsHTML += '<li>Traditional Design Verification</li>';
            break;
    }

    docsHTML += '</ul>';
    docsList.innerHTML = docsHTML;
}

// ==============================================
// FILE HANDLING FUNCTIONS
// ==============================================
// FILE HANDLING FUNCTIONS
// ==============================================

/**
 * Preview single file function
 */
function previewSingleFile(input) {
    const preview = document.getElementById('single-file-preview');

    if (!preview) {
        // Create preview container if it doesn't exist
        const previewContainer = document.createElement('div');
        previewContainer.id = 'single-file-preview';
        previewContainer.style.display = 'none';
        input.parentNode.appendChild(previewContainer);
    }

    const previewElement = document.getElementById('single-file-preview');

    if (input.files && input.files[0]) {
        const file = input.files[0];
        const fileSize = file.size / 1024 / 1024; // Convert to MB

        // Check file size (max 10MB)
        if (fileSize > 10) {
            agrisysModal.warning('File size must be less than 10MB', { title: 'File Too Large' });
            input.value = '';
            previewElement.style.display = 'none';
            return;
        }

        // Show preview
        previewElement.innerHTML = `
            <div class="file-preview-content">
                <span>📄 ${file.name} (${fileSize.toFixed(2)} MB)</span>
                <button type="button" class="remove-file-btn" onclick="removeSingleFile()">Remove</button>
            </div>
        `;
        previewElement.className = 'file-preview';
        previewElement.style.display = 'block';
    } else {
        previewElement.style.display = 'none';
    }
}

/**
 * Remove single file function
 */
function removeSingleFile() {
    const input = document.getElementById('boatr_supporting_documents');
    const preview = document.getElementById('single-file-preview');

    input.value = '';
    preview.style.display = 'none';
}

// ==============================================
// TAB FUNCTIONS
// ==============================================

/**
 * Show tab function
 */
function showTab(tabId, event) {
    // Hide all tab contents
    const tabContents = document.querySelectorAll('.boatr-tab-content');
    tabContents.forEach(content => {
        content.style.display = 'none';
    });

    // Remove active class from all tabs
    const tabButtons = document.querySelectorAll('.boatr-tab-btn');
    tabButtons.forEach(btn => {
        btn.classList.remove('active');
    });

    // Show selected tab and mark button as active
    if (event) {
        document.getElementById(tabId).style.display = 'block';
        event.target.classList.add('active');
    }
}


/**
 * Show all main page sections
 */
function showAllMainSections() {
    const sections = [
        'home',
        'events',
        'services',
        'how-it-works',
        '.help-section'
    ];

    sections.forEach(selector => {
        const element = selector.startsWith('.')
            ? document.querySelector(selector)
            : document.getElementById(selector);
        if (element) element.style.display = 'block';
    });
}

// ==============================================
// FORM VALIDATION
// ==============================================

/**
 * Initialize FishR number validation with dynamic feedback
 */
function initializeFishRValidation() {
    const fishRInput = document.querySelector('#boatr_fishr_number');
    if (!fishRInput) return;

    // Remove any existing event listeners
    fishRInput.removeEventListener('input', handleFishRInput);
    fishRInput.removeEventListener('blur', validateFishRNumber);
    fishRInput.removeEventListener('focus', handleFishRFocus);

    // Add dynamic validation events
    fishRInput.addEventListener('input', handleFishRInput);
    fishRInput.addEventListener('blur', validateFishRNumber);
    fishRInput.addEventListener('focus', handleFishRFocus);

    console.log('FishR validation initialized');
}

/**
 * Handle FishR input changes (real-time feedback)
 */
function handleFishRInput(event) {
    const fishRInput = event.target;
    const number = fishRInput.value.trim();

    // Clear previous validation state
    clearValidationMessage(fishRInput);

    if (!number) {
        fishRInput.style.borderColor = '';
        fishRInput.style.backgroundColor = '';
        return;
    }

    // Check format in real-time
    const formatValid = /^FISHR-[A-Z0-9]{8}$/i.test(number);

    if (number.length < 6) {
        // Too short - neutral state
        fishRInput.style.borderColor = '#6c757d';
        fishRInput.style.backgroundColor = '';
        showValidationMessage(fishRInput, 'Enter your FishR registration number', 'info');
    } else if (!formatValid && number.length >= 6) {
        // Invalid format
        fishRInput.style.borderColor = '#dc3545';
        fishRInput.style.backgroundColor = '#fff8f8';
        showValidationMessage(fishRInput, 'Format should be FISHR-XXXXXXXX', 'error');
    } else if (formatValid) {
        // Valid format - show pending validation
        fishRInput.style.borderColor = '#ffc107';
        fishRInput.style.backgroundColor = '#fffbf0';
        showValidationMessage(fishRInput, 'Checking FishR registration...', 'warning');

        // Debounced validation
        clearTimeout(fishRInput.validationTimeout);
        fishRInput.validationTimeout = setTimeout(() => {
            validateFishRNumberSilent(fishRInput);
        }, 800);
    }
}

/**
 * Handle FishR input focus
 */
function handleFishRFocus(event) {
    const fishRInput = event.target;
    if (!fishRInput.value.trim()) {
        showValidationMessage(fishRInput, 'Enter your approved FishR registration number (FISHR-XXXXXXXX)', 'info');
    }
}

/**
 * Validate FishR number on blur
 */
async function validateFishRNumber(event) {
    const fishRInput = event.target;
    const number = fishRInput.value.trim();

    if (!number) {
        clearValidationMessage(fishRInput);
        fishRInput.style.borderColor = '';
        fishRInput.style.backgroundColor = '';
        return;
    }

    await validateFishRNumberSilent(fishRInput);
}

/**
 * Silent FishR validation
 */
async function validateFishRNumberSilent(fishRInput) {
    const number = fishRInput.value.trim();

    if (!number) return;

    try {
        // Show loading state
        fishRInput.style.borderColor = '#ffc107';
        fishRInput.style.backgroundColor = '#fffbf0';
        showValidationMessage(fishRInput, 'Validating FishR registration...', 'warning');

        const response = await fetch(`/api/validate-fishr/${encodeURIComponent(number)}`);
        const data = await response.json();

        if (data.valid) {
            // Valid FishR number
            fishRInput.style.borderColor = '#28a745';
            fishRInput.style.backgroundColor = '#f8fff8';
            showValidationMessage(fishRInput, 'Valid FishR registration number', 'success');
            fishRInput.dataset.validated = 'true';
        } else {
            // Invalid FishR number
            fishRInput.style.borderColor = '#dc3545';
            fishRInput.style.backgroundColor = '#fff8f8';
            showValidationMessage(fishRInput, 'Invalid or non-approved FishR number. Please ensure you have an approved FishR registration.', 'error');
            fishRInput.dataset.validated = 'false';
        }
    } catch (error) {
        console.error('Error validating FishR number:', error);
        fishRInput.style.borderColor = '#ffc107';
        fishRInput.style.backgroundColor = '#fffbf0';
        showValidationMessage(fishRInput, 'Unable to verify FishR number. Please check your connection.', 'warning');
        fishRInput.dataset.validated = 'error';
    }
}

/**
 * Show validation message
 */
function showValidationMessage(input, message, type) {
    clearValidationMessage(input);

    const messageDiv = document.createElement('div');
    messageDiv.className = `validation-message ${type}`;
    messageDiv.innerHTML = message;

    input.parentNode.insertBefore(messageDiv, input.nextSibling);

    if (type === 'success' || type === 'info') {
        setTimeout(() => {
            if (messageDiv.parentNode) {
                messageDiv.remove();
            }
        }, 5000);
    }
}

/**
 * Clear validation message
 */
function clearValidationMessage(input) {
    const existingMessage = input.parentNode.querySelector('.validation-message');
    if (existingMessage) {
        existingMessage.remove();
    }
}

// ==============================================
// FORM SUBMISSION
// ==============================================

/**
 * Handles Boat Registration form submission
 * UPDATED: Checks FishR validation before submission
 */
function submitBoatRForm(event) {
    event.preventDefault();

    console.log('=== BoatR Form Submission Started ===');

    if (!isUserAuthenticatedAndVerified()) {
        showAuthRequired('BoatR Registration');
        return false;
    }

    const form = document.getElementById('boatr-registration-form');
    if (!form) {
        console.error('Boat Registration form not found');
        agrisysModal.error('Form not found. Please refresh the page and try again.', { title: 'Form Error' });
        return false;
    }

    // CRITICAL CHECK: Validate FishR before anything else
    const fishRInput = form.querySelector('#boatr_fishr_number');
    if (!fishRInput || fishRInput.dataset.validated !== 'true') {
        agrisysModal.error('Cannot submit: Your FishR registration number must be validated first. Please verify your FishR number and wait for validation to complete.', { title: 'FishR Validation Required' });
        if (fishRInput) fishRInput.focus();
        return false;
    }

    const csrfToken = getCSRFToken();
    if (!csrfToken) {
        agrisysModal.error('Security token is missing. Please refresh the page and try again.', { title: 'Security Error' });
        location.reload();
        return false;
    }

    if (!validateBoatRForm(form)) {
        return false;
    }

    const formData = new FormData(form);
    formData.set('_token', csrfToken);

    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = 'Submitting...';

    console.log('Sending request to /submit-boatr...');

    fetch('/submit-boatr', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);

        if (!response.ok) {
            if (response.status === 419) {
                agrisysModal.error('Security token expired. The page will refresh automatically.', { title: 'Session Expired' });
                setTimeout(() => location.reload(), 2000);
                throw new Error('CSRF token expired (419)');
            } else if (response.status === 422) {
                return response.json().then(data => {
                    throw new Error('Validation Error: ' + JSON.stringify(data.errors || data.message));
                });
            } else if (response.status === 500) {
                return response.text().then(text => {
                    console.error('Server error response:', text);
                    throw new Error('Server error (500). Please try again later.');
                });
            } else {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
        }
        return response.json();
    })
    .then(data => {
        console.log('Success response:', data);

        if (data.success) {
            let message = data.message;
            if (data.data?.has_document) {
                message += ' Document uploaded successfully.';
            }
            agrisysModal.success(message, {
                title: 'Registration Submitted!',
                reference: data.application_number || data.data?.application_number || null,
                onClose: () => {
                    resetBoatRForm();
                    closeFormBoatR();
                    // Scroll to top after modal closes and form is hidden
                    setTimeout(() => {
                        document.documentElement.scrollTop = 0;
                        document.body.scrollTop = 0;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }, 500);
                }
            });
        } else {
            console.error('Submission error:', data);

            if (data.errors) {
                const errorList = Object.keys(data.errors).map(field => data.errors[field].join(', '));
                agrisysModal.validationError(errorList, { title: 'Please Correct the Following' });
            } else {
                agrisysModal.error(data.message || 'Error submitting form. Please try again.', { title: 'Submission Failed' });
            }
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);

        if (error.message.includes('419') || error.message.includes('CSRF')) {
            agrisysModal.error('Security token expired. The page will refresh automatically.', { title: 'Session Expired' });
            setTimeout(() => location.reload(), 2000);
        } else if (error.message.includes('Failed to fetch')) {
            agrisysModal.error('Network connection error. Please check your internet connection and try again.', { title: 'Connection Error' });
        } else if (error.message.includes('Validation Error')) {
            agrisysModal.error(error.message, { title: 'Validation Error' });
        } else {
            agrisysModal.error('An error occurred while submitting your application. Please try again.', { title: 'Submission Error' });
        }
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
        console.log('=== BoatR Form Submission Completed ===');
    });

    return false;
}

/**
 * Validates Boat Registration form data
 * UPDATED: Properly validates motorized vs non-motorized boats
 */
function validateBoatRForm(form) {
    const formData = new FormData(form);
    
    // Base required fields
    const requiredFields = [
        'first_name',
        'last_name',
        'fishr_number',
        'vessel_name',
        'boat_type',
        'boat_classification',
        'boat_length',
        'boat_width',
        'boat_depth',
        'primary_fishing_gear'
    ];

    // Check for missing required fields
    const missingFields = requiredFields.filter(field => {
        const value = formData.get(field);
        return !value || value.toString().trim() === '';
    });

    if (missingFields.length > 0) {
        const fieldNames = missingFields.map(field => {
            return field.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        });
        agrisysModal.warning(`Please fill in the following required fields: ${fieldNames.join(', ')}`, { title: 'Missing Information' });
        return false;
    }

    // ========== VALIDATE CONTACT NUMBER ==========
    const contactNumber = formData.get('contact_number');
    if (!contactNumber) {
        agrisysModal.warning('Contact number is required', { title: 'Missing Information' });
        document.getElementById('boatr_contact_number').focus();
        return false;
    }

    const cleaned = contactNumber.replace(/[\s\-]/g, '');
    const contactPatterns = [
        /^09\d{9}$/
    ];

    const isValidContact = contactPatterns.some(pattern => pattern.test(cleaned));
    if (!isValidContact) {
        agrisysModal.warning('Please enter a valid contact number (09XXXXXXXXX)', { title: 'Invalid Contact Number' });
        document.getElementById('boatr_contact_number').focus();
        return false;
    }

    // ========== VALIDATE VESSEL NAME ==========
    const vesselName = formData.get('vessel_name');
    const vesselNamePattern = /^[a-zA-Z0-9\s\-']*$/;
    
    if (!vesselNamePattern.test(vesselName)) {
        agrisysModal.warning('Vessel name contains invalid special characters. Only use letters, numbers, spaces, hyphens, and apostrophes.', { title: 'Invalid Vessel Name' });
        document.getElementById('boatr_vessel_name').focus();
        return false;
    }

    // Validate boat classification and engine requirements
    const boatClassification = formData.get('boat_classification');
    console.log('🚤 Boat Classification:', boatClassification);

    if (boatClassification === 'Motorized') {
        console.log('✅ Motorized boat detected - engine fields are REQUIRED');
        
        const engineType = formData.get('engine_type');
        const engineHP = formData.get('engine_horsepower');

        if (!engineType || engineType.trim() === '') {
            agrisysModal.warning('Please enter the engine type for motorized boats', { title: 'Missing Engine Information' });
            document.getElementById('boatr_engine_type').focus();
            return false;
        }

        if (!engineHP || engineHP.trim() === '') {
            agrisysModal.warning('Please enter the engine horsepower for motorized boats', { title: 'Missing Engine Information' });
            document.getElementById('boatr_engine_horsepower').focus();
            return false;
        }

        const hp = parseInt(engineHP);
        if (isNaN(hp) || hp <= 0 || hp > 500) {
            agrisysModal.warning('Please enter valid engine horsepower (1-500 HP)', { title: 'Invalid Value' });
            document.getElementById('boatr_engine_horsepower').focus();
            return false;
        }
    } else if (boatClassification === 'Non-motorized') {
        console.log('✅ Non-motorized boat detected - engine fields are NOT required');
        // Engine fields should be empty and that's okay
    }

    // Validate FishR number format
    const fishRNumber = formData.get('fishr_number');
    if (!fishRNumber.match(/^FISHR-[A-Z0-9]{8}$/i)) {
        agrisysModal.warning('Please enter a valid FishR registration number (format: FISHR-XXXXXXXX)', { title: 'Invalid Format' });
        const fishRInput = form.querySelector('#boatr_fishr_number');
        if (fishRInput) fishRInput.focus();
        return false;
    }

    // CRITICAL: Check if FishR was validated
    const fishRInput = form.querySelector('#boatr_fishr_number');
    if (!fishRInput || fishRInput.dataset.validated !== 'true') {
        agrisysModal.error('Your FishR registration number has not been validated or is invalid.', { title: 'FishR Validation Required' });
        if (fishRInput) fishRInput.focus();
        return false;
    }

    // Validate boat dimensions
    const length = parseFloat(formData.get('boat_length'));
    const width = parseFloat(formData.get('boat_width'));
    const depth = parseFloat(formData.get('boat_depth'));

    if (isNaN(length) || length <= 0 || length > 200) {
        agrisysModal.warning('Please enter a valid boat length (1-200 feet)', { title: 'Invalid Measurement' });
        return false;
    }

    if (isNaN(width) || width <= 0 || width > 50) {
        agrisysModal.warning('Please enter a valid boat width (1-50 feet)', { title: 'Invalid Measurement' });
        return false;
    }

    if (isNaN(depth) || depth <= 0 || depth > 30) {
        agrisysModal.warning('Please enter a valid boat depth (1-30 feet)', { title: 'Invalid Measurement' });
        return false;
    }

    // Validate primary fishing gear
    const fishingGear = formData.get('primary_fishing_gear');
    console.log('🎣 Primary Fishing Gear:', fishingGear);
    
    if (!fishingGear || fishingGear.trim() === '') {
        agrisysModal.warning('Please select a primary fishing gear', { title: 'Missing Information' });
        document.getElementById('boatr_primary_fishing_gear').focus();
        return false;
    }

    return true;
}
/**
 * Validates Boat Registration form data
 * UPDATED: Requires FishR validation, prevents submission without verified FishR
 */
function validateBoatRFormUpdated(form) {
    const formData = new FormData(form);
    
    // Base required fields
    const requiredFields = [
        'first_name',
        'last_name',
        'fishr_number',
        'vessel_name',
        'boat_type',
        'boat_classification',
        'boat_length',
        'boat_width',
        'boat_depth',
        'primary_fishing_gear'
    ];

    // Check for missing required fields
    const missingFields = requiredFields.filter(field => {
        const value = formData.get(field);
        return !value || value.toString().trim() === '';
    });

    if (missingFields.length > 0) {
        const fieldNames = missingFields.map(field => {
            return field.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        });
        agrisysModal.warning(`Please fill in the following required fields: ${fieldNames.join(', ')}`, { title: 'Missing Information' });
        return false;
    }

    // ========== NEW: VALIDATE CONTACT NUMBER ==========
    const contactNumber = formData.get('contact_number');
    if (!contactNumber) {
        agrisysModal.warning('Contact number is required', { title: 'Missing Information' });
        document.getElementById('boatr_contact_number').focus();
        return false;
    }

    const cleaned = contactNumber.replace(/[\s\-]/g, '');
    const contactPatterns = [
        /^09\d{9}$/
    ];

    const isValidContact = contactPatterns.some(pattern => pattern.test(cleaned));
    if (!isValidContact) {
        agrisysModal.warning('Please enter a valid contact number (09XXXXXXXXX)', { title: 'Invalid Contact Number' });
        document.getElementById('boatr_contact_number').focus();
        return false;
    }

    // ========== NEW: VALIDATE VESSEL NAME ==========
    const vesselName = formData.get('vessel_name');
    const vesselNamePattern = /^[a-zA-Z0-9\s\-']*$/;
    
    if (!vesselNamePattern.test(vesselName)) {
        agrisysModal.warning('Vessel name contains invalid special characters. Only use letters, numbers, spaces, hyphens, and apostrophes.', { title: 'Invalid Vessel Name' });
        document.getElementById('boatr_vessel_name').focus();
        return false;
    }

    // Validate boat classification
    const boatClassification = formData.get('boat_classification');
    console.log('🚤 Boat Classification:', boatClassification);

    if (boatClassification === 'Motorized') {
        console.log('✅ Motorized boat detected - engine fields are REQUIRED');
        
        const engineType = formData.get('engine_type');
        const engineHP = formData.get('engine_horsepower');

        if (!engineType || engineType.trim() === '') {
            agrisysModal.warning('Please enter the engine type for motorized boats', { title: 'Missing Engine Information' });
            document.getElementById('boatr_engine_type').focus();
            return false;
        }

        if (!engineHP || engineHP.trim() === '') {
            agrisysModal.warning('Please enter the engine horsepower for motorized boats', { title: 'Missing Engine Information' });
            document.getElementById('boatr_engine_horsepower').focus();
            return false;
        }

        const hp = parseInt(engineHP);
        if (isNaN(hp) || hp <= 0 || hp > 500) {
            agrisysModal.warning('Please enter valid engine horsepower (1-500 HP)', { title: 'Invalid Value' });
            document.getElementById('boatr_engine_horsepower').focus();
            return false;
        }
    }

    // Validate FishR number format
    const fishRNumber = formData.get('fishr_number');
    if (!fishRNumber.match(/^FISHR-[A-Z0-9]{8}$/i)) {
        agrisysModal.warning('Please enter a valid FishR registration number (format: FISHR-XXXXXXXX)', { title: 'Invalid Format' });
        const fishRInput = form.querySelector('#boatr_fishr_number');
        if (fishRInput) fishRInput.focus();
        return false;
    }

    // CRITICAL: Check if FishR was validated
    const fishRInput = form.querySelector('#boatr_fishr_number');
    if (!fishRInput || fishRInput.dataset.validated !== 'true') {
        agrisysModal.error('Your FishR registration number has not been validated or is invalid.', { title: 'FishR Validation Required' });
        if (fishRInput) fishRInput.focus();
        return false;
    }

    // Validate boat dimensions
    const length = parseFloat(formData.get('boat_length'));
    const width = parseFloat(formData.get('boat_width'));
    const depth = parseFloat(formData.get('boat_depth'));

    if (isNaN(length) || length <= 0 || length > 200) {
        agrisysModal.warning('Please enter a valid boat length (1-200 feet)', { title: 'Invalid Measurement' });
        return false;
    }

    if (isNaN(width) || width <= 0 || width > 50) {
        agrisysModal.warning('Please enter a valid boat width (1-50 feet)', { title: 'Invalid Measurement' });
        return false;
    }

    if (isNaN(depth) || depth <= 0 || depth > 30) {
        agrisysModal.warning('Please enter a valid boat depth (1-30 feet)', { title: 'Invalid Measurement' });
        return false;
    }

    return true;
}


// ==============================================
// UPDATE initializeBoatRForm TO INCLUDE NEW VALIDATIONS
// Replace the existing initializeBoatRForm with this
// ==============================================

function initializeBoatRFormUpdated() {
    console.log('Initializing BoatR form with enhanced validation...');

    // Initialize FishR validation
    initializeFishRValidation();

    // Initialize Contact Number validation (NEW)
    initializeBoatRContactValidation();

    // Initialize Vessel Name validation (NEW)
    initializeBoatRVesselNameValidation();

    // Initialize boat type field
    const boatTypeSelect = document.getElementById('boatr_boat_type');
    if (boatTypeSelect && boatTypeSelect.value) {
        handleBoatTypeChange(boatTypeSelect);
    }

    // Initialize boat classification field
    const boatClassificationSelect = document.getElementById('boatr_boat_classification');
    if (boatClassificationSelect && boatClassificationSelect.value) {
        handleBoatClassificationChange(boatClassificationSelect);
    }

    // Ensure CSRF token is available
    ensureCSRFToken();

    console.log('BoatR form initialized successfully with enhanced validation');
}


// ==============================================
// UTILITY FUNCTIONS
// ==============================================

/**
 * Initialize Boat Registration form
 */
/**
 * Initialize Boat Registration form with all validations
 */
function initializeBoatRForm() {
    console.log('Initializing BoatR form with enhanced validation...');

    // Initialize FishR validation
    initializeFishRValidation();

    // Initialize Contact Number validation
    initializeBoatRContactValidation();

    // Initialize Vessel Name validation
    initializeBoatRVesselNameValidation();

    // Initialize boat type field
    const boatTypeSelect = document.getElementById('boatr_boat_type');
    if (boatTypeSelect && boatTypeSelect.value) {
        handleBoatTypeChange(boatTypeSelect);
    }

    // Initialize boat classification field
    const boatClassificationSelect = document.getElementById('boatr_boat_classification');
    if (boatClassificationSelect && boatClassificationSelect.value) {
        handleBoatClassificationChange(boatClassificationSelect);
    }

    // Ensure CSRF token is available
    ensureCSRFToken();

    console.log('BoatR form initialized successfully with enhanced validation');
}

/**
 * Reset Boat Registration form
 */
function resetBoatRForm() {
    const form = document.getElementById('boatr-registration-form');
    if (form) {
        form.reset();

        // Reset boat classification to hide engine fields
        const boatClassificationSelect = document.getElementById('boatr_boat_classification');
        if (boatClassificationSelect) {
            boatClassificationSelect.value = '';
            handleBoatClassificationChange(boatClassificationSelect);
        }

        // Hide engine fields on reset
        const engineFieldsContainer = document.getElementById('boatr_engine_fields_container');
        if (engineFieldsContainer) {
            engineFieldsContainer.style.display = 'none';
            engineFieldsContainer.classList.remove('visible');
        }

        // Clear validation messages
        const validationMessages = form.querySelectorAll('.validation-message');
        validationMessages.forEach(msg => msg.remove());

        // Reset input styles
        const inputs = form.querySelectorAll('input, select');
        inputs.forEach(input => {
            input.style.borderColor = '';
            input.style.backgroundColor = '';
            delete input.dataset.validated;

            if (input.validationTimeout) {
                clearTimeout(input.validationTimeout);
                delete input.validationTimeout;
            }
        });

        // Hide file preview
        const preview = document.getElementById('single-file-preview');
        if (preview) preview.style.display = 'none';

        console.log('Boat Registration form reset');
    }
}


// ==============================================
// CSS STYLES INJECTION
// ==============================================

/**
 * Inject CSS styles for BoatR form
 */
function injectBoatRStyles() {
    // Check if styles already injected
    if (document.querySelector('#boatr-styles')) {
        return;
    }

    const style = document.createElement('style');
    style.id = 'boatr-styles';
    style.textContent = `
        /* BoatR Form Styles */
        .application-section {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .form-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .form-header h2 {
            color: #2c5530;
            margin-bottom: 10px;
        }

        .form-tabs {
            display: flex;
            margin-bottom: 30px;
            border-bottom: 2px solid #e0e0e0;
        }

        .tab-btn {
            flex: 1;
            padding: 12px 20px;
            background: none;
            border: none;
            cursor: pointer;
            font-weight: 500;
            color: #666;
            transition: all 0.3s ease;
        }

        .tab-btn.active {
            color: #2c5530;
            border-bottom: 3px solid #2c5530;
        }

        .tab-btn:hover {
            background: #f5f5f5;
        }

        .tab-content {
            display: none;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-row {
            display: flex;
            gap: 15px;
        }

        .form-row .form-group {
            flex: 1;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s ease;
            box-sizing: border-box;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #2c5530;
            box-shadow: 0 0 0 3px rgba(44, 85, 48, 0.1);
        }

        .form-help {
            display: block;
            margin-top: 5px;
            color: #666;
            font-size: 14px;
        }

        .file-preview {
            margin-top: 10px;
            padding: 15px;
            border: 2px dashed #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .file-preview-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .remove-file-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }

        .remove-file-btn:hover {
            background: #c82333;
        }

        .alert {
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            border-left: 4px solid;
        }

        .alert-info {
            background-color: #d1ecf1;
            border-color: #bee5eb;
            color: #0c5460;
        }

        .alert i {
            margin-right: 8px;
        }

        .form-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }

        .cancel-btn,
        .submit-btn {
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .cancel-btn {
            background: #6c757d;
            color: white;
        }

        .cancel-btn:hover {
            background: #5a6268;
        }

        .submit-btn {
            background: #2c5530;
            color: white;
        }

        .submit-btn:hover {
            background: #1e3a21;
        }

        .submit-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        /* Validation message styles */
        .validation-message {
            display: block;
            margin-top: 5px;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            border-left: 3px solid;
            animation: slideIn 0.3s ease-out;
        }

        .validation-message.success {
            background-color: #d4edda;
            border-color: #28a745;
            color: #155724;
        }

        .validation-message.error {
            background-color: #f8d7da;
            border-color: #dc3545;
            color: #721c24;
        }

        .validation-message.warning {
            background-color: #fff3cd;
            border-color: #ffc107;
            color: #856404;
        }

        .validation-message.info {
            background-color: #d1ecf1;
            border-color: #17a2b8;
            color: #0c5460;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Enhanced input focus states */
        #boatr_fishr_number:focus {
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25);
        }

        .validation-message i {
            margin-right: 5px;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
                gap: 0;
            }

            .form-buttons {
                flex-direction: column;
            }

            .tab-btn {
                padding: 10px 15px;
                font-size: 14px;
            }

            .application-section {
                margin: 10px;
                padding: 15px;
            }
        }

        /* Additional styling for better UX */
        .form-group input[type="file"] {
            padding: 8px;
            border: 2px dashed #ddd;
            background: #f9f9f9;
        }

        .form-group input[type="file"]:hover {
            border-color: #2c5530;
            background: #f0f8f0;
        }

        .required-docs-list {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }

        .required-docs-list h4 {
            color: #2c5530;
            margin-bottom: 10px;
        }

        .required-docs-list ul {
            margin: 0;
            padding-left: 20px;
        }

        .required-docs-list li {
            margin-bottom: 5px;
            color: #555;
        }

        /* Engine fields container styling */
        #boatr_engine_fields_container {
            border-radius: 5px;
            margin-bottom: 20px;
        }

        #boatr_engine_fields_container.visible {
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    `;

    document.head.appendChild(style);
    console.log('BoatR styles injected');
}
/**
 * Handles boat classification (motorization) selection changes
 * UPDATED: Properly manages engine fields visibility and requirements
 */
function handleBoatClassificationChange(select) {
    if (!select) {
        console.error('Select element not provided');
        return;
    }

    const classification = select.value;
    console.log("Selected Boat Classification:", classification);

    const engineFieldsContainer = document.getElementById('boatr_engine_fields_container');
    const engineTypeInput = document.getElementById('boatr_engine_type');
    const engineHorsepowerInput = document.getElementById('boatr_engine_horsepower');

    if (!engineFieldsContainer) {
        console.error('Engine fields container not found');
        return;
    }

    if (classification === 'Motorized') {
        // Show engine fields and make them required
        engineFieldsContainer.style.display = 'block';
        engineFieldsContainer.classList.add('visible');
        engineTypeInput.required = true;
        engineHorsepowerInput.required = true;
        console.log('Engine fields shown and set as required');
    } else if (classification === 'Non-motorized') {
        // Hide engine fields and make them optional
        engineFieldsContainer.style.display = 'none';
        engineFieldsContainer.classList.remove('visible');
        engineTypeInput.required = false;
        engineHorsepowerInput.required = false;
        // Clear the fields
        engineTypeInput.value = '';
        engineHorsepowerInput.value = '';
        console.log('Engine fields hidden and cleared');
    }
}
// ==============================================
// INITIALIZATION
// ==============================================

/**
 * Initialize when DOM is ready
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('BoatR module DOM ready');

    // Inject styles
    injectBoatRStyles();

    // Ensure CSRF token is present
    ensureCSRFToken();

    // Initialize if form is visible
    const boatRForm = document.getElementById('boatr-form');
    if (boatRForm && boatRForm.style.display !== 'none') {
        initializeBoatRForm();
    }
});


/**
 * Initialize Contact Number Real-Time Validation
 */
function initializeBoatRContactValidation() {
    const contactInput = document.getElementById('boatr_contact_number');
    if (!contactInput) return;

    // Real-time validation on input
    contactInput.addEventListener('input', function(e) {
        validateBoatRContact(e.target);
    });

    // Validation on blur
    contactInput.addEventListener('blur', function(e) {
        validateBoatRContactOnBlur(e.target);
    });

    console.log('BoatR contact validation initialized');
}

/**
 * Real-time contact validation with visual feedback
 */
function validateBoatRContact(input) {
    const value = input.value.trim();
    let warningElement = document.getElementById('boatr_contact_number-warning');

    // Clear previous styling
    input.style.borderColor = '';
    input.style.backgroundColor = '';

    // Empty field - no warning
    if (!value) {
        if (warningElement) warningElement.style.display = 'none';
        return;
    }

    // Remove spaces and hyphens for checking
    const cleaned = value.replace(/[\s\-]/g, '');

    // Valid formats:
    // 09XXXXXXXXX (11 digits)
    const patterns = [
        /^09\d{9}$/,        // 09XXXXXXXXX
    ];

    const isValid = patterns.some(pattern => pattern.test(cleaned));

    // Create warning element if not exists
    if (!warningElement) {
        warningElement = document.createElement('span');
        warningElement.id = 'boatr_contact_number-warning';
        warningElement.style.color = '#ff6b6b';
        warningElement.style.fontSize = '0.875rem';
        warningElement.style.display = 'none';
        warningElement.style.marginTop = '4px';
        warningElement.style.display = 'block';
        input.parentNode.appendChild(warningElement);
    }

    if (isValid) {
        // Valid contact number
        input.style.borderColor = '#28a745';
        input.style.backgroundColor = '#f8fff8';
        warningElement.style.color = '#28a745';
        warningElement.style.display = 'block';

        // Auto-hide success message after 2 seconds
        setTimeout(() => {
            if (input.value.trim() === value) {
                warningElement.style.display = 'none';
                input.style.borderColor = '';
                input.style.backgroundColor = '';
            }
        }, 2000);
    } else if (/^09\d{0,8}$/.test(cleaned) || /^\+639\d{0,8}$/.test(cleaned) || /^639\d{0,8}$/.test(cleaned)) {
        // Incomplete but valid format so far
        input.style.borderColor = '#ffc107';
        input.style.backgroundColor = '#fffbf0';
        warningElement.style.color = '#856404';
        warningElement.textContent = 'Continue entering digits...';
        warningElement.style.display = 'block';
    } else {
        // Invalid format
        input.style.borderColor = '#dc3545';
        input.style.backgroundColor = '#fff8f8';
        warningElement.style.color = '#dc3545';
        warningElement.textContent = 'Format: 09XXXXXXXXX';
        warningElement.style.display = 'block';
    }
}

/**
 * Contact validation on blur (when user leaves field)
 */
function validateBoatRContactOnBlur(input) {
    const value = input.value.trim();
    const warningElement = document.getElementById('boatr_contact_number-warning');

    if (!value) {
        input.style.borderColor = '';
        input.style.backgroundColor = '';
        if (warningElement) warningElement.style.display = 'none';
        return;
    }

    const cleaned = value.replace(/[\s\-]/g, '');
    const patterns = [
        /^09\d{9}$/,
    ];

    const isValid = patterns.some(pattern => pattern.test(cleaned));

    if (!isValid) {
        input.style.borderColor = '#dc3545';
        input.style.backgroundColor = '#fff8f8';
        if (warningElement) {
            warningElement.style.display = 'block';
            warningElement.textContent = 'Invalid contact number. Use: 09XXXXXXXXX';
        }
    } else {
        input.style.borderColor = '#28a745';
        input.style.backgroundColor = '#f8fff8';
    }
}
/**
 * Initialize Vessel Name Validation
 */
function initializeBoatRVesselNameValidation() {
    const vesselNameInput = document.getElementById('boatr_vessel_name');
    if (!vesselNameInput) return;

    // Real-time validation on input
    vesselNameInput.addEventListener('input', function(e) {
        validateBoatRVesselName(e.target);
    });

    // Validation on blur
    vesselNameInput.addEventListener('blur', function(e) {
        validateBoatRVesselNameOnBlur(e.target);
    });

    console.log('BoatR vessel name validation initialized');
}
/**
 * Real-time vessel name validation
 * Allowed: Letters, numbers, spaces, hyphens, apostrophes
 * Not allowed: Special characters like @, #, $, %, &, !, etc.
 */
function validateBoatRVesselName(input) {
    const value = input.value;
    let warningElement = document.getElementById('boatr_vessel_name-warning');

    // Clear previous styling
    input.style.borderColor = '';
    input.style.backgroundColor = '';

    // Empty field - no warning
    if (!value) {
        if (warningElement) warningElement.style.display = 'none';
        return;
    }

    // Pattern: Only allow letters (a-z, A-Z), numbers (0-9), spaces, hyphens (-), apostrophes (')
    const validPattern = /^[a-zA-Z0-9\s\-']*$/;
    const hasInvalidChars = !validPattern.test(value);

    // Create warning element if not exists
    if (!warningElement) {
        warningElement = document.createElement('span');
        warningElement.id = 'boatr_vessel_name-warning';
        warningElement.style.color = '#ff6b6b';
        warningElement.style.fontSize = '0.875rem';
        warningElement.style.marginTop = '4px';
        warningElement.style.display = 'none';
        input.parentNode.appendChild(warningElement);
    }

    if (hasInvalidChars) {
        // Invalid - contains special characters
        input.style.borderColor = '#dc3545';
        input.style.backgroundColor = '#fff8f8';
        warningElement.style.color = '#dc3545';
        warningElement.textContent = 'Only letters, numbers, spaces, hyphens (-), and apostrophes (\') are allowed';
        warningElement.style.display = 'block';
    } else if (value.length > 0) {
        // Valid vessel name
        input.style.borderColor = '#28a745';
        input.style.backgroundColor = '#f8fff8';
        warningElement.style.color = '#28a745';
        // warningElement.textContent = '✓ Valid vessel name';
        warningElement.style.display = 'block';

        // Auto-hide success message after 2 seconds
        setTimeout(() => {
            if (input.value === value) {
                warningElement.style.display = 'none';
                input.style.borderColor = '';
                input.style.backgroundColor = '';
            }
        }, 2000);
    }
}

/**
 * Vessel name validation on blur
 */
function validateBoatRVesselNameOnBlur(input) {
    const value = input.value;
    const warningElement = document.getElementById('boatr_vessel_name-warning');

    if (!value) {
        input.style.borderColor = '';
        input.style.backgroundColor = '';
        if (warningElement) warningElement.style.display = 'none';
        return;
    }

    const validPattern = /^[a-zA-Z0-9\s\-']*$/;
    const hasInvalidChars = !validPattern.test(value);

    if (hasInvalidChars) {
        input.style.borderColor = '#dc3545';
        input.style.backgroundColor = '#fff8f8';
        if (warningElement) {
            warningElement.style.display = 'block';
            warningElement.textContent = 'Special characters are not allowed. Use only letters, numbers, spaces, hyphens, and apostrophes.';
        }
    } else {
        input.style.borderColor = '#28a745';
        input.style.backgroundColor = '#f8fff8';
    }
}


/**
 * Legacy initialization function
 */
function initializeBoatRegistration() {
    injectBoatRStyles();
    initializeBoatRForm();
}

/**
 * Initialize Boat Registration form
 */
function initializeBoatRForm() {
    console.log('Initializing BoatR form...');

      initializeBoatRFormUpdated();

    // Initialize FishR validation
    initializeFishRValidation();

    // Initialize boat type field
    const boatTypeSelect = document.getElementById('boatr_boat_type');
    if (boatTypeSelect && boatTypeSelect.value) {
        handleBoatTypeChange(boatTypeSelect);
    }

    // Initialize boat classification field
    const boatClassificationSelect = document.getElementById('boatr_boat_classification');
    if (boatClassificationSelect && boatClassificationSelect.value) {
        handleBoatClassificationChange(boatClassificationSelect);
    }

    // Ensure CSRF token is available
    ensureCSRFToken();

    console.log('BoatR form initialized successfully');
}
// ==============================================
// GLOBAL FUNCTIONS FOR COMPATIBILITY
// ==============================================

// Make functions available globally
window.openFormBoatR = openFormBoatR;
window.closeFormBoatR = closeFormBoatR;
window.submitBoatRForm = submitBoatRForm;
window.handleBoatTypeChange = handleBoatTypeChange;
window.initializeBoatRegistration = initializeBoatRegistration;
window.fillSampleBoatRData = fillSampleBoatRData;
window.previewSingleFile = previewSingleFile;
window.removeSingleFile = removeSingleFile;
window.showTab = showTab;
window.handleBoatClassificationChange = handleBoatClassificationChange;

// Auto-initialize when script loads
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        injectBoatRStyles();
    });
} else {
    injectBoatRStyles();
}

console.log('BoatR module loaded successfully ✅');