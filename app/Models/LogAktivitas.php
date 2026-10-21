<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class LogAktivitas
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function create(int $idUser, string $aktivitas): void
    {
        $stmt = $this->db->prepare('INSERT INTO LOG_AKTIVITAS (id_user, aktivitas) VALUES (?, ?)');
        $stmt->execute([$idUser, $aktivitas]);
    }

    public function allWithAdmin(int $limit = 100, int $offset = 0, array $filters = []): array
    {
        $sql = 'SELECT l.*, u.nama_petugas, u.jabatan
                FROM LOG_AKTIVITAS l
                JOIN USER_ADMIN u ON u.id_user = l.id_user
                WHERE 1=1';
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= ' AND (l.aktivitas LIKE ? OR u.nama_petugas LIKE ?)';
            $like = '%' . $filters['search'] . '%';
            $params[] = $like;
            $params[] = $like;
        }
        if (!empty($filters['date_from'])) {
            $sql .= ' AND DATE(l.tgl_log) >= ?';
            $params[] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $sql .= ' AND DATE(l.tgl_log) <= ?';
            $params[] = $filters['date_to'];
        }

        $sql .= ' ORDER BY l.tgl_log DESC LIMIT ? OFFSET ?';
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function count(array $filters = []): int
    {
        $sql = 'SELECT COUNT(*) FROM LOG_AKTIVITAS l
                JOIN USER_ADMIN u ON u.id_user = l.id_user
                WHERE 1=1';
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= ' AND (l.aktivitas LIKE ? OR u.nama_petugas LIKE ?)';
            $like = '%' . $filters['search'] . '%';
            $params[] = $like;
            $params[] = $like;
        }
        if (!empty($filters['date_from'])) {
            $sql .= ' AND DATE(l.tgl_log) >= ?';
            $params[] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $sql .= ' AND DATE(l.tgl_log) <= ?';
            $params[] = $filters['date_to'];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }
}
