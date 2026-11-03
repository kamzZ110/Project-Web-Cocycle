<!DOCTYPE html>
<html lang="en"><head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - CoCycle</title>
    <link rel="stylesheet" href="{{ asset('log-sign/auth.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body><div class="auth-container"><div class="auth-form">
    <h2 class="mb-3">Set new password</h2><p class="text-muted">Create a new password for your account.</p>
    @include('partials.feedback')
    <form action="{{ route('password.update') }}" method="POST">@csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">
        <div class="mb-3"><label for="newPassword" class="form-label">New Password</label><input type="password" id="newPassword" name="password" class="form-control" required minlength="6" autocomplete="new-password"></div>
        <div class="mb-3"><label for="confirmPassword" class="form-label">Confirm Password</label><input type="password" id="confirmPassword" name="password_confirmation" class="form-control" required minlength="6" autocomplete="new-password"></div>
        <button type="submit" class="btn btn-primary w-100">Reset Password</button>
    </form>
</div><div class="auth-image"><img src="{{ asset('assets/SignUp.jpg') }}" alt="CoCycle" class="img-fluid"></div></div></body></html>
