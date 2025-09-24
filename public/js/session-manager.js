// Session Manager - Handle user session state on frontend
class SessionManager {
    constructor() {
        this.user = this.getCurrentUser();
        this.initializeUserInterface();
    }

    getCurrentUser() {
        // Check if user data is available in the page (from your Blade template)
        if (typeof window.userData !== 'undefined' && window.userData) {
            return window.userData;
        }
        return null;
    }

    isLoggedIn() {
        return this.user !== null;
    }

    initializeUserInterface() {
        // Set up any UI elements that depend on login status
        this.loadUserApplications();
        
        // Log current user state for debugging
        if (this.isLoggedIn()) {
            console.log('User is logged in:', this.user);
        } else {
            console.log('User is not logged in');
        }
    }

    loadUserApplications() {
        if (!this.isLoggedIn()) return;

        // This function is defined in your auth.js
        if (typeof loadUserApplications === 'function') {
            loadUserApplications();
        }
    }

    // Handle logout
    async logout() {
        try {
            const response = await fetch('/auth/logout', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            const data = await response.json();
            
            if (data.success) {
                // Clear user data
                this.user = null;
                window.userData = null;
                
                // Show success message
                if (typeof showNotification === 'function') {
                    showNotification('success', 'Successfully logged out!');
                }
                
                // Reload page to show updated UI
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                throw new Error(data.message || 'Logout failed');
            }
        } catch (error) {
            console.error('Logout error:', error);
            if (typeof showNotification === 'function') {
                showNotification('error', 'Logout failed. Refreshing page...');
            }
            // Fallback: reload page anyway
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }
    }

    // Handle successful login (called from auth.js)
    onLoginSuccess(userData) {
        this.user = userData;
        window.userData = userData;
        console.log('Login successful, user data updated');
    }
}

// Initialize session manager when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize session manager
    window.sessionManager = new SessionManager();
});

// Make logout available globally for your dropdown
window.logoutUser = function() {
    if (confirm('Are you sure you want to log out?')) {
        if (window.sessionManager) {
            window.sessionManager.logout();
        }
    }
};

console.log('Session Manager loaded');