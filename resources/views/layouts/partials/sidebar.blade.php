<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-light-primary elevation-3" style="position: fixed; height: 100vh;">
    <!-- Brand Logo -->
    <a href="{{ auth()->user()->role === 'admin' ? route('home') : route('user.dashboard') }}" class="brand-link d-flex align-items-center justify-content-start bg-white shadow-sm">
        <img src="{{ asset('images/kopi-loka-favicon.png') }}" alt="Logo" class="brand-image img-circle elevation-2" style="height: 35px;">
       <span class="brand-text ml-2" style="color: #FF6600; font-weight: 800;">
    {{ config('app.name') }}
</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
            <div class="image">
                <img src="{{ auth()->user()->getAvatar() }}" class="img-circle elevation-2" alt="User Image" style="height: 35px;">
            </div>
            <div class="info">
                <a href="#" class="d-block text-sm text-dark">{{ auth()->user()->getFullname() }}</a>
                <span class="badge {{ auth()->user()->role === 'admin' ? 'badge-primary' : 'badge-success' }}">
                    {{ ucfirst(auth()->user()->role) }}
                </span>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" 
                data-widget="treeview" role="menu" data-accordion="false">

                @php
                $user = auth()->user();
                $isAdmin = $user->role === 'admin';
                $currentRoute = request()->route()->getName();
            
                $menus = [
                    // Admin melihat dashboard admin
                    [
                        'route' => 'home', 
                        'icon' => 'fas fa-tachometer-alt', 
                        'label' => __('dashboard.title'), 
                        'visible' => $isAdmin
                    ],
                    
                    // User melihat dashboard user
                    [
                        'route' => 'user.dashboard', 
                        'icon' => 'fas fa-tachometer-alt', 
                        'label' => __('dashboard.title'), 
                        'visible' => !$isAdmin
                    ],
                    
                    // Menu POS untuk semua role
                    [
                        'route' => 'cart.index', 
                        'icon' => 'fas fa-cart-plus', 
                        'label' => __('cart.title'), 
                        'segment' => 'cart', 
                        'visible' => true
                    ],
                    
                    // Menu Customer untuk semua role
                    [
                        'route' => 'customers.index', 
                        'icon' => 'fas fa-users', 
                        'label' => __('customer.title'), 
                        'segment' => 'customers', 
                        'visible' => true
                    ],
                    
                    // Menu khusus admin
                    [
                        'route' => 'products.index', 
                        'icon' => 'fas fa-th-large', 
                        'label' => __('product.title'), 
                        'segment' => 'products', 
                        'visible' => $isAdmin
                    ],
                    [
                        'route' => 'orders.index', 
                        'icon' => 'fas fa-shopping-basket', 
                        'label' => __('order.title'), 
                        'segment' => 'orders', 
                        'visible' => $isAdmin
                    ],
                    [
                        'route' => 'suppliers.index', 
                        'icon' => 'fas fa-people-carry', 
                        'label' => __('Supplier'), 
                        'segment' => 'suppliers', 
                        'visible' => $isAdmin
                    ],
                    [
                        'route' => 'settings.index', 
                        'icon' => 'fas fa-cogs', 
                        'label' => __('settings.title'), 
                        'segment' => 'settings', 
                        'visible' => $isAdmin
                    ],
                ];
            @endphp
            
            @foreach ($menus as $menu)
                @if ($menu['visible'])
                    <li class="nav-item">
                        <a href="{{ route($menu['route']) }}" 
                           class="nav-link {{ 
                                $currentRoute === $menu['route'] || 
                                (isset($menu['segment']) && request()->segment(2) === $menu['segment']) 
                                ? 'active' : '' 
                            }}">
                            <i class="nav-icon {{ $menu['icon'] }}"></i>
                            <p>{{ $menu['label'] }}</p>
                        </a>
                    </li>
                @endif
            @endforeach
            
                <!-- Logout -->
                <li class="nav-item mt-3">
                    <a href="#" class="nav-link text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>{{ __('common.Logout') }}</p>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li>

            </ul>
        </nav>
    </div>
</aside>