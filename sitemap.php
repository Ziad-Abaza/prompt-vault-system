<?php
require_once 'bootstrap.php';

/**
 * Rebuild Sitemap
 * Focus: Publicly accessible URLs only.
 */

// Fetch public content
$public_prompts = get_public_prompts();

// Categories with at least one public prompt
$categories = query("SELECT DISTINCT c.* FROM categories c JOIN prompts p ON c.id = p.category_id WHERE p.is_public = 1 ORDER BY c.name ASC")->fetchAll();

// Tags with at least one public prompt
$tags = query("SELECT DISTINCT t.* FROM tags t JOIN prompt_tags pt ON t.id = pt.tag_id JOIN prompts p ON pt.prompt_id = p.id WHERE p.is_public = 1 ORDER BY t.name ASC")->fetchAll();

// Collections with at least one public prompt
$collections = query("SELECT DISTINCT c.* FROM collections c JOIN prompt_collections pc ON c.id = pc.collection_id JOIN prompts p ON pc.prompt_id = p.id WHERE p.is_public = 1 ORDER BY c.name ASC")->fetchAll();

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
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $path = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $base_url = "$protocol://$host$path/";
}

// 1. Static Public Pages
$static_pages = [
    ['url' => 'index.php', 'priority' => '1.0', 'changefreq' => 'daily'],
    ['url' => 'public_prompts.php', 'priority' => '0.9', 'changefreq' => 'daily'],
    ['url' => 'public_collections.php', 'priority' => '0.9', 'changefreq' => 'daily'],
    ['url' => 'public_categories.php', 'priority' => '0.8', 'changefreq' => 'weekly'],
    ['url' => 'leaderboards.php', 'priority' => '0.8', 'changefreq' => 'daily'],
    ['url' => 'about.php', 'priority' => '0.8', 'changefreq' => 'monthly'],
    ['url' => 'privacy.php', 'priority' => '0.3', 'changefreq' => 'yearly'],
    ['url' => 'terms.php', 'priority' => '0.3', 'changefreq' => 'yearly'],
];

foreach ($static_pages as $page) {
    echo '  <url>' . PHP_EOL;
    echo '    <loc>' . htmlspecialchars($base_url . $page['url']) . '</loc>' . PHP_EOL;
    echo '    <changefreq>' . $page['changefreq'] . '</changefreq>' . PHP_EOL;
    echo '    <priority>' . $page['priority'] . '</priority>' . PHP_EOL;
    echo '  </url>' . PHP_EOL;
}

// 2. Public Prompts (Slug-based)
foreach ($public_prompts as $prompt) {
    $slug_part = !empty($prompt['slug']) ? '-' . $prompt['slug'] : '';
    echo '  <url>' . PHP_EOL;
    echo '    <loc>' . htmlspecialchars($base_url . 'prompt.php?id=' . $prompt['id'] . $slug_part) . '</loc>' . PHP_EOL;
    if (!empty($prompt['updated_at'])) {
        echo '    <lastmod>' . date('c', strtotime($prompt['updated_at'])) . '</lastmod>' . PHP_EOL;
    }
    echo '    <changefreq>weekly</changefreq>' . PHP_EOL;
    echo '    <priority>0.8</priority>' . PHP_EOL;
    echo '  </url>' . PHP_EOL;
}

// 3. Public Category Landing Pages
foreach ($categories as $cat) {
    echo '  <url>' . PHP_EOL;
    echo '    <loc>' . htmlspecialchars($base_url . 'prompts/' . $cat['slug']) . '</loc>' . PHP_EOL;
    echo '    <changefreq>weekly</changefreq>' . PHP_EOL;
    echo '    <priority>0.6</priority>' . PHP_EOL;
    echo '  </url>' . PHP_EOL;
}

// 4. Public Tag Archive Pages
foreach ($tags as $tag) {
    echo '  <url>' . PHP_EOL;
    echo '    <loc>' . htmlspecialchars($base_url . 'prompts/tag/' . $tag['slug']) . '</loc>' . PHP_EOL;
    echo '    <changefreq>weekly</changefreq>' . PHP_EOL;
    echo '    <priority>0.5</priority>' . PHP_EOL;
    echo '  </url>' . PHP_EOL;
}

// 5. Public Collection Archive Pages
foreach ($collections as $coll) {
    echo '  <url>' . PHP_EOL;
    echo '    <loc>' . htmlspecialchars($base_url . 'collections/' . $coll['slug']) . '</loc>' . PHP_EOL;
    echo '    <changefreq>weekly</changefreq>' . PHP_EOL;
    echo '    <priority>0.7</priority>' . PHP_EOL;
    echo '  </url>' . PHP_EOL;
}

// 6. Public Author Profiles
$authors = query("SELECT DISTINCT u.* FROM users u JOIN prompts p ON u.id = p.user_id WHERE p.is_public = 1")->fetchAll();
foreach ($authors as $author) {
    echo '  <url>' . PHP_EOL;
    echo '    <loc>' . htmlspecialchars($base_url . 'u/' . $author['slug']) . '</loc>' . PHP_EOL;
    echo '    <changefreq>weekly</changefreq>' . PHP_EOL;
    echo '    <priority>0.5</priority>' . PHP_EOL;
    echo '  </url>' . PHP_EOL;
}

echo '</urlset>' . PHP_EOL;
