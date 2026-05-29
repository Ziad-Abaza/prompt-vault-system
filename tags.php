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
                set_flash('Tag updated successfully.');
            } else {
                create_tag($name);
                set_flash('New tag created.');
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
    set_flash('Tag removed.');
    redirect('tags.php');
}

$tags = get_tags();
$edit_tag = isset($_GET['edit']) ? get_tag($_GET['edit']) : null;

include 'includes/header.php';
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-12">
        <h1 class="text-4xl font-bold text-slate-900 tracking-tight mb-2">Tags</h1>
        <p class="text-slate-500 text-lg">Cross-link prompts with reusable labels.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <!-- Form Column -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6 sticky top-8">
                <h3 class="text-lg font-bold text-slate-900 mb-6">
                    <?php echo $edit_tag ? 'Edit Tag' : 'New Tag'; ?>
                </h3>
                <form action="tags.php" method="POST" class="space-y-4">
                    <?php echo csrf_input(); ?>
                    <?php if ($edit_tag): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_tag['id']; ?>">
                    <?php endif; ?>
                    
                    <div>
                        <label for="name" class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Tag Name</label>
                        <input type="text" name="name" id="name" required value="<?php echo esc($edit_tag['name'] ?? ''); ?>" class="block w-full px-4 py-3 bg-slate-50 border-slate-200 rounded-xl focus:ring-primary-500 focus:border-primary-500 sm:text-sm" placeholder="e.g. brainstorming">
                    </div>
                    
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                        <?php echo $edit_tag ? 'Update Tag' : 'Create Tag'; ?>
                    </button>
                    
                    <?php if ($edit_tag): ?>
                        <a href="tags.php" class="w-full flex justify-center py-3 px-4 border border-slate-200 rounded-xl text-sm font-bold text-slate-600 bg-white hover:bg-slate-50 transition-colors">
                            Cancel
                        </a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- List Column -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8">
                <div class="flex flex-wrap gap-3">
                    <?php foreach ($tags as $tag): ?>
                        <div class="group relative flex items-center bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 hover:border-primary-300 transition-all">
                            <a href="index.php?tag_id=<?php echo $tag['id']; ?>" class="text-slate-700 font-semibold mr-8">#<?php echo esc($tag['name']); ?></a>
                            <div class="absolute right-2 flex items-center space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="tags.php?edit=<?php echo $tag['id']; ?>" class="p-1 text-slate-400 hover:text-amber-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <a href="tags.php?delete=<?php echo $tag['id']; ?>" onclick="return confirm('Are you sure?');" class="p-1 text-slate-400 hover:text-red-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($tags)): ?>
                        <div class="w-full py-12 text-center">
                            <p class="text-slate-400 text-sm italic">No tags created yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
