<?php $pageTitle = 'Kirim Saran'; ?>
<div class="max-w-xl mx-auto px-4 py-12">
  <div class="text-center mb-8">
    <div class="inline-flex w-14 h-14 bg-emerald-100 rounded-2xl items-center justify-center mb-4">
      <i data-lucide="message-circle" class="w-7 h-7 text-emerald-600"></i>
    </div>
    <h1 class="text-2xl font-bold text-slate-800">Kirim Saran &amp; Aspirasi</h1>
    <p class="text-slate-500 mt-2 text-sm">Sampaikan pendapat atau saran Anda untuk peningkatan layanan infrastruktur.</p>
  </div>

  <?php if (!empty($errors)): ?>
  <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-4">
    <?php foreach ($errors as $e): ?><p class="text-red-600 text-sm"><?= htmlspecialchars($e) ?></p><?php endforeach; ?>
  </div>
  <?php endif; ?>

  <form method="POST" action="<?= $appConfig['base_url'] ?>/saran" class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 space-y-4">
    <div>
      <label for="nama_pengirim" class="block text-sm font-medium text-slate-700 mb-1">Nama (Opsional)</label>
      <input type="text" id="nama_pengirim" name="nama_pengirim" value="<?= htmlspecialchars($old['nama_pengirim'] ?? '') ?>"
        class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition"
        placeholder="Nama Anda (boleh dikosongkan)"/>
    </div>
    <div>
      <label for="isi_saran" class="block text-sm font-medium text-slate-700 mb-1">Isi Saran <span class="text-red-500">*</span></label>
      <textarea id="isi_saran" name="isi_saran" rows="5" required
        class="w-full border <?= isset($errors['isi_saran']) ? 'border-red-400 bg-red-50' : 'border-slate-300' ?> rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition resize-none"
        placeholder="Tuliskan saran atau aspirasi Anda untuk peningkatan layanan..."><?= htmlspecialchars($old['isi_saran'] ?? '') ?></textarea>
    </div>
    <?php if ($appConfig['recaptcha_enabled']): ?>
    <div class="flex justify-center">
      <div class="g-recaptcha" data-sitekey="<?= htmlspecialchars($appConfig['recaptcha_site_key']) ?>"></div>
    </div>
    <?php if (isset($errors['recaptcha'])): ?>
    <p class="text-red-500 text-xs text-center"><?= htmlspecialchars($errors['recaptcha']) ?></p>
    <?php endif; ?>
    <?php endif; ?>
    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl transition text-sm">
      Kirim Saran
    </button>
  </form>
</div>

<?php if ($appConfig['recaptcha_enabled']): ?>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php endif; ?>
