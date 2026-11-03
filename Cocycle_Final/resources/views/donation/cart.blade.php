@extends('layouts.donation')

@push('styles')
<style>
    .cart-img { width: 80px; height: 80px; object-fit: cover; border-radius: 10px; }
</style>
@endpush

@section('content')
<div class="container mt-4">
    <form action="{{ route('donation.checkout') }}" method="POST">
    @csrf
    
    <div class="row">
        <div class="col-lg-8 mb-4">
            <h3 class="fw-bold mb-4">Pencatatan Donasi</h3>
            
            <div class="card p-4 mb-4 shadow-sm border-0">
                <h5 class="mb-3 text-success fw-bold"><i class="fas fa-users me-2"></i>Pilih Komunitas Penerima</h5>
                <div class="row g-3 community-selector">
                    <div class="col-md-6">
                        <input type="radio" name="community" id="comm1" value="Greenpeace Indonesia" checked class="d-none">
                        <label for="comm1" class="w-100 cursor-pointer">
                            <div class="card p-3 h-100 border-success community-card selected">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded p-2 me-3"><i class="fas fa-tree text-success fa-2x"></i></div>
                                    <div>
                                        <h6 class="fw-bold mb-0">Greenpeace Indonesia</h6>
                                        <small class="text-muted">Lingkungan & Hutan</small>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                    <div class="col-md-6">
                        <input type="radio" name="community" id="comm2" value="Bank Sampah Induk" class="d-none">
                        <label for="comm2" class="w-100 cursor-pointer">
                            <div class="card p-3 h-100 community-card">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded p-2 me-3"><i class="fas fa-recycle text-warning fa-2x"></i></div>
                                    <div>
                                        <h6 class="fw-bold mb-0">Bank Sampah Induk</h6>
                                        <small class="text-muted">Daur Ulang</small>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="card p-4 shadow-sm border-0">
                <h5 class="mb-3 fw-bold">Detail Item</h5>
                @if(session('donation_cart') && count(session('donation_cart')) > 0)
                    @php $total = 0; @endphp
                    @foreach(session('donation_cart') as $k => $item)
                    @php $total += $item['price'] * $item['qty']; @endphp
                    <div class="d-flex align-items-center border-bottom py-3 item-row" data-price="{{ $item['price'] }}" id="row-{{ $k }}">
                        <img src="{{ $item['image'] }}" class="rounded" width="70" height="70" style="object-fit: cover;">
                        <div class="ms-3 flex-grow-1">
                            <h6 class="fw-bold mb-0">{{ $item['name'] }}</h6>
                            <input type="number" name="qty[{{ $k }}]" value="{{ $item['qty'] }}" min="0" class="form-control form-control-sm d-inline-block mt-1 qty-input" style="width: 70px;">
                            <small class="text-danger d-none removed-badge">Akan dihapus</small>
                        </div>
                        <span class="fw-bold row-total">Rp {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                @else
                    <div class="alert alert-warning">Keranjang masih kosong.</div>
                @endif
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card p-4 shadow-sm border-0" style="position: sticky; top: 100px;">
                <h5 class="fw-bold mb-3">Ringkasan</h5>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Total Harga</span>
                    <span id="total-display">Rp {{ isset($total) ? number_format($total, 0, ',', '.') : 0 }}</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Biaya Aplikasi</span>
                    <span>Rp 1.000</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-4">
                    <span class="fw-bold fs-5">Sub Total</span>
                    <span class="fw-bold fs-5" id="subtotal-display">Rp {{ isset($total) ? number_format($total + 1000, 0, ',', '.') : 0 }}</span>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold small">Metode Pembayaran</label>
                    <select name="payment_method" class="form-select rounded-pill">
                        <option value="E-Wallet">Dompet Digital (Gopay/Ovo)</option>
                        <option value="Transfer Bank">Transfer Bank</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2" {{ (!session('donation_cart')) ? 'disabled' : '' }}>Donasi Sekarang</button>
                <button type="submit" formaction="{{ route('donation.update') }}" class="btn btn-link w-100 mt-2">Perbarui Keranjang</button>
            </div>
        </div>
    </div>
    </form>
</div>
@endsection

@push('scripts')
<style>
    .community-card { cursor: pointer; border: 1px solid #dee2e6; transition: 0.2s; }
    .community-card:hover { border-color: #5CB338; }
    input[type="radio"]:checked + label .community-card { border: 2px solid #5CB338; background-color: #f0fff4; }
</style>
<script>
    document.querySelectorAll('.qty-input').forEach(input => {
        input.addEventListener('change', updateTotal);
        input.addEventListener('input', updateTotal);
    });

    function updateTotal() {
        let total = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const price = parseFloat(row.dataset.price);
            const input = row.querySelector('.qty-input');
            const qty = parseInt(input.value) || 0;
            const badge = row.querySelector('.removed-badge');
            const name = row.querySelector('h6');
            
            if (qty <= 0) {
                badge.classList.remove('d-none');
                name.style.textDecoration = 'line-through';
                name.style.color = '#999';
                row.querySelector('.row-total').innerText = 'Rp 0';
            } else {
                badge.classList.add('d-none');
                name.style.textDecoration = 'none';
                name.style.color = '';
                row.querySelector('.row-total').innerText = 'Rp ' + (price * qty).toLocaleString('id-ID');
                total += price * qty;
            }
        });

        document.getElementById('total-display').innerText = 'Rp ' + total.toLocaleString('id-ID');
        // Add fee 1000 only if total > 0
        const finalTotal = total > 0 ? total + 1000 : 0;
        document.getElementById('subtotal-display').innerText = 'Rp ' + finalTotal.toLocaleString('id-ID');
        
        const btn = document.querySelector('button[type="submit"]');
        if(total <= 0) btn.disabled = true;
        else btn.disabled = false;
    }
</script>
@endpush
