<?php
namespace App\Controllers\Public;

use App\Core\Controller;
use App\Core\Logger;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Masyarakat;
use App\Models\Pengaduan;
use App\Models\Tanggapan;

class PengaduanController extends Controller
{
    public function create(): void
    {
        $kecamatanModel = new Kecamatan();
        $this->view('public/pengaduan/form', [
            'kecamatanList' => $kecamatanModel->all(),
            'errors'        => $_SESSION['form_errors'] ?? [],
            'old'           => $_SESSION['form_old']    ?? [],
        ], 'public');
        unset($_SESSION['form_errors'], $_SESSION['form_old']);
    }

    public function store(): void
    {
        $data = $_POST;
        $errors = $this->validate($data);

        if (empty($errors) && !$this->verifyRecaptcha()) {
            $errors['recaptcha'] = 'Verifikasi reCAPTCHA gagal. Silakan coba lagi.';
        }

        $fotoNama = null;
        if (empty($errors) && isset($_FILES['foto_bukti']) && $_FILES['foto_bukti']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->handleUpload($_FILES['foto_bukti']);
            if ($uploadResult['success']) {
                $fotoNama = $uploadResult['filename'];
            } else {
                $errors['foto_bukti'] = $uploadResult['message'];
            }
        } elseif (empty($errors)) {
            $errors['foto_bukti'] = 'Foto bukti wajib diunggah.';
        }

        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_old']    = $data;
            Logger::warning('Validasi form pengaduan gagal', ['errors' => $errors]);
            $this->redirect('pengaduan');
            return;
        }

        $masyarakatModel = new Masyarakat();
        $idMasyarakat = $masyarakatModel->create([
            'nama_lengkap' => trim($data['nama_lengkap']),
            'email'        => trim($data['email']),
            'no_telepon'   => trim($data['no_telepon'] ?? ''),
            'alamat'       => trim($data['alamat'] ?? ''),
        ]);

        $pengaduanModel = new Pengaduan();
        $kode = $pengaduanModel->generateKodeTiket();
        $pengaduanModel->create([
            'kode_tiket'    => $kode,
            'id_masyarakat' => $idMasyarakat,
            'id_kelurahan'  => (int) $data['id_kelurahan'],
            'isi_laporan'   => trim($data['isi_laporan']),
            'foto_bukti'    => $fotoNama,
        ]);

        Logger::info('Pengaduan baru berhasil dibuat', [
            'kode_tiket'    => $kode,
            'id_masyarakat' => $idMasyarakat,
            'id_kelurahan'  => $data['id_kelurahan'],
        ]);

        $_SESSION['success_kode'] = $kode;
        $this->redirect('pengaduan/tracking?success=1&kode=' . $kode);
    }

    public function tracking(): void
    {
        $successKode = $_GET['kode'] ?? null;
        $this->view('public/pengaduan/tracking', [
            'successKode' => $successKode,
            'errors'      => $_SESSION['tracking_errors'] ?? [],
        ], 'public');
        unset($_SESSION['tracking_errors']);
    }

    public function cekStatus(): void
    {
        $kode = trim($_POST['kode_tiket'] ?? '');
        if (empty($kode)) {
            $_SESSION['tracking_errors'] = ['Kode tiket tidak boleh kosong.'];
            $this->redirect('pengaduan/tracking');
            return;
        }
        $this->redirect('pengaduan/' . urlencode($kode));
    }

    public function show(string $kode): void
    {
        $pengaduanModel = new Pengaduan();
        $tanggapanModel = new Tanggapan();

        $pengaduan = $pengaduanModel->findByKode(strtoupper($kode));
        if (!$pengaduan) {
            Logger::warning('Kode tiket tidak ditemukan', ['kode' => $kode]);
            $this->view('public/pengaduan/not_found', ['kode' => $kode], 'public');
            return;
        }

        $tanggapan = $tanggapanModel->byPengaduan((int) $pengaduan['id_pengaduan']);

        Logger::info('Cek status pengaduan', ['kode_tiket' => $kode]);
        $this->view('public/pengaduan/detail', [
            'pengaduan' => $pengaduan,
            'tanggapan' => $tanggapan,
        ], 'public');
    }

    public function getKelurahan(string $id): void
    {
        $kelurahanModel = new Kelurahan();
        $list = $kelurahanModel->byKecamatan((int) $id);
        $this->json($list);
    }

    private function validate(array $data): array
    {
        $errors = [];
        if (empty(trim($data['nama_lengkap'] ?? '')))  $errors['nama_lengkap']  = 'Nama lengkap wajib diisi.';
        if (empty(trim($data['email'] ?? '')))          $errors['email']         = 'Email wajib diisi.';
        elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Format email tidak valid.';
        if (empty($data['id_kelurahan']))               $errors['id_kelurahan']  = 'Kelurahan wajib dipilih.';
        if (empty(trim($data['isi_laporan'] ?? '')))    $errors['isi_laporan']   = 'Isi laporan wajib diisi.';
        return $errors;
    }

    private function handleUpload(array $file): array
    {
        $cfg = $this->appConfig;
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $cfg['allowed_ext'])) {
            return ['success' => false, 'message' => 'Format file tidak didukung. Gunakan JPG, PNG, atau WEBP.'];
        }

        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file['tmp_name']);
        if (!in_array($mime, $allowedMimes)) {
            Logger::error('Upload file gagal: Tipe file tidak valid (MIME)', ['mime' => $mime]);
            return ['success' => false, 'message' => 'Tipe file tidak valid.'];
        }

        if ($file['size'] > $cfg['max_file_size']) {
            Logger::error('Upload foto melebihi batas ukuran', ['size' => $file['size']]);
            return ['success' => false, 'message' => 'Ukuran file maksimal 5MB.'];
        }

        $filename = 'foto_' . date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $dest     = $cfg['upload_dir'] . $filename;

        if (!is_dir($cfg['upload_dir'])) mkdir($cfg['upload_dir'], 0755, true);

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            Logger::error('Gagal memindahkan file upload', ['dest' => $dest]);
            return ['success' => false, 'message' => 'Gagal mengunggah foto. Coba lagi.'];
        }

        Logger::info('Foto berhasil diunggah', ['filename' => $filename]);
        return ['success' => true, 'filename' => $filename];
    }
}
