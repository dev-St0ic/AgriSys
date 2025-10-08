// ==============================================
// MODERN SEEDLINGS MODULE - Enhanced UI/UX
// ==============================================

// Global variables
window._seedlingsChoices = null;
let selectedItems = new Map(); // itemId -> {name, quantity, categoryName}

// ==============================================
// MAIN NAVIGATION FUNCTIONS
// ==============================================

function openFormSeedlings(event) {
    event.preventDefault();
    performCompleteReset();
    hideAllMainSections();
    hideAllForms();
    
    const choice = document.getElementById('seedlings-choice');
    if (choice) {
        choice.style.display = 'block';
        setTimeout(() => {
            choice.scrollIntoView({ behavior: 'smooth', block: 'start' });
            setupCategoryToggle(); // Initialize category toggle
        }, 100);
    }
    history.pushState(null, '', '/services/seedlings');
}

function closeFormSeedlings() {
    performCompleteReset();
    hideAllForms();
    showAllMainSections();
    window.scrollTo({ top: 0, behavior: 'smooth' });
    history.pushState(null, '', '/services');
}

function backToSeedlingsChoice() {
    hideAllForms();
    const choice = document.getElementById('seedlings-choice');
    if (choice) {
        choice.style.display = 'block';
        restorePreviousSelections();
        setTimeout(() => {
            choice.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 100);
    }
    history.pushState(null, '', '/services/seedlings');
}

// ==============================================
// CATEGORY SHOW MORE/LESS FUNCTIONALITY
// ==============================================

function initializeCategoryTabs() {
    const categoryTabs = document.querySelector('.seedlings-category-tabs');
    if (!categoryTabs) return;
    
    // Remove existing toggle button if any
    const existingToggle = categoryTabs.querySelector('.category-toggle-btn');
    if (existingToggle) {
        existingToggle.remove();
    }
    
    const allTabs = Array.from(categoryTabs.querySelectorAll('.seedlings-category-tab:not(.category-toggle-btn)'));
    const MAX_VISIBLE = 8; // Including "All Items" button
    
    // Only add show more/less if there are more than MAX_VISIBLE tabs
    if (allTabs.length <= MAX_VISIBLE) {
        // Remove hidden class from all tabs if less than max
        allTabs.forEach(tab => {
            tab.classList.remove('category-tab-hidden');
        });
        return;
    }
    
    // Hide tabs beyond the first MAX_VISIBLE
    allTabs.forEach((tab, index) => {
        if (index >= MAX_VISIBLE) {
            tab.classList.add('category-tab-hidden');
        } else {
            tab.classList.remove('category-tab-hidden');
        }
    });
    
    // Create Show More/Less button
    const toggleButton = document.createElement('button');
    toggleButton.className = 'seedlings-category-tab category-toggle-btn';
    toggleButton.innerHTML = '<i class="fas fa-chevron-down"></i> Show More';
    toggleButton.setAttribute('data-expanded', 'false');
    toggleButton.type = 'button';
    
    toggleButton.addEventListener('click', function(e) {
        e.preventDefault();
        const isExpanded = this.getAttribute('data-expanded') === 'true';
        
        if (isExpanded) {
            // Collapse - hide extra tabs
            allTabs.forEach((tab, index) => {
                if (index >= MAX_VISIBLE) {
                    tab.classList.add('category-tab-hidden');
                }
            });
            this.innerHTML = '<i class="fas fa-chevron-down"></i> Show More';
            this.setAttribute('data-expanded', 'false');
        } else {
            // Expand - show all tabs
            allTabs.forEach(tab => {
                tab.classList.remove('category-tab-hidden');
            });
            this.innerHTML = '<i class="fas fa-chevron-up"></i> Show Less';
            this.setAttribute('data-expanded', 'true');
        }
    });
    
    // Append toggle button to category tabs
    categoryTabs.appendChild(toggleButton);
}

function setupCategoryToggle() {
    // Wait a bit to ensure all categories are rendered
    setTimeout(() => {
        initializeCategoryTabs();
    }, 100);
}

// ==============================================
// FILTERING AND SEARCH FUNCTIONS
// ==============================================

function filterByCategory(categoryName) {
    // Update active tab
    document.querySelectorAll('.seedlings-category-tab:not(.category-toggle-btn)').forEach(tab => {
        tab.classList.remove('active');
    });
    event.target.classList.add('active');
    
    // Filter items
    const items = document.querySelectorAll('.seedlings-item-card');
    let visibleCount = 0;
    
    items.forEach(item => {
        const itemCategory = item.dataset.category;
        if (categoryName === 'all' || itemCategory === categoryName) {
            item.classList.remove('hidden');
            visibleCount++;
        } else {
            item.classList.add('hidden');
        }
    });
    
    updateNoResultsDisplay(visibleCount);
}

function searchItems() {
    const searchTerm = document.getElementById('seedlings-search').value.toLowerCase();
    const items = document.querySelectorAll('.seedlings-item-card');
    let visibleCount = 0;
    
    items.forEach(item => {
        const itemName = item.dataset.itemName;
        const isVisible = itemName.includes(searchTerm);
        
        if (isVisible && !item.classList.contains('hidden')) {
            item.style.display = 'flex';
            visibleCount++;
        } else if (isVisible) {
            // Item matches search but may be hidden by category filter
            if (!item.classList.contains('hidden')) {
                item.style.display = 'flex';
                visibleCount++;
            }
        } else {
            item.style.display = 'none';
        }
    });
    
    updateNoResultsDisplay(visibleCount);
}

function filterByStock() {
    const stockFilter = document.getElementById('stock-filter').value;
    const items = document.querySelectorAll('.seedlings-item-card');
    let visibleCount = 0;
    
    items.forEach(item => {
        const stockStatus = item.dataset.stockStatus;
        let shouldShow = false;
        
        switch(stockFilter) {
            case 'all':
                shouldShow = true;
                break;
            case 'in-stock':
                shouldShow = stockStatus === 'in_stock';
                break;
            case 'low-stock':
                shouldShow = stockStatus === 'low_stock';
                break;
            case 'out-of-stock':
                shouldShow = stockStatus === 'out_of_stock';
                break;
        }
        
        if (shouldShow && item.style.display !== 'none') {
            item.classList.remove('hidden');
            visibleCount++;
        } else {
            item.classList.add('hidden');
        }
    });
    
    updateNoResultsDisplay(visibleCount);
}

function sortItems() {
    const sortBy = document.getElementById('sort-by').value;
    const grid = document.getElementById('items-grid');
    const items = Array.from(document.querySelectorAll('.seedlings-item-card'));
    
    items.sort((a, b) => {
        switch(sortBy) {
            case 'name-asc':
                return a.dataset.itemName.localeCompare(b.dataset.itemName);
            case 'name-desc':
                return b.dataset.itemName.localeCompare(a.dataset.itemName);
            case 'stock-high':
                return parseInt(b.dataset.stock) - parseInt(a.dataset.stock);
            case 'stock-low':
                return parseInt(a.dataset.stock) - parseInt(b.dataset.stock);
            default:
                return 0;
        }
    });
    
    // Re-append items in sorted order
    items.forEach(item => grid.appendChild(item));
}

function updateNoResultsDisplay(visibleCount) {
    const noResults = document.getElementById('no-results');
    if (visibleCount === 0) {
        noResults.style.display = 'block';
    } else {
        noResults.style.display = 'none';
    }
}

// ==============================================
// SELECTION MANAGEMENT
// ==============================================

function toggleItemSelection(checkbox, itemId) {
    const qtyWrapper = document.getElementById(`qty-wrapper-${itemId}`);
    const qtyInput = document.getElementById(`qty-${itemId}`);
    
    if (checkbox.checked) {
        // Show quantity input
        qtyWrapper.style.display = 'flex';
        
        // Add to selection
        const itemName = checkbox.value;
        const categoryName = checkbox.name;
        const quantity = parseInt(qtyInput.value) || 1;
        
        selectedItems.set(itemId, {
            id: itemId,
            name: itemName,
            categoryName: categoryName,
            quantity: quantity
        });
    } else {
        // Hide quantity input
        qtyWrapper.style.display = 'none';
        
        // Remove from selection
        selectedItems.delete(itemId);
    }
    
    updateSelectionSummary();
    updateProceedButton();
}

function incrementQty(itemId) {
    const input = document.getElementById(`qty-${itemId}`);
    const max = parseInt(input.max);
    let value = parseInt(input.value) || 1;
    
    if (value < max) {
        input.value = value + 1;
        updateQuantity(itemId);
    }
}

function decrementQty(itemId) {
    const input = document.getElementById(`qty-${itemId}`);
    const min = parseInt(input.min) || 1;
    let value = parseInt(input.value) || 1;
    
    if (value > min) {
        input.value = value - 1;
        updateQuantity(itemId);
    }
}

function updateQuantity(itemId) {
    const qtyInput = document.getElementById(`qty-${itemId}`);
    const quantity = parseInt(qtyInput.value) || 1;
    
    // Update in selected items map
    if (selectedItems.has(itemId)) {
        const item = selectedItems.get(itemId);
        item.quantity = quantity;
        selectedItems.set(itemId, item);
    }
    
    updateSelectionSummary();
}

function updateSelectionSummary() {
    const summaryDiv = document.getElementById('selection-summary');
    const countSpan = document.getElementById('selected-count');
    
    const totalItems = selectedItems.size;
    
    if (totalItems > 0) {
        summaryDiv.style.display = 'flex';
        countSpan.textContent = totalItems;
    } else {
        summaryDiv.style.display = 'none';
    }
    
    // Update the tab button badge
    updateSummaryTabBadge();
}

function updateSummaryTabBadge() {
    const summaryBtn = document.querySelector('.seedlings-tab-btn[onclick*="seedlings-summary-tab"]');
    if (summaryBtn) {
        const badge = summaryBtn.querySelector('.tab-badge');
        if (selectedItems.size > 0) {
            if (badge) {
                badge.textContent = selectedItems.size;
            }
        }
    }
}

function updateProceedButton() {
    const proceedBtn = document.getElementById('proceed-btn');
    if (selectedItems.size > 0) {
        proceedBtn.disabled = false;
    } else {
        proceedBtn.disabled = true;
    }
}

function clearAllSelections() {
    // Uncheck all checkboxes
    document.querySelectorAll('.seedlings-item-card input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = false;
        const itemId = checkbox.dataset.itemId;
        const qtyWrapper = document.getElementById(`qty-wrapper-${itemId}`);
        if (qtyWrapper) {
            qtyWrapper.style.display = 'none';
        }
    });
    
    // Clear selected items
    selectedItems.clear();
    
    // Update UI
    updateSelectionSummary();
    updateProceedButton();
}

// ==============================================
// PROCEED TO FORM
// ==============================================

function proceedToSeedlingsForm() {
    if (selectedItems.size === 0) {
        alert('Please select at least one item.');
        return;
    }
    
    // Organize selections by category
    const selections = {};
    let totalQuantity = 0;
    
    selectedItems.forEach(item => {
        if (!selections[item.categoryName]) {
            selections[item.categoryName] = [];
        }
        selections[item.categoryName].push({
            id: item.id,
            name: item.name,
            quantity: item.quantity
        });
        totalQuantity += item.quantity;
    });
    
    // Store in global variable
    window._seedlingsChoices = {
        selections: selections,
        totalQuantity: totalQuantity
    };
    
    // Go directly to form (no alert popup)
    showApplicationForm();
}

function showApplicationForm() {
    hideAllForms();
    
    const appForm = document.getElementById('seedlings-form');
    if (appForm) {
        appForm.style.display = 'block';
        toggleSupportingDocuments(window._seedlingsChoices.totalQuantity);
        
        populateSeedlingsSummary();
        
        showSeedlingsTab('seedlings-form-tab', null);
        
        setTimeout(() => {
            appForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 100);
    }
    history.pushState(null, '', '/services/seedlings/form');
}

// ==============================================
// TAB SWITCHING
// ==============================================

function showSeedlingsTab(tabId, event) {
    if (event) {
        event.preventDefault();
    }
    
    const parentSection = document.getElementById('seedlings-form');
    if (!parentSection) return;
    
    // Remove active class from all buttons
    parentSection.querySelectorAll('.seedlings-tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Hide all tab content
    parentSection.querySelectorAll('.seedlings-tab-content').forEach(content => {
        content.style.display = 'none';
    });
    
    // Activate clicked button
    if (event && event.target) {
        event.target.classList.add('active');
    } else {
        const firstButton = parentSection.querySelector('.seedlings-tab-btn');
        if (firstButton) firstButton.classList.add('active');
    }
    
    // Show selected tab
    const selectedTab = document.getElementById(tabId);
    if (selectedTab) {
        selectedTab.style.display = 'block';
        setTimeout(() => {
            selectedTab.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }, 50);
    }
}

// ==============================================
// SUPPORTING DOCUMENTS
// ==============================================

function toggleSupportingDocuments(totalQuantity) {
    const docsField = document.getElementById('supporting-docs-field');
    const docsInput = document.getElementById('seedlings-docs');
    const docsLabel = document.querySelector('label[for="seedlings-docs"]');
    const docsSmall = docsInput ? docsInput.nextElementSibling : null;
    
    if (totalQuantity >= 100) {
        if (docsField) docsField.style.display = 'block';
        if (docsInput) docsInput.setAttribute('required', 'required');
        if (docsLabel) docsLabel.innerHTML = 'Supporting Documents (Required)';
        if (docsSmall) {
            docsSmall.innerHTML = 'Required: Proof of planting area (land title, lease agreement, barangay certification, photos of planting area, etc.). Multiple files allowed.';
        }
    } else {
        if (docsField) docsField.style.display = 'none';
        if (docsInput) docsInput.removeAttribute('required');
    }
}

// ==============================================
// SUMMARY DISPLAY
// ==============================================

function populateSeedlingsSummary() {
    const summaryContainer = document.getElementById('seedlings-summary-tab');
    if (!summaryContainer || !window._seedlingsChoices) return;
    
    let summaryHTML = `
        <div class="summary-content">
            <h3><i class="fas fa-shopping-cart"></i> Selected Items (${window._seedlingsChoices.totalQuantity} total)</h3>
    `;
    
    Object.entries(window._seedlingsChoices.selections).forEach(([category, items]) => {
        if (items.length > 0) {
            const categoryDisplay = category.charAt(0).toUpperCase() + category.slice(1);
            summaryHTML += `<div class="summary-category">`;
            summaryHTML += `<strong>${categoryDisplay}:</strong>`;
            summaryHTML += '<ul>';
            items.forEach(item => {
                summaryHTML += `<li>${item.name} - <span class="qty-highlight">${item.quantity} units</span></li>`;
            });
            summaryHTML += '</ul></div>';
        }
    });
    
    summaryHTML += `</div>`;
    summaryContainer.innerHTML = summaryHTML;
}

// ==============================================
// FORM SUBMISSION
// ==============================================

function submitSeedlingsRequest(event) {
    event.preventDefault();
    
    const form = document.getElementById('seedlings-request-form');
    if (!form) {
        console.error('Form not found');
        return false;
    }
    
    const submitBtn = form.querySelector('.seedlings-submit-btn');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Submitting...';
    submitBtn.disabled = true;
    
    const formData = new FormData(form);
    
    if (window._seedlingsChoices) {
        formData.append('selected_seedlings', JSON.stringify(window._seedlingsChoices));
    } else {
        alert('Please select items first');
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
        return;
    }
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
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
            alert('✅ ' + data.message);
            performCompleteReset();
            closeFormSeedlings();
        } else {
            if (data.errors) {
                let errorMsg = 'Validation errors:\n';
                for (let field in data.errors) {
                    errorMsg += `${field}: ${data.errors[field].join(', ')}\n`;
                }
                alert(errorMsg);
            } else {
                alert('❌ ' + (data.message || 'There was an error submitting your request.'));
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ There was an error submitting your request. Please try again.');
    })
    .finally(() => {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
}

// ==============================================
// RESET FUNCTIONS
// ==============================================

function performCompleteReset() {
    window._seedlingsChoices = null;
    selectedItems.clear();
    
    // Reset application form
    const applicationForm = document.getElementById('seedlings-request-form');
    if (applicationForm) {
        applicationForm.reset();
        applicationForm.querySelectorAll('select').forEach(select => {
            select.selectedIndex = 0;
            select.value = '';
        });
    }
    
    // Reset choice form
    clearAllSelections();
    
    // Clear summary
    const summaryContainer = document.getElementById('seedlings-summary-tab');
    if (summaryContainer) {
        summaryContainer.innerHTML = '';
    }
    
    // Reset supporting documents
    resetSupportingDocuments();
    
    // Reset filters
    const searchInput = document.getElementById('seedlings-search');
    const stockFilter = document.getElementById('stock-filter');
    const sortBy = document.getElementById('sort-by');
    
    if (searchInput) searchInput.value = '';
    if (stockFilter) stockFilter.value = 'all';
    if (sortBy) sortBy.value = 'name-asc';
    
    // Reset category filter
    const allTab = document.querySelector('[data-category="all"]');
    if (allTab) {
        filterByCategory.call(allTab, 'all');
    }
    
    console.log('Complete reset performed');
}

function resetSupportingDocuments() {
    const docsField = document.getElementById('supporting-docs-field');
    const docsInput = document.getElementById('seedlings-docs');
    
    if (docsField) docsField.style.display = 'none';
    if (docsInput) {
        docsInput.removeAttribute('required');
        docsInput.value = '';
    }
}

function restorePreviousSelections() {
    if (!window._seedlingsChoices) return;
    
    Object.entries(window._seedlingsChoices.selections).forEach(([category, items]) => {
        items.forEach(item => {
            const checkbox = document.querySelector(`input[data-item-id="${item.id}"]`);
            if (checkbox) {
                checkbox.checked = true;
                toggleItemSelection(checkbox, item.id);
                
                const qtyInput = document.getElementById(`qty-${item.id}`);
                if (qtyInput) {
                    qtyInput.value = item.quantity;
                }
            }
        });
    });
}

// ==============================================
// INITIALIZATION
// ==============================================

document.addEventListener('DOMContentLoaded', function() {
    window._seedlingsChoices = null;
    selectedItems.clear();
    
    const form = document.getElementById('seedlings-request-form');
    if (form) {
        form.addEventListener('submit', submitSeedlingsRequest);
    }
    
    window.addEventListener('popstate', function() {
        performCompleteReset();
    });
    
    // Initialize category show more/less
    setupCategoryToggle();
});

// ==============================================
// GLOBAL EXPORTS
// ==============================================

window.openFormSeedlings = openFormSeedlings;
window.closeFormSeedlings = closeFormSeedlings;
window.proceedToSeedlingsForm = proceedToSeedlingsForm;
window.showSeedlingsTab = showSeedlingsTab;
window.backToSeedlingsChoice = backToSeedlingsChoice;
window.toggleItemSelection = toggleItemSelection;
window.incrementQty = incrementQty;
window.decrementQty = decrementQty;
window.updateQuantity = updateQuantity;
window.filterByCategory = filterByCategory;
window.searchItems = searchItems;
window.filterByStock = filterByStock;
window.sortItems = sortItems;
window.clearAllSelections = clearAllSelections;
window.submitSeedlingsRequest = submitSeedlingsRequest;
window.initializeCategoryTabs = initializeCategoryTabs;
window.setupCategoryToggle = setupCategoryToggle;

console.log('Modern Seedlings module loaded successfully');