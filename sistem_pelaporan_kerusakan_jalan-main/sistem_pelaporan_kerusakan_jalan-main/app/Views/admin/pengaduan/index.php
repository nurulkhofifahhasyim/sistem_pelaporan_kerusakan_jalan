<?php $pageTitle = 'Kelola Pengaduan'; ?>

<div class="flex items-center justify-between mb-4">
  <h2 class="text-lg font-semibold text-slate-700">Data Pengaduan</h2>
  <a href="<?= $appConfig['base_url'] ?>/admin/pengaduan/export?status=<?= urlencode($filters['status'] ?? '') ?>&search=<?= urlencode($filters['search'] ?? '') ?>"
     class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
    Export CSV
  </a>
</div>

<?php if ($flash): ?>
<div class="bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-3 mb-4 text-emerald-700 text-sm flex items-center gap-2">
  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
  <?= htmlspecialchars($flash) ?>
</div>
<?php endif; ?>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 mb-4">
  <form method="GET" class="flex flex-wrap gap-3 items-end">
    <div class="flex-1 min-w-48">
      <label class="block text-xs font-medium text-slate-500 mb-1">Cari</label>
      <input type="text" name="search" value="<?= htmlspecialchars($filters['search']) ?>"
        class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
        placeholder="Kode tiket, nama, kelurahan..."/>
    </div>
    <div>
      <label class="block text-xs font-medium text-slate-500 mb-1">Status</label>
      <select name="status" class="border border-slate-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
        <option value="">Semua Status</option>
        <?php foreach (['Diproses','Selesai','Ditolak'] as $s): ?>
        <option value="<?= $s ?>" <?= $filters['status'] === $s ? 'selected' : '' ?>><?= $s ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-700 transition">Filter</button>
    <a href="<?= $appConfig['base_url'] ?>/admin/pengaduan" class="text-slate-500 text-sm hover:text-slate-700 py-2">Reset</a>
  </form>
</div>

<div class="flex gap-3 flex-wrap mb-4">
  <?php foreach (['total'=>'Total','Diproses'=>'Diproses','Selesai'=>'Selesai','Ditolak'=>'Ditolak'] as $k=>$v): ?>
  <span class="text-xs font-semibold px-3 py-1 rounded-full bg-slate-100 text-slate-600">
    <?= $v ?>: <?= $stats[$k] ?? 0 ?>
  </span>
  <?php endforeach; ?>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-slate-50 border-b border-slate-100">
        <tr>
          <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Kode Tiket</th>
          <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Pelapor</th>
          <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden md:table-cell">Foto</th>
          <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden md:table-cell">Lokasi</th>
          <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden lg:table-cell">Tanggal</th>
          <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</th>
          <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-50">
        <?php if (empty($list)): ?>
        <tr><td colspan="7" class="text-center py-10 text-slate-400">Tidak ada data pengaduan.</td></tr>
        <?php else: foreach ($list as $row): ?>
        <tr class="hover:bg-slate-50 transition">
          <td class="px-5 py-3 font-mono text-xs font-bold text-primary-600"><?= htmlspecialchars($row['kode_tiket']) ?></td>
          <td class="px-5 py-3 text-slate-700"><?= htmlspecialchars($row['nama_lengkap']) ?></td>
          <td class="px-5 py-3 hidden md:table-cell">
            <?php if ($row['foto_bukti']): ?>
            <img src="<?= $appConfig['upload_url'] . htmlspecialchars($row['foto_bukti']) ?>" alt="Bukti" class="w-12 h-12 object-cover rounded-lg border border-slate-200"/>
            <?php else: ?>
            <span class="text-slate-300 text-xs">—</span>
            <?php endif; ?>
          </td>
          <td class="px-5 py-3 text-slate-500 hidden md:table-cell"><?= htmlspecialchars($row['nama_kelurahan']) ?>, <?= htmlspecialchars($row['nama_kecamatan']) ?></td>
          <td class="px-5 py-3 text-slate-400 text-xs hidden lg:table-cell"><?= date('d M Y', strtotime($row['tgl_pengaduan'])) ?></td>
          <td class="px-5 py-3">
            <span class="status-badge status-<?= strtolower($row['status_pengaduan']) ?>"><?= htmlspecialchars($row['status_pengaduan']) ?></span>
          </td>
          <td class="px-5 py-3">
            <div class="flex items-center justify-end">
              <a href="<?= $appConfig['base_url'] ?>/admin/pengaduan/<?= $row['id_pengaduan'] ?>" class="inline-flex items-center gap-1 bg-primary-50 text-primary-600 hover:bg-primary-100 border border-primary-200 text-xs font-semibold px-3 py-1.5 rounded-lg transition" title="Lihat Detail">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                Detail
              </a>
            </div>
          </td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>
