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
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    My Applications
                                </a>
                                <a href="#" class="dropdown-item" onclick="showProfileModal()">
                                    <svg class="dropdown-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    View Profile
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="#" class="dropdown-item logout" onclick="logoutUser()">
                                    <svg class="dropdown-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
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
            <button class="btn-services" onclick="document.getElementById('services').scrollIntoView({ behavior: 'smooth' })">Explore Services</button>
        </div>
        <div class="welcome-image"></div>
    </section>

    <!-- Projects Section -->
    <section class="projects" id="projects">
        <img src="../images/logos/cagoFull.png" alt="City Agriculture Office Logo" class="logo-icon">
        <img src="../images/logos/CityOfSanPedro.jpg" alt="City of San Pedro Logo" class="logo-icon">
        <h2>City<span class="highlight"> Agriculture Office of San Pedro, Laguna</span></h2>
        <p class="projects-subtitle">Ongoing and past events and projects of the San Pedro City Agriculture Office dedicated to promoting agricultural growth and community development.</p>
        
        <div class="projects-filters">
            <button class="filter-btn active" data-filter="all">View All</button>
            <button class="filter-btn" data-filter="garden-care">Announcements</button>
            <button class="filter-btn" data-filter="lawn-care">Ongoing Events</button>
            <button class="filter-btn" data-filter="planting">Upcoming Events</button>
            <button class="filter-btn" data-filter="landscape">Past Events</button>
        </div>
        
        <div class="projects-grid">
            <!-- Project Card 1 -->
            <div class="project-card" data-category="garden-care">
                <img src="../images/projects/1.jpg" alt="Garden Care" class="project-image">
                <div class="project-content">
                    <h3>Community Garden Initiative</h3>
                    <p class="project-description">Urban gardening program bringing together residents to cultivate fresh produce while learning sustainable farming techniques including organic gardening, composting, and water conservation.</p>
                    
                    <div class="event-info-box">
                        <div class="date">📅 Every Saturday | 8:00 AM - 12:00 PM</div>
                        <div class="location">📍 San Pedro Community Garden, Brgy. Riverside</div>
                    </div>
                    
                    <button class="expand-btn">
                        <span>View More Details</span>
                        <span class="arrow">▼</span>
                    </button>
                    
                    <div class="expandable-details">
                        <ul class="details-list">
                            <li>
                                <span class="icon">👥</span>
                                <div class="text">
                                    <div class="label">Who Can Join</div>
                                    <div class="value">All residents welcome, families encouraged</div>
                                </div>
                            </li>
                            <li>
                                <span class="icon">💰</span>
                                <div class="text">
                                    <div class="label">Cost</div>
                                    <div class="value">Free for all participants</div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Project Card 2 -->
            <div class="project-card" data-category="landscape">
                <img src="../images/projects/2.jpg" alt="Landscape" class="project-image">
                <div class="project-content">
                    <h3>Green Corridor Project</h3>
                    <p class="project-description">City-wide landscaping initiative that transformed urban spaces into vibrant green zones. Over 500 native trees planted and pocket gardens created throughout the city to improve air quality and aesthetics.</p>
                    
                    <div class="event-info-box">
                        <div class="date">✅ Completed: September 2024</div>
                        <div class="location">📍 12 Barangays across San Pedro City</div>
                    </div>
                    
                    <button class="expand-btn">
                        <span>View More Details</span>
                        <span class="arrow">▼</span>
                    </button>
                    
                    <div class="expandable-details">
                        <ul class="details-list">
                            <li>
                                <span class="icon">🌳</span>
                                <div class="text">
                                    <div class="label">Achievement</div>
                                    <div class="value">500+ native trees planted</div>
                                </div>
                            </li>
                            <li>
                                <span class="icon">🎯</span>
                                <div class="text">
                                    <div class="label">Impact</div>
                                    <div class="value">Improved air quality and urban aesthetics</div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Project Card 3 -->
            <div class="project-card" data-category="planting">
                <img src="../images/projects/3.jpg" alt="Planting" class="project-image">
                <div class="project-content">
                    <h3>Tree Planting Drive</h3>
                    <p class="project-description">Annual tree planting event with a goal to plant 1,000 indigenous trees across the city. Volunteers receive free seedlings, refreshments, and certificates of participation.</p>
                    
                    <div class="event-info-box">
                        <div class="date">🌱 November 15, 2025 | 6:00 AM - 10:00 AM</div>
                        <div class="location">📍 Various locations citywide</div>
                    </div>
                    
                    <button class="expand-btn">
                        <span>View More Details</span>
                        <span class="arrow">▼</span>
                    </button>
                    
                    <div class="expandable-details">
                        <ul class="details-list">
                            <li>
                                <span class="icon">🎁</span>
                                <div class="text">
                                    <div class="label">Freebies</div>
                                    <div class="value">Free seedlings, refreshments, certificate</div>
                                </div>
                            </li>
                            <li>
                                <span class="icon">📝</span>
                                <div class="text">
                                    <div class="label">Registration</div>
                                    <div class="value">Contact City Agriculture Office</div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Project Card 4 -->
            <div class="project-card" data-category="planting">
                <img src="../images/projects/4.jpg" alt="Planting" class="project-image">
                <div class="project-content">
                    <h3>Vegetable Farming Workshop</h3>
                    <p class="project-description">Expert-led workshops covering advanced vegetable cultivation methods, pest management, and market strategies to help farmers maximize yields while minimizing environmental impact.</p>
                    
                    <div class="event-info-box">
                        <div class="date">📚 October 28, 2025 | 2:00 PM - 5:00 PM</div>
                        <div class="location">📍 Agriculture Office Training Center</div>
                    </div>
                    
                    <button class="expand-btn">
                        <span>View More Details</span>
                        <span class="arrow">▼</span>
                    </button>
                    
                    <div class="expandable-details">
                        <ul class="details-list">
                            <li>
                                <span class="icon">👨‍🌾</span>
                                <div class="text">
                                    <div class="label">For</div>
                                    <div class="value">Local farmers and aspiring growers</div>
                                </div>
                            </li>
                            <li>
                                <span class="icon">🎁</span>
                                <div class="text">
                                    <div class="label">Freebies</div>
                                    <div class="value">Free seeds, tools, and training materials</div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Project Card 5 -->
            <div class="project-card" data-category="planting">
                <img src="../images/projects/5.jpg" alt="Planting" class="project-image">
                <div class="project-content">
                    <h3>Organic Rice Cultivation</h3>
                    <p class="project-description">Year-long program supporting farmers transitioning to organic rice farming methods that eliminate harmful pesticides. Includes training, organic fertilizers, and access to premium markets.</p>
                    
                    <div class="event-info-box">
                        <div class="date">🌾 January - December 2025</div>
                        <div class="location">📍 All rice farming areas in San Pedro</div>
                    </div>
                    
                    <button class="expand-btn">
                        <span>View More Details</span>
                        <span class="arrow">▼</span>
                    </button>
                    
                    <div class="expandable-details">
                        <ul class="details-list">
                            <li>
                                <span class="icon">🌾</span>
                                <div class="text">
                                    <div class="label">Support Provided</div>
                                    <div class="value">Training, organic fertilizers, market access</div>
                                </div>
                            </li>
                            <li>
                                <span class="icon">🏆</span>
                                <div class="text">
                                    <div class="label">Certification</div>
                                    <div class="value">Organic farming certification assistance</div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Project Card 6 -->
            <div class="project-card" data-category="lawn-care">
                <img src="../images/projects/6.jpg" alt="Lawn Care" class="project-image">
                <div class="project-content">
                    <h3>Park Maintenance Program</h3>
                    <p class="project-description">Daily maintenance of city parks through regular mowing, trimming, and landscaping ensuring safe, clean, and beautiful spaces for families and communities.</p>
                    
                    <div class="event-info-box">
                        <div class="date">🔄 Ongoing | Daily Operations</div>
                        <div class="location">📍 All public parks across San Pedro</div>
                    </div>
                    
                    <button class="expand-btn">
                        <span>View More Details</span>
                        <span class="arrow">▼</span>
                    </button>
                    
                    <div class="expandable-details">
                        <ul class="details-list">
                            <li>
                                <span class="icon">🛠️</span>
                                <div class="text">
                                    <div class="label">Services</div>
                                    <div class="value">Mowing, trimming, landscaping, sanitation</div>
                                </div>
                            </li>
                            <li>
                                <span class="icon">📞</span>
                                <div class="text">
                                    <div class="label">Report Issues</div>
                                    <div class="value">Contact City Agriculture Office</div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Project Card 7 -->
            <div class="project-card" data-category="lawn-care">
                <img src="../images/projects/7.jpg" alt="Lawn Care" class="project-image">
                <div class="project-content">
                    <h3>Sports Field Renovation</h3>
                    <p class="project-description">Complete overhaul of community sports facilities including new turf installation, modern drainage systems, and efficient irrigation creating world-class venues for youth sports and tournaments.</p>
                    
                    <div class="event-info-box">
                        <div class="date">⚙️ In Progress | Target: December 2025</div>
                        <div class="location">📍 San Pedro Sports Complex</div>
                    </div>
                    
                    <button class="expand-btn">
                        <span>View More Details</span>
                        <span class="arrow">▼</span>
                    </button>
                    
                    <div class="expandable-details">
                        <ul class="details-list">
                            <li>
                                <span class="icon">⚽</span>
                                <div class="text">
                                    <div class="label">Facilities</div>
                                    <div class="value">Soccer fields, basketball courts, running tracks</div>
                                </div>
                            </li>
                            <li>
                                <span class="icon">🔧</span>
                                <div class="text">
                                    <div class="label">Upgrades</div>
                                    <div class="value">New turf, drainage, irrigation, lighting</div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Project Card 8 -->
            <div class="project-card" data-category="garden-care">
                <img src="../images/projects/8.jpg" alt="Garden Care" class="project-image">
                <div class="project-content">
                    <h3>Urban Farming Training</h3>
                    <p class="project-description">Monthly training on innovative techniques for growing vegetables in small spaces using containers, vertical gardens, and hydroponics. Perfect for apartment dwellers and homeowners.</p>
                    
                    <div class="event-info-box">
                        <div class="date">🏙️ First Sunday of Every Month | 9:00 AM - 12:00 PM</div>
                        <div class="location">📍 Agriculture Office Training Center</div>
                    </div>
                    
                    <button class="expand-btn">
                        <span>View More Details</span>
                        <span class="arrow">▼</span>
                    </button>
                    
                    <div class="expandable-details">
                        <ul class="details-list">
                            <li>
                                <span class="icon">🌱</span>
                                <div class="text">
                                    <div class="label">Techniques</div>
                                    <div class="value">Container gardening, vertical gardens, hydroponics</div>
                                </div>
                            </li>
                            <li>
                                <span class="icon">📦</span>
                                <div class="text">
                                    <div class="label">Materials</div>
                                    <div class="value">All training materials and starter kits provided</div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
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
                        <img src="../images/services/ServicesRSBSATemporary.jpg" alt="RSBSA Service">
                    </div>
                    <h3>RSBSA Registration</h3>
                    <p>Register your details for the Registry System for Basic Sectors in Agriculture (RSBSA).</p>
                    <button class="btn-choice" onclick="openRSBSAForm(event)">Apply Now</button>
                </div>
                <div class="card">
                    <div class="card-image">
                        <img src="../images/services/ServicesSeedlingsTemporary.jpg" alt="Seedlings Service">
                    </div>
                    <h3>Seedlings Request</h3>
                    <p>Request free seedlings to support your agricultural livelihood.</p>
                    <button class="btn-choice" onclick="openFormSeedlings(event)">Apply Now</button>
                </div>
                <div class="card">
                    <div class="card-image">
                        <img src="../images/services/ServicesFishrTemporary.jpg" alt="Fishr Service">
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
                        <img src="../images/services/ServicesBoatrTemporary.jpg" alt="Boatr Service">
                    </div>
                    <h3>BoatR Registration</h3>
                    <p>Apply for registration and assistance for your fishing boats.</p>
                    <button class="btn-choice" onclick="openFormBoatR(event)">Apply Now</button>
                </div>
                <div class="card">
                    <div class="card-image">
                        <img src="../images/services/ServicesTrainingTemporary.jpg" alt="Training Service">
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
                        <strong>Email:</strong>
                        <a href="mailto:agriculture@sanpedro.gov.ph">agriculture@sanpedro.gov.ph</a>
                    </div>
                    <div class="contact-info-item">
                        <strong>Phone:</strong>
                        <a href="tel:+631234567890">(049) 123-4567</a>
                    </div>
                    <div class="contact-info-item">
                        <strong>Office Hours:</strong>
                        Monday - Friday: 8:00 AM - 5:00 PM
                    </div>
                    <div class="contact-info-item">
                        <strong>Address:</strong>
                        City Agriculture Office<br>
                        San Pedro City Hall, Laguna
                    </div>
                </div>

                <div class="quick-contact-section">
                    <h4>Send Quick Message</h4>
                    <form id="quick-contact-form">
                        <input type="text" placeholder="Your Name" required class="contact-form-input">
                        <input type="email" placeholder="Your Email" required class="contact-form-input">
                        <select required class="contact-form-select">
                            <option value="">Select Issue Type</option>
                            <option value="application">Application Status</option>
                            <option value="emergency">Emergency Agricultural Concern</option>
                            <option value="general">General Inquiry</option>
                            <option value="technical">Technical Support</option>
                        </select>
                        <textarea placeholder="Your Message" rows="4" required class="contact-form-textarea"></textarea>
                        <button type="submit" class="contact-form-submit">Send Message</button>
                    </form>
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
                                    <span class="info-label">Member Since:</span>
                                    <span
                                        class="info-value">{{ isset($user['created_at']) ? date('M Y', strtotime($user['created_at'])) : 'N/A' }}</span>
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
                            {{-- Server-rendered verification button to match backend status --}}
                            @php
                                // Normalize status for rendering
                                $s = $status;
                            @endphp

                            @if (in_array($s, ['verified', 'approved']))
                                <button class="profile-action-btn verified" id="verify-action-btn" disabled>
                                    <span class="btn-icon">✅</span>
                                    Verified
                                </button>
                            @elseif(in_array($s, ['pending', 'pending_verification']))
                                <button class="profile-action-btn pending" id="verify-action-btn" disabled>
                                    <span class="btn-icon">⏳</span>
                                    Pending Verification
                                </button>
                            @elseif($s === 'rejected')
                                <button class="profile-action-btn rejected" id="verify-action-btn"
                                    onclick="showVerificationModal()">
                                    <span class="btn-icon">🔄</span>
                                    Retry Verification
                                </button>
                            @else
                                <button class="profile-action-btn primary" id="verify-action-btn"
                                    onclick="showVerificationModal()">
                                    <span class="btn-icon">✅</span>
                                    Verify Now
                                </button>
                            @endif

                            <button class="profile-action-btn secondary" onclick="editProfile()">
                                <span class="btn-icon">✏️</span>
                                Edit Profile
                            </button>
                            <button class="profile-action-btn secondary" onclick="changePassword()">
                                <span class="btn-icon">🔒</span>
                                Change Password
                            </button>
                            <button class="profile-action-btn secondary"
                                onclick="showMyApplicationsModal(); closeProfileModal();">
                                <span class="btn-icon">📋</span>
                                View Applications
                            </button>
                        </div>

                        <!-- Recent Activity -->
                        <div class="recent-activity">
                            <h5>Recent Activity</h5>
                            <div class="activity-list" id="recent-activity-list">
                                <!-- Will be populated by JavaScript -->
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

        <!-- PROFILE VERIFICATION MODAL - UPDATED TO MATCH BACKEND -->
        <div id="verification-modal" class="modal-overlay" style="display: none;">
            <div class="modal-content verification-modal">
                <div class="modal-header">
                    <h3>Profile Verification</h3>
                    <span class="modal-close" onclick="closeVerificationModal()">&times;</span>
                </div>

                <div class="modal-body">
                    <div class="verification-content">
                        <div class="verification-header">
                            <h4>Complete Your Profile Verification</h4>
                            <p>Please provide the following information to verify your account and access all services.
                            </p>
                        </div>

                        <form id="verification-form">
                            <!-- Personal Information -->
                            <div class="verification-section">
                                <h5>Personal Information</h5>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="firstName">First Name *</label>
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
                                        <label for="lastName">Last Name *</label>
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

                                <!-- ADDED: Date of Birth field (REQUIRED by backend) -->
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="dateOfBirth">Date of Birth *</label>
                                        <input type="date" id="dateOfBirth" name="dateOfBirth" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="contactNumber">Contact Number *</label>
                                        <input type="tel" id="contactNumber" name="contactNumber" required
                                            placeholder="09XXXXXXXXX" pattern="[0-9]{11}">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="role">Role *</label>
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

                            <!-- Address Information -->
                            <div class="verification-section">
                                <h5>Address Information</h5>

                                <div class="form-group">
                                    <label for="barangay">Barangay *</label>
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
                                    <label for="completeAddress">Complete Address *</label>
                                    <textarea id="completeAddress" name="completeAddress" required rows="3"
                                        placeholder="Enter your complete address (House No., Street, Subdivision, etc.)"></textarea>
                                </div>
                            </div>

                            <!-- Document Uploads -->
                            <div class="verification-section">
                                <h5>Required Documents</h5>
                                <p class="section-description">Please upload clear, high-quality images of the required
                                    documents.</p>

                                <div class="form-row">
                                    <div class="form-group file-upload-group">
                                        <label for="idFront">Government ID (Front) *</label>
                                        <input type="file" id="idFront" name="idFront" required
                                            accept="image/*" class="file-input">
                                        <div class="file-upload-area"
                                            onclick="document.getElementById('idFront').click()">
                                            <div class="upload-icon">📄</div>
                                            <div class="upload-text">Click to upload ID front</div>
                                            <div class="upload-subtext">Supported: JPG, PNG, PDF (Max 5MB)</div>
                                        </div>
                                        <div id="idFrontPreview" class="image-preview" style="display: none;"></div>
                                    </div>

                                    <div class="form-group file-upload-group">
                                        <label for="idBack">Government ID (Back) *</label>
                                        <input type="file" id="idBack" name="idBack" required
                                            accept="image/*" class="file-input">
                                        <div class="file-upload-area"
                                            onclick="document.getElementById('idBack').click()">
                                            <div class="upload-icon">📄</div>
                                            <div class="upload-text">Click to upload ID back</div>
                                            <div class="upload-subtext">Supported: JPG, PNG, PDF (Max 5MB)</div>
                                        </div>
                                        <div id="idBackPreview" class="image-preview" style="display: none;"></div>
                                    </div>
                                </div>

                                <div class="form-group file-upload-group">
                                    <label for="locationProof">Location/Role Proof *</label>
                                    <input type="file" id="locationProof" name="locationProof" required
                                        accept="image/*" class="file-input">
                                    <div class="file-upload-area"
                                        onclick="document.getElementById('locationProof').click()">
                                        <div class="upload-icon">📍</div>
                                        <div class="upload-text">Click to upload location/role proof</div>
                                        <div class="upload-subtext">Farm photo, fishing area, business permit, etc.
                                            (Max 5MB)</div>
                                    </div>
                                    <div id="locationProofPreview" class="image-preview" style="display: none;">
                                    </div>
                                    <div class="form-text">
                                        For farmers: Photo of your farm or agricultural land<br>
                                        For fisherfolk: Photo of fishing area or boat<br>
                                        For others: Relevant business permit or location proof
                                    </div>
                                </div>
                            </div>

                            <!-- Verification Notice -->
                            <div class="verification-notice">
                                <div class="notice-icon">⚠️</div>
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
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
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
                                    placeholder="Choose a username"
                                    autocomplete="username" pattern="^(?![0-9])[a-zA-Z0-9_.]{3,20}$" minlength="3" maxlength="20" oninput="checkUsernameAvailability(this.value)">
                                <div class="username-status"></div>
                                <div class="form-text">Username must be 3–20 characters long and contain only letters, numbers, underscores, or dots. Cannot start with a number.</div>
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
                                <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
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
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                                Sign up with Facebook
                            </button>
                             <!-- Back to Login Button -->
                            <div class="login-prompt">
                                <p>Already have an account? <a href="#" onclick="showLogInForm(); return false;">Login here</a></p>
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
                                <input type="number" id="edit-age" name="age" min="18" max="100"
                                    readonly>
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
                    <div class="password-requirements">
                        <h5>Password Requirements</h5>
                        <ul>
                            <li>At least 8 characters long</li>
                            <li>Mix of uppercase and lowercase letters</li>
                            <li>Include numbers and special characters</li>
                            <li>Different from your current password</li>
                        </ul>
                    </div>

                    <!--  Prevent default form submission -->
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
                                <input type="password" id="new-password" name="new_password" required minlength="8"
                                    autocomplete="new-password" placeholder="Enter your new password"
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
                        </div>

                        <div class="form-group">
                            <label for="confirm-new-password">Confirm New Password *</label>
                            <div class="password-input-container">
                                <input type="password" id="confirm-new-password" name="confirm_new_password" required
                                    autocomplete="new-password" placeholder="Confirm your new password"
                                    oninput="checkNewPasswordMatch(document.getElementById('new-password').value, this.value)">
                                <button type="button" class="password-toggle"
                                    onclick="togglePasswordVisibility('confirm-new-password')">
                                    Show
                                </button>
                            </div>
                            <div class="password-match-status confirm-new-password-match"></div>
                        </div>

                        <div class="security-notice">
                            <div class="notice-icon">🔒</div>
                            <div class="notice-content">
                                <h6>Security Tip</h6>
                                <p>For your security, you will be logged out after changing your password and will need
                                    to log in again with your new password.</p>
                            </div>
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
                <p>The Agricultural Service System (AgriSys) is designed to optimize service delivery for the City Agriculture Office of San Pedro, Laguna. We aim to streamline agricultural services and support local farmers.</p>

                <div class="social-links">
                    <span>Follow us:</span>
                    <a href="https://www.facebook.com/sanpedroagri" target="_blank" title="Facebook">
                        <svg width="16" height="16" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
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
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</body>

</html>
