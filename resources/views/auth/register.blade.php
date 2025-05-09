@extends('layouts.app')

@section('content')
<div class="register-page" style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh; background-color: #f5f5f5; padding: 20px;">
    <div class="app-name" style="font-size: 32px; font-weight: bold; color: #ff6600; margin-bottom: 20px;">
        KOPI LOKA
    </div>

    <div class="card" style="width: 100%; max-width: 420px; border-radius: 12px; box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);">
        <div class="card-body" style="padding: 30px;">
            <div class="text-center mb-4">
                <img src="{{ asset('images/kopi-loka-favicon.png') }}" alt="Logo" style="width: 80px; height: 80px; object-fit: contain;">
                <h5 class="mt-3" style="font-size: 16px; color: #333;">Register to create your account</h5>
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="mb-3">
                    <input id="first_name" type="text" class="form-control @error('first_name') is-invalid @enderror"
                        name="first_name" value="{{ old('first_name') }}" required autocomplete="first_name" autofocus
                        placeholder="First Name">

                    @error('first_name')
                        <span class="invalid-feedback d-block mt-1" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-3">
                    <input id="last_name" type="text" class="form-control @error('last_name') is-invalid @enderror"
                        name="last_name" value="{{ old('last_name') }}" required autocomplete="last_name"
                        placeholder="Last Name">

                    @error('last_name')
                        <span class="invalid-feedback d-block mt-1" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-3">
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                        name="email" value="{{ old('email') }}" required autocomplete="email"
                        placeholder="Email Address">

                    @error('email')
                        <span class="invalid-feedback d-block mt-1" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-3">
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                        name="password" required autocomplete="new-password" placeholder="Password">

                    @error('password')
                        <span class="invalid-feedback d-block mt-1" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-3">
                    <input id="password-confirm" type="password" class="form-control"
                        name="password_confirmation" required autocomplete="new-password" placeholder="Confirm Password">
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn" style="background-color: #ff6600; color: white; font-weight: bold;">
                        Register
                    </button>
                </div>

                <div class="text-center mt-2" style="font-size: 14px;">
                    <span>Already have an account?</span>
                    <a href="{{ route('login') }}" class="text-decoration-none" style="color: #ff6600;">Login here</a>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
