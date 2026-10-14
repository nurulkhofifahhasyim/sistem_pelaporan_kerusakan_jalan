<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Saran;

class SaranController extends Controller
{
    private Saran $saranModel;

    public function __construct()
    {
        parent::__construct();
        $this->saranModel = new Saran();
    }

    public function index(): void
    {
        $this->requireAuth();
        $list = $this->saranModel->all();
        $this->view('admin/saran/index', [
            'list'  => $list,
            'flash' => $this->getFlash('success'),
        ], 'admin');
    }

    public function destroy(string $id): void
    {
        $this->requireAuth();
        $this->saranModel->delete((int) $id);
        $this->flash('success', 'Saran berhasil dihapus.');
        $this->redirect('admin/saran');
    }
}
