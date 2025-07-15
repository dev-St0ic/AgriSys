/**
 * Inventory Management JavaScript
 * Handles all interactive functionality for the inventory index page
 */

class InventoryManager {
    constructor() {
        this.initializeEventListeners();
        this.initializeFilters();
        this.initializePagination();
        this.initializeModals();
        this.initializeAnimations();
        this.initializeKeyboardShortcuts();
    }

    /**
     * Initialize all event listeners
     */
    initializeEventListeners() {
        // Enhanced auto-hide alerts with animation
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);

        // Add loading states to action buttons
        const actionButtons = document.querySelectorAll('.btn-group .btn');
        actionButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                if (button.getAttribute('href') && !button.getAttribute('href').startsWith('#')) {
                    this.addLoadingState(button);
                }
            });
        });
    }

    /**
     * Initialize filter functionality
     */
    initializeFilters() {
        const filterForm = document.getElementById('filterForm');
        if (!filterForm) return;

        const selects = filterForm.querySelectorAll('select');
        const searchInput = filterForm.querySelector('input[name="search"]');
        let searchTimeout;

        // Auto-submit on select changes
        selects.forEach(select => {
            select.addEventListener('change', () => {
                setTimeout(() => filterForm.submit(), 100);
            });
        });

        // Real-time search with debounce
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    if (e.target.value.length >= 2 || e.target.value.length === 0) {
                        filterForm.submit();
                    }
                }, 500);
            });
        }
    }

    /**
     * Initialize pagination functionality
     */
    initializePagination() {
        const paginationLinks = document.querySelectorAll('.pagination .page-link');
        
        paginationLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                if (link.getAttribute('href') && !link.closest('.page-item').classList.contains('disabled')) {
                    this.addLoadingState(link, '<i class="fas fa-spinner fa-spin"></i>');
                }
            });
        });
    }

    /**
     * Initialize modal functionality
     */
    initializeModals() {
        const stockModals = document.querySelectorAll('[id^="adjustStockModal"]');
        
        stockModals.forEach(modal => {
            const adjustmentType = modal.querySelector('select[name="adjustment_type"]');
            const quantityInput = modal.querySelector('input[name="quantity"]');
            const alertElement = modal.querySelector('.alert');
            
            if (adjustmentType && quantityInput && alertElement) {
                adjustmentType.addEventListener('change', () => {
                    const currentStockMatch = alertElement.textContent.match(/\d+/);
                    const currentStock = currentStockMatch ? parseInt(currentStockMatch[0]) : 0;
                    
                    this.updateQuantityInput(adjustmentType.value, quantityInput, currentStock);
                });
            }
        });
    }

    /**
     * Update quantity input based on adjustment type
     */
    updateQuantityInput(adjustmentType, quantityInput, currentStock) {
        switch (adjustmentType) {
            case 'set':
                quantityInput.placeholder = 'Enter new stock level';
                quantityInput.removeAttribute('max');
                break;
            case 'subtract':
                quantityInput.placeholder = 'Enter amount to subtract';
                quantityInput.setAttribute('max', currentStock);
                break;
            case 'add':
            default:
                quantityInput.placeholder = 'Enter amount to add';
                quantityInput.removeAttribute('max');
                break;
        }
    }

    /**
     * Initialize page animations
     */
    initializeAnimations() {
        window.addEventListener('load', () => {
            this.animateStatsCards();
        });
    }

    /**
     * Animate statistics cards on page load
     */
    animateStatsCards() {
        const statsCards = document.querySelectorAll('.stats-card');
        
        statsCards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.5s ease';
            
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
    }

    /**
     * Initialize keyboard shortcuts
     */
    initializeKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            // Ctrl/Cmd + K to focus search
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                const searchInput = document.querySelector('input[name="search"]');
                if (searchInput) {
                    searchInput.focus();
                    searchInput.select();
                }
            }
            
            // Ctrl/Cmd + N to add new item
            if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
                e.preventDefault();
                const createButton = document.querySelector('a[href*="create"]');
                if (createButton) {
                    window.location.href = createButton.href;
                }
            }
        });
    }

    /**
     * Add loading state to button
     */
    addLoadingState(button, loadingContent = '<i class="fas fa-spinner fa-spin"></i>') {
        const originalText = button.innerHTML;
        button.innerHTML = loadingContent;
        button.style.pointerEvents = 'none';
        button.disabled = true;
        
        // Restore after timeout or page navigation
        setTimeout(() => {
            button.innerHTML = originalText;
            button.style.pointerEvents = 'auto';
            button.disabled = false;
        }, 3000);
    }

    /**
     * Export inventory functionality
     */
    exportInventory() {
        const searchParams = new URLSearchParams(window.location.search);
        const exportUrl = `${window.location.pathname}?${searchParams.toString()}&export=csv`;
        
        // Create and trigger download
        const link = document.createElement('a');
        link.href = exportUrl;
        link.download = 'inventory_export.csv';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        // Show notification
        this.showNotification(
            'Export Started', 
            'Your inventory data is being prepared for download.', 
            'success'
        );
    }

    /**
     * Show toast notification
     */
    showNotification(title, message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <strong>${title}</strong><br>${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        
        // Get or create toast container
        let toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
            document.body.appendChild(toastContainer);
        }
        
        toastContainer.appendChild(toast);
        
        // Show and auto-hide
        if (typeof bootstrap !== 'undefined') {
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
        }
        
        setTimeout(() => {
            if (toast.parentNode) {
                toast.remove();
            }
        }, 5000);
    }

    /**
     * Refresh page data without full reload
     */
    refreshData() {
        const loadingOverlay = this.createLoadingOverlay();
        document.body.appendChild(loadingOverlay);
        
        setTimeout(() => {
            window.location.reload();
        }, 500);
    }

    /**
     * Create loading overlay
     */
    createLoadingOverlay() {
        const overlay = document.createElement('div');
        overlay.className = 'loading-overlay';
        overlay.innerHTML = '<div class="loading-spinner"></div>';
        return overlay;
    }
}

// Utility functions for global access
window.InventoryManager = InventoryManager;

// Export function for global access
window.exportInventory = function() {
    if (window.inventoryManager) {
        window.inventoryManager.exportInventory();
    }
};

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.inventoryManager = new InventoryManager();
});

// Handle page visibility changes for better performance
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        // Page is hidden, pause any animations or timers
        clearTimeout(window.inventoryAnimationTimeout);
    } else {
        // Page is visible, resume functionality
        if (window.inventoryManager) {
            window.inventoryManager.initializeAnimations();
        }
    }
});