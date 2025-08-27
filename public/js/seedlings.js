// ==============================================
// SEEDLINGS MODULE - Extracted from landing.js
// ==============================================

// Global variable to store user selections
window._seedlingsChoices = null;

// ==============================================
// MAIN NAVIGATION FUNCTIONS
// ==============================================

//Opens the seedlings choice form (first step)
function openFormSeedlings(event) {
    event.preventDefault();
    hideAllMainSections();
    hideAllForms();

    const choice = document.getElementById('seedlings-choice');
    if (choice) choice.style.display = 'block';

    // Scroll to the seedlings choice form smoothly
    setTimeout(() => {
        const seedlingsChoice = document.getElementById('seedlings-choice');
        if (seedlingsChoice) {
            seedlingsChoice.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    }, 100); // Small delay to ensure form is visible
    history.pushState(null, '', '/services/seedlings');
}

//Closes seedlings forms and returns to main services

function closeFormSeedlings() {
    hideAllForms();
    showAllMainSections();
    window.scrollTo({ top: 0, behavior: 'smooth' });
    history.pushState(null, '', '/services');
}

//Goes back to seedlings choice from application form

function backToSeedlingsChoice() {
    hideAllForms();
    const choice = document.getElementById('seedlings-choice');

    if (choice) {
        choice.style.display = 'block';
        restorePreviousSelections();
    }

    // Scroll to the seedlings choice form smoothly
    setTimeout(() => {
        const choice = document.getElementById('seedlings-choice');
        if (choice) {
            choice.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    }, 100); // Small delay to ensure form is visible
    history.pushState(null, '', '/services/seedlings');
}

// ==============================================
// SELECTION MANAGEMENT
// ==============================================

//Toggles quantity input field visibility when checkbox is selected

function toggleQuantity(checkbox, quantityId) {
    const quantityDiv = document.getElementById(quantityId);
    if (quantityDiv) {
        quantityDiv.style.display = checkbox.checked ? 'flex' : 'none';
    }
}

//Processes user selections and proceeds to application form

function proceedToSeedlingsForm() {
    const form = document.getElementById('seedlings-choice-form');
    if (!form) {
        console.error('Seedlings choice form not found');
        return;
    }

    // Collect selections with quantities
    const selections = collectUserSelections(form);

    // Validate selections
    if (!validateSelections(selections)) {
        alert('Please select at least one: Vegetable Seedling, Fruit-bearing Seedling, or Organic Fertilizer.');
        return;
    }

    // Calculate total quantity
    const totalQuantity = calculateTotalQuantity(selections);
    selections.totalQuantity = totalQuantity;

    // Show summary to user
    showSelectionSummary(selections);

    // Save selections globally
    window._seedlingsChoices = selections;

    // Show application form
    showApplicationForm();
}

//Collects all user selections with quantities
function collectUserSelections(form) {
    const vegetables = [];
    const fruits = [];
    const fertilizers = [];

    // Collect vegetables
    form.querySelectorAll('input[name="vegetables"]:checked').forEach(cb => {
        const quantity = getQuantityForItem(form, cb.value);
        vegetables.push({ name: cb.value, quantity });
    });

    // Collect fruits
    form.querySelectorAll('input[name="fruits"]:checked').forEach(cb => {
        const quantity = getQuantityForItem(form, cb.value);
        fruits.push({ name: cb.value, quantity });
    });

    // Collect fertilizers
    form.querySelectorAll('input[name="fertilizers"]:checked').forEach(cb => {
        const quantity = getQuantityForItem(form, cb.value);
        fertilizers.push({ name: cb.value, quantity });
    });

    return { vegetables, fruits, fertilizers };
}

//Gets quantity for a specific item
function getQuantityForItem(form, itemName) {
    const quantityInput = form.querySelector(`input[name="${itemName.replace(/ /g, '_')}_quantity"]`);
    return quantityInput ? parseInt(quantityInput.value) || 1 : 1;
}

//Validates that at least one item is selected
function validateSelections(selections) {
    return selections.vegetables.length > 0 ||
           selections.fruits.length > 0 ||
           selections.fertilizers.length > 0;
}

//Calculates total quantity across all selections

function calculateTotalQuantity(selections) {
    return [...selections.vegetables, ...selections.fruits, ...selections.fertilizers]
        .reduce((sum, item) => sum + item.quantity, 0);
}

//Shows summary alert to user

function showSelectionSummary(selections) {
    let summary = 'You have chosen:\n\n';

    if (selections.vegetables.length) {
        summary += '🌱 Vegetable Seedlings:\n';
        selections.vegetables.forEach(v => summary += `  • ${v.name}: ${v.quantity} pcs\n`);
        summary += '\n';
    }

    if (selections.fruits.length) {
        summary += '🍎 Fruit-bearing Seedlings:\n';
        selections.fruits.forEach(f => summary += `  • ${f.name}: ${f.quantity} pcs\n`);
        summary += '\n';
    }

    if (selections.fertilizers.length) {
        summary += '🌿 Organic Fertilizers:\n';
        selections.fertilizers.forEach(fert => summary += `  • ${fert.name}: ${fert.quantity} pcs\n`);
        summary += '\n';
    }

    summary += `Total Quantity: ${selections.totalQuantity} pcs\n\n`;
    alert(summary);
}

//Shows the application form with proper setup

function showApplicationForm() {
    hideAllForms();

    const appForm = document.getElementById('seedlings-form');
    if (appForm) {
        appForm.style.display = 'block';

        // Setup supporting documents requirement
        toggleSupportingDocuments(window._seedlingsChoices.totalQuantity);

        // Show summary in form
        const summaryDiv = document.getElementById('seedlings-summary');
        if (summaryDiv) {
            summaryDiv.style.display = 'block';
            populateSeedlingsSummary();
        }

        // Activate first tab
        activateApplicationTab('seedlings-form');
    }

    // Scroll to the application form smoothly
    setTimeout(() => {
        const appForm = document.getElementById('seedlings-form');
        if (appForm) {
            appForm.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    }, 100); // Small delay to ensure form is visible
    history.pushState(null, '', '/services/seedlings/form');
}

/**
 * Main tab switching function for Seedlings form
 * This is the function your HTML onclick events are calling
 */
function showSeedlingsTab(tabId, event) {
    console.log('Switching to Seedlings tab:', tabId);

    // Prevent default button behavior
    if (event) {
        event.preventDefault();
    }

    // Get the parent section containing all tabs
    const parentSection = event.target.closest('.seedlings-application-section') ||
                         event.target.closest('#seedlings-form');
    if (!parentSection) {
        console.error('Parent section not found for Seedlings tab switching');
        return;
    }

    // Remove active class from all tab buttons
    const tabButtons = parentSection.querySelectorAll('.seedlings-tab-btn');
    tabButtons.forEach(btn => btn.classList.remove('active'));

    // Hide all tab content
    const tabContents = parentSection.querySelectorAll('.seedlings-tab-content');
    tabContents.forEach(content => content.style.display = 'none');

    // Add active class to clicked button
    if (event && event.target) {
        event.target.classList.add('active');
    }

    // Show the selected tab content
    const selectedTab = document.getElementById(tabId);
    if (selectedTab) {
        selectedTab.style.display = 'block';
        console.log('Seedlings tab switched successfully to:', tabId);
         // Auto-scroll to the active tab content
        setTimeout(() => {
            selectedTab.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest'
            });
        }, 50); // Small delay for smooth transition
    } else {
        console.error('Tab content not found:', tabId);
    }
}

// ==============================================
// SUPPORTING DOCUMENTS LOGIC
// ==============================================

//Shows/hides supporting documents field based on quantity

function toggleSupportingDocuments(totalQuantity) {
    const docsField = document.getElementById('supporting-docs-field');
    const docsInput = document.getElementById('seedlings-docs');
    const docsLabel = document.querySelector('label[for="seedlings-docs"]');
    const docsSmall = docsInput ? docsInput.nextElementSibling : null;

    if (totalQuantity >= 100) {
        // Require supporting documents for large orders
        if (docsField) docsField.style.display = 'block';
        if (docsInput) docsInput.setAttribute('required', 'required');
        if (docsLabel) docsLabel.innerHTML = 'Supporting Documents (Required)';
        if (docsSmall) {
            docsSmall.innerHTML = 'Required: Proof of planting area (land title, lease agreement, barangay certification of available land, photos of planting area, etc.). Multiple files allowed.';
        }
    } else {
        // Hide for smaller orders
        if (docsField) docsField.style.display = 'none';
        if (docsInput) docsInput.removeAttribute('required');
    }
}

// ==============================================
// SUMMARY DISPLAY
// ==============================================

//Populates the summary section in the application form

function populateSeedlingsSummary() {
    const summaryContainer = document.getElementById('seedlings-summary');
    if (!summaryContainer || !window._seedlingsChoices) return;

    let summaryHTML = '<h3 style="color: #40916c; margin-bottom: 15px;">📋 Your Selected Items:</h3>';

    // Add vegetables section
    if (window._seedlingsChoices.vegetables.length > 0) {
        summaryHTML += buildSummarySection('🌱 Vegetable Seedlings:', window._seedlingsChoices.vegetables);
    }

    // Add fruits section
    if (window._seedlingsChoices.fruits.length > 0) {
        summaryHTML += buildSummarySection('🍎 Fruit-bearing Seedlings:', window._seedlingsChoices.fruits);
    }

    // Add fertilizers section
    if (window._seedlingsChoices.fertilizers.length > 0) {
        summaryHTML += buildSummarySection('🌿 Organic Fertilizers:', window._seedlingsChoices.fertilizers);
    }

    // Add total quantity display
    summaryHTML += buildTotalQuantitySection();

    summaryContainer.innerHTML = summaryHTML;
}

//Builds HTML for a summary section

function buildSummarySection(title, items) {
    let html = `<div style="margin-bottom: 15px;"><strong style="color: #2d6a4f;">${title}</strong>`;
    html += '<ul style="margin: 8px 0; padding-left: 20px;">';

    items.forEach(item => {
        html += `<li style="margin: 4px 0;">${item.name} - <span style="color: #40916c; font-weight: bold;">${item.quantity} pcs</span></li>`;
    });

    html += '</ul></div>';
    return html;
}

//Builds the total quantity section

function buildTotalQuantitySection() {
    let html = '<div style="margin-top: 20px; padding: 15px; background-color: #e8f5e8; border-radius: 8px; border-left: 4px solid #40916c;">';
    html += `<strong style="color: #2d6a4f;">Total Quantity: <span style="color: #40916c; font-size: 1.2em;">${window._seedlingsChoices.totalQuantity} pcs</span></strong>`;
    html += '</div>';
    return html;
}

// ==============================================
// STATE RESTORATION
// ==============================================

// Restores previous selections when going back

function restorePreviousSelections() {
    if (!window._seedlingsChoices) return;

    const form = document.getElementById('seedlings-choice-form');
    if (!form) return;

    // Restore vegetables
    restoreItemSelections(form, 'vegetables', window._seedlingsChoices.vegetables);

    // Restore fruits
    restoreItemSelections(form, 'fruits', window._seedlingsChoices.fruits);

    // Restore fertilizers
    restoreItemSelections(form, 'fertilizers', window._seedlingsChoices.fertilizers);
}

//Restores selections for a specific item type

function restoreItemSelections(form, itemType, items) {
    items.forEach(item => {
        const checkbox = form.querySelector(`input[name="${itemType}"][value="${item.name}"]`);
        if (checkbox) {
            checkbox.checked = true;

            // Show quantity field and restore value
            const quantityId = item.name.replace(/ /g, '-') + '-qty';
            toggleQuantity(checkbox, quantityId);

            const quantityInput = form.querySelector(`input[name="${item.name.replace(/ /g, '_')}_quantity"]`);
            if (quantityInput) quantityInput.value = item.quantity;
        }
    });
}

// ==============================================
// FORM SUBMISSION
// ==============================================

//Handles seedlings request form submission

// ==============================================
// ENHANCED FORM SUBMISSION WITH COMPLETE RESET
// ==============================================

// Handles seedlings request form submission with complete reset
function submitSeedlingsRequest(event) {
    event.preventDefault();

    const form = document.getElementById('seedlings-request-form');
    if (!form) {
        console.error('Seedlings request form not found');
        return false;
    }

    // Show loading state
    const submitBtn = form.querySelector('.seedlings-submit-btn');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Submitting...';
    submitBtn.disabled = true;

    // Prepare form data
    const formData = new FormData(form);

    // Add selected seedlings data
    if (window._seedlingsChoices) {
        formData.append('selected_seedlings', JSON.stringify(window._seedlingsChoices));
    }

    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Submit via AJAX
    fetch('/apply/seedlings', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            alert('✅ ' + data.message);

            // COMPLETE RESET FOR NEXT USER
            performCompleteReset();

            // Return to main services page
            closeFormSeedlings();
        } else {
            // Show error message
            alert('❌ ' + (data.message || 'There was an error submitting your request.'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ There was an error submitting your request. Please try again.');
    })
    .finally(() => {
        // Reset button state
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
}
// ==============================================
// COMPLETE RESET FUNCTION
// ==============================================

function performCompleteReset() {
    // 1. Reset global seedlings choices
    window._seedlingsChoices = null;

    // 2. Reset the main application form with enhanced field handling
    const applicationForm = document.getElementById('seedlings-request-form');
    if (applicationForm) {
        applicationForm.reset();

        // Manually reset select fields (especially barangay)
        applicationForm.querySelectorAll('select').forEach(select => {
            select.selectedIndex = 0;
            select.value = '';
        });

        // Special handling for barangay field
        const barangaySelect = applicationForm.querySelector('select[name="barangay"]');
        if (barangaySelect) {
            barangaySelect.selectedIndex = 0;
            barangaySelect.value = '';
            barangaySelect.dispatchEvent(new Event('change'));
        }
    }

    // 3. Reset the choice form (checkboxes and quantities)
    resetChoiceForm();

    // 4. Clear summary display
    clearSummaryDisplay();

    // 5. Reset supporting documents field
    resetSupportingDocuments();

    // 6. Reset any file inputs
    resetFileInputs();

    console.log('Complete seedlings form reset performed');
}

// ==============================================
// INDIVIDUAL RESET FUNCTIONS
// ==============================================

function resetChoiceForm() {
    const choiceForm = document.getElementById('seedlings-choice-form');
    if (!choiceForm) return;

    // Reset all checkboxes
    choiceForm.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = false;
    });

    // Reset all quantity inputs and hide quantity fields
    choiceForm.querySelectorAll('input[type="number"]').forEach(input => {
        input.value = '';
    });

    // Hide all quantity divs
    choiceForm.querySelectorAll('[id$="-qty"]').forEach(qtyDiv => {
        qtyDiv.style.display = 'none';
    });

    // Reset the entire form to be safe
    choiceForm.reset();
}

function clearSummaryDisplay() {
    const summaryContainer = document.getElementById('seedlings-summary');
    if (summaryContainer) {
        summaryContainer.innerHTML = '';
        summaryContainer.style.display = 'none';
    }
}

function resetSupportingDocuments() {
    const docsField = document.getElementById('supporting-docs-field');
    const docsInput = document.getElementById('seedlings-docs');
    const docsLabel = document.querySelector('label[for="seedlings-docs"]');
    const docsSmall = docsInput ? docsInput.nextElementSibling : null;

    // Hide the supporting documents field
    if (docsField) {
        docsField.style.display = 'none';
    }

    // Remove required attribute
    if (docsInput) {
        docsInput.removeAttribute('required');
        docsInput.value = ''; // Clear any selected files
    }

    // Reset label text
    if (docsLabel) {
        docsLabel.innerHTML = 'Supporting Documents';
    }

    // Reset help text
    if (docsSmall) {
        docsSmall.innerHTML = 'Upload supporting documents if required.';
    }
}

function resetFileInputs() {
    // Reset all file inputs in the forms
    document.querySelectorAll('#seedlings-form input[type="file"], #seedlings-choice input[type="file"]').forEach(fileInput => {
        fileInput.value = '';

        // If there's a custom file display, reset it too
        const fileDisplay = fileInput.parentElement.querySelector('.file-display');
        if (fileDisplay) {
            fileDisplay.innerHTML = '';
        }
    });
}

// ==============================================
// ENHANCED CLOSE FUNCTION
// ==============================================

// Enhanced close function that also performs reset
function closeFormSeedlings() {
    // Perform complete reset when closing
    performCompleteReset();

    // Hide all forms and show main sections
    hideAllForms();
    showAllMainSections();

    // Scroll to top and update URL
    window.scrollTo({ top: 0, behavior: 'smooth' });
    history.pushState(null, '', '/services');
}
// ==============================================
// BROWSER REFRESH/NAVIGATION RESET
// ==============================================

// Reset data when page loads (in case of browser refresh)
document.addEventListener('DOMContentLoaded', function() {
    // Reset on page load to ensure clean state
    window._seedlingsChoices = null;

    // Set up form submission handler
    const form = document.getElementById('seedlings-request-form');
    if (form) {
        form.addEventListener('submit', submitSeedlingsRequest);
    }

    // Handle browser back/forward buttons
    window.addEventListener('popstate', function() {
        // Reset data when navigating with browser buttons
        performCompleteReset();
    });
});

// ==============================================
// ADDITIONAL RESET TRIGGER FUNCTIONS
// ==============================================

// Function to manually trigger reset (useful for testing or other scenarios)
function manualReset() {
    performCompleteReset();
    console.log('Manual reset performed');
}

// Reset when starting a new application
function openFormSeedlings(event) {
    event.preventDefault();

    // Perform reset before opening form
    performCompleteReset();

    hideAllMainSections();
    hideAllForms();

    const choice = document.getElementById('seedlings-choice');
    if (choice) choice.style.display = 'block';

    // Scroll to the seedlings choice form smoothly
    setTimeout(() => {
        const choice = document.getElementById('seedlings-choice');
        if (choice) {
            choice.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    }, 100); // Small delay to ensure form is visible
    history.pushState(null, '', '/services/seedlings');
}

// Make sure the form calls this function
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('seedlings-request-form');
    if (form) {
        form.addEventListener('submit', submitSeedlingsRequest);
    }
});



// Gathers all form data
function gatherFormData(form) {
    return {
        firstName: form.first_name?.value || '',
        middleName: form.middle_name?.value || '',
        lastName: form.last_name?.value || '',
        mobile: form.mobile?.value || '',
        barangay: form.barangay?.value || '',
        address: form.address?.value || '',
        selections: window._seedlingsChoices
    };
}

//Shows final summary before submission

function showFinalSummary(formData) {
    const { selections } = formData;
    let summary = 'You have chosen:\n';

    if (selections.vegetables.length) {
        summary += '- Vegetable Seedlings: ' + selections.vegetables.map(v => `${v.name} (${v.quantity})`).join(', ') + '\n';
    }
    if (selections.fruits.length) {
        summary += '- Fruit-bearing Seedlings: ' + selections.fruits.map(f => `${f.name} (${f.quantity})`).join(', ') + '\n';
    }
    if (selections.fertilizers.length) {
        summary += '- Organic Fertilizers: ' + selections.fertilizers.map(fert => `${fert.name} (${fert.quantity})`).join(', ') + '\n';
    }

    summary += '\nApplicant Details:\n';
    summary += `Name: ${formData.firstName} ${formData.middleName} ${formData.lastName}\n`;
    summary += `Mobile: ${formData.mobile}\nBarangay: ${formData.barangay}\nAddress: ${formData.address}`;

    alert(summary);
}

// ==============================================
// UTILITY FUNCTIONS (these should be imported from landing.js)
// ==============================================

// Note: These functions should remain in landing.js and be accessible globally
// - hideAllMainSections()
// - showAllMainSections()
// - hideAllForms()
// - activateApplicationTab(formId)

// ==============================================
// GLOBAL FUNCTIONS FOR COMPATIBILITY
// ==============================================

// Make functions available globally for HTML onclick handlers
window.openFormSeedlings = openFormSeedlings;
window.closeFormSeedlings = closeFormSeedlings;
window.proceedToSeedlingsForm = proceedToSeedlingsForm;
window.showSeedlingsTab = showSeedlingsTab;
window.backToSeedlingsChoice = backToSeedlingsChoice;

console.log('Seedlings module loaded successfully');
