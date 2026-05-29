<?php
require_once 'bootstrap.php';

include 'includes/header.php';
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-12 text-center">
        <h1 class="text-5xl font-extrabold text-slate-900 tracking-tight mb-4"><?php echo APP_NAME; ?> Search</h1>
        <p class="text-slate-500 text-xl font-medium">Find exactly what you need in seconds.</p>
    </div>

    <div class="form-section shadow-2xl shadow-slate-200/50">
        <div class="p-8 md:p-12">
            <form action="index.php" method="GET" class="space-y-10">
                <div class="form-group">
                    <label for="search" class="form-label px-1">Global Keyword Search</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none transition-colors group-focus-within:text-primary-500">
                            <svg class="h-8 w-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" name="search" id="search" autofocus 
                            class="form-input !pl-16 !py-6 !text-2xl !font-bold" 
                            placeholder="Type keywords, titles, or tags...">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-4 border-t border-slate-50">
                    <div class="form-group">
                        <label for="category_id" class="form-label px-1">Filter by Category</label>
                        <div class="relative">
                            <select name="category_id" id="category_id" class="form-select">
                                <option value="">Any Category</option>
                                <?php foreach (get_categories() as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>"><?php echo esc($cat['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="tag_id" class="form-label px-1">Filter by Tag</label>
                        <div class="relative">
                            <select name="tag_id" id="tag_id" class="form-select">
                                <option value="">Any Tag</option>
                                <?php foreach (get_tags() as $tag): ?>
                                    <option value="<?php echo $tag['id']; ?>"><?php echo esc($tag['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-6">
                    <button type="submit" class="btn-primary w-full py-5 text-xl flex items-center justify-center">
                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        Search Your Prompts
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="mt-16 text-center">
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mb-8">Popular Classification Shortcuts</h3>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="index.php" class="px-8 py-3 bg-white border border-slate-200 rounded-2xl text-sm font-bold text-slate-600 hover:border-primary-300 hover:text-primary-600 transition-all shadow-sm hover:shadow-lg hover:shadow-primary-900/5">
                Browse Entire Library
            </a>
            <?php foreach (array_slice(get_categories(), 0, 5) as $cat): ?>
                <a href="index.php?category_id=<?php echo $cat['id']; ?>" class="px-8 py-3 bg-white border border-slate-200 rounded-2xl text-sm font-bold text-slate-600 hover:border-primary-300 hover:text-primary-600 transition-all shadow-sm hover:shadow-lg hover:shadow-primary-900/5">
                    <?php echo esc($cat['name']); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
