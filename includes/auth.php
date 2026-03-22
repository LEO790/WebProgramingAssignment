<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';

function auth_start_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function auth_is_logged_in(): bool
{
    auth_start_session();
    return isset($_SESSION['user_id']) && is_int($_SESSION['user_id']);
}

function auth_require_login(): void
{
    if (!auth_is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

/**
 * @return array{id:int,username:string,email:string,full_name:?string,role:string}|null
 */
function auth_current_user(): ?array
{
    if (!auth_is_logged_in()) {
        return null;
    }
    $stmt = db()->prepare(
        'SELECT id, username, email, full_name, role FROM users WHERE id = :id LIMIT 1'
    );
    $stmt->execute(['id' => $_SESSION['user_id']]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function auth_is_admin(): bool
{
    $user = auth_current_user();
    return $user !== null && ($user['role'] ?? 'user') === 'admin';
}

function auth_require_admin(): void
{
    auth_require_login();
    if (!auth_is_admin()) {
        header('Location: index.php');
        exit;
    }
}

/**
 * @return array{success:bool,message:string}
 */
function auth_register(string $username, string $email, string $password, ?string $fullName): array
{
    $username = trim($username);
    $email = trim(strtolower($email));
    $fullName = $fullName !== null ? trim($fullName) : null;
    if ($fullName === '') {
        $fullName = null;
    }

    if ($username === '' || strlen($username) > 50) {
        return ['success' => false, 'message' => 'Username is required (max 50 characters).'];
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'Please enter a valid email address.'];
    }
    if (strlen($password) < 8) {
        return ['success' => false, 'message' => 'Password must be at least 8 characters.'];
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    if ($hash === false) {
        return ['success' => false, 'message' => 'Could not process password. Try again.'];
    }

    try {
        $stmt = db()->prepare(
            'INSERT INTO users (username, email, password_hash, full_name, role) VALUES (:u, :e, :p, :f, :role)'
        );
        $stmt->execute([
            'u' => $username,
            'e' => $email,
            'p' => $hash,
            'f' => $fullName,
            'role' => 'user',
        ]);
    } catch (PDOException $e) {
        if ((int) $e->getCode() === 23000) {
            return ['success' => false, 'message' => 'That username or email is already registered.'];
        }
        return ['success' => false, 'message' => 'Registration failed. Please try again later.'];
    }

    return ['success' => true, 'message' => 'Account created. You can log in now.'];
}

/**
 * @return array{success:bool,message:string}
 */
function auth_login(string $identifier, string $password): array
{
    $identifier = trim($identifier);
    if ($identifier === '' || $password === '') {
        return ['success' => false, 'message' => 'Enter your username or email and password.'];
    }

    $stmt = db()->prepare(
        'SELECT id, password_hash, role FROM users WHERE LOWER(username) = LOWER(:u) OR email = :e LIMIT 1'
    );
    $stmt->execute(['u' => $identifier, 'e' => strtolower($identifier)]);
    $row = $stmt->fetch();
    if (!$row || !password_verify($password, $row['password_hash'])) {
        return ['success' => false, 'message' => 'Invalid login details.'];
    }

    auth_start_session();
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $row['id'];
    $_SESSION['role'] = (string) ($row['role'] ?? 'user');

    return ['success' => true, 'message' => 'Welcome back.'];
}

function auth_logout(): void
{
    auth_start_session();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

/**
 * @return array{success:bool,message:string}
 */
function auth_update_profile(int $userId, ?string $fullName, ?string $newPassword, string $currentPassword): array
{
    $stmt = db()->prepare('SELECT password_hash FROM users WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $userId]);
    $row = $stmt->fetch();
    if (!$row || !password_verify($currentPassword, $row['password_hash'])) {
        return ['success' => false, 'message' => 'Current password is incorrect.'];
    }

    $fullName = $fullName !== null ? trim($fullName) : null;
    if ($fullName === '') {
        $fullName = null;
    }

    if ($newPassword !== null && $newPassword !== '') {
        if (strlen($newPassword) < 8) {
            return ['success' => false, 'message' => 'New password must be at least 8 characters.'];
        }
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        if ($hash === false) {
            return ['success' => false, 'message' => 'Could not update password.'];
        }
        $upd = db()->prepare(
            'UPDATE users SET full_name = :f, password_hash = :p WHERE id = :id'
        );
        $upd->execute(['f' => $fullName, 'p' => $hash, 'id' => $userId]);
    } else {
        $upd = db()->prepare('UPDATE users SET full_name = :f WHERE id = :id');
        $upd->execute(['f' => $fullName, 'id' => $userId]);
    }

    return ['success' => true, 'message' => 'Profile updated.'];
}
