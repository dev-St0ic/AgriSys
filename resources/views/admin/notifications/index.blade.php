{{-- resources/views/admin/notifications/index.blade.php --}}

@extends('layouts.app')

@section('page-title', 'All Notifications')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-dark mb-0"><i class="fas fa-bell me-2 text-primary"></i>Notifications</h2>
            <small class="text-muted">View and manage your notifications</small>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="markAllAsRead()">
                <i class="fas fa-check-double me-2"></i>Mark All as Read
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearAllNotifications()">
                <i class="fas fa-trash me-2"></i>Clear All
            </button>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-3">
            <ul class="nav nav-pills" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $filterRead === 'all' ? 'active' : '' }}" 
                            onclick="filterNotifications('all')" type="button">
                        <i class="fas fa-inbox me-2"></i>All
                        <span class="badge bg-secondary ms-2" id="countAll">0</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $filterRead === 'unread' ? 'active' : '' }}" 
                            onclick="filterNotifications('unread')" type="button">
                        <i class="fas fa-circle me-2"></i>Unread
                        <span class="badge bg-primary ms-2" id="countUnread">0</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $filterRead === 'read' ? 'active' : '' }}" 
                            onclick="filterNotifications('read')" type="button">
                        <i class="fas fa-check-circle me-2"></i>Read
                        <span class="badge bg-success ms-2" id="countRead">0</span>
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div id="notificationsContainer" class="notification-list-container">
                <!-- Notifications will be loaded here -->
                <div class="text-center py-5">
                    <div class="spinner-border spinner-border-sm text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-muted">Loading notifications...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <nav aria-label="Pagination" class="d-flex justify-content-center mt-4" id="paginationContainer" style="display: none;">
        <ul class="pagination" id="paginationLinks"></ul>
    </nav>
</div>

<style>
.notification-list-container {
    max-height: calc(100vh - 300px);
    overflow-y: auto;
}

.notification-item-full {
    padding: 16px;
    border-bottom: 1px solid #f0f0f0;
    transition: all 0.2s ease;
    cursor: pointer;
}

.notification-item-full:hover {
    background-color: #f8f9fa;
}

.notification-item-full.unread {
    background-color: #e3f2fd;
    border-left: 4px solid #2196F3;
}

.notification-item-full.unread:hover {
    background-color: #cfe9fc;
}

.notification-icon-full {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
}

.notification-content-full {
    flex: 1;
}

.notification-title-full {
    font-size: 0.95rem;
    font-weight: 600;
    margin-bottom: 4px;
    color: #333;
}

.notification-message-full {
    font-size: 0.9rem;
    color: #666;
    margin-bottom: 8px;
    line-height: 1.4;
}

.notification-meta-full {
    display: flex;
    justify-content: space-between;
    font-size: 0.8rem;
    color: #999;
}

.notification-actions {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}

.notification-actions button {
    padding: 4px 8px;
    font-size: 0.75rem;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-state i {
    font-size: 3rem;
    color: #ddd;
    margin-bottom: 16px;
}

.empty-state p {
    color: #999;
    font-size: 1rem;
}
</style>

<script>
let currentFilter = '{{ $filterRead }}';
let currentPage = 1;

// Load notifications on page load
document.addEventListener('DOMContentLoaded', function() {
    loadAllNotifications();
});

// Load all notifications
function loadAllNotifications() {
    const url = `/admin/notifications?filter=${currentFilter}&page=${currentPage}`;
    
    fetch(url, {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        displayAllNotifications(data);
        updateCounts();
    })
    .catch(error => {
        console.error('Error loading notifications:', error);
        showEmptyState();
    });
}

// Display all notifications
function displayAllNotifications(data) {
    const container = document.getElementById('notificationsContainer');
    
    if (!data.data || data.data.length === 0) {
        showEmptyState();
        return;
    }

    container.innerHTML = data.data.map(notif => `
        <div class="notification-item-full ${notif.is_read ? '' : 'unread'} d-flex align-items-start gap-3">
            <div class="notification-icon-full bg-${notif.color} bg-opacity-10">
                <i class="fas ${notif.icon} text-${notif.color}"></i>
            </div>
            <div class="notification-content-full">
                <div class="notification-title-full">${notif.title}</div>
                <div class="notification-message-full">${notif.message}</div>
                <div class="notification-meta-full">
                    <span><i class="far fa-clock me-1"></i>${notif.time_ago}</span>
                    <span>${notif.created_at}</span>
                </div>
            </div>
            <div class="notification-actions">
                ${!notif.is_read ? `
                    <button type="button" class="btn btn-sm btn-primary" 
                            onclick="markAsRead(${notif.id})" title="Mark as read">
                        <i class="fas fa-check"></i>
                    </button>
                ` : ''}
                ${notif.action_url ? `
                    <button type="button" class="btn btn-sm btn-info" 
                            onclick="navigateTo('${notif.action_url}')" title="View details">
                        <i class="fas fa-arrow-right"></i>
                    </button>
                ` : ''}
                <button type="button" class="btn btn-sm btn-danger" 
                        onclick="deleteNotification(${notif.id})" title="Delete">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `).join('');

    // Update pagination if needed
    if (data.last_page > 1) {
        renderPagination(data);
    }
}

// Show empty state
function showEmptyState() {
    document.getElementById('notificationsContainer').innerHTML = `
        <div class="empty-state">
            <i class="fas fa-bell-slash"></i>
            <p>No notifications found</p>
        </div>
    `;
}

// Filter notifications
function filterNotifications(filter) {
    currentFilter = filter;
    currentPage = 1;
    loadAllNotifications();
}

// Mark single notification as read
function markAsRead(notificationId) {
    fetch(`/admin/notifications/${notificationId}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', 'Notification marked as read');
            loadAllNotifications();
        }
    })
    .catch(error => console.error('Error:', error));
}

// Mark all as read
function markAllAsRead() {
    if (!confirm('Mark all notifications as read?')) return;

    fetch('/admin/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', data.message);
            loadAllNotifications();
        }
    })
    .catch(error => console.error('Error:', error));
}

// Delete single notification
function deleteNotification(notificationId) {
    if (!confirm('Delete this notification?')) return;

    fetch(`/admin/notifications/${notificationId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', 'Notification deleted');
            loadAllNotifications();
        }
    })
    .catch(error => console.error('Error:', error));
}

// Clear all notifications
function clearAllNotifications() {
    if (!confirm('Delete all notifications? This cannot be undone.')) return;

    fetch('/admin/notifications/clear-all', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', data.message);
            loadAllNotifications();
        }
    })
    .catch(error => console.error('Error:', error));
}

// Navigate to action URL
function navigateTo(url) {
    window.location.href = url;
}

// Update counts
function updateCounts() {
    fetch('/admin/notifications?filter=all', {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('countAll').textContent = data.total || 0;
    });
}

// Render pagination
function renderPagination(data) {
    const container = document.getElementById('paginationContainer');
    const links = document.getElementById('paginationLinks');
    
    if (data.last_page <= 1) {
        container.style.display = 'none';
        return;
    }

    container.style.display = 'flex';
    links.innerHTML = '';

    // Previous button
    if (data.prev_page_url) {
        links.innerHTML += `
            <li class="page-item">
                <button class="page-link" onclick="goToPage(${data.current_page - 1})">Previous</button>
            </li>
        `;
    }

    // Page numbers
    for (let i = 1; i <= data.last_page; i++) {
        links.innerHTML += `
            <li class="page-item ${i === data.current_page ? 'active' : ''}">
                <button class="page-link" onclick="goToPage(${i})">${i}</button>
            </li>
        `;
    }

    // Next button
    if (data.next_page_url) {
        links.innerHTML += `
            <li class="page-item">
                <button class="page-link" onclick="goToPage(${data.current_page + 1})">Next</button>
            </li>
        `;
    }
}

// Go to page
function goToPage(page) {
    currentPage = page;
    loadAllNotifications();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>
@endsection