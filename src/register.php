<?php
require_once '../config/db.php';
require_once '../vendor/autoload.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"));

$name = $data->name ?? null;
$email = $data->email ?? null;
$password = $data->password ?? null;

if (!$name || !$email || !$password) {
    http_response_code(400);
    echo json_encode(["message" => "Semua field wajib diisi"]);
    exit;
}

try {
    // Cek jika email sudah terdaftar
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        http_response_code(409);
        echo json_encode(["message" => "Email sudah digunakan"]);
        exit;
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Simpan user baru
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$name, $email, $hashedPassword]);

    echo json_encode(["message" => "Registrasi berhasil"]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["message" => "Terjadi kesalahan", "error" => $e->getMessage()]);
}
