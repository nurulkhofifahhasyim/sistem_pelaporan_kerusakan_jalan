<?php $pageTitle = 'Query Recommendation — ELK Stack'; ?>

<div class="mb-6">
  <h2 class="text-lg font-semibold text-slate-700">Query Elasticsearch Recommendation</h2>
  <p class="text-slate-500 text-sm mt-1">Sistem otomatis mendeteksi pola error dari <code class="bg-slate-100 px-1 rounded text-xs">logs/app.log</code> dan menghasilkan Query DSL siap-pakai untuk Kibana Dev Tools.</p>
</div>

<div class="bg-primary-50 border border-primary-100 rounded-2xl p-5 mb-6 text-sm text-primary-800">
  <p class="font-semibold mb-2">📌 Cara menggunakan query ini:</p>
  <ol class="list-decimal list-inside space-y-1 text-primary-700">
    <li>Buka Kibana → klik menu ☰ → <strong>Console</strong> (atau Dev Tools)</li>
    <li>Salin query di bawah → paste ke panel kiri Console</li>
    <li>Tekan <kbd class="bg-primary-100 border border-primary-300 px-1 rounded text-xs">Ctrl + Enter</kbd> untuk menjalankan</li>
  </ol>
  <div class="mt-3 pt-3 border-t border-primary-200">
    <p class="text-primary-600 text-xs">
      <strong>⏱ Time Range:</strong> Query menggunakan rentang waktu <code class="bg-primary-100 px-1 rounded"><?= htmlspecialchars($timeRange) ?></code> — dihitung otomatis berdasarkan log tertua yang ada.
    </p>
  </div>
</div>

<?php if (empty($patterns)): ?>
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-12 text-center">
  <div class="text-5xl mb-3">📭</div>
  <h3 class="font-semibold text-slate-600 mb-1">Belum ada data log</h3>
  <p class="text-slate-400 text-sm">File <code>logs/app.log</code> masih kosong atau belum ada pola error yang terdeteksi. Gunakan aplikasi terlebih dahulu untuk menghasilkan log.</p>
</div>
<?php else: ?>
<div class="space-y-4">
  <?php foreach ($patterns as $i => $p):
    $severityClass = $p['severity'] === 'ERROR' ? 'bg-red-50 border-red-200' :
                    ($p['severity'] === 'WARNING' ? 'bg-amber-50 border-amber-200' : 'bg-primary-50 border-primary-200');
    $badgeClass = $p['severity'] === 'ERROR' ? 'bg-red-100 text-red-700' :
                 ($p['severity'] === 'WARNING' ? 'bg-amber-100 text-amber-700' : 'bg-primary-100 text-primary-700');
  ?>
  <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">

    <div class="px-5 py-4 flex flex-wrap items-center justify-between gap-3 border-b border-slate-100">
      <div class="flex items-center gap-3">
        <span class="<?= $badgeClass ?> text-xs font-bold px-2.5 py-1 rounded-full"><?= $p['severity'] ?></span>
        <h3 class="font-semibold text-slate-800"><?= htmlspecialchars($p['title']) ?></h3>
        <span class="bg-slate-100 text-slate-600 text-xs px-2 py-0.5 rounded-full font-medium"><?= $p['count'] ?>x</span>
      </div>
      <button onclick="copyQuery('query-<?= $i ?>')"
        class="bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold px-3 py-1.5 rounded-lg transition flex items-center gap-1.5" id="copy-btn-<?= $i ?>">
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
        Copy Query
      </button>
    </div>

    <div class="px-5 py-3 bg-slate-50 border-b border-slate-100">
      <p class="text-sm text-slate-600"><?= htmlspecialchars($p['desc']) ?></p>
    </div>

    <div class="px-5 pt-3 pb-1">
      <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Elasticsearch Query DSL</p>
      <p class="text-xs text-slate-400 font-mono mb-1">GET /app-logs-*/_search</p>
    </div>

    <div class="px-5 pb-5">
      <pre id="query-<?= $i ?>" class="bg-slate-900 text-emerald-300 rounded-xl p-4 text-xs overflow-x-auto font-mono leading-relaxed"><?= htmlspecialchars($p['query']) ?></pre>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<script>
function copyQuery(id) {
  const el = document.getElementById(id);
  navigator.clipboard.writeText('GET /app-logs-*/_search\n' + el.textContent).then(() => {
    const parts = id.split('-');
    const i = parts[parts.length - 1];
    const btn = document.getElementById('copy-btn-' + i);
    const orig = btn.innerHTML;
    btn.innerHTML = '✅ Tersalin!';
    btn.classList.add('bg-emerald-600');
    btn.classList.remove('bg-slate-800');
    setTimeout(() => { btn.innerHTML = orig; btn.classList.remove('bg-emerald-600'); btn.classList.add('bg-slate-800'); }, 2000);
  });
}
</script>
