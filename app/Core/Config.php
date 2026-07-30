<?php

declare(strict_types=1);

namespace App\Core;

final class Config
{
    /** @var array<string, string> */
    private static array $values = [];

    public static function load(string $rootPath): void
    {
        $envFile = $rootPath . '/.env';

        if (is_readable($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];

            foreach ($lines as $line) {
                $line = trim($line);

                if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                    continue;
                }

                [$key, $value] = array_map('trim', explode('=', $line, 2));
                $value = trim($value, "\"'");

                if ($key !== '' && getenv($key) === false) {
                    self::$values[$key] = $value;
                }
            }
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $environmentValue = getenv($key);

        if ($environmentValue !== false) {
            return $environmentValue;
        }

        return self::$values[$key] ?? $default;
    }

    public static function bool(string $key, bool $default = false): bool
    {
        $value = self::get($key);

        if ($value === null) {
            return $default;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}

