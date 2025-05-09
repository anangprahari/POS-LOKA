@extends('layouts.admin')

@section('title', __('product.Create_Product'))
@section('content-header', __('product.Create_Product'))
@section('content-actions')
<a href="{{ route('products.index') }}" class="btn btn-sm btn-secondary">
    <i class="fas fa-arrow-left mr-1"></i>
    {{ __('Back to Products') }}
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

/* Select Styling */
select.form-control {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%23495057' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    background-size: 16px;
    padding-right: 40px;
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
</style>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-plus-circle mr-2"></i>{{ __('product.Create_Product') }}</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-section">
                <div class="form-section-title">{{ __('Basic Information') }}</div>
                
                <div class="form-group">
                    <label for="name">{{ __('product.Name') }}</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="name"
                        placeholder="{{ __('product.Name') }}" value="{{ old('name') }}">
                    @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description">{{ __('product.Description') }}</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                        id="description" rows="4"
                        placeholder="{{ __('product.Description') }}">{{ old('description') }}</textarea>
                    @error('description')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-title">{{ __('Product Image') }}</div>
                
                <div class="form-group">
                    <label for="image">{{ __('product.Image') }}</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="image" id="image">
                        <label class="custom-file-label" for="image">{{ __('product.Choose_file') }}</label>
                    </div>
                    @error('image')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                    
                    <!-- Image preview will be inserted here when a file is selected -->
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-title">{{ __('Product Details') }}</div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="barcode">{{ __('product.Barcode') }}</label>
                            <input type="text" name="barcode" class="form-control @error('barcode') is-invalid @enderror"
                                id="barcode" placeholder="{{ __('product.Barcode') }}" value="{{ old('barcode') }}">
                            @error('barcode')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status">{{ __('product.Status') }}</label>
                            <select name="status" class="form-control @error('status') is-invalid @enderror" id="status">
                                <option value="1" {{ old('status') === 1 ? 'selected' : ''}}>{{ __('common.Active') }}</option>
                                <option value="0" {{ old('status') === 0 ? 'selected' : ''}}>{{ __('common.Inactive') }}</option>
                            </select>
                            @error('status')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="price">{{ __('product.Price') }}</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp.</span>
                                </div>
                                <input type="text" name="price" class="form-control @error('price') is-invalid @enderror" id="price"
                                    placeholder="{{ __('product.Price') }}" value="{{ old('price') }}">
                            </div>
                            @error('price')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="quantity">{{ __('product.Quantity') }}</label>
                            <div class="input-group">
                                <input type="text" name="quantity" class="form-control @error('quantity') is-invalid @enderror"
                                    id="quantity" placeholder="{{ __('product.Quantity') }}" value="{{ old('quantity', 1) }}">
                                <div class="input-group-append">
                                    <span class="input-group-text">cup</span>
                                </div>
                            </div>
                            @error('quantity')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-footer">
                <a href="{{ route('products.index') }}" class="btn btn-secondary">
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
            $(this).parent().find('label').addClass('text-primary');
        }).on('blur', function() {
            $(this).parent().find('label').removeClass('text-primary');
        });
        
        // Preview image on file select
        $('#image').on('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Remove any existing preview
                    $('.current-image-container').remove();
                    
                    // Create new preview container
                    const imagePreview = `
                        <div class="current-image-container">
                            <img src="${e.target.result}" alt="Image preview" class="current-image">
                            <span class="current-image-label">{{ __("Image Preview") }}</span>
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