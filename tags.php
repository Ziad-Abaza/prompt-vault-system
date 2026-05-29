<?php
require_once 'bootstrap.php';

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $id = $_POST['id'] ?? null;

    if (!empty($name)) {
        try {
            if ($id) {
                update_tag($id, $name);
                set_flash('Tag updated.');
            } else {
                create_tag($name);
                set_flash('Tag created.');
            }
        } catch (Exception $e) {
            set_flash('Error: ' . $e->getMessage(), 'error');
        }
    }
    redirect('tags.php');
}

// Handle Delete
if (isset($_GET['delete'])) {
    delete_tag($_GET['delete']);
    set_flash('Tag deleted.');
    redirect('tags.php');
}

$tags = get_tags();
$edit_tag = isset($_GET['edit']) ? get_tag($_GET['edit']) : null;

include 'includes/header.php';
?>

<div class="max-w-2xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Manage Tags</h1>

    <div class="bg-white shadow sm:rounded-lg mb-8">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                <?php echo $edit_tag ? 'Edit Tag' : 'Add New Tag'; ?>
            </h3>
            <form action="tags.php" method="POST" class="mt-5 sm:flex sm:items-center">
                <?php echo csrf_input(); ?>
                <?php if ($edit_tag): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_tag['id']; ?>">
                <?php endif; ?>
                <div class="w-full sm:max-w-xs">
                    <label for="name" class="sr-only">Name</label>
                    <input type="text" name="name" id="name" required value="<?php echo esc($edit_tag['name'] ?? ''); ?>" class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Tag name">
                </div>
                <button type="submit" class="mt-3 w-full inline-flex items-center justify-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:mt-0 sm:ml-3 sm:w-auto">
                    <?php echo $edit_tag ? 'Update' : 'Add'; ?>
                </button>
                <?php if ($edit_tag): ?>
                    <a href="tags.php" class="mt-3 w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:mt-0 sm:ml-3 sm:w-auto">
                        Cancel
                    </a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-md border border-gray-200">
        <ul class="divide-y divide-gray-200">
            <?php foreach ($tags as $tag): ?>
                <li>
                    <div class="px-4 py-4 flex items-center justify-between sm:px-6">
                        <div class="text-sm font-medium text-gray-900">
                            <?php echo esc($tag['name']); ?>
                        </div>
                        <div class="flex space-x-4">
                            <a href="tags.php?edit=<?php echo $tag['id']; ?>" class="text-primary hover:text-blue-900 text-sm font-medium">Edit</a>
                            <a href="tags.php?delete=<?php echo $tag['id']; ?>" onclick="return confirm('Are you sure? Prompts with this tag will just lose the tag.');" class="text-red-600 hover:text-red-900 text-sm font-medium">Delete</a>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
            <?php if (empty($tags)): ?>
                <li class="px-4 py-8 text-center text-gray-500 italic">No tags yet.</li>
            <?php endif; ?>
        </ul>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
