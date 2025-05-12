@extends('layouts.admin')

@section('title', __('order.Orders_List'))
@section('content-header', __('order.Orders_List'))
@section('content-actions')
<a href="{{route('cart.index')}}" class="btn btn-primary">{{ __('cart.title') }}</a>
@endsection
@section('content')
<div class="card">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-7">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
            </div>
            <div class="col-md-5">
                <form action="{{route('orders.index')}}">
                    <div class="row">
                        <div class="col-md-5">
                            <input type="date" name="start_date" class="form-control" value="{{request('start_date')}}" />
                        </div>
                        <div class="col-md-5">
                            <input type="date" name="end_date" class="form-control" value="{{request('end_date')}}" />
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-primary" type="submit">{{ __('order.submit') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('order.ID') }}</th>
                        <th>{{ __('order.Customer_Name') }}</th>
                        <th>{{ __('Subtotal') }}</th>
                        <th>{{ __('Discount') }}</th>
                        <th>{{ __('order.Total') }}</th>
                        <th>{{ __('order.Created_At') }}</th>
                        <th>{{ __('order.Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                    <tr>
                        <td>{{$order->id}}</td>
                        <td>{{$order->getCustomerName()}}</td>
                        <td>{{ config('settings.currency_symbol') }} {{number_format($order->subtotal(), 2)}}</td>
                        <td>
                            @if($order->discount > 0)
                                @if($order->discount_type == 'percentage')
                                    {{ $order->discount }}% ({{ config('settings.currency_symbol') }} {{number_format($order->discountAmount(), 2)}})
                                @else
                                    {{ config('settings.currency_symbol') }} {{number_format($order->discount, 2)}}
                                @endif
                            @else
                                {{ config('settings.currency_symbol') }} 0.00
                            @endif
                        </td>
                        <td>{{ config('settings.currency_symbol') }} {{$order->formattedTotal()}}</td>
                        <td>{{$order->created_at->format('Y-m-d H:i')}}</td>
                        <td>
                            <div class="btn-group">
                                <button
                                    class="btn btn-sm btn-secondary btnShowInvoice"
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
                                    <ion-icon name="eye"></ion-icon>
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
        <div class="mt-3">
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
                <h5 class="modal-title" id="modalInvoiceLabel">{{ config('app.name') }} - Invoice</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Placeholder for dynamic content -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btnPrintInvoice">
                    <ion-icon name="print"></ion-icon> Print
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://unpkg.com/ionicons@4.5.10-0/dist/ionicons.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
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

            // Buka modal
            $('#modalInvoice').modal('show');

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
                            <td>${parseFloat(unitPrice).toFixed(2)}</td>
                            <td>${item.quantity}</td>
                            <td>${parseFloat(item.price).toFixed(2)}</td>
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
                <div id="invoice-content" class="card">
                    <div class="card-header">
                        Invoice <strong>#${orderId}</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-sm-6">
                                <h6 class="mb-3">Customer:</h6>
                                <div><strong>${customerName || 'General Customer'}</strong></div>
                                <div>Order Date: ${new Date(createdAt).toLocaleString()}</div>
                                <div>Invoice #: ${orderId}</div>
                            </div>
                            <div class="col-sm-6 text-right">
                                <h6 class="mb-3">Company:</h6>
                                <div><strong>{{ config('app.name') }}</strong></div>
                                <div>{{ config('settings.store_address') }}</div>
                                <div>{{ config('settings.store_phone') }}</div>
                            </div>
                        </div>
                        <div class="table-responsive-sm">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Item</th>
                                        <th>Description</th>
                                        <th>Unit Cost</th>
                                        <th>Qty</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${itemsHTML}
                                </tbody>
                                <tfoot>
                                  <tr>
                                    <th class="text-right" colspan="5">Subtotal</th>
                                    <th>{{ config('settings.currency_symbol') }} ${subtotal.toFixed(2)}</th>
                                  </tr>
                                  <tr>
                                    <th class="text-right" colspan="5">Discount</th>
                                    <th>${discountDisplay}</th>
                                  </tr>
                                  <tr>
                                    <th class="text-right" colspan="5">Total</th>
                                    <th>{{ config('settings.currency_symbol') }} ${totalAmount.toFixed(2)}</th>
                                  </tr>
                                </tfoot>
                            </table>
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
                            }
                            .card {
                                border: none;
                            }
                            .card-header, .card-body {
                                padding: 0;
                                margin-bottom: 10mm;
                            }
                            .table {
                                width: 100%;
                                margin-bottom: 1rem;
                                color: #212529;
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
                            }
                            .text-right {
                                text-align: right !important;
                            }
                        }
                    </style>
                </head>
                <body>
                    ${$('#invoice-content').prop('outerHTML')}
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
    });
</script>
@endsection