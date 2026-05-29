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
                set_flash('Collection updated successfully.');
            } else {
                create_collection($name, $description);
                set_flash('New collection created.');
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
    set_flash('Collection removed.');
    redirect('collections.php');
}

$collections = get_collections();
$edit_coll = isset($_GET['edit']) ? get_collection($_GET['edit']) : null;

include 'includes/header.php';
?>

<div class="max-w-6xl mx-auto">
    <div class="mb-12 flex flex-col md:flex-row md:items-end md:justify-between gap-6">
        <div>
            <h1 class="text-4xl font-bold text-slate-900 tracking-tight mb-2">Collections</h1>
            <p class="text-slate-500 text-lg">Group prompts into logical workspaces.</p>
        </div>
        <?php if (!$edit_coll): ?>
            <button onclick="document.getElementById('coll-form').scrollIntoView({behavior: 'smooth'})" class="inline-flex items-center px-6 py-3 bg-slate-900 text-white font-semibold rounded-xl hover:bg-slate-800 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                New Collection
            </button>
        <?php endif; ?>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 items-start">
        <!-- List Column -->
        <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php foreach ($collections as $coll): ?>
                <div class="group bg-white rounded-3xl border border-slate-200 p-8 hover:border-primary-300 hover:shadow-xl hover:shadow-primary-900/5 transition-all">
                    <div class="flex items-center justify-between mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <div class="flex items-center space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <a href="collections.php?edit=<?php echo $coll['id']; ?>" class="p-2 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            <a href="collections.php?delete=<?php echo $coll['id']; ?>" onclick="return confirm('Are you sure?');" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2"><?php echo esc($coll['name']); ?></h3>
                    <p class="text-slate-500 text-sm mb-8 leading-relaxed line-clamp-2"><?php echo esc($coll['description'] ?: 'No description provided for this collection.'); ?></p>
                    <a href="index.php?collection_id=<?php echo $coll['id']; ?>" class="inline-flex items-center text-sm font-bold text-primary-600 hover:text-primary-700 uppercase tracking-widest">
                        View Workspace
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </a>
                </div>
            <?php endforeach; ?>
            <?php if (empty($collections)): ?>
                <div class="col-span-full py-20 text-center bg-white rounded-3xl border border-dashed border-slate-200">
                    <p class="text-slate-400 text-lg italic">No collections found.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Form Column -->
        <div class="lg:col-span-1" id="coll-form">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8 sticky top-8">
                <h3 class="text-xl font-bold text-slate-900 mb-8">
                    <?php echo $edit_coll ? 'Edit Collection' : 'Create Collection'; ?>
                </h3>
                <form action="collections.php" method="POST" class="space-y-6">
                    <?php echo csrf_input(); ?>
                    <?php if ($edit_coll): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_coll['id']; ?>">
                    <?php endif; ?>
                    
                    <div>
                        <label for="name" class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Collection Title</label>
                        <input type="text" name="name" id="name" required value="<?php echo esc($edit_coll['name'] ?? ''); ?>" class="block w-full px-4 py-3 bg-slate-50 border-slate-200 rounded-xl focus:ring-primary-500 focus:border-primary-500 sm:text-sm" placeholder="e.g. Content Marketing">
                    </div>

                    <div>
                        <label for="description" class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Description</label>
                        <textarea name="description" id="description" rows="4" class="block w-full px-4 py-3 bg-slate-50 border-slate-200 rounded-xl focus:ring-primary-500 focus:border-primary-500 sm:text-sm" placeholder="What is this collection for?"><?php echo esc($edit_coll['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <button type="submit" class="w-full flex justify-center py-4 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                        <?php echo $edit_coll ? 'Save Changes' : 'Create Collection'; ?>
                    </button>
                    
                    <?php if ($edit_coll): ?>
                        <a href="collections.php" class="w-full flex justify-center py-4 px-4 border border-slate-200 rounded-xl text-sm font-bold text-slate-600 bg-white hover:bg-slate-50 transition-colors">
                            Discard
                        </a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
