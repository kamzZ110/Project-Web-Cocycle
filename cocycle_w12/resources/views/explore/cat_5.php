<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoCycle - Explore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="hp_1.js"></script>
    <link rel="stylesheet" href="katalog.css">
</head>

<body>
    <header>
        <a href="homepage_1.php" class="logo" style="padding-left: 3%;">
            <img src="../assets/logo_cocycle.png" alt="logo" width="60">
        </a>

        <nav id="navMenu">
            <div class="search-container">
                <input type="text" class="search-input" placeholder="Ketik untuk mencari...">
                <button class="search-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-search" viewBox="0 0 16 16">
                        <path
                            d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                    </svg> </button>
            </div>

            <a href="#cart" class="active">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-basket2-fill" viewBox="0 0 16 16">
                    <path
                        d="M5.929 1.757a.5.5 0 1 0-.858-.514L2.217 6H.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h.623l1.844 6.456A.75.75 0 0 0 3.69 15h8.622a.75.75 0 0 0 .722-.544L14.877 8h.623a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1.717L10.93 1.243a.5.5 0 1 0-.858.514L12.617 6H3.383zM4 10a1 1 0 0 1 2 0v2a1 1 0 1 1-2 0zm3 0a1 1 0 0 1 2 0v2a1 1 0 1 1-2 0zm4-1a1 1 0 0 1 1 1v2a1 1 0 1 1-2 0v-2a1 1 0 0 1 1-1" />
                </svg>
            </a>

            <a href="homepage_1.php">Home</a>
            <a href="#about">About Us</a>
            <a href="#community">Community</a>
        </nav>
    </header>

    <div class="container mt-4">
        <div class="row">

            <aside class="col-lg-3">
                <div class="sidebar-widget mb-4">
                    <div class="widget-header" data-bs-toggle="collapse" data-bs-target="#categoryList"
                        aria-expanded="true" style="cursor: pointer;">
                        <h5 class="widget-title">Category</h5>
                        <i class="fas fa-chevron-up"></i>
                    </div>
                    <div id="categoryList" class="collapse show">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><a href="katalog.html">All</a></li>
                            <li class="list-group-item"><a href="cat_1.html">Bread and Pastry</a></li>
                            <li class="list-group-item"><a href="cat_2.html">Vegetables & Fruits</a></li>
                            <li class="list-group-item"><a href="cat_3.html">Meats, Frozen & Seafood</a></li>
                            <li class="list-group-item"><a href="cat_4.html">Drinks</a></li>
                            <li class="list-group-item active-cat"><a href="cat_5.html">Snacks</a></li>
                        </ul>
                    </div>
                </div>

                <div class="sidebar-widget mb-4">
                    <div class="widget-header">
                        <h5 class="widget-title">Top Rated Resto</h5>
                    </div>
                    <div class="list-group list-group-flush top-rated-products">
                        <a href="#" class="list-group-item list-group-item-action d-flex gap-3 py-3">
                            <img src="../assets/resto.jpg" alt="Eggs" width="48" height="48"
                                class="rounded-circle flex-shrink-0">
                            <div class="d-flex gap-2 w-100 justify-content-between">
                                <div>
                                    <h6 class="mb-0 small">ABC</h6>
                                    <i class="fas fa-star text-warning me-1"></i>
                                    <small class="text-muted">(4.9/5)</small>
                                </div>
                            </div>
                        </a>

                        <a href="#" class="list-group-item list-group-item-action d-flex gap-3 py-3">
                            <img src="../assets/resto2.jpg" alt="Cinnamon" width="48" height="48"
                                class="rounded-circle flex-shrink-0">
                            <div class="d-flex gap-2 w-100 justify-content-between">
                                <div>
                                    <h6 class="mb-0 small">XYZ</h6>
                                    <i class="fas fa-star text-warning me-1"></i>
                                    <small class="text-muted">(4.8/5)</small>
                                </div>
                            </div>
                        </a>

                        <a href="#" class="list-group-item list-group-item-action d-flex gap-3 py-3">
                            <img src="../assets/resto2.jpg" alt="Apricots" width="48" height="48"
                                class="rounded-circle flex-shrink-0">
                            <div class="d-flex gap-2 w-100 justify-content-between">
                                <div>
                                    <h6 class="mb-0 small">DCE</h6>
                                    <i class="fas fa-star text-warning me-1"></i>
                                    <small class="text-muted">(4.7/5)</small>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </aside>

            <main class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h2 class="fw-bold">View our all products</h2>
                        <p class="text-muted">Start Your Daily Shopping with CoCycle</p>
                    </div>
                </div>

                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    <div class="col">
                        <div class="card h-100 product-card">
                            <img class="img" src="../assets/granola.jpg" alt="Granola">
                            <div class="card-body">
                                <h6 class="card-title">Granola Bar</h6>
                                <p class="card-text text-muted">Snacks</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <p class="fw-bold fs-5 mb-0">$24.00</p>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-star text-warning me-1"></i>
                                        <small class="text-muted">(4.9/5)</small>
                                        <button class="btn btn-success btn-circle ms-2">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>