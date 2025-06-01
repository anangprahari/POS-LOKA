@extends('layouts.admin')

@section('title', __('supplier.Create_supplier'))
@section('content-header', __('Create Supplier'))
@section('content-actions')
<a href="{{ route('suppliers.index') }}" class="btn btn-sm btn-secondary">
    <i class="fas fa-arrow-left mr-1"></i>
    {{ __('Back to Suppliers') }}
</a>
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
    transform: none;
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
    padding: 10px 15px;
    border: 1px solid rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
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

/* Custom File Input */
.custom-file {
    position: relative;
    display: inline-block;
    width: 100%;
    height: calc(1.6em + 0.75rem + 10px);
    margin-bottom: 0;
}

.custom-file-input {
    position: relative;
    z-index: 2;
    width: 100%;
    height: calc(1.6em + 0.75rem + 10px);
    margin: 0;
    opacity: 0;
}

.custom-file-label {
    position: absolute;
    top: 0;
    right: 0;
    left: 0;
    z-index: 1;
    height: calc(1.6em + 0.75rem + 10px);
    padding: 0.375rem 0.75rem;
    font-weight: 400;
    line-height: 1.6;
    color: #495057;
    background-color: #fff;
    border: 1px solid rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
}

.custom-file-label::after {
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    z-index: 3;
    display: flex;
    align-items: center;
    padding: 0.375rem 0.75rem;
    color: #fff;
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
    border-left: inherit;
    border-radius: 0 8px 8px 0;
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

.btn-secondary {
    background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%);
    border: none;
    color: white;
}

.btn-secondary:hover {
    background: linear-gradient(135deg, #7f8c8d 0%, #6b7b7c 100%);
}

.btn i {
    margin-right: 5px;
}

/* Image Preview */
.image-preview-container {
    margin-top: 15px;
    margin-bottom: 15px;
    text-align: center;
}

.image-preview {
    width: 120px;
    height: 120px;
    object-fit: cover;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.image-preview:hover {
    transform: scale(1.05);
}

.image-preview-label {
    display: block;
    margin-top: 8px;
    font-size: 0.8rem;
    color: #7f8c8d;
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

/* Current image preview styling */
.current-image-container {
    margin-top: 15px;
    text-align: center;
}

.current-image {
    width: 120px;
    height: 120px;
    object-fit: cover;
    border-radius: 50%;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.current-image:hover {
    transform: scale(1.05);
}

.current-image-label {
    display: block;
    margin-top: 8px;
    font-size: 0.8rem;
    color: #7f8c8d;
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
</style>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-user-tie mr-2"></i>{{ __('Create Supplier') }}</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('suppliers.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-section">
                <div class="form-section-title">{{ __('Personal Information') }}</div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="first_name">{{ __('First Name') }}</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                </div>
                                <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror"
                                    id="first_name" placeholder="{{ __('First Name') }}" value="{{ old('first_name') }}">
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
                            <label for="last_name">{{ __('Last Name') }}</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                </div>
                                <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror"
                                    id="last_name" placeholder="{{ __('Last Name') }}" value="{{ old('last_name') }}">
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
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email">{{ __('Email') }}</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                </div>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" placeholder="{{ __('Email') }}" value="{{ old('email') }}">
                            </div>
                            @error('email')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="phone">{{ __('Phone') }}</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                </div>
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                    id="phone" placeholder="{{ __('Phone') }}" value="{{ old('phone') }}">
                            </div>
                            @error('phone')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="address">{{ __('Address') }}</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                        </div>
                        <input type="text" name="address" class="form-control @error('address') is-invalid @enderror"
                            id="address" placeholder="{{ __('Address') }}" value="{{ old('address') }}">
                    </div>
                    @error('address')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-title">{{ __('Profile Image') }}</div>
                
                <div class="form-group">
                    <label for="avatar">{{ __('Avatar') }}</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="avatar" id="avatar">
                        <label class="custom-file-label" for="avatar">{{ __('Choose file') }}</label>
                    </div>
                    @error('avatar')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                    
                    <!-- Image preview will be inserted here when a file is selected -->
                </div>
            </div>

            <div class="form-footer">
                <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times mr-1"></i> {{ __('Cancel') }}
                </a>
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-save mr-1"></i> {{ __('common.Create') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
<script>
    $(document).ready(function () {
        bsCustomFileInput.init();
        
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
        
        // Preview image on file select
        $('#avatar').on('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Remove any existing preview
                    $('.current-image-container').remove();
                    
                    // Create new preview container
                    const imagePreview = `
                        <div class="current-image-container">
                            <img src="${e.target.result}" alt="Avatar preview" class="current-image">
                            <span class="current-image-label">{{ __("Avatar Preview") }}</span>
                        </div>
                    `;
                    
                    // Insert after the custom file input
                    $('.custom-file').after(imagePreview);
                }
                reader.readAsDataURL(file);
            }
        });
    });
</script>
@endsection