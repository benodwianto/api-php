<?php
include '../config/db.php';
require_once '../vendor/autoload.php'; // pastikan ini sesuai
include '../config/jwt.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header('Content-Type: application/json');

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $payload = [
            'iss' => $issuer,
            'aud' => $audience,
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'user_id' => $user['id'],
        ];

        $jwt = JWT::encode($payload, $key, 'HS256');

        echo json_encode([
            'message' => 'Login berhasil',
            'token' => $jwt
        ]);
    } else {
        http_response_code(401);
        echo json_encode(['message' => 'Email atau password salah']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Kesalahan server', 'error' => $e->getMessage()]);
}
