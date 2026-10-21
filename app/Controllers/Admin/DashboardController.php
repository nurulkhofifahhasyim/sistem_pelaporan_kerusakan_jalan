<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Pengaduan;
use App\Models\Saran;
use App\Models\LogAktivitas;

class DashboardController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();

        $pengaduanModel  = new Pengaduan();
        $saranModel      = new Saran();

        $availableYears = $pengaduanModel->availableYears();
        $selectedYear   = isset($_GET['tahun']) ? (int) $_GET['tahun'] : (int) date('Y');

        if (!in_array($selectedYear, $availableYears) && !empty($availableYears)) {
            $selectedYear = $availableYears[0];
        }

        $stats           = $pengaduanModel->countByStatus();
        $recent          = $pengaduanModel->recentList(5);
        $chartBulanan    = $pengaduanModel->chartPerBulan($selectedYear);
        $chartStatus     = $pengaduanModel->chartStatusPerBulan($selectedYear);
        $chartKecamatan  = $pengaduanModel->chartPerKecamatan($selectedYear);

        $this->view('admin/dashboard', [
            'stats'          => $stats,
            'recentList'     => $recent,
            'totalSaran'     => $saranModel->count(),
            'chartBulanan'   => $chartBulanan,
            'chartStatus'    => $chartStatus,
            'chartKecamatan' => $chartKecamatan,
            'selectedYear'   => $selectedYear,
            'availableYears' => $availableYears,
        ], 'admin');
    }
}
