<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

init_db();
$pdo = get_db();
$pdo->exec('DELETE FROM photos;');
$pdo->exec('DELETE FROM visitors;');

$uploadDir = UPLOAD_DIR;
if (is_dir($uploadDir)) {
    foreach (glob($uploadDir . '/*') ?: [] as $f) {
        if (is_file($f) && basename($f) !== '.upload.lock') {
            @unlink($f);
        }
    }
}

echo json_encode(['reset' => true]);
