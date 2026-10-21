<?php
namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    private function __construct() {}
    private function __clone() {}

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $config = require __DIR__ . '/../Config/database.php';

            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                $config['host'],
                $config['dbname'],
                $config['charset']
            );

            try {
                self::$instance = new PDO($dsn, $config['username'], $config['password'], [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
} catch (PDOException $e) {
                \App\Core\Logger::error('Database connection failed', [
                    'exception'     => get_class($e),
                    'error_code'    => $e->getCode(),
                    'error_message' => $e->getMessage(),
                    'file'          => $e->getFile(),
                    'line'          => $e->getLine(),
                ]);
                http_response_code(500);
                die('Terjadi kesalahan pada sistem. Silakan coba lagi nanti.');
            }
        }

        return self::$instance;
    }
}
