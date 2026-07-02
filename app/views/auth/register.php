<?php

declare(strict_types=1);

$selectedRole = old('role', $role ?? 'seeker');
?>
<section class="auth-grid">
    <div class="hero">
        <span class="eyebrow">Create Account</span>
        <h1>Register as a seeker, provider, or admin.</h1>
        <p class="muted">
            The registration flow is role-aware. Provider accounts also capture business details and start in a
            pending verification state.
        </p>
        <ul class="list">
            <li>seekers can request services and fund their wallet</li>
            <li>providers can publish services and request withdrawals</li>
            <li>admins can manage the platform and monitor activity</li>
        </ul>
    </div>

    <div class="panel">
        <h2>Sign up</h2>
        <form method="post" action="<?= e(url('register')) ?>">
            <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">

            <div class="field">
                <label for="role">Account role</label>
                <select id="role" name="role">
                    <option value="seeker" <?= $selectedRole === 'seeker' ? 'selected' : '' ?>>Service seeker</option>
                    <option value="provider" <?= $selectedRole === 'provider' ? 'selected' : '' ?>>Service provider</option>
                    <option value="admin" <?= $selectedRole === 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>

            <div class="field">
                <label for="full_name">Full name</label>
                <input id="full_name" name="full_name" value="<?= e(old('full_name')) ?>" required>
            </div>

            <div class="field">
                <label for="email">Email address</label>
                <input id="email" type="email" name="email" value="<?= e(old('email')) ?>" required>
            </div>

            <div class="field">
                <label for="phone">Phone number</label>
                <input id="phone" name="phone" value="<?= e(old('phone')) ?>">
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" required>
            </div>

            <div class="field">
                <label for="password_confirmation">Confirm password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required>
            </div>

            <div class="field">
                <label for="business_name">Business name for providers</label>
                <input id="business_name" name="business_name" value="<?= e(old('business_name')) ?>">
                <div class="hint">Required only when the role is service provider.</div>
            </div>

            <div class="field">
                <label for="skills">Provider skills</label>
                <textarea id="skills" name="skills"><?= e(old('skills')) ?></textarea>
            </div>

            <div class="field">
                <label for="bio">Provider bio</label>
                <textarea id="bio" name="bio"><?= e(old('bio')) ?></textarea>
            </div>

            <div class="field">
                <label for="years_experience">Years of experience</label>
                <input id="years_experience" type="number" min="0" name="years_experience" value="<?= e(old('years_experience')) ?>">
            </div>

            <button type="submit">Create account</button>
        </form>
        <p class="muted">Already registered? <a href="<?= e(url('login')) ?>">Sign in here</a>.</p>
    </div>
</section>
