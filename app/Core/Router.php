<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    /** @var array<string, array<string, array{0:class-string,1:string}>> */
    private array $routes = [];

    public function get(string $route, array $handler): void
    {
        $this->add('GET', $route, $handler);
    }

    public function post(string $route, array $handler): void
    {
        $this->add('POST', $route, $handler);
    }

    public function dispatch(string $method, string $route): void
    {
        $method = strtoupper($method);
        $route = trim($route, '/') ?: 'dashboard';
        $handler = $this->routes[$method][$route] ?? null;

        if ($handler === null) {
            http_response_code(404);
            View::render('error', [
                'title' => 'Seite nicht gefunden',
                'code' => 404,
                'message' => 'Die angeforderte Seite wurde nicht gefunden.',
            ]);

            return;
        }

        if ($method === 'POST' && !Csrf::validate($_POST['_csrf'] ?? null)) {
            Logger::warning('CSRF validation failed', ['route' => $route, 'user_id' => Auth::id()]);
            http_response_code(419);
            View::render('error', [
                'title' => 'Sicherheitsprüfung fehlgeschlagen',
                'code' => 419,
                'message' => 'Bitte laden Sie die Seite neu und versuchen Sie es noch einmal.',
            ]);

            return;
        }

        [$controllerClass, $controllerMethod] = $handler;
        $controller = new $controllerClass();
        $controller->{$controllerMethod}();
    }

    private function add(string $method, string $route, array $handler): void
    {
        $this->routes[$method][trim($route, '/')] = $handler;
    }
}

