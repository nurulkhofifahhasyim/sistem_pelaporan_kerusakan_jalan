<?php $pageTitle = 'Edit Kecamatan'; ?>
<div class="max-w-lg">
  <a href="<?= $appConfig['base_url'] ?>/admin/kecamatan" class="text-slate-500 text-sm hover:text-slate-700 mb-4 inline-block">← Kembali</a>
  <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
    <h2 class="font-semibold text-slate-700 mb-4">Edit Kecamatan</h2>
    <?php if (!empty($errors)): ?>
    <div class="bg-red-50 border border-red-200 rounded-xl p-3 mb-4"><?php foreach($errors as $e): ?><p class="text-red-600 text-sm"><?= htmlspecialchars($e) ?></p><?php endforeach; ?></div>
    <?php endif; ?>
    <form method="POST" action="<?= $appConfig['base_url'] ?>/admin/kecamatan/<?= $kec['id_kecamatan'] ?>">
      <label class="block text-sm font-medium text-slate-700 mb-1">Nama Kecamatan <span class="text-red-500">*</span></label>
      <input type="text" name="nama_kecamatan" value="<?= htmlspecialchars($kec['nama_kecamatan']) ?>" required
        class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 mb-4"/>
      <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-2.5 rounded-xl transition text-sm">Update</button>
    </form>
  </div>
</div>
