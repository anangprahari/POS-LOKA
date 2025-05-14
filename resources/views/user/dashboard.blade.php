@extends('layouts.admin')

@section('title', 'User Dashboard')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">User Dashboard</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header border-0">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title">Quick Actions</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-column">
                            <a href="{{ route('cart.index') }}" class="btn btn-primary mb-3">
                                <i class="fas fa-shopping-cart mr-2"></i> Point of Sale
                            </a>
                            <a href="{{ route('customers.index') }}" class="btn btn-secondary">
                                <i class="fas fa-users mr-2"></i> Manage Customers
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header border-0">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title">Customer Information</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center border-bottom mb-3">
                            <p class="text-success text-xl">
                                <i class="fas fa-users"></i>
                            </p>
                            <p class="d-flex flex-column text-right">
                                <span class="font-weight-bold">
                                    {{ $customers_count }}
                                </span>
                                <span class="text-muted">Total Customers</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection