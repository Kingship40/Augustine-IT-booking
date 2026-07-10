<?php

declare(strict_types=1);

function provider_service_categories(): array
{
    return [
        'Web Development',
        'Mobile App Development (Android & iOS)',
        'UI/UX Design',
        'Software Development',
        'Database Design & Management',
        'Cloud Computing Solutions',
        'Cybersecurity Services',
        'Network Setup & Administration',
        'IT Support & Help Desk',
        'Computer Repairs & Maintenance',
        'Server Installation & Management',
        'API Development & Integration',
        'E-commerce Website Development',
        'AI & Machine Learning Solutions',
        'Data Analytics & Business Intelligence',
        'Digital Transformation Consulting',
        'IT Training & Certification',
        'DevOps & CI/CD Implementation',
        'System Integration',
        'Software Testing & Quality Assurance',
        'SEO & Digital Marketing',
        'Graphic Design & Branding',
        'Domain Registration & Web Hosting',
        'Backup & Disaster Recovery Solutions',
    ];
}

function ensure_provider_profile_columns(): void
{
    $db = db();
    $columns = $db->query('SHOW COLUMNS FROM provider_profiles')->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array('service_category', $columns, true)) {
        $db->exec('ALTER TABLE provider_profiles ADD COLUMN service_category VARCHAR(150) NULL');
    }

    if (!in_array('service_other_text', $columns, true)) {
        $db->exec('ALTER TABLE provider_profiles ADD COLUMN service_other_text VARCHAR(150) NULL');
    }
}

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
    ensure_provider_profile_columns();
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
            $serviceCategory = trim((string) ($data['service_category'] ?? ''));
            $serviceOtherText = trim((string) ($data['service_other_text'] ?? ''));
            $resolvedServiceCategory = $serviceOtherText !== '' ? $serviceOtherText : $serviceCategory;

            $skillsValue = trim((string) ($data['skills'] ?? ''));
            if ($resolvedServiceCategory !== '') {
                $skillsValue = $skillsValue === '' ? $resolvedServiceCategory : $skillsValue . ' | ' . $resolvedServiceCategory;
            }

            $profileStatement = $pdo->prepare(
                'INSERT INTO provider_profiles (user_id, business_name, bio, skills, years_experience, availability_status, verification_status, service_category, service_other_text)
                 VALUES (:user_id, :business_name, :bio, :skills, :years_experience, :availability_status, :verification_status, :service_category, :service_other_text)'
            );
            $profileStatement->execute([
                'user_id' => $userId,
                'business_name' => $data['business_name'],
                'bio' => $data['bio'] ?: null,
                'skills' => $skillsValue !== '' ? $skillsValue : null,
                'years_experience' => $data['years_experience'] !== '' ? (int) $data['years_experience'] : null,
                'availability_status' => 'available',
                'verification_status' => 'pending',
                'service_category' => $resolvedServiceCategory !== '' ? $resolvedServiceCategory : null,
                'service_other_text' => $serviceOtherText !== '' ? $serviceOtherText : null,
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
