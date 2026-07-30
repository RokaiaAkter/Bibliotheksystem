<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Repositories\TicketRepository;
use App\Services\TicketService;
use Throwable;

final class TicketController extends BaseController
{
    public function index(): void
    {
        Auth::requirePermission('tickets.view');
        $this->render('tickets', [
            'title' => 'Support-Tickets',
            'tickets' => (new TicketRepository($this->database))->all(),
        ]);
    }

    public function create(): void
    {
        Auth::requirePermission('tickets.create');

        try {
            $ticketId = (new TicketService($this->database))->create($_POST, (int) Auth::id());
            $this->audit('create', 'support_ticket', $ticketId, [
                'priority' => (string) ($_POST['priority'] ?? 'medium'),
                'external_provider' => isset($_POST['external_provider']),
            ]);
            $this->success('Das Support-Ticket wurde angelegt.', 'tickets');
        } catch (Throwable $exception) {
            $this->failure($exception, 'tickets');
        }
    }

    public function updateStatus(): void
    {
        Auth::requirePermission('tickets.manage');

        try {
            $ticketId = (int) ($_POST['ticket_id'] ?? 0);
            $status = (string) ($_POST['status'] ?? '');

            if ($ticketId < 1) {
                throw new \DomainException('Ungültige Ticket-ID.');
            }

            (new TicketService($this->database))->updateStatus($ticketId, $status);
            $this->audit('update_status', 'support_ticket', $ticketId, ['status' => $status]);
            $this->success('Der Ticketstatus wurde aktualisiert.', 'tickets');
        } catch (Throwable $exception) {
            $this->failure($exception, 'tickets');
        }
    }
}

