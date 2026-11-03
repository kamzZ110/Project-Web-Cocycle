@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/explore/cart.css') }}">
    <style>
        .cart-img { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; }
        .qty-box { display: inline-flex; border: 1px solid #ddd; border-radius: 5px; overflow: hidden; }
        .qty-input { width: 40px; text-align: center; border: none; background: transparent; }
        .qty-box button { border: none; background: #f8f9fa; padding: 5px 10px; cursor: pointer; }
        .qty-box button:hover { background: #e9ecef; }
    </style>
@endpush

@section('content')
<div class="container py-5">
    <h2 class="mb-4 fw-bold">Keranjang Belanja</h2>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    @if ($cart_items->count() > 0)
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th>Harga</th>
                                        <th>Qty</th>
                                        <th>Total</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $grand_total = 0; @endphp
                                    @foreach ($cart_items as $item)
                                        @php 
                                            // Always fetch current normalized price from Product model
                                            $price = $item->product ? $item->product->price : $item->product_price;
                                            $total_price = $price * $item->quantity;
                                            $grand_total += $total_price;
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ asset($item->product_image) }}" class="cart-img me-2" onerror="this.src='{{ asset('assets/default_product.png') }}'">
                                                    <div><strong>{{ $item->product_name }}</strong><br><small class="text-muted">Toko: {{ $item->product?->store?->store_name ?? 'Tidak tersedia' }}</small>
                                                        @if(!$item->product?->store || $item->product->store->user?->role !== 'partner')
                                                            <br><small class="text-danger">Item dari toko lama belum terhubung ke partner. Hapus item dan pilih kembali dari marketplace.</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>Rp {{ number_format($price, 0, ',', '.') }}</td>
                                            <td>
                                                <div class="d-flex align-items-center qty-box">
                                                    <button class="update-qty" data-id="{{ $item->id }}" data-type="minus">-</button>
                                                    <input type="text" class="qty-input" value="{{ $item->quantity }}" readonly>
                                                    <button class="update-qty" data-id="{{ $item->id }}" data-type="plus">+</button>
                                                </div>
                                            </td>
                                            <td class="fw-bold">Rp {{ number_format($total_price, 0, ',', '.') }}</td>
                                            <td>
                                                <button class="btn btn-sm text-danger remove-item" data-id="{{ $item->id }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-basket fa-3x text-muted mb-3"></i>
                            <h5>Keranjangmu kosong</h5>
                            <a href="{{ route('marketplace.index') }}" class="btn btn-primary mt-2">Belanja Sekarang</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="fw-bold">Ringkasan</h5>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Total Belanja</span>
                        <span class="fw-bold fs-5">Rp {{ isset($grand_total) ? number_format($grand_total, 0, ',', '.') : '0' }}</span>
                    </div>
                    <button id="checkout-btn" class="btn btn-success w-100 fw-bold py-2" {{ ($cart_items->count() == 0) ? 'disabled' : '' }}>
                        Checkout Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Ensure metadata exists in layout
            }
        });

        // Add meta csrf if not exists (handled in layout usually, but checking)
        if ($('meta[name="csrf-token"]').length === 0) {
            $('head').append('<meta name="csrf-token" content="{{ csrf_token() }}">');
        }

        $('.update-qty').click(function() {
            let id = $(this).data('id');
            let type = $(this).data('type');
            let input = $(this).siblings('input');
            let newQty = type === 'plus' ? parseInt(input.val()) + 1 : parseInt(input.val()) - 1;
            if (newQty < 1) return;

            $.post('{{ route('cart.update') }}', {
                _token: '{{ csrf_token() }}',
                cart_id: id,
                qty: newQty
            }, function(res) {
                if (res.status === 'success') location.reload();
            }, 'json').fail(function(xhr) { alert(xhr.responseJSON?.message || 'Permintaan gagal. Silakan coba lagi.'); });
        });

        $('.remove-item').click(function() {
            if (!confirm("Hapus item ini?")) return;
            $.post('{{ route('cart.remove') }}', {
                _token: '{{ csrf_token() }}',
                cart_id: $(this).data('id')
            }, function(res) {
                if (res.status === 'success') location.reload();
            }, 'json').fail(function(xhr) { alert(xhr.responseJSON?.message || 'Permintaan gagal. Silakan coba lagi.'); });
        });

        $('#checkout-btn').click(function() {
            if (!confirm("Proses pembayaran?")) return;
            $.post('{{ route('cart.checkout') }}', {
                _token: '{{ csrf_token() }}'
            }, function(res) {
                alert(res.message);
                if(res.status === 'success') location.reload();
            }, 'json').fail(function(xhr) { alert(xhr.responseJSON?.message || 'Permintaan gagal. Silakan coba lagi.'); });
        });
    });
</script>
@endpush
