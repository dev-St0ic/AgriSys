// ==============================================
// SEEDLINGS MODULE - Dynamic Categories Support 
// ==============================================

// Global variable to store user selections
window._seedlingsChoices = null;

// ==============================================
// MAIN NAVIGATION FUNCTIONS
// ==============================================

// Opens the seedlings choice form (first step)
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

// Closes seedlings forms and returns to main services
function closeFormSeedlings() {
    // Perform complete reset when closing
    performCompleteReset();

    hideAllForms();
    showAllMainSections();
    window.scrollTo({ top: 0, behavior: 'smooth' });
    history.pushState(null, '', '/services');
}

// Goes back to seedlings choice from application form
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
// SELECTION MANAGEMENT - UPDATED FOR DYNAMIC
// ==============================================

function collectUserSelections(form) {
    const selections = {};
    
    // Get all category checkboxes (grouped by name attribute)
    form.querySelectorAll('input[type="checkbox"]:checked').forEach(cb => {
        const categoryName = cb.name;
        const itemId = cb.dataset.itemId;
        const itemName = cb.value;
        
        // Find quantity input using the itemId
        const quantityInput = form.querySelector(`input[name="quantity_${itemId}"]`);
        const quantity = quantityInput ? parseInt(quantityInput.value) || 1 : 1;
        
        // Initialize category array if not exists
        if (!selections[categoryName]) {
            selections[categoryName] = [];
        }
        
        // Add item to category
        selections[categoryName].push({
            id: itemId,
            name: itemName,
            quantity: quantity
        });
    });
    
    return selections;
}

function validateSelections(selections) {
    // Check if any category has items selected
    return Object.values(selections).some(items => items.length > 0);
}

function calculateTotalQuantity(selections) {
    return Object.values(selections).reduce((total, items) => {
        return total + items.reduce((sum, item) => sum + item.quantity, 0);
    }, 0);
}

function showSelectionSummary(selections) {
    let summary = 'You have chosen:\n\n';
    
    const categoryIcons = {
        seeds: '🌾',
        seedlings: '🌱',
        fruits: '🍎',
        ornamentals: '🌺',
        fingerlings: '🐟',
        fertilizers: '🌿'
        // Add more category icons as needed
    };
    
    Object.entries(selections).forEach(([category, items]) => {
        if (items.length > 0) {
            const icon = categoryIcons[category] || '📦';
            const categoryDisplay = category.charAt(0).toUpperCase() + category.slice(1);
            summary += `${icon} ${categoryDisplay}:\n`;
            items.forEach(item => {
                summary += `  • ${item.name}: ${item.quantity} units\n`;
            });
            summary += '\n';
        }
    });
    
    const totalQuantity = calculateTotalQuantity(selections);
    summary += `Total Items: ${totalQuantity} units\n`;
    alert(summary);
}

function populateSeedlingsSummary() {
    const summaryContainer = document.getElementById('seedlings-summary');
    if (!summaryContainer || !window._seedlingsChoices) return;

    let summaryHTML = '<h3 style="color: #40916c; margin-bottom: 15px;">📋 Your Selected Items:</h3>';

    // Category icons mapping
    const categoryIcons = {
        seeds: '🌾',
        seedlings: '🌱',
        fruits: '🍎',
        ornamentals: '🌺',
        fingerlings: '🐟',
        fertilizers: '🌿'
        // Add more category icons as needed
    };

    Object.entries(window._seedlingsChoices.selections).forEach(([category, items]) => {
        if (items.length > 0) {
            const icon = categoryIcons[category] || '📦';
            const categoryDisplay = category.charAt(0).toUpperCase() + category.slice(1);
            summaryHTML += buildSummarySection(`${icon} ${categoryDisplay}:`, items);
        }
    });

    // Add total quantity section
    summaryHTML += buildTotalQuantitySection(window._seedlingsChoices.totalQuantity);
    summaryContainer.innerHTML = summaryHTML;
}

// Toggle quantity field visibility
function toggleQuantity(checkbox, quantityId) {
    const quantityControl = document.getElementById(quantityId);
    if (quantityControl) {
        if (checkbox.checked) {
            quantityControl.style.display = 'flex';
        } else {
            quantityControl.style.display = 'none';
        }
    }
}

function buildSummarySection(title, items) {
    let html = `<div style="margin-bottom: 15px;"><strong style="color: #2d6a4f;">${title}</strong>`;
    html += '<ul style="margin: 8px 0; padding-left: 20px;">';
    
    items.forEach(item => {
        html += `<li style="margin: 4px 0;">${item.name} - <span style="color: #40916c; font-weight: bold;">${item.quantity} units</span></li>`;
    });
    
    html += '</ul></div>';
    return html;
}

function buildTotalQuantitySection(totalQuantity) {
    return `
        <div style="margin-top: 20px; padding: 15px; background-color: #e8f5e8; border-radius: 8px; border-left: 4px solid #40916c;">
            <strong style="color: #2d6a4f;">Total Quantity: 
                <span style="color: #40916c; font-size: 1.2em;">${totalQuantity} units</span>
            </strong>
        </div>
    `;
}

// ==============================================
// UPDATED PROCEED FUNCTION
// ==============================================

function proceedToSeedlingsForm() {
    const form = document.getElementById('seedlings-choice-form');
    if (!form) {
        console.error('Seedlings choice form not found');
        return;
    }

    const selections = collectUserSelections(form);

    if (!validateSelections(selections)) {
        alert('Please select at least one item from any category.');
        return;
    }

    const totalQuantity = calculateTotalQuantity(selections);
    
    window._seedlingsChoices = {
        selections: selections,
        totalQuantity: totalQuantity
    };

    showSelectionSummary(selections);
    showApplicationForm();
}

// Collects all user selections with quantities for all 6 categories
function collectUserSelections(form) {
    const selections = {};
    
    // Get all category checkboxes (grouped by name attribute)
    form.querySelectorAll('input[type="checkbox"]:checked').forEach(cb => {
        const categoryName = cb.name;
        const itemId = cb.dataset.itemId;
        const itemName = cb.value;
        
        // Find quantity input using the itemId
        const quantityInput = form.querySelector(`input[name="quantity_${itemId}"]`);
        const quantity = quantityInput ? parseInt(quantityInput.value) || 1 : 1;
        
        // Initialize category array if not exists
        if (!selections[categoryName]) {
            selections[categoryName] = [];
        }
        
        // Add item to category
        selections[categoryName].push({
            id: itemId,
            name: itemName,
            quantity: quantity
        });
    });
    
    return selections;
}

// Gets quantity for a specific item
function getQuantityForItem(form, itemName) {
    // Create a mapping for complex item names to their quantity field names
    const quantityFieldMap = {
        'Emerald Bitter Gourd Seeds': 'emerald_bitter_gourd_seeds_quantity',
        'Golden Harvest Rice Seeds': 'golden_harvest_rice_seeds_quantity',
        'Green Gem String Bean Seeds': 'green_gem_string_bean_seeds_quantity',
        'Okra Seeds': 'okra_seeds_quantity',
        'Pioneer Hybrid Corn Seeds': 'pioneer_hybrid_corn_seeds_quantity',
        'Red Ruby Tomato Seeds': 'red_ruby_tomato_seeds_quantity',
        'Sunshine Carrot Seeds': 'sunshine_carrot_seeds_quantity',
        'Yellow Pearl Squash Seeds': 'yellow_pearl_squash_seeds_quantity',
        'Avocado Seedling': 'avocado_seedling_quantity',
        'Calamansi Seedling': 'calamansi_seedling_quantity',
        'Guava Seedling': 'guava_seedling_quantity',
        'Guyabano Seedling': 'guyabano_seedling_quantity',
        'Mango Seedling': 'mango_seedling_quantity',
        'Papaya Seedling': 'papaya_seedling_quantity',
        'Santol Seedling': 'santol_seedling_quantity',
        'Dwarf Coconut Tree': 'dwarf_coconut_tree_quantity',
        'Lakatan Banana Tree': 'lakatan_banana_tree_quantity',
        'Rambutan Tree': 'rambutan_tree_quantity',
        'Star Apple Tree': 'star_apple_tree_quantity',
        'Anthurium': 'anthurium_quantity',
        'Bougainvillea': 'bougainvillea_quantity',
        'Fortune Plant': 'fortune_plant_quantity',
        'Gumamela (Hibiscus)': 'gumamela_quantity',
        'Sansevieria (Snake Plant)': 'sansevieria_quantity',
        'Catfish Fingerling': 'catfish_fingerling_quantity',
        'Milkfish (Bangus) Fingerling': 'milkfish_fingerling_quantity',
        'Tilapia Fingerlings': 'tilapia_fingerlings_quantity',
        'Ammonium Sulfate (21-0-0)': 'ammonium_sulfate_quantity',
        'Humic Acid': 'humic_acid_quantity',
        'Pre-processed Chicken Manure': 'pre_processed_chicken_manure_quantity',
        'Urea (46-0-0)': 'urea_quantity',
        'Vermicast Fertilizer': 'vermicast_fertilizer_quantity'
    };

    // Use the mapped field name if available, otherwise create one from the item name
    const fieldName = quantityFieldMap[itemName] || itemName.replace(/[\s\(\)\-]/g, '_').toLowerCase() + '_quantity';
    const quantityInput = form.querySelector(`input[name="${fieldName}"]`);
    return quantityInput ? parseInt(quantityInput.value) || 1 : 1;
}

// Validates that at least one item is selected
function validateSelections(selections) {
    // Check if any category has items selected
    return Object.values(selections).some(items => items.length > 0);
}

// Calculates total quantity across all selections
function calculateTotalQuantity(selections) {
    return Object.values(selections).reduce((total, items) => {
        return total + items.reduce((sum, item) => sum + item.quantity, 0);
    }, 0);
}


// Shows summary alert to user
function showSelectionSummary(selections) {
    let summary = 'You have chosen:\n\n';
    
    const categoryIcons = {
        seeds: '🌾',
        seedlings: '🌱',
        fruits: '🍎',
        ornamentals: '🌺',
        fingerlings: '🐟',
        fertilizers: '🌿'
        // Add more category icons as needed
    };
    
    Object.entries(selections).forEach(([category, items]) => {
        if (items.length > 0) {
            const icon = categoryIcons[category] || '📦';
            const categoryDisplay = category.charAt(0).toUpperCase() + category.slice(1);
            summary += `${icon} ${categoryDisplay}:\n`;
            items.forEach(item => {
                summary += `  • ${item.name}: ${item.quantity} units\n`;
            });
            summary += '\n';
        }
    });
    
    const totalQuantity = calculateTotalQuantity(selections);
    summary += `Total Items: ${totalQuantity} units\n`;
    alert(summary);
}

// Shows the application form with proper setup
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
        showSeedlingsTab('seedlings-form-tab', null);
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
    const parentSection = document.getElementById('seedlings-form');
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
    } else {
        // If no event (programmatic call), activate the first button
        const firstButton = parentSection.querySelector('.seedlings-tab-btn');
        if (firstButton) {
            firstButton.classList.add('active');
        }
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

// Shows/hides supporting documents field based on quantity
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

// Populates the summary section in the application form
function populateSeedlingsSummary() {
    const summaryContainer = document.getElementById('seedlings-summary');
    if (!summaryContainer || !window._seedlingsChoices) return;

    let summaryHTML = '<h3 style="color: #40916c; margin-bottom: 15px;">📋 Your Selected Items:</h3>';

    // Category icons mapping
    const categoryIcons = {
        seeds: '🌾',
        seedlings: '🌱',
        fruits: '🍎',
        ornamentals: '🌺',
        fingerlings: '🐟',
        fertilizers: '🌿'
        // Add more category icons as needed
    };

    Object.entries(window._seedlingsChoices.selections).forEach(([category, items]) => {
        if (items.length > 0) {
            const icon = categoryIcons[category] || '📦';
            const categoryDisplay = category.charAt(0).toUpperCase() + category.slice(1);
            summaryHTML += buildSummarySection(`${icon} ${categoryDisplay}:`, items);
        }
    });

    // Add total quantity section
    summaryHTML += buildTotalQuantitySection(window._seedlingsChoices.totalQuantity);
    summaryContainer.innerHTML = summaryHTML;
}

// Builds HTML for a summary section
function buildSummarySection(title, items) {
    let html = `<div style="margin-bottom: 15px;"><strong style="color: #2d6a4f;">${title}</strong>`;
    html += '<ul style="margin: 8px 0; padding-left: 20px;">';
    
    items.forEach(item => {
        html += `<li style="margin: 4px 0;">${item.name} - <span style="color: #40916c; font-weight: bold;">${item.quantity} units</span></li>`;
    });
    
    html += '</ul></div>';
    return html;
}

// Builds the total quantity section
function buildTotalQuantitySection(totalQuantity) {
    return `
        <div style="margin-top: 20px; padding: 15px; background-color: #e8f5e8; border-radius: 8px; border-left: 4px solid #40916c;">
            <strong style="color: #2d6a4f;">Total Quantity: 
                <span style="color: #40916c; font-size: 1.2em;">${totalQuantity} units</span>
            </strong>
        </div>
    `;
}

// ==============================================
// STATE RESTORATION
// ==============================================

// Restores previous selections when going back
function restorePreviousSelections() {
    if (!window._seedlingsChoices) return;

    const form = document.getElementById('seedlings-choice-form');
    if (!form) return;

    Object.entries(window._seedlingsChoices.selections).forEach(([category, items]) => {
        items.forEach(item => {
            // Restore checkbox
            const checkbox = form.querySelector(`input[name="${category}"][data-item-id="${item.id}"]`);
            if (checkbox) {
                checkbox.checked = true;
                
                // Restore quantity
                const quantityInput = form.querySelector(`input[name="quantity_${item.id}"]`);
                if (quantityInput) {
                    quantityInput.value = item.quantity;
                    
                    // Show quantity field
                    const quantityDiv = quantityInput.closest('.quantity-field');
                    if (quantityDiv) {
                        quantityDiv.style.display = 'flex';
                    }
                }
            }
        });
    });
}

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
        input.value = '1'; // Reset to default value
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

        // If there's a custom file display, reset it
        const fileDisplay = fileInput.parentElement.querySelector('.file-display');
        if (fileDisplay) {
            fileDisplay.innerHTML = '';
        }
    });
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

// Gathers all form data
function gatherFormData(form) {
    return {
        firstName: form.first_name?.value || '',
        middleName: form.middle_name?.value || '',
        lastName: form.last_name?.value || '',
        mobile: form.mobile?.value || '',
        email: form.email?.value || '',
        barangay: form.barangay?.value || '',
        address: form.address?.value || '',
        selections: window._seedlingsChoices
    };
}

// Shows final summary before submission
function showFinalSummary(formData) {
    const { selections } = formData;
    let summary = 'You have chosen:\n';

    if (selections.seeds.length) {
        summary += '- Seeds: ' + selections.seeds.map(s => `${s.name} (${s.quantity})`).join(', ') + '\n';
    }
    if (selections.seedlings.length) {
        summary += '- Seedlings: ' + selections.seedlings.map(v => `${v.name} (${v.quantity})`).join(', ') + '\n';
    }
    if (selections.fruits.length) {
        summary += '- Fruit-bearing Trees: ' + selections.fruits.map(f => `${f.name} (${f.quantity})`).join(', ') + '\n';
    }
    if (selections.ornamentals.length) {
        summary += '- Ornamentals: ' + selections.ornamentals.map(o => `${o.name} (${o.quantity})`).join(', ') + '\n';
    }
    if (selections.fingerlings.length) {
        summary += '- Fingerlings: ' + selections.fingerlings.map(f => `${f.name} (${f.quantity})`).join(', ') + '\n';
    }
    if (selections.fertilizers.length) {
        summary += '- Fertilizers: ' + selections.fertilizers.map(fert => `${fert.name} (${fert.quantity})`).join(', ') + '\n';
    }

    summary += '\nApplicant Details:\n';
    summary += `Name: ${formData.firstName} ${formData.middleName} ${formData.lastName}\n`;
    summary += `Mobile: ${formData.mobile}\nEmail: ${formData.email}\nBarangay: ${formData.barangay}\nAddress: ${formData.address}`;

    alert(summary);
}

// ==============================================
// GLOBAL FUNCTIONS FOR COMPATIBILITY
// ==============================================

// Make functions available globally for HTML onclick handlers
window.openFormSeedlings = openFormSeedlings;
window.closeFormSeedlings = closeFormSeedlings;
window.proceedToSeedlingsForm = proceedToSeedlingsForm;
window.showSeedlingsTab = showSeedlingsTab;
window.backToSeedlingsChoice = backToSeedlingsChoice;
window.toggleQuantity = toggleQuantity;
window.submitSeedlingsRequest = submitSeedlingsRequest;
window.manualReset = manualReset;

console.log('Seedlings module loaded successfully - Dynamic categories supported');