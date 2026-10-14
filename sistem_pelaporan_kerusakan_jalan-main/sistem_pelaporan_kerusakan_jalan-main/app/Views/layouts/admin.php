<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?> — LaporJalan Kendari</title>
  <link rel="icon" type="image/png" href="<?= $appConfig['asset_url'] ?>/assets/img/logo-lapor-jalan-cropped.png"/>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"/>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { sans: ['Inter', 'sans-serif'] },
          colors: {
            primary: { 50:'#eef4ff',100:'#dbe8ff',200:'#b9cff9',300:'#8faeed',400:'#5f86dc',500:'#2f5fbf',600:'#143f91',700:'#0b2c6b',800:'#071f52',900:'#05183f',950:'#031027' },
            road: { 500:'#4b5563',700:'#303846',900:'#111827' },
            mark: { 400:'#fbbf24',500:'#f59e0b' },
          }
        }
      }
    }
  </script>
  <style>
    body { font-family: 'Inter', sans-serif; }
    .sidebar-link { transition: all 0.15s ease; }
    .sidebar-link:hover, .sidebar-link.active { background: rgba(255,255,255,0.1); }
    .status-badge { display:inline-flex; align-items:center; padding:2px 10px; border-radius:9999px; font-size:.75rem; font-weight:600; }
    .status-diproses { background:#dbe8ff; color:#071f52; }
    .status-selesai  { background:#d1fae5; color:#065f46; }
    .status-ditolak  { background:#fee2e2; color:#991b1b; }
    [data-lucide] { display:inline-block; }
  </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased flex h-screen overflow-hidden" style="font-family:'Inter',sans-serif">

<aside id="sidebar" class="w-64 bg-primary-950 text-white flex-shrink-0 flex flex-col fixed inset-y-0 left-0 z-50 transform -translate-x-full md:relative md:translate-x-0 transition-transform duration-300">

  <div class="flex items-center gap-3 px-5 py-5 border-b border-white/10">
    <div class="bg-white rounded-lg px-2 py-1 flex-shrink-0 shadow-sm">
    <img src="<?= $appConfig['asset_url'] ?>/assets/img/logo-lapor-jalan-cropped.png" alt="Lapor Jalan" class="h-9 w-auto object-contain flex-shrink-0"/>
    </div>
    <div>
      <div class="font-bold text-sm leading-tight">LaporJalan</div>
      <div class="text-xs text-slate-400">Panel Admin</div>
    </div>
  </div>

  <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
    <?php $base = $appConfig['base_url']; $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); ?>
    <a href="<?= $base ?>/admin/dashboard" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-slate-300 <?= str_contains($uri,'dashboard')?'active':'' ?>">
      <i data-lucide="layout-dashboard" class="w-4 h-4 flex-shrink-0"></i>
      Dashboard
    </a>
    <a href="<?= $base ?>/admin/pengaduan" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-slate-300 <?= str_contains($uri,'pengaduan')?'active':'' ?>">
      <i data-lucide="clipboard-list" class="w-4 h-4 flex-shrink-0"></i>
      Pengaduan
    </a>
    <a href="<?= $base ?>/admin/saran" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-slate-300 <?= str_contains($uri,'/saran')?'active':'' ?>">
      <i data-lucide="message-square" class="w-4 h-4 flex-shrink-0"></i>
      Saran
    </a>

    <div class="pt-2 pb-1 px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Master Data</div>
    <?php $isWilayah = str_contains($uri,'kecamatan') || str_contains($uri,'kelurahan'); ?>
    <div>
      <button onclick="document.getElementById('submenu-wilayah').classList.toggle('hidden'); this.querySelector('[data-lucide=chevron-down]')?.classList.toggle('rotate-180')"
        class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-slate-300 w-full text-left <?= $isWilayah?'active':'' ?>">
        <i data-lucide="map-pin" class="w-4 h-4 flex-shrink-0"></i>
        Kelola Wilayah
        <i data-lucide="chevron-down" class="w-3.5 h-3.5 ml-auto transition-transform <?= $isWilayah?'rotate-180':'' ?>"></i>
      </button>
      <div id="submenu-wilayah" class="ml-7 mt-1 space-y-1 <?= $isWilayah?'':'hidden' ?>">
        <a href="<?= $base ?>/admin/kecamatan" class="sidebar-link flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-slate-400 <?= str_contains($uri,'kecamatan')?'active text-white':'' ?>">
          <i data-lucide="map" class="w-3.5 h-3.5 flex-shrink-0"></i>
          Kecamatan
        </a>
        <a href="<?= $base ?>/admin/kelurahan" class="sidebar-link flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-slate-400 <?= str_contains($uri,'kelurahan')?'active text-white':'' ?>">
          <i data-lucide="building-2" class="w-3.5 h-3.5 flex-shrink-0"></i>
          Kelurahan
        </a>
      </div>
    </div>

    <div class="pt-2 pb-1 px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">ELK & Monitoring</div>
    <a href="<?= $base ?>/admin/log-aktivitas" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-slate-300 <?= str_contains($uri,'log-aktivitas')?'active':'' ?>">
      <i data-lucide="file-text" class="w-4 h-4 flex-shrink-0"></i>
      Log Aktivitas
    </a>
    <a href="<?= $base ?>/admin/query-recommend" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-slate-300 <?= str_contains($uri,'query-recommend')?'active':'' ?>">
      <i data-lucide="terminal" class="w-4 h-4 flex-shrink-0"></i>
      Query Recommend
    </a>
  </nav>

  <div class="px-4 py-4 border-t border-white/10">
    <a href="<?= $base ?>/admin/logout" class="flex items-center gap-2 text-slate-400 hover:text-red-400 text-sm transition">
      <i data-lucide="log-out" class="w-4 h-4"></i>
      Keluar
    </a>
  </div>
</aside>

<div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden" onclick="closeSidebar()"></div>

<div class="flex-1 flex flex-col overflow-hidden">

  <header class="bg-white border-b border-slate-200 px-4 sm:px-6 py-3 flex items-center gap-4">
    <button onclick="toggleSidebar()" class="md:hidden text-slate-500 hover:text-slate-700">
      <i data-lucide="menu" class="w-6 h-6"></i>
    </button>
    <h1 class="text-base font-semibold text-slate-700 flex-1"><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></h1>
    <a href="<?= $appConfig['base_url'] ?>/" target="_blank" class="text-xs text-primary-600 hover:underline flex items-center gap-1">
      <i data-lucide="external-link" class="w-3 h-3"></i>
      Lihat Situs
    </a>
    <div class="hidden sm:flex items-center gap-3 ml-4 pl-4 border-l border-slate-200">
      <div class="text-right">
        <div class="text-sm font-medium text-slate-700"><?= htmlspecialchars($_SESSION['admin_nama'] ?? '') ?></div>
        <div class="text-xs text-slate-400"><?= htmlspecialchars($_SESSION['admin_jabatan'] ?? '') ?></div>
      </div>
      <div class="w-8 h-8 bg-primary-600 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
        <?= strtoupper(substr($_SESSION['admin_nama'] ?? 'A', 0, 1)) ?>
      </div>
    </div>
  </header>

  <main class="flex-1 overflow-y-auto p-4 sm:p-6">
    <?= $content ?>
  </main>
</div>

<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
<script>
  lucide.createIcons();
  function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('-translate-x-full');
    document.getElementById('sidebar-overlay').classList.toggle('hidden');
  }
  function closeSidebar() {
    document.getElementById('sidebar').classList.add('-translate-x-full');
    document.getElementById('sidebar-overlay').classList.add('hidden');
  }
</script>
</body>
</html>
