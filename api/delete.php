<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/upload_service.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$photoId = (int)($_POST['id'] ?? 0);
if ($photoId <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Valid photo ID is required.']);
    exit;
}

$ok = delete_photo($photoId);
echo json_encode(['deleted' => $ok]);
