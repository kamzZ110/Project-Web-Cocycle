<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CoCycle</title>
    <link rel="stylesheet" href="{{ asset('log-sign/auth.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-image">
            <div class="overlay-text"><h5>Welcome to the community</h5><p>Login to explore</p></div>
        </div>
        <div class="auth-form">
            <h2 class="mb-3">Login your account!</h2>
            
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(request()->boolean('session_expired'))
                <div class="alert alert-warning" role="alert">
                    Sesi atau token formulir sudah kedaluwarsa. Silakan masuk kembali, buka ulang halaman, lalu ulangi perubahan. Form sebelumnya tidak dikirim ulang.
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="mb-3"><input type="email" name="email" class="form-control" placeholder="Email" required value="{{ old('email') }}"></div>
                <div class="mb-3"><input type="password" name="password" class="form-control" placeholder="Password" required></div>
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                        <input type="checkbox" name="remember" class="form-check-input" id="rememberLogin" style="border: 1px solid #555;">
                        <label class="form-check-label" for="rememberLogin" style="margin-left: 5px;">Remember me</label>
                    </div>
                    <a href="{{ route('password.request') }}" class="small">Forgot password?</a>
                </div>
                
                <button type="submit" class="btn btn-primary w-100">Continue</button>
            </form>
            
            <div class="text-center mt-3">
                <p class="mb-1">Sign in With</p>
                <div class="d-flex justify-content-center gap-3">
                    <i class="bi bi-facebook fs-4" style="color:#1877F2; cursor:pointer;"></i>
                    <i class="bi bi-google fs-4" style="color:#DB4437; cursor:pointer;"></i>
                    <i class="bi bi-apple fs-4" style="color:#000; cursor:pointer;"></i>
                </div>
                
                <p class="mt-3">Don't have an account? <a href="{{ route('register') }}">Sign up</a></p>
            </div>
        </div>
    </div>
    <script src="{{ asset('vendor/bootstrap-5.3.8.bundle.min.js') }}"></script>
    <script src="{{ asset('log-sign/main.js') }}"></script>
</body>
</html>
