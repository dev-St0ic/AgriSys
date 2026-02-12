@extends('layouts.guest')

@section('title', 'Admin Login - AgriSys')

@section('styles')
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            overflow: hidden;
            background: url('{{ asset('images/logos/sanpedrobg.jpg') }}') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: 0;
        }

        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            padding: 20px;
            position: relative;
            z-index: 1;
        }

        .login-image {
            display: none;
        }

        .login-form {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(10px);
            padding: 40px 50px;
            width: 100%;
            max-width: 400px;
            border-radius: 8px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .form-wrapper {
            width: 100%;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header i {
            display: none;
        }

        .login-header h2 {
            color: #ffffff;
            font-size: 2.2rem;
            font-weight: 300;
            margin-bottom: 5px;
        }

        .login-header p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
            font-weight: 300;
        }

        .form-group {
            margin-bottom: 18px;
            position: relative;
        }

        .form-group label {
            display: none;
        }

        .form-group {
            margin-bottom: 18px;
            position: relative;
        }

        .form-group i {
            position: absolute;
            left: 0;
            top: 12px;
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
            z-index: 1;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px 12px 30px;
            border: none;
            border-bottom: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 0;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            background: transparent;
            color: #ffffff !important;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .form-control:focus {
            outline: none;
            border-bottom-color: rgba(255, 255, 255, 0.8);
            background: transparent;
            box-shadow: none;
            color: #ffffff !important;
        }

        .form-control:-webkit-autofill,
        .form-control:-webkit-autofill:hover,
        .form-control:-webkit-autofill:focus {
            -webkit-text-fill-color: #ffffff !important;
            -webkit-box-shadow: 0 0 0px 1000px rgba(0, 0, 0, 0) inset;
            transition: background-color 5000s ease-in-out 0s;
        }

        .form-control.is-invalid {
            border-bottom-color: #fc8181;
        }

        .invalid-feedback {
            color: #fc8181;
            font-size: 0.85rem;
            margin-top: 5px;
            display: block;
        }

        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.85rem;
        }

        .remember-me input[type="checkbox"] {
            margin-right: 8px;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: #48bb78;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 0.95rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
            text-transform: uppercase;
        }

        .btn-login:hover {
            background: #38a169;
            box-shadow: 0 4px 12px rgba(72, 187, 120, 0.4);
        }

        .footer-text {
            text-align: center;
            margin-top: 20px;
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.75rem;
        }

        @media (max-width: 968px) {
            .login-form {
                max-width: 100%;
                padding: 40px 30px;
            }
        }

        @media (max-width: 576px) {
            .login-form {
                padding: 30px 20px;
            }

            .login-header h2 {
                font-size: 1.8rem;
            }
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            top: 12px;
            left: auto !important;
            cursor: pointer;
            color: rgba(255, 255, 255, 0.6);
            transition: color 0.3s ease;
        }

        .toggle-password:hover {
            color: rgba(255, 255, 255, 0.9);
        }
    </style>
@endsection

@section('content')
    <div class="login-container">
        <div class="login-image"></div>

        <div class="login-form">
            <div class="form-wrapper">
                <div class="login-header">
                    <h2>Welcome Back</h2>
                    <p>Please sign in to continue</p>
                </div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form-group">
                        <i class="fas fa-user"></i>
                        <input type="text" class="form-control @error('email') is-invalid @enderror" id="email"
                            name="email" value="{{ old('email') }}" placeholder="Email" required autofocus>
                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                            name="password" placeholder="Password" required>
                        <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                        @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <button type="submit" class="btn-login">
                        Login
                    </button>

                    <div class="footer-text">
                        © {{ date('Y') }} AgriSys. All rights reserved.
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function () {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>

@endsection
