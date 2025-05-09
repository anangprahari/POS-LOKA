@extends('layouts.app')

@section('content')
<div class="login-page" style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh; background-color: #f5f5f5; padding: 20px;">
    <div class="app-name" style="font-size: 32px; font-weight: bold; color: #ff6600; margin-bottom: 20px;">
        KOPI LOKA
    </div>

    <div class="card" style="width: 100%; max-width: 360px; border-radius: 12px; box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);">
        <div class="card-body" style="padding: 30px;">

            <div class="text-center mb-4">
                <img src="{{ asset('images/kopi-loka-favicon.png') }}" alt="Logo" style="width: 80px; height: 80px; object-fit: contain;">
                <h5 class="mt-3" style="font-size: 16px; color: #333;">Sign in to start your session</h5>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-3">
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                        name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                        placeholder="Email">

                    @error('email')
                        <span class="invalid-feedback d-block mt-1" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-3">
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                        name="password" required autocomplete="current-password" placeholder="Password">

                    @error('password')
                        <span class="invalid-feedback d-block mt-1" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">
                            Remember Me
                        </label>
                    </div>
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn" style="background-color: #ff6600; color: white; font-weight: bold;">
                        Sign In
                    </button>
                </div>

                <div class="text-center mt-3" style="font-size: 14px;">
                    @if (Route::has('password.request'))
                        <a class="text-decoration-none" href="{{ route('password.request') }}" style="color: #ff6600;">I forgot my password</a>
                    @endif
                    <br>
                    @if (Route::has('register'))
                        <a class="text-decoration-none" href="{{ route('register') }}" style="color: #ff6600;">Register a new membership</a>
                    @endif
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
