<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Config;

final class SystemController extends BaseController
{
    public function index(): void
    {
        Auth::requirePermission('system.view');

        $databaseStatus = 'OK';

        try {
            $this->database->query('SELECT 1')->fetchColumn();
        } catch (\Throwable) {
            $databaseStatus = 'FEHLER';
        }

        $logFile = APP_ROOT . '/storage/logs/app.log';
        $logLines = [];

        if (is_readable($logFile)) {
            $allLines = file($logFile, FILE_IGNORE_NEW_LINES) ?: [];
            $logLines = array_reverse(array_slice($allLines, -40));
        }

        $this->render('system', [
            'title' => 'Systemstatus und Logs',
            'health' => [
                'application' => 'OK',
                'database' => $databaseStatus,
                'php_version' => PHP_VERSION,
                'environment' => (string) Config::get('APP_ENV', 'unknown'),
                'log_writable' => is_writable(APP_ROOT . '/storage/logs') ? 'Ja' : 'Nein',
                'free_space' => $this->formatBytes((int) disk_free_space(APP_ROOT)),
                'server_time' => date('d.m.Y H:i:s T'),
            ],
            'logLines' => $logLines,
            'recentAudits' => $this->audit->recent(20),
        ]);
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $index = 0;
        $value = (float) $bytes;

        while ($value >= 1024 && $index < count($units) - 1) {
            $value /= 1024;
            $index++;
        }

        return number_format($value, 1, ',', '.') . ' ' . $units[$index];
    }
}

