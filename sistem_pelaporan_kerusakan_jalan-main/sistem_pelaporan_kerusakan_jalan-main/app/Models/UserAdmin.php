<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class UserAdmin
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findByUsername(string $username): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM USER_ADMIN WHERE username = ?');
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    public function verifyPassword(string $plain, string $hash): bool
    {
        return password_verify($plain, $hash);
    }

    public function all(): array
    {
        $stmt = $this->db->query('SELECT id_user, nama_petugas, username, jabatan FROM USER_ADMIN ORDER BY nama_petugas ASC');
        return $stmt->fetchAll();
    }
}
