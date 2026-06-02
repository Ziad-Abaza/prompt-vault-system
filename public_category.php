<?php
require_once 'bootstrap.php';

$slug = $_GET['slug'] ?? null;
if (!$slug) {
    redirect('public_prompts.php');
}

$category = get_category_by_slug($slug);
if (!$category) {
    abort(404);
}

// Pagination setup
$per_page = 24;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $per_page;

// Query public prompts for this category
$sql = "SELECT p.*, c.name as category_name, u.username as author_name 
        FROM prompts p 
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN users u ON p.user_id = u.id
        WHERE p.is_public = 1 AND p.category_id = ? 
        ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
$prompts = query($sql, [$category['id'], $per_page, $offset])->fetchAll();

// Total count for pagination and meta
$total_prompts = query("SELECT COUNT(*) FROM prompts WHERE is_public = 1 AND category_id = ?", [$category['id']])->fetchColumn();
$total_pages = ceil($total_prompts / $per_page);

$page_title = "{$category['name']} Prompts — Free AI Prompts Library";
$meta_description = "Browse {$total_prompts} free {$category['name']} prompts for AI tools. Copy and use the best curated {$category['name']} templates for ChatGPT, Claude, and Midjourney.";
$canonical_url = rtrim(Env::get('APP_URL', ''), '/') . '/prompts/' . $category['slug'];

// Pagination URLs for rel="prev/next"
$base_pagination_url = rtrim(Env::get('APP_URL', ''), '/') . '/prompts/' . $category['slug'];
$prev_page_url = ($page > 1) ? $base_pagination_url . '?page=' . ($page - 1) : null;
$next_page_url = ($page < $total_pages) ? $base_pagination_url . '?page=' . ($page + 1) : null;

// ItemList Structured Data
$page_schema = [
    "@type" => "ItemList",
    "name" => $page_title,
    "description" => $meta_description,
    "url" => $canonical_url,
    "itemListElement" => []
];

foreach ($prompts as $i => $p) {
    $slug_part = !empty($p['slug']) ? '-' . $p['slug'] : '';
    $page_schema['itemListElement'][] = [
        "@type" => "ListItem",
        "position" => $i + 1 + ($offset),
        "url" => rtrim(Env::get('APP_URL', ''), '/') . '/prompt.php?id=' . $p['id'] . $slug_part
    ];
}

// Breadcrumbs
$breadcrumbs = [
    ['name' => 'Home', 'url' => 'public_prompts.php'],
    ['name' => 'Prompts', 'url' => 'public_prompts.php'],
    ['name' => $category['name'], 'url' => "prompts/{$category['slug']}"]
];

include 'includes/header.php';
?>

<div class="max-w-7xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2 text-[10px] font-bold uppercase tracking-widest">
            <li>
                <a href="public_prompts.php" class="text-slate-400 hover:text-primary-600 transition-colors">Home</a>
            </li>
            <li>
                <svg class="h-3 w-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
                </svg>
            </li>
            <li>
                <a href="public_prompts.php" class="text-slate-400 hover:text-primary-600 transition-colors">Prompts</a>
            </li>
            <li>
                <svg class="h-3 w-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
                </svg>
            </li>
            <li class="text-slate-900"><?php echo esc($category['name']); ?></li>
        </ol>
    </nav>

    <!-- Category Header -->
    <div class="mb-12">
        <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight mb-4">
            <?php echo esc($category['name']); ?> <span class="text-primary-600 text-3xl">Prompts</span>
        </h1>
        <p class="text-slate-600 text-lg max-w-3xl leading-relaxed">
            Discover a curated collection of high-quality <?php echo esc($category['name']); ?> prompts designed to help you get the most out of AI. 
            Whether you're using ChatGPT, Claude, or Midjourney, these templates provide a solid foundation for your <?php echo esc(strtolower($category['name'])); ?> workflows.
        </p>
    </div>

    <!-- Main Feed Grid -->
    <?php if (empty($prompts)): ?>
        <div class="py-20 text-center bg-white rounded-3xl border border-slate-100">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-slate-50 border border-slate-100 shadow-sm mb-4">
                <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H4a2 2 0 00-2 2v11a2 2 0 002 2h11M20 13l-4 4m4-4l4 4m-4-4v6m-6-6H7m1-4h.01M11 16h.01"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-slate-900 mb-1">No prompts found in this category</h3>
            <p class="text-slate-500 text-sm font-medium max-w-xs mx-auto mb-6">We're still growing our collection. Check back soon for new additions!</p>
            <a href="public_prompts.php" class="px-5 py-2.5 bg-primary-600 text-white text-sm font-bold rounded-lg hover:bg-primary-700 transition-colors">Explore All Prompts</a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            <?php foreach ($prompts as $prompt): ?>
                <?php include 'includes/prompt_card.php'; ?>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="mt-12 mb-8 flex justify-center">
                <nav class="flex items-center space-x-2">
                    <?php if ($page > 1): ?>
                        <a href="<?php echo $app_url_base; ?>/prompts/<?php echo $category['slug']; ?>?page=<?php echo $page - 1; ?>" class="px-5 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-600 font-bold text-[10px] uppercase tracking-wider hover:bg-slate-50 transition-all">Previous</a>
                    <?php endif; ?>
                    
                    <span class="px-4 py-2 text-slate-400 font-bold text-[10px] uppercase tracking-wider">Page <?php echo $page; ?> of <?php echo $total_pages; ?></span>

                    <?php if ($page < $total_pages): ?>
                        <a href="<?php echo $app_url_base; ?>/prompts/<?php echo $category['slug']; ?>?page=<?php echo $page + 1; ?>" class="px-5 py-2.5 rounded-xl bg-slate-900 text-white font-bold text-[10px] uppercase tracking-wider hover:bg-primary-600 transition-all shadow-lg shadow-slate-900/20">Next Page</a>
                    <?php endif; ?>
                </nav>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
