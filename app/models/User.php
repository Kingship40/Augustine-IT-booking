<?php

declare(strict_types=1);

function find_user_by_email(string $email): ?array
{
    $statement = db()->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
    $statement->execute(['email' => $email]);
    $user = $statement->fetch();

    return $user ?: null;
}

function find_user_by_id(int $id): ?array
{
    $statement = db()->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
    $statement->execute(['id' => $id]);
    $user = $statement->fetch();

    return $user ?: null;
}

function register_user(array $data): array
{
    $pdo = db();
    $pdo->beginTransaction();

    try {
        $statement = $pdo->prepare(
            'INSERT INTO users (full_name, email, phone, password_hash, role, status)
             VALUES (:full_name, :email, :phone, :password_hash, :role, :status)'
        );

        $statement->execute([
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?: null,
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role' => $data['role'],
            'status' => $data['role'] === 'provider' ? 'pending_verification' : 'active',
        ]);

        $userId = (int) $pdo->lastInsertId();

        $walletStatement = $pdo->prepare(
            'INSERT INTO wallets (user_id, balance, escrow_balance, total_earned, total_withdrawn)
             VALUES (:user_id, 0, 0, 0, 0)'
        );
        $walletStatement->execute(['user_id' => $userId]);

        if ($data['role'] === 'seeker') {
            $profileStatement = $pdo->prepare(
                'INSERT INTO seeker_profiles (user_id, address, city, state)
                 VALUES (:user_id, :address, :city, :state)'
            );
            $profileStatement->execute([
                'user_id' => $userId,
                'address' => null,
                'city' => null,
                'state' => null,
            ]);
        }

        if ($data['role'] === 'provider') {
            $profileStatement = $pdo->prepare(
                'INSERT INTO provider_profiles (user_id, business_name, bio, skills, years_experience, availability_status, verification_status)
                 VALUES (:user_id, :business_name, :bio, :skills, :years_experience, :availability_status, :verification_status)'
            );
            $profileStatement->execute([
                'user_id' => $userId,
                'business_name' => $data['business_name'],
                'bio' => $data['bio'] ?: null,
                'skills' => $data['skills'] ?: null,
                'years_experience' => $data['years_experience'] !== '' ? (int) $data['years_experience'] : null,
                'availability_status' => 'available',
                'verification_status' => 'pending',
            ]);
        }

        $pdo->commit();

        return find_user_by_id($userId) ?? [];
    } catch (Throwable $exception) {
        $pdo->rollBack();
        throw $exception;
    }
}

function authenticate_user(string $email, string $password): ?array
{
    $user = find_user_by_email($email);

    if (!$user) {
        return null;
    }

    if (!password_verify($password, $user['password_hash'])) {
        return null;
    }

    return $user;
}

function get_provider_profile_by_user_id(int $userId): ?array
{
    $statement = db()->prepare('SELECT * FROM provider_profiles WHERE user_id = :user_id LIMIT 1');
    $statement->execute(['user_id' => $userId]);
    $profile = $statement->fetch();

    return $profile ?: null;
}

function get_wallet_by_user_id(int $userId): ?array
{
    $statement = db()->prepare('SELECT * FROM wallets WHERE user_id = :user_id LIMIT 1');
    $statement->execute(['user_id' => $userId]);
    $wallet = $statement->fetch();

    return $wallet ?: null;
}
