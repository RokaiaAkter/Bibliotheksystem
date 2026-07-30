<?php

declare(strict_types=1);

namespace App\Core;

final class Csrf
{
    public static function token(): string
    {
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }

        return (string) $_SESSION['_csrf'];
    }

    public static function field(): string
    {
        return '<input type="hidden" name="_csrf" value="' .
            htmlspecialchars(self::token(), ENT_QUOTES, 'UTF-8') .
            '">';
    }

    public static function validate(?string $submittedToken): bool
    {
        $storedToken = $_SESSION['_csrf'] ?? '';

        return is_string($submittedToken)
            && is_string($storedToken)
            && $storedToken !== ''
            && hash_equals($storedToken, $submittedToken);
    }
}

