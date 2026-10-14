<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($pageTitle ?? 'Login') ?> - LaporJalan Admin</title>
  <link rel="icon" type="image/png" href="<?= $appConfig['asset_url'] ?>/assets/img/logo-lapor-jalan-cropped.png"/>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"/>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { sans: ['Inter', 'sans-serif'] },
          colors: {
            primary: { 50:'#eef4ff',100:'#dbe8ff',200:'#b9cff9',300:'#8faeed',400:'#5f86dc',500:'#2f5fbf',600:'#143f91',700:'#0b2c6b',800:'#071f52',900:'#05183f',950:'#031027' },
            mark: { 400:'#fbbf24',500:'#f59e0b' },
          }
        }
      }
    }
  </script>
  <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gradient-to-br from-primary-950 via-primary-900 to-slate-950 min-h-screen flex items-center justify-center">
  <?= $content ?>
  <script src="<?= $appConfig['asset_url'] ?>/assets/js/lucide.min.js"></script>
  <script>lucide.createIcons();</script>
</body>
</html>
