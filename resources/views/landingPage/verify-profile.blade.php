<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Profile Verification – AgriSys</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logos/cago_web.png') }}">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}?v={{ config('app.asset_version') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}?v={{ config('app.asset_version') }}">
    <link rel="stylesheet" href="{{ asset('css/toast-notifications.css') }}?v={{ config('app.asset_version') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        /* Language Selector Styling */
        .language-selector {
            margin-right: 15px;
            display: inline-flex;
            align-items: center;
            position: relative;
        }

        .lang-dropdown-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            border: 2px solid #4CAF50;
            border-radius: 25px;
            background-color: white;
            color: #333;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            outline: none;
            transition: all 0.3s ease;
            min-width: 140px;
        }

        .lang-dropdown-btn:hover {
            background-color: #f0f9f0;
            box-shadow: 0 3px 8px rgba(76, 175, 80, .2);
        }

        .lang-dropdown-btn.active {
            background-color: #4CAF50;
            color: white;
            border-color: #4CAF50;
        }

        .lang-dropdown-btn.active .lang-globe-icon,
        .lang-dropdown-btn.active .lang-chevron {
            color: white;
        }

        .lang-globe-icon {
            font-size: 20px;
            color: #4CAF50;
            display: flex;
            align-items: center;
        }

        .lang-text {
            flex: 1;
            text-align: left;
        }

        .lang-chevron {
            font-size: 18px;
            color: #4CAF50;
            transition: transform 0.3s ease;
        }

        .lang-dropdown-btn.open .lang-chevron {
            transform: rotate(180deg);
        }

        .lang-dropdown-menu {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            background: white;
            border: 2px solid #4CAF50;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .15);
            min-width: 140px;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }

        .lang-dropdown-menu.open {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .lang-option {
            padding: 12px 20px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            font-weight: 500;
            color: #333;
        }

        .lang-option:first-child {
            border-radius: 10px 10px 0 0;
        }

        .lang-option:last-child {
            border-radius: 0 0 10px 10px;
        }

        .lang-option:hover {
            background-color: #f0f9f0;
        }

        .lang-option.active {
            background-color: #4CAF50;
            color: white;
        }

        .lang-option .check-icon {
            margin-left: auto;
            font-size: 16px;
            color: #4CAF50;
        }

        .lang-option.active .check-icon {
            color: white;
        }

        #google_translate_element,
        #google_translate_element *,
        .goog-te-banner-frame.skiptranslate,
        .goog-te-gadget,
        .goog-te-gadget-simple,
        .goog-te-gadget-icon,
        .goog-te-combo,
        .goog-logo-link,
        .goog-te-gadget span,
        .goog-te-menu-value,
        .goog-te-menu-value span,
        .goog-te-balloon-frame,
        div#goog-gt-,
        .goog-te-spinner-pos {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            height: 0 !important;
            width: 0 !important;
            overflow: hidden !important;
        }

        body {
            top: 0 !important;
        }

        body.translated-ltr {
            top: 0 !important;
        }

        .skiptranslate,
        .goog-te-banner-frame {
            display: none !important;
        }

        @media (max-width: 768px) {
            .language-selector {
                margin-right: 6px;
            }

            .lang-dropdown-btn {
                padding: 6px 12px;
                font-size: 11px;
                min-width: 95px;
                gap: 6px;
                border-width: 1.5px;
            }

            .lang-globe-icon {
                font-size: 14px;
            }

            .lang-chevron {
                font-size: 12px;
            }

            .lang-dropdown-menu {
                min-width: 95px;
                right: 0;
                left: auto;
            }

            .lang-option {
                padding: 9px 12px;
                font-size: 11px;
            }
        }

        @media (max-width: 380px) {
            .lang-dropdown-btn {
                padding: 5px 8px;
                font-size: 0;
                min-width: 40px;
                gap: 0;
            }

            .lang-text {
                display: none;
            }

            .lang-chevron {
                display: none;
            }

            .lang-globe-icon {
                font-size: 16px;
            }
        }

        /* ── Page layout ── */
        body {
            background: #f4f7f4;
        }

        .verify-page {
            max-width: 820px;
            margin: 40px auto 60px;
            padding: 0 20px;
        }

        /* ── Page header ── */
        .verify-page-header {
            text-align: center;
            margin-bottom: 36px;
        }

        .verify-page-header .icon-wrapper {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: #e8f5e9;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }

        .verify-page-header h1 {
            font-size: 28px;
            font-weight: 700;
            color: #0A6953;
            margin: 0 0 8px;
        }

        .verify-page-header p {
            font-size: 15px;
            color: #666;
            margin: 0;
        }

        /* ── Card ── */
        .verify-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, .07);
            padding: 36px 40px;
            margin-bottom: 24px;
        }

        @media (max-width: 600px) {
            .verify-card {
                padding: 24px 18px;
            }
        }

        /* ── Section headings ── */
        .section-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e8f5e9;
        }

        .section-header svg {
            color: #0A6953;
            flex-shrink: 0;
        }

        .section-header h5 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
            color: #0A6953;
        }

        .section-description {
            font-size: 13px;
            color: #777;
            margin: -12px 0 16px;
        }

        /* ── Form helpers ── */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        @media (max-width: 600px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #444;
            margin-bottom: 6px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            color: #1a1a1a;
            background: #fafafa;
            transition: border-color .2s, box-shadow .2s;
            outline: none;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #0A6953;
            box-shadow: 0 0 0 3px rgba(10, 105, 83, .12);
            background: #fff;
        }

        .form-group input.is-invalid,
        .form-group select.is-invalid,
        .form-group textarea.is-invalid {
            border-color: #ef4444;
        }

        .form-group small {
            display: block;
            font-size: 11px;
            color: #999;
            margin-top: 4px;
        }

        .invalid-feedback {
            display: block;
            color: #ef4444;
            font-size: 12px;
            margin-top: 4px;
        }

        /* ── File upload ── */
        .file-input {
            display: none !important;
        }

        .file-upload-area {
            border: 2px dashed #b2dfdb;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            background: #f0faf8;
            transition: border-color .2s, background .2s;
        }

        .file-upload-area:hover {
            border-color: #0A6953;
            background: #e8f5e9;
        }

        .upload-icon {
            color: #0A6953;
            margin-bottom: 6px;
        }

        .upload-text {
            font-size: 13px;
            color: #555;
        }

        .image-preview {
            margin-top: 10px;
            border-radius: 8px;
            overflow: hidden;
            border: 2px solid #b2dfdb;
        }

        .image-preview img {
            width: 100%;
            max-height: 180px;
            object-fit: cover;
            display: block;
        }

        .form-text {
            margin-top: 10px;
            font-size: 12px;
            color: #777;
            line-height: 1.6;
            background: #f9fafb;
            padding: 10px 12px;
            border-radius: 8px;
            border-left: 3px solid #0A6953;
        }

        /* ── Notice banner ── */
        .verification-notice {
            display: flex;
            gap: 14px;
            background: #e8f5e9;
            border: 1px solid #b2dfdb;
            border-radius: 10px;
            padding: 16px 18px;
            margin: 24px 0 28px;
            align-items: flex-start;
        }

        .notice-icon {
            color: #0A6953;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .notice-content h6 {
            margin: 0 0 4px;
            font-size: 14px;
            font-weight: 600;
            color: #0A6953;
        }

        .notice-content p {
            margin: 0;
            font-size: 13px;
            color: #444;
            line-height: 1.5;
        }

        /* ── Submit button ── */
        .verification-submit-btn {
            width: 100%;
            padding: 15px;
            background: #0A6953;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background .2s, transform .1s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .verification-submit-btn:hover:not(:disabled) {
            background: #07584a;
        }

        .verification-submit-btn:active:not(:disabled) {
            transform: scale(.99);
        }

        .verification-submit-btn:disabled {
            opacity: .7;
            cursor: not-allowed;
        }

        /* ── Success state ── */
        .success-overlay {
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 60px 40px;
        }

        .success-overlay .success-icon-wrap {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #e8f5e9;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 24px;
        }

        .success-overlay h2 {
            font-size: 24px;
            color: #0A6953;
            margin: 0 0 10px;
        }

        .success-overlay p {
            font-size: 15px;
            color: #555;
            margin: 0 0 28px;
            max-width: 420px;
        }

        .success-overlay .go-home-btn {
            padding: 13px 36px;
            background: #0A6953;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background .2s;
        }

        .success-overlay .go-home-btn:hover {
            background: #07584a;
        }
    </style>
</head>

<body>
    <!-- Mobile Navigation Overlay -->
    <div class="mobile-nav-overlay" id="mobileNavOverlay" onclick="closeMobileNav()"></div>

    <!-- Mobile Navigation Menu -->
    <nav class="mobile-nav-menu" id="mobileNavMenu">
        <div class="mobile-nav-header">
            <span class="mobile-nav-title">Navigation</span>
            <button class="mobile-nav-close" onclick="closeMobileNav()" aria-label="Close menu">&times;</button>
        </div>

        @if (isset($user))
            <div class="mobile-nav-user-section">
                <div class="mobile-nav-user-avatar"><i class="fas fa-user-circle"></i></div>
                <div class="mobile-nav-user-info">
                    <div class="mobile-nav-user-name">{{ $user['name'] ?? $user['username'] }}</div>
                </div>
            </div>
        @endif

        <div class="mobile-nav-items">
            <a href="{{ url('/') }}" class="mobile-nav-item"><i class="fas fa-home"></i> Home</a>
            <a href="{{ url('/') }}#services" class="mobile-nav-item"><i class="fas fa-tools"></i> Supplies &amp;
                Garden Tools</a>
            <a href="{{ url('/') }}#services" class="mobile-nav-item"><i class="fas fa-file-alt"></i> RSBSA
                Application</a>
            <a href="{{ url('/') }}#services" class="mobile-nav-item"><i class="fas fa-fish"></i> FishR
                Registration</a>
            <a href="{{ url('/') }}#services" class="mobile-nav-item"><i class="fas fa-ship"></i> BoatR
                Registration</a>
            <a href="{{ url('/') }}#services" class="mobile-nav-item"><i class="fas fa-chalkboard-teacher"></i>
                Training Request</a>

            @if (isset($user))
                <div class="mobile-nav-divider"></div>
                <a href="{{ url('/') }}" class="mobile-nav-item mobile-nav-logout"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            @endif
        </div>
    </nav>

    <header>
        <div class="header-left">
            <div class="logo-text">
                <div class="logo-container">
                    <img src="{{ asset('images/logos/agrii-removebg.png') }}" alt="AgriSys Logo" class="main-logo">
                </div>
            </div>

            <!-- Desktop Navigation -->
            <div class="header-center nav-buttons">
                <a href="{{ url('/') }}" class="btn">Home</a>
                <a href="{{ url('/') }}#services" class="btn">Supplies &amp; Garden Tools</a>
                <a href="{{ url('/') }}#services" class="btn">RSBSA</a>
                <a href="{{ url('/') }}#services" class="btn">FishR</a>
                <a href="{{ url('/') }}#services" class="btn">BoatR</a>
                <a href="{{ url('/') }}#services" class="btn">Training</a>
            </div>

            <div class="header-right auth-buttons">
                <!-- Language Selector -->
                <div class="language-selector">
                    <button class="lang-dropdown-btn" onclick="toggleLangDropdown()" id="langDropdownBtn">
                        <span class="lang-globe-icon"><i class="fas fa-globe"></i></span>
                        <span class="lang-text" id="currentLangText">English</span>
                        <span class="lang-chevron"><i class="fas fa-chevron-down"></i></span>
                    </button>
                    <div class="lang-dropdown-menu" id="langDropdownMenu">
                        <div class="lang-option active" onclick="selectLanguage('en', 'English')" data-lang="en">
                            English <span class="check-icon"><i class="fas fa-check"></i></span>
                        </div>
                        <div class="lang-option" onclick="selectLanguage('tl', 'Filipino')" data-lang="tl">
                            Filipino
                        </div>
                    </div>
                </div>
                <div id="google_translate_element"></div>

                <!-- Mobile Menu Toggle -->
                <button class="mobile-menu-toggle" onclick="toggleMobileNav()" aria-label="Toggle navigation menu">
                    <span>&#9776;</span>
                </button>

                @if (isset($user))
                    <div class="user-profile" id="user-profile" onclick="toggleUserDropdown()">
                        <div class="user-avatar">
                            {{ strtoupper(substr($user['name'] ?? $user['username'], 0, 1)) }}
                        </div>
                        <div class="user-info">
                            <div class="user-name">
                                @php $firstName = explode(' ', $user['name'] ?? $user['username'])[0]; @endphp
                                {{ $firstName }}
                            </div>
                            <div class="user-status" id="header-user-status">
                                @php $statusLower = strtolower($user['status'] ?? ''); @endphp
                                <span id="status-text" class="status-badge-text">
                                    @if ($statusLower === 'approved' || $statusLower === 'verified')
                                        Verified
                                    @elseif($statusLower === 'pending' || $statusLower === 'pending_verification')
                                        Under Review
                                    @elseif($statusLower === 'rejected')
                                        Verification Failed
                                    @elseif($statusLower === 'unverified')
                                        Not Verified
                                    @else
                                        {{ ucfirst($user['status'] ?? 'Active') }}
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="dropdown-arrow">▼</div>

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
                                <a href="{{ url('/') }}" class="dropdown-item">
                                    <svg class="dropdown-icon" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                    Back to Home
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="#" class="dropdown-item logout"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
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
                    <a href="{{ url('/') }}" class="btn btn-login">Log in</a>
                    <a href="{{ url('/') }}" class="btn btn-signup">Sign Up</a>
                @endif
            </div>
        </div>
    </header>

    <!-- Hidden logout form -->
    @if (isset($user))
        <form id="logout-form" action="{{ route('auth.logout') }}" method="POST" style="display:none;">
            @csrf
        </form>
    @endif

    <div class="verify-page">

        <!-- Page header -->
        <div class="verify-page-header">
            <div class="icon-wrapper">
                <svg width="40" height="40" viewBox="0 0 48 48" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path d="M24 4C12.95 4 4 12.95 4 24s8.95 20 20 20 20-8.95 20-20-8.95-20-20-20z" stroke="#0A6953"
                        stroke-width="2" fill="none" />
                    <path d="M20 32l-6-6 1.41-1.41L20 29.18l10.59-10.59L32 20l-12 12z" fill="#0A6953" />
                </svg>
            </div>
            <h1>Complete Your Profile Verification</h1>
            <p>Please provide the following information to verify your account and access all services.</p>
        </div>

        <!-- Form card (hidden on success) -->
        <div class="verify-card" id="verify-form-card">
            <form id="verification-form" enctype="multipart/form-data">
                @csrf

                <!-- Personal Information -->
                <div class="section-header">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    <h5>Personal Information</h5>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="firstName">First Name <span style="color:#ef4444">*</span></label>
                        <input type="text" id="firstName" name="firstName" required
                            placeholder="Enter your first name">
                        <small>Letters and spaces only</small>
                    </div>
                    <div class="form-group">
                        <label for="middleName">Middle Name <span
                                style="color:#999;font-weight:400">(Optional)</span></label>
                        <input type="text" id="middleName" name="middleName"
                            placeholder="Enter your middle name">
                        <small>Letters and spaces only</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="lastName">Last Name <span style="color:#ef4444">*</span></label>
                        <input type="text" id="lastName" name="lastName" required
                            placeholder="Enter your last name">
                        <small>Letters and spaces only</small>
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
                        <label for="dateOfBirth">Date of Birth <span style="color:#ef4444">*</span></label>
                        <input type="date" id="dateOfBirth" name="dateOfBirth" required>
                    </div>
                    <div class="form-group">
                        <label for="age">Age</label>
                        <input type="number" id="age" name="age" min="18" max="100" readonly
                            placeholder="Auto-calculated">
                        <small>Calculated automatically from date of birth</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="sex">Sex <span style="color:#ef4444">*</span></label>
                        <select id="sex" name="sex" required>
                            <option value="">Select Sex</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Preferred not to say">Preferred not to say</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="role">Sector <span style="color:#ef4444">*</span></label>
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

                <!-- Address Information -->
                <div class="section-header" style="margin-top:8px">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                    <h5>Address Information</h5>
                </div>

                <div class="form-group">
                    <label for="barangay">Barangay <span style="color:#ef4444">*</span></label>
                    <select id="barangay" name="barangay" required>
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

                <div class="form-group">
                    <label for="completeAddress">Complete Address <span style="color:#ef4444">*</span></label>
                    <textarea id="completeAddress" name="completeAddress" required rows="3"
                        placeholder="Enter your complete address (House No., Street, Subdivision, etc.)"></textarea>
                </div>

                <!-- Emergency Contact -->
                <div class="section-header" style="margin-top:8px">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <path
                            d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.21 12.8a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z">
                        </path>
                    </svg>
                    <h5>Emergency Contact</h5>
                </div>
                <p class="section-description">Provide a contact person we can reach in case of emergency.</p>

                <div class="form-row">
                    <div class="form-group">
                        <label for="emergencyContactName">Emergency Contact Name <span
                                style="color:#ef4444">*</span></label>
                        <input type="text" id="emergencyContactName" name="emergencyContactName" required
                            placeholder="Full name of emergency contact">
                        <small>Letters and spaces only</small>
                    </div>
                    <div class="form-group">
                        <label for="emergencyContactPhone">Emergency Contact Phone <span
                                style="color:#ef4444">*</span></label>
                        <input type="tel" id="emergencyContactPhone" name="emergencyContactPhone" required
                            placeholder="09123456789" pattern="[0-9]{11}">
                        <small>11-digit Philippine mobile number</small>
                    </div>
                </div>

                <!-- Document Uploads -->
                <div class="section-header" style="margin-top:8px">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                    </svg>
                    <h5>Required Documents</h5>
                </div>
                <p class="section-description">Please upload clear, high-quality images. Supported: JPG, PNG (Max 10MB
                    each).</p>

                <div class="form-row">
                    <div class="form-group file-upload-group">
                        <label for="idFront">Government ID (Front) <span style="color:#ef4444">*</span></label>
                        <input type="file" id="idFront" name="idFront" required accept="image/*"
                            class="file-input" onchange="previewImage(this, 'idFrontPreview')">
                        <div class="file-upload-area" onclick="document.getElementById('idFront').click()">
                            <div class="upload-icon">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="14" rx="2"></rect>
                                    <path d="M3 18h18"></path>
                                </svg>
                            </div>
                            <div class="upload-text">Click to upload ID front</div>
                        </div>
                        <div id="idFrontPreview" class="image-preview" style="display:none;"></div>
                    </div>

                    <div class="form-group file-upload-group">
                        <label for="idBack">Government ID (Back) <span style="color:#ef4444">*</span></label>
                        <input type="file" id="idBack" name="idBack" required accept="image/*"
                            class="file-input" onchange="previewImage(this, 'idBackPreview')">
                        <div class="file-upload-area" onclick="document.getElementById('idBack').click()">
                            <div class="upload-icon">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="14" rx="2"></rect>
                                    <path d="M3 18h18"></path>
                                </svg>
                            </div>
                            <div class="upload-text">Click to upload ID back</div>
                        </div>
                        <div id="idBackPreview" class="image-preview" style="display:none;"></div>
                    </div>
                </div>

                <div class="form-group file-upload-group">
                    <label for="locationProof">Location / Role Proof <span style="color:#ef4444">*</span></label>
                    <input type="file" id="locationProof" name="locationProof" required accept="image/*"
                        class="file-input" onchange="previewImage(this, 'locationProofPreview')">
                    <div class="file-upload-area" onclick="document.getElementById('locationProof').click()">
                        <div class="upload-icon">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                        </div>
                        <div class="upload-text">Click to upload location/role proof</div>
                    </div>
                    <div id="locationProofPreview" class="image-preview" style="display:none;"></div>
                    <div class="form-text">
                        <strong>Farmers:</strong> Photo of your farm or agricultural land<br>
                        <strong>Fisherfolk:</strong> Photo of fishing area or boat<br>
                        <strong>Others:</strong> Any document or photo that proves your livelihood or business
                    </div>
                </div>

                <!-- Notice -->
                <div class="verification-notice">
                    <div class="notice-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="16" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                        </svg>
                    </div>
                    <div class="notice-content">
                        <h6>Verification Process</h6>
                        <p>Your submitted documents will be reviewed within 1–3 business days. You will receive an SMS
                            notification once your verification is approved or if additional documents are required.</p>
                    </div>
                </div>

                <button type="submit" class="verification-submit-btn" id="verifySubmitBtn">
                    <span class="btn-text">Submit for Verification</span>
                    <span class="btn-loader" style="display:none;">
                        <span
                            style="display:inline-block;width:16px;height:16px;border:3px solid #fff;border-top-color:transparent;border-radius:50%;animation:spin .8s linear infinite;"></span>
                    </span>
                </button>
            </form>
        </div>

        <!-- Success state (shown after submission) -->
        <div class="verify-card success-overlay" id="success-card">
            <div class="success-icon-wrap">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#0A6953"
                    stroke-width="2.5">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
            </div>
            <h2>Verification Submitted!</h2>
            <p>Your profile has been submitted for review. We will notify you via SMS within 1–3 business days once your
                account is verified.</p>
            <a href="{{ url('/') }}" class="go-home-btn">Back to Home</a>
        </div>
    </div>

    <script src="{{ asset('js/toast-notifications.js') }}?v={{ config('app.asset_version') }}"></script>

    <style>
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>

    <script>
        // ── Image preview helper ──────────────────────────────────
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            if (!preview) return;
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // ── Auto-calculate age from DOB ───────────────────────────
        document.getElementById('dateOfBirth').addEventListener('change', function() {
            const dob = new Date(this.value);
            const today = new Date();
            let age = today.getFullYear() - dob.getFullYear();
            const m = today.getMonth() - dob.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) age--;
            const ageInput = document.getElementById('age');
            if (ageInput && age >= 0) ageInput.value = age;
        });

        // ── Validation helpers ────────────────────────────────────
        function showFieldError(fieldName, message) {
            const field = document.querySelector(`[name="${fieldName}"]`);
            if (!field) return;
            field.classList.add('is-invalid');
            const existing = field.parentElement.querySelector('.invalid-feedback');
            if (existing) existing.remove();
            const err = document.createElement('div');
            err.className = 'invalid-feedback';
            err.textContent = message;
            field.parentElement.appendChild(err);
        }

        function clearErrors() {
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
        }

        function validateName(value, label) {
            if (!value || !value.trim()) return `${label} is required`;
            if (value.trim().length < 2) return `${label} must be at least 2 characters`;
            if (!/^[A-Za-zÑñ\s]+$/.test(value.trim())) return `${label} can only contain letters and spaces`;
            return null;
        }

        function validateRequired(value, label) {
            return (!value || !value.trim()) ? `${label} is required` : null;
        }

        function validatePhone(value) {
            if (!value || !value.trim()) return 'Emergency contact phone is required';
            if (!/^09\d{9}$/.test(value.trim())) return 'Must be a valid 11-digit Philippine mobile number (09XXXXXXXXX)';
            return null;
        }

        function validateDOB(value) {
            if (!value) return 'Date of birth is required';
            const dob = new Date(value);
            const today = new Date();
            if (dob >= today) return 'Date of birth cannot be today or in the future';
            const age = Math.floor((today - dob) / (365.25 * 24 * 60 * 60 * 1000));
            if (age < 18) return 'You must be at least 18 years old';
            if (age > 100) return 'Date of birth cannot be more than 100 years ago';
            return null;
        }

        function validateFile(input, label) {
            if (!input.files || input.files.length === 0) return `${label} is required`;
            const file = input.files[0];
            if (file.size > 10 * 1024 * 1024) return `${label} must not exceed 10MB`;
            return null;
        }

        // ── Form submission ───────────────────────────────────────
        document.getElementById('verification-form').addEventListener('submit', function(e) {
            e.preventDefault();
            clearErrors();

            const f = this;
            let valid = true;
            let firstInvalid = null;

            const checks = [
                ['firstName', validateName(f.querySelector('[name="firstName"]').value, 'First Name')],
                ['lastName', validateName(f.querySelector('[name="lastName"]').value, 'Last Name')],
                ['middleName', (() => {
                    const v = f.querySelector('[name="middleName"]').value;
                    return (v && v.trim()) ? validateName(v, 'Middle Name') : null;
                })()],
                ['sex', validateRequired(f.querySelector('[name="sex"]').value, 'Sex')],
                ['role', validateRequired(f.querySelector('[name="role"]').value, 'Sector')],
                ['dateOfBirth', validateDOB(f.querySelector('[name="dateOfBirth"]').value)],
                ['barangay', validateRequired(f.querySelector('[name="barangay"]').value, 'Barangay')],
                ['completeAddress', validateRequired(f.querySelector('[name="completeAddress"]').value,
                    'Complete Address')],
                ['emergencyContactName', validateName(f.querySelector('[name="emergencyContactName"]').value,
                    'Emergency Contact Name')],
                ['emergencyContactPhone', validatePhone(f.querySelector('[name="emergencyContactPhone"]')
                    .value)],
                ['idFront', validateFile(f.querySelector('[name="idFront"]'), 'Government ID (Front)')],
                ['idBack', validateFile(f.querySelector('[name="idBack"]'), 'Government ID (Back)')],
                ['locationProof', validateFile(f.querySelector('[name="locationProof"]'),
                    'Location/Role Proof')],
            ];

            checks.forEach(([name, msg]) => {
                if (msg) {
                    valid = false;
                    showFieldError(name, msg);
                    if (!firstInvalid) firstInvalid = document.querySelector(`[name="${name}"]`);
                }
            });

            if (!valid) {
                if (firstInvalid) firstInvalid.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                return;
            }

            // Loading state
            const btn = document.getElementById('verifySubmitBtn');
            const btnText = btn.querySelector('.btn-text');
            const btnLoader = btn.querySelector('.btn-loader');
            btn.disabled = true;
            if (btnText) btnText.style.visibility = 'hidden';
            if (btnLoader) btnLoader.style.display = 'inline-block';

            // Build FormData
            const formData = new FormData();
            formData.append('firstName', f.querySelector('[name="firstName"]').value.trim());
            formData.append('lastName', f.querySelector('[name="lastName"]').value.trim());
            formData.append('middleName', f.querySelector('[name="middleName"]').value.trim());
            formData.append('extensionName', f.querySelector('[name="extensionName"]').value.trim());
            formData.append('sex', f.querySelector('[name="sex"]').value);
            formData.append('role', f.querySelector('[name="role"]').value);
            formData.append('dateOfBirth', f.querySelector('[name="dateOfBirth"]').value);
            formData.append('barangay', f.querySelector('[name="barangay"]').value);
            formData.append('completeAddress', f.querySelector('[name="completeAddress"]').value.trim());
            formData.append('emergencyContactName', f.querySelector('[name="emergencyContactName"]').value.trim());
            formData.append('emergencyContactPhone', f.querySelector('[name="emergencyContactPhone"]').value
        .trim());

            const idFront = f.querySelector('[name="idFront"]').files[0];
            const idBack = f.querySelector('[name="idBack"]').files[0];
            const locProof = f.querySelector('[name="locationProof"]').files[0];
            if (idFront) formData.append('idFront', idFront);
            if (idBack) formData.append('idBack', idBack);
            if (locProof) formData.append('locationProof', locProof);

            fetch('/auth/verify-profile', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        // Show success card, hide form card
                        document.getElementById('verify-form-card').style.display = 'none';
                        const sc = document.getElementById('success-card');
                        sc.style.display = 'flex';
                        sc.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    } else {
                        // Re-enable button
                        btn.disabled = false;
                        if (btnText) btnText.style.visibility = 'visible';
                        if (btnLoader) btnLoader.style.display = 'none';

                        // Show field-level errors from server
                        if (data.errors) {
                            Object.entries(data.errors).forEach(([field, msgs]) => {
                                showFieldError(field, Array.isArray(msgs) ? msgs[0] : msgs);
                            });
                        }

                        if (typeof showNotification === 'function') {
                            showNotification('error', data.message ||
                                'Submission failed. Please check the form.');
                        }
                    }
                })
                .catch(() => {
                    btn.disabled = false;
                    if (btnText) btnText.style.visibility = 'visible';
                    if (btnLoader) btnLoader.style.display = 'none';
                    if (typeof showNotification === 'function') {
                        showNotification('error', 'Network error. Please check your connection and try again.');
                    }
                });
        });
    </script>

    <script src="{{ asset('js/toast-notifications.js') }}?v={{ config('app.asset_version') }}"></script>

    <!-- Mobile nav + dropdown + language selector JS -->
    <script>
        // Mobile nav
        function toggleMobileNav() {
            const menu = document.getElementById('mobileNavMenu');
            menu.classList.contains('active') ? closeMobileNav() : openMobileNav();
        }

        function openMobileNav() {
            document.getElementById('mobileNavMenu').classList.add('active');
            document.getElementById('mobileNavOverlay').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeMobileNav() {
            document.getElementById('mobileNavMenu').classList.remove('active');
            document.getElementById('mobileNavOverlay').classList.remove('active');
            document.body.style.overflow = '';
        }
        window.addEventListener('resize', () => {
            if (window.innerWidth > 768) closeMobileNav();
        });

        // User dropdown
        function toggleUserDropdown() {
            document.getElementById('user-dropdown')?.classList.toggle('show');
        }
        document.addEventListener('click', function(e) {
            const profile = document.getElementById('user-profile');
            const dropdown = document.getElementById('user-dropdown');
            if (dropdown && profile && !profile.contains(e.target)) {
                dropdown.classList.remove('show');
            }
        });

        // Language selector
        function toggleLangDropdown() {
            document.getElementById('langDropdownMenu').classList.toggle('open');
            document.getElementById('langDropdownBtn').classList.toggle('open');
        }

        function selectLanguage(langCode, langName) {
            document.getElementById('langDropdownMenu').classList.remove('open');
            document.getElementById('langDropdownBtn').classList.remove('open');
            document.getElementById('currentLangText').textContent = langName;
            document.querySelectorAll('.lang-option').forEach(o => {
                o.classList.remove('active');
                const c = o.querySelector('.check-icon');
                if (c) c.remove();
            });
            const sel = document.querySelector(`.lang-option[data-lang="${langCode}"]`);
            if (sel) {
                sel.classList.add('active');
                const ck = document.createElement('span');
                ck.className = 'check-icon';
                ck.innerHTML = '<i class="fas fa-check"></i>';
                sel.appendChild(ck);
            }
            const googleTransCookie = langCode === 'en' ? '/en/en' : `/en/${langCode}`;
            document.cookie = `googtrans=${googleTransCookie}; path=/`;
            document.cookie = `googtrans=${googleTransCookie}; path=/; domain=${window.location.hostname}`;
            const translateSelect = document.querySelector('.goog-te-combo');
            if (translateSelect) {
                translateSelect.value = langCode;
                translateSelect.dispatchEvent(new Event('change'));
            } else {
                location.reload();
            }
        }
        document.addEventListener('click', function(e) {
            const selector = document.querySelector('.language-selector');
            if (selector && !selector.contains(e.target)) {
                document.getElementById('langDropdownMenu')?.classList.remove('open');
                document.getElementById('langDropdownBtn')?.classList.remove('open');
            }
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeMobileNav();
                document.getElementById('langDropdownMenu')?.classList.remove('open');
                document.getElementById('langDropdownBtn')?.classList.remove('open');
            }
        });

        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'en',
                includedLanguages: 'en,tl',
                autoDisplay: false
            }, 'google_translate_element');
        }
    </script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit">
    </script>
</body>

</html>
