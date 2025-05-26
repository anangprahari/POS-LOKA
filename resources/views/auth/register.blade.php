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

            <div class="form-row">
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-icon">
                            <i class="fas fa-user"></i>
                        </span>
                        <input 
                            id="first_name" 
                            type="text" 
                            class="form-control @error('first_name') is-invalid @enderror"
                            name="first_name" 
                            value="{{ old('first_name') }}" 
                            required 
                            autocomplete="given-name" 
                            autofocus
                            placeholder="First Name">
                    </div>
                    @error('first_name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <span class="input-icon">
                            <i class="fas fa-user"></i>
                        </span>
                        <input 
                            id="last_name" 
                            type="text" 
                            class="form-control @error('last_name') is-invalid @enderror"
                            name="last_name" 
                            value="{{ old('last_name') }}" 
                            required 
                            autocomplete="family-name"
                            placeholder="Last Name">
                    </div>
                    @error('last_name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <div class="input-group">
                    <span class="input-icon">
                        <i class="fas fa-envelope"></i>
                    </span>
                    <input 
                        id="email" 
                        type="email" 
                        class="form-control @error('email') is-invalid @enderror"
                        name="email" 
                        value="{{ old('email') }}" 
                        required 
                        autocomplete="email"
                        placeholder="Email Address">
                </div>
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <div class="input-group">
                    <span class="input-icon">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input 
                        id="password" 
                        type="password" 
                        class="form-control @error('password') is-invalid @enderror"
                        name="password" 
                        required 
                        autocomplete="new-password" 
                        placeholder="Password">
                </div>
                @error('password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <div class="input-group">
                    <span class="input-icon">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input 
                        id="password-confirm" 
                        type="password" 
                        class="form-control"
                        name="password_confirmation" 
                        required 
                        autocomplete="new-password" 
                        placeholder="Confirm Password">
                </div>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary register-btn">
                    <i class="fas fa-user-plus"></i>
                    Register
                </button>
            </div>

            <div class="register-links">
                <span>Already have an account?</span>
                <a href="{{ route('login') }}">
                    <i class="fas fa-sign-in-alt"></i> 
                    Login here
                </a>
            </div>
        </form>
    </div>
</div>

<style>
    * {
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 0;
    }

    .register-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        padding: 2rem 1rem;
    }

    .register-card {
        width: 100%;
        max-width: 480px;
        background-color: white;
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        overflow: hidden;
        transition: all 0.3s ease;
        position: relative;
    }

    .register-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
    }

    .register-header {
        text-align: center;
        padding: 2.5rem 2rem 1.5rem;
        background: white;
    }

    .register-logo {
        width: 80px;
        height: 80px;
        object-fit: contain;
        margin-bottom: 1rem;
        border-radius: 50%;
        box-shadow: 0 4px 15px rgba(255, 102, 0, 0.3);
        transition: transform 0.3s ease;
    }

    .register-logo:hover {
        transform: scale(1.1) rotate(5deg);
    }

    .register-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: #ff6600;
        margin-bottom: 0.5rem;
        text-shadow: 0 2px 4px rgba(255, 102, 0, 0.1);
    }

    .register-subtitle {
        font-size: 0.95rem;
        color: #666;
        margin-bottom: 0;
        font-weight: 400;
    }

    .register-form {
        padding: 2rem 2rem 2.5rem;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-row .form-group {
        margin-bottom: 0;
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
        z-index: 2;
        font-size: 1rem;
        transition: color 0.3s ease;
    }

    .form-control {
        width: 100%;
        padding: 1rem 1rem 1rem 2.8rem;
        border: 2px solid #e8e8e8;
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background-color: #fafafa;
    }

    .form-control:focus {
        border-color: #ff6600;
        box-shadow: 0 0 0 4px rgba(255, 102, 0, 0.1);
        outline: none;
        background-color: white;
    }

    .form-control:focus + .input-icon,
    .input-group:focus-within .input-icon {
        color: #ff6600;
    }

    .form-control.is-invalid {
        border-color: #e53e3e;
        box-shadow: 0 0 0 4px rgba(229, 62, 62, 0.1);
    }

    .error-message {
        display: block;
        color: #e53e3e;
        font-size: 0.85rem;
        margin-top: 0.5rem;
        padding-left: 0.5rem;
        font-weight: 500;
    }

    .register-btn {
        width: 100%;
        padding: 1rem;
        background: linear-gradient(135deg, #ff6600 0%, #e65c00 100%);
        border: none;
        border-radius: 12px;
        color: white;
        font-weight: 600;
        font-size: 1.1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        position: relative;
        overflow: hidden;
        margin-top: 1rem;
    }

    .register-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }

    .register-btn:hover::before {
        left: 100%;
    }

    .register-btn:hover {
        background: linear-gradient(135deg, #e65c00 0%, #cc5200 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255, 102, 0, 0.4);
    }

    .register-btn:active {
        transform: translateY(0);
    }

    .register-links {
        margin-top: 2rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.75rem;
        text-align: center;
    }

    .register-links span {
        color: #666;
        font-size: 0.9rem;
        font-weight: 500;
    }

    .register-links a {
        color: #ff6600;
        text-decoration: none;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 500;
        padding: 0.5rem 1rem;
        border-radius: 8px;
    }

    .register-links a:hover {
        color: #e65c00;
        background-color: rgba(255, 102, 0, 0.1);
        transform: translateX(3px);
    }

    @media (max-width: 768px) {
        .register-container {
            padding: 1rem;
        }
        
        .register-card {
            max-width: 100%;
        }

        .form-row {
            grid-template-columns: 1fr;
            gap: 0;
        }

        .form-row .form-group {
            margin-bottom: 1.5rem;
        }

        .register-form {
            padding: 1.5rem;
        }

        .register-header {
            padding: 2rem 1.5rem 1rem;
        }
    }

    @media (max-width: 480px) {
        .register-title {
            font-size: 1.5rem;
        }

        .form-control {
            padding: 0.875rem 0.875rem 0.875rem 2.5rem;
            font-size: 0.95rem;
        }

        .register-btn {
            font-size: 1rem;
            padding: 0.875rem;
        }
    }
</style>
@endsection