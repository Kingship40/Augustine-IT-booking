<?php

declare(strict_types=1);

function get_seeker_dashboard_data(int $userId): array
{
    $wallet = get_wallet_by_user_id($userId);

    $requestCount = scalar_count(
        'SELECT COUNT(*) FROM service_requests sr
         INNER JOIN seeker_profiles sp ON sp.id = sr.seeker_id
         WHERE sp.user_id = :user_id',
        ['user_id' => $userId]
    );

    $completedCount = scalar_count(
        'SELECT COUNT(*) FROM service_requests sr
         INNER JOIN seeker_profiles sp ON sp.id = sr.seeker_id
         WHERE sp.user_id = :user_id AND sr.status = :status',
        ['user_id' => $userId, 'status' => 'completed']
    );

    $recentRequests = fetch_all(
        'SELECT sr.subject, sr.status, sr.amount, sr.created_at, pp.business_name
         FROM service_requests sr
         INNER JOIN seeker_profiles sp ON sp.id = sr.seeker_id
         INNER JOIN provider_profiles pp ON pp.id = sr.provider_id
         WHERE sp.user_id = :user_id
         ORDER BY sr.created_at DESC
         LIMIT 5',
        ['user_id' => $userId]
    );

    return [
        'wallet' => $wallet,
        'request_count' => $requestCount,
        'completed_count' => $completedCount,
        'recent_requests' => $recentRequests,
    ];
}

function get_provider_dashboard_data(int $userId): array
{
    $wallet = get_wallet_by_user_id($userId);
    $profile = get_provider_profile_by_user_id($userId);

    $serviceCount = scalar_count(
        'SELECT COUNT(*) FROM services s
         INNER JOIN provider_profiles pp ON pp.id = s.provider_id
         WHERE pp.user_id = :user_id',
        ['user_id' => $userId]
    );

    $jobCount = scalar_count(
        'SELECT COUNT(*) FROM service_requests sr
         INNER JOIN provider_profiles pp ON pp.id = sr.provider_id
         WHERE pp.user_id = :user_id',
        ['user_id' => $userId]
    );

    $pendingWithdrawalCount = scalar_count(
        'SELECT COUNT(*) FROM payout_requests pr
         INNER JOIN provider_profiles pp ON pp.id = pr.provider_id
         WHERE pp.user_id = :user_id AND pr.status = :status',
        ['user_id' => $userId, 'status' => 'pending']
    );

    $recentJobs = fetch_all(
        'SELECT sr.subject, sr.status, sr.amount, sr.created_at, u.full_name AS seeker_name
         FROM service_requests sr
         INNER JOIN provider_profiles pp ON pp.id = sr.provider_id
         INNER JOIN seeker_profiles sp ON sp.id = sr.seeker_id
         INNER JOIN users u ON u.id = sp.user_id
         WHERE pp.user_id = :user_id
         ORDER BY sr.created_at DESC
         LIMIT 5',
        ['user_id' => $userId]
    );

    return [
        'wallet' => $wallet,
        'profile' => $profile,
        'service_count' => $serviceCount,
        'job_count' => $jobCount,
        'pending_withdrawal_count' => $pendingWithdrawalCount,
        'recent_jobs' => $recentJobs,
    ];
}

function get_admin_dashboard_data(): array
{
    return [
        'total_users' => scalar_count('SELECT COUNT(*) FROM users'),
        'total_seekers' => scalar_count('SELECT COUNT(*) FROM users WHERE role = :role', ['role' => 'seeker']),
        'total_providers' => scalar_count('SELECT COUNT(*) FROM users WHERE role = :role', ['role' => 'provider']),
        'pending_providers' => scalar_count('SELECT COUNT(*) FROM provider_profiles WHERE verification_status = :status', ['status' => 'pending']),
        'total_requests' => scalar_count('SELECT COUNT(*) FROM service_requests'),
        'pending_withdrawals' => scalar_count('SELECT COUNT(*) FROM payout_requests WHERE status = :status', ['status' => 'pending']),
        'total_reviews' => scalar_count('SELECT COUNT(*) FROM reviews'),
        'recent_users' => fetch_all(
            'SELECT full_name, email, role, status, created_at
             FROM users
             ORDER BY created_at DESC
             LIMIT 6'
        ),
    ];
}

function scalar_count(string $sql, array $params = []): int
{
    $statement = db()->prepare($sql);
    $statement->execute($params);

    return (int) $statement->fetchColumn();
}

function fetch_all(string $sql, array $params = []): array
{
    $statement = db()->prepare($sql);
    $statement->execute($params);

    return $statement->fetchAll();
}
