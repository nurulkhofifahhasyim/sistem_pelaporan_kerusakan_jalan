<?php $pageTitle = 'Beranda'; ?>

<section class="bg-white border-b border-slate-100">
  <div class="max-w-5xl mx-auto px-4 py-16 md:py-24">
    <div class="text-center max-w-3xl mx-auto">
      <span class="inline-flex items-center gap-2 bg-blue-50 text-blue-600 rounded-full px-4 py-1.5 text-sm font-medium mb-6">
        <span class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></span>
        Layanan Pelaporan Kota Kendari
      </span>
      <h1 class="text-3xl md:text-5xl font-bold text-slate-900 mb-5 leading-tight">
        Laporkan Kerusakan Jalan<br/>di Wilayah Anda
      </h1>
      <p class="text-lg text-slate-500 mb-8 max-w-2xl mx-auto">
        Platform pelaporan digital Kota Kendari. Tanpa registrasi akun. Pantau progres perbaikan dengan kode tiket.
      </p>
      <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <a href="<?= $appConfig['base_url'] ?>/pengaduan"
           class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-7 py-3.5 rounded-lg transition text-sm flex items-center justify-center gap-2">
          <i data-lucide="plus" class="w-4 h-4"></i> Buat Laporan
        </a>
        <a href="<?= $appConfig['base_url'] ?>/pengaduan/tracking"
           class="border border-slate-300 hover:border-blue-300 text-slate-700 hover:text-blue-600 font-semibold px-7 py-3.5 rounded-lg transition text-sm flex items-center justify-center gap-2">
          <i data-lucide="search" class="w-4 h-4"></i> Cek Status Laporan
        </a>
      </div>
    </div>
  </div>
</section>

<section class="max-w-5xl mx-auto px-4 py-12">
  <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    <?php
      $statItems = [
        ['label'=>'Total Laporan',   'val'=>$stats['total'],    'lucide'=>'clipboard-list', 'color'=>'text-blue-600',    'bg'=>'bg-blue-50'],
        ['label'=>'Sedang Diproses', 'val'=>$stats['Diproses'], 'lucide'=>'loader',         'color'=>'text-amber-600',   'bg'=>'bg-amber-50'],
        ['label'=>'Selesai',         'val'=>$stats['Selesai'],  'lucide'=>'check-circle-2', 'color'=>'text-emerald-600', 'bg'=>'bg-emerald-50'],
        ['label'=>'Ditolak',         'val'=>$stats['Ditolak'],  'lucide'=>'x-circle',       'color'=>'text-red-600',     'bg'=>'bg-red-50'],
      ];
      foreach ($statItems as $s):
    ?>
    <div class="bg-white rounded-xl p-5 border border-slate-200">
      <div class="w-9 h-9 <?= $s['bg'] ?> rounded-lg flex items-center justify-center mb-3">
        <i data-lucide="<?= $s['lucide'] ?>" class="w-4.5 h-4.5 <?= $s['color'] ?>"></i>
      </div>
      <div class="text-2xl font-bold text-slate-800"><?= number_format($s['val']) ?></div>
      <div class="text-sm text-slate-500 mt-0.5"><?= $s['label'] ?></div>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<section class="bg-white border-y border-slate-100">
  <div class="max-w-5xl mx-auto px-4 py-16">
    <div class="text-center mb-10">
      <h2 class="text-2xl font-bold text-slate-800">Cara Melaporkan</h2>
      <p class="text-slate-500 mt-2 text-sm">Cukup 3 langkah mudah</p>
    </div>
    <div class="grid md:grid-cols-3 gap-6">
      <?php $steps = [
        ['no'=>'1','title'=>'Isi Formulir',     'desc'=>'Lengkapi identitas, pilih lokasi, dan deskripsikan kerusakan jalan.','lucide'=>'file-pen-line'],
        ['no'=>'2','title'=>'Unggah Foto',      'desc'=>'Sertakan foto bukti agar penanganan lebih tepat sasaran.',           'lucide'=>'camera'],
        ['no'=>'3','title'=>'Dapatkan Tiket',   'desc'=>'Sistem generate kode tiket unik untuk memantau status laporan.',     'lucide'=>'ticket'],
      ]; foreach ($steps as $step): ?>
      <div class="text-center p-6">
        <div class="w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center mx-auto mb-4">
          <i data-lucide="<?= $step['lucide'] ?>" class="w-5 h-5 text-slate-600"></i>
        </div>
        <div class="text-xs font-bold text-blue-600 mb-2"><?= $step['no'] ?></div>
        <h3 class="text-base font-semibold text-slate-800 mb-1.5"><?= $step['title'] ?></h3>
        <p class="text-slate-500 text-sm leading-relaxed"><?= $step['desc'] ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="max-w-5xl mx-auto px-4 py-16">
  <div class="bg-slate-900 rounded-2xl p-10 text-center">
    <h2 class="text-2xl font-bold text-white mb-3">Temukan Kerusakan Jalan?</h2>
    <p class="text-slate-400 mb-6 max-w-md mx-auto text-sm">Suara Anda membantu kami memprioritaskan perbaikan infrastruktur jalan di Kota Kendari.</p>
    <div class="flex flex-col sm:flex-row gap-3 justify-center">
      <a href="<?= $appConfig['base_url'] ?>/pengaduan" class="bg-white hover:bg-slate-100 text-slate-900 font-semibold px-6 py-3 rounded-lg transition text-sm">Buat Laporan</a>
      <a href="<?= $appConfig['base_url'] ?>/saran" class="border border-slate-600 hover:border-slate-400 text-slate-300 hover:text-white font-medium px-6 py-3 rounded-lg transition text-sm">Kirim Saran</a>
    </div>
  </div>
</section>
