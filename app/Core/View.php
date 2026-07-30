<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

final class View
{
    public static function render(string $template, array $data = [], bool $withLayout = true): void
    {
        $templatePath = APP_ROOT . '/views/' . $template . '.php';

        if (!is_file($templatePath)) {
            throw new RuntimeException("View not found: {$template}");
        }

        extract($data, EXTR_SKIP);
        ob_start();
        require $templatePath;
        $content = (string) ob_get_clean();

        if (!$withLayout) {
            echo $content;

            return;
        }

        require APP_ROOT . '/views/layout.php';
    }
}

