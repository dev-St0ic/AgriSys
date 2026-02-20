@extends('layouts.guest')

@section('title', 'Email Verification - AgriSys')

@section('styles')
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        min-height: 100vh;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }

    .verification-container {
        width: 100%;
        max-width: 500px;
    }

    .verification-card {
        background: white;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        text-align: center;
        animation: slideUp 0.5s ease-out;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .status-icon {
        font-size: 5rem;
        margin-bottom: 24px;
        animation: bounceIn 0.6s ease-out 0.3s both;
    }

    @keyframes bounceIn {
        0% {
            opacity: 0;
            transform: scale(0.3);
        }
        50% {
            transform: scale(1.05);
        }
        100% {
            opacity: 1;
            transform: scale(1);
        }
    }

    .status-icon.success {
        color: #28a745;
    }

    .status-icon.error {
        color: #dc3545;
    }

    .verification-card h2 {
        color: #333;
        margin-bottom: 16px;
        font-size: 1.75rem;
        font-weight: 600;
    }

    .verification-message {
        color: #666;
        margin-bottom: 28px;
        line-height: 1.6;
        font-size: 1rem;
    }

    .verification-details {
        background: #f8f9fa;
        padding: 16px;
        border-radius: 8px;
        margin-bottom: 24px;
        text-align: left;
    }

    .verification-details p {
        margin: 8px 0;
        font-size: 0.9rem;
        color: #555;
    }

    .verification-details strong {
        color: #333;
    }

    .verification-actions {
        display: flex;
        gap: 12px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .btn {
        padding: 12px 28px;
        border: none;
        border-radius: 6px;
        font-size: 1rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary {
        background-color: #667eea;
        color: white;
    }

    .btn-primary:hover {
        background-color: #5568d3;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    }

    .btn-secondary {
        background-color: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(108, 117, 125, 0.4);
    }

    .alert {
        padding: 12px 16px;
        border-radius: 6px;
        margin-bottom: 20px;
        text-align: left;
        display: flex;
        align-items: start;
        gap: 12px;
    }

    .alert i {
        font-size: 1.2rem;
        margin-top: 2px;
    }

    .alert-info {
        background-color: #d1ecf1;
        color: #0c5460;
        border: 1px solid #bee5eb;
    }

    .alert-warning {
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }

    @media (max-width: 576px) {
        .verification-card {
            padding: 30px 20px;
        }

        .verification-card h2 {
            font-size: 1.5rem;
        }

        .btn {
            padding: 10px 20px;
            font-size: 0.95rem;
        }
    }
</style>
@endsection

@section('content')
<div class="verification-container">
    <div class="verification-card">
        @if(session('success'))
            <div class="status-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
            
            <h2>Password Changed Successfully!</h2>
            
            <p class="verification-message">
                {{ session('success') }}
            </p>

            <div class="verification-details">
                <p><strong><i class="fas fa-info-circle me-2"></i>What's Next?</strong></p>
                <p>• Use your new password to login</p>
                <p>• Make sure to remember your new password</p>
                <p>• For security, don't share your password with anyone</p>
            </div>

            <div class="verification-actions">
                <a href="{{ route('login') }}" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i>
                    Go to Login
                </a>
            </div>

        @elseif(session('error'))
            <div class="status-icon error">
                <i class="fas fa-times-circle"></i>
            </div>
            
            <h2>Verification Failed</h2>
            
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <div>{{ session('error') }}</div>
            </div>

            <p class="verification-message">
                If you continue to experience issues, please contact support or try requesting a new password change.
            </p>

            <div class="verification-actions">
                <a href="{{ route('login') }}" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i>
                    Go to Login
                </a>
                <a href="{{ route('admin.profile.edit') }}" class="btn btn-secondary">
                    <i class="fas fa-user-edit"></i>
                    Try Again
                </a>
            </div>

        @else
            <div class="status-icon">
                <i class="fas fa-spinner fa-spin"></i>
            </div>
            
            <h2>Processing Verification...</h2>
            
            <p class="verification-message">
                Please wait while we verify your password change request.
            </p>
        @endif
    </div>
</div>
@endsection