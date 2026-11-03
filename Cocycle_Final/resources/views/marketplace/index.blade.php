@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/katalog.css') }}">
    <!-- Override container width if needed -->
    <style>
        .product-card img {
            height: 200px;
            width: 100%;
            object-fit: cover;
        }
        .list-group-item.active-cat {
            background-color: #FCDD9D !important;
            font-weight: bold;
            border-left: 5px solid #D4AF37;
        }
        /* Navbar overlap fix if needed */
        body { padding-top: 0px; }
    </style>
@endpush

@section('content')
    <div class="container mt-4">
        <div class="row">
            <!-- SIDEBAR -->
            <aside class="col-lg-3 mb-4">
                <div class="sidebar-widget mb-4">
                    <div class="widget-header" data-bs-toggle="collapse" data-bs-target="#categoryList">
                        <h5 class="widget-title">Category</h5><i class="fas fa-chevron-up"></i>
                    </div>
                    <div id="categoryList" class="collapse show">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item {{ $current_category == 'All' ? 'active-cat' : '' }}">
                                <a href="{{ route('marketplace.index') }}">All Products</a>
                            </li>
                            @foreach(['Bakery & Pastry', 'Vegetables & Fruits', 'Drinks', 'Snacks'] as $cat)
                            <li class="list-group-item {{ $current_category == $cat ? 'active-cat' : '' }}">
                                <a href="{{ route('marketplace.index', ['category' => $cat]) }}">{{ $cat }}</a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                
                <!-- TOP RATED RESTO (Static for now / could be dynamic) -->
                <div class="sidebar-widget mb-4">
                    <div class="widget-header">
                        <h5 class="widget-title">Top Rated Resto</h5>
                    </div>
                    <div class="list-group list-group-flush top-rated-products">
                        <a href="{{ route('marketplace.index', ['resto' => 'Happy Bread Shop']) }}" class="list-group-item list-group-item-action d-flex gap-3 py-3">
                            <img src="{{ asset('assets/resto.jpg') }}" width="48" height="48" class="rounded-circle flex-shrink-0">
                            <div>
                                <h6 class="mb-0 small">Happy Bread Shop</h6><small class="text-muted">⭐ 4.9</small>
                            </div>
                        </a>
                        <!-- Add more mock restaurants if needed -->
                    </div>
                </div>
            </aside>

            <!-- MAIN CONTENT -->
            <main class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h2 class="fw-bold">{{ $filter_title }}</h2>
                        <p class="text-muted">Start Your Daily Shopping with CoCycle</p>
                    </div>
                    <!-- Legacy Search Form logic was in Header, here we might rely on global search or add one -->
                </div>

                <div class="row g-4">
                    @forelse($products as $offers)
                        @php $product = $offers->first(); @endphp
                        <div class="col-12 col-md-6 col-lg-4 d-flex align-items-stretch">
                            <div class="card h-100 product-card view-btn w-100 shadow-sm"
                                style="cursor: pointer;"
                                data-product-id="{{ $product->id }}"
                                data-name="{{ $product->name }}"
                                data-category="{{ $product->category }}"
                                data-price="Rp {{ number_format($product->price, 0, ',', '.') }}"
                                data-rating="{{ $product->rating ?? '0' }}/5"
                                data-distance="0.5 km"
                                data-restaurant="{{ $product->store->store_name ?? 'CoCycle Store' }}"
                                data-description="{{ $product->description }}">

                                <img class="card-img-top" src="{{ asset($product->image ?: 'assets/default_product.png') }}" alt="{{ $product->name }}">

                                <div class="card-body d-flex flex-column">
                                    <h6 class="card-title fw-bold">{{ $product->name }}</h6>
                                    <p class="card-text text-muted small mb-1">
                                        @if($offers->count() > 1)
                                            <label for="store-choice-{{ $product->id }}">Pilih toko:</label>
                                            <select id="store-choice-{{ $product->id }}" class="form-select form-select-sm store-choice" aria-label="Pilih toko untuk {{ $product->name }}" onclick="event.stopPropagation()">
                                                <option value="">Pilih toko</option>
                                                @foreach($offers as $offer)
                                                    <option value="{{ $offer->id }}">{{ $offer->store->store_name ?? 'CoCycle Store' }}</option>
                                                @endforeach
                                            </select>
                                        @else
                                            <i class="fas fa-store me-1"></i>{{ $product->store->store_name ?? 'CoCycle Store' }}
                                        @endif
                                    </p>

                                    <div class="mt-auto d-flex justify-content-between align-items-center pt-3">
                                        <span class="fw-bold text-success fs-5">Rp {{ number_format($product->price, 0, ',', '.') }}</span>

                                        <button type="button" class="btn btn-success btn-sm rounded-circle add-cart-direct"
                                            style="width: 32px; height: 32px; padding: 0;"
                                            data-product-id="{{ $product->id }}"
                                data-name="{{ $product->name }}"
                                            data-price="{{ $product->price }}"
                                            data-image="{{ asset($product->image ?: 'assets/default_product.png') }}"
                                            onclick="event.stopPropagation(); addSelectedProduct(this.closest('.product-card'));">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <h4 class="text-muted">Produk tidak ditemukan :(</h4>
                            <a href="{{ route('marketplace.index') }}" class="btn btn-outline-primary mt-2">Reset Pencarian</a>
                        </div>
                    @endforelse
                </div>
            </main>
        </div>
    </div>

    <!-- POPUP VIEW (Replicated from legacy) -->
    <div class="popup-view">
        <div class="popup-card">
            <a href="#" class="close-btn">&times;</a>
            <div class="product-img"><img src="" alt="Product Image"></div>
            <div class="info">
                <h3>Product Name</h3>
                <p class="category text-muted"></p>
                <p class="restaurant"></p>
                <p class="rating"></p>
                <p class="distance"></p>
                <p class="popup-description"></p>
                <span class="price fw-bold"></span><br>
                <a href="#" class="add-cart-btn">Add to Cart</a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Pass PHP variables to JS if needed, or rely on data attributes -->
    <script>
        // Custom script to handle Popup Logic (Replicating legacy script)
        document.addEventListener('DOMContentLoaded', () => {
             // Add CSRF to AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const popupView = document.querySelector('.popup-view');
            const popupCard = document.querySelector('.popup-card');
            const closeBtn = document.querySelector('.close-btn');

            document.querySelectorAll('.product-card').forEach(card => {
                card.addEventListener('click', function() {
                    const d = this.dataset;
                    const choice = this.querySelector('.store-choice');
                    if (choice && !choice.value) { alert('Pilih toko terlebih dahulu.'); choice.focus(); return; }
                    const imgSrc = this.querySelector('img').src;

                    popupView.querySelector('img').src = imgSrc;
                    popupView.querySelector('h3').innerText = d.name;
                    popupView.querySelector('.category').innerText = d.category;
                    popupView.querySelector('.restaurant').innerText = choice ? choice.options[choice.selectedIndex].text : d.restaurant;
                    popupView.querySelector('.rating').innerText = 'Rate: ' + d.rating;
                    popupView.querySelector('.distance').innerText = 'Distance: ' + d.distance;
                    popupView.querySelector('.popup-description').innerText = d.description;
                    popupView.querySelector('.price').innerText = d.price;

                    // Update Add to Cart Button in Popup
                    const addBtn = popupView.querySelector('.add-cart-btn');
                    addBtn.onclick = function(e) {
                         e.preventDefault();
                         addToCartLaravel(choice ? choice.value : d.productId);
                    };

                    popupView.classList.add('active');
                });
            });

            closeBtn.addEventListener('click', (e) => {
                e.preventDefault();
                popupView.classList.remove('active');
            });
        });

        function addSelectedProduct(card) {
            const choice = card.querySelector('.store-choice');
            if (choice && !choice.value) { alert('Pilih toko terlebih dahulu.'); choice.focus(); return; }
            addToCartLaravel(choice ? choice.value : card.dataset.productId);
        }

        function addToCartLaravel(productId) {
            $.post('{{ route('cart.add') }}', {
                _token: '{{ csrf_token() }}',
                product_id: productId
            }, function(response) {
                if (response.status === 'success') {
                    alert("✅ " + response.message);
                } else {
                    alert("❌ " + response.message);
                }
            }).fail(function(xhr) {
                 if (xhr.status === 401) {
                    alert("⚠️ Please login first!");
                    window.location.href = '{{ route('login') }}';
                } else {
                    alert(xhr.responseJSON?.message || 'Tidak dapat menambahkan produk.');
                }
            });
        }
    </script>
@endpush
