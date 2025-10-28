@extends('layouts.app')

@section('title', 'DSS Report Preview - AgriSys Admin')
@section('page-title', 'Decision Support System - Report Preview')

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

        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-2 text-primary">
                                    <i class="fas fa-brain me-2"></i>Decision Support System
                                </h4>
                                <p class="text-muted mb-0">AI-Powered Agricultural Intelligence Report</p>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-primary" id="refreshDataBtn">
                                    <i class="fas fa-sync-alt me-1"></i>Refresh Data
                                </button>
                                <div class="dropdown">
                                    <button
                                        class="btn btn-success dropdown-toggle"
                                        type="button" data-bs-toggle="dropdown">
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
                    </div>
                </div>
            </div>
        </div>

        <!-- Period Selection -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <form id="periodForm" class="row g-3">
                            <div class="col-md-4">
                                <label for="monthSelect" class="form-label">Month</label>
                                <select class="form-select" id="monthSelect" name="month">
                                    @foreach (range(1, 12) as $m)
                                        <option value="{{ sprintf('%02d', $m) }}" {{ $m == $month ? 'selected' : '' }}>
                                            {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="yearSelect" class="form-label">Year</label>
                                <select class="form-select" id="yearSelect" name="year">
                                    @foreach (range(now()->year - 2, now()->year) as $y)
                                        <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>
                                            {{ $y }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary d-block">
                                    <i class="fas fa-search me-1"></i>Generate Report
                                </button>
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
                                <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 0%"></div>
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

            // Load initial data
            loadDSSData();

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

            async function loadDSSData(month = null, year = null) {
                const currentMonth = month || document.getElementById('monthSelect').value;
                const currentYear = year || document.getElementById('yearSelect').value;

                const progressInterval = showLoading(
                    `Generating DSS Report for ${getMonthName(currentMonth)} ${currentYear}...`
                );

                try {
                    const response = await fetch(
                        `{{ route('admin.dss.preview') }}?month=${currentMonth}&year=${currentYear}`, {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });

                    const data = await response.json();
                    hideLoading(progressInterval);

                    if (data.success && data.html) {
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
                    'July', 'August', 'September', 'October', 'November', 'December'];
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

                refreshBtn.disabled = true;
                refreshBtn.innerHTML =
                    '<i class="fas fa-spinner fa-spin me-1"></i>Refreshing...';

                try {
                    const response = await fetch(
                        `{{ route('admin.dss.refresh.data') }}?month=${month}&year=${year}`
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
                window.open(
                    `{{ route('admin.dss.download.pdf') }}?month=${month}&year=${year}`,
                    '_blank');
            });

            // Download Word
            downloadWord.addEventListener('click', function(e) {
                e.preventDefault();
                const month = document.getElementById('monthSelect').value;
                const year = document.getElementById('yearSelect').value;
                window.open(
                    `{{ route('admin.dss.download.word') }}?month=${month}&year=${year}`,
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
