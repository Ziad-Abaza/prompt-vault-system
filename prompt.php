<?php
require_once 'bootstrap.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    redirect('index.php');
}

$prompt = get_prompt($id);
if (!$prompt) {
    set_flash('Prompt not found.', 'error');
    redirect('index.php');
}

include 'includes/header.php';
?>

<div class="max-w-4xl mx-auto">
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-4">
            <li>
                <div>
                    <a href="index.php" class="text-gray-400 hover:text-gray-500">
                        <svg class="flex-shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span class="sr-only">Home</span>
                    </a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                    <a href="index.php" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">Prompts</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                    <span class="ml-4 text-sm font-medium text-gray-700"><?php echo esc($prompt['title']); ?></span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg border border-gray-200">
        <div class="px-4 py-5 sm:px-6 flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-gray-900"><?php echo esc($prompt['title']); ?></h1>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Category: <span class="font-medium text-gray-900"><?php echo esc($prompt['category_name'] ?? 'Uncategorized'); ?></span>
                </p>
            </div>
            <div class="flex space-x-2">
                <button onclick="copyToClipboard(<?php echo esc(json_encode($prompt['content'])); ?>)" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                    </svg>
                    Copy
                </button>
                <a href="prompt_edit.php?id=<?php echo $prompt['id']; ?>" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit
                </a>
            </div>
        </div>
        <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
            <div class="prose max-w-none text-gray-800 whitespace-pre-wrap font-mono bg-gray-50 p-4 rounded-md border border-gray-200">
                <?php echo esc($prompt['content']); ?>
            </div>
        </div>
        
        <?php if (!empty($prompt['tags'])): ?>
            <div class="px-4 py-4 sm:px-6 border-t border-gray-200">
                <h3 class="text-sm font-medium text-gray-500 mb-2">Tags</h3>
                <div class="flex flex-wrap gap-2">
                    <?php foreach ($prompt['tags'] as $tag): ?>
                        <a href="index.php?tag_id=<?php echo $tag['id']; ?>" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 hover:bg-blue-200">
                            <?php echo esc($tag['name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($prompt['collections'])): ?>
            <div class="px-4 py-4 sm:px-6 border-t border-gray-200">
                <h3 class="text-sm font-medium text-gray-500 mb-2">Collections</h3>
                <div class="flex flex-wrap gap-2">
                    <?php foreach ($prompt['collections'] as $coll): ?>
                        <a href="index.php?collection_id=<?php echo $coll['id']; ?>" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 hover:bg-gray-200">
                            <?php echo esc($coll['name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="px-4 py-4 sm:px-6 border-t border-gray-200 bg-gray-50 text-xs text-gray-500 flex justify-between">
            <span>Created: <?php echo date('M j, Y H:i', strtotime($prompt['created_at'])); ?></span>
            <span>Last Updated: <?php echo date('M j, Y H:i', strtotime($prompt['updated_at'])); ?></span>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
