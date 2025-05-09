@extends('layouts.app')

@section('content')
<div class="login-page" style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh; background-color: #f5f5f5; padding: 20px;">
    <div class="app-name" style="font-size: 32px; font-weight: bold; color: #ff6600; margin-bottom: 20px;">
        KOPI LOKA
    </div>

    <div class="card" style="width: 100%; max-width: 360px; border-radius: 12px; box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);">
        <div class="card-body" style="padding: 30px;">
            <h5 class="text-center mb-4" style="font-size: 16px; color: #333;">Forgot your password?</h5>
            <p class="text-center" style="font-size: 14px; color: #666;">Enter your email and we’ll send you a link to reset your password.</p>

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="mb-3">
                    <input type="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email"
                        name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                    @error('email')
                        <span class="invalid-feedback d-block mt-1" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn" style="background-color: #ff6600; color: white; font-weight: bold;">
                        Send Reset Link
                    </button>
                </div>
            </form>

            <div class="text-center mt-3" style="font-size: 14px;">
                <a class="text-decoration-none" href="{{ route('login') }}" style="color: #ff6600;">Back to Login</a>
                <br>
                <a class="text-decoration-none" href="{{ route('register') }}" style="color: #ff6600;">Register a new account</a>
            </div>
        </div>
    </div>
</div>
@endsection
