@extends('layouts.admin')

@section('title', __('settings.Update_Settings'))
@section('content-header', __('settings.Update_Settings'))

@section('content')
{{-- @if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <div class="d-flex align-items-center">
        <i class="fas fa-check-circle fa-2x mr-3"></i>
        <div>
            <strong>{{ __('settings.Success') }}!</strong> {{ session('success') }}
        </div>
    </div>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif --}}
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-cogs mr-2"></i>{{ __('settings.Update_Settings') }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('settings.store') }}" method="post">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <!-- App Information Section -->
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-info-circle mr-2"></i>{{ __('App Information') }}</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="app_name">
                                    <i class="fas fa-signature text-primary mr-1"></i>
                                    {{ __('settings.App_name') }}
                                </label>
                                <input type="text" name="app_name" 
                                    class="form-control form-control-lg @error('app_name') is-invalid @enderror" 
                                    id="app_name" 
                                    placeholder="{{ __('settings.App_name') }}" 
                                    value="{{ old('app_name', config('settings.app_name')) }}">
                                @error('app_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                                <small class="form-text text-muted">{{ __('settings.App_name_help') }}</small>
                            </div>

                            <div class="form-group">
                                <label for="app_description">
                                    <i class="fas fa-align-left text-primary mr-1"></i>
                                    {{ __('settings.App_description') }}
                                </label>
                                <textarea name="app_description" 
                                    class="form-control @error('app_description') is-invalid @enderror" 
                                    id="app_description" 
                                    rows="4"
                                    placeholder="{{ __('App description') }}">{{ old('app_description', config('settings.app_description')) }}</textarea>
                                @error('app_description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                                <small class="form-text text-muted">{{ __('App description help') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <!-- System Settings Section -->
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-sliders-h mr-2"></i>{{ __('System Settings') }}</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="currency_symbol">
                                    <i class="fas fa-dollar-sign text-primary mr-1"></i>
                                    {{ __('settings.Currency_symbol') }}
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-money-bill"></i></span>
                                    </div>
                                    <input type="text" name="currency_symbol" 
                                        class="form-control @error('currency_symbol') is-invalid @enderror" 
                                        id="currency_symbol" 
                                        placeholder="{{ __('Currency symbol') }}" 
                                        value="{{ old('currency_symbol', config('settings.currency_symbol')) }}">
                                </div>
                                @error('currency_symbol')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                                <small class="form-text text-muted">{{ __('Currency symbol help') }}</small>
                            </div>

                            <div class="form-group">
                                <label for="warning_quantity">
                                    <i class="fas fa-exclamation-triangle text-primary mr-1"></i>
                                    {{ __('settings.Warning_quantity') }}
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-layer-group"></i></span>
                                    </div>
                                    <input type="number" name="warning_quantity" 
                                        class="form-control @error('warning_quantity') is-invalid @enderror" 
                                        id="warning_quantity" 
                                        placeholder="{{ __('Warning quantity') }}" 
                                        value="{{ old('warning_quantity', config('settings.warning_quantity')) }}">
                                </div>
                                @error('warning_quantity')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                                <small class="form-text text-muted">{{ __('Warning quantity help') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary btn-lg px-5">
                    <i class="fas fa-save mr-2"></i>{{ __('Save Settings') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection