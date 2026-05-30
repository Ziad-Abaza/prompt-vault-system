<?php
require_once 'bootstrap.php';

$filters = [
    'category_id' => $_GET['category_id'] ?? null,
    'tag_id' => $_GET['tag_id'] ?? null,
    'collection_id' => $_GET['collection_id'] ?? null,
    'search' => $_GET['search'] ?? null,
];

$prompts = get_prompts($filters);
$categories = get_categories();
$tags = get_tags();
$collections = get_collections();

$page_title = "Prompt Library Dashboard";
$meta_description = "Manage and organize your AI prompts in a centralized workspace. Browse categories, tags, and collections.";
$breadcrumbs = [
    ['name' => 'Library', 'url' => 'index.php']
];

include 'includes/header.php';
?>

<div class="mb-12">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
        <div>
            <h1 class="text-4xl font-bold text-slate-900 tracking-tight mb-2"><?php echo APP_NAME; ?> Dashboard</h1>
            <p class="text-slate-500 text-lg">A centralized workspace for organizing, managing, and discovering AI prompts.</p>
        </div>
        <a href="prompt_edit.php" class="inline-flex items-center px-6 py-3 border border-transparent rounded-xl shadow-sm text-base font-semibold text-white bg-primary-600 hover:bg-primary-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Create New Prompt
        </a>
    </div>
</div>

<!-- Search and Filter Bar -->
<div class="bg-white p-2 rounded-2xl shadow-sm border border-slate-200 mb-10">
    <form action="index.php" method="GET" class="flex flex-col md:flex-row gap-2">
        <div class="flex-grow relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input type="text" name="search" value="<?php echo esc($filters['search']); ?>" class="block w-full pl-11 pr-4 py-3 border-transparent bg-transparent rounded-xl focus:ring-0 sm:text-sm" placeholder="Search prompts by title or keywords...">
        </div>
        
        <div class="flex gap-2 p-1">
            <select name="category_id" class="block w-full md:w-48 pl-3 pr-10 py-2 text-sm border-slate-100 bg-slate-50 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php echo $filters['category_id'] == $cat['id'] ? 'selected' : ''; ?>>
                        <?php echo esc($cat['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="collection_id" class="block w-full md:w-48 pl-3 pr-10 py-2 text-sm border-slate-100 bg-slate-50 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                <option value="">All Collections</option>
                <?php foreach ($collections as $coll): ?>
                    <option value="<?php echo $coll['id']; ?>" <?php echo $filters['collection_id'] == $coll['id'] ? 'selected' : ''; ?>>
                        <?php echo esc($coll['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-semibold rounded-lg hover:bg-slate-800 transition-colors">
                Apply
            </button>
            <?php if (array_filter($filters)): ?>
                <a href="index.php" class="px-5 py-2 bg-slate-100 text-slate-600 text-sm font-semibold rounded-lg hover:bg-slate-200 transition-colors">
                    Reset
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Active Filter Chips (if any) -->
<?php if ($filters['tag_id'] || $filters['category_id'] || $filters['collection_id'] || $filters['search']): ?>
    <div class="flex flex-wrap gap-2 mb-8">
        <?php if ($filters['category_id']): ?>
            <?php $active_cat = array_filter($categories, fn($c) => $c['id'] == $filters['category_id']); ?>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-primary-100 text-primary-800">
                Category: <?php echo esc(reset($active_cat)['name']); ?>
                <a href="index.php?<?php echo http_build_query(array_merge($filters, ['category_id' => ''])); ?>" class="ml-2 text-primary-400 hover:text-primary-600">&times;</a>
            </span>
        <?php endif; ?>
        
        <?php if ($filters['tag_id']): ?>
            <?php $active_tag = array_filter($tags, fn($t) => $t['id'] == $filters['tag_id']); ?>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                Tag: <?php echo esc(reset($active_tag)['name']); ?>
                <a href="index.php?<?php echo http_build_query(array_merge($filters, ['tag_id' => ''])); ?>" class="ml-2 text-indigo-400 hover:text-indigo-600">&times;</a>
            </span>
        <?php endif; ?>

        <?php if ($filters['search']): ?>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-slate-100 text-slate-800">
                Search: "<?php echo esc($filters['search']); ?>"
                <a href="index.php?<?php echo http_build_query(array_merge($filters, ['search' => ''])); ?>" class="ml-2 text-slate-400 hover:text-slate-600">&times;</a>
            </span>
        <?php endif; ?>
    </div>
<?php endif; ?>

<!-- Prompts Grid -->
<?php if (empty($prompts)): ?>
    <div class="bg-white rounded-3xl p-20 border border-slate-200 text-center shadow-sm">
        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="h-10 w-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
        </div>
        <h3 class="text-xl font-bold text-slate-900 mb-2">No prompts found</h3>
        <p class="text-slate-500 max-w-sm mx-auto mb-8">Try adjusting your filters or start fresh with a new prompt.</p>
        <a href="prompt_edit.php" class="inline-flex items-center px-6 py-3 bg-primary-600 text-white font-semibold rounded-xl hover:bg-primary-700 transition-colors">
            Create First Prompt
        </a>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <?php foreach ($prompts as $prompt): ?>
            <div class="group bg-white rounded-2xl border border-slate-200 hover:border-primary-300 hover:shadow-xl hover:shadow-primary-900/5 transition-all duration-300 flex flex-col overflow-hidden">
                <div class="p-6 flex-grow">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex flex-wrap gap-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-primary-50 text-primary-700 uppercase tracking-wider">
                                <?php echo esc($prompt['category_name'] ?? 'Uncategorized'); ?>
                            </span>
                            <?php if ($prompt['is_public']): ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-green-50 text-green-700 uppercase tracking-wider border border-green-100">
                                    Public
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="flex space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button onclick="copyToClipboard(<?php echo esc(json_encode($prompt['content'])); ?>, this)" class="p-2 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors" title="Copy Prompt">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                </svg>
                            </button>
                            <a href="prompt_edit.php?id=<?php echo $prompt['id']; ?>" class="p-2 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Edit Prompt">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                        </div>
                    </div>
                    
                    <a href="prompt.php?id=<?php echo $prompt['id'] . '-' . $prompt['slug']; ?>" class="block group-hover:text-primary-600 transition-colors">
                        <h2 class="text-xl font-bold text-slate-900 mb-3 leading-snug"><?php echo esc($prompt['title']); ?></h2>
                    </a>
                    
                    <p class="text-slate-600 text-sm line-clamp-2 leading-relaxed mb-6">
                        <?php echo esc($prompt['content']); ?>
                    </p>
                </div>
                
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-400">
                        Updated <?php echo date('M j, Y', strtotime($prompt['updated_at'])); ?>
                    </span>
                    <a href="prompt.php?id=<?php echo $prompt['id'] . '-' . $prompt['slug']; ?>" class="text-xs font-bold text-primary-600 hover:text-primary-800 uppercase tracking-widest flex items-center">
                        View Prompt
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
