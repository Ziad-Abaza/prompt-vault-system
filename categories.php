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
$edit_cat = null;
if (isset($_GET['edit'])) {
    $edit_cat = get_category($_GET['edit']);
    if (!$edit_cat) {
        abort(404);
    }
}

include 'includes/header.php';
?>

<div class="max-w-6xl mx-auto">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
        <div>
            <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight mb-2">Manage <span class="text-primary-600">Categories</span></h1>
            <p class="text-slate-500 font-medium max-w-md">High-level classification to keep your prompt library structured and easy to navigate.</p>
        </div>
        
        <!-- Quick Stats -->
        <div class="flex items-center gap-4">
            <div class="bg-white px-6 py-3 rounded-2xl border border-slate-200 shadow-sm flex flex-col items-center">
                <span class="text-2xl font-black text-slate-900"><?php echo count($categories); ?></span>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Categories</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Form Section -->
        <div class="lg:col-span-4">
            <div class="bg-white rounded-[2rem] border border-slate-200 shadow-xl shadow-slate-200/40 overflow-hidden sticky top-8">
                <div class="bg-slate-50/50 px-8 py-6 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-widest">
                        <?php echo $edit_cat ? 'Edit Category' : 'Create New'; ?>
                    </h3>
                    <?php if ($edit_cat): ?>
                        <div class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></div>
                    <?php endif; ?>
                </div>
                
                <form action="categories.php" method="POST" class="p-8 space-y-6">
                    <?php echo csrf_input(); ?>
                    <?php if ($edit_cat): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_cat['id']; ?>">
                    <?php endif; ?>
                    
                    <?php if (isset($errors['form'])): ?>
                        <div class="p-4 rounded-xl bg-red-50 border border-red-100 text-red-700 text-xs font-bold flex items-start">
                            <svg class="w-4 h-4 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <?php echo esc($errors['form']); ?>
                        </div>
                    <?php endif; ?>

                    <div class="space-y-2">
                        <label for="name" class="block text-xs font-bold text-slate-500 uppercase tracking-widest px-1">Display Name</label>
                        <input type="text" name="name" id="name" required autofocus
                            value="<?php echo esc($_POST['name'] ?? $edit_cat['name'] ?? ''); ?>" 
                            class="block w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 focus:bg-white transition-all text-slate-900 font-bold placeholder-slate-300 <?php echo isset($errors['name']) ? 'border-red-300 ring-red-500/5 bg-red-50/30' : ''; ?>" 
                            placeholder="e.g. Content Marketing">
                        <?php if (isset($errors['name'])): ?>
                            <p class="text-red-500 text-[10px] font-bold mt-1 px-1"><?php echo esc($errors['name']); ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="flex flex-col gap-3">
                        <button type="submit" class="w-full py-4 bg-primary-600 text-white font-bold rounded-2xl hover:bg-primary-700 shadow-lg shadow-primary-600/20 transition-all transform active:scale-[0.98]">
                            <?php echo $edit_cat ? 'Update Category' : 'Create Category'; ?>
                        </button>
                        
                        <?php if ($edit_cat): ?>
                            <a href="categories.php" class="w-full py-4 bg-white border border-slate-200 text-slate-500 font-bold rounded-2xl hover:bg-slate-50 hover:text-slate-700 text-center transition-all text-xs uppercase tracking-widest">
                                Discard Changes
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- List Section -->
        <div class="lg:col-span-8 space-y-6">
            <!-- Search & Filter Bar -->
            <div class="bg-white rounded-3xl border border-slate-200 p-4 shadow-sm flex items-center gap-4">
                <div class="relative flex-grow">
                    <input type="text" id="categorySearch" placeholder="Search categories..." 
                        class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-medium focus:ring-4 focus:ring-primary-500/5 focus:border-primary-400 transition-all">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Categories Grid -->
            <div id="categoryGrid" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php foreach ($categories as $cat): ?>
                    <div class="category-card group bg-white rounded-3xl border border-slate-200 p-6 hover:border-primary-300 hover:shadow-xl hover:shadow-primary-900/5 transition-all" data-name="<?php echo strtolower(esc($cat['name'])); ?>">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center min-w-0">
                                <div class="w-12 h-12 shrink-0 rounded-2xl bg-primary-50 text-primary-600 flex items-center justify-center mr-4 group-hover:bg-primary-600 group-hover:text-white transition-colors duration-300">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-bold text-slate-900 truncate"><?php echo esc($cat['name']); ?></h3>
                            </div>
                            
                            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="index.php?category_id=<?php echo $cat['id']; ?>" class="p-2 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-all" title="View Prompts">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                </a>
                                <a href="categories.php?edit=<?php echo $cat['id']; ?>" class="p-2 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-xl transition-all" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                <a href="categories.php?delete=<?php echo $cat['id']; ?>" onclick="return confirm('Delete this category? Prompts will become Uncategorized.');" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all" title="Delete">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Empty State -->
            <div id="noResults" class="hidden py-20 text-center bg-white rounded-[2.5rem] border border-slate-100 shadow-sm">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-slate-50 mb-4">
                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-1">No matches found</h3>
                <p class="text-slate-500 text-sm font-medium">We couldn't find any categories matching your search.</p>
            </div>

            <?php if (empty($categories)): ?>
                <div class="py-24 text-center bg-white rounded-[3rem] border border-slate-200 border-dashed">
                    <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-primary-50 text-primary-400 mb-6">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 mb-2">Start Organizing</h3>
                    <p class="text-slate-500 font-medium max-w-xs mx-auto mb-8 text-balance">Create your first category to start structuring your professional prompt library.</p>
                    <button onclick="document.getElementById('name').focus()" class="px-8 py-4 bg-primary-600 text-white font-bold rounded-2xl hover:bg-primary-700 transition-all shadow-xl shadow-primary-600/20">Add Category Now</button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('categorySearch');
    const cards = document.querySelectorAll('.category-card');
    const noResults = document.getElementById('noResults');
    const grid = document.getElementById('categoryGrid');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            let visibleCount = 0;

            cards.forEach(card => {
                const name = card.dataset.name;
                if (name.includes(query)) {
                    card.style.display = 'block';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            if (visibleCount === 0 && query !== '') {
                noResults.classList.remove('hidden');
                grid.classList.add('hidden');
            } else {
                noResults.classList.add('hidden');
                grid.classList.remove('hidden');
            }
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>
