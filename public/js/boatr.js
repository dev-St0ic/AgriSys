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
        alert('Security token is missing. Please refresh the page and try again.');
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

    window.scrollTo({ top: 0, behavior: 'smooth' });
    
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
    
    window.scrollTo({ top: 0, behavior: 'smooth' });
    
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
    if (!event) return;

    const formSection = event.target.closest('#boatr-form') || document.getElementById('boatr-form');
    if (!formSection) return;

    // Hide all tab contents
    const allTabContents = formSection.querySelectorAll('.boatr-tab-content');
    allTabContents.forEach(content => {
        content.style.display = 'none';
    });

    // Remove active class from all tab buttons
    const allTabButtons = formSection.querySelectorAll('.boatr-tab-btn');
    allTabButtons.forEach(btn => {
        btn.classList.remove('active');
    });

    // Show selected tab and activate button
    const targetTab = document.getElementById(tabId);
    if (targetTab) {
        targetTab.style.display = 'block';
        event.target.classList.add('active');
    }
}
function initializeBoatRTabs() {
    const boatrForm = document.getElementById('boatr-form');
    if (!boatrForm) return;

    // Hide only the Requirements and Information tabs, keep Application Form visible
    const requirementsTab = document.getElementById('boatr-requirements-tab');
    const infoTab = document.getElementById('boatr-info-tab');
    
    if (requirementsTab) requirementsTab.style.display = 'none';
    if (infoTab) infoTab.style.display = 'none';

    // Remove active class from all buttons first
    const allButtons = boatrForm.querySelectorAll('.boatr-tab-btn');
    allButtons.forEach(btn => {
        btn.classList.remove('active');
    });

    // Ensure Application Form tab is visible and its button is active
    const firstTab = document.getElementById('boatr-form-tab');
    const firstButton = boatrForm.querySelector('.boatr-tab-btn');
    
    if (firstTab && firstButton) {
        firstTab.style.display = 'block';
        firstButton.classList.add('active');
    }
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
        showValidationMessage(fishRInput, '🔄 Checking FishR registration...', 'warning');
        
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
        showValidationMessage(fishRInput, '🔄 Validating FishR registration...', 'warning');
        
        const response = await fetch(`/api/validate-fishr/${encodeURIComponent(number)}`);
        const data = await response.json();

        if (data.valid) {
            // Valid FishR number
            fishRInput.style.borderColor = '#28a745';
            fishRInput.style.backgroundColor = '#f8fff8';
            showValidationMessage(fishRInput, '✅ Valid FishR registration number', 'success');
            fishRInput.dataset.validated = 'true';
        } else {
            // Invalid FishR number
            fishRInput.style.borderColor = '#dc3545';
            fishRInput.style.backgroundColor = '#fff8f8';
            showValidationMessage(fishRInput, '❌ Invalid or non-approved FishR number. Please ensure you have an approved FishR registration.', 'error');
            fishRInput.dataset.validated = 'false';
        }
    } catch (error) {
        console.error('Error validating FishR number:', error);
        fishRInput.style.borderColor = '#ffc107';
        fishRInput.style.backgroundColor = '#fffbf0';
        showValidationMessage(fishRInput, '⚠️ Unable to verify FishR number. Please check your connection.', 'warning');
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
 * Handles Boat Registration form submission - COMPLETE WORKING VERSION
 */
function submitBoatRForm(event) {
    event.preventDefault();
    
    console.log('=== BoatR Form Submission Started ===');
    
    const form = document.getElementById('boatr-registration-form');
    if (!form) {
        console.error('Boat Registration form not found');
        alert('Form not found. Please refresh the page and try again.');
        return false;
    }

    // Get CSRF token
    const csrfToken = getCSRFToken();
    if (!csrfToken) {
        alert('Security token is missing. Please refresh the page and try again.');
        location.reload();
        return false;
    }

    // Validate form before submission
    if (!validateBoatRForm(form)) {
        return false;
    }

    // Create FormData object to handle file uploads
    const formData = new FormData(form);
    
    // Ensure CSRF token is included
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
        console.log('Response headers:', [...response.headers.entries()]);
        
        if (!response.ok) {
            if (response.status === 419) {
                alert('Security token expired. The page will refresh automatically.');
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
            alert('✅ ' + message);
            resetBoatRForm();
            closeFormBoatR();
        } else {
            console.error('Submission error:', data);
            
            if (data.errors) {
                let errorMessage = 'Please correct the following errors:\n';
                Object.keys(data.errors).forEach(field => {
                    errorMessage += `• ${data.errors[field].join(', ')}\n`;
                });
                alert('❌ ' + errorMessage);
            } else {
                alert('❌ ' + (data.message || 'Error submitting form. Please try again.'));
            }
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        
        if (error.message.includes('419') || error.message.includes('CSRF')) {
            alert('❌ Security token expired. The page will refresh automatically.');
            setTimeout(() => location.reload(), 2000);
        } else if (error.message.includes('Failed to fetch')) {
            alert('❌ Network connection error. Please check your internet connection and try again.');
        } else if (error.message.includes('Validation Error')) {
            alert('❌ ' + error.message);
        } else {
            alert('❌ An error occurred while submitting your application. Please try again.');
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
 */
function validateBoatRForm(form) {
    const formData = new FormData(form);
    const requiredFields = [
        'first_name',
        'last_name',
        'fishr_number',
        'vessel_name',
        'boat_type',
        'boat_length',
        'boat_width',
        'boat_depth',
        'engine_type',
        'engine_horsepower',
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
        alert(`Please fill in the following required fields: ${fieldNames.join(', ')}`);
        return false;
    }
    
    // Validate FishR number format
    const fishRNumber = formData.get('fishr_number');
    if (!fishRNumber.match(/^FISHR-[A-Z0-9]{8}$/i)) {
        alert('Please enter a valid FishR registration number (format: FISHR-XXXXXXXX)');
        const fishRInput = form.querySelector('#boatr_fishr_number');
        if (fishRInput) fishRInput.focus();
        return false;
    }
    
    // Check if FishR was validated (optional)
    const fishRInput = form.querySelector('#boatr_fishr_number');
    if (fishRInput && fishRInput.dataset.validated === 'false') {
        alert('The FishR registration number you entered is not valid or not approved. Please enter a valid approved FishR number.');
        fishRInput.focus();
        return false;
    }
    
    // Validate boat dimensions
    const length = parseFloat(formData.get('boat_length'));
    const width = parseFloat(formData.get('boat_width'));
    const depth = parseFloat(formData.get('boat_depth'));
    
    if (isNaN(length) || length <= 0 || length > 200) {
        alert('Please enter a valid boat length (1-200 feet)');
        return false;
    }
    
    if (isNaN(width) || width <= 0 || width > 50) {
        alert('Please enter a valid boat width (1-50 feet)');
        return false;
    }
    
    if (isNaN(depth) || depth <= 0 || depth > 30) {
        alert('Please enter a valid boat depth (1-30 feet)');
        return false;
    }
    
    // Validate engine horsepower
    const hp = parseInt(formData.get('engine_horsepower'));
    if (isNaN(hp) || hp <= 0 || hp > 500) {
        alert('Please enter valid engine horsepower (1-500 HP)');
        return false;
    }
    
    return true;
}

// ==============================================
// UTILITY FUNCTIONS
// ==============================================

/**
 * Initialize Boat Registration form
 */
function initializeBoatRForm() {
    console.log('Initializing BoatR form...');

     // Initialize tabs first
    initializeBoatRTabs();
    
    // Initialize FishR validation
    initializeFishRValidation();
    
    // Initialize boat type field
    const boatTypeSelect = document.getElementById('boatr_boat_type');
    if (boatTypeSelect && boatTypeSelect.value) {
        handleBoatTypeChange(boatTypeSelect);
    }
    
    // Ensure CSRF token is available
    ensureCSRFToken();
    
    console.log('BoatR form initialized successfully');
}

/**
 * Reset Boat Registration form
 */
function resetBoatRForm() {
    const form = document.getElementById('boatr-registration-form');
    if (form) {
        form.reset();
        
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

/**
 * Fill sample data for testing
 */
function fillSampleBoatRData() {
    const form = document.getElementById('boatr-registration-form');
    if (!form) return;
    
    const sampleData = {
        first_name: 'Juan',
        middle_name: 'dela',
        last_name: 'Cruz',
        fishr_number: 'FISHR-SAMPLE01',
        vessel_name: 'MV Lucky Star',
        boat_type: 'Banca',
        boat_length: '15.5',
        boat_width: '3.2',
        boat_depth: '2.1',
        engine_type: 'Yamaha Outboard Motor',
        engine_horsepower: '40',
        primary_fishing_gear: 'Hook and Line'
    };
    
    Object.keys(sampleData).forEach(key => {
        const input = form.querySelector(`[name="${key}"]`);
        if (input) {
            input.value = sampleData[key];
        }
    });
    
    // Trigger boat type change
    const boatTypeSelect = form.querySelector('[name="boat_type"]');
    if (boatTypeSelect) {
        handleBoatTypeChange(boatTypeSelect);
    }
    
    console.log('Sample data filled');
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
 * Legacy initialization function
 */
function initializeBoatRegistration() {
    injectBoatRStyles();
    initializeBoatRForm();
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

// Auto-initialize when script loads
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        injectBoatRStyles();
    });
} else {
    injectBoatRStyles();
}

console.log('BoatR module loaded successfully ✅');