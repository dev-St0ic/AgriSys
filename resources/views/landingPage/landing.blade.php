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
        <div class="buttons">
            <button type="button" class="btn" onclick="goHome(event)">Home</button>
            <button type="button" class="btn" onclick="openRSBSAForm(event)">RSBSA</button>
            <button type="button" class="btn" onclick="openFormSeedlings(event)">Seedlings</button>
            <button type="button" class="btn" onclick="openFormFishR(event)">FishR</button>
            <button type="button" class="btn" onclick="openFormBoatR(event)">BoatR</button>
            <button type="button" class="btn" onclick="openFormTraining(event)">Training</button>
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
        <p class="services-subtitle">We provide comprehensive agricultural and fisheries support services to help you grow and succeed</p>

        <div class="services-grid">
            <!-- Row 1: Cards 1 & 2 -->
            <div class="row-two"> 
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
            </div>
             <!-- Row 2: Cards 3 & 4 -->
            <div class="row-two">
                <div class="card">
                    <div class="card-image">
                        <img src="../images/services/ServicesFishrTemporary.jpg" alt="Fishr Service">
                    </div>
                    <h3>FishR Registration</h3>
                    <p>Register in the FishR system for fisherfolk support and services.</p>
                    <button class="btn-choice" onclick="openFormFishR(event)">Apply Now</button>
                </div>
                <div class="card">
                    <div class="card-image">
                        <img src="../images/services/ServicesBoatrTemporary.jpg" alt="Boatr Service">
                    </div>
                    <h3>BoatR Registration</h3>
                    <p>Apply for registration and assistance for your fishing boats.</p>
                    <button class="btn-choice" onclick="openFormBoatR(event)">Apply Now</button>
                </div>
            </div>
            <!-- Row 3: Card 5 (centered) -->
            <div class="row-one">
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
        <p class="HowItWorks-subtitle">Getting started with our agricultural services is simple and straightforward. Follow these three easy steps:</p>
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
        <p>If you have questions about your application or urgent agricultural concerns such as crop diseases or natural disasters, our support team is here to assist you.</p>
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
                        <strong>📧 Email:</strong>
                        <a href="mailto:agriculture@sanpedro.gov.ph">agriculture@sanpedro.gov.ph</a>
                    </div>
                    <div class="contact-info-item">
                        <strong>📞 Phone:</strong>
                        <a href="tel:+631234567890">(049) 123-4567</a>
                    </div>
                    <div class="contact-info-item">
                        <strong>🕐 Office Hours:</strong>
                        Monday - Friday: 8:00 AM - 5:00 PM
                    </div>
                    <div class="contact-info-item">
                        <strong>📍 Address:</strong>
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
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" style="opacity: 0.8;">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
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
</body>

</html>