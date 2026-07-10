<?php

declare(strict_types=1);

$selectedRole = old('role', $role ?? 'seeker');
$selectedServiceCategory = old('service_category', $serviceCategory ?? '');
$serviceOtherChecked = !empty(old('service_other', $serviceOther ?? ''));
$serviceOtherText = old('service_other_text', $serviceOtherText ?? '');
?>
<section class="auth-grid">
    <div class="hero">
        <span class="eyebrow">Create Account</span>
        <h1>Register as a seeker or provider.</h1>
        <p class="muted">
            Provider accounts can select a service category during sign-up and start in a pending verification state.
        </p>
        <ul class="list">
            <li>seekers can request services and fund their wallet</li>
            <li>providers can publish services and request withdrawals</li>
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

            <div id="provider-specific-fields" style="<?= $selectedRole === 'provider' ? '' : 'display:none;' ?>">
                <div class="field">
                    <label for="business_name">Business name for providers</label>
                    <input id="business_name" name="business_name" value="<?= e(old('business_name')) ?>">
                    <div class="hint">Required only when the role is service provider.</div>
                </div>

                <div class="field">
                    <label for="service_category">Primary service category</label>
                    <select id="service_category" name="service_category">
                        <option value="">Select a service category</option>
                        <?php foreach ($service_categories as $category): ?>
                            <option value="<?= e($category) ?>" <?= $selectedServiceCategory === $category ? 'selected' : '' ?>><?= e($category) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="field">
                    <label for="service_other">
                        <input type="checkbox" id="service_other" name="service_other" value="1" <?= $serviceOtherChecked ? 'checked' : '' ?>>
                        Other service category
                    </label>
                </div>

                <div id="service_other_text_wrap" class="field" style="<?= $serviceOtherChecked ? '' : 'display:none;' ?>">
                    <label for="service_other_text">Tell us the service category</label>
                    <input id="service_other_text" name="service_other_text" value="<?= e($serviceOtherText) ?>">
                </div>

                <div class="field">
                    <label for="skills">Additional provider skills</label>
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
            </div>

            <button type="submit">Create account</button>
        </form>
        <p class="muted">Already registered? <a href="<?= e(url('login')) ?>">Sign in here</a>.</p>
    </div>
</section>

<script>
    const roleSelect = document.getElementById('role');
    const providerFields = document.getElementById('provider-specific-fields');
    const serviceOtherCheckbox = document.getElementById('service_other');
    const serviceOtherTextWrap = document.getElementById('service_other_text_wrap');

    function toggleProviderFields() {
        if (!roleSelect || !providerFields) {
            return;
        }

        const isProvider = roleSelect.value === 'provider';
        providerFields.style.display = isProvider ? '' : 'none';

        if (!isProvider && serviceOtherCheckbox) {
            serviceOtherCheckbox.checked = false;
        }

        if (!serviceOtherCheckbox || !serviceOtherTextWrap) {
            return;
        }

        serviceOtherTextWrap.style.display = serviceOtherCheckbox.checked ? '' : 'none';
    }

    roleSelect?.addEventListener('change', toggleProviderFields);
    serviceOtherCheckbox?.addEventListener('change', toggleProviderFields);
    toggleProviderFields();
</script>
