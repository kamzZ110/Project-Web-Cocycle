<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Produk - CoCycle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { 
            --green-sage: #6B8E23; 
            --green-light: #9ACD32;
            --teal: #20B2AA;
            --orange: #FF8C42;
            --pale: #EEE9B0; 
            --bg: #fafafaff; 
        }
        body { background-color: var(--bg); font-family: 'Poppins', sans-serif; }
        
        /* Header & Tabs - Vibrant Gradient */
        .header-bg { 
            background: linear-gradient(135deg, #6B8E23 0%, #9ACD32 50%, #20B2AA 100%);
            border-bottom: none;
            padding-top: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .header-bg h5, .header-bg .nav-link, .header-bg span, .header-bg a { color: white !important; }
        
        .nav-tabs-custom { border-bottom: none; margin-top: 10px; }
        .nav-tabs-custom .nav-link { 
            color: rgba(255,255,255,0.7) !important;
            border: none; 
            padding: 15px 30px; 
            font-weight: 600; 
            text-transform: uppercase;
            letter-spacing: 0.5px; 
            transition: all 0.3s;
            border-radius: 8px 8px 0 0;
        }
        .nav-tabs-custom .nav-link:hover { 
            color: white !important;
            background: rgba(255,255,255,0.15);
        }
        .nav-tabs-custom .nav-link.active { 
            color: white !important;
            background: rgba(255,255,255,0.25);
            border-bottom: 3px solid white;
            font-weight: 700;
        }

        /* Product Card */
        .product-card {
            background: white; border-radius: 15px; overflow: hidden;
            transition: 0.3s; border: none; height: 100%;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.08); }
        .img-box { 
            height: 160px; background: #f8f8f8; 
            display: flex; align-items: center; justify-content: center; color: #ccc; 
            border-radius: 12px;
            overflow: hidden;
        }
        .img-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .stat-icon { font-size: 0.75rem; color: #888; margin-right: 12px; display: inline-flex; align-items: center; }
        .stat-icon i { margin-right: 4px; color: var(--green-sage); }
        
        .btn-ubah { 
            border: 1px solid #ccc; background: transparent; color: #333;
            border-radius: 8px; font-size: 0.85rem; font-weight: 600; padding: 6px 20px; 
            text-decoration: none; transition: 0.2s;
        }
        .btn-ubah:hover { background: #333; color: white; border-color: #333; }
        
        .btn-delete {
            border: 1px solid #dc3545; background: transparent; color:  #dc3545;
            border-radius: 8px; font-size: 0.85rem; font-weight: 600; padding: 6px 15px; 
            transition: 0.2s; cursor: pointer;
        }
        .btn-delete:hover { background: #dc3545; color: white; }
        
        .btn-add-product {
            background: white; border: 2px dashed var(--green-sage); color: var(--green-sage);
            border-radius: 15px; padding: 20px; font-weight: 700; width: 100%; transition: 0.3s;
            text-transform: uppercase; letter-spacing: 1px;
            display: block; text-align: center; text-decoration: none;
        }
        .btn-add-product:hover { background: var(--green-sage); color: white; }
        
        .badge-status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-active { background: #d4edda; color: #155724; }
        .badge-inactive { background: #f8d7da; color: #721c24; }
        .badge-out { background: #fff3cd; color: #856404; }
    </style>
    <link rel="stylesheet" href="{{ asset('css/partner-theme.css') }}">
</head>
<body>

    <div class="header-bg sticky-top shadow-sm">
        <div class="container-fluid px-5">
            <div class="d-flex justify-content-between align-items-center mb-0">
                <a href="{{ route('partner.home') }}" class="text-white text-decoration-none">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-arrow-left fs-4"></i>
                        <h5 class="mb-0 fw-bold">MANAJEMEN PRODUK</h5>
                    </div>
                </a>
                <div class="d-flex align-items-center gap-3">
                    <span class="text-white fw-bold">{{ strtoupper($nama_toko) }}</span>
                    <a href="{{ route('partner.profile.index') }}" class="text-white"><i class="bi bi-person-circle fs-3"></i></a>
                </div>
            </div>
        </div>
        
        <div class="d-flex justify-content-center">
            <ul class="nav nav-tabs nav-tabs-custom">
                <li class="nav-item"><a class="nav-link active" href="{{ route('partner.products') }}">PRODUK</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('partner.orders.index') }}">RIWAYAT PESANAN</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('partner.analysis.index') }}">ANALISIS</a></li>
            </ul>
        </div>
    </div>

    <div class="container-fluid px-5 my-5">
        
        <div class="d-flex gap-3 mb-4">
            <button class="btn bg-white text-success fw-bold px-4 rounded-pill border border-success">
                Semua Produk ({{ count($products) }})
            </button>
            @php
                $outCount = $products->filter(fn($p) => $p->stock <= 0)->count();
            @endphp
            <button class="btn bg-white text-muted fw-bold px-4 rounded-pill border">
                Stok Habis ({{ $outCount }})
            </button>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            </div>
        @endif

        <div class="row g-4">
            @if($products->isEmpty())
                <div class="col-12 text-center py-5">
                    <i class="bi bi-box-seam fs-1 text-muted"></i>
                    <p class="text-muted mt-3">Belum ada produk. Tambahkan produk pertama Anda!</p>
                </div>
            @else
                @foreach($products as $product)
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="product-card p-3">
                        <div class="img-box mb-3">
                            @if(!empty($product->image) && $product->image != 'default_product.jpg')
                                <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" onerror="this.src='{{ asset('assets/default_product.png') }}'">
                            @else
                                <i class="bi bi-image fs-1"></i>
                            @endif
                        </div>
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="fw-bold mb-1">{{ $product->name }}</h6>
                                <p class="text-success fw-bold mb-1">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                <small class="text-muted">Stok: {{ $product->stock }}</small>
                            </div>
                            <span class="badge-status {{ $product->stock > 0 ? 'badge-active' : 'badge-out' }}">
                                {{ $product->stock > 0 ? 'Active' : 'Out of Stock' }}
                            </span>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-end mt-3">
                            <div class="stats d-flex flex-column gap-1">
                                <span class="stat-icon"><i class="bi bi-eye-fill"></i> {{ $product->views_count ?? 0 }}</span>
                                <span class="stat-icon"><i class="bi bi-cart-fill"></i> {{ $product->sold_count ?? 0 }}</span>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('partner.products.edit', $product->id) }}" class="btn-ubah">Ubah</a>
                                <form action="{{ route('partner.products.delete', $product->id) }}" method="POST" onsubmit="return confirm('Yakin mau hapus produk ini?');" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            @endif
        </div>

        <div class="row mt-5">
            <div class="col-12">
                <a href="{{ route('partner.products.create') }}" class="btn-add-product">
                    <i class="bi bi-plus-circle me-2"></i> Tambah Produk Baru
                </a>
            </div>
        </div>
        
    </div>

    <!-- Scripts -->
    <script src="{{ asset('vendor/bootstrap-5.3.0.bundle.min.js') }}"></script>
</body>
</html>
