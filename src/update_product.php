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

$id = $_POST['id'] ?? null;
$name = $_POST['name'] ?? null;
$price = $_POST['price'] ?? null;
$description = $_POST['description'] ?? null;
$stock = $_POST['stock'] ?? null;
$category_id = $_POST['category_id'] ?? null;

if (!$id || !$name || !$price) {
    http_response_code(400);
    echo json_encode(['message' => 'ID, name, dan price wajib diisi']);
    exit;
}

try {
    // Ambil nama gambar lama untuk menghapus jika diganti
    $stmt = $pdo->prepare("SELECT image FROM products WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);
    $oldImage = $existing['image'] ?? null;

    // Proses gambar baru jika ada
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $destPath = $uploadDir . $fileName;

        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
        $fileType = mime_content_type($fileTmpPath);
        if (!in_array($fileType, $allowedTypes)) {
            echo json_encode(['message' => 'Format gambar tidak didukung']);
            exit;
        }

        move_uploaded_file($fileTmpPath, $destPath);

        // Hapus gambar lama jika ada
        if ($oldImage && file_exists($uploadDir . $oldImage)) {
            unlink($uploadDir . $oldImage);
        }

        $image = $fileName;
    } else {
        // Tidak ada gambar baru, pakai yang lama
        $image = $oldImage;
    }

    // Update produk
    $stmt = $pdo->prepare("UPDATE products SET 
        name = :name,
        price = :price,
        description = :description,
        stock = :stock,
        category_id = :category_id,
        image = :image
        WHERE id = :id");

    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':stock', $stock);
    $stmt->bindParam(':category_id', $category_id);
    $stmt->bindParam(':image', $image);

    $stmt->execute();
    echo json_encode(['message' => 'Produk berhasil diperbarui']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Gagal memperbarui produk', 'error' => $e->getMessage()]);
}
