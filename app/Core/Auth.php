<?php

declare(strict_types=1);

namespace App\Core;

use App\Repositories\UserRepository;

final class Auth
{
    /** @var array<string, list<string>> */
    private const ROLE_PERMISSIONS = [
        'admin' => [
            'dashboard.view',
            'books.view',
            'books.manage',
            'loans.manage',
            'users.manage',
            'phones.manage',
            'tickets.view',
            'tickets.create',
            'tickets.manage',
            'system.view',
        ],
        'librarian' => [
            'dashboard.view',
            'books.view',
            'books.manage',
            'loans.manage',
            'tickets.view',
            'tickets.create',
        ],
        'support' => [
            'dashboard.view',
            'books.view',
            'phones.manage',
            'tickets.view',
            'tickets.create',
            'tickets.manage',
            'system.view',
        ],
    ];

    public static function attempt(string $email, string $password): bool
    {
        $repository = new UserRepository(Database::connection());
        $user = $repository->findByEmail($email);

        if (!$user || !(bool) $user['active'] || !password_verify($password, $user['password_hash'])) {
            Logger::warning('Login failed', ['email' => $email]);

            return false;
        }

        if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
            $repository->updatePasswordHash((int) $user['id'], password_hash($password, PASSWORD_DEFAULT));
        }

        session_regenerate_id(true);
        unset($_SESSION['_csrf']);
        $_SESSION['user'] = [
            'id' => (int) $user['id'],
            'name' => (string) $user['name'],
            'email' => (string) $user['email'],
            'role' => (string) $user['role'],
        ];

        Logger::info('Login successful', ['user_id' => (int) $user['id']]);

        return true;
    }

    public static function logout(): void
    {
        $userId = self::id();
        unset($_SESSION['user']);
        session_regenerate_id(true);
        unset($_SESSION['_csrf']);
        Logger::info('Logout', ['user_id' => $userId]);
    }

    public static function check(): bool
    {
        return isset($_SESSION['user']['id']);
    }

    /** @return array{id:int,name:string,email:string,role:string}|null */
    public static function user(): ?array
    {
        return self::check() ? $_SESSION['user'] : null;
    }

    public static function id(): ?int
    {
        return self::check() ? (int) $_SESSION['user']['id'] : null;
    }

    public static function can(string $permission): bool
    {
        $role = (string) ($_SESSION['user']['role'] ?? '');

        return in_array($permission, self::ROLE_PERMISSIONS[$role] ?? [], true);
    }

    public static function requireLogin(): void
    {
        if (!self::validateSession()) {
            flash('error', 'Bitte melden Sie sich zuerst an.');
            redirect('login');
        }
    }

    public static function validateSession(): bool
    {
        if (!self::check()) {
            return false;
        }

        $repository = new UserRepository(Database::connection());
        $user = $repository->findById((int) $_SESSION['user']['id']);

        if (!$user || !(bool) $user['active']) {
            unset($_SESSION['user']);

            return false;
        }

        $_SESSION['user'] = [
            'id' => (int) $user['id'],
            'name' => (string) $user['name'],
            'email' => (string) $user['email'],
            'role' => (string) $user['role'],
        ];

        return true;
    }

    public static function requirePermission(string $permission): void
    {
        self::requireLogin();

        if (!self::can($permission)) {
            http_response_code(403);
            View::render('error', [
                'title' => 'Kein Zugriff',
                'code' => 403,
                'message' => 'Sie haben für diese Funktion keine Berechtigung.',
            ]);
            exit;
        }
    }
}
