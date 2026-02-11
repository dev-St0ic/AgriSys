@extends('layouts.guest')

@section('title', 'Verify Email - AgriSys')

@section('content')
<div class="verification-container">
    <div class="verification-card">
        <h2>Verify Your Email Address</h2>
        
        <p class="verification-message">
            A verification link has been sent to your email address. 
            Click the link to verify your email and activate your admin account. 
            If you didn't receive it, click "Resend Verification Email" below.
        </p>

        @if (session('status') == 'verification-link-sent')
            <div class="alert alert-success">
                A new verification link has been sent to the email address you provided.
                <br><small>You can request another in 5 minutes.</small>
            </div>
        @endif

        <div class="verification-actions">
            <form method="POST" action="{{ route('verification.send') }}" id="resendForm">
                @csrf
                <button type="submit" class="btn btn-primary" id="resendBtn">
                    Resend Verification Email
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    .verification-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 20px;
    }

    .verification-card {
        background: white;
        padding: 40px;
        border-radius: 8px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        max-width: 500px;
        text-align: center;
    }

    .verification-card h2 {
        color: #333;
        margin-bottom: 20px;
        font-size: 1.8rem;
    }

    .verification-message {
        color: #666;
        margin-bottom: 25px;
        line-height: 1.6;
    }

    .alert {
        padding: 12px 16px;
        border-radius: 4px;
        margin-bottom: 25px;
        background-color: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
    }

    .verification-actions {
        display: flex;
        gap: 10px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background-color: #667eea;
        color: white;
    }

    .btn-primary:hover:not(:disabled) {
        background-color: #5568d3;
    }

    .btn:disabled {
        background-color: #0056b3; 
        cursor: not-allowed;
        opacity: 0.6;
        font-weight: bold;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const resendBtn = document.getElementById('resendBtn');
        const cooldownTime = 5 * 60 * 1000; // 5 minutes in milliseconds

        function updateButtonState() {
            const now = Date.now();
            const storedTime = localStorage.getItem('lastResendTime');
            
            if (storedTime) {
                const timePassed = now - parseInt(storedTime);
                
                if (timePassed < cooldownTime) {
                    const timeRemaining = Math.ceil((cooldownTime - timePassed) / 1000);
                    const minutes = Math.floor(timeRemaining / 60);
                    const seconds = timeRemaining % 60;
                    const secondsStr = seconds < 10 ? '0' + seconds : seconds;
                    
                    resendBtn.disabled = true;
                    resendBtn.innerHTML = `Try again in <strong>${minutes}:${secondsStr}</strong>`;
                    
                    console.log(`Countdown: ${minutes}:${secondsStr}`); // Debug log
                    
                    // Update countdown every second
                    setTimeout(updateButtonState, 1000);
                } else {
                    resendBtn.disabled = false;
                    resendBtn.textContent = 'Resend Verification Email';
                }
            }
        }

        // Update button state on page load
        updateButtonState();

        // Store timestamp when form is submitted
        document.getElementById('resendForm').addEventListener('submit', function(e) {
            localStorage.setItem('lastResendTime', Date.now().toString());
            updateButtonState(); // Update immediately after click
        });
    });
</script>
@endsection