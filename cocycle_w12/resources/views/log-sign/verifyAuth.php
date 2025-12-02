<?php
session_start();
require __DIR__ . "/../backend/db_connect.php";

// Jika tidak ada email di sesi (akses langsung tanpa forgot password), tendang ke login
if (!isset($_SESSION['reset_email'])) {
    header("Location: ../backend/login.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $otp_input = $_POST['otp_code'];
    $email = $_SESSION['reset_email'];

    // 1. Cek Kecocokan OTP dan Waktu Kadaluarsa
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND kode_otp = ? AND batas_waktu > NOW()");
    $stmt->bind_param("ss", $email, $otp_input);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // OTP Valid
        $_SESSION['otp_verified'] = true; // Kunci akses ke reset password
        header("Location: resetPassword.php");
        exit;
    } else {
        $error = "Kode salah atau sudah kadaluarsa.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Verify Code - CoCycle</title>
  <link rel="stylesheet" href="auth.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>
<body>
  <div class="auth-container">
    <div class="auth-form">
      <a href="../backend/login.php" class="small d-inline-block mb-3"><i class="bi bi-arrow-left"></i> Back to login</a>
      <h2 class="mb-3">Verify code</h2>
      <p class="text-muted">An authentication code has been sent to <b><?= $_SESSION['reset_email'] ?></b>.</p>

      <?php if($error): ?>
          <div class="alert alert-danger"><?= $error ?></div>
      <?php endif; ?>

      <form action="" method="POST">
        <div class="mb-3 position-relative">
          <input type="text" name="otp_code" class="form-control" placeholder="Enter 6-digit Code" required>
        </div>
        <p class="small text-muted">Didn’t receive a code? <a href="forgotPassword.php">Resend</a></p>
        <button type="submit" class="btn btn-primary w-100">Verify</button>
      </form>
    </div>
    <div class="auth-image">
      <img src="assets/verify.png" alt="verify illustration" class="img-fluid">
    </div>
  </div>
</body>
</html>