<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Logger;
use App\Models\Pengaduan;
use App\Models\Tanggapan;
use App\Models\LogAktivitas;

class PengaduanController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $model = new Pengaduan();
        $filters = [
            'status' => $_GET['status'] ?? '',
            'search' => $_GET['search'] ?? '',
        ];
        $list = $model->allWithDetails($filters);
        $stats = $model->countByStatus();
        $this->view('admin/pengaduan/index', [
            'list'    => $list,
            'stats'   => $stats,
            'filters' => $filters,
            'flash'   => $this->getFlash('success'),
        ], 'admin');
    }

    public function detail(string $id): void
    {
        $this->requireAuth();
        $pengaduanModel = new Pengaduan();
        $tanggapanModel = new Tanggapan();

        $pengaduan = $pengaduanModel->findById((int) $id);
        if (!$pengaduan) {
            $this->redirect('admin/pengaduan');
            return;
        }
        $tanggapan = $tanggapanModel->byPengaduan((int) $id);

        $this->view('admin/pengaduan/detail', [
            'pengaduan' => $pengaduan,
            'tanggapan' => $tanggapan,
            'flash'     => $this->getFlash('success'),
            'error'     => $this->getFlash('error'),
        ], 'admin');
    }

    public function exportExcel(): void
    {
        $this->requireAuth();
        $model = new Pengaduan();
        $filters = [
            'status' => $_GET['status'] ?? '',
            'search' => $_GET['search'] ?? '',
        ];
        $list = $model->allWithDetails($filters);

        $filename = 'pengaduan_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $output = fopen('php://output', 'w');

        fwrite($output, "\xEF\xBB\xBF");

        fputcsv($output, ['No', 'Kode Tiket', 'Nama Pelapor', 'Email', 'No. Telepon', 'Kecamatan', 'Kelurahan', 'Deskripsi Kerusakan', 'Status', 'Tanggal Laporan']);

        $sanitize = fn($v) => preg_match('/^[=+\-@\t\r]/', (string)$v) ? "'" . $v : $v;
        foreach ($list as $i => $row) {
            fputcsv($output, [
                $i + 1,
                $sanitize($row['kode_tiket']),
                $sanitize($row['nama_lengkap']),
                $sanitize($row['email']),
                $sanitize($row['no_telepon'] ?: ''),
                $sanitize($row['nama_kecamatan']),
                $sanitize($row['nama_kelurahan']),
                $sanitize($row['isi_laporan']),
                $sanitize($row['status_pengaduan']),
                date('d/m/Y H:i', strtotime($row['tgl_pengaduan'])),
            ]);
        }

        fclose($output);
        exit;
    }

    public function storeTanggapan(string $id): void
    {
        $this->requireAuth();

        $isi    = trim($_POST['isi_tanggapan'] ?? '');
        $status = $_POST['status_pengaduan'] ?? '';
        $validStatuses = ['Diproses', 'Selesai', 'Ditolak'];

        if (empty($isi)) {
            $this->flash('error', 'Isi tanggapan tidak boleh kosong.');
            $this->redirect('admin/pengaduan/' . $id);
            return;
        }
        if (!in_array($status, $validStatuses)) {
            $this->flash('error', 'Status tidak valid.');
            $this->redirect('admin/pengaduan/' . $id);
            return;
        }

        $pengaduanModel = new Pengaduan();
        $pengaduan      = $pengaduanModel->findById((int) $id);
        if (!$pengaduan) {
            $this->redirect('admin/pengaduan');
            return;
        }

        $tanggapanModel = new Tanggapan();
        $logModel       = new LogAktivitas();
        $adminId        = $_SESSION['admin_id'];

        $fotoTanggapan = null;
        if (!empty($_FILES['foto_tanggapan']['name']) && $_FILES['foto_tanggapan']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['foto_tanggapan'];
            $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if (!in_array($ext, $allowedExt)) {
                $this->flash('error', 'Format foto tidak didukung. Gunakan JPG, PNG, atau WebP.');
                $this->redirect('admin/pengaduan/' . $id);
                return;
            }
            if ($file['size'] > 5 * 1024 * 1024) {
                $this->flash('error', 'Ukuran foto maksimal 5MB.');
                $this->redirect('admin/pengaduan/' . $id);
                return;
            }

            $uploadDir = $this->appConfig['upload_dir'] . 'tanggapan/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $filename = 'tgp_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
                $fotoTanggapan = 'tanggapan/' . $filename;
            } else {
                Logger::error('Gagal upload foto tanggapan', ['dest' => $uploadDir . $filename]);
            }
        }

        $tanggapanModel->create((int) $id, $adminId, $isi, $fotoTanggapan, $status);

        $pengaduanModel->updateStatus((int) $id, $status);

        $aktivitas = 'Menanggapi laporan ' . $pengaduan['kode_tiket'] . ' → Status: ' . $status;
        $logModel->create($adminId, $aktivitas);

        Logger::info('Tanggapan admin diberikan', [
            'admin_id'    => $adminId,
            'kode_tiket'  => $pengaduan['kode_tiket'],
            'status_baru' => $status,
            'id_pengaduan'=> $id,
        ]);

        $this->flash('success', 'Tanggapan berhasil disimpan dan status diperbarui.');
        $this->redirect('admin/pengaduan/' . $id);
    }
}
