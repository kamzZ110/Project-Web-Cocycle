<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Toko - Partner CoCycle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --green-sage: #8A9A5B;
            --gold: #F6C55A;
            --cream: #FFF7E0;
            --bg-light: #f8f9f4;
        }
        body { background-color: var(--bg-light); font-family: 'Poppins', sans-serif; color: #333; }

        /* Navbar */
        .navbar { background-color: var(--beam-cream, #FFF7E0); border-bottom: 2px solid #EEE9B0; padding: 1rem 2rem; }
        .navbar-brand { font-weight: 700; color: #333; }
        .brand-icon { color: var(--green-sage); margin-right: 10px; font-size: 1.5rem; }

        /* Profile Card */
        .profile-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            padding: 30px;
            margin-top: 30px;
        }

        .profile-img-container {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            overflow: hidden;
            border: 4px solid var(--gold);
            margin: 0 auto 20px;
            position: relative;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .profile-img-container img { width: 100%; height: 100%; object-fit: cover; }
        .profile-img-placeholder { font-size: 3rem; color: #ccc; }

        .btn-sage {
            background-color: var(--green-sage);
            color: white; border: none; font-weight: 600;
            padding: 10px 25px; border-radius: 10px;
            transition: 0.3s;
        }
        .btn-sage:hover { background-color: #7a8a4f; color: white; }

        .form-control:focus {
            border-color: var(--green-sage);
            box-shadow: 0 0 0 0.25rem rgba(138, 154, 91, 0.25);
        }

        .upload-btn-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            margin-top: 10px;
        }
        .upload-btn-wrapper input[type=file] {
            font-size: 100px;
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            cursor: pointer;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/partner-theme.css') }}">
</head>
<body>

    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('partner.home') }}">
                <i class="bi bi-shop brand-icon"></i>
                <span>{{ strtoupper($store->store_name ?? '') }}</span>
            </a>
            <div class="ms-auto">
                <a href="{{ route('partner.home') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Kembali</a>
            </div>
        </div>
    </nav>

    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                @include('partials.feedback')
                 @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                <div class="profile-card">
                    <h4 class="text-center fw-bold mb-4" style="color: var(--green-sage);">Informasi Toko</h4>

                    <form action="{{ route('partner.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Logo Toko -->
                        <div class="text-center mb-4">
                            <div class="profile-img-container">
                                @if (!empty($store->logo_url))
                                    <img src="{{ asset($store->logo_url) }}" alt="Logo Toko">
                                @else
                                    <i class="bi bi-shop profile-img-placeholder"></i>
                                @endif
                            </div>
                            <div class="upload-btn-wrapper">
                                <button type="button" class="btn btn-outline-secondary btn-sm">Ganti Logo</button>
                                <input type="file" name="logo_image" accept="image/*" onchange="this.form.submit()" />
                            </div>
                            <div class="form-text small">Format: JPG, PNG. Maks 2MB.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Toko</label>
                            <input type="text" name="name" class="form-control" value="{{ $store->store_name }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Deskripsi Singkat</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Ceritakan sedikit tentang tokomu...">{{ $store->description }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Nomor Telepon</label>
                                <input type="text" name="phone" class="form-control" value="{{ $store->phone }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Email Bisnis</label>
                                <input type="email" name="email" class="form-control" value="{{ $store->email }}">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Alamat Lengkap</label>
                            <textarea name="address" class="form-control" rows="2">{{ $store->address }}</textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-sage py-2">SIMPAN PERUBAHAN</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('vendor/bootstrap-5.3.0.bundle.min.js') }}"></script>
</body>
</html>
