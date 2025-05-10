<?php
$host = "localhost";
$user = "root";
$pass = "";
$database = "ecommerce";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // ini penting!
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
