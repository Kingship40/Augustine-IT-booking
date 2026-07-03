<?php

declare(strict_types=1);

// Corrected relative paths to match the flat directory structure
require __DIR__ . '/bootstrap.php';

$app = require __DIR__ . '/app.php';
$route = $_GET['route'] ?? $app['default_route'];

function render(string $view, array $data = []): void
{
    global $app;

    extract($data, EXTR_SKIP);

    ob_start();
    require __DIR__ . '/' . $view . '.php';
    $content = ob_get_clean();

    require __DIR__ . '/layout.php';
}

function validate_registration(array $input): array
{
    $errors = [];
    $role = $input['role'] ?? 'seeker';
    $email = strtolower(trim((string) ($input['email'] ?? '')));

    if (!in_array($role, ['seeker', 'provider', 'admin'], true)) {
        $errors[] = 'Please choose a valid account role.';
    }

    if (trim($input['full_name'] ?? '') === '') {
        $errors[] = 'Full name is required.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'A valid email address is required.';
    }

    if (strlen($input['password'] ?? '') < 6) {
        $errors[] = 'Password must be at least 6 characters long.';
    }

    if (($input['password'] ?? '') !== ($input['password_confirmation'] ?? '')) {
        $errors[] = 'Password confirmation does not match.';
    }

    if ($role === 'provider' && trim($input['business_name'] ?? '') === '') {
        $errors[] = 'Business name is required for provider registration.';
    }

    if ($email !== '' && find_user_by_email($email)) {
        $errors[] = 'That email address is already registered.';
    }

    return $errors;
}

function database_ready(): bool
{
    try {
        db()->query('SELECT 1');
        return true;
    } catch (Throwable) {
        return false;
    }
}

if (!database_ready()) {
    $title = 'Database Setup Needed';
    ob_start();
    ?>
    <section class="hero">
        <span class="eyebrow">Database Required</span>
        <h1>Connect database before using authentication.</h1>
        <p class="muted">
            Update your credentials in <code>database.php</code> and import
            <code>schema.sql</code> into your database cluster. After that, reload this page.
        </p>
    </section>
    <?php
    $content = ob_get_clean();
    require __DIR__ . '/layout.php';
    exit;
}

if ($route === 'home') {
    clear_old();
    render('home', ['title' => 'Home']);
    exit;
}

if ($route === 'register' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    require_guest();
    render('register', [
        'title' => 'Register',
        'role' => $_GET['role'] ?? 'seeker',
    ]);
    exit;
}

if ($route === 'register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_guest();

    if (!verify_csrf_token($_POST['_token'] ?? null)) {
        set_flash('error', 'Your session token is invalid. Please try again.');
        redirect('register');
    }

    set_old($_POST);
    $errors = validate_registration($_POST);

    if ($errors !== []) {
        set_flash('error', implode(' ', $errors));
        redirect('register', ['role' => $_POST['role'] ?? 'seeker']);
    }

    try {
        $user = register_user([
            'role' => trim((string) $_POST['role']),
            'full_name' => trim((string) $_POST['full_name']),
            'email' => strtolower(trim((string) $_POST['email'])),
            'phone' => trim((string) ($_POST['phone'] ?? '')),
            'password' => (string) $_POST['password'],
            'business_name' => trim((string) ($_POST['business_name'] ?? '')),
            'skills' => trim((string) ($_POST['skills'] ?? '')),
            'bio' => trim((string) ($_POST['bio'] ?? '')),
            'years_experience' => trim((string) ($_POST['years_experience'] ?? '')),
        ]);

        clear_old();
        session_regenerate_id(true);
        login_user($user);
        set_flash('success', 'Registration successful. Your account is ready.');
        redirect(dashboard_route_for_role($user['role']));
    } catch (Throwable $exception) {
        set_flash('error', 'Registration could not be completed right now.');
        redirect('register', ['role' => $_POST['role'] ?? 'seeker']);
    }
}

if ($route === 'login' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    require_guest();
    render('login', ['title' => 'Login']);
    exit;
}

if ($route === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_guest();

    if (!verify_csrf_token($_POST['_token'] ?? null)) {
        set_flash('error', 'Your session token is invalid. Please try again.');
        redirect('login');
    }

    set_old($_POST);
    $user = authenticate_user(
        strtolower(trim((string) ($_POST['email'] ?? ''))),
        (string) ($_POST['password'] ?? '')
    );

    if (!$user) {
        set_flash('error', 'Incorrect email or password.');
        redirect('login');
    }

    clear_old();
    session_regenerate_id(true);
    login_user($user);
    set_flash('success', 'Welcome back.');
    redirect(dashboard_route_for_role($user['role']));
}

if ($route === 'logout' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_auth();

    if (!verify_csrf_token($_POST['_token'] ?? null)) {
        set_flash('error', 'Your session token is invalid. Please try again.');
        redirect('dashboard');
    }

    logout_user();
    session_regenerate_id(true);
    set_flash('success', 'You have been logged out.');
    redirect('login');
}

if ($route === 'dashboard') {
    require_auth();
    redirect(dashboard_route_for_role(current_user()['role']));
}

if ($route === 'seeker-dashboard') {
    require_role('seeker');
    render('seeker', [
        'title' => 'Seeker Dashboard',
        'user' => current_user(),
        'data' => get_seeker_dashboard_data((int) current_user()['id']),
    ]);
    exit;
}

if ($route === 'provider-dashboard') {
    require_role('provider');
    render('provider', [
        'title' => 'Provider Dashboard',
        'user' => current_user(),
        'data' => get_provider_dashboard_data((int) current_user()['id']),
    ]);
    exit;
}

if ($route === 'admin-dashboard') {
    require_role('admin');
    render('admin', [
        'title' => 'Admin Dashboard',
        'user' => current_user(),
        'data' => get_admin_dashboard_data(),
    ]);
    exit;
}

http_response_code(404);
render('home', ['title' => 'Page Not Found']);