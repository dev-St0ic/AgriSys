// Enhanced My Applications Modal - User Application Management (Simplified)
// Fetch and display user's applications with professional styling
// Updated: Added Under Review filter, Service-based filtering, reduced redundancy
// FIXED: Philippine Timezone (UTC+8) with proper calendar day comparison

/**
 * Load user applications in modal
 */
function loadUserApplicationsInModal() {
    const grid = document.getElementById('applications-modal-grid');
    if (!grid) return;

    // Reset filter buttons first
    resetApplicationFilters();

    // Check if user is logged in
    if (!window.userData) {
        grid.innerHTML = `
            <div class="empty-state">
                <div class="empty-icon">
                    <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="8.5" cy="7" r="4"/>
                        <path d="M20 8v6M23 11h-6"/>
                    </svg>
                </div>
                <h2 class="empty-title">Please Log In</h2>
                <p class="empty-text">You need to be logged in to view your applications.</p>
                <button class="primary-btn" onclick="closeApplicationsModal(); openAuthModal('login');">
                    Log In to Your Account
                </button>
            </div>
        `;
        return;
    }

    // Show loading state
    grid.innerHTML = `
        <div class="loading">
            <div class="loader"></div>
            <p>Loading your applications...</p>
        </div>
    `;

    // Fetch real applications from backend
    fetch('/api/user/applications/all', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.applications && data.applications.length > 0) {
            renderApplicationsInModal(data.applications);
            updateApplicationStatistics(data.applications);
            populateServiceFilters(data.applications);
        } else {
            renderEmptyApplications();
        }
    })
    .catch(error => {
        console.error('Error loading applications:', error);
        renderEmptyApplications();
    });
}

/**
 * Update statistics based on applications
 */
function updateApplicationStatistics(applications) {
    const statsContainer = document.getElementById('applications-stats');
    if (!statsContainer) return;

    const total = applications.length;
    const pending = applications.filter(app => ['pending', 'under_review', 'processing'].includes(app.status.toLowerCase())).length;
    const approved = applications.filter(app => app.status.toLowerCase() === 'approved').length;
    const rejected = applications.filter(app => app.status.toLowerCase() === 'rejected').length;

    statsContainer.innerHTML = `
        <div class="stats-section">
            <div class="stat-card">
                <div class="stat-number">${total}</div>
                <div class="stat-label">Total Applications</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">${pending}</div>
                <div class="stat-label">Pending Review</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">${approved}</div>
                <div class="stat-label">Approved</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">${rejected}</div>
                <div class="stat-label">Rejected</div>
            </div>
        </div>
    `;
}

/**
 * Populate service filter buttons based on applications
 */
function populateServiceFilters(applications) {
    const serviceFilterContainer = document.getElementById('service-filter-container');
    if (!serviceFilterContainer) return;

    // Get unique services from applications
    const uniqueServices = [...new Set(applications.map(app => app.type).filter(Boolean))];
    
    if (uniqueServices.length === 0) return;

    let filterHTML = '<button class="service-filter-btn active" onclick="filterApplicationsByService(\'all\', this)">All Services</button>';
    
    uniqueServices.forEach(service => {
        const serviceId = service.toLowerCase().replace(/\s+/g, '-');
        filterHTML += `<button class="service-filter-btn" onclick="filterApplicationsByService('${serviceId}', this)" data-service="${service}">${service}</button>`;
    });

    serviceFilterContainer.innerHTML = filterHTML;
}

/**
 * Render applications in modal
 * Updated: Removed category badge, reduced redundancy, added service data attribute
 */
function renderApplicationsInModal(applications) {
    const grid = document.getElementById('applications-modal-grid');
    if (!grid) return;

    if (applications.length === 0) {
        renderEmptyApplications();
        return;
    }

    grid.innerHTML = applications.map(app => {
        const statusClass = getStatusClass(app.status);
        const statusLabel = formatStatus(app.status);
        const isRejected = app.status.toLowerCase() === 'rejected';
        const serviceId = app.type.toLowerCase().replace(/\s+/g, '-');

        return `
            <div class="app-card ${statusClass}" data-service="${serviceId}" data-status="${app.status.toLowerCase()}">
                <div class="card-header">
                    <div class="app-type">
                        <h3 class="app-title">${app.type}</h3>
                    </div>
                    <span class="status-badge ${statusClass}">
                        <span class="status-dot"></span>
                        ${statusLabel}
                    </span>
                </div>

                <div class="card-body">
                    <div class="ref-number">
                        <span class="ref-label">Reference:</span>
                        <span class="ref-value">${app.application_number || app.reference_number || 'N/A'}</span>
                    </div>

                    ${app.full_name || app.barangay || app.livelihood ? `
                        <div class="info-grid">
                            ${app.full_name ? `
                                <div class="info-item">
                                    <span class="info-label">Name</span>
                                    <span class="info-value">${app.full_name}</span>
                                </div>
                            ` : ''}
                            ${app.barangay ? `
                                <div class="info-item">
                                    <span class="info-label">Barangay</span>
                                    <span class="info-value">${app.barangay}</span>
                                </div>
                            ` : ''}
                            ${app.livelihood ? `
                                <div class="info-item">
                                    <span class="info-label">Sector</span>
                                    <span class="info-value">${app.livelihood}</span>
                                </div>
                            ` : ''}
                        </div>
                    ` : ''}
                </div>

                <div class="card-footer">
                    <div class="submitted-date">
                        <span class="date-label">Submitted</span>
                        <span class="date-value">${formatApplicationDate(app.submitted_at || app.date || app.created_at)}</span>
                    </div>
                    ${isRejected ? `
                        <button class="action-btn resubmit-btn" onclick="handleResubmit('${app.type}')">
                            Resubmit
                        </button>
                    ` : `
                    `}
                </div>
            </div>
        `;
    }).join('');
}

/**
 * Handle Resubmit button click - Navigate to Services section and open the form
 */
function handleResubmit(applicationType) {
    closeApplicationsModal();

    const typeMap = {
        'RSBSA Registration': {
            formId: 'new-rsbsa',
            openFunction: (e) => openRSBSAForm(e),
            path: '/services/rsbsa'
        },
        'Supply Request': {
            formId: 'seedlings-form',
            openFunction: (e) => openFormSeedlings(e),
            path: '/services/seedlings'
        },
        'FishR Registration': {
            formId: 'fishr-form',
            openFunction: (e) => openFormFishR(e),
            path: '/services/fishr'
        },
        'BoatR Registration': {
            formId: 'boatr-form',
            openFunction: (e) => openFormBoatR(e),
            path: '/services/boatr'
        },
        'Training Request': {
            formId: 'training-form',
            openFunction: (e) => openFormTraining(e),
            path: '/services/training'
        }
    };

    const formConfig = typeMap[applicationType];

    if (formConfig) {
        // Scroll to top immediately
        document.documentElement.scrollTop = 0;
        document.body.scrollTop = 0;
        window.scrollTo({ top: 0, behavior: 'smooth' });
        
        // Create synthetic event
        const syntheticEvent = new Event('click');
        syntheticEvent.preventDefault = () => {};
        
        // Call the form open function
        formConfig.openFunction(syntheticEvent);
        
        // Scroll to the form after a small delay to ensure it's displayed
        setTimeout(() => {
            const formElement = document.getElementById(formConfig.formId);
            if (formElement) {
                formElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }, 600);
    } else {
        // Scroll to top
        document.documentElement.scrollTop = 0;
        document.body.scrollTop = 0;
        window.scrollTo({ top: 0, behavior: 'smooth' });
        
        const servicesSection = document.getElementById('services');
        if (servicesSection) {
            servicesSection.scrollIntoView({ behavior: 'smooth' });
        }
        agrisysModal.info('Please select your desired service from the available options.', { title: 'Service Selection' });
    }
}

/**
 * Render empty state
 */
function renderEmptyApplications() {
    const grid = document.getElementById('applications-modal-grid');
    if (!grid) return;

    grid.innerHTML = `
        <div class="empty-state">
            <div class="empty-icon">
                <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="12" y1="13" x2="8" y2="13"/>
                    <line x1="12" y1="17" x2="8" y2="17"/>
                </svg>
            </div>
            <h2 class="empty-title">No Applications Yet</h2>
            <p class="empty-text">You haven't submitted any applications. Start by exploring our available services designed to support your agricultural needs.</p>
            <button class="primary-btn" onclick="closeApplicationsModal(); document.getElementById('services').scrollIntoView({ behavior: 'smooth' });">
                Browse Available Services
            </button>
        </div>
    `;
}

/**
 * Get status class for styling
 */
function getStatusClass(status) {
    const statusMap = {
        'pending': 'status-pending',
        'under_review': 'status-under-review',
        'processing': 'status-processing',
        'approved': 'status-approved',
        'rejected': 'status-rejected'
    };
    return statusMap[status.toLowerCase()] || 'status-default';
}

/**
 * Format status text
 */
function formatStatus(status) {
    const statusMap = {
        'pending': 'Pending',
        'under_review': 'Under Review',
        'approved': 'Approved',
        'rejected': 'Rejected'
    };
    return statusMap[status.toLowerCase()] || status;
}

/**
 * Format application date - PHILIPPINE TIMEZONE (UTC+8) - FIXED VERSION
 * Uses calendar day comparison and Math.floor() for accurate date display
 */
function formatApplicationDate(dateString) {
    if (!dateString) return 'Date unknown';

    try {
        const date = new Date(dateString);
        const now = new Date();
        
        // Convert to Philippine timezone (Asia/Manila = UTC+8)
        const phDate = new Date(date.toLocaleString('en-US', { timeZone: 'Asia/Manila' }));
        const phNow = new Date(now.toLocaleString('en-US', { timeZone: 'Asia/Manila' }));
        
        // Compare only calendar dates (ignore time of day)
        const phDateOnly = new Date(phDate.getFullYear(), phDate.getMonth(), phDate.getDate());
        const phNowOnly = new Date(phNow.getFullYear(), phNow.getMonth(), phNow.getDate());
        
        // Calculate difference in calendar days
        const diffTime = phNowOnly - phDateOnly;
        const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24)); // Use Math.floor()

        if (diffDays === 0) return 'Today';        // Same calendar day in PH
        if (diffDays === 1) return 'Yesterday';    // 1 calendar day ago in PH
        if (diffDays < 7) return `${diffDays}d ago`;
        if (diffDays < 30) return `${Math.floor(diffDays / 7)}w ago`;
        
        // For older dates, show formatted date in Philippine timezone
        return phDate.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            timeZone: 'Asia/Manila'
        });
    } catch (error) {
        console.error('Error formatting application date:', error);
        return 'Invalid date';
    }
}

/**
 * Filter applications by status - UPDATED to handle both status and service filtering
 */
function filterApplicationsByStatus(status) {
    const cards = document.querySelectorAll('.app-card');
    const buttons = document.querySelectorAll('.filter-btn');

    // Remove active class from all buttons
    buttons.forEach(btn => btn.classList.remove('active'));
    
    // Add active class to clicked button
    if (event && event.target) {
        event.target.classList.add('active');
    }

    // Always remove the empty filter message first
    removeEmptyFilterMessage();

    // Reset service filters when status filter is used
    resetServiceFilters();

    if (status === 'all') {
        // Show all cards
        cards.forEach(card => card.style.display = '');
    } else {
        // Filter by specific status
        let visibleCount = 0;
        cards.forEach(card => {
            const cardStatus = card.dataset.status;
            const isVisible = cardStatus === status;
            card.style.display = isVisible ? '' : 'none';
            if (isVisible) visibleCount++;
        });

        // Show empty message if no results
        if (visibleCount === 0 && cards.length > 0) {
            showEmptyFilterMessage(status);
        }
    }
}

/**
 * Filter applications by service
 */
function filterApplicationsByService(serviceId, buttonElement) {
    const cards = document.querySelectorAll('.app-card');
    const buttons = document.querySelectorAll('.service-filter-btn');

    // Remove active class from all service filter buttons
    buttons.forEach(btn => btn.classList.remove('active'));
    
    // Add active class to clicked button
    if (buttonElement) {
        buttonElement.classList.add('active');
    }

    // Always remove the empty filter message first
    removeEmptyFilterMessage();

    // Reset status filters when service filter is used
    resetStatusFilters();

    if (serviceId === 'all') {
        // Show all cards
        cards.forEach(card => card.style.display = '');
    } else {
        // Filter by specific service
        let visibleCount = 0;
        cards.forEach(card => {
            const cardService = card.dataset.service;
            const isVisible = cardService === serviceId;
            card.style.display = isVisible ? '' : 'none';
            if (isVisible) visibleCount++;
        });

        // Show empty message if no results
        if (visibleCount === 0 && cards.length > 0) {
            showEmptyServiceFilterMessage(serviceId);
        }
    }
}

/**
 * Show empty state message when status filter has no results
 */
function showEmptyFilterMessage(status) {
    let messageContainer = document.querySelector('.empty-filter-message');
    if (!messageContainer) {
        messageContainer = document.createElement('div');
        messageContainer.className = 'empty-filter-message';
        const grid = document.getElementById('applications-modal-grid');
        if (grid && grid.parentElement) {
            grid.parentElement.appendChild(messageContainer);
        } else {
            return;
        }
    }

    const messages = {
        'pending': {
            title: 'No Pending Applications',
            text: 'You don\'t have any applications currently pending.'
        },
        'under_review': {
            title: 'No Applications Under Review',
            text: 'You don\'t have any applications currently being reviewed.'
        },
        'processing': {
            title: 'No Applications Being Processed',
            text: 'You don\'t have any applications currently being processed.'
        },
        'approved': {
            title: 'No Approved Applications',
            text: 'You don\'t have any approved applications yet. Submit an application to get started!',
            button: 'Browse Services'
        },
        'rejected': {
            title: 'No Rejected Applications',
            text: 'Great! You don\'t have any rejected applications.'
        }
    };

    const message = messages[status] || {
        title: 'No Applications Found',
        text: 'There are no applications with this status.'
    };

    messageContainer.innerHTML = `
        <div class="empty-filter-state">
            <div class="empty-filter-icon">
                <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <line x1="9" y1="13" x2="15" y2="13"/>
                    <line x1="9" y1="17" x2="15" y2="17"/>
                </svg>
            </div>
            <h3 class="empty-filter-title">${message.title}</h3>
            <p class="empty-filter-text">${message.text}</p>
            ${message.button ? `<button class="empty-filter-btn" onclick="filterApplicationsByStatus('all');">${message.button}</button>` : ''}
        </div>
    `;
}

/**
 * Show empty state message when service filter has no results
 */
function showEmptyServiceFilterMessage(serviceId) {
    let messageContainer = document.querySelector('.empty-filter-message');
    if (!messageContainer) {
        messageContainer = document.createElement('div');
        messageContainer.className = 'empty-filter-message';
        const grid = document.getElementById('applications-modal-grid');
        if (grid && grid.parentElement) {
            grid.parentElement.appendChild(messageContainer);
        } else {
            return;
        }
    }

    messageContainer.innerHTML = `
        <div class="empty-filter-state">
            <div class="empty-filter-icon">
                <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <line x1="9" y1="13" x2="15" y2="13"/>
                    <line x1="9" y1="17" x2="15" y2="17"/>
                </svg>
            </div>
            <h3 class="empty-filter-title">No Applications for This Service</h3>
            <p class="empty-filter-text">You don\'t have any applications for the selected service.</p>
            <button class="empty-filter-btn" onclick="filterApplicationsByService('all', document.querySelector('.service-filter-btn'));">View All Services</button>
        </div>
    `;
}

/**
 * Remove empty filter message
 */
function removeEmptyFilterMessage() {
    const messageContainer = document.querySelector('.empty-filter-message');
    if (messageContainer) {
        messageContainer.remove();
    }
}

/**
 * Reset status filter buttons to show 'All Applications'
 */
function resetStatusFilters() {
    const buttons = document.querySelectorAll('.filter-btn');
    buttons.forEach(btn => btn.classList.remove('active'));
    
    const allButton = Array.from(buttons).find(btn => btn.textContent.trim() === 'All Applications');
    if (allButton) {
        allButton.classList.add('active');
    } else if (buttons.length > 0) {
        buttons[0].classList.add('active');
    }
}

/**
 * Reset service filter buttons to show 'All Services'
 */
function resetServiceFilters() {
    const buttons = document.querySelectorAll('.service-filter-btn');
    buttons.forEach(btn => btn.classList.remove('active'));
    
    const allButton = Array.from(buttons).find(btn => btn.textContent.trim() === 'All Services');
    if (allButton) {
        allButton.classList.add('active');
    } else if (buttons.length > 0) {
        buttons[0].classList.add('active');
    }
}

/**
 * Reset ALL filter buttons on modal close/open
 */
function resetApplicationFilters() {
    // Reset status filters
    const statusButtons = document.querySelectorAll('.filter-btn');
    console.log('Resetting status filters - found', statusButtons.length, 'buttons');
    
    statusButtons.forEach((btn, index) => {
        if (btn.classList.contains('active')) {
            console.log('Removing active from status button', index, '(' + btn.textContent.trim() + ')');
            btn.classList.remove('active');
        }
    });
    
    const allStatusButton = Array.from(statusButtons).find(btn => btn.textContent.trim() === 'All Applications');
    if (allStatusButton) {
        allStatusButton.classList.add('active');
        console.log('All Applications button set to active');
    } else if (statusButtons.length > 0) {
        statusButtons[0].classList.add('active');
    }

    // Reset service filters
    const serviceButtons = document.querySelectorAll('.service-filter-btn');
    console.log('Resetting service filters - found', serviceButtons.length, 'buttons');
    
    serviceButtons.forEach((btn, index) => {
        if (btn.classList.contains('active')) {
            console.log('Removing active from service button', index, '(' + btn.textContent.trim() + ')');
            btn.classList.remove('active');
        }
    });
    
    const allServiceButton = Array.from(serviceButtons).find(btn => btn.textContent.trim() === 'All Services');
    if (allServiceButton) {
        allServiceButton.classList.add('active');
        console.log('All Services button set to active');
    } else if (serviceButtons.length > 0) {
        serviceButtons[0].classList.add('active');
    }
    
    // Show all application cards
    const cards = document.querySelectorAll('.app-card');
    cards.forEach(card => {
        card.style.display = '';
    });
    
    console.log('Filter reset complete - showing', cards.length, 'cards');
}

// Export functions globally
window.loadUserApplicationsInModal = loadUserApplicationsInModal;
window.renderApplicationsInModal = renderApplicationsInModal;
window.renderEmptyApplications = renderEmptyApplications;
window.filterApplicationsByStatus = filterApplicationsByStatus;
window.filterApplicationsByService = filterApplicationsByService;
window.resetApplicationFilters = resetApplicationFilters;
window.resetStatusFilters = resetStatusFilters;
window.resetServiceFilters = resetServiceFilters;
window.handleResubmit = handleResubmit;
window.showEmptyFilterMessage = showEmptyFilterMessage;
window.showEmptyServiceFilterMessage = showEmptyServiceFilterMessage;
window.removeEmptyFilterMessage = removeEmptyFilterMessage;
window.populateServiceFilters = populateServiceFilters;

