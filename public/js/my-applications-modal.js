// My Applications Modal - RSBSA Integration
// Fetch and display user's real RSBSA applications

/**
 * Load user applications in modal - WITH REAL RSBSA DATA
 */
function loadUserApplicationsInModal() {
    const grid = document.getElementById('applications-modal-grid');
    if (!grid) return;

    // Check if user is logged in
    if (!window.userData) {
        grid.innerHTML = `
            <div class="empty-applications">
                <h4>Please Log In</h4>
                <p>You need to be logged in to view your applications.</p>
                <button class="quick-action-btn" onclick="closeApplicationsModal(); openAuthModal('login');">
                    <span>🔐</span> Log In
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
        const statusIcon = getStatusIcon(app.status);
        const appIcon = getApplicationIcon(app.type);

        return `
            <div class="application-card ${statusClass}">
                <div class="application-header">
                    <div class="app-icon">${appIcon}</div>
                    <div class="app-title">
                        <h4>${app.type}</h4>
                        <p class="app-number">${app.application_number || app.reference_number || 'N/A'}</p>
                    </div>
                </div>
                
                <p class="app-description">${app.description || 'Application submitted successfully'}</p>
                
                <div class="app-details">
                    ${app.full_name ? `<div class="detail-item"><strong>Name:</strong> ${app.full_name}</div>` : ''}
                    ${app.livelihood ? `<div class="detail-item"><strong>Livelihood:</strong> ${app.livelihood}</div>` : ''}
                    ${app.barangay ? `<div class="detail-item"><strong>Barangay:</strong> ${app.barangay}</div>` : ''}
                </div>
                
                <div class="application-footer">
                    <div class="application-status status-${app.status.toLowerCase().replace(/[_\s]/g, '-')}">
                        ${statusIcon} ${formatStatus(app.status)}
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
            <div class="empty-icon">📋</div>
            <h4>No Applications Yet</h4>
            <p>You haven't submitted any applications yet. Browse our services to get started!</p>
            <button class="quick-action-btn" onclick="closeApplicationsModal(); document.getElementById('services').scrollIntoView({ behavior: 'smooth' });">
                <span>🌾</span> Browse Services
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
        'under_review': 'status-review',
        'approved': 'status-approved',
        'rejected': 'status-rejected',
        'processing': 'status-processing'
    };
    return statusMap[status.toLowerCase()] || 'status-default';
}

/**
 * Get status icon
 */
function getStatusIcon(status) {
    const iconMap = {
        'pending': '⏳',
        'under_review': '🔍',
        'approved': '✅',
        'rejected': '❌',
        'processing': '⚙️'
    };
    return iconMap[status.toLowerCase()] || '📄';
}

/**
 * Get application type icon
 */
function getApplicationIcon(type) {
    const iconMap = {
        'RSBSA Registration': '📋',
        'Seedlings Request': '🌱',
        'FishR Registration': '🐟',
        'BoatR Registration': '⛵',
        'Training Request': '📚'
    };
    return iconMap[type] || '📄';
}

/**
 * Format status text
 */
function formatStatus(status) {
    return status
        .replace(/_/g, ' ')
        .replace(/\b\w/g, l => l.toUpperCase());
}

/**
 * Format application date
 */
function formatApplicationDate(dateString) {
    if (!dateString) return 'N/A';

    const date = new Date(dateString);
    const now = new Date();
    const diffTime = Math.abs(now - date);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    if (diffDays === 0) return 'Today';
    if (diffDays === 1) return 'Yesterday';
    if (diffDays < 7) return `${diffDays} days ago`;
    if (diffDays < 30) return `${Math.floor(diffDays / 7)} weeks ago`;
    
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

// Add CSS for application cards
const applicationStyles = `
<style>
.application-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    border-left: 4px solid #ccc;
}

.application-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}

.application-card.status-pending {
    border-left-color: #ffc107;
}

.application-card.status-review {
    border-left-color: #17a2b8;
}

.application-card.status-approved {
    border-left-color: #28a745;
}

.application-card.status-rejected {
    border-left-color: #dc3545;
}

.application-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
}

.app-icon {
    font-size: 32px;
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    border-radius: 8px;
}

.app-title h4 {
    margin: 0;
    font-size: 16px;
    color: #333;
}

.app-number {
    margin: 4px 0 0 0;
    font-size: 12px;
    color: #666;
    font-family: monospace;
}

.app-description {
    color: #666;
    font-size: 14px;
    margin-bottom: 12px;
    line-height: 1.5;
}

.app-details {
    background: #f8f9fa;
    padding: 10px;
    border-radius: 6px;
    margin-bottom: 12px;
}

.detail-item {
    font-size: 13px;
    color: #555;
    margin-bottom: 4px;
}

.detail-item:last-child {
    margin-bottom: 0;
}

.detail-item strong {
    color: #333;
}

.application-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 12px;
}

.application-status {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-under-review, .status-processing {
    background: #d1ecf1;
    color: #0c5460;
}

.status-approved {
    background: #d4edda;
    color: #155724;
}

.status-rejected {
    background: #f8d7da;
    color: #721c24;
}

.application-date {
    font-size: 12px;
    color: #999;
}

.app-remarks {
    margin-top: 12px;
    padding: 10px;
    background: #fff3cd;
    border-radius: 6px;
    font-size: 13px;
    color: #856404;
}

.empty-applications {
    grid-column: 1 / -1;
    text-align: center;
    padding: 60px 20px;
}

.empty-icon {
    font-size: 64px;
    margin-bottom: 20px;
    opacity: 0.5;
}

.empty-applications h4 {
    font-size: 24px;
    color: #333;
    margin-bottom: 12px;
}

.empty-applications p {
    color: #666;
    margin-bottom: 24px;
}

.quick-action-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.quick-action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.loading-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: 60px 20px;
}

.loader {
    border: 4px solid #f3f3f3;
    border-top: 4px solid #667eea;
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

// Inject styles
if (!document.getElementById('application-card-styles')) {
    const styleEl = document.createElement('div');
    styleEl.id = 'application-card-styles';
    styleEl.innerHTML = applicationStyles;
    document.head.appendChild(styleEl);
}

// Export function
window.loadUserApplicationsInModal = loadUserApplicationsInModal;

console.log('✅ My Applications RSBSA integration loaded');