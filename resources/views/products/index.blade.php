@extends('layouts.admin')

@section('title', __('product.Product_List'))
@section('content-header', __('product.Product_List'))
@section('content-actions')
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

/* Product Image */
.product-img {
    width: 65px;
    height: 65px;
    object-fit: cover;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.product-img:hover {
    transform: scale(1.1);
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

.badge-custom.active {
    background-color: #2ecc71;
    color: white;
}

.badge-custom.inactive {
    background-color: #e74c3c;
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

.btn-danger {
    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
    border: none;
    box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);
}

.btn-danger:hover {
    background: linear-gradient(135deg, #c0392b 0%, #a93226 100%);
    box-shadow: 0 6px 20px rgba(231, 76, 60, 0.4);
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
<div class="row mb-4">
    <div class="col-md-6">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="productSearch" class="form-control" placeholder="{{ __('Search Products') }}">
        </div>
    </div>
    <div class="col-md-6">
        <div class="filter-buttons text-md-right">
            <button class="filter-button active">{{ __('All') }}</button>
            <button class="filter-button">{{ __('Active') }}</button>
            <button class="filter-button">{{ __('Inactive') }}</button>
        </div>
    </div>
</div>

<!-- Stats Cards - Similar to Dashboard style -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
        <div class="dashboard-card card-sales">
            <div class="animated-bg"></div>
            <div class="card-icon">
                <i class="fas fa-box"></i>
            </div>
            <div class="card-value">{{ $products->total() }}</div>
            <div class="card-title">{{ __('Total Products Unit') }}</div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
        <div class="dashboard-card card-income">
            <div class="animated-bg"></div>
            <div class="card-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="card-value">{{ $products->where('status', 1)->count() }}</div>
            <div class="card-title">{{ __('Active Products Unit') }}</div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
        <div class="dashboard-card card-income-today">
            <div class="animated-bg"></div>
            <div class="card-icon">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="card-value">{{ $products->where('status', 0)->count() }}</div>
            <div class="card-title">{{ __('Inactive Products Unit') }}</div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
        <div class="dashboard-card card-customers">
            <div class="animated-bg"></div>
            <div class="card-icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="card-value">Rp. {{ number_format($products->sum('price'), 0, ',', '.') }}</div>
            <div class="card-title">{{ __('Total Value Unit') }}</div>
        </div>
    </div>
</div>

<!-- Product List Table -->
<div class="card product-list shadow-sm border-0">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h3 class="m-0">{{ __('product.Product_List') }}</h3>
        
        <div class="d-flex flex-grow-1 justify-content-between align-items-center ml-3">
            <div class="flex-grow-1 text-center">
                <a href="{{ route('products.create') }}" class="btn btn-orange">
                    <i class="fas fa-plus-circle mr-1"></i>
                    {{ __('Add New Product') }}
                </a>
            </div>
            <div class="text-right">
                <a href="{{ route('products.export') }}" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> {{ __('Export to Excel') }}
                </a>
            </div>
        </div>
    </div>
    
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap align-middle">
            <thead>
                <tr>
                    <th>{{ __('product.ID') }}</th>
                    <th>{{ __('product.Name') }}</th>
                    <th>{{ __('product.Image') }}</th>
                    <th>{{ __('product.Barcode') }}</th>
                    <th>{{ __('product.Price') }}</th>
                    <th>{{ __('product.Quantity') }}</th>
                    <th>{{ __('product.Status') }}</th>
                    <th>{{ __('product.Created_At') }}</th>
                    <th>{{ __('product.Updated_At') }}</th>
                    <th>{{ __('product.Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                <tr>
                    <td>{{$product->id}}</td>
                    <td>{{$product->name}}</td>
                    <td><img class="product-img" src="{{ Storage::url($product->image) }}" alt=""></td>
                    <td>{{$product->barcode}}</td>
                    <td>Rp. {{ number_format($product->price, 0, ',', '.') }}</td>
                    <td>{{ $product->quantity }} cup</td>
                    <td>
                        <span class="badge-custom {{ $product->status ? 'active' : 'inactive' }}">
                            {{$product->status ? __('common.Active') : __('common.Inactive') }}
                        </span>
                    </td>
                    <td>{{$product->created_at}}</td>
                    <td>{{$product->updated_at}}</td>
                    <td>
                        <div class="btn-group">
                            <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn btn-sm btn-danger btn-delete" data-url="{{route('products.destroy', $product)}}">
                                <i class="fas fa-trash"></i>
                            </button>
                            <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        <div class="d-flex justify-content-center">
            {{ $products->render() }}
        </div>
    </div>
</div>

<!-- Product Detail Modal -->
<div class="modal fade" id="productDetailModal" tabindex="-1" role="dialog" aria-labelledby="productDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productDetailModalLabel">{{ __('Product Detail') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div id="productDetailContent" style="display: none;">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="product-image-container text-center mb-3">
                                <img id="modal-product-image" class="img-fluid rounded" src="" alt="Product Image">
                            </div>
                            <div class="product-status text-center">
                                <span id="modal-product-status" class="badge-custom"></span>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <h3 id="modal-product-name" class="mb-3"></h3>
                            <div class="product-details">
                                <div class="product-info-row">
                                    <strong>{{ __('product.Barcode') }}:</strong>
                                    <span id="modal-product-barcode"></span>
                                </div>
                                <div class="product-info-row">
                                    <strong>{{ __('product.Price') }}:</strong>
                                    <span id="modal-product-price" class="text-primary font-weight-bold"></span>
                                </div>
                                <div class="product-info-row">
                                    <strong>{{ __('product.Quantity') }}:</strong>
                                    <span id="modal-product-quantity"></span>
                                </div>
                                <div class="product-info-row">
                                    <strong>{{ __('product.Created_At') }}:</strong>
                                    <span id="modal-product-created"></span>
                                </div>
                                <div class="product-info-row">
                                    <strong>{{ __('product.Updated_At') }}:</strong>
                                    <span id="modal-product-updated"></span>
                                </div>
                            </div>
                            <div class="product-description mt-4">
                                <h5>{{ __('product.Description') }}</h5>
                                <p id="modal-product-description" class="text-muted"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> {{ __('Close') }}
                </button>
            </div>
        </div>
    </div>
</div>


<!-- Add animation styles for the dashboard-card class -->
<style>
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
    
    .animated-bg {
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 100%);
        z-index: 0;
    }
    .product-image-container {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 20px;
    }
    
    .product-image-container img {
        max-height: 250px;
        object-fit: contain;
    }
    
    .product-info-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #eee;
    }
    
    .product-info-row:last-child {
        border-bottom: none;
    }
    
    .product-details {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 12px;
        margin-bottom: 15px;
    }
    
    #productDetailModal .modal-content {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }
    
    #productDetailModal .modal-header {
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        color: white;
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
        padding: 15px 20px;
    }
    
    #productDetailModal .modal-footer {
        border-top: 1px solid rgba(0,0,0,0.05);
    }
</style>
@endsection

@section('js')
<script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<script type="module">
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
        
        // Initialize search functionality
        $("#productSearch").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("table tbody tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
        
       // Filter buttons functionality - Fixed properly
$(".filter-button").click(function() {
    $(".filter-button").removeClass("active");
    $(this).addClass("active");
    
    var filter = $(this).text().trim().toLowerCase();
    
    $("table tbody tr").each(function() {
        // Mencari elemen span dengan class badge-custom di dalam baris
        var statusBadge = $(this).find("td .badge-custom");
        
        if (filter === '{{ strtolower(__("all")) }}') {
            $(this).show();
        } else if (filter === '{{ strtolower(__("active")) }}') {
            // Cek apakah badge memiliki class 'active'
            if (statusBadge.hasClass('active')) {
                $(this).show();
            } else {
                $(this).hide();
            }
        } else if (filter === '{{ strtolower(__("inactive")) }}') {
            // Cek apakah badge memiliki class 'inactive'
            if (statusBadge.hasClass('inactive')) {
                $(this).show();
            } else {
                $(this).hide();
            }
        }
    });
});
        
        // Delete product functionality (from original code)
        $(document).on('click', '.btn-delete', function() {
            var $this = $(this);
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: '{{ __('product.sure ') }}',
                text: '{{ __('product.really_delete ') }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ __('product.yes_delete ') }}',
                cancelButtonText: '{{ __('product.No ') }}',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.post($this.data('url'), {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    }, function(res) {
                        $this.closest('tr').fadeOut(500, function() {
                            $(this).remove();
                        });
                        
                        // Update card counts after deletion
                        let totalCount = parseInt($('.card-sales .card-value').text()) - 1;
                        $('.card-sales .card-value').text(totalCount);
                        
                        if($this.closest('tr').find('.badge-custom').hasClass('active')) {
                            let activeCount = parseInt($('.card-income .card-value').text()) - 1;
                            $('.card-income .card-value').text(activeCount);
                        } else {
                            let inactiveCount = parseInt($('.card-income-today .card-value').text()) - 1;
                            $('.card-income-today .card-value').text(inactiveCount);
                        }
                    });
                }
            });
        });
    });
    $(document).on('click', '.btn-info', function(e) {
    e.preventDefault();
    
    // Get the full URL from the button's href
    var url = $(this).attr('href');
    
    // Show modal and loading spinner
    $('#productDetailModal').modal('show');
    $('#productDetailContent').hide();
    $('.spinner-border').show();
    
    // Helper function for number formatting
    function numberWithCommas(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
    
    // Fetch product details via AJAX with the correct URL
    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(response) {
            console.log("Response data:", response);
            
            // Hide spinner and show content
            $('.spinner-border').hide();
            $('#productDetailContent').show();
            
            // Format date and time
            const createdDate = new Date(response.data.created_at);
            const updatedDate = new Date(response.data.updated_at);
            const dateOptions = { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            
            // Populate modal with product data
            $('#modal-product-name').text(response.data.name);
            $('#modal-product-image').attr('src', response.data.image_url);
            $('#modal-product-barcode').text(response.data.barcode);
            $('#modal-product-price').text(`Rp. ${numberWithCommas(response.data.price)}`);
            $('#modal-product-quantity').text(`${response.data.quantity} cup`);
            $('#modal-product-created').text(createdDate.toLocaleDateString('id-ID', dateOptions));
            $('#modal-product-updated').text(updatedDate.toLocaleDateString('id-ID', dateOptions));
            $('#modal-product-description').text(response.data.description || '{{ __("product.No_Description") }}');
            
            // Set product status
            if (response.data.status) {
                $('#modal-product-status').removeClass('inactive').addClass('active').text('{{ __("common.Active") }}');
            } else {
                $('#modal-product-status').removeClass('active').addClass('inactive').text('{{ __("common.Inactive") }}');
            }
            
            // Set edit link
            $('#modal-edit-link').attr('href', `/products/${response.data.id}/edit`);
        },
        error: function(xhr, status, error) {
            // Handle error with more detail
            console.error("AJAX Error:", xhr.responseText);
            $('.spinner-border').hide();
            $('#productDetailContent').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> Error: ${xhr.status} - ${error}
                    <br>
                    <small>Check console for more details</small>
                </div>
            `).show();
        }
    });
});
</script>

@endsection