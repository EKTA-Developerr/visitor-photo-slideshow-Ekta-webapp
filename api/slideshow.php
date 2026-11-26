<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/upload_service.php';

$items = get_slideshow_items();
$pdo = get_db();
$visitorCount = (int)$pdo->query('SELECT COUNT(*) FROM visitors')->fetchColumn();

echo json_encode([
    'items' => $items,
    'visitor_count' => $visitorCount,
]);
