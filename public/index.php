<?php
include '../config/db.php';

$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (preg_match('#^/api/product/([0-9]+)$#', $request, $matches)) {
    $_GET['id'] = $matches[1];
    require_once '../src/get_product.php';
} else {
    switch ($request) {
        case '/api/products':
            require_once '../src/get_products.php';
            break;
        case '/api/add-product':
            require_once '../src/add_product.php';
            break;
        case '/api/update-product':
            require_once '../src/update_product.php';
            break;
        case '/api/delete-product':
            require_once '../src/delete_product.php';
            break;
        default:
            http_response_code(404);
            echo json_encode(['message' => 'Endpoint tidak ditemukan']);
            break;
    }
}
