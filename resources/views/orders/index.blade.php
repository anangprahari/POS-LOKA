@extends('layouts.admin')

@section('title', __('order.Orders_List'))
@section('content-header', __('order.Orders_List'))
@section('content-actions')
<a href="{{route('cart.index')}}" class="btn btn-orange">{{ __('cart.title') }}</a>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}">
<style>
/* Modern UI Elements & Card Styling */
.card {
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    overflow: hidden;
    border: none;
    margin-bottom: 30px;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
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

/* Table Styling */
.table {
    margin-bottom: 0;
}

.table th {
    background-color: #f8f9fa;
    border-top: none;
    font-weight: 600;
    color: #495057;
    padding: 15px;
    font-size: 0.9rem;
}

.table td {
    padding: 15px;
    vertical-align: middle;
}

.table-hover tbody tr {
    transition: all 0.2s ease;
}

.table-hover tbody tr:hover {
    background-color: rgba(52, 152, 219, 0.05);
    transform: translateY(-1px);
}

/* Badge Styling */
.badge-custom {
    display: inline-block;
    padding: 6px 16px;
    font-weight: 500;
    border-radius: 30px;
    font-size: 0.8rem;
    text-align: center;
    white-space: nowrap;
    transition: all 0.3s ease;
}

.badge-custom.not-paid {
    background-color: #e74c3c;
    color: white;
}

.badge-custom.partial {
    background-color: #f39c12;
    color: white;
}

.badge-custom.paid {
    background-color: #2ecc71;
    color: white;
}

.badge-custom.change {
    background-color: #3498db;
    color: white;
}

/* Order ID Badge */
.order-id-badge {
    background-color: #e8f4fc;
    color: #3498db;
    padding: 5px 10px;
    border-radius: 20px;
    font-weight: 500;
    font-size: 0.85rem;
    display: inline-block;
}

/* Customer Name Badge */
.customer-name-badge {
    background-color: #f0f8e8;
    color: #27ae60;
    padding: 5px 10px;
    border-radius: 20px;
    font-weight: 500;
    font-size: 0.85rem;
    display: inline-block;
}

/* Amount Badge */
.amount-badge {
    background-color: #f8f4ff;
    color: #9b59b6;
    padding: 5px 10px;
    border-radius: 20px;
    font-weight: 500;
    font-size: 0.85rem;
    display: inline-block;
}

/* Discount Badge */
.discount-badge {
    background-color: #fff3cd;
    color: #f39c12;
    padding: 3px 8px;
    border-radius: 15px;
    font-size: 0.8rem;
    display: inline-flex;
    align-items: center;
}

/* Buttons */
.btn {
    border-radius: 8px;
    padding: 8px 16px;
    transition: all 0.3s ease;
    font-weight: 500;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
}

.btn i {
    margin-right: 5px;
}

.btn-orange {
    background: linear-gradient(135deg, #ff6600 0%, #ff4500 100%);
    border: none;
    color: white;
    box-shadow: 0 4px 15px rgba(255, 102, 0, 0.3);
}

.btn-orange:hover {
    background: linear-gradient(135deg, #ff4500 0%, #ff3300 100%);
    box-shadow: 0 6px 20px rgba(255, 102, 0, 0.4);
}

.btn-primary {
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
    border: none;
    box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
}

.btn-primary:hover {
    background: linear-gradient(135deg, #2980b9 0%, #1c6ca1 100%);
    box-shadow: 0 6px 20px rgba(52, 152, 219, 0.4);
}

.btn-secondary {
    background: linear-gradient(135deg, #7f8c8d 0%, #576574 100%);
    border: none;
    box-shadow: 0 4px 15px rgba(127, 140, 141, 0.3);
    color: white;
}

.btn-secondary:hover {
    background: linear-gradient(135deg, #576574 0%, #34495e 100%);
    box-shadow: 0 6px 20px rgba(127, 140, 141, 0.4);
    color: white;
}

.btn-success {
    background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
    border: none;
    box-shadow: 0 4px 15px rgba(46, 204, 113, 0.3);
}

.btn-success:hover {
    background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
    box-shadow: 0 6px 20px rgba(46, 204, 113, 0.4);
}

.btn-info {
    background: linear-gradient(135deg, #00bcd4 0%, #0097a7 100%);
    border: none;
    color: white;
    box-shadow: 0 4px 15px rgba(0, 188, 212, 0.3);
}

.btn-info:hover {
    background: linear-gradient(135deg, #0097a7 0%, #00838f 100%);
    box-shadow: 0 6px 20px rgba(0, 188, 212, 0.4);
}

/* Date Range Picker Styling */
.date-range-container {
    background-color: white;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    padding: 20px;
    margin-bottom: 20px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.date-range-container:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.date-range-container input[type="date"] {
    border-radius: 8px;
    border: 1px solid rgba(0, 0, 0, 0.1);
    padding: 10px 15px;
    transition: all 0.3s ease;
}

.date-range-container input[type="date"]:focus {
    border-color: #3498db;
    box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.25);
}

/* Dashboard Cards */
.dashboard-card {
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    padding: 20px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: 100%;
    position: relative;
    overflow: hidden;
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.5s ease, transform 0.5s ease;
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
    color: white;
}

.dashboard-card .card-title {
    font-size: 16px;
    color: rgba(255, 255, 255, 0.8);
    margin-bottom: 15px;
}

.card-total-orders {
    background-color: #3498db;
    color: white;
}

.card-total-amount {
    background-color: #2ecc71;
    color: white;
}

.card-total-discount {
    background-color: #f39c12;
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

/* Search Box */
.search-box {
    position: relative;
    margin-bottom: 20px;
}

.search-box input {
    border-radius: 30px;
    padding: 12px 20px;
    padding-left: 45px;
    border: 1px solid rgba(0, 0, 0, 0.1);
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    width: 100%;
}

.search-box input:focus {
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    border-color: #3498db;
}

.search-box i {
    position: absolute;
    left: 20px;
    top: 50%;
    transform: translateY(-50%);
    color: #999;
}

/* Modal Styling */
.modal-content {
    border-radius: 15px;
    border: none;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.modal-header {
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
    color: white;
    border-top-left-radius: 15px;
    border-top-right-radius: 15px;
    border: none;
}

.modal-title {
    font-weight: 600;
}

.modal-footer {
    border-top: 1px solid rgba(0, 0, 0, 0.05);
}

/* Invoice Styling */
.invoice-card {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
}

.invoice-header {
    padding: 20px;
    background-color: #f8f9fa;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.invoice-body {
    padding: 20px;
}

.invoice-customer {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.invoice-table th {
    background-color: #f8f9fa;
}

.invoice-total {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    margin-top: 20px;
}

.invoice-total-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px dashed rgba(0, 0, 0, 0.1);
}

.invoice-total-row:last-child {
    border-bottom: none;
}

.invoice-total-row .total-label {
    font-weight: 600;
}

.invoice-total-row .total-value {
    font-weight: 700;
}

/* Pagination */
.pagination {
    margin-top: 20px;
    justify-content: center;
}

.page-link {
    border-radius: 50%;
    margin: 0 5px;
    color: #3498db;
    border: none;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.page-link:hover {
    background-color: #3498db;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
}

.page-item.active .page-link {
    background-color: #3498db;
    border-color: #3498db;
    box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
}

/* Alert styling */
.alert {
    border-radius: 12px;
    border: none;
    padding: 15px 20px;
    margin-bottom: 20px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
}

.alert-success {
    background-color: #d4edda;
    border-left: 4px solid #28a745;
    color: #155724;
}

.alert-danger {
    background-color: #f8d7da;
    border-left: 4px solid #dc3545;
    color: #721c24;
}

.close {
    font-size: 1.2rem;
    opacity: 0.8;
    transition: all 0.2s;
}

.close:hover {
    opacity: 1;
}

/* Order Actions */
.order-actions {
    display: flex;
    gap: 8px;
    justify-content: center;
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
</style>
@endsection

@section('content')
<div class="row mb-3">
    <div class="col-md-7">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong><i class="fas fa-check-circle mr-2"></i></strong> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong><i class="fas fa-exclamation-circle mr-2"></i></strong> {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
    </div>
    <div class="col-md-5">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="orderSearch" class="form-control" placeholder="{{ __('Search Orders...') }}">
        </div>
    </div>
</div>

<div class="date-range-container">
    <form action="{{ route('orders.index') }}" method="GET">
        @csrf
        <div class="row align-items-center">
            <div class="col-lg-3 col-md-4">
                <h5 class="mb-0"><i class="fas fa-calendar-alt mr-2"></i> {{ __('Date Range') }}</h5>
            </div>
            <div class="col-lg-3 col-md-3">
                <div class="form-group mb-0">
                    <label for="start_date">{{ __('Start Date') }}</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" value="{{ request('start_date') }}" />
                </div>
            </div>
            <div class="col-lg-3 col-md-3">
                <div class="form-group mb-0">
                    <label for="end_date">{{ __('End Date') }}</label>
                    <input type="date" id="end_date" name="end_date" class="form-control" value="{{ request('end_date') }}" />
                </div>
            </div>
            <div class="col-lg-3 col-md-2 text-right">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-filter mr-1"></i> {{ __('Filter') }}
                </button>
                @if(request()->has('start_date') || request()->has('end_date'))
                <a href="{{ route('orders.index') }}" class="btn btn-secondary ml-2">
                    <i class="fas fa-times mr-1"></i> {{ __('Clear') }}
                </a>
                @endif
            </div>
        </div>
    </form>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
        <div class="dashboard-card card-total-orders">
            <div class="animated-bg"></div>
            <div class="card-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="card-value">{{ $orders->total() }}</div>
            <div class="card-title">{{ __('Total Orders') }}</div>
        </div>
    </div>
    
    <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
        <div class="dashboard-card card-total-amount">
            <div class="animated-bg"></div>
            <div class="card-icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="card-value">{{ config('settings.currency_symbol') }} {{ number_format($total, 2) }}</div>
            <div class="card-title">{{ __('Total Amount') }}</div>
        </div>
    </div>
    
    <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
        <div class="dashboard-card card-total-discount">
            <div class="animated-bg"></div>
            <div class="card-icon">
                <i class="fas fa-tag"></i>
            </div>
            <div class="card-value">{{ config('settings.currency_symbol') }} {{ number_format($discountSum, 2) }}</div>
            <div class="card-title">{{ __('Total Discount') }}</div>
        </div>
    </div>
</div>

<!-- Orders List Table -->
<div class="card shadow-sm border-0">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h3 class="m-0">{{ __('Orders List') }}</h3>
        <div class="d-flex flex-grow-1 justify-content-between align-items-center ml-3">
            <div class="flex-grow-1 text-center">
            </div>
            <div class="text-right">
                <a href="{{ route('orders.export') }}"  class="btn btn-success">
                    <i class="fas fa-file-excel"></i> {{ __('Export Orders to Excel') }}
                </a>
                <a href="{{ route('orders.export-details') }}"  class="btn btn-success ml-2">
                    <i class="fas fa-file-excel"></i> {{ __('Export Orders Items to Excel') }}
                </a>
            </div>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap align-middle">
            <thead>
                <tr>
                    <th>{{ __('order.ID') }}</th>
                    <th>{{ __('order.Customer_Name') }}</th>
                    <th>{{ __('Subtotal') }}</th>
                    <th>{{ __('Discount') }}</th>
                    <th>{{ __('order.Total') }}</th>
                    <th>{{ __('order.Created_At') }}</th>
                    <th class="text-center">{{ __('order.Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                <tr>
                    <td>
                        <div class="order-id-badge">#{{$order->id}}</div>
                    </td>
                    <td>
                        <div class="customer-name-badge">{{$order->getCustomerName()}}</div>
                    </td>
                    <td>
                        <div class="amount-badge">{{ config('settings.currency_symbol') }} {{number_format($order->subtotal(), 2)}}</div>
                    </td>
                    <td>
                        @if($order->discount > 0)
                            @if($order->discount_type == 'percentage')
                                <div class="discount-badge">
                                    {{ $order->discount }}% ({{ config('settings.currency_symbol') }} {{number_format($order->discountAmount(), 2)}})
                                </div>
                            @else
                                <div class="discount-badge">
                                    {{ config('settings.currency_symbol') }} {{number_format($order->discount, 2)}}
                                </div>
                            @endif
                        @else
                            <div class="discount-badge">{{ config('settings.currency_symbol') }} 0.00</div>
                        @endif
                    </td>
                    <td>
                        <div class="amount-badge">{{ config('settings.currency_symbol') }} {{$order->formattedTotal()}}</div>
                    </td>
                    <td>
                        <span title="{{$order->created_at}}">
                            {{ \Carbon\Carbon::parse($order->created_at)->format('M d, Y H:i') }}
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="order-actions">
                            <button
                                class="btn btn-sm btn-info btnShowInvoice"
                                data-toggle="modal"
                                data-target="#modalInvoice"
                                data-order-id="{{ $order->id }}"
                                data-customer-name="{{ $order->getCustomerName() }}"
                                data-subtotal="{{ $order->subtotal() }}"
                                data-discount="{{ $order->discount }}"
                                data-discount-type="{{ $order->discount_type }}"
                                data-discount-amount="{{ $order->discountAmount() }}"
                                data-total="{{ $order->total() }}"
                                data-items="{{ json_encode($order->items->load('product')) }}"
                                data-created-at="{{ $order->created_at }}">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2" class="text-right">{{ __('Subtotal') }}:</th>
                    <th>{{ config('settings.currency_symbol') }} {{ number_format($subtotalSum, 2) }}</th>
                    <th>{{ config('settings.currency_symbol') }} {{ number_format($discountSum, 2) }}</th>
                    <th>{{ config('settings.currency_symbol') }} {{ number_format($total, 2) }}</th>
                    <th></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="card-footer">
        <div class="d-flex justify-content-center">
            {{ $orders->appends(request()->except('page'))->render() }}
        </div>
    </div>
</div>
@endsection

@section('model')
<!-- Modal untuk melihat Invoice -->
<div class="modal fade" id="modalInvoice" tabindex="-1" role="dialog" aria-labelledby="modalInvoiceLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalInvoiceLabel">
                    <i class="fas fa-file-invoice mr-2"></i> {{ config('app.name') }} - Invoice
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Placeholder for dynamic content -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Close
                </button>
                <button type="button" class="btn btn-primary" id="btnPrintInvoice">
                    <i class="fas fa-print mr-1"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://unpkg.com/ionicons@4.5.10-0/dist/ionicons.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<script>
    $(document).ready(function() {
        // Animation for dashboard cards
        setTimeout(function() {
            $('.dashboard-card').each(function(index) {
                setTimeout(() => {
                    $(this).css({
                        'opacity': 1,
                        'transform': 'translateY(0)'
                    });
                }, index * 100);
            });
        }, 300);

        // Search functionality
        $("#orderSearch").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("table tbody tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        // Event handler untuk modal Invoice
        $(document).on('click', '.btnShowInvoice', function(event) {
            console.log("Modal show event triggered!");

            // Ambil data dari tombol yang diklik
            var button = $(this);
            var orderId = button.data('order-id');
            var customerName = button.data('customer-name');
            var subtotal = parseFloat(button.data('subtotal'));
            var discount = parseFloat(button.data('discount') || 0);
            var discountType = button.data('discount-type') || 'fixed';
            var discountAmount = parseFloat(button.data('discount-amount') || 0);
            var totalAmount = parseFloat(button.data('total'));
            var createdAt = button.data('created-at');
            var items = button.data('items');

            // Menyiapkan HTML untuk item
            var itemsHTML = '';
            if (items && items.length) {
                items.forEach(function(item, index) {
                    var unitPrice = item.price / item.quantity;
                    itemsHTML += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${item.product.name}</td>
                            <td>${item.product.description || 'N/A'}</td>
                            <td class="text-right">${parseFloat(unitPrice).toFixed(2)}</td>
                            <td class="text-center">${item.quantity}</td>
                            <td class="text-right">${parseFloat(item.price).toFixed(2)}</td>
                        </tr>
                    `;
                });
            }

            // Format discount display
            var discountDisplay = '';
            if (discount > 0) {
                if (discountType === 'percentage') {
                    discountDisplay = `${discount}% ({{ config('settings.currency_symbol') }} ${discountAmount.toFixed(2)})`;
                } else {
                    discountDisplay = `{{ config('settings.currency_symbol') }} ${discount.toFixed(2)}`;
                }
            } else {
                discountDisplay = '{{ config('settings.currency_symbol') }} 0.00';
            }

            // Update konten modal
            var modalBody = $('#modalInvoice').find('.modal-body');
            modalBody.html(`
                <div id="invoice-content" class="invoice-card">
                    <div class="invoice-header">
                        <div>
                            <h4 class="mb-0">Invoice <strong>#${orderId}</strong></h4>
                            <small class="text-muted">Created: ${new Date(createdAt).toLocaleString()}</small>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ config('app.name') }}</h5>
                        </div>
                    </div>
                    <div class="invoice-body">
                        <div class="invoice-customer">
                            <h6 class="mb-2">Customer Information:</h6>
                            <strong>${customerName || 'General Customer'}</strong>
                        </div>
                        <div class="table-responsive-sm">
                            <table class="table table-striped invoice-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Item</th>
                                        <th>Description</th>
                                        <th class="text-right">Unit Cost</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-right">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${itemsHTML}
                                </tbody>
                            </table>
                        </div>
                        <div class="invoice-total">
                            <div class="invoice-total-row">
                                <div class="total-label">Subtotal:</div>
                                <div class="total-value">{{ config('settings.currency_symbol') }} ${subtotal.toFixed(2)}</div>
                            </div>
                            <div class="invoice-total-row">
                                <div class="total-label">Discount:</div>
                                <div class="total-value">${discountDisplay}</div>
                            </div>
                            <div class="invoice-total-row">
                                <div class="total-label">Total:</div>
                                <div class="total-value">{{ config('settings.currency_symbol') }} ${totalAmount.toFixed(2)}</div>
                            </div>
                        </div>
                    </div>
                </div>
            `);

            // Simpan order ID untuk digunakan saat print
            $('#btnPrintInvoice').data('order-id', orderId);
        });

        // Tombol Print Invoice
        $('#btnPrintInvoice').on('click', function() {
            // Create a new window for printing
            var printWindow = window.open('', '_blank', 'width=800,height=600');
            
            // Add custom print styles
            printWindow.document.write(`
                <html>
                <head>
                    <title>Invoice #${$(this).data('order-id')}</title>
                    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
                    <style>
                        @media print {
                            body { 
                                margin: 0;
                                padding: 10mm;
                                font-family: Arial, sans-serif;
                            }
                            .invoice-card {
                                border: 1px solid #ddd;
                                border-radius: 8px;
                                overflow: hidden;
                                margin-bottom: 20px;
                            }
                            .invoice-header {
                                background-color: #f8f9fa;
                                padding: 20px;
                                border-bottom: 1px solid #ddd;
                                display: flex;
                                justify-content: space-between;
                                align-items: center;
                            }
                            .invoice-body {
                                padding: 20px;
                            }
                            .invoice-customer {
                                background-color: #f8f9fa;
                                padding: 15px;
                                border-radius: 8px;
                                margin-bottom: 20px;
                            }
                            .table {
                                width: 100%;
                                margin-bottom: 1rem;
                                color: #212529;
                                border-collapse: collapse;
                            }
                            .table th,
                            .table td {
                                padding: 0.75rem;
                                vertical-align: top;
                                border-top: 1px solid #dee2e6;
                            }
                            .table thead th {
                                vertical-align: bottom;
                                border-bottom: 2px solid #dee2e6;
                                background-color: #f8f9fa;
                            }
                            .text-right {
                                text-align: right !important;
                            }
                            .text-center {
                                text-align: center !important;
                            }
                            .invoice-total {
                                background-color: #f8f9fa;
                                padding: 15px;
                                border-radius: 8px;
                                margin-top: 20px;
                            }
                            .invoice-total-row {
                                display: flex;
                                justify-content: space-between;
                                padding: 8px 0;
                                border-bottom: 1px dashed #dee2e6;
                            }
                            .invoice-total-row:last-child {
                                border-bottom: none;
                            }
                            .invoice-total-row .total-label {
                                font-weight: 600;
                            }
                            .invoice-total-row .total-value {
                                font-weight: 700;
                            }
                            .invoice-footer {
                                text-align: center;
                                margin-top: 30px;
                                padding-top: 20px;
                                border-top: 1px solid #dee2e6;
                                font-size: 0.9em;
                            }
                        }
                    </style>
                </head>
                <body>
                    ${$('#invoice-content').prop('outerHTML')}
                    <div class="invoice-footer">
                        <p>Thank you for your business!</p>
                        <p class="small">{{ config('app.name') }} &copy; ${new Date().getFullYear()}</p>
                    </div>
                    <script>
                        window.onload = function() {
                            window.print();
                            window.onafterprint = function() {
                                window.close();
                            }
                        }
                    <\/script>
                </body>
                </html>
            `);
            
            printWindow.document.close();
        });

        // Add animation to table rows
        $('table tbody tr').each(function(index) {
            $(this).css({
                'opacity': 0,
                'transform': 'translateY(20px)'
            });
            
            setTimeout(() => {
                $(this).animate({
                    'opacity': 1,
                    'transform': 'translateY(0)'
                }, 300);
            }, 100 + (index * 50));
        });

        // Add pulse effect to cards on hover
        $('.dashboard-card').hover(
            function() {
                $(this).find('.animated-bg').css({
                    'background': 'radial-gradient(circle at center, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0) 70%)',
                    'transform': 'scale(1)',
                    'opacity': 1,
                    'transition': 'all 0.5s ease'
                });
            },
            function() {
                $(this).find('.animated-bg').css({
                    'transform': 'scale(1.5)',
                    'opacity': 0,
                    'transition': 'all 0.5s ease'
                });
            }
        );

        // Responsive adjustments
        function adjustUIForScreenSize() {
            if ($(window).width() < 768) {
                $('.date-range-container .row').removeClass('align-items-center');
                $('.date-range-container .form-group').addClass('mb-3');
                $('.date-range-container button').addClass('btn-block');
            } else {
                $('.date-range-container .row').addClass('align-items-center');
                $('.date-range-container .form-group').removeClass('mb-3');
                $('.date-range-container button').removeClass('btn-block');
            }
        }
        
        // Run on load and window resize
        adjustUIForScreenSize();
        $(window).resize(adjustUIForScreenSize);

        // Add tooltips to action buttons
        $('[data-toggle="tooltip"]').tooltip();

        // Add date range validation
        $('#start_date, #end_date').on('change', function() {
            var startDate = new Date($('#start_date').val());
            var endDate = new Date($('#end_date').val());
            
            if (startDate > endDate && $('#end_date').val() !== '') {
                alert('End date cannot be before start date');
                $('#end_date').val('');
            }
        });

        // Add currency formatter utility
        function formatCurrency(amount) {
            return '{{ config('settings.currency_symbol') }} ' + parseFloat(amount).toFixed(2);
        }
    });
</script>
@endsection