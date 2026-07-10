<?php

declare(strict_types=1);

function get_seeker_dashboard_data(int $seekerId): array
{
    $db = db();

    // 1. Count Total Requests Created by Seeker
    $reqCountStmt = $db->prepare('SELECT COUNT(*) as count FROM service_requests WHERE seeker_id = :seeker_id');
    $reqCountStmt->execute(['seeker_id' => $seekerId]);
    $requestCount = (int) ($reqCountStmt->fetch()['count'] ?? 0);

    // 2. Count Completed Requests
    $compCountStmt = $db->prepare("SELECT COUNT(*) as count FROM service_requests WHERE seeker_id = :seeker_id AND status = 'completed'");
    $compCountStmt->execute(['seeker_id' => $seekerId]);
    $completedCount = (int) ($compCountStmt->fetch()['count'] ?? 0);

    // 3. Fetch Recent Requests
    $recentStmt = $db->prepare("
        SELECT r.*, u.full_name as business_name 
        FROM service_requests r
        LEFT JOIN users u ON r.provider_id = u.id
        WHERE r.seeker_id = :seeker_id
        ORDER BY r.created_at DESC 
        LIMIT 5
    ");
    $recentStmt->execute(['seeker_id' => $seekerId]);
    $recentRequests = $recentStmt->fetchAll() ?: [];

    // 4. Fetch All Live Platform Providers for Discovery List
    $providersStmt = $db->prepare(
        'SELECT u.id, u.full_name, u.email, u.phone, '
        . 'COALESCE(p.business_name, u.full_name) AS business_name, '
        . 'COALESCE(p.service_category, p.service_other_text, \'Not set\') AS service_category '
        . 'FROM users u '
        . 'LEFT JOIN provider_profiles p ON p.user_id = u.id '
        . 'WHERE u.role = :role '
        . 'ORDER BY u.full_name ASC'
    );
    $providersStmt->execute(['role' => 'provider']);
    $providers = $providersStmt->fetchAll() ?: [];

    return [
        'request_count'   => $requestCount,
        'completed_count' => $completedCount,
        'recent_requests' => $recentRequests,
        'providers'       => $providers,
    ];
}

function get_provider_dashboard_data(int $providerId): array
{
    $db = db();

    // 1. Fetch profile business name only
    $profileStmt = $db->prepare('SELECT business_name FROM provider_profiles WHERE user_id = :id LIMIT 1');
    $profileStmt->execute(['id' => $providerId]);
    $profile = $profileStmt->fetch() ?: [
        'business_name' => 'Not set'
    ];

    // 2. Dynamically Count total requests/services assigned to this provider
    $serviceCountStmt = $db->prepare('SELECT COUNT(*) as count FROM service_requests WHERE provider_id = :provider_id');
    $serviceCountStmt->execute(['provider_id' => $providerId]);
    $serviceCount = (int) ($serviceCountStmt->fetch()['count'] ?? 0);

    // 3. Count total active jobs or total completed transactions
    $jobCountStmt = $db->prepare("SELECT COUNT(*) as count FROM service_requests WHERE provider_id = :provider_id AND status = 'completed'");
    $jobCountStmt->execute(['provider_id' => $providerId]);
    $jobCount = (int) ($jobCountStmt->fetch()['count'] ?? 0);

    $recentJobs = [];

    return [
        'profile'                  => $profile,
        'service_count'            => $serviceCount,
        'job_count'                => $jobCount,
        'pending_withdrawal_count' => 0,
        'recent_jobs'              => $recentJobs,
    ];
}

function get_admin_dashboard_data(): array
{
    return [
        'seeker_count'             => 0,
        'provider_count'           => 0,
        'pending_verifications'    => 0,
        'pending_withdrawals'      => 0,
        'recent_users'             => [],
    ];
}