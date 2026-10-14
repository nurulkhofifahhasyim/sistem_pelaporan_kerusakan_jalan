<?php $pageTitle = 'Cek Status Laporan'; ?>

<div class="max-w-2xl mx-auto px-4 py-12">

  <div class="text-center mb-8">
    <div class="inline-flex w-14 h-14 bg-primary-100 rounded-2xl items-center justify-center mb-4">
      <i data-lucide="search" class="w-7 h-7 text-primary-600"></i>
    </div>
    <h1 class="text-2xl font-bold text-slate-800">Cek Status Laporan</h1>
    <p class="text-slate-500 mt-2 text-sm">Masukkan Kode Tiket yang Anda terima saat mengajukan laporan.</p>
  </div>

  <?php if (!empty($successKode)): ?>
  <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-5 mb-6 text-center">
    <div class="flex justify-center mb-3 text-emerald-500">
      <i data-lucide="check-circle-2" class="w-10 h-10"></i>
    </div>
    <p class="font-bold text-emerald-800 text-lg mb-1">Laporan Berhasil Dikirim!</p>
    <p class="text-emerald-700 text-sm mb-3">Kode tiket Anda:</p>
    <div class="inline-block bg-white border-2 border-emerald-400 rounded-xl px-6 py-3">
      <span class="text-2xl font-mono font-extrabold text-emerald-700 tracking-widest"><?= htmlspecialchars($successKode) ?></span>
    </div>
    <p class="text-emerald-600 text-xs mt-3 flex items-center justify-center gap-1.5">
      <i data-lucide="info" class="w-4 h-4"></i>
      Simpan kode ini! Anda tidak bisa melacak laporan tanpa kode ini.
    </p>
  </div>
  <?php endif; ?>

  <?php if (!empty($errors)): ?>
  <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-4">
    <?php foreach ($errors as $e): ?><p class="text-red-600 text-sm"><?= htmlspecialchars($e) ?></p><?php endforeach; ?>
  </div>
  <?php endif; ?>

  <form method="POST" action="<?= $appConfig['base_url'] ?>/pengaduan/tracking" class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
    <label for="kode_tiket" class="block text-sm font-medium text-slate-700 mb-2">Kode Tiket <span class="text-red-500">*</span></label>
    <div class="flex gap-3">
      <input type="text" id="kode_tiket" name="kode_tiket"
        value="<?= htmlspecialchars($successKode ?? '') ?>"
        class="flex-1 border border-slate-300 rounded-lg px-4 py-3 font-mono text-sm uppercase tracking-wider focus:outline-none focus:ring-2 focus:ring-primary-500 transition"
        placeholder="RJL-20260505-ABCD" required/>
      <button type="submit"
        class="bg-primary-600 hover:bg-primary-700 text-white font-bold px-6 py-3 rounded-lg transition text-sm whitespace-nowrap flex items-center gap-2">
        <i data-lucide="search" class="w-4 h-4"></i> Cari
      </button>
    </div>
    <p class="text-xs text-slate-400 mt-2">Format kode tiket: RJL-YYYYMMDD-XXXX</p>
  </form>
</div>
