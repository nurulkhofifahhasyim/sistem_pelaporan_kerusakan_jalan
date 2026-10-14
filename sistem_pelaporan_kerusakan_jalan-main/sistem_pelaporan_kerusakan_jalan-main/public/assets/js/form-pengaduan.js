(function () {
  const BASE_URL = (window.APP_BASE_URL || '').replace(/\/$/, '');

  const kecSelect = document.getElementById('id_kecamatan');
  const kelSelect = document.getElementById('id_kelurahan');
  const fotoInput = document.getElementById('foto_bukti');
  const fotoPreview = document.getElementById('foto-preview');
  const fotoPreviewContainer = document.getElementById('foto-preview-container');
  const fotoIcon  = document.getElementById('foto-icon');
  const fotoLabel = document.getElementById('foto-label');

  if (kecSelect && kelSelect) {
    kecSelect.addEventListener('change', function () {
      const id = this.value;
      kelSelect.innerHTML = '<option value="">Memuat kelurahan...</option>';
      kelSelect.disabled = true;

      if (!id) {
        kelSelect.innerHTML = '<option value="">-- Pilih Kecamatan Dulu --</option>';
        return;
      }

      fetch(BASE_URL + '/api/kelurahan/' + encodeURIComponent(id))
        .then(res => res.json())
        .then(data => {
          kelSelect.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';
          data.forEach(function (kel) {
            const opt = document.createElement('option');
            opt.value = kel.id_kelurahan;
            opt.textContent = kel.nama_kelurahan;
            kelSelect.appendChild(opt);
          });
          kelSelect.disabled = false;
        })
        .catch(function () {
          kelSelect.innerHTML = '<option value="">Gagal memuat. Coba lagi.</option>';
          kelSelect.disabled = false;
        });
    });
  }

  if (fotoInput) {
    fotoInput.addEventListener('change', function () {
      const file = this.files[0];
      if (!file) return;

      if (file.size > 5 * 1024 * 1024) {
        alert('Ukuran file melebihi 5MB. Pilih file yang lebih kecil.');
        this.value = '';
        return;
      }

      const reader = new FileReader();
      reader.onload = function (e) {
        fotoPreview.src = e.target.result;
        fotoPreviewContainer.classList.remove('hidden');
        if (fotoIcon) fotoIcon.classList.add('hidden');
        if (fotoLabel) fotoLabel.textContent = file.name;
      };
      reader.readAsDataURL(file);
    });
  }

  const form = document.querySelector('form[action*="pengaduan"]');
  const submitBtn = document.getElementById('submit-btn');
  if (form && submitBtn) {
    form.addEventListener('submit', function () {
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<svg class="animate-spin w-4 h-4 mr-2 inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Mengirim...';
    });
  }
})();
