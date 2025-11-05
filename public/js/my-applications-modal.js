// My Applications Modal - User Application Management
// Fetch and display user's applications with professional styling

/**
 * Load user applications in modal
 */
function loadUserApplicationsInModal() {
    const grid = document.getElementById('applications-modal-grid');
    if (!grid) return;

    // Check if user is logged in
    if (!window.userData) {
        grid.innerHTML = `
            <div class="empty-applications">
                <div class="empty-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2a10 10 0 0 1 10 10v3.5a4.5 4.5 0 0 1-4.5 4.5"></path>
                        <path d="M12 2a10 10 0 0 0-10 10v3.5A4.5 4.5 0 0 0 6.5 20"></path>
                        <line x1="12" y1="6" x2="12" y2="18"></line>
                    </svg>
                </div>
                <h4>Please Log In</h4>
                <p>You need to be logged in to view your applications.</p>
                <button class="quick-action-btn" onclick="closeApplicationsModal(); openAuthModal('login');">
                    Log In to Your Account
                </button>
            </div>
        `;
        return;
    }

    // Show loading state
    grid.innerHTML = `
        <div class="loading-state">
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
        if (data.success && data.applications) {
            renderApplicationsInModal(data.applications);
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
 * Render applications in modal
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
        const appTypeLabel = formatApplicationType(app.type);

        return `
            <div class="application-card ${statusClass}">
                <div class="application-header">
                    <div class="app-type-badge">${appTypeLabel}</div>
                    <div class="app-title">
                        <h4>${app.type}</h4>
                        ${app.application_number ? `<p class="app-number">Ref: ${app.application_number}</p>` : ''}
                    </div>
                </div>
                
                ${app.description ? `<p class="app-description">${app.description}</p>` : ''}
                
                <div class="app-details">
                    ${app.full_name ? `<div class="detail-item"><span class="label">Name:</span> <span class="value">${app.full_name}</span></div>` : ''}
                    ${app.livelihood ? `<div class="detail-item"><span class="label">Livelihood:</span> <span class="value">${app.livelihood}</span></div>` : ''}
                    ${app.barangay ? `<div class="detail-item"><span class="label">Barangay:</span> <span class="value">${app.barangay}</span></div>` : ''}
                </div>
                
                <div class="application-footer">
                    <div class="application-status status-${app.status.toLowerCase().replace(/[_\s]/g, '-')}">
                        <span class="status-dot"></span>
                        <span class="status-text">${statusLabel}</span>
                    </div>
                    <div class="application-date">
                        ${formatApplicationDate(app.submitted_at || app.date || app.created_at)}
                    </div>
                </div>

                ${app.remarks ? `<div class="app-remarks"><strong>Remarks:</strong> ${app.remarks}</div>` : ''}
            </div>
        `;
    }).join('');
}

/**
 * Render empty state
 */
function renderEmptyApplications() {
    const grid = document.getElementById('applications-modal-grid');
    if (!grid) return;

    grid.innerHTML = `
        <div class="empty-applications">
            <div class="empty-icon">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
            </div>
            <h4>No Applications Yet</h4>
            <p>You haven't submitted any applications. Browse our services to get started.</p>
            <button class="quick-action-btn" onclick="closeApplicationsModal(); document.getElementById('services').scrollIntoView({ behavior: 'smooth' });">
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
        'approved': 'status-approved',
        'rejected': 'status-rejected',
        'processing': 'status-processing'
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
        'rejected': 'Rejected',
        'processing': 'Processing'
    };
    return statusMap[status.toLowerCase()] || status;
}

/**
 * Format application type text
 */
function formatApplicationType(type) {
    const typeMap = {
        'RSBSA Registration': 'RSBSA',
        'Seedlings Request': 'Seedlings',
        'FishR Registration': 'Fishing',
        'BoatR Registration': 'Boat',
        'Training Request': 'Training'
    };
    return typeMap[type] || type;
}

/**
 * Format application date
 */
function formatApplicationDate(dateString) {
    if (!dateString) return 'Date unknown';

    const date = new Date(dateString);
    const now = new Date();
    const diffTime = Math.abs(now - date);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    if (diffDays === 0) return 'Submitted today';
    if (diffDays === 1) return 'Submitted yesterday';
    if (diffDays < 7) return `Submitted ${diffDays} days ago`;
    if (diffDays < 30) return `Submitted ${Math.floor(diffDays / 7)} weeks ago`;
    
    return 'Submitted ' + date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

// ==========================================
// STYLES FOR APPLICATION CARDS
// ==========================================

const applicationStyles = `
<style>
.application-card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    border-left: 5px solid #d0d0d0;
}

.application-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
}

.application-card.status-pending {
    border-left-color: #f59e0b;
}

.application-card.status-under-review {
    border-left-color: #0ea5e9;
}

.application-card.status-approved {
    border-left-color: #10b981;
}

.application-card.status-rejected {
    border-left-color: #ef4444;
}

.application-card.status-processing {
    border-left-color: #8b5cf6;
}

.application-header {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 12px;
}

.app-type-badge {
    background: #f3f4f6;
    color: #374151;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    white-space: nowrap;
}

.app-title h4 {
    margin: 0;
    font-size: 16px;
    color: #1f2937;
    font-weight: 600;
}

.app-number {
    margin: 4px 0 0 0;
    font-size: 12px;
    color: #6b7280;
    font-family: 'Courier New', monospace;
}

.app-description {
    color: #4b5563;
    font-size: 14px;
    margin-bottom: 12px;
    line-height: 1.6;
}

.app-details {
    background: #f9fafb;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 12px;
    border: 1px solid #e5e7eb;
}

.detail-item {
    font-size: 13px;
    color: #4b5563;
    margin-bottom: 6px;
    display: flex;
    gap: 8px;
}

.detail-item:last-child {
    margin-bottom: 0;
}

.detail-item .label {
    font-weight: 600;
    color: #1f2937;
    min-width: 80px;
}

.detail-item .value {
    color: #6b7280;
}

.application-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px solid #e5e7eb;
}

.application-status {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
}

.status-pending .status-dot {
    background: #f59e0b;
}

.status-under-review .status-dot {
    background: #0ea5e9;
}

.status-approved .status-dot {
    background: #10b981;
}

.status-rejected .status-dot {
    background: #ef4444;
}

.status-processing .status-dot {
    background: #8b5cf6;
}

.status-pending {
    background: #fef3c7;
    color: #92400e;
}

.status-under-review {
    background: #cffafe;
    color: #0c4a6e;
}

.status-approved {
    background: #dcfce7;
    color: #166534;
}

.status-rejected {
    background: #fee2e2;
    color: #7f1d1d;
}

.status-processing {
    background: #ede9fe;
    color: #5b21b6;
}

.application-date {
    font-size: 12px;
    color: #9ca3af;
}

.app-remarks {
    margin-top: 12px;
    padding: 10px 12px;
    background: #fef3c7;
    border-radius: 6px;
    font-size: 13px;
    color: #92400e;
    border-left: 3px solid #f59e0b;
}

.empty-applications {
    grid-column: 1 / -1;
    text-align: center;
    padding: 60px 20px;
}

.empty-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    margin-bottom: 20px;
    color: #d1d5db;
}

.empty-icon svg {
    stroke-linecap: round;
    stroke-linejoin: round;
}

.empty-applications h4 {
    font-size: 22px;
    color: #1f2937;
    margin-bottom: 8px;
    font-weight: 600;
}

.empty-applications p {
    color: #6b7280;
    margin-bottom: 24px;
    font-size: 14px;
}

.quick-action-btn {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
    color: white;
    border: none;
    padding: 12px 28px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.quick-action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
}

.quick-action-btn:active {
    transform: translateY(0);
}

.loading-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: 60px 20px;
}

.loader {
    border: 4px solid #e5e7eb;
    border-top: 4px solid #059669;
    border-radius: 50%;
    width: 48px;
    height: 48px;
    animation: spin 1s linear infinite;
    margin: 0 auto 20px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
`;

// Inject styles if not already present
if (!document.getElementById('application-card-styles')) {
    const styleEl = document.createElement('div');
    styleEl.id = 'application-card-styles';
    styleEl.innerHTML = applicationStyles;
    document.head.appendChild(styleEl);
}

// Export functions globally
window.loadUserApplicationsInModal = loadUserApplicationsInModal;
window.renderApplicationsInModal = renderApplicationsInModal;
window.renderEmptyApplications = renderEmptyApplications;

console.log('Applications Modal Module Loaded Successfully');