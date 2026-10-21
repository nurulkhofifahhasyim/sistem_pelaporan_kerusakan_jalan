<?php $pageTitle = 'Dashboard'; ?>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
  <?php
  $cards = [
    ['label'=>'Total Laporan',   'val'=>$stats['total'],    'lucide'=>'clipboard-list',  'color'=>'text-blue-600',    'bg'=>'bg-blue-50'],
    ['label'=>'Diproses',        'val'=>$stats['Diproses'], 'lucide'=>'settings',        'color'=>'text-amber-600',   'bg'=>'bg-amber-50'],
    ['label'=>'Selesai',         'val'=>$stats['Selesai'],  'lucide'=>'check-circle-2',  'color'=>'text-emerald-600', 'bg'=>'bg-emerald-50'],
    ['label'=>'Ditolak',         'val'=>$stats['Ditolak'],  'lucide'=>'x-circle',        'color'=>'text-red-600',     'bg'=>'bg-red-50'],
  ];
  foreach ($cards as $c):
  ?>
  <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
    <div class="flex items-center justify-between mb-3">
      <div class="w-10 h-10 <?= $c['bg'] ?> rounded-xl flex items-center justify-center">
        <i data-lucide="<?= $c['lucide'] ?>" class="w-5 h-5 <?= $c['color'] ?>"></i>
      </div>
    </div>
    <div class="text-2xl font-bold text-slate-800"><?= number_format($c['val']) ?></div>
    <div class="text-sm text-slate-500 mt-1"><?= $c['label'] ?></div>
  </div>
  <?php endforeach; ?>
</div>

<div class="flex items-center justify-between mb-4">
  <h2 class="text-lg font-semibold text-slate-700">Grafik Pelaporan</h2>
  <form method="GET" class="flex items-center gap-2">
    <label class="text-sm text-slate-500">Tahun:</label>
    <select name="tahun" onchange="this.form.submit()" class="border border-slate-200 rounded-lg px-3 py-1.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
      <?php foreach ($availableYears as $y): ?>
      <option value="<?= $y ?>" <?= $y == $selectedYear ? 'selected' : '' ?>><?= $y ?></option>
      <?php endforeach; ?>
      <?php if (empty($availableYears)): ?>
      <option value="<?= date('Y') ?>" selected><?= date('Y') ?></option>
      <?php endif; ?>
    </select>
  </form>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 mb-6">
  <h3 class="font-semibold text-slate-700 mb-4 text-sm">Pengaduan per Bulan — Tahun <?= $selectedYear ?></h3>
  <canvas id="chartStatus" height="100"></canvas>
</div>

<div class="grid lg:grid-cols-3 gap-6">

  <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100">
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
      <h2 class="font-semibold text-slate-700">Laporan Terbaru</h2>
      <a href="<?= $appConfig['base_url'] ?>/admin/pengaduan" class="text-xs text-primary-600 hover:underline">Lihat Semua →</a>
    </div>
    <div class="divide-y divide-slate-50">
      <?php if (empty($recentList)): ?>
        <p class="text-center text-slate-400 py-10 text-sm">Belum ada laporan.</p>
      <?php else: foreach ($recentList as $r): ?>
      <div class="px-6 py-3 flex items-center justify-between gap-3 hover:bg-slate-50 transition">
        <div class="min-w-0">
          <p class="font-mono text-xs font-bold text-primary-600"><?= htmlspecialchars($r['kode_tiket']) ?></p>
          <p class="text-sm text-slate-700 truncate"><?= htmlspecialchars($r['nama_lengkap']) ?></p>
          <p class="text-xs text-slate-400"><?= htmlspecialchars($r['nama_kelurahan']) ?></p>
        </div>
        <span class="status-badge <?= 'status-' . strtolower($r['status_pengaduan']) ?> flex-shrink-0">
          <?= htmlspecialchars($r['status_pengaduan']) ?>
        </span>
      </div>
      <?php endforeach; endif; ?>
    </div>
  </div>

  <div class="space-y-4">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
      <h2 class="font-semibold text-slate-700 mb-3">Ringkasan</h2>
      <div class="space-y-2">
        <div class="flex justify-between text-sm">
          <span class="text-slate-500">Total Saran</span>
          <span class="font-semibold text-emerald-600"><?= $totalSaran ?></span>
        </div>
      </div>
    </div>
    <div class="bg-primary-50 border border-primary-100 rounded-2xl p-5">
      <h3 class="font-semibold text-primary-800 text-sm mb-3 flex items-center gap-2">
        <i data-lucide="zap" class="w-4 h-4 text-amber-500"></i>
        Akses Cepat
      </h3>
      <div class="space-y-2">
        <a href="<?= $appConfig['base_url'] ?>/admin/pengaduan?status=Diproses" class="flex items-center gap-2 text-primary-700 hover:text-primary-900 text-sm hover:underline">
          <i data-lucide="arrow-right" class="w-3 h-3"></i> Lihat laporan Diproses
        </a>
        <a href="<?= $appConfig['base_url'] ?>/admin/query-recommend" class="flex items-center gap-2 text-primary-700 hover:text-primary-900 text-sm hover:underline">
          <i data-lucide="arrow-right" class="w-3 h-3"></i> Query Recommendation
        </a>
        <a href="<?= $appConfig['base_url'] ?>/admin/log-aktivitas" class="flex items-center gap-2 text-primary-700 hover:text-primary-900 text-sm hover:underline">
          <i data-lucide="arrow-right" class="w-3 h-3"></i> Log Aktivitas Admin
        </a>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
const bulanLabels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

const dataStatus = {
  Diproses: <?= json_encode(array_values($chartStatus['Diproses'])) ?>,
  Selesai:  <?= json_encode(array_values($chartStatus['Selesai'])) ?>,
  Ditolak:  <?= json_encode(array_values($chartStatus['Ditolak'])) ?>
};

new Chart(document.getElementById('chartStatus'), {
  type: 'bar',
  data: {
    labels: bulanLabels,
    datasets: [
      { label: 'Diproses', data: dataStatus.Diproses, backgroundColor: 'rgba(20, 63, 145, 0.75)', borderRadius: 4 },
      { label: 'Selesai',  data: dataStatus.Selesai,  backgroundColor: 'rgba(16, 185, 129, 0.7)', borderRadius: 4 },
      { label: 'Ditolak',  data: dataStatus.Ditolak,  backgroundColor: 'rgba(239, 68, 68, 0.7)',  borderRadius: 4 },
    ]
  },
  options: {
    responsive: true,
    plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 15 } } },
    scales: {
      x: { stacked: true },
      y: { stacked: true, beginAtZero: true, ticks: { stepSize: 1 } }
    }
  }
});
</script>
