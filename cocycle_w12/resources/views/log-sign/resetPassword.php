<?php
session_start();
require __DIR__ . "/../backend/db_connect.php";

// Cek apakah sudah melewati tahap verifikasi OTP
if (!isset($_SESSION['otp_verified']) || $_SESSION['otp_verified'] !== true) {
    header("Location: forgotPassword.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];
    $email = $_SESSION['reset_email'];

    if ($new_pass !== $confirm_pass) {
        $error = "Password tidak cocok!";
    } elseif (strlen($new_pass) < 6) {
        $error = "Password minimal 6 karakter.";
    } else {
        // 1. Hash Password Baru
        $hashed_password = password_hash($new_pass, PASSWORD_BCRYPT);

        // 2. Update DB & Hapus Token (supaya tidak bisa dipakai ulang)
        $stmt = $conn->prepare("UPDATE users SET password = ?, kode_otp = NULL, batas_Waktu = NULL WHERE email = ?");
        $stmt->bind_param("ss", $hashed_password, $email);

        if ($stmt->execute()) {
            // Hapus sesi
            session_destroy();
            echo "<script>
                alert('Password berhasil diubah! Silakan login dengan password baru.');
                window.location.href = '../backend/login.php';
            </script>";
            exit;
        } else {
            $error = "Gagal mengupdate password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password - CoCycle</title>
  <link rel="stylesheet" href="auth.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>
<body>
  <div class="auth-container">
    <div class="auth-form">
      <h2 class="mb-3">Set new password</h2>
      <p class="text-muted">Create a new password for your account.</p>

      <?php if($error): ?>
          <div class="alert alert-danger"><?= $error ?></div>
      <?php endif; ?>

      <form action="" method="POST">
        <div class="mb-3">
          <label for="newPassword" class="form-label">New Password</label>
          <input type="password" name="new_password" id="newPassword" class="form-control" placeholder="Enter new password" required>
          <small class="text-muted">Must be at least 6 characters</small>
        </div>
        
        <div class="mb-3">
          <label for="confirmPassword" class="form-label">Confirm Password</label>
          <input type="password" name="confirm_password" id="confirmPassword" class="form-control" placeholder="Re-enter new password" required>
        </div>
        
        <button type="submit" class="btn btn-primary w-100">Reset Password</button>
      </form>
    </div>
    <div class="auth-image">
      <img src="assets/forgot.png" alt="reset password illustration" class="img-fluid">
    </div>
  </div>
</body>
</html>