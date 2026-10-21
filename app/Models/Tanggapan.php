<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Tanggapan
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function create(int $idPengaduan, int $idUser, string $isi, ?string $foto = null, string $status = 'Diproses'): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO TANGGAPAN (id_pengaduan, id_user, isi_tanggapan, foto_tanggapan, status_saat_itu) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([$idPengaduan, $idUser, $isi, $foto, $status]);
        return (int) $this->db->lastInsertId();
    }

    public function byPengaduan(int $idPengaduan): array
    {
        $stmt = $this->db->prepare(
            'SELECT t.*, u.nama_petugas, u.jabatan
             FROM TANGGAPAN t
             JOIN USER_ADMIN u ON u.id_user = t.id_user
             WHERE t.id_pengaduan = ?
             ORDER BY t.tgl_tanggapan ASC'
        );
        $stmt->execute([$idPengaduan]);
        return $stmt->fetchAll();
    }
}
