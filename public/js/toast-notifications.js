// ==============================================
// TOAST NOTIFICATION SYSTEM - REDESIGNED
// Modern, modal-style, farmer-friendly
// ==============================================

class ToastNotification {
    constructor(options = {}) {
        this.container = null;
        this.toasts = [];
        this.defaultDuration = options.duration || 5500;
        this.maxToasts = options.maxToasts || 2;
        this.soundEnabled = options.soundEnabled || false;
        
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
        const duration = options.duration !== undefined ? options.duration : this.defaultDuration;
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

        // Play sound if enabled
        if (this.soundEnabled && type !== 'info') {
            this.playNotificationSound(type);
        }

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
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'polite');
        
        const icon = this.getIcon(type);
        
        toast.innerHTML = `
            <div class="toast-icon">${icon}</div>
            <div class="toast-content">
                <div class="toast-title">${this.escapeHtml(title)}</div>
                <div class="toast-message">${this.escapeHtml(message)}</div>
            </div>
            <button class="toast-close" type="button" aria-label="Close notification">&times;</button>
            ${duration > 0 ? '<div class="toast-progress"></div>' : ''}
        `;

        // Close button event
        const closeBtn = toast.querySelector('.toast-close');
        closeBtn.addEventListener('click', (e) => {
            e.preventDefault();
            this.remove(toast);
        });

        // Pause progress on hover
        if (duration > 0) {
            toast.addEventListener('mouseenter', () => {
                const progress = toast.querySelector('.toast-progress');
                if (progress) {
                    progress.style.animationPlayState = 'paused';
                }
            });

            toast.addEventListener('mouseleave', () => {
                const progress = toast.querySelector('.toast-progress');
                if (progress) {
                    progress.style.animationPlayState = 'running';
                }
            });
        }

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
        }, 600);
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
            success: 'Success!',
            error: 'Error',
            warning: 'Warning',
            info: 'Information'
        };
        return titles[type] || 'Notification';
    }

    // Escape HTML to prevent XSS
    escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    playNotificationSound(type) {
        try {
            // Create simple beep using Web Audio API
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();

            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);

            const now = audioContext.currentTime;
            
            if (type === 'success') {
                oscillator.frequency.setValueAtTime(800, now);
                oscillator.frequency.setValueAtTime(1000, now + 0.1);
            } else if (type === 'error') {
                oscillator.frequency.setValueAtTime(300, now);
                oscillator.frequency.setValueAtTime(200, now + 0.1);
            } else {
                oscillator.frequency.value = 600;
            }

            gainNode.gain.setValueAtTime(0.2, now);
            gainNode.gain.exponentialRampToValueAtTime(0.01, now + 0.1);

            oscillator.start(now);
            oscillator.stop(now + 0.1);
        } catch (e) {
            // Silently fail if audio not available
        }
    }

    // Convenience methods
    success(message, options = {}) {
        return this.show(message, 'success', {
            title: 'Success!',
            ...options
        });
    }

    error(message, options = {}) {
        return this.show(message, 'error', {
            title: 'Error',
            ...options
        });
    }

    warning(message, options = {}) {
        return this.show(message, 'warning', {
            title: 'Warning',
            ...options
        });
    }

    info(message, options = {}) {
        return this.show(message, 'info', {
            title: 'Information',
            ...options
        });
    }

    clearAll() {
        this.toasts.slice().forEach(toast => this.remove(toast));
    }

    enableSound() {
        this.soundEnabled = true;
    }

    disableSound() {
        this.soundEnabled = false;
    }
}

// Initialize global toast instance
const toast = new ToastNotification({
    duration: 5500,
    maxToasts: 2,
    soundEnabled: false
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
        
        // Show as toast if message exists
        if (message) {
            toast.show(message, type, { duration: 6000 });
        }
        
        // Hide the original alert
        alert.style.display = 'none';
    });
});

// ==============================================
// ACCESSIBILITY & KEYBOARD SHORTCUTS
// ==============================================

// Dismiss last toast on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && toast.toasts.length > 0) {
        const lastToast = toast.toasts[toast.toasts.length - 1];
        if (lastToast) {
            toast.remove(lastToast);
        }
    }
});

// ==============================================
// USAGE EXAMPLES (for reference)
// ==============================================

/*
// Basic usage
toast.success('Your application was submitted successfully!');
toast.error('Failed to save your request. Please try again.');
toast.warning('Please review your information before submitting.');
toast.info('Your application is being processed.');

// With custom title
toast.success('Profile updated!', {
    title: 'Great news!',
    duration: 4000
});

// Long message
toast.error('Failed to upload document. Please check file size and format.', {
    duration: 7000
});

// Without auto-dismiss
toast.info('Important: This message will stay until you close it', {
    duration: 0
});

// Clear all toasts
toast.clearAll();

// Enable notification sounds
toast.enableSound();
*/

console.log('🌾 Toast notification system loaded - Redesigned for farmers!');