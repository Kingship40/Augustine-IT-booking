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
    $providersStmt = $db->prepare("
        SELECT id, full_name, email, phone 
        FROM users 
        WHERE role = 'provider'
        ORDER BY full_name ASC
    ");
    $providersStmt->execute();
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

    $profileStmt = $db->prepare('SELECT business_name, verification_status, availability_status FROM users WHERE id = :id LIMIT 1');
    $profileStmt->execute(['id' => $providerId]);
    $profile = $profileStmt->fetch() ?: [
        'business_name' => 'Not set',
        'verification_status' => 'pending',
        'availability_status' => 'available'
    ];

    return [
        'profile'                  => $profile,
        'service_count'            => 0,
        'job_count'                => 0,
        'pending_withdrawal_count' => 0,
        'recent_jobs'              => [],
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