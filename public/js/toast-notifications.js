// ==============================================
// TOAST NOTIFICATION SYSTEM
// Modern, non-intrusive notifications
// ==============================================

class ToastNotification {
    constructor(options = {}) {
        this.container = null;
        this.toasts = [];
        this.defaultDuration = options.duration || 5000;
        this.maxToasts = options.maxToasts || 5;
        this.position = options.position || 'top-right'; // top-right, top-left, bottom-right, bottom-left
        
        this.init();
    }

    init() {
        // Create container if it doesn't exist
        if (!document.getElementById('toast-container')) {
            this.container = document.createElement('div');
            this.container.id = 'toast-container';
            this.container.className = 'toast-container';
            document.body.appendChild(this.container);
        } else {
            this.container = document.getElementById('toast-container');
        }
    }

    show(message, type = 'info', options = {}) {
        const duration = options.duration || this.defaultDuration;
        const title = options.title || this.getDefaultTitle(type);
        
        // Remove oldest toast if max limit reached
        if (this.toasts.length >= this.maxToasts) {
            this.remove(this.toasts[0]);
        }

        // Create toast element
        const toast = this.createToast(message, type, title, duration);
        
        // Add to container
        this.container.appendChild(toast);
        this.toasts.push(toast);

        // Trigger animation
        setTimeout(() => {
            toast.classList.add('show');
        }, 10);

        // Auto remove after duration
        if (duration > 0) {
            setTimeout(() => {
                this.remove(toast);
            }, duration);
        }

        return toast;
    }

    createToast(message, type, title, duration) {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        
        const icon = this.getIcon(type);
        
        toast.innerHTML = `
            <div class="toast-icon">${icon}</div>
            <div class="toast-content">
                <div class="toast-title">${title}</div>
                <div class="toast-message">${message}</div>
            </div>
            <button class="toast-close" aria-label="Close">&times;</button>
            ${duration > 0 ? '<div class="toast-progress"></div>' : ''}
        `;

        // Close button event
        const closeBtn = toast.querySelector('.toast-close');
        closeBtn.addEventListener('click', () => {
            this.remove(toast);
        });

        return toast;
    }

    remove(toast) {
        if (!toast || !toast.parentElement) return;

        toast.classList.remove('show');
        toast.classList.add('hide');

        setTimeout(() => {
            if (toast.parentElement) {
                toast.parentElement.removeChild(toast);
            }
            
            const index = this.toasts.indexOf(toast);
            if (index > -1) {
                this.toasts.splice(index, 1);
            }
        }, 400);
    }

    getIcon(type) {
        const icons = {
            success: '✓',
            error: '✕',
            warning: '⚠',
            info: 'ℹ'
        };
        return icons[type] || icons.info;
    }

    getDefaultTitle(type) {
        const titles = {
            success: 'Success',
            error: 'Error',
            warning: 'Warning',
            info: 'Information'
        };
        return titles[type] || 'Notification';
    }

    // Convenience methods
    success(message, options = {}) {
        return this.show(message, 'success', options);
    }

    error(message, options = {}) {
        return this.show(message, 'error', options);
    }

    warning(message, options = {}) {
        return this.show(message, 'warning', options);
    }

    info(message, options = {}) {
        return this.show(message, 'info', options);
    }

    clearAll() {
        this.toasts.forEach(toast => this.remove(toast));
    }
}

// Initialize global toast instance
const toast = new ToastNotification({
    duration: 5000,
    maxToasts: 5,
    position: 'top-right'
});

// Make it globally available
window.toast = toast;

// ==============================================
// AUTOMATIC CONVERSION OF LARAVEL FLASH MESSAGES
// ==============================================

document.addEventListener('DOMContentLoaded', function() {
    // Find Laravel alert messages
    const laravelAlerts = document.querySelectorAll('.alert');
    
    laravelAlerts.forEach(alert => {
        let type = 'info';
        let message = alert.textContent.trim();
        
        // Determine type from class
        if (alert.classList.contains('alert-success')) {
            type = 'success';
        } else if (alert.classList.contains('alert-danger') || alert.classList.contains('alert-error')) {
            type = 'error';
        } else if (alert.classList.contains('alert-warning')) {
            type = 'warning';
        }
        
        // Show as toast
        toast.show(message, type);
        
        // Hide the original alert
        alert.style.display = 'none';
    });
});

// ==============================================
// USAGE EXAMPLES (for reference)
// ==============================================

/*
// Basic usage
toast.success('Operation completed successfully!');
toast.error('Something went wrong!');
toast.warning('Please check your input!');
toast.info('New message received!');

// With custom options
toast.success('Profile updated!', {
    title: 'Great!',
    duration: 3000
});

// With custom title
toast.error('Failed to save changes', {
    title: 'Oops!',
    duration: 7000
});

// Without auto-dismiss (duration: 0)
toast.info('This will stay until closed', {
    duration: 0
});

// Clear all toasts
toast.clearAll();
*/

console.log('Toast notification system loaded');