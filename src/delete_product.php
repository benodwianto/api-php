<?php
include '../config/db.php';
require_once '../middleware/auth.php';

header('Content-Type: application/json');

$decodedUser = authenticate();

$id = $_POST['id'] ?? null;

if (!$id) {
    http_response_code(400);
    echo json_encode(['message' => 'ID produk wajib diisi']);
    exit;
}

$uploadDir = '../assets/img/';

try {
    $stmt = $pdo->prepare("SELECT image FROM products WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        http_response_code(404);
        echo json_encode(['message' => 'Produk tidak ditemukan']);
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
    $stmt->execute([':id' => $id]);

    // Hapus gambar dari server jika ada
    if ($product['image']) {
        $imagePath = $uploadDir . $product['image'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    echo json_encode(['message' => 'Produk berhasil dihapus']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Gagal menghapus produk', 'error' => $e->getMessage()]);
}
