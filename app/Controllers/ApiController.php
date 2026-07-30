<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Repositories\BookRepository;

final class ApiController extends BaseController
{
    public function books(): void
    {
        header('Content-Type: application/json; charset=UTF-8');
        header('Cache-Control: no-store');
        header('X-Content-Type-Options: nosniff');

        if (!Auth::validateSession()) {
            http_response_code(401);
            echo json_encode(['error' => 'authentication_required']);

            return;
        }

        if (!Auth::can('books.view')) {
            http_response_code(403);
            echo json_encode(['error' => 'permission_denied']);

            return;
        }

        $query = substr(trim((string) ($_GET['q'] ?? '')), 0, 100);
        $books = (new BookRepository($this->database))->search($query, 50);
        echo json_encode(['data' => $books], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
