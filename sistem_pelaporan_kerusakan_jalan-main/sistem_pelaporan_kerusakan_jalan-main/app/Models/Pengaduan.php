<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Pengaduan
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function generateKodeTiket(): string
    {
        do {
            $kode = 'RJL-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 4));
        } while ($this->findByKode($kode));
        return $kode;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO PENGADUAN (kode_tiket, id_masyarakat, id_kelurahan, isi_laporan, foto_bukti)
             VALUES (:kode_tiket, :id_masyarakat, :id_kelurahan, :isi_laporan, :foto_bukti)'
        );
        $stmt->execute([
            ':kode_tiket'    => $data['kode_tiket'],
            ':id_masyarakat' => $data['id_masyarakat'],
            ':id_kelurahan'  => $data['id_kelurahan'],
            ':isi_laporan'   => $data['isi_laporan'],
            ':foto_bukti'    => $data['foto_bukti'],
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function findByKode(string $kode): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT p.*, m.nama_lengkap, m.email, m.no_telepon, m.alamat,
                    k.nama_kelurahan, kec.nama_kecamatan
             FROM PENGADUAN p
             JOIN MASYARAKAT m   ON m.id_masyarakat  = p.id_masyarakat
             JOIN KELURAHAN k    ON k.id_kelurahan    = p.id_kelurahan
             JOIN KECAMATAN kec  ON kec.id_kecamatan  = k.id_kecamatan
             WHERE p.kode_tiket = ?'
        );
        $stmt->execute([$kode]);
        return $stmt->fetch();
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT p.*, m.nama_lengkap, m.email, m.no_telepon, m.alamat,
                    k.nama_kelurahan, kec.nama_kecamatan
             FROM PENGADUAN p
             JOIN MASYARAKAT m   ON m.id_masyarakat  = p.id_masyarakat
             JOIN KELURAHAN k    ON k.id_kelurahan    = p.id_kelurahan
             JOIN KECAMATAN kec  ON kec.id_kecamatan  = k.id_kecamatan
             WHERE p.id_pengaduan = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function allWithDetails(array $filters = []): array
    {
        $sql = 'SELECT p.*, m.nama_lengkap, m.email, m.no_telepon, k.nama_kelurahan, kec.nama_kecamatan
                FROM PENGADUAN p
                JOIN MASYARAKAT m   ON m.id_masyarakat = p.id_masyarakat
                JOIN KELURAHAN k    ON k.id_kelurahan   = p.id_kelurahan
                JOIN KECAMATAN kec  ON kec.id_kecamatan = k.id_kecamatan
                WHERE 1=1';
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= ' AND p.status_pengaduan = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['search'])) {
            $sql .= ' AND (p.kode_tiket LIKE ? OR m.nama_lengkap LIKE ? OR k.nama_kelurahan LIKE ?)';
            $like = '%' . $filters['search'] . '%';
            array_push($params, $like, $like, $like);
        }

        $sql .= ' ORDER BY p.tgl_pengaduan DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare('UPDATE PENGADUAN SET status_pengaduan = ? WHERE id_pengaduan = ?');
        return $stmt->execute([$status, $id]);
    }

    public function countByStatus(): array
    {
        $stmt = $this->db->query(
            'SELECT status_pengaduan, COUNT(*) as total FROM PENGADUAN GROUP BY status_pengaduan'
        );
        $rows   = $stmt->fetchAll();
        $result = ['Diproses' => 0, 'Selesai' => 0, 'Ditolak' => 0, 'total' => 0];
        foreach ($rows as $row) {
            $result[$row['status_pengaduan']] = (int) $row['total'];
            $result['total'] += (int) $row['total'];
        }
        return $result;
    }

    public function recentList(int $limit = 5): array
    {
        $stmt = $this->db->prepare(
            'SELECT p.kode_tiket, p.status_pengaduan, p.tgl_pengaduan, m.nama_lengkap, k.nama_kelurahan
             FROM PENGADUAN p
             JOIN MASYARAKAT m ON m.id_masyarakat = p.id_masyarakat
             JOIN KELURAHAN k  ON k.id_kelurahan  = p.id_kelurahan
             ORDER BY p.tgl_pengaduan DESC LIMIT ?'
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function chartPerBulan(?int $tahun = null): array
    {
        $year = $tahun ?? (int) date('Y');
        $stmt = $this->db->prepare(
            'SELECT MONTH(tgl_pengaduan) as bulan, COUNT(*) as total
             FROM PENGADUAN
             WHERE YEAR(tgl_pengaduan) = ?
             GROUP BY MONTH(tgl_pengaduan)
             ORDER BY bulan ASC'
        );
        $stmt->execute([$year]);
        $rows = $stmt->fetchAll();

        $data = array_fill(1, 12, 0);
        foreach ($rows as $row) {
            $data[(int) $row['bulan']] = (int) $row['total'];
        }
        return $data;
    }

    public function chartStatusPerBulan(?int $tahun = null): array
    {
        $year = $tahun ?? (int) date('Y');
        $stmt = $this->db->prepare(
            'SELECT MONTH(tgl_pengaduan) as bulan, status_pengaduan, COUNT(*) as total
             FROM PENGADUAN
             WHERE YEAR(tgl_pengaduan) = ?
             GROUP BY MONTH(tgl_pengaduan), status_pengaduan
             ORDER BY bulan ASC'
        );
        $stmt->execute([$year]);
        $rows = $stmt->fetchAll();

        $statuses = ['Diproses', 'Selesai', 'Ditolak'];
        $data = [];
        foreach ($statuses as $s) {
            $data[$s] = array_fill(1, 12, 0);
        }
        foreach ($rows as $row) {
            $data[$row['status_pengaduan']][(int) $row['bulan']] = (int) $row['total'];
        }
        return $data;
    }

    public function chartPerKecamatan(?int $tahun = null): array
    {
        $year = $tahun ?? (int) date('Y');
        $stmt = $this->db->prepare(
            'SELECT kec.nama_kecamatan, COUNT(*) as total
             FROM PENGADUAN p
             JOIN KELURAHAN k   ON k.id_kelurahan  = p.id_kelurahan
             JOIN KECAMATAN kec ON kec.id_kecamatan = k.id_kecamatan
             WHERE YEAR(p.tgl_pengaduan) = ?
             GROUP BY kec.nama_kecamatan
             ORDER BY total DESC
             LIMIT 10'
        );
        $stmt->execute([$year]);
        return $stmt->fetchAll();
    }

    public function availableYears(): array
    {
        $stmt = $this->db->query(
            'SELECT DISTINCT YEAR(tgl_pengaduan) as tahun FROM PENGADUAN ORDER BY tahun DESC'
        );
        return array_column($stmt->fetchAll(), 'tahun');
    }
}
