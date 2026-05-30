<?php
require_once 'bootstrap.php';

$filters = [
    'category_id' => $_GET['category_id'] ?? null,
    'tag_id' => $_GET['tag_id'] ?? null,
    'collection_id' => $_GET['collection_id'] ?? null,
    'search' => $_GET['search'] ?? null,
];

$prompts = get_prompts($filters);

// Handle AJAX request for grid only
if (isset($_GET['ajax'])) {
    if (empty($prompts)) {
        echo '<div class="col-span-full bg-white rounded-2xl p-12 border border-slate-200 text-center shadow-sm">
            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="h-8 w-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-1">No prompts found</h3>
            <p class="text-slate-500 text-sm max-w-sm mx-auto mb-6">Try adjusting your filters or start fresh with a new prompt.</p>
            <a href="prompt_edit.php" class="inline-flex items-center px-5 py-2.5 bg-primary-600 text-white text-sm font-bold rounded-lg hover:bg-primary-700 transition-colors">
                Create First Prompt
            </a>
        </div>';
    } else {
        foreach ($prompts as $prompt) {
            include 'includes/prompt_card.php';
        }
    }
    exit;
}

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

<div class="mb-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight"><?php echo APP_NAME; ?></h1>
            <p class="text-slate-500 text-sm">Discover, organize, and manage your AI prompts.</p>
        </div>
        <a href="prompt_edit.php" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-primary-600 hover:bg-primary-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
            <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            New Prompt
        </a>
    </div>
</div>

<!-- Search and Filter Bar -->
<div class="bg-white p-1.5 rounded-xl shadow-sm border border-slate-200 mb-6">
    <form action="index.php" method="GET" class="flex flex-col md:flex-row gap-2">
        <div class="flex-grow relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input type="text" name="search" value="<?php echo esc($filters['search']); ?>" class="block w-full pl-9 pr-4 py-2 border-transparent bg-transparent rounded-lg focus:ring-0 text-sm" placeholder="Search prompts...">
        </div>
        
        <div class="flex flex-wrap md:flex-nowrap gap-2 p-1">
            <select name="category_id" class="block w-full md:w-40 pl-2 pr-8 py-1.5 text-xs border-slate-100 bg-slate-50 rounded-md focus:ring-primary-500 focus:border-primary-500 font-medium">
                <option value="">Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php echo $filters['category_id'] == $cat['id'] ? 'selected' : ''; ?>>
                        <?php echo esc($cat['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="collection_id" class="block w-full md:w-40 pl-2 pr-8 py-1.5 text-xs border-slate-100 bg-slate-50 rounded-md focus:ring-primary-500 focus:border-primary-500 font-medium">
                <option value="">Collections</option>
                <?php foreach ($collections as $coll): ?>
                    <option value="<?php echo $coll['id']; ?>" <?php echo $filters['collection_id'] == $coll['id'] ? 'selected' : ''; ?>>
                        <?php echo esc($coll['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="px-4 py-1.5 bg-slate-900 text-white text-xs font-bold rounded-md hover:bg-slate-800 transition-colors">
                Apply
            </button>
            <?php if (array_filter($filters)): ?>
                <a href="index.php" class="px-4 py-1.5 bg-slate-100 text-slate-600 text-xs font-bold rounded-md hover:bg-slate-200 transition-colors">
                    Reset
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Active Filter Chips (if any) -->
<?php if ($filters['tag_id'] || $filters['category_id'] || $filters['collection_id'] || $filters['search']): ?>
    <div class="flex flex-wrap gap-2 mb-6">
        <?php if ($filters['category_id']): ?>
            <?php $active_cat = array_filter($categories, fn($c) => $c['id'] == $filters['category_id']); ?>
            <?php if (!empty($active_cat)): ?>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-primary-50 text-primary-700 border border-primary-100">
                    Category: <?php echo esc(reset($active_cat)['name']); ?>
                    <a href="index.php?<?php echo http_build_query(array_merge($filters, ['category_id' => ''])); ?>" class="ml-1.5 text-primary-400 hover:text-primary-600">&times;</a>
                </span>
            <?php endif; ?>
        <?php endif; ?>
        
        <?php if ($filters['tag_id']): ?>
            <?php $active_tag = array_filter($tags, fn($t) => $t['id'] == $filters['tag_id']); ?>
            <?php if (!empty($active_tag)): ?>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                    Tag: <?php echo esc(reset($active_tag)['name']); ?>
                    <a href="index.php?<?php echo http_build_query(array_merge($filters, ['tag_id' => ''])); ?>" class="ml-1.5 text-indigo-400 hover:text-indigo-600">&times;</a>
                </span>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($filters['search']): ?>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                Search: "<?php echo esc($filters['search']); ?>"
                <a href="index.php?<?php echo http_build_query(array_merge($filters, ['search' => ''])); ?>" class="ml-1.5 text-slate-400 hover:text-slate-600">&times;</a>
            </span>
        <?php endif; ?>
    </div>
<?php endif; ?>

<!-- Prompts Grid -->
<?php if (empty($prompts)): ?>
    <div class="bg-white rounded-2xl p-12 border border-slate-200 text-center shadow-sm">
        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="h-8 w-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
        </div>
        <h3 class="text-lg font-bold text-slate-900 mb-1">No prompts found</h3>
        <p class="text-slate-500 text-sm max-w-sm mx-auto mb-6">Try adjusting your filters or start fresh with a new prompt.</p>
        <a href="prompt_edit.php" class="inline-flex items-center px-5 py-2.5 bg-primary-600 text-white text-sm font-bold rounded-lg hover:bg-primary-700 transition-colors">
            Create First Prompt
        </a>
    </div>
<?php else: ?>
    <div id="prompts-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
        <?php foreach ($prompts as $prompt): ?>
            <?php include 'includes/prompt_card.php'; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.querySelector('form');
    const grid = document.getElementById('prompts-grid');
    
    if (filterForm && grid) {
        const updateGrid = async (url) => {
            grid.style.opacity = '0.5';
            try {
                const ajaxUrl = new URL(url);
                ajaxUrl.searchParams.set('ajax', '1');
                const response = await fetch(ajaxUrl);
                const html = await response.text();
                grid.innerHTML = html;
                window.history.pushState({}, '', url);
            } catch (e) {
                console.error('Failed to update grid', e);
            }
            grid.style.opacity = '1';
        };

        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(filterForm);
            const params = new URLSearchParams(formData);
            updateGrid(window.location.pathname + '?' + params.toString());
        });

        // Handle select changes automatically
        filterForm.querySelectorAll('select').forEach(select => {
            select.addEventListener('change', () => {
                filterForm.dispatchEvent(new Event('submit'));
            });
        });

        // Handle search input with debounce
        let timeout;
        filterForm.querySelector('input[name="search"]').addEventListener('input', (e) => {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                filterForm.dispatchEvent(new Event('submit'));
            }, 300);
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>
