<?php
$appConfig = require __DIR__ . '/../app/Config/app.php';
$baseUrl = rtrim($appConfig['base_url'] ?? '', '/');
$publicPrefix = $baseUrl . '/public';

$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$path = parse_url($requestUri, PHP_URL_PATH) ?: '';
$query = parse_url($requestUri, PHP_URL_QUERY);

if ($publicPrefix !== '' && strpos($path, $publicPrefix) === 0) {
    $newPath = $baseUrl . substr($path, strlen($publicPrefix));
    $_SERVER['REQUEST_URI'] = $newPath . ($query ? '?' . $query : '');
}

require __DIR__ . '/../index.php';
