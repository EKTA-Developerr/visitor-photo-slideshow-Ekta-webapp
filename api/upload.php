<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/upload_service.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$name = (string)($_POST['name'] ?? '');
$identifier = (string)($_POST['identifier'] ?? '');
$consentVal = (string)($_POST['consent'] ?? '');
$consent = in_array($consentVal, ['true', 'on', '1', 'yes'], true);

$files = [];
if (isset($_FILES['photos'])) {
    $uploaded = $_FILES['photos'];
    if (is_array($uploaded['tmp_name'])) {
        foreach ($uploaded['tmp_name'] as $idx => $tmpPath) {
            if (($uploaded['error'][$idx] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK && is_uploaded_file($tmpPath)) {
                $bytes = file_get_contents($tmpPath);
                if ($bytes !== false) {
                    $files[] = $bytes;
                }
            }
        }
    } elseif (($uploaded['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK && is_uploaded_file($uploaded['tmp_name'])) {
        $bytes = file_get_contents($uploaded['tmp_name']);
        if ($bytes !== false) {
            $files[] = $bytes;
        }
    }
}

try {
    $result = save_visitor_upload($name, $identifier, $consent, $files);
    http_response_code(201);
    echo json_encode($result);
} catch (InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error while saving upload.']);
}
