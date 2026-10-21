<?php
$pageTitle = 'Monitor Logging';
$base = $appConfig['base_url'];

$query = [
    'range'      => $filters['range'],
    'level'      => $filters['level'],
    'q'          => $filters['q'],
    'endpoint'   => $filters['endpoint'],
    'request_id' => $filters['request_id'],
    'hide_req'   => $filters['hide_req'] ? '1' : '0',
];
$url = fn(array $over = []) => $base . '/admin/monitor-log?' . http_build_query(array_filter(array_merge($query, $over), fn($v) => $v !== ''));

$levelStyle = [
    'ERROR'   => 'bg-red-100 text-red-700 border-red-200',
    'WARNING' => 'bg-amber-100 text-amber-800 border-amber-200',
    'INFO'    => 'bg-sky-100 text-sky-700 border-sky-200',
];
$fmtTime = fn(?string $ts) => $ts ? date('d M Y H:i:s', strtotime($ts)) : '-';
$statusClass = fn(?int $s) => $s === null ? 'text-slate-400' : ($s >= 500 ? 'text-red-600 font-semibold' : ($s >= 400 ? 'text-amber-600 font-semibold' : 'text-emerald-600'));
?>

<div class="flex flex-wrap items-center justify-between gap-3 mb-4">
  <div>
    <h2 class="text-lg font-semibold text-slate-700">Monitor Logging Aplikasi</h2>
    <p class="text-sm text-slate-500">Sumber: <code class="bg-slate-200/60 px-1 rounded text-xs">logs/app.log</code> → Logstash → Elasticsearch
      <code class="bg-slate-200/60 px-1 rounded text-xs"><?= htmlspecialchars($esIndex) ?></code></p>
  </div>
  <div class="flex items-center gap-3">
    <label class="flex items-center gap-2 text-sm text-slate-600 cursor-pointer select-none">
      <input type="checkbox" id="autoRefresh" class="rounded border-slate-300"/> Auto-refresh 15 dtk
    </label>
    <a href="<?= htmlspecialchars($url()) ?>" class="inline-flex items-center gap-1.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-medium px-3 py-1.5 rounded-lg transition">
      <i data-lucide="refresh-cw" class="w-4 h-4"></i> Refresh
    </a>
    <a href="<?= htmlspecialchars($kibanaUrl) ?>/app/discover" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium px-3 py-1.5 rounded-lg transition">
      <i data-lucide="external-link" class="w-4 h-4"></i> Buka Kibana
    </a>
  </div>
</div>

<!-- Filter -->
<form method="GET" action="<?= $base ?>/admin/monitor-log" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-5 grid grid-cols-2 md:grid-cols-6 gap-3 items-end">
  <div>
    <label class="block text-xs font-medium text-slate-500 mb-1">Rentang waktu</label>
    <select name="range" class="w-full border border-slate-200 rounded-lg px-2.5 py-2 text-sm bg-white">
      <?php foreach ($ranges as $k => [$label]): ?>
      <option value="<?= $k ?>" <?= $k === $filters['range'] ? 'selected' : '' ?>><?= $label ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div>
    <label class="block text-xs font-medium text-slate-500 mb-1">Level</label>
    <select name="level" class="w-full border border-slate-200 rounded-lg px-2.5 py-2 text-sm bg-white">
      <option value="">Semua</option>
      <?php foreach ($levels as $lv): ?>
      <option value="<?= $lv ?>" <?= $lv === $filters['level'] ? 'selected' : '' ?>><?= $lv ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-span-2">
    <label class="block text-xs font-medium text-slate-500 mb-1">Cari pesan / context</label>
    <input type="text" name="q" value="<?= htmlspecialchars($filters['q']) ?>" placeholder='cth: PDOException, "Login admin gagal"'
      class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm"/>
  </div>
  <div>
    <label class="block text-xs font-medium text-slate-500 mb-1">Endpoint</label>
    <input type="text" name="endpoint" value="<?= htmlspecialchars($filters['endpoint']) ?>" placeholder="/admin/pengaduan"
      class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm"/>
  </div>
  <div>
    <label class="block text-xs font-medium text-slate-500 mb-1">Request ID</label>
    <input type="text" name="request_id" value="<?= htmlspecialchars($filters['request_id']) ?>" placeholder="16 karakter hex"
      class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm font-mono"/>
  </div>
  <div class="col-span-2 md:col-span-6 flex flex-wrap items-center justify-between gap-3">
    <label class="flex items-center gap-2 text-sm text-slate-600">
      <input type="hidden" name="hide_req" value="0"/>
      <input type="checkbox" name="hide_req" value="1" <?= $filters['hide_req'] ? 'checked' : '' ?> class="rounded border-slate-300"/>
      Sembunyikan log "HTTP request" di tabel (tetap dihitung di grafik)
    </label>
    <div class="flex gap-2">
      <a href="<?= $base ?>/admin/monitor-log" class="text-sm text-slate-500 hover:text-slate-700 px-3 py-2">Reset</a>
      <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold px-5 py-2 rounded-lg transition">Terapkan</button>
    </div>
  </div>
</form>

<?php if ($error): ?>
<div class="bg-red-50 border border-red-200 rounded-2xl p-5 mb-5">
  <div class="flex items-start gap-3">
    <i data-lucide="plug-zap" class="w-5 h-5 text-red-600 mt-0.5 flex-shrink-0"></i>
    <div class="text-sm">
      <p class="font-semibold text-red-700 mb-1">Gagal mengambil log dari Elasticsearch</p>
      <p class="text-red-600 font-mono text-xs break-all mb-3"><?= htmlspecialchars($error) ?></p>
      <p class="text-slate-600 mb-1">Periksa:</p>
      <ul class="list-disc ml-5 text-slate-600 space-y-0.5">
        <li>Elasticsearch berjalan dan dapat diakses di <code class="text-xs bg-white px-1 rounded"><?= htmlspecialchars($esHost) ?></code></li>
        <li><code class="text-xs bg-white px-1 rounded">ELASTICSEARCH_USER</code> / <code class="text-xs bg-white px-1 rounded">ELASTICSEARCH_PASSWORD</code> di file <code class="text-xs bg-white px-1 rounded">.env</code> sudah benar, lalu jalankan <code class="text-xs bg-white px-1 rounded">docker compose up -d</code></li>
        <li>Container Logstash berjalan: <code class="text-xs bg-white px-1 rounded">docker compose logs logstash</code></li>
      </ul>
    </div>
  </div>
</div>
<?php else: ?>

<!-- Statistik -->
<?php
$s = $log['stats'];
$cards = [
    ['Total Request',       number_format($s['total_req']),                  'activity',       'text-primary-600', 'bg-primary-50', null],
    ['Error (log)',         number_format($log['levels']['ERROR']),         'octagon-alert',  'text-red-600',     'bg-red-50',     $url(['level' => 'ERROR', 'page' => ''])],
    ['Warning (log)',       number_format($log['levels']['WARNING']),       'triangle-alert', 'text-amber-600',   'bg-amber-50',   $url(['level' => 'WARNING', 'page' => ''])],
    ['Response 5xx',        number_format($s['req_5xx']) . ' <span class="text-sm font-medium text-slate-400">(' . $s['err_rate'] . '%)</span>', 'server-crash', 'text-rose-600', 'bg-rose-50', null],
    ['Response 4xx',        number_format($s['req_4xx']),                    'circle-slash',   'text-orange-600',  'bg-orange-50',  null],
    ['Rata-rata / p95',     ($s['avg_ms'] ?? '-') . ' <span class="text-sm font-medium text-slate-400">/ ' . ($s['p95_ms'] ?? '-') . ' ms</span>', 'timer', 'text-emerald-600', 'bg-emerald-50', null],
];
?>
<div class="grid grid-cols-2 lg:grid-cols-6 gap-3 mb-5">
  <?php foreach ($cards as [$label, $val, $icon, $color, $bg, $link]): ?>
  <<?= $link ? 'a href="' . htmlspecialchars($link) . '"' : 'div' ?> class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm <?= $link ? 'hover:border-slate-300 transition' : '' ?>">
    <div class="w-8 h-8 <?= $bg ?> rounded-lg flex items-center justify-center mb-2">
      <i data-lucide="<?= $icon ?>" class="w-4 h-4 <?= $color ?>"></i>
    </div>
    <div class="text-xl font-bold text-slate-800"><?= $val ?></div>
    <div class="text-xs text-slate-500 mt-0.5"><?= $label ?></div>
  </<?= $link ? 'a' : 'div' ?>>
  <?php endforeach; ?>
</div>

<!-- Grafik -->
<div class="grid lg:grid-cols-2 gap-5 mb-5">
  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
    <h3 class="font-semibold text-slate-700 text-sm">Linimasa Log per Level</h3>
    <p class="text-xs text-slate-400 mb-3"><?= $ranges[$filters['range']][0] ?> · interval <?= $ranges[$filters['range']][1] ?> · <?= number_format($log['all_events']) ?> event · zona waktu <?= date_default_timezone_get() ?></p>
    <div class="h-64"><canvas id="chartTimeline"></canvas></div>
  </div>
  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
    <h3 class="font-semibold text-slate-700 text-sm">Jumlah Request &amp; Response Time</h3>
    <p class="text-xs text-slate-400 mb-3">Bar = jumlah request per status · garis = rata-rata durasi (ms)</p>
    <div class="h-64"><canvas id="chartRequests"></canvas></div>
  </div>
</div>

<!-- Ringkasan untuk debugging -->
<div class="grid lg:grid-cols-3 gap-5 mb-5">
  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
    <h3 class="font-semibold text-slate-700 text-sm mb-3">Error &amp; Warning Teratas</h3>
    <?php if (empty($log['top_errors'])): ?>
    <p class="text-sm text-slate-400">Tidak ada error atau warning 🎉</p>
    <?php else: ?>
    <ul class="space-y-2">
      <?php foreach ($log['top_errors'] as $e): ?>
      <li>
        <a href="<?= htmlspecialchars($url(['q' => '"' . $e['message'] . '"', 'level' => $e['level'], 'page' => ''])) ?>" class="flex items-start gap-2 group">
          <span class="text-[10px] font-bold px-1.5 py-0.5 rounded border <?= $levelStyle[$e['level']] ?? '' ?>"><?= $e['count'] ?>×</span>
          <span class="text-sm text-slate-700 group-hover:text-primary-600 break-all leading-snug"><?= htmlspecialchars($e['message']) ?>
            <span class="block text-xs text-slate-400">terakhir <?= $fmtTime($e['last_seen']) ?></span>
          </span>
        </a>
      </li>
      <?php endforeach; ?>
    </ul>
    <?php endif; ?>
  </div>
  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
    <h3 class="font-semibold text-slate-700 text-sm mb-3">Endpoint dengan Error / 5xx</h3>
    <?php if (empty($log['error_endpoints'])): ?>
    <p class="text-sm text-slate-400">Tidak ada.</p>
    <?php else: ?>
    <ul class="space-y-2">
      <?php foreach ($log['error_endpoints'] as $e): ?>
      <li class="flex items-center justify-between gap-2">
        <a href="<?= htmlspecialchars($url(['endpoint' => $e['endpoint'], 'level' => 'ERROR', 'page' => ''])) ?>" class="text-sm font-mono text-slate-700 hover:text-primary-600 truncate"><?= htmlspecialchars($e['endpoint']) ?></a>
        <span class="text-xs font-semibold text-red-600"><?= $e['count'] ?></span>
      </li>
      <?php endforeach; ?>
    </ul>
    <?php endif; ?>
  </div>
  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
    <h3 class="font-semibold text-slate-700 text-sm mb-3">Endpoint Paling Lambat</h3>
    <?php if (empty($log['slowest'])): ?>
    <p class="text-sm text-slate-400">Belum ada data request.</p>
    <?php else: ?>
    <ul class="space-y-2">
      <?php foreach ($log['slowest'] as $e): ?>
      <li class="flex items-center justify-between gap-2">
        <a href="<?= htmlspecialchars($url(['endpoint' => $e['endpoint'], 'hide_req' => '0', 'page' => ''])) ?>" class="text-sm font-mono text-slate-700 hover:text-primary-600 truncate"><?= htmlspecialchars($e['endpoint']) ?></a>
        <span class="text-xs text-slate-500 whitespace-nowrap"><?= $e['avg_ms'] ?> ms · <?= $e['count'] ?>×</span>
      </li>
      <?php endforeach; ?>
    </ul>
    <?php endif; ?>
  </div>
</div>

<!-- Tabel log -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
  <div class="flex flex-wrap items-center justify-between gap-2 px-5 py-3 border-b border-slate-100">
    <h3 class="font-semibold text-slate-700 text-sm">Log Event
      <span class="text-slate-400 font-normal">(<?= number_format($log['total_events']) ?> hasil<?= $log['total_events'] >= 10000 ? '+' : '' ?>)</span></h3>
    <?php if ($filters['request_id'] !== ''): ?>
    <span class="text-xs bg-primary-50 text-primary-700 px-2 py-1 rounded-lg">Menampilkan semua log untuk request <code><?= htmlspecialchars($filters['request_id']) ?></code></span>
    <?php endif; ?>
  </div>

  <?php if (empty($log['hits'])): ?>
  <div class="text-center py-12 text-slate-400 text-sm">
    <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2"></i>
    Tidak ada log yang cocok pada rentang waktu ini.
    <?php if ($log['all_events'] === 0): ?>
    <p class="text-xs mt-1">Belum ada data di index. Pastikan container Logstash berjalan dan mengirim <code>logs/app.log</code>.</p>
    <?php endif; ?>
  </div>
  <?php else: ?>
  <div class="divide-y divide-slate-100">
    <?php foreach ($log['hits'] as $h):
      $lv     = $h['level'] ?? 'INFO';
      $status = isset($h['http']['status_code']) ? (int) $h['http']['status_code'] : null;
      $ctx    = $h['context'] ?? [];
      $trace  = is_array($ctx) ? ($ctx['trace'] ?? null) : null;
      $raw    = $h;
      unset($raw['_id'], $raw['_index'], $raw['context_text'], $raw['event'], $raw['log']);
      $json   = json_encode($raw, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    ?>
    <details class="group <?= $lv === 'ERROR' ? 'bg-red-50/40' : '' ?>">
      <summary class="list-none cursor-pointer px-5 py-2.5 hover:bg-slate-50 grid grid-cols-12 gap-3 items-center text-sm">
        <span class="col-span-12 md:col-span-2 text-xs text-slate-500 font-mono whitespace-nowrap"><?= $fmtTime($h['@timestamp'] ?? null) ?></span>
        <span class="col-span-3 md:col-span-1"><span class="text-[10px] font-bold px-1.5 py-0.5 rounded border <?= $levelStyle[$lv] ?? '' ?>"><?= htmlspecialchars($lv) ?></span></span>
        <span class="col-span-9 md:col-span-5 text-slate-800 break-all"><?= htmlspecialchars($h['message'] ?? '') ?>
          <?php if (!empty($ctx['error_message'])): ?><span class="block text-xs text-red-600 font-mono truncate"><?= htmlspecialchars($ctx['error_message']) ?></span><?php endif; ?>
        </span>
        <span class="col-span-9 md:col-span-3 text-xs font-mono text-slate-500 truncate" title="<?= htmlspecialchars($h['http']['endpoint'] ?? '') ?>">
          <span class="text-slate-400"><?= htmlspecialchars($h['http']['method'] ?? '') ?></span> <?= htmlspecialchars($h['http']['endpoint'] ?? '') ?>
        </span>
        <span class="col-span-3 md:col-span-1 text-xs text-right font-mono <?= $statusClass($status) ?>">
          <?= $status ?? '' ?><?= isset($h['http']['duration_ms']) ? '<span class="text-slate-400 font-normal"> ' . $h['http']['duration_ms'] . 'ms</span>' : '' ?>
        </span>
      </summary>
      <div class="px-5 pb-4 pt-1 bg-slate-50 border-t border-slate-100">
        <div class="flex flex-wrap gap-x-6 gap-y-1 text-xs text-slate-600 mb-3">
          <span>Request ID:
            <?php if (!empty($h['request_id'])): ?>
            <a href="<?= htmlspecialchars($url(['request_id' => $h['request_id'], 'level' => '', 'q' => '', 'endpoint' => '', 'page' => ''])) ?>" class="font-mono text-primary-600 hover:underline"><?= htmlspecialchars($h['request_id']) ?></a>
            <span class="text-slate-400">(lihat semua log request ini)</span>
            <?php else: ?>-<?php endif; ?>
          </span>
          <span>Aktor: <b><?= htmlspecialchars((string) ($h['actor']['role'] ?? '-')) ?></b><?= !empty($h['actor']['user_id']) ? ' #' . htmlspecialchars((string) $h['actor']['user_id']) : '' ?></span>
          <span>IP: <span class="font-mono"><?= htmlspecialchars($h['http']['ip_address'] ?? '-') ?></span></span>
          <?php if (!empty($ctx['file'])): ?>
          <span>Lokasi: <span class="font-mono text-red-700"><?= htmlspecialchars($ctx['file']) ?>:<?= htmlspecialchars((string) ($ctx['line'] ?? '')) ?></span></span>
          <?php endif; ?>
        </div>
        <?php if ($trace): ?>
        <p class="text-xs font-semibold text-slate-500 mb-1">Stack trace</p>
        <pre class="bg-slate-900 text-red-200 text-xs rounded-lg p-3 overflow-x-auto mb-3 whitespace-pre"><?= htmlspecialchars($trace) ?></pre>
        <?php endif; ?>
        <div class="flex items-center justify-between mb-1">
          <p class="text-xs font-semibold text-slate-500">Dokumen lengkap</p>
          <button type="button" onclick="copyJson(this)" class="text-xs text-primary-600 hover:underline">Salin JSON</button>
        </div>
        <pre class="json bg-slate-900 text-slate-100 text-xs rounded-lg p-3 overflow-x-auto max-h-80"><?= htmlspecialchars($json) ?></pre>
        <p class="text-[11px] text-slate-400 mt-1 font-mono"><?= htmlspecialchars($h['_index']) ?> / <?= htmlspecialchars($h['_id']) ?></p>
      </div>
    </details>
    <?php endforeach; ?>
  </div>

  <?php
  $lastPage = (int) ceil(min($log['total_events'], 10000) / $perPage);
  if ($lastPage > 1):
  ?>
  <div class="flex items-center justify-between px-5 py-3 border-t border-slate-100 text-sm">
    <span class="text-slate-500">Halaman <?= $page ?> dari <?= $lastPage ?></span>
    <div class="flex gap-2">
      <?php if ($page > 1): ?>
      <a href="<?= htmlspecialchars($url(['page' => $page - 1])) ?>" class="px-3 py-1.5 border border-slate-200 rounded-lg hover:bg-slate-50">← Sebelumnya</a>
      <?php endif; ?>
      <?php if ($page < $lastPage): ?>
      <a href="<?= htmlspecialchars($url(['page' => $page + 1])) ?>" class="px-3 py-1.5 border border-slate-200 rounded-lg hover:bg-slate-50">Berikutnya →</a>
      <?php endif; ?>
    </div>
  </div>
  <?php endif; ?>
  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
(function () {
  const timeline = <?= json_encode($log['timeline']) ?>;
  const requests = <?= json_encode($log['requests']) ?>;
  const longRange = <?= json_encode(in_array($filters['range'], ['7d', '30d'], true)) ?>;
  // Pakai zona waktu server agar sama dengan waktu di tabel log
  const tz = <?= json_encode(date_default_timezone_get()) ?>;
  const fmt = new Intl.DateTimeFormat('id-ID', longRange
    ? { timeZone: tz, day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit', hour12: false }
    : { timeZone: tz, hour: '2-digit', minute: '2-digit', hour12: false });
  const labels = arr => arr.map(ms => fmt.format(new Date(ms)));
  const common = {
    responsive: true, maintainAspectRatio: false, animation: false,
    interaction: { mode: 'index', intersect: false },
    plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11 } } } },
  };

  new Chart(document.getElementById('chartTimeline'), {
    type: 'bar',
    data: {
      labels: labels(timeline.labels),
      datasets: [
        { label: 'INFO',    data: timeline.INFO,    backgroundColor: '#7dd3fc' },
        { label: 'WARNING', data: timeline.WARNING, backgroundColor: '#fbbf24' },
        { label: 'ERROR',   data: timeline.ERROR,   backgroundColor: '#ef4444' },
      ],
    },
    options: { ...common, scales: {
      x: { stacked: true, grid: { display: false }, ticks: { maxTicksLimit: 12, font: { size: 10 } } },
      y: { stacked: true, beginAtZero: true, ticks: { precision: 0 } },
    } },
  });

  new Chart(document.getElementById('chartRequests'), {
    data: {
      labels: labels(requests.labels),
      datasets: [
        { type: 'bar',  label: '2xx/3xx', data: requests.ok,      backgroundColor: '#34d399', stack: 'req', yAxisID: 'y' },
        { type: 'bar',  label: '4xx',     data: requests['4xx'],  backgroundColor: '#fb923c', stack: 'req', yAxisID: 'y' },
        { type: 'bar',  label: '5xx',     data: requests['5xx'],  backgroundColor: '#e11d48', stack: 'req', yAxisID: 'y' },
        { type: 'line', label: 'Rata-rata ms', data: requests.avg_ms, borderColor: '#1e3a8a', backgroundColor: '#1e3a8a',
          pointRadius: 0, borderWidth: 1.5, spanGaps: true, yAxisID: 'ms' },
      ],
    },
    options: { ...common, scales: {
      x:  { stacked: true, grid: { display: false }, ticks: { maxTicksLimit: 12, font: { size: 10 } } },
      y:  { stacked: true, beginAtZero: true, ticks: { precision: 0 }, title: { display: true, text: 'request' } },
      ms: { position: 'right', beginAtZero: true, grid: { display: false }, title: { display: true, text: 'ms' } },
    } },
  });
})();
</script>
<?php endif; ?>

<script>
function copyJson(btn) {
  const pre = btn.closest('div').nextElementSibling;
  navigator.clipboard.writeText(pre.textContent).then(() => {
    btn.textContent = 'Tersalin ✓';
    setTimeout(() => btn.textContent = 'Salin JSON', 1500);
  });
}
(function () {
  const box = document.getElementById('autoRefresh');
  let on = false;
  try { on = localStorage.getItem('monitorAutoRefresh') === '1'; } catch (e) {}
  box.checked = on;
  let timer = null;
  const apply = () => {
    clearInterval(timer);
    // Jangan reload saat developer sedang membuka detail log
    if (box.checked) timer = setInterval(() => { if (!document.querySelector('details[open]')) location.reload(); }, 15000);
  };
  box.addEventListener('change', () => {
    try { localStorage.setItem('monitorAutoRefresh', box.checked ? '1' : '0'); } catch (e) {}
    apply();
  });
  apply();
})();
</script>
