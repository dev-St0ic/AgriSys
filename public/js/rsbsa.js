// RSBSA Frontend JavaScript Functions - COMPLETE VERSION WITH PERSISTENCE AND RELOAD HANDLING
// Updated to maintain form state on page reload and match FishR notification style exactly

/**
 * Global CSRF token management
 */
let rsbsaCSRFToken = null;

/**
 * Get fresh CSRF token for RSBSA
 */
async function refreshRSBSACSRFToken() {
    try {
        const response = await fetch('/csrf-token');
        const data = await response.json();
        rsbsaCSRFToken = data.csrf_token;

        // Update meta tag
        const metaTag = document.querySelector('meta[name="csrf-token"]');
        if (metaTag) {
            metaTag.setAttribute('content', rsbsaCSRFToken);
        }

        // Update all CSRF input fields
        document.querySelectorAll('input[name="_token"]').forEach(input => {
            input.value = rsbsaCSRFToken;
        });

        console.log('RSBSA CSRF token refreshed');
        return rsbsaCSRFToken;
    } catch (error) {
        console.error('Failed to refresh RSBSA CSRF token:', error);
        throw error;
    }
}

/**
 * Get current CSRF token for RSBSA
 */
function getRSBSACSRFToken() {
    if (rsbsaCSRFToken) return rsbsaCSRFToken;

    const metaTag = document.querySelector('meta[name="csrf-token"]');
    if (metaTag) {
        rsbsaCSRFToken = metaTag.getAttribute('content');
        return rsbsaCSRFToken;
    }

    console.error('No RSBSA CSRF token found');
    return null;
}

/**
 * Fetch with CSRF retry logic for RSBSA
 */
async function fetchRSBSAWithCSRFRetry(url, options, retries = 1) {
    try {
        const response = await fetch(url, options);

        // If CSRF error, refresh token and retry
        if (response.status === 419 && retries > 0) {
            console.log('RSBSA CSRF token mismatch, refreshing token and retrying...');

            // Refresh CSRF token
            const newToken = await refreshRSBSACSRFToken();

            // Update headers with new token
            if (options.headers) {
                options.headers['X-CSRF-TOKEN'] = newToken;
            }

            // If FormData, we need to update the _token field
            if (options.body instanceof FormData) {
                options.body.set('_token', newToken);
            }

            // Retry the request
            return await fetchRSBSAWithCSRFRetry(url, options, retries - 1);
        }

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return response;
    } catch (error) {
        if (retries > 0 && error.message.includes('419')) {
            throw new Error('RSBSA CSRF token mismatch after retry');
        }
        throw error;
    }
}

/**
 * Check if we should show RSBSA form based on URL
 */
function shouldShowRSBSAForm() {
    const currentPath = window.location.pathname;
    return currentPath === '/services/rsbsa';
}

/**
 * Opens the RSBSA Registration form - FIXED VERSION
 */
function openRSBSAForm(event) {
    if (event && typeof event.preventDefault === 'function') {
        event.preventDefault();
    }

    if (!showAuthRequired('RSBSA Registration')) {
        return false;
    }

    // Block if user already has a pending/under-review application
    if (window.pendingApplications && window.pendingApplications.rsbsa) {
        agrisysModal.warning(
            'You already have a pending RSBSA application. Please wait for your current application to be processed before submitting a new one.',
            { title: 'Application Already Submitted' }
        );
        return false;
    }

    console.log('Opening RSBSA form');

    if (typeof hideAllMainSections === 'function') hideAllMainSections();
    if (typeof hideAllForms === 'function') hideAllForms();

    const formElement = document.getElementById('rsbsa-form');
    if (formElement) {
        formElement.style.display = 'block';

        // Reset form and clear any previous messages (only if not from page load)
        if (event && event.type !== 'load' && event.type !== 'DOMContentLoaded') {
            resetRSBSAForm();
        }

        // Scroll to top with proper timing and multiple fallbacks
        setTimeout(() => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
            document.documentElement.scrollTop = 0;
            document.body.scrollTop = 0;
        }, 50);

        // Update URL without page reload
        if (window.location.pathname !== '/services/rsbsa') {
            history.pushState({page: 'rsbsa'}, '', '/services/rsbsa');
        }

        console.log('RSBSA form opened successfully');
    } else {
        console.error('RSBSA Registration form element not found');
        agrisysModal.error('Form not available. Please refresh the page and try again.', { title: 'Form Error' });
        return;
    }
}

/**
 * Closes RSBSA Registration form and returns to main services
 */
function closeFormRSBSA() {
    console.log('Closing RSBSA form');

    const formElement = document.getElementById('rsbsa-form');
    if (formElement) {
        formElement.style.display = 'none';
        console.log('RSBSA form closed');
    }

    // Show main sections again
    if (typeof showAllMainSections === 'function') showAllMainSections();

    // Update URL to home page
    if (window.location.pathname !== '/') {
        history.pushState({page: 'home'}, '', '/');
    }
}

/**
 * Resets the RSBSA form to initial state - MATCHES FISHR EXACTLY
 */
function resetRSBSAForm() {
    const form = document.querySelector('#rsbsa-form form') || document.getElementById('rsbsa-form');
    if (form) {
        // Reset form data
        form.reset();

        // Clear any error styling
        const errorFields = form.querySelectorAll('.error');
        errorFields.forEach(field => field.classList.remove('error'));

        // Clear any validation error messages
        const errorTexts = form.querySelectorAll('.error-text');
        errorTexts.forEach(error => error.remove());

        // Reset submit button state
        const submitBtn = form.querySelector('.rsbsa-submit-btn') || form.querySelector('[type="submit"]') || form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = false;
            const btnText = submitBtn.querySelector('.btn-text');
            const btnLoading = submitBtn.querySelector('.btn-loading');
            if (btnText) btnText.style.display = 'inline';
            if (btnLoading) btnLoading.style.display = 'none';
        }

        // Clear file input if exists
        const fileInput = form.querySelector('[name="supporting_docs"]');
        if (fileInput) {
            fileInput.value = '';
        }

        // Remove any file display elements
        if (typeof removeFile === 'function') {
            removeFile();
        }

        console.log('RSBSA form reset to initial state');
    }
}

/**
 * RSBSA Tab switching - Based on working BoatR pattern
 */
function showRSBSATab(tabName, event) {
    if (!event) return;

    // Get the RSBSA form container - be specific
    const formSection = event.target.closest('#rsbsa-form') || document.getElementById('rsbsa-form');
    if (!formSection) {
        console.error('RSBSA form section not found');
        return;
    }

    // Hide all tab contents within RSBSA form
    const allTabContents = formSection.querySelectorAll('.rsbsa-tab-content');
    allTabContents.forEach(content => {
        content.style.display = 'none';
        console.log('Hiding tab:', content.id);
    });

    // Remove active class from all tab buttons within RSBSA form
    const allTabButtons = formSection.querySelectorAll('.rsbsa-tab-btn');
    allTabButtons.forEach(btn => {
        btn.classList.remove('active');
    });

    // Show selected tab and activate button
    const targetTab = document.getElementById(tabName);
    if (targetTab) {
        targetTab.style.display = 'block';
        event.target.classList.add('active');
        console.log('Showing tab:', tabName);
    } else {
        console.error('Target tab not found:', tabName);
    }
}

/**
 * Initialize RSBSA tabs - Based on BoatR pattern
 */
function initializeRSBSATabs() {
    const rsbsaForm = document.getElementById('rsbsa-form');
    if (!rsbsaForm) return;

    // Hide Requirements and Information tabs, keep form visible
    const requirementsTab = document.getElementById('requirements');
    const infoTab = document.getElementById('information');

    if (requirementsTab) requirementsTab.style.display = 'none';
    if (infoTab) infoTab.style.display = 'none';

    // Remove active class from all buttons first
    const allButtons = rsbsaForm.querySelectorAll('.rsbsa-tab-btn');
    allButtons.forEach(btn => {
        btn.classList.remove('active');
    });

    // Ensure Application Form tab is visible and its button is active
    const firstTab = document.getElementById('form');
    const firstButton = rsbsaForm.querySelector('.rsbsa-tab-btn');

    if (firstTab && firstButton) {
        firstTab.style.display = 'block';
        firstButton.classList.add('active');
    }

    console.log('RSBSA tabs initialized');
}

/**
 * ⭐ MAIN TOGGLE: Show/Hide livelihood-specific field groups
 * THIS IS THE FIXED VERSION - ONLY ONE DEFINITION
 */
function toggleRSBSALivelihoodFields(selectElement) {
    console.log('🔄 toggleRSBSALivelihoodFields called with value:', selectElement.value);

    const selectedValue = selectElement.value;

    // Get all livelihood field group containers
    const farmerFields = document.getElementById('rsbsa-farmer-fields');
    const farmworkerFields = document.getElementById('rsbsa-farmworker-fields');
    const fisherfolkFields = document.getElementById('rsbsa-fisherfolk-fields');
    const agriYouthFields = document.getElementById('rsbsa-agriyouth-fields');

    // Hide ALL livelihood field groups
    if (farmerFields) farmerFields.style.display = 'none';
    if (farmworkerFields) farmworkerFields.style.display = 'none';
    if (fisherfolkFields) fisherfolkFields.style.display = 'none';
    if (agriYouthFields) agriYouthFields.style.display = 'none';

    // Get farm location field
    const farmLocationInput = document.getElementById('rsbsa-farm_location');

    // Show ONLY the selected livelihood group
    switch(selectedValue) {
        case 'Farmer':
            console.log('✅ Showing Farmer fields');
            if (farmerFields) farmerFields.style.display = 'block';
            // Make farm location required for farmers
            if (farmLocationInput) farmLocationInput.required = true;
            break;

        case 'Farmworker/Laborer':
            console.log('✅ Showing Farmworker fields');
            if (farmworkerFields) farmworkerFields.style.display = 'block';
            // Farm location not required for farmworkers
            if (farmLocationInput) farmLocationInput.required = false;
            break;

        case 'Fisherfolk':
            console.log('✅ Showing Fisherfolk fields');
            if (fisherfolkFields) fisherfolkFields.style.display = 'block';
            // Farm location not required for fisherfolk
            if (farmLocationInput) farmLocationInput.required = false;
            break;

        case 'Agri-youth':
            console.log('✅ Showing Agri-youth fields');
            if (agriYouthFields) agriYouthFields.style.display = 'block';
            // Farm location not required for agri-youth
            if (farmLocationInput) farmLocationInput.required = false;
            break;

        default:
            console.log('⚠️ No livelihood selected');
            // Hide all and make farm location not required
            if (farmLocationInput) farmLocationInput.required = false;
            break;
    }
}

/**
 * NESTED TOGGLE 1: Show "Specify Other Crops" when farmer selects "Other Crops"
 */
function toggleRSBSAFarmerOtherCrops() {
    const cropsSelect = document.getElementById('rsbsa-farmer_crops');
    const otherCropsField = document.getElementById('rsbsa-farmer-other-crops-field');

    if (cropsSelect && otherCropsField) {
        if (cropsSelect.value === 'Other Crops') {
            otherCropsField.style.display = 'block';
        } else {
            otherCropsField.style.display = 'none';
        }
    }
}

/**
 * NESTED TOGGLE 2: Show "Specify Other Farm Work" when farmworker selects "Others"
 */
function toggleRSBSAFarmworkerOtherType() {
    const typeSelect = document.getElementById('rsbsa-farmworker_type');
    const otherTypeField = document.getElementById('rsbsa-farmworker-other-type-field');

    if (typeSelect && otherTypeField) {
        if (typeSelect.value === 'Others') {
            otherTypeField.style.display = 'block';
        } else {
            otherTypeField.style.display = 'none';
        }
    }
}

/**
 * NESTED TOGGLE 3: Show "Specify Other Fishing Activity" when fisherfolk selects "Others"
 */
function toggleRSBSAFisherfolfOtherActivity() {
    const activitySelect = document.getElementById('rsbsa-fisherfolk_activity');
    const otherActivityField = document.getElementById('rsbsa-fisherfolk-other-activity-field');

    if (activitySelect && otherActivityField) {
        if (activitySelect.value === 'Others') {
            otherActivityField.style.display = 'block';
        } else {
            otherActivityField.style.display = 'none';
        }
    }
}

/**
 * UPDATED VALIDATION - FARM LOCATION ONLY FOR FARMERS
 * Matches server validation exactly and validates all fields
 */
function validateRSBSAForm(form) {
    const requiredFields = [
        'first_name',
        'last_name',
        'sex',
        'barangay',
        'address',
        'contact_number',
        'main_livelihood'
    ];

    // Field label mappings for better error messages
    const fieldLabels = {
        'first_name': 'First Name',
        'last_name': 'Last Name',
        'sex': 'Sex',
        'barangay': 'Barangay',
        'address': 'Address',
        'contact_number': 'Contact Number',
        'main_livelihood': 'Main Livelihood'
    };

    let isValid = true;
    let errors = [];

    // 1️⃣ VALIDATE REQUIRED FIELDS
    requiredFields.forEach(fieldName => {
        const field = form.querySelector(`[name="${fieldName}"]`);
        if (!field || !field.value.trim()) {
            isValid = false;
            const fieldLabel = fieldLabels[fieldName] || fieldName.replace('_', ' ').toLowerCase().replace(/\b\w/g, l => l.toUpperCase());
            errors.push(`${fieldLabel} is required`);

            if (field) {
                field.classList.add('error');
                field.addEventListener('input', function() {
                    this.classList.remove('error');
                }, { once: true });
            }
        }
    });

    // 2️⃣ VALIDATE FIRST NAME FORMAT
    const firstNameField = form.querySelector('[name="first_name"]');
    if (firstNameField && firstNameField.value) {
        const namePattern = /^[a-zA-Z\s\'-]+$/;
        if (!namePattern.test(firstNameField.value)) {
            isValid = false;
            errors.push('First name can only contain letters, spaces, hyphens, and apostrophes');
            firstNameField.classList.add('error');
        }
    }

    // 3️⃣ VALIDATE LAST NAME FORMAT
    const lastNameField = form.querySelector('[name="last_name"]');
    if (lastNameField && lastNameField.value) {
        const namePattern = /^[a-zA-Z\s\'-]+$/;
        if (!namePattern.test(lastNameField.value)) {
            isValid = false;
            errors.push('Last name can only contain letters, spaces, hyphens, and apostrophes');
            lastNameField.classList.add('error');
        }
    }

    // 4️⃣ VALIDATE MIDDLE NAME FORMAT
    const middleNameField = form.querySelector('[name="middle_name"]');
    if (middleNameField && middleNameField.value) {
        const namePattern = /^[a-zA-Z\s\'-]+$/;
        if (!namePattern.test(middleNameField.value)) {
            isValid = false;
            errors.push('Middle name can only contain letters, spaces, hyphens, and apostrophes');
            middleNameField.classList.add('error');
        }
    }

    // 5️⃣ VALIDATE NAME EXTENSION
    const nameExtensionField = form.querySelector('[name="name_extension"]');
    if (nameExtensionField && nameExtensionField.value) {
        const extensionPattern = /^[a-zA-Z.\s]*$/;
        if (!extensionPattern.test(nameExtensionField.value)) {
            isValid = false;
            errors.push('Name extension can only contain letters, periods, and spaces');
            nameExtensionField.classList.add('error');
        }
    }

    // 6️⃣ VALIDATE SEX
    const sexField = form.querySelector('[name="sex"]');
    if (sexField && sexField.value) {
        const validSexOptions = ['Male', 'Female', 'Preferred not to say'];
        if (!validSexOptions.includes(sexField.value)) {
            isValid = false;
            errors.push('Please select a valid sex option');
            sexField.classList.add('error');
        }
    }

    // 7️⃣ VALIDATE BARANGAY
    const barangayField = form.querySelector('[name="barangay"]');
    if (barangayField && !barangayField.value) {
        isValid = false;
        errors.push('Barangay is required');
        barangayField.classList.add('error');
    }

    // 8️⃣ VALIDATE ADDRESS FIELD
    const addressField = form.querySelector('[name="address"]');
    if (addressField && !addressField.value.trim()) {
        isValid = false;
        errors.push('Complete address is required');
        addressField.classList.add('error');
    } else if (addressField && addressField.value) {
        const addressPattern = /^[a-zA-Z0-9\s,.\'-]+$/;
        if (!addressPattern.test(addressField.value)) {
            isValid = false;
            errors.push('Address can only contain letters, numbers, spaces, commas, periods, hyphens, and apostrophes');
            addressField.classList.add('error');
        }
    }

    // 9️⃣ VALIDATE Contact NUMBER
    const contactField = form.querySelector('[name="contact_number"]');
    if (contactField && contactField.value) {
        const contactPattern = /^09\d{9}$/;
        if (!contactPattern.test(contactField.value.replace(/\s+/g, ''))) {
            isValid = false;
            errors.push('Contact number must be: +639XXXXXXXXX or 09XXXXXXXXX (11 digits total)');
            contactField.classList.add('error');
        }
    }

    // 🔟 VALIDATE MAIN LIVELIHOOD
    const livelihoodField = form.querySelector('[name="main_livelihood"]');
    if (livelihoodField && livelihoodField.value) {
        const validOptions = ['Farmer', 'Farmworker/Laborer', 'Fisherfolk', 'Agri-youth'];
        if (!validOptions.includes(livelihoodField.value)) {
            isValid = false;
            errors.push('Please select a valid main livelihood option');
            livelihoodField.classList.add('error');
        }
    }

    // FARMER-SPECIFIC VALIDATIONS
    if (livelihoodField && livelihoodField.value === 'Farmer') {
        const farmerCropsField = form.querySelector('[name="farmer_crops"]');
        if (!farmerCropsField || !farmerCropsField.value) {
            isValid = false;
            errors.push('Crops/Commodity is required for Farmer');
            if (farmerCropsField) farmerCropsField.classList.add('error');
        }

        if (farmerCropsField && farmerCropsField.value === 'Other Crops') {
            const otherCropsField = form.querySelector('[name="farmer_other_crops"]');
            if (!otherCropsField || !otherCropsField.value.trim()) {
                isValid = false;
                errors.push('Please specify the other crops/commodity');
                if (otherCropsField) otherCropsField.classList.add('error');
            } else {
                const cropsPattern = /^[a-zA-Z\s,'\-]+$/;
                if (!cropsPattern.test(otherCropsField.value)) {
                    isValid = false;
                    errors.push('Other crops can only contain letters, spaces, commas, hyphens, and apostrophes');
                    otherCropsField.classList.add('error');
                }
            }
        }

        const livestockField = form.querySelector('[name="farmer_livestock"]');
        if (livestockField && livestockField.value) {
            const livestockPattern = /^[a-zA-Z0-9\s,()'\-]*$/;
            if (!livestockPattern.test(livestockField.value)) {
                isValid = false;
                errors.push('Livestock/Poultry format is invalid');
                livestockField.classList.add('error');
            }
        }

        const landAreaField = form.querySelector('[name="farmer_land_area"]');
        if (landAreaField && landAreaField.value) {
            const landArea = parseFloat(landAreaField.value);
            if (isNaN(landArea) || landArea < 0 || landArea > 1000) {
                isValid = false;
                errors.push('Land area must be between 0 and 1000 hectares');
                landAreaField.classList.add('error');
            }
        }

        const typeOfFarmField = form.querySelector('[name="farmer_type_of_farm"]');
        if (!typeOfFarmField || !typeOfFarmField.value) {
            isValid = false;
            errors.push('Type of farm is required for Farmer');
            if (typeOfFarmField) typeOfFarmField.classList.add('error');
        }

        const landOwnershipField = form.querySelector('[name="farmer_land_ownership"]');
        if (!landOwnershipField || !landOwnershipField.value) {
            isValid = false;
            errors.push('Land ownership is required for Farmer');
            if (landOwnershipField) landOwnershipField.classList.add('error');
        }

        // ⭐ FARM LOCATION - REQUIRED ONLY FOR FARMERS
        const farmLocationField = form.querySelector('[name="farm_location"]');
        if (!farmLocationField || !farmLocationField.value.trim()) {
            isValid = false;
            errors.push('Farm location is required for Farmer');
            if (farmLocationField) farmLocationField.classList.add('error');
        } else {
            const locationPattern = /^[a-zA-Z0-9\s,'\-]+$/;
            if (!locationPattern.test(farmLocationField.value)) {
                isValid = false;
                errors.push('Farm location can only contain letters, numbers, spaces, commas, hyphens, and apostrophes');
                farmLocationField.classList.add('error');
            }
        }
    }

    // FARMWORKER-SPECIFIC VALIDATIONS
    if (livelihoodField && livelihoodField.value === 'Farmworker/Laborer') {
        const farmworkerTypeField = form.querySelector('[name="farmworker_type"]');
        if (!farmworkerTypeField || !farmworkerTypeField.value) {
            isValid = false;
            errors.push('Type of farm work is required');
            if (farmworkerTypeField) farmworkerTypeField.classList.add('error');
        }

        if (farmworkerTypeField && farmworkerTypeField.value === 'Others') {
            const otherTypeField = form.querySelector('[name="farmworker_other_type"]');
            if (!otherTypeField || !otherTypeField.value.trim()) {
                isValid = false;
                errors.push('Please specify the other farm work type');
                if (otherTypeField) otherTypeField.classList.add('error');
            } else {
                const typePattern = /^[a-zA-Z\s,'\-]+$/;
                if (!typePattern.test(otherTypeField.value)) {
                    isValid = false;
                    errors.push('Farm work type can only contain letters, spaces, commas, hyphens, and apostrophes');
                    otherTypeField.classList.add('error');
                }
            }
        }
    }

    // FISHERFOLK-SPECIFIC VALIDATIONS
    if (livelihoodField && livelihoodField.value === 'Fisherfolk') {
        const fisherfolkActivityField = form.querySelector('[name="fisherfolk_activity"]');
        if (!fisherfolkActivityField || !fisherfolkActivityField.value) {
            isValid = false;
            errors.push('Fishing activity is required');
            if (fisherfolkActivityField) fisherfolkActivityField.classList.add('error');
        }

        if (fisherfolkActivityField && fisherfolkActivityField.value === 'Others') {
            const otherActivityField = form.querySelector('[name="fisherfolk_other_activity"]');
            if (!otherActivityField || !otherActivityField.value.trim()) {
                isValid = false;
                errors.push('Please specify the other fishing activity');
                if (otherActivityField) otherActivityField.classList.add('error');
            } else {
                const activityPattern = /^[a-zA-Z\s,'\-]+$/;
                if (!activityPattern.test(otherActivityField.value)) {
                    isValid = false;
                    errors.push('Fishing activity can only contain letters, spaces, commas, hyphens, and apostrophes');
                    otherActivityField.classList.add('error');
                }
            }
        }
    }

    // AGRI-YOUTH-SPECIFIC VALIDATIONS
    if (livelihoodField && livelihoodField.value === 'Agri-youth') {
        const farmingHouseholdField = form.querySelector('[name="agriyouth_farming_household"]');
        if (!farmingHouseholdField || !farmingHouseholdField.value) {
            isValid = false;
            errors.push('Part of farming household is required');
            if (farmingHouseholdField) farmingHouseholdField.classList.add('error');
        }

        const trainingField = form.querySelector('[name="agriyouth_training"]');
        if (!trainingField || !trainingField.value) {
            isValid = false;
            errors.push('Training/Education is required');
            if (trainingField) trainingField.classList.add('error');
        }

        const participationField = form.querySelector('[name="agriyouth_participation"]');
        if (!participationField || !participationField.value) {
            isValid = false;
            errors.push('Agricultural activity/program participation is required');
            if (participationField) participationField.classList.add('error');
        }
    }

    // VALIDATE FILE UPLOAD
    const fileField = form.querySelector('[name="supporting_docs"]');
    if (fileField && fileField.files.length > 0) {
        const file = fileField.files[0];
        const maxSize = 10 * 1024 * 1024; // 10MB
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];

        if (file.size > maxSize) {
            isValid = false;
            errors.push('File size must be less than 10MB');
            fileField.classList.add('error');
        }

        if (!allowedTypes.includes(file.type)) {
            isValid = false;
            errors.push('File must be JPG, PNG, or PDF format');
            fileField.classList.add('error');
        }
    }

    return { isValid, errors };
}

/**
 * Helper to clear field errors
 */
function clearRSBSAErrors() {
    const form = document.querySelector('#rsbsa-form form') || document.getElementById('rsbsa-form');
    if (!form) return;

    form.querySelectorAll('.error').forEach(field => {
        field.classList.remove('error');
    });

    form.querySelectorAll('.validation-warning').forEach(warning => {
        warning.style.display = 'none';
    });
}

/**
 * Format mobile number to match server requirements
 */
function formatMobileNumber(input) {
    let value = input.value.replace(/\D/g, '');

    if (value.startsWith('63') && value.length >= 11) {
        value = '+' + value;
    } else if (value.match(/^9\d{9}$/)) {
        value = '0' + value;
    } else if (value.startsWith('0') && value.length === 11) {
        // Already correct
    } else if (!value.startsWith('0') && !value.startsWith('+')) {
        if (value.length === 10 && value.startsWith('9')) {
            value = '0' + value;
        }
    }

    input.value = value;
}

/**
 * Main form submission handler
 */
function handleRSBSAFormSubmission() {
    const rsbsaForm = document.querySelector('#rsbsa-form form') || document.querySelector('#rsbsa-form');

    if (!rsbsaForm) {
        console.error('RSBSA form not found');
        return;
    }

    console.log('RSBSA form found, attaching event listener');

    rsbsaForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        e.stopPropagation();

        console.log('RSBSA form submission intercepted');

        if (!isUserAuthenticatedAndVerified()) {
            showAuthRequired('RSBSA Registration');
            return false;
        }

       const validation = validateRSBSAFormSubmission(this);
        if (!validation.isValid) {
            displayRSBSAValidationErrors(validation.errors);
            return false;
        }

        // Contact number validation
        const contactField = form.querySelector('[name="contact_number"]');
        if (!contactField || !contactField.value.trim()) {
            isValid = false;
            errors['contact_number'] = ['Contact number is required'];
        } else {
            const contactValue = contactField.value.replace(/\s+/g, ''); // Remove spaces
            const contactPattern = /^09\d{9}$/;
            if (!contactPattern.test(contactValue)) {
                isValid = false;
                errors['contact_number'] = ['Contact number must be 09XXXXXXXXX (11 digits)'];
            }
        }

        const submitButton = this.querySelector('.rsbsa-submit-btn') || this.querySelector('[type="submit"]') || this.querySelector('button[type="submit"]');
        if (!submitButton) {
            console.error('Submit button not found');
            return false;
        }

        const originalText = submitButton.textContent;
        const btnText = submitButton.querySelector('.btn-text');
        const btnLoading = submitButton.querySelector('.btn-loading');

        if (btnText && btnLoading) {
            btnText.style.display = 'none';
            btnLoading.style.display = 'inline';
        } else {
            submitButton.textContent = 'Submitting...';
        }
        submitButton.disabled = true;

        try {
            console.log('Ensuring CSRF token...');
            let csrfToken = getRSBSACSRFToken();

            if (!csrfToken) {
                console.log('No cached CSRF token, fetching fresh one...');
                csrfToken = await refreshRSBSACSRFToken();
            }

            if (!csrfToken) {
                throw new Error('CSRF token could not be obtained');
            }

            console.log('CSRF token obtained:', csrfToken.substring(0, 10) + '...');

            const formData = new FormData(this);
            formData.set('_token', csrfToken);

            console.log('Submitting to /apply/rsbsa');

            const response = await fetchRSBSAWithCSRFRetry('/apply/rsbsa', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            console.log('Response status:', response.status);

            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                console.error('Non-JSON response:', text.substring(0, 500));
                throw new Error('Server returned non-JSON response. Please check server logs.');
            }

            const result = await response.json();
            console.log('Response data:', result);

            if (response.ok && result.success) {
                const successMessage = result.message || 'Your RSBSA application has been submitted successfully!';
                const applicationNumber = result.application_number || result.reference_number || null;

                agrisysModal.success(successMessage, {
                    title: 'Application Submitted!',
                    reference: applicationNumber,
                    onClose: () => {
                        this.reset();
                        if (typeof removeFile === 'function') {
                            removeFile();
                        }
                        closeFormRSBSA();
                        setTimeout(() => {
                            document.documentElement.scrollTop = 0;
                            document.body.scrollTop = 0;
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        }, 500);
                    }
                });
            } else {
                let errorMessage = result.message || 'There was an error submitting your application.';

                if (result.errors) {
                    const errorList = Object.values(result.errors).flat();
                    agrisysModal.validationError(errorList, { title: 'Submission Failed' });
                } else {
                    console.error('Application submission failed:', result);
                    agrisysModal.error(errorMessage, { title: 'Submission Failed' });
                }
            }

        } catch (error) {
            console.error('Submission error:', error);

            if (error.message.includes('419') || error.message.includes('CSRF')) {
                agrisysModal.error('Your session has expired. Please refresh the page and try again.', { title: 'Session Expired' });
            } else if (error.message.includes('422')) {
                agrisysModal.error('Please check your form data and try again.', { title: 'Validation Error' });
            } else if (error.message.includes('Network')) {
                agrisysModal.error('Network error. Please check your connection and try again.', { title: 'Connection Error' });
            } else {
                agrisysModal.error('There was an error submitting your request. Please try again.', { title: 'Submission Error' });
            }
        } finally {
            if (btnText && btnLoading) {
                btnText.style.display = 'inline';
                btnLoading.style.display = 'none';
            } else {
                submitButton.textContent = originalText;
            }
            submitButton.disabled = false;
        }

        return false;
    });
}

/**
 * Auto-fill form with sample data for testing
 */
function fillSampleRSBSAData() {
    const form = document.querySelector('#rsbsa-form form') || document.getElementById('rsbsa-form');
    if (!form) return;

    const sampleData = {
        first_name: 'Maria',
        middle_name: 'Santos',
        last_name: 'Cruz',
        sex: 'Female',
        barangay: 'San Jose',
        address: '123 Main Street, Poblacion',
        contact_number: '09123456789',
        main_livelihood: 'Farmer'
    };

    Object.keys(sampleData).forEach(fieldName => {
        const input = form.querySelector(`[name="${fieldName}"]`);
        if (input) {
            input.value = sampleData[fieldName];

            if (input.tagName === 'SELECT') {
                input.dispatchEvent(new Event('change'));
            }
        }
    });

    console.log('Sample data filled for RSBSA form');
}

/**
 * Clear form data
 */
function clearRSBSAForm() {
    if (confirm('Are you sure you want to clear all form data?')) {
        resetRSBSAForm();
    }
}

/**
 * Handle browser back/forward buttons
 */
function handleRSBSAPopState(event) {
    console.log('RSBSA Pop state event:', event.state);

    if (event.state && event.state.page === 'rsbsa') {
        openRSBSAForm(new Event('popstate'));
    } else {
        closeFormRSBSA();
    }
}

/**
 * Hide all main page sections
 */
function hideAllMainSections() {
    const sections = ['home', 'events', 'services', 'how-it-works', '.help-section'];

    sections.forEach(selector => {
        const element = selector.startsWith('.')
            ? document.querySelector(selector)
            : document.getElementById(selector);
        if (element) element.style.display = 'none';
    });
}

/**
 * Show all main page sections
 */
function showAllMainSections() {
    const sections = ['home', 'events', 'services', 'how-it-works', '.help-section'];

    sections.forEach(selector => {
        const element = selector.startsWith('.')
            ? document.querySelector(selector)
            : document.getElementById(selector);
        if (element) element.style.display = 'block';
    });
}

/**
 * Hide all application forms
 */
function hideAllForms() {
    const formIds = ['rsbsa-form', 'seedlings-choice', 'seedlings-form', 'fishr-form', 'boatr-form'];

    formIds.forEach(id => {
        const element = document.getElementById(id);
        if (element) element.style.display = 'none';
    });

    const formSections = document.querySelectorAll('.rsbsa-application-section');
    formSections.forEach(section => {
        section.style.display = 'none';
    });
}

/**
 * Remove file display
 */
function removeFile() {
    const fileDisplay = document.querySelector('.file-display');
    if (fileDisplay) {
        fileDisplay.remove();
    }

    const fileInput = document.querySelector('[name="supporting_docs"]');
    if (fileInput) {
        fileInput.value = '';
    }

    console.log('File removed from RSBSA form');
}

/**
 * Check and show RSBSA form on page load if URL matches
 */
function checkAndShowRSBSAOnLoad() {
    console.log('Checking if RSBSA form should be shown on page load...');
    console.log('Current pathname:', window.location.pathname);

    if (shouldShowRSBSAForm()) {
        console.log('URL indicates RSBSA form should be shown - opening form');

        setTimeout(() => {
            const formElement = document.getElementById('rsbsa-form');
            if (formElement) {
                console.log('RSBSA form element found, opening...');
                openRSBSAForm({ type: 'load' });
            } else {
                console.log('RSBSA form element not found, retrying...');
                setTimeout(() => {
                    const retryFormElement = document.getElementById('rsbsa-form');
                    if (retryFormElement) {
                        openRSBSAForm({ type: 'load' });
                    } else {
                        console.error('RSBSA form element still not found after retry');
                    }
                }, 500);
            }
        }, 100);
    }
}
/**
 * NEW: Handle RSBSA validation errors inline with auto-scroll (NO MODAL)
 */
function displayRSBSAValidationErrors(errors) {
    console.log('Displaying RSBSA validation errors:', errors);

    // Clear previous error messages
    const errorSpans = document.querySelectorAll('#rsbsa-form .error-text');
    errorSpans.forEach(span => span.remove());

    let firstErrorField = null;

    // Show new error messages inline
    for (const fieldName in errors) {
        const input = document.querySelector(`#rsbsa-form [name="${fieldName}"]`);
        if (input) {
            const errorSpan = document.createElement('span');
            errorSpan.className = 'error-text';
            errorSpan.textContent = errors[fieldName][0];
            errorSpan.style.color = '#dc3545';
            errorSpan.style.fontSize = '12px';
            errorSpan.style.marginTop = '5px';
            errorSpan.style.display = 'block';
            input.parentNode.appendChild(errorSpan);

            // Mark first error field
            if (!firstErrorField) {
                firstErrorField = input;
            }
        }
    }

    // Auto-scroll to first error field like FishR does
    if (firstErrorField) {
        firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
        firstErrorField.focus();
    }
}

/**
 * UPDATED: Form submission validation - returns object instead of modal
 */
function validateRSBSAFormSubmission(form) {
    const errors = {};
    let isValid = true;

    const requiredFields = [
        'first_name',
        'last_name',
        'sex',
        'barangay',
        'address',
        'contact_number',
        'main_livelihood'
    ];

    requiredFields.forEach(fieldName => {
        const field = form.querySelector(`[name="${fieldName}"]`);
        if (!field || !field.value.trim()) {
            isValid = false;
            errors[fieldName] = [`${fieldName.replace('_', ' ').charAt(0).toUpperCase() + fieldName.replace('_', ' ').slice(1)} is required`];
        }
    });

    // Farmer validations
    const livelihoodField = form.querySelector('[name="main_livelihood"]');
    if (livelihoodField && livelihoodField.value === 'Farmer') {
        const farmerCropsField = form.querySelector('[name="farmer_crops"]');
        if (!farmerCropsField || !farmerCropsField.value) {
            isValid = false;
            errors['farmer_crops'] = ['Crops/Commodity is required'];
        }

        const typeOfFarmField = form.querySelector('[name="farmer_type_of_farm"]');
        if (!typeOfFarmField || !typeOfFarmField.value) {
            isValid = false;
            errors['farmer_type_of_farm'] = ['Type of farm is required'];
        }

        const landOwnershipField = form.querySelector('[name="farmer_land_ownership"]');
        if (!landOwnershipField || !landOwnershipField.value) {
            isValid = false;
            errors['farmer_land_ownership'] = ['Land ownership is required'];
        }

        const farmLocationField = form.querySelector('[name="farm_location"]');
        if (!farmLocationField || !farmLocationField.value.trim()) {
            isValid = false;
            errors['farm_location'] = ['Farm location is required'];
        }
    }

    return { isValid, errors };
}
/**
 * Initialize RSBSA module
 */
function initializeRSBSAModule() {
    console.log('Initializing RSBSA module...');

    checkAndShowRSBSAOnLoad();
    getRSBSACSRFToken();
    handleRSBSAFormSubmission();
    initializeRSBSATabs();

    // const contactInput = document.querySelector('#rsbsa-form [name="contact_number"]');
    // if (contactInput) {
    //     contactInput.addEventListener('input', function(e) {
    //         formatMobileNumber(e.target);
    //     });
    // }

    const allInputs = document.querySelectorAll('#rsbsa-form input, #rsbsa-form select');
    allInputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.classList.remove('error');
        });
    });

    // Setup event listeners for nested field toggles
    const farmerCropsSelect = document.getElementById('rsbsa-farmer_crops');
    if (farmerCropsSelect) {
        farmerCropsSelect.addEventListener('change', toggleRSBSAFarmerOtherCrops);
    }

    const farmworkerTypeSelect = document.getElementById('rsbsa-farmworker_type');
    if (farmworkerTypeSelect) {
        farmworkerTypeSelect.addEventListener('change', toggleRSBSAFarmworkerOtherType);
    }

    const fisherfolkActivitySelect = document.getElementById('rsbsa-fisherfolk_activity');
    if (fisherfolkActivitySelect) {
        fisherfolkActivitySelect.addEventListener('change', toggleRSBSAFisherfolfOtherActivity);
    }

    // ⭐ TRIGGER LIVELIHOOD TOGGLE ON PAGE LOAD
    const livelihoodSelect = document.getElementById('rsbsa-main_livelihood');
    if (livelihoodSelect && livelihoodSelect.value) {
        console.log('🔄 Triggering livelihood toggle for existing value:', livelihoodSelect.value);
        toggleRSBSALivelihoodFields(livelihoodSelect);
    }

    window.addEventListener('popstate', handleRSBSAPopState);

    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.shiftKey && e.key === 'R') {
            e.preventDefault();
            fillSampleRSBSAData();
        }
        if (e.ctrlKey && e.shiftKey && e.key === 'C') {
            e.preventDefault();
            clearRSBSAForm();
        }
    });

    // === REAL-TIME VALIDATION FOR NAME FIELDS ===
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

// === REAL-TIME VALIDATION FOR CONTACT NUMBER ===
const contactInput = document.querySelector('#rsbsa-form [name="contact_number"]');
const contactWarning = document.getElementById('rsbsa-mobile-warning');

if (contactInput && contactWarning) {
    const phonePattern = /^09\d{9}$/;

    contactInput.addEventListener('input', function(e) {
        const value = e.target.value;

        if (value && !phonePattern.test(value)) {
            contactWarning.style.display = 'block';
            contactInput.style.borderColor = '#ff6b6b';
        } else {
            contactWarning.style.display = 'none';
            contactInput.style.borderColor = '';
        }
    });

    contactInput.addEventListener('blur', function(e) {
        const value = e.target.value;

        if (value && !phonePattern.test(value)) {
            contactWarning.style.display = 'block';
            contactInput.style.borderColor = '#ff6b6b';
        }
    });
}

// === REAL-TIME VALIDATION FOR ADDRESS ===
const addressInput = document.querySelector('#rsbsa-form [name="address"]');
if (addressInput) {
    addressInput.addEventListener('input', function(e) {
        const value = e.target.value;
        const pattern = /^[a-zA-Z0-9\s,.\'-]*$/;

        if (value && !pattern.test(value)) {
            this.style.borderColor = '#ff6b6b';
            this.style.backgroundColor = '#ffe6e6';
        } else {
            this.style.borderColor = '';
            this.style.backgroundColor = '';
        }
    });
}

// === REAL-TIME VALIDATION FOR FARM LOCATION (FARMER ONLY) ===
const farmLocationInput = document.querySelector('#rsbsa-form [name="farm_location"]');
if (farmLocationInput) {
    const farmLocationWarning = document.getElementById('rsbsa-farm_location-warning');

    farmLocationInput.addEventListener('input', function(e) {
        const value = e.target.value;
        const pattern = /^[a-zA-Z0-9\s,.\'-]*$/;

        if (value && !pattern.test(value)) {
            if (farmLocationWarning) {
                farmLocationWarning.style.display = 'block';
            }
            this.style.borderColor = '#ff6b6b';
        } else {
            if (farmLocationWarning) {
                farmLocationWarning.style.display = 'none';
            }
            this.style.borderColor = '';
        }
    });
}

    console.log('RSBSA module initialized successfully');
}

/**
 * Initialize when DOM is ready
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing RSBSA form');
    setTimeout(initializeRSBSAModule, 200);
    console.log('RSBSA form initialization scheduled');
});

/**
 * Also handle window load event
 */
window.addEventListener('load', function() {
    console.log('Window loaded, double-checking RSBSA form display');
    setTimeout(checkAndShowRSBSAOnLoad, 300);
});

/**
 * Handle page visibility change
 */
document.addEventListener('visibilitychange', function() {
    if (!document.hidden && shouldShowRSBSAForm()) {
        console.log('Page became visible and should show RSBSA form');
        setTimeout(checkAndShowRSBSAOnLoad, 100);
    }
});

// Export functions for global access
window.openRSBSAForm = openRSBSAForm;
window.closeFormRSBSA = closeFormRSBSA;
window.resetRSBSAForm = resetRSBSAForm;
window.showRSBSATab = showRSBSATab;
window.toggleRSBSALivelihoodFields = toggleRSBSALivelihoodFields;
window.removeFile = removeFile;
window.fillSampleRSBSAData = fillSampleRSBSAData;
window.clearRSBSAForm = clearRSBSAForm;
window.validateRSBSAForm = validateRSBSAForm;
window.clearRSBSAErrors = clearRSBSAErrors;

console.log('✅ Complete RSBSA JavaScript module loaded - FIXED VERSION');
