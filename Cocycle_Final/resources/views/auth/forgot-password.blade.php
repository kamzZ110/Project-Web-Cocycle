<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - CoCycle</title>
    <link rel="stylesheet" href="{{ asset('log-sign/auth.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="auth-container"><div class="auth-form">
    <a href="{{ route('login') }}" class="small d-inline-block mb-3">← Back to login</a>
    <h2 class="mb-3">Forgot your password?</h2>
    <p class="text-muted">Enter your email below to recover your password</p>
    @include('partials.feedback')
    <form action="{{ route('password.email') }}" method="POST">@csrf
        <div class="mb-3"><input aria-label="Email" type="email" name="email" class="form-control" placeholder="Email" value="{{ old('email') }}" required></div>
        <button type="submit" class="btn btn-primary w-100">Submit</button>
    </form>
</div><div class="auth-image"><img src="{{ asset('assets/SignUp.jpg') }}" alt="CoCycle" class="img-fluid"></div></div>
</body></html>
