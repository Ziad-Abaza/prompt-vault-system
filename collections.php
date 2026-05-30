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
$edit_coll = null;
if (isset($_GET['edit'])) {
    $edit_coll = get_collection($_GET['edit']);
    if (!$edit_coll) {
        abort(404);
    }
}

include 'includes/header.php';
?>

<div class="max-w-7xl mx-auto">
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Collections</h1>
            <p class="text-slate-500 text-sm">Group prompts into focused workspaces for specific workflows.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start pb-20">
        <!-- List Column -->
        <div class="lg:col-span-3 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
            <?php foreach ($collections as $coll): ?>
                <div class="group bg-white rounded-2xl border border-slate-200 p-5 hover:border-primary-400 hover:shadow-xl transition-all duration-200 flex flex-col">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center transition-transform group-hover:scale-105 shadow-sm border border-indigo-100/50">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-500 uppercase tracking-wider border border-slate-200">
                                <?php echo $coll['prompt_count'] ?? 0; ?> Prompts
                            </span>
                        </div>
                        <div class="flex items-center space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <a href="collections.php?edit=<?php echo $coll['id']; ?>" class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            <a href="collections.php?delete=<?php echo $coll['id']; ?>" onclick="return confirm('Are you sure?');" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-1.5 group-hover:text-primary-600 transition-colors truncate"><?php echo esc($coll['name']); ?></h3>
                    <p class="text-slate-500 text-xs mb-6 leading-relaxed line-clamp-2 min-h-[2rem] font-medium"><?php echo esc($coll['description'] ?: 'No description provided for this collection.'); ?></p>
                    <div class="mt-auto">
                        <a href="index.php?collection_id=<?php echo $coll['id']; ?>" class="inline-flex items-center text-[10px] font-bold text-primary-600 hover:text-primary-800 uppercase tracking-widest group">
                            Explore
                            <svg class="w-3 h-3 ml-1.5 transform group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if (empty($collections)): ?>
                <div class="col-span-full py-20 text-center bg-white rounded-2xl border border-dashed border-slate-200 shadow-sm">
                    <p class="text-slate-400 text-sm font-medium">Your collection library is empty.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Form Column -->
        <div class="lg:col-span-1" id="coll-form-container">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden sticky top-8">
                <div class="px-5 py-3 border-b border-slate-50 bg-slate-50/50">
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-widest">
                        <?php echo $edit_coll ? 'Edit Collection' : 'New Workspace'; ?>
                    </h3>
                </div>
                <form action="collections.php" method="POST" class="p-5 space-y-4">
                    <?php echo csrf_input(); ?>
                    <?php if ($edit_coll): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_coll['id']; ?>">
                    <?php endif; ?>

                    <?php if (isset($errors['form'])): ?>
                        <p class="text-red-500 text-[10px] font-bold"><?php echo esc($errors['form']); ?></p>
                    <?php endif; ?>
                    
                    <div class="space-y-1">
                        <label for="name" class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest px-1">Name</label>
                        <input type="text" name="name" id="name" required value="<?php echo esc($_POST['name'] ?? $edit_coll['name'] ?? ''); ?>" 
                            class="block w-full px-3 py-2 bg-slate-50 border border-slate-100 rounded-lg focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 focus:bg-white transition-all text-sm font-bold text-slate-900 <?php echo isset($errors['name']) ? 'border-red-300' : ''; ?>" 
                            placeholder="e.g. Content Strategy">
                        <?php if (isset($errors['name'])): ?>
                            <p class="text-red-500 text-[10px] font-bold mt-1"><?php echo esc($errors['name']); ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="space-y-1">
                        <label for="description" class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest px-1">Description</label>
                        <textarea name="description" id="description" rows="4" 
                            class="block w-full px-3 py-2 bg-slate-50 border border-slate-100 rounded-lg focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 focus:bg-white transition-all text-xs font-medium text-slate-700 leading-relaxed <?php echo isset($errors['description']) ? 'border-red-300' : ''; ?>" 
                            placeholder="Briefly describe the scope of this workspace..."><?php echo esc($_POST['description'] ?? $edit_coll['description'] ?? ''); ?></textarea>
                        <?php if (isset($errors['description'])): ?>
                            <p class="text-red-500 text-[10px] font-bold mt-1"><?php echo esc($errors['description']); ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="flex flex-col gap-2 pt-2">
                        <button type="submit" class="px-5 py-2.5 bg-primary-600 text-white text-xs font-bold rounded-lg hover:bg-primary-700 shadow-md shadow-primary-600/10 transition-all active:scale-[0.98]">
                            <?php echo $edit_coll ? 'Save Changes' : 'Create Workspace'; ?>
                        </button>
                        
                        <?php if ($edit_coll): ?>
                            <a href="collections.php" class="px-5 py-2.5 bg-white border border-slate-200 text-slate-600 text-xs font-bold rounded-lg hover:bg-slate-50 text-center transition-all">
                                Cancel
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
