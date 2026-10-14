<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Logger;
use App\Models\Kecamatan;
use App\Models\LogAktivitas;

class KecamatanController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $model = new Kecamatan();
        $this->view('admin/kecamatan/index', [
            'list'  => $model->all(),
            'flash' => $this->getFlash('success'),
            'error' => $this->getFlash('error'),
        ], 'admin');
    }

    public function create(): void
    {
        $this->requireAuth();
        $this->view('admin/kecamatan/create', [
            'errors' => $_SESSION['kec_errors'] ?? [],
            'old'    => $_SESSION['kec_old']    ?? [],
        ], 'admin');
        unset($_SESSION['kec_errors'], $_SESSION['kec_old']);
    }

    public function store(): void
    {
        $this->requireAuth();
        $nama  = trim($_POST['nama_kecamatan'] ?? '');
        $model = new Kecamatan();

        if (empty($nama)) {
            $_SESSION['kec_errors'] = ['Nama kecamatan wajib diisi.'];
            $_SESSION['kec_old']    = $_POST;
            $this->redirect('admin/kecamatan/create');
            return;
        }
        if ($model->existsByName($nama)) {
            $_SESSION['kec_errors'] = ['Nama kecamatan sudah ada.'];
            $_SESSION['kec_old']    = $_POST;
            $this->redirect('admin/kecamatan/create');
            return;
        }

        $id = $model->create($nama);
        (new LogAktivitas())->create($_SESSION['admin_id'], 'Menambahkan kecamatan: ' . $nama);
        Logger::info('Kecamatan baru ditambahkan', ['nama' => $nama, 'id' => $id]);
        $this->flash('success', 'Kecamatan berhasil ditambahkan.');
        $this->redirect('admin/kecamatan');
    }

    public function edit(string $id): void
    {
        $this->requireAuth();
        $model = new Kecamatan();
        $kec   = $model->find((int) $id);
        if (!$kec) { $this->redirect('admin/kecamatan'); return; }
        $this->view('admin/kecamatan/edit', [
            'kec'    => $kec,
            'errors' => $_SESSION['kec_errors'] ?? [],
        ], 'admin');
        unset($_SESSION['kec_errors']);
    }

    public function update(string $id): void
    {
        $this->requireAuth();
        $nama  = trim($_POST['nama_kecamatan'] ?? '');
        $model = new Kecamatan();

        if (empty($nama)) {
            $_SESSION['kec_errors'] = ['Nama kecamatan wajib diisi.'];
            $this->redirect('admin/kecamatan/' . $id . '/edit');
            return;
        }
        if ($model->existsByName($nama, (int) $id)) {
            $_SESSION['kec_errors'] = ['Nama kecamatan sudah ada.'];
            $this->redirect('admin/kecamatan/' . $id . '/edit');
            return;
        }

        $model->update((int) $id, $nama);
        (new LogAktivitas())->create($_SESSION['admin_id'], 'Memperbarui kecamatan ID:' . $id . ' → ' . $nama);
        Logger::info('Kecamatan diperbarui', ['id' => $id, 'nama' => $nama]);
        $this->flash('success', 'Kecamatan berhasil diperbarui.');
        $this->redirect('admin/kecamatan');
    }

    public function destroy(string $id): void
    {
        $this->requireSuperadmin();
        $model = new Kecamatan();
        $kec   = $model->find((int) $id);
        if ($kec) {
            $model->delete((int) $id);
            (new LogAktivitas())->create($_SESSION['admin_id'], 'Menghapus kecamatan: ' . $kec['nama_kecamatan']);
            Logger::warning('Kecamatan dihapus', ['id' => $id, 'nama' => $kec['nama_kecamatan']]);
            $this->flash('success', 'Kecamatan berhasil dihapus.');
        }
        $this->redirect('admin/kecamatan');
    }
}
