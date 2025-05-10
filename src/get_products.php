<?php
include '../config/db.php';
$sql = "SELECT * FROM products";
$statement = $pdo->prepare($sql);
if (!$statement->execute()) {
    echo json_encode(['message' => 'Terjadi kesalahan']);
    exit;
}
$statement->execute();
$products = $statement->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($products);
