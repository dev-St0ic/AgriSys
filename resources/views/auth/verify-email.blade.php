@extends('layouts.guest')

@section('title', 'Verify Email - AgriSys')

@section('content')
<div class="verification-container">
    <div class="verification-card">
        <h2>Verify Your Email Address</h2>
        
        <p class="verification-message">
            Thanks for signing up! Before getting started, could you verify your email address by clicking on the link 
            we just emailed to you? If you didn't receive the email, we will gladly send you another.
        </p>

        @if (session('status') == 'verification-link-sent')
            <div class="alert alert-success">
                A new verification link has been sent to the email address you provided.
            </div>
        @endif

        <div class="verification-actions">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn btn-primary">
                    Resend Verification Email
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-secondary">
                    Sign Out
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

    .btn-primary:hover {
        background-color: #5568d3;
    }

    .btn-secondary {
        background-color: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
    }
</style>
@endsection
