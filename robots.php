<?php
require_once 'bootstrap.php';

// Set header to plain text
header("Content-Type: text/plain; charset=utf-8");

// Base URL detection for the sitemap
$app_url = Env::get('APP_URL');
if ($app_url) {
    $base_url = rtrim($app_url, '/') . '/';
} else {
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
    $host = $_SERVER['HTTP_HOST'];
    $path = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $base_url = "$protocol://$host$path/";
}

$sitemap_url = $base_url . 'sitemap.xml';

echo "User-agent: *" . PHP_EOL;
echo "Allow: /" . PHP_EOL;
echo "Disallow: /includes/" . PHP_EOL;
echo "Disallow: /data/" . PHP_EOL;
echo PHP_EOL;
echo "Sitemap: " . $sitemap_url . PHP_EOL;
