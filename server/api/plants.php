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
    'SELECT p.id, p.name, p.scientific_name, p.description, p.price, p.available, p.care_level,
            GROUP_CONCAT(img.filename ORDER BY img.sort_order SEPARATOR \'|\') AS photos
     FROM plants p
     LEFT JOIN plant_images img ON img.plant_id = p.id
     GROUP BY p.id
     ORDER BY p.id'
);

$plants = array_map(function ($row) {
    unset($row['id']);
    $row['photos'] = $row['photos'] ? explode('|', $row['photos']) : [];
    return $row;
}, $stmt->fetchAll(PDO::FETCH_ASSOC));

echo json_encode($plants);
