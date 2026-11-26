<?php
declare(strict_types=1);

define('MAX_UPLOAD_BYTES', (int)(getenv('MAX_UPLOAD_MB') ?: 5) * 1024 * 1024);
define('SLIDE_DURATION_SECONDS', (int)(getenv('SLIDE_DURATION_SECONDS') ?: 8));
define('UPLOAD_DIR', getenv('UPLOAD_DIR') ?: __DIR__ . '/../uploads');

// MySQL (XAMPP / phpMyAdmin) Configuration
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_NAME', getenv('DB_NAME') ?: 'visitor_slideshow');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') !== false ? (string)getenv('DB_PASS') : '');