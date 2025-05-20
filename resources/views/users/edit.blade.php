@extends('layouts.admin')

@section('title', __('Edit User'))

@section('content-header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{ __('Edit User') }}</h1>
            </div>
        </div>
    </div>
@endsection

@section('css')
<style>
/* Card styling */
.card {
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    overflow: hidden;
    border: none;
    margin-bottom: 30px;
    opacity: 1;
    transform: translateY(20px);
}

.card-header {
    padding: 20px;
    background-color: white;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
}

.card-body {
    padding: 25px;
}

/* Form Controls */
.form-group {
    margin-bottom: 25px;
}

.form-group label {
    font-weight: 600;
    margin-bottom: 8px;
    font-size: 0.9rem;
    color: #495057;
    display: block;
    transition: color 0.3s ease;
}

.form-group small {
    color: #7d8a96;
    margin-top: 5px;
    display: block;
}

.form-control {
    border-radius: 8px;
    padding: 5px 15px;
    border: 1px solid rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    font-size: 15px;
}

.form-control:focus {
    border-color: #3498db;
    box-shadow: 0 5px 15px rgba(52, 152, 219, 0.1);
}

.form-control.is-invalid {
    border-color: #e74c3c;
    box-shadow: 0 5px 15px rgba(231, 76, 60, 0.1);
}

.invalid-feedback {
    font-size: 80%;
    margin-top: 8px;
}

/* Input group styling */
.input-group-text {
    border-radius: 8px 0 0 8px;
    background: linear-gradient(135deg, #f1f2f6 0%, #e9ecef 100%);
    border: 1px solid rgba(0, 0, 0, 0.1);
}

.input-group .form-control {
    border-radius: 0 8px 8px 0;
}

/* Form sections */
.form-section {
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    padding-bottom: 20px;
    margin-bottom: 25px;
}

.form-section:last-of-type {
    border-bottom: none;
    padding-bottom: 0;
}

.form-section-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #3498db;
    margin-bottom: 20px;
}

/* Buttons */
.btn {
    border-radius: 8px;
    padding: 12px 24px;
    transition: all 0.3s ease;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 0.875rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    position: relative;
    overflow: hidden;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
}

.btn-primary {
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
    border: none;
    color: white;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #2980b9 0%, #1c6ca1 100%);
}

.btn-default, .btn-secondary {
    background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%);
    border: none;
    color: white;
}

.btn-default:hover, .btn-secondary:hover {
    background: linear-gradient(135deg, #7f8c8d 0%, #6b7b7c 100%);
}

.btn i {
    margin-right: 5px;
}

/* Animation for form input focus */
@keyframes inputFocusAnimation {
    0% { box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05); }
    100% { box-shadow: 0 5px 15px rgba(52, 152, 219, 0.2); }
}

.form-control:focus {
    animation: inputFocusAnimation 0.3s forwards;
}

/* Button Animation */
@keyframes buttonPulse {
    0% { box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3); }
    50% { box-shadow: 0 8px 20px rgba(52, 152, 219, 0.5); }
    100% { box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3); }
}

.btn-primary:focus {
    animation: buttonPulse 1.5s infinite;
}

/* User info badge */
.user-info-badge {
    display: inline-flex;
    align-items: center;
    padding: 6px 12px;
    background: linear-gradient(135deg, #f6f9fc 0%, #edf2f7 100%);
    border-radius: 30px;
    font-weight: 600;
    font-size: 0.9rem;
    margin-left: 10px;
    color: #4a5568;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}

.user-info-badge .avatar {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 8px;
    background-color: #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    color: #4a5568;
}

/* Form footer */
.form-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid rgba(0, 0, 0, 0.05);
}

/* Hint text */
.text-muted {
    color: #7d8a96 !important;
    font-size: 85%;
    font-weight: normal;
}

.text-danger {
    color: #e74c3c !important;
}

/* Select styling */
select.form-control {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23495057' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: calc(100% - 15px) center;
    padding-right: 35px;
}

select.form-control:disabled {
    background-color: #e9ecef;
    cursor: not-allowed;
    opacity: 0.8;
}
</style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-user-edit mr-2"></i>{{ __('Edit User') }}</h3>
                        <div class="user-info-badge">
                            <div class="avatar">{{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}</div>
                            {{ $user->first_name }} {{ $user->last_name }}
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <form action="{{ route('users.update', $user->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-section">
                                <div class="form-section-title">{{ __('Personal Information') }}</div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="first_name">{{ __('First Name') }} <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                </div>
                                                <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}" placeholder="{{ __('First Name') }}" required>
                                            </div>
                                            @error('first_name')
                                                <span class="invalid-feedback d-block" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="last_name">{{ __('Last Name') }} <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                </div>
                                                <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}" placeholder="{{ __('Last Name') }}" required>
                                            </div>
                                            @error('last_name')
                                                <span class="invalid-feedback d-block" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <div class="form-section-title">{{ __('Contact Information') }}</div>
                                
                                <div class="form-group">
                                    <label for="email">{{ __('Email Address') }} <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        </div>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" placeholder="{{ __('Email Address') }}" required>
                                    </div>
                                    @error('email')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-section">
                                <div class="form-section-title">{{ __('Security') }}</div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password">{{ __('Password') }}</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                                </div>
                                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="{{ __('New Password') }}">
                                            </div>
                                            <small class="text-muted">{{ __('Leave blank to keep current password') }}</small>
                                            @error('password')
                                                <span class="invalid-feedback d-block" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password_confirmation">{{ __('Confirm Password') }}</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                                </div>
                                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="{{ __('Confirm New Password') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <div class="form-section-title">{{ __('User Role') }}</div>
                                
                                <div class="form-group">
                                    <label for="role">{{ __('Role') }} <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                        </div>
                                        <select class="form-control @error('role') is-invalid @enderror" id="role" name="role" required {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                            <option value="">{{ __('Select Role') }}</option>
                                            <option value="admin" {{ (old('role', $user->role) == 'admin') ? 'selected' : '' }}>{{ __('Admin') }}</option>
                                            <option value="user" {{ (old('role', $user->role) == 'user') ? 'selected' : '' }}>{{ __('User') }}</option>
                                        </select>
                                    </div>
                                    @if($user->id === auth()->id())
                                        <input type="hidden" name="role" value="{{ $user->role }}">
                                        <small class="text-muted">{{ __('You cannot change your own role.') }}</small>
                                    @endif
                                    @error('role')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-footer">
                                <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left mr-1"></i> {{ __('Cancel') }}
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i> {{ __('Update') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    $(document).ready(function () {
        // Add animation classes when page loads
        setTimeout(function() {
            $('.card').css({
                'opacity': '1',
                'transform': 'translateY(0)'
            });
        }, 100);
        
        // Focus animation for inputs
        $('.form-control').on('focus', function() {
            $(this).parent().parent().find('label').addClass('text-primary');
        }).on('blur', function() {
            $(this).parent().parent().find('label').removeClass('text-primary');
        });
    });
</script>
@endsection