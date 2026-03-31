<?php
require_once __DIR__ . '/_auth.php';
require_once __DIR__ . '/../../config.php';

$pdo = new PDO(
    'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
    DB_USER, DB_PASS,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$id = (int)($_GET['id'] ?? 0);
if ($id) {
    // Delete associated images from disk
    $imgs = $pdo->prepare('SELECT filename FROM isopod_images WHERE isopod_id = ?');
    $imgs->execute([$id]);
    foreach ($imgs->fetchAll(PDO::FETCH_COLUMN) as $filename) {
        $file = __DIR__ . '/../images/' . $filename;
        if (file_exists($file)) unlink($file);
    }
    $pdo->prepare('DELETE FROM isopods WHERE id = ?')->execute([$id]);
}

header('Location: isopods.php');
exit;
