<?php
$pageTitle = 'Detail Laporan ' . htmlspecialchars($pengaduan['kode_tiket']);
$statusMap = [
  'Diproses' => ['bg' => 'status-diproses', 'label' => 'Sedang Diproses', 'lucide' => 'settings'],
  'Selesai'  => ['bg' => 'status-selesai',  'label' => 'Selesai',         'lucide' => 'check-circle-2'],
  'Ditolak'  => ['bg' => 'status-ditolak',  'label' => 'Ditolak',         'lucide' => 'x-circle'],
];
$st = $statusMap[$pengaduan['status_pengaduan']] ?? $statusMap['Diproses'];
?>

<div class="max-w-2xl mx-auto px-4 py-12">

  <a href="<?= $appConfig['base_url'] ?>/pengaduan/tracking" class="inline-flex items-center gap-2 text-primary-600 hover:text-primary-800 text-sm mb-6 transition">
    <i data-lucide="arrow-left" class="w-4 h-4"></i>
    Kembali ke Cek Status
  </a>

  <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 mb-4">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
      <div>
        <p class="text-xs text-slate-500 mb-1">Kode Tiket</p>
        <p class="font-mono text-xl font-extrabold text-slate-800 tracking-wider"><?= htmlspecialchars($pengaduan['kode_tiket']) ?></p>
      </div>
      <span class="<?= $st['bg'] ?> text-sm font-semibold px-4 py-2 rounded-full flex items-center gap-1.5">
        <i data-lucide="<?= $st['lucide'] ?>" class="w-4 h-4"></i>
        <?= $st['label'] ?>
      </span>
    </div>
    <p class="text-xs text-slate-400">Dilaporkan pada: <?= date('d F Y, H:i', strtotime($pengaduan['tgl_pengaduan'])) ?> WIB</p>
  </div>

  <div class="bg-white rounded-2xl shadow-sm border border-slate-200 divide-y divide-slate-100 mb-4">
    <div class="p-5">
      <h2 class="font-semibold text-slate-700 mb-4">Informasi Pelapor</h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
        <div><span class="text-slate-500">Nama:</span> <span class="font-medium"><?= htmlspecialchars($pengaduan['nama_lengkap']) ?></span></div>
        <div><span class="text-slate-500">Email:</span> <span class="font-medium"><?= htmlspecialchars($pengaduan['email']) ?></span></div>
        <?php if ($pengaduan['no_telepon']): ?>
        <div><span class="text-slate-500">Telepon:</span> <span class="font-medium"><?= htmlspecialchars($pengaduan['no_telepon']) ?></span></div>
        <?php endif; ?>
      </div>
    </div>
    <div class="p-5">
      <h2 class="font-semibold text-slate-700 mb-3">Lokasi &amp; Deskripsi</h2>
      <p class="text-sm text-slate-600 mb-1 flex items-center gap-1.5">
        <i data-lucide="map-pin" class="w-4 h-4 text-slate-400"></i>
        <?= htmlspecialchars($pengaduan['nama_kelurahan']) ?>, <?= htmlspecialchars($pengaduan['nama_kecamatan']) ?>
      </p>
      <p class="text-sm text-slate-700 leading-relaxed bg-slate-50 rounded-lg p-3 mt-2"><?= nl2br(htmlspecialchars($pengaduan['isi_laporan'])) ?></p>
    </div>
    <?php if ($pengaduan['foto_bukti']): ?>
    <div class="p-5">
      <h2 class="font-semibold text-slate-700 mb-3">Foto Bukti</h2>
      <img src="<?= $appConfig['upload_url'] . htmlspecialchars($pengaduan['foto_bukti']) ?>"
           alt="Foto kerusakan jalan" class="w-full object-cover cursor-pointer"
           onclick="this.classList.toggle('max-h-64')"/>
      <p class="text-xs text-slate-400 mt-1">Klik foto untuk memperbesar</p>
    </div>
    <?php endif; ?>
  </div>

  <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
    <h2 class="font-semibold text-slate-700 mb-4">Riwayat Tanggapan</h2>
    <?php if (empty($tanggapan)): ?>
    <div class="text-center py-8">
      <div class="flex justify-center mb-3 text-slate-300">
        <i data-lucide="mail-open" class="w-12 h-12"></i>
      </div>
      <p class="text-slate-500 text-sm">Belum ada tanggapan dari petugas.</p>
      <p class="text-slate-400 text-xs mt-1">Kami akan segera meninjau laporan Anda.</p>
    </div>
    <?php else: ?>
    <div class="relative">
      <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-slate-200"></div>
      <div class="space-y-6">
        <?php foreach ($tanggapan as $t): ?>
        <div class="relative pl-10">
          <div class="absolute left-0 top-1 w-8 h-8 bg-primary-600 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
            <?= strtoupper(substr($t['nama_petugas'], 0, 1)) ?>
          </div>
          <div class="bg-slate-50 rounded-xl p-4">
            <div class="flex items-center justify-between mb-2">
              <span class="font-semibold text-sm text-slate-800"><?= htmlspecialchars($t['nama_petugas']) ?></span>
              <span class="text-xs text-slate-400"><?= date('d M Y H:i', strtotime($t['tgl_tanggapan'])) ?></span>
            </div>
            <?php
              $statusColors = ['Diproses'=>'status-diproses','Selesai'=>'status-selesai','Ditolak'=>'status-ditolak'];
              $sc = $statusColors[$t['status_saat_itu']] ?? 'status-diproses';
            ?>
            <div class="mb-2">
              <span class="<?= $sc ?> text-xs font-semibold px-2.5 py-0.5 rounded-full">
                Status: <?= htmlspecialchars($t['status_saat_itu']) ?>
              </span>
            </div>
            <p class="text-sm text-slate-700 leading-relaxed"><?= nl2br(htmlspecialchars($t['isi_tanggapan'])) ?></p>
            <?php if (!empty($t['foto_tanggapan'])): ?>
            <img src="<?= $appConfig['upload_url'] . htmlspecialchars($t['foto_tanggapan']) ?>" alt="Foto tanggapan" class="mt-3 rounded-lg max-h-48 object-cover border border-slate-200 cursor-pointer" onclick="this.classList.toggle('max-h-48'); this.classList.toggle('max-h-none')"/>
            <?php endif; ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>
