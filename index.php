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

include 'includes/header.php';
?>

<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Prompts</h1>
    <a href="prompt_edit.php" class="mt-4 md:mt-0 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        New Prompt
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    <!-- Sidebar Filters -->
    <div class="lg:col-span-1 space-y-6">
        <form action="index.php" method="GET" class="space-y-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                <input type="text" name="search" id="search" value="<?php echo esc($filters['search']); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm" placeholder="Title or content...">
            </div>
            
            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
                <select name="category_id" id="category_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo $filters['category_id'] == $cat['id'] ? 'selected' : ''; ?>>
                            <?php echo esc($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="tag_id" class="block text-sm font-medium text-gray-700">Tag</label>
                <select name="tag_id" id="tag_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm">
                    <option value="">All Tags</option>
                    <?php foreach ($tags as $tag): ?>
                        <option value="<?php echo $tag['id']; ?>" <?php echo $filters['tag_id'] == $tag['id'] ? 'selected' : ''; ?>>
                            <?php echo esc($tag['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="collection_id" class="block text-sm font-medium text-gray-700">Collection</label>
                <select name="collection_id" id="collection_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm">
                    <option value="">All Collections</option>
                    <?php foreach ($collections as $coll): ?>
                        <option value="<?php echo $coll['id']; ?>" <?php echo $filters['collection_id'] == $coll['id'] ? 'selected' : ''; ?>>
                            <?php echo esc($coll['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex space-x-2">
                <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    Filter
                </button>
                <a href="index.php" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Prompts List -->
    <div class="lg:col-span-3">
        <?php if (empty($prompts)): ?>
            <div class="bg-white p-12 rounded-lg border-2 border-dashed border-gray-300 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No prompts found</h3>
                <p class="mt-1 text-sm text-gray-500">Try adjusting your filters or create a new prompt.</p>
                <div class="mt-6">
                    <a href="prompt_edit.php" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        New Prompt
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($prompts as $prompt): ?>
                    <div class="bg-white shadow overflow-hidden sm:rounded-md border border-gray-200">
                        <div class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <a href="prompt.php?id=<?php echo $prompt['id']; ?>" class="text-lg font-medium text-primary truncate">
                                    <?php echo esc($prompt['title']); ?>
                                </a>
                                <div class="ml-2 flex-shrink-0 flex">
                                    <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        <?php echo esc($prompt['category_name'] ?? 'Uncategorized'); ?>
                                    </p>
                                </div>
                            </div>
                            <div class="mt-2 sm:flex sm:justify-between">
                                <div class="sm:flex">
                                    <p class="flex items-center text-sm text-gray-500 line-clamp-2 mr-4">
                                        <?php echo esc(mb_strimwidth($prompt['content'], 0, 150, "...")); ?>
                                    </p>
                                </div>
                                <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0 whitespace-nowrap">
                                    <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p>
                                        Updated <?php echo date('M j, Y', strtotime($prompt['updated_at'])); ?>
                                    </p>
                                </div>
                            </div>
                            <div class="mt-4 flex justify-end space-x-3">
                                <button onclick="copyToClipboard(<?php echo esc(json_encode($prompt['content'])); ?>)" class="text-gray-400 hover:text-gray-600">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                    </svg>
                                </button>
                                <a href="prompt_edit.php?id=<?php echo $prompt['id']; ?>" class="text-gray-400 hover:text-gray-600">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
