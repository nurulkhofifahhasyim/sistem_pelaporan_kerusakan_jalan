<?php $pageTitle = 'Buat Laporan Kerusakan Jalan'; $extraJs = 'form-pengaduan'; ?>

<div class="max-w-2xl mx-auto px-4 py-12">

  <div class="text-center mb-8">
    <div class="inline-flex w-14 h-14 bg-primary-100 rounded-2xl items-center justify-center mb-4">
      <i data-lucide="triangle-alert" class="w-7 h-7 text-primary-600"></i>
    </div>
    <h1 class="text-2xl font-bold text-slate-800">Formulir Pengaduan</h1>
    <p class="text-slate-500 mt-2 text-sm">Isi data berikut untuk melaporkan kerusakan jalan. Semua field bertanda <span class="text-red-500">*</span> wajib diisi.</p>
  </div>

  <?php if (!empty($errors)): ?>
  <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
    <div class="flex items-center gap-2 text-red-700 font-semibold mb-2">
      <i data-lucide="alert-circle" class="w-4 h-4"></i>
      Harap perbaiki kesalahan berikut:
    </div>
    <ul class="list-disc list-inside text-red-600 text-sm space-y-1">
      <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
    </ul>
  </div>
  <?php endif; ?>

  <form method="POST" action="<?= $appConfig['base_url'] ?>/pengaduan" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-sm border border-slate-200 divide-y divide-slate-100">

    <div class="p-6">
      <h2 class="font-semibold text-slate-700 mb-4 flex items-center gap-2">
        <span class="w-6 h-6 bg-primary-100 text-primary-600 rounded-full text-xs font-bold flex items-center justify-center">1</span>
        Identitas Pelapor
      </h2>
      <div class="space-y-4">
        <div>
          <label for="nama_lengkap" class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
          <input type="text" id="nama_lengkap" name="nama_lengkap" value="<?= htmlspecialchars($old['nama_lengkap'] ?? '') ?>"
            class="w-full border <?= isset($errors['nama_lengkap']) ? 'border-red-400 bg-red-50' : 'border-slate-300' ?> rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition"
            placeholder="Nama sesuai KTP" required/>
          <?php if (isset($errors['nama_lengkap'])): ?><p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['nama_lengkap']) ?></p><?php endif; ?>
        </div>
        <div>
          <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email <span class="text-red-500">*</span></label>
          <input type="email" id="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>"
            class="w-full border <?= isset($errors['email']) ? 'border-red-400 bg-red-50' : 'border-slate-300' ?> rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition"
            placeholder="contoh@email.com" required/>
          <?php if (isset($errors['email'])): ?><p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['email']) ?></p><?php endif; ?>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label for="no_telepon" class="block text-sm font-medium text-slate-700 mb-1">No. Telepon</label>
            <input type="tel" id="no_telepon" name="no_telepon" value="<?= htmlspecialchars($old['no_telepon'] ?? '') ?>"
              class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition"
              placeholder="08xxxxxxxxxx"/>
          </div>
          <div>
            <label for="alamat" class="block text-sm font-medium text-slate-700 mb-1">Alamat</label>
            <input type="text" id="alamat" name="alamat" value="<?= htmlspecialchars($old['alamat'] ?? '') ?>"
              class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition"
              placeholder="Jl. Contoh No. 1"/>
          </div>
        </div>
      </div>
    </div>

    <div class="p-6">
      <h2 class="font-semibold text-slate-700 mb-4 flex items-center gap-2">
        <span class="w-6 h-6 bg-primary-100 text-primary-600 rounded-full text-xs font-bold flex items-center justify-center">2</span>
        Lokasi Kerusakan
      </h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label for="id_kecamatan" class="block text-sm font-medium text-slate-700 mb-1">Kecamatan <span class="text-red-500">*</span></label>
          <select id="id_kecamatan" name="id_kecamatan"
            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition bg-white">
            <option value="">-- Pilih Kecamatan --</option>
            <?php foreach ($kecamatanList as $kec): ?>
            <option value="<?= $kec['id_kecamatan'] ?>" <?= ($old['id_kecamatan'] ?? '') == $kec['id_kecamatan'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($kec['nama_kecamatan']) ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label for="id_kelurahan" class="block text-sm font-medium text-slate-700 mb-1">Kelurahan <span class="text-red-500">*</span></label>
          <select id="id_kelurahan" name="id_kelurahan"
            class="w-full border <?= isset($errors['id_kelurahan']) ? 'border-red-400 bg-red-50' : 'border-slate-300' ?> rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition bg-white"
            disabled>
            <option value="">-- Pilih Kecamatan Dulu --</option>
          </select>
          <?php if (isset($errors['id_kelurahan'])): ?><p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['id_kelurahan']) ?></p><?php endif; ?>
        </div>
      </div>
    </div>

    <div class="p-6">
      <h2 class="font-semibold text-slate-700 mb-4 flex items-center gap-2">
        <span class="w-6 h-6 bg-primary-100 text-primary-600 rounded-full text-xs font-bold flex items-center justify-center">3</span>
        Detail Laporan
      </h2>
      <div class="space-y-4">
        <div>
          <label for="isi_laporan" class="block text-sm font-medium text-slate-700 mb-1">Deskripsi Kerusakan <span class="text-red-500">*</span></label>
          <textarea id="isi_laporan" name="isi_laporan" rows="4"
            class="w-full border <?= isset($errors['isi_laporan']) ? 'border-red-400 bg-red-50' : 'border-slate-300' ?> rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 transition resize-none"
            placeholder="Deskripsikan kerusakan jalan secara detail: lokasi spesifik, jenis kerusakan (lubang, retak, dst), tingkat keparahan..."
            required><?= htmlspecialchars($old['isi_laporan'] ?? '') ?></textarea>
          <?php if (isset($errors['isi_laporan'])): ?><p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['isi_laporan']) ?></p><?php endif; ?>
        </div>
        <div>
          <label for="foto_bukti" class="block text-sm font-medium text-slate-700 mb-1">Foto Bukti <span class="text-red-500">*</span></label>
          <label for="foto_bukti" class="flex flex-col items-center justify-center border-2 border-dashed <?= isset($errors['foto_bukti']) ? 'border-red-400' : 'border-slate-300' ?> rounded-xl cursor-pointer hover:border-primary-400 transition p-6 bg-slate-50" id="foto-drop-area">
            <div id="foto-preview-container" class="hidden mb-3">
              <img id="foto-preview" src="" alt="Preview" class="max-h-48 rounded-lg object-contain mx-auto"/>
            </div>
            <i data-lucide="image" id="foto-icon" class="w-10 h-10 text-slate-400 mb-2"></i>
            <span id="foto-label" class="text-sm text-slate-500">Klik untuk pilih foto atau drag &amp; drop</span>
            <span class="text-xs text-slate-400 mt-1">JPG, PNG, WEBP — Maks. 5MB</span>
            <input type="file" id="foto_bukti" name="foto_bukti" accept=".jpg,.jpeg,.png,.webp" class="hidden" required/>
          </label>
          <?php if (isset($errors['foto_bukti'])): ?><p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($errors['foto_bukti']) ?></p><?php endif; ?>
        </div>
      </div>
    </div>

    <div class="p-6">
      <?php if ($appConfig['recaptcha_enabled']): ?>
      <div class="mb-4 flex justify-center">
        <div class="g-recaptcha" data-sitekey="<?= htmlspecialchars($appConfig['recaptcha_site_key']) ?>"></div>
      </div>
      <?php if (isset($errors['recaptcha'])): ?>
      <p class="text-red-500 text-xs text-center mb-3"><?= htmlspecialchars($errors['recaptcha']) ?></p>
      <?php endif; ?>
      <?php endif; ?>
      <button type="submit" id="submit-btn"
        class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 px-6 rounded-xl transition flex items-center justify-center gap-2 text-sm">
        <i data-lucide="send" class="w-4 h-4"></i>
        Kirim Laporan
      </button>
      <p class="text-xs text-slate-400 text-center mt-3">Dengan mengirim laporan ini, Anda menyetujui data Anda digunakan untuk keperluan penanganan infrastruktur.</p>
    </div>
  </form>
</div>

<?php if ($appConfig['recaptcha_enabled']): ?>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php endif; ?>
