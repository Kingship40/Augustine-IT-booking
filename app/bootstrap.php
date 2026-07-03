<?php

declare(strict_types=1);

// Look one level up (in the root directory) for these flat files
$app = require __DIR__ . '/../app.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name($app['session_name']);
    session_start();
}

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../http.php';
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../User.php';
require_once __DIR__ . '/../Dashboard.php';