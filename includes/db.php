<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

function get_db(?string $dbPath = null): PDO
{
    $path = $dbPath ?? DB_PATH;
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }

    $pdo = new PDO('sqlite:' . $path, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $pdo->exec('PRAGMA journal_mode=WAL;');
    $pdo->exec('PRAGMA busy_timeout=15000;');
    $pdo->exec('PRAGMA foreign_keys=ON;');
    return $pdo;
}

function init_db(?string $dbPath = null): void
{
    $pdo = get_db($dbPath);
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS visitors (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            identifier TEXT NOT NULL UNIQUE,
            consent_given INTEGER NOT NULL DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS photos (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            visitor_id INTEGER NOT NULL,
            filename TEXT NOT NULL UNIQUE,
            file_hash TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (visitor_id) REFERENCES visitors(id) ON DELETE CASCADE,
            UNIQUE(visitor_id, file_hash)
        );
    ");
}

function normalize_identifier(string $raw): string
{
    $cleaned = strtolower(trim($raw));
    if (strpos($cleaned, '@') === false) {
        $digits = preg_replace('/\D+/', '', $cleaned);
        if ($digits !== null && strlen($digits) >= 7) {
            return $digits;
        }
    }
    return $cleaned;
}
