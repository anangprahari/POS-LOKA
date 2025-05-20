@extends('layouts.app')
@section('content')
<div class="register-container">
    <div class="register-card">
        <div class="register-header">
            <img src="{{ asset('images/kopi-loka-favicon.png') }}" alt="KOPI LOKA Logo" class="register-logo">
            <h1 class="register-title">KOPI LOKA</h1>
            <p class="register-subtitle">Register to create your account</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="register-form">
            @csrf

            <div class="form-group">
                <div class="input-group">
                    <span class="input-icon"><i class="fas fa-user"></i></span>
                    <input id="first_name" type="text" class="form-control @error('first_name') is-invalid @enderror"
                        name="first_name" value="{{ old('first_name') }}" required autocomplete="first_name" autofocus
                        placeholder="First Name">
                </div>
                @error('first_name')
                    <span class="error-message">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <div class="input-group">
                    <span class="input-icon"><i class="fas fa-user"></i></span>
                    <input id="last_name" type="text" class="form-control @error('last_name') is-invalid @enderror"
                        name="last_name" value="{{ old('last_name') }}" required autocomplete="last_name"
                        placeholder="Last Name">
                </div>
                @error('last_name')
                    <span class="error-message">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <div class="input-group">
                    <span class="input-icon"><i class="fas fa-envelope"></i></span>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                        name="email" value="{{ old('email') }}" required autocomplete="email"
                        placeholder="Email Address">
                </div>
                @error('email')
                    <span class="error-message">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <div class="input-group">
                    <span class="input-icon"><i class="fas fa-lock"></i></span>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                        name="password" required autocomplete="new-password" placeholder="Password">
                </div>
                @error('password')
                    <span class="error-message">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <div class="input-group">
                    <span class="input-icon"><i class="fas fa-lock"></i></span>
                    <input id="password-confirm" type="password" class="form-control"
                        name="password_confirmation" required autocomplete="new-password" placeholder="Confirm Password">
                </div>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary register-btn">
                    Register
                </button>
            </div>

            <div class="register-links">
                <span>Already have an account?</span>
                <a href="{{ route('login') }}">
                    <i class="fas fa-sign-in-alt"></i> Login here
                </a>
            </div>
        </form>
    </div>
</div>

<style>
    .register-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 80vh;
        padding: 2rem 1rem;
    }

    .register-card {
        width: 100%;
        max-width: 460px;
        background-color: white;
        border-radius: 12px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .register-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
    }

    .register-header {
        text-align: center;
        padding: 2rem 2rem 1rem;
    }

    .register-logo {
        width: 90px;
        height: 90px;
        object-fit: contain;
        margin-bottom: 1rem;
    }

    .register-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #ff6600;
        margin-bottom: 0.5rem;
    }

    .register-subtitle {
        font-size: 0.9rem;
        color: #666;
        margin-bottom: 1rem;
    }

    .register-form {
        padding: 0 2rem 2rem;
    }

    .form-group {
        margin-bottom: 1.25rem;
    }

    .input-group {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-icon {
        position: absolute;
        left: 15px;
        color: #999;
    }

    .form-control {
        width: 100%;
        padding: 0.875rem 1rem 0.875rem 2.5rem;
        border: 1px solid #eeeeee;
        border-radius: 8px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #ff6600;
        box-shadow: 0 0 0 3px rgba(255, 102, 0, 0.25);
        outline: none;
    }

    .error-message {
        display: block;
        color: #e53e3e;
        font-size: 0.8rem;
        margin-top: 0.5rem;
        padding-left: 0.5rem;
    }

    .register-btn {
        width: 100%;
        padding: 0.875rem;
        background-color: #ff6600;
        border: none;
        border-radius: 8px;
        color: white;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .register-btn:hover {
        background-color: #e65c00;
        transform: translateY(-2px);
    }

    .register-links {
        margin-top: 1.5rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
        text-align: center;
    }

    .register-links span {
        color: #666;
        font-size: 0.875rem;
    }

    .register-links a {
        color: #ff6600;
        text-decoration: none;
        font-size: 0.875rem;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .register-links a:hover {
        color: #e65c00;
        transform: translateX(3px);
    }

    @media (max-width: 768px) {
        .register-card {
            max-width: 100%;
        }
    }
</style>
@endsection
