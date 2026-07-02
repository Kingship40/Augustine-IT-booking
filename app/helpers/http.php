<?php

declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function url(string $route, array $params = []): string
{
    $query = array_merge(['route' => $route], $params);

    return 'index.php?' . http_build_query($query);
}

function redirect(string $route, array $params = []): never
{
    header('Location: ' . url($route, $params));
    exit;
}

function set_flash(string $key, string $message): void
{
    $_SESSION['_flash'][$key] = $message;
}

function get_flash(string $key): ?string
{
    if (!isset($_SESSION['_flash'][$key])) {
        return null;
    }

    $message = $_SESSION['_flash'][$key];
    unset($_SESSION['_flash'][$key]);

    return $message;
}

function old(string $key, string $default = ''): string
{
    return $_SESSION['_old'][$key] ?? $default;
}

function set_old(array $data): void
{
    $_SESSION['_old'] = $data;
}

function clear_old(): void
{
    unset($_SESSION['_old']);
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['_csrf'];
}

function verify_csrf_token(?string $token): bool
{
    return is_string($token)
        && isset($_SESSION['_csrf'])
        && hash_equals($_SESSION['_csrf'], $token);
}
