<?php
session_start();
require __DIR__ . "/../backend/db_connect.php";

date_default_timezone_set('Asia/Jakarta'); 

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // 1. Cek apakah email ada
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // 2. Generate OTP 6 Digit
        $otp = rand(100000, 999999);
        
        // 3. Set waktu kadaluarsa (15 menit dari SEKARANG WAKTU JAKARTA)
        $expiry = date("Y-m-d H:i:s", strtotime('+15 minutes'));

        // 4. Simpan OTP ke Database
        $update = $conn->prepare("UPDATE users SET kode_otp = ?, batas_waktu = ? WHERE email = ?");
        $update->bind_param("sss", $otp, $expiry, $email);
        
        if ($update->execute()) {
            $_SESSION['reset_email'] = $email;

            // --- SIMULASI KIRIM EMAIL ---
            echo "<script>
                alert('Kode OTP Anda adalah: $otp');
                window.location.href = 'verifyAuth.php';
            </script>";
            exit;
        }
    } else {
        $error = "Email tidak terdaftar!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password - CoCycle</title>
  <link rel="stylesheet" href="auth.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>
<body>
  <div class="auth-container">
    <div class="auth-form">
      <a href="../backend/login.php" class="small d-inline-block mb-3"><i class="bi bi-arrow-left"></i> Back to login</a>
      <h2 class="mb-3">Forgot your password?</h2>
      <p class="text-muted">Enter your email below to recover your password</p>

      <?php if($error): ?>
          <div class="alert alert-danger"><?= $error ?></div>
      <?php endif; ?>

      <form action="" method="POST">
        <div class="mb-3">
          <input type="email" name="email" class="form-control" placeholder="Email" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Submit</button>
      </form>
    </div>
    <div class="auth-image">
      <img src="assets/forgot.png" alt="forgot illustration" class="img-fluid">
    </div>
  </div>
</body>
</html>