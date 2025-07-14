// ==============================================
// RSBSA MODULE - Extracted from landing.js
// Registry System for Basic Sectors in Agriculture
// ==============================================

// ==============================================
// MAIN NAVIGATION FUNCTIONS
// ==============================================

/**
 * Opens the RSBSA choice form (New vs Old/Update)
 */
function openFormRSBSA(event) {
    event.preventDefault();
    hideAllMainSections();
    hideAllForms();
    
    const choice = document.getElementById('rsbsa-choice');
    if (choice) {
        choice.style.display = 'block';
    } else {
        console.error('RSBSA choice form not found');
        return;
    }
    
    window.scrollTo({ top: 0, behavior: 'smooth' });
    history.pushState(null, '', '/services/rsbsa');
}

/**
 * Opens the New RSBSA Registration form
 */
function openNewRSBSA() {
    hideAllForms();
    
    const form = document.getElementById('new-rsbsa');
    if (form) {
        form.style.display = 'block';
        activateApplicationTab('new-rsbsa');
    } else {
        console.error('New RSBSA form not found');
        return;
    }
    
    window.scrollTo({ top: 0, behavior: 'smooth' });
    history.pushState(null, '', '/services/rsbsa/new');
}

/**
 * Opens the Old/Update RSBSA Registration form
 */
function openOldRSBSA() {
    hideAllForms();
    
    const form = document.getElementById('old-rsbsa');
    if (form) {
        form.style.display = 'block';
        activateApplicationTab('old-rsbsa');
    } else {
        console.error('Old RSBSA form not found');
        return;
    }
    
    window.scrollTo({ top: 0, behavior: 'smooth' });
    history.pushState(null, '', '/services/rsbsa/old');
}

/**
 * Closes RSBSA forms and returns to main services
 */
function closeFormRSBSA() {
    hideAllForms();
    showAllMainSections();
    window.scrollTo({ top: 0, behavior: 'smooth' });
    history.pushState(null, '', '/services');
}

/**
 * Goes back to RSBSA choice from application forms
 */
function backToRSBSAChoice() {
    hideAllForms();
    
    const choice = document.getElementById('rsbsa-choice');
    if (choice) {
        choice.style.display = 'block';
    } else {
        console.error('RSBSA choice form not found');
        return;
    }
    
    window.scrollTo({ top: 0, behavior: 'smooth' });
    history.pushState(null, '', '/services/rsbsa');
}

// ==============================================
// FORM VALIDATION FUNCTIONS
// ==============================================

/**
 * Validates New RSBSA Registration form
 */
function validateNewRSBSAForm(formData) {
    const requiredFields = [
        'first_name',
        'last_name', 
        'mobile',
        'barangay',
        'address'
    ];
    
    const missingFields = requiredFields.filter(field => !formData[field] || formData[field].trim() === '');
    
    if (missingFields.length > 0) {
        alert(`Please fill in the following required fields: ${missingFields.join(', ')}`);
        return false;
    }
    
    // Validate mobile number format (basic validation)
    const mobilePattern = /^(09|\+639)\d{9}$/;
    if (!mobilePattern.test(formData.mobile.replace(/\s+/g, ''))) {
        alert('Please enter a valid mobile number (e.g., 09123456789)');
        return false;
    }
    
    return true;
}

/**
 * Validates Old/Update RSBSA form
 */
function validateOldRSBSAForm(formData) {
    const requiredFields = [
        'rsbsa_number',
        'first_name',
        'last_name',
        'mobile'
    ];
    
    const missingFields = requiredFields.filter(field => !formData[field] || formData[field].trim() === '');
    
    if (missingFields.length > 0) {
        alert(`Please fill in the following required fields: ${missingFields.join(', ')}`);
        return false;
    }
    
    // Validate RSBSA number format (adjust pattern as needed)
    const rsbsaPattern = /^[A-Z0-9-]{10,20}$/;
    if (!rsbsaPattern.test(formData.rsbsa_number.replace(/\s+/g, ''))) {
        alert('Please enter a valid RSBSA number');
        return false;
    }
    
    return true;
}

// ==============================================
// FORM SUBMISSION FUNCTIONS
// ==============================================

/**
 * Handles New RSBSA form submission
 */
function submitNewRSBSA(event) {
    event.preventDefault();
    
    const form = document.getElementById('new-rsbsa-form');
    if (!form) {
        console.error('New RSBSA form not found');
        return false;
    }
    
    // Gather form data
    const formData = gatherNewRSBSAData(form);
    
    // Validate form
    if (!validateNewRSBSAForm(formData)) {
        return false;
    }
    
    // Show submission summary
    showNewRSBSASummary(formData);
    
    // Here you would typically submit to server
    console.log('New RSBSA submission data:', formData);
    
    // For demo purposes, prevent actual submission
    return false;
}

/**
 * Handles Old/Update RSBSA form submission
 */
function submitOldRSBSA(event) {
    event.preventDefault();
    
    const form = document.getElementById('old-rsbsa-form');
    if (!form) {
        console.error('Old RSBSA form not found');
        return false;
    }
    
    // Gather form data
    const formData = gatherOldRSBSAData(form);
    
    // Validate form
    if (!validateOldRSBSAForm(formData)) {
        return false;
    }
    
    // Show submission summary
    showOldRSBSASummary(formData);
    
    // Here you would typically submit to server
    console.log('Old RSBSA submission data:', formData);
    
    // For demo purposes, prevent actual submission
    return false;
}

// ==============================================
// DATA COLLECTION FUNCTIONS
// ==============================================

/**
 * Gathers data from New RSBSA form
 */
function gatherNewRSBSAData(form) {
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
        
        // Farm Information
        farm_location: form.farm_location?.value?.trim() || '',
        farm_area: form.farm_area?.value?.trim() || '',
        crops: Array.from(form.querySelectorAll('input[name="crops"]:checked')).map(cb => cb.value),
        
        // Supporting Documents
        supporting_docs: form.supporting_docs?.files || null,
        
        timestamp: new Date().toISOString()
    };
}

/**
 * Gathers data from Old/Update RSBSA form
 */
function gatherOldRSBSAData(form) {
    return {
        // RSBSA Information
        rsbsa_number: form.rsbsa_number?.value?.trim() || '',
        
        // Personal Information
        first_name: form.first_name?.value?.trim() || '',
        middle_name: form.middle_name?.value?.trim() || '',
        last_name: form.last_name?.value?.trim() || '',
        suffix: form.suffix?.value?.trim() || '',
        
        // Contact Information
        mobile: form.mobile?.value?.trim() || '',
        email: form.email?.value?.trim() || '',
        
        // Updated Information
        new_address: form.new_address?.value?.trim() || '',
        new_farm_location: form.new_farm_location?.value?.trim() || '',
        new_farm_area: form.new_farm_area?.value?.trim() || '',
        updated_crops: Array.from(form.querySelectorAll('input[name="updated_crops"]:checked')).map(cb => cb.value),
        
        // Update Reason
        update_reason: form.update_reason?.value?.trim() || '',
        
        // Supporting Documents
        supporting_docs: form.supporting_docs?.files || null,
        
        timestamp: new Date().toISOString()
    };
}

// ==============================================
// SUMMARY DISPLAY FUNCTIONS
// ==============================================

/**
 * Shows summary for New RSBSA submission
 */
function showNewRSBSASummary(formData) {
    let summary = '=== NEW RSBSA REGISTRATION ===\n\n';
    
    summary += 'Personal Information:\n';
    summary += `Name: ${formData.first_name} ${formData.middle_name} ${formData.last_name} ${formData.suffix}`.trim() + '\n';
    summary += `Mobile: ${formData.mobile}\n`;
    if (formData.email) summary += `Email: ${formData.email}\n`;
    
    summary += '\nAddress Information:\n';
    summary += `Barangay: ${formData.barangay}\n`;
    summary += `Address: ${formData.address}\n`;
    
    summary += '\nFarm Information:\n';
    if (formData.farm_location) summary += `Farm Location: ${formData.farm_location}\n`;
    if (formData.farm_area) summary += `Farm Area: ${formData.farm_area}\n`;
    if (formData.crops.length > 0) summary += `Crops: ${formData.crops.join(', ')}\n`;
    
    if (formData.supporting_docs && formData.supporting_docs.length > 0) {
        summary += `\nSupporting Documents: ${formData.supporting_docs.length} file(s) attached\n`;
    }
    
    alert(summary);
}

/**
 * Shows summary for Old/Update RSBSA submission
 */
function showOldRSBSASummary(formData) {
    let summary = '=== RSBSA UPDATE/RENEWAL ===\n\n';
    
    summary += `RSBSA Number: ${formData.rsbsa_number}\n\n`;
    
    summary += 'Personal Information:\n';
    summary += `Name: ${formData.first_name} ${formData.middle_name} ${formData.last_name} ${formData.suffix}`.trim() + '\n';
    summary += `Mobile: ${formData.mobile}\n`;
    if (formData.email) summary += `Email: ${formData.email}\n`;
    
    summary += '\nUpdated Information:\n';
    if (formData.new_address) summary += `New Address: ${formData.new_address}\n`;
    if (formData.new_farm_location) summary += `New Farm Location: ${formData.new_farm_location}\n`;
    if (formData.new_farm_area) summary += `New Farm Area: ${formData.new_farm_area}\n`;
    if (formData.updated_crops.length > 0) summary += `Updated Crops: ${formData.updated_crops.join(', ')}\n`;
    
    if (formData.update_reason) summary += `\nReason for Update: ${formData.update_reason}\n`;
    
    if (formData.supporting_docs && formData.supporting_docs.length > 0) {
        summary += `\nSupporting Documents: ${formData.supporting_docs.length} file(s) attached\n`;
    }
    
    alert(summary);
}

// ==============================================
// UTILITY FUNCTIONS
// ==============================================

/**
 * Resets New RSBSA form
 */
function resetNewRSBSAForm() {
    const form = document.getElementById('new-rsbsa-form');
    if (form) {
        form.reset();
        console.log('New RSBSA form reset');
    }
}

/**
 * Resets Old/Update RSBSA form
 */
function resetOldRSBSAForm() {
    const form = document.getElementById('old-rsbsa-form');
    if (form) {
        form.reset();
        console.log('Old RSBSA form reset');
    }
}

/**
 * Auto-fills RSBSA form with sample data (for testing)
 */
function fillSampleRSBSAData(formType = 'new') {
    if (formType === 'new') {
        const form = document.getElementById('new-rsbsa-form');
        if (form) {
            if (form.first_name) form.first_name.value = 'Juan';
            if (form.middle_name) form.middle_name.value = 'Santos';
            if (form.last_name) form.last_name.value = 'Cruz';
            if (form.mobile) form.mobile.value = '09123456789';
            if (form.barangay) form.barangay.value = 'Sample Barangay';
            if (form.address) form.address.value = 'Sample Address';
            console.log('Sample data filled for New RSBSA form');
        }
    } else if (formType === 'old') {
        const form = document.getElementById('old-rsbsa-form');
        if (form) {
            if (form.rsbsa_number) form.rsbsa_number.value = 'RSBSA-2024-001';
            if (form.first_name) form.first_name.value = 'Maria';
            if (form.last_name) form.last_name.value = 'Garcia';
            if (form.mobile) form.mobile.value = '09987654321';
            console.log('Sample data filled for Old RSBSA form');
        }
    }
}

// ==============================================
// INITIALIZATION
// ==============================================

/**
 * Initialize RSBSA module
 */
function initializeRSBSAModule() {
    console.log('RSBSA module initialized');
    
    // Add any initialization logic here
    // For example, setting up event listeners for specific RSBSA form elements
}

// ==============================================
// UTILITY FUNCTIONS (these should be imported from landing.js)
// ==============================================

// Note: These functions should remain in landing.js and be accessible globally
// - hideAllMainSections()
// - showAllMainSections() 
// - hideAllForms()
// - activateApplicationTab(formId)

console.log('RSBSA module loaded successfully');