<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../../config.php';

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$stmt = $pdo->query(
    'SELECT name, species, morph, locality, gender, date_of_birth,
            sire, dame, weight_g, price, available, description, photo, obtained_from
     FROM lizards
     ORDER BY id'
);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
