<?php
session_start(); 

// 1. CEK LOGIN (Proteksi Halaman)
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    // Login ada di satu folder (backend), panggil langsung
    header("location: login.php");
    exit;
}

// 2. AMBIL FOTO PROFIL
// Jika user punya foto, pakai itu. Jika tidak, pakai default.
$profilePic = isset($_SESSION["profile_pic"]) && !empty($_SESSION["profile_pic"]) 
              ? $_SESSION["profile_pic"] 
              : "assets/default_profile.png";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cocycle — Home</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="../explore/hp_1.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
</head>

<body>

    <header class="site-header">
        <div class="logo">
            <img src="../assets/logo_cocycle.png" alt="logo" width="60">
        </div>
        <nav id="navMenu">
            <a href="homepage_1.php">Home</a>
            <a href="#about">About Us</a>
            <a href="#community">Community</a>
        </nav>

        <div>
            <button id="profileBtn" class="profile-btn d-flex align-items-center" aria-expanded="false" aria-controls="profileMenu" style="border:none; background:none; padding:0;">
                
                <img src="../<?php echo htmlspecialchars($profilePic); ?>" 
                     alt="Profile" 
                     style="width: 35px; height: 35px; border-radius: 50%; object-fit: cover; border: 2px solid #444;">

                <span class="d-none d-sm-inline" style="margin-left: 10px; font-weight: 600; color: #444;">
                    <?php echo htmlspecialchars($_SESSION["username"]); ?>
                </span>
            </button>

            <div id="profileMenu" class="card profile-menu shadow-sm">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">My Account</li>
                    <li class="list-group-item">Settings</li>
                    <li class="list-group-item text-danger" id="logoutBtn" style="cursor: pointer;">Log Out</li>
                </ul>
            </div>
        </div>
    </header>

    <div id="homeCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="../assets/tanaman.jpg" class="d-block w-100" alt="Slide 1">
            </div>
            <div class="carousel-item">
                <img src="../assets/community.jpg" class="d-block w-100" alt="Slide 2">
            </div>
            <div class="carousel-item">
                <img src="../assets/tanaman2.jpg" class="d-block w-100" alt="Slide 3">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#homeCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#homeCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <main class="py-4">
        <div class="container">
            <h5 class="text-center mb-4" style="font-weight:700; font-size: 30px;">What Do You Want To Do Today..?</h5>

            <div class="row features justify-content-center gap-3 mb-5">
                <div class="col-12 col-sm-6 col-md-4">
                    
                    <div class="card feature-market" role="button" onclick="window.location.href='../explore/katalog.php';">
                        
                        <div class="d-flex align-items-center">
                            <div class="feature-ico">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#444" stroke-width="1.2">
                                    <rect x="3" y="7" width="18" height="11" rx="2" />
                                    <path d="M3 7l2-3h14l2 3" />
                                </svg>
                            </div>
                            <div>
                                <h5 style="margin:0; font-weight: 700;">Marketplace</h5>
                                <div class="muted" style="color: #fff;">Buy & Sell fresh produce</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-md-4">
                    <div class="card feature-donation" role="button" onclick="openDonation()">
                        <div class="d-flex align-items-center">
                            <div class="feature-ico">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#444" stroke-width="1.2">
                                    <path d="M12 21s-6-4-8-7a6 6 0 0 1 11-4 6 6 0 0 1 4-1c0 0-3 5-7 12z" />
                                </svg>
                            </div>
                            <div>
                                <h5 style="margin:0; font-weight: 700;">Donation</h5>
                                <div class="muted" style="color: #fff;">Support food access</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <section class="mb-5">
                <h5 class="text-center mb-3" style="font-weight:700; font-size: 30px;">Promos & News</h5>
                <div class="row g-4">
                    <div class="col-12 col-md-6 col-lg-4" onclick="openNews(1)" style="cursor:pointer;">
                        <div class="news-card">
                            <div style="height:150px;border-radius:.8rem;background:linear-gradient(180deg,var(--yellow-1),var(--gold-1)); overflow:hidden;">
                                <img src="../assets/food.jpg" alt="makanan promo" style="width:100%; height:100%; object-fit:cover;">
                            </div>
                            <h6 class="mt-2" style="font-weight:700">Sarapan Sehat untuk Awal Hari</h6>
                            <p class="muted">Nikmati menu sarapan bergizi dengan sayuran segar dan telur organik.</p>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4" onclick="openNews(2)" style="cursor:pointer;">
                        <div class="news-card">
                            <div style="height:150px;border-radius:.8rem;background:linear-gradient(180deg,var(--cream-2),var(--yellow-1)); overflow:hidden;">
                                <img src="../assets/promo2.png" alt="makanan promo" style="width:100%; height:100%; object-fit:cover;">
                            </div>
                            <h6 class="mt-2" style="font-weight:700">Promo Spesial Pizza Minggu Ini!</h6>
                            <p class="muted">Dapatkan dua pizza lezat hanya dengan harga satu!</p>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 d-none d-lg-block" onclick="openNews(3)" style="cursor:pointer;">
                        <div class="news-card">
                            <div style="height:150px;border-radius:.8rem;background:linear-gradient(180deg,var(--pale),var(--sage)); overflow:hidden;">
                                <img src="../assets/sayurqan.jpg" alt="roti" style="width:100%; height:100%; object-fit:cover;">
                            </div>
                            <h6 class="mt-2" style="font-weight:700">Sayuran Lokal, Segar Setiap Hari</h6>
                            <p class="muted">Dukung petani lokal dengan membeli sayuran langsung dari sumbernya.</p>
                        </div>
                    </div>
                </div>
                <div class="text-end mt-3">
                    <a href="#" class="muted">Show all news</a>
                </div>
            </section>

            <section class="mb-5">
                <h5 class="text-center mb-3" style="font-weight:700; font-size: 30px;">Food Storage Tips</h5>
                <div class="row g-3">
                    <div class="col-12" onclick="openTip(1)" style="cursor:pointer;">
                        <div class="tip-item">
                            <div style="color: var(--gold-1)">
                                <h6 style="margin:0;font-weight:700">Simpan Buah di Tempat Terpisah</h6>
                                <p class="muted mb-0">Pisahkan pisang dan apel dari sayuran agar tahan lebih lama.</p>
                            </div>
                            <div style="width:72px;height:72px;border-radius:8px;background:linear-gradient(180deg,var(--cream-1),var(--cream-2)); overflow:hidden;">
                                <img src="../assets/pisang.jpg" alt="pisang" style="width:100%; height:100%; object-fit:cover;">
                            </div>
                        </div>
                    </div>
                    <div class="col-12" onclick="openTip(2)" style="cursor:pointer;">
                        <div class="tip-item">
                            <div style="color: var(--gold-1)">
                                <h6 style="margin:0;font-weight:700">Gunakan Wadah Kedap Udara</h6>
                                <p class="muted mb-0">Simpan bahan makanan kering dalam wadah kedap udara.</p>
                            </div>
                            <div style="width:72px;height:72px;border-radius:8px;background:linear-gradient(180deg,var(--yellow-1),var(--gold-1)); overflow:hidden;">
                                <img src="../assets/kacang.jpg" alt="kacang" style="width:100%; height:100%; object-fit:cover;">
                            </div>
                        </div>
                    </div>
                    <div class="col-12" onclick="openTip(3)" style="cursor:pointer;">
                        <div class="tip-item">
                            <div style="color: var(--gold-1)">
                                <h6 style="margin:0;font-weight:700">Atur Suhu Kulkas dengan Benar</h6>
                                <p class="muted mb-0">Pastikan suhu kulkas stabil untuk menjaga kualitas makanan.</p>
                            </div>
                            <div style="width:72px;height:72px;border-radius:8px;background:linear-gradient(180deg,var(--pale),var(--sage)); overflow:hidden;">
                                <img src="../assets/ice batu.jpg" alt="es batu" style="width:100%; height:100%; object-fit:cover;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-end mt-3"><a href="#" class="muted">Show all tips</a></div>
            </section>

        </div>
    </main>

    <footer class="site-footer">
        <div class="container">
            <div class="text-center mt-3 muted small" style="color: rgb(96, 87, 87);">© Cocycle 2025.</div>
        </div>
    </footer>

    <div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="detailTitle" class="modal-title">Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detailBody"></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script src="../log-sign/main.js"></script>

</body>
</html>

