<?php

declare(strict_types=1);

namespace App\Repositories;

final class PhoneRepository extends BaseRepository
{
    public function all(): array
    {
        return $this->database
            ->query(
                'SELECT id, employee_name, department, extension, active, created_at
                 FROM telephone_extensions
                 ORDER BY department, employee_name'
            )
            ->fetchAll();
    }

    public function create(array $data): int
    {
        $statement = $this->database->prepare(
            'INSERT INTO telephone_extensions (employee_name, department, extension, active)
             VALUES (:employee_name, :department, :extension, 1)'
        );
        $statement->execute([
            'employee_name' => $data['employee_name'],
            'department' => $data['department'],
            'extension' => $data['extension'],
        ]);

        return (int) $this->database->lastInsertId();
    }

    public function toggleActive(int $id): bool
    {
        $statement = $this->database->prepare(
            'UPDATE telephone_extensions SET active = NOT active WHERE id = :id'
        );
        $statement->execute(['id' => $id]);

        return $statement->rowCount() === 1;
    }

    public function extensionExists(string $extension): bool
    {
        $statement = $this->database->prepare(
            'SELECT COUNT(*) FROM telephone_extensions WHERE extension = :extension'
        );
        $statement->execute(['extension' => $extension]);

        return (int) $statement->fetchColumn() > 0;
    }

    public function countActive(): int
    {
        return (int) $this->database
            ->query('SELECT COUNT(*) FROM telephone_extensions WHERE active = 1')
            ->fetchColumn();
    }
}

