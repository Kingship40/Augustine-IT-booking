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
    <meta name="theme-color" content="#0a121e">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <style>
        :root {
            --bg: #060b13;
            --panel: #0d1726;
            --ink: #f1f5f9;
            --muted: #94a3b8;
            --brand: #1d4ed8;
            --brand-soft: rgba(29, 78, 216, 0.15);
            --accent: #38bdf8;
            --border: #1e293b;
            --danger: #ef4444;
            --danger-soft: rgba(239, 68, 68, 0.1);
            --success: #10b981;
            --success-soft: rgba(16, 185, 129, 0.1);
            --shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            --glow: 0 0 15px rgba(56, 189, 248, 0.4);
        }
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            color: var(--ink);
            background: 
                radial-gradient(circle at top left, rgba(29, 78, 216, 0.15), transparent 35%),
                var(--bg);
            min-height: 100vh;
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
            background: rgba(13, 23, 38, 0.8);
            border: 1px solid var(--border);
            border-radius: 20px;
            box-shadow: var(--shadow);
            backdrop-filter: blur(12px);
        }
        .brand {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .brand strong {
            font-size: 1.05rem;
            color: #ffffff;
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
            transition: all 0.2s ease;
        }
        .chip {
            background: #111e30;
            border: 1px solid var(--border);
            color: var(--accent);
        }
        .button,
        button {
            cursor: pointer;
            background: var(--brand);
            color: #ffffff;
            font-weight: 700;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .button:hover,
        button:hover {
            background: #2563eb;
            box-shadow: var(--glow);
        }
        .button.secondary {
            background: #1e293b;
            color: #ffffff;
            border: 1px solid var(--border);
        }
        .button.secondary:hover {
            background: #334155;
            box-shadow: none;
        }
        .hero,
        .panel {
            background: rgba(13, 23, 38, 0.9);
            border: 1px solid var(--border);
            border-radius: 24px;
            box-shadow: var(--shadow);
        }
        .hero {
            padding: 28px;
            margin-bottom: 24px;
            background: linear-gradient(135deg, #0d1726 0%, #090f1a 100%);
            border-left: 4px solid var(--brand);
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
        .panel {
            padding: 22px;
        }
        .panel h2,
        .panel h3,
        .hero h1 {
            margin-top: 0;
            color: #ffffff;
        }
        .eyebrow {
            display: inline-block;
            margin-bottom: 14px;
            padding: 7px 12px;
            border-radius: 999px;
            background: var(--brand-soft);
            color: var(--accent);
            font-size: 0.85rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            border: 1px solid rgba(56, 189, 248, 0.2);
        }
        .muted {
            color: var(--muted);
        }
        .stat {
            padding: 20px;
            background: #0d1726;
        }
        .stat strong {
            display: block;
            font-size: 1.8rem;
            margin-top: 8px;
            color: #ffffff;
        }
        .alert {
            margin-bottom: 18px;
            padding: 14px 16px;
            border-radius: 16px;
            border: 1px solid transparent;
        }
        .alert.success {
            background: var(--success-soft);
            color: #34d399;
            border-color: rgba(16, 185, 129, 0.2);
        }
        .alert.error {
            background: var(--danger-soft);
            color: #f87171;
            border-color: rgba(239, 68, 68, 0.2);
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
            color: #e2e8f0;
        }
        input,
        select,
        textarea {
            width: 100%;
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 13px 14px;
            font: inherit;
            color: #ffffff;
            background: #070c14;
        }
        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 8px rgba(56, 189, 248, 0.2);
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
            border-bottom: 1px solid var(--border);
        }
        th {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: var(--muted);
        }
        td {
            color: #e2e8f0;
        }
        .status {
            display: inline-flex;
            padding: 6px 10px;
            border-radius: 999px;
            background: #1e293b;
            color: var(--accent);
            font-size: 0.82rem;
            font-weight: 700;
            text-transform: capitalize;
            border: 1px solid var(--border);
        }
        .empty {
            padding: 24px;
            border-radius: 16px;
            background: #090f1a;
            border: 1px dashed var(--border);
            color: var(--muted);
            text-align: center;
        }
        @media (max-width: 840px) {
            .hero-grid {
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
                <span>Premium Dark UI Workspace</span>
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
                    <a class="button" href="<?= e(url('register')) ?>">Get Started</a>
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