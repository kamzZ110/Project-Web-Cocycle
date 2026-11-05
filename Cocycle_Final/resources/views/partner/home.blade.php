<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Partner - CoCycle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* Definisi Palet Warna */
        :root {
            --yellow-1: #FEE59A;
            --gold-1: #F6C55A;
            --green-sage: #8A9A5B;
            --cream-1: #FFF7E0;
            --cream-2: #FFF2C6;
            --pale: #EEE9B0;
            --bg-site: #fafafaff;
            --dark-text: #222;
            --muted-text: #6b6b6b;
            --success-green: #2e7d32;
        }

        body { 
            background-color: var(--bg-site); 
            font-family: 'Poppins', sans-serif;
            color: var(--dark-text);
        }

        /* Navbar */
        .navbar { 
            background-color: var(--cream-1) !important; 
            padding: 1rem 2rem; 
            border-bottom: 2px solid var(--pale);
        }
        .navbar-brand { font-weight: 700; color: var(--dark-text) !important; }
        .brand-icon { color: var(--green-sage); font-size: 1.5rem; margin-right: 10px; }
        .nav-link { 
            font-weight: 600; 
            color: var(--muted-text) !important; 
            margin-left: 25px; 
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: color 0.3s;
        }
        .nav-link:hover { color: var(--green-sage) !important; }

        /* Headings */
        h4 { font-weight: 700; color: var(--green-sage); }

        /* Kartu Pemasukan */
        .earnings-card { 
            background-color: var(--cream-2); 
            border-radius: 20px; 
            padding: 30px; 
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .saldo-box { 
            background: white; 
            border-radius: 15px; 
            padding: 20px; 
            text-align: center; 
            border: 2px solid var(--yellow-1);
            margin-bottom: 25px;
        }
        .saldo-box h2 { 
            font-weight: 800; 
            color: var(--success-green); 
            margin: 0;
        }
        .btn-pemasukan {
            background-color: white;
            border: 2px solid var(--green-sage);
            color: var(--green-sage);
            font-weight: 700;
            padding: 12px;
            border-radius: 12px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .btn-pemasukan:hover {
            background-color: var(--green-sage);
            color: white;
        }
        .btn-pemasukan i { font-size: 1.2rem; margin-right: 8px; }

        /* Daftar Pesanan */
        .order-list { padding-right: 1rem; }
        .order-item { 
            border-bottom: 1px solid var(--pale); 
            padding: 20px 0; 
            transition: background-color 0.2s;
        }
        .order-item:hover { background-color: rgba(238, 233, 176, 0.2); padding-left: 10px; padding-right: 10px; border-radius: 10px;}
        .order-icon-box {
            width: 50px; height: 50px;
            background-color: var(--cream-1);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            margin-right: 15px;
            border: 1px solid var(--pale);
        }
        .order-icon { color: var(--green-sage); font-size: 1.5rem; }
        .badge-order { background-color: var(--green-sage); font-weight: 600; }
        .arrow-icon { color: var(--muted-text); transition: color 0.3s, transform 0.3s; }
        .order-item:hover .arrow-icon { color: var(--green-sage); transform: translateX(5px); }

        /* Kartu Produk */
        .product-card { 
            background-color: var(--pale); 
            border-radius: 18px; 
            padding: 20px; 
            margin-bottom: 20px; 
            border: none; 
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.08); }
        
        .product-img-placeholder { 
            background-color: white; 
            border-radius: 12px;
            height: 140px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            border: 2px solid var(--cream-2);
            overflow: hidden;
        }
        .product-img-placeholder img { width: 100%; height: 100%; object-fit: cover; }
        .placeholder-icon { color: var(--pale); font-size: 4rem; opacity: 0.5; }

        .price-text { color: var(--success-green); font-weight: 700; font-size: 1.1rem; }
        
        .stats-container { display: flex; gap: 15px; color: var(--muted-text); font-size: 0.9rem; }
        .stat-item i { color: var(--green-sage); margin-right: 5px; }

        .btn-ubah { 
            background-color: var(--gold-1); 
            color: white;
            border: none;
            border-radius: 8px; 
            padding: 8px 20px; 
            font-weight: 700;
            font-size: 0.9rem;
            transition: background-color 0.3s;
        }
        .btn-ubah:hover { background-color: #e0b350; }

        /* Tombol Kelola Produk */
        .btn-kelola-container { margin-top: 30px; }
        .btn-kelola { 
            background-color: var(--green-sage); 
            color: white;
            border: none; 
            border-radius: 15px; 
            font-weight: 800; 
            font-size: 1.1rem;
            width: 100%; 
            padding: 18px; 
            box-shadow: 0 4px 10px rgba(138, 154, 91, 0.4);
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .btn-kelola:hover { background-color: var(--success-green); transform: translateY(-2px); box-shadow: 0 6px 15px rgba(46, 125, 50, 0.3); }

        @media (max-width: 576px) {
            .container-fluid.mt-5 { padding-left: 1rem !important; padding-right: 1rem !important; }
            .container-fluid.mt-5 > .row { margin-left: 0; margin-right: 0; }
            .order-item > .d-flex:first-child { min-width: 0; flex: 1; }
            .order-item > .d-flex:first-child > div:last-child { min-width: 0; overflow-wrap: anywhere; }
            .order-item .badge-order { max-width: 100%; white-space: normal; overflow-wrap: anywhere; }
            .order-item > .arrow-icon { flex-shrink: 0; margin-left: .5rem; }
            .product-card .row, .product-card .col-md-8 { min-width: 0; }
            .product-card .col-md-8 { padding-left: 0 !important; margin-top: 1rem; }
            .product-card h5 { overflow-wrap: anywhere; }
            .stats-container { flex-wrap: wrap; gap: .5rem 1rem; }
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/partner-theme.css') }}">
</head>
<body>

    @if(session('role_notice'))
        <div class="alert alert-warning m-3" role="alert">
            {{ session('role_notice') }}
            <form action="{{ route('logout') }}" method="POST" class="d-inline ms-2">@csrf<button class="btn btn-sm btn-outline-dark" type="submit">Keluar untuk ganti akun</button></form>
        </div>
    @endif

    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('partner.profile.index') }}">
                <i class="bi bi-shop brand-icon"></i>
                <span>{{ strtoupper($nama_toko) }}</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="ms-auto navbar-nav">
                    <a class="nav-link" href="{{ route('partner.products') }}">PRODUCT</a>
                    <a class="nav-link" href="{{ route('partner.chat.index') }}">CHAT</a>
                    <a class="nav-link" href="{{ route('partner.analysis.index') }}">ANALISIS TOKO</a>
                    <a class="nav-link text-success" href="{{ route('partner.profile.index') }}"><i class="bi bi-person-circle"></i> PROFIL</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-5 px-5 mb-5">
        <div class="row g-5">
            
            <div class="col-lg-5">
                <h4 class="mb-4">Pemasukan Hari Ini</h4>
                <div class="card earnings-card mb-5">
                    <div class="saldo-box">
                        <small class="text-muted fw-bold ls-1">Saldo Tersedia</small>
                        <h2>Rp {{ number_format($total_income, 2, ',', '.') }}</h2>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <button onclick="window.location.href='{{ route('partner.withdrawal') }}'" class="btn btn-pemasukan w-100">
                                <i class="bi bi-cash-stack"></i> Tarik
                            </button>
                        </div>
                        <div class="col-6">
                             <button onclick="window.location.href='{{ route('partner.orders.index') }}'" class="btn btn-pemasukan w-100">
                                <i class="bi bi-clock-history"></i> Riwayat
                            </button>
                        </div>
                    </div>
                </div>

                <h4 class="mb-4">Pesanan Terbaru</h4>
                <div class="order-list">
                    @if (empty($recent_orders) || count($recent_orders) == 0)
                        <p class="text-muted text-center py-4">Belum ada pesanan.</p>
                    @else
                        @foreach($recent_orders as $order)
                        <div class="order-item d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="order-icon-box">
                                    <i class="bi bi-basket2-fill order-icon"></i>
                                </div>
                                <div>
                                    <span class="badge badge-order mb-2">#{{ $order->invoice_number }}</span>
                                    <h6 class="mb-1 fw-bold">{{ $order->user->fullname ?? $order->user->username ?? 'Guest' }}</h6>
                                    <small class="text-muted">
                                        <i class="bi bi-calendar-event me-1"></i> 
                                        {{ $order->created_at->format('d M Y | H:i') }}
                                        @php
                                            $statusColor = $order->status == 'Completed' ? 'success' : ($order->status == 'Cancelled' ? 'danger' : 'warning');
                                        @endphp
                                        <span class="ms-2 text-{{ $statusColor }}">
                                            ({{ $order->status }})
                                        </span>
                                    </small>
                                </div>
                            </div>
                            <a href="{{ route('partner.orders.show', $order->id) }}" class="arrow-icon">
                                <i class="bi bi-chevron-right fs-4"></i>
                            </a>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-lg-7">
                <h4 class="mb-4">Produk Terlaris</h4>
                
                @if(count($best_products) == 0)
                    <p class="text-muted">Belum ada data produk.</p>
                @else
                    @foreach($best_products as $p)
                    <div class="card product-card">
                        <div class="row g-0 align-items-center">
                            <div class="col-md-4">
                                <div class="product-img-placeholder">
                                    @if(!empty($p->image) && $p->image != 'default_product.jpg')
                                        <img src="{{ asset($p->image) }}" alt="Product">
                                    @else
                                        <i class="bi bi-image placeholder-icon"></i>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-8 ps-4">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h5 class="fw-bold mb-1">{{ $p->name }}</h5>
                                        <p class="price-text mb-0">Rp {{ number_format($p->price, 0, ',', '.') }}</p>
                                    </div>
                                    <a href="{{ route('partner.products.edit', $p->id) }}" class="btn btn-ubah">Ubah</a>
                                </div>
                                
                                <div class="stats-container mt-4">
                                    <div class="stat-item"><i class="bi bi-eye-fill"></i> {{ $p->views_count }} Dilihat</div>
                                    <div class="stat-item"><i class="bi bi-cart-fill"></i> {{ $p->sold_count }} Terjual</div>
                                    <div class="stat-item"><i class="bi bi-heart-fill"></i> {{ $p->rating }} Rating</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif

                <div class="btn-kelola-container">
                    <button onclick="window.location.href='{{ route('partner.products') }}'" class="btn btn-kelola">
                        <i class="bi bi-box-seam me-2"></i> KELOLA SEMUA PRODUK
                    </button>
                </div>
            </div>

        </div>
    </div>

    <script src="{{ asset('vendor/bootstrap-5.3.0.bundle.min.js') }}"></script>
</body>
</html>
