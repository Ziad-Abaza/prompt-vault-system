<?php
require_once 'bootstrap.php';

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $id = $_POST['id'] ?? null;

    if (!empty($name)) {
        try {
            if ($id) {
                update_category($id, $name);
                set_flash('Category updated successfully.');
            } else {
                create_category($name);
                set_flash('New category created.');
            }
        } catch (Exception $e) {
            set_flash('Error: ' . $e->getMessage(), 'error');
        }
    }
    redirect('categories.php');
}

// Handle Delete
if (isset($_GET['delete'])) {
    delete_category($_GET['delete']);
    set_flash('Category removed.');
    redirect('categories.php');
}

$categories = get_categories();
$edit_cat = isset($_GET['edit']) ? get_category($_GET['edit']) : null;

include 'includes/header.php';
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-12">
        <h1 class="text-4xl font-bold text-slate-900 tracking-tight mb-2">Categories</h1>
        <p class="text-slate-500 text-lg">Classify your prompts for better organization.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <!-- Form Column -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6 sticky top-8">
                <h3 class="text-lg font-bold text-slate-900 mb-6">
                    <?php echo $edit_cat ? 'Edit Category' : 'New Category'; ?>
                </h3>
                <form action="categories.php" method="POST" class="space-y-4">
                    <?php echo csrf_input(); ?>
                    <?php if ($edit_cat): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_cat['id']; ?>">
                    <?php endif; ?>
                    
                    <div>
                        <label for="name" class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Category Name</label>
                        <input type="text" name="name" id="name" required value="<?php echo esc($edit_cat['name'] ?? ''); ?>" class="block w-full px-4 py-3 bg-slate-50 border-slate-200 rounded-xl focus:ring-primary-500 focus:border-primary-500 sm:text-sm" placeholder="e.g. Creative Writing">
                    </div>
                    
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                        <?php echo $edit_cat ? 'Update Category' : 'Create Category'; ?>
                    </button>
                    
                    <?php if ($edit_cat): ?>
                        <a href="categories.php" class="w-full flex justify-center py-3 px-4 border border-slate-200 rounded-xl text-sm font-bold text-slate-600 bg-white hover:bg-slate-50 transition-colors">
                            Cancel
                        </a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- List Column -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                <ul class="divide-y divide-slate-100">
                    <?php foreach ($categories as $cat): ?>
                        <li class="group hover:bg-slate-50 transition-colors">
                            <div class="px-6 py-5 flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center mr-4">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                        </svg>
                                    </div>
                                    <span class="text-slate-900 font-semibold"><?php echo esc($cat['name']); ?></span>
                                </div>
                                <div class="flex items-center space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="index.php?category_id=<?php echo $cat['id']; ?>" class="p-2 text-slate-400 hover:text-primary-600 hover:bg-white rounded-lg transition-colors" title="View Prompts">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    <a href="categories.php?edit=<?php echo $cat['id']; ?>" class="p-2 text-slate-400 hover:text-amber-600 hover:bg-white rounded-lg transition-colors" title="Edit Category">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <a href="categories.php?delete=<?php echo $cat['id']; ?>" onclick="return confirm('Are you sure? Prompts in this category will become Uncategorized.');" class="p-2 text-slate-400 hover:text-red-600 hover:bg-white rounded-lg transition-colors" title="Delete Category">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                    <?php if (empty($categories)): ?>
                        <li class="px-6 py-12 text-center">
                            <p class="text-slate-400 text-sm italic">No categories created yet.</p>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
