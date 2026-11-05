<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan - CoCycle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome for fallback icons if needed -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
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
        
        .header-bg { 
            background: linear-gradient(135deg, #6B8E23 0%, #9ACD32 50%, #20B2AA 100%);
            border-bottom: none;
            padding-top: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
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
        
        .order-card {
            background: white; border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
            transition: 0.2s; border-left: 5px solid var(--green-sage);
        }
        .order-card:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.08); }
        
        .btn-detail {
            border: 1px solid var(--green-sage); color: var(--green-sage);
            border-radius: 8px; font-size: 0.9rem; font-weight: 600;
            padding: 8px 20px; text-decoration: none; transition: 0.2s;
        }
        .btn-detail:hover { background: var(--green-sage); color: white; }

        @media (max-width: 576px) {
            .header-bg .container-fluid { padding-left: 1rem !important; padding-right: 1rem !important; }
            .header-bg .container-fluid > .d-flex { gap: .5rem; }
            .header-bg .container-fluid > .d-flex > a > .d-flex { gap: .5rem !important; }
            .header-bg .container-fluid > .d-flex > .d-flex { min-width: 0; gap: .5rem !important; }
            .header-bg .container-fluid > .d-flex > .d-flex span { max-width: 10rem; overflow-wrap: anywhere; text-align: right; }
            .container-fluid.px-5.my-5 { padding-left: 1rem !important; padding-right: 1rem !important; }
            .order-card > .d-flex { align-items: stretch !important; flex-wrap: wrap; gap: 1rem; }
            .order-card > .d-flex > div:first-child { min-width: 0; flex: 1 1 100%; }
            .order-card > .d-flex > div:last-child { min-width: 0; flex: 1 1 100%; text-align: left !important; }
            .order-card .d-flex.gap-2 { flex-wrap: wrap; justify-content: flex-start !important; }
            .order-card h6 { overflow-wrap: anywhere; }
        }
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
                        <h5 class="mb-0 fw-bold">RIWAYAT PESANAN</h5>
                    </div>
                </a>
                <div class="d-flex align-items-center gap-3">
                    <span class="text-white fw-bold">{{ strtoupper($store->store_name) }}</span>
                    <a href="{{ route('partner.profile.index') }}" class="text-white"><i class="bi bi-person-circle fs-3"></i></a>
                </div>
            </div>
        </div>
        
        <div class="d-flex justify-content-center">
            <ul class="nav nav-tabs nav-tabs-custom">
                <li class="nav-item"><a class="nav-link" href="{{ route('partner.products') }}">PRODUK</a></li>
                <li class="nav-item"><a class="nav-link active" href="{{ route('partner.orders.index') }}">RIWAYAT PESANAN</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('partner.analysis.index') }}">ANALISIS</a></li>
            </ul>
        </div>
    </div>

    <div class="container-fluid px-5 my-5">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Pesanan Masuk</h4>
            <span class="badge bg-light text-dark fs-6 border">{{ count($orders) }} Orders</span>
        </div>

        <div class="row g-4">
            @forelse($orders as $order)
            <div class="col-12">
                <div class="order-card p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <h6 class="fw-bold mb-0">#{{ $order->invoice_number }}</h6>
                                @php
                                    $statusClass = match($order->status) {
                                        'Completed' => 'bg-success',
                                        'Preparing' => 'bg-warning',
                                        'Ready' => 'bg-primary',
                                        'Cancelled' => 'bg-danger',
                                        default => 'bg-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }}">
                                    {{ $order->status }}
                                </span>
                            </div>
                            <div class="text-muted small mb-1">
                                <i class="bi bi-person me-1"></i> {{ $order->user?->fullname ?: ($order->user?->username ?? 'Guest') }}
                            </div>
                            <div class="text-muted small">
                                <i class="bi bi-clock me-1"></i> {{ $order->created_at->format('d M Y, H:i') }}
                            </div>
                        </div>
                        
                        <div class="text-end">
                            <h5 class="fw-bold text-success mb-1">Rp {{ number_format($order->total_price, 0, ',', '.') }}</h5>
                            <p class="small text-muted mb-3">{{ $order->items->count() }} Items</p>
                            <div class="d-flex gap-2 justify-content-end">
                                <button class="btn btn-sm btn-outline-danger" onclick="updateStatus({{ $order->id }}, 'Cancelled')">Cancel</button>
                                @if($order->status == 'Preparing')
                                    <button class="btn btn-sm btn-success" onclick="updateStatus({{ $order->id }}, 'Ready')">Set Ready</button>
                                @elseif($order->status == 'Ready')
                                    <button class="btn btn-sm btn-success" onclick="updateStatus({{ $order->id }}, 'Completed')">Complete</button>
                                @endif
                                <a href="{{ route('partner.orders.show', $order->id) }}" class="btn-detail">Detail</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-clipboard-x fs-1 text-muted"></i>
                <p class="text-muted mt-3">Belum ada pesanan masuk.</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('vendor/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap-5.3.0.bundle.min.js') }}"></script>
    <script>
        function updateStatus(orderId, newStatus) {
            if (!confirm('Ubah status pesanan menjadi ' + newStatus + '?')) return;
            
            $.post('{{ route('partner.orders.updateStatus') }}', {
                _token: '{{ csrf_token() }}',
                order_id: orderId,
                status: newStatus
            }, function(data) {
                if (data.status === 'success') {
                    alert('Status berhasil diupdate!');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            }).fail(function(xhr) {
                alert(xhr.responseJSON?.message || 'Perubahan status gagal.');
            });
        }
    </script>
</body>
</html>
