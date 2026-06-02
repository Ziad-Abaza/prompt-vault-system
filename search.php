<?php
require_once 'bootstrap.php';

$query = trim($_GET['q'] ?? '');
$results = [];

if ($query !== '') {
    $filters = ['search' => $query];
    
    // If guest, search ONLY public prompts
    if (!is_logged_in()) {
        $filters['is_public'] = true;
    }
    
    $results = get_prompts($filters);
}

$page_title = $query ? "Search results for \"$query\"" : "Search Prompt Library";
$meta_description = $query ? "Discover prompts matching \"$query\" in our AI library. Copy and use high-quality community prompts." : "Search our comprehensive library of AI prompts for ChatGPT, Claude, and Midjourney.";
$canonical_url = APP_URL_BASE . '/search.php' . ($query ? '?q=' . urlencode($query) : '');

include 'includes/header.php';
?>

<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-10 text-center max-w-2xl mx-auto">
        <h1 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight mb-4">
            <?php echo $query ? 'Search <span class="text-primary-600">Results</span>' : 'Search <span class="text-primary-600">Library</span>'; ?>
        </h1>
        <p class="text-slate-500 font-medium">Find exactly what you need in our vast collection of prompt intelligence.</p>
    </div>

    <!-- Search Box -->
    <div class="mb-12">
        <form action="<?php echo APP_URL_BASE; ?>/search.php" method="GET" class="relative max-w-2xl mx-auto">
            <input type="text" name="q" value="<?php echo esc($query); ?>" autofocus
                class="block w-full px-12 py-5 bg-white border border-slate-200 rounded-[2rem] shadow-xl shadow-slate-200/40 focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all text-lg font-bold placeholder-slate-300" 
                placeholder="Search by keywords, tags, or categories...">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <button type="submit" class="absolute inset-y-2 right-2 px-6 bg-primary-600 text-white font-bold rounded-2xl hover:bg-primary-700 transition-all shadow-lg shadow-primary-600/20">Search</button>
        </form>
    </div>

    <!-- Results Section -->
    <?php if ($query !== ''): ?>
        <div class="mb-6 flex items-center justify-between">
            <h2 class="text-lg font-bold text-slate-900">Found <?php echo count($results); ?> results for "<?php echo esc($query); ?>"</h2>
            <?php if (!is_logged_in()): ?>
                <span class="px-3 py-1 bg-slate-100 text-slate-500 rounded-lg text-[10px] font-bold uppercase tracking-widest">Guest Mode: Public Prompts Only</span>
            <?php endif; ?>
        </div>

        <?php if (empty($results)): ?>
            <div class="py-20 text-center bg-white rounded-[3rem] border border-slate-100 shadow-sm">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-slate-50 mb-4">
                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 9.172a4 4 0 0112.728 0M9.172 9.172a4 4 0 0112.728 0M9.172 9.172a4 4 0 0112.728 0"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-1">No matching prompts</h3>
                <p class="text-slate-500 text-sm font-medium">Try broadening your search or use different keywords.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mb-12">
                <?php foreach ($results as $prompt): ?>
                    <?php include 'includes/prompt_card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <!-- Search Tips/Guide -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto mb-12">
            <div class="text-center">
                <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center mx-auto mb-4 font-black text-xs">01</div>
                <h4 class="font-bold text-slate-900 mb-2">Keywords</h4>
                <p class="text-xs text-slate-400 leading-relaxed">Search by prompt content or title keywords (e.g. "Python refactor")</p>
            </div>
            <div class="text-center">
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center mx-auto mb-4 font-black text-xs">02</div>
                <h4 class="font-bold text-slate-900 mb-2">Techniques</h4>
                <p class="text-xs text-slate-400 leading-relaxed">Find prompts for specific AI techniques like "Few-shot" or "Zero-shot"</p>
            </div>
            <div class="text-center">
                <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center mx-auto mb-4 font-black text-xs">03</div>
                <h4 class="font-bold text-slate-900 mb-2">Tool Names</h4>
                <p class="text-xs text-slate-400 leading-relaxed">Filter by intended model such as "ChatGPT", "Claude", or "Midjourney"</p>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
