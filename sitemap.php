<?php
require_once 'bootstrap.php';

// Fetch public content
$public_prompts = get_public_prompts();
$categories = get_categories();
$tags = get_tags();
$collections = get_collections();

// Set header to XML
header("Content-Type: application/xml; charset=utf-8");

echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . PHP_EOL;

// Base URL detection
$app_url = Env::get('APP_URL');
if ($app_url) {
    $base_url = rtrim($app_url, '/') . '/';
} else {
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
    $host = $_SERVER['HTTP_HOST'];
    $path = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $base_url = "$protocol://$host$path/";
}

// Static Pages
$static_pages = [
    ['url' => 'index.php', 'priority' => '1.0', 'changefreq' => 'daily'],
    ['url' => 'search.php', 'priority' => '0.8', 'changefreq' => 'weekly'],
    ['url' => 'categories.php', 'priority' => '0.7', 'changefreq' => 'weekly'],
    ['url' => 'tags.php', 'priority' => '0.7', 'changefreq' => 'weekly'],
    ['url' => 'collections.php', 'priority' => '0.7', 'changefreq' => 'weekly'],
];

foreach ($static_pages as $page) {
    echo '  <url>' . PHP_EOL;
    echo '    <loc>' . htmlspecialchars($base_url . $page['url']) . '</loc>' . PHP_EOL;
    echo '    <changefreq>' . $page['changefreq'] . '</changefreq>' . PHP_EOL;
    echo '    <priority>' . $page['priority'] . '</priority>' . PHP_EOL;
    echo '  </url>' . PHP_EOL;
}

// Public Prompts
foreach ($public_prompts as $prompt) {
    echo '  <url>' . PHP_EOL;
    echo '    <loc>' . htmlspecialchars($base_url . 'prompt.php?id=' . $prompt['id']) . '</loc>' . PHP_EOL;
    echo '    <lastmod>' . date('c', strtotime($prompt['updated_at'])) . '</lastmod>' . PHP_EOL;
    echo '    <changefreq>weekly</changefreq>' . PHP_EOL;
    echo '    <priority>0.8</priority>' . PHP_EOL;
    echo '  </url>' . PHP_EOL;
}

// Categories
foreach ($categories as $cat) {
    echo '  <url>' . PHP_EOL;
    echo '    <loc>' . htmlspecialchars($base_url . 'index.php?category_id=' . $cat['id']) . '</loc>' . PHP_EOL;
    echo '    <changefreq>weekly</changefreq>' . PHP_EOL;
    echo '    <priority>0.5</priority>' . PHP_EOL;
    echo '  </url>' . PHP_EOL;
}

// Tags
foreach ($tags as $tag) {
    echo '  <url>' . PHP_EOL;
    echo '    <loc>' . htmlspecialchars($base_url . 'index.php?tag_id=' . $tag['id']) . '</loc>' . PHP_EOL;
    echo '    <changefreq>weekly</changefreq>' . PHP_EOL;
    echo '    <priority>0.5</priority>' . PHP_EOL;
    echo '  </url>' . PHP_EOL;
}

echo '</urlset>' . PHP_EOL;
