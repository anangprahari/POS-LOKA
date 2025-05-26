@extends('layouts.admin')

@section('title', 'User Dashboard')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.41.0/apexcharts.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
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
        border: none;
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
        opacity: 0.3;
    }
    
    .dashboard-card .card-value {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 5px;
    }
    
    .dashboard-card .card-title {
        font-size: 16px;
        margin-bottom: 15px;
        opacity: 0.8;
    }
    
    .dashboard-card .card-footer {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 10px 20px;
        background-color: rgba(0, 0, 0, 0.05);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background-color 0.3s ease;
    }
    
    .dashboard-card .card-footer:hover {
        background-color: rgba(0, 0, 0, 0.1);
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

    /* Improved color scheme for cards */
    .card-customers {
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        color: white;
    }
    
    .card-customers-today {
        background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
        color: white;
    }

    .card-orders {
        background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
        color: white;
    }
    
    .card-orders-today {
        background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
        color: white;
    }
    
    .card-orders-today .card-title {
        color: white;
    }
    
    .card-orders-today .card-icon {
        color: white;
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
        color: rgba(255, 255, 255, 0.8);
    }
    
    .decrement {
        color: rgba(255, 255, 255, 0.8);
    }
    
    /* Welcome Card */
    .welcome-card {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        color: white;
        border-radius: 12px;
        overflow: hidden;
    }
    
    .welcome-card h2 {
        font-weight: 700;
        margin-bottom: 10px;
    }
    
    .welcome-card .welcome-icon {
        font-size: 72px;
        opacity: 0.2;
        position: absolute;
        right: 20px;
        top: 20px;
    }

    /* Improved Quick Action Buttons */
    .quick-action-btn {
        border-radius: 10px;
        padding: 20px 15px;
        text-align: center;
        transition: all 0.3s ease;
        border: none;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .quick-action-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .quick-action-btn i {
        font-size: 24px;
        margin-bottom: 10px;
        display: block;
    }
    
    .quick-action-btn .small {
        font-size: 12px;
        opacity: 0.9;
        margin-top: 5px;
    }
    
    .btn-pos {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        color: white;
    }
    
    .btn-customers {
        background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
        color: white;
    }
    
    .btn-products {
        background: linear-gradient(135deg, #36b9cc 0%, #258391 100%);
        color: white;
    }
    
    .btn-reports {
        background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);
        color: white;
    }

    .btn-block {
        width: 100%;
    }

    .mr-2, .me-2 {
        margin-right: 0.5rem;
    }

    .ml-1 {
        margin-left: 0.25rem;
    }

    .mt-1 {
        margin-top: 0.25rem;
    }
    
    /* Responsive adjustments */
    @media (max-width: 992px) {
        .dashboard-card .card-value {
            font-size: 24px;
        }
        
        .dashboard-card .card-icon {    
            font-size: 28px;
        }
    }
</style>

<div class="container-fluid py-4">
    <!-- Welcome message -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card welcome-card dashboard-card">
                <div class="animated-bg"></div>
                <i class="fas fa-user-circle welcome-icon"></i>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2>{{ __('Welcome Back') }}, {{ auth()->user()->getFullname() }}!</h2>
                            <p class="mb-0">You're logged in as {{ ucfirst(auth()->user()->role) }}</p>
                        </div>
                        <div class="col-md-4 text-center d-none d-md-block">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
            <div class="dashboard-card card-customers">
                <div class="animated-bg"></div>
                <div class="card-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="card-value">{{ $total_customers ?? 0 }}</div>
                <div class="card-title">Total Customers</div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
            <div class="dashboard-card card-customers-today">
                <div class="animated-bg"></div>
                <div class="card-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="card-value">{{ $customers_today ?? 0 }}</div>
                <div class="card-title">New Customers Today</div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
            <div class="dashboard-card card-orders">
                <div class="animated-bg"></div>
                <div class="card-icon">
                    <i class="fas fa-receipt"></i>
                </div>
                <div class="card-value">{{ $total_orders ?? 0 }}</div>
                <div class="card-title">Total Orders</div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
            <div class="dashboard-card card-orders-today">
                <div class="animated-bg"></div>
                <div class="card-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="card-value">{{ $orders_today ?? 0 }}</div>
                <div class="card-title">Orders Today</div>
            </div>
        </div>
    </div>

    <!-- Quick Actions and Charts -->
    <div class="row mb-4">
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="chart-container">
                <h3 class="section-title">
                    <i class="fas fa-bolt text-warning me-2"></i>
                    Quick Actions
                </h3>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <a href="{{ route('cart.index') }}" class="btn btn-pos quick-action-btn btn-block py-3">
                            <i class="fas fa-shopping-cart"></i>
                            Point of Sale
                            <div class="small">Start a new sale</div>
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="{{ route('customers.index') }}" class="btn btn-customers quick-action-btn btn-block py-3">
                            <i class="fas fa-users"></i>
                            Manage Customers
                            <div class="small">View all customers</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="chart-container">
                <h3 class="section-title">
                    <i class="fas fa-chart-line text-primary me-2"></i>
                    Customer Activity (Last 7 Days)
                </h3>
                <div id="customerActivityChart" style="height: 250px;"></div>
            </div>
        </div>
    </div>

    <!-- Order Activity Chart - Full Width -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="chart-container">
                <h3 class="section-title">
                    <i class="fas fa-chart-area text-warning me-2"></i>
                    Order Activity (Last 7 Days)
                </h3>
                <div id="orderActivityChart" style="height: 300px;"></div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animate dashboard cards on load
        document.querySelectorAll('.dashboard-card').forEach(function(card, index) {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            setTimeout(function() {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
                card.style.transition = 'all 0.5s ease';
            }, 100 * index);
        });
        
        // Customer Activity Chart with dynamic data
        var customerActivityData = {!! json_encode($customer_activity_data ?? [0,0,0,0,0,0,0]) !!};
        var customerActivityLabels = {!! json_encode($customer_activity_labels ?? ['Mon','Tue','Wed','Thu','Fri','Sat','Sun']) !!};
        
        var customerActivityOptions = {
            series: [{
                name: 'New Customers',
                data: customerActivityData
            }],
            chart: {
                height: '100%',
                type: 'area',
                toolbar: {
                    show: false
                },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800
                }
            },
            colors: ['#2ecc71'],
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.3,
                    stops: [0, 90, 100]
                }
            },
            xaxis: {
                categories: customerActivityLabels,
                labels: {
                    style: {
                        colors: '#6c757d'
                    }
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#6c757d'
                    }
                }
            },
            tooltip: {
                theme: 'light',
                y: {
                    formatter: function (val) {
                        return val + " customers"
                    }
                }
            }
        };

        var customerActivityChart = new ApexCharts(document.querySelector("#customerActivityChart"), customerActivityOptions);
        customerActivityChart.render();

        // Order Activity Chart
        var orderActivityData = {!! json_encode($order_activity_data ?? [0,0,0,0,0,0,0]) !!};
        var orderActivityLabels = {!! json_encode($order_activity_labels ?? ['Mon','Tue','Wed','Thu','Fri','Sat','Sun']) !!};
        
        var orderActivityOptions = {
            series: [{
                name: 'Orders',
                data: orderActivityData
            }],
            chart: {
                height: '100%',
                type: 'line',
                toolbar: {
                    show: false
                },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800
                }
            },
            colors: ['#f39c12'],
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            xaxis: {
                categories: orderActivityLabels,
                labels: {
                    style: {
                        colors: '#6c757d'
                    }
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#6c757d'
                    }
                }
            },
            tooltip: {
                theme: 'light',
                y: {
                    formatter: function (val) {
                        return val + " orders"
                    }
                }
            }
        };

        var orderActivityChart = new ApexCharts(document.querySelector("#orderActivityChart"), orderActivityOptions);
        orderActivityChart.render();
    });
</script>
@endsection