<?php $pageTitle = 'Saran Terkirim'; ?>
<div class="max-w-lg mx-auto px-4 py-20 text-center">
  <div class="flex justify-center mb-4 text-emerald-500">
    <i data-lucide="check-circle-2" class="w-16 h-16"></i>
  </div>
  <h1 class="text-2xl font-bold text-slate-800 mb-2">Terima Kasih!</h1>
  <p class="text-slate-500 mb-8 text-sm">Saran Anda telah berhasil dikirim. Kami menghargai setiap masukan dari masyarakat untuk meningkatkan kualitas layanan.</p>
  <div class="flex flex-col sm:flex-row gap-3 justify-center">
    <a href="<?= $appConfig['base_url'] ?>/" class="bg-slate-800 hover:bg-slate-700 text-white font-bold px-6 py-3 rounded-xl transition text-sm">Kembali ke Beranda</a>
    <a href="<?= $appConfig['base_url'] ?>/pengaduan" class="bg-primary-600 hover:bg-primary-700 text-white font-bold px-6 py-3 rounded-xl transition text-sm">Buat Laporan</a>
  </div>
</div>
