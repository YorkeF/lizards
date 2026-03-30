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
    'SELECT l.name, l.species, l.morph, l.locality, l.gender, l.date_of_birth,
            l.sire, l.dame, l.weight_g, l.price, l.available, l.description, l.obtained_from,
            GROUP_CONCAT(i.filename ORDER BY i.sort_order SEPARATOR \'|\') AS photos
     FROM lizards l
     LEFT JOIN lizard_images i ON i.lizard_id = l.id
     GROUP BY l.id
     ORDER BY l.id'
);

$lizards = array_map(function ($row) {
    $row['photos'] = $row['photos'] ? explode('|', $row['photos']) : [];
    return $row;
}, $stmt->fetchAll(PDO::FETCH_ASSOC));

echo json_encode($lizards);
