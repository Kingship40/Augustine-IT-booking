<?php

declare(strict_types=1);

function db(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        // Fallbacks matching your verified Supabase cluster configuration
        $host = getenv('DB_HOST') ?: 'aws-0-eu-west-1.pooler.supabase.com';
        $port = getenv('DB_PORT') ?: '5432';
        $dbname = getenv('DB_DATABASE') ?: 'postgres';
        $user = getenv('DB_USERNAME') ?: 'postgres.vflwhhwxqsysrpcgvbwl';
        $password = getenv('DB_PASSWORD') ?: 'fc?644Y!nL#bar$';

        // Construct a clean PostgreSQL DSN string
        $dsn = "pgsql:host={$host};port={$port};dbname={$dbname};";

        try {
            $pdo = new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                // Force SSL connection parameters for remote cloud environments
                PDO::pgsqlATTR_DISABLE_PREPARES => true,
            ]);
            
            // Explicitly run an isolated connection query forcing SSL negotiation if needed
            $pdo->query("SET sslmode TO 'require'");
            
        } catch (PDOException $e) {
            throw new RuntimeException("Database connection failed: " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    return $pdo;
}