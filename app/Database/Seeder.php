<?php

declare(strict_types=1);

namespace App\Database;

use PDO;

final class Seeder
{
    public static function run(PDO $database): void
    {
        $userCount = (int) $database->query('SELECT COUNT(*) FROM users')->fetchColumn();

        if ($userCount > 0) {
            return;
        }

        $statement = $database->prepare(
            'INSERT INTO users (name, email, password_hash, role, active)
             VALUES (:name, :email, :password_hash, :role, 1)'
        );

        $demoUsers = [
            ['System Admin', 'admin@bibliothek.local', 'Admin123!', 'admin'],
            ['Bibliothek Team', 'staff@bibliothek.local', 'Staff123!', 'librarian'],
            ['IT Support', 'support@bibliothek.local', 'Support123!', 'support'],
        ];

        foreach ($demoUsers as [$name, $email, $password, $role]) {
            $statement->execute([
                'name' => $name,
                'email' => $email,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'role' => $role,
            ]);
        }
    }
}

