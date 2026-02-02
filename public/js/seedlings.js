// ==============================================
// MODERN SEEDLINGS MODULE - Enhanced UI/UX
// ==============================================

// Global variables
window._seedlingsChoices = null;
let selectedItems = new Map(); // itemId -> {name, quantity, categoryName}

// Filter state management
let currentFilters = {
    category: 'all',
    search: '',
    stock: 'all'
};

// ==============================================
// MAIN NAVIGATION FUNCTIONS
// ==============================================

function openFormSeedlings(event) {
    if (event && typeof event.preventDefault === 'function') {
        event.preventDefault();
    }

    // Check authentication before allowing access
    if (!showAuthRequired('Supplies & Garden Tools')) {
        return false;
    }

    console.log('Opening Seedlings form');

    performCompleteReset();
    hideAllMainSections();
    hideAllForms();

    const choice = document.getElementById('seedlings-choice');
    if (choice) {
        choice.style.display = 'block';
        // Scroll to top with proper timing and multiple fallbacks
        setTimeout(() => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
            document.documentElement.scrollTop = 0;
            document.body.scrollTop = 0;
        }, 50);
    }
    history.pushState(null, '', '/services/seedlings');
}

function closeFormSeedlings() {
    performCompleteReset();
    hideAllForms();
    showAllMainSections();
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

function closeSeedlingsModal() {
    performCompleteReset();
    hideAllForms();
    showAllMainSections();
    window.location.href = '/'; // Navigate to home
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
    toggleButton.style.flexShrink = '0'; // ← ADDed THIS
    toggleButton.style.whiteSpace = 'nowrap'; // ← ADDed THIS

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

// Initialize category tab event listeners
function initCategoryTabs() {
    const tabsContainer = document.getElementById('category-tabs-container');
    if (!tabsContainer) return;

    tabsContainer.addEventListener('click', function(e) {
        const tab = e.target.closest('.seedlings-category-tab');
        if (!tab) return;

        e.preventDefault();
        e.stopPropagation();

        const categoryName = tab.getAttribute('data-category');
        if (categoryName) {
            filterByCategory(categoryName);
        }
    });
}

// Apply all active filters to items
function applyFilters() {
    const items = document.querySelectorAll('.seedlings-item-card');
    let visibleCount = 0;

    items.forEach(item => {
        const itemCategory = item.getAttribute('data-category');
        const itemName = item.getAttribute('data-item-name') || '';
        const stockStatus = item.getAttribute('data-stock-status');

        // Check all filter conditions
        const categoryMatch = currentFilters.category === 'all' || itemCategory === currentFilters.category;
        const searchMatch = currentFilters.search === '' || itemName.toLowerCase().includes(currentFilters.search.toLowerCase());

        let stockMatch = true;
        if (currentFilters.stock !== 'all') {
            switch(currentFilters.stock) {
                case 'in-stock':
                    stockMatch = stockStatus === 'in_stock';
                    break;
                case 'low-stock':
                    stockMatch = stockStatus === 'low_stock';
                    break;
                case 'out-of-stock':
                    stockMatch = stockStatus === 'out_of_stock';
                    break;
            }
        }

        // Show item only if it matches all filters
        if (categoryMatch && searchMatch && stockMatch) {
            item.classList.remove('hidden');
            item.style.display = '';
            visibleCount++;
        } else {
            item.classList.add('hidden');
            item.style.display = 'none';
        }
    });

    updateNoResultsDisplay(visibleCount);
    currentPage = 1;
    updatePagination();

    return visibleCount;
}

function filterByCategory(categoryName) {
    console.log('Filter by category:', categoryName);

    // Update filter state
    currentFilters.category = categoryName;
    currentFilters.search = ''; // Clear search when changing category

    // Clear search input
    const searchInput = document.getElementById('seedlings-search');
    if (searchInput) {
        searchInput.value = '';
    }

    // Update active tab
    const allTabs = document.querySelectorAll('.seedlings-category-tab');
    allTabs.forEach(tab => {
        if (tab.getAttribute('data-category') === categoryName) {
            tab.classList.add('active');
        } else {
            tab.classList.remove('active');
        }
    });

    // Apply all filters
    applyFilters();
}

function searchItems() {
    const searchInput = document.getElementById('seedlings-search');
    currentFilters.search = searchInput ? searchInput.value.trim() : '';

    // Apply all filters
    applyFilters();
}

function filterByStock() {
    const stockSelect = document.getElementById('stock-filter');
    currentFilters.stock = stockSelect ? stockSelect.value : 'all';

    // Apply all filters
    applyFilters();
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

        // Get additional item details for cart display
        const itemCard = checkbox.closest('.seedlings-item-card');
        const imageSrc = itemCard.querySelector('.seedlings-item-image img')?.src || '';
        const itemIcon = itemCard.querySelector('.seedlings-item-category i')?.className || 'fas fa-leaf';

        selectedItems.set(itemId, {
            id: itemId,
            name: itemName,
            categoryName: categoryName,
            quantity: quantity,
            imageSrc: imageSrc,
            icon: itemIcon
        });
    } else {
        // Hide quantity input
        qtyWrapper.style.display = 'none';

        // Remove from selection
        selectedItems.delete(itemId);
    }

    updateSelectionSummary();
    updateCartItemsList();
    updateProceedButton();
}

function incrementQty(itemId) {
    const input = document.getElementById(`qty-${itemId}`);
    const max = parseInt(input.max);
    let value = parseInt(input.value) || 1;

    if (value < max) {
        input.value = value + 1;
        updateQuantity(itemId);
    }  else {
        // Show user-friendly notification when exceeding stock
        showStockNotification(itemId, max);
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
    const maxStock = parseInt(qtyInput.max);

    // Validate and cap quantity if it exceeds available stock
    if (quantity > maxStock) {
        qtyInput.value = maxStock;
        showStockNotification(itemId, maxStock);
        return;
    }

    // Update in selected items map
    if (selectedItems.has(itemId)) {
        const item = selectedItems.get(itemId);
        item.quantity = quantity;
        selectedItems.set(itemId, item);
    }

    updateSelectionSummary();
    updateCartItemsList();
}

// Show notification when user tries to exceed available stock
//shows warning as a TOAST notification
function showStockNotification(itemId, availableStock) {
    // Use the global toast notification system instead of card warning
    toast.warning(
        `We only have ${availableStock} unit${availableStock !== 1 ? 's' : ''} available for this item right now`,
        {
            title: 'Stock Limit',
            duration: 5000
        }
    );
}

function updateSelectionSummary() {
    const summaryDiv = document.getElementById('selection-summary');
    const countSpan = document.getElementById('selected-count');
    const filterCount = document.getElementById('filter-count');

    const totalItems = selectedItems.size;

    if (totalItems > 0) {
        summaryDiv.style.display = 'flex';
        countSpan.textContent = totalItems;
        if (filterCount) {
            filterCount.textContent = totalItems;
        }
    } else {
        summaryDiv.style.display = 'none';
        if (filterCount) {
            filterCount.textContent = '0';
        }
    }

    // Update the tab button badge
    updateSummaryTabBadge();
}

// New function to update cart items list
function updateCartItemsList() {
    const cartItemsList = document.getElementById('cart-modal-items');
    if (!cartItemsList) return;

    if (selectedItems.size === 0) {
        cartItemsList.innerHTML = '<div class="cart-empty-message"><i class="fas fa-shopping-cart"></i>No items selected</div>';
        return;
    }

    let html = '';
    selectedItems.forEach((item, itemId) => {
        html += `
            <div class="cart-item" data-item-id="${itemId}">
                <div class="cart-item-image">
                    ${item.imageSrc
                        ? `<img src="${item.imageSrc}" alt="${item.name}">`
                        : `<i class="${item.icon}"></i>`
                    }
                </div>
                <div class="cart-item-info">
                    <div class="cart-item-name" title="${item.name}">${item.name}</div>
                    <div class="cart-item-category">
                        <i class="${item.icon}"></i> ${item.categoryName}
                    </div>
                    <span class="cart-item-quantity"><i class="fas fa-boxes"></i> Qty: ${item.quantity}</span>
                </div>
                <button class="cart-item-remove" onclick="removeItemFromCart('${itemId}')" title="Remove item">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
    });

    cartItemsList.innerHTML = html;
}

// New function to open cart modal
function openCartModal() {
    const modal = document.getElementById('cartModalOverlay');
    if (!modal) return;

    updateCartItemsList(); // Refresh the list
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

// New function to close cart modal
function closeCartModal(event) {
    if (event) {
        event.stopPropagation();
    }

    const modal = document.getElementById('cartModalOverlay');
    if (!modal) return;

    modal.style.display = 'none';
    document.body.style.overflow = '';
}

// Updated toggle function (now opens modal instead)
function toggleCartExpansion() {
    openCartModal();
}

// New function to remove item from cart
function removeItemFromCart(itemId) {
    // Find and uncheck the checkbox
    const checkbox = document.querySelector(`input[data-item-id="${itemId}"]`);
    if (checkbox) {
        checkbox.checked = false;
        toggleItemSelection(checkbox, itemId);
    }
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
    updateCartItemsList();
    updateProceedButton();
}

// Clear all items from cart with confirmation
function clearCartItems() {
    if (selectedItems.size === 0) return;

    if (confirm('Are you sure you want to remove all items from your cart?')) {
        clearAllSelections();
        closeCartModal();
    }
}

// Proceed to application form
function proceedToApplication() {
    if (selectedItems.size === 0) {
        toast.warning('Please select at least one item before proceeding');
        return;
    }

    // Close the modal first
    closeCartModal();

    // Switch to the summary tab
    switchTab('seedlings-summary-tab');
}

// ==============================================
// PROCEED TO FORM
// ==============================================

function proceedToSeedlingsForm() {
    if (selectedItems.size === 0) {
        agrisysModal.warning('Please select at least one item.', { title: 'No Items Selected' });
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

    // Close modal BEFORE showing form
    closeCartModal();

    // Go directly to form
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

        // Ensure body scroll is enabled
        document.body.style.overflow = 'auto';
        document.documentElement.style.overflow = 'auto';

        // Force scroll to top with delay
        setTimeout(() => {
            window.scrollTo(0, 0);
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
        //scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}
// Pick up date - ALWAYS SHOW
function showPickupDateField(totalQuantity) {
    const pickupDateSection = document.getElementById('pickup-date-section');
    const pickupInput = document.getElementById('seedlings-pickup_date');
    
    // ✅ SHOW PICKUP DATE FIELD FOR ALL REQUESTS
    if (pickupDateSection) pickupDateSection.style.display = 'block';
    if (pickupInput) {
        pickupInput.required = true;
        
        // Set dynamic min and max dates based on TODAY
        const today = new Date();
        const minDate = new Date(today);
        minDate.setDate(minDate.getDate() + 1); // 1 day from today
        
        const maxDate = new Date(today);
        maxDate.setDate(maxDate.getDate() + 30); // 30 days from today
        
        // Format dates as YYYY-MM-DD
        const formatDate = (date) => date.toISOString().split('T')[0];
        
        pickupInput.min = formatDate(minDate);
        pickupInput.max = formatDate(maxDate);
        pickupInput.value = ''; // Clear any previous value
    }
}

function initPickupDateField() {
    const pickupInput = document.getElementById('seedlings-pickup_date');
    const displayDiv = document.getElementById('pickup-date-display');
    const displayText = document.getElementById('pickup-date-text');

    if (!pickupInput) return;

    // Set min/max dates
    const today = new Date();
    const minDate = new Date(today);
    minDate.setDate(minDate.getDate() + 1);
    
    const maxDate = new Date(today);
    maxDate.setDate(maxDate.getDate() + 30);

    const formatDate = (date) => date.toISOString().split('T')[0];
    
    pickupInput.min = formatDate(minDate);
    pickupInput.max = formatDate(maxDate);

    // ✅ VALIDATE ON DATE CHANGE
    pickupInput.addEventListener('change', function() {
        if (!this.value) {
            // User cleared the date
            displayDiv.style.display = 'none';
            return;
        }

        const selectedDate = new Date(this.value + 'T00:00:00');
        const dayOfWeek = selectedDate.getDay(); // 0=Sunday, 6=Saturday
        const dayName = selectedDate.toLocaleDateString('en-US', { weekday: 'long' });
        const fullDate = selectedDate.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });

        // ✅ CHECK IF WEEKEND (0 = Sunday, 6 = Saturday)
        if (dayOfWeek === 0 || dayOfWeek === 6) {
            this.value = ''; // Clear invalid selection
            displayDiv.style.display = 'none';
            
            // Show user-friendly warning
            toast.warning(
                `${dayName}s are closed. Please select a weekday (Monday-Friday).`,
                {
                    title: '⚠️ Weekend Not Available',
                    duration: 4000
                }
            );
            return;
        }

        // ✅ VALID WEEKDAY - SHOW CONFIRMATION
        displayText.innerHTML = `
            <i class="fas fa-check-circle" style="color: #40916c; margin-right: 8px;"></i>
            <strong>${fullDate}</strong> <span style="color: #666;">(${dayName})</span>
        `;
        displayDiv.style.display = 'block';
    });

    // ✅ VALIDATE ON BLUR (When user leaves the field)
    pickupInput.addEventListener('blur', function() {
        if (!this.value) return;

        const selectedDate = new Date(this.value + 'T00:00:00');
        const dayOfWeek = selectedDate.getDay();

        if (dayOfWeek === 0 || dayOfWeek === 6) {
            this.value = '';
            displayDiv.style.display = 'none';
            
            toast.warning(
                'Weekends are not available for pickup. Please select a weekday.',
                {
                    title: '❌ Invalid Selection',
                    duration: 5000
                }
            );
        }
    });
}

// Call on DOMContentLoaded
document.addEventListener('DOMContentLoaded', function() {
    initPickupDateField();
});


// ==============================================
// SUPPORTING DOCUMENTS
function toggleSupportingDocuments(totalQuantity) {
    const docsField = document.getElementById('supporting-docs-field');
    const docsInput = document.getElementById('seedlings-docs');

    // ✅ ALWAYS SHOW supporting documents as OPTIONAL
    if (docsField) docsField.style.display = 'block';
    if (docsInput) docsInput.required = false; // Always optional
    
    showPickupDateField(totalQuantity);
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

    // ✅ CHECK PICKUP DATE VALIDATION FIRST (BEFORE ANY SUBMISSION)
    const pickupDateSection = document.getElementById('pickup-date-section');
    const pickupInput = document.getElementById('seedlings-pickup_date');
    
    if (pickupDateSection && pickupDateSection.style.display !== 'none' && !pickupInput.value) {
        agrisysModal.warning('Please select a pickup date', { title: 'Pickup Date Required' });
        return false; // STOP execution
    }

    // ✅ DOUBLE-CHECK: NO WEEKENDS ALLOWED
    if (pickupInput.value) {
        const selectedDate = new Date(pickupInput.value + 'T00:00:00');
        const dayOfWeek = selectedDate.getDay();
        
        if (dayOfWeek === 0 || dayOfWeek === 6) {
            agrisysModal.warning(
                'Weekends are not available for pickup. Please select a weekday (Monday-Friday).',
                { title: 'Invalid Pickup Date' }
            );
            return false; // STOP execution
        }
    }

    const submitBtn = form.querySelector('.seedlings-submit-btn');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Submitting...';
    submitBtn.disabled = true;

    const formData = new FormData(form);

    if (window._seedlingsChoices) {
        formData.append('selected_seedlings', JSON.stringify(window._seedlingsChoices));
    } else {
        agrisysModal.warning('Please select items first', { title: 'No Items Selected' });
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
        return false;
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
            // ✅ RESET PICKUP DATE FIELD IMMEDIATELY AFTER SUCCESS
            const pickupInput = document.getElementById('seedlings-pickup_date');
            const displayDiv = document.getElementById('pickup-date-display');
            
            if (pickupInput) {
                pickupInput.value = '';
            }
            if (displayDiv) {
                displayDiv.style.display = 'none';
            }

            agrisysModal.success(data.message, {
                title: 'Request Submitted!',
                reference: data.request_number || data.reference_number || data.application_number || null,
                onClose: () => {
                    performCompleteReset();
                    closeFormSeedlings();
                    setTimeout(() => {
                        document.documentElement.scrollTop = 0;
                        document.body.scrollTop = 0;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }, 500);
                }
            });
        } else {
            if (data.errors) {
                const errorList = Object.entries(data.errors).map(([field, messages]) => `${field}: ${messages.join(', ')}`);
                agrisysModal.validationError(errorList, { title: 'Validation Errors' });
            } else {
                agrisysModal.error(data.message || 'There was an error submitting your request.', { title: 'Submission Failed' });
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        agrisysModal.error('There was an error submitting your request. Please try again.', { title: 'Submission Failed' });
    })
    .finally(() => {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
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
        allTab.click();
    }

    console.log('Complete reset performed');
}
function resetSupportingDocuments() {
    const docsField = document.getElementById('supporting-docs-field');
    const docsInput = document.getElementById('seedlings-docs');
    const pickupDateSection = document.getElementById('pickup-date-section');
    const pickupInput = document.getElementById('seedlings-pickup_date');

    // ✅ Always show docs as optional
    if (docsField) docsField.style.display = 'block';
    if (docsInput) {
        docsInput.required = false;
        docsInput.value = '';
    }
    
    // Pickup date always shows but reset it
    if (pickupDateSection) pickupDateSection.style.display = 'block';
    if (pickupInput) {
        pickupInput.required = true;
        pickupInput.value = '';
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


// // ALSO UPDATE toggleSupportingDocuments() TO SHOW DOCS FOR ALL TOO:

// function toggleSupportingDocuments(totalQuantity) {
//     const docsField = document.getElementById('supporting-docs-field');
//     const docsInput = document.getElementById('seedlings-docs');

//     // ✅ SHOW FOR ALL REQUESTS
//     if (docsField) docsField.style.display = 'block';
//     if (docsInput) docsInput.required = true;
    
//     showPickupDateField(totalQuantity);
// }


// ==============================================
// INITIALIZATION
// ==============================================

document.addEventListener('DOMContentLoaded', function() {

    // Initialize category tabs with event listeners
    initCategoryTabs();

    // Add event listener for manual quantity input (if user types directly)
    document.querySelectorAll('.qty-input').forEach(input => {
        input.addEventListener('change', function() {
            const itemId = this.id.replace('qty-', '');
            updateQuantity(itemId);
        });
    });

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

    // Real-time validation for contact number
    const mobileInput = document.getElementById('seedlings-mobile');
    const mobileWarning = document.getElementById('seedlings-mobile-warning');

    if (mobileInput) {
        mobileInput.addEventListener('input', function(e) {
            const value = e.target.value;
            const phonePattern = /^09\d{9}$/;

            if (value !== '' && !phonePattern.test(value)) {
                if (!mobileWarning) {
                    const warning = document.createElement('span');
                    warning.className = 'validation-warning';
                    warning.id = 'seedlings-mobile-warning';
                    warning.style.cssText = 'color: #ff6b6b; font-size: 0.875rem; display: block; margin-top: 4px;';
                    warning.textContent = 'Contact number must be in format 09XXXXXXXXX (11 digits)';
                    mobileInput.parentNode.appendChild(warning);
                } else {
                    mobileWarning.style.display = 'block';
                }
                mobileInput.style.borderColor = '#ff6b6b';
            } else {
                if (mobileWarning) {
                    mobileWarning.style.display = 'none';
                }
                mobileInput.style.borderColor = '';
            }
        });

        mobileInput.addEventListener('blur', function(e) {
            const value = e.target.value;
            const phonePattern = /^09\d{9}$/;

            if (value !== '' && !phonePattern.test(value)) {
                if (!mobileWarning) {
                    const warning = document.createElement('span');
                    warning.className = 'validation-warning';
                    warning.id = 'seedlings-mobile-warning';
                    warning.style.cssText = 'color: #ff6b6b; font-size: 0.875rem; display: block; margin-top: 4px;';
                    warning.textContent = 'Contact number must be in format 09XXXXXXXXX (11 digits)';
                    mobileInput.parentNode.appendChild(warning);
                } else {
                    mobileWarning.style.display = 'block';
                }
                mobileInput.style.borderColor = '#ff6b6b';
            }
        });
    }
});



// ==============================================
// GLOBAL EXPORTS
// ==============================================

window.openFormSeedlings = openFormSeedlings;
window.closeFormSeedlings = closeFormSeedlings;
window.proceedToSeedlingsForm = proceedToSeedlingsForm;
window.showSeedlingsTab = showSeedlingsTab;
window.backToSeedlingsChoice = backToSeedlingsChoice;

// ==============================================
// QUICK VIEW MODAL (E-COMMERCE STYLE)
// ==============================================

// function showQuickView(itemId, itemName, categoryName, description, stock, unit, stockStatus, icon, imagePath) {
//     const modal = document.getElementById('quickViewModal');
//     if (!modal) return;

//     // Populate modal content
//     const qvImage = document.getElementById('qv-image');
//     const qvName = document.getElementById('qv-name');
//     const qvCategory = document.getElementById('qv-category');
//     const qvDescription = document.getElementById('qv-description');
//     const qvStock = document.getElementById('qv-stock');
//     const qvStockBadge = document.getElementById('qv-stock-badge');

//     // Set image
//     if (imagePath) {
//         qvImage.src = imagePath;
//         qvImage.style.display = 'block';
//     } else {
//         qvImage.style.display = 'none';
//     }

//     // Set content
//     qvName.textContent = itemName;
//     qvCategory.innerHTML = `<i class="fas ${icon}"></i> ${categoryName}`;
//     qvDescription.textContent = description || 'No description available';
//     qvStock.textContent = `${stock} ${unit}`;

//     // Set stock badge
//     let badgeClass = '';
//     let badgeText = '';
//     if (stockStatus === 'in_stock') {
//         badgeClass = 'in_stock';
//         badgeText = 'In Stock';
//     } else if (stockStatus === 'low_stock') {
//         badgeClass = 'low_stock';
//         badgeText = 'Low Stock';
//     } else {
//         badgeClass = 'out_of_stock';
//         badgeText = 'Out of Stock';
//     }
//     qvStockBadge.className = `qv-stock-badge ${badgeClass}`;
//     qvStockBadge.textContent = badgeText;

//     // Show modal
//     modal.style.display = 'flex';
//     document.body.style.overflow = 'hidden';

//     return false;
// }

// function closeQuickView(event) {
//     if (event) {
//         event.stopPropagation();
//     }

//     const modal = document.getElementById('quickViewModal');
//     if (!modal) return;

//     modal.style.display = 'none';
//     document.body.style.overflow = '';
//     return false;
// }

// Export quick view functions
window.showQuickView = showQuickView;
window.closeQuickView = closeQuickView;

// Export cart modal functions
window.toggleCartExpansion = toggleCartExpansion;
window.openCartModal = openCartModal;
window.closeCartModal = closeCartModal;
window.removeItemFromCart = removeItemFromCart;
window.updateCartItemsList = updateCartItemsList;
window.clearCartItems = clearCartItems;
window.proceedToApplication = proceedToApplication;

// Close modals on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeQuickView();
        closeCartModal();
    }
});

// Close cart modal when clicking outside
document.addEventListener('click', function(e) {
    const modal = document.getElementById('cartModalOverlay');
    if (modal && e.target === modal) {
        closeCartModal();
    }
});

// ==============================================
// PAGINATION FUNCTIONALITY
// ==============================================
let currentPage = 1;

function getItemsPerPage() {
    // 6 items on mobile (768px and below), 8 items on desktop
    return window.innerWidth <= 768 ? 6 : 8;
}

function updatePagination() {
    const itemsPerPage = getItemsPerPage();
    const items = Array.from(document.querySelectorAll('.seedlings-item-card'));
    // Get items that are not filtered out (only by category/search/stock filters, not pagination)
    const visibleItems = items.filter(item => {
        const hiddenByFilter = item.classList.contains('hidden');
        const hiddenBySearch = item.hasAttribute('data-search-hidden');
        return !hiddenByFilter && !hiddenBySearch;
    });

    const totalPages = Math.ceil(visibleItems.length / itemsPerPage);
    const pagination = document.getElementById('pagination');

    // Show/hide pagination based on number of items
    if (totalPages > 1) {
        pagination.style.display = 'flex';
    } else {
        pagination.style.display = 'none';
    }

    // Update pagination info
    document.getElementById('current-page').textContent = currentPage;
    document.getElementById('total-pages').textContent = totalPages;

    // Enable/disable buttons
    document.getElementById('prev-page').disabled = currentPage === 1;
    document.getElementById('next-page').disabled = currentPage === totalPages || totalPages === 0;

    // Show only items for current page
    visibleItems.forEach((item, index) => {
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;

        if (index >= startIndex && index < endIndex) {
            item.style.display = 'flex';
            item.removeAttribute('data-page-hidden');
        } else {
            item.style.display = 'none';
            item.setAttribute('data-page-hidden', 'true');
        }
    });

    // Scroll to top of items grid
    const itemsGrid = document.getElementById('items-grid');
    if (itemsGrid) {
        itemsGrid.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

function changePage(direction) {
    const itemsPerPage = getItemsPerPage();
    const items = Array.from(document.querySelectorAll('.seedlings-item-card'));
    // Count items that are visible after filters (not pagination)
    const visibleItems = items.filter(item => {
        const hiddenByFilter = item.classList.contains('hidden');
        const hiddenBySearch = item.hasAttribute('data-search-hidden');
        return !hiddenByFilter && !hiddenBySearch;
    });
    const totalPages = Math.ceil(visibleItems.length / itemsPerPage);

    currentPage += direction;

    // Boundary checks
    if (currentPage < 1) currentPage = 1;
    if (currentPage > totalPages) currentPage = totalPages;

    updatePagination();
}
// ============================================
// PICKUP DATE FIELD - COMPLETE FIX
// ============================================

function initPickupDateField() {
    const pickupInput = document.getElementById('seedlings-pickup_date');
    const displayDiv = document.getElementById('pickup-date-display');
    const displayText = document.getElementById('pickup-date-text');

    if (!pickupInput) return;

    // ✅ Set min/max dates (1-30 days from today)
    const today = new Date();
    const minDate = new Date(today);
    minDate.setDate(minDate.getDate() + 1); // 1 day from today
    
    const maxDate = new Date(today);
    maxDate.setDate(maxDate.getDate() + 30); // 30 days from today

    const formatDate = (date) => date.toISOString().split('T')[0];
    
    pickupInput.min = formatDate(minDate);
    pickupInput.max = formatDate(maxDate);

    // ============================================
    // DISABLE WEEKENDS IN HTML5 CALENDAR
    // ============================================
    pickupInput.addEventListener('click', function() {
        // This is a visual hint - actual validation happens on change
        console.log('Calendar opened - weekends will be disabled');
    });

    // ============================================
    // VALIDATE ON DATE CHANGE
    // ============================================
    pickupInput.addEventListener('change', function() {
        if (!this.value) {
            // User cleared the date
            displayDiv.style.display = 'none';
            return;
        }

        const selectedDate = new Date(this.value + 'T00:00:00');
        const dayOfWeek = selectedDate.getDay(); // 0=Sunday, 6=Saturday
        const dayName = selectedDate.toLocaleDateString('en-US', { weekday: 'long' });
        const fullDate = selectedDate.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });

        // ✅ CHECK IF WEEKEND (0 = Sunday, 6 = Saturday)
        if (dayOfWeek === 0 || dayOfWeek === 6) {
            this.value = ''; // Clear invalid selection
            displayDiv.style.display = 'none';
            
            // Show user-friendly warning
            toast.warning(
                `${dayName}s are closed. Please select a weekday (Monday-Friday).`,
                {
                    title: 'Weekend Not Available',
                    duration: 4000
                }
            );
            return;
        }

        // ✅ VALID WEEKDAY - SHOW CONFIRMATION
        displayText.innerHTML = `
            <i class="fas fa-check-circle" style="color: #40916c; margin-right: 8px;"></i>
            <strong>${fullDate}</strong> <span style="color: #666;">(${dayName})</span>
        `;
        displayDiv.style.display = 'block';
    });

    // ============================================
    // VALIDATE ON BLUR (When user leaves the field)
    // ============================================
    pickupInput.addEventListener('blur', function() {
        if (!this.value) return;

        const selectedDate = new Date(this.value + 'T00:00:00');
        const dayOfWeek = selectedDate.getDay();

        if (dayOfWeek === 0 || dayOfWeek === 6) {
            this.value = '';
            displayDiv.style.display = 'none';
            
            toast.warning(
                'Weekends are not available for pickup. Please select a weekday.',
                {
                    title: '❌ Invalid Selection',
                    duration: 5000
                }
            );
        }
    });
}

// ============================================
// CALL ON PAGE LOAD
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    initPickupDateField();
});
function disableWeekendsInDatePicker() {
    const pickupInput = document.getElementById('seedlings-pickup_date');
    
    pickupInput.addEventListener('change', function() {
        const selectedDate = new Date(this.value);
        const day = selectedDate.getDay();
        
        // 0 = Sunday, 6 = Saturday
        if (day === 0 || day === 6) {
            toast.warning('Saturdays and Sundays are closed. Please select a weekday.', {
                title: 'Weekend Not Available'
            });
            this.value = '';
            return;
        }
    });
}

// searchItems already calls updatePagination internally

// Wrap filterByStock to ensure pagination updates
const originalFilterByStock = filterByStock;
window.filterByStock = function() {
    originalFilterByStock.call(this);
};

// Wrap sortItems to ensure pagination updates
const originalSortItems = sortItems;
window.sortItems = function() {
    originalSortItems.call(this);
};

// Initialize pagination on page load
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        updatePagination();
    }, 500);
});

// Update pagination on window resize
let resizeTimer;
window.addEventListener('resize', function() {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(() => {
        currentPage = 1; // Reset to first page on resize
        updatePagination();
    }, 250);
});

// Export functions to window
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
window.changePage = changePage;
window.updatePagination = updatePagination;
window.initCategoryTabs = initCategoryTabs;
window.applyFilters = applyFilters;
window.showPickupDateField = showPickupDateField;

console.log('Modern Seedlings module loaded successfully');
