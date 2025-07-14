// ==============================================
// BOAT REGISTRATION MODULE - Extracted from landing.js
// Boat Registration and Management System
// ==============================================

// ==============================================
// MAIN NAVIGATION FUNCTIONS
// ==============================================

/**
 * Opens the Boat Registration form
 */
function openFormBoatR(event) {
    event.preventDefault();
    hideAllMainSections();
    hideAllForms();

    const formElement = document.getElementById('boatr-form');
    if (formElement) {
        formElement.style.display = 'block';
        activateApplicationTab('boatr-form');
    } else {
        console.error('Boat Registration form not found');
        return;
    }

    window.scrollTo({ top: 0, behavior: 'smooth' });
    history.pushState(null, '', '/services/boatr');
}

/**
 * Closes Boat Registration form and returns to main services
 */
function closeFormBoatR() {
    const formElement = document.getElementById('boatr-form');
    if (formElement) formElement.style.display = 'none';
    
    showAllMainSections();
    window.scrollTo({ top: 0, behavior: 'smooth' });
    history.pushState(null, '', '/services');
}

// ==============================================
// BOAT TYPE MANAGEMENT
// ==============================================

/**
 * Handles boat type selection changes and updates form accordingly
 */
function handleBoatTypeChange(select) {
    if (!select) {
        console.error('Select element not provided');
        return;
    }

    const boatType = select.value;
    console.log("Selected Boat Type:", boatType);
    
    // Update form fields based on boat type
    updateFormFieldsByBoatType(boatType);
    
    // Update required documents based on boat type
    updateRequiredDocuments(boatType);
    
    // Show/hide additional fields
    toggleBoatTypeSpecificFields(boatType);
}

/**
 * Updates form fields visibility and requirements based on boat type
 */
function updateFormFieldsByBoatType(boatType) {
    const engineFields = document.getElementById('engine-fields');
    const commercialFields = document.getElementById('commercial-fields');
    
    switch (boatType) {
        case 'motorized':
            if (engineFields) engineFields.style.display = 'block';
            if (commercialFields) commercialFields.style.display = 'none';
            break;
        case 'non-motorized':
            if (engineFields) engineFields.style.display = 'none';
            if (commercialFields) commercialFields.style.display = 'none';
            break;
        case 'commercial':
            if (engineFields) engineFields.style.display = 'block';
            if (commercialFields) commercialFields.style.display = 'block';
            break;
        default:
            if (engineFields) engineFields.style.display = 'none';
            if (commercialFields) commercialFields.style.display = 'none';
    }
}

/**
 * Updates required documents list based on boat type
 */
function updateRequiredDocuments(boatType) {
    const docsList = document.getElementById('required-docs-list');
    if (!docsList) return;
    
    let docsHTML = '<h4>Required Documents:</h4><ul>';
    
    // Base documents for all boat types
    docsHTML += '<li>Boat Owner\'s Valid ID</li>';
    docsHTML += '<li>Proof of Boat Ownership (Receipt/Invoice)</li>';
    
    switch (boatType) {
        case 'motorized':
            docsHTML += '<li>Engine Specifications</li>';
            docsHTML += '<li>Engine Purchase Receipt</li>';
            break;
        case 'commercial':
            docsHTML += '<li>Engine Specifications</li>';
            docsHTML += '<li>Engine Purchase Receipt</li>';
            docsHTML += '<li>Commercial Fishing License</li>';
            docsHTML += '<li>Business Permit</li>';
            docsHTML += '<li>Environmental Compliance Certificate</li>';
            break;
        case 'non-motorized':
            docsHTML += '<li>Barangay Certification</li>';
            break;
    }
    
    docsHTML += '</ul>';
    docsList.innerHTML = docsHTML;
}

/**
 * Shows/hides boat type specific fields
 */
function toggleBoatTypeSpecificFields(boatType) {
    // Engine-related fields
    const engineHp = document.getElementById('engine-hp-field');
    const engineBrand = document.getElementById('engine-brand-field');
    
    // Commercial-specific fields
    const grossTonnage = document.getElementById('gross-tonnage-field');
    const fishingLicense = document.getElementById('fishing-license-field');
    
    const showEngineFields = boatType === 'motorized' || boatType === 'commercial';
    const showCommercialFields = boatType === 'commercial';
    
    if (engineHp) engineHp.style.display = showEngineFields ? 'block' : 'none';
    if (engineBrand) engineBrand.style.display = showEngineFields ? 'block' : 'none';
    if (grossTonnage) grossTonnage.style.display = showCommercialFields ? 'block' : 'none';
    if (fishingLicense) fishingLicense.style.display = showCommercialFields ? 'block' : 'none';
}

// ==============================================
// DOCUMENT UPLOAD MANAGEMENT
// ==============================================

/**
 * Manages document upload restrictions (admin-only after inspection)
 */
function manageDocumentUpload() {
    const uploadInput = document.querySelector('#boatr-form-tab input[type="file"]');
    const uploadContainer = document.getElementById('upload-container');
    
    if (uploadInput) {
        uploadInput.disabled = true;
        uploadInput.title = "Upload disabled - for admin use only after on-site inspection";
        
        // Add visual indicator
        if (uploadContainer) {
            uploadContainer.style.opacity = '0.6';
            uploadContainer.style.pointerEvents = 'none';
        }
    }
    
    // Add info message
    addUploadInfoMessage();
}

/**
 * Adds informational message about upload process
 */
function addUploadInfoMessage() {
    const uploadSection = document.getElementById('upload-section');
    if (uploadSection && !document.getElementById('upload-info-message')) {
        const infoDiv = document.createElement('div');
        infoDiv.id = 'upload-info-message';
        infoDiv.className = 'alert alert-info';
        infoDiv.innerHTML = `
            <strong>Note:</strong> Document upload will be enabled by admin staff after on-site boat inspection. 
            Please proceed with your application and schedule an inspection appointment.
        `;
        uploadSection.appendChild(infoDiv);
    }
}

/**
 * Enables document upload (admin function)
 */
function enableDocumentUpload() {
    const uploadInput = document.querySelector('#boatr-form-tab input[type="file"]');
    const uploadContainer = document.getElementById('upload-container');
    
    if (uploadInput) {
        uploadInput.disabled = false;
        uploadInput.title = "Upload supporting documents";
        
        if (uploadContainer) {
            uploadContainer.style.opacity = '1';
            uploadContainer.style.pointerEvents = 'auto';
        }
    }
    
    console.log('Document upload enabled by admin');
}

// ==============================================
// FORM VALIDATION
// ==============================================

/**
 * Validates Boat Registration form data
 */
function validateBoatRForm(formData) {
    const requiredFields = [
        'first_name',
        'last_name',
        'mobile',
        'barangay',
        'address',
        'boat_type',
        'boat_name',
        'boat_length',
        'boat_width'
    ];
    
    const missingFields = requiredFields.filter(field => !formData[field] || formData[field].toString().trim() === '');
    
    if (missingFields.length > 0) {
        alert(`Please fill in the following required fields: ${missingFields.join(', ')}`);
        return false;
    }
    
    // Validate mobile number
    const mobilePattern = /^(09|\+639)\d{9}$/;
    if (!mobilePattern.test(formData.mobile.replace(/\s+/g, ''))) {
        alert('Please enter a valid mobile number (e.g., 09123456789)');
        return false;
    }
    
    // Validate boat dimensions
    if (isNaN(formData.boat_length) || formData.boat_length <= 0) {
        alert('Please enter a valid boat length');
        return false;
    }
    
    if (isNaN(formData.boat_width) || formData.boat_width <= 0) {
        alert('Please enter a valid boat width');
        return false;
    }
    
    // Validate boat type specific fields
    if (formData.boat_type === 'motorized' || formData.boat_type === 'commercial') {
        if (!formData.engine_hp || isNaN(formData.engine_hp) || formData.engine_hp <= 0) {
            alert('Please enter valid engine horsepower');
            return false;
        }
    }
    
    if (formData.boat_type === 'commercial') {
        if (!formData.fishing_license || formData.fishing_license.trim() === '') {
            alert('Fishing license number is required for commercial boats');
            return false;
        }
    }
    
    return true;
}

// ==============================================
// FORM SUBMISSION
// ==============================================

/**
 * Handles Boat Registration form submission
 */
function submitBoatRForm(event) {
    event.preventDefault();
    
    const form = document.getElementById('boatr-registration-form');
    if (!form) {
        console.error('Boat Registration form not found');
        return false;
    }
    
    // Gather form data
    const formData = gatherBoatRData(form);
    
    // Validate form
    if (!validateBoatRForm(formData)) {
        return false;
    }
    
    // Show submission summary
    showBoatRSummary(formData);
    
    // Here you would typically submit to server
    console.log('Boat Registration submission data:', formData);
    
    // For demo purposes, prevent actual submission
    return false;
}

// ==============================================
// DATA COLLECTION
// ==============================================

/**
 * Gathers all data from Boat Registration form
 */
function gatherBoatRData(form) {
    return {
        // Personal Information
        first_name: form.first_name?.value?.trim() || '',
        middle_name: form.middle_name?.value?.trim() || '',
        last_name: form.last_name?.value?.trim() || '',
        suffix: form.suffix?.value?.trim() || '',
        
        // Contact Information
        mobile: form.mobile?.value?.trim() || '',
        email: form.email?.value?.trim() || '',
        
        // Address Information
        barangay: form.barangay?.value?.trim() || '',
        address: form.address?.value?.trim() || '',
        
        // Boat Information
        boat_type: form.boat_type?.value || '',
        boat_name: form.boat_name?.value?.trim() || '',
        boat_length: parseFloat(form.boat_length?.value) || 0,
        boat_width: parseFloat(form.boat_width?.value) || 0,
        boat_material: form.boat_material?.value || '',
        boat_color: form.boat_color?.value?.trim() || '',
        year_built: form.year_built?.value || '',
        
        // Engine Information (if applicable)
        engine_hp: parseFloat(form.engine_hp?.value) || 0,
        engine_brand: form.engine_brand?.value?.trim() || '',
        engine_model: form.engine_model?.value?.trim() || '',
        engine_serial: form.engine_serial?.value?.trim() || '',
        
        // Commercial Information (if applicable)
        gross_tonnage: parseFloat(form.gross_tonnage?.value) || 0,
        fishing_license: form.fishing_license?.value?.trim() || '',
        
        // Usage Information
        primary_use: form.primary_use?.value || '',
        fishing_area: form.fishing_area?.value?.trim() || '',
        
        // Supporting Documents
        supporting_docs: form.supporting_docs?.files || null,
        
        timestamp: new Date().toISOString()
    };
}

// ==============================================
// SUMMARY DISPLAY
// ==============================================

/**
 * Shows submission summary for Boat Registration
 */
function showBoatRSummary(formData) {
    let summary = '=== BOAT REGISTRATION APPLICATION ===\n\n';
    
    summary += 'Personal Information:\n';
    summary += `Name: ${formData.first_name} ${formData.middle_name} ${formData.last_name} ${formData.suffix}`.trim() + '\n';
    summary += `Mobile: ${formData.mobile}\n`;
    if (formData.email) summary += `Email: ${formData.email}\n`;
    
    summary += '\nAddress Information:\n';
    summary += `Barangay: ${formData.barangay}\n`;
    summary += `Address: ${formData.address}\n`;
    
    summary += '\nBoat Information:\n';
    summary += `Boat Type: ${formData.boat_type}\n`;
    summary += `Boat Name: ${formData.boat_name}\n`;
    summary += `Dimensions: ${formData.boat_length}m x ${formData.boat_width}m\n`;
    if (formData.boat_material) summary += `Material: ${formData.boat_material}\n`;
    if (formData.boat_color) summary += `Color: ${formData.boat_color}\n`;
    if (formData.year_built) summary += `Year Built: ${formData.year_built}\n`;
    
    if (formData.boat_type === 'motorized' || formData.boat_type === 'commercial') {
        summary += '\nEngine Information:\n';
        summary += `Engine: ${formData.engine_hp} HP\n`;
        if (formData.engine_brand) summary += `Brand: ${formData.engine_brand}\n`;
        if (formData.engine_model) summary += `Model: ${formData.engine_model}\n`;
        if (formData.engine_serial) summary += `Serial: ${formData.engine_serial}\n`;
    }
    
    if (formData.boat_type === 'commercial') {
        summary += '\nCommercial Information:\n';
        if (formData.gross_tonnage) summary += `Gross Tonnage: ${formData.gross_tonnage}\n`;
        summary += `Fishing License: ${formData.fishing_license}\n`;
    }
    
    summary += '\nUsage Information:\n';
    if (formData.primary_use) summary += `Primary Use: ${formData.primary_use}\n`;
    if (formData.fishing_area) summary += `Fishing Area: ${formData.fishing_area}\n`;
    
    if (formData.supporting_docs && formData.supporting_docs.length > 0) {
        summary += `\nSupporting Documents: ${formData.supporting_docs.length} file(s) attached\n`;
    }
    
    summary += '\nNote: On-site inspection will be scheduled after application review.\n';
    
    alert(summary);
}

// ==============================================
// UTILITY FUNCTIONS
// ==============================================

/**
 * Resets Boat Registration form
 */
function resetBoatRForm() {
    const form = document.getElementById('boatr-registration-form');
    if (form) {
        form.reset();
        
        // Reset dynamic fields
        const engineFields = document.getElementById('engine-fields');
        const commercialFields = document.getElementById('commercial-fields');
        if (engineFields) engineFields.style.display = 'none';
        if (commercialFields) commercialFields.style.display = 'none';
        
        // Reset document upload state
        manageDocumentUpload();
        
        console.log('Boat Registration form reset');
    }
}

/**
 * Auto-fills form with sample data (for testing)
 */
function fillSampleBoatRData() {
    const form = document.getElementById('boatr-registration-form');
    if (form) {
        if (form.first_name) form.first_name.value = 'Captain';
        if (form.middle_name) form.middle_name.value = 'Sea';
        if (form.last_name) form.last_name.value = 'Navigator';
        if (form.mobile) form.mobile.value = '09123456789';
        if (form.barangay) form.barangay.value = 'Barangay Port';
        if (form.address) form.address.value = 'Harbor Area, Sample City';
        if (form.boat_type) form.boat_type.value = 'motorized';
        if (form.boat_name) form.boat_name.value = 'Sea Explorer';
        if (form.boat_length) form.boat_length.value = '8.5';
        if (form.boat_width) form.boat_width.value = '2.5';
        if (form.engine_hp) form.engine_hp.value = '40';
        if (form.engine_brand) form.engine_brand.value = 'Yamaha';
        
        // Trigger boat type change
        if (form.boat_type) {
            handleBoatTypeChange(form.boat_type);
        }
        
        console.log('Sample data filled for Boat Registration form');
    }
}

/**
 * Gets boat registration statistics (placeholder for future implementation)
 */
function getBoatRStats() {
    // This would typically fetch from server
    return {
        totalRegistrations: 0,
        pendingInspections: 0,
        approvedToday: 0
    };
}

// ==============================================
// INITIALIZATION
// ==============================================

/**
 * Initialize Boat Registration module
 */
function initializeBoatRModule() {
    console.log('Boat Registration module initialized');
    
    // Set up document upload restrictions
    manageDocumentUpload();
    
    // Initialize boat type field if it exists
    const boatTypeSelect = document.getElementById('boat_type');
    if (boatTypeSelect) {
        handleBoatTypeChange(boatTypeSelect);
    }
}

/**
 * Initialize boat registration features (called from landing.js)
 */
function initializeBoatRegistration() {
    const uploadInput = document.querySelector('#boatr-form-tab input[type="file"]');
    if (uploadInput) {
        uploadInput.disabled = true;
        uploadInput.title = "Upload disabled - for admin use only after on-site inspection";
    }
}

// ==============================================
// UTILITY FUNCTIONS (these should be imported from landing.js)
// ==============================================

// Note: These functions should remain in landing.js and be accessible globally
// - hideAllMainSections()
// - showAllMainSections() 
// - hideAllForms()
// - activateApplicationTab(formId)

console.log('Boat Registration module loaded successfully');