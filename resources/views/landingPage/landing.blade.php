<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AgriSys - San Pedro City Agriculture Office</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logos/cago_web.png') }}">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}?v={{ config('app.asset_version') }}">
    <link rel="stylesheet" href="{{ asset('css/seedlings.css') }}?v={{ config('app.asset_version') }}">
    <link rel="stylesheet" href="{{ asset('css/fishr.css') }}?v={{ config('app.asset_version') }}">
    <link rel="stylesheet" href="{{ asset('css/boatr.css') }}?v={{ config('app.asset_version') }}">
    <link rel="stylesheet" href="{{ asset('css/rsbsa.css') }}?v={{ config('app.asset_version') }}">
    <link rel="stylesheet" href="{{ asset('css/training.css') }}?v={{ config('app.asset_version') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}?v={{ config('app.asset_version') }}">
    <link rel="stylesheet" href="{{ asset('css/toast-notifications.css') }}?v={{ config('app.asset_version') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

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

    <!-- Mobile Navigation Overlay -->
    <div class="mobile-nav-overlay" id="mobileNavOverlay" onclick="closeMobileNav()"></div>

    <!-- Mobile Navigation Menu -->
    <nav class="mobile-nav-menu" id="mobileNavMenu">
        <div class="mobile-nav-header">
            <span class="mobile-nav-title">Navigation</span>
            <button class="mobile-nav-close" onclick="closeMobileNav()" aria-label="Close menu">&times;</button>
        </div>
        <div class="mobile-nav-items">
            <a href="javascript:void(0)" class="mobile-nav-item" onclick="goHome(event); closeMobileNav();">
                <i class="fas fa-home"></i> Home
            </a>
            <a href="javascript:void(0)" class="mobile-nav-item" onclick="openFormSeedlings(event); closeMobileNav();">
                <i class="fas fa-tools"></i> Supplies & Garden Tools
            </a>
            <a href="javascript:void(0)" class="mobile-nav-item" onclick="openRSBSAForm(event); closeMobileNav();">
                <i class="fas fa-file-alt"></i> RSBSA Application
            </a>
            <a href="javascript:void(0)" class="mobile-nav-item" onclick="openFormFishR(event); closeMobileNav();">
                <i class="fas fa-fish"></i> FishR Registration
            </a>
            <a href="javascript:void(0)" class="mobile-nav-item" onclick="openFormBoatR(event); closeMobileNav();">
                <i class="fas fa-ship"></i> BoatR Registration
            </a>
            <a href="javascript:void(0)" class="mobile-nav-item" onclick="openFormTraining(event); closeMobileNav();">
                <i class="fas fa-chalkboard-teacher"></i> Training Registration
            </a>
        </div>
    </nav>

    <header>
        <div class="header-left">
            <div class="logo-text">
                <div class="logo-container">
                    <img src="{{ asset('images/logos/agrii-removebg.png') }}" alt="AgriSys Logo" class="main-logo">
                </div>
            </div>

            <!-- Desktop Navigation (hidden on mobile) -->
            <div class="header-center nav-buttons">
                <button type="button" class="btn" onclick="goHome(event)">Home</button>
                <button type="button" class="btn" onclick="openFormSeedlings(event)">Supplies & Garden
                    Tools</button>
                <button type="button" class="btn" onclick="openRSBSAForm(event)">RSBSA</button>
                <button type="button" class="btn" onclick="openFormFishR(event)">FishR</button>
                <button type="button" class="btn" onclick="openFormBoatR(event)">BoatR</button>
                <button type="button" class="btn" onclick="openFormTraining(event)">Training</button>
            </div>

            <div class="header-right auth-buttons">
                <!-- Mobile Menu Toggle Button (visible only on mobile) -->
                <button class="mobile-menu-toggle" onclick="toggleMobileNav()" aria-label="Toggle navigation menu">
                    <span>☰</span>
                </button>

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
                            <div class="user-status" id="header-user-status">
                                @if (isset($user['status']))
                                    <span id="status-text" class="status-badge-text">
                                        @php
                                            $statusLower = strtolower($user['status']);
                                            if ($statusLower === 'approved' || $statusLower === 'verified') {
                                                echo 'Verified';
                                            } elseif (
                                                $statusLower === 'pending' ||
                                                $statusLower === 'pending_verification'
                                            ) {
                                                echo 'Under Review';
                                            } elseif ($statusLower === 'rejected') {
                                                echo 'Verification Failed';
                                            } elseif ($statusLower === 'unverified') {
                                                echo 'Not Verified';
                                            } else {
                                                echo ucfirst($user['status']);
                                            }
                                        @endphp
                                    </span>
                                @else
                                    <span id="status-text" class="status-badge-text">Active</span>
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
                                    </div>
                                </div>
                            </div>

                            <div class="dropdown-menu">
                                <a href="#" class="dropdown-item" onclick="showMyApplicationsModal()">
                                    <svg class="dropdown-icon" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    My Applications
                                </a>
                                <a href="#" class="dropdown-item" onclick="showProfileModal()">
                                    <svg class="dropdown-icon" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
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
        <!-- Background Slideshow -->
        <div class="slideshow-container">
            @forelse($slides as $index => $slide)
                <div class="slide {{ $index === 0 ? 'active' : '' }}"
                    style="background-image: url('{{ $slide->image_url ?? $slide['image_url'] }}');"
                    data-title="{{ $slide->title ?? ($slide['title'] ?? '') }}"
                    data-description="{{ $slide->description ?? ($slide['description'] ?? '') }}">
                </div>
            @empty
                <!-- Fallback slide if no slides are available -->
                <div class="slide active" style="background-image: url('{{ asset('images/hero/bg1.jpg') }}');"
                    data-title="Welcome to AgriSys" data-description="Agricultural Services System">
                </div>
            @endforelse
        </div>

        <!-- Fallback background for when images don't load -->
        <div class="welcome-fallback"></div>

        <!-- Overlay for better text readability -->
        <div class="welcome-overlay"></div>

        <div class="welcome-content">
            <h2>Welcome to <span class="highlight">AgriSys</span><br>Agriculture System</br></h2>
            <p>The Agricultural Service System of the City Agriculture Office of San Pedro, Laguna</p>
            <button class="btn-services"
                onclick="document.getElementById('services').scrollIntoView({ behavior: 'smooth' })">Explore
                Services</button>
        </div>
        <div class="welcome-image"></div>

        <!-- Slideshow Controls -->
        <div class="slideshow-controls">
            <button class="slideshow-nav prev" onclick="previousSlide()" aria-label="Previous slide">❮</button>
            <div class="slide-indicators">
                @forelse($slides as $index => $slide)
                    <span class="indicator {{ $index === 0 ? 'active' : '' }}"
                        onclick="currentSlide({{ $index + 1 }})"></span>
                @empty
                    <span class="indicator active" onclick="currentSlide(1)"></span>
                @endforelse
            </div>
            <button class="slideshow-nav next" onclick="nextSlide()" aria-label="Next slide">❯</button>
        </div>
    </section>

    <!-- Events Section - Updated Layout -->
    <section class="events" id="events">
        <!-- Header with Logos and Title -->
        <div class="events-header">
            <img src="{{ asset('images/logos/agrii-removebg.png') }}" alt="AgriSys Logo"
                class="logo-icon agrisys-logo-large" loading="lazy">
            <img src="{{ asset('images/logos/Sanpedro.png') }}" alt="City of San Pedro Logo" class="logo-icon"
                loading="lazy">
            <img src="{{ asset('images/logos/Cago.png') }}" alt="CAGO Logo" class="logo-icon" loading="lazy">
        </div>

        <!-- Title and Subtitle -->
        <h2 id="events-title">City Agriculture Office Events</h2>
        <p class="events-subtitle" id="events-subtitle">Loading events information...</p>

        <!-- Dynamic Events Container -->
        <!-- Structure:
            - Top 3 Cards (from first 3 events)
            - Featured Large Section (from first event or featured)
            All content populated by JavaScript from API -->
        <div class="events-container">
            <!-- Loading state -->
            <div class="events-loading">
                <div class="loader"></div>
                <p>Loading events...</p>
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
                    <h3>Supplies & Garden Tools</h3>
                    <p>Request free supplies & garden tools to elevate your agricultural practices.</p>
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
                            <a href="mailto:agriculture.sanpedrocity@gmail.com">agriculture.sanpedrocity@gmail.com</a>
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
                            <p>8808-2020 Local 109</p>
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
                                        <label for="sex">Sex</label>
                                        <select id="sex" name="sex" required>
                                            <option value="">Select Sex</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="role">Sector</label>
                                        <select id="role" name="role" required>
                                            <option value="" disabled selected>Select your sector</option>
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
                                    <p>Your submitted documents will be reviewed within 1–3 business days. You will
                                        receive an SMS notification once your verification is approved or if additional
                                        documents are required.</p>
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
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" required
                                placeholder="Enter your username">
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
                                <label for="signup-contact">Contact Number</label>
                                <input type="tel" id="signup-contact" name="contact_number" required
                                    placeholder="e.g. 09123456789" autocomplete="tel" maxlength="11"
                                    pattern="09[0-9]{9}" oninput="checkContactAvailability(this.value)">
                                <div class="contact-status"></div>
                                <div class="form-text">Enter your active mobile number starting with 09 for SMS
                                    notifications.</div>
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
                                    <label for="agree-terms">I agree to the
                                        <a href="#" onclick="openTermsModal(event)">Terms of Service</a>
                                        and
                                        <a href="#" onclick="openPrivacyModal(event)">Privacy Policy</a></label>
                                </div>
                            </div>

                            <button type="submit" class="auth-submit-btn">
                                <span class="btn-text">SIGN UP</span>
                                <span class="btn-loader" style="display: none;">Creating...</span>
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

        <!-- FORGOT PASSWORD MODAL -->
        <div id="forgot-password-modal" class="auth-modal-overlay" style="display: none;">
            <div class="auth-modal-content forgot-password-modal">
                <div class="auth-modal-header">
                    <h3 id="forgot-password-title">Forgot Password</h3>
                    <span class="auth-modal-close" onclick="closeForgotPasswordModal()">&times;</span>
                </div>

                <div class="auth-modal-body">
                    <!-- Error/Success Messages -->
                    <div id="forgot-error-message" class="auth-message auth-error" style="display: none;"></div>
                    <div id="forgot-success-message" class="auth-message auth-success" style="display: none;"></div>

                    <!-- Step 1: Enter Username or Contact Number -->
                    <div id="forgot-step-1" class="forgot-step">
                        <h2 class="step-header">Reset Your Password</h2>
                        <p class="step-description">Enter your username or registered mobile number to receive a
                            verification code</p>

                        <form id="forgot-contact-form" class="auth-form">
                            <div class="form-group">
                                <label for="forgot-identifier">Username or Mobile Number</label>
                                <input type="text" id="forgot-identifier" name="identifier" required
                                    placeholder="Enter username or 09XXXXXXXXX" minlength="3"
                                    oninput="validateForgotIdentifier(this.value)" autocomplete="username">
                                <div class="form-text">We'll send an OTP to the mobile number linked to your account
                                </div>
                            </div>

                            <button type="submit" class="auth-submit-btn" id="send-otp-btn">
                                <span class="btn-text">Send OTP</span>
                                <span class="btn-loader" style="display: none;">Sending...</span>
                            </button>
                        </form>

                        <div class="login-prompt">
                            <p>Remember your password? <a href="#" onclick="backToLogin()">Back to Login</a></p>
                        </div>
                    </div>

                    <!-- Step 2: Enter OTP -->
                    <div id="forgot-step-2" class="forgot-step" style="display: none;">
                        <h2 class="step-header">Enter Verification Code</h2>
                        <p class="step-description">We sent a 6-digit code to <span id="masked-contact"></span></p>
                        <p class="step-description-small" id="account-username-display" style="display: none;">
                            Account: <strong id="account-username"></strong></p>

                        <form id="forgot-otp-form" class="auth-form">
                            <div class="form-group">
                                <label for="forgot-otp">Verification Code</label>
                                <div class="otp-input-container">
                                    <input type="text" id="forgot-otp-1" class="otp-input" maxlength="1"
                                        pattern="[0-9]" inputmode="numeric" autocomplete="one-time-code">
                                    <input type="text" id="forgot-otp-2" class="otp-input" maxlength="1"
                                        pattern="[0-9]" inputmode="numeric">
                                    <input type="text" id="forgot-otp-3" class="otp-input" maxlength="1"
                                        pattern="[0-9]" inputmode="numeric">
                                    <input type="text" id="forgot-otp-4" class="otp-input" maxlength="1"
                                        pattern="[0-9]" inputmode="numeric">
                                    <input type="text" id="forgot-otp-5" class="otp-input" maxlength="1"
                                        pattern="[0-9]" inputmode="numeric">
                                    <input type="text" id="forgot-otp-6" class="otp-input" maxlength="1"
                                        pattern="[0-9]" inputmode="numeric">
                                </div>
                                <input type="hidden" id="forgot-otp-combined" name="otp">
                            </div>

                            <div class="otp-timer">
                                <span id="otp-countdown">Code expires in <strong>05:00</strong></span>
                            </div>

                            <button type="submit" class="auth-submit-btn" id="verify-otp-btn">
                                <span class="btn-text">Verify Code</span>
                                <span class="btn-loader" style="display: none;">Verifying...</span>
                            </button>
                        </form>

                        <div class="resend-otp">
                            <p>Didn't receive the code? <a href="#" id="resend-otp-link"
                                    onclick="resendOtp(event)">Resend OTP</a></p>
                        </div>

                        <div class="login-prompt">
                            <p><a href="#" onclick="goToStep1()">← Change number</a></p>
                        </div>
                    </div>

                    <!-- Step 3: Reset Password -->
                    <div id="forgot-step-3" class="forgot-step" style="display: none;">
                        <h2 class="step-header">Create New Password</h2>
                        <p class="step-description">Your identity has been verified. Set your new password below.</p>

                        <!-- Account Info Display -->
                        <div class="account-info-box" id="reset-account-info">
                            <div class="account-info-row">
                                <span class="account-info-label">Username:</span>
                                <span class="account-info-value" id="reset-username-display">—</span>
                            </div>
                            <div class="account-info-row">
                                <span class="account-info-label">Mobile:</span>
                                <span class="account-info-value" id="reset-contact-display">—</span>
                            </div>
                        </div>

                        <form id="forgot-reset-form" class="auth-form">
                            <input type="hidden" id="reset-token" name="reset_token">
                            <input type="hidden" id="reset-contact" name="contact_number">

                            <div class="form-group">
                                <label for="new-password">New Password</label>
                                <div class="password-input-container">
                                    <input type="password" id="new-password" name="password" required minlength="8"
                                        placeholder="Create a strong password"
                                        oninput="checkResetPasswordStrength(this.value)">
                                    <button type="button" class="password-toggle"
                                        onclick="togglePasswordVisibility('new-password')">
                                        Show
                                    </button>
                                </div>
                                <div class="password-strength reset-password-strength">
                                    <div class="strength-bar">
                                        <div class="strength-fill"></div>
                                    </div>
                                    <div class="strength-text">Password strength</div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="confirm-new-password">Confirm New Password</label>
                                <div class="password-input-container">
                                    <input type="password" id="confirm-new-password" name="password_confirmation"
                                        required placeholder="Confirm your new password"
                                        oninput="checkResetPasswordMatch()">
                                    <button type="button" class="password-toggle"
                                        onclick="togglePasswordVisibility('confirm-new-password')">
                                        Show
                                    </button>
                                </div>
                                <div class="password-match-status reset-password-match"></div>
                            </div>

                            <button type="submit" class="auth-submit-btn" id="reset-password-btn">
                                <span class="btn-text">Reset Password</span>
                                <span class="btn-loader" style="display: none;">Resetting...</span>
                            </button>
                        </form>
                    </div>

                    <!-- Step 4: Success -->
                    <div id="forgot-step-4" class="forgot-step" style="display: none;">
                        <div class="success-icon">
                            <svg width="80" height="80" viewBox="0 0 80 80" fill="none">
                                <circle cx="40" cy="40" r="38" stroke="#10b981"
                                    stroke-width="4" />
                                <path d="M24 40L35 51L56 30" stroke="#10b981" stroke-width="4"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                        <h2 class="step-header success-header">Password Reset Successful!</h2>
                        <p class="step-description">Your password has been changed successfully. You can now log in
                            with your new password.</p>

                        <button type="button" class="auth-submit-btn" onclick="backToLogin()">
                            <span class="btn-text">Back to Login</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- EDIT PROFILE MODAL - UPDATED VERSION WITH EDITABLE USERNAME (ONCE) -->
    <div id="edit-profile-modal" class="modal-overlay" style="display: none;">
        <div class="modal-content edit-profile-modal">
            <div class="modal-header">
                <h3>Edit Profile</h3>
                <span class="modal-close" onclick="closeEditProfileModal()">&times;</span>
            </div>

            <div class="modal-body">
                <form id="edit-profile-form">
                    <!-- Profile Image & Username Section -->
                    <div class="profile-edit-section">
                        <h5>Profile Information</h5>

                        <div class="profile-image-section">
                            <div class="profile-image-display">
                                <div id="profile-image-preview" class="profile-image-avatar">
                                    <span id="profile-avatar-letter">U</span>
                                </div>
                                <div class="profile-image-info">
                                    <p class="profile-image-label">Profile Picture</p>
                                    <p class="profile-image-note">Avatar based on your username</p>
                                </div>
                            </div>
                        </div>

                        <!-- Username Field - Editable once -->
                        <div class="form-group">
                            <div class="username-field-wrapper">
                                <label for="edit-username">Username *</label>
                                <div class="username-input-container">
                                    <input type="text" id="edit-username" name="username"
                                        placeholder="Enter your username" minlength="3" maxlength="50"
                                        pattern="^[a-zA-Z0-9_]+$" data-original-username="">
                                    <span id="username-edit-indicator" class="username-edit-indicator"
                                        style="display: none;">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2">
                                            <path d="M12 6c3.314 0 6-1.343 6-3s-2.686-3-6-3-6 1.343-6 3 2.686 3 6 3z" />
                                            <path
                                                d="M6 9c-1.654.737-3 1.956-3 3.341 0 2.219 2.686 4 6 4s6-1.781 6-4c0-1.385-1.346-2.604-3-3.341" />
                                        </svg>
                                        Can only be changed once
                                    </span>
                                </div>
                                <small>Letters, numbers, and underscores only (3-50 characters)</small>
                            </div>
                        </div>


                    </div>

                    <!-- Contact Information Section -->
                    <div class="profile-edit-section">
                        <h5>Contact Information</h5>

                        <div class="form-group">
                            <label for="edit-contact-number">Contact Number *</label>
                            <input type="tel" id="edit-contact-number" name="contact_number"
                                placeholder="09XXXXXXXXX or +639XXXXXXXXX" pattern="^(\+639|09)\d{9}$"
                                maxlength="20">
                            <small>11-digit Philippine mobile number format</small>
                        </div>
                    </div>

                    <!-- Address Information Section -->
                    <div class="profile-edit-section">
                        <h5>Address Information</h5>

                        <div class="form-group">
                            <label for="edit-complete-address">Complete Address *</label>
                            <textarea id="edit-complete-address" name="complete_address"
                                placeholder="Enter your complete address (House No., Street, Subdivision, etc.)" rows="3"
                                maxlength="500"></textarea>
                            <small>Include house number, street name, and subdivision/barangay details</small>
                        </div>

                        <div class="form-group">
                            <label for="edit-barangay">Barangay *</label>
                            <select id="edit-barangay" name="barangay" required>
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

                    <!-- Modal Actions -->
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

    <!-- Terms of Service Modal - UPDATED VERSION -->
    <div id="terms-modal" class="modal-overlay" style="display: none;">
        <div class="modal-content terms-modal-content">
            <div class="modal-header">
                <h3>Terms of Service</h3>
                <span class="modal-close" onclick="closeTermsModal()">&times;</span>
            </div>
            <div class="modal-body">
                <div class="modal-scroll-content">
                    <!-- 1. Acceptance of Terms -->
                    <div class="section">
                        <h2>1. Acceptance of Terms</h2>
                        <p>These Terms of Service ("Terms") govern your access to and use of AgriSys, a web-based
                            decision support and records management system with integration of large language model for
                            data analysis, accessible through agrisys.site and operated by the City Agriculture Office
                            of San Pedro, Laguna ("City Agriculture Office," "we," "our," or "CAgO").</p>
                        <p>By creating an account, logging in, submitting any registration form, requesting agricultural
                            supplies, or using any feature of AgriSys, you acknowledge that you have read, understood,
                            and agree to be bound by these Terms of Service, the AgriSys Data Privacy Notice, and all
                            applicable laws and regulations of the Republic of the Philippines, including but not
                            limited to:</p>
                        <ul>
                            <li>Republic Act No. 10173 (Data Privacy Act of 2012)</li>
                            <li>Republic Act No. 9470 (National Archives of the Philippines Act)</li>
                            <li>Local Government Code of the Philippines</li>
                            <li>Department of Agriculture regulations and programs</li>
                        </ul>
                        <p>If you do not agree to these Terms, you must immediately cease all use of AgriSys and refrain
                            from creating an account or submitting any information through the system.</p>
                    </div>

                    <!-- 2. Description of Service -->
                    <div class="section">
                        <h2>2. Description of Service</h2>
                        <p>AgriSys is a decision support system developed to modernize and streamline agricultural
                            service delivery for the City Agriculture Office of San Pedro, Laguna. The system provides
                            the following core functionalities:</p>

                        <h4>2.1 Beneficiary Registration and Management</h4>
                        <ul>
                            <li>Registration of farmers, fisherfolk, livestock raisers, and poultry raisers in the
                                Registry System for Basic Sectors in Agriculture (RSBSA)</li>
                            <li>Maintenance of updated beneficiary profiles including agricultural sector, commodity
                                details, farm information, and contact details</li>
                            <li>Digital archiving of supporting documents and government-issued identification</li>
                        </ul>

                        <h4>2.2 Agricultural Supply Request and Distribution Management</h4>
                        <ul>
                            <li>Online submission of requests for agricultural inputs including seedlings, tilapia
                                fingerlings, and organic fertilizers</li>
                            <li>Processing, approval, and tracking of supply requests by City Agriculture Office
                                personnel</li>
                            <li>Real-time monitoring of supply inventory levels with automated alerts</li>
                            <li>Generation of distribution records and acknowledgment receipts</li>
                        </ul>

                        <h4>2.3 Training and Capacity Building</h4>
                        <ul>
                            <li>Application for agricultural training programs, seminars, and workshops</li>
                            <li>Management of community events and agricultural fairs</li>
                            <li>Tracking of participation and completion records</li>
                        </ul>

                        <h4>2.4 Prescriptive Analytics and Decision Support</h4>
                        <ul>
                            <li>Integration of Anthropic's Claude Sonnet 4.5 large language model to analyze
                                agricultural data and generate actionable insights</li>
                            <li>Identification of trends in crop demand, supply distribution patterns, and beneficiary
                                needs</li>
                            <li>Visual dashboards and reports that support evidence-based planning and resource
                                allocation</li>
                            <li>Data-driven recommendations to optimize operational efficiency and service delivery</li>
                        </ul>

                        <h4>2.5 Document Retrieval and Records Management</h4>
                        <ul>
                            <li>Digitization of paper-based records for secure storage and quick retrieval</li>
                            <li>Searchable database of beneficiary information, transaction history, and program
                                documentation</li>
                            <li>Compliance with government archival and record retention standards</li>
                        </ul>

                        <p style="margin-top: 15px;">AgriSys is provided primarily for official agricultural programs
                            and services administered by the City Agriculture Office. The system, its features, and
                            availability may be updated, improved, modified, or temporarily suspended from time to time
                            at the sole discretion of the City Agriculture Office for maintenance, security
                            enhancements, policy compliance, or operational requirements.</p>
                    </div>

                    <!-- 3. User Accounts and Responsibilities -->
                    <div class="section">
                        <h2>3. User Accounts and Responsibilities</h2>
                        <h4>3.1 Eligibility and Account Types</h4>
                        <p>AgriSys is accessible to the following authorized user categories:</p>
                        <p><strong>Beneficiaries:</strong></p>
                        <ul>
                            <li>Registered farmers, fisherfolk, livestock raisers, and poultry raisers residing in San
                                Pedro, Laguna</li>
                            <li>Individuals or organizations eligible for agricultural programs administered by the City
                                Agriculture Office</li>
                            <li>Representatives authorized to act on behalf of eligible beneficiaries (with proper
                                documentation)</li>
                        </ul>
                        <p><strong>City Agriculture Office Personnel:</strong></p>
                        <ul>
                            <li>Admin Users: City Agriculturists and designated staff responsible for processing
                                requests and managing beneficiary records</li>
                            <li>SuperAdmin Users: City Head Agriculturist and authorized administrators with system-wide
                                configuration capabilities</li>
                        </ul>
                        <p>Minors (individuals under 18 years of age) may only register with the consent and supervision
                            of a parent or legal guardian.</p>

                        <h4>3.2 Account Creation and Verification</h4>
                        <p>To create an account, you must provide accurate, complete, and current personal information,
                            submit valid government-issued identification, and agree to the processing of your personal
                            data in accordance with the AgriSys Data Privacy Notice.</p>

                        <h4>3.3 Account Security and Confidentiality</h4>
                        <p>You are solely responsible for maintaining the confidentiality and security of your username,
                            password, and any other login credentials. You agree not to share your account credentials
                            with any other person or entity. The City Agriculture Office is not liable for any loss,
                            damage, or unauthorized access resulting from your failure to safeguard your account
                            credentials.</p>

                        <h4>3.4 Accuracy and Updates of Information</h4>
                        <p>You agree to provide truthful, accurate, and complete information in all submissions and
                            promptly update your account information when changes occur. Failure to maintain accurate
                            information may result in delays in service delivery, denial of supply requests, or
                            suspension of your account.</p>
                    </div>

                    <!-- 4. Acceptable Use -->
                    <div class="section">
                        <h2>4. Acceptable Use</h2>
                        <p>You agree to use AgriSys only for lawful purposes and in connection with legitimate
                            agricultural services, programs, and transactions administered by the City Agriculture
                            Office of San Pedro, Laguna.</p>

                        <h4>4.1 Prohibited Activities</h4>
                        <p><strong>Unauthorized Access and Security Violations:</strong></p>
                        <ul>
                            <li>Attempting to gain unauthorized access to any part of AgriSys or other user accounts
                            </li>
                            <li>Bypassing, disabling, or interfering with security features, authentication mechanisms,
                                or access controls</li>
                            <li>Probing, scanning, or testing the system for vulnerabilities without prior written
                                authorization</li>
                            <li>Using automated tools, scripts, bots, or spiders to access, scrape, or extract data from
                                AgriSys</li>
                        </ul>

                        <p><strong>False Information and Fraud:</strong></p>
                        <ul>
                            <li>Submitting false, misleading, fraudulent, or deceptive information in registration forms
                                or supply requests</li>
                            <li>Misrepresenting your identity, agricultural sector, farm ownership, or eligibility
                                status</li>
                            <li>Making duplicate requests or registrations to obtain more than your fair share of
                                agricultural supplies</li>
                            <li>Falsifying documents, signatures, or supporting evidence submitted to the City
                                Agriculture Office</li>
                        </ul>

                        <p><strong>System Interference and Misuse:</strong></p>
                        <ul>
                            <li>Uploading, transmitting, or distributing viruses, malware, ransomware, or any malicious
                                code</li>
                            <li>Interfering with the normal operation, performance, or functionality of AgriSys or its
                                host networks</li>
                            <li>Overloading the system with excessive requests or traffic that may degrade service
                                availability</li>
                            <li>Modifying, reverse engineering, decompiling, or attempting to extract source code from
                                AgriSys</li>
                        </ul>

                        <p><strong>Data Misuse and Privacy Violations:</strong></p>
                        <ul>
                            <li>Accessing, collecting, copying, or using personal data of other beneficiaries obtained
                                through AgriSys for unauthorized purposes</li>
                            <li>Disclosing, sharing, selling, or distributing personal data to third parties without
                                proper authorization</li>
                            <li>Using any data obtained from AgriSys for unauthorized commercial activities, political
                                campaigns, or marketing</li>
                        </ul>

                        <p><strong>Impersonation and Misrepresentation:</strong></p>
                        <ul>
                            <li>Impersonating City Agriculture Office personnel, government officials, or other
                                beneficiaries</li>
                            <li>Creating accounts using another person's identity or information without their consent
                            </li>
                        </ul>

                        <p>The City Agriculture Office reserves the right to monitor system usage, conduct audits, and
                            investigate suspected violations. Evidence of prohibited activities may result in account
                            suspension, termination, legal action, and reporting to appropriate law enforcement or
                            regulatory authorities.</p>
                    </div>

                    <!-- 5. Data, Privacy, and Confidentiality -->
                    <div class="section">
                        <h2>5. Data, Privacy, and Confidentiality</h2>
                        <h4>5.1 Data Collection and Processing</h4>
                        <p>Use of AgriSys involves the collection, storage, processing, and analysis of personal and
                            sensitive personal information for beneficiary registration, agricultural service delivery,
                            supply management, training coordination, decision support analytics, and compliance
                            reporting.</p>
                        <p>By using AgriSys, you acknowledge and consent that your personal data will be processed in
                            accordance with the AgriSys Data Privacy Notice and Republic Act No. 10173 (Data Privacy Act
                            of 2012), its Implementing Rules and Regulations, and all relevant issuances of the National
                            Privacy Commission.</p>

                        <h4>5.2 Confidentiality Obligations for Authorized Personnel</h4>
                        <p>If you are a City Agriculture Office staff member granted access to beneficiary data, supply
                            records, or other confidential information through AgriSys, you agree to use such data
                            solely for official purposes related to your assigned duties and responsibilities, maintain
                            strict confidentiality, and report any suspected data breaches immediately to the Data
                            Protection Officer.</p>
                        <p>Unauthorized disclosure or misuse of personal data is a violation of these Terms of Service
                            and may result in disciplinary action, account termination, and legal consequences under the
                            Data Privacy Act of 2012.</p>

                        <h4>5.3 Third-Party Service Providers</h4>
                        <p>AgriSys utilizes the following third-party service providers to operate the system:</p>
                        <ul>
                            <li><strong>Anthropic (Claude Sonnet 4.5):</strong> For large language model analytics and
                                prescriptive decision support insights</li>
                            <li><strong>Hostinger:</strong> For web hosting and server infrastructure</li>
                            <li><strong>Cloudflare:</strong> For content delivery, security, and DDoS protection</li>
                        </ul>
                        <p>These service providers are selected based on their security standards and compliance with
                            data protection regulations. The City Agriculture Office ensures that appropriate data
                            processing agreements are in place to safeguard your information.</p>
                    </div>

                    <!-- 6. Intellectual Property and System Content -->
                    <div class="section">
                        <h2>6. Intellectual Property and System Content</h2>
                        <h4>6.1 Ownership of AgriSys</h4>
                        <p>AgriSys, including its software architecture, source code, database design, user interface,
                            decision support features, prescriptive analytics capabilities, system documentation, and
                            all related intellectual property, is owned and controlled by the City Agriculture Office of
                            San Pedro, Laguna and/or its authorized developers (Polytechnic University of the
                            Philippines - San Pedro Campus research team: Prias, Jasper T.; Reyes, Jerald G.; Saez,
                            Blanca Alexis C.; Viado, Arvy M.).</p>

                        <h4>6.2 Limited License to Use</h4>
                        <p>You are granted a limited, non-exclusive, non-transferable, revocable right to access and use
                            AgriSys solely for authorized purposes. <strong>Beneficiaries:</strong> To register, submit
                            supply requests, apply for training programs, and view transaction history. <strong>City
                                Agriculture Office Personnel:</strong> To perform official duties related to beneficiary
                            management, supply distribution, and program administration.</p>

                        <h4>6.3 Restrictions on Use</h4>
                        <p>You may not reproduce, duplicate, copy, modify, adapt, distribute, sublicense, or reverse
                            engineer any part of AgriSys. <strong>Exception:</strong> City Agriculture Office personnel
                            may reproduce system-generated reports, dashboards, and analytics as necessary for official
                            reporting purposes, provided that such use is consistent with data privacy obligations.</p>

                        <h4>6.4 User-Supplied Data</h4>
                        <p>You retain ownership of the personal information, documents, and data that you submit through
                            AgriSys. However, by submitting this information, you grant the City Agriculture Office a
                            non-exclusive, royalty-free license to use, process, store, and analyze such data for the
                            purposes stated in the AgriSys Data Privacy Notice and these Terms of Service.</p>
                    </div>

                    <!-- 7. Service Availability, Changes, and Suspension -->
                    <div class="section">
                        <h2>7. Service Availability, Changes, and Suspension</h2>
                        <h4>7.1 Service Availability</h4>
                        <p>The City Agriculture Office strives to maintain AgriSys as a reliable, accessible, and
                            functional platform for agricultural service delivery. However, we do not guarantee that the
                            system will be available at all times without interruption, free from errors, fully secure
                            from cyberattacks, or compatible with all devices and browsers.</p>

                        <h4>7.2 Maintenance and Updates</h4>
                        <p>AgriSys may be temporarily unavailable or experience reduced functionality due to scheduled
                            maintenance, security patches, database backups, or testing of new features. Where feasible,
                            the City Agriculture Office will provide advance notice of scheduled maintenance through
                            system notifications, sms, or official announcements.</p>

                        <h4>7.3 Modifications to Features and Services</h4>
                        <p>The City Agriculture Office reserves the right to add, modify, or remove features,
                            functionalities, and services offered through AgriSys. Continued use of AgriSys following
                            such changes constitutes your acceptance of the modified system and policies.</p>

                        <h4>7.4 Suspension or Discontinuation of Service</h4>
                        <p>The City Agriculture Office may, at its sole discretion and without prior notice, temporarily
                            suspend or permanently discontinue all or part of AgriSys for reasons including changes in
                            government policy, legal or regulatory requirements, security threats, or operational
                            inefficiencies. In the event of permanent discontinuation, the City Agriculture Office will,
                            where feasible, provide reasonable notice.</p>

                        <h4>7.5 No Liability for Service Interruptions</h4>
                        <p>The City Agriculture Office shall not be liable for any loss, damage, inconvenience, delay,
                            or inability to access AgriSys resulting from system downtime, maintenance, third-party
                            service provider outages, or force majeure events.</p>
                    </div>

                    <!-- 8. Limitation of Liability -->
                    <div class="section">
                        <h2>8. Limitation of Liability</h2>
                        <h4>8.1 Disclaimer of Warranties</h4>
                        <p>To the maximum extent permitted by law, AgriSys is provided on an "as is" and "as available"
                            basis without warranties of any kind, either express or implied. The City Agriculture Office
                            makes no representation or warranty that AgriSys will meet your specific requirements or
                            expectations, or that any defects or errors will be corrected.</p>

                        <h4>8.2 Limitation of Liability</h4>
                        <p>To the fullest extent permitted by applicable law, the City Agriculture Office, its officers,
                            employees, staff, authorized developers, technology partners, and service providers shall
                            not be liable for any direct, indirect, incidental, consequential, special, punitive, or
                            exemplary damages arising from or in connection with your use or inability to use AgriSys,
                            errors in data or analytics, delays in processing, unauthorized access to your account, or
                            reliance on system-generated insights without independent verification.</p>

                        <h4>8.3 Professional Judgment and Verification</h4>
                        <p>Prescriptive analytics, decision support recommendations, and insights generated by AgriSys
                            using Anthropic's Claude Sonnet 4.5 large language model are intended to assist City
                            Agriculture Office personnel in planning and resource allocation. However, all analytics,
                            reports, and recommendations remain subject to professional judgment, field validation, and
                            compliance with applicable agricultural program guidelines. City Agriculture Office
                            personnel are responsible for independently verifying the accuracy and appropriateness of
                            system-generated insights before making operational decisions.</p>

                        <h4>8.4 Indemnification</h4>
                        <p>You agree to indemnify, defend, and hold harmless the City Agriculture Office, its officers,
                            employees, authorized developers, and service providers from and against any claims,
                            liabilities, damages, losses, costs, and expenses arising from or related to your violation
                            of these Terms of Service, your submission of false or fraudulent information, your
                            unauthorized use of AgriSys, or your misuse of personal data.</p>
                    </div>

                    <!-- 9. Termination and Access Restriction -->
                    <div class="section">
                        <h2>9. Termination and Access Restriction</h2>
                        <h4>9.1 Termination by the City Agriculture Office</h4>
                        <p>The City Agriculture Office may, at its sole discretion and without prior notice, suspend,
                            restrict, or permanently terminate your access to AgriSys if you violate any provision of
                            these Terms of Service, engage in prohibited activities, submit false information, are found
                            to be ineligible for agricultural programs, or if legal, regulatory, or security concerns
                            require immediate action.</p>

                        <h4>9.2 Termination by You</h4>
                        <p>You may request termination of your account at any time by contacting the City Agriculture
                            Office Data Protection Officer. Upon voluntary termination, your access to AgriSys will be
                            revoked and you will no longer be able to submit supply requests or access system features.
                        </p>

                        <h4>9.3 Effect of Termination</h4>
                        <p>Upon termination of your account, you must immediately cease all use of AgriSys. Your right
                            to use the system and any licenses granted under these Terms of Service will automatically
                            terminate. Certain personal data and transaction records may continue to be retained in
                            accordance with Republic Act No. 9470, the City Agriculture Office's 5-year data retention
                            policy, and legal requirements for audit, compliance, and program monitoring purposes.</p>

                        <h4>9.4 Survival of Terms</h4>
                        <p>The following provisions shall survive termination of your account: Sections 5 (Data,
                            Privacy, and Confidentiality), 6 (Intellectual Property), 8 (Limitation of Liability), and
                            10 (Governing Law).</p>
                    </div>

                    <!-- 10. Governing Law and Dispute Resolution -->
                    <div class="section">
                        <h2>10. Governing Law and Dispute Resolution</h2>
                        <p>These Terms of Service, and any disputes arising out of or in connection with your use of
                            AgriSys, shall be governed by and construed in accordance with the laws of the Republic of
                            the Philippines, without regard to conflict of law principles.</p>
                        <p>Any dispute, controversy, or claim arising out of or relating to these Terms of Service, the
                            AgriSys Data Privacy Notice, or your use of AgriSys shall, as far as practicable, be
                            resolved amicably through direct consultation and coordination with the City Agriculture
                            Office or mediation or facilitated dialogue with the involvement of the City Government of
                            San Pedro, Laguna.</p>
                        <p>You agree that any legal action or proceeding related to these Terms of Service or AgriSys
                            shall be brought exclusively in the courts of San Pedro, Laguna, or such other courts as may
                            have proper jurisdiction under Philippine law.</p>
                    </div>

                    <!-- 11. Changes to Terms of Service -->
                    <div class="section">
                        <h2>11. Changes to Terms of Service</h2>
                        <p>The City Agriculture Office reserves the right to revise, modify, update, or replace these
                            Terms of Service at any time to reflect changes in applicable laws, updates to AgriSys
                            features, improvements in security and privacy, or clarifications based on user feedback or
                            legal review.</p>
                        <p>All updates to these Terms of Service will be posted on the AgriSys platform (agrisys.site)
                            and may also be posted on the official website of the City Agriculture Office of San Pedro,
                            Laguna. The "Last Updated" date will reflect the most recent revision.</p>
                        <p>Continued use of AgriSys after the posting of updated Terms of Service constitutes your
                            acceptance of and agreement to be bound by the revised terms. If you do not agree with the
                            changes, you must immediately cease using AgriSys and may request termination of your
                            account.</p>
                    </div>

                    <!-- 12. Contact Information -->
                    <div class="section">
                        <h2>12. Contact Information</h2>
                        <p><strong>Data Protection Officer / System Administrator</strong><br>
                            City Agriculture Office<br>
                            San Pedro City Hall<br>
                            San Pedro, Laguna, Philippines</p>
                        <p><strong>Phone:</strong> (049) 8808-2020 Local 109<br>
                            <strong>Email:</strong> agriculture.sanpedrocity@gmail.com
                        </p>
                        <p><strong>Office Hours:</strong><br>
                            Monday to Friday, 8:00 AM - 5:00 PM (Closed on weekends and public holidays)</p>
                    </div>

                    <!-- 13. Acknowledgment and Consent -->
                    <div class="section">
                        <h2>13. Acknowledgment and Consent</h2>
                        <p>By creating an account, logging in, or using any feature of AgriSys, you acknowledge that:
                        </p>
                        <ol>
                            <li>You have read, understood, and agree to be bound by these Terms of Service in their
                                entirety</li>
                            <li>You have read and understood the AgriSys Data Privacy Notice and consent to the
                                collection, use, storage, and sharing of your personal data as described therein</li>
                            <li>You agree to comply with all applicable laws, regulations, and policies while using
                                AgriSys</li>
                            <li>You understand that the City Agriculture Office may modify these Terms at any time, and
                                that continued use of AgriSys constitutes acceptance of such modifications</li>
                            <li>You understand your responsibilities regarding account security, accuracy of
                                information, acceptable use, and confidentiality obligations</li>
                            <li>You acknowledge the limitations of liability and disclaimers set forth in these Terms
                            </li>
                        </ol>
                        <p>If you do not agree with any part of these Terms of Service or the AgriSys Data Privacy
                            Notice, you must not use AgriSys.</p>
                    </div>

                    <div class="section footer-section">
                        <p style="text-align: center; font-style: italic;">Thank you for using AgriSys. The City
                            Agriculture Office of San Pedro, Laguna is committed to modernizing agricultural service
                            delivery and supporting the livelihoods of our local farmers, fisherfolk, and livestock
                            raisers through secure, efficient, and data-driven solutions.</p>
                        <p style="text-align: center; color: #999; font-size: 0.9em;">Effective Date: December 2025 |
                            Last Updated: December 2025</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="close-button" onclick="closeTermsModal()">Close</button>
            </div>
        </div>
    </div>

    <!-- Privacy Policy Modal - UPDATED VERSION -->
    <div id="privacy-modal" class="modal-overlay" style="display: none;">
        <div class="modal-content privacy-modal-content">
            <div class="modal-header">
                <h3>Data Privacy Notice</h3>
                <span class="modal-close" onclick="closePrivacyModal()">&times;</span>
            </div>
            <div class="modal-body">
                <div class="modal-scroll-content">
                    <!-- Introduction -->
                    <div class="section">
                        <h2>Introduction</h2>
                        <p>We, at the City Agriculture Office of San Pedro, Laguna, protect the privacy of your personal
                            and sensitive personal information ("personal data") and commit to comply with the Republic
                            Act No. 10173 or the Data Privacy Act ("DPA"), its Implementing Rules and Regulations
                            ("IRR"), and other relevant issuances of the National Privacy Commission. Our goal is to
                            protect your personal data on the channels you interact with us – through the AgriSys
                            platform (agrisys.site), our office premises, physical and digital forms submitted, and
                            other service delivery touchpoints.</p>
                        <p>This Privacy Notice outlines our approach on how we protect your privacy and explains the
                            purpose and legal basis for the processing of personal data that the City Agriculture Office
                            collects from you through AgriSys, the measures in place to protect your data privacy, and
                            the rights that you may exercise in relation to such information.</p>
                    </div>

                    <!-- Why We Collect and Process Personal Data -->
                    <div class="section">
                        <h2>Why We Collect and Process Personal Data</h2>
                        <p>In general, the City Agriculture Office collects, uses and discloses personal data for the
                            following purposes:</p>
                        <ol>
                            <li>To register and maintain an updated database of farmers, fisherfolk, livestock raisers,
                                and poultry raisers in San Pedro City for the Registry System for Basic Sectors in
                                Agriculture (RSBSA)</li>
                            <li>To process and manage requests for agricultural supplies including seedlings,
                                fertilizers, fingerlings, fruit-bearing trees, and organic fertilizers</li>
                            <li>To monitor agricultural supply inventory levels and generate alerts for timely
                                restocking and equitable distribution</li>
                            <li>To process applications for training programs, seminars, agricultural fairs, and
                                capacity-building activities</li>
                            <li>To generate prescriptive analytics and decision support insights using large language
                                model technology to identify trends in crop demand, supply distribution, and beneficiary
                                needs</li>
                            <li>To conduct comprehensive stakeholder analysis and needs prioritization for agricultural
                                programs</li>
                            <li>To respond to court orders, instructions and requests from authorities including
                                regulatory, governmental, and law enforcement</li>
                            <li>To discharge our mandate pursuant to Philippine laws, Local Government Code provisions,
                                and Department of Agriculture programs</li>
                            <li>To respond to, process and handle your queries, requests, feedback, suggestions and
                                complaints</li>
                            <li>To conduct studies and research for the purpose of reviewing, developing and improving
                                our provision of agricultural services</li>
                            <li>To prepare reports required by the Local Government Unit, Department of Agriculture, and
                                other government agencies</li>
                            <li>To prevent, detect, investigate fraud and manage the safety and security of our premises
                                and digital systems including system activity logs and access controls</li>
                        </ol>
                    </div>

                    <!-- What We May Collect From You -->
                    <div class="section">
                        <h2>What We May Collect From You</h2>
                        <p>The types of personal data we collect and share depend on the means by which it was collected
                            and your interactions with AgriSys. This may include, among others:</p>

                        <h4>Personal Information</h4>
                        <ol>
                            <li>Basic personal information like your name, date of birth, sex/gender, marital status,
                                and citizenship, including supporting documents such as government-issued ID details
                                (front and back images)</li>
                            <li>Your contact details like your complete address, barangay, mobile number, and email
                                address (if available)</li>
                            <li>Agricultural profile information including your sector, primary commodity or crop, farm
                                location and size, type of farming or fishing activity, and livestock and poultry
                                inventory</li>
                            <li>Supporting documents that verify your status as a farmer, fisherfolk, livestock raiser,
                                or poultry raiser (e.g., certificates, permits, land titles, barangay certifications)
                            </li>
                            <li>Transaction records including supply requests, quantities and types of agricultural
                                inputs requested, request dates and approval status, distribution records and
                                acknowledgment receipts</li>
                            <li>Training and seminar participation records, agricultural fair and community event
                                attendance</li>
                            <li>Specimen signatures on acknowledgment receipts and official forms</li>
                            <li>Images via CCTV and other similar recording devices which may be observed when visiting
                                our office and/or using our facilities</li>
                        </ol>

                        <h4>Technical Information</h4>
                        <p>We collect non-personal identification information about users whenever they interact with
                            AgriSys for security and analytics purposes. Non-personal identification information may
                            include IP address, browser type and version, device type (desktop, mobile, tablet), access
                            timestamps, and other technical information used as the means of connection, such as
                            operating system. This type of information cannot be used to identify individual visitors
                            unless necessary for security investigations.</p>
                    </div>

                    <!-- How We May Share Your Data -->
                    <div class="section">
                        <h2>How We May Share Your Data</h2>
                        <p>The City Agriculture Office does not and will not share your personal data with third parties
                            unless necessary for the accomplishment of the above-mentioned purposes and unless you give
                            your consent thereto. Such third parties may include other departments of the San Pedro City
                            Government, agents, outsourced service providers, and other third parties.</p>

                        <h4>Government Agencies</h4>
                        <p>Our third parties include other government agencies/bodies from the executive, legislative
                            and judicial branches of government, including the Department of Agriculture, Philippine
                            Statistics Authority, and other national agencies for statistical reporting, policy
                            development, and program monitoring.</p>

                        <h4>Technology Service Providers</h4>
                        <p>We engage outsourced service providers to achieve utmost support in delivering services to
                            you. AgriSys utilizes the following technology service providers:</p>
                        <ol>
                            <li><strong>Anthropic (Claude Sonnet 4.5):</strong> For large language model analytics and
                                prescriptive decision support insights</li>
                            <li><strong>Hostinger:</strong> For web hosting and server infrastructure</li>
                            <li><strong>Cloudflare:</strong> For content delivery, security, and DDoS protection</li>
                            <li><strong>Philippine Short Message Service Gateway:</strong> For bulk short message
                                service notifications and alerts</li>
                            <li><strong>Simple Mail Transfer Protocol:</strong> For reliable email transmission and
                                communication</li>
                        </ol>
                        <p>Any personal data shared with third parties shall be covered by the appropriate agreement to
                            ensure that all personal data is adequately safeguarded. This City Agriculture Office does
                            not and will not sell personal data to any third party.</p>
                        <p><strong>Note:</strong> While AgriSys currently does not integrate with other government
                            platforms, future updates may enable data sharing with relevant agencies to improve service
                            coordination. Any such integration will be implemented in accordance with data protection
                            laws, and you will be notified of significant changes to our data sharing practices.</p>
                    </div>

                    <!-- How We Protect Your Data -->
                    <div class="section">
                        <h2>How We Protect Your Data</h2>
                        <p>The City Agriculture Office implements technical, organizational and physical security
                            measures to protect your personal data against loss, misuse, modification, unauthorized or
                            accidental access or disclosure, alteration or destruction. We put the following safeguards
                            in place:</p>
                        <ol>
                            <li>Role-based access control with encrypted login credentials ensuring only authorized City
                                Agriculture Office personnel can access AgriSys</li>
                            <li>Use of a secured server behind a firewall with intrusion detection systems</li>
                            <li>Deployment of encryption on computers and devices, with all passwords encrypted using
                                industry-standard hashing algorithms and secure HTTPS protocols for data transmission
                            </li>
                            <li>Use of antivirus and malware protection with updated security patches</li>
                            <li>Regular automated backups to ensure data can be restored in case of system failure or
                                data loss</li>
                            <li>System activity logs tracking all data access and modifications for audit and
                                accountability purposes</li>
                            <li>Establishment of physical security controls including restricted access to office
                                premises and secure storage of paper records in locked cabinets</li>
                        </ol>
                        <p>Moreover, we restrict access to your personal data only to qualified and authorized City
                            Agriculture Office personnel who hold your personal data with strict confidentiality. We
                            likewise train our employees to properly handle your data and comply with data privacy
                            principles and security protocols.</p>
                        <p>Despite these measures, no system can guarantee absolute security. While we strive to protect
                            your data using industry best practices, we encourage you to safeguard your own account
                            credentials and report any suspected security incidents to our office immediately.</p>
                    </div>

                    <!-- How We Store and Dispose Your Data -->
                    <div class="section">
                        <h2>How We Store and Dispose Your Data</h2>
                        <p>The City Agriculture Office retains and stores your personal data only according to
                            operational needs and in compliance with its legal mandates. Our data retention and disposal
                            policy is in accordance with Republic Act No. 9470 or the National Archives of the
                            Philippines Act and applicable laws. Generally, this City Agriculture Office shall only
                            retain your data for five (5) years after the processing relevant to the purpose has been
                            terminated or after your last transaction with our office.</p>

                        <h4>Extended Retention</h4>
                        <p>However, this City Agriculture Office may retain and store your personal data beyond five
                            years when:</p>
                        <ol>
                            <li>Required by law or regulation</li>
                            <li>Necessary to establish, exercise or defend legal claims</li>
                            <li>Needed for ongoing agricultural programs or longitudinal studies</li>
                            <li>You remain an active beneficiary receiving services from the City Agriculture Office
                            </li>
                            <li>When provided by law for legitimate purposes</li>
                        </ol>

                        <h4>Data Disposal</h4>
                        <p>When your personal data is no longer necessary to achieve any of the above-mentioned
                            purposes, this City Agriculture Office destroys, deletes or disposes of the same following
                            the security standards. Digital records are securely deleted from AgriSys databases and
                            backup systems using data erasure methods that prevent recovery. Paper documents and
                            physical records are shredded or incinerated following secure disposal protocols. Data used
                            for statistical or research purposes may be anonymized or de-identified so that it can no
                            longer identify you personally.</p>
                    </div>

                    <!-- Your Data Privacy Rights -->
                    <div class="section">
                        <h2>Your Data Privacy Rights</h2>
                        <p>Under the DPA, you have the following rights:</p>

                        <h4>1. Right to be Informed</h4>
                        <p>You have the right to be informed about how your personal data is being collected, used, and
                            shared. This Privacy Notice serves that purpose.</p>

                        <h4>2. Right to Object</h4>
                        <p>You have the right to object to the processing of your personal data, including processing
                            for direct marketing purposes, subject to limitations when processing is required by law or
                            necessary for the performance of official functions.</p>

                        <h4>3. Right to Access</h4>
                        <p>You have the right to reasonable access to your personal data held by AgriSys, including
                            basic profile information, supply request history, training records, and documents you
                            submitted.</p>

                        <h4>4. Right to Rectify or Correct Erroneous Data</h4>
                        <p>If you believe that your personal data is inaccurate, incomplete, or outdated, you have the
                            right to request correction or update with supporting documentation.</p>

                        <h4>5. Right to Erase or Block</h4>
                        <p>You have the right to request deletion or blocking of your personal data under certain
                            circumstances, subject to limitations when we are required to retain your data for legal
                            compliance, ongoing programs, or to fulfill our mandate as a government office.</p>

                        <h4>6. Right to Secure Data Portability</h4>
                        <p>You have the right to obtain a copy of your personal data in a structured, commonly used, and
                            machine-readable format (e.g., PDF, Excel).</p>

                        <h4>7. Right to be Indemnified for Damages</h4>
                        <p>You have the right to be indemnified for any damages sustained due to inaccurate, incomplete,
                            outdated, false, unlawfully obtained, or unauthorized use of your personal data.</p>

                        <h4>8. Right to File a Complaint</h4>
                        <p>If you believe your data privacy rights have been violated, you have the right to file a
                            complaint with the City Agriculture Office Data Protection Officer or the National Privacy
                            Commission.</p>

                        <h4>How to Exercise Your Rights</h4>
                        <p>To exercise any of these rights, submit a written request to our Data Protection Officer
                            (contact details below). We will respond to your request within a reasonable timeframe,
                            typically within 15 to 30 days, and may require identity verification to protect your data
                            from unauthorized access.</p>
                    </div>

                    <!-- Children's Privacy -->
                    <div class="section">
                        <h2>Children's Privacy</h2>
                        <p>AgriSys is designed for use by adult beneficiaries (18 years and older) who are farmers,
                            fisherfolk, livestock raisers, or their authorized representatives. We do not knowingly
                            collect personal data from minors (individuals under 18 years of age) without proper consent
                            from a parent or legal guardian.</p>
                        <p>If you are under 18 and wish to register as a beneficiary, please ensure that your parent or
                            legal guardian completes the registration on your behalf and consents to the processing of
                            your data.</p>
                        <p>If we become aware that we have collected personal data from a minor without appropriate
                            consent, we will take steps to delete such information promptly.</p>
                    </div>

                    <!-- Changes to the Privacy Notice -->
                    <div class="section">
                        <h2>Changes to the Privacy Notice</h2>
                        <p>The City Agriculture Office may amend this Privacy Notice to reflect relevant updates and
                            ensure conformity with legal and regulatory requirements for processing personal data.</p>

                        <h4>Notification of Changes</h4>
                        <ol>
                            <li>All updates will be posted on the AgriSys website (agrisys.site) and the official
                                website of the San Pedro City Agriculture Office</li>
                            <li>The "Last Updated" date will reflect the most recent changes</li>
                            <li>If we make material changes that significantly affect how we collect, use, or share your
                                data, we will notify you through AgriSys notifications, email (if available), or
                                announcements at our office</li>
                        </ol>

                        <p>Your continued use of AgriSys after the posting of updated privacy terms constitutes your
                            acceptance of the revised policy. We encourage you to review this Privacy Notice
                            periodically to stay informed about how we protect your data.</p>
                    </div>

                    <!-- Limitations of This Privacy Notice -->
                    <div class="section">
                        <h2>Limitations of This Privacy Notice</h2>
                        <p>AgriSys may contain links to websites of government departments and other organizations.
                            However, this Privacy Notice applies only to the City Agriculture Office of San Pedro,
                            Laguna and the AgriSys platform.</p>
                        <p>You are encouraged and advised to read the respective privacy notices of other
                            agencies/offices when accessing external platforms or services.</p>
                    </div>

                    <!-- How You May Contact Us -->
                    <div class="section">
                        <h2>How You May Contact Us</h2>
                        <p>For further privacy-related inquiries or complaints, you may contact our Data Protection
                            Officer:</p>

                        <p><strong>Data Protection Officer</strong><br>
                            City Agriculture Office<br>
                            San Pedro City Hall<br>
                            San Pedro, Laguna, Philippines</p>

                        <p><strong>Email:</strong> agriculture.sanpedrocity@gmail.com<br>
                            <strong>Phone:</strong> (049) 8808-2020 Local 109<br>
                            <strong>Office Hours:</strong> Monday to Friday, 8:00 AM - 5:00 PM (Closed on weekends and
                            public holidays)
                        </p>

                        <h4>For Data Privacy Complaints</h4>
                        <p>For complaints regarding data privacy violations, you may also contact the National Privacy
                            Commission:</p>

                        <p><strong>Website:</strong> https://privacy.gov.ph<br>
                            <strong>Email:</strong> info@privacy.gov.ph or complaints@privacy.gov.ph<br>
                            <strong>Hotline:</strong> (02) 8234-2228
                        </p>
                    </div>

                    <!-- Acknowledgment and Consent -->
                    <div class="section footer-section">
                        <h2>Acknowledgment and Consent</h2>
                        <p>By registering with and using AgriSys, you acknowledge that you have read and understood this
                            Privacy Notice and consent to the collection, use, storage, and sharing of your personal
                            data as described herein.</p>
                        <p style="text-align: center; color: #999; font-size: 0.9em;">Effective Date: December 2025 |
                            Last Updated: December 2025</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="close-button" onclick="closePrivacyModal()">Close</button>
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


            </div>

            <!-- Services column -->
            <div class="footer-column">
                <h3>Our Services</h3>
                <ul>
                    <li><a href="#services">RSBSA Registration</a></li>
                    <li><a href="#services">Supplies & Garden Tools</a></li>
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
                <p style="margin-top: 12px;">Phone: 8808-2020 Local 109<br>
                    Email: <a href="mailto:agriculture.sanpedrocity@gmail.com">agriculture.sanpedrocity@gmail.com</a>
                </p>
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
                <a href="{{ route('privacy-policy') }}" target="_blank" class="footer-link">Privacy Policy</a>
                <a href="{{ route('terms-of-service') }}" target="_blank" class="footer-link">Terms of
                    Service</a>
            </div>
        </div>
    </footer>

    <!-- Toast Notification System - MUST LOAD FIRST (used by all other scripts) -->
    <script src="{{ asset('js/toast-notifications.js') }}?v={{ config('app.asset_version') }}"></script>

    
    <script src="{{ asset('js/events-loader.js') }}?v={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('js/slideshow.js') }}?v={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('js/landing.js') }}?v={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('js/submission-service.js') }}?v={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('js/seedlings.js') }}?v={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('js/rsbsa.js') }}?v={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('js/fishr.js') }}?v={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('js/boatr.js') }}?v={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('js/training.js') }}?v={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('js/auth.js') }}?v={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('js/session-manager.js') }}?v={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('js/rsbsa-autofill.js') }}?v={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('js/training-autofill.js') }}?v={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('js/fishr-autofill.js') }}?v={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('js/boatr-autofill.js') }}?v={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('js/seedlings-autofill.js') }}?v={{ config('app.asset_version') }}"></script>
    <script src="{{ asset('js/my-applications-modal.js') }}?v={{ config('app.asset_version') }}"></script>

    <!-- Mobile Navigation Script -->
    <script>
        // Mobile Navigation Functions
        function toggleMobileNav() {
            const menu = document.getElementById('mobileNavMenu');
            const overlay = document.getElementById('mobileNavOverlay');
            const isActive = menu.classList.contains('active');

            if (isActive) {
                closeMobileNav();
            } else {
                openMobileNav();
            }
        }

        function openMobileNav() {
            const menu = document.getElementById('mobileNavMenu');
            const overlay = document.getElementById('mobileNavOverlay');

            menu.classList.add('active');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeMobileNav() {
            const menu = document.getElementById('mobileNavMenu');
            const overlay = document.getElementById('mobileNavOverlay');

            menu.classList.remove('active');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        // Close mobile nav when pressing Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeMobileNav();
            }
        });

        // Close mobile nav when window is resized to desktop size
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                closeMobileNav();
            }
        });


        // privacy policy and terms of service

        // Terms Modal Functions
        function openTermsModal(event) {
            event.preventDefault();
            document.getElementById('terms-modal').style.display = 'flex';
        }

        function closeTermsModal() {
            document.getElementById('terms-modal').style.display = 'none';
        }

        // Privacy Modal Functions
        function openPrivacyModal(event) {
            event.preventDefault();
            document.getElementById('privacy-modal').style.display = 'flex';
        }

        function closePrivacyModal() {
            document.getElementById('privacy-modal').style.display = 'none';
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(e) {
            const termsModal = document.getElementById('terms-modal');
            const privacyModal = document.getElementById('privacy-modal');

            if (e.target === termsModal) {
                closeTermsModal();
            }
            if (e.target === privacyModal) {
                closePrivacyModal();
            }
        });

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeTermsModal();
                closePrivacyModal();
            }
        });
    </script>
</body>

</html>
