<?php
require_once 'bootstrap.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    redirect('index.php');
}

$prompt = get_prompt($id);
if (!$prompt) {
    abort(404);
}

$page_title = $prompt['title'];
$meta_description = substr(strip_tags($prompt['content']), 0, 160);
$breadcrumbs = [
    ['name' => 'Library', 'url' => 'index.php'],
    ['name' => $prompt['category_name'] ?? 'Uncategorized', 'url' => 'index.php?category_id=' . ($prompt['category_id'] ?? '')],
    ['name' => $prompt['title'], 'url' => 'prompt.php?id=' . $prompt['id']]
];

include 'includes/header.php';
?>

<div class="max-w-4xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="flex mb-10" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2 text-sm font-medium">
            <li>
                <a href="index.php" class="text-slate-400 hover:text-slate-600 transition-colors">Workspace</a>
            </li>
            <li>
                <svg class="h-5 w-5 text-slate-300" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
            </li>
            <li class="text-slate-900 truncate max-w-[200px]"><?php echo esc($prompt['title']); ?></li>
        </ol>
    </nav>

    <!-- Header Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
        <div class="flex-grow">
            <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight leading-tight mb-3">
                <?php echo esc($prompt['title']); ?>
            </h1>
            <div class="flex flex-wrap items-center gap-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-primary-100 text-primary-800 uppercase tracking-wider">
                    <?php echo esc($prompt['category_name'] ?? 'Uncategorized'); ?>
                </span>
                <span class="text-slate-400 text-sm">
                    Updated <?php echo date('M j, Y', strtotime($prompt['updated_at'])); ?>
                </span>
            </div>
        </div>
        <div class="flex items-center space-x-3 shrink-0">
            <button onclick="copyToClipboard(<?php echo esc(json_encode($prompt['content'])); ?>, this)" class="inline-flex items-center px-5 py-2.5 bg-slate-900 text-white text-sm font-semibold rounded-xl hover:bg-slate-800 transition-colors shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                </svg>
                Copy Content
            </button>
            <a href="prompt_edit.php?id=<?php echo $prompt['id']; ?>" class="inline-flex items-center px-5 py-2.5 bg-white border border-slate-200 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-50 transition-colors shadow-sm">
                <svg class="w-5 h-5 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit
            </a>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-8 md:p-12">
            <div class="prose prose-slate prose-lg max-w-none">
                <div class="bg-slate-50 rounded-2xl p-6 md:p-8 font-mono text-slate-800 whitespace-pre-wrap leading-relaxed border border-slate-100 text-base md:text-lg">
                    <?php echo esc($prompt['content']); ?>
                </div>
            </div>
        </div>

        <!-- Footer Metadata -->
        <div class="px-8 py-6 bg-slate-50 border-t border-slate-100">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <?php if (!empty($prompt['tags'])): ?>
                    <div>
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Associated Tags</h3>
                        <div class="flex flex-wrap gap-2">
                            <?php foreach ($prompt['tags'] as $tag): ?>
                                <a href="index.php?tag_id=<?php echo $tag['id']; ?>" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-white border border-slate-200 text-slate-600 hover:border-primary-300 hover:text-primary-700 transition-colors">
                                    #<?php echo esc($tag['name']); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($prompt['collections'])): ?>
                    <div>
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Part of Collections</h3>
                        <div class="flex flex-wrap gap-2">
                            <?php foreach ($prompt['collections'] as $coll): ?>
                                <a href="index.php?collection_id=<?php echo $coll['id']; ?>" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-white border border-slate-200 text-slate-600 hover:border-primary-300 hover:text-primary-700 transition-colors">
                                    <svg class="w-3 h-3 mr-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                    <?php echo esc($coll['name']); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Navigation Footer -->
    <div class="mt-12 flex justify-between items-center text-sm text-slate-500 font-medium border-t border-slate-200 pt-8">
        <a href="index.php" class="flex items-center hover:text-primary-600 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Library
        </a>
        <span class="hidden md:inline">ID: #<?php echo $prompt['id']; ?> &bull; Created <?php echo date('M j, Y', strtotime($prompt['created_at'])); ?></span>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
