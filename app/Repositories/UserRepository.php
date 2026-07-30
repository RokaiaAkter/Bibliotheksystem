<?php

declare(strict_types=1);

namespace App\Repositories;

final class UserRepository extends BaseRepository
{
    public function findById(int $id): ?array
    {
        $statement = $this->database->prepare(
            'SELECT id, name, email, role, active
             FROM users
             WHERE id = :id
             LIMIT 1'
        );
        $statement->execute(['id' => $id]);
        $user = $statement->fetch();

        return $user ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $statement = $this->database->prepare(
            'SELECT id, name, email, password_hash, role, active
             FROM users
             WHERE email = :email
             LIMIT 1'
        );
        $statement->execute(['email' => strtolower(trim($email))]);
        $user = $statement->fetch();

        return $user ?: null;
    }

    public function all(): array
    {
        return $this->database
            ->query(
                'SELECT id, name, email, role, active, created_at
                 FROM users
                 ORDER BY name'
            )
            ->fetchAll();
    }

    public function create(array $data): int
    {
        $statement = $this->database->prepare(
            'INSERT INTO users (name, email, password_hash, role, active)
             VALUES (:name, :email, :password_hash, :role, 1)'
        );
        $statement->execute([
            'name' => $data['name'],
            'email' => strtolower($data['email']),
            'password_hash' => $data['password_hash'],
            'role' => $data['role'],
        ]);

        return (int) $this->database->lastInsertId();
    }

    public function toggleActive(int $id): bool
    {
        $statement = $this->database->prepare(
            'UPDATE users SET active = NOT active WHERE id = :id'
        );
        $statement->execute(['id' => $id]);

        return $statement->rowCount() === 1;
    }

    public function updatePasswordHash(int $id, string $passwordHash): void
    {
        $statement = $this->database->prepare(
            'UPDATE users SET password_hash = :password_hash WHERE id = :id'
        );
        $statement->execute(['password_hash' => $passwordHash, 'id' => $id]);
    }

    public function emailExists(string $email): bool
    {
        $statement = $this->database->prepare(
            'SELECT COUNT(*) FROM users WHERE email = :email'
        );
        $statement->execute(['email' => strtolower(trim($email))]);

        return (int) $statement->fetchColumn() > 0;
    }

    public function countActive(): int
    {
        return (int) $this->database
            ->query('SELECT COUNT(*) FROM users WHERE active = 1')
            ->fetchColumn();
    }

    public function countActiveByRole(string $role): int
    {
        $statement = $this->database->prepare(
            'SELECT COUNT(*) FROM users WHERE role = :role AND active = 1'
        );
        $statement->execute(['role' => $role]);

        return (int) $statement->fetchColumn();
    }
}
