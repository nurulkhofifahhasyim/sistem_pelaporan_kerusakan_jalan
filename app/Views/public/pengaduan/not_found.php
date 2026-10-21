<?php $pageTitle = 'Laporan Tidak Ditemukan'; ?>
<div class="max-w-lg mx-auto px-4 py-20 text-center">
  <div class="flex justify-center mb-4 text-slate-300">
    <i data-lucide="search" class="w-16 h-16"></i>
  </div>
  <h1 class="text-2xl font-bold text-slate-800 mb-2">Kode Tiket Tidak Ditemukan</h1>
  <p class="text-slate-500 mb-2">Kode <strong class="font-mono text-slate-700"><?= htmlspecialchars($kode) ?></strong> tidak terdaftar dalam sistem kami.</p>
  <p class="text-slate-400 text-sm mb-8">Pastikan Anda memasukkan kode yang benar, termasuk tanda hubung (-).</p>
  <a href="<?= $appConfig['base_url'] ?>/pengaduan/tracking" class="bg-primary-600 hover:bg-primary-700 text-white font-bold px-6 py-3 rounded-xl transition text-sm">Coba Lagi</a>
</div>
