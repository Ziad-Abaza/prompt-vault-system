<?php
require_once 'bootstrap.php';

$errors = [];

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $validator = new Validator($_POST);
    $validator->required('name', 'Tag name is required.')
              ->max('name', 50);

    if ($validator->is_valid()) {
        $name = trim($_POST['name']);
        $id = $_POST['id'] ?? null;

        try {
            if ($id) {
                update_tag($id, $name);
                set_flash('Tag updated successfully.');
            } else {
                create_tag($name);
                set_flash('New tag created.');
            }
            redirect('tags.php');
        } catch (Exception $e) {
            $errors['form'] = $e->getMessage();
        }
    } else {
        $errors = $validator->get_errors();
    }
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
        <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight mb-2">Tags</h1>
        <p class="text-slate-500 text-lg font-medium">Cross-link prompts with granular labels for better discoverability.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <!-- Form Column -->
        <div class="lg:col-span-1">
            <div class="form-section sticky top-8 shadow-xl shadow-slate-200/40">
                <div class="form-section-header">
                    <h3 class="form-section-title">
                        <?php echo $edit_tag ? 'Edit Tag' : 'New Tag'; ?>
                    </h3>
                </div>
                <form action="tags.php" method="POST" class="form-body !space-y-6">
                    <?php echo csrf_input(); ?>
                    <?php if ($edit_tag): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_tag['id']; ?>">
                    <?php endif; ?>
                    
                    <?php if (isset($errors['form'])): ?>
                        <p class="form-error mb-4"><?php echo esc($errors['form']); ?></p>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="name" class="form-label px-1">Tag Name</label>
                        <input type="text" name="name" id="name" required value="<?php echo esc($_POST['name'] ?? $edit_tag['name'] ?? ''); ?>" 
                            class="form-input <?php echo isset($errors['name']) ? 'border-red-300 ring-4 ring-red-500/5 bg-red-50/30' : ''; ?>" 
                            placeholder="e.g. brainstorming">
                        <?php if (isset($errors['name'])): ?>
                            <p class="form-error"><?php echo esc($errors['name']); ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="flex flex-col space-y-3 pt-2">
                        <button type="submit" class="btn-primary w-full py-3.5 text-sm">
                            <?php echo $edit_tag ? 'Update Tag' : 'Create Tag'; ?>
                        </button>
                        
                        <?php if ($edit_tag): ?>
                            <a href="tags.php" class="btn-secondary w-full py-3.5 text-sm text-center">
                                Discard
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- List Column -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8 min-h-[400px]">
                <div class="flex flex-wrap gap-4">
                    <?php foreach ($tags as $tag): ?>
                        <div class="group relative flex items-center bg-slate-50 border border-slate-100 rounded-2xl px-6 py-4 hover:border-primary-400 hover:bg-white hover:shadow-xl hover:shadow-primary-900/5 transition-all">
                            <a href="index.php?tag_id=<?php echo $tag['id']; ?>" class="text-slate-800 font-bold text-lg mr-12 transition-colors group-hover:text-primary-600">#<?php echo esc($tag['name']); ?></a>
                            <div class="absolute right-2 flex items-center space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="tags.php?edit=<?php echo $tag['id']; ?>" class="p-2 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-xl transition-colors shadow-sm bg-white border border-slate-100">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <a href="tags.php?delete=<?php echo $tag['id']; ?>" onclick="return confirm('Are you sure?');" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-colors shadow-sm bg-white border border-slate-100">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($tags)): ?>
                        <div class="w-full py-20 text-center">
                            <p class="text-slate-400 text-lg italic font-medium">No tags created yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
