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
            <div class="logos">
                <img src="../images/logos/AgriSysRemoveBG.png" alt="System Logo" class="logo-img-1">
                <img src="../images/logos/CagoRemoveBG.png" alt="Client Logo" class="logo-img-2">

                <div class="location">
                    <div class="city">San Pedro</div>
                    <div>City Agriculture Office</div>
                </div>
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
            <button type="button" class="btn btn-login" onclick="openAuthModal('login')">Log in</button>
            </div>
    </header>

    <section class="announcement">
        <p><strong>📢 Announcement:</strong> Seedling distribution starts July 1, 2025. Visit the Seedlings section for
            more info.</p>
    </section>

    <section class="welcome" id="home">
        <h2>Welcome to AgriSys</h2>
        <p>The Agricultural Service System of the City Agriculture Office of San Pedro, Laguna</p>
        <button class="btn-services"
            onclick="document.getElementById('services').scrollIntoView({ behavior: 'smooth' })">Explore
            Services</button>
    </section>


    <!-- Services Section -->
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

    <!-- Contact Modal - Add before closing </body> tag -->
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

    <!-- AUTH MODALS -->
    <div id="auth-modal" class="auth-modal-overlay" style="display: none;">
        <div class="auth-modal-content">
            <div class="auth-modal-header">
                <h3 id="auth-modal-title">Welcome Back</h3>
                <span class="auth-modal-close" onclick="closeAuthModal()">&times;</span>
            </div>
            
            <div class="auth-modal-body">
                <!-- Error/Success Messages -->
                <div id="auth-error-message" class="auth-message auth-error" style="display: none;"></div>
                <div id="auth-success-message" class="auth-message auth-success" style="display: none;"></div>

                <!-- Log In Form (Default) -->
                <form id="login-form" class="auth-form" style="display: block;">
                    @csrf
                    <div class="form-group">
                        <label for="username">Username or Email</label>
                        <input type="text" id="username" name="username" required placeholder="Enter your username or email">
                    </div>

                    <div class="form-group">
                        <label for="login-password">Password</label>
                        <div class="password-input-container">
                            <input type="password" id="login-password" name="password" required placeholder="Enter your password">
                            <button type="button" class="password-toggle" onclick="togglePasswordVisibility('login-password')">
                                👁️
                            </button>
                        </div>
                    </div>
                    
                    <div class="auth-links">
                        <a href="#" onclick="showForgotPassword()">Forgot your password?</a>
                    </div>

                    <button type="submit" class="auth-submit-btn">
                        <span class="btn-text">Sign In</span>
                        <span class="btn-loader" style="display: none;">⏳</span>
                    </button>

                    <!-- Divider -->
                    <div class="auth-divider">
                        <span>or</span>
                    </div>

                    <!-- Google Sign In Button -->
                    <button type="button" class="google-signin-btn" onclick="signInWithGoogle()">
                        <svg width="18" height="18" viewBox="0 0 24 24" class="google-icon">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        Continue with Google
                    </button>

                    <!-- Sign Up Prompt -->
                    <div class="signup-prompt">
                        <p>Don't have an account? <a href="#" onclick="showSignUpForm()">Sign up here</a></p>
                    </div>
                </form>

                <!-- Sign Up Form (Hidden by default) -->
                <form id="signup-form" class="auth-form" style="display: none;">
                    @csrf
                    <div class="back-to-login">
                        <button type="button" onclick="showLogInForm()" class="back-btn">← Back to Sign In</button>
                    </div>

                    <h2 style="text-align: center; margin-bottom: 30px; color: #333;">Create Your Account</h2>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="signup-firstname">First Name</label>
                            <input type="text" id="signup-firstname" name="firstname" required placeholder="Enter first name">
                        </div>
                        
                        <div class="form-group">
                            <label for="signup-middlename">Middle Name <span class="optional">(optional)</span></label>
                            <input type="text" id="signup-middlename" name="middlename" placeholder="Enter middle name">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="signup-lastname">Last Name</label>
                            <input type="text" id="signup-lastname" name="lastname" required placeholder="Enter last name">
                        </div>
                        
                        <div class="form-group suffix">
                            <label for="signup-suffix">Suffix</label>
                            <select id="signup-suffix" name="suffix">
                                <option value="">None</option>
                                <option value="jr">Jr.</option>
                                <option value="sr">Sr.</option>
                                <option value="ii">II</option>
                                <option value="iii">III</option>
                                <option value="iv">IV</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="signup-role">Role</label>
                        <select id="signup-role" name="role" required>
                            <option value="">Select your role</option>
                            <option value="farmer">Farmer</option>
                            <option value="fisherman">Fisherman</option>
                            <option value="agricultural_worker">Agricultural Worker</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="signup-contact">Contact Number</label>
                        <input type="tel" id="signup-contact" name="contact" required placeholder="Enter contact number">
                    </div>

                    <div class="form-group">
                        <label for="signup-password">Password</label>
                        <div class="password-input-container">
                            <input type="password" id="signup-password" name="password" required minlength="8" placeholder="Create password (min. 8 characters)">
                            <button type="button" class="password-toggle" onclick="togglePasswordVisibility('signup-password')">
                                👁️
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="signup-confirm-password">Confirm Password</label>
                        <div class="password-input-container">
                            <input type="password" id="signup-confirm-password" name="confirm_password" required placeholder="Confirm your password">
                            <button type="button" class="password-toggle" onclick="togglePasswordVisibility('signup-confirm-password')">
                                👁️
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" id="agree-terms" name="agree_terms" required>
                            <label for="agree-terms">I agree to the <a href="#" target="_blank">Terms of Service</a> and <a href="#" target="_blank">Privacy Policy</a></label>
                        </div>
                    </div>

                    <button type="submit" class="auth-submit-btn">
                        <span class="btn-text">Create Account</span>
                        <span class="btn-loader" style="display: none;">⏳</span>
                    </button>

                    <!-- Google Sign Up Option -->
                    <div class="auth-divider">
                        <span>or</span>
                    </div>

                    <button type="button" class="google-signin-btn" onclick="signUpWithGoogle()">
                        <svg width="18" height="18" viewBox="0 0 24 24" class="google-icon">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        Sign up with Google
                    </button>
                </form>
            </div>
        </div>
    </div>

    <footer class="footer" id="main-footer">
        <div class="footer-container">
            <div class="footer-column">
                <h3>About AgriSys</h3>
                <p>The Agricultural Service System (AgriSys) is designed to optimize service delivery for the City
                    Agriculture Office of San Pedro, Laguna. We aim to streamline agricultural services and support
                    local farmers.</p>

                <div class="social-links">
                    <span style="margin-right: 10px; color: #a8e6cf;">Facebook us on Facebook:</span>
                    <a href="https://www.facebook.com/sanpedroagri" target="_blank" title="Facebook">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"
                            style="opacity: 0.8;">
                            <path
                                d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                        </svg>
                    </a>
                </div>
            </div>

            <div class="footer-column">
                <h3>Our Services</h3>
                <ul>
                    <li><a href="#rsbsa">RSBSA Registration</a></li>
                    <li><a href="#seedlings">Seedlings Request</a></li>
                    <li><a href="#fishr">FishR Registration</a></li>
                    <li><a href="#boatr">BoatR Registration</a></li>
                    <li><a href="#training">Training Request</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h3>Contact Us</h3>
                <p>City Agriculture Office<br>
                    San Pedro City Hall<br>
                    Laguna, Philippines</p>
                <p>Phone: (123) 456-7890<br>
                    Email: <a href="mailto:agriculture@sanpedro.gov.ph">agriculture@sanpedro.gov.ph</a></p>

                <div class="office-hours">
                    <strong>Office Hours:</strong><br>
                    Monday - Friday: 8:00 AM - 5:00 PM<br>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2025 City Agriculture Office of San Pedro. All rights reserved.</p>
            <p style="margin-top: 10px; font-size: 0.8rem;">
                <a href="#privacy" style="color: #ccc;">Privacy Policy</a> |
                <a href="#terms" style="color: #ccc;">Terms of Service</a> |
                <a href="#accessibility" style="color: #ccc;">Accessibility</a>
            </p>
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
</body>

</html>
