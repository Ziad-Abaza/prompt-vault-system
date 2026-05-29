<?php
require_once 'bootstrap.php';

$errors = [];

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $validator = new Validator($_POST);
    $validator->required('name', 'Category name is required.')
              ->max('name', 50);

    if ($validator->is_valid()) {
        $name = trim($_POST['name']);
        $id = $_POST['id'] ?? null;

        try {
            if ($id) {
                update_category($id, $name);
                set_flash('Category updated successfully.');
            } else {
                create_category($name);
                set_flash('New category created.');
            }
            redirect('categories.php');
        } catch (Exception $e) {
            $errors['form'] = $e->getMessage();
        }
    } else {
        $errors = $validator->get_errors();
    }
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
        <p class="text-slate-500 text-lg">Organize your prompts into high-level classifications.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <!-- Form Column -->
        <div class="lg:col-span-1">
            <div class="form-section sticky top-8">
                <div class="px-6 py-4 border-b border-slate-50 bg-slate-50/50">
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-widest">
                        <?php echo $edit_cat ? 'Edit Category' : 'New Category'; ?>
                    </h3>
                </div>
                <form action="categories.php" method="POST" class="p-6 space-y-6">
                    <?php echo csrf_input(); ?>
                    <?php if ($edit_cat): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_cat['id']; ?>">
                    <?php endif; ?>
                    
                    <?php if (isset($errors['form'])): ?>
                        <p class="form-error mb-4"><?php echo esc($errors['form']); ?></p>
                    <?php endif; ?>

                    <div>
                        <label for="name" class="form-label px-1">Display Name</label>
                        <input type="text" name="name" id="name" required value="<?php echo esc($_POST['name'] ?? $edit_cat['name'] ?? ''); ?>" 
                            class="form-input <?php echo isset($errors['name']) ? 'border-red-300 ring-red-100' : ''; ?>" 
                            placeholder="e.g. Marketing">
                        <?php if (isset($errors['name'])): ?>
                            <p class="form-error"><?php echo esc($errors['name']); ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="flex flex-col space-y-3">
                        <button type="submit" class="btn-primary w-full py-2.5 text-sm">
                            <?php echo $edit_cat ? 'Update' : 'Create'; ?>
                        </button>
                        
                        <?php if ($edit_cat): ?>
                            <a href="categories.php" class="btn-secondary w-full py-2.5 text-sm text-center">
                                Cancel
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- List Column -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                <ul class="divide-y divide-slate-100">
                    <?php foreach ($categories as $cat): ?>
                        <li class="group hover:bg-slate-50 transition-colors">
                            <div class="px-8 py-6 flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 rounded-2xl bg-primary-50 text-primary-600 flex items-center justify-center mr-5 transition-transform group-hover:scale-110">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                        </svg>
                                    </div>
                                    <span class="text-slate-900 font-bold text-lg"><?php echo esc($cat['name']); ?></span>
                                </div>
                                <div class="flex items-center space-x-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="index.php?category_id=<?php echo $cat['id']; ?>" class="p-2 text-slate-400 hover:text-primary-600 hover:bg-white rounded-xl transition-colors shadow-sm" title="Filter Prompts">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                                        </svg>
                                    </a>
                                    <a href="categories.php?edit=<?php echo $cat['id']; ?>" class="p-2 text-slate-400 hover:text-amber-600 hover:bg-white rounded-xl transition-colors shadow-sm" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <a href="categories.php?delete=<?php echo $cat['id']; ?>" onclick="return confirm('Are you sure? Prompts in this category will become Uncategorized.');" class="p-2 text-slate-400 hover:text-red-600 hover:bg-white rounded-xl transition-colors shadow-sm" title="Delete">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                    <?php if (empty($categories)): ?>
                        <li class="px-8 py-16 text-center">
                            <p class="text-slate-400 text-sm italic">You haven't created any categories yet.</p>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
