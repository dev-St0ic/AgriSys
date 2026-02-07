@extends('layouts.app')

@section('title', 'Admin Dashboard - AgriSys')
@section('page-title', 'Dashboard')

@section('content')
    <div class="farmvista-dashboard">
        <!-- Header Section with Welcome -->
        <div class="dashboard-welcome-header">
            <div class="welcome-content">
                <h1 class="welcome-title" id="greeting">Good Morning!</h1>
            </div>
        </div>

        <!-- Main Dashboard Grid -->
        <div class="dashboard-main-grid">
            <!-- Left Column - Main Content -->
            <div class="dashboard-left-section">

                <!-- Key Metrics Cards Row -->
                <div class="metrics-row">
                    <!-- Total Users Card -->
                    <div class="metric-card-modern">
                        <div class="metric-icon-wrapper green">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="metric-info">
                            <div class="metric-label">Total Users</div>
                            <div class="metric-value-large">{{ number_format($keyMetrics['total_users'] ?? 0) }}</div>
                            <div class="metric-trend positive">
                                <i class="fas fa-arrow-up"></i> +2% from last month
                            </div>
                        </div>
                    </div>

                    <!-- Current Applications Card -->
                    <div class="metric-card-modern">
                        <div class="metric-icon-wrapper blue">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="metric-info">
                            <div class="metric-label">Current Applications</div>
                            <div class="metric-value-large">{{ number_format($keyMetrics['total_pending'] ?? 0) }}</div>
                            <div class="metric-trend positive">
                                <i class="fas fa-arrow-up"></i> +5.67% from last month
                            </div>
                        </div>
                    </div>

                    <!-- Approval Status Card -->
                    <div class="metric-card-modern">
                        <div class="metric-icon-wrapper purple">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="metric-info">
                            <div class="metric-label">Approval Status</div>
                            @php
                                $approvedCount = collect($applicationStatus ?? [])->sum('approved');
                            @endphp
                            <div class="metric-value-large">{{ number_format($approvedCount) }}</div>
                            <div class="metric-trend positive">
                                <i class="fas fa-arrow-up"></i> +150% from last month
                            </div>
                        </div>
                    </div>

                    <!-- Upcoming Deadlines Card -->
                    <div class="metric-card-modern">
                        <div class="metric-icon-wrapper orange">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="metric-info">
                            <div class="metric-label">Pending Reviews</div>
                            <div class="metric-value-large">{{ number_format($keyMetrics['total_pending'] ?? 0) }}</div>
                            <div class="metric-description">{{ collect($recentActivity ?? [])->count() }} applications need
                                attention</div>
                        </div>
                    </div>
                </div>

                <!-- User Registration Chart -->
                <div class="chart-card-farmvista">
                    <div class="chart-header-modern">
                        <div class="chart-title-area">
                            <h3 class="chart-title">User Registration</h3>
                            <div class="chart-legend-inline">
                                <span class="legend-dot green"></span> New Users
                                <span class="legend-dot blue"></span> Present
                            </div>
                        </div>
                        <div class="chart-filters" style="display: flex; align-items: center; gap: 8px;">
                            <label style="font-size: 12px; color: #666; margin: 0;">From:</label>
                            <input type="month" class="time-filter-input" id="startMonth"
                                value="{{ $startMonth ?? now()->subMonths(5)->format('Y-m') }}"
                                style="padding: 4px 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 12px;">
                            <label style="font-size: 12px; color: #666; margin: 0;">To:</label>
                            <input type="month" class="time-filter-input" id="endMonth"
                                value="{{ $endMonth ?? now()->format('Y-m') }}"
                                style="padding: 4px 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 12px;">
                            <button onclick="updateUserRegistrationChart()"
                                style="padding: 4px 12px; background: #28a745; color: white; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">Apply</button>
                        </div>
                    </div>
                    <div class="chart-container-area">
                        <canvas id="userRegistrationChart"></canvas>
                    </div>
                </div>

                <!-- Service Overview Cards -->
                <div class="service-overview-grid">
                    @php
                        $serviceImages = [
                            'rsbsa' => 'ServicesRSBSATemporary.jpg',
                            'seedling' => 'ServicesSeedlingsTemporary.jpg',
                            'fishr' => 'ServicesFishrTemporary.jpg',
                            'boatr' => 'ServicesBoatrTemporary.jpg',
                            'training' => 'ServicesTrainingTemporary.jpg',
                        ];
                    @endphp
                    @foreach ($applicationStatus ?? [] as $key => $service)
                        <div class="service-card">
                            <div class="service-image-wrapper">
                                <img src="{{ asset('images/services/' . ($serviceImages[$key] ?? 'default.jpg')) }}"
                                    alt="{{ $service['name'] }}" class="service-image">
                                <div class="service-image-overlay"></div>
                            </div>
                            <div class="service-content">
                                <h4 class="service-name">{{ $service['name'] }}</h4>
                                <div class="service-stats">
                                    <div class="stat-item">
                                        <div class="stat-value warning">{{ $service['pending'] }}</div>
                                        <div class="stat-label">Pending</div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-value success">{{ $service['approved'] }}</div>
                                        <div class="stat-label">Approved</div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-value danger">{{ $service['rejected'] }}</div>
                                        <div class="stat-label">Rejected</div>
                                    </div>
                                </div>
                                <a href="{{ route($service['route']) }}" class="service-action-link">
                                    View Details <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach

                    <!-- Supply Management Card -->
                    <div class="service-card">
                        <div class="service-image-wrapper">
                            <img src="{{ asset('images/services/SupplyManagement.png') }}" alt="Supply Management"
                                class="service-image">
                            <div class="service-image-overlay"></div>
                        </div>
                        <div class="service-content">
                            <h4 class="service-name">Supply Management</h4>
                            <div class="service-stats">
                                <div class="stat-item">
                                    <div class="stat-value warning">{{ $supplyAlerts['total_low_stock_count'] ?? 0 }}</div>
                                    <div class="stat-label">Low Supply</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value success">{{ $supplyAlerts['total_items_count'] ?? 0 }}</div>
                                    <div class="stat-label">Total Items</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value danger">{{ $supplyAlerts['total_out_of_stock_count'] ?? 0 }}
                                    </div>
                                    <div class="stat-label">Out of Supply</div>
                                </div>
                            </div>
                            <a href="{{ route('admin.seedlings.supply-management.index') }}" class="service-action-link">
                                View Details <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Task Management -->
                <div class="task-card-farmvista">
                    <div class="card-header-with-action">
                        <h3 class="card-title-compact">Task Management</h3>
                        <div class="header-actions-compact">
                            <button class="icon-btn-compact"
                                onclick="window.location.href='{{ route('admin.seedlings.supply-management.index') }}'">
                                <i class="fas fa-plus"></i>
                            </button>
                            <a href="{{ route('admin.rsbsa.applications') }}" class="view-all-link-compact">
                                View All <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="task-list">
                        <div class="task-table-header">
                            <div class="task-col-name">Task Name</div>
                            <div class="task-col-assigned">Assigned To</div>
                            <div class="task-col-due">Due Date</div>
                            <div class="task-col-status">Status</div>
                        </div>
                        @if (isset($recentActivity) && count($recentActivity) > 0)
                            @foreach ($recentActivity->take(5) as $activity)
                                <div class="task-row">
                                    <div class="task-col-name">
                                        <span class="task-name-text">{{ $activity['type'] ?? 'Application' }}
                                            Review</span>
                                    </div>
                                    <div class="task-col-assigned">
                                        <span class="task-assigned-text">{{ $activity['name'] ?? 'N/A' }}</span>
                                    </div>
                                    <div class="task-col-due">
                                        <span
                                            class="task-date-text">{{ $activity['created_at']->format('M d, Y') }}</span>
                                    </div>
                                    <div class="task-col-status">
                                        <span class="task-status-badge {{ $activity['status_color'] ?? 'warning' }}">
                                            {{ $activity['action'] ?? 'Pending' }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="task-empty-state">
                                <i class="fas fa-tasks"></i>
                                <p>No pending tasks</p>
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            <!-- Right Column - Sidebar -->
            <div class="dashboard-right-section">

                <!-- Weather Widget - San Pedro, Laguna -->
                <div class="weather-card-farmvista" id="weatherCard">
                    <div class="card-header-compact">
                        <h3 class="card-title-compact">
                            <i class="fas fa-map-marker-alt"></i> San Pedro, Laguna
                        </h3>
                        <span class="weather-time" id="weatherTime">--:--</span>
                    </div>
                    <div class="weather-main-display">
                        <div class="weather-icon-large" id="weatherIcon">
                            <i class="fas fa-spinner fa-spin"></i>
                        </div>
                        <div class="weather-temp-large">
                            <span id="weatherTemp">--</span>°C
                        </div>
                        <div class="weather-condition" id="weatherCondition">Loading weather...</div>
                    </div>
                    <div class="weather-details-grid">
                        <div class="weather-detail-item">
                            <i class="fas fa-tint"></i>
                            <div>
                                <div class="detail-label">Humidity</div>
                                <div class="detail-value" id="humidity">--%</div>
                            </div>
                        </div>
                        <div class="weather-detail-item">
                            <i class="fas fa-cloud-rain"></i>
                            <div>
                                <div class="detail-label">Rain Chance</div>
                                <div class="detail-value" id="rainChance">--%</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Service Summary Donut Chart -->
                <div class="summary-card-farmvista">
                    <div class="card-header-compact">
                        <h3 class="card-title-compact">Services Summary</h3>
                    </div>
                    <div class="donut-chart-wrapper">
                        <canvas id="serviceSummaryChart"></canvas>
                    </div>
                    <div class="chart-legend-list">
                        @php
                            $serviceColors = ['#4CAF50', '#FFC107', '#2196F3', '#E91E63', '#9C27B0'];
                            $services = $applicationStatus ?? [];
                            $totalRequests = collect($services)->sum(function ($s) {
                                return $s['pending'] + $s['approved'];
                            });

                            // If no data, use dummy data
                            if (empty($services) || $totalRequests === 0) {
                                $services = [
                                    ['name' => 'RSBSA', 'pending' => 15, 'approved' => 30],
                                    ['name' => 'Seedling', 'pending' => 10, 'approved' => 22],
                                    ['name' => 'FishR', 'pending' => 8, 'approved' => 20],
                                    ['name' => 'BoatR', 'pending' => 5, 'approved' => 10],
                                    ['name' => 'Training', 'pending' => 7, 'approved' => 13],
                                ];
                                $totalRequests = collect($services)->sum(function ($s) {
                                    return $s['pending'] + $s['approved'];
                                });
                            }

                            $index = 0;
                        @endphp
                        @foreach ($services as $service)
                            @php
                                $count = $service['pending'] + $service['approved'];
                                $percentage = $totalRequests > 0 ? round(($count / $totalRequests) * 100, 1) : 0;
                            @endphp
                            <div class="legend-item-row">
                                <span class="legend-color-box"
                                    style="background: {{ $serviceColors[$index % 5] }}"></span>
                                <span class="legend-text">{{ $service['name'] }}</span>
                                <span class="legend-badge">{{ $count }}</span>
                                <span class="legend-percentage">{{ $percentage }}%</span>
                            </div>
                            @php $index++; @endphp
                        @endforeach
                    </div>
                </div>

                <!-- Geographic Distribution Chart -->
                <div class="summary-card-farmvista">
                    <div class="card-header-compact">
                        <h3 class="card-title-compact">Top 5 Barangays</h3>
                        <select class="time-filter-compact" id="geoDistributionFilter">
                            <option value="users">Users</option>
                            <option value="applications">Applications</option>
                        </select>
                    </div>
                    <div class="geo-chart-wrapper">
                        <canvas id="geographicDistributionChart"></canvas>
                    </div>
                </div>

                <!-- Recent Activities Card -->
                <div class="summary-card-farmvista">
                    <div class="card-header-compact">
                        <h3 class="card-title-compact">Recent Activities</h3>
                    </div>
                    <div class="activities-list">
                        @if (isset($recentActivity) && count($recentActivity) > 0)
                            @foreach ($recentActivity->take(5) as $activity)
                                <div class="activity-item">
                                    <div class="activity-icon {{ $activity['status_color'] ?? 'warning' }}">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="activity-details">
                                        <div class="activity-title">{{ $activity['type'] ?? 'Application' }}</div>
                                        <div class="activity-name">{{ $activity['name'] ?? 'N/A' }}</div>
                                        <div class="activity-time">{{ $activity['created_at']->diffForHumans() }}</div>
                                    </div>
                                    <div class="activity-badge {{ $activity['status_color'] ?? 'warning' }}">
                                        {{ $activity['action'] ?? 'Pending' }}
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="activities-empty-state">
                                <i class="fas fa-inbox"></i>
                                <p>No recent activities</p>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!-- Load Chart.js before our scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <!-- Chart Scripts -->
    <script>
        // Global function to update User Registration Chart based on date range
        function updateUserRegistrationChart() {
            const startMonth = document.getElementById('startMonth').value;
            const endMonth = document.getElementById('endMonth').value;

            if (!startMonth || !endMonth) {
                alert('Please select both start and end dates');
                return;
            }

            if (startMonth > endMonth) {
                alert('Start date must be before end date');
                return;
            }

            console.log('Updating chart with range:', startMonth, 'to', endMonth);
            const url = new URL(window.location.href);
            url.searchParams.set('start_month', startMonth);
            url.searchParams.set('end_month', endMonth);
            window.location.href = url.toString();
        }

        // Check if Chart.js is loaded
        if (typeof Chart === 'undefined') {
            console.error('Chart.js library failed to load!');
        } else {
            console.log('Chart.js version:', Chart.version);
        }

        document.addEventListener('DOMContentLoaded', function() {
            console.log('Dashboard charts initializing...');

            // User Registration Bar Chart
            const userRegistrationCtx = document.getElementById('userRegistrationChart');
            if (userRegistrationCtx) {
                try {
                    console.log('Creating User Registration Chart...');
                    @php
                        $registrationLabels = collect($userRegistrationData ?? [])->pluck('month');
                        $registrationCounts = collect($userRegistrationData ?? [])->pluck('count');

                        // If no data, use dummy data for demonstration
                        if ($registrationLabels->isEmpty()) {
                            $currentDate = \Carbon\Carbon::now();
                            $registrationLabels = collect();
                            $registrationCounts = collect();

                            for ($i = 5; $i >= 0; $i--) {
                                $date = $currentDate->copy()->subMonths($i);
                                $registrationLabels->push($date->format('M Y'));
                                $registrationCounts->push(rand(45, 95)); // Dummy data
                            }
                        }
                    @endphp

                    // Debug: Check what data we received from backend
                    console.log('Raw userRegistrationData from backend:', @json($userRegistrationData ?? []));

                    const registrationLabels = {!! json_encode($registrationLabels) !!};
                    const registrationCounts = {!! json_encode($registrationCounts) !!};
                    console.log('Registration labels:', registrationLabels);
                    console.log('Registration counts:', registrationCounts);

                    const userRegistrationChart = new Chart(userRegistrationCtx, {
                        type: 'bar',
                        data: {
                            labels: registrationLabels,
                            datasets: [{
                                label: 'New Users',
                                data: registrationCounts,
                                backgroundColor: '#4CAF50',
                                borderRadius: 8,
                                barThickness: 40
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    backgroundColor: '#ffffff',
                                    titleColor: '#000000',
                                    bodyColor: '#666666',
                                    borderColor: '#e0e0e0',
                                    borderWidth: 1,
                                    padding: 10,
                                    displayColors: false,
                                    callbacks: {
                                        label: function(context) {
                                            return context.parsed.y + ' users';
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: '#f5f5f5'
                                    },
                                    ticks: {
                                        callback: function(value) {
                                            return value + ' users';
                                        },
                                        precision: 0
                                    },
                                    suggestedMax: Math.max(10, Math.max(...registrationCounts) + 5)
                                },
                                x: {
                                    grid: {
                                        display: false
                                    }
                                }
                            }
                        }
                    });
                    console.log('User Registration Chart created successfully');
                } catch (error) {
                    console.error('Error creating User Registration Chart:', error);
                }
            } else {
                console.error('User Registration Chart canvas not found');
            }

            // Service Summary Donut Chart
            const serviceSummaryCtx = document.getElementById('serviceSummaryChart');
            if (serviceSummaryCtx) {
                try {
                    @php
                        $serviceData = collect($applicationStatus ?? [])
                            ->map(function ($service) {
                                return $service['pending'] + $service['approved'];
                            })
                            ->values();

                        $serviceLabels = collect($applicationStatus ?? [])
                            ->pluck('name')
                            ->values();
                        $totalServiceRequests = $serviceData->sum();

                        // If no data, use dummy data
                        if ($serviceLabels->isEmpty() || $totalServiceRequests === 0) {
                            $serviceLabels = collect(['RSBSA', 'Seedling', 'FishR', 'BoatR', 'Training']);
                            $serviceData = collect([45, 32, 28, 15, 20]);
                            $totalServiceRequests = $serviceData->sum();
                        }
                    @endphp
                    console.log('Creating Service Summary Chart...');

                    const serviceLabels = {!! json_encode($serviceLabels) !!};
                    const serviceData = {!! json_encode($serviceData) !!};
                    const totalServiceRequests = {{ $totalServiceRequests }};

                    console.log('Service labels:', serviceLabels);
                    console.log('Service data:', serviceData);
                    console.log('Total requests:', totalServiceRequests);

                    const serviceSummaryChart = new Chart(serviceSummaryCtx, {
                        type: 'doughnut',
                        data: {
                            labels: serviceLabels,
                            datasets: [{
                                data: serviceData,
                                backgroundColor: ['#4CAF50', '#FFC107', '#2196F3', '#E91E63',
                                    '#9C27B0'
                                ],
                                borderWidth: 3,
                                borderColor: '#ffffff',
                                cutout: '65%',
                                spacing: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                    padding: 12,
                                    cornerRadius: 8,
                                    titleFont: {
                                        size: 14,
                                        weight: 'bold'
                                    },
                                    bodyFont: {
                                        size: 13
                                    },
                                    callbacks: {
                                        label: function(context) {
                                            const label = context.label || '';
                                            const value = context.parsed;
                                            const total = context.dataset.data.reduce((a, b) => a + b,
                                                0);
                                            const percentage = ((value / total) * 100).toFixed(1);
                                            return `${label}: ${value} (${percentage}%)`;
                                        }
                                    }
                                },
                                datalabels: false
                            },
                            animation: {
                                animateRotate: true,
                                duration: 1000
                            }
                        },
                        plugins: [{
                            id: 'centerText',
                            beforeDraw: function(chart) {
                                const ctx = chart.ctx;
                                const chartArea = chart.chartArea;
                                const centerX = (chartArea.left + chartArea.right) / 2;
                                const centerY = (chartArea.top + chartArea.bottom) / 2;

                                const total = chart.data.datasets[0].data.reduce((a, b) => a +
                                    b, 0);

                                // Draw center text
                                ctx.save();
                                ctx.font = 'bold 24px Inter';
                                ctx.fillStyle = '#1f2937';
                                ctx.textAlign = 'center';
                                ctx.textBaseline = 'middle';
                                ctx.fillText(total.toLocaleString(), centerX, centerY - 10);

                                ctx.font = '14px Inter';
                                ctx.fillStyle = '#64748b';
                                ctx.fillText('Total Requests', centerX, centerY + 15);
                                ctx.restore();
                            },
                            afterDraw: function(chart) {
                                const ctx = chart.ctx;
                                const meta = chart.getDatasetMeta(0);
                                const total = chart.data.datasets[0].data.reduce((a, b) => a +
                                    b, 0);

                                ctx.save();
                                ctx.font = 'bold 14px Inter, sans-serif';
                                ctx.textAlign = 'center';
                                ctx.textBaseline = 'middle';

                                chart.data.datasets[0].data.forEach((value, index) => {
                                    if (value > 0) {
                                        const percentage = ((value / total) * 100)
                                            .toFixed(1);

                                        // Only show percentage if slice is large enough
                                        if (percentage > 5) {
                                            const element = meta.data[index];

                                            // Calculate the middle angle of the segment
                                            const startAngle = element.startAngle;
                                            const endAngle = element.endAngle;
                                            const midAngle = (startAngle + endAngle) /
                                                2;

                                            // Calculate position based on the segment's center point
                                            const chartArea = chart.chartArea;
                                            const centerX = (chartArea.left + chartArea
                                                .right) / 2;
                                            const centerY = (chartArea.top + chartArea
                                                .bottom) / 2;

                                            // Position the text at 70% of the radius from center
                                            const radius = (element.outerRadius -
                                                    element.innerRadius) * 0.7 + element
                                                .innerRadius;
                                            const x = centerX + Math.cos(midAngle) *
                                                radius;
                                            const y = centerY + Math.sin(midAngle) *
                                                radius;

                                            const text = `${percentage}%`;

                                            ctx.fillStyle = '#ffffff';
                                            ctx.strokeStyle = '#000000';
                                            ctx.lineWidth = 3;
                                            ctx.textAlign = 'center';
                                            ctx.textBaseline = 'middle';
                                            ctx.strokeText(text, x, y);
                                            ctx.fillText(text, x, y);
                                        }
                                    }
                                });

                                ctx.restore();
                            }
                        }]
                    });
                    console.log('Service Summary Chart created successfully');
                } catch (error) {
                    console.error('Error creating Service Summary Chart:', error);
                }
            } else {
                console.error('Service Summary Chart canvas not found');
            }

            // Geographic Distribution Bar Chart
            const geoDistributionCtx = document.getElementById('geographicDistributionChart');
            if (geoDistributionCtx) {
                try {
                    @php
                        $usersData = collect($geographicDistribution['users'] ?? []);
                        $applicationsData = collect($geographicDistribution['applications'] ?? []);
                        $userLabels = $usersData->pluck('barangay')->toArray();
                        $userCounts = $usersData->pluck('count')->toArray();
                        $appLabels = $applicationsData->pluck('barangay')->toArray();
                        $appCounts = $applicationsData->pluck('count')->toArray();
                    @endphp

                    console.log('Creating Geographic Distribution Chart...');
                    const geoData = {
                        users: {
                            labels: {!! json_encode($userLabels) !!},
                            data: {!! json_encode($userCounts) !!}
                        },
                        applications: {
                            labels: {!! json_encode($appLabels) !!},
                            data: {!! json_encode($appCounts) !!}
                        }
                    };

                    console.log('Geographic data:', geoData);
                    let currentGeoView = 'users';
                    const geoChart = new Chart(geoDistributionCtx, {
                        type: 'bar',
                        data: {
                            labels: geoData.users.labels,
                            datasets: [{
                                label: 'Registered Users',
                                data: geoData.users.data,
                                backgroundColor: '#4CAF50',
                                borderRadius: 6,
                                barThickness: 25
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            indexAxis: 'y',
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    backgroundColor: '#ffffff',
                                    titleColor: '#000000',
                                    bodyColor: '#666666',
                                    borderColor: '#e0e0e0',
                                    borderWidth: 1,
                                    padding: 8,
                                    displayColors: false,
                                    callbacks: {
                                        label: function(context) {
                                            return context.parsed.x + (currentGeoView === 'users' ?
                                                ' users' : ' applications');
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    grid: {
                                        color: '#f5f5f5'
                                    },
                                    ticks: {
                                        stepSize: 1,
                                        font: {
                                            size: 10
                                        }
                                    }
                                },
                                y: {
                                    grid: {
                                        display: false
                                    },
                                    ticks: {
                                        font: {
                                            size: 11
                                        }
                                    }
                                }
                            }
                        }
                    });

                    console.log('Geographic Distribution Chart created successfully');

                    // Filter change handler
                    const geoFilter = document.getElementById('geoDistributionFilter');
                    if (geoFilter) {
                        geoFilter.addEventListener('change', function(e) {
                            currentGeoView = e.target.value;
                            const isUsers = currentGeoView === 'users';

                            geoChart.data.labels = geoData[currentGeoView].labels;
                            geoChart.data.datasets[0].data = geoData[currentGeoView].data;
                            geoChart.data.datasets[0].label = isUsers ? 'Registered Users' :
                                'Total Applications';
                            geoChart.data.datasets[0].backgroundColor = isUsers ? '#4CAF50' : '#2196F3';
                            geoChart.update();
                        });
                    }
                } catch (error) {
                    console.error('Error creating Geographic Distribution Chart:', error);
                }
            } else {
                console.error('Geographic Distribution Chart canvas not found');
            }

            console.log('All dashboard charts initialized');
        });

        // Weather Widget - Separate from DOMContentLoaded to ensure it runs
        (function() {
            // Weather condition mapping
            const weatherConditions = {
                0: {
                    description: 'Clear Sky',
                    icon: 'fas fa-sun'
                },
                1: {
                    description: 'Mainly Clear',
                    icon: 'fas fa-sun'
                },
                2: {
                    description: 'Partly Cloudy',
                    icon: 'fas fa-cloud-sun'
                },
                3: {
                    description: 'Overcast',
                    icon: 'fas fa-cloud'
                },
                45: {
                    description: 'Foggy',
                    icon: 'fas fa-smog'
                },
                48: {
                    description: 'Foggy',
                    icon: 'fas fa-smog'
                },
                51: {
                    description: 'Light Drizzle',
                    icon: 'fas fa-cloud-rain'
                },
                53: {
                    description: 'Drizzle',
                    icon: 'fas fa-cloud-rain'
                },
                55: {
                    description: 'Heavy Drizzle',
                    icon: 'fas fa-cloud-showers-heavy'
                },
                61: {
                    description: 'Light Rain',
                    icon: 'fas fa-cloud-rain'
                },
                63: {
                    description: 'Rain',
                    icon: 'fas fa-cloud-showers-heavy'
                },
                65: {
                    description: 'Heavy Rain',
                    icon: 'fas fa-cloud-showers-heavy'
                },
                71: {
                    description: 'Light Snow',
                    icon: 'fas fa-snowflake'
                },
                73: {
                    description: 'Snow',
                    icon: 'fas fa-snowflake'
                },
                75: {
                    description: 'Heavy Snow',
                    icon: 'fas fa-snowflake'
                },
                77: {
                    description: 'Snow Grains',
                    icon: 'fas fa-snowflake'
                },
                80: {
                    description: 'Light Showers',
                    icon: 'fas fa-cloud-rain'
                },
                81: {
                    description: 'Showers',
                    icon: 'fas fa-cloud-showers-heavy'
                },
                82: {
                    description: 'Heavy Showers',
                    icon: 'fas fa-cloud-showers-heavy'
                },
                85: {
                    description: 'Light Snow Showers',
                    icon: 'fas fa-snowflake'
                },
                86: {
                    description: 'Snow Showers',
                    icon: 'fas fa-snowflake'
                },
                95: {
                    description: 'Thunderstorm',
                    icon: 'fas fa-bolt'
                },
                96: {
                    description: 'Thunderstorm with Hail',
                    icon: 'fas fa-bolt'
                },
                99: {
                    description: 'Thunderstorm with Hail',
                    icon: 'fas fa-bolt'
                }
            };

            function updateWeather() {
                fetch(
                        'https://api.open-meteo.com/v1/forecast?latitude=14.3583&longitude=121.0161&current_weather=true&hourly=relativehumidity_2m,precipitation_probability&timezone=Asia/Manila'
                    )
                    .then(response => {
                        if (!response.ok) throw new Error('Weather API failed');
                        return response.json();
                    })
                    .then(data => {
                        if (!data.current_weather) return;

                        const w = data.current_weather;
                        const condition = weatherConditions[w.weathercode] || {
                            description: 'Unknown',
                            icon: 'fas fa-cloud'
                        };

                        // Get current hour index for hourly data
                        const now = new Date();
                        const currentHourIndex = now.getHours();

                        // Update all elements
                        const tempEl = document.getElementById('weatherTemp');
                        const iconEl = document.getElementById('weatherIcon');
                        const condEl = document.getElementById('weatherCondition');
                        const humidityEl = document.getElementById('humidity');
                        const rainEl = document.getElementById('rainChance');
                        const timeEl = document.getElementById('weatherTime');

                        if (tempEl) tempEl.textContent = Math.round(w.temperature);
                        if (iconEl) iconEl.innerHTML = '<i class="' + condition.icon + '"></i>';
                        if (condEl) condEl.textContent = condition.description;

                        // Update humidity from hourly data
                        if (humidityEl && data.hourly && data.hourly.relativehumidity_2m) {
                            const humidity = data.hourly.relativehumidity_2m[currentHourIndex] || 0;
                            humidityEl.textContent = Math.round(humidity) + '%';
                        }

                        // Update rain chance from hourly data
                        if (rainEl && data.hourly && data.hourly.precipitation_probability) {
                            const rainChance = data.hourly.precipitation_probability[currentHourIndex] || 0;
                            rainEl.textContent = Math.round(rainChance) + '%';
                        }

                        if (timeEl) {
                            const time = new Date(w.time);
                            timeEl.textContent = time.toLocaleTimeString('en-US', {
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: true
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Weather error:', error);
                        const condEl = document.getElementById('weatherCondition');
                        if (condEl) condEl.textContent = 'Weather unavailable';
                    });
            }

            // Wait for DOM and then update
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', updateWeather);
            } else {
                updateWeather();
            }

            // Refresh every 10 minutes
            setInterval(updateWeather, 600000);
        })();

        // Dynamic greeting based on time of day
        function updateGreeting() {
            const hour = new Date().getHours();
            const greetingEl = document.getElementById('greeting');
            
            if (hour >= 5 && hour < 12) {
                greetingEl.textContent = 'Good Morning!';
            } else if (hour >= 12 && hour < 17) {
                greetingEl.textContent = 'Good Afternoon!';
            } else if (hour >= 17 && hour < 21) {
                greetingEl.textContent = 'Good Evening!';
            } else {
                greetingEl.textContent = 'Hello!';
            }
        }

        // Call on page load
        updateGreeting();

        // Optional: Update greeting every minute
        setInterval(updateGreeting, 60000);
    </script>

    <style>
        /* Modern FarmVista Dashboard Styles */
        .farmvista-dashboard {
            padding: 2rem;
            background: #f5f7fa;
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        /* Adjust padding when sidebar is collapsed for better use of space */
        html.sidebar-collapsed-state .farmvista-dashboard {
            padding: 2rem 3rem;
        }

        /* Welcome Header */
        .dashboard-welcome-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .welcome-title {
            font-size: 2rem;
            font-weight: 700;
            color: #1a1a1a;
            margin: 0;
        }

        .welcome-subtitle {
            font-size: 0.95rem;
            color: #666;
            margin: 0.5rem 0 0 0;
        }

        .header-right-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .weather-mini {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            font-size: 0.9rem;
            color: #333;
        }

        .weather-mini i {
            color: #FFC107;
            font-size: 1.2rem;
        }

        .export-btn-modern {
            padding: 0.75rem 1.5rem;
            background: #22c55e;
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s;
        }

        .export-btn-modern:hover {
            background: #16a34a;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
        }

        /* Main Grid Layout */
        .dashboard-main-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 380px;
            gap: 2rem;
            transition: grid-template-columns 0.3s ease, gap 0.3s ease;
            max-width: 100%;
            overflow: hidden;
        }

        /* Ensure proper layout in both states */
        .dashboard-left-section {
            min-width: 0;
            overflow: hidden;
        }

        .dashboard-right-section {
            width: 380px;
            flex-shrink: 0;
        }

        /* Metrics Row */
        .metrics-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .metric-card-modern {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
        }

        .metric-card-modern:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .metric-icon-wrapper {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            flex-shrink: 0;
        }

        .metric-icon-wrapper.green {
            background: linear-gradient(135deg, #4CAF50, #66BB6A);
        }

        .metric-icon-wrapper.blue {
            background: linear-gradient(135deg, #2196F3, #42A5F5);
        }

        .metric-icon-wrapper.purple {
            background: linear-gradient(135deg, #9C27B0, #BA68C8);
        }

        .metric-icon-wrapper.orange {
            background: linear-gradient(135deg, #FF9800, #FFA726);
        }

        .metric-info {
            flex: 1;
        }

        .metric-label {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 0.5rem;
        }

        .metric-value-large {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 0.25rem;
        }

        .metric-trend {
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .metric-trend.positive {
            color: #4CAF50;
        }

        .metric-trend.negative {
            color: #f44336;
        }

        .metric-description {
            font-size: 0.8rem;
            color: #999;
            margin-top: 0.25rem;
        }

        /* Chart Card */
        .chart-card-farmvista {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }

        .chart-header-modern {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .chart-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1a1a1a;
            margin: 0;
        }

        .chart-legend-inline {
            display: flex;
            align-items: center;
            gap: 1rem;
            font-size: 0.85rem;
            color: #666;
        }

        .legend-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 0.25rem;
        }

        .legend-dot.green {
            background: #4CAF50;
        }

        .legend-dot.yellow {
            background: #FFC107;
        }

        .chart-filters {
            display: flex;
            gap: 0.5rem;
        }

        .time-filter-select {
            padding: 0.5rem 1rem;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 0.85rem;
            cursor: pointer;
            background: white;
        }

        .chart-container-area {
            height: 300px;
            position: relative;
        }

        /* Service Overview Grid */
        .service-overview-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .service-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
        }

        .service-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .service-image-wrapper {
            position: relative;
            width: 100%;
            height: 180px;
            overflow: hidden;
        }

        .service-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .service-card:hover .service-image {
            transform: scale(1.05);
        }

        .service-image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.4));
        }

        .service-icon-badge-overlay {
            position: absolute;
            top: 1rem;
            right: 1rem;
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .service-content {
            padding: 1.5rem;
        }

        .service-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .service-icon-badge {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: white;
        }

        .service-icon-badge.rsbsa,
        .service-icon-badge-overlay.rsbsa {
            background: linear-gradient(135deg, #4CAF50, #66BB6A);
        }

        .service-icon-badge.seedling,
        .service-icon-badge-overlay.seedling {
            background: linear-gradient(135deg, #FFC107, #FFD54F);
        }

        .service-icon-badge.fishr,
        .service-icon-badge-overlay.fishr {
            background: linear-gradient(135deg, #2196F3, #42A5F5);
        }

        .service-icon-badge.boatr,
        .service-icon-badge-overlay.boatr {
            background: linear-gradient(135deg, #E91E63, #F06292);
        }

        .service-icon-badge.training,
        .service-icon-badge-overlay.training {
            background: linear-gradient(135deg, #9C27B0, #BA68C8);
        }

        .service-icon-badge.supply,
        .service-icon-badge-overlay.supply {
            background: linear-gradient(135deg, #FF9800, #FFB74D);
        }

        .service-name {
            font-size: 1.2rem;
            font-weight: 700;
            color: #4CAF50;
            margin: 0 0 1rem 0;
        }

.service-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.5rem;
            margin-bottom: 1rem;
            width: 100%;
        }

        .stat-item {
            text-align: center;
            padding: 0;
            min-width: 0;
        }

        .stat-value {
            font-size: 1.2rem;
            font-weight: 700;
            color: #333;
            display: block;
            white-space: nowrap;
        }

        .stat-value.success {
            color: #4CAF50;
        }

        .stat-value.warning {
            color: #FFC107;
        }

        .stat-value.danger {
            color: #f44336;
        }

        .stat-label {
            font-size: 0.6rem;
            color: #999;
            margin-top: 0.25rem;
            white-space: nowrap;
            display: block;
        }

        .service-action-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.85rem;
            background: #4CAF50;
            border-radius: 8px;
            color: white;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.3s;
            margin-top: 1rem;
        }

        .service-action-link:hover {
            background: #45a049;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(76, 175, 80, 0.3);
        }

        /* Right Sidebar Cards */
        .dashboard-right-section {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .summary-card-farmvista,
        .metrics-card-farmvista,
        .weather-card-farmvista,
        .task-card-farmvista {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        /* Weather Card Styles */
        .weather-card-farmvista {
            background: linear-gradient(135deg, #4CAF50 0%, #66BB6A 100%);
            color: white;
        }

        .weather-card-farmvista .card-header-compact {
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
        }

        .weather-card-farmvista .card-title-compact {
            color: white;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .weather-time {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.8);
        }

        .weather-main-display {
            text-align: center;
            padding: 1rem 0;
        }

        .weather-icon-large {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: rgba(255, 255, 255, 0.9);
        }

        .weather-temp-large {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .weather-condition {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 1.5rem;
        }

        .weather-details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }

        .weather-detail-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .weather-detail-item i {
            font-size: 1.5rem;
            color: rgba(255, 255, 255, 0.8);
        }

        .detail-label {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 0.25rem;
        }

        .detail-value {
            font-size: 1rem;
            font-weight: 600;
        }

        .card-header-compact {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .card-title-compact {
            font-size: 1rem;
            font-weight: 600;
            color: #1a1a1a;
            margin: 0;
        }

        .time-filter-compact {
            padding: 0.4rem 0.75rem;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            font-size: 0.8rem;
            cursor: pointer;
            background: white;
        }

        .donut-chart-wrapper {
            height: 200px;
            position: relative;
            margin-bottom: 1rem;
        }

        .geo-chart-wrapper {
            height: 250px;
            position: relative;
            margin-bottom: 0.5rem;
        }

        /* Recent Activities */
        .activities-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .activity-item {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 0.75rem;
            background: #f9f9f9;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .activity-item:hover {
            background: #f0f0f0;
        }

        .activity-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            color: white;
            flex-shrink: 0;
        }

        .activity-icon.warning {
            background: #FFC107;
        }

        .activity-icon.success {
            background: #4CAF50;
        }

        .activity-icon.danger {
            background: #f44336;
        }

        .activity-details {
            flex: 1;
        }

        .activity-title {
            font-size: 0.85rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.25rem;
        }

        .activity-name {
            font-size: 0.8rem;
            color: #666;
            margin-bottom: 0.25rem;
        }

        .activity-time {
            font-size: 0.75rem;
            color: #999;
        }

        .activity-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 500;
            flex-shrink: 0;
        }

        .activity-badge.warning {
            background: #FFF3E0;
            color: #F57C00;
        }

        .activity-badge.success {
            background: #E8F5E9;
            color: #2E7D32;
        }

        .activity-badge.danger {
            background: #FFEBEE;
            color: #C62828;
        }

        .activities-empty-state {
            padding: 2rem 1rem;
            text-align: center;
            color: #999;
        }

        .activities-empty-state i {
            font-size: 2.5rem;
            margin-bottom: 0.75rem;
            opacity: 0.3;
        }

        .activities-empty-state p {
            margin: 0;
            font-size: 0.85rem;
        }

        .chart-legend-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .legend-item-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
        }

        .legend-color-box {
            width: 12px;
            height: 12px;
            border-radius: 3px;
            flex-shrink: 0;
        }

        .legend-text {
            flex: 1;
            color: #666;
        }

        .legend-badge {
            background: #f3f4f6;
            color: #1f2937;
            font-weight: 600;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
            min-width: 32px;
            text-align: center;
        }

        .legend-percentage {
            color: #6b7280;
            font-weight: 500;
            min-width: 45px;
            text-align: right;
        }

        .legend-value {
            font-weight: 600;
            color: #333;
        }

        .card-subtitle-compact {
            font-size: 0.75rem;
            color: #9ca3af;
            margin-top: 0.25rem;
        }

        /* Gauge Chart */
        .gauge-chart-wrapper {
            height: 150px;
            position: relative;
            margin-bottom: 1rem;
        }

        .metrics-info-text {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            padding-top: 1rem;
            border-top: 1px solid #f0f0f0;
        }

        .metric-detail-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.85rem;
        }

        .metric-label-text {
            color: #666;
        }

        .metric-value-text {
            font-weight: 600;
            color: #333;
        }

        /* Task Management */
        .card-header-with-action {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .header-actions-compact {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .icon-btn-compact {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: none;
            background: #4CAF50;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .icon-btn-compact:hover {
            background: #45a049;
            transform: scale(1.1);
        }

        .view-all-link-compact {
            font-size: 0.85rem;
            color: #666;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.25rem;
            transition: all 0.3s;
        }

        .view-all-link-compact:hover {
            color: #4CAF50;
        }

        .task-list {
            display: flex;
            flex-direction: column;
        }

        .task-table-header {
            display: grid;
            grid-template-columns: 2fr 1.5fr 1fr 1fr;
            gap: 0.5rem;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f0f0f0;
            font-size: 0.75rem;
            font-weight: 600;
            color: #999;
            text-transform: uppercase;
        }

        .task-row {
            display: grid;
            grid-template-columns: 2fr 1.5fr 1fr 1fr;
            gap: 0.5rem;
            padding: 1rem 0;
            border-bottom: 1px solid #f5f5f5;
            align-items: center;
            font-size: 0.85rem;
        }

        .task-name-text {
            font-weight: 500;
            color: #333;
        }

        .task-assigned-text,
        .task-date-text {
            color: #666;
        }

        .task-status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
            text-align: center;
            display: inline-block;
        }

        .task-status-badge.warning {
            background: #FFF3E0;
            color: #F57C00;
        }

        .task-status-badge.success {
            background: #E8F5E9;
            color: #2E7D32;
        }

        .task-status-badge.danger {
            background: #FFEBEE;
            color: #C62828;
        }

        .task-empty-state {
            padding: 3rem 1rem;
            text-align: center;
            color: #999;
        }

        .task-empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }

        /* Responsive Design */
        @media (max-width: 1400px) {
            .dashboard-main-grid {
                grid-template-columns: minmax(0, 1fr) 320px;
            }

            .dashboard-right-section {
                width: 320px;
            }
        }

        @media (max-width: 1200px) {
            .dashboard-main-grid {
                grid-template-columns: 1fr;
            }

            .dashboard-right-section {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                gap: 1.5rem;
            }

            .task-card-farmvista {
                margin-top: 0;
            }
        }

        @media (max-width: 768px) {
            .farmvista-dashboard {
                padding: 1rem;
            }

            .welcome-title {
                font-size: 1.5rem;
            }

            .dashboard-welcome-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .metrics-row {
                grid-template-columns: 1fr;
            }

            .service-overview-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .task-table-header,
            .task-row {
                grid-template-columns: 1fr;
                gap: 0.25rem;
            }

            .task-table-header {
                display: none;
            }

            .task-row {
                padding: 1rem;
                background: #f9f9f9;
                border-radius: 8px;
                margin-bottom: 0.5rem;
                border: none;
            }

            .task-col-name::before {
                content: 'Task: ';
                font-weight: 600;
                color: #999;
            }

            .task-col-assigned::before {
                content: 'Assigned: ';
                font-weight: 600;
                color: #999;
            }

            .task-col-due::before {
                content: 'Due: ';
                font-weight: 600;
                color: #999;
            }
        }
    </style>

@endsection
