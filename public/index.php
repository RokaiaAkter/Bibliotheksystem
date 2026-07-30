<?php

declare(strict_types=1);

use App\Controllers\ApiController;
use App\Controllers\AuthController;
use App\Controllers\BookController;
use App\Controllers\DashboardController;
use App\Controllers\LoanController;
use App\Controllers\PhoneController;
use App\Controllers\SystemController;
use App\Controllers\TicketController;
use App\Controllers\UserController;
use App\Core\Config;
use App\Core\Database;
use App\Core\Logger;
use App\Core\Router;
use App\Core\View;
use App\Database\Seeder;

require dirname(__DIR__) . '/app/bootstrap.php';

$route = trim((string) ($_GET['route'] ?? 'dashboard'), '/');

if ($route === 'health') {
    header('Content-Type: application/json; charset=UTF-8');
    header('Cache-Control: no-store');

    try {
        Database::connection()->query('SELECT 1')->fetchColumn();
        echo json_encode(['status' => 'ok', 'database' => 'ok']);
    } catch (Throwable $exception) {
        Logger::exception($exception);
        http_response_code(503);
        echo json_encode(['status' => 'error', 'database' => 'unavailable']);
    }

    exit;
}

set_exception_handler(static function (Throwable $exception) use ($route): void {
    Logger::exception($exception);
    http_response_code(500);

    if (str_starts_with($route, 'api/')) {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['error' => 'internal_server_error']);

        return;
    }

    $message = Config::bool('APP_DEBUG', false)
        ? $exception->getMessage()
        : 'Ein interner Fehler ist aufgetreten. Bitte prüfen Sie das Anwendungslog.';

    View::render('error', [
        'title' => 'Interner Fehler',
        'code' => 500,
        'message' => $message,
    ]);
});

try {
    Seeder::run(Database::connection());
} catch (Throwable $exception) {
    Logger::exception($exception);
    http_response_code(503);
    View::render('error', [
        'title' => 'Datenbank nicht bereit',
        'code' => 503,
        'message' => 'Die Anwendung kann die Datenbank noch nicht erreichen. Prüfen Sie Docker, .env und die MySQL-Logs.',
    ]);
    exit;
}

$router = new Router();

$router->get('login', [AuthController::class, 'showLogin']);
$router->post('login', [AuthController::class, 'login']);
$router->post('logout', [AuthController::class, 'logout']);

$router->get('dashboard', [DashboardController::class, 'index']);

$router->get('books', [BookController::class, 'index']);
$router->post('books/create', [BookController::class, 'create']);
$router->post('books/delete', [BookController::class, 'delete']);
$router->get('books/export', [BookController::class, 'exportXml']);
$router->post('books/import', [BookController::class, 'importXml']);
$router->post('loans/checkout', [LoanController::class, 'checkout']);
$router->post('loans/return', [LoanController::class, 'returnBook']);

$router->get('users', [UserController::class, 'index']);
$router->post('users/create', [UserController::class, 'create']);
$router->post('users/toggle', [UserController::class, 'toggle']);

$router->get('phones', [PhoneController::class, 'index']);
$router->post('phones/create', [PhoneController::class, 'create']);
$router->post('phones/toggle', [PhoneController::class, 'toggle']);

$router->get('tickets', [TicketController::class, 'index']);
$router->post('tickets/create', [TicketController::class, 'create']);
$router->post('tickets/status', [TicketController::class, 'updateStatus']);

$router->get('system', [SystemController::class, 'index']);
$router->get('api/books', [ApiController::class, 'books']);

$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $route);
