<?php
require_once 'bootstrap.php';

// Requires login
if (!is_logged_in()) {
    redirect('login.php');
}

$filters = [
    'search' => $_GET['search'] ?? null,
];

// Fetch saved prompts
$prompts = get_saved_prompts($filters);

$page_title = "My Favorites";
$breadcrumbs = [
    ['name' => 'Library', 'url' => 'dashboard.php'],
    ['name' => 'Favorites', 'url' => 'favorites.php']
];

include 'includes/header.php';
?>

<div class="max-w-7xl mx-auto">
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">My <span class="text-primary-600">Favorites</span></h1>
            <p class="text-slate-500 text-sm">Your curated collection of high-performance AI prompts.</p>
        </div>
        
        <form action="favorites.php" method="GET" class="relative group">
            <input type="text" name="search" value="<?php echo esc($filters['search']); ?>" 
                class="h-10 w-full md:w-64 pl-10 pr-4 bg-white border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all text-xs font-medium" 
                placeholder="Search favorites...">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <svg class="h-4 w-4 text-slate-400 group-focus-within:text-primary-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </form>
    </div>

    <?php if (empty($prompts)): ?>
        <div class="py-24 text-center bg-white rounded-[3rem] border border-slate-200 border-dashed">
            <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-primary-50 text-primary-400 mb-6">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
            </div>
            <h3 class="text-2xl font-black text-slate-900 mb-2">Your collection is empty</h3>
            <p class="text-slate-500 font-medium max-w-xs mx-auto mb-8 text-balance">Save prompts from the public hub to build your own curated intelligence library.</p>
            <a href="public_prompts.php" class="px-8 py-4 bg-primary-600 text-white font-bold rounded-2xl hover:bg-primary-700 transition-all shadow-xl shadow-primary-600/20">Explore Community Prompts</a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php foreach ($prompts as $prompt): ?>
                <?php include 'includes/prompt_card.php'; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
