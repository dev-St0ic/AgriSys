// ==============================================
// SESSION MANAGER
// It handles automatic logout, session expiration, and graceful recovery
// ==============================================

    const sessionManager = {
        // Configuration
        config: {
            // Session timeout in milliseconds (30 minutes = 1800000ms)
            SESSION_TIMEOUT: 30 * 60 * 1000,
            // Check interval in milliseconds (every 5 minutes)
            CHECK_INTERVAL: 5 * 60 * 1000,
            // Warning time before session expires (5 minutes)
            WARNING_TIME: 5 * 60 * 1000,
        },

        // State tracking
        state: {
            isSessionActive: true,
            lastActivityTime: Date.now(),
            warningShown: false,
            checkIntervalId: null,
            inactivityTimeoutId: null,
            isExpired: false,
        },

        /**
         * Initialize session manager
         * Call this when page loads and user is logged in
         */
        init() {
            if (!window.userData) {
                return;
            }
            this.startActivityTracking();
            this.startSessionCheck();
        },

        /**
         * Track user activity and reset inactivity timer
         */
        startActivityTracking() {
            const activityEvents = ['mousedown', 'keydown', 'scroll', 'touchstart', 'click'];

            activityEvents.forEach(event => {
                document.addEventListener(event, () => {
                    // Don't reset if session already expired
                    if (this.state.isExpired) {
                        return;
                    }

                    const timeSinceLastActivity = Date.now() - this.state.lastActivityTime;
                    this.state.lastActivityTime = Date.now();
                    this.state.warningShown = false;

                    // Clear existing timeout
                    if (this.state.inactivityTimeoutId) {
                        clearTimeout(this.state.inactivityTimeoutId);
                    }

                    // Set new timeout for inactivity
                    this.state.inactivityTimeoutId = setTimeout(() => {
                        this.handleSessionExpired('inactivity');
                    }, this.config.SESSION_TIMEOUT);
                }, true);
            });
        },

        /**
         * Start periodic session validity check
         */
        startSessionCheck() {
            this.state.checkIntervalId = setInterval(() => {
                this.checkSessionValidity();
            }, this.config.CHECK_INTERVAL);
        },

        /**
         * Check if session is still valid with backend
         */
        async checkSessionValidity() {
            // Skip check if already expired
            if (this.state.isExpired) {
                return;
            }

            try {
                const response = await fetch('/api/user/session-check', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                    credentials: 'same-origin'
                });

                // Session expired on server
                if (response.status === 401 || response.status === 403) {
                    this.handleSessionExpired('server');
                    return;
                }

                if (!response.ok) {
                    console.warn('Session check failed:', response.status);
                    return;
                }

                const data = await response.json();
                if (!data.success || !data.authenticated) {
                    this.handleSessionExpired('server');
                    return;
                }

                this.state.isSessionActive = true;
            } catch (error) {
            }
        },

        /**
         * Handle session expiration gracefully
         * Why session expired (inactivity, server, etc)
         */
        handleSessionExpired(reason = 'unknown') {
            // Prevent multiple expiration handlers
            if (this.state.isExpired) {
                return;
            }
            this.state.isExpired = true;

            // Stop all tracking IMMEDIATELY
            this.stop();

            // Update state
            this.state.isSessionActive = false;
            window.userData = null;

            // Stop verification polling if active
            if (typeof stopVerificationPolling === 'function') {
                stopVerificationPolling();
            }

            // Close all modals
            this.closeAllModals();

            // Clear any pending logout requests
            this.abortPendingRequests();

            // Show session expired notification
            this.showSessionExpiredNotification(reason);

            // CALL SERVER TO DESTROY SESSION FIRST
            this.destroySessionOnServer().then(() => {
                setTimeout(() => {
                    this.reloadToLoginPage();
                }, 1500);
            }).catch(error => {
                console.error('Error destroying server session:', error);
                setTimeout(() => {
                    this.reloadToLoginPage();
                }, 1500);
            });
        },

        /**
         * Explicitly destroy the session on the server
         */
        async destroySessionOnServer() {
            try {
                const response = await fetch('/auth/logout', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    credentials: 'same-origin'
                });

                if (response.ok) {
                    const data = await response.json();
                    return true;
                } else {
                    return false;
                }
            } catch (error) {
                return false;
            }
        },

        /**
         * Abort any pending fetch requests to prevent interference
         */
        abortPendingRequests() {
            // Cancel any pending logout requests
            if (window.logoutAbortController) {
                window.logoutAbortController.abort();
                window.logoutAbortController = null;
            }
        },

        /**
         * Show session expired notification
         * Why session expired
         */
        showSessionExpiredNotification(reason = 'unknown') {
            // Design modal 
            if (!document.querySelector('#session-expired-styles')) {
                const styles = document.createElement('style');
                styles.id = 'session-expired-styles';
                styles.textContent = `
                    .session-expired-notification {
                        position: fixed;
                        top: 0;
                        left: 0;
                        right: 0;
                        bottom: 0;
                        background: rgba(0, 0, 0, 0.5);
                        backdrop-filter: blur(4px);
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        z-index: 99999;
                        animation: fadeIn 0.3s ease-out;
                        pointer-events: auto;
                    }

                    @keyframes fadeIn {
                        from {
                            opacity: 0;
                        }
                        to {
                            opacity: 1;
                        }
                    }

                    .session-expired-content {
                        background: white;
                        border-radius: 16px;
                        padding: 32px;
                        text-align: center;
                        max-width: 400px;
                        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                        animation: slideIn 0.4s cubic-bezier(0.16, 1, 0.3, 1);
                    }

                    @keyframes slideIn {
                        from {
                            transform: translateY(-20px);
                            opacity: 0;
                        }
                        to {
                            transform: translateY(0);
                            opacity: 1;
                        }
                    }

                    .session-expired-icon {
                        width: 64px;
                        height: 64px;
                        margin: 0 auto 20px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        background: #fee2e2;
                        border-radius: 50%;
                    }

                    .session-expired-message h3 {
                        margin: 0 0 8px;
                        font-size: 20px;
                        font-weight: 600;
                        color: #1f2937;
                    }

                    .session-expired-message p {
                        margin: 0 0 20px;
                        color: #6b7280;
                        font-size: 14px;
                        line-height: 1.5;
                    }

                    .session-expired-spinner {
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        gap: 12px;
                        color: #9ca3af;
                        font-size: 14px;
                    }

                    .spinner {
                        width: 16px;
                        height: 16px;
                        border: 2px solid #e5e7eb;
                        border-top-color: #3b82f6;
                        border-radius: 50%;
                        animation: rotate 0.8s linear infinite;
                    }

                    @keyframes rotate {
                        to {
                            transform: rotate(360deg);
                        }
                    }
                `;
                document.head.appendChild(styles);
            }

            // Create notification container
            const notification = document.createElement('div');
            notification.id = 'session-expired-notification';
            notification.className = 'session-expired-notification';

            let message = 'Your session has expired due to inactivity. Please log in again to continue.';
            if (reason === 'server') {
                message = 'Your session has ended. Please log in again to continue.';
            }

            notification.innerHTML = `
                <div class="session-expired-content">
                    <div class="session-expired-icon">
                        <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="24" cy="24" r="22" stroke="#ef4444" stroke-width="2"/>
                            <path d="M24 14v10" stroke="#ef4444" stroke-width="2" stroke-linecap="round"/>
                            <circle cx="24" cy="28" r="1.5" fill="#ef4444"/>
                        </svg>
                    </div>
                    <div class="session-expired-message">
                        <h3>Session Expired</h3>
                        <p>${message}</p>
                    </div>
                    <div class="session-expired-spinner">
                        <div class="spinner"></div>
                        <span>Reloading...</span>
                    </div>
                </div>
            `;

            // Append directly to body to bypass any stacking context issues
            document.body.appendChild(notification);

            // Prevent interactions with page
            document.body.style.overflow = 'hidden';
            document.body.style.pointerEvents = 'none';
        },

        /**
         * Close all open modals
         */
        closeAllModals() {
            const modals = [
                'auth-modal',
                'profile-modal',
                'applications-modal',
                'verification-modal',
                'edit-profile-modal',
                'change-password-modal',
                'forgot-password-modal',
                'contact-modal',
                'terms-modal',
                'privacy-modal',
                'logout-confirmation-overlay'
            ];

            modals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.style.display = 'none';
                }
            });

            // Also remove any dynamically created modals
            const alertModals = document.querySelectorAll('[id*="-alert-modal"], [id*="-confirmation-"]');
            alertModals.forEach(modal => modal.remove());
        },

        /**
         * Reload page to login state
         */
        reloadToLoginPage() {

            // Clear user data before reload
            window.userData = null;
            sessionStorage.clear();

            // Reload with cache bust and session_expired flag
            const loginUrl = new URL(window.location.origin);
            loginUrl.searchParams.set('session_expired', 'true');
            loginUrl.searchParams.set('t', Date.now());

            window.location.href = loginUrl.toString();
        },

        /**
         * Stop session manager
         */
        stop() {
            if (this.state.checkIntervalId) {
                clearInterval(this.state.checkIntervalId);
                this.state.checkIntervalId = null;
            }

            if (this.state.inactivityTimeoutId) {
                clearTimeout(this.state.inactivityTimeoutId);
                this.state.inactivityTimeoutId = null;
            }
        },

        /**
         * Get current session state
         */
            getState() {
                return {
                    isActive: this.state.isSessionActive,
                    isExpired: this.state.isExpired,
                    lastActivityTime: this.state.lastActivityTime,
                    timeSinceLastActivity: Date.now() - this.state.lastActivityTime,
                    sessionTimeoutMs: this.config.SESSION_TIMEOUT,
                };
            }
    };

    /**
     *  Handles session cleanup gracefully
     */
    function logoutUserSafely() {
        // Mark as expired to prevent re-entry
        sessionManager.state.isExpired = true;
        sessionManager.stop();

        // Abort controller for logout request
        window.logoutAbortController = new AbortController();

        // Update UI state
        window.userData = null;

        // Show logout confirmation if function exists
        if (typeof showLogoutConfirmation === 'function') {
            showLogoutConfirmation();
        }
    }

    /**
     * Logout submission with proper error handling
     */
    window.confirmLogoutEnhanced = async function() {
        const confirmBtn = document.querySelector('.btn-confirm-logout');
        if (!confirmBtn) return;

        // Mark session as expired to prevent re-entry
        sessionManager.state.isExpired = true;
        sessionManager.stop();

        // Set button to loading state
        confirmBtn.disabled = true;
        const btnText = confirmBtn.querySelector('.btn-text');
        const btnLoader = confirmBtn.querySelector('.btn-loader');

        if (btnText) btnText.style.display = 'none';
        if (btnLoader) btnLoader.style.display = 'inline';

        // Stop verification polling if active
        if (typeof stopVerificationPolling === 'function') {
            stopVerificationPolling();
        }

        try {
            // Attempt logout with timeout
            const logoutPromise = fetch('/auth/logout', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                credentials: 'same-origin',
                signal: window.logoutAbortController?.signal
            });

            // Set 5-second timeout for logout request
            const timeoutPromise = new Promise((_, reject) =>
                setTimeout(() => reject(new Error('Logout request timeout')), 5000)
            );

            const response = await Promise.race([logoutPromise, timeoutPromise]);

            // Handle both success and already-expired cases
            if (response.status === 401 || response.status === 403) {
                console.log('ℹSession already expired on server');
            }

            // Show success message
            if (btnText) {
                btnText.textContent = 'Logging Out...';
                btnText.style.display = 'inline';
            }
            if (btnLoader) btnLoader.style.display = 'none';

            // Show notification
            if (typeof showNotification === 'function') {
                showNotification('success', 'You have been logged out successfully');
            }

            // Clear user data
            window.userData = null;

            // Close modal and redirect
            setTimeout(() => {
                closeLogoutConfirmation();
                setTimeout(() => {
                    // Reload to login page
                    const loginUrl = new URL(window.location.origin);
                    loginUrl.searchParams.set('logged_out', 'true');
                    loginUrl.searchParams.set('t', Date.now());
                    window.location.href = loginUrl.toString();
                }, 300);
            }, 800);

        } catch (error) {
            if (error.name === 'AbortError') {
                console.log('Logout request was aborted');
            } else if (error.message === 'Logout request timeout') {
                console.log('Logout request timed out - proceeding with client-side logout');
            } else {
                console.error('Logout error:', error);
            }

            // Even on error, treat as logout for user experience
            if (btnText) {
                btnText.textContent = 'Logged Out!';
                btnText.style.display = 'inline';
            }
            if (btnLoader) btnLoader.style.display = 'none';

            // Show notification
            if (typeof showNotification === 'function') {
                showNotification('success', 'You have been logged out.');
            }

            // Clear user data
            window.userData = null;

            // Close modal and redirect anyway
            setTimeout(() => {
                closeLogoutConfirmation();
                setTimeout(() => {
                    // Reload to login page
                    const loginUrl = new URL(window.location.origin);
                    loginUrl.searchParams.set('logged_out', 'true');
                    loginUrl.searchParams.set('t', Date.now());
                    window.location.href = loginUrl.toString();
                }, 300);
            }, 800);
        }
    };

    /**
     * Handle global 401/403 responses
     */
    const originalFetch = window.fetch;
    window.fetch = function(...args) {
        return originalFetch.apply(this, args).then(response => {
            // Check for 401 Unauthorized or 403 Forbidden
            if ((response.status === 401 || response.status === 403) && window.userData) {

                // Only handle if it's not a logout request (logout requests are expected to fail if already expired)
                if (!args[0].includes('/auth/logout')) {
                    sessionManager.handleSessionExpired('server');
                }
            }
            return response;
        }).catch(error => {
            // Network errors don't indicate session expiration
            console.error('Fetch error:', error);
            throw error;
        });
    };

    /**
     * Initialize session manager when DOM is ready
     */
    document.addEventListener('DOMContentLoaded', function() {
        // Check if there's a session_expired parameter
        const urlParams = new URLSearchParams(window.location.search);
        const sessionExpired = urlParams.get('session_expired');
        const loggedOut = urlParams.get('logged_out');

        if (sessionExpired === 'true' || loggedOut === 'true') {
            // Clear URL parameter
            window.history.replaceState({}, document.title, window.location.pathname);

            // Show session expired/logged out message if applicable
            if (sessionExpired === 'true' && !window.userData) {
                if (typeof showNotification === 'function') {
                    showNotification('warning', 'Your session has expired. Please log in again.');
                }
            }
        }

        // Initialize session manager if user is logged in
        if (window.userData) {
            sessionManager.init();
        }
    });

    /**
     * Clean up session manager when leaving page
     */
    window.addEventListener('unload', function() {
        sessionManager.stop();
    });


