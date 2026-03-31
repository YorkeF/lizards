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
    'SELECT l.id, l.name, l.species, l.morph, l.locality, l.gender, l.date_of_birth,
            l.sire, l.dame, l.weight_g, l.price, l.available, l.description, l.obtained_from,
            l.is_breeder, l.hide_weight_history,
            GROUP_CONCAT(i.filename ORDER BY i.sort_order SEPARATOR \'|\') AS photos
     FROM lizards l
     LEFT JOIN lizard_images i ON i.lizard_id = l.id
     GROUP BY l.id
     ORDER BY l.id'
);
$lizards = $stmt->fetchAll(PDO::FETCH_ASSOC);

$wStmt = $pdo->query(
    'SELECT lizard_id, weighed_on, weight_g FROM lizard_weights ORDER BY lizard_id, weighed_on ASC'
);
$weightsByLizard = [];
foreach ($wStmt->fetchAll(PDO::FETCH_ASSOC) as $w) {
    $weightsByLizard[(int)$w['lizard_id']][] = [
        'date'     => $w['weighed_on'],
        'weight_g' => (float)$w['weight_g'],
    ];
}

$lizards = array_map(function ($row) use ($weightsByLizard) {
    $lid = (int)$row['id'];
    unset($row['id']);
    $row['photos']         = $row['photos'] ? explode('|', $row['photos']) : [];
    $row['weight_history'] = $weightsByLizard[$lid] ?? [];
    return $row;
}, $lizards);

echo json_encode($lizards);
