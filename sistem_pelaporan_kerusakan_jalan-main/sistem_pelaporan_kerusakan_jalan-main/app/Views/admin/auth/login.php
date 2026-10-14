<?php $pageTitle = 'Login Admin'; ?>
<div class="w-full max-w-md px-4">
  <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-3xl p-8 shadow-2xl">

    <div class="text-center mb-8">
      <div class="inline-flex bg-white rounded-2xl px-4 py-3 items-center justify-center mb-4 shadow-lg">
        <img src="<?= $appConfig['asset_url'] ?>/assets/img/logo-lapor-jalan-cropped.png" alt="Lapor Jalan" class="h-14 w-auto object-contain"/>
      </div>
      <h1 class="text-2xl font-bold text-white">LaporJalan Admin</h1>
      <p class="text-slate-400 text-sm mt-1">Masuk ke panel pengelolaan</p>
    </div>

    <?php if ($error): ?>
    <div class="bg-red-500/20 border border-red-500/40 rounded-xl p-3 mb-5 flex items-center gap-2 text-red-300 text-sm">
      <i data-lucide="alert-circle" class="w-4 h-4 flex-shrink-0"></i>
      <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="<?= $appConfig['base_url'] ?>/admin/login" class="space-y-4">
      <div>
        <label for="username" class="block text-sm font-medium text-slate-300 mb-1.5">Username</label>
        <input type="text" id="username" name="username" required autofocus
          class="w-full bg-white/10 border border-white/20 text-white placeholder-slate-500 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
          placeholder="Masukkan username"/>
      </div>
      <div>
        <label for="password" class="block text-sm font-medium text-slate-300 mb-1.5">Password</label>
        <div class="relative">
          <input type="password" id="password" name="password" required
            class="w-full bg-white/10 border border-white/20 text-white placeholder-slate-500 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition pr-10"
            placeholder="Masukkan password"/>
          <button type="button" onclick="togglePwd()" class="absolute right-3 top-3 text-slate-400 hover:text-white transition">
            <i data-lucide="eye" id="eye-icon" class="w-4 h-4"></i>
          </button>
        </div>
      </div>
      <button type="submit"
        class="w-full bg-mark-400 hover:bg-mark-500 text-slate-950 font-bold py-3 rounded-xl transition shadow-lg mt-2 text-sm">
        Masuk ke Dashboard
      </button>
    </form>

    <p class="text-center text-slate-500 text-xs mt-6">
      <a href="<?= $appConfig['base_url'] ?>/" class="hover:text-slate-300 transition">← Kembali ke Situs Publik</a>
    </p>
  </div>
</div>

<script>
  function togglePwd() {
    const pwd = document.getElementById('password');
    pwd.type = pwd.type === 'password' ? 'text' : 'password';
  }
</script>
