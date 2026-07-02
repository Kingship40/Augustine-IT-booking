<?php

declare(strict_types=1);
?>
<section class="hero">
    <div class="hero-grid">
        <div>
            <span class="eyebrow">Phase 1 Complete</span>
            <h1>Authentication and role-based dashboards for the IT service marketplace.</h1>
            <p class="muted">
                This plain PHP CRUD foundation supports seeker, provider, and admin accounts with secure sign up,
                sign in, session handling, route protection, and role-based dashboard views.
            </p>
            <div class="nav-links" style="margin-top:18px;">
                <a class="button" href="<?= e(url('register')) ?>">Start registration</a>
                <a class="button secondary" href="<?= e(url('login')) ?>">Existing user login</a>
            </div>
        </div>
        <div class="panel">
            <h3>Included in this phase</h3>
            <ul class="list">
                <li>single registration form with role selection</li>
                <li>provider-only onboarding fields</li>
                <li>password hashing and CSRF protection</li>
                <li>automatic wallet creation for every account</li>
                <li>automatic seeker and provider profile creation</li>
                <li>separate dashboards for seeker, provider, and admin</li>
            </ul>
        </div>
    </div>
</section>

<section class="grid cards">
    <div class="panel stat">
        <span class="muted">Service Seekers</span>
        <strong>Request & pay</strong>
        <p class="muted">Create jobs, fund a wallet, track service progress, and review providers after completion.</p>
    </div>
    <div class="panel stat">
        <span class="muted">Service Providers</span>
        <strong>Deliver & withdraw</strong>
        <p class="muted">Manage profile details, receive jobs, track earnings, and submit withdrawal requests.</p>
    </div>
    <div class="panel stat">
        <span class="muted">Administrators</span>
        <strong>Monitor & control</strong>
        <p class="muted">Supervise users, requests, provider verification, withdrawals, and platform reporting.</p>
    </div>
</section>
