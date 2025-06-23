<?php
include '../config/db.php';
require_once '../middleware/auth.php';

header('Content-Type: application/json');

$decodedUser = authenticate();

// Pastikan direktori upload ada
$uploadDir = '../assets/img/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

try {
    // Cek jika file diupload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $destPath = $uploadDir . $fileName;

        // Validasi ekstensi (opsional tapi direkomendasikan)
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
        $fileType = mime_content_type($fileTmpPath);
        if (!in_array($fileType, $allowedTypes)) {
            echo json_encode(['message' => 'Format gambar tidak didukung']);
            exit;
        }

        move_uploaded_file($fileTmpPath, $destPath);
    } else {
        $fileName = null; // Tidak ada gambar yang diupload
    }

    // Persiapan SQL
    $stmt = $pdo->prepare("INSERT INTO products (name, price, description, stock, category_id, image, created_at)
                            VALUES (:name, :price, :description, :stock, :category_id, :image, :created_at)");

    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':stock', $stock);
    $stmt->bindParam(':category_id', $category_id);
    $stmt->bindParam(':image', $fileName);
    $stmt->bindParam(':created_at', $created_at);

    // Data dari POST
    $name = $_POST['name'] ?? '';
    $price = $_POST['price'] ?? 0;
    $description = $_POST['description'] ?? '';
    $stock = $_POST['stock'] ?? 0;
    $category_id = $_POST['category_id'] ?? null;
    $created_at = date('Y-m-d H:i:s');

    $stmt->execute();
    echo json_encode(['message' => 'Produk berhasil ditambahkan']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Produk gagal ditambahkan', 'error' => $e->getMessage()]);
}
