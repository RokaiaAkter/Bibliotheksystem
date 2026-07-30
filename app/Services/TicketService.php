<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\TicketRepository;
use DomainException;
use PDO;

final class TicketService
{
    private const PRIORITIES = ['low', 'medium', 'high', 'critical'];
    private const STATUSES = ['open', 'in_progress', 'waiting_provider', 'resolved'];

    private TicketRepository $tickets;

    public function __construct(PDO $database)
    {
        $this->tickets = new TicketRepository($database);
    }

    public function create(array $input, int $createdBy): int
    {
        $title = trim((string) ($input['title'] ?? ''));
        $description = trim((string) ($input['description'] ?? ''));
        $priority = (string) ($input['priority'] ?? 'medium');
        $externalProvider = isset($input['external_provider']);

        if ($title === '' || strlen($title) > 180) {
            throw new DomainException('Bitte geben Sie einen kurzen, klaren Titel ein.');
        }

        if (strlen($description) < 10 || strlen($description) > 4000) {
            throw new DomainException('Die Beschreibung muss zwischen 10 und 4000 Zeichen lang sein.');
        }

        if (!in_array($priority, self::PRIORITIES, true)) {
            throw new DomainException('Die Priorität ist ungültig.');
        }

        return $this->tickets->create([
            'title' => $title,
            'description' => $description,
            'priority' => $priority,
            'external_provider' => $externalProvider,
            'created_by' => $createdBy,
        ]);
    }

    public function updateStatus(int $id, string $status): void
    {
        if (!in_array($status, self::STATUSES, true)) {
            throw new DomainException('Der Status ist ungültig.');
        }

        if (!$this->tickets->updateStatus($id, $status)) {
            throw new DomainException('Das Ticket wurde nicht gefunden oder nicht verändert.');
        }
    }
}
