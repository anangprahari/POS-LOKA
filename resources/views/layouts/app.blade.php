<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'KOPI LOKA') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #ff6600;
            --primary-hover: #e65c00;
            --primary-light: #fff0e6;
            --text-dark: #333333;
            --text-light: #666666;
            --text-muted: #999999;
            --bg-light: #f9f9f9;
            --border-color: #eeeeee;
            --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            line-height: 1.6;
        }

        #app {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Navigation */
        .nav {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 4.5rem;
        }

        .nav-logo {
            display: flex;
            align-items: center;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .nav-logo img {
            height: 34px;
            margin-right: 10px;
        }
        .nav-logo:hover {
            color: var(--primary-color);
            text-decoration: none;
        }
        .auth-links {
            display: flex;
            align-items: center;
        }

        .btn {
            display: inline-block;
            padding: 0.625rem 1.25rem;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: var(--transition);
            border: none;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
        }

        .btn-outline {
            background-color: transparent;
            color: var(--primary-color);
            border: 1.5px solid var(--primary-color);
        }

        .btn-outline:hover {
            background-color: var(--primary-light);
            transform: translateY(-2px);
        }

        .ml-4 {
            margin-left: 1rem;
        }

        /* User Dropdown */
        .dropdown {
            position: relative;
        }

        .dropdown-toggle {
            display: flex;
            align-items: center;
            background: none;
            border: none;
            padding: 0.5rem;
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--text-dark);
            cursor: pointer;
            transition: var(--transition);
        }

        .dropdown-toggle:hover {
            color: var(--primary-color);
        }

        .dropdown-toggle svg {
            margin-left: 0.5rem;
            width: 1rem;
            height: 1rem;
        }

        .dropdown-toggle .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: var(--primary-light);
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-right: 8px;
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            width: 14rem;
            margin-top: 0.5rem;
            background-color: white;
            border-radius: 8px;
            box-shadow: var(--box-shadow);
            overflow: hidden;
            border: 1px solid var(--border-color);
            display: none;
            transform: translateY(10px);
            opacity: 0;
            transition: transform 0.3s ease, opacity 0.3s ease;
        }

        .dropdown-menu.show {
            display: block;
            transform: translateY(0);
            opacity: 1;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.25rem;
            text-decoration: none;
            color: var(--text-dark);
            font-size: 0.875rem;
            transition: var(--transition);
        }

        .dropdown-item i {
            margin-right: 10px;
            color: var(--primary-color);
            width: 20px;
            text-align: center;
        }

        .dropdown-item:hover {
            background-color: var(--primary-light);
            color: var(--primary-color);
        }

        .dropdown-divider {
            height: 1px;
            background-color: var(--border-color);
            margin: 0.25rem 0;
        }

        /* Mobile Menu Toggle */
        .mobile-menu-toggle {
            display: none;
            border: none;
            background: none;
            color: var(--text-light);
            padding: 0.5rem;
            border-radius: 6px;
            cursor: pointer;
        }

        .mobile-menu-toggle:hover {
            color: var(--primary-color);
            background-color: var(--primary-light);
        }

        .mobile-menu {
            display: none;
            background-color: white;
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 0;
        }

        .mobile-user-section {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--border-color);
        }

        .user-name {
            font-weight: 500;
            color: var(--text-dark);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }

        .user-name .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: var(--primary-light);
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-right: 10px;
        }

        .mobile-nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: var(--text-dark);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: var(--transition);
        }

        .mobile-nav-link i {
            margin-right: 12px;
            color: var(--primary-color);
            width: 20px;
            text-align: center;
        }

        .mobile-nav-link:hover {
            background-color: var(--primary-light);
            color: var(--primary-color);
        }

        .mobile-auth-buttons {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            padding: 1rem 1.5rem;
        }

        .mobile-auth-buttons .btn {
            width: 100%;
            text-align: center;
        }

        /* Main Content */
        main {
            flex-grow: 1;
        }

        .content-container {
            padding: 2rem 1.5rem;
        }

        /* Footer */
        footer {
            background-color: white;
            border-top: 1px solid var(--border-color);
            margin-top: auto;
        }

        .footer-container {
            padding: 2rem 1.5rem;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-brand {
            display: flex;
            align-items: center;
        }

        .footer-brand img {
            height: 40px;
            margin-right: 1rem;
        }

        .footer-brand-text {
            display: flex;
            flex-direction: column;
        }

        .footer-brand-name {
            font-weight: 700;
            font-size: 1.25rem;
            color: var(--primary-color);
        }

        .footer-brand-tagline {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .footer-text {
            color: var(--text-muted);
            font-size: 0.875rem;
            margin-top: 1rem;
        }

        .social-links {
            display: flex;
            gap: 1rem;
        }

        .social-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            color: var(--primary-color);
            background-color: var(--primary-light);
            border-radius: 50%;
            font-size: 1.125rem;
            transition: var(--transition);
        }

        .social-link:hover {
            background-color: var(--primary-color);
            color: white;
            transform: translateY(-3px);
        }

        /* Form Controls */
        .form-control {
            display: block;
            width: 100%;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            font-weight: 400;
            line-height: 1.5;
            color: var(--text-dark);
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            outline: 0;
            box-shadow: 0 0 0 0.25rem rgba(255, 102, 0, 0.25);
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        /* Card Styles */
        .card {
            background-color: white;
            border-radius: 12px;
            box-shadow: var(--box-shadow);
            overflow: hidden;
            transition: var(--transition);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .auth-links {
                display: none;
            }

            .mobile-menu-toggle {
                display: block;
            }

            .mobile-menu.show {
                display: block;
            }

            .footer-content {
                flex-direction: column;
                gap: 1.5rem;
                text-align: center;
            }
            
            .footer-brand {
                flex-direction: column;
                text-align: center;
            }
            
            .footer-brand img {
                margin-right: 0;
                margin-bottom: 0.5rem;
            }
            
            .social-links {
                justify-content: center;
                margin-top: 1rem;
            }
        }
    </style>
</head>

<body>
    <div id="app">
        <!-- Navigation -->
        <nav class="nav">
            <div class="container nav-container">
                <!-- Logo -->
                <a class="nav-logo" href="{{ url('/') }}">
                    <img src="{{ asset('images/kopi-loka-favicon.png') }}" alt="Logo">
                    KOPI LOKA
                </a>

                <!-- Auth Links -->
                <div class="auth-links">
                    @guest
                        <a href="{{ route('login') }}" class="btn btn-outline">{{ __('Login') }}</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-primary ml-4">{{ __('Register') }}</a>
                        @endif
                    @else
                        <!-- User Dropdown -->
                        <div class="dropdown">
                            <button class="dropdown-toggle" id="userDropdown" onclick="toggleDropdown()">
                                <div class="avatar">{{ substr(Auth::user()->getFullname(), 0, 1) }}</div>
                                {{ Auth::user()->getFullname() }}
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width: 16px; height: 16px; margin-left: 8px;">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            
                            <div class="dropdown-menu" id="userDropdownMenu">
                                <a href="#" class="dropdown-item">
                                    <i class="fas fa-user-circle"></i> Profile
                                </a>
                                <a href="#" class="dropdown-item">
                                    <i class="fas fa-cog"></i> Settings
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="{{ route('logout') }}" 
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                   class="dropdown-item">
                                    <i class="fas fa-sign-out-alt"></i> {{ __('Logout') }}
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    @endguest
                </div>

                <!-- Mobile Menu Button -->
                <button class="mobile-menu-toggle" id="mobileMenuToggle" onclick="toggleMobileMenu()">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <!-- Mobile Menu -->
            <div class="mobile-menu" id="mobileMenu">
                @guest
                    <div class="mobile-auth-buttons">
                        <a href="{{ route('login') }}" class="btn btn-outline">{{ __('Login') }}</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-primary">{{ __('Register') }}</a>
                        @endif
                    </div>
                @else
                    <div class="mobile-user-section">
                        <div class="user-name">
                            <div class="avatar">{{ substr(Auth::user()->getFullname(), 0, 1) }}</div>
                            {{ Auth::user()->getFullname() }}
                        </div>
                        <a href="#" class="mobile-nav-link">
                            <i class="fas fa-user-circle"></i> Profile
                        </a>
                        <a href="#" class="mobile-nav-link">
                            <i class="fas fa-cog"></i> Settings
                        </a>
                        <a href="{{ route('logout') }}" 
                           onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();"
                           class="mobile-nav-link">
                            <i class="fas fa-sign-out-alt"></i> {{ __('Logout') }}
                        </a>
                        <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                @endguest
            </div>
        </nav>

        <!-- Main Content -->
        <main>
            <div class="container content-container">
                @yield('content')
            </div>
        </main>

        <!-- Footer -->
        <footer>
            <div class="container footer-container">
                <div class="footer-content">
                    <div>
                        <div class="footer-brand">
                            <img src="{{ asset('images/kopi-loka-favicon.png') }}" alt="Logo">
                            <div class="footer-brand-text">
                                <div class="footer-brand-name">KOPI LOKA</div>
                                <div class="footer-brand-tagline">Temani harimu dengan secangkir kopi</div>
                            </div>
                        </div>
                        <p class="footer-text">
                            &copy; {{ date('Y') }} KOPI LOKA. All rights reserved.
                        </p>
                    </div>
                    <div class="social-links">
                        <a href="#" class="social-link" aria-label="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://www.instagram.com/praf.bude?igsh=MXJ0aHdjMmV2b2sxYQ==" class="social-link" aria-label="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="social-link" aria-label="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="social-link" aria-label="TikTok">
                            <i class="fab fa-tiktok"></i>
                        </a>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- JavaScript for interactive components -->
    <script>
        // Dropdown Toggle with animation
        function toggleDropdown() {
            const dropdownMenu = document.getElementById('userDropdownMenu');
            dropdownMenu.classList.toggle('show');
        }

        // Mobile Menu Toggle with icon change
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobileMenu');
            mobileMenu.classList.toggle('show');
            
            const menuIcon = document.querySelector('#mobileMenuToggle i');
            if (mobileMenu.classList.contains('show')) {
                menuIcon.classList.remove('fa-bars');
                menuIcon.classList.add('fa-times');
            } else {
                menuIcon.classList.remove('fa-times');
                menuIcon.classList.add('fa-bars');
            }
        }

        // Close dropdown when clicking outside
        window.addEventListener('click', function(event) {
            const dropdown = document.getElementById('userDropdown');
            const dropdownMenu = document.getElementById('userDropdownMenu');
            
            if (dropdown && dropdownMenu && dropdown !== event.target && !dropdown.contains(event.target)) {
                dropdownMenu.classList.remove('show');
            }
        });
    </script>
</body>

</html>