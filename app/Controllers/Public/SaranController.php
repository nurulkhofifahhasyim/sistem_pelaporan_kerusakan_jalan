<?php
namespace App\Controllers\Public;

use App\Core\Controller;
use App\Core\Logger;
use App\Models\Saran;

class SaranController extends Controller
{
    public function create(): void
    {
        $this->view('public/saran/form', [
            'errors' => $_SESSION['saran_errors'] ?? [],
            'old'    => $_SESSION['saran_old']    ?? [],
        ], 'public');
        unset($_SESSION['saran_errors'], $_SESSION['saran_old']);
    }

    public function store(): void
    {
        $isi = trim($_POST['isi_saran'] ?? '');
        if (empty($isi)) {
            $_SESSION['saran_errors'] = ['isi_saran' => 'Isi saran tidak boleh kosong.'];
            $_SESSION['saran_old']    = $_POST;
            $this->redirect('saran');
            return;
        }

        if (!$this->verifyRecaptcha()) {
            $_SESSION['saran_errors'] = ['recaptcha' => 'Verifikasi reCAPTCHA gagal. Silakan coba lagi.'];
            $_SESSION['saran_old']    = $_POST;
            $this->redirect('saran');
            return;
        }

        $model = new Saran();
        $model->create([
            'nama_pengirim' => trim($_POST['nama_pengirim'] ?? ''),
            'isi_saran'     => $isi,
        ]);

        Logger::info('Saran baru masuk', ['nama' => $_POST['nama_pengirim'] ?? 'Anonim']);
        $this->view('public/saran/success', [], 'public');
    }
}
