@extends('layouts.app')

@section('title', 'DSS Report Preview - AgriSys Admin')
@section('page-icon', 'fas fa-brain')
@section('page-title', 'Decision Support System - Report Preview')

@section('styles')
    <style>
        /* Category Tab Styles - From Supplies & Garden Tools */
        .btn-outline-secondary {
            padding: 12px 24px;
            background: #ffffff !important;
            border: 2px solid #e0e0e0 !important;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.95rem;
            color: #555 !important;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: none !important;
        }

        .btn-outline-secondary:hover {
            background: #f8f9fa !important;
            border-color: #40916c !important;
            color: #40916c !important;
            box-shadow: none !important;
        }

        .btn-outline-secondary:focus,
        .btn-outline-secondary:active,
        .btn-outline-secondary:focus-visible {
            background: #f8f9fa !important;
            border-color: #40916c !important;
            color: #40916c !important;
            box-shadow: none !important;
        }

        .btn-success {
            background: linear-gradient(135deg, #40916c 0%, #52b788 100%) !important;
            color: #ffffff !important;
            border: 2px solid #40916c !important;
            box-shadow: 0 4px 12px rgba(64, 145, 108, 0.3) !important;
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #2d6a4f 0%, #40916c 100%) !important;
            border-color: #2d6a4f !important;
        }
        /* ─── Wrapper ─────────────────────────────────────────────── */
        .dss-nav-wrapper {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            box-shadow: 0 1px 6px rgba(0, 0, 0, 0.06);
            padding: 6px 8px;
            overflow: hidden;
        }

        /* ─── Horizontal scroll container ────────────────────────── */
        .dss-nav-scroll {
            overflow-x: auto;
            overflow-y: hidden;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
        }
        .dss-nav-scroll::-webkit-scrollbar {
            display: none;
        }

        /* ─── The nav row itself ──────────────────────────────────── */
        .dss-nav {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
            gap: 4px;
            white-space: nowrap;
            padding: 2px 0;
            width: 100%;
        }

        /* ─── Individual pill (button) ───────────────────────────── */
        .dss-nav-pill {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 18px;
            border-radius: 10px;
            border: 1.5px solid transparent;
            background: transparent;
            color: #6b7280;
            font-size: 0.845rem;
            font-weight: 600;
            letter-spacing: 0.01em;
            cursor: pointer;
            flex-shrink: 0;
            white-space: nowrap;
            transition: color 0.18s ease, background 0.18s ease,
                        border-color 0.18s ease, box-shadow 0.18s ease,
                        transform 0.18s ease;
        }

        .dss-nav-pill:hover {
            color: #1a7a4a;
            background: #f0faf4;
            border-color: #b7e4c7;
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(40, 167, 69, 0.12);
        }

        /* ─── Active pill ─────────────────────────────────────────── */
        .dss-nav-pill.active {
            color: #ffffff;
            background: linear-gradient(135deg, #2d6a4f 0%, #52b788 100%);
            border-color: #2d6a4f;
            box-shadow: 0 3px 12px rgba(45, 106, 79, 0.35);
            transform: translateY(-1px);
        }

        .dss-nav-pill.active:hover {
            background: linear-gradient(135deg, #1e4d38 0%, #40916c 100%);
            box-shadow: 0 5px 16px rgba(45, 106, 79, 0.4);
        }

        /* ─── Icon ────────────────────────────────────────────────── */
        .dss-nav-pill-icon {
            display: flex;
            align-items: center;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        .dss-nav-pill.active .dss-nav-pill-icon {
            color: rgba(255, 255, 255, 0.95);
        }
        /* ─── Wrapper ─────────────────────────────────────────────── */
        .dss-filter-wrapper {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            box-shadow: 0 1px 6px rgba(0, 0, 0, 0.06);
            padding: 16px 20px;
        }

        /* ─── Form row ────────────────────────────────────────────── */
        .dss-filter-form {
            display: flex;
            align-items: flex-end;
            gap: 12px;
            flex-wrap: wrap;
            justify-content: flex-start;
        }

        /* ─── Label + input group ─────────────────────────────────── */
        .dss-filter-form .filter-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .dss-filter-form .filter-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #6b7280;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 5px;
            margin: 0;
        }

        .dss-filter-form .filter-label i {
            color: #40916c;
            font-size: 0.75rem;
        }

        /* ─── Inputs (shared with select) ────────────────────────── */
        .dss-filter-form .filter-input {
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            padding: 8px 12px;
            font-size: 0.875rem;
            font-weight: 500;
            color: #1f2937;
            background: #f9fafb;
            transition: border-color 0.18s ease, box-shadow 0.18s ease, background 0.18s ease;
            outline: none;
            min-width: 150px;
            appearance: auto; /* keeps native select arrow */
        }

        .dss-filter-form .filter-input:focus {
            border-color: #40916c;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(64, 145, 108, 0.12);
        }

        /* ─── Arrow divider ───────────────────────────────────────── */
        .dss-filter-form .filter-divider {
            color: #d1d5db;
            font-size: 0.75rem;
            padding-bottom: 10px;
            flex-shrink: 0;
        }

        /* ─── Actions group ───────────────────────────────────────── */
        .dss-filter-form .filter-actions {
            display: flex;
            gap: 8px;
            margin-left: auto;
            flex-wrap: wrap;
            align-items: flex-end;
        }

        /* ─── Base button ─────────────────────────────────────────── */
        .filter-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 18px;
            border-radius: 10px;
            font-size: 0.845rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: transform 0.18s ease, box-shadow 0.18s ease, background 0.18s ease;
            white-space: nowrap;
            line-height: 1.4;
        }

        .filter-btn:hover {
            transform: translateY(-1px);
            text-decoration: none;
        }

        /* Generate / Apply */
        .filter-btn-apply {
            background: linear-gradient(135deg, #2d6a4f 0%, #52b788 100%);
            color: #ffffff;
            box-shadow: 0 2px 8px rgba(45, 106, 79, 0.25);
        }
        .filter-btn-apply:hover {
            background: linear-gradient(135deg, #1e4d38 0%, #40916c 100%);
            box-shadow: 0 4px 14px rgba(45, 106, 79, 0.35);
            color: #ffffff;
        }

        /* Refresh */
        .filter-btn-refresh {
            background: #f0faf4;
            color: #2d6a4f;
            border: 1.5px solid #b7e4c7;
            box-shadow: 0 1px 4px rgba(45, 106, 79, 0.08);
        }
        .filter-btn-refresh:hover {
            background: #d8f3dc;
            box-shadow: 0 3px 10px rgba(45, 106, 79, 0.15);
            color: #1e4d38;
        }
        .filter-btn-refresh:disabled {
            opacity: 0.6;
            transform: none;
            cursor: not-allowed;
        }

        /* Export dropdown trigger */
        .filter-btn-export {
            background: #f0faf4;
            color: #2d6a4f;
            border: 1.5px solid #b7e4c7;
            box-shadow: 0 1px 4px rgba(45, 106, 79, 0.08);
        }
        .filter-btn-export:hover,
        .filter-btn-export:focus,
        .filter-btn-export.show {
            background: #d8f3dc;
            box-shadow: 0 3px 10px rgba(45, 106, 79, 0.15);
            color: #1e4d38;
        }

        /* ─── Responsive ──────────────────────────────────────────── */
        @media (max-width: 768px) {
            .dss-filter-form {
                flex-direction: column;
                align-items: stretch;
            }
            .dss-filter-form .filter-divider {
                display: none;
            }
            .dss-filter-form .filter-actions {
                margin-left: 0;
            }
            .filter-btn {
                justify-content: center;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Loading Overlay -->
        <div id="loadingOverlay" class="position-fixed top-0 start-0 w-100 h-100 d-none"
            style="background: rgba(255,255,255,0.9); z-index: 9999;">
            <div class="d-flex justify-content-center align-items-center h-100">
                <div class="text-center">
                    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 fs-5">Generating DSS Report...</p>
                    <p class="text-muted">This may take a few moments</p>
                </div>
            </div>
        </div>

        <!-- Service Navigation Tabs -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="dss-nav-wrapper">
                    <div class="dss-nav-scroll">
                        <nav class="dss-nav">
                            <button class="dss-nav-pill {{ $service === 'comprehensive' ? 'active' : '' }}"
                                id="comprehensive-tab" data-service="comprehensive" type="button">
                                <span class="dss-nav-pill-icon"><i class="fas fa-boxes"></i></span>
                                <span class="dss-nav-pill-label">Supplies Report</span>
                            </button>

                            <button class="dss-nav-pill {{ $service === 'training' ? 'active' : '' }}"
                                id="training-tab" data-service="training" type="button">
                                <span class="dss-nav-pill-icon"><i class="fas fa-graduation-cap"></i></span>
                                <span class="dss-nav-pill-label">Training Report</span>
                            </button>

                            <button class="dss-nav-pill {{ $service === 'rsbsa' ? 'active' : '' }}"
                                id="rsbsa-tab" data-service="rsbsa" type="button">
                                <span class="dss-nav-pill-icon"><i class="fas fa-users"></i></span>
                                <span class="dss-nav-pill-label">RSBSA Report</span>
                            </button>

                            <button class="dss-nav-pill {{ $service === 'fishr' ? 'active' : '' }}"
                                id="fishr-tab" data-service="fishr" type="button">
                                <span class="dss-nav-pill-icon"><i class="fas fa-fish"></i></span>
                                <span class="dss-nav-pill-label">FISHR Report</span>
                            </button>

                            <button class="dss-nav-pill {{ $service === 'boatr' ? 'active' : '' }}"
                                id="boatr-tab" data-service="boatr" type="button">
                                <span class="dss-nav-pill-icon"><i class="fas fa-ship"></i></span>
                                <span class="dss-nav-pill-label">BOATR Report</span>
                            </button>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Period Selection -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="dss-filter-wrapper">
                    <form id="periodForm" class="dss-filter-form">
                        <input type="hidden" id="serviceInput" name="service" value="{{ $service }}">

                        <!-- Month -->
                        <div class="filter-group">
                            <label for="monthSelect" class="filter-label">
                                <i class="fas fa-calendar-alt"></i>
                                Month
                            </label>
                            <select class="filter-input" id="monthSelect" name="month">
                                @foreach (range(1, 12) as $m)
                                    <option value="{{ sprintf('%02d', $m) }}" {{ $m == $month ? 'selected' : '' }}>
                                        {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Divider -->
                        <div class="filter-divider">
                            —
                        </div>

                        <!-- Year -->
                        <div class="filter-group">
                            <label for="yearSelect" class="filter-label">
                                <i class="fas fa-calendar-check"></i>
                                Year
                            </label>
                            <!-- you can set if you wanted more than 3 showing select in year instead of - 2 -->
                            <select class="filter-input" id="yearSelect" name="year">
                                @foreach (range(now()->year - 2, now()->year) as $y)
                                    <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Actions -->
                        <div class="filter-actions">
                            <button type="submit" class="filter-btn filter-btn-apply">
                                <i class="fas fa-search"></i>
                                Generate Report
                            </button>

                            <button type="button" class="filter-btn filter-btn-refresh" id="refreshDataBtn">
                                <i class="fas fa-sync-alt"></i>
                                Refresh Data
                            </button>

                            <div class="dropdown">
                                <button class="filter-btn filter-btn-export dropdown-toggle"
                                    type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-download"></i>
                                    Export Report
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="#" id="downloadPdf">
                                            <i class="fas fa-file-pdf me-2 text-danger"></i>Download PDF
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#" id="downloadWord">
                                            <i class="fas fa-file-word me-2 text-primary"></i>Download Word
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>



        <!-- Report Content -->
        <div id="reportContent">
            <!-- Initial Loading State -->
            <div id="loadingState" class="row">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-body text-center py-5">
                            <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <h5>Generating DSS Report...</h5>
                            <div class="progress mt-3" style="height: 6px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 0%">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content will be loaded here -->
            <div id="reportData" style="display: none;">
                <!-- AJAX content loads here -->
            </div>

            <!-- Fallback state -->
            <div id="noDataState" style="display: none;">
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-brain fa-3x text-muted mb-3"></i>
                                <h5>No Report Data Available</h5>
                                <p class="text-muted">Please select a period and generate a report.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Modal -->
    <div class="modal fade" id="loadingModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h5>Generating AI-Powered Report</h5>
                    <p class="text-muted mb-0">Please wait while we analyze your data...</p>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const periodForm = document.getElementById('periodForm');
            const refreshBtn = document.getElementById('refreshDataBtn');
            const downloadPdf = document.getElementById('downloadPdf');
            const downloadWord = document.getElementById('downloadWord');
            const loadingOverlay = document.getElementById('loadingOverlay');
            const loadingState = document.getElementById('loadingState');
            const reportData = document.getElementById('reportData');
            const noDataState = document.getElementById('noDataState');
            const serviceInput = document.getElementById('serviceInput');

            // Cache for storing reports per service
            const reportCache = {
                comprehensive: null,
                training: null,
                rsbsa: null
            };

            // Service tab switching
            const serviceTabs = document.querySelectorAll('.dss-nav-pill');
            serviceTabs.forEach(tab => {
                tab.addEventListener('click', function () {
                    // Remove active from all pills
                    serviceTabs.forEach(t => t.classList.remove('active'));
                    // Add active to clicked pill
                    this.classList.add('active');
                    // Update hidden input
                    const selectedService = this.dataset.service;
                    serviceInput.value = selectedService;

                    // Show cached report if available, otherwise show "no data"
                    if (reportCache[selectedService]) {
                        showData(reportCache[selectedService]);
                    } else {
                        showNoData();
                    }
                });
            });

            // Show initial "no data" state instead of loading
            showNoData();

            // Progress animation
            function animateProgress() {
                const progressBar = document.querySelector('#loadingState .progress-bar');
                if (progressBar) {
                    let width = 0;
                    const interval = setInterval(() => {
                        width += Math.random() * 15;
                        if (width > 90) width = 90;
                        progressBar.style.width = width + '%';
                    }, 500);
                    return interval;
                }
            }

            function showLoading(message = 'Generating DSS Report...') {
                loadingState.style.display = 'block';
                reportData.style.display = 'none';
                noDataState.style.display = 'none';

                const messageEl = document.querySelector('#loadingState h5');
                if (messageEl) messageEl.textContent = message;

                return animateProgress();
            }

            function hideLoading(progressInterval) {
                if (progressInterval) clearInterval(progressInterval);
                loadingState.style.display = 'none';
            }

            function showData(html) {
                reportData.innerHTML = html;
                reportData.style.display = 'block';
                noDataState.style.display = 'none';
            }

            function showNoData() {
                loadingState.style.display = 'none';
                reportData.style.display = 'none';
                noDataState.style.display = 'block';
            }

            function getServiceName() {
                const service = serviceInput.value;
                return service === 'training' ? 'Training' :
                    service === 'rsbsa' ? 'RSBSA' : 'Comprehensive';
            }

            async function loadDSSData(month = null, year = null) {
                const currentMonth = month || document.getElementById('monthSelect').value;
                const currentYear = year || document.getElementById('yearSelect').value;
                const currentService = serviceInput.value;

                const progressInterval = showLoading(
                    `Generating ${getServiceName()} DSS Report for ${getMonthName(currentMonth)} ${currentYear}...`
                );

                try {
                    const response = await fetch(
                        `{{ route('admin.dss.preview') }}?month=${currentMonth}&year=${currentYear}&service=${currentService}`, {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });

                    const data = await response.json();
                    hideLoading(progressInterval);

                    if (data.success && data.html) {
                        // Cache the report for this service
                        reportCache[currentService] = data.html;
                        showData(data.html);
                        showToast('Report generated successfully!', 'success');
                    } else {
                        showNoData();
                        showToast(data.message || 'Failed to load report data', 'error');
                    }
                } catch (error) {
                    hideLoading(progressInterval);
                    showNoData();
                    showToast('Failed to load DSS data: ' + error.message, 'error');
                    console.error('DSS loading error: ', error);
                }
            }

            function getMonthName(monthNum) {
                const months = ['January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December'
                ];
                return months[parseInt(monthNum) - 1];
            }

            function showToast(message, type = 'info') {
                const toast = document.createElement('div');
                toast.className =
                    `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
                toast.style.cssText =
                    'top: 20px; right: 20px; z-index: 10000; min-width: 300px;';
                toast.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
                document.body.appendChild(toast);

                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 5000);
            }

            // Period form submission - use AJAX instead of page reload
            periodForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const month = document.getElementById('monthSelect').value;
                const year = document.getElementById('yearSelect').value;
                loadDSSData(month, year);
            });

            // Refresh data
            refreshBtn.addEventListener('click', async function() {
                const month = document.getElementById('monthSelect').value;
                const year = document.getElementById('yearSelect').value;
                const service = serviceInput.value;

                refreshBtn.disabled = true;
                refreshBtn.innerHTML =
                    '<i class="fas fa-spinner fa-spin me-1"></i>Refreshing...';

                try {
                    const response = await fetch(
                        `{{ route('admin.dss.refresh.data') }}?month=${month}&year=${year}&service=${service}`
                    );
                    const data = await response.json();

                    if (data.success) {
                        loadDSSData(month, year);
                        showToast('Data refreshed successfully!', 'success');
                    } else {
                        showToast(data.message || 'Failed to refresh data', 'error');
                    }
                } catch (error) {
                    showToast('Failed to refresh data: ' + error.message, 'error');
                } finally {
                    refreshBtn.disabled = false;
                    refreshBtn.innerHTML =
                        '<i class="fas fa-sync-alt me-1"></i>Refresh Data';
                }
            });

            // Download PDF
            downloadPdf.addEventListener('click', function(e) {
                e.preventDefault();
                const month = document.getElementById('monthSelect').value;
                const year = document.getElementById('yearSelect').value;
                const service = serviceInput.value;
                window.open(
                    `{{ route('admin.dss.download.pdf') }}?month=${month}&year=${year}&service=${service}`,
                    '_blank');
            });

            // Download Word
            downloadWord.addEventListener('click', function(e) {
                e.preventDefault();
                const month = document.getElementById('monthSelect').value;
                const year = document.getElementById('yearSelect').value;
                const service = serviceInput.value;
                window.open(
                    `{{ route('admin.dss.download.word') }}?month=${month}&year=${year}&service=${service}`,
                    '_blank');
            });
        });
    </script>
@endsection

@php
    function getRatingColor($rating)
    {
        switch (strtolower($rating)) {
            case 'excellent':
                return 'success';
            case 'good':
                return 'primary';
            case 'fair':
                return 'warning';
            case 'poor':
                return 'danger';
            default:
                return 'secondary';
        }
    }
@endphp
