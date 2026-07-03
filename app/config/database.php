<?php

declare(strict_types=1);

return [
    'host'     => getenv('DB_HOST') ?: 'aws-0-eu-west-1.pooler.supabase.com',
    'port'     => getenv('DB_PORT') ?: '5432',
    'database' => getenv('DB_DATABASE') ?: 'postgres',
    'username' => getenv('DB_USERNAME') ?: 'postgres.vflwhhwxqsysrpcgvbwl',
    'password' => getenv('DB_PASSWORD') ?: 'fc?644Y!nL#bar$',
];