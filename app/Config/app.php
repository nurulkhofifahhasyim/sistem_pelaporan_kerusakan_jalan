<?php
// Docker menjalankan app di root (APP_BASE_URL=""); XAMPP tetap di sub-folder
$baseUrl = getenv('APP_BASE_URL') !== false ? rtrim(getenv('APP_BASE_URL'), '/') : '/sistem_pelaporan_kerusakan_jalan';

return [
    'name' => 'LaporJalan Kendari',
    'base_url' => $baseUrl,
    'asset_url' => $baseUrl . '/public',
    'upload_dir' => __DIR__ . '/../../public/uploads/pengaduan/',
    'upload_url' => $baseUrl . '/public/uploads/pengaduan/',
    'max_file_size' => 5 * 1024 * 1024,
    'allowed_ext' => ['jpg', 'jpeg', 'png', 'webp'],

    'recaptcha_site_key' => '6LfGugAtAAAAAKb3w4KjjBE8OSax-evVZc4YNeXd',
    'recaptcha_secret_key' => '6LfGugAtAAAAAATemM0Tevf3pWD8tQVa0A_UJV6l',
    'recaptcha_enabled' => false,
];
