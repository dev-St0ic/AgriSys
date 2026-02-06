// ==============================================
// AGRISYS MODAL NOTIFICATION SYSTEM (MOBILE OPTIMIZED)
// Consistent, modern modal-based notifications
// Replaces browser alert() calls with styled modals
// ==============================================

class AgrisysModal {
    constructor() {
        this.modalContainer = null;
        this.init();
    }

    init() {
        // Create modal container if not exists
        if (!document.getElementById('agrisys-modal-container')) {
            this.createModalStyles();
            this.createModalContainer();
        }
        this.modalContainer = document.getElementById('agrisys-modal-container');
    }

    createModalStyles() {
        if (document.getElementById('agrisys-modal-styles')) return;

        const styles = document.createElement('style');
        styles.id = 'agrisys-modal-styles';
        styles.textContent = `
            /* Modal Overlay */
            .agrisys-modal-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.6);
                backdrop-filter: blur(4px);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 99999;
                opacity: 0;
                visibility: hidden;
                transition: opacity 0.3s ease, visibility 0.3s ease;
                padding: 20px;
            }

            .agrisys-modal-overlay.show {
                opacity: 1;
                visibility: visible;
            }

            /* Modal Container */
            .agrisys-modal {
                background: #ffffff;
                border-radius: 16px;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
                max-width: 420px;
                width: 90%;
                max-height: 90vh;
                overflow: hidden;
                transform: scale(0.9) translateY(20px);
                transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
                position: relative;
            }

            .agrisys-modal-overlay.show .agrisys-modal {
                transform: scale(1) translateY(0);
            }

            /* Modal Header */
            .agrisys-modal-header {
                background: linear-gradient(135deg, #40916c 0%, #2d6a4f 100%);
                color: white;
                padding: 24px 24px 16px;
                display: flex;
                align-items: center;
                gap: 16px;
                border-bottom: none;
                border-radius: 16px 16px 0 0;
            }

            .agrisys-modal-icon {
                width: 52px;
                height: 52px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 24px;
                flex-shrink: 0;
            }

            /* Icon Colors */
            .agrisys-modal-success .agrisys-modal-icon {
                background: linear-gradient(135deg, #d1fae5, #a7f3d0);
                color: #065f46;
            }

            .agrisys-modal-error .agrisys-modal-icon {
                background: linear-gradient(135deg, #fee2e2, #fecaca);
                color: #991b1b;
            }

            .agrisys-modal-warning .agrisys-modal-icon {
                background: linear-gradient(135deg, #fef3c7, #fde68a);
                color: #92400e;
            }

            .agrisys-modal-info .agrisys-modal-icon {
                background: linear-gradient(135deg, #dbeafe, #bfdbfe);
                color: #1e40af;
            }

            /* Type-specific header backgrounds */
            .agrisys-modal-error .agrisys-modal-header {
                background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            }

            .agrisys-modal-warning .agrisys-modal-header {
                background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            }

            .agrisys-modal-info .agrisys-modal-header {
                background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            }

            .agrisys-modal-title-container {
                flex: 1;
            }

            .agrisys-modal-title {
                margin: 0;
                font-size: 18px;
                font-weight: 600;
                color: #ffffff;
                line-height: 1.3;
            }

            .agrisys-modal-subtitle {
                margin: 4px 0 0;
                font-size: 13px;
                color: rgba(255, 255, 255, 0.9);
            }

            /* Modal Body */
            .agrisys-modal-body {
                padding: 20px 24px;
                max-height: 300px;
                overflow-y: auto;
            }

            .agrisys-modal-message {
                color: #374151;
                font-size: 15px;
                line-height: 1.6;
                margin: 0;
                white-space: pre-line;
            }

            .agrisys-modal-reference {
                margin-top: 16px;
                padding: 12px 16px;
                background: #f3f4f6;
                border-radius: 8px;
                border: 4px solid #40916c;
            }

            .agrisys-modal-reference-label {
                font-size: 12px;
                color: #0550e6;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                margin-bottom: 4px;
            }

            .agrisys-modal-reference-value {
                font-size: 16px;
                font-weight: 600;
                color: #40916c;
                font-family: 'Monaco', 'Menlo', monospace;
            }

            /* Error List Styling */
            .agrisys-modal-error-list {
                list-style: none;
                padding: 0;
                margin: 0;
            }

            .agrisys-modal-error-list li {
                padding: 8px 0;
                border-bottom: 1px solid #f3f4f6;
                display: flex;
                align-items: flex-start;
                gap: 8px;
            }

            .agrisys-modal-error-list li:last-child {
                border-bottom: none;
            }

            .agrisys-modal-error-list li::before {
                content: "•";
                color: #dc3545;
                font-weight: bold;
                flex-shrink: 0;
            }

            /* Modal Footer */
            .agrisys-modal-footer {
                padding: 16px 24px 24px;
                display: flex;
                justify-content: flex-end;
                gap: 12px;
            }

            .agrisys-modal-btn {
                padding: 12px 24px;
                border-radius: 8px;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
                border: none;
                transition: all 0.2s ease;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .agrisys-modal-btn-primary {
                background: #40916c;
                color: white;
            }

            .agrisys-modal-btn-primary:hover {
                background: #2d6a4f;
                transform: translateY(-1px);
            }

            .agrisys-modal-btn-secondary {
                background: #f3f4f6;
                color: #374151;
            }

            .agrisys-modal-btn-secondary:hover {
                background: #e5e7eb;
            }

            .agrisys-modal-btn-danger {
                background: #dc3545;
                color: white;
            }

            .agrisys-modal-btn-danger:hover {
                background: #c82333;
            }

            /* Type-specific button styling */
            .agrisys-modal-success .agrisys-modal-btn-primary {
                background: #40916c;
            }

            .agrisys-modal-success .agrisys-modal-btn-primary:hover {
                background: #2d6a4f;
            }

            .agrisys-modal-error .agrisys-modal-btn-primary {
                background: #dc3545;
            }

            .agrisys-modal-error .agrisys-modal-btn-primary:hover {
                background: #c82333;
            }

            .agrisys-modal-warning .agrisys-modal-btn-primary {
                background: #f59e0b;
            }

            .agrisys-modal-warning .agrisys-modal-btn-primary:hover {
                background: #d97706;
            }

            .agrisys-modal-info .agrisys-modal-btn-primary {
                background: #3b82f6;
            }

            .agrisys-modal-info .agrisys-modal-btn-primary:hover {
                background: #2563eb;
            }

            /* Close button */
            .agrisys-modal-close {
                position: absolute;
                top: 16px;
                right: 16px;
                width: 32px;
                height: 32px;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.15);
                border: none;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 18px;
                color: white;
                transition: all 0.2s ease;
            }

            .agrisys-modal-close:hover {
                background: rgba(255, 255, 255, 0.25);
                color: white;
            }

            /* ========================================== */
            /* MOBILE CENTERING FIX */
            /* ========================================== */
            
            @media (max-width: 480px) {
                .agrisys-modal-overlay {
                    padding: 0 !important;
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    width: 100vw;
                    height: 100vh;
                }

                .agrisys-modal {
                    width: 90vw !important;
                    max-width: 400px !important;
                    margin: 0 auto !important;
                    max-height: 80vh;
                }

                .agrisys-modal-overlay.show .agrisys-modal {
                    transform: scale(1) translateY(0) !important;
                }

                .agrisys-modal-header {
                    padding: 20px 20px 14px;
                }

                .agrisys-modal-body {
                    padding: 16px 20px;
                    max-height: 40vh;
                }

                .agrisys-modal-footer {
                    padding: 14px 20px 20px;
                    flex-direction: column;
                    gap: 10px;
                }

                .agrisys-modal-btn {
                    width: 100%;
                    justify-content: center;
                }

                .agrisys-modal-icon {
                    width: 44px;
                    height: 44px;
                    font-size: 20px;
                }

                .agrisys-modal-title {
                    font-size: 16px;
                }

                .agrisys-modal-message {
                    font-size: 14px;
                }
            }

            /* For very small screens (landscape mobile) */
            @media (max-height: 600px) {
                .agrisys-modal-body {
                    max-height: 30vh !important;
                }

                .agrisys-modal {
                    max-height: 70vh !important;
                }
            }

            /* Tablet sizing */
            @media (min-width: 481px) and (max-width: 768px) {
                .agrisys-modal {
                    width: 85vw !important;
                    max-width: 500px !important;
                }

                .agrisys-modal-footer {
                    flex-direction: row;
                }

                .agrisys-modal-btn {
                    width: auto;
                }
            }

            /* Animation for shake effect on error */
            @keyframes agrisys-shake {
                0%, 100% { transform: translateX(0); }
                10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
                20%, 40%, 60%, 80% { transform: translateX(5px); }
            }

            .agrisys-modal-shake {
                animation: agrisys-shake 0.5s ease-in-out;
            }
        `;
        document.head.appendChild(styles);
    }

    createModalContainer() {
        const container = document.createElement('div');
        container.id = 'agrisys-modal-container';
        document.body.appendChild(container);
    }

    /**
     * Get icon based on modal type
     */
    getIcon(type) {
        const icons = {
            success: '✓',
            error: '✕',
            warning: '⚠',
            info: 'ℹ'
        };
        return icons[type] || icons.info;
    }

    /**
     * Get default title based on type
     */
    getDefaultTitle(type) {
        const titles = {
            success: 'Success!',
            error: 'Error',
            warning: 'Warning',
            info: 'Information'
        };
        return titles[type] || 'Notice';
    }

    /**
     * Show modal notification
     * @param {Object} options - Modal configuration
     * @param {string} options.type - Modal type: 'success', 'error', 'warning', 'info'
     * @param {string} options.message - Main message to display
     * @param {string} options.title - Optional title (uses default if not provided)
     * @param {string} options.subtitle - Optional subtitle
     * @param {string} options.reference - Optional reference number to display
     * @param {Array} options.errors - Optional array of error messages for validation errors
     * @param {Function} options.onClose - Optional callback when modal closes
     * @param {boolean} options.showCancel - Show cancel button for confirmations
     * @param {Function} options.onConfirm - Callback for confirm button in confirmation modals
     * @param {string} options.confirmText - Text for confirm button (default: 'OK')
     * @param {string} options.cancelText - Text for cancel button (default: 'Cancel')
     */
    show(options) {
        const {
            type = 'info',
            message = '',
            title = this.getDefaultTitle(type),
            subtitle = null,
            reference = null,
            errors = null,
            onClose = null,
            showCancel = false,
            onConfirm = null,
            confirmText = 'OK',
            cancelText = 'Cancel'
        } = options;

        // Create modal overlay
        const overlay = document.createElement('div');
        overlay.className = 'agrisys-modal-overlay';
        overlay.innerHTML = `
            <div class="agrisys-modal agrisys-modal-${type}" role="dialog" aria-modal="true">
                <button class="agrisys-modal-close" aria-label="Close">&times;</button>
                <div class="agrisys-modal-header">
                    <div class="agrisys-modal-icon">${this.getIcon(type)}</div>
                    <div class="agrisys-modal-title-container">
                        <h3 class="agrisys-modal-title">${title}</h3>
                        ${subtitle ? `<p class="agrisys-modal-subtitle">${subtitle}</p>` : ''}
                    </div>
                </div>
                <div class="agrisys-modal-body">
                    ${this.renderBody(message, reference, errors)}
                </div>
                <div class="agrisys-modal-footer">
                    ${showCancel ? `<button class="agrisys-modal-btn agrisys-modal-btn-secondary" data-action="cancel">${cancelText}</button>` : ''}
                    <button class="agrisys-modal-btn agrisys-modal-btn-primary" data-action="confirm">${confirmText}</button>
                </div>
            </div>
        `;

        // Add to container
        this.modalContainer.appendChild(overlay);

        // Trigger show animation
        requestAnimationFrame(() => {
            overlay.classList.add('show');
        });

        // Handle close
        const closeModal = (confirmed = false) => {
            overlay.classList.remove('show');
            setTimeout(() => {
                overlay.remove();
                if (confirmed && onConfirm) {
                    onConfirm();
                }
                if (onClose) {
                    onClose(confirmed);
                }
            }, 300);
        };

        // Close button event
        overlay.querySelector('.agrisys-modal-close').addEventListener('click', () => closeModal(false));

        // Confirm button event
        overlay.querySelector('[data-action="confirm"]').addEventListener('click', () => closeModal(true));

        // Cancel button event (if exists)
        const cancelBtn = overlay.querySelector('[data-action="cancel"]');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => closeModal(false));
        }

        // Close on overlay click
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                closeModal(false);
            }
        });

        // Close on Escape key
        const escapeHandler = (e) => {
            if (e.key === 'Escape') {
                closeModal(false);
                document.removeEventListener('keydown', escapeHandler);
            }
        };
        document.addEventListener('keydown', escapeHandler);

        // Focus the confirm button
        setTimeout(() => {
            overlay.querySelector('[data-action="confirm"]').focus();
        }, 100);

        return {
            close: () => closeModal(false),
            confirm: () => closeModal(true)
        };
    }

    /**
     * Render modal body content
     */
    renderBody(message, reference, errors) {
        let html = '';

        // Main message
        if (message) {
            // Clean up emoji prefixes that were used with alert()
            let cleanMessage = message
                .replace(/^✅\s*/g, '')
                .replace(/^❌\s*/g, '')
                .replace(/^⚠️\s*/g, '')
                .replace(/^ℹ️\s*/g, '');

            html += `<p class="agrisys-modal-message">${cleanMessage}</p>`;
        }

        // Reference number
        if (reference) {
            html += `
                <div class="agrisys-modal-reference">
                    <div class="agrisys-modal-reference-label">Reference Number</div>
                    <div class="agrisys-modal-reference-value">${reference}</div>
                </div>
            `;
        }

        // Error list
        if (errors && errors.length > 0) {
            html += `
                <ul class="agrisys-modal-error-list">
                    ${errors.map(err => `<li>${err}</li>`).join('')}
                </ul>
            `;
        }

        return html;
    }

    // Convenience methods
    success(message, options = {}) {
        return this.show({ type: 'success', message, ...options });
    }

    error(message, options = {}) {
        return this.show({ type: 'error', message, ...options });
    }

    warning(message, options = {}) {
        return this.show({ type: 'warning', message, ...options });
    }

    info(message, options = {}) {
        return this.show({ type: 'info', message, ...options });
    }

    /**
     * Show confirmation modal
     */
    confirm(message, options = {}) {
        return new Promise((resolve) => {
            this.show({
                type: options.type || 'warning',
                message,
                title: options.title || 'Confirm Action',
                showCancel: true,
                confirmText: options.confirmText || 'Confirm',
                cancelText: options.cancelText || 'Cancel',
                onClose: (confirmed) => resolve(confirmed),
                ...options
            });
        });
    }

    /**
     * Show validation errors modal
     */
    validationError(errors, options = {}) {
        const errorList = Array.isArray(errors) ? errors : Object.values(errors).flat();
        return this.show({
            type: 'error',
            title: options.title || 'Validation Error',
            message: options.message || 'Please correct the following errors:',
            errors: errorList,
            ...options
        });
    }
}

// Initialize global modal instance
const agrisysModal = new AgrisysModal();

// Make it globally available
window.agrisysModal = agrisysModal;

// ==============================================
// LEGACY ALERT REPLACEMENT WRAPPER
// Provides drop-in replacement for window.alert()
// ==============================================

/**
 * Show AgriSys styled modal - drop-in replacement for alert()
 * Automatically detects message type from emoji prefixes
 * @param {string} message - Message to display
 */
function showAgrisysAlert(message) {
    let type = 'info';
    let cleanMessage = message;

    // Detect type from emoji prefix
    if (message.startsWith('✅')) {
        type = 'success';
        cleanMessage = message.replace(/^✅\s*/, '');
    } else if (message.startsWith('❌')) {
        type = 'error';
        cleanMessage = message.replace(/^❌\s*/, '');
    } else if (message.startsWith('⚠') || message.startsWith('⚠️')) {
        type = 'warning';
        cleanMessage = message.replace(/^⚠️?\s*/, '');
    } else if (message.startsWith('ℹ') || message.startsWith('ℹ️')) {
        type = 'info';
        cleanMessage = message.replace(/^ℹ️?\s*/, '');
    }

    // Check for reference number pattern
    let reference = null;
    const refMatch = cleanMessage.match(/Reference:\s*([A-Z0-9-]+)/i);
    if (refMatch) {
        reference = refMatch[1];
        cleanMessage = cleanMessage.replace(/\n\nReference:\s*[A-Z0-9-]+/i, '');
    }

    // Check for validation errors pattern
    let errors = null;
    if (cleanMessage.includes('Validation errors:') || cleanMessage.includes('Please correct the following')) {
        const lines = cleanMessage.split('\n');
        errors = lines.filter(line => line.trim().startsWith('•') || line.trim().startsWith('-'))
                     .map(line => line.replace(/^[•\-]\s*/, '').trim());
        if (errors.length > 0) {
            cleanMessage = lines[0];
        }
    }

    return agrisysModal.show({
        type,
        message: cleanMessage,
        reference,
        errors
    });
}

// Export for use in modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { AgrisysModal, agrisysModal, showAgrisysAlert };
}

console.log('AgriSys Modal Notification System loaded (Mobile Optimized)');