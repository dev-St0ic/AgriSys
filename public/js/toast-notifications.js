// ==============================================
// TOAST NOTIFICATION SYSTEM
// Centered, farmer-friendly notifications
// ==============================================

class ToastNotification {
    constructor(options = {}) {
        this.container = null;
        this.toasts = [];
        this.defaultDuration = options.duration || 5000;
        this.maxToasts = options.maxToasts || 3;
        this.position = options.position || 'top-center';
        this.soundEnabled = options.soundEnabled !== undefined ? options.soundEnabled : true; // Enabled by default
        
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
        
        const icon = this.getIcon(type);
        
        toast.innerHTML = `
            <div class="toast-icon">${icon}</div>
            <div class="toast-content">
                <div class="toast-title">${this.escapeHtml(title)}</div>
                <div class="toast-message">${this.escapeHtml(message)}</div>
            </div>
            <button class="toast-close" aria-label="Close notification">&times;</button>
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
        }, 500);
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

    /**
     * Play notification sound based on type
     * Uses Web Audio API for rich tone notifications
     */
    playNotificationSound(type) {
        try {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            let frequencies, durations;

            switch(type) {
                case 'success':
                    // Two ascending tones for success
                    frequencies = [523.25, 659.25]; // C5, E5
                    durations = [150, 150];
                    break;
                case 'error':
                    // Two descending tones for error
                    frequencies = [392, 261.63]; // G4, C4
                    durations = [200, 200];
                    break;
                case 'warning':
                    // Single warning tone
                    frequencies = [554.37]; // C#5
                    durations = [180];
                    break;
                default:
                    return;
            }

            frequencies.forEach((freq, index) => {
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();

                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);

                oscillator.frequency.value = freq;
                oscillator.type = 'sine';

                const startTime = audioContext.currentTime + (index > 0 ? durations[index - 1] / 1000 : 0);

                gainNode.gain.setValueAtTime(0.3, startTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, startTime + durations[index] / 1000);

                oscillator.start(startTime);
                oscillator.stop(startTime + durations[index] / 1000);
            });
        } catch (e) {
            // Silently fail if audio not available
            console.debug('Audio notification unavailable:', e.message);
        }
    }

    /**
     * Enable notification sounds
     */
    enableSound() {
        this.soundEnabled = true;
    }

    /**
     * Disable notification sounds
     */
    disableSound() {
        this.soundEnabled = false;
    }

    // Convenience methods
    success(message, options = {}) {
        return this.show(message, 'success', {
            title: 'Success',
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
}

// Initialize global toast instance
const toast = new ToastNotification({
    duration: 5000,
    maxToasts: 3,
    position: 'top-center'
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
        if (message) {
            toast.show(message, type);
        }
        
        // Hide the original alert
        alert.style.display = 'none';
    });
});

// ==============================================
// ACCESSIBILITY ENHANCEMENTS
// ==============================================

// Dismiss toast on Escape key
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
toast.success('Application submitted successfully!');
toast.error('Failed to save your request. Please try again.');
toast.warning('Please review your information before submitting.');
toast.info('Your application is being processed.');

// With custom options
toast.success('Profile updated!', {
    title: 'Great!',
    duration: 3000
});

// With longer duration
toast.error('Failed to save changes', {
    title: 'Oops!',
    duration: 7000
});

// Without auto-dismiss (duration: 0)
toast.info('This will stay until you close it', {
    duration: 0
});

// Clear all toasts
toast.clearAll();
*/

console.log('🌾 Toast notification system loaded - Sound enabled by default, appears above all modals!');