@extends('layouts.donation')

@push('styles')
    <!-- Inline styles from donation/index.php and style.css -->
    <style>
         :root {
            --brand-primary: #5CB338;
            --brand-secondary: #FFC145;
        }
        .category-item { transition: transform 0.2s; }
        .category-item:hover { transform: translateY(-5px); }
        .form-control-lg { border-radius: 50px; font-size: 1rem; }
    </style>
@endpush

@section('content')
<div class="container mt-4">
    <div class="text-center mb-5">
        <h1 class="fw-bold mb-3">Apa yang ingin kamu <span style="color:var(--brand-primary);">donasikan</span>?</h1>
        <div class="row justify-content-center">
            <div class="col-md-6 position-relative">
                <form action="{{ route('donation.index') }}" method="GET">
                    <input type="text" name="search" class="form-control form-control-lg ps-5" placeholder="Cari jenis makanan sisa..." value="{{ $search_keyword }}">
                    <button type="submit" style="background:none;border:none;position:absolute;top:50%;left:30px;transform:translateY(-50%);color:#6c757d;padding:0;cursor:pointer;">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center gap-4 mb-5 text-center flex-wrap">
        @foreach(['Makanan'=>'utensils', 'Sayur'=>'carrot', 'Buah'=>'apple-whole'] as $name=>$icon)
        <a href="{{ route('donation.index', ['category' => $name]) }}" class="text-decoration-none text-dark">
            <div class="category-item" style="cursor: pointer;">
                <div class="bg-white rounded-circle shadow-sm d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 70px; height: 70px; color: var(--brand-primary);">
                    <i class="fas fa-{{ $icon }} fa-xl"></i>
                </div>
                <span class="fw-bold small">{{ $name }}</span>
            </div>
        </a>
        @endforeach
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">Makanan Donasi</h4>
    </div>

    <div class="row g-4">
        @forelse($donation_items as $p)
        <div class="col-md-3 col-6">
            <div class="card h-100">
                <div class="position-relative">
                    <img src="{{ asset($p->image) }}" class="card-img-top" style="height: 180px; object-fit: cover; border-radius: 16px 16px 0 0;" alt="{{ $p->name }}">
                    <span class="badge bg-light text-dark position-absolute top-0 start-0 m-2 shadow-sm"><i class="fas fa-star text-warning"></i> {{ $p->rating ?? '5.0' }}</span>
                </div>
                <div class="card-body">
                    <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">{{ $p->category }}</small>
                    <h5 class="card-title fw-bold mt-1 mb-1">{{ $p->name }}</h5>
                    <p class="text-muted small mb-3"><i class="fas fa-store me-1"></i> {{ $p->store_name }}</p>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-dark">Rp {{ number_format($p->price) }}</span>
                        <form method="POST" action="{{ route('donation.add') }}">
                            @csrf
                            <input type="hidden" name="id" value="{{ $p->id }}">
                            <input type="hidden" name="name" value="{{ $p->name }}">
                            <input type="hidden" name="price" value="{{ $p->price }}">
                            <input type="hidden" name="img" value="{{ asset($p->image) }}">
                            <button type="submit" class="btn btn-dark rounded-circle btn-sm" style="width:32px;height:32px;"><i class="fas fa-plus"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
            <div class="col-12 text-center">
                <p>Tidak ada item yang ditemukan kriteria ini.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
