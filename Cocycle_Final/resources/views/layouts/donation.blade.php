<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cocycle - Donation</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/donation.css') }}">
    
    <!-- Custom Donation Styles -->
    <style>
        /* Replicate legacy donation/style.css if available, or basic header styles */
        .navbar-custom { background-color: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .logo img { height: 50px; }
    </style>
    @stack('styles')
</head>
<body>

    <nav class="navbar navbar-expand-lg fixed-top navbar-custom">
        <div class="container">
            <a href="{{ route('customer.home') }}" class="logo">
                <img src="{{ asset('assets/logo_cocycle.png') }}" alt="logo" width="60">
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <!-- Donation specific nav items -->
                    <li class="nav-item"><a class="nav-link" href="{{ route('donation.index') }}">Donation Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('donation.impact') }}">Impact Tracker</a></li>
                </ul>
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('donation.cart') }}" class="position-relative text-dark">
                        <i class="fas fa-shopping-bag fa-lg"></i>
                        @if(session('donation_cart') && count(session('donation_cart')) > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                            {{ count(session('donation_cart')) }}
                        </span>
                        @endif
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Spacer for fixed navbar -->
    <div style="margin-top: 80px;"></div>

    <main>
        @include('partials.feedback')
        @yield('content')
    </main>

    <script src="{{ asset('vendor/bootstrap-5.3.2.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/jquery-3.6.0.min.js') }}"></script>
    @stack('scripts')
</body>
</html>
