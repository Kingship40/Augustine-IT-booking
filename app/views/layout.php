<?php

declare(strict_types=1);

$flashSuccess = get_flash('success');
$flashError = get_flash('error');
$viewer = current_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(($title ?? 'Untitled') . ' | ' . $app['name']) ?></title>
    
    <!-- PWA Configuration -->
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#14532d">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('sw.js')
                    .then(reg => console.log('Service Worker Registered Successfully!', reg.scope))
                    .catch(err => console.log('Service Worker Registration Failed:', err));
            });
        }
    </script>

    <style>
        :root {
            --bg: #eef3f8;
            --panel: #ffffff;
            --ink: #16202d;
            --muted: #5d6a79;
            --brand: #14532d;
            --brand-soft: #dff5e7;
            --accent: #0f766e;
            --border: #d9e3ee;
            --danger: #991b1b;
            --danger-soft: #fee2e2;
            --success: #166534;
            --success-soft: #dcfce7;
            --shadow: 0 18px 40px rgba(22, 32, 45, 0.08);
        }
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at top left, rgba(20, 83, 45, 0.10), transparent 28%),
                linear-gradient(180deg, #f8fbff 0%, var(--bg) 100%);
        }
        a {
            color: inherit;
            text-decoration: none;
        }
        .shell {
            max-width: 1180px;
            margin: 0 auto;
            padding: 24px 18px 56px;
        }
        .nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 24px;
            padding: 18px 20px;
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid rgba(217, 227, 238, 0.9);
            border-radius: 20px;
            box-shadow: var(--shadow);
            backdrop-filter: blur(10px);
        }
        .brand {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .brand strong {
            font-size: 1.05rem;
        }
        .brand span {
            color: var(--muted);
            font-size: 0.9rem;
        }
        .nav-links {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .chip,
        .button,
        button {
            border: 0;
            border-radius: 999px;
            padding: 11px 16px;
            font-size: 0.95rem;
        }
        .chip {
            background: #f3f7fb;
            border: 1px solid var(--border);
        }
        .button,
        button {
            cursor: pointer;
            background: var(--brand);
            color: #ffffff;
            font-weight: 700;
        }
        .button.secondary {
            background: #ffffff;
            color: var(--ink);
            border: 1px solid var(--border);
        }
        .hero,
        .panel {
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(217, 227, 238, 0.92);
            border-radius: 24px;
            box-shadow: var(--shadow);
        }
        .hero {
            padding: 28px;
            margin-bottom: 24px;
        }
        .hero-grid,
        .grid {
            display: grid;
            gap: 18px;
        }
        .hero-grid {
            grid-template-columns: 1.3fr 0.9fr;
            align-items: center;
        }
        .grid.cards {
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
        }
        .grid.two {
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        }
        .panel {
            padding: 22px;
        }
        .panel h2,
        .panel h3,
        .hero h1 {
            margin-top: 0;
        }
        .eyebrow {
            display: inline-block;
            margin-bottom: 14px;
            padding: 7px 12px;
            border-radius: 999px;
            background: var(--brand-soft);
            color: var(--brand);
            font-size: 0.85rem;
            font-weight: 700;
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }
        .muted {
            color: var(--muted);
        }
        .stat {
            padding: 20px;
        }
        .stat strong {
            display: block;
            font-size: 1.8rem;
            margin-top: 8px;
        }
        .alert {
            margin-bottom: 18px;
            padding: 14px 16px;
            border-radius: 16px;
            border: 1px solid transparent;
        }
        .alert.success {
            background: var(--success-soft);
            color: var(--success);
            border-color: #bbf7d0;
        }
        .alert.error {
            background: var(--danger-soft);
            color: var(--danger);
            border-color: #fecaca;
        }
        form {
            display: grid;
            gap: 14px;
        }
        .field {
            display: grid;
            gap: 7px;
        }
        label {
            font-size: 0.95rem;
            font-weight: 700;
        }
        input,
        select,
        textarea {
            width: 100%;
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 13px 14px;
            font: inherit;
            color: var(--ink);
            background: #ffffff;
        }
        textarea {
            min-height: 120px;
            resize: vertical;
        }
        .hint {
            font-size: 0.85rem;
            color: var(--muted);
        }
        .table-wrap {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th,
        td {
            text-align: left;
            padding: 12px 10px;
            border-bottom: 1px solid #ebf1f6;
        }
        th {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: var(--muted);
        }
        .status {
            display: inline-flex;
            padding: 6px 10px;
            border-radius: 999px;
            background: #edf7ff;
            color: #0c4a6e;
            font-size: 0.82rem;
            font-weight: 700;
            text-transform: capitalize;
        }
        .empty {
            padding: 16px;
            border-radius: 16px;
            background: #f8fafc;
            border: 1px dashed var(--border);
            color: var(--muted);
        }
        .auth-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 22px;
        }
        .list {
            margin: 0;
            padding-left: 18px;
            line-height: 1.8;
        }
        @media (max-width: 840px) {
            .hero-grid,
            .auth-grid {
                grid-template-columns: 1fr;
            }
            .nav {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="shell">
        <nav class="nav">
            <div class="brand">
                <strong><a href="<?= e(url('home')) ?>"><?= e($app['name']) ?></a></strong>
                <span>Responsive PHP CRUD platform for seekers, providers, and admins</span>
            </div>
            <div class="nav-links">
                <?php if ($viewer): ?>
                    <span class="chip"><?= e($viewer['full_name']) ?> (<?= e($viewer['role']) ?>)</span>
                    <a class="button secondary" href="<?= e(url('dashboard')) ?>">Dashboard</a>
                    <form method="post" action="<?= e(url('logout')) ?>" style="display:inline-grid;">
                        <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">
                        <button type="submit">Logout</button>
                    </form>
                <?php else: ?>
                    <a class="button secondary" href="<?= e(url('login')) ?>">Sign in</a>
                    <a class="button" href="<?= e(url('register')) ?>">Create account</a>
                <?php endif; ?>
            </div>
        </nav>

        <?php if ($flashSuccess): ?>
            <div class="alert success"><?= e($flashSuccess) ?></div>
        <?php endif; ?>

        <?php if ($flashError): ?>
            <div class="alert error"><?= e($flashError) ?></div>
        <?php endif; ?>

        <?= $content ?>
    </div>
</body>
</html>