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
                set_flash('Category updated.');
            } else {
                create_category($name);
                set_flash('Category created.');
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
    set_flash('Category deleted.');
    redirect('categories.php');
}

$categories = get_categories();
$edit_cat = isset($_GET['edit']) ? get_category($_GET['edit']) : null;

include 'includes/header.php';
?>

<div class="max-w-2xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Manage Categories</h1>

    <div class="bg-white shadow sm:rounded-lg mb-8">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                <?php echo $edit_cat ? 'Edit Category' : 'Add New Category'; ?>
            </h3>
            <form action="categories.php" method="POST" class="mt-5 sm:flex sm:items-center">
                <?php echo csrf_input(); ?>
                <?php if ($edit_cat): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_cat['id']; ?>">
                <?php endif; ?>
                <div class="w-full sm:max-w-xs">
                    <label for="name" class="sr-only">Name</label>
                    <input type="text" name="name" id="name" required value="<?php echo esc($edit_cat['name'] ?? ''); ?>" class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Category name">
                </div>
                <button type="submit" class="mt-3 w-full inline-flex items-center justify-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:mt-0 sm:ml-3 sm:w-auto">
                    <?php echo $edit_cat ? 'Update' : 'Add'; ?>
                </button>
                <?php if ($edit_cat): ?>
                    <a href="categories.php" class="mt-3 w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:mt-0 sm:ml-3 sm:w-auto">
                        Cancel
                    </a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-md border border-gray-200">
        <ul class="divide-y divide-gray-200">
            <?php foreach ($categories as $cat): ?>
                <li>
                    <div class="px-4 py-4 flex items-center justify-between sm:px-6">
                        <div class="text-sm font-medium text-gray-900">
                            <?php echo esc($cat['name']); ?>
                        </div>
                        <div class="flex space-x-4">
                            <a href="categories.php?edit=<?php echo $cat['id']; ?>" class="text-primary hover:text-blue-900 text-sm font-medium">Edit</a>
                            <a href="categories.php?delete=<?php echo $cat['id']; ?>" onclick="return confirm('Are you sure? Prompts in this category will become Uncategorized.');" class="text-red-600 hover:text-red-900 text-sm font-medium">Delete</a>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
            <?php if (empty($categories)): ?>
                <li class="px-4 py-8 text-center text-gray-500 italic">No categories yet.</li>
            <?php endif; ?>
        </ul>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
