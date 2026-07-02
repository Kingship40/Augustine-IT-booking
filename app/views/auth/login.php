<?php

declare(strict_types=1);
?>
<section class="auth-grid">
    <div class="hero">
        <span class="eyebrow">Sign In</span>
        <h1>Access the right dashboard for your account role.</h1>
        <p class="muted">
            After login, seekers, providers, and admins are redirected automatically to their own workspace.
        </p>
        <ul class="list">
            <li>seeker dashboard for requests and wallet activity</li>
            <li>provider dashboard for profile status and jobs</li>
            <li>admin dashboard for platform oversight</li>
        </ul>
    </div>

    <div class="panel">
        <h2>Login</h2>
        <form method="post" action="<?= e(url('login')) ?>">
            <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">

            <div class="field">
                <label for="email">Email address</label>
                <input id="email" type="email" name="email" value="<?= e(old('email')) ?>" required>
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" required>
            </div>

            <button type="submit">Sign in</button>
        </form>
        <p class="muted">Need an account? <a href="<?= e(url('register')) ?>">Create one here</a>.</p>
    </div>
</section>
