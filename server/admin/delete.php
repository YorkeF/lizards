<?php
require_once __DIR__ . '/_auth.php';
require_once __DIR__ . '/../../config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $stmt = $pdo->prepare('DELETE FROM lizards WHERE id = ?');
    $stmt->execute([$id]);
}

header('Location: index.php');
exit;
