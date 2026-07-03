<?php

declare(strict_types=1);

// Look in the current folder (app/) for app.php
$app = require __DIR__ . '/app.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name($app['session_name']);
    session_start();
}

// Look one level up in the root directory for the flat files
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../http.php';
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../User.php';
require_once __DIR__ . '/../Dashboard.php';