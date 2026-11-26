<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/validators.php';

function save_visitor_upload(
    string $name,
    string $rawIdentifier,
    bool $consent,
    array $files,
    ?string $dbPath = null,
    ?string $uploadDir = null
): array {
    $name = trim($name);
    $identifier = normalize_identifier($rawIdentifier);

    if ($name === '' || $identifier === '') {
        throw new InvalidArgumentException('Name and email/phone identifier are required.');
    }
    if (!$consent) {
        throw new InvalidArgumentException('Privacy consent is required before uploading.');
    }
    if (count($files) === 0) {
        throw new InvalidArgumentException('Please select at least one photo.');
    }

    $targetDir = $uploadDir ?? UPLOAD_DIR;
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0775, true);
    }

    init_db($dbPath);

    $lockFile = fopen($targetDir . '/.upload.lock', 'c+');
    if ($lockFile === false) {
        throw new RuntimeException('Unable to acquire upload lock.');
    }
    flock($lockFile, LOCK_EX);

    try {
        $pdo = get_db($dbPath);
        $pdo->beginTransaction();

        $stmt = $pdo->prepare('SELECT id, name FROM visitors WHERE identifier = ?');
        $stmt->execute([$identifier]);
        $existing = $stmt->fetch();

        $isReturning = $existing !== false;
        if ($isReturning) {
            $visitorId = (int)$existing['id'];
            $upd = $pdo->prepare('UPDATE visitors SET name = ? WHERE id = ?');
            $upd->execute([$name, $visitorId]);
        } else {
            $ins = $pdo->prepare('INSERT INTO visitors (name, identifier, consent_given) VALUES (?, ?, 1)');
            $ins->execute([$name, $identifier]);
            $visitorId = (int)$pdo->lastInsertId();
        }

        $savedPhotos = [];
        $skippedDuplicates = 0;

        foreach ($files as $bytes) {
            [$ok, $result] = validate_image_bytes($bytes);
            if (!$ok) {
                throw new InvalidArgumentException($result);
            }
            $ext = $result;
            $fileHash = hash('sha256', $bytes);

            $dupStmt = $pdo->prepare('SELECT id FROM photos WHERE visitor_id = ? AND file_hash = ?');
            $dupStmt->execute([$visitorId, $fileHash]);
            if ($dupStmt->fetch() !== false) {
                $skippedDuplicates++;
                continue;
            }

            $uniqueName = bin2hex(random_bytes(16)) . '.' . $ext;
            file_put_contents($targetDir . '/' . $uniqueName, $bytes);

            $photoIns = $pdo->prepare('INSERT INTO photos (visitor_id, filename, file_hash) VALUES (?, ?, ?)');
            $photoIns->execute([$visitorId, $uniqueName, $fileHash]);
            $savedPhotos[] = $uniqueName;
        }

        $countStmt = $pdo->prepare('SELECT COUNT(*) FROM photos WHERE visitor_id = ?');
        $countStmt->execute([$visitorId]);
        $totalVisitorPhotos = (int)$countStmt->fetchColumn();

        $pdo->commit();

        return [
            'visitor_name' => $name,
            'is_returning' => $isReturning,
            'saved_count' => count($savedPhotos),
            'skipped_duplicates' => $skippedDuplicates,
            'total_visitor_photos' => $totalVisitorPhotos,
            'saved_filenames' => $savedPhotos,
        ];
    } catch (Throwable $e) {
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    } finally {
        flock($lockFile, LOCK_UN);
        fclose($lockFile);
    }
}

function replace_photo(int $photoId, string $bytes, ?string $dbPath = null, ?string $uploadDir = null): array
{
    [$ok, $result] = validate_image_bytes($bytes);
    if (!$ok) {
        throw new InvalidArgumentException($result);
    }
    $ext = $result;
    $newHash = hash('sha256', $bytes);
    $targetDir = $uploadDir ?? UPLOAD_DIR;
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0775, true);
    }

    init_db($dbPath);
    $pdo = get_db($dbPath);
    $stmt = $pdo->prepare('SELECT id, visitor_id, filename FROM photos WHERE id = ?');
    $stmt->execute([$photoId]);
    $row = $stmt->fetch();
    if (!$row) {
        throw new InvalidArgumentException('Photo not found.');
    }

    $dupStmt = $pdo->prepare('SELECT id FROM photos WHERE visitor_id = ? AND file_hash = ? AND id != ?');
    $dupStmt->execute([(int)$row['visitor_id'], $newHash, $photoId]);
    if ($dupStmt->fetch() !== false) {
        throw new InvalidArgumentException('This photo is already uploaded for this visitor.');
    }

    $newName = bin2hex(random_bytes(16)) . '.' . $ext;
    file_put_contents($targetDir . '/' . $newName, $bytes);

    $upd = $pdo->prepare('UPDATE photos SET filename = ?, file_hash = ? WHERE id = ?');
    $upd->execute([$newName, $newHash, $photoId]);

    $oldPath = $targetDir . '/' . $row['filename'];
    if (is_file($oldPath)) {
        @unlink($oldPath);
    }

    return ['id' => $photoId, 'url' => 'uploads/' . $newName];
}

function delete_photo(int $photoId, ?string $dbPath = null, ?string $uploadDir = null): bool
{
    init_db($dbPath);
    $pdo = get_db($dbPath);
    $stmt = $pdo->prepare('SELECT filename FROM photos WHERE id = ?');
    $stmt->execute([$photoId]);
    $row = $stmt->fetch();
    if (!$row) {
        return false;
    }

    $del = $pdo->prepare('DELETE FROM photos WHERE id = ?');
    $del->execute([$photoId]);

    $targetDir = $uploadDir ?? UPLOAD_DIR;
    $path = $targetDir . '/' . $row['filename'];
    if (is_file($path)) {
        @unlink($path);
    }
    return true;
}

function get_slideshow_items(?string $dbPath = null): array
{
    init_db($dbPath);
    $pdo = get_db($dbPath);
    $rows = $pdo->query("
        SELECT p.id, p.filename, p.created_at, v.name AS visitor_name,
               (SELECT COUNT(*) FROM photos p2 WHERE p2.visitor_id = v.id) AS visitor_photo_count
        FROM photos p
        JOIN visitors v ON v.id = p.visitor_id
        ORDER BY p.id DESC
    ")->fetchAll();

    return array_map(static function (array $r): array {
        return [
            'id' => (int)$r['id'],
            'url' => 'uploads/' . $r['filename'],
            'visitor_name' => $r['visitor_name'],
            'is_returning' => ((int)$r['visitor_photo_count']) > 1,
            'created_at' => $r['created_at'],
        ];
    }, $rows);
}
