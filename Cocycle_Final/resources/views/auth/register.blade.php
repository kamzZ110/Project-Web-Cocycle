<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - CoCycle</title>
    <link rel="stylesheet" href="{{ asset('log-sign/auth.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-image">
            <div class="overlay-text"><h5>Welcome to the community</h5><p>Sign up to explore</p></div>
        </div>
        <div class="auth-form">
            <h2 class="mb-3">{{ $role == 'partner' ? 'Sign Up as Partner' : 'Create your account!' }}</h2>
            <p class="text-muted">Enter your full details</p>
            
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                         @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
             @endif

            <form action="{{ route('register') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="role_type" value="{{ $role }}">

                <div class="text-center mb-3">
                    <label for="profilePicInput" class="position-relative d-inline-block" style="cursor:pointer;">
                        <img id="profilePicPreview" src="{{ asset('assets/default_profile.png') }}" class="rounded-circle bg-light" style="width:130px;height:130px;object-fit:cover;border:2px solid #ddd;">
                        <input type="file" id="profilePicInput" name="profile_pic" accept="image/*" class="d-none" onchange="document.getElementById('profilePicPreview').src = window.URL.createObjectURL(this.files[0])">
                        <div class="position-absolute bottom-0 end-0 bg-primary rounded-circle p-1"><i class="bi bi-camera text-white"></i></div>
                    </label>
                </div>

                <div class="mb-3"><input type="text" name="username" class="form-control" placeholder="Username" required value="{{ old('username') }}"></div>
                <div class="mb-3"><input type="email" name="email" class="form-control" placeholder="Email" required value="{{ old('email') }}"></div>
                <div class="mb-3"><input type="tel" name="phone" class="form-control" placeholder="Phone" required value="{{ old('phone') }}"></div>
                <div class="mb-3"><input type="text" name="address" class="form-control" placeholder="Address" required value="{{ old('address') }}"></div>
                
                @if ($role == 'partner')
                <div class="mb-3">
                    <input type="text" name="nama_toko" class="form-control" placeholder="Nama Toko" required value="{{ old('nama_toko') }}">
                </div>
                @endif

                <div class="mb-3"><input type="password" name="password" class="form-control" placeholder="Password" required></div>
                
                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" id="remember" style="border: 1px solid #555;">
                    <label class="form-check-label" for="remember" style="margin-left: 5px;">Remember me</label>
                </div>
                
                <button type="submit" class="btn btn-primary w-100">Continue</button>
            </form>

            <div class="text-center mt-3">
                <p class="mt-3">Already have an account? <a href="{{ route('login') }}">Login</a></p>
            </div>
        </div>
    </div>  
    <script src="{{ asset('vendor/bootstrap-5.3.8.bundle.min.js') }}"></script>
    <script src="{{ asset('log-sign/main.js') }}"></script>
</body>
</html>