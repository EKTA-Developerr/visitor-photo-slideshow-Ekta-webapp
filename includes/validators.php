<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

function detect_image_type(string $bytes): ?string
{
    if (strlen($bytes) < 12) {
        return null;
    }
    if (substr($bytes, 0, 3) === "\xff\xd8\xff") {
        return 'jpg';
    }
    if (substr($bytes, 0, 8) === "\x89PNG\r\n\x1a\n") {
        return 'png';
    }
    if (substr($bytes, 0, 4) === 'RIFF' && substr($bytes, 8, 4) === 'WEBP') {
        return 'webp';
    }
    return null;
}

function validate_image_bytes(string $bytes, int $maxBytes = MAX_UPLOAD_BYTES): array
{
    if ($bytes === '') {
        return [false, 'Empty file uploaded.'];
    }
    if (strlen($bytes) > $maxBytes) {
        $mb = (int)($maxBytes / (1024 * 1024));
        return [false, "File exceeds {$mb} MB limit."];
    }
    $ext = detect_image_type($bytes);
    if ($ext === null) {
        return [false, 'Invalid file type. Only JPG, PNG, and WebP images are allowed.'];
    }
    return [true, $ext];
}
