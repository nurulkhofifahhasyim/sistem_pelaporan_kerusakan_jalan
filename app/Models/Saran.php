<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Saran
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO SARAN (nama_pengirim, isi_saran) VALUES (:nama_pengirim, :isi_saran)'
        );
        $stmt->execute([
            ':nama_pengirim' => $data['nama_pengirim'] ?? null,
            ':isi_saran'     => $data['isi_saran'],
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function all(): array
    {
        $stmt = $this->db->query('SELECT * FROM SARAN ORDER BY tgl_saran DESC');
        return $stmt->fetchAll();
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM SARAN')->fetchColumn();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM SARAN WHERE id_saran = :id');
        return $stmt->execute([':id' => $id]);
    }
}
