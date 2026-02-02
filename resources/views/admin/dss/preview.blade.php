@extends('layouts.app')

@section('title', 'DSS Report Preview - AgriSys Admin')
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
                <div class="d-flex gap-2 justify-content-center flex-wrap">
                    <button
                        class="btn {{ $service === 'comprehensive' ? 'btn-success' : 'btn-outline-secondary' }} px-4 py-2"
                        id="comprehensive-tab" data-service="comprehensive" type="button">
                        <i class="fas fa-boxes me-2"></i>Supplies Request Report
                    </button>
                    <button class="btn {{ $service === 'training' ? 'btn-success' : 'btn-outline-secondary' }} px-4 py-2"
                        id="training-tab" data-service="training" type="button">
                        <i class="fas fa-graduation-cap me-2"></i>Training Request Report
                    </button>
                    <button class="btn {{ $service === 'rsbsa' ? 'btn-success' : 'btn-outline-secondary' }} px-4 py-2"
                        id="rsbsa-tab" data-service="rsbsa" type="button">
                        <i class="fas fa-users me-2"></i>RSBSA Request Report
                    </button>
                    <button class="btn {{ $service === 'fishr' ? 'btn-success' : 'btn-outline-secondary' }} px-4 py-2"
                        id="fishr-tab" data-service="fishr" type="button">
                        <i class="fas fa-fish me-2"></i>FISHR Request Report
                    </button>
                    <button class="btn {{ $service === 'boatr' ? 'btn-success' : 'btn-outline-secondary' }} px-4 py-2"
                        id="boatr-tab" data-service="boatr" type="button">
                        <i class="fas fa-ship me-2"></i>BOATR Request Report
                    </button>
                </div>
            </div>
        </div>

        <!-- Period Selection -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <form id="periodForm" class="row g-3">
                            <input type="hidden" id="serviceInput" name="service" value="{{ $service }}">
                            <div class="col-md-3">
                                <label for="monthSelect" class="form-label">Month</label>
                                <select class="form-select" id="monthSelect" name="month">
                                    @foreach (range(1, 12) as $m)
                                        <option value="{{ sprintf('%02d', $m) }}" {{ $m == $month ? 'selected' : '' }}>
                                            {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="yearSelect" class="form-label">Year</label>
                                <select class="form-select" id="yearSelect" name="year">
                                    @foreach (range(now()->year - 2, now()->year) as $y)
                                        <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>
                                            {{ $y }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search me-1"></i>Generate Report
                                    </button>
                                    <button type="button" class="btn btn-outline-primary" id="refreshDataBtn">
                                        <i class="fas fa-sync-alt me-1"></i>Refresh Data
                                    </button>
                                    <div class="dropdown">
                                        <button class="btn btn-success dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown">
                                            <i class="fas fa-download me-1"></i>Export Report
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#" id="downloadPdf">
                                                    <i class="fas fa-file-pdf me-2 text-danger"></i>Download PDF
                                                </a>
                                            </li>
                                            <li><a class="dropdown-item" href="#" id="downloadWord">
                                                    <i class="fas fa-file-word me-2 text-primary"></i>Download Word
                                                </a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
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
            const serviceTabs = document.querySelectorAll('[data-service]');
            serviceTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    // Remove active from all tabs
                    serviceTabs.forEach(t => {
                        t.classList.remove('btn-success');
                        t.classList.add('btn-outline-secondary');
                    });
                    // Add active to clicked tab
                    this.classList.remove('btn-outline-secondary');
                    this.classList.add('btn-success');
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
