{{-- resources/views/admin/archive/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Archive - AgriSys Admin')
@section('page-icon', 'fas fa-file-archive')
@section('page-title', 'Archive')

@section('content')

{{-- ISO 15489 Notice Banner --}}
<div class="alert alert-warning border-0 mb-4" role="alert"
     style="background: linear-gradient(135deg,#fff8e1,#fff3cd); border-left: 5px solid #ffc107 !important;">
    <div class="d-flex align-items-center gap-3">
        <i class="fas fa-shield-alt fa-2x text-warning"></i>
        <div>
            <strong>ISO 15489 Records Management Archive</strong>
            <p class="mb-0 small text-muted">
                All records in this archive are <strong>immutable and locked</strong>.
                Disposal requires Super Admin approval and follows the configured retention schedule.
                All actions are permanently logged for audit compliance.
            </p>
        </div>
    </div>
</div>

{{-- Stats Row --}}
<div class="row g-3 mb-4">
    <div class="col-md-2 col-sm-4 col-6">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-2 fw-bold text-success">{{ number_format($stats['total_retained']) }}</div>
            <div class="small text-muted">Retained</div>
        </div>
    </div>
    <div class="col-md col-sm-4 col-6">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-2 fw-bold text-danger">{{ number_format($stats['permanent_records']) }}</div>
            <div class="small text-muted">Permanent</div>
        </div>
    </div>
    <div class="col-md col-sm-4 col-6">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-2 fw-bold text-warning">{{ number_format($stats['eligible_for_disposal']) }}</div>
            <div class="small text-muted">Eligible for Disposal</div>
        </div>
    </div>
    <div class="col-md col-sm-4 col-6">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-2 fw-bold text-orange" style="color:#fd7e14">{{ number_format($stats['approved_for_disposal']) }}</div>
            <div class="small text-muted">Disposal Approved</div>
        </div>
    </div>
    <div class="col-md col-sm-4 col-6">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-2 fw-bold text-secondary">{{ number_format($stats['total_disposed']) }}</div>
            <div class="small text-muted">Disposed</div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter me-2"></i>Filters & Search</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.archive.index') }}" id="filterForm">
            <div class="row g-2">
                <div class="col-md-2">
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="retained"             {{ request('status') == 'retained' ? 'selected' : '' }}>Retained</option>
                        <option value="approved_for_disposal"{{ request('status') == 'approved_for_disposal' ? 'selected' : '' }}>Approved for Disposal</option>
                        <option value="disposed"             {{ request('status') == 'disposed' ? 'selected' : '' }}>Disposed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="type" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Types</option>
                        <option value="fishr"             {{ request('type') == 'fishr' ? 'selected' : '' }}>FishR</option>
                        <option value="fishr_annex"       {{ request('type') == 'fishr_annex' ? 'selected' : '' }}>FishR Annex</option>
                        <option value="boatr"             {{ request('type') == 'boatr' ? 'selected' : '' }}>BoatR</option>
                        <option value="boatr_annex"       {{ request('type') == 'boatr_annex' ? 'selected' : '' }}>BoatR Annex</option>
                        <option value="rsbsa"             {{ request('type') == 'rsbsa' ? 'selected' : '' }}>RSBSA</option>
                        <option value="training"          {{ request('type') == 'training' ? 'selected' : '' }}>Training</option>
                        <option value="seedlings"         {{ request('type') == 'seedlings' ? 'selected' : '' }}>Supplies</option>
                        <option value="user_registration" {{ request('type') == 'user_registration' ? 'selected' : '' }}>User Registrations</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="retention" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Retention</option>
                        <option value="permanent"   {{ request('retention') == 'permanent' ? 'selected' : '' }}>Permanent</option>
                        <option value="long_term"   {{ request('retention') == 'long_term' ? 'selected' : '' }}>Long Term</option>
                        <option value="standard"    {{ request('retention') == 'standard' ? 'selected' : '' }}>Standard</option>
                        <option value="short_term"  {{ request('retention') == 'short_term' ? 'selected' : '' }}>Short Term</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="expired" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All</option>
                        <option value="1" {{ request('expired') == '1' ? 'selected' : '' }}>Expired Retention Only</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control form-control-sm"
                               placeholder="Search name, reference..."
                               value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary btn-sm" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-1">
                    <a href="{{ route('admin.archive.index') }}" class="btn btn-secondary btn-sm w-100">Clear</a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Archive Table --}}
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-file-archive me-2"></i>Archived Records
            <span class="badge bg-primary ms-2">{{ $items->total() }}</span>
        </h6>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.archive.retention-schedules') }}"
               class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-calendar-alt me-1"></i>Retention Schedules
            </a>
            <a href="{{ route('admin.archive.audit-log') }}"
               class="btn btn-outline-primary btn-sm">
                <i class="fas fa-history me-1"></i>Audit Log
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-bordered" id="archiveTable">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center" style="width:130px;">Reference</th>
                        <th class="text-center">Type</th>
                        <th>Item Name</th>
                        <th class="text-center">Classification</th>
                        <th class="text-center">Retention</th>
                        <th class="text-center">Expires</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Archived</th>
                        <th class="text-center" style="width:120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr class="{{ $item->is_expired && $item->disposal_status === 'retained' ? 'table-warning' : '' }}">
                            <td class="text-center">
                                <code class="text-primary small">{{ $item->archive_reference_number }}</code>
                                @if($item->is_locked)
                                    <i class="fas fa-lock text-muted ms-1" title="Record is locked (immutable)" style="font-size:0.7rem;"></i>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary small">{{ $item->type_label }}</span>
                            </td>
                            <td>
                                <strong class="text-dark">{{ $item->item_name }}</strong>
                                @if($item->is_expired && $item->disposal_status === 'retained')
                                    <span class="badge bg-warning text-dark ms-1 small">
                                        <i class="fas fa-exclamation-triangle"></i> Expired
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ $item->classification_badge_color }}">
                                    {{ ucfirst($item->classification) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge
                                    @if($item->retention_category === 'permanent') bg-danger
                                    @elseif($item->retention_category === 'long_term') bg-primary
                                    @elseif($item->retention_category === 'standard') bg-info text-dark
                                    @else bg-secondary @endif">
                                    {{ $item->retention_category_label }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($item->retention_category === 'permanent')
                                    <span class="text-danger fw-bold small">PERMANENT</span>
                                @elseif($item->retention_expires_at)
                                    @php $daysLeft = $item->days_until_expiry; @endphp
                                    <small class="{{ $daysLeft < 0 ? 'text-danger' : ($daysLeft < 365 ? 'text-warning' : 'text-muted') }}">
                                        {{ $item->retention_expires_at->format('M d, Y') }}
                                        <br>
                                        @if($daysLeft < 0)
                                            <span class="badge bg-danger" style="font-size:0.65rem;">
                                                {{ abs($daysLeft) }}d overdue
                                            </span>
                                        @elseif($daysLeft < 365)
                                            <span class="badge bg-warning text-dark" style="font-size:0.65rem;">
                                                {{ $daysLeft }}d left
                                            </span>
                                        @else
                                            <span class="text-muted" style="font-size:0.7rem;">
                                                {{ number_format($daysLeft / 365, 1) }}y left
                                            </span>
                                        @endif
                                    </small>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ $item->disposal_status_badge_color }}">
                                    {{ ucwords(str_replace('_', ' ', $item->disposal_status)) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <small class="text-muted">
                                    {{ $item->archived_at->format('M d, Y') }}<br>
                                    <span class="text-muted" style="font-size:0.7rem;">by {{ $item->archivedBy?->name ?? 'Unknown' }}</span>
                                </small>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary"
                                            onclick="viewArchiveRecord({{ $item->id }})"
                                            title="View Details & Audit Log">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if($item->disposal_status === 'retained' && $item->is_expired)
                                        <button class="btn btn-outline-warning"
                                                onclick="openApproveDisposalModal({{ $item->id }}, '{{ $item->archive_reference_number }}')"
                                                title="Approve for Disposal">
                                            <i class="fas fa-check-circle"></i>
                                        </button>
                                    @endif
                                    @if($item->disposal_status === 'approved_for_disposal')
                                        <button class="btn btn-outline-success"
                                                onclick="openRevokeDisposalModal({{ $item->id }}, '{{ $item->archive_reference_number }}')"
                                                title="Revoke Disposal Approval">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                        <button class="btn btn-outline-danger"
                                                onclick="openDisposeModal({{ $item->id }}, '{{ $item->archive_reference_number }}')"
                                                title="Permanently Dispose">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">
                                <i class="fas fa-file-archive fa-3x mb-3" style="opacity:0.2;"></i>
                                <p>No archived records found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($items->hasPages())
            <div class="d-flex justify-content-center mt-4">
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm">
                        {{-- Previous Page Link --}}
                        @if ($items->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link">Back</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $items->appends(request()->query())->previousPageUrl() }}" rel="prev">Back</a>
                            </li>
                        @endif

                        {{-- Pagination Elements --}}
                        @php
                            $currentPage = $items->currentPage();
                            $lastPage = $items->lastPage();
                            $startPage = max(1, $currentPage - 2);
                            $endPage = min($lastPage, $currentPage + 2);

                            if ($endPage - $startPage < 4) {
                                if ($startPage == 1) {
                                    $endPage = min($lastPage, $startPage + 4);
                                } else {
                                    $startPage = max(1, $endPage - 4);
                                }
                            }
                        @endphp

                        @for ($page = $startPage; $page <= $endPage; $page++)
                            @if ($page == $currentPage)
                                <li class="page-item active">
                                    <span class="page-link bg-primary border-primary">{{ $page }}</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $items->appends(request()->query())->url($page) }}">{{ $page }}</a>
                                </li>
                            @endif
                        @endfor

                        {{-- Next Page Link --}}
                        @if ($items->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $items->appends(request()->query())->nextPageUrl() }}" rel="next">Next</a>
                            </li>
                        @else
                            <li class="page-item disabled">
                                <span class="page-link">Next</span>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>
        @endif
    </div>
</div>


{{-- ============================================================
     MODALS
     ============================================================ --}}

{{-- View Record Details Modal --}}
<div class="modal fade" id="archiveDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header" style="background:linear-gradient(135deg,#1a237e,#283593);color:white;">
                <h5 class="modal-title w-100 text-center">
                    <i class="fas fa-file-archive me-2"></i>Archive Record Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="archiveModalBody">
                <div class="text-center py-4"><div class="spinner-border text-primary"></div></div>
            </div>
        </div>
    </div>
</div>

{{-- Approve Disposal Modal --}}
<div class="modal fade" id="approveDisposalModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title w-100 text-center">
                    <i class="fas fa-check-circle me-2"></i>Approve Record for Disposal
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <strong>ISO 15489 Disposal Approval</strong><br>
                    You are approving record <strong id="approveRefNumber"></strong> for disposal.
                    This requires a valid justification. The actual disposal is a separate step.
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Disposal Justification <span class="text-danger">*</span></label>
                    <textarea id="disposalNotes" class="form-control" rows="4"
                              placeholder="Provide justification for disposal (minimum 10 characters)..."></textarea>
                    <div class="form-text text-muted">This will be permanently recorded in the audit log.</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" onclick="confirmApproveDisposal()" id="confirmApproveBtn">
                    <span class="btn-text"><i class="fas fa-check-circle me-2"></i>Approve for Disposal</span>
                    <span class="btn-loader d-none"><span class="spinner-border spinner-border-sm me-2"></span>Approving...</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Revoke Disposal Modal --}}
<div class="modal fade" id="revokeDisposalModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title w-100 text-center">Revoke Disposal Approval</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Revoking disposal approval for: <strong id="revokeRefNumber"></strong></p>
                <div class="mb-3">
                    <label class="form-label">Reason for Revocation <span class="text-danger">*</span></label>
                    <input type="text" id="revokeReason" class="form-control" placeholder="State reason...">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="confirmRevokeDisposal()" id="confirmRevokeBtn">
                    <span class="btn-text"><i class="fas fa-undo me-2"></i>Revoke Disposal</span>
                    <span class="btn-loader d-none"><span class="spinner-border spinner-border-sm me-2"></span></span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Dispose Modal --}}
<div class="modal fade" id="disposeModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title w-100 text-center">
                    <i class="fas fa-skull-crossbones me-2"></i>PERMANENT DISPOSAL
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <strong><i class="fas fa-exclamation-triangle me-2"></i>IRREVERSIBLE ACTION</strong><br>
                    This will permanently wipe the record data. The compliance record (metadata + audit log) is retained per ISO 15489.
                    <br><br>Record: <strong id="disposeRefNumber"></strong>
                </div>
                <div class="mb-3">
                    <label class="form-label">Disposal Reason <span class="text-danger">*</span></label>
                    <textarea id="disposeReason" class="form-control" rows="3"
                              placeholder="State the final reason for disposal..."></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">
                        Type <code>CONFIRM_DISPOSE</code> to confirm <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="disposeConfirm" class="form-control"
                           placeholder="CONFIRM_DISPOSE">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmDispose()" id="confirmDisposeBtn">
                    <span class="btn-text"><i class="fas fa-trash-alt me-2"></i>Permanently Dispose</span>
                    <span class="btn-loader d-none"><span class="spinner-border spinner-border-sm me-2"></span>Disposing...</span>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .archive-integrity-pass { background: #d4edda; border-left: 4px solid #28a745; padding: 8px 12px; border-radius: 4px; }
    .archive-integrity-fail { background: #f8d7da; border-left: 4px solid #dc3545; padding: 8px 12px; border-radius: 4px; }
    .audit-log-item { border-left: 3px solid #dee2e6; padding-left: 12px; margin-bottom: 8px; }
    .audit-log-item:last-child { margin-bottom: 0; }
    table.table { white-space: normal; }
    code { font-size: 0.8rem; }
    /* Custom Pagination Styles */
    .pagination {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 8px;
        margin: 0;
    }
    .pagination .page-item .page-link {
        color: #6c757d;
        background-color: transparent;
        border: none;
        padding: 8px 12px;
        margin: 0 2px;
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    .pagination .page-item .page-link:hover {
        color: #495057;
        background-color: #e9ecef;
        text-decoration: none;
    }
    .pagination .page-item.active .page-link {
        color: white;
        background-color: #007bff;
        border-color: #007bff;
        font-weight: 600;
    }
    .pagination .page-item.disabled .page-link {
        color: #adb5bd;
        background-color: transparent;
        cursor: not-allowed;
    }
    .pagination .page-item:first-child .page-link,
    .pagination .page-item:last-child .page-link {
        font-weight: 600;
    }
    .toast-container {
    position: fixed; top: 20px; right: 20px; z-index: 9999;
    display: flex; flex-direction: column; gap: 12px; pointer-events: none;
    }
    .toast-notification {
        background: white; border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        min-width: 380px; max-width: 600px; overflow: hidden;
        opacity: 0; transform: translateX(400px);
        transition: all 0.3s cubic-bezier(0.23, 1, 0.320, 1);
        pointer-events: auto;
    }
    .toast-notification.show { opacity: 1; transform: translateX(0); }
    .toast-notification .toast-content { display: flex; align-items: center; padding: 20px; font-size: 1.05rem; }
    .toast-notification .toast-content i { font-size: 1.5rem; }
    .toast-notification .toast-content span { flex: 1; color: #333; }
    .toast-notification.toast-success { border-left: 4px solid #28a745; }
    .toast-notification.toast-success .toast-content i { color: #28a745; }
    .toast-notification.toast-error   { border-left: 4px solid #dc3545; }
    .toast-notification.toast-error   .toast-content i { color: #dc3545; }
    .toast-notification.toast-warning { border-left: 4px solid #ffc107; }
    .toast-notification.toast-warning .toast-content i { color: #ffc107; }
    .toast-notification.toast-info    { border-left: 4px solid #17a2b8; }
    .toast-notification.toast-info    .toast-content i { color: #17a2b8; }
    .btn-close-toast { background: none; border: none; font-size: 1.1rem; opacity: 0.5; cursor: pointer; padding: 0; margin-left: 10px; }
    .btn-close-toast:hover { opacity: 1; }
</style>

<script>
const CSRF = () => document.querySelector('meta[name="csrf-token"]')?.content || '';

let currentApproveId = null;
let currentRevokeId  = null;
let currentDisposeId = null;

// ─── View Record ────────────────────────────────────────────────────────────
function viewArchiveRecord(id) {
    document.getElementById('archiveModalBody').innerHTML =
        '<div class="text-center py-4"><div class="spinner-border text-primary"></div><p class="mt-2 text-muted">Loading record...</p></div>';
    new bootstrap.Modal(document.getElementById('archiveDetailsModal')).show();

    fetch(`/admin/archive/${id}`, {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF() }
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) {
            document.getElementById('archiveModalBody').innerHTML = '<p class="text-danger text-center py-4">Error loading record.</p>';
            return;
        }
        const d = data.data;

        // ── Field label mapping (snake_case → human readable) ──
        const fieldLabels = {
            id: null, // hide
            archive_record_id: null,
            checksum: null,
            password: null,
            remember_token: null,
            created_at: null,
            updated_at: null,
            deleted_at: null,
            // Friendly mappings
            first_name: 'First Name',
            last_name: 'Last Name',
            middle_name: 'Middle Name',
            full_name: 'Full Name',
            contact_number: 'Contact Number',
            email: 'Email Address',
            address: 'Address',
            barangay: 'Barangay',
            municipality: 'Municipality',
            province: 'Province',
            region: 'Region',
            birthdate: 'Date of Birth',
            birth_date: 'Date of Birth',
            age: 'Age',
            sex: 'Sex',
            gender: 'Gender',
            civil_status: 'Civil Status',
            nationality: 'Nationality',
            occupation: 'Occupation',
            farm_size: 'Farm Size',
            farm_location: 'Farm Location',
            commodity: 'Commodity / Crop',
            application_number: 'Application No.',
            request_number: 'Request No.',
            reference_number: 'Reference No.',
            status: 'Status',
            remarks: 'Remarks',
            is_active: 'Active',
            role: 'Role',
            username: 'Username',
            boat_name: 'Boat Name',
            boat_type: 'Boat Type',
            registration_number: 'Registration No.',
            vessel_type: 'Vessel Type',
            engine_type: 'Engine Type',
            gross_tonnage: 'Gross Tonnage',
            fishing_gear: 'Fishing Gear',
            fish_type: 'Fish Type',
            quantity: 'Quantity',
            unit: 'Unit',
            training_title: 'Training Title',
            training_date: 'Training Date',
            venue: 'Venue',
            title: 'Title',
            name: 'Name',
            description: 'Description',
            notes: 'Notes',
            item_name: 'Item Name',
            approved_quantity: 'Approved Quantity',
            requested_quantity: 'Requested Quantity',
        };

        // ── Format a single value nicely ──
        const fmt = (key, val) => {
            if (val === null || val === undefined || val === '') return '<span class="text-muted">—</span>';
            if (typeof val === 'boolean' || val === 1 || val === 0) {
                return val ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>';
            }
            if (typeof val === 'string' && val.match(/^\d{4}-\d{2}-\d{2}[T\s]\d{2}:\d{2}/)) {
                const dt = new Date(val);
                return `<i class="fas fa-calendar-alt text-muted me-1" style="font-size:0.75rem;"></i>${dt.toLocaleDateString('en-US', { year:'numeric', month:'short', day:'numeric' })} ${dt.toLocaleTimeString('en-US', { hour:'2-digit', minute:'2-digit' })}`;
            }
            if (typeof val === 'string' && val.match(/^\d{4}-\d{2}-\d{2}$/)) {
                const dt = new Date(val + 'T00:00:00');
                return dt.toLocaleDateString('en-US', { year:'numeric', month:'long', day:'numeric' });
            }
            if (typeof val === 'object') return `<code class="small text-muted">${JSON.stringify(val)}</code>`;
            if (typeof val === 'string' && val.length > 120) return `<span title="${val}">${val.substring(0,120)}<span class="text-muted">…</span></span>`;
            return String(val);
        };

        // ── Build data rows (only known/mapped fields, skip hidden) ──
        let dataRowsHtml = '';
        let unknownRows  = '';
        if (d.data) {
            Object.entries(d.data).forEach(([k, v]) => {
                if (fieldLabels[k] === null) return; // explicitly hidden
                const label = fieldLabels[k] || k.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                const row = `
                    <div class="d-flex py-2 border-bottom align-items-start">
                        <span class="text-muted small" style="min-width:160px;flex-shrink:0;">${label}</span>
                        <span class="small fw-semibold text-dark">${fmt(k, v)}</span>
                    </div>`;
                if (fieldLabels[k]) {
                    dataRowsHtml += row;
                } else {
                    unknownRows += row;
                }
            });
        }

        const dataSection = (dataRowsHtml || unknownRows)
            ? (dataRowsHtml + unknownRows)
            : '<p class="text-muted small text-center py-3">Data has been disposed or is unavailable.</p>';

        // ── Integrity banner ──
        const integrityHtml = d.integrity?.valid
            ? `<div class="d-flex align-items-center gap-2 p-3 rounded mb-3" style="background:#d4edda;border-left:4px solid #28a745;">
                    <i class="fas fa-shield-alt text-success fa-lg"></i>
                    <div><strong class="text-success">Integrity Verified</strong><br>
                    <small class="text-muted">Record data matches its stored checksum — not tampered.</small></div>
               </div>`
            : `<div class="d-flex align-items-center gap-2 p-3 rounded mb-3" style="background:#f8d7da;border-left:4px solid #dc3545;">
                    <i class="fas fa-exclamation-triangle text-danger fa-lg"></i>
                    <div><strong class="text-danger">Integrity Check Failed</strong><br>
                    <small class="text-muted">Data does not match stored checksum — possible tampering detected.</small></div>
               </div>`;

        // ── Audit trail ──
        const auditIcons = {
            archived:             { icon: 'fa-archive',        color: 'primary'   },
            approved_for_disposal:{ icon: 'fa-check-circle',   color: 'warning'   },
            revoked:              { icon: 'fa-undo',           color: 'info'      },
            disposed:             { icon: 'fa-trash-alt',      color: 'danger'    },
            viewed:               { icon: 'fa-eye',            color: 'secondary' },
        };
        const auditHtml = (d.audit_logs || []).length
            ? (d.audit_logs.map(log => {
                const ico = auditIcons[log.action?.toLowerCase()] || { icon:'fa-circle', color:'secondary' };
                return `
                    <div class="d-flex gap-3 pb-3 mb-3 border-bottom">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:36px;height:36px;background:var(--bs-${ico.color},#6c757d)15;border:2px solid var(--bs-${ico.color},#6c757d);">
                            <i class="fas ${ico.icon} text-${ico.color}" style="font-size:0.8rem;"></i>
                        </div>
                        <div>
                            <div class="fw-semibold small">${log.action?.replace(/_/g,' ').replace(/\b\w/g,l=>l.toUpperCase()) || '—'}</div>
                            <div class="text-muted" style="font-size:0.75rem;"><i class="fas fa-user me-1"></i>${log.performed_by || 'System'} · ${log.performed_at || ''}</div>
                            ${log.notes ? `<div class="mt-1 p-2 rounded small text-muted" style="background:#f8f9fa;">${log.notes}</div>` : ''}
                        </div>
                    </div>`;
              }).join(''))
            : '<p class="text-muted small text-center">No audit entries yet.</p>';

        // ── Retention badge color ──
        const retentionColors = { permanent:'danger', long_term:'primary', standard:'info', short_term:'secondary' };
        const retColor = retentionColors[d.retention_category] || 'secondary';

        document.getElementById('archiveModalBody').innerHTML = `

            <!-- Header strip -->
            <div class="p-3 rounded mb-4 d-flex flex-wrap align-items-center justify-content-between gap-2"
                 style="background:linear-gradient(135deg,#f0f4ff,#e8eaf6);">
                <div>
                    <div class="text-muted small mb-1">Archive Reference</div>
                    <code class="text-primary fs-6">${d.archive_reference_number || '—'}</code>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <span class="badge bg-secondary px-3 py-2">${d.type_label || '—'}</span>
                    <span class="badge bg-${d.classification_badge || 'secondary'} px-3 py-2">${d.classification || '—'}</span>
                    <span class="badge bg-${d.disposal_status_badge || 'secondary'} px-3 py-2">
                        ${(d.disposal_status || '').replace(/_/g,' ').replace(/\b\w/g,l=>l.toUpperCase())}
                    </span>
                    ${d.is_locked ? '<span class="badge bg-dark px-3 py-2"><i class="fas fa-lock me-1"></i>Immutable</span>' : ''}
                </div>
            </div>

            ${integrityHtml}

            <!-- Two-column layout -->
            <div class="row g-3 mb-3">

                <!-- LEFT: Record data -->
                <div class="col-lg-7">
                    <div class="card border-0 h-100" style="background:#f8f9fa;">
                        <div class="card-header border-0 bg-white d-flex align-items-center gap-2">
                            <i class="fas fa-id-card text-primary"></i>
                            <strong class="small">Record Information</strong>
                        </div>
                        <div class="card-body" style="max-height:380px;overflow-y:auto;">
                            ${dataSection}
                        </div>
                    </div>
                </div>

                <!-- RIGHT: Meta + Retention -->
                <div class="col-lg-5 d-flex flex-column gap-3">

                    <!-- Archive Meta -->
                    <div class="card border-0" style="background:#f8f9fa;">
                        <div class="card-header border-0 bg-white d-flex align-items-center gap-2">
                            <i class="fas fa-archive text-secondary"></i>
                            <strong class="small">Archive Details</strong>
                        </div>
                        <div class="card-body py-2 px-3">
                            <div class="d-flex justify-content-between py-2 border-bottom small">
                                <span class="text-muted">Archived By</span>
                                <span class="fw-semibold">${d.archived_by || '—'}</span>
                            </div>
                            <div class="d-flex justify-content-between py-2 border-bottom small">
                                <span class="text-muted">Archived At</span>
                                <span class="fw-semibold">${d.archived_at || '—'}</span>
                            </div>
                            <div class="d-flex justify-content-between py-2 small">
                                <span class="text-muted">Archive Reason</span>
                                <span class="fw-semibold text-end" style="max-width:60%;">${d.archive_reason || '—'}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Retention -->
                    <div class="card border-0" style="background:#f8f9fa;">
                        <div class="card-header border-0 bg-white d-flex align-items-center gap-2">
                            <i class="fas fa-calendar-check text-warning"></i>
                            <strong class="small">Retention Schedule</strong>
                        </div>
                        <div class="card-body py-2 px-3">
                            <div class="d-flex justify-content-between py-2 border-bottom small">
                                <span class="text-muted">Category</span>
                                <span class="badge bg-${retColor}">${d.retention_category_label || '—'}</span>
                            </div>
                            <div class="d-flex justify-content-between py-2 border-bottom small">
                                <span class="text-muted">Period</span>
                                <span class="fw-semibold">${d.retention_years > 0 ? d.retention_years + ' years' : 'Permanent'}</span>
                            </div>
                            <div class="d-flex justify-content-between py-2 border-bottom small">
                                <span class="text-muted">Expires</span>
                                <span class="fw-semibold ${d.is_expired ? 'text-danger' : 'text-dark'}">${d.retention_expires_at || 'Never'}</span>
                            </div>
                            <div class="d-flex justify-content-between py-2 small">
                                <span class="text-muted">Legal Basis</span>
                                <span class="fw-semibold text-end" style="max-width:60%;font-size:0.75rem;">${d.disposal_authority || '—'}</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Audit Trail -->
            <div class="card border-0" style="background:#f8f9fa;">
                <div class="card-header border-0 bg-white d-flex align-items-center gap-2">
                    <i class="fas fa-history text-info"></i>
                    <strong class="small">Audit Trail</strong>
                    <span class="badge bg-light text-muted ms-1">${(d.audit_logs || []).length} entries</span>
                </div>
                <div class="card-body" style="max-height:260px;overflow-y:auto;">
                    ${auditHtml}
                </div>
            </div>
        `;
    })
    .catch(() => {
        document.getElementById('archiveModalBody').innerHTML =
            '<div class="alert alert-danger m-3"><i class="fas fa-exclamation-circle me-2"></i>Error loading record. Please try again.</div>';
    });
}

// ─── Approve Disposal ────────────────────────────────────────────────────────
function openApproveDisposalModal(id, ref) {
    currentApproveId = id;
    document.getElementById('approveRefNumber').textContent = ref;
    document.getElementById('disposalNotes').value = '';
    new bootstrap.Modal(document.getElementById('approveDisposalModal')).show();
}

function confirmApproveDisposal() {
    const notes = document.getElementById('disposalNotes').value.trim();
    if (notes.length < 10) { showToast('warning', 'Justification must be at least 10 characters.'); return; }

    const btn = document.getElementById('confirmApproveBtn');
    setLoadingState(btn, true);

    fetch(`/admin/archive/${currentApproveId}/approve-disposal`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF() },
        body: JSON.stringify({ notes })
    })
    .then(r => r.json())
    .then(data => {
        bootstrap.Modal.getInstance(document.getElementById('approveDisposalModal')).hide();
        showToast(data.success ? 'success' : 'error', data.message);
        if (data.success) setTimeout(() => location.reload(), 1500);
    })
    .finally(() => setLoadingState(btn, false));
}

// ─── Revoke Disposal ─────────────────────────────────────────────────────────
function openRevokeDisposalModal(id, ref) {
    currentRevokeId = id;
    document.getElementById('revokeRefNumber').textContent = ref;
    document.getElementById('revokeReason').value = '';
    new bootstrap.Modal(document.getElementById('revokeDisposalModal')).show();
}

function confirmRevokeDisposal() {
    const reason = document.getElementById('revokeReason').value.trim();
    if (reason.length < 5) { showToast('warning', 'Reason is required.'); return; }

    const btn = document.getElementById('confirmRevokeBtn');
    setLoadingState(btn, true);

    fetch(`/admin/archive/${currentRevokeId}/revoke-disposal`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF() },
        body: JSON.stringify({ reason })
    })
    .then(r => r.json())
    .then(data => {
        bootstrap.Modal.getInstance(document.getElementById('revokeDisposalModal')).hide();
        showToast(data.success ? 'success' : 'error', data.message);
        if (data.success) setTimeout(() => location.reload(), 1500);
    })
    .finally(() => setLoadingState(btn, false));
}

// ─── Dispose ─────────────────────────────────────────────────────────────────
function openDisposeModal(id, ref) {
    currentDisposeId = id;
    document.getElementById('disposeRefNumber').textContent = ref;
    document.getElementById('disposeReason').value = '';
    document.getElementById('disposeConfirm').value = '';
    new bootstrap.Modal(document.getElementById('disposeModal')).show();
}

function confirmDispose() {
    const reason  = document.getElementById('disposeReason').value.trim();
    const confirm = document.getElementById('disposeConfirm').value.trim();

    if (reason.length < 10) { showToast('warning','Disposal reason must be at least 10 characters.'); return; }
    if (confirm !== 'CONFIRM_DISPOSE') { showToast('warning', 'Please type CONFIRM_DISPOSE exactly to proceed.'); return; }

    const btn = document.getElementById('confirmDisposeBtn');
    setLoadingState(btn, true);

    fetch(`/admin/archive/${currentDisposeId}/dispose`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF() },
        body: JSON.stringify({ reason, confirm })
    })
    .then(r => r.json())
    .then(data => {
        bootstrap.Modal.getInstance(document.getElementById('disposeModal')).hide();
        showToast(data.success ? 'success' : 'error', data.message);
        if (data.success) setTimeout(() => location.reload(), 1500);
    })
    .finally(() => setLoadingState(btn, false));
}

// ─── Helpers ─────────────────────────────────────────────────────────────────
function setLoadingState(btn, loading) {
    btn.querySelector('.btn-text').classList.toggle('d-none', loading);
    btn.querySelector('.btn-loader').classList.toggle('d-none', !loading);
    btn.disabled = loading;
}
// ─── Toast System ─────────────────────────────────────────────────────────────
function createToastContainer() {
    let container = document.getElementById('toastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toastContainer';
        container.className = 'toast-container';
        document.body.appendChild(container);
    }
    return container;
}

function showToast(type, message) {
    const toastContainer = createToastContainer();
    const iconMap = {
        'success': 'fas fa-check-circle',
        'error':   'fas fa-exclamation-circle',
        'warning': 'fas fa-exclamation-triangle',
        'info':    'fas fa-info-circle'
    };
    const toast = document.createElement('div');
    toast.className = `toast-notification toast-${type}`;
    toast.innerHTML = `
        <div class="toast-content">
            <i class="${iconMap[type] || iconMap['info']} me-2"></i>
            <span>${message}</span>
            <button type="button" class="btn-close-toast ms-auto"
                onclick="removeToast(this.closest('.toast-notification'))">✕</button>
        </div>`;
    toastContainer.appendChild(toast);
    setTimeout(() => toast.classList.add('show'), 10);
    setTimeout(() => { if (document.contains(toast)) removeToast(toast); }, 5000);
}

function removeToast(toast) {
    toast.classList.remove('show');
    setTimeout(() => { if (toast.parentElement) toast.remove(); }, 300);
}
</script>

@endsection