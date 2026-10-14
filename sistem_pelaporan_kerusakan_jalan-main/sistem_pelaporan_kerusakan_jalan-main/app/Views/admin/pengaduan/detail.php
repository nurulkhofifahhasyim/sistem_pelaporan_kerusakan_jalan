<?php
$pageTitle = 'Detail Pengaduan ' . htmlspecialchars($pengaduan['kode_tiket']);
$statusMap = ['Diproses'=>'status-diproses','Selesai'=>'status-selesai','Ditolak'=>'status-ditolak'];
?>

<a href="<?= $appConfig['base_url'] ?>/admin/pengaduan" class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-700 text-sm mb-4 transition">
  ← Kembali ke Daftar Pengaduan
</a>

<?php if ($flash): ?>
<div class="bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-3 mb-4 text-emerald-700 text-sm"><?= htmlspecialchars($flash) ?></div>
<?php endif; ?>
<?php if ($error): ?>
<div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 mb-4 text-red-600 text-sm"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="grid lg:grid-cols-3 gap-6">

  <div class="lg:col-span-2 space-y-4">

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
      <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <div>
          <p class="text-xs text-slate-400">Kode Tiket</p>
          <p class="font-mono text-xl font-extrabold text-slate-800"><?= htmlspecialchars($pengaduan['kode_tiket']) ?></p>
        </div>
        <span class="status-badge <?= $statusMap[$pengaduan['status_pengaduan']] ?? 'status-diproses' ?> text-sm">
          <?= htmlspecialchars($pengaduan['status_pengaduan']) ?>
        </span>
      </div>
      <div class="grid sm:grid-cols-2 gap-3 text-sm">
        <div><span class="text-slate-500">Pelapor:</span> <span class="font-medium"><?= htmlspecialchars($pengaduan['nama_lengkap']) ?></span></div>
        <div><span class="text-slate-500">Email:</span> <span class="font-medium"><?= htmlspecialchars($pengaduan['email']) ?></span></div>
        <?php if ($pengaduan['no_telepon']): ?>
        <div><span class="text-slate-500">Telepon:</span> <span class="font-medium"><?= htmlspecialchars($pengaduan['no_telepon']) ?></span></div>
        <?php endif; ?>
        <div><span class="text-slate-500">Lokasi:</span> <span class="font-medium"><?= htmlspecialchars($pengaduan['nama_kelurahan']) ?>, <?= htmlspecialchars($pengaduan['nama_kecamatan']) ?></span></div>
        <div><span class="text-slate-500">Tanggal:</span> <span class="font-medium"><?= date('d M Y H:i', strtotime($pengaduan['tgl_pengaduan'])) ?></span></div>
      </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
      <h3 class="font-semibold text-slate-700 mb-3">Deskripsi Kerusakan</h3>
      <p class="text-sm text-slate-700 leading-relaxed bg-slate-50 rounded-xl p-4"><?= nl2br(htmlspecialchars($pengaduan['isi_laporan'])) ?></p>
    </div>

    <?php if ($pengaduan['foto_bukti']): ?>
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
      <h3 class="font-semibold text-slate-700 mb-3">Foto Bukti</h3>
      <img src="<?= $appConfig['upload_url'] . htmlspecialchars($pengaduan['foto_bukti']) ?>"
           alt="Foto bukti" class="w-full object-cover cursor-pointer hover:opacity-90 transition"
           onclick="openLightbox(this.src)"/>
    </div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
      <h3 class="font-semibold text-slate-700 mb-4">Riwayat Tanggapan (<?= count($tanggapan) ?>)</h3>
      <?php if (empty($tanggapan)): ?>
        <p class="text-slate-400 text-sm text-center py-4">Belum ada tanggapan.</p>
      <?php else: ?>
      <div class="space-y-4">
        <?php foreach ($tanggapan as $t): ?>
        <div class="flex gap-3">
          <div class="w-8 h-8 bg-primary-600 text-white rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">
            <?= strtoupper(substr($t['nama_petugas'], 0, 1)) ?>
          </div>
          <div class="flex-1 bg-slate-50 rounded-xl p-3">
            <div class="flex items-center justify-between mb-1">
              <span class="text-sm font-semibold text-slate-800"><?= htmlspecialchars($t['nama_petugas']) ?> <span class="text-xs text-slate-400">(<?= $t['jabatan'] ?>)</span></span>
              <span class="text-xs text-slate-400"><?= date('d M Y H:i', strtotime($t['tgl_tanggapan'])) ?></span>
            </div>
            <div class="mb-2">
              <span class="status-badge status-<?= strtolower($t['status_saat_itu']) ?> text-xs">
                Status diubah ke: <?= htmlspecialchars($t['status_saat_itu']) ?>
              </span>
            </div>
            <p class="text-sm text-slate-700"><?= nl2br(htmlspecialchars($t['isi_tanggapan'])) ?></p>
            <?php if (!empty($t['foto_tanggapan'])): ?>
            <img src="<?= $appConfig['upload_url'] . htmlspecialchars($t['foto_tanggapan']) ?>" alt="Foto tanggapan" class="mt-2 rounded-lg max-h-48 object-cover border border-slate-200 cursor-pointer hover:opacity-90 transition" onclick="openLightbox(this.src)"/>
            <?php endif; ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <div>
    <div class="bg-white rounded-2xl border border-primary-100 shadow-sm p-5 sticky top-6">
      <h3 class="font-semibold text-slate-700 mb-4">Beri Tanggapan</h3>
      <form method="POST" action="<?= $appConfig['base_url'] ?>/admin/pengaduan/<?= $pengaduan['id_pengaduan'] ?>/tanggapan" enctype="multipart/form-data" class="space-y-4">
        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">Update Status</label>
          <select name="status_pengaduan" class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
            <?php foreach (['Diproses','Selesai','Ditolak'] as $s): ?>
            <option value="<?= $s ?>" <?= $pengaduan['status_pengaduan'] === $s ? 'selected' : '' ?>><?= $s ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">Isi Tanggapan <span class="text-red-500">*</span></label>
          <textarea name="isi_tanggapan" rows="5" required
            class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none"
            placeholder="Tuliskan tanggapan, progres perbaikan, atau alasan penolakan..."></textarea>
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-600 mb-1">Foto Bukti Tanggapan <span class="text-slate-400">(opsional)</span></label>
          <input type="file" name="foto_tanggapan" accept="image/jpeg,image/png,image/webp"
            class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm bg-white file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-600 hover:file:bg-primary-100 focus:outline-none focus:ring-2 focus:ring-primary-500"/>
          <p class="text-xs text-slate-400 mt-1">Format: JPG, PNG, WebP. Maks 5MB.</p>
        </div>
        <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-2.5 rounded-xl transition text-sm">
          Simpan &amp; Update Status
        </button>
      </form>
    </div>
  </div>
</div>

<div id="lightbox" class="fixed inset-0 z-[999] bg-black/80 hidden items-center justify-center p-4" onclick="closeLightbox()">
  <button onclick="closeLightbox()" class="absolute top-4 right-4 text-white/80 hover:text-white transition">
    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
  </button>
  <img id="lightbox-img" src="" alt="Preview" class="max-w-full max-h-[90vh] shadow-2xl object-contain" onclick="event.stopPropagation()"/>
</div>

<script>
function openLightbox(src) {
  const lb = document.getElementById('lightbox');
  document.getElementById('lightbox-img').src = src;
  lb.classList.remove('hidden');
  lb.classList.add('flex');
  document.body.style.overflow = 'hidden';
}
function closeLightbox() {
  const lb = document.getElementById('lightbox');
  lb.classList.add('hidden');
  lb.classList.remove('flex');
  document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') closeLightbox();
});
</script>
