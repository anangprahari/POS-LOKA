@extends('layouts.admin')
@section('content-header', __('dashboard.title'))
@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.41.0/apexcharts.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    .dashboard-card {
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        padding: 20px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
        position: relative;
        overflow: hidden;
    }
    
    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }
    
    .dashboard-card .card-icon {
        font-size: 36px;
        position: absolute;
        right: 20px;
        top: 20px;
        opacity: 0.2;
    }
    
    .dashboard-card .card-value {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 5px;
    }
    
    .dashboard-card .card-title {
        font-size: 16px;
        color: rgba(0, 0, 0, 0.6);
        margin-bottom: 15px;
    }
    
    .dashboard-card .card-footer {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 10px 20px;
        background-color: rgba(0, 0, 0, 0.03);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background-color 0.3s ease;
    }
    
    .dashboard-card .card-footer:hover {
        background-color: rgba(0, 0, 0, 0.06);
    }
    
    .chart-container {
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        padding: 20px;
        background-color: white;
        margin-bottom: 30px;
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,0.05);
        
    }
    .chart-container:hover {
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }

    .data-card {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        margin-bottom: 30px;
    }
    
    .data-card .card-header {
        padding: 20px;
        background-color: white;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .data-card .card-header h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
    }
    
    .data-card .card-header .card-tools {
        display: flex;
        gap: 10px;
    }
    
    .data-card .card-body {
        padding: 0;
    }
    
    table.dataTable {
        width: 100% !important;
        margin: 0 !important;
    }
    
    .product-img {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid rgba(0, 0, 0, 0.1);
    }
    
    .badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 500;
        font-size: 12px;
    }
    
    .badge-success {
        background-color: rgba(38, 179, 3, 0.1);
        color: #26b303;
    }
    
    .badge-danger {
        background-color: rgba(255, 0, 0, 0.1);
        color: #ff0000;
    }
    
    .section-title {
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f3f3f3;
        font-size: 22px;
        font-weight: 600;
        color: #333;
    }
    .period-selector .btn {
    transition: all 0.2s ease;
    font-weight: 500;
}

.period-selector .btn.active {
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    transform: translateY(-1px);
}
.chart-legend {
    font-size: 0.85rem;
    color: #666;
}

.period-label {
    font-size: 0.85rem;
    padding: 5px 10px;
    border-radius: 15px;
}

/* Animation for chart switch */
#bestSellingChart .apexcharts-bar-series,
#last7DaysChart .apexcharts-area-series {
    transition: all 0.5s ease-in-out;
}
    .statistic-change {
        font-size: 14px;
        margin-top: 8px;
    }
    
    .increment {
        color: #26b303;
    }
    
    .decrement {
        color: #ff0000;
    }
    
    /* Custom scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    
    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    
    ::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
    
    .animated-bg {
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 100%);
        z-index: 0;
    }
    
    .card-sales {
        background-color: #3498db;
        color: white;
    }
    
    .card-income {
        background-color: #2ecc71;
        color: white;
    }
    
    .card-income-today {
        background-color: #e74c3c;
        color: white;
    }
    
    .card-customers {
        background-color: #f39c12;
        color: white;
    }
    
    .tab-container {
        margin-bottom: 30px;
    }
    
    .tab-nav {
        display: flex;
        background-color: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        margin-bottom: 20px;
    }
    
    .tab-button {
        padding: 15px 25px;
        background-color: transparent;
        border: none;
        cursor: pointer;
        flex: 1;
        font-size: 16px;
        font-weight: 500;
        color: #666;
        transition: all 0.3s ease;
        text-align: center;
    }
    
    .tab-button.active {
        background-color: #3498db;
        color: white;
    }
    
    .tab-content {
        display: none;
    }
    
    .tab-content.active {
        display: block;
    }
    
    @media (max-width: 992px) {
        .dashboard-card .card-value {
            font-size: 24px;
        }
        
        .dashboard-card .card-icon {    
            font-size: 28px;
        }
    }
    
    @media (max-width: 768px) {
        .tab-button {
            padding: 10px 15px;
            font-size: 14px;
        }
    }

     /* Modern Tab Navigation */
     .tab-nav {
        display: flex;
        background-color: #f8f9fa;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        margin-bottom: 25px;
        border: 1px solid rgba(0, 0, 0, 0.05);
        position: relative;
    }
    
    .tab-button {
        padding: 16px 24px;
        background-color: transparent;
        border: none;
        cursor: pointer;
        flex: 1;
        font-size: 15px;
        font-weight: 500;
        color: #555;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        position: relative;
        overflow: hidden;
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .tab-button::before {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 0;
        height: 3px;
        background-color: #3498db;
        transition: width 0.3s ease;
        z-index: -1;
    }
    
    .tab-button:hover {
        color: #3498db;
    }
    
    .tab-button:hover::before {
        width: 80%;
    }
    
    .tab-button.active {
        color: #3498db;
        background-color: #fff;
        font-weight: 600;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
    }
    
    .tab-button.active::before {
        width: 80%;
    }
    
    /* Enhanced Data Cards */
    .data-card {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        margin-bottom: 35px;
        border: 1px solid rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }
    
    .data-card:hover {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }
    
    .data-card .card-header {
        padding: 20px 25px;
        background-color: white;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .data-card .card-header h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
        color: #333;
        display: flex;
        align-items: center;
    }
    
    .data-card .card-body {
        padding: 0;
        background-color: #fff;
    }
    
    /* Modern Table Styling */
    .data-table {
        width: 100% !important;
        margin: 0 !important;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .data-table thead th {
        background-color: #f8f9fa;
        color: #555;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.5px;
        padding: 15px;
        border-bottom: 2px solid #eaeaea;
        white-space: nowrap;
    }
    
    .data-table tbody tr {
        transition: all 0.2s ease;
    }
    
    .data-table tbody tr:hover {
        background-color: rgba(52, 152, 219, 0.04);
    }
    
    .data-table tbody td {
        padding: 15px;
        vertical-align: middle;
        border-bottom: 1px solid #f0f0f0;
    }
    
    /* Product Image */
    .product-img {
        width: 55px;
        height: 55px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid rgba(0, 0, 0, 0.08);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        transition: all 0.2s ease;
    }
    
    .product-img:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }
    
    /* Export Button */
    .btn-export {
        border-radius: 8px;
        padding: 8px 16px;
        font-weight: 500;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
    
    .btn-export:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }
    
    /* Status Indicators */
    .status-indicator {
        display: inline-flex;
        align-items: center;
        padding: 5px 10px;
        border-radius: 20px;
        font-weight: 500;
        font-size: 13px;
        min-width: 85px;
        justify-content: center;
    }
    
    .status-indicator.active {
        background-color: rgba(38, 179, 3, 0.1);
        color: #26b303;
    }
    
    .status-indicator.inactive {
        background-color: rgba(255, 59, 48, 0.1);
        color: #ff3b30;
    }
    
    .status-dot {
        font-size: 10px;
        margin-right: 5px;
    }
    
    /* Quantity Badge */
    .quantity-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        font-weight: 600;
        font-size: 14px;
    }
    
    .quantity-badge.critical {
        background-color: rgba(255, 59, 48, 0.15);
        color: #ff3b30;
    }
    
    .quantity-badge.warning {
        background-color: rgba(255, 149, 0, 0.15);
        color: #ff9500;
    }
    
    .quantity-display {
        font-weight: 500;
    }
    
    /* Text Styling */
    .product-name {
        font-weight: 500;
        color: #333;
    }
    
    .product-id {
        color: #6c757d;
        font-weight: 500;
        font-size: 14px;
    }
    
    .product-barcode {
        font-family: monospace;
        font-size: 14px;
        color: #555;
    }
    
    .product-price {
        font-weight: 600;
        color: #333;
    }
    
    .date-display {
        color: #6c757d;
        font-size: 14px;
    }
    
    .total-sold {
        font-weight: 700;
        color: #2ecc71;
        background: rgba(46, 204, 113, 0.1);
        padding: 4px 10px;
        border-radius: 20px;
        display: inline-block;
    }
    
    /* DataTables Custom Styling */
    .dataTables_wrapper .dataTables_filter input {
        border-radius: 8px;
        border: 1px solid #ddd;
        padding: 8px 15px;
        margin-left: 10px;
        transition: all 0.3s ease;
    }
    
    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #3498db;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        outline: none;
    }
    
    .dataTables_wrapper .dataTables_length select {
        border-radius: 8px;
        border: 1px solid #ddd;
        padding: 5px 10px;
    }
    
    .dataTables_wrapper .dataTables_info {
        color: #6c757d;
        font-size: 14px;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border-radius: 6px;
        padding: 5px 12px;
        margin: 0 3px;
        border: 1px solid #ddd;
        background: #fff;
        transition: all 0.2s ease;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #f0f0f0;
        border-color: #ccc;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #3498db;
        border-color: #3498db;
        color: white !important;
    }
    
    /* Tab Animation */
    .tab-content {
        display: none;
        animation: fadeIn 0.5s ease;
    }
    
    .tab-content.active {
        display: block;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    /* Responsive adjustments */
    @media (max-width: 992px) {
        .tab-button {
            padding: 12px 15px;
            font-size: 14px;
        }
        
        .data-table thead th {
            padding: 12px 10px;
            font-size: 11px;
        }
        
        .data-table tbody td {
            padding: 12px 10px;
        }
    }
    
    @media (max-width: 768px) {
        .tab-nav {
            flex-wrap: wrap;
        }
        
        .tab-button {
            min-width: 50%;
            flex: initial;
        }
        
        .product-img {
            width: 45px;
            height: 45px;
        }
    }
</style>

<div class="container-fluid py-4">
    <!-- Welcome message and brief summary -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2>{{ __('Welcome Back') }}, {{ auth()->user()->getFullname() }}!</h2>
                            @if($new_orders_count > 0)
                                <p class="text-primary mb-2">
                                    <i class="fas fa-bell"></i> 
                                    {{ __('You have') }} <strong>{{ $new_orders_count }}</strong> {{ __('new orders since your last login') }}
                                    <small class="text-muted">({{ \Carbon\Carbon::parse($last_login_at)->diffForHumans() }})</small>
                                </p>
                            @endif
                            
                            <p class="text-muted">{{ __('Your monthly sales target progress') }}</p>
                            <div class="progress mt-3" style="height: 8px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $target_progress }}%;" aria-valuenow="{{ $target_progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <small class="text-muted">{{ __('monthly target') }}: {{ config('settings.currency_symbol') }} {{ number_format($monthly_target, 2) }}</small>
                                <small class="text-primary">{{ number_format($target_progress, 1) }}%</small>
                            </div>
                        </div>
                        <div class="col-md-4 text-center d-none d-md-block">
                            <div class="display-4 text-primary pb-2">
                                <i class="fas fa-chart-line fa-2x"></i>
                            </div>
                            @if($target_progress < 40)
                                <span class="badge bg-warning text-dark">{{ __('Keep going!') }}</span>
                            @elseif($target_progress < 80)
                                <span class="badge bg-info">{{ __('Good progress!') }}</span>
                            @else
                                <span class="badge bg-success">{{ __('Excellent work!') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
            <div class="dashboard-card card-sales">
                <div class="animated-bg"></div>
                <div class="card-icon" >
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="card-value">{{$orders_count}}</div>
                <div class="card-title" style="font-size: 24px; color: white;">{{ __('Orders Count') }}</div>
                <div class="statistic-change increment" >    
                </div>
                <div class="card-footer">
                    <a href="{{route('orders.index')}}" class="text-white">
                        {{ __('common.More_info') }} <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
            <div class="dashboard-card card-income">
                <div class="animated-bg"></div>
                <div class="card-icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="card-value">{{config('settings.currency_symbol')}} {{number_format($income, 2)}}</div>
                <div class="card-title" style="font-size: 24px; color: white;">{{ __('Income') }}</div>
                <div class="statistic-change increment">
                </div>
                <div class="card-footer">
                    <a href="{{route('orders.index')}}" class="text-white">
                        {{ __('common.More_info') }} <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
            <div class="dashboard-card card-income-today">
                <div class="animated-bg"></div>
                <div class="card-icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="card-value">{{config('settings.currency_symbol')}} {{number_format($income_today, 2)}}</div>
                <div class="card-title" style="font-size: 24px; color: white;">{{ __('Income Today') }}</div>
                <div class="statistic-change decrement">
                </div>
                <div class="card-footer">
                    <a href="{{route('orders.index')}}" class="text-white">
                        {{ __('common.More_info') }} <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
            <div class="dashboard-card card-customers">
                <div class="animated-bg"></div>
                <div class="card-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="card-value">{{$customers_count}}</div>
                <div class="card-title" style="font-size: 24px; color: white;">
                    {{ __('Members Count') }}
                </div>                
                <div class="statistic-change increment">
                </div>
                <div class="card-footer">
                    <a href="{{ route('customers.index') }}" class="text-white">
                        {{ __('common.More_info') }} <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts Section -->
    <div class="row mb-4">
        <div class="col-lg-8 col-md-12 mb-4">
            <div class="chart-container shadow-sm rounded p-4 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="section-title mb-0">
                        <i class="fas fa-chart-bar text-primary me-2"></i>
                        {{ __('Best Selling Products') }}
                    </h3>
                    <div class="btn-group period-selector">
                        <button class="btn btn-sm btn-outline-primary active" data-period="weekly">{{ __('weekly') }}</button>
                        <button class="btn btn-sm btn-outline-primary" data-period="monthly">{{ __('monthly') }}</button>
                        <button class="btn btn-sm btn-outline-primary" data-period="yearly">{{ __('yearly') }}</button>
                    </div>
                </div>
                <div class="chart-legend mb-3">
                    <span class="period-label badge bg-light text-dark">
                        <i class="far fa-calendar-alt me-1"></i>
                        <span id="current-period">{{ __('weekly') }}</span>
                    </span>
                </div>
                <div id="bestSellingChart" style="height: 350px;"></div>
            </div>
        </div>
        
        <div class="col-lg-4 col-md-12 mb-4">
            <div class="chart-container shadow-sm rounded p-4 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="section-title mb-0">
                        <i class="fas fa-coins text-info me-2"></i>
                        {{ __('Last 7 Days Income') }}
                    </h3>
                    <span class="badge bg-light text-secondary income-summary">
                        <i class="fas fa-chart-line me-1"></i>
                        <span id="income-trend"></span>
                    </span>
                </div>
                <div class="income-highlights mb-3">
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="income-stat p-2 rounded bg-light">
                                <div class="stat-label">{{ __('Total') }}</div>
                                <div class="stat-value" id="total-income">-</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="income-stat p-2 rounded bg-light">
                                <div class="stat-label">{{ __('Average') }}</div>
                                <div class="stat-value" id="average-income">-</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="last7DaysChart" style="height: 290px;"></div>
            </div>
        </div>
    </div>
    
   <!-- Products Tables Section - Modernized -->
<div class="tab-container">
    <div class="tab-nav rounded-lg shadow-sm">
        <button class="tab-button active" data-tab="tab1">
            <i class="fas fa-exclamation-triangle me-2 text-warning mr-2"></i>{{ __('low stock products') }}
        </button>
        <button class="tab-button" data-tab="tab2">
            <i class="fas fa-fire me-2 text-danger mr-2"></i>{{ __('hot products') }}
        </button>
        <button class="tab-button" data-tab="tab3">
            <i class="fas fa-chart-line me-2 text-primary mr-2"></i>{{ __('hot products year') }}
        </button>
        <button class="tab-button" data-tab="tab4">
            <i class="fas fa-trophy me-2 text-success mr-2"></i>{{ __('best selling products') }}
        </button>
    </div>
    
    <div id="tab1" class="tab-content active">
        <div class="data-card">
            <div class="card-header">
                <h3><i class="fas fa-exclamation-triangle text-warning me-2 mr-2"></i>{{ __('low stock products') }}</h3>
                <div class="card-tools">
                    <a href="{{ route('export.low-stock-products') }}" class="btn btn-success btn-export">
                        <i class="fas fa-file-excel me-2 mr-2"></i> {{ __('Export to Excel') }}
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>{{ __('name') }}</th>
                                <th>{{ __('image') }}</th>
                                <th>{{ __('barcode') }}</th>
                                <th>{{ __('price') }}</th>
                                <th>{{ __('quantity') }}</th>
                                <th>{{ __('status') }}</th>
                                <th>{{ __('updated at') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($low_stock_products as $product)
                            <tr>
                                <td><span class="product-id">#{{$product->id}}</span></td>
                                <td><span class="product-name">{{$product->name}}</span></td>
                                <td><img class="product-img" src="{{ Storage::url($product->image) }}" alt=""></td>
                                <td><span class="product-barcode">{{$product->barcode}}</span></td>
                                <td><span class="product-price">{{config('settings.currency_symbol')}} {{number_format($product->price, 2)}}</span></td>
                                <td>
                                    <span class="quantity-badge {{$product->quantity < 5 ? 'critical' : 'warning'}}">
                                        {{$product->quantity}}
                                    </span>
                                </td>
                                <td>
                                    <span class="status-indicator {{ $product->status ? 'active' : 'inactive' }}">
                                        <i class="fas fa-circle status-dot"></i>
                                        {{$product->status ? __('common.Active') : __('common.Inactive') }}
                                    </span>
                                </td>
                                <td><span class="date-display">{{ $product->updated_at instanceof \DateTime ? $product->updated_at->format('d M Y') : $product->updated_at }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div id="tab2" class="tab-content">
        <div class="data-card">
            <div class="card-header">
                <h3><i class="fas fa-fire text-danger me-2 mr-2"></i>{{ __('hot products') }}</h3>
                <div class="card-tools">
                    <a href="{{ route('export.hot-products') }}" class="btn btn-success btn-export">
                        <i class="fas fa-file-excel me-2 mr-2"></i> {{ __('Export to Excel') }}
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>{{ __('name') }}</th>
                                <th>{{ __('image') }}</th>
                                <th>{{ __('barcode') }}</th>
                                <th>{{ __('price') }}</th>
                                <th>{{ __('quantity') }}</th>
                                <th>{{ __('status') }}</th>
                                <th>{{ __('updated at') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($current_month_products as $product)
                            <tr>
                                <td><span class="product-id">#{{$product->id}}</span></td>
                                <td><span class="product-name">{{$product->name}}</span></td>
                                <td><img class="product-img" src="{{ Storage::url($product->image) }}" alt=""></td>
                                <td><span class="product-barcode">{{$product->barcode}}</span></td>
                                <td><span class="product-price">{{config('settings.currency_symbol')}} {{number_format($product->price, 2)}}</span></td>
                                <td><span class="quantity-display">{{$product->quantity}}</span></td>
                                <td>
                                    <span class="status-indicator {{ $product->status ? 'active' : 'inactive' }}">
                                        <i class="fas fa-circle status-dot"></i>
                                        {{$product->status ? __('common.Active') : __('common.Inactive') }}
                                    </span>
                                </td>
                                <td><span class="date-display">{{ $product->updated_at instanceof \DateTime ? $product->updated_at->format('d M Y') : $product->updated_at }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div id="tab3" class="tab-content">
        <div class="data-card">
            <div class="card-header">
                <h3><i class="fas fa-chart-line text-primary me-2 mr-2"></i>{{ __('hot products year') }}</h3>
                <div class="card-tools">
                    <a href="{{ route('export.hot-products-year') }}" class="btn btn-success btn-export">
                        <i class="fas fa-file-excel me-2 mr-2"></i> {{ __('Export to Excel') }}
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>{{ __('name') }}</th>
                                <th>{{ __('image') }}</th>
                                <th>{{ __('barcode') }}</th>
                                <th>{{ __('price') }}</th>
                                <th>{{ __('quantity') }}</th>
                                <th>{{ __('status') }}</th>
                                <th>{{ __('updated at') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($past_months_products as $product)
                            <tr>
                                <td><span class="product-id">#{{$product->id}}</span></td>
                                <td><span class="product-name">{{$product->name}}</span></td>
                                <td><img class="product-img" src="{{ Storage::url($product->image) }}" alt=""></td>
                                <td><span class="product-barcode">{{$product->barcode}}</span></td>
                                <td><span class="product-price">{{config('settings.currency_symbol')}} {{number_format($product->price, 2)}}</span></td>
                                <td><span class="quantity-display">{{$product->quantity}}</span></td>
                                <td>
                                    <span class="status-indicator {{ $product->status ? 'active' : 'inactive' }}">
                                        <i class="fas fa-circle status-dot"></i>
                                        {{$product->status ? __('common.Active') : __('common.Inactive') }}
                                    </span>
                                </td>
                                <td><span class="date-display">{{ $product->updated_at instanceof \DateTime ? $product->updated_at->format('d M Y') : $product->updated_at }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div id="tab4" class="tab-content">
        <div class="data-card">
            <div class="card-header">
                <h3><i class="fas fa-trophy text-success me-2 mr-2"></i>{{ __('best selling products') }}</h3>
                <div class="card-tools">
                    <a href="{{ route('export.best-selling-products') }}" class="btn btn-success btn-export">
                        <i class="fas fa-file-excel me-2 mr-2"></i> {{ __('Export to Excel') }}
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>{{ __('name') }}</th>
                                <th>{{ __('image') }}</th>
                                <th>{{ __('barcode') }}</th>
                                <th>{{ __('price') }}</th>
                                <th>{{ __('quantity') }}</th>
                                <th>{{ __('total sold') }}</th>
                                <th>{{ __('status') }}</th>
                                <th>{{ __('updated at') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($best_selling_products as $product)
                            <tr>
                                <td><span class="product-id">#{{$product->id}}</span></td>
                                <td><span class="product-name">{{$product->name}}</span></td>
                                <td><img class="product-img" src="{{ Storage::url($product->image) }}" alt=""></td>
                                <td><span class="product-barcode">{{$product->barcode}}</span></td>
                                <td><span class="product-price">{{config('settings.currency_symbol')}} {{number_format($product->price, 2)}}</span></td>
                                <td><span class="quantity-display">{{$product->quantity}}</span></td>
                                <td><span class="total-sold">{{$product->total_sold}}</span></td>
                                <td>
                                    <span class="status-indicator {{ $product->status ? 'active' : 'inactive' }}">
                                        <i class="fas fa-circle status-dot"></i>
                                        {{$product->status ? __('common.Active') : __('common.Inactive') }}
                                    </span>
                                </td>
                                <td><span class="date-display">{{ $product->updated_at instanceof \DateTime ? $product->updated_at->format('d M Y') : $product->updated_at }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
   // This will enhance the existing tab functionality
   $(document).ready(function() {

  // Calculate income statistics for Last 7 days chart
  const incomeData = JSON.parse('{!! $last_7_days_data !!}');
        if (incomeData && incomeData.length > 0) {
            // Calculate total
            const totalIncome = incomeData.reduce((sum, value) => sum + value, 0);
            const averageIncome = totalIncome / incomeData.length;
            
            // Format with currency
            const currencySymbol = "{{ config('settings.currency_symbol') }}";
            document.getElementById('total-income').textContent = currencySymbol + ' ' + totalIncome.toLocaleString('id-ID');
            document.getElementById('average-income').textContent = currencySymbol + ' ' + averageIncome.toLocaleString('id-ID');

            
            // Determine trend
            const firstHalf = incomeData.slice(0, Math.floor(incomeData.length / 2));
            const secondHalf = incomeData.slice(Math.floor(incomeData.length / 2));
            
            const firstHalfAvg = firstHalf.reduce((sum, val) => sum + val, 0) / firstHalf.length;
            const secondHalfAvg = secondHalf.reduce((sum, val) => sum + val, 0) / secondHalf.length;
            
            const trendElement = document.getElementById('income-trend');
            if (secondHalfAvg > firstHalfAvg) {
                const increasePercent = ((secondHalfAvg - firstHalfAvg) / firstHalfAvg * 100).toFixed(1);
                trendElement.textContent = `${increasePercent}% {{ __('Up') }}`;
                trendElement.classList.add('text-success');
            } else if (secondHalfAvg < firstHalfAvg) {
                const decreasePercent = ((firstHalfAvg - secondHalfAvg) / firstHalfAvg * 100).toFixed(1);
                trendElement.textContent = `${decreasePercent}% {{ __('Down') }}`;
                trendElement.classList.add('text-danger');
            } else {
                trendElement.textContent = `{{ __('Stable') }}`;
                trendElement.classList.add('text-secondary');
            }
        }
    });

        // Enhanced tab functionality with smooth transitions
        $('.tab-button').on('click', function() {
            var tabId = $(this).data('tab');
            
            // Update active tab button with animation
            $('.tab-button').removeClass('active');
            $(this).addClass('active');
            
            // Hide all content first
            $('.tab-content').removeClass('active');
            
            // Show appropriate content with a slight delay for smoother transition
            setTimeout(function() {
                $('#' + tabId).addClass('active');
            }, 50);
        });
        
        // Initialize DataTables with enhanced options
        $('.data-table').DataTable({
            responsive: true,
            pageLength: 7,
            lengthMenu: [7, 10, 25, 50],
            language: {
                search: "<i class='fas fa-search'></i>",
                searchPlaceholder: "{{ __('dashboard search') }}...",
                lengthMenu: "{{ __('Show') }} _MENU_ {{ __('entries') }}",
                info: "{{ __('Showing') }} _START_ {{ __('to') }} _END_ {{ __('of') }} _TOTAL_ {{ __('entries') }}",
                paginate: {
                    first: "<i class='fas fa-angle-double-left'></i>",
                    last: "<i class='fas fa-angle-double-right'></i>",
                    next: "<i class='fas fa-angle-right'></i>",
                    previous: "<i class='fas fa-angle-left'></i>"
                }
            },
            drawCallback: function() {
                // Add hover effects to table rows after each draw
                $('.data-table tbody tr').hover(
                    function() { $(this).addClass('highlight'); },
                    function() { $(this).removeClass('highlight'); }
                );
            }
        });
        
        // Add tooltip functionality to images
        $('.product-img').on('mouseenter', function() {
            var productName = $(this).closest('tr').find('.product-name').text();
            $(this).attr('title', productName);
        });
    

    // ApexCharts - Best Selling Products
    var bestSellingOptions = {
        series: [{
            name: "{{ __('dashboard.total_sold') }}",
            data: @json($best_selling_products->pluck('total_sold'))
        }],
        chart: {
            type: 'bar',
            height: 350,
            toolbar: {
                show: false
            },
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800,
                animateGradually: {
                    enabled: true,
                    delay: 150
                },
                dynamicAnimation: {
                    enabled: true,
                    speed: 350
                }
            },
            fontFamily: 'Helvetica, Arial, sans-serif',
            background: '#fff'
        },
        plotOptions: {
            bar: {
                borderRadius: 8,
                columnWidth: '60%',
                distributed: true,
                dataLabels: {
                    position: 'top'
                },
                // Add gradient effect
                colors: {
                    ranges: [{
                        from: 0,
                        to: Infinity,
                        color: undefined
                    }],
                    backgroundBarColors: [],
                    backgroundBarOpacity: 0.1
                }
            }
        },
        colors: ['#4361ee', '#3a0ca3', '#7209b7', '#f72585', '#4cc9f0', '#4895ef', '#560bad', '#480ca8'],
        dataLabels: {
            enabled: true,
            formatter: function(val) {
                return val.toFixed(0); // Ensure integer display
            },
            offsetY: -20,
            style: {
                fontSize: '12px',
                colors: ["#304758"],
                fontWeight: 'bold'
            }
        },
        grid: {
            borderColor: '#f1f1f1',
            row: {
                colors: ['#f9f9f9', 'transparent'],
                opacity: 0.2
            }
        },
        xaxis: {
            categories: @json($best_selling_products->pluck('name')),
            position: 'bottom',
            labels: {
                rotate: -45,
                style: {
                    fontSize: '12px',
                    fontFamily: 'Helvetica, Arial, sans-serif',
                },
                trim: true,
                maxHeight: 120
            },
            title: {
                text: "{{ __('Products') }}",
                style: {
                    fontSize: '13px',
                    fontWeight: 600
                }
            },
            axisBorder: {
                show: true,
                color: '#e0e0e0'
            },
            axisTicks: {
                show: true,
                color: '#e0e0e0'
            }
        },
        yaxis: {
            title: {
                text: "{{ __('Units Sold') }}",
                style: {
                    fontSize: '13px',
                    fontWeight: 600
                }
            },
            labels: {
                formatter: function(val) {
                    // Format to integer without decimal points
                    return Math.round(val);
                }
            }
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    // Format to integer without decimal points
                    return Math.round(val) + " {{ __('Units') }}";
                }
            },
            theme: 'light',
            x: {
                show: true
            },
            marker: {
                show: true
            }
        },
        states: {
            hover: {
                filter: {
                    type: 'lighten',
                    value: 0.15
                }
            },
            active: {
                allowMultipleDataPointsSelection: false,
                filter: {
                    type: 'darken',
                    value: 0.35
                }
            }
        },
        responsive: [
            {
                breakpoint: 576,
                options: {
                    plotOptions: {
                        bar: {
                            borderRadius: 5,
                            columnWidth: '80%'
                        }
                    },
                    xaxis: {
                        labels: {
                            rotate: -90
                        }
                    }
                }
            }
        ]
    };

    var bestSellingChart = new ApexCharts(document.querySelector("#bestSellingChart"), bestSellingOptions);
    bestSellingChart.render();

    // Last 7 Days Income Chart
    var last7DaysOptions = {
        series: [{
            name: "{{ __('daily income') }}",
            data: JSON.parse('{!! $last_7_days_data !!}')
        }],
        chart: {
            height: 320,
            type: 'area',
            toolbar: {
                show: false
            },
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800,
                animateGradually: {
                    enabled: true,
                    delay: 150
                },
                dynamicAnimation: {
                    enabled: true,
                    speed: 350
                }
            },
            fontFamily: 'Helvetica, Arial, sans-serif',
            dropShadow: {
                enabled: true,
                top: 3,
                left: 2,
                blur: 4,
                opacity: 0.1
            }
        },
        colors: ['#4cc9f0'],
        dataLabels: {
            enabled: true,
            background: {
                enabled: true,
                borderRadius: 4,
                padding: 4,
                opacity: 0.9,
                borderWidth: 1,
                borderColor: '#fff'
            },
            style: {
                fontSize: '10px',
                fontWeight: 600
            },
            formatter: function(val) {
                return "{{ config('settings.currency_symbol') }} " + val.toFixed(0);
            },
            offsetY: -5
        },
        stroke: {
            curve: 'smooth',
            width: 3,
            lineCap: 'round'
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.3,
                stops: [0, 90, 100],
                colorStops: [
                    {
                        offset: 0,
                        color: '#4cc9f0',
                        opacity: 0.8
                    },
                    {
                        offset: 100,
                        color: '#4361ee',
                        opacity: 0.2
                    }
                ]
            }
        },
        grid: {
            borderColor: '#f1f1f1',
            strokeDashArray: 4,
            xaxis: {
                lines: {
                    show: true
                }
            },
            yaxis: {
                lines: {
                    show: true
                }
            },
            padding: {
                top: 0,
                right: 0,
                bottom: 0,
                left: 10
            }
        },
        xaxis: {
            categories: JSON.parse('{!! $last_7_days_labels !!}'),
            labels: {
                style: {
                    fontSize: '12px',
                    fontFamily: 'Helvetica, Arial, sans-serif',
                    fontWeight: 500
                },
                rotate: -5
            },
            axisBorder: {
                show: true,
                color: '#e0e0e0'
            },
            axisTicks: {
                show: true,
                color: '#e0e0e0'
            },
            title: {
                text: "{{ __('Day') }}",
                style: {
                    fontSize: '13px',
                    fontWeight: 600,
                    cssClass: 'axis-title'
                },
                offsetY: 5
            },
            crosshairs: {
                show: true,
                position: 'back',
                stroke: {
                    color: '#b6b6b6',
                    width: 1,
                    dashArray: 3
                }
            },
            tooltip: {
                enabled: true,
                offsetY: 0
            }
        },
        yaxis: {
            title: {
                text: "{{ __('Income') }} ({{ config('settings.currency_symbol') }})",
                style: {
                    fontSize: '13px',
                    fontWeight: 600,
                    cssClass: 'axis-title'
                },
                offsetX: -5
            },
            labels: {
                formatter: function (val) {
                    return "{{ config('settings.currency_symbol') }} " + val.toFixed(0);
                },
                style: {
                    fontSize: '12px',
                    fontWeight: 500
                }
            },
            min: function(min) {
                return min * 0.85; // Add some padding to bottom
            },
            forceNiceScale: true,
            floating: false
        },
        tooltip: {
            theme: 'light',
            x: {
                show: true,
                format: 'dd MMM'
            },
            y: {
                title: {
                    formatter: function() {
                        return "{{ __('Income') }}:";
                    }
                },
                formatter: function (val) {
                    return "{{ config('settings.currency_symbol') }} " + val.toFixed(2);
                }
            },
            marker: {
                show: true
            },
            fixed: {
                enabled: false,
                position: 'topRight',
                offsetY: 0
            },
            style: {
                fontSize: '12px',
                fontFamily: 'Helvetica, Arial, sans-serif'
            }
        },
        markers: {
            size: 5,
            colors: ["#4cc9f0"],
            strokeColors: "#fff",
            strokeWidth: 2,
            hover: {
                size: 8,
                sizeOffset: 3
            },
            discrete: [],
            shape: 'circle',
            radius: 2
        },
        annotations: {
            points: [
                {
                    x: 'auto',
                    y: 'auto',
                    yAxisIndex: 0,
                    seriesIndex: 0,
                    marker: {
                        size: 0
                    }
                }
            ]
        },
        states: {
            hover: {
                filter: {
                    type: 'lighten',
                    value: 0.05
                }
            },
            active: {
                filter: {
                    type: 'darken',
                    value: 0.1
                }
            }
        },
        responsive: [{
            breakpoint: 576,
            options: {
                chart: {
                    height: 300
                },
                dataLabels: {
                    enabled: false
                }
            }
        }]
    };

    var last7DaysChart = new ApexCharts(document.querySelector("#last7DaysChart"), last7DaysOptions);
    last7DaysChart.render();
    
    // Find max value to highlight it
    const incomeData = JSON.parse('{!! $last_7_days_data !!}');
    const labels = JSON.parse('{!! $last_7_days_labels !!}');
    
    if (incomeData && incomeData.length > 0) {
        const maxVal = Math.max(...incomeData);
        const maxIndex = incomeData.indexOf(maxVal);
        
        if (maxIndex !== -1) {
            // Highlight max value point
            last7DaysChart.updateOptions({
                annotations: {
                    points: [{
                        x: labels[maxIndex],
                        y: maxVal,
                        marker: {
                            size: 8,
                            fillColor: '#FF4560',
                            strokeColor: '#fff',
                            strokeWidth: 2,
                            radius: 4
                        },
                        label: {
                            borderColor: '#FF4560',
                            offsetY: 0,
                            style: {
                                color: '#fff',
                                background: '#FF4560',
                                fontSize: '10px',
                                fontWeight: 'bold'
                            },
                            text: '{{ __("Peak") }}'
                        }
                    }]
                }
            });
        }
    }

    // Add animation to the dashboard cards
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.dashboard-card').forEach(function(card, index) {
            setTimeout(function() {
                card.style.opacity = 1;
                card.style.transform = 'translateY(0)';
            }, 100 * index);
        });
        
      
    
    // Toggle between time periods for the chart
    document.querySelectorAll('[data-period]').forEach(function(button) {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            document.querySelectorAll('[data-period]').forEach(function(btn) {
                btn.classList.remove('active');
            });
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Update period label
            document.getElementById('current-period').textContent = this.textContent.trim();
            
            // Here you would update the chart data based on the selected period
            const period = this.getAttribute('data-period');
            
            // Show loading state
            bestSellingChart.updateOptions({
                chart: {
                    animations: {
                        dynamicAnimation: {
                            enabled: true,
                            speed: 350
                        }
                    }
                }
            });
            
            // Small delay to show animation
            setTimeout(function() {
                // This would be replaced with actual AJAX call to get data for different periods
                if (period === 'weekly') {
                    bestSellingChart.updateSeries([{
                        name: "{{ __('dashboard.total_sold') }}",
                        data: @json($best_selling_products->pluck('total_sold'))
                    }]);
                } else if (period === 'monthly') {
                    // These would be replaced with actual data
                    const monthlyData = @json($best_selling_products->pluck('total_sold')->map(function($value) { return $value * 1.2; }));
                    bestSellingChart.updateSeries([{
                        name: "{{ __('dashboard.total_sold') }}",
                        data: monthlyData
                    }]);
                } else if (period === 'yearly') {
                    const yearlyData = @json($best_selling_products->pluck('total_sold')->map(function($value) { return $value * 5; }));
                    bestSellingChart.updateSeries([{
                        name: "{{ __('dashboard.total_sold') }}",
                        data: yearlyData
                    }]);
                }
            }, 300);
        });
    });
});
</script>
@endsection
               