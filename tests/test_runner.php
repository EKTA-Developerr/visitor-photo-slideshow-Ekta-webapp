<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/upload_service.php';

function assert_true(bool $cond, string $msg): void
{
    if (!$cond) {
        fwrite(STDERR, "FAIL: {$msg}\n");
        exit(1);
    }
}

$tmpDir = sys_get_temp_dir() . '/vps_php_test_' . bin2hex(random_bytes(4));
mkdir($tmpDir, 0775, true);
$dbPath = $tmpDir . '/test.db';
$uploadDir = $tmpDir . '/uploads';

$validPng = "\x89PNG\r\n\x1a\n" . str_repeat("\x00", 32);
$validJpg = "\xff\xd8\xff\xe0" . str_repeat("\x00", 32);

// 1. Reject missing consent
$threw = false;
try {
    save_visitor_upload('Ekta', 'ekta@gmail.com', false, [$validPng], $dbPath, $uploadDir);
} catch (InvalidArgumentException $e) {
    $threw = true;
}
assert_true($threw, 'Missing consent should throw InvalidArgumentException');

// 2. Reject invalid magic bytes
$threwInvalid = false;
try {
    save_visitor_upload('Ekta', 'ekta@gmail.com', true, ['not-an-image'], $dbPath, $uploadDir);
} catch (InvalidArgumentException $e) {
    $threwInvalid = true;
}
assert_true($threwInvalid, 'Invalid file bytes should be rejected');

// 3. Returning visitor + duplicate prevention
$r1 = save_visitor_upload('Ekta', 'Ekta@gmail.com', true, [$validPng], $dbPath, $uploadDir);
assert_true($r1['is_returning'] === false && $r1['saved_count'] === 1, 'First upload should succeed');

$rDup = save_visitor_upload('Ekta', 'ekta@gmail.com', true, [$validPng], $dbPath, $uploadDir);
assert_true($rDup['is_returning'] === true && $rDup['saved_count'] === 0 && $rDup['skipped_duplicates'] === 1, 'Duplicate photo should be skipped');

$r2 = save_visitor_upload('Ekta', 'ekta@gmail.com', true, [$validJpg], $dbPath, $uploadDir);
assert_true($r2['saved_count'] === 1 && $r2['total_visitor_photos'] === 2, 'New photo for returning visitor should save');

$items = get_slideshow_items($dbPath);
assert_true(count($items) === 2, 'Slideshow should contain 2 unique photos');

echo "ALL 4 PHP TESTS PASSED!\n";
