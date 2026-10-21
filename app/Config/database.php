<?php
return [
    'host'    => getenv('DB_HOST') ?: 'localhost',
    'dbname'  => getenv('DB_NAME') ?: 'db_pelaporan_jalan',
    'username'=> getenv('DB_USER') ?: 'root',
    'password'=> getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : '',
    'charset' => 'utf8mb4',
];
