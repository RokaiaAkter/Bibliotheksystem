<?php

declare(strict_types=1);

namespace App\Core;

use Throwable;

final class Logger
{
    public static function info(string $message, array $context = []): void
    {
        self::write('INFO', $message, $context);
    }

    public static function warning(string $message, array $context = []): void
    {
        self::write('WARNING', $message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        self::write('ERROR', $message, $context);
    }

    public static function exception(Throwable $exception): void
    {
        self::error('Unhandled exception', [
            'type' => $exception::class,
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ]);
    }

    private static function write(string $level, string $message, array $context): void
    {
        $logDirectory = defined('APP_ROOT')
            ? APP_ROOT . '/storage/logs'
            : sys_get_temp_dir();
        $logFile = $logDirectory . '/app.log';

        if (!is_dir($logDirectory)) {
            @mkdir($logDirectory, 0750, true);
        }

        $entry = [
            'time' => date(DATE_ATOM),
            'level' => $level,
            'message' => $message,
            'context' => self::sanitize($context),
        ];

        $line = json_encode(
            $entry,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE
        ) . PHP_EOL;

        if (@file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX) === false) {
            error_log($line);
        }
    }

    private static function sanitize(array $context): array
    {
        $sensitiveKeys = ['password', 'token', 'secret', 'authorization', 'cookie'];

        foreach ($context as $key => $value) {
            if (in_array(strtolower((string) $key), $sensitiveKeys, true)) {
                $context[$key] = '[redacted]';
            }
        }

        return $context;
    }
}

