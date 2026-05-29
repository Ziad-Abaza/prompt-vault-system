<?php
require_once 'bootstrap.php';

$errors = [];

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $validator = new Validator($_POST);
    $validator->required('name', 'Collection name is required.')
              ->max('name', 100)
              ->max('description', 500);

    if ($validator->is_valid()) {
        $name = trim($_POST['name']);
        $description = trim($_POST['description'] ?? '');
        $id = $_POST['id'] ?? null;

        try {
            if ($id) {
                update_collection($id, $name, $description);
                set_flash('Collection updated successfully.');
            } else {
                create_collection($name, $description);
                set_flash('New collection created.');
            }
            redirect('collections.php');
        } catch (Exception $e) {
            $errors['form'] = $e->getMessage();
        }
    } else {
        $errors = $validator->get_errors();
    }
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
            <p class="text-slate-500 text-lg">Create focused workspaces by grouping related prompts together.</p>
        </div>
        <?php if (!$edit_coll): ?>
            <button onclick="document.getElementById('coll-form-container').scrollIntoView({behavior: 'smooth'})" class="btn-primary">
                New Collection
            </button>
        <?php endif; ?>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 items-start">
        <!-- List Column -->
        <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-8">
            <?php foreach ($collections as $coll): ?>
                <div class="group bg-white rounded-3xl border border-slate-200 p-8 hover:border-primary-300 hover:shadow-xl hover:shadow-primary-900/5 transition-all">
                    <div class="flex items-center justify-between mb-8">
                        <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center transition-transform group-hover:scale-110 shadow-sm border border-indigo-100/50">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <div class="flex items-center space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <a href="collections.php?edit=<?php echo $coll['id']; ?>" class="p-2 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-xl transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            <a href="collections.php?delete=<?php echo $coll['id']; ?>" onclick="return confirm('Are you sure?');" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-3 group-hover:text-primary-600 transition-colors"><?php echo esc($coll['name']); ?></h3>
                    <p class="text-slate-500 text-sm mb-10 leading-relaxed line-clamp-2 min-h-[2.5rem]"><?php echo esc($coll['description'] ?: 'No description provided for this collection.'); ?></p>
                    <a href="index.php?collection_id=<?php echo $coll['id']; ?>" class="inline-flex items-center text-xs font-bold text-primary-600 hover:text-primary-800 uppercase tracking-widest group">
                        Open Workspace
                        <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </a>
                </div>
            <?php endforeach; ?>
            <?php if (empty($collections)): ?>
                <div class="col-span-full py-24 text-center bg-white rounded-3xl border border-dashed border-slate-200">
                    <p class="text-slate-400 text-lg italic font-medium">Your collection library is empty.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Form Column -->
        <div class="lg:col-span-1" id="coll-form-container">
            <div class="form-section sticky top-8 shadow-xl shadow-slate-200/50">
                <div class="px-8 py-6 border-b border-slate-50 bg-slate-50/50">
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-widest">
                        <?php echo $edit_coll ? 'Edit Collection' : 'Create New Collection'; ?>
                    </h3>
                </div>
                <form action="collections.php" method="POST" class="p-8 space-y-8">
                    <?php echo csrf_input(); ?>
                    <?php if ($edit_coll): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_coll['id']; ?>">
                    <?php endif; ?>

                    <?php if (isset($errors['form'])): ?>
                        <p class="form-error mb-4"><?php echo esc($errors['form']); ?></p>
                    <?php endif; ?>
                    
                    <div>
                        <label for="name" class="form-label px-1">Collection Title</label>
                        <input type="text" name="name" id="name" required value="<?php echo esc($_POST['name'] ?? $edit_coll['name'] ?? ''); ?>" 
                            class="form-input font-bold <?php echo isset($errors['name']) ? 'border-red-300 ring-red-100' : ''; ?>" 
                            placeholder="e.g. Sales Templates">
                        <?php if (isset($errors['name'])): ?>
                            <p class="form-error"><?php echo esc($errors['name']); ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="description" class="form-label px-1">Description (Optional)</label>
                        <textarea name="description" id="description" rows="5" 
                            class="form-input text-sm leading-relaxed <?php echo isset($errors['description']) ? 'border-red-300 ring-red-100' : ''; ?>" 
                            placeholder="Briefly describe the purpose of this group..."><?php echo esc($_POST['description'] ?? $edit_coll['description'] ?? ''); ?></textarea>
                        <?php if (isset($errors['description'])): ?>
                            <p class="form-error"><?php echo esc($errors['description']); ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="flex flex-col space-y-4 pt-4">
                        <button type="submit" class="btn-primary w-full">
                            <?php echo $edit_coll ? 'Save Changes' : 'Create Collection'; ?>
                        </button>
                        
                        <?php if ($edit_coll): ?>
                            <a href="collections.php" class="btn-secondary w-full text-center">
                                Discard
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
