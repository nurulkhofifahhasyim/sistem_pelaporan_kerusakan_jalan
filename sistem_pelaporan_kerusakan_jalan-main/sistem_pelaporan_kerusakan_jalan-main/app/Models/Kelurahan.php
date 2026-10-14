<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Kelurahan
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function all(): array
    {
        $stmt = $this->db->query(
            'SELECT k.*, kec.nama_kecamatan
             FROM KELURAHAN k
             JOIN KECAMATAN kec ON kec.id_kecamatan = k.id_kecamatan
             ORDER BY kec.nama_kecamatan, k.nama_kelurahan ASC'
        );
        return $stmt->fetchAll();
    }

    public function byKecamatan(int $idKecamatan): array
    {
        $stmt = $this->db->prepare('SELECT * FROM KELURAHAN WHERE id_kecamatan = ? ORDER BY nama_kelurahan ASC');
        $stmt->execute([$idKecamatan]);
        return $stmt->fetchAll();
    }

    public function find(int $id): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT k.*, kec.nama_kecamatan
             FROM KELURAHAN k
             JOIN KECAMATAN kec ON kec.id_kecamatan = k.id_kecamatan
             WHERE k.id_kelurahan = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create(int $idKecamatan, string $nama): int
    {
        $stmt = $this->db->prepare('INSERT INTO KELURAHAN (id_kecamatan, nama_kelurahan) VALUES (?, ?)');
        $stmt->execute([$idKecamatan, $nama]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, int $idKecamatan, string $nama): bool
    {
        $stmt = $this->db->prepare('UPDATE KELURAHAN SET id_kecamatan = ?, nama_kelurahan = ? WHERE id_kelurahan = ?');
        return $stmt->execute([$idKecamatan, $nama, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM KELURAHAN WHERE id_kelurahan = ?');
        return $stmt->execute([$id]);
    }
}
