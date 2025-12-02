<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - CoCycle</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-image">
            <div class="overlay-text"><h5>Welcome to the community</h5><p>Sign up to explore</p></div>
        </div>
        <div class="auth-form">
            
            <h2 class="mb-3"><?php echo $pageTitle; ?></h2>
            
            <form action="" method="POST" enctype="multipart/form-data">
                
                <input type="hidden" name="role_type" value="<?php echo $role; ?>">

                <div class="text-center mb-3">
                    <label for="profilePicInput" class="position-relative d-inline-block" style="cursor:pointer;">
                        <img id="profilePicPreview" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='130' height='130' fill='%23aaa' class='bi bi-person-circle' viewBox='0 0 16 16'%3E%3Cpath d='M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z'/%3E%3Cpath fill-rule='evenodd' d='M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z'/%3E%3C/svg%3E" class="rounded-circle bg-light" style="width:130px;height:130px;object-fit:cover;border:2px solid #ddd;">
                        <div class="position-absolute bottom-0 end-0 bg-primary rounded-circle p-1"><i class="bi bi-camera text-white"></i></div>
                    </label>
                    <input type="file" id="profilePicInput" name="profile_pic" accept="image/*" class="d-none">
                </div>

                <div class="mb-3"><input type="text" name="username" class="form-control" placeholder="Username" required></div>
                <div class="mb-3"><input type="email" name="email" class="form-control" placeholder="Email" required></div>
                <div class="mb-3"><input type="tel" name="phone" class="form-control" placeholder="Phone" required></div>
                <div class="mb-3"><input type="password" name="password" class="form-control" placeholder="Password" required></div>
                
                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" id="remember" style="border: 1px solid #555;">
                    <label class="form-check-label" for="remember" style="margin-left: 5px;">Remember me</label>
                </div>
                
                <button type="submit" name="register" class="btn btn-primary w-100">Continue</button>
            </form>

            <div class="text-center mt-3">
                <p class="mt-3">Already have an account? <a href="login.php">Login</a></p>
            </div>
        </div>
    </div>  
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/main.js') }}"></script>
</body>
</html> 