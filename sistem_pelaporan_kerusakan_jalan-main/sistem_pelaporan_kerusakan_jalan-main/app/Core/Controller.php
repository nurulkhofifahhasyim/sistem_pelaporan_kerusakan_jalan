<?php
namespace App\Core;

class Controller
{
    protected array $appConfig;

    public function __construct()
    {
        $this->appConfig = require __DIR__ . '/../Config/app.php';
    }

    protected function view(string $viewPath, array $data = [], string $layout = 'public'): void
    {
        extract($data, EXTR_SKIP);
        $appConfig = $this->appConfig;

        ob_start();
        require __DIR__ . '/../Views/' . $viewPath . '.php';
        $content = ob_get_clean();

        require __DIR__ . '/../Views/layouts/' . $layout . '.php';
    }

    protected function redirect(string $path): void
    {
        $base = rtrim($this->appConfig['base_url'], '/');
        $path = ltrim($path, '/');
        header('Location: ' . $base . '/' . $path);
        exit;
    }

    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    protected function flash(string $key, string $message): void
    {
        $_SESSION['flash'][$key] = $message;
    }

    protected function getFlash(string $key): ?string
    {
        $msg = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $msg;
    }

    protected function handleImageUpload(array $file, string $uploadDir): array
    {
        $allowedExt   = ['jpg', 'jpeg', 'png', 'webp'];
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
        $maxSize      = 5 * 1024 * 1024;

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExt)) {
            return ['success' => false, 'message' => 'Format file tidak didukung. Gunakan JPG, PNG, atau WEBP.'];
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        if (!in_array($finfo->file($file['tmp_name']), $allowedMimes)) {
            return ['success' => false, 'message' => 'Tipe file tidak valid.'];
        }

        if ($file['size'] > $maxSize) {
            return ['success' => false, 'message' => 'Ukuran file maksimal 5MB.'];
        }

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filename = bin2hex(random_bytes(8)) . '_' . time() . '.' . $ext;
        if (!move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
            Logger::error('Gagal upload file', ['dest' => $uploadDir . $filename]);
            return ['success' => false, 'message' => 'Gagal mengunggah file. Coba lagi.'];
        }

        return ['success' => true, 'filename' => $filename];
    }

    protected function logAktivitas(string $aktivitas): void
    {
        if (!empty($_SESSION['admin_id'])) {
            (new \App\Models\LogAktivitas())->create($_SESSION['admin_id'], $aktivitas);
        }
    }

    protected function csrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    protected function verifyCsrf(): void
    {
        $token = $_POST['csrf_token'] ?? '';
        if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            http_response_code(403);
            die('Permintaan tidak valid (CSRF).');
        }

        unset($_SESSION['csrf_token']);
    }

    protected function verifyRecaptcha(): bool
    {
        if (empty($this->appConfig['recaptcha_enabled'])) {
            return true;
        }

        $response = $_POST['g-recaptcha-response'] ?? '';
        if (empty($response)) {
            return false;
        }

        $secret = $this->appConfig['recaptcha_secret_key'];
        $url    = 'https://www.google.com/recaptcha/api/siteverify';
        $data   = http_build_query([
            'secret'   => $secret,
            'response' => $response,
            'remoteip' => $_SERVER['REMOTE_ADDR'] ?? '',
        ]);

        $ctx = stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => 'Content-Type: application/x-www-form-urlencoded',
                'content' => $data,
                'timeout' => 5,
            ],
        ]);

        $result = @file_get_contents($url, false, $ctx);
        if ($result === false) {
            return false;
        }

        $json = json_decode($result, true);
        return !empty($json['success']);
    }

    protected function requireAuth(): void
    {
        if (empty($_SESSION['admin_id'])) {
            $this->redirect('admin/login');
        }
    }

    protected function requireSuperadmin(): void
    {
        $this->requireAuth();
        if (($_SESSION['admin_jabatan'] ?? '') !== 'Superadmin') {
            http_response_code(403);
            die('Akses ditolak. Hanya Superadmin yang dapat mengakses fitur ini.');
        }
    }

    protected function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}
