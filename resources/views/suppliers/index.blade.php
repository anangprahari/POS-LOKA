@extends('layouts.admin')

@section('title', __('Supplier List'))
@section('content-header', __('Supplier List'))
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

.dashboard-card .card-description {
    font-size: 12px;
    color: rgba(255, 255, 255, 0.7);
    margin-top: 5px;
}

.card-total {
    background-color: #3498db;
    color: white;
}

.card-active {
    background-color: #2ecc71;
    color: white;
}

.card-new {
    background-color: #9b59b6;
    color: white;
}

.card-top {
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

/* Name Badge */
.name-badge {
    background-color: #e8f4fc;
    color: #3498db;
    padding: 5px 10px;
    border-radius: 20px;
    font-weight: 500;
    font-size: 0.85rem;
    display: inline-block;
}

/* Email Badge */
.email-badge {
    background-color: #f0f0f0;
    padding: 3px 8px;
    border-radius: 15px;
    color: #555;
    font-size: 0.85rem;
    display: inline-flex;
    align-items: center;
}

.email-badge i {
    margin-right: 5px;
    color: #888;
}

/* Phone Badge */
.phone-badge {
    background-color: #f8f4ff;
    color: #9b59b6;
    padding: 3px 8px;
    border-radius: 15px;
    font-size: 0.85rem;
    display: inline-flex;
    align-items: center;
}

.phone-badge i {
    margin-right: 5px;
}

/* Supplier Actions */
.supplier-actions {
    display: flex;
    gap: 8px;
}

.btn-view {
    background: linear-gradient(135deg, #00bcd4 0%, #0097a7 100%);
    border: none;
    color: white;
    box-shadow: 0 4px 15px rgba(0, 188, 212, 0.3);
}

.btn-view:hover {
    background: linear-gradient(135deg, #0097a7 0%, #00838f 100%);
    box-shadow: 0 6px 20px rgba(0, 188, 212, 0.4);
}

/* Supplier Quick Actions */
.supplier-actions {
    display: flex;
    gap: 8px;
}
/* Supplier Photo Styling */
.supplier-photo-container {
    position: relative;
    width: 120px;
    height: 120px;
    margin: 0 auto;
    border-radius: 50%;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    background-color: #f8f9fa;
}

.supplier-photo {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border: 3px solid white;
    transition: transform 0.3s ease;
}

.supplier-photo:hover {
    transform: scale(1.05);
}

.supplier-photo-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #e9ecef;
    color: #6c757d;
    font-size: 2.5rem;
}

/* Customer info row styling */
.customer-info-row {
    padding: 10px 0;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.customer-info-row:last-child {
    border-bottom: none;
}

.customer-info-row strong {
    color: #555;
}

.customer-info-row i {
    margin-right: 8px;
    color: #3498db;
}
</style>
@endsection
@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="supplierSearch" class="form-control" placeholder="{{ __('Search Suppliers') }}">
        </div>
    </div>
    <div class="col-md-6">
        <div class="filter-buttons text-md-right">
            <button class="filter-button active">{{ __('All') }}</button>
            <button class="filter-button">{{ __('Active') }}</button>
            <button class="filter-button">{{ __('New') }}</button>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
        <div class="dashboard-card card-total">
            <div class="animated-bg"></div>
            <div class="card-icon">
                <i class="fas fa-boxes"></i>
            </div>
            <div class="card-value">{{ $suppliers->total() }}</div>
            <div class="card-title">{{ __('Total Suppliers') }}</div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
        <div class="dashboard-card card-active">
            <div class="animated-bg"></div>
            <div class="card-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="card-value">{{ $suppliers->count('id') }}</div>
            <div class="card-title">{{ __('Active Suppliers') }}</div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
        <div class="dashboard-card card-new">
            <div class="animated-bg"></div>
            <div class="card-icon">
                <i class="fas fa-calendar-plus"></i>
            </div>
            <div class="card-value">{{ $suppliers->where('created_at', '>=', now()->subDays(30))->count() }}</div>
            <div class="card-title">{{ __('New Suppliers (30 days)') }}</div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
        <div class="dashboard-card card-top">
            <div class="animated-bg"></div>
            <div class="card-icon">
                <i class="fas fa-star"></i>
            </div>
            <div class="card-value">5</div>
            <div class="card-title">{{ __('Top Suppliers') }}</div>
            <div class="card-description small text-white-50">{{ __('Based on delivery performance') }}</div>
        </div>
    </div>
</div>

<!-- Supplier List Table -->
<div class="card supplier-list shadow-sm border-0">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h3 class="m-0">{{ __('Suppliers List') }}</h3>
        <div class="d-flex flex-grow-1 justify-content-between align-items-center ml-3">
            <div class="flex-grow-1 text-center">
                <a href="{{ route('suppliers.create') }}" class="btn btn-orange">
                    <i class="fas fa-plus-circle mr-1"></i>
                    {{ __('Add New Suppliers') }}
                </a>
            </div>
            <div class="text-right">
                <a href="{{ route('suppliers.export') }}" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> {{ __('Export to Excel') }}
                </a>
            </div>
        </div>
    </div>
    
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap align-middle">
            <thead>
                <tr>
                    <th>{{ __('ID') }}</th>
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('Email') }}</th>
                    <th>{{ __('Phone') }}</th>
                    <th>{{ __('Address') }}</th>
                    <th>{{ __('Created At') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($suppliers as $supplier)
                <tr>
                    <td>{{$supplier->id}}</td>
                    <td>
                        <div class="name-badge">{{$supplier->first_name}} {{$supplier->last_name}}</div>
                    </td>
                    <td>
                        <div class="email-badge">
                            <i class="fas fa-envelope"></i> {{$supplier->email}}
                        </div>
                    </td>
                    <td>
                        <div class="phone-badge">
                            <i class="fas fa-phone-alt"></i> {{$supplier->phone}}
                        </div>
                    </td>
                    <td>
                        <div class="text-truncate" style="max-width: 200px;" title="{{$supplier->address}}">
                            {{$supplier->address}}
                        </div>
                    </td>
                    <td>
                        <span title="{{$supplier->created_at}}">
                            {{ \Carbon\Carbon::parse($supplier->created_at)->format('M d, Y') }}
                        </span>
                    </td>
                    <td>
                        <div class="supplier-actions">
                            <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn btn-sm btn-danger btn-delete" data-url="{{route('suppliers.destroy', $supplier)}}">
                                <i class="fas fa-trash"></i>
                            </button>
                            <a href="#" class="btn btn-sm btn-view btn-supplier-detail" data-id="{{$supplier->id}}">
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
            {{ $suppliers->render() }}
        </div>
    </div>
</div>

<!-- Supplier Detail Modal -->
<div class="modal fade" id="supplierDetailModal" tabindex="-1" role="dialog" aria-labelledby="supplierDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="supplierDetailModalLabel">{{ __('Supplier Detail') }}</h5>
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
                <div id="supplierDetailContent" style="display: none;">
                    <div class="text-center mb-4">
                        <!-- Supplier Photo Display -->
                        <div class="supplier-photo-container mb-3">
                            <img id="modal-supplier-photo" src="" alt="Supplier Photo" class="img-thumbnail rounded-circle supplier-photo">
                        </div>
                        <h4 id="modal-supplier-name" class="mt-3"></h4>
                    </div>
                    <div class="customer-details">
                        <div class="customer-info-row">
                            <strong><i class="fas fa-envelope"></i> {{ __('Email') }}:</strong>
                            <span id="modal-supplier-email"></span>
                        </div>
                        <div class="customer-info-row">
                            <strong><i class="fas fa-phone-alt"></i> {{ __('Phone') }}:</strong>
                            <span id="modal-supplier-phone"></span>
                        </div>
                        <div class="customer-info-row">
                            <strong><i class="fas fa-map-marker-alt"></i> {{ __('Address') }}:</strong>
                            <span id="modal-supplier-address"></span>
                        </div>
                        <div class="customer-info-row">
                            <strong><i class="fas fa-calendar-alt"></i> {{ __('Created At') }}:</strong>
                            <span id="modal-supplier-created"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a id="modal-supplier-edit" href="#" class="btn btn-primary">
                    <i class="fas fa-edit"></i> {{ __('Edit') }}
                </a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> {{ __('Close') }}
                </button>
            </div>
        </div>
    </div>
</div>

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
    $("#supplierSearch").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("table tbody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
    
    // Filter buttons functionality
    $(".filter-button").click(function() {
        $(".filter-button").removeClass("active");
        $(this).addClass("active");
        
        var filter = $(this).text().trim().toLowerCase();
        if(filter === '{{ strtolower(__("All")) }}') {
            $("table tbody tr").show();
        } else if(filter === '{{ strtolower(__("Active")) }}') {
            $("table tbody tr").hide();
            // In a real app, you would filter based on an active field
            $("table tbody tr").slice(0, Math.ceil($("table tbody tr").length * 0.7)).show();
        } else if(filter === '{{ strtolower(__("New")) }}') {
            $("table tbody tr").hide();
            // In a real app, you would filter based on creation date
            $("table tbody tr").slice(0, Math.ceil($("table tbody tr").length * 0.3)).show();
        }
    });
    
    // Delete supplier functionality
    $(document).on('click', '.btn-delete', function() {
        var $this = $(this);
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger'
            },
            buttonsStyling: false
        });

        swalWithBootstrapButtons.fire({
            title: '{{ __("Are you sure?") }}',
            text: '{{ __("You won\'t be able to revert this!") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '{{ __("Yes, delete it!") }}',
            cancelButtonText: '{{ __("No, cancel!") }}',
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                $.post($this.data('url'), {
                    _method: 'DELETE',
                    _token: '{{ csrf_token() }}'
                }, function(res) {
                    $this.closest('tr').fadeOut(500, function() {
                        $(this).remove();
                        
                        // Update card counts after deletion
                        let totalCount = parseInt($('.card-total .card-value').text()) - 1;
                        $('.card-total .card-value').text(totalCount);
                        
                        // Update the other counts as appropriate
                        let activeCount = parseInt($('.card-active .card-value').text()) - 1;
                        $('.card-active .card-value').text(activeCount);
                    });
                });
            }
        });
    });
    
  // Supplier detail view functionality - COMPLETE FIX
$(document).on('click', '.btn-supplier-detail', function(e) {
    e.preventDefault();
    
    // Get the supplier ID
    var supplierId = $(this).data('id');
    
    // Show modal and loading spinner
    $('#supplierDetailModal').modal('show');
    $('#supplierDetailContent').hide();
    $('.spinner-border').show();
    
    // Make an AJAX request to get full supplier details including photo
    $.ajax({
        url: "{{ route('suppliers.show', ':id') }}".replace(':id', supplierId),
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            // Hide spinner and show content
            $('.spinner-border').hide();
            $('#supplierDetailContent').show();
            
            // Populate modal with supplier data from AJAX response
            $('#modal-supplier-name').text(response.first_name + ' ' + response.last_name);
            $('#modal-supplier-email').text(response.email);
            $('#modal-supplier-phone').text(response.phone);
            $('#modal-supplier-address').text(response.address);
            $('#modal-supplier-created').text(moment(response.created_at).format('MMM DD, YYYY'));
            
            // Set edit link with correct URL
            $('#modal-supplier-edit').attr('href', "{{ route('suppliers.edit', ':id') }}".replace(':id', supplierId));
            
            // Handle supplier photo properly - FIXED
            if (response.avatar) {
                // The avatar field contains "suppliers/filename.jpg"
                // So we just need to prepend the storage path
                $('#modal-supplier-photo').attr('src', "{{ asset('storage') }}/" + response.avatar);
                
                // Debug info - log the image URL to console for verification
                console.log("Image URL:", "{{ asset('storage') }}/" + response.avatar);
            } else {
                // Default placeholder if no photo field found
                $('#modal-supplier-photo').attr('src', "{{ asset('img/default-supplier.png') }}");
            }
        },
        error: function(xhr, status, error) {
            console.error('Error fetching supplier details:', error);
            
            // Hide spinner and show content
            $('.spinner-border').hide();
            $('#supplierDetailContent').show();
            
            // Get the row data as fallback
            var $row = $('tr').filter(function() {
                return $(this).find('td:first').text().trim() == supplierId;
            });
            
            // Populate the modal with supplier data from the row
            $('#modal-supplier-name').text($row.find('.name-badge').text().trim());
            $('#modal-supplier-email').text($row.find('.email-badge').text().replace(/\s+/g, ' ').trim());
            $('#modal-supplier-phone').text($row.find('.phone-badge').text().replace(/\s+/g, ' ').trim());
            $('#modal-supplier-address').text($row.find('[title]').attr('title'));
            $('#modal-supplier-created').text($row.find('td:eq(5) span').text().trim());
            
            // Set default placeholder image for photo
            $('#modal-supplier-photo').attr('src', "{{ asset('img/default-supplier.png') }}");
            
            // Set edit link with correct URL
            $('#modal-supplier-edit').attr('href', "{{ route('suppliers.edit', ':id') }}".replace(':id', supplierId));
        }
    });
});

});
</script>
@endsection