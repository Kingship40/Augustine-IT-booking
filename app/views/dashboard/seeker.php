<?php

declare(strict_types=1);
?>
<section class="hero">
    <div class="hero-grid">
        <div>
            <span class="eyebrow">Seeker Dashboard</span>
            <h1>Welcome back, <?= e($user['full_name'] ?? 'Seeker') ?>.</h1>
            <p class="muted">This view is prepared for service requests, provider discovery, and review history.</p>
        </div>
        <div class="panel">
            <h3>Quick direction</h3>
            <ul class="list">
                <li>Browse registered platform service providers below</li>
                <li>Submit a targeted service request assignment</li>
                <li>Confirm job completion to close out active reviews</li>
            </ul>
        </div>
    </div>
</section>

<section class="grid cards">
    <div class="panel stat">
        <span class="muted">Requests Created</span>
        <strong><?= e((string) ($data['request_count'] ?? 0)) ?></strong>
    </div>
    <div class="panel stat">
        <span class="muted">Completed Requests</span>
        <strong><?= e((string) ($data['completed_count'] ?? 0)) ?></strong>
    </div>
</section>

<section class="panel" style="margin-top:24px;">
    <h2>Available IT Service Providers</h2>
    <?php if (empty($data['providers'])): ?>
        <div class="empty">No IT service providers are currently registered on the platform.</div>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Provider Name</th>
                        <th>Email Contact</th>
                        <th>Phone Number</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['providers'] as $provider): ?>
                        <tr>
                            <td><strong><?= e($provider['full_name'] ?? 'Unknown Provider') ?></strong></td>
                            <td><?= e($provider['email'] ?? 'N/A') ?></td>
                            <td><?= e($provider['phone'] ?? 'None shared') ?></td>
                            <td>
                                <span class="status" style="background:var(--brand-soft); color:var(--brand); cursor:pointer;">
                                    Request Service
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<section class="panel" style="margin-top:24px;">
    <h2>Recent requests</h2>
    <?php if (empty($data['recent_requests'])): ?>
        <div class="empty">No service requests yet. The next development phase will link provider buttons directly to request configuration flows.</div>
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
                            <td><?= e($request['subject'] ?? 'Service Request') ?></td>
                            <td><?= e($request['business_name'] ?? 'Unassigned') ?></td>
                            <td><span class="status"><?= e($request['status'] ?? 'pending') ?></span></td>
                            <td>NGN <?= number_format((float) ($request['amount'] ?? 0), 2) ?></td>
                            <td><?= e($request['created_at'] ?? 'N/A') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>