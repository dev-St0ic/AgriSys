<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AgriSys - San Pedro City Agriculture Office</title>
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fishr.css') }}">
    <link rel="stylesheet" href="{{ asset('css/boatr.css') }}">
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
            <div class="logo">AgriSys</div>
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

    <section class="welcome" id="home">
        <h2>Welcome to AgriSys</h2>
        <p>The Agricultural Service System of the City Agriculture Office of San Pedro, Laguna</p>
        <button class="btn-services"
            onclick="document.getElementById('services').scrollIntoView({ behavior: 'smooth' })">Explore
            Services</button>
    </section>

    <section class="announcement">
        <p><strong>📢 Announcement:</strong> Seedling distribution starts July 1, 2025. Visit the Seedlings section for
            more info.</p>
    </section>

    <!-- Services Section -->
    <section class="services" id="services">
        <h2>OUR SERVICES</h2>
        <div class="card-container">
            <div class="card">
                <h3>RSBSA Registration</h3>
                <p>Register your details for the Registry System for Basic Sectors in Agriculture (RSBSA).</p>
                <button class="btn-choice" onclick="openRSBSAForm(event)">Apply Now</button>
            </div>
            <div class="card">
                <h3>Seedlings Request</h3>
                <p>Request free seedlings to support your agricultural livelihood.</p>
                <button class="btn-choice" onclick="openFormSeedlings(event)">Apply Now</button>
            </div>
            <div class="card">
                <h3>FishR Registration</h3>
                <p>Register in the FishR system for fisherfolk support and services.</p>
                <button class="btn-choice" onclick="openFormFishR(event)">Apply Now</button>
            </div>
            <div class="card">
                <h3>BoatR Registration</h3>
                <p>Apply for registration and assistance for your fishing boats.</p>
                <button class="btn-choice" onclick="openFormBoatR(event)">Apply Now</button>
            </div>
            <div class="card">
                <h3>Training Request</h3>
                <p>Apply for agricultural training programs to enhance your farming skills and knowledge.</p>
                <button class="btn-choice" onclick="openFormTraining(event)">Apply Now</button>
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
        <h2>HOW IT WORKS</h2>
        <div class="steps">
            <div class="step">
                <h3>1. Fill Out the Form</h3>
                <p>Select a service and complete the required online application form with your details.</p>
            </div>
            <div class="step">
                <h3>2. Submit Documents</h3>
                <p>Upload any required supporting documents or provide them to the City Agriculture Office.</p>
            </div>
            <div class="step">
                <h3>3. Receive Approval</h3>
                <p>Once approved, you will be notified and can access the requested agricultural service.</p>
            </div>
        </div>
    </section>

    <section class="help-section">
        <h2>Need Help?</h2>
        <p>If you have any questions or need assistance with your application, please don't hesitate to contact our
            support team.</p>
        <div class="help-buttons">
            <button class="btn-help">Contact Us</button>
            <button class="btn-help">Visit Office</button>
        </div>
    </section>

    <footer class="footer" id="main-footer">
        <div class="footer-container">
            <div class="footer-column">
                <h3>About AgriSys</h3>
                <p>The Agricultural Service System (AgriSys) is designed to optimize service delivery for the City
                    Agriculture Office of San Pedro, Laguna. We aim to streamline agricultural services and support
                    local farmers.</p>
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
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2025 City Agriculture Office of San Pedro. All rights reserved.</p>
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