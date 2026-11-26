<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

function get_db(?string $dbName = null): PDO
{
    $name = $dbName ?? DB_NAME;
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . $name . ';charset=utf8mb4';

    return new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
}

function init_db(?string $dbName = null): void
{
    $name = $dbName ?? DB_NAME;
    $rootPdo = new PDO('mysql:host=' . DB_HOST . ';charset=utf8mb4', DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    $safeDb = preg_replace('/[^a-zA-Z0-9_]/', '', $name) ?: 'visitor_slideshow';
    $rootPdo->exec("CREATE DATABASE IF NOT EXISTS `{$safeDb}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    $rootPdo->exec("USE `{$safeDb}`;");

    $rootPdo->exec("
        CREATE TABLE IF NOT EXISTS visitors (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(150) NOT NULL,
            identifier VARCHAR(255) NOT NULL UNIQUE,
            consent_given TINYINT(1) NOT NULL DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;

        CREATE TABLE IF NOT EXISTS photos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            visitor_id INT NOT NULL,
            filename VARCHAR(255) NOT NULL UNIQUE,
            file_hash CHAR(64) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_visitor FOREIGN KEY (visitor_id) REFERENCES visitors(id) ON DELETE CASCADE,
            UNIQUE KEY uniq_visitor_hash (visitor_id, file_hash)
        ) ENGINE=InnoDB;
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