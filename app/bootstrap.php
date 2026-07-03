<?php

declare(strict_types=1);

// Step out of app/ directly into the root folder where app.php sits
$app = require __DIR__ . '/../app.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name($app['session_name']);
    session_start();
}

// Step out of app/ directly into the root folder where these flat files live
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../http.php';
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../User.php';
require_once __DIR__ . '/../Dashboard.php';