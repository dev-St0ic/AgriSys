{{-- resources/views/admin/archive/retention-schedules.blade.php --}}
@extends('layouts.app')

@section('title', 'Retention Schedules - Archive')
@section('page-icon', 'fas fa-calendar-alt')
@section('page-title', 'Retention Schedules')

@section('content')

<div class="alert alert-info border-0 mb-4" style="border-left:5px solid #0d6efd !important;">
    <strong><i class="fas fa-info-circle me-2"></i>ISO 15489 Retention Schedules</strong>
    <p class="mb-0 small text-muted mt-1">
        These schedules define how long each record type must be retained before disposal is permitted.
        Changes are logged. Legal basis references Philippine government regulations.
    </p>
</div>

<div class="card shadow">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-calendar-alt me-2"></i>Retention Schedules by Record Type
            <span class="badge bg-primary ms-2">{{ $schedules->total() }}</span>
        </h6>
        <a href="{{ route('admin.archive.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Back to Archive
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Record Type</th>
                        <th class="text-center">Category</th>
                        <th class="text-center">Retention Period</th>
                        <th>Legal Basis</th>
                        <th>Description</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schedules as $s)
                    <tr>
                        <td><strong>{{ $s->record_label }}</strong><br><code class="small text-muted">{{ $s->record_type }}</code></td>
                        <td class="text-center">
                            <span class="badge
                                @if($s->retention_category === 'permanent') bg-danger
                                @elseif($s->retention_category === 'long_term') bg-primary
                                @elseif($s->retention_category === 'standard') bg-info text-dark
                                @else bg-secondary @endif">
                                {{ ucwords(str_replace('_',' ',$s->retention_category)) }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if($s->retention_category === 'permanent')
                                <span class="text-danger fw-bold">∞ Permanent</span>
                            @else
                                <strong>{{ $s->retention_years }}</strong> years
                            @endif
                        </td>
                        <td><small class="text-muted">{{ $s->legal_basis ?? '—' }}</small></td>
                        <td><small class="text-muted">{{ $s->description ?? '—' }}</small></td>
                        <td class="text-center">
                            <button class="btn btn-outline-primary btn-sm"
                                    onclick="openEditSchedule({{ $s->id }}, '{{ $s->record_label }}', '{{ $s->retention_category }}', {{ $s->retention_years }}, '{{ addslashes($s->legal_basis ?? '') }}', '{{ addslashes($s->description ?? '') }}')"
                                    title="Edit Retention Schedule">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="fas fa-calendar-alt fa-3x mb-3" style="opacity:0.2;"></i>
                            <p>No retention schedules found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($schedules->hasPages())
            <div class="d-flex justify-content-center mt-3">
                {{ $schedules->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>

<!-- Edit Retention Schedule Modal -->
<div class="modal fade" id="editScheduleModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title w-100 text-center">Edit Retention Schedule</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Editing schedule for: <strong id="editScheduleLabel"></strong></p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Retention Category</label>
                        <select id="editRetentionCategory" class="form-select" onchange="handleCategoryChange()">
                            <option value="permanent">Permanent (Never delete)</option>
                            <option value="long_term">Long Term (25-50 years)</option>
                            <option value="standard">Standard (7-10 years)</option>
                            <option value="short_term">Short Term (3-5 years)</option>
                        </select>
                    </div>
                    <div class="col-md-6" id="yearsField">
                        <label class="form-label fw-bold">Retention Years</label>
                        <input type="number" id="editRetentionYears" class="form-control"
                               min="1" max="100" placeholder="e.g. 10">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Legal Basis</label>
                        <input type="text" id="editLegalBasis" class="form-control"
                               placeholder="e.g. RA 8550, COA Circular 2009-006">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Description</label>
                        <textarea id="editDescription" class="form-control" rows="2"
                                  placeholder="Brief description of why this retention period applies..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveRetentionSchedule()" id="saveScheduleBtn">
                    <span class="btn-text"><i class="fas fa-save me-2"></i>Save Changes</span>
                    <span class="btn-loader d-none"><span class="spinner-border spinner-border-sm me-2"></span>Saving...</span>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Toast notification styles */
.toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.toast-notification {
    min-width: 300px;
    max-width: 500px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    padding: 16px;
    opacity: 0;
    transform: translateX(400px);
    transition: all 0.3s ease;
}

.toast-notification.show {
    opacity: 1;
    transform: translateX(0);
}

.toast-content {
    display: flex;
    align-items: center;
    gap: 8px;
}

.toast-success { border-left: 4px solid #28a745; }
.toast-error { border-left: 4px solid #dc3545; }
.toast-warning { border-left: 4px solid #ffc107; }
.toast-info { border-left: 4px solid #17a2b8; }

.btn-close-toast {
    padding: 0;
    background: transparent;
    border: none;
    font-size: 1.2rem;
    cursor: pointer;
    opacity: 0.5;
}

.btn-close-toast:hover {
    opacity: 1;
}
</style>

<script>
const CSRF = () => document.querySelector('meta[name="csrf-token"]')?.content || '';
let currentScheduleId = null;

// Toast notification function (same as recycle bin)
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

function openEditSchedule(id, label, category, years, legalBasis, description) {
    currentScheduleId = id;
    document.getElementById('editScheduleLabel').textContent = label;
    document.getElementById('editRetentionCategory').value = category;
    document.getElementById('editRetentionYears').value = years;
    document.getElementById('editLegalBasis').value = legalBasis;
    document.getElementById('editDescription').value = description;
    handleCategoryChange();
    new bootstrap.Modal(document.getElementById('editScheduleModal')).show();
}

function handleCategoryChange() {
    const isPermanent = document.getElementById('editRetentionCategory').value === 'permanent';
    document.getElementById('yearsField').style.display = isPermanent ? 'none' : 'block';
}

function saveRetentionSchedule() {
    const btn = document.getElementById('saveScheduleBtn');
    btn.querySelector('.btn-text').classList.add('d-none');
    btn.querySelector('.btn-loader').classList.remove('d-none');
    btn.disabled = true;

    fetch(`/admin/archive/retention-schedules/${currentScheduleId}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF() },
        body: JSON.stringify({
            retention_category: document.getElementById('editRetentionCategory').value,
            retention_years:    document.getElementById('editRetentionYears').value || 0,
            legal_basis:        document.getElementById('editLegalBasis').value,
            description:        document.getElementById('editDescription').value,
        })
    })
    .then(r => r.json())
    .then(data => {
        bootstrap.Modal.getInstance(document.getElementById('editScheduleModal')).hide();
        
        // Use toast instead of alert
        if (data.success) {
            showToast('success', data.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast('error', data.message || 'Failed to update retention schedule');
        }
    })
    .catch(err => {
        showToast('error', 'Error updating retention schedule');
    })
    .finally(() => {
        btn.querySelector('.btn-text').classList.remove('d-none');
        btn.querySelector('.btn-loader').classList.add('d-none');
        btn.disabled = false;
    });
}
</script>

@endsection