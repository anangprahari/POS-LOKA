@extends('layouts.app')

@section('content')
<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <img src="{{ asset('images/kopi-loka-favicon.png') }}" alt="KOPI LOKA Logo" class="login-logo">
            <h1 class="login-title">KOPI LOKA</h1>
            <p class="login-subtitle">Sign in to start your session</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="login-form">
            @csrf

            <div class="form-group">
                <div class="input-group">
                    <span class="input-icon"><i class="fas fa-envelope"></i></span>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                        name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                        placeholder="Email">
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
                        name="password" required autocomplete="current-password" placeholder="Password">
                </div>
                @error('password')
                    <span class="error-message">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            <div class="form-group remember-me">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">
                        Remember Me
                    </label>
                </div>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary login-btn">
                    Sign In
                </button>
            </div>

            <div class="login-links">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}">
                        <i class="fas fa-key"></i> I forgot my password
                    </a>
                @endif
                @if (Route::has('register'))
                    <a href="{{ route('register') }}">
                        <i class="fas fa-user-plus"></i> Register a new membership
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<style>
    .login-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 80vh;
        padding: 2rem 1rem;
    }

    .login-card {
        width: 100%;
        max-width: 420px;
        background-color: white;
        border-radius: 12px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .login-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
    }

    .login-header {
        text-align: center;
        padding: 2rem 2rem 1rem;
    }

    .login-logo {
        width: 90px;
        height: 90px;
        object-fit: contain;
        margin-bottom: 1rem;
    }

    .login-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #ff6600;
        margin-bottom: 0.5rem;
    }

    .login-subtitle {
        font-size: 0.9rem;
        color: #666;
        margin-bottom: 1rem;
    }

    .login-form {
        padding: 0 2rem 2rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
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

    .remember-me {
        display: flex;
        align-items: center;
    }

    .form-check {
        display: flex;
        align-items: center;
    }

    .form-check-input {
        width: 18px;
        height: 18px;
        margin-right: 8px;
        cursor: pointer;
    }

    .form-check-input:checked {
        background-color: #ff6600;
        border-color: #ff6600;
    }

    .form-check-label {
        font-size: 0.875rem;
        cursor: pointer;
    }

    .login-btn {
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

    .login-btn:hover {
        background-color: #e65c00;
        transform: translateY(-2px);
    }

    .login-links {
        margin-top: 1.5rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
    }

    .login-links a {
        color: #ff6600;
        text-decoration: none;
        font-size: 0.875rem;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .login-links a:hover {
        color: #e65c00;
        transform: translateX(3px);
    }

    @media (max-width: 768px) {
        .login-card {
            max-width: 100%;
        }
    }
</style>
@endsection