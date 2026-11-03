<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name', 'CoCycle')) - Eat Smart, Waste Less</title>
    <meta name="description" content="@yield('meta_description', 'CoCycle adalah platform yang menghubungkan toko makanan dengan konsumen untuk mengurangi food waste dengan harga terjangkau.')">
    <meta name="keywords" content="food waste, makanan murah, selamatkan makanan, cocycle, lingkungan">
    <meta name="author" content="CoCycle Team">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', config('app.name', 'CoCycle'))">
    <meta property="og:description" content="@yield('meta_description', 'Selamatkan makanan, selamatkan bumi bersama CoCycle.')">
    <meta property="og:image" content="{{ asset('assets/logo_cocycle.png') }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    
    <!-- Scripts -->
    <script src="{{ asset('vendor/jquery-3.6.0.min.js') }}"></script>
    
    <!-- Custom Styles -->
    
    <style>
        body { font-family: 'Nunito', sans-serif; background-color: #fafafa; }
        /* Common Header Styles replicating legacy Katalog header */
        header { background: #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.05); padding: 15px 5%; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 1000; }
        .logo img { height: 50px; width: auto; }
        #navMenu { display: flex; gap: 20px; align-items: center; }
        #navMenu a { text-decoration: none; color: #555; font-weight: 600; font-size: 1rem; transition: 0.3s; }
        #navMenu a:hover { color: #5CB338; }
        .search-container { position: relative; }
        .search-input { padding: 8px 15px; border-radius: 20px 0 0 20px; border: 1px solid #ddd; outline: none; background: #f9f9f9; }
        .search-btn { padding: 8px 15px; border-radius: 0 20px 20px 0; border: 1px solid #ddd; border-left: none; background: #5CB338; color: white; cursor: pointer; }
    </style>
    @stack('styles')
    <style>
        @media (max-width: 700px) {
            header { flex-wrap: wrap; gap: 10px; padding: 12px 16px; }
            #navMenu { width: 100%; flex-wrap: wrap; gap: 10px; justify-content: center; }
            #navMenu .search-container { flex: 1 1 100%; min-width: 0; }
            #navMenu .search-input { flex: 1; width: 0; min-width: 0; margin-right: 0; }
            #navMenu a { font-size: 14px; }
            body { overflow-x: hidden; }
            .container, .container-fluid { max-width: 100%; }
            .container-fluid.px-5 { padding-left: 1rem !important; padding-right: 1rem !important; }
            .row { --bs-gutter-x: 1rem; }
            .table-responsive { overflow-x: auto; }
            .popup-view .popup-card { width: calc(100vw - 24px); max-height: 90vh; overflow-y: auto; margin: 12px; }
        }
    </style>
</head>
<body>

    <header>
        <a href="{{ route('customer.home') }}" class="logo">
            <img src="{{ asset('assets/logo_cocycle.png') }}" alt="logo">
        </a>
        <nav id="navMenu">
            <!-- Reuse Logic from Katalog Header -->
             <form action="{{ route('marketplace.index') }}" method="GET" class="search-container d-flex">
                <input type="text" name="search" class="search-input"
                    placeholder="Search products..."
                    value="{{ request('search') }}">
                <button type="submit" class="search-btn">
                    <i class="fas fa-search"></i>
                </button>
            </form>

            <a href="{{ route('cart.index') }}" class="active">
                <i class="fas fa-shopping-basket fa-lg"></i>
            </a>
            
            <a href="{{ route('customer.home') }}">Home</a>
            <a href="{{ route('landing') }}#about">About Us</a>
            <a href="{{ route('landing') }}#community">Community</a>
        </nav>
    </header>

    <main>
        @include('partials.feedback')
        @yield('content')
    </main>

    <!-- Scripts -->
    <script src="{{ asset('vendor/bootstrap-5.3.2.bundle.min.js') }}"></script>
    @stack('scripts')
</body>
</html>
