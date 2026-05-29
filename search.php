<?php
require_once 'bootstrap.php';

include 'includes/header.php';
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-12 text-center">
        <h1 class="text-5xl font-extrabold text-slate-900 tracking-tight mb-4">Search Vault</h1>
        <p class="text-slate-500 text-xl">Find exactly what you need in seconds.</p>
    </div>

    <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200 overflow-hidden mb-12">
        <div class="p-8 md:p-12">
            <form action="index.php" method="GET" class="space-y-8">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none">
                        <svg class="h-8 w-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="search" id="search" autofocus 
                        class="block w-full pl-16 pr-6 py-6 bg-slate-50 border-transparent rounded-2xl focus:ring-primary-500 focus:bg-white text-2xl font-medium text-slate-900 transition-all placeholder-slate-300" 
                        placeholder="Keyword, title, or content fragment...">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="category_id" class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Filter by Category</label>
                        <select name="category_id" id="category_id" class="block w-full px-5 py-4 bg-slate-50 border-slate-100 rounded-xl focus:ring-primary-500 focus:border-primary-500 text-base font-semibold transition-all">
                            <option value="">Any Category</option>
                            <?php foreach (get_categories() as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>"><?php echo esc($cat['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="tag_id" class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Filter by Tag</label>
                        <select name="tag_id" id="tag_id" class="block w-full px-5 py-4 bg-slate-50 border-slate-100 rounded-xl focus:ring-primary-500 focus:border-primary-500 text-base font-semibold transition-all">
                            <option value="">Any Tag</option>
                            <?php foreach (get_tags() as $tag): ?>
                                <option value="<?php echo $tag['id']; ?>"><?php echo esc($tag['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full flex justify-center py-5 px-4 border border-transparent rounded-2xl shadow-lg shadow-primary-600/20 text-xl font-bold text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all transform hover:-translate-y-1">
                        Search Your Prompts
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="text-center">
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-6">Popular Shortcuts</h3>
        <div class="flex flex-wrap justify-center gap-3">
            <a href="index.php" class="px-6 py-3 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-600 hover:border-primary-300 hover:text-primary-600 transition-all shadow-sm">
                Browse All
            </a>
            <?php foreach (array_slice(get_categories(), 0, 5) as $cat): ?>
                <a href="index.php?category_id=<?php echo $cat['id']; ?>" class="px-6 py-3 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-600 hover:border-primary-300 hover:text-primary-600 transition-all shadow-sm">
                    <?php echo esc($cat['name']); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
