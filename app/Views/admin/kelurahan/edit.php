<?php $pageTitle = 'Edit Kelurahan'; ?>
<div class="max-w-lg">
  <a href="<?= $appConfig['base_url'] ?>/admin/kelurahan" class="text-slate-500 text-sm hover:text-slate-700 mb-4 inline-block">← Kembali</a>
  <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
    <h2 class="font-semibold text-slate-700 mb-4">Edit Kelurahan</h2>
    <?php if (!empty($errors)): ?><div class="bg-red-50 border border-red-200 rounded-xl p-3 mb-4"><?php foreach($errors as $e): ?><p class="text-red-600 text-sm"><?= htmlspecialchars($e) ?></p><?php endforeach; ?></div><?php endif; ?>
    <form method="POST" action="<?= $appConfig['base_url'] ?>/admin/kelurahan/<?= $kel['id_kelurahan'] ?>" class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Kecamatan <span class="text-red-500">*</span></label>
        <select name="id_kecamatan" required class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary-500">
          <?php foreach ($kecamatanList as $k): ?>
          <option value="<?= $k['id_kecamatan'] ?>" <?= $kel['id_kecamatan'] == $k['id_kecamatan'] ? 'selected' : '' ?>><?= htmlspecialchars($k['nama_kecamatan']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Nama Kelurahan <span class="text-red-500">*</span></label>
        <input type="text" name="nama_kelurahan" value="<?= htmlspecialchars($kel['nama_kelurahan']) ?>" required
          class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"/>
      </div>
      <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-2.5 rounded-xl transition text-sm">Update</button>
    </form>
  </div>
</div>
