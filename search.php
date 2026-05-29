<?php
require_once 'bootstrap.php';

include 'includes/header.php';
?>

<div class="max-w-2xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-900 mb-8 text-center">Search Your Prompts</h1>

    <div class="bg-white shadow sm:rounded-lg overflow-hidden border border-gray-200">
        <div class="px-4 py-8 sm:p-10">
            <form action="index.php" method="GET" class="space-y-6">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700">Keyword</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" name="search" id="search" class="focus:ring-primary focus:border-primary block w-full pl-10 sm:text-lg border-gray-300 rounded-md py-3" placeholder="Search by title or content...">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
                        <select name="category_id" id="category_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm">
                            <option value="">Any Category</option>
                            <?php foreach (get_categories() as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>"><?php echo esc($cat['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="tag_id" class="block text-sm font-medium text-gray-700">Tag</label>
                        <select name="tag_id" id="tag_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm">
                            <option value="">Any Tag</option>
                            <?php foreach (get_tags() as $tag): ?>
                                <option value="<?php echo $tag['id']; ?>"><?php echo esc($tag['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-lg font-medium text-white bg-primary hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        Find Prompts
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="mt-10 text-center">
        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Quick Links</h3>
        <div class="mt-4 flex flex-wrap justify-center gap-2">
            <a href="index.php" class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50">
                All Prompts
            </a>
            <?php foreach (get_categories() as $cat): ?>
                <a href="index.php?category_id=<?php echo $cat['id']; ?>" class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50">
                    <?php echo esc($cat['name']); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
