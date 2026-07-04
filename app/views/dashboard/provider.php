<?php

declare(strict_types=1);
?>
<section class="hero">
    <div class="hero-grid">
        <div>
            <span class="eyebrow">Provider Dashboard</span>
            <h1>Welcome back, <?= e($user['full_name'] ?? 'Provider') ?>.</h1>
            <p class="muted">This dashboard tracks provider setup, jobs, and overall platform activity.</p>
        </div>
        <div class="panel">
            <h3>Profile summary</h3>
            <p><strong>Business:</strong> <?= e($data['profile']['business_name'] ?? 'Not set') ?></p>
        </div>
    </div>
</section>

<section class="grid cards">
    <div class="panel stat">
        <span class="muted">Published Services</span>
        <strong><?= e((string) ($data['service_count'] ?? 0)) ?></strong>
    </div>
    <div class="panel stat">
        <span class="muted">Total Jobs</span>
        <strong><?= e((string) ($data['job_count'] ?? 0)) ?></strong>
    </div>
</section>

<section class="panel" style="margin-top:24px;">
    <h2>Recent jobs</h2>
    <?php if (empty($data['recent_jobs'])): ?>
        <div class="empty">No assigned jobs yet. The next phase will connect providers to services, requests, and job updates.</div>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Seeker</th>
                        <th>Status</th>
                        <th>Amount</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['recent_jobs'] as $job): ?>
                        <tr>
                            <td><?= e($job['subject'] ?? 'Service Request') ?></td>
                            <td><?= e($job['seeker_name'] ?? 'Client') ?></td>
                            <td><span class="status"><?= e($job['status'] ?? 'pending') ?></span></td>
                            <td>NGN <?= number_format((float) ($job['amount'] ?? 0), 2) ?></td>
                            <td><?= e($job['created_at'] ?? 'N/A') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>