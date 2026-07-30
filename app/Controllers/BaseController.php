<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Logger;
use App\Core\View;
use App\Repositories\AuditRepository;
use PDO;
use Throwable;

abstract class BaseController
{
    protected readonly PDO $database;
    protected readonly AuditRepository $audit;

    public function __construct()
    {
        $this->database = Database::connection();
        $this->audit = new AuditRepository($this->database);
    }

    protected function render(string $template, array $data = []): void
    {
        View::render($template, $data);
    }

    protected function success(string $message, string $route): never
    {
        flash('success', $message);
        redirect($route);
    }

    protected function failure(Throwable $exception, string $route): never
    {
        Logger::exception($exception);
        $message = $exception instanceof \DomainException
            ? $exception->getMessage()
            : 'Die Aktion konnte nicht abgeschlossen werden. Bitte prüfen Sie das Log.';
        flash('error', $message);
        redirect($route);
    }

    protected function audit(
        string $action,
        string $entityType,
        ?int $entityId = null,
        array $details = []
    ): void {
        $this->audit->record(Auth::id(), $action, $entityType, $entityId, $details);
    }
}

