<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Repositories\PhoneRepository;
use Throwable;

final class PhoneController extends BaseController
{
    public function index(): void
    {
        Auth::requirePermission('phones.manage');
        $this->render('phones', [
            'title' => 'Telefon-Backend',
            'phones' => (new PhoneRepository($this->database))->all(),
        ]);
    }

    public function create(): void
    {
        Auth::requirePermission('phones.manage');

        try {
            $employeeName = trim((string) ($_POST['employee_name'] ?? ''));
            $department = trim((string) ($_POST['department'] ?? ''));
            $extension = trim((string) ($_POST['extension'] ?? ''));

            if (
                $employeeName === ''
                || $department === ''
                || !preg_match('/^[0-9]{3,8}$/', $extension)
            ) {
                throw new \DomainException('Name, Abteilung und eine 3- bis 8-stellige Durchwahl sind erforderlich.');
            }

            $repository = new PhoneRepository($this->database);

            if ($repository->extensionExists($extension)) {
                throw new \DomainException('Diese Durchwahl ist bereits vergeben.');
            }

            $phoneId = $repository->create([
                'employee_name' => substr($employeeName, 0, 120),
                'department' => substr($department, 0, 120),
                'extension' => $extension,
            ]);
            $this->audit('create', 'telephone_extension', $phoneId, ['extension' => $extension]);
            $this->success('Die Durchwahl wurde angelegt.', 'phones');
        } catch (Throwable $exception) {
            $this->failure($exception, 'phones');
        }
    }

    public function toggle(): void
    {
        Auth::requirePermission('phones.manage');

        try {
            $phoneId = (int) ($_POST['phone_id'] ?? 0);

            if ($phoneId < 1 || !(new PhoneRepository($this->database))->toggleActive($phoneId)) {
                throw new \DomainException('Die Durchwahl wurde nicht gefunden.');
            }

            $this->audit('toggle_active', 'telephone_extension', $phoneId);
            $this->success('Der Status der Durchwahl wurde geändert.', 'phones');
        } catch (Throwable $exception) {
            $this->failure($exception, 'phones');
        }
    }
}

