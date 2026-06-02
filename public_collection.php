<?php
require_once 'bootstrap.php';

$slug = $_GET['slug'] ?? null;
if (!$slug) {
    redirect('public_prompts.php');
}

$collection = get_collection_by_slug($slug);
if (!$collection) {
    abort(404);
}

// Pagination setup
$per_page = 24;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $per_page;

// Query public prompts for this collection via join table
$sql = "SELECT p.*, c.name as category_name, c.slug as category_slug, u.username as author_name 
        FROM prompts p 
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN users u ON p.user_id = u.id
        JOIN prompt_collections pc ON p.id = pc.prompt_id
        WHERE p.is_public = 1 AND pc.collection_id = ? 
        ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
$prompts = query($sql, [$collection['id'], $per_page, $offset])->fetchAll();

// Total count for pagination and meta
$total_prompts = query("SELECT COUNT(*) FROM prompt_collections pc JOIN prompts p ON pc.prompt_id = p.id WHERE p.is_public = 1 AND pc.collection_id = ?", [$collection['id']])->fetchColumn();
$total_pages = ceil($total_prompts / $per_page);

$page_title = "{$collection['name']} — AI Prompt Collection";
$meta_description = !empty($collection['description']) ? substr(strip_tags($collection['description']), 0, 155) . '...' : "Browse the {$collection['name']} collection of curated AI prompts. Copy and use high-performance templates for ChatGPT, Claude, and Midjourney.";
$canonical_url = APP_URL_BASE . '/collections/' . $collection['slug'];

// Pagination URLs for rel="prev/next"
$base_pagination_url = APP_URL_BASE . '/collections/' . $collection['slug'];
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
        "url" => APP_URL_BASE . '/prompt.php?id=' . $p['id'] . $slug_part
    ];
}

// Breadcrumbs
$breadcrumbs = [
    ['name' => 'Home', 'url' => APP_URL_BASE . '/public_prompts.php'],
    ['name' => 'Collections', 'url' => APP_URL_BASE . '/public_prompts.php'],
    ['name' => $collection['name'], 'url' => APP_URL_BASE . "/collections/{$collection['slug']}"]
];

include 'includes/header.php';
?>

<div class="max-w-7xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2 text-[10px] font-bold uppercase tracking-widest">
            <li>
                <a href="<?php echo APP_URL_BASE; ?>/public_prompts.php" class="text-slate-400 hover:text-primary-600 transition-colors">Home</a>
            </li>
            <li>
                <svg class="h-3 w-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
                </svg>
            </li>
            <li>
                <a href="<?php echo APP_URL_BASE; ?>/public_prompts.php" class="text-slate-400 hover:text-primary-600 transition-colors">Collections</a>
            </li>
            <li>
                <svg class="h-3 w-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
                </svg>
            </li>
            <li class="text-slate-900"><?php echo esc($collection['name']); ?></li>
        </ol>
    </nav>

    <!-- Collection Header -->
    <div class="mb-12">
        <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight mb-4">
            <?php echo esc($collection['name']); ?>
        </h1>
        <?php if (!empty($collection['description'])): ?>
            <p class="text-slate-600 text-lg max-w-3xl leading-relaxed">
                <?php echo esc($collection['description']); ?>
            </p>
        <?php else: ?>
            <p class="text-slate-600 text-lg max-w-3xl leading-relaxed">
                A curated selection of high-performance AI prompts. Master your workflow with these hand-picked templates.
            </p>
        <?php endif; ?>
    </div>

    <!-- Main Feed Grid -->
    <?php if (empty($prompts)): ?>
        <div class="py-20 text-center bg-white rounded-3xl border border-slate-100">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-slate-50 border border-slate-100 shadow-sm mb-4">
                <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-slate-900 mb-1">No prompts found in this collection</h3>
            <p class="text-slate-500 text-sm font-medium max-w-xs mx-auto mb-6">We're still curating this collection. Explore our other public prompts in the meantime!</p>
            <a href="<?php echo APP_URL_BASE; ?>/public_prompts.php" class="px-5 py-2.5 bg-primary-600 text-white text-sm font-bold rounded-lg hover:bg-primary-700 transition-colors">Explore All Prompts</a>
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
                        <a href="<?php echo APP_URL_BASE; ?>/collections/<?php echo $collection['slug']; ?>?page=<?php echo $page - 1; ?>" class="px-5 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-600 font-bold text-[10px] uppercase tracking-wider hover:bg-slate-50 transition-all">Previous</a>
                    <?php endif; ?>
                    
                    <span class="px-4 py-2 text-slate-400 font-bold text-[10px] uppercase tracking-wider">Page <?php echo $page; ?> of <?php echo $total_pages; ?></span>

                    <?php if ($page < $total_pages): ?>
                        <a href="<?php echo APP_URL_BASE; ?>/collections/<?php echo $collection['slug']; ?>?page=<?php echo $page + 1; ?>" class="px-5 py-2.5 rounded-xl bg-slate-900 text-white font-bold text-[10px] uppercase tracking-wider hover:bg-primary-600 transition-all shadow-lg shadow-slate-900/20">Next Page</a>
                    <?php endif; ?>
                </nav>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
