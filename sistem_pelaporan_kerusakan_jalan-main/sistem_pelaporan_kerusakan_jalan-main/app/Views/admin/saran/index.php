<?php $pageTitle = 'Saran & Aspirasi'; ?>
<div class="flex items-center justify-between mb-4">
  <h2 class="text-lg font-semibold text-slate-700">Data Saran & Aspirasi Masyarakat</h2>
  <span class="text-sm text-slate-400">Total: <?= count($list) ?> saran</span>
</div>
<?php if ($flash): ?><div class="bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-3 mb-4 text-emerald-700 text-sm"><?= htmlspecialchars($flash) ?></div><?php endif; ?>
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
  <div class="overflow-x-auto">
  <table class="w-full text-sm">
    <thead class="bg-slate-50 border-b border-slate-100">
      <tr>
        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">#</th>
        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Pengirim</th>
        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Isi Saran</th>
        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Tanggal</th>
        <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Aksi</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-slate-50">
      <?php if (empty($list)): ?>
      <tr><td colspan="5" class="text-center py-10 text-slate-400">Belum ada saran masuk.</td></tr>
      <?php else: foreach ($list as $i => $row): ?>
      <tr class="hover:bg-slate-50 transition">
        <td class="px-5 py-3 text-slate-400"><?= $i + 1 ?></td>
        <td class="px-5 py-3 font-medium text-slate-800"><?= htmlspecialchars($row['nama_pengirim'] ?: 'Anonim') ?></td>
        <td class="px-5 py-3 text-slate-700 max-w-md">
          <p class="line-clamp-2"><?= htmlspecialchars($row['isi_saran']) ?></p>
        </td>
        <td class="px-5 py-3 text-slate-400 text-xs"><?= date('d M Y H:i', strtotime($row['tgl_saran'])) ?></td>
        <td class="px-5 py-3">
          <div class="flex items-center gap-2 justify-end">
            <form method="POST" action="<?= $appConfig['base_url'] ?>/admin/saran/<?= $row['id_saran'] ?>/delete" onsubmit="return confirm('Yakin ingin menghapus saran ini?')">
              <button type="submit" class="inline-flex items-center gap-1 bg-red-50 text-red-600 hover:bg-red-100 border border-red-200 text-xs font-semibold px-3 py-1.5 rounded-lg transition" title="Hapus">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Hapus
              </button>
            </form>
          </div>
        </td>
      </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
  </div>
</div>
