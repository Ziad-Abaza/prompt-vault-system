<?php
require_once 'bootstrap.php';

// Fetch all public prompts
$public_prompts = get_public_prompts();

// Set header to XML
header("Content-Type: application/xml; charset=utf-8");

echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

// Base URL detection
$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
$base_url .= str_replace('sitemap.php', '', $_SERVER['PHP_SELF']);

// Main Library Page
echo '  <url>' . PHP_EOL;
echo '    <loc>' . htmlspecialchars($base_url . 'index.php') . '</loc>' . PHP_EOL;
echo '    <changefreq>daily</changefreq>' . PHP_EOL;
echo '    <priority>1.0</priority>' . PHP_EOL;
echo '  </url>' . PHP_EOL;

// Public Prompts
foreach ($public_prompts as $prompt) {
    echo '  <url>' . PHP_EOL;
    echo '    <loc>' . htmlspecialchars($base_url . 'prompt.php?id=' . $prompt['id']) . '</loc>' . PHP_EOL;
    echo '    <lastmod>' . date('c', strtotime($prompt['updated_at'])) . '</lastmod>' . PHP_EOL;
    echo '    <changefreq>weekly</changefreq>' . PHP_EOL;
    echo '    <priority>0.8</priority>' . PHP_EOL;
    echo '  </url>' . PHP_EOL;
}

echo '</urlset>' . PHP_EOL;
