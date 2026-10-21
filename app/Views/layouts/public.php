<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($pageTitle ?? 'LaporJalan Kendari') ?> — LaporJalan Kendari</title>
  <meta name="description" content="Platform pelaporan kerusakan jalan Kota Kendari, Sulawesi Tenggara. Laporkan, lacak, dan pantau perbaikan jalan di wilayah Anda."/>
  <link rel="icon" type="image/png" href="<?= $appConfig['asset_url'] ?>/assets/img/logo-lapor-jalan-cropped.png"/>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"/>
  <script>
    window.APP_BASE_URL = <?= json_encode($appConfig['base_url']) ?>;
    window.APP_ASSET_URL = <?= json_encode($appConfig['asset_url']) ?>;

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
    .gradient-hero { background: linear-gradient(135deg, #031027 0%, #071f52 52%, #143f91 100%); }
    .glass { background: rgba(255,255,255,0.08); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.15); }
    .card-hover { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
    .status-diproses { background:#dbe8ff; color:#071f52; }
    .status-selesai  { background:#d1fae5; color:#065f46; }
    .status-ditolak  { background:#fee2e2; color:#991b1b; }
    /* Lucide icon sizing helper */
    [data-lucide] { display:inline-block; }
  </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col">

<nav class="bg-white border-b border-slate-200 shadow-sm sticky top-0 z-50">
  <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between h-16">
      <a href="<?= $appConfig['base_url'] ?>/" class="flex items-center">
        <img src="<?= $appConfig['asset_url'] ?>/assets/img/logo-lapor-jalan-cropped.png" alt="Lapor Jalan" class="h-10 w-auto object-contain"/>
      </a>

      <div class="hidden md:flex items-center gap-6">
        <a href="<?= $appConfig['base_url'] ?>/" class="text-slate-600 hover:text-blue-600 text-sm font-medium transition">Beranda</a>
        <a href="<?= $appConfig['base_url'] ?>/pengaduan" class="text-slate-600 hover:text-blue-600 text-sm font-medium transition">Laporkan</a>
        <a href="<?= $appConfig['base_url'] ?>/pengaduan/tracking" class="text-slate-600 hover:text-blue-600 text-sm font-medium transition">Cek Status</a>
        <a href="<?= $appConfig['base_url'] ?>/saran" class="text-slate-600 hover:text-blue-600 text-sm font-medium transition">Saran</a>
      </div>

      <button id="mobile-menu-btn" class="md:hidden text-slate-600 p-2">
        <i data-lucide="menu" class="w-6 h-6"></i>
      </button>
    </div>

    <div id="mobile-menu" class="hidden md:hidden pb-4 border-t border-slate-100 pt-2">
      <a href="<?= $appConfig['base_url'] ?>/" class="block text-slate-600 hover:text-blue-600 py-2 text-sm">Beranda</a>
      <a href="<?= $appConfig['base_url'] ?>/pengaduan" class="block text-slate-600 hover:text-blue-600 py-2 text-sm">Laporkan</a>
      <a href="<?= $appConfig['base_url'] ?>/pengaduan/tracking" class="block text-slate-600 hover:text-blue-600 py-2 text-sm">Cek Status</a>
      <a href="<?= $appConfig['base_url'] ?>/saran" class="block text-slate-600 hover:text-blue-600 py-2 text-sm">Saran</a>
    </div>
  </div>
</nav>

<main class="flex-1">
  <?= $content ?>
</main>

<footer class="bg-white border-t border-slate-200 mt-auto">
  <div class="max-w-6xl mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row items-center justify-between gap-4">
      <div class="flex items-center">
        <img src="<?= $appConfig['asset_url'] ?>/assets/img/logo-lapor-jalan-cropped.png" alt="Lapor Jalan" class="h-9 w-auto object-contain"/>
      </div>
      <p class="text-sm text-slate-500 text-center">© <?= date('Y') ?> Sistem Pelaporan Kerusakan Jalan Kota Kendari, Sulawesi Tenggara.</p>
    </div>
  </div>
</footer>

<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
<script>
  lucide.createIcons();
  document.getElementById('mobile-menu-btn').addEventListener('click', function() {
    document.getElementById('mobile-menu').classList.toggle('hidden');
  });
</script>
<?php if (isset($extraJs)): ?>
  <script src="<?= $appConfig['asset_url'] ?>/assets/js/<?= $extraJs ?>.js"></script>
<?php endif; ?>
</body>
</html>
