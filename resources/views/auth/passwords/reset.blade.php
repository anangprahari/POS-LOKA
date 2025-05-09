@extends('layouts.app')

@section('content')
<div class="login-page" style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh; background-color: #f5f5f5; padding: 20px;">
    <div class="app-name" style="font-size: 32px; font-weight: bold; color: #ff6600; margin-bottom: 20px;">
        KOPI LOKA
    </div>

    <div class="card" style="width: 100%; max-width: 360px; border-radius: 12px; box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);">
        <div class="card-body" style="padding: 30px;">
            <h5 class="text-center mb-4" style="font-size: 16px; color: #333;">Reset your password</h5>

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mb-3">
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                        name="email" value="{{ $email ?? old('email') }}" required autofocus placeholder="Email">

                    @error('email')
                        <span class="invalid-feedback d-block mt-1" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-3">
                    <input id="password" type="password"
                        class="form-control @error('password') is-invalid @enderror"
                        name="password" required autocomplete="new-password" placeholder="New Password">

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
                        Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
