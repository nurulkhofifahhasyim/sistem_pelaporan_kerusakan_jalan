<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Kecamatan
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function all(): array
    {
        $stmt = $this->db->query('SELECT * FROM KECAMATAN ORDER BY nama_kecamatan ASC');
        return $stmt->fetchAll();
    }

    public function find(int $id): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM KECAMATAN WHERE id_kecamatan = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create(string $nama): int
    {
        $stmt = $this->db->prepare('INSERT INTO KECAMATAN (nama_kecamatan) VALUES (?)');
        $stmt->execute([$nama]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, string $nama): bool
    {
        $stmt = $this->db->prepare('UPDATE KECAMATAN SET nama_kecamatan = ? WHERE id_kecamatan = ?');
        return $stmt->execute([$nama, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM KECAMATAN WHERE id_kecamatan = ?');
        return $stmt->execute([$id]);
    }

    public function existsByName(string $nama, int $excludeId = 0): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM KECAMATAN WHERE nama_kecamatan = ? AND id_kecamatan != ?');
        $stmt->execute([$nama, $excludeId]);
        return $stmt->fetchColumn() > 0;
    }
}
