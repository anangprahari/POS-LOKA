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
</style>

<div class="container-fluid py-4">
    <!-- Welcome message and brief summary -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card dashboard-card">
                <div class="card-body">
                    <h2>{{ __('welcome back') }}, {{ auth()->user()->name }}!</h2>
                    <p class="text-muted">{{ __('overview message') }}</p>
                    <div class="progress mt-3" style="height: 8px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 85%;" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <small class="text-muted">{{ __('monthly target') }}</small>
                        <small class="text-primary">85%</small>
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
                    {{ __('Customers Count') }}
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
            <div class="chart-container">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="section-title mb-0">{{ __('best selling products') }}</h3>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-primary active" data-period="weekly">{{ __('weekly') }}</button>
                        <button class="btn btn-sm btn-outline-primary" data-period="monthly">{{ __('monthly') }}</button>
                        <button class="btn btn-sm btn-outline-primary" data-period="yearly">{{ __('yearly') }}</button>
                    </div>
                </div>
                <div id="bestSellingChart" style="height: 350px;"></div>
            </div>
        </div>
        
        <div class="col-lg-4 col-md-12 mb-4">
            <div class="chart-container">
                <h3 class="section-title">{{ __('sales distribution') }}</h3>
                <div id="salesDistributionChart" style="height: 350px;"></div>
            </div>
        </div>
    </div>
    
    <!-- Products Tables Section -->
    <div class="tab-container">
        <div class="tab-nav">
            <button class="tab-button active" data-tab="tab1">{{ __('low stock products') }}</button>
            <button class="tab-button" data-tab="tab2">{{ __('hot products') }}</button>
            <button class="tab-button" data-tab="tab3">{{ __('hot products year') }}</button>
            <button class="tab-button" data-tab="tab4">{{ __('best selling products') }}</button>
        </div>
        
        <div id="tab1" class="tab-content active">
            <div class="data-card">
                <div class="card-header">
                    <h3>{{ __('low stock products') }}</h3>
                    <div class="card-tools">
                        <button class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-file-export"></i> {{ __('export') }}
                        </button>
                        <button class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> {{ __('restock') }}
                        </button>
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
                                    <th>{{ __('updated_at') }}</th>
                                    <th>{{ __('actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($low_stock_products as $product)
                                <tr>
                                    <td>{{$product->id}}</td>
                                    <td>{{$product->name}}</td>
                                    <td><img class="product-img" src="{{ Storage::url($product->image) }}" alt=""></td>
                                    <td>{{$product->barcode}}</td>
                                    <td>{{config('settings.currency_symbol')}} {{number_format($product->price, 2)}}</td>
                                    <td>
                                        <span class="badge badge-{{$product->quantity < 5 ? 'danger' : 'warning'}}">
                                            {{$product->quantity}}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $product->status ? 'success' : 'danger' }}">
                                            {{$product->status ? __('common.Active') : __('common.Inactive') }}
                                        </span>
                                    </td>
                                    <td>{{ $product->updated_at instanceof \DateTime ? $product->updated_at->format('d M Y') : $product->updated_at }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
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
                    <h3>{{ __('hot products') }}</h3>
                    <div class="card-tools">
                        <button class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-file-export"></i> {{ __('export') }}
                        </button>
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
                                    <th>{{ __('updated_at') }}</th>
                                    <th>{{ __('actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($current_month_products as $product)
                                <tr>
                                    <td>{{$product->id}}</td>
                                    <td>{{$product->name}}</td>
                                    <td><img class="product-img" src="{{ Storage::url($product->image) }}" alt=""></td>
                                    <td>{{$product->barcode}}</td>
                                    <td>{{config('settings.currency_symbol')}} {{number_format($product->price, 2)}}</td>
                                    <td>{{$product->quantity}}</td>
                                    <td>
                                        <span class="badge badge-{{ $product->status ? 'success' : 'danger' }}">
                                            {{$product->status ? __('common.Active') : __('common.Inactive') }}
                                        </span>
                                    </td>
                                    <td>{{ $product->updated_at instanceof \DateTime ? $product->updated_at->format('d M Y') : $product->updated_at }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
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
                    <h3>{{ __('hot products year') }}</h3>
                    <div class="card-tools">
                        <button class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-file-export"></i> {{ __('export') }}
                        </button>
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
                                    <th>{{ __('updated_at') }}</th>
                                    <th>{{ __('actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($past_months_products as $product)
                                <tr>
                                    <td>{{$product->id}}</td>
                                    <td>{{$product->name}}</td>
                                    <td><img class="product-img" src="{{ Storage::url($product->image) }}" alt=""></td>
                                    <td>{{$product->barcode}}</td>
                                    <td>{{config('settings.currency_symbol')}} {{number_format($product->price, 2)}}</td>
                                    <td>{{$product->quantity}}</td>
                                    <td>
                                        <span class="badge badge-{{ $product->status ? 'success' : 'danger' }}">
                                            {{$product->status ? __('common.Active') : __('common.Inactive') }}
                                        </span>
                                    </td>
                                    <td>{{ $product->updated_at instanceof \DateTime ? $product->updated_at->format('d M Y') : $product->updated_at }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
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
                    <h3>{{ __('best selling products') }}</h3>
                    <div class="card-tools">
                        <button class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-file-export"></i> {{ __('export') }}
                        </button>
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
                                    <th>{{ __('total_sold') }}</th>
                                    <th>{{ __('status') }}</th>
                                    <th>{{ __('updated_at') }}</th>
                                    <th>{{ __('actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($best_selling_products as $product)
                                <tr>
                                    <td>{{$product->id}}</td>
                                    <td>{{$product->name}}</td>
                                    <td><img class="product-img" src="{{ Storage::url($product->image) }}" alt=""></td>
                                    <td>{{$product->barcode}}</td>
                                    <td>{{config('settings.currency_symbol')}} {{number_format($product->price, 2)}}</td>
                                    <td>{{$product->quantity}}</td>
                                    <td><strong class="text-success">{{$product->total_sold}}</strong></td>
                                    <td>
                                        <span class="badge badge-{{ $product->status ? 'success' : 'danger' }}">
                                            {{$product->status ? __('common.Active') : __('common.Inactive') }}
                                        </span>
                                    </td>
                                    <td>{{ $product->updated_at instanceof \DateTime ? $product->updated_at->format('d M Y') : $product->updated_at }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Initialize DataTables
    $(document).ready(function() {
        $('.data-table').DataTable({
            responsive: true,
            pageLength: 5,
            lengthMenu: [5, 10, 25, 50],
            language: {
                search: "<i class='fas fa-search'></i>",
                searchPlaceholder: "{{ __('dashboard.search') }}..."
            }
        });
        
        // Tab functionality
        $('.tab-button').on('click', function() {
            var tabId = $(this).data('tab');
            
            // Update active tab button
            $('.tab-button').removeClass('active');
            $(this).addClass('active');
            
            // Show appropriate content
            $('.tab-content').removeClass('active');
            $('#' + tabId).addClass('active');
        });
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
            }
        },
        plotOptions: {
            bar: {
                borderRadius: 6,
                columnWidth: '55%',
                distributed: true,
                dataLabels: {
                    position: 'top'
                }
            }
        },
        colors: ['#3498db', '#2ecc71', '#9b59b6', '#e74c3c', '#f39c12', '#1abc9c', '#34495e', '#f1c40f'],
        dataLabels: {
            enabled: true,
            formatter: function(val) {
                return val;
            },
            offsetY: -20,
            style: {
                fontSize: '12px',
                colors: ["#304758"]
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
                }
            },
            title: {
                text: "{{ __('dashboard.total_sold') }}"
            }
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return val + " {{ __('dashboard.units') }}";
                }
            }
        }
    };

    var bestSellingChart = new ApexCharts(document.querySelector("#bestSellingChart"), bestSellingOptions);
    bestSellingChart.render();

    // Sales Distribution Pie Chart
    var salesDistributionOptions = {
        series: @json($best_selling_products->pluck('total_sold')),
        chart: {
            width: '100%',
            type: 'donut',
            height: 350
        },
        labels: @json($best_selling_products->pluck('name')),
        colors: ['#3498db', '#2ecc71', '#9b59b6', '#e74c3c', '#f39c12', '#1abc9c', '#34495e', '#f1c40f'],
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 300
                },
                legend: {
                    position: 'bottom'
                }
            }
        }],
        plotOptions: {
            pie: {
                donut: {
                    size: '55%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            showAlways: false,
                            label: "{{ __('dashboard total sold') }}",
                            formatter: function(w) {
                                return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                            }
                        }
                    }
                }
            }
        },
        dataLabels: {
            enabled: false
        },
        legend: {
            position: 'bottom',
            formatter: function(seriesName, opts) {
                return [seriesName, " - ", opts.w.globals.series[opts.seriesIndex]]
            }
        }
    };

    var salesDistributionChart = new ApexCharts(document.querySelector("#salesDistributionChart"), salesDistributionOptions);
    salesDistributionChart.render();

    // Add animation to the dashboard cards
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.dashboard-card').forEach(function(card, index) {
            setTimeout(function() {
                card.style.opacity = 1;
                card.style.transform = 'translateY(0)';
            }, 100 * index);
        });
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
            
            // Here you would update the chart data based on the selected period
            // For demo purposes, we'll just show an alert
            const period = this.getAttribute('data-period');
            
            // This would be replaced with actual AJAX call to get data for different periods
            if (period === 'weekly') {
                bestSellingChart.updateSeries([{
                    name: "{{ __('dashboard.total_sold') }}",
                    data: @json($best_selling_products->pluck('total_sold'))
                }]);
            } else if (period === 'monthly') {
                // These would be replaced with actual data
                bestSellingChart.updateSeries([{
                    name: "{{ __('dashboard.total_sold') }}",
                    data: @json($best_selling_products->pluck('total_sold')->map(function($value) { return $value * 1.2; }))
                }]);
            } else if (period === 'yearly') {
                bestSellingChart.updateSeries([{
                    name: "{{ __('dashboard.total_sold') }}",
                    data: @json($best_selling_products->pluck('total_sold')->map(function($value) { return $value * 5; }))
                }]);
            }
        });
    });
</script>
@endsection
               