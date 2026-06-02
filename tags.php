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
$edit_tag = null;
if (isset($_GET['edit'])) {
    $edit_tag = get_tag($_GET['edit']);
    if (!$edit_tag) {
        abort(404);
    }
}

include 'includes/header.php';
?>

<div class="max-w-6xl mx-auto">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
        <div>
            <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight mb-2">Manage <span class="text-primary-600">Tags</span></h1>
            <p class="text-slate-500 font-medium max-w-md">Granular labels to cross-link prompts and improve granular discoverability across your vault.</p>
        </div>
        
        <!-- Quick Stats -->
        <div class="flex items-center gap-4">
            <div class="bg-white px-6 py-3 rounded-2xl border border-slate-200 shadow-sm flex flex-col items-center">
                <span class="text-2xl font-black text-slate-900"><?php echo count($tags); ?></span>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Tags</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Form Section -->
        <div class="lg:col-span-4">
            <div class="bg-white rounded-[2rem] border border-slate-200 shadow-xl shadow-slate-200/40 overflow-hidden sticky top-8">
                <div class="bg-slate-50/50 px-8 py-6 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-widest">
                        <?php echo $edit_tag ? 'Edit Tag' : 'Create New'; ?>
                    </h3>
                    <?php if ($edit_tag): ?>
                        <div class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></div>
                    <?php endif; ?>
                </div>
                
                <form action="tags.php" method="POST" class="p-8 space-y-6">
                    <?php echo csrf_input(); ?>
                    <?php if ($edit_tag): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_tag['id']; ?>">
                    <?php endif; ?>
                    
                    <?php if (isset($errors['form'])): ?>
                        <div class="p-4 rounded-xl bg-red-50 border border-red-100 text-red-700 text-xs font-bold flex items-start">
                            <svg class="w-4 h-4 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <?php echo esc($errors['form']); ?>
                        </div>
                    <?php endif; ?>

                    <div class="space-y-2">
                        <label for="name" class="block text-xs font-bold text-slate-500 uppercase tracking-widest px-1">Tag Name</label>
                        <input type="text" name="name" id="name" required autofocus
                            value="<?php echo esc($_POST['name'] ?? $edit_tag['name'] ?? ''); ?>" 
                            class="block w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 focus:bg-white transition-all text-slate-900 font-bold placeholder-slate-300 <?php echo isset($errors['name']) ? 'border-red-300 ring-red-500/5 bg-red-50/30' : ''; ?>" 
                            placeholder="e.g. brainstorming">
                        <?php if (isset($errors['name'])): ?>
                            <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><?php echo esc($errors['name']); ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="flex flex-col gap-3">
                        <button type="submit" class="w-full py-4 bg-primary-600 text-white font-bold rounded-2xl hover:bg-primary-700 shadow-lg shadow-primary-600/20 transition-all transform active:scale-[0.98]">
                            <?php echo $edit_tag ? 'Update Tag' : 'Create Tag'; ?>
                        </button>
                        
                        <?php if ($edit_tag): ?>
                            <a href="tags.php" class="w-full py-4 bg-white border border-slate-200 text-slate-500 font-bold rounded-2xl hover:bg-slate-50 hover:text-slate-700 text-center transition-all text-xs uppercase tracking-widest">
                                Discard Changes
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- List Section -->
        <div class="lg:col-span-8 space-y-6">
            <!-- Search Bar -->
            <div class="bg-white rounded-3xl border border-slate-200 p-4 shadow-sm flex items-center gap-4">
                <div class="relative flex-grow">
                    <input type="text" id="tagSearch" placeholder="Search tags..." 
                        class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-medium focus:ring-4 focus:ring-primary-500/5 focus:border-primary-400 transition-all">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Tags Grid -->
            <div id="tagGrid" class="bg-white rounded-[2.5rem] border border-slate-200 p-8 min-h-[400px]">
                <div class="flex flex-wrap gap-3">
                    <?php foreach ($tags as $tag): ?>
                        <div class="tag-card group relative flex items-center bg-slate-50 border border-slate-100 rounded-2xl px-5 py-3 hover:border-primary-400 hover:bg-white hover:shadow-xl hover:shadow-primary-900/5 transition-all" data-name="<?php echo strtolower(esc($tag['name'])); ?>">
                            <a href="index.php?tag_id=<?php echo $tag['id']; ?>" class="text-slate-800 font-bold text-sm mr-10 transition-colors group-hover:text-primary-600">#<?php echo esc($tag['name']); ?></a>
                            
                            <div class="absolute right-2 flex items-center space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="tags.php?edit=<?php echo $tag['id']; ?>" class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                <a href="tags.php?delete=<?php echo $tag['id']; ?>" onclick="return confirm('Delete this tag?');" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Empty State Search -->
                <div id="noResults" class="hidden py-16 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 mb-4">
                        <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-1">No tags found</h3>
                    <p class="text-slate-500 text-xs font-medium">Try a different search term.</p>
                </div>

                <?php if (empty($tags)): ?>
                    <div class="py-20 text-center">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-primary-50 text-primary-400 mb-6">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-2">Tag Your Prompts</h3>
                        <p class="text-slate-500 font-medium max-w-xs mx-auto mb-8">Create granular tags to link related prompts across different categories.</p>
                        <button onclick="document.getElementById('name').focus()" class="px-8 py-3.5 bg-primary-600 text-white font-bold rounded-2xl hover:bg-primary-700 transition-all shadow-xl shadow-primary-600/20">Add First Tag</button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('tagSearch');
    const cards = document.querySelectorAll('.tag-card');
    const noResults = document.getElementById('noResults');
    const tagGrid = document.querySelector('#tagGrid .flex-wrap');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            let visibleCount = 0;

            cards.forEach(card => {
                const name = card.dataset.name;
                if (name.includes(query)) {
                    card.style.display = 'flex';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            if (visibleCount === 0 && query !== '') {
                noResults.classList.remove('hidden');
                tagGrid.classList.add('hidden');
            } else {
                noResults.classList.add('hidden');
                tagGrid.classList.remove('hidden');
            }
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>
