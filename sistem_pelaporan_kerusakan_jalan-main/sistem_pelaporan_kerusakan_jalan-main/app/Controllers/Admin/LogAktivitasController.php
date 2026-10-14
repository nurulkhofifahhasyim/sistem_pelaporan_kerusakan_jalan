<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\LogAktivitas;

class LogAktivitasController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $perPage = 20;
        $page    = max(1, (int) ($_GET['page'] ?? 1));
        $offset  = ($page - 1) * $perPage;
        $model   = new LogAktivitas();

        $filters = [
            'search'    => $_GET['search'] ?? '',
            'date_from' => $_GET['date_from'] ?? '',
            'date_to'   => $_GET['date_to'] ?? '',
        ];

        $total   = $model->count($filters);
        $list    = $model->allWithAdmin($perPage, $offset, $filters);
        $this->view('admin/log-aktivitas/index', [
            'list'    => $list,
            'total'   => $total,
            'page'    => $page,
            'perPage' => $perPage,
            'filters' => $filters,
        ], 'admin');
    }

    public function exportCsv(): void
    {
        $this->requireAuth();
        $model = new LogAktivitas();

        $filters = [
            'search'    => $_GET['search'] ?? '',
            'date_from' => $_GET['date_from'] ?? '',
            'date_to'   => $_GET['date_to'] ?? '',
        ];

        $total = $model->count($filters);
        $list  = $model->allWithAdmin($total, 0, $filters);

        $filename = 'log_aktivitas_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $output = fopen('php://output', 'w');
        fwrite($output, "\xEF\xBB\xBF");

        fputcsv($output, ['No', 'Petugas', 'Jabatan', 'Aktivitas', 'Waktu']);

        foreach ($list as $i => $row) {
            fputcsv($output, [
                $i + 1,
                $row['nama_petugas'],
                $row['jabatan'],
                $row['aktivitas'],
                date('d/m/Y H:i', strtotime($row['tgl_log'])),
            ]);
        }

        fclose($output);
        exit;
    }
}
