<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Masyarakat
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO MASYARAKAT (nama_lengkap, email, no_telepon, alamat)
             VALUES (:nama_lengkap, :email, :no_telepon, :alamat)'
        );
        $stmt->execute([
            ':nama_lengkap' => $data['nama_lengkap'],
            ':email'        => $data['email'],
            ':no_telepon'   => $data['no_telepon'] ?? null,
            ':alamat'       => $data['alamat']      ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }
}
