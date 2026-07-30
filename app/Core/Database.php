<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

final class Database
{
    private static ?PDO $connection = null;

    public static function connection(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $host = (string) Config::get('DB_HOST', '127.0.0.1');
        $port = (string) Config::get('DB_PORT', '3306');
        $database = (string) Config::get('DB_DATABASE', 'bibliothksystem');
        $username = (string) Config::get('DB_USERNAME', 'bibliothek');
        $password = (string) Config::get('DB_PASSWORD', '');
        $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";

        try {
            self::$connection = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_STRINGIFY_FETCHES => false,
            ]);
        } catch (PDOException $exception) {
            Logger::error('Database connection failed', [
                'host' => $host,
                'database' => $database,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }

        return self::$connection;
    }
}

