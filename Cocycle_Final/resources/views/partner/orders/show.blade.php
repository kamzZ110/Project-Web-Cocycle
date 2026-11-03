<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan - CoCycle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* Styling from detail_order.php */
        :root { --green-sage: #8A9A5B; --gold: #F6C55A; --bg-light: #f8f9f4; }
        body { background-color: var(--bg-light); font-family: 'Poppins', sans-serif; }
        
        .top-bar { 
            background: #fff; 
            padding: 15px 30px; 
            border-bottom: 2px solid var(--gold); 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            position: sticky; 
            top: 0; 
            z-index: 1000; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .main-card { background: #fff; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); overflow: hidden; margin-top: 20px; }
        .map-container { height: 250px; position: relative; width: 100%; border-bottom: 1px solid #eee; }
        .map-container iframe { width: 100%; height: 100%; border: 0; }
        .cust-info-overlay { padding: 15px; border-bottom: 1px solid #eee; }
        .product-list { max-height: 300px; overflow-y: auto; padding-right: 5px; }
        .product-mini-item { display: flex; align-items: center; padding: 10px; border-radius: 10px; background: #fafafa; margin-bottom: 8px; }
        .img-placeholder { width: 45px; height: 45px; background: #eee; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 12px; overflow: hidden;}
        .img-placeholder img { width: 100%; height: 100%; object-fit: cover; }
        .summary-box { background: #fdfdfd; border-left: 1px solid #eee; padding: 20px; height: 100%; }
        .price-row { display: flex; justify-content: space-between; margin-bottom: 5px; }
        .total-row { margin-top: 15px; padding-top: 15px; border-top: 2px dashed #eee; display: flex; justify-content: space-between; align-items: center; }
        .btn-sage { background-color: var(--green-sage); color: white; border: none; font-weight: 600; padding: 12px; border-radius: 10px; width: 100%; transition: 0.2s; }
        .btn-sage:hover { background-color: #7a8a4f; color: white; transform: translateY(-1px); }
        .btn-outline-custom { border: 1px solid #ddd; border-radius: 10px; padding: 8px; font-size: 0.85rem; display: flex; align-items: center; justify-content: center; color: #555; text-decoration: none; margin-bottom: 8px; }
        .btn-outline-custom:hover { background: #f5f5f5; color: var(--green-sage); }
    </style>
</head>
<body>

    <div class="top-bar">
        <div class="d-flex align-items-center">
            <a href="{{ route('partner.orders.index') }}" class="text-dark me-3"><i class="fas fa-arrow-left fs-4"></i></a>
            <span class="fw-bold text-success fs-5">{{ $store->store_name }}</span>
        </div>
        <div class="fw-bold small text-muted">Invoice #{{ $order->invoice_number }}</div>
    </div>

    <div class="container-fluid px-3 px-lg-5 mb-5">
        <div class="main-card mt-4">
            <div class="row g-0">
                <div class="col-lg-7">
                    <!-- Map -->
                    @php
                        $address = $order->user->address ?? 'Universitas Multimedia Nusantara, Gading Serpong, Tangerang';
                        $encoded_address = urlencode($address);
                        $link_maps = "https://maps.google.com/?q=" . $encoded_address;
                    @endphp
                    <div class="map-container">
                        <iframe src="https://maps.google.com/maps?q={{ $encoded_address }}&t=&z=15&ie=UTF8&iwloc=&output=embed"></iframe>
                    </div>
                    
                    <div class="cust-info-overlay">
                        <div class="row align-items-center">
                            <div class="col-md-7">
                                <h6 class="fw-bold mb-1">{{ $order->user?->fullname ?: ($order->user?->username ?? 'Guest') }} <span class="text-muted fw-normal small">| {{ $order->user->phone ?? '-' }}</span></h6>
                                <p class="small text-muted mb-2"><i class="fas fa-map-marker-alt text-danger me-2"></i> {{ $address }}</p>
                            </div>
                            <div class="col-md-5">
                                <a href="{{ $link_maps }}" target="_blank" class="btn-outline-custom">
                                    <i class="fas fa-location-arrow me-2 text-primary"></i> Rute ke Navigasi
                                </a>
                                <a href="#" class="btn-outline-custom">
                                    <i class="fas fa-comment-dots me-2 text-success"></i> Hubungi Pelanggan
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="p-3">
                        <h6 class="fw-bold mb-3" style="color: var(--green-sage);">Detail Pesanan ({{ $order->items->count() }} Item)</h6>
                        <div class="product-list">
                            @foreach($order->items as $item)
                            <div class="product-mini-item">
                                <div class="img-placeholder">
                                    <img src="{{ asset($item->product->image ?? 'assets/default_product.png') }}" onerror="this.src='{{ asset('assets/default_product.png') }}'">
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-bold">{{ $item->product->name ?? 'Deleted Product' }}</span>
                                        <span class="fw-bold">x{{ $item->quantity }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-success small">Rp {{ number_format($item->price_per_item, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="summary-box">
                        <h6 class="fw-bold mb-3" style="color: var(--green-sage);">Ringkasan Pembayaran</h6>
                        <div class="price-row">
                            <span>Status Pesanan</span>
                            @php
                                $statusClass = match($order->status) {
                                    'Completed' => 'bg-success',
                                    'Preparing' => 'bg-warning',
                                    'Ready' => 'bg-primary',
                                    'Cancelled' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $statusClass }}">{{ $order->status }}</span>
                        </div>
                        <br>
                        
                        @php
                            $app_fee = $order->total_price * 0.1;
                            $net_income = $order->total_price - $app_fee;
                        @endphp

                        <div class="price-row">
                            <span>Total Harga</span>
                            <span>Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                        </div>
                        <div class="price-row text-danger">
                            <span>Potongan Aplikasi (10%)</span>
                            <span>- Rp {{ number_format($app_fee, 0, ',', '.') }}</span>
                        </div>
                        <div class="price-row mt-2">
                            <span>Metode Pembayaran</span>
                            <span class="badge bg-success">Tunai</span>
                        </div>

                        <div class="total-row">
                            <span class="fw-bold fs-6">Total Diterima</span>
                            <span class="fw-bold fs-4" style="color: var(--green-sage);">Rp {{ number_format($net_income, 0, ',', '.') }}</span>
                        </div>

                        <div class="mt-4">
                            @if ($order->status === 'Ready')
                                <button onclick="updateStatus({{ $order->id }}, 'Completed')" class="btn-sage shadow-sm mb-3">PESANAN SELESAI</button>
                                <button onclick="updateStatus({{ $order->id }}, 'Cancelled')" class="btn btn-outline-danger w-100 shadow-sm mb-3">Batalkan Pesanan</button>
                                <div class="alert alert-light border-0 small text-muted text-center py-2">
                                    <i class="fas fa-info-circle me-1"></i> Pastikan pelanggan sudah menerima pesanan sebelum klik selesai.
                                </div>
                            @elseif ($order->status === 'Preparing')
                                <button onclick="updateStatus({{ $order->id }}, 'Ready')" class="btn btn-warning w-100 shadow-sm mb-3 text-white fw-bold">SET READY</button>
                                <button onclick="updateStatus({{ $order->id }}, 'Cancelled')" class="btn btn-outline-danger w-100 shadow-sm mb-3">Batalkan Pesanan</button>
                            @elseif ($order->status === 'Completed' || $order->status === 'Cancelled')
                                <div class="alert alert-success text-center"><i class="fas fa-check-circle"></i> Pesanan ini sudah {{ strtolower($order->status) }}.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('vendor/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap-5.3.0.bundle.min.js') }}"></script>
    <script>
        function updateStatus(orderId, newStatus) {
            let msg = (newStatus === 'Completed') 
                ? "Apakah pesanan sudah diterima pelanggan?"
                : 'Ubah status pesanan menjadi ' + newStatus + '?';

            if (!confirm(msg)) return;
            
            $.post('{{ route('partner.orders.updateStatus') }}', {
                _token: '{{ csrf_token() }}',
                order_id: orderId,
                status: newStatus
            }, function(data) {
                if (data.status === 'success') {
                    alert('Success!');
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
