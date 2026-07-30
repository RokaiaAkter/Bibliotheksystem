<?php

declare(strict_types=1);

namespace App\Repositories;

final class AuditRepository extends BaseRepository
{
    public function record(
        ?int $userId,
        string $action,
        string $entityType,
        ?int $entityId = null,
        array $details = []
    ): void {
        $statement = $this->database->prepare(
            'INSERT INTO audit_logs
                (user_id, action, entity_type, entity_id, details, ip_address)
             VALUES
                (:user_id, :action, :entity_type, :entity_id, :details, :ip_address)'
        );
        $statement->execute([
            'user_id' => $userId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'details' => json_encode($details, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'ip_address' => substr((string) ($_SERVER['REMOTE_ADDR'] ?? 'cli'), 0, 45),
        ]);
    }

    public function recent(int $limit = 12): array
    {
        $statement = $this->database->prepare(
            'SELECT
                audit_logs.action,
                audit_logs.entity_type,
                audit_logs.entity_id,
                audit_logs.details,
                audit_logs.created_at,
                users.name AS user_name
             FROM audit_logs
             LEFT JOIN users ON users.id = audit_logs.user_id
             ORDER BY audit_logs.created_at DESC
             LIMIT :limit'
        );
        $statement->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }
}
