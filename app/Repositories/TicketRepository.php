<?php

declare(strict_types=1);

namespace App\Repositories;

final class TicketRepository extends BaseRepository
{
    public function all(): array
    {
        return $this->database
            ->query(
                'SELECT
                    support_tickets.id,
                    support_tickets.title,
                    support_tickets.description,
                    support_tickets.priority,
                    support_tickets.status,
                    support_tickets.external_provider,
                    support_tickets.created_at,
                    support_tickets.updated_at,
                    users.name AS created_by_name
                 FROM support_tickets
                 LEFT JOIN users ON users.id = support_tickets.created_by
                 ORDER BY
                    FIELD(support_tickets.status, "open", "in_progress", "waiting_provider", "resolved"),
                    FIELD(support_tickets.priority, "critical", "high", "medium", "low"),
                    support_tickets.created_at DESC'
            )
            ->fetchAll();
    }

    public function create(array $data): int
    {
        $statement = $this->database->prepare(
            'INSERT INTO support_tickets
                (title, description, priority, status, external_provider, created_by)
             VALUES
                (:title, :description, :priority, :status, :external_provider, :created_by)'
        );
        $statement->execute([
            'title' => $data['title'],
            'description' => $data['description'],
            'priority' => $data['priority'],
            'status' => $data['external_provider'] ? 'waiting_provider' : 'open',
            'external_provider' => $data['external_provider'] ? 1 : 0,
            'created_by' => $data['created_by'],
        ]);

        return (int) $this->database->lastInsertId();
    }

    public function updateStatus(int $id, string $status): bool
    {
        $statement = $this->database->prepare(
            'UPDATE support_tickets SET status = :status WHERE id = :id'
        );
        $statement->execute(['status' => $status, 'id' => $id]);

        return $statement->rowCount() === 1;
    }

    public function counts(): array
    {
        $rows = $this->database
            ->query('SELECT status, COUNT(*) AS total FROM support_tickets GROUP BY status')
            ->fetchAll();
        $counts = [
            'open' => 0,
            'in_progress' => 0,
            'waiting_provider' => 0,
            'resolved' => 0,
        ];

        foreach ($rows as $row) {
            $counts[(string) $row['status']] = (int) $row['total'];
        }

        $counts['active'] = $counts['open'] + $counts['in_progress'] + $counts['waiting_provider'];

        return $counts;
    }
}

