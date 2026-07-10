<?php

declare(strict_types=1);

function load_environment_file(): void
{
    $envPath = __DIR__ . '/../.env';

    if (!file_exists($envPath)) {
        return;
    }

    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        if (strpos($line, '=') === false) {
            continue;
        }

        [$name, $value] = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        if ((str_starts_with($value, '"') && str_ends_with($value, '"')) || (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
            $value = substr($value, 1, -1);
        }

        putenv("{$name}={$value}");
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }
}

load_environment_file();

// Look inside the config/ folder relative to this directory
$app = require __DIR__ . '/config/app.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name($app['session_name']);
    session_start();
}

// Point directly to the subdirectories inside the app folder
require_once __DIR__ . '/helpers/db.php';
require_once __DIR__ . '/helpers/http.php';
require_once __DIR__ . '/helpers/auth.php';
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/Dashboard.php';

ensure_provider_profile_columns();