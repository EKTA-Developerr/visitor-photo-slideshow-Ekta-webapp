<?php
declare(strict_types=1);

define('MAX_UPLOAD_BYTES', (int)(getenv('MAX_UPLOAD_MB') ?: 5) * 1024 * 1024);
define('SLIDE_DURATION_SECONDS', (int)(getenv('SLIDE_DURATION_SECONDS') ?: 8));
define('DB_PATH', getenv('DB_PATH') ?: __DIR__ . '/../data/visitors.db');
define('UPLOAD_DIR', getenv('UPLOAD_DIR') ?: __DIR__ . '/../uploads');
