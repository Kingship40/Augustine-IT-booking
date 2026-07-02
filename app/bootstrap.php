<?php

declare(strict_types=1);

$app = require __DIR__ . '/config/app.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name($app['session_name']);
    session_start();
}

require_once __DIR__ . '/helpers/db.php';
require_once __DIR__ . '/helpers/http.php';
require_once __DIR__ . '/helpers/auth.php';
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/Dashboard.php';
