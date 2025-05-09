@extends('layouts.admin')

@section('title', __('order.Orders_List'))
@section('content-header', __('order.Orders_List'))
@section('content-actions')
<a href="{{route('cart.index')}}" class="btn btn-orange">{{ __('cart.title') }}</a>
@endsection

@section('css')
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

/* Status Badges */
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

.card-received-amount {
    background-color: #9b59b6;
    color: white;
}

.card-pending-amount {
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

/* Partial Payment Modal Styling */
.payment-form-group {
    margin-bottom: 20px;
}

.payment-form-group label {
    font-weight: 600;
    margin-bottom: 8px;
    display: block;
}

.payment-form-group input {
    width: 100%;
    padding: 12px 15px;
    border-radius: 8px;
    border: 1px solid rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.payment-form-group input:focus {
    border-color: #3498db;
    box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.25);
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

/* Filter Buttons */
.filter-buttons {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}

.filter-button {
    border-radius: 30px;
    padding: 8px 20px;
    background-color: white;
    border: 1px solid rgba(0, 0, 0, 0.1);
    color: #666;
    font-weight: 500;
    transition: all 0.3s ease;
}

.filter-button:hover, .filter-button.active {
    background-color: #3498db;
    color: white;
    border-color: #3498db;
    box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
}
</style>
@endsection

@section('content')
<!-- Search and Filter Row -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="orderSearch" class="form-control" placeholder="{{ __('Search Orders...') }}">
        </div>
    </div>
    <div class="col-md-6">
        <div class="filter-buttons text-md-right">
            <button class="filter-button active" data-filter="all">{{ __('All') }}</button>
            <button class="filter-button" data-filter="not-paid">{{ __('Not Paid') }}</button>
            <button class="filter-button" data-filter="partial">{{ __('Partial') }}</button>
            <button class="filter-button" data-filter="paid">{{ __('Paid') }}</button>
        </div>
    </div>
</div>

<!-- Date Range Picker -->
<div class="date-range-container">
    <form action="{{route('orders.index')}}">
        <div class="row align-items-center">
            <div class="col-lg-3 col-md-4">
                <h5 class="mb-0"><i class="fas fa-calendar-alt mr-2"></i> {{ __('Date Range') }}</h5>
            </div>
            <div class="col-lg-3 col-md-3">
                <div class="form-group mb-0">
                    <label for="start_date">{{ __(' Start Date') }}</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" value="{{request('start_date')}}" />
                </div>
            </div>
            <div class="col-lg-3 col-md-3">
                <div class="form-group mb-0">
                    <label for="end_date">{{ __('End Date') }}</label>
                    <input type="date" id="end_date" name="end_date" class="form-control" value="{{request('end_date')}}" />
                </div>
            </div>
            <div class="col-lg-3 col-md-2 text-right">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-filter mr-1"></i> {{ __('Filter') }}
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
        <div class="dashboard-card card-total-orders">
            <div class="animated-bg"></div>
            <div class="card-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="card-value">{{ $orders->total() }}</div>
            <div class="card-title">{{ __('Total Orders Unit') }}</div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
        <div class="dashboard-card card-total-amount">
            <div class="animated-bg"></div>
            <div class="card-icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="card-value">{{ config('settings.currency_symbol') }} {{ number_format($total, 2) }}</div>
            <div class="card-title">{{ __('Total Amount Unit') }}</div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
        <div class="dashboard-card card-received-amount">
            <div class="animated-bg"></div>
            <div class="card-icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="card-value">{{ config('settings.currency_symbol') }} {{ number_format($receivedAmount, 2) }}</div>
            <div class="card-title">{{ __('Received Amount Unit') }}</div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
        <div class="dashboard-card card-pending-amount">
            <div class="animated-bg"></div>
            <div class="card-icon">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div class="card-value">{{ config('settings.currency_symbol') }} {{ number_format($total - $receivedAmount, 2) }}</div>
            <div class="card-title">{{ __('Pending Amount Unit') }}</div>
        </div>
    </div>
</div>

<!-- Orders Table -->
<div class="card shadow-sm border-0">
    <div class="card-header">
        <h3>{{ __('order.Orders_List') }}</h3>
        <div class="card-tools">
            <button class="btn btn-sm btn-outline-primary">
                <i class="fas fa-file-export"></i> {{ __('Export') }}
            </button>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>{{ __('order.ID') }}</th>
                    <th>{{ __('order.Customer_Name') }}</th>
                    <th>{{ __('order.Total') }}</th>
                    <th>{{ __('order.Received_Amount') }}</th>
                    <th>{{ __('order.Status') }}</th>
                    <th>{{ __('order.To_Pay') }}</th>
                    <th>{{ __('order.Created_At') }}</th>
                    <th class="text-center">{{ __('order.Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                <tr class="order-row" data-status="{{ $order->receivedAmount() == 0 ? 'not-paid' : ($order->receivedAmount() < $order->total() ? 'partial' : ($order->receivedAmount() == $order->total() ? 'paid' : 'change')) }}">
                    <td>{{$order->id}}</td>
                    <td>{{$order->getCustomerName()}}</td>
                    <td>{{ config('settings.currency_symbol') }} {{$order->formattedTotal()}}</td>
                    <td>{{ config('settings.currency_symbol') }} {{$order->formattedReceivedAmount()}}</td>
                    <td>
                        @if($order->receivedAmount() == 0)
                            <span class="badge-custom not-paid">{{ __('order.Not_Paid') }}</span>
                        @elseif($order->receivedAmount() < $order->total())
                            <span class="badge-custom partial">{{ __('order.Partial') }}</span>
                        @elseif($order->receivedAmount() == $order->total())
                            <span class="badge-custom paid">{{ __('order.Paid') }}</span>
                        @elseif($order->receivedAmount() > $order->total())
                            <span class="badge-custom change">{{ __('order.Change') }}</span>
                        @endif
                    </td>
                    <td>{{config('settings.currency_symbol')}} {{number_format($order->total() - $order->receivedAmount(), 2)}}</td>
                    <td>{{ \Carbon\Carbon::parse($order->created_at)->format('M d, Y H:i') }}</td>
                    <td class="text-center">
                        <div class="btn-group">
                            <button
                                class="btn btn-sm btn-info btnShowInvoice"
                                data-toggle="modal"
                                data-target="#modalInvoice"
                                data-order-id="{{ $order->id }}"
                                data-customer-name="{{ $order->getCustomerName() }}"
                                data-total="{{ $order->total() }}"
                                data-received="{{ $order->receivedAmount() }}"
                                data-items="{{ json_encode($order->items) }}"
                                data-created-at="{{ $order->created_at }}"
                                data-payment="{{ isset($order->payments) && count($order->payments) > 0 ? $order->payments[0]->amount : 0 }}">
                                <i class="fas fa-eye"></i>
                            </button>

                            @if($order->total() > $order->receivedAmount())
                                <!-- Button for Partial Payment -->
                                <button class="btn btn-sm btn-primary partialPaymentBtn" 
                                        data-toggle="modal" data-target="#partialPaymentModal" 
                                        data-orders-id="{{ $order->id }}" 
                                        data-remaining-amount="{{ $order->total() - $order->receivedAmount() }}">
                                    <i class="fas fa-money-bill"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2">{{ __('order.Total') }}</th>
                    <th>{{ config('settings.currency_symbol') }} {{ number_format($total, 2) }}</th>
                    <th>{{ config('settings.currency_symbol') }} {{ number_format($receivedAmount, 2) }}</th>
                    <th></th>
                    <th>{{ config('settings.currency_symbol') }} {{ number_format($total - $receivedAmount, 2) }}</th>
                    <th colspan="2"></th>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="card-footer">
        <div class="d-flex justify-content-center">
            {{ $orders->render() }}
        </div>
    </div>
</div>
@endsection

@section('model')
<!-- Invoice Modal -->
<div class="modal fade" id="modalInvoice" tabindex="-1" role="dialog" aria-labelledby="modalInvoiceLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalInvoiceLabel">
                    <i class="fas fa-file-invoice mr-2"></i> {{ __('order.Invoice_Details') }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <!-- Invoice content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> {{ __('order.Close') }}
                </button>
                <button type="button" class="btn btn-primary" id="printInvoice">
                    <i class="fas fa-print mr-1"></i> {{ __('order.Print') }}
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Partial Payment Modal -->
<div class="modal fade" id="partialPaymentModal" tabindex="-1" role="dialog" aria-labelledby="partialPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="partialPaymentModalLabel">
                    <i class="fas fa-money-bill-wave mr-2"></i> {{ __('order.Pay_Partial_Amount') }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="partialPaymentForm" method="POST" action="{{ route('orders.partial-payment') }}">
                    @csrf
                    <input type="hidden" name="order_id" id="modalOrderId" value="">
                    
                    <div class="payment-info-alert alert alert-info mb-4">
                        <div class="d-flex align-items-center">
                            <div class="mr-3">
                                <i class="fas fa-info-circle fa-2x"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">{{ __('order.Payment_Info') }}</h6>
                                <p class="mb-0">{{ __('order.Amount_Due') }}: <strong>{{ config('settings.currency_symbol') }} <span id="amountDue">0.00</span></strong></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="payment-form-group">
                        <label for="partialAmount">{{ __('order.Amount_To_Pay') }}</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">{{ config('settings.currency_symbol') }}</span>
                            </div>
                            <input type="number" class="form-control" step="0.01" id="partialAmount" name="amount" min="0.01">
                        </div>
                        <small class="form-text text-muted">{{ __('order.Enter_Amount_Below_Or_Equal') }} {{ config('settings.currency_symbol') }} <span id="maxAmount">0.00</span></small>
                    </div>
                    
                    <div class="text-right mt-4">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i> {{ __('order.Cancel') }}
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check mr-1"></i> {{ __('order.Confirm_Payment') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://unpkg.com/ionicons@4.5.10-0/dist/ionicons.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://kit.fontawesome.com/yourcode.js" crossorigin="anonymous"></script>
<script>
$(document).ready(function() {
    // Animate dashboard cards on page load
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
    
    // Filter buttons functionality
    $(".filter-button").click(function() {
        $(".filter-button").removeClass("active");
        $(this).addClass("active");
        
        var filter = $(this).data('filter');
        if(filter === 'all') {
            $(".order-row").show();
        } else {
            $(".order-row").hide();
            $(`.order-row[data-status="${filter}"]`).show();
        }
    });

    // Print invoice functionality
    $('#printInvoice').click(function() {
        var printContents = document.getElementById('modalInvoice').querySelector('.modal-body').innerHTML;
        var originalContents = document.body.innerHTML;
        
        document.body.innerHTML = `
            <html>
                <head>
                    <title>Print Invoice</title>
                    <style>
                        body { font-family: Arial, sans-serif; }
                        .invoice-card { border: 1px solid #ddd; padding: 20px; }
                        .invoice-header { display: flex; justify-content: space-between; padding-bottom: 20px; border-bottom: 1px solid #eee; }
                        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #eee; }
                        th { background-color: #f8f9fa; }
                        .invoice-total { margin-top: 20px; padding: 15px; background-color: #f8f9fa; }
                        .invoice-total-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dashed #eee; }
                        .invoice-total-row:last-child { border-bottom: none; }
                        @media print {
                            body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
                        }
                    </style>
                </head>
                <body>
                    <div class="invoice-card">
                        ${printContents}
                    </div>
                </body>
            </html>
        `;
        
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    });
    
    // Handle showing invoice details in modal
    $('.btnShowInvoice').click(function() {
        // Fetch data attributes from the button
        var button = $(this);
        var orderId = button.data('order-id');
        var customerName = button.data('customer-name');
        var totalAmount = button.data('total');
        var receivedAmount = button.data('received');
        var payment = button.data('payment');
        var createdAt = button.data('created-at');
        var items = button.data('items');
        
        var formattedDate = new Date(createdAt).toLocaleDateString('en-US', {
            year: 'numeric', 
            month: 'short', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        
        // Generate status badge based on payment status
        var statusBadge = '';
        if(receivedAmount == 0) {
            statusBadge = '<span class="badge-custom not-paid">{{ __("order.Not_Paid") }}</span>';
        } else if(receivedAmount < totalAmount) {
            statusBadge = '<span class="badge-custom partial">{{ __("order.Partial") }}</span>';
        } else if(receivedAmount == totalAmount) {
            statusBadge = '<span class="badge-custom paid">{{ __("order.Paid") }}</span>';
        } else if(receivedAmount > totalAmount) {
            statusBadge = '<span class="badge-custom change">{{ __("order.Change") }}</span>';
        }
        
        // Build items HTML
        var itemsHTML = '';
        var subtotal = 0;
        
        if (items && items.length) {
            items.forEach(function(item, index) {
                var itemTotal = parseFloat(item.product.price) * item.quantity;
                subtotal += itemTotal;
                
                itemsHTML += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.product.name}</td>
                        <td>${item.description || '-'}</td>
                        <td class="text-right">{{ config('settings.currency_symbol') }} ${parseFloat(item.product.price).toFixed(2)}</td>
                        <td class="text-center">${item.quantity}</td>
                        <td class="text-right">{{ config('settings.currency_symbol') }} ${itemTotal.toFixed(2)}</td>
                    </tr>
                `;
            });
        }
        
        // Calculate remaining amount
        var remainingAmount = totalAmount - receivedAmount;
        
        // Build the invoice HTML
        var invoiceHTML = `
            <div class="invoice-card">
                <div class="invoice-header">
                    <div>
                        <h4 class="mb-0">{{ __('order.Invoice') }} #${orderId}</h4>
                        <small class="text-muted">{{ __('order.Created_At') }}: ${formattedDate}</small>
                    </div>
                    <div>
                        <h5 class="mb-0">{{ config('settings.app_name') }}</h5>
                        <div class="mt-2">${statusBadge}</div>
                    </div>
                </div>
                
                <div class="invoice-body">
                    <div class="invoice-customer">
                        <h6 class="mb-2">{{ __('order.Customer_Details') }}:</h6>
                        <strong>${customerName || '{{ __("order.Walk_in_Customer") }}'}</strong>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table invoice-table">
                            <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="25%">{{ __('order.Item') }}</th>
                                    <th width="25%">{{ __('order.Description') }}</th>
                                    <th width="15%" class="text-right">{{ __('order.Unit_Price') }}</th>
                                    <th width="10%" class="text-center">{{ __('order.Qty') }}</th>
                                    <th width="20%" class="text-right">{{ __('order.Total') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${itemsHTML}
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="invoice-total">
                        <div class="invoice-total-row">
                            <div class="total-label">{{ __('order.Subtotal') }}:</div>
                            <div class="total-value">{{ config('settings.currency_symbol') }} ${subtotal.toFixed(2)}</div>
                        </div>
                        <div class="invoice-total-row">
                            <div class="total-label">{{ __('order.Total') }}:</div>
                            <div class="total-value">{{ config('settings.currency_symbol') }} ${totalAmount.toFixed(2)}</div>
                        </div>
                        <div class="invoice-total-row">
                            <div class="total-label">{{ __('order.Paid_Amount') }}:</div>
                            <div class="total-value">{{ config('settings.currency_symbol') }} ${receivedAmount.toFixed(2)}</div>
                        </div>
                        <div class="invoice-total-row">
                            <div class="total-label">{{ __('order.Balance') }}:</div>
                            <div class="total-value ${remainingAmount > 0 ? 'text-danger' : 'text-success'}">
                                {{ config('settings.currency_symbol') }} ${Math.abs(remainingAmount).toFixed(2)} 
                                ${remainingAmount > 0 ? '({{ __("order.Due") }})' : remainingAmount < 0 ? '({{ __("order.Change") }})' : ''}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Insert the invoice HTML into the modal
        $('#modalInvoice .modal-body').html(invoiceHTML);
    });
    
    // Handle partial payment modal
    $('.partialPaymentBtn').click(function() {
        var orderId = $(this).data('orders-id');
        var remainingAmount = $(this).data('remaining-amount');
        
        // Set the order ID in the hidden field
        $('#modalOrderId').val(orderId);
        
        // Set the maximum amount and show due amount
        $('#maxAmount').text(remainingAmount.toFixed(2));
        $('#amountDue').text(remainingAmount.toFixed(2));
        
        // Set the default value for the payment input
        $('#partialAmount').val(remainingAmount.toFixed(2));
        $('#partialAmount').attr('max', remainingAmount);
    });
    
    // Form validation for partial payment
    $('#partialPaymentForm').submit(function(e) {
        var amount = parseFloat($('#partialAmount').val());
        var maxAmount = parseFloat($('#maxAmount').text());
        
        if (amount <= 0) {
            e.preventDefault();
            alert("{{ __('order.Amount_Must_Be_Greater_Than_Zero') }}");
            return false;
        }
        
        if (amount > maxAmount) {
            e.preventDefault();
            alert("{{ __('order.Amount_Cannot_Exceed_Due_Amount') }}");
            return false;
        }
    });
    
    // Animation for background gradients
    function animateBackgrounds() {
        $('.dashboard-card').each(function() {
            var $card = $(this);
            var $bg = $card.find('.animated-bg');
            
            $bg.css({
                'background': `linear-gradient(135deg, 
                                rgba(255,255,255,0.1) 0%, 
                                rgba(255,255,255,0) 50%, 
                                rgba(255,255,255,0.1) 100%)`,
                'background-size': '200% 200%',
                'animation': 'gradientAnimation 3s ease infinite'
            });
        });
    }
    
    // Add CSS animation for backgrounds
    $('<style>')
        .prop('type', 'text/css')
        .html(`
            @keyframes gradientAnimation {
                0% { background-position: 0% 50% }
                50% { background-position: 100% 50% }
                100% { background-position: 0% 50% }
            }
        `)
        .appendTo('head');
    
    // Initialize animations
    animateBackgrounds();
});
</script>
@endsection