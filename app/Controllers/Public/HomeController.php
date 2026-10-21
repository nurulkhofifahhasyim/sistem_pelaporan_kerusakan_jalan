<?php
namespace App\Controllers\Public;

use App\Core\Controller;
use App\Models\Pengaduan;

class HomeController extends Controller
{
    public function index(): void
    {
        $pengaduanModel = new Pengaduan();
        $stats = $pengaduanModel->countByStatus();
        $this->view('public/home', ['stats' => $stats], 'public');
    }
}
