<?php
header("Content-Type: application/json");
include "db_connect.php";

$data = json_decode(file_get_contents("php://input"), true);

$username = $data["username"] ?? '';
$email = $data["email"] ?? '';
$phone = $data["phone"] ?? '';
$password = $data["password"] ?? '';

if (empty($username) || empty($email) || empty($phone) || empty($password)) {
    echo json_encode(["status" => "error", "message" => "Semua field wajib diisi!"]);
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

$stmt = $conn->prepare("INSERT INTO users (username, email, phone, password) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $username, $email, $phone, $hashedPassword);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Akun berhasil dibuat!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Email atau username sudah digunakan."]);
}

$stmt->close();
$conn->close();
?>
