<?php

declare(strict_types=1);
?>
<section class="hero">
    <div class="hero-grid">
        <div>
            <span class="eyebrow">Admin Dashboard</span>
            <h1>Platform control center for <?= e($user['full_name']) ?>.</h1>
            <p class="muted">This overview is ready for user management, provider verification, wallet supervision, and reporting.</p>
        </div>
        <div class="panel">
            <h3>Admin responsibilities</h3>
            <ul class="list">
                <li>approve provider verification</li>
                <li>monitor user registrations and jobs</li>
                <li>review withdrawal requests</li>
                <li>manage platform communication and reports</li>
            </ul>
        </div>
    </div>
</section>

<section class="grid cards">
    <div class="panel stat">
        <span class="muted">Total Users</span>
        <strong><?= e((string) ($data['total_users'] ?? 0)) ?></strong>
    </div>
    <div class="panel stat">
        <span class="muted">Seekers</span>
        <strong><?= e((string) ($data['total_seekers'] ?? 0)) ?></strong>
    </div>
    <div class="panel stat">
        <span class="muted">Providers</span>
        <strong><?= e((string) ($data['total_providers'] ?? 0)) ?></strong>
    </div>
    <div class="panel stat">
        <span class="muted">Pending Providers</span>
        <strong><?= e((string) ($data['pending_providers'] ?? 0)) ?></strong>
    </div>
    <div class="panel stat">
        <span class="muted">Service Requests</span>
        <strong><?= e((string) ($data['total_requests'] ?? 0)) ?></strong>
    </div>
    <div class="panel stat">
        <span class="muted">Pending Withdrawals</span>
        <strong><?= e((string) ($data['pending_withdrawals'] ?? 0)) ?></strong>
    </div>
    <div class="panel stat">
        <span class="muted">Reviews</span>
        <strong><?= e((string) ($data['total_reviews'] ?? 0)) ?></strong>
    </div>
</section>

<section class="panel" style="margin-top:24px;">
    <h2>Recent users</h2>
    <?php if (empty($data['recent_users'])): ?>
        <div class="empty">No users registered yet.</div>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Joined</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['recent_users'] as $row): ?>
                        <tr>
                            <td><?= e($row['full_name']) ?></td>
                            <td><?= e($row['email']) ?></td>
                            <td><span class="status"><?= e($row['role']) ?></span></td>
                            <td><span class="status"><?= e($row['status']) ?></span></td>
                            <td><?= e($row['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>