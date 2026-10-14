<?php $pageTitle = 'Log Aktivitas Admin'; ?>

<div class="flex items-center justify-between mb-4">
  <h2 class="text-lg font-semibold text-slate-700">Log Aktivitas Admin</h2>
  <a href="<?= $appConfig['base_url'] ?>/admin/log-aktivitas/export?search=<?= urlencode($filters['search']) ?>&date_from=<?= urlencode($filters['date_from']) ?>&date_to=<?= urlencode($filters['date_to']) ?>"
     class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
    Export CSV
  </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 mb-4">
  <form method="GET" class="flex flex-wrap gap-3 items-end">
    <div class="flex-1 min-w-48">
      <label class="block text-xs font-medium text-slate-500 mb-1">Cari</label>
      <input type="text" name="search" value="<?= htmlspecialchars($filters['search']) ?>"
        class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
        placeholder="Nama petugas, aktivitas..."/>
    </div>
    <div>
      <label class="block text-xs font-medium text-slate-500 mb-1">Dari Tanggal</label>
      <input type="date" name="date_from" value="<?= htmlspecialchars($filters['date_from']) ?>"
        class="border border-slate-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary-500"/>
    </div>
    <div>
      <label class="block text-xs font-medium text-slate-500 mb-1">Sampai Tanggal</label>
      <input type="date" name="date_to" value="<?= htmlspecialchars($filters['date_to']) ?>"
        class="border border-slate-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary-500"/>
    </div>
    <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-700 transition">Filter</button>
    <a href="<?= $appConfig['base_url'] ?>/admin/log-aktivitas" class="text-slate-500 text-sm hover:text-slate-700 py-2">Reset</a>
  </form>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
  <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
    <h2 class="font-semibold text-slate-700">Riwayat Aktivitas Admin</h2>
    <span class="text-xs text-slate-400">Total: <?= number_format($total) ?> aktivitas</span>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-slate-50 border-b border-slate-100">
        <tr>
          <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">#</th>
          <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Petugas</th>
          <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Aktivitas</th>
          <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Waktu</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-50">
        <?php if (empty($list)): ?>
        <tr><td colspan="4" class="text-center py-10 text-slate-400">Belum ada log aktivitas.</td></tr>
        <?php else: foreach ($list as $i => $row): ?>
        <tr class="hover:bg-slate-50 transition">
          <td class="px-5 py-3 text-slate-400"><?= ($page - 1) * $perPage + $i + 1 ?></td>
          <td class="px-5 py-3">
            <span class="font-medium text-slate-800"><?= htmlspecialchars($row['nama_petugas']) ?></span>
            <span class="ml-1 text-xs text-slate-400">(<?= htmlspecialchars($row['jabatan']) ?>)</span>
          </td>
          <td class="px-5 py-3 text-slate-700"><?= htmlspecialchars($row['aktivitas']) ?></td>
          <td class="px-5 py-3 text-slate-400 text-xs"><?= date('d M Y H:i', strtotime($row['tgl_log'])) ?></td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>

  <?php
  $totalPages = (int) ceil($total / $perPage);
  if ($totalPages > 1):
    $queryParams = http_build_query(array_filter($filters));
    $base = $appConfig['base_url'] . '/admin/log-aktivitas?' . ($queryParams ? $queryParams . '&' : '') . 'page=';
  ?>
  <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-between text-sm">
    <span class="text-slate-400">Halaman <?= $page ?> dari <?= $totalPages ?></span>
    <div class="flex gap-1">
      <?php if ($page > 1): ?><a href="<?= $base . ($page-1) ?>" class="px-3 py-1 bg-slate-100 hover:bg-slate-200 rounded-lg text-slate-600 transition text-xs">← Prev</a><?php endif; ?>
      <?php if ($page < $totalPages): ?><a href="<?= $base . ($page+1) ?>" class="px-3 py-1 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition text-xs">Next →</a><?php endif; ?>
    </div>
  </div>
  <?php endif; ?>
</div>
