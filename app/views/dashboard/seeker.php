<?php

declare(strict_types=1);
?>
<section class="hero">
    <div class="hero-grid">
        <div>
            <span class="eyebrow">Seeker Dashboard</span>
            <h1>Welcome back, <?= e($user['full_name']) ?>.</h1>
            <p class="muted">This view is prepared for service requests, wallet activity, provider discovery, and review history.</p>
        </div>
        <div class="panel">
            <h3>Quick direction</h3>
            <ul class="list">
                <li>browse providers and available services</li>
                <li>submit a service request</li>
                <li>fund wallet and pay through the platform</li>
                <li>confirm completion and leave a review</li>
            </ul>
        </div>
    </div>
</section>

<section class="grid cards">
    <div class="panel stat">
        <span class="muted">Wallet Balance</span>
        <strong>NGN <?= number_format((float) ($data['wallet']['balance'] ?? 0), 2) ?></strong>
    </div>
    <div class="panel stat">
        <span class="muted">Requests Created</span>
        <strong><?= e((string) $data['request_count']) ?></strong>
    </div>
    <div class="panel stat">
        <span class="muted">Completed Requests</span>
        <strong><?= e((string) $data['completed_count']) ?></strong>
    </div>
</section>

<section class="panel" style="margin-top:24px;">
    <h2>Recent requests</h2>
    <?php if ($data['recent_requests'] === []): ?>
        <div class="empty">No service requests yet. The next phase will connect this dashboard to provider browsing and request creation.</div>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Provider</th>
                        <th>Status</th>
                        <th>Amount</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['recent_requests'] as $request): ?>
                        <tr>
                            <td><?= e($request['subject']) ?></td>
                            <td><?= e($request['business_name']) ?></td>
                            <td><span class="status"><?= e($request['status']) ?></span></td>
                            <td>NGN <?= number_format((float) $request['amount'], 2) ?></td>
                            <td><?= e($request['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
