<?php
namespace App\Core;

class Logger
{
    private static string $logFile = __DIR__ . '/../../logs/app.log';
    private static ?string $requestId = null;

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

    /**
     * Satu entri per HTTP request (dipakai untuk menghitung jumlah request & response time).
     */
    public static function request(int $statusCode, float $durationMs): void
    {
        self::write('INFO', 'HTTP request', [], [
            'status_code' => $statusCode,
            'duration_ms' => round($durationMs, 2),
        ]);
    }

    public static function exception(\Throwable $e, string $prefix = 'Unhandled exception'): void
    {
        self::error($prefix . ': ' . get_class($e), [
            'exception'     => get_class($e),
            'error_code'    => $e->getCode(),
            'error_message' => $e->getMessage(),
            'file'          => $e->getFile(),
            'line'          => $e->getLine(),
            'trace'         => substr($e->getTraceAsString(), 0, 8000),
        ]);
    }

    /**
     * ID unik per request, agar semua log dari satu request bisa ditelusuri bersama.
     */
    public static function requestId(): string
    {
        return self::$requestId ??= bin2hex(random_bytes(8));
    }

    private static function write(string $level, string $message, array $context, array $httpExtra = []): void
    {
        $userId = $_SESSION['admin_id'] ?? null;
        $role = $_SESSION['admin_jabatan'] ?? 'Guest';

        $entry = [
            'timestamp'  => date('c'),
            'level'      => $level,
            'app_name'   => 'laporjalan-kendari',
            'message'    => $message,
            'request_id' => self::requestId(),
            'http'       => [
                'method'     => $_SERVER['REQUEST_METHOD'] ?? 'CLI',
                'endpoint'   => $_SERVER['REQUEST_URI'] ?? '/',
                'ip_address' => self::clientIp(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'CLI',
            ] + $httpExtra,
            'actor'      => [
                'user_id' => $userId,
                'role'    => $role,
            ],
            'context'    => empty($context) ? new \stdClass() : $context,
        ];

        $json = json_encode($entry, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);

        $dir = dirname(self::$logFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents(self::$logFile, $json . PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    private static function clientIp(): string
    {
        $forwardedFor = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '';
        if ($forwardedFor !== '') {
            return trim(explode(',', $forwardedFor)[0]);
        }

        return $_SERVER['REMOTE_ADDR'] ?? 'CLI';
    }
}
