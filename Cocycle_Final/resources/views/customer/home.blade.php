<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cocycle — Home</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('explore/hp_1.css') }}">
    
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>

    @if(session('role_notice'))
        <div class="alert alert-warning m-3" role="alert">{{ session('role_notice') }}
            <form action="{{ route('logout') }}" method="POST" class="d-inline ms-2">@csrf<button class="btn btn-sm btn-outline-dark" type="submit">Keluar untuk ganti akun</button></form>
        </div>
    @endif

    <header class="site-header">
        <div class="logo">
            <img src="{{ asset('assets/logo_cocycle.png') }}" alt="logo" width="60">
        </div>
        <nav id="navMenu">
            <a href="#home">Home</a>
            <a href="#promo">Promos</a>
            <a href="#tips">Tips</a>
        </nav>

        <div>
            <button id="profileBtn" class="profile-btn d-flex align-items-center" aria-expanded="false" aria-controls="profileMenu" style="border:none; background:none; padding:0;">
                
                <img src="{{ asset(Auth::user()->profile_pic ?: 'assets/default_profile.png') }}" 
                     alt="Profile" 
                     style="width: 35px; height: 35px; border-radius: 50%; object-fit: cover; border: 2px solid #444;">

                <span class="d-none d-sm-inline" style="margin-left: 10px; font-weight: 600; color: #444;">
                    {{ Auth::user()->username }}
                </span>
            </button>

            <div id="profileMenu" class="card profile-menu shadow-sm">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item" id="editProfileBtn" style="cursor: pointer;">Edit Profile</li>
                    <li class="list-group-item"><a href="{{ route('customer.chat.index') }}" class="text-reset text-decoration-none">Chat</a></li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="list-group-item text-danger border-0 w-100 text-start bg-transparent" id="logoutBtn" style="cursor: pointer;">Log Out</button>
                    </form>
                </ul>
            </div>
        </div>
    </header>

    <div id="home" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="{{ asset('assets/tanaman.jpg') }}" class="d-block w-100" alt="Slide 1">
            </div>
            <div class="carousel-item">
                <img src="{{ asset('assets/community.jpg') }}" class="d-block w-100" alt="Slide 2">
            </div>
            <div class="carousel-item">
                <img src="{{ asset('assets/tanaman2.jpg') }}" class="d-block w-100" alt="Slide 3">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#home" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#home" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <main  class="py-4">
        <div class="container">
            <h5 class="text-center mb-4" style="font-weight:700; font-size: 30px;">What Do You Want To Do Today..?</h5>

            <div class="row features justify-content-center g-4 mb-5">
                <div class="col-12 col-md-5">
                    <div class="card feature-market h-100" role="button" onclick="window.location.href='{{ route('marketplace.index') }}';">
                        <div class="d-flex align-items-center h-100 p-3">
                            <div class="feature-ico me-3">
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

                <div class="col-12 col-md-5">
                    <div class="card feature-donation h-100" role="button" onclick="window.location.href='{{ route('donation.index') }}';">
                        <div class="d-flex align-items-center h-100 p-3">
                            <div class="feature-ico me-3">
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

            <section id="promo" class="mb-5">
                <h5 class="text-center mb-3" style="font-weight:700; font-size: 30px;">Promos & News</h5>
                <div class="row g-4">
                    <div class="col-12 col-md-6 col-lg-4" onclick="openNews(1)" style="cursor:pointer;">
                        <div class="news-card">
                            <div style="height:150px;border-radius:.8rem;background:linear-gradient(180deg,var(--yellow-1),var(--gold-1)); overflow:hidden;">
                                <img src="{{ asset('assets/food.jpg') }}" alt="makanan promo" style="width:100%; height:100%; object-fit:cover;">
                            </div>
                            <h6 class="mt-2" style="font-weight:700">Sarapan Sehat untuk Awal Hari</h6>
                            <p class="muted">Nikmati menu sarapan bergizi dengan sayuran segar dan telur organik.</p>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4" onclick="openNews(2)" style="cursor:pointer;">
                        <div class="news-card">
                            <div style="height:150px;border-radius:.8rem;background:linear-gradient(180deg,var(--cream-2),var(--yellow-1)); overflow:hidden;">
                                <img src="{{ asset('assets/promo2.png') }}" alt="makanan promo" style="width:100%; height:100%; object-fit:cover;">
                            </div>
                            <h6 class="mt-2" style="font-weight:700">Promo Spesial Pizza Minggu Ini!</h6>
                            <p class="muted">Dapatkan dua pizza lezat hanya dengan harga satu!</p>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 d-none d-lg-block" onclick="openNews(3)" style="cursor:pointer;">
                        <div class="news-card">
                            <div style="height:150px;border-radius:.8rem;background:linear-gradient(180deg,var(--pale),var(--sage)); overflow:hidden;">
                                <img src="{{ asset('assets/sayurqan.jpg') }}" alt="roti" style="width:100%; height:100%; object-fit:cover;">
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

            <section id="tips" class="mb-5">
                <h5 class="text-center mb-3" style="font-weight:700; font-size: 30px;">Food Storage Tips</h5>
                <div class="row g-3">
                    <div class="col-12"  style="cursor:pointer;">
                        <div class="tip-item">
                            <div style="color: var(--gold-1)">
                                <h6 style="margin:0;font-weight:700">Simpan Buah di Tempat Terpisah</h6>
                                <p class="muted mb-0">Pisahkan pisang dan apel dari sayuran agar tahan lebih lama.</p>
                            </div>
                            <div style="width:72px;height:72px;border-radius:8px;background:linear-gradient(180deg,var(--cream-1),var(--cream-2)); overflow:hidden;">
                                <img src="{{ asset('assets/pisang.jpg') }}" alt="pisang" style="width:100%; height:100%; object-fit:cover;">
                            </div>
                        </div>
                    </div>
                    <div class="col-12" style="cursor:pointer;">
                        <div class="tip-item">
                            <div style="color: var(--gold-1)">
                                <h6 style="margin:0;font-weight:700">Gunakan Wadah Kedap Udara</h6>
                                <p class="muted mb-0">Simpan bahan makanan kering dalam wadah kedap udara.</p>
                            </div>
                            <div style="width:72px;height:72px;border-radius:8px;background:linear-gradient(180deg,var(--yellow-1),var(--gold-1)); overflow:hidden;">
                                <img src="{{ asset('assets/kacang.jpg') }}" alt="kacang" style="width:100%; height:100%; object-fit:cover;">
                            </div>
                        </div>
                    </div>
                    <div class="col-12" style="cursor:pointer;">
                        <div class="tip-item">
                            <div style="color: var(--gold-1)">
                                <h6 style="margin:0;font-weight:700">Atur Suhu Kulkas dengan Benar</h6>
                                <p class="muted mb-0">Pastikan suhu kulkas stabil untuk menjaga kualitas makanan.</p>
                            </div>
                            <div style="width:72px;height:72px;border-radius:8px;background:linear-gradient(180deg,var(--pale),var(--sage)); overflow:hidden;">
                                <img src="{{ asset('assets/ice batu.jpg') }}" alt="es batu" style="width:100%; height:100%; object-fit:cover;">
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </main>

    <footer class="site-footer">
        <div class="container">
            <div class="text-center mt-3 muted small" style="color: rgb(96, 87, 87);">© Cocycle 2025.</div>
        </div>
    </footer>

<!-- PROFILE BOX  -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 550px;">
            <div class="modal-content" style="border: none; border-radius: 15px; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15); background: #fff;">
                <div class="modal-header" style="background: linear-gradient(135deg, #5CB338 0%, #4a9230 100%); border: none; padding: 30px 30px 20px 30px; border-radius: 15px 15px 0 0;">
                    <h5 class="modal-title" style="font-weight: 700; font-size: 24px; color: white;">Edit Profile</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body" style="padding: 25px 30px 30px 30px;">
                    <div id="editProfileMessage"></div>

                    <!-- Profil Picture -->
                    <div style="text-align: center; margin-bottom: 30px; margin-top: 10px;">
                        <div style="position: relative; width: 120px; height: 120px; margin: 0 auto 15px;">
                            <img id="modalProfilePicImg" 
                                 src="{{ asset(Auth::user()->profile_pic ?: 'assets/default_profile.png') }}" 
                                 alt="Profile" 
                                 style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover; border: 4px solid #5CB338; cursor: pointer; background: transparent;">
                            <div style="position: absolute; bottom: 0; right: 0; background: rgba(255, 255, 255, 0.3); backdrop-filter: blur(8px); border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 18px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);" onclick="document.getElementById('modalProfilePicInput').click();">
                                📷
                            </div>
                        </div>
                        <input type="file" id="modalProfilePicInput" style="display: none;" accept="image/*">
                        <p style="color: #888; font-size: 13px; margin-top: 8px;">Click camera to change</p>
                    </div>

                    <!-- Dalem form -->
                    <form id="modalEditProfileForm">
                        <!-- Username, dan lainnya -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                            <div>
                                <label style="display: block; font-weight: 600; margin-bottom: 6px; color: #333; font-size: 14px;">Username</label>
                                <input type="text" id="modalUsername" name="username" value="{{ Auth::user()->username }}" style="width: 100%; padding: 10px 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; font-family: 'Nunito', sans-serif; box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);" required>
                            </div>
                            <div>
                                <label style="display: block; font-weight: 600; margin-bottom: 6px; color: #333; font-size: 14px;">Email</label>
                                <input type="email" id="modalEmail" name="email" value="{{ Auth::user()->email }}" style="width: 100%; padding: 10px 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; font-family: 'Nunito', sans-serif; background: #f5f5f5; cursor: not-allowed; box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);" readonly>
                            </div>
                        </div>

                        <!-- Phone, Full Name -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                            <div>
                                <label style="display: block; font-weight: 600; margin-bottom: 6px; color: #333; font-size: 14px;">Phone Number</label>
                                <input type="tel" id="modalPhone" name="phone" value="{{ Auth::user()->phone }}" style="width: 100%; padding: 10px 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; font-family: 'Nunito', sans-serif; box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);">
                            </div>
                            <div>
                                <label style="display: block; font-weight: 600; margin-bottom: 6px; color: #333; font-size: 14px;">Full Name</label>
                                <input type="text" id="modalFullname" name="fullname" value="{{ Auth::user()->fullname }}" style="width: 100%; padding: 10px 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; font-family: 'Nunito', sans-serif; box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);">
                            </div>
                        </div>

                        <!-- Alamat -->
                        <div style="margin-bottom: 0px;">
                            <label style="display: block; font-weight: 600; margin-bottom: 6px; color: #333; font-size: 14px;">Address</label>
                            <textarea id="modalAddress" name="address" style="width: 100%; padding: 10px 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; font-family: 'Nunito', sans-serif; resize: none; box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);" rows="2">{{ Auth::user()->address }}</textarea>
                        </div>

                        <!-- Password -->
                        <div style="background: #f8f9fa; padding: 0px 0px; border-radius: 6px; margin-bottom: 15px;">
                            <p style="margin: 0 0 10px 0; color: #666; font-size: 13px;">To change your password, click the link below:</p>
                            <a href="{{ route('password.request') }}" style="display: inline-block; padding: 6px 15px; background: white; border: 1px solid #ddd; border-radius: 5px; text-decoration: none; color: #333; font-size: 13px; font-weight: 600;">Change Password</a>
                        </div>

                        <!-- Tombol submit / cancel -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 25px;">
                            <button type="submit" style="padding: 12px; background: #5CB338; color: white; border: none; border-radius: 6px; font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.3s;">Save Changes</button>
                            <button type="button" data-bs-dismiss="modal" style="padding: 12px; background: #f0f0f0; color: #333; border: none; border-radius: 6px; font-weight: 600; font-size: 14px; cursor: pointer;">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- DETAIL MODAL (untuk news, tips, dll) -->
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

    <script src="{{ asset('vendor/bootstrap-5.3.2.bundle.min.js') }}"></script>
    
    <script src="{{ asset('log-sign/main.js') }}"></script>

</body>
</html>
