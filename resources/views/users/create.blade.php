@extends('layouts.admin')

@section('title', __('Create User'))

@section('content-header')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center ">
            <h1 class="mb-0">{{ __('Create User') }}</h1>
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

/* Form footer */
.form-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid rgba(0, 0, 0, 0.05);
}
</style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-user-plus mr-2"></i>{{ __('Add New User') }}</h3>
                    </div>
                    
                    <div class="card-body">
                        <form action="{{ route('users.store') }}" method="POST">
                            @csrf
                            
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
                                                <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name') }}" placeholder="{{ __('First Name') }}" required>
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
                                                <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name') }}" placeholder="{{ __('Last Name') }}" required>
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
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="{{ __('Email Address') }}" required>
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
                                            <label for="password">{{ __('Password') }} <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                                </div>
                                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="{{ __('Password') }}" required>
                                            </div>
                                            @error('password')
                                                <span class="invalid-feedback d-block" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password_confirmation">{{ __('Confirm Password') }} <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                                </div>
                                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="{{ __('Confirm Password') }}" required>
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
                                        <select class="form-control @error('role') is-invalid @enderror" id="role" name="role" required>
                                            <option value="">{{ __('Select Role') }}</option>
                                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>{{ __('Admin') }}</option>
                                            <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>{{ __('User') }}</option>
                                        </select>
                                    </div>
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
                                    <i class="fas fa-save mr-1"></i> {{ __('Save') }}
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