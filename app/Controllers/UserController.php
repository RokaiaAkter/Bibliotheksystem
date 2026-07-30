<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Repositories\UserRepository;
use App\Services\UserService;
use Throwable;

final class UserController extends BaseController
{
    public function index(): void
    {
        Auth::requirePermission('users.manage');
        $this->render('users', [
            'title' => 'Benutzer und Rollen',
            'users' => (new UserRepository($this->database))->all(),
        ]);
    }

    public function create(): void
    {
        Auth::requirePermission('users.manage');

        try {
            $userId = (new UserService($this->database))->create($_POST);
            $this->audit('create', 'user', $userId, ['role' => (string) ($_POST['role'] ?? '')]);
            $this->success('Der Benutzer wurde angelegt.', 'users');
        } catch (Throwable $exception) {
            $this->failure($exception, 'users');
        }
    }

    public function toggle(): void
    {
        Auth::requirePermission('users.manage');

        try {
            $userId = (int) ($_POST['user_id'] ?? 0);

            if ($userId < 1 || $userId === Auth::id()) {
                throw new \DomainException('Sie können Ihr eigenes Konto hier nicht deaktivieren.');
            }

            $repository = new UserRepository($this->database);
            $targetUser = $repository->findById($userId);

            if (!$targetUser) {
                throw new \DomainException('Der Benutzer wurde nicht gefunden.');
            }

            if (
                $targetUser['role'] === 'admin'
                && (bool) $targetUser['active']
                && $repository->countActiveByRole('admin') <= 1
            ) {
                throw new \DomainException('Das letzte aktive Admin-Konto darf nicht deaktiviert werden.');
            }

            $repository->toggleActive($userId);
            $this->audit('toggle_active', 'user', $userId);
            $this->success('Der Kontostatus wurde geändert.', 'users');
        } catch (Throwable $exception) {
            $this->failure($exception, 'users');
        }
    }
}
