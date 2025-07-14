// ==============================================
// FISH REGISTRATION MODULE - Extracted from landing.js
// Fisheries Registration and Management System
// ==============================================

// ==============================================
// MAIN NAVIGATION FUNCTIONS
// ==============================================

/**
 * Opens the Fish Registration form
 */
function openFormFishR(event) {
    event.preventDefault();
    hideAllMainSections();
    hideAllForms();

    const formElement = document.getElementById('fishr-form');
    if (formElement) {
        formElement.style.display = 'block';
        activateApplicationTab('fishr-form');
    } else {
        console.error('Fish Registration form not found');
        return;
    }

    window.scrollTo({ top: 0, behavior: 'smooth' });
    history.pushState(null, '', '/services/fishr');
}

/**
 * Closes Fish Registration form and returns to main services
 */
function closeFormFishR() {
    const formElement = document.getElementById('fishr-form');
    if (formElement) formElement.style.display = 'none';
    
    showAllMainSections();
    window.scrollTo({ top: 0, behavior: 'smooth' });
    history.pushState(null, '', '/services');
}

// ==============================================
// LIVELIHOOD MANAGEMENT
// ==============================================

/**
 * Handles livelihood selection and shows/hides related fields
 */
function toggleOtherLivelihood(select) {
    if (!select) {
        console.error('Select element not provided');
        return;
    }

    const otherField = document.getElementById('other-livelihood-field');
    const selectedValue = select.value;

    // Show/hide "Other" livelihood input field
    if (otherField) {
        otherField.style.display = selectedValue === 'others' ? 'block' : 'none';
        
        // Clear the input when hiding
        if (selectedValue !== 'others') {
            const otherInput = otherField.querySelector('input');
            if (otherInput) otherInput.value = '';
        }
    }

    // Handle supporting documents requirement based on livelihood type
    handleSupportingDocsRequirement(selectedValue);
    
    // Log for debugging
    console.log('Fish Registration - Livelihood changed to:', selectedValue);
}

/**
 * Manages supporting documents requirement based on livelihood type
 */
function handleSupportingDocsRequirement(livelihoodType) {
    const docsInput = document.getElementById('fishr-docs');
    const docsLabel = document.querySelector('label[for="fishr-docs"]');
    
    if (docsInput) {
        if (livelihoodType === 'capture') {
            // Capture fishing doesn't require supporting documents
            docsInput.removeAttribute('required');
            if (docsLabel) {
                docsLabel.innerHTML = 'Supporting Documents (Optional)';
                docsLabel.style.color = '';
            }
        } else {
            // Other livelihood types require supporting documents
            docsInput.setAttribute('required', 'required');
            if (docsLabel) {
                docsLabel.innerHTML = 'Supporting Documents (Required) *';
                docsLabel.style.color = '#dc3545';
            }
        }
    }
}

// ==============================================
// FORM VALIDATION
// ==============================================

/**
 * Validates Fish Registration form data
 */
function validateFishRForm(formData) {
    const requiredFields = [
        'first_name',
        'last_name',
        'mobile',
        'barangay',
        'address',
        'livelihood_type'
    ];
    
    const missingFields = requiredFields.filter(field => !formData[field] || formData[field].trim() === '');
    
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
    
    // Validate livelihood-specific requirements
    if (formData.livelihood_type === 'others' && (!formData.other_livelihood || formData.other_livelihood.trim() === '')) {
        alert('Please specify your livelihood type');
        return false;
    }
    
    // Check supporting documents requirement
    if (formData.livelihood_type !== 'capture' && (!formData.supporting_docs || formData.supporting_docs.length === 0)) {
        alert('Supporting documents are required for this livelihood type');
        return false;
    }
    
    return true;
}

// ==============================================
// FORM SUBMISSION
// ==============================================

/**
 * Handles Fish Registration form submission
 */
function submitFishRForm(event) {
    event.preventDefault();
    
    const form = document.getElementById('fishr-registration-form');
    if (!form) {
        console.error('Fish Registration form not found');
        return false;
    }
    
    // Gather form data
    const formData = gatherFishRData(form);
    
    // Validate form
    if (!validateFishRForm(formData)) {
        return false;
    }
    
    // Show submission summary
    showFishRSummary(formData);
    
    // Here you would typically submit to server
    console.log('Fish Registration submission data:', formData);
    
    // For demo purposes, prevent actual submission
    return false;
}

// ==============================================
// DATA COLLECTION
// ==============================================

/**
 * Gathers all data from Fish Registration form
 */
function gatherFishRData(form) {
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
        
        // Livelihood Information
        livelihood_type: form.livelihood_type?.value || '',
        other_livelihood: form.other_livelihood?.value?.trim() || '',
        
        // Fishing Information
        fishing_area: form.fishing_area?.value?.trim() || '',
        boat_type: form.boat_type?.value || '',
        fishing_gear: Array.from(form.querySelectorAll('input[name="fishing_gear"]:checked')).map(cb => cb.value),
        years_experience: form.years_experience?.value || '',
        
        // Organization Information
        organization_member: form.organization_member?.value || '',
        organization_name: form.organization_name?.value?.trim() || '',
        
        // Supporting Documents
        supporting_docs: form.supporting_docs?.files || null,
        
        timestamp: new Date().toISOString()
    };
}

// ==============================================
// SUMMARY DISPLAY
// ==============================================

/**
 * Shows submission summary for Fish Registration
 */
function showFishRSummary(formData) {
    let summary = '=== FISH REGISTRATION APPLICATION ===\n\n';
    
    summary += 'Personal Information:\n';
    summary += `Name: ${formData.first_name} ${formData.middle_name} ${formData.last_name} ${formData.suffix}`.trim() + '\n';
    summary += `Mobile: ${formData.mobile}\n`;
    if (formData.email) summary += `Email: ${formData.email}\n`;
    
    summary += '\nAddress Information:\n';
    summary += `Barangay: ${formData.barangay}\n`;
    summary += `Address: ${formData.address}\n`;
    
    summary += '\nLivelihood Information:\n';
    if (formData.livelihood_type === 'others') {
        summary += `Livelihood Type: ${formData.other_livelihood}\n`;
    } else {
        summary += `Livelihood Type: ${formData.livelihood_type}\n`;
    }
    
    summary += '\nFishing Information:\n';
    if (formData.fishing_area) summary += `Fishing Area: ${formData.fishing_area}\n`;
    if (formData.boat_type) summary += `Boat Type: ${formData.boat_type}\n`;
    if (formData.fishing_gear.length > 0) summary += `Fishing Gear: ${formData.fishing_gear.join(', ')}\n`;
    if (formData.years_experience) summary += `Years of Experience: ${formData.years_experience}\n`;
    
    if (formData.organization_member === 'yes' && formData.organization_name) {
        summary += `\nOrganization: ${formData.organization_name}\n`;
    }
    
    if (formData.supporting_docs && formData.supporting_docs.length > 0) {
        summary += `\nSupporting Documents: ${formData.supporting_docs.length} file(s) attached\n`;
    }
    
    alert(summary);
}

// ==============================================
// UTILITY FUNCTIONS
// ==============================================

/**
 * Resets Fish Registration form
 */
function resetFishRForm() {
    const form = document.getElementById('fishr-registration-form');
    if (form) {
        form.reset();
        
        // Reset dynamic fields
        const otherField = document.getElementById('other-livelihood-field');
        if (otherField) otherField.style.display = 'none';
        
        // Reset documents requirement
        const docsInput = document.getElementById('fishr-docs');
        const docsLabel = document.querySelector('label[for="fishr-docs"]');
        if (docsInput) docsInput.removeAttribute('required');
        if (docsLabel) {
            docsLabel.innerHTML = 'Supporting Documents (Optional)';
            docsLabel.style.color = '';
        }
        
        console.log('Fish Registration form reset');
    }
}

/**
 * Auto-fills form with sample data (for testing)
 */
function fillSampleFishRData() {
    const form = document.getElementById('fishr-registration-form');
    if (form) {
        if (form.first_name) form.first_name.value = 'Pedro';
        if (form.middle_name) form.middle_name.value = 'Santos';
        if (form.last_name) form.last_name.value = 'Mangingisda';
        if (form.mobile) form.mobile.value = '09123456789';
        if (form.barangay) form.barangay.value = 'Barangay Seaside';
        if (form.address) form.address.value = 'Coastal Area, Sample City';
        if (form.livelihood_type) form.livelihood_type.value = 'aquaculture';
        if (form.fishing_area) form.fishing_area.value = 'Manila Bay';
        if (form.years_experience) form.years_experience.value = '10';
        
        // Trigger livelihood change
        if (form.livelihood_type) {
            toggleOtherLivelihood(form.livelihood_type);
        }
        
        console.log('Sample data filled for Fish Registration form');
    }
}

/**
 * Gets fish registration statistics (placeholder for future implementation)
 */
function getFishRStats() {
    // This would typically fetch from server
    return {
        totalRegistrations: 0,
        pendingApplications: 0,
        approvedToday: 0
    };
}

// ==============================================
// INITIALIZATION
// ==============================================

/**
 * Initialize Fish Registration module
 */
function initializeFishRModule() {
    console.log('Fish Registration module initialized');
    
    // Set up any initial event listeners or configurations
    const livelihoodSelect = document.getElementById('livelihood_type');
    if (livelihoodSelect) {
        // Ensure proper initialization of livelihood field
        toggleOtherLivelihood(livelihoodSelect);
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

console.log('Fish Registration module loaded successfully');