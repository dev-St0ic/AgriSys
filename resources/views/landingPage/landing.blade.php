<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AgriSys - San Pedro City Agriculture Office</title>
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    <link rel="stylesheet" href="{{ asset('css/seedlings.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fishr.css') }}">
    <link rel="stylesheet" href="{{ asset('css/boatr.css') }}">
    <link rel="stylesheet" href="{{ asset('css/rsbsa.css') }}">
    <link rel="stylesheet" href="{{ asset('css/training.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('css/toast-notifications.css') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&display=swap" rel="stylesheet">

    @if (isset($user))
        <script>
            // Pass user data to JavaScript
            window.userData = @json($user);
            console.log('User data loaded:', window.userData);
        </script>
    @else
        <script>
            // No user logged in
            window.userData = null;
            console.log('No user logged in');
        </script>
    @endif
</head>

<body>
    <!-- Success/Error Messages -->
    @if (session('success'))
        <div class="alert alert-success" style="display: none;">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger" style="display: none;">
            {{ session('error') }}
        </div>
    @endif

    @if (session('warning'))
        <div class="alert alert-warning" style="display: none;">
            {{ session('warning') }}
        </div>
    @endif

    @if (session('info'))
        <div class="alert alert-info" style="display: none;">
            {{ session('info') }}
        </div>
    @endif

    <header>
        <div class="header-left">
            <div class="logo-text">
                <h2 class="logo-text">
                    <span class="logo-agri">Agri</span><span class="logo-sys">Sys</span>
                </h2>
            </div>
            <div class="header-center nav-buttons">
                <button type="button" class="btn" onclick="goHome(event)">Home</button>
                <button type="button" class="btn" onclick="openRSBSAForm(event)">RSBSA</button>
                <button type="button" class="btn" onclick="openFormSeedlings(event)">Seedlings</button>
                <button type="button" class="btn" onclick="openFormFishR(event)">FishR</button>
                <button type="button" class="btn" onclick="openFormBoatR(event)">BoatR</button>
                <button type="button" class="btn" onclick="openFormTraining(event)">Training</button>
            </div>

            <div class="header-right auth-buttons">
                @if (isset($user))
                    <!-- User Profile Dropdown -->
                    <div class="user-profile" id="user-profile" onclick="toggleUserDropdown()">
                        <div class="user-avatar">
                            {{ strtoupper(substr($user['name'] ?? $user['username'], 0, 1)) }}
                        </div>
                        <div class="user-info">
                            <div class="user-name">
                                @php
                                    $fullName = $user['name'] ?? $user['username'];
                                    $firstName = explode(' ', $fullName)[0];
                                @endphp
                                {{ $firstName }}
                            </div>
                            <div class="user-status">
                                @if (isset($user['status']) && strtolower($user['status']) == 'approved')
                                    ✓ Verified
                                @elseif(isset($user['status']) && strtolower($user['status']) == 'pending')
                                    ⏳ Pending
                                @else
                                    {{ ucfirst($user['status'] ?? 'Active') }}
                                @endif
                            </div>
                        </div>
                        <div class="dropdown-arrow">▼</div>

                        <!-- Dropdown Menu -->
                        <div class="user-dropdown" id="user-dropdown">
                            <div class="dropdown-header">
                                <div class="dropdown-user-info">
                                    <div class="dropdown-avatar">
                                        {{ strtoupper(substr($user['name'] ?? $user['username'], 0, 1)) }}
                                    </div>
                                    <div class="dropdown-details">
                                        <div class="dropdown-name">{{ $user['name'] ?? $user['username'] }}</div>
                                        <div class="dropdown-email">{{ $user['email'] }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="dropdown-menu">
                                <a href="#" class="dropdown-item" onclick="showMyApplicationsModal()">
                                    <svg class="dropdown-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    My Applications
                                </a>
                                <a href="#" class="dropdown-item" onclick="showProfileModal()">
                                    <svg class="dropdown-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    View Profile
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="#" class="dropdown-item logout" onclick="logoutUser()">
                                    <svg class="dropdown-icon" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Log Out
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Login and Sign Up Button (for guests) -->
                    <button type="button" class="btn btn-signup" onclick="openAuthModal('signup')">Sign Up</button>
                    <button type="button" class="btn btn-login" onclick="openAuthModal('login')">Log in</button>
                @endif
            </div>
        </div>
    </header>

    <!-- Welcome Section (Same for both guest and logged-in users) -->
    <!-- <section class="welcome" id="home">
        <h2>Welcome to AgriSys</h2>
        <p>The Agricultural Service System of the City Agriculture Office of San Pedro, Laguna</p>
        <button class="btn-services"
            onclick="document.getElementById('services').scrollIntoView({ behavior: 'smooth' })">Explore Services</button>
    </section> -->
    <section class="welcome" id="home">
        <div class="welcome-content">
            <h2>Welcome to <span class="highlight">AgriSys</span><br>Agriculture System</br></h2>
            <p>The Agricultural Service System of the City Agriculture Office of San Pedro, Laguna</p>
            <button class="btn-services"
                onclick="document.getElementById('services').scrollIntoView({ behavior: 'smooth' })">Explore
                Services</button>
        </div>
        <div class="welcome-image"></div>
    </section>

    <!-- Events Section - Updated to load dynamically -->
    <section class="events" id="events">
        <img src="{{ asset('images/logos/cagoFull.png') }}" alt="City Agriculture Office Logo" class="logo-icon">
        <img src="{{ asset('images/logos/CityOfSanPedro.jpg') }}" alt="City of San Pedro Logo" class="logo-icon">
        <h2>City<span class="highlight"> Agriculture Office of San Pedro, Laguna</span></h2>
        <p class="events-subtitle">Ongoing and past events and initiatives of the San Pedro City Agriculture Office
            dedicated to promoting agricultural growth and community development.</p>

        <div class="events-filters">
            <button class="filter-btn active" data-filter="all">View All</button>
            <button class="filter-btn" data-filter="announcement">Announcements</button>
            <button class="filter-btn" data-filter="ongoing">Ongoing Events</button>
            <button class="filter-btn" data-filter="upcoming">Upcoming Events</button>
            <button class="filter-btn" data-filter="past">Past Events</button>
        </div>
            <!-- Events Grid - Will be populated by JavaScript -->
            <div class="events-grid">
                <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">
                    <div class="spinner-border text-primary" role="status">
                        main
                    </div>
                    <p class="text-muted mt-3">Loading events...</p>
                </div>
            </div>
    </section>

    <!-- Services Section (Always visible) -->
    <section class="services" id="services">
        <h2>OUR SERVICES</h2>
        <p class="services-subtitle">We provide comprehensive agricultural and fisheries support services to help you
            grow and succeed</p>

        <div class="services-grid">
            <!-- Row 1: Cards 1, 2, & 3 (Top row with 3 cards) -->
            <div class="row-three">
                <div class="card">
                    <div class="card-image">
                        <img src="{{ asset('images/services/ServicesRSBSATemporary.jpg') }}" alt="RSBSA Service">
                    </div>
                    <h3>RSBSA Registration</h3>
                    <p>Register your details for the Registry System for Basic Sectors in Agriculture (RSBSA).</p>
                    <button class="btn-choice" onclick="openRSBSAForm(event)">Apply Now</button>
                </div>
                <div class="card">
                    <div class="card-image">
                        <img src="{{ asset('images/services/ServicesSeedlingsTemporary.jpg') }}"
                            alt="Seedlings Service">
                    </div>
                    <h3>Seedlings Request</h3>
                    <p>Request free seedlings to support your agricultural livelihood.</p>
                    <button class="btn-choice" onclick="openFormSeedlings(event)">Apply Now</button>
                </div>
                <div class="card">
                    <div class="card-image">
                        <img src="{{ asset('images/services/ServicesFishrTemporary.jpg') }}" alt="Fishr Service">
                    </div>
                    <h3>FishR Registration</h3>
                    <p>Register in the FishR system for fisherfolk support and services.</p>
                    <button class="btn-choice" onclick="openFormFishR(event)">Apply Now</button>
                </div>
            </div>
            <!-- Row 2: Cards 4 & 5 (Bottom row with 2 cards, centered) -->
            <div class="row-two-centered">
                <div class="card">
                    <div class="card-image">
                        <img src="{{ asset('images/services/ServicesBoatrTemporary.jpg') }}" alt="Boatr Service">
                    </div>
                    <h3>BoatR Registration</h3>
                    <p>Apply for registration and assistance for your fishing boats.</p>
                    <button class="btn-choice" onclick="openFormBoatR(event)">Apply Now</button>
                </div>
                <div class="card">
                    <div class="card-image">
                        <img src="{{ asset('images/services/ServicesTrainingTemporary.jpg') }}"
                            alt="Training Service">
                    </div>
                    <h3>Training Request</h3>
                    <p>Apply for agricultural training programs to enhance your farming skills and knowledge.</p>
                    <button class="btn-choice" onclick="openFormTraining(event)">Apply Now</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Include all service forms -->
    @include('landingPage.rsbsa')
    @include('landingPage.seedlings')
    @include('landingPage.fishr')
    @include('landingPage.boatr')
    @include('landingPage.training')

    <section class="how-it-works" id="how-it-works">
        <h2>How It Works</h2>
        <p class="HowItWorks-subtitle">Getting started with our agricultural services is simple and straightforward.
            Follow these three easy steps:</p>
        <div class="steps">
            <div class="step">
                <div class="step-icon"></div>
                <div class="step-number">1</div>
                <h3>1. Fill Out the Form</h3>
                <p>Select a service and complete the required online application form with your details.</p>
            </div>
            <div class="step">
                <div class="step-icon"></div>
                <div class="step-number">2</div>
                <h3>2. Submit Documents</h3>
                <p>Upload any required supporting documents or provide them to the City Agriculture Office.</p>
            </div>
            <div class="step">
                <div class="step-icon"></div>
                <div class="step-number">3</div>
                <h3>3. Receive Approval</h3>
                <p>Once approved, you will be notified and can access the requested agricultural service.</p>
            </div>
        </div>
    </section>

    <section class="help-section">
        <h2>Need Help?</h2>
        <p>If you have questions about your application or urgent agricultural concerns such as crop diseases or natural
            disasters, our support team is here to assist you.</p>
        <div class="help-buttons">
            <button class="btn-help">Contact Us</button>
            <button class="btn-help">Visit Office</button>
        </div>
    </section>

    <!-- Contact Modal -->
    <div id="contact-modal" class="contact-modal-overlay" style="display: none;">
        <div class="contact-modal-content">
            <div class="contact-modal-header">
                <h3>Contact Our Support Team</h3>
                <span class="contact-modal-close">&times;</span>
            </div>
            <div class="contact-modal-body">
                <div class="contact-info-section">
                    <div class="contact-info-item">
                        <div class="contact-info-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="contact-info-text">
                            <strong>Email</strong>
                            <a href="mailto:agriculture@sanpedro.gov.ph">agriculture@sanpedro.gov.ph</a>
                        </div>
                    </div>
                    <div class="contact-info-item">
                        <div class="contact-info-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <div class="contact-info-text">
                            <strong>Phone</strong>
                            <a href="tel:+631234567890">(049) 123-4567</a>
                        </div>
                    </div>
                    <div class="contact-info-item">
                        <div class="contact-info-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="contact-info-text">
                            <strong>Office Hours</strong>
                            <span>Monday - Friday: 8:00 AM - 5:00 PM</span>
                        </div>
                    </div>
                    <div class="contact-info-item">
                        <div class="contact-info-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div class="contact-info-text">
                            <strong>Address</strong>
                            <span>City Agriculture Office<br>San Pedro City Hall, Laguna</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if (isset($user))
        <!-- USER PROFILE MODAL -->
        <div id="profile-modal" class="modal-overlay" style="display: none;">
            <div class="modal-content profile-modal">
                <div class="modal-header">
                    <h3>My Profile</h3>
                    <span class="modal-close" onclick="closeProfileModal()">&times;</span>
                </div>

                <div class="modal-body">
                    <div class="profile-content">
                        <!-- Profile Header -->
                        @php $status = strtolower($user['status'] ?? 'active'); @endphp
                        <div class="profile-header">
                            <div class="profile-avatar-large">
                                {{ strtoupper(substr($user['name'] ?? $user['username'], 0, 1)) }}
                            </div>
                            <div class="profile-header-info">
                                <h4>{{ $user['name'] ?? $user['username'] }}</h4>
                                <p class="profile-email">{{ $user['email'] }}</p>
                                <div class="profile-status-badge status-{{ $status }}">
                                    {{ ucfirst($user['status'] ?? 'Active') }}
                                </div>
                            </div>
                        </div>

                        <!-- Profile Information -->
                        <div class="profile-info-grid">
                            <div class="profile-info-card">
                                <h5>Account Information</h5>
                                <div class="info-row">
                                    <span class="info-label">Username:</span>
                                    <span class="info-value">{{ $user['username'] }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Email:</span>
                                    <span class="info-value">{{ $user['email'] }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Account Status:</span>
                                    <span
                                        class="info-value status-text">{{ ucfirst($user['status'] ?? 'Active') }}</span>
                                </div>
                            </div>

                            <div class="profile-info-card">
                                <h5>Application Summary</h5>
                                <div class="stats-grid">
                                    <div class="stat-item">
                                        <div class="stat-number">{{ $user['total_applications'] ?? '0' }}</div>
                                        <div class="stat-label">Total Applications</div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-number">{{ $user['approved_applications'] ?? '0' }}</div>
                                        <div class="stat-label">Approved</div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-number">{{ $user['pending_applications'] ?? '0' }}</div>
                                        <div class="stat-label">Pending</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Profile Actions -->
                        <div class="profile-actions">
                            @php
                                $s = $status;
                            @endphp

                            @if (in_array($s, ['verified', 'approved']))
                                <button class="profile-action-btn verified" id="verify-action-btn" disabled>
                                    <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Verified</span>
                                </button>
                            @elseif(in_array($s, ['pending', 'pending_verification']))
                                <button class="profile-action-btn pending" id="verify-action-btn" disabled>
                                    <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <circle cx="12" cy="12" r="10" />
                                        <path d="M12 6v6l4 2" />
                                    </svg>
                                    <span>Pending Verification</span>
                                </button>
                            @elseif($s === 'rejected')
                                <button class="profile-action-btn rejected" id="verify-action-btn"
                                    onclick="showVerificationModal()">
                                    <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <path d="M4 12v8a2 2 0 002 2h12a2 2 0 002-2v-8" />
                                        <polyline points="16 6 12 2 8 6" />
                                        <line x1="12" y1="2" x2="12" y2="15" />
                                    </svg>
                                    <span>Retry Verification</span>
                                </button>
                            @else
                                <button class="profile-action-btn primary" id="verify-action-btn"
                                    onclick="showVerificationModal()">
                                    <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Verify Account</span>
                                </button>
                            @endif

                            <button class="profile-action-btn secondary" onclick="editProfile()">
                                <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" />
                                    <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                                </svg>
                                <span>Edit Profile</span>
                            </button>

                            <button class="profile-action-btn secondary" onclick="changePassword()">
                                <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <rect x="3" y="11" width="18" height="11" rx="2"
                                        ry="2" />
                                    <path d="M7 11V7a5 5 0 0110 0v4" />
                                </svg>
                                <span>Change Password</span>
                            </button>

                            <button class="profile-action-btn secondary"
                                onclick="showMyApplicationsModal(); closeProfileModal();">
                                <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" />
                                    <polyline points="14 2 14 8 20 8" />
                                    <line x1="16" y1="13" x2="8" y2="13" />
                                    <line x1="16" y1="17" x2="8" y2="17" />
                                    <polyline points="10 9 9 9 8 9" />
                                </svg>
                                <span>View Applications</span>
                            </button>
                        </div>

                        <!-- Recent Activity -->
                        <div class="recent-activity">
                            <h5>Recent Activity</h5>
                            <div class="activity-list" id="recent-activity-list">
                                <div class="activity-placeholder">
                                    <p>No recent activity to display</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MY APPLICATIONS MODAL -->
        <div id="applications-modal" class="modal-overlay" style="display: none;">
            <div class="modal-content applications-modal">
                <div class="modal-header">
                    <h3>My Applications</h3>
                    <span class="modal-close" onclick="closeApplicationsModal()">&times;</span>
                </div>

                <div class="modal-body">
                    <div class="applications-grid" id="applications-modal-grid">
                        <!-- Will be populated by JavaScript -->
                        <div class="loading-state">
                            <div class="loader"></div>
                            <p>Loading your applications...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- PROFILE VERIFICATION MODAL - UPDATED WITH AGE AND EMERGENCY CONTACT -->
        <div id="verification-modal" class="modal-overlay" style="display: none;">
            <div class="modal-content verification-modal">
                <div class="modal-header">
                    <h3>Profile Verification</h3>
                    <span class="modal-close" onclick="closeVerificationModal()">&times;</span>
                </div>

                <div class="modal-body">
                    <div class="verification-content">
                        <div class="verification-header">
                            <div class="verification-header-icon">
                                <svg width="48" height="48" viewBox="0 0 48 48" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M24 4C12.95 4 4 12.95 4 24s8.95 20 20 20 20-8.95 20-20-8.95-20-20-20z"
                                        stroke="#0A6953" stroke-width="2" fill="none" />
                                    <path d="M20 32l-6-6 1.41-1.41L20 29.18l10.59-10.59L32 20l-12 12z"
                                        fill="#0A6953" />
                                </svg>
                            </div>
                            <h4>Complete Your Profile Verification</h4>
                            <p>Please provide the following information to verify your account and access all services.
                            </p>
                        </div>

                        <form id="verification-form">
                            <!-- Personal Information -->
                            <div class="verification-section">
                                <div class="section-header">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                    <h5>Personal Information</h5>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="firstName">First Name</label>
                                        <input type="text" id="firstName" name="firstName" required
                                            placeholder="Enter your first name">
                                    </div>
                                    <div class="form-group">
                                        <label for="middleName">Middle Name (Optional)</label>
                                        <input type="text" id="middleName" name="middleName"
                                            placeholder="Enter your middle name">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="lastName">Last Name</label>
                                        <input type="text" id="lastName" name="lastName" required
                                            placeholder="Enter your last name">
                                    </div>
                                    <div class="form-group">
                                        <label for="extensionName">Name Extension</label>
                                        <select id="extensionName" name="extensionName">
                                            <option value="">None</option>
                                            <option value="Jr.">Jr.</option>
                                            <option value="Sr.">Sr.</option>
                                            <option value="II">II</option>
                                            <option value="III">III</option>
                                            <option value="IV">IV</option>
                                            <option value="V">V</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="dateOfBirth">Date of Birth</label>
                                        <input type="date" id="dateOfBirth" name="dateOfBirth" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="age">Age</label>
                                        <input type="number" id="age" name="age" min="18"
                                            max="100" readonly placeholder="Auto-calculated">
                                        <small>Calculated automatically from date of birth</small>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="contactNumber">Contact Number</label>
                                        <input type="tel" id="contactNumber" name="contactNumber" required
                                            placeholder="09123456789" pattern="[0-9]{11}">
                                        <small>11-digit Philippine mobile number</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="role">Role</label>
                                        <select id="role" name="role" required>
                                            <option value="" disabled selected>Select your role</option>
                                            <option value="farmer">Farmer</option>
                                            <option value="fisherfolk">Fisherfolk</option>
                                            <option value="general">General Public</option>
                                            <option value="agri-entrepreneur">Agricultural Entrepreneur</option>
                                            <option value="cooperative-member">Cooperative Member</option>
                                            <option value="government-employee">Government Employee</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Address Information -->
                            <div class="verification-section">
                                <div class="section-header">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                        <circle cx="12" cy="10" r="3"></circle>
                                    </svg>
                                    <h5>Address Information</h5>
                                </div>

                                <div class="form-group">
                                    <label for="barangay">Barangay</label>
                                    <select id="barangay" name="barangay" required>
                                        <option value="" disabled selected>Select Barangay</option>
                                        <option value="Bagong Silang">Bagong Silang</option>
                                        <option value="Cuyab">Cuyab</option>
                                        <option value="Estrella">Estrella</option>
                                        <option value="G.S.I.S.">G.S.I.S.</option>
                                        <option value="Landayan">Landayan</option>
                                        <option value="Langgam">Langgam</option>
                                        <option value="Laram">Laram</option>
                                        <option value="Magsaysay">Magsaysay</option>
                                        <option value="Nueva">Nueva</option>
                                        <option value="Poblacion">Poblacion</option>
                                        <option value="Riverside">Riverside</option>
                                        <option value="San Antonio">San Antonio</option>
                                        <option value="San Roque">San Roque</option>
                                        <option value="San Vicente">San Vicente</option>
                                        <option value="Santo Niño">Santo Niño</option>
                                        <option value="United Bayanihan">United Bayanihan</option>
                                        <option value="United Better Living">United Better Living</option>
                                        <option value="Sampaguita Village">Sampaguita Village</option>
                                        <option value="Calendola">Calendola</option>
                                        <option value="Narra">Narra</option>
                                        <option value="Chrysanthemum">Chrysanthemum</option>
                                        <option value="Fatima">Fatima</option>
                                        <option value="Maharlika">Maharlika</option>
                                        <option value="Pacita 1">Pacita 1</option>
                                        <option value="Pacita 2">Pacita 2</option>
                                        <option value="Rosario">Rosario</option>
                                        <option value="San Lorenzo Ruiz">San Lorenzo Ruiz</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="completeAddress">Complete Address</label>
                                    <textarea id="completeAddress" name="completeAddress" required rows="3"
                                        placeholder="Enter your complete address (House No., Street, Subdivision, etc.)"></textarea>
                                </div>
                            </div>

                            <!-- Emergency Contact Information -->
                            <div class="verification-section">
                                <div class="section-header">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2">
                                        <path
                                            d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z">
                                        </path>
                                    </svg>
                                    <h5>Emergency Contact</h5>
                                </div>
                                <p class="section-description">Provide a contact person we can reach in case of
                                    emergency</p>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="emergencyContactName">Emergency Contact Name</label>
                                        <input type="text" id="emergencyContactName" name="emergencyContactName"
                                            required placeholder="Full name of emergency contact">
                                    </div>
                                    <div class="form-group">
                                        <label for="emergencyContactPhone">Emergency Contact Phone</label>
                                        <input type="tel" id="emergencyContactPhone" name="emergencyContactPhone"
                                            required placeholder="09123456789" pattern="[0-9]{11}">
                                        <small>11-digit Philippine mobile number</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Document Uploads -->
                            <div class="verification-section">
                                <div class="section-header">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                        <polyline points="14 2 14 8 20 8"></polyline>
                                    </svg>
                                    <h5>Required Documents</h5>
                                </div>
                                <p class="section-description">Please upload clear, high-quality images of the required
                                    documents. Supported formats: JPG, PNG, PDF (Max 5MB)</p>

                                <div class="form-row">
                                    <div class="form-group file-upload-group">
                                        <label for="idFront">Government ID (Front)</label>
                                        <input type="file" id="idFront" name="idFront" required
                                            accept="image/*" class="file-input"
                                            onchange="previewImage(this, 'idFrontPreview')">
                                        <div class="file-upload-area"
                                            onclick="document.getElementById('idFront').click()">
                                            <div class="upload-icon">
                                                <svg width="32" height="32" viewBox="0 0 24 24"
                                                    fill="none" stroke="currentColor" stroke-width="2">
                                                    <rect x="3" y="4" width="18" height="14" rx="2">
                                                    </rect>
                                                    <path d="M3 18h18"></path>
                                                </svg>
                                            </div>
                                            <div class="upload-text">Click to upload ID front</div>
                                        </div>
                                        <div id="idFrontPreview" class="image-preview" style="display: none;"></div>
                                    </div>

                                    <div class="form-group file-upload-group">
                                        <label for="idBack">Government ID (Back)</label>
                                        <input type="file" id="idBack" name="idBack" required
                                            accept="image/*" class="file-input"
                                            onchange="previewImage(this, 'idBackPreview')">
                                        <div class="file-upload-area"
                                            onclick="document.getElementById('idBack').click()">
                                            <div class="upload-icon">
                                                <svg width="32" height="32" viewBox="0 0 24 24"
                                                    fill="none" stroke="currentColor" stroke-width="2">
                                                    <rect x="3" y="4" width="18" height="14" rx="2">
                                                    </rect>
                                                    <path d="M3 18h18"></path>
                                                </svg>
                                            </div>
                                            <div class="upload-text">Click to upload ID back</div>
                                        </div>
                                        <div id="idBackPreview" class="image-preview" style="display: none;"></div>
                                    </div>
                                </div>

                                <div class="form-group file-upload-group">
                                    <label for="locationProof">Location/Role Proof</label>
                                    <input type="file" id="locationProof" name="locationProof" required
                                        accept="image/*" class="file-input"
                                        onchange="previewImage(this, 'locationProofPreview')">
                                    <div class="file-upload-area"
                                        onclick="document.getElementById('locationProof').click()">
                                        <div class="upload-icon">
                                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2">
                                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                                <circle cx="12" cy="10" r="3"></circle>
                                            </svg>
                                        </div>
                                        <div class="upload-text">Click to upload location/role proof</div>
                                    </div>
                                    <div id="locationProofPreview" class="image-preview" style="display: none;">
                                    </div>
                                    <div class="form-text">
                                        <strong>For farmers:</strong> Photo of your farm or agricultural land<br>
                                        <strong>For fisherfolk:</strong> Photo of fishing area or boat<br>
                                        <strong>For others:</strong> Relevant business permit or location proof
                                    </div>
                                </div>
                            </div>

                            <!-- Verification Notice -->
                            <div class="verification-notice">
                                <div class="notice-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="12" y1="16" x2="12" y2="12"></line>
                                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                    </svg>
                                </div>
                                <div class="notice-content">
                                    <h6>Verification Process</h6>
                                    <p>Your submitted documents will be reviewed by our admin team within 2-3 business
                                        days. You will receive an email notification once your verification is approved
                                        or if additional documents are needed.</p>
                                </div>
                            </div>

                            <button type="submit" class="verification-submit-btn">
                                <span class="btn-text">Submit for Verification</span>
                                <span class="btn-loader" style="display: none;">Submitting...</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    @endif

    @if (!isset($user))
        <!-- AUTH MODALS (Only show for guests) -->
        <div id="auth-modal" class="auth-modal-overlay" style="display: none;">
            <div class="auth-modal-content">
                <div class="auth-modal-header">
                    <h3 id="auth-modal-title">LOG IN</h3>
                    <span class="auth-modal-close" onclick="closeAuthModal()">&times;</span>
                </div>

                <div class="auth-modal-body">
                    <!-- Error/Success Messages -->
                    <div id="auth-error-message" class="auth-message auth-error" style="display: none;"></div>
                    <div id="auth-success-message" class="auth-message auth-success" style="display: none;"></div>

                    <!-- Log In Form (Default) -->
                    <form id="login-form" class="auth-form" style="display: block;">
                        <h2 class="step-header">Welcome Back!</h2>
                        <p class="step-description">Sign in to access your agricultural services</p>
                        <div class="form-group">
                            <label for="username">Username or Email</label>
                            <input type="text" id="username" name="username" required
                                placeholder="Enter your username or email">
                        </div>

                        <div class="form-group">
                            <label for="login-password">Password</label>
                            <div class="password-input-container">
                                <input type="password" id="login-password" name="password" required
                                    placeholder="Enter your password">
                                <button type="button" class="password-toggle"
                                    onclick="togglePasswordVisibility('login-password')">
                                    Show
                                </button>
                            </div>
                        </div>

                        <div class="auth-links">
                            <a href="#" onclick="showForgotPassword()">Forgot your password?</a>
                        </div>

                        <button type="submit" class="auth-submit-btn">
                            <span class="btn-text">Sign In</span>
                            <span class="btn-loader" style="display: none;">Signing in...</span>
                        </button>

                        <!-- Divider -->
                        <div class="auth-divider">
                            <span>or</span>
                        </div>

                        <!-- Facebook Sign In Button -->
                        <button type="button" class="facebook-signin-btn" onclick="signInWithFacebook()">
                            <svg class="facebook-icon" viewBox="0 0 24 24" fill="#1877f2">
                                <path
                                    d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                            </svg>
                            Continue with Facebook
                        </button>

                        <!-- Sign Up Prompt -->
                        <div class="signup-prompt">
                            <p>Don't have an account? <a href="#" onclick="showSignUpForm()">Sign up here</a>
                            </p>
                        </div>
                    </form>

                    <!-- Simplified Sign Up Form (Hidden by default) -->
                    <div id="signup-form" class="auth-form" style="display: none;">
                        <form id="signup-form-submit">
                            <h2 class="step-header">Create Your Account</h2>
                            <p class="step-description">Fill in the details below to get started</p>
                            <div class="form-group">
                                <label for="signup-username">Username</label>
                                <input type="text" id="signup-username" name="username" required
                                    placeholder="Choose a username" autocomplete="username"
                                    pattern="^(?![0-9])[a-zA-Z0-9_.]{3,20}$" minlength="3" maxlength="20"
                                    oninput="checkUsernameAvailability(this.value)">
                                <div class="username-status"></div>
                                <div class="form-text">Username must be 3–20 characters long and contain only letters,
                                    numbers, underscores, or dots. Cannot start with a number.</div>
                            </div>

                            <div class="form-group">
                                <label for="signup-email">Email Address</label>
                                <input type="email" id="signup-email" name="email" required
                                    placeholder="e.g. juan.farmer@gmail.com" autocomplete="email" maxlength="254">
                            </div>

                            <div class="form-group">
                                <label for="signup-password">Password</label>
                                <div class="password-input-container">
                                    <input type="password" id="signup-password" name="password" required
                                        minlength="8" placeholder="Create a strong password"
                                        autocomplete="new-password">
                                    <button type="button" class="password-toggle"
                                        onclick="togglePasswordVisibility('signup-password')">
                                        Show
                                    </button>
                                </div>
                                <div class="password-strength">
                                    <div class="strength-bar">
                                        <div class="strength-fill"></div>
                                    </div>
                                    <div class="strength-text">Password strength</div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="signup-confirm-password">Confirm Password</label>
                                <div class="password-input-container">
                                    <input type="password" id="signup-confirm-password" name="confirm_password"
                                        required placeholder="Confirm your password" autocomplete="new-password">
                                    <button type="button" class="password-toggle"
                                        onclick="togglePasswordVisibility('signup-confirm-password')">
                                        Show
                                    </button>
                                </div>
                                <div class="password-match-status"></div>
                            </div>

                            <div class="form-group">
                                <div class="checkbox-group">
                                    <input type="checkbox" id="agree-terms" name="agree_terms" required>
                                    <label for="agree-terms">I agree to the <a href="#" target="_blank">Terms
                                            of Service</a> and <a href="#" target="_blank">Privacy
                                            Policy</a></label>
                                </div>
                            </div>

                            <!-- Add this before the SIGN UP button -->
                            <div class="recaptcha-container">
                                <div class="g-recaptcha" data-sitekey="{{ config('recaptcha.site_key') }}"></div>
                            </div>

                            <button type="submit" class="auth-submit-btn">
                                <span class="btn-text">SIGN UP</span>
                                <span class="btn-loader" style="display: none;">Creating...</span>
                            </button>

                            <!-- Divider -->
                            <div class="auth-divider">
                                <span>or</span>
                            </div>
                            <!-- Facebook Sign Up Button -->
                            <button type="button" class="facebook-signin-btn" onclick="signUpWithFacebook()">
                                <svg class="facebook-icon" viewBox="0 0 24 24" fill="#1877f2">
                                    <path
                                        d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                                </svg>
                                Sign up with Facebook
                            </button>
                            <!-- Back to Login Button -->
                            <div class="login-prompt">
                                <p>Already have an account? <a href="#"
                                        onclick="showLogInForm(); return false;">Login here</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- EDIT PROFILE MODAL -->
    <div id="edit-profile-modal" class="modal-overlay" style="display: none;">
        <div class="modal-content edit-profile-modal">
            <div class="modal-header">
                <h3>Edit Profile</h3>
                <span class="modal-close" onclick="closeEditProfileModal()">&times;</span>
            </div>

            <div class="modal-body">
                <form id="edit-profile-form">
                    <div class="profile-edit-section">
                        <h5>Personal Information</h5>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="edit-first-name">First Name *</label>
                                <input type="text" id="edit-first-name" name="first_name" required>
                            </div>
                            <div class="form-group">
                                <label for="edit-middle-name">Middle Name</label>
                                <input type="text" id="edit-middle-name" name="middle_name">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="edit-last-name">Last Name *</label>
                                <input type="text" id="edit-last-name" name="last_name" required>
                            </div>
                            <div class="form-group">
                                <label for="edit-name-extension">Name Extension</label>
                                <select id="edit-name-extension" name="name_extension">
                                    <option value="">Select</option>
                                    <option value="Jr.">Jr.</option>
                                    <option value="Sr.">Sr.</option>
                                    <option value="III">III</option>
                                    <option value="IV">IV</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="edit-contact-number">Contact Number *</label>
                                <input type="tel" id="edit-contact-number" name="contact_number"
                                    placeholder="e.g., +639123456789">
                            </div>
                            <div class="form-group">
                                <label for="edit-gender">Gender</label>
                                <select id="edit-gender" name="gender">
                                    <option value="">Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                    <option value="prefer_not_to_say">Prefer not to say</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="edit-date-of-birth">Date of Birth</label>
                                <input type="date" id="edit-date-of-birth" name="date_of_birth">
                            </div>
                            <div class="form-group">
                                <label for="edit-age">Age</label>
                                <input type="number" id="edit-age" name="age" min="18"
                                    max="100" readonly>
                                <small>Calculated automatically from date of birth</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="edit-user-type">User Type</label>
                            <select id="edit-user-type" name="user_type">
                                <option value="">Select Type</option>
                                <option value="farmer">Farmer</option>
                                <option value="fisherfolk">Fisherfolk</option>
                                <option value="individual">Individual</option>
                            </select>
                        </div>
                    </div>

                    <div class="profile-edit-section">
                        <h5>Address Information</h5>

                        <div class="form-group">
                            <label for="edit-complete-address">Complete Address *</label>
                            <textarea id="edit-complete-address" name="complete_address" placeholder="Enter your complete address"
                                rows="3"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="edit-barangay">Barangay</label>
                            <select id="edit-barangay" name="barangay">
                                <option value="">Select Barangay</option>
                                <option value="Bagong Silang">Bagong Silang</option>
                                <option value="Calendola">Calendola</option>
                                <option value="Chrysanthemum">Chrysanthemum</option>
                                <option value="Cuyab">Cuyab</option>
                                <option value="Estrella">Estrella</option>
                                <option value="Fatima">Fatima</option>
                                <option value="G.S.I.S.">G.S.I.S.</option>
                                <option value="Landayan">Landayan</option>
                                <option value="Langgam">Langgam</option>
                                <option value="Laram">Laram</option>
                                <option value="Magsaysay">Magsaysay</option>
                                <option value="Maharlika">Maharlika</option>
                                <option value="Narra">Narra</option>
                                <option value="Nueva">Nueva</option>
                                <option value="Pacita 1">Pacita 1</option>
                                <option value="Pacita 2">Pacita 2</option>
                                <option value="Poblacion">Poblacion</option>
                                <option value="Riverside">Riverside</option>
                                <option value="Rosario">Rosario</option>
                                <option value="Sampaguita Village">Sampaguita Village</option>
                                <option value="San Antonio">San Antonio</option>
                                <option value="San Lorenzo Ruiz">San Lorenzo Ruiz</option>
                                <option value="San Roque">San Roque</option>
                                <option value="San Vicente">San Vicente</option>
                                <option value="Santo Niño">Santo Niño</option>
                                <option value="United Bayanihan">United Bayanihan</option>
                                <option value="United Better Living">United Better Living</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-actions">
                        <button type="button" class="btn-secondary" onclick="closeEditProfileModal()">
                            Cancel
                        </button>
                        <button type="submit" class="btn-primary" id="save-profile-btn">
                            <span class="btn-text">Save Changes</span>
                            <span class="btn-loader" style="display: none;">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- CHANGE PASSWORD MODAL -->
    <div id="change-password-modal" class="modal-overlay" style="display: none;">
        <div class="modal-content change-password-modal">
            <div class="modal-header">
                <h3>Change Password</h3>
                <span class="modal-close" onclick="closeChangePasswordModal()">&times;</span>
            </div>

            <div class="modal-body">
                <div class="change-password-content">
                    <!-- Security Info Banner - Replaces password requirements -->
                    <div class="security-info-banner">
                        <div class="banner-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="16" x2="12" y2="12"></line>
                                <line x1="12" y1="8" x2="12.01" y2="8"></line>
                            </svg>
                        </div>
                        <p>Choose a strong password that you haven't used elsewhere. You'll need to log in again after
                            changing it.</p>
                    </div>

                    <!-- Prevent default form submission -->
                    <form id="change-password-form" onsubmit="return handleChangePasswordSubmit(event)">
                        <div class="form-group">
                            <label for="current-password">Current Password *</label>
                            <div class="password-input-container">
                                <input type="password" id="current-password" name="current_password" required
                                    autocomplete="current-password" placeholder="Enter your current password">
                                <button type="button" class="password-toggle"
                                    onclick="togglePasswordVisibility('current-password')">
                                    Show
                                </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="new-password">New Password *</label>
                            <div class="password-input-container">
                                <input type="password" id="new-password" name="new_password" required
                                    minlength="8" autocomplete="new-password"
                                    placeholder="Enter your new password"
                                    oninput="checkNewPasswordStrength(this.value)">
                                <button type="button" class="password-toggle"
                                    onclick="togglePasswordVisibility('new-password')">
                                    Show
                                </button>
                            </div>
                            <div class="password-strength new-password-strength">
                                <div class="strength-bar">
                                    <div class="strength-fill"></div>
                                </div>
                                <div class="strength-text">Password strength</div>
                            </div>
                            <!-- Password requirements checklist will be inserted here dynamically -->
                        </div>

                        <div class="form-group">
                            <label for="confirm-new-password">Confirm New Password *</label>
                            <div class="password-input-container">
                                <input type="password" id="confirm-new-password" name="confirm_new_password"
                                    required autocomplete="new-password" placeholder="Confirm your new password"
                                    oninput="checkNewPasswordMatch(document.getElementById('new-password').value, this.value)">
                                <button type="button" class="password-toggle"
                                    onclick="togglePasswordVisibility('confirm-new-password')">
                                    Show
                                </button>
                            </div>
                            <div class="password-match-status confirm-new-password-match"></div>
                        </div>

                        <div class="modal-actions">
                            <button type="button" class="btn-secondary" onclick="closeChangePasswordModal()">
                                Cancel
                            </button>
                            <button type="submit" class="btn-primary change-password-submit-btn">
                                <span class="btn-text">Change Password</span>
                                <span class="btn-loader" style="display: none;">Changing...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer Section -->
    <footer class="footer" id="main-footer">
        <div class="footer-container">
            <!-- Main content column (left side - like GAEA) -->
            <div class="footer-main">
                <div class="footer-logo">
                    <h2>AgriSys</h2>
                </div>
                <p>The Agricultural Service System (AgriSys) is designed to optimize service delivery for the City
                    Agriculture Office of San Pedro, Laguna. We aim to streamline agricultural services and support
                    local farmers.</p>

                <div class="social-links">
                    <span>Follow us:</span>
                    <a href="https://www.facebook.com/sanpedroagri" target="_blank" title="Facebook">
                        <svg width="16" height="16" viewBox="0 0 24 24">
                            <path
                                d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Services column -->
            <div class="footer-column">
                <h3>Our Services</h3>
                <ul>
                    <li><a href="#services">RSBSA Registration</a></li>
                    <li><a href="#services">Seedlings Request</a></li>
                    <li><a href="#services">FishR Registration</a></li>
                    <li><a href="#services">BoatR Registration</a></li>
                    <li><a href="#services">Training Request</a></li>
                </ul>
            </div>

            <!-- Contact column -->
            <div class="footer-column">
                <h3>Contact Us</h3>
                <p>City Agriculture Office<br>
                    San Pedro City Hall<br>
                    Laguna, Philippines</p>
                <p style="margin-top: 12px;">Phone: (123) 456-7890<br>
                    Email: <a href="mailto:agriculture@sanpedro.gov.ph">agriculture@sanpedro.gov.ph</a></p>
            </div>

            <!-- Office Hours column -->
            <div class="footer-column">
                <h3>Office Hours</h3>
                <div class="office-hours">
                    <strong>Monday - Friday</strong><br>
                    8:00 AM - 5:00 PM
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p>© 2025 City Agriculture Office of San Pedro. All rights reserved.</p>
            <div class="footer-bottom-links">
                <a href="#privacy" target="_blank">Privacy Policy</a>
                <a href="#terms" target="_blank">Terms of Service</a>
                <a href="#accessibility" target="_blank">Accessibility</a>
            </div>
        </div>
    </footer>

    <script src="{{ asset('js/events-loader.js') }}"></script>
    <script src="{{ asset('js/landing.js') }}"></script>
    <script src="{{ asset('js/seedlings.js') }}"></script>
    <script src="{{ asset('js/rsbsa.js') }}"></script>
    <script src="{{ asset('js/fishr.js') }}"></script>
    <script src="{{ asset('js/boatr.js') }}"></script>
    <script src="{{ asset('js/training.js') }}"></script>
    <script src="{{ asset('js/api-service.js') }}"></script>
    <script src="{{ asset('js/auth.js') }}"></script>
    <script src="{{ asset('js/session-manager.js') }}"></script>
    <script src="{{ asset('js/rsbsa-autofill.js') }}"></script>
    <script src="{{ asset('js/training-autofill.js') }}"></script>
    <script src="{{ asset('js/fishr-autofill.js') }}"></script>
    <script src="{{ asset('js/boatr-autofill.js') }}"></script>
    <script src="{{ asset('js/seedlings-autofill.js') }}"></script>
    <script src="{{ asset('js/my-applications-modal.js') }}"></script>
    <script src="{{ asset('js/toast-notifications.js') }}"></script>

    <!-- reCAPTCHA initialization callback -->
    <script>
        // Global callback function for when reCAPTCHA is loaded
        window.onRecaptchaLoad = function() {
            console.log('reCAPTCHA API loaded successfully');
            window.recaptchaLoaded = true;

            // Try to render reCAPTCHA immediately if container exists
            setTimeout(() => {
                const recaptchaContainer = document.querySelector('.g-recaptcha');
                if (recaptchaContainer && recaptchaContainer.children.length === 0) {
                    if (typeof window.renderRecaptcha === 'function') {
                        window.renderRecaptcha();
                    }
                }
            }, 100);
        };

        // Fallback check if callback doesn't fire
        document.addEventListener('DOMContentLoaded', function() {
            // Check if reCAPTCHA is loaded after page load
            setTimeout(function() {
                if (typeof grecaptcha !== 'undefined') {
                    window.recaptchaLoaded = true;
                    console.log('reCAPTCHA API detected after DOM load');

                    // Try to render if container exists and not already rendered
                    const recaptchaContainer = document.querySelector('.g-recaptcha');
                    if (recaptchaContainer && recaptchaContainer.children.length === 0) {
                        if (typeof window.renderRecaptcha === 'function') {
                            window.renderRecaptcha();
                        }
                    }
                } else {
                    console.warn('reCAPTCHA API not loaded after 1 second');
                }
            }, 1000);
        });
    </script>

    <script src="https://www.google.com/recaptcha/api.js?onload=onRecaptchaLoad&render=explicit" async defer></script>
    main
</body>

</html>
