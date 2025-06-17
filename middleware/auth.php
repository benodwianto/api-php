<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once '../vendor/autoload.php';

function authenticate()
{
    $headers = getallheaders();

    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(['message' => 'Token tidak ditemukan']);
        exit;
    }

    $authHeader = $headers['Authorization'];
    $token = str_replace('Bearer ', '', $authHeader);

    $config = require '../config/jwt.php'; 
    $key = $config['secret_key'] ?? null;

    if (!$key || !is_string($key)) {
        http_response_code(500);
        echo json_encode(['message' => 'Kunci JWT tidak valid']);
        exit;
    }

    try {
        $decoded = JWT::decode($token, new Key($key, 'HS256'));
        return $decoded; // token valid
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(['message' => 'Token tidak valid', 'error' => $e->getMessage()]);
        exit;
    }
}
