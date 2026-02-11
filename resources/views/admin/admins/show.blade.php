@extends('layouts.app')

@section('title', 'View Admin - AgriSys')
@section('page-title', 'Admin Details')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary text-center">
                        <i class="fas fa-user me-2"></i>Admin User Details
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4 text-center">
                            <div class="avatar-lg mx-auto mb-3">
                                <div class="avatar-title rounded-circle bg-primary">
                                    {{ strtoupper(substr($admin->name, 0, 2)) }}
                                </div>
                            </div>
                            <span class="badge bg-{{ $admin->isSuperAdmin() ? 'danger' : 'primary' }} fs-6">
                                <i class="fas fa-{{ $admin->isSuperAdmin() ? 'crown' : 'user-shield' }} me-1"></i>
                                {{ $admin->isSuperAdmin() ? 'Super Admin' : 'Admin' }}
                            </span>
                        </div>
                        <div class="col-md-8">
                            <h4 class="mb-3">{{ $admin->name }}</h4>

                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <strong><i class="fas fa-envelope me-2"></i>Email:</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $admin->email }}
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <strong><i class="fas fa-user-shield me-2"></i>Role:</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $admin->isSuperAdmin() ? 'Super Admin' : 'Admin' }}
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <strong><i class="fas fa-calendar-alt me-2"></i>Created:</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $admin->created_at->format('F d, Y \a\t g:i A') }}
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <strong><i class="fas fa-clock me-2"></i>Last Updated:</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $admin->updated_at->format('F d, Y \a\t g:i A') }}
                                </div>
                            </div>

                            @if ($admin->id === auth()->id())
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    This is your account
                                </div>
                            @endif
                        </div>
                    </div>

                    <hr>

                    <!-- Permissions Section - CENTERED & FULL WIDTH -->
                    <div class="mb-4">
                        <h5 class="mb-4 text-center">
                            <i class="fas fa-key me-2"></i>Permissions
                        </h5>
                        <div class="row justify-content-center">
                            <div class="col-lg-8">
                                <ul class="list-group">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span><i class="fas fa-tachometer-alt me-2 text-primary"></i>Access Admin Dashboard</span>
                                        <i class="fas fa-check-circle text-success fs-5"></i>
                                    </li>
                                    @if ($admin->isSuperAdmin())
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="fas fa-users-cog me-2 text-primary"></i>Manage Admin Users</span>
                                            <i class="fas fa-check-circle text-success fs-5"></i>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="fas fa-user-plus me-2 text-primary"></i>Create New Admins</span>
                                            <i class="fas fa-check-circle text-success fs-5"></i>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="fas fa-user-times me-2 text-primary"></i>Delete Admin Users</span>
                                            <i class="fas fa-check-circle text-success fs-5"></i>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="fas fa-crown me-2 text-warning"></i>Full System Access</span>
                                            <i class="fas fa-check-circle text-success fs-5"></i>
                                        </li>
                                    @else
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="fas fa-users-cog me-2 text-muted"></i>Manage Admin Users</span>
                                            <i class="fas fa-times-circle text-danger fs-5"></i>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="fas fa-user-plus me-2 text-muted"></i>Create New Admins</span>
                                            <i class="fas fa-times-circle text-danger fs-5"></i>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><i class="fas fa-user-times me-2 text-muted"></i>Delete Admin Users</span>
                                            <i class="fas fa-times-circle text-danger fs-5"></i>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.admins.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to List
                        </a>
                        <div>
                            <a href="{{ route('admin.admins.edit', $admin) }}" class="btn btn-success me-2">
                                <i class="fas fa-edit me-2"></i>Edit Admin
                            </a>
                            @if ($admin->id !== auth()->id())
                                <button type="button" class="btn btn-danger" 
                                    onclick="showDeleteAdminModal({{ $admin->id }}, '{{ $admin->name }}')">
                                    <i class="fas fa-trash me-2"></i>Delete Admin
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Admin Modal -->
    <div class="modal fade" id="deleteAdminModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title w-100 text-center">Delete Admin User</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger" role="alert">
                        <strong><i class="fas fa-exclamation-triangle me-2"></i>Warning!</strong>
                        <p class="mb-0">Are you sure you want to delete this admin user? <strong id="delete_admin_name"></strong> will be moved to recycle bin.</p>
                    </div>
                    <ul class="mb-0">
                        <li>Remove the admin user from the system</li>
                        <li>All associated activity logs will be preserved</li>
                        <li>Item can be restored from recycle bin</li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="confirmDeleteAdmin()" id="confirm_delete_admin_btn">
                        <span class="btn-text">Delete Admin</span>
                        <span class="btn-loader" style="display: none;"><span class="spinner-border spinner-border-sm me-2"></span>Deleting...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .avatar-lg {
            height: 5rem;
            width: 5rem;
        }

        .avatar-title {
            align-items: center;
            background-color: #6c757d;
            color: #fff;
            display: flex;
            font-size: 1.5rem;
            font-weight: 500;
            height: 100%;
            justify-content: center;
            width: 100%;
        }

        /* Enhanced Permissions List */
        .list-group-item {
            border-left: 3px solid transparent;
            transition: all 0.3s ease;
            padding: 1rem 1.25rem;
        }

        .list-group-item:hover {
            background-color: #f8f9fa;
            border-left-color: #0d6efd;
            transform: translateX(5px);
        }

        .list-group-item i.fa-check-circle {
            font-size: 1.25rem;
        }

        .list-group-item i.fa-times-circle {
            font-size: 1.25rem;
        }

        /* Toast notification styles */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            pointer-events: none;
        }

        .toast-notification {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 6px 10px;
            min-width: 200px;
            max-width: 280px;
            overflow: hidden;
            opacity: 0;
            transform: translateY(-20px);
            transition: all 0.3s ease-in-out;
            pointer-events: auto;
        }

        .toast-notification.show {
            opacity: 1;
            transform: translateY(0);
        }

        .toast-content {
            display: flex;
            align-items: center;
            width: 100%;
            gap: 8px;
            font-size: 0.85rem;
        }

        .toast-content i {
            font-size: 0.9rem;
            min-width: 14px;
        }

        .toast-content span {
            flex: 1;
            color: #333;
            line-height: 1.3;
        }

        .toast-notification.toast-success {
            border-left: 4px solid var(--bs-success);
        }

        .toast-notification.toast-error {
            border-left: 4px solid var(--bs-danger);
        }

        .toast-notification.toast-warning {
            border-left: 4px solid var(--bs-warning);
        }

        .toast-notification.toast-info {
            border-left: 4px solid var(--bs-info);
        }

        .btn-close-toast {
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 0;
            margin: 0;
            opacity: 0.5;
            transition: opacity 0.2s;
            font-size: 0.9rem;
            line-height: 1;
        }

        .btn-close-toast:hover {
            opacity: 1;
        }
    </style>

    <script>
        let currentDeleteAdminId = null;

        /**
         * Show delete admin modal
         */
        function showDeleteAdminModal(adminId, adminName) {
            currentDeleteAdminId = adminId;
            document.getElementById('delete_admin_name').textContent = adminName;
            new bootstrap.Modal(document.getElementById('deleteAdminModal')).show();
        }

        /**
         * Confirm delete admin
         */
        async function confirmDeleteAdmin() {
            if (!currentDeleteAdminId) {
                showToast('error', 'Admin ID not found');
                return;
            }

            try {
                const deleteBtn = document.getElementById('confirm_delete_admin_btn');
                deleteBtn.querySelector('.btn-text').style.display = 'none';
                deleteBtn.querySelector('.btn-loader').style.display = 'inline';
                deleteBtn.disabled = true;

                const response = await fetch(`/admin/admins/${currentDeleteAdminId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Failed to delete admin');
                }

                showToast('success', data.message || 'Admin user moved to recycle bin successfully');
                
                const modal = bootstrap.Modal.getInstance(document.getElementById('deleteAdminModal'));
                if (modal) modal.hide();

                setTimeout(() => {
                    window.location.href = '/admin/admins';
                }, 1500);

            } catch (error) {
                console.error('Error:', error);
                showToast('error', 'Error deleting admin: ' + error.message);
                
                const deleteBtn = document.getElementById('confirm_delete_admin_btn');
                deleteBtn.querySelector('.btn-text').style.display = 'inline';
                deleteBtn.querySelector('.btn-loader').style.display = 'none';
                deleteBtn.disabled = false;
            }
        }

        /**
         * Toast notification function
         */
        function showToast(type, message) {
            const toastContainer = document.getElementById('toastContainer') || createToastContainer();
            const iconMap = {
                'success': { icon: 'fas fa-check-circle', color: 'success' },
                'error': { icon: 'fas fa-exclamation-circle', color: 'danger' },
                'warning': { icon: 'fas fa-exclamation-triangle', color: 'warning' },
                'info': { icon: 'fas fa-info-circle', color: 'info' }
            };

            const config = iconMap[type] || iconMap['info'];
            const toast = document.createElement('div');
            toast.className = `toast-notification toast-${type}`;
            toast.innerHTML = `
                <div class="toast-content">
                    <i class="${config.icon} me-2" style="color: var(--bs-${config.color});"></i>
                    <span>${message}</span>
                    <button type="button" class="btn-close btn-close-toast ms-auto" onclick="removeToast(this.closest('.toast-notification'))"></button>
                </div>
            `;

            toastContainer.appendChild(toast);
            setTimeout(() => toast.classList.add('show'), 10);
            setTimeout(() => removeToast(toast), 5000);
        }

        function createToastContainer() {
            const container = document.createElement('div');
            container.id = 'toastContainer';
            container.className = 'toast-container';
            document.body.appendChild(container);
            return container;
        }

        function removeToast(element) {
            element.classList.remove('show');
            setTimeout(() => element.remove(), 300);
        }

        // Clean up modal on close
        document.addEventListener('DOMContentLoaded', function() {
            const deleteAdminModal = document.getElementById('deleteAdminModal');
            if (deleteAdminModal) {
                deleteAdminModal.addEventListener('hidden.bs.modal', function() {
                    const deleteBtn = document.getElementById('confirm_delete_admin_btn');
                    deleteBtn.querySelector('.btn-text').style.display = 'inline';
                    deleteBtn.querySelector('.btn-loader').style.display = 'none';
                    deleteBtn.disabled = false;
                    currentDeleteAdminId = null;

                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    backdrops.forEach(backdrop => backdrop.remove());
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';
                });
            }
        });
    </script>
@endsection