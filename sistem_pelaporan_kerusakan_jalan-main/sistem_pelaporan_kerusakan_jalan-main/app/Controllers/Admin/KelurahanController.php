<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Logger;
use App\Models\Kelurahan;
use App\Models\Kecamatan;
use App\Models\LogAktivitas;

class KelurahanController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $model = new Kelurahan();
        $this->view('admin/kelurahan/index', [
            'list'  => $model->all(),
            'flash' => $this->getFlash('success'),
            'error' => $this->getFlash('error'),
        ], 'admin');
    }

    public function create(): void
    {
        $this->requireAuth();
        $kecModel = new Kecamatan();
        $this->view('admin/kelurahan/create', [
            'kecamatanList' => $kecModel->all(),
            'errors'        => $_SESSION['kel_errors'] ?? [],
            'old'           => $_SESSION['kel_old']    ?? [],
        ], 'admin');
        unset($_SESSION['kel_errors'], $_SESSION['kel_old']);
    }

    public function store(): void
    {
        $this->requireAuth();
        $nama         = trim($_POST['nama_kelurahan'] ?? '');
        $idKecamatan  = (int) ($_POST['id_kecamatan'] ?? 0);
        $errors = [];

        if (empty($nama))       $errors[] = 'Nama kelurahan wajib diisi.';
        if ($idKecamatan === 0) $errors[] = 'Kecamatan wajib dipilih.';

        if (!empty($errors)) {
            $_SESSION['kel_errors'] = $errors;
            $_SESSION['kel_old']    = $_POST;
            $this->redirect('admin/kelurahan/create');
            return;
        }

        $model = new Kelurahan();
        $id    = $model->create($idKecamatan, $nama);
        (new LogAktivitas())->create($_SESSION['admin_id'], 'Menambahkan kelurahan: ' . $nama);
        Logger::info('Kelurahan baru ditambahkan', ['nama' => $nama, 'id_kecamatan' => $idKecamatan]);
        $this->flash('success', 'Kelurahan berhasil ditambahkan.');
        $this->redirect('admin/kelurahan');
    }

    public function edit(string $id): void
    {
        $this->requireAuth();
        $model    = new Kelurahan();
        $kecModel = new Kecamatan();
        $kel      = $model->find((int) $id);
        if (!$kel) { $this->redirect('admin/kelurahan'); return; }
        $this->view('admin/kelurahan/edit', [
            'kel'           => $kel,
            'kecamatanList' => $kecModel->all(),
            'errors'        => $_SESSION['kel_errors'] ?? [],
        ], 'admin');
        unset($_SESSION['kel_errors']);
    }

    public function update(string $id): void
    {
        $this->requireAuth();
        $nama        = trim($_POST['nama_kelurahan'] ?? '');
        $idKecamatan = (int) ($_POST['id_kecamatan'] ?? 0);
        $errors = [];
        if (empty($nama))       $errors[] = 'Nama kelurahan wajib diisi.';
        if ($idKecamatan === 0) $errors[] = 'Kecamatan wajib dipilih.';

        if (!empty($errors)) {
            $_SESSION['kel_errors'] = $errors;
            $this->redirect('admin/kelurahan/' . $id . '/edit');
            return;
        }

        (new Kelurahan())->update((int) $id, $idKecamatan, $nama);
        (new LogAktivitas())->create($_SESSION['admin_id'], 'Memperbarui kelurahan ID:' . $id . ' → ' . $nama);
        Logger::info('Kelurahan diperbarui', ['id' => $id, 'nama' => $nama]);
        $this->flash('success', 'Kelurahan berhasil diperbarui.');
        $this->redirect('admin/kelurahan');
    }

    public function destroy(string $id): void
    {
        $this->requireSuperadmin();
        $model = new Kelurahan();
        $kel   = $model->find((int) $id);
        if ($kel) {
            $model->delete((int) $id);
            (new LogAktivitas())->create($_SESSION['admin_id'], 'Menghapus kelurahan: ' . $kel['nama_kelurahan']);
            Logger::warning('Kelurahan dihapus', ['id' => $id, 'nama' => $kel['nama_kelurahan']]);
            $this->flash('success', 'Kelurahan berhasil dihapus.');
        }
        $this->redirect('admin/kelurahan');
    }
}
