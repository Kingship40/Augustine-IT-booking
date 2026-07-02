<?php

declare(strict_types=1);

function current_user(): ?array
{
    return $_SESSION['auth_user'] ?? null;
}

function is_logged_in(): bool
{
    return current_user() !== null;
}

function login_user(array $user): void
{
    $_SESSION['auth_user'] = [
        'id' => (int) $user['id'],
        'full_name' => $user['full_name'],
        'email' => $user['email'],
        'role' => $user['role'],
        'status' => $user['status'],
    ];
}

function logout_user(): void
{
    unset($_SESSION['auth_user']);
}

function require_guest(): void
{
    if (is_logged_in()) {
        redirect('dashboard');
    }
}

function require_auth(): void
{
    if (!is_logged_in()) {
        set_flash('error', 'Please sign in to continue.');
        redirect('login');
    }
}

function require_role(string $role): void
{
    require_auth();

    if ((current_user()['role'] ?? null) !== $role) {
        set_flash('error', 'You do not have access to that page.');
        redirect('dashboard');
    }
}

function dashboard_route_for_role(string $role): string
{
    return match ($role) {
        'admin' => 'admin-dashboard',
        'provider' => 'provider-dashboard',
        default => 'seeker-dashboard',
    };
}
