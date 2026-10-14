<?php
namespace App\Core;

class Logger
{
    private static string $logFile = __DIR__ . '/../../logs/app.log';

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

    private static function write(string $level, string $message, array $context): void
    {
        $userId = $_SESSION['admin_id'] ?? null;
        $role = $_SESSION['admin_jabatan'] ?? 'Guest';

        $entry = [
            'timestamp' => date('c'),
            'level'     => $level,
            'app_name'  => 'laporjalan-kendari',
            'message'   => $message,
            'http'      => [
                'method'     => $_SERVER['REQUEST_METHOD'] ?? 'CLI',
                'endpoint'   => $_SERVER['REQUEST_URI'] ?? '/',
                'ip_address' => self::clientIp(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'CLI',
            ],
            'actor'     => [
                'user_id' => $userId,
                'role'    => $role,
            ],
            'context'   => empty($context) ? new \stdClass() : $context,
        ];

        $json = json_encode($entry, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

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

    private static function endpoint(): string
    {
        if (PHP_SAPI === 'cli') {
            return 'CLI';
        }

        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri    = $_SERVER['REQUEST_URI'] ?? '/';

        return $method . ' ' . $uri;
    }
}
