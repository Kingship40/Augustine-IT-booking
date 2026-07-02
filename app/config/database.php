<?php

declare(strict_types=1);

return [
    'driver'   => 'pgsql', 
    'host'     => getenv('DB_HOST') ?: 'db.vflwhhwxqsysrpcgvbwl.supabase.co',
    'port'     => getenv('DB_PORT') ?: '5432',
    'database' => getenv('DB_DATABASE') ?: 'postgres',
    'username' => getenv('DB_USERNAME') ?: 'postgres',
    'password' => getenv('DB_PASSWORD') ?: 'fc?644Y!nL#bar$',
    'charset'  => 'utf8', // Added to satisfy app/helpers/db.php line 20
];