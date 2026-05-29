<?php
require_once 'bootstrap.php';

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $id = $_POST['id'] ?? null;

    if (!empty($name)) {
        try {
            if ($id) {
                update_collection($id, $name, $description);
                set_flash('Collection updated.');
            } else {
                create_collection($name, $description);
                set_flash('Collection created.');
            }
        } catch (Exception $e) {
            set_flash('Error: ' . $e->getMessage(), 'error');
        }
    }
    redirect('collections.php');
}

// Handle Delete
if (isset($_GET['delete'])) {
    delete_collection($_GET['delete']);
    set_flash('Collection deleted.');
    redirect('collections.php');
}

$collections = get_collections();
$edit_coll = isset($_GET['edit']) ? get_collection($_GET['edit']) : null;

include 'includes/header.php';
?>

<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Manage Collections</h1>

    <div class="bg-white shadow sm:rounded-lg mb-8">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                <?php echo $edit_coll ? 'Edit Collection' : 'Add New Collection'; ?>
            </h3>
            <form action="collections.php" method="POST" class="mt-5 space-y-4">
                <?php echo csrf_input(); ?>
                <?php if ($edit_coll): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_coll['id']; ?>">
                <?php endif; ?>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" name="name" id="name" required value="<?php echo esc($edit_coll['name'] ?? ''); ?>" class="mt-1 shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md" placeholder="e.g. Creative Writing">
                    </div>
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description (Optional)</label>
                        <input type="text" name="description" id="description" value="<?php echo esc($edit_coll['description'] ?? ''); ?>" class="mt-1 shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Brief description...">
                    </div>
                </div>

                <div class="flex space-x-3">
                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        <?php echo $edit_coll ? 'Update' : 'Add'; ?>
                    </button>
                    <?php if ($edit_coll): ?>
                        <a href="collections.php" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                            Cancel
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <?php foreach ($collections as $coll): ?>
            <div class="bg-white shadow overflow-hidden sm:rounded-lg border border-gray-200">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900"><?php echo esc($coll['name']); ?></h3>
                            <p class="mt-1 text-sm text-gray-500"><?php echo esc($coll['description'] ?: 'No description.'); ?></p>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-4">
                        <a href="index.php?collection_id=<?php echo $coll['id']; ?>" class="text-sm font-medium text-primary hover:text-blue-900">View Prompts</a>
                        <a href="collections.php?edit=<?php echo $coll['id']; ?>" class="text-sm font-medium text-gray-500 hover:text-gray-700">Edit</a>
                        <a href="collections.php?delete=<?php echo $coll['id']; ?>" onclick="return confirm('Are you sure you want to delete this collection? Prompts will not be deleted.');" class="text-sm font-medium text-red-600 hover:text-red-900">Delete</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (empty($collections)): ?>
            <div class="md:col-span-2 bg-white p-8 text-center text-gray-500 italic border-2 border-dashed border-gray-300 rounded-lg">
                No collections yet.
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
