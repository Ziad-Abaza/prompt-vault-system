<?php
require_once 'bootstrap.php';

/**
 * Robots.txt Generator
 * Optimized for SEO and security.
 */

// Set header to plain text
header("Content-Type: text/plain; charset=utf-8");

// Base URL detection for the sitemap
$app_url = Env::get('APP_URL');
if ($app_url) {
    $base_url = rtrim($app_url, '/') . '/';
} else {
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $path = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $base_url = "$protocol://$host$path/";
}

// Extract the path part of the base URL for prefixing robots rules if needed
// But usually robots.txt is at root. If it's in a subdirectory, paths are relative to root.
$parsed_url = parse_url($base_url);
$base_path = isset($parsed_url['path']) ? rtrim($parsed_url['path'], '/') : '';

$sitemap_url = $base_url . 'sitemap.xml';

echo "User-agent: *" . PHP_EOL;

// Public Pages (Allowed)
echo "Allow: {$base_path}/public_prompts.php" . PHP_EOL;
echo "Allow: {$base_path}/prompt.php" . PHP_EOL;
echo "Allow: {$base_path}/sitemap.xml" . PHP_EOL;
echo "Allow: {$base_path}/robots.txt" . PHP_EOL;
echo "Allow: {$base_path}/assets/" . PHP_EOL;

// Authentication & Management (Disallowed)
echo "Disallow: {$base_path}/login.php" . PHP_EOL;
echo "Disallow: {$base_path}/register.php" . PHP_EOL;
echo "Disallow: {$base_path}/logout.php" . PHP_EOL;
echo "Disallow: {$base_path}/export.php" . PHP_EOL;
echo "Disallow: {$base_path}/import.php" . PHP_EOL;
echo "Disallow: {$base_path}/prompt_edit.php" . PHP_EOL;
echo "Disallow: {$base_path}/prompt_delete.php" . PHP_EOL;
echo "Disallow: {$base_path}/seed.php" . PHP_EOL;
echo "Disallow: {$base_path}/index.php" . PHP_EOL;
echo "Disallow: {$base_path}/categories.php" . PHP_EOL;
echo "Disallow: {$base_path}/tags.php" . PHP_EOL;
echo "Disallow: {$base_path}/collections.php" . PHP_EOL;
echo "Disallow: {$base_path}/search.php" . PHP_EOL;
echo "Disallow: {$base_path}/track_copy.php" . PHP_EOL;

// Internal Directories
echo "Disallow: {$base_path}/includes/" . PHP_EOL;
echo "Disallow: {$base_path}/data/" . PHP_EOL;

echo PHP_EOL;
echo "Sitemap: " . $sitemap_url . PHP_EOL;
