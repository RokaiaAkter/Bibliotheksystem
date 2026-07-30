<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\UserRepository;
use DomainException;
use PDO;

final class UserService
{
    private const ROLES = ['admin', 'librarian', 'support'];

    private UserRepository $users;

    public function __construct(PDO $database)
    {
        $this->users = new UserRepository($database);
    }

    public function create(array $input): int
    {
        $name = trim((string) ($input['name'] ?? ''));
        $email = strtolower(trim((string) ($input['email'] ?? '')));
        $password = (string) ($input['password'] ?? '');
        $role = (string) ($input['role'] ?? '');

        if ($name === '' || strlen($name) > 120) {
            throw new DomainException('Bitte geben Sie einen gültigen Namen ein.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new DomainException('Bitte geben Sie eine gültige E-Mail-Adresse ein.');
        }

        if (strlen($password) < 10) {
            throw new DomainException('Das Passwort muss mindestens 10 Zeichen lang sein.');
        }

        if (!in_array($role, self::ROLES, true)) {
            throw new DomainException('Die gewählte Rolle ist ungültig.');
        }

        if ($this->users->emailExists($email)) {
            throw new DomainException('Diese E-Mail-Adresse ist bereits vorhanden.');
        }

        return $this->users->create([
            'name' => $name,
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'role' => $role,
        ]);
    }
}

