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
    'SELECT i.id, i.name, i.scientific_name, i.description, i.price, i.available, i.obtained_from,
            GROUP_CONCAT(img.filename ORDER BY img.sort_order SEPARATOR \'|\') AS photos
     FROM isopods i
     LEFT JOIN isopod_images img ON img.isopod_id = i.id
     GROUP BY i.id
     ORDER BY i.id'
);

$isopods = array_map(function ($row) {
    $lid = (int)$row['id'];
    unset($row['id']);
    $row['photos'] = $row['photos'] ? explode('|', $row['photos']) : [];
    return $row;
}, $stmt->fetchAll(PDO::FETCH_ASSOC));

echo json_encode($isopods);
