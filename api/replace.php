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
if ($photoId <= 0 || !isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['error' => 'Valid photo ID and image file are required.']);
    exit;
}

$bytes = file_get_contents($_FILES['photo']['tmp_name']);
if ($bytes === false) {
    http_response_code(400);
    echo json_encode(['error' => 'Unable to read uploaded file.']);
    exit;
}

try {
    echo json_encode(replace_photo($photoId, $bytes));
} catch (InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
