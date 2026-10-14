<?php $pageTitle = '404 - Halaman Tidak Ditemukan'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>404 - Halaman Tidak Ditemukan</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: { 50:'#eef4ff',100:'#dbe8ff',200:'#b9cff9',300:'#8faeed',400:'#5f86dc',500:'#2f5fbf',600:'#143f91',700:'#0b2c6b',800:'#071f52',900:'#05183f',950:'#031027' }
          }
        }
      }
    }
  </script>
</head>
<body class="min-h-screen bg-slate-50 flex items-center justify-center px-4">
<div class="text-center">
  <div class="text-8xl font-extrabold text-slate-200 mb-4">404</div>
  <h1 class="text-2xl font-bold text-slate-700 mb-2">Halaman Tidak Ditemukan</h1>
  <p class="text-slate-500 mb-6 text-sm">Halaman yang Anda cari tidak ada atau telah dipindahkan.</p>
  <a href="<?= $appConfig['base_url'] ?>/" class="bg-primary-600 hover:bg-primary-700 text-white font-bold px-6 py-3 rounded-xl transition text-sm">Kembali ke Beranda</a>
</div>
</body>
</html>
