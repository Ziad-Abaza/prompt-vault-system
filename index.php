<?php
require_once 'bootstrap.php';

/**
 * Public Landing Page
 * Performance-optimized queries for discovery sections.
 */

// 1. Trending Prompts (Weighted impact score)
$trending_prompts = query("SELECT p.*, c.name as category_name, c.slug as category_slug, u.username as author_name, u.slug as author_slug,
                           (SELECT COUNT(*) FROM user_saved_prompts usp WHERE usp.prompt_id = p.id) as save_count 
                           FROM prompts p 
                           LEFT JOIN categories c ON p.category_id = c.id
                           LEFT JOIN users u ON p.user_id = u.id
                           WHERE p.is_public = 1 
                           ORDER BY (p.view_count + p.copy_count * 5 + (SELECT COUNT(*) FROM user_saved_prompts usp WHERE usp.prompt_id = p.id) * 10) DESC LIMIT 4")->fetchAll();

// 2. Most Used Prompts (Views + Copies)
$popular_prompts = query("SELECT p.*, c.name as category_name, c.slug as category_slug, u.username as author_name, u.slug as author_slug
                          FROM prompts p 
                          LEFT JOIN categories c ON p.category_id = c.id
                          LEFT JOIN users u ON p.user_id = u.id
                          WHERE p.is_public = 1 
                          ORDER BY (p.view_count + p.copy_count) DESC LIMIT 4")->fetchAll();

// 3. Featured Collections
$featured_collections = query("SELECT c.*, (SELECT COUNT(*) FROM prompt_collections pc WHERE pc.collection_id = c.id) as prompt_count 
                               FROM collections c 
                               WHERE (SELECT COUNT(*) FROM prompt_collections pc JOIN prompts p ON pc.prompt_id = p.id WHERE pc.collection_id = c.id AND p.is_public = 1) > 0
                               ORDER BY prompt_count DESC LIMIT 3")->fetchAll();

// 4. Top Contributors
$top_authors = get_top_authors(5);

$page_title = "Master Your Prompt Engineering";
$meta_description = "Atlas Library is the professional workspace to organize, discover, and share AI prompts. Build your private vault or explore our community hub.";
$canonical_url = rtrim(Env::get('APP_URL', ''), '/') . '/';

include 'includes/header.php';
?>

<div class="max-w-6xl mx-auto">
    <!-- Hero Section -->
    <div class="relative py-20 md:py-32 overflow-hidden">
        <div class="relative z-10 text-center">
            <h1 class="text-5xl md:text-7xl font-black text-slate-900 tracking-tight mb-8 leading-[1.1]">
                Your Intelligence, <br>
                <span class="text-primary-600">Perfectly Organized.</span>
            </h1>
            <p class="text-xl text-slate-500 font-medium max-w-2xl mx-auto mb-12 leading-relaxed">
                Atlas Library is the ultimate workspace for prompt engineers. Save, categorize, and discover high-performance prompts for ChatGPT, Claude, and Midjourney.
            </p>
            
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <?php if (is_logged_in()): ?>
                    <a href="dashboard.php" class="btn-primary px-12 py-5 text-lg">Go to My Dashboard</a>
                <?php else: ?>
                    <a href="register.php" class="btn-primary px-12 py-5 text-lg">Start Your Private Vault</a>
                    <a href="public_prompts.php" class="btn-secondary px-12 py-5 text-lg">Explore Community Hub</a>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Abstract Background Deco -->
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-primary-50 rounded-full blur-3xl -z-0 opacity-50"></div>
    </div>

    <!-- Feature Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-32">
        <div class="bg-white p-10 rounded-[2.5rem] border border-slate-100 shadow-sm">
            <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center mb-8">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-slate-900 mb-4">Structured Library</h3>
            <p class="text-slate-500 leading-relaxed">Turn chaotic chat histories into a structured knowledge base with advanced categories and tags.</p>
        </div>
        <div class="bg-white p-10 rounded-[2.5rem] border border-slate-100 shadow-sm">
            <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mb-8">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-slate-900 mb-4">Private & Secure</h3>
            <p class="text-slate-500 leading-relaxed">Your prompts are your intellectual property. Atlas ensures they remain private and under your control.</p>
        </div>
        <div class="bg-white p-10 rounded-[2.5rem] border border-slate-100 shadow-sm">
            <div class="w-14 h-14 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center mb-8">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-slate-900 mb-4">Community Sharing</h3>
            <p class="text-slate-500 leading-relaxed">Contribute to the public ecosystem or discover curated prompt packs from the world's best engineers.</p>
        </div>
    </div>

    <!-- Trending Prompts Section -->
    <div class="mb-32">
        <div class="flex items-end justify-between mb-10">
            <div>
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-2">Trending <span class="text-primary-600">Now</span></h2>
                <p class="text-slate-500 font-medium">The most effective prompts being used by the community today.</p>
            </div>
            <a href="public_prompts.php?sort=trending" class="text-sm font-bold text-primary-600 hover:text-primary-700 uppercase tracking-widest flex items-center transition-all group">
                View Trending
                <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($trending_prompts as $prompt): ?>
                <?php include 'includes/prompt_card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Most Popular Section -->
    <div class="mb-32">
        <div class="flex items-end justify-between mb-10">
            <div>
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-2">Most <span class="text-primary-600">Popular</span></h2>
                <p class="text-slate-500 font-medium">The all-time community favorites with the highest copy counts.</p>
            </div>
            <a href="public_prompts.php?sort=popular" class="text-sm font-bold text-primary-600 hover:text-primary-700 uppercase tracking-widest flex items-center transition-all group">
                View Popular
                <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php if (isset($popular_prompts)): ?>
                <?php foreach ($popular_prompts as $prompt): ?>
                    <?php include 'includes/prompt_card.php'; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Featured Collections -->
    <div class="mb-32">
        <div class="flex items-end justify-between mb-10">
            <div>
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-2">Featured <span class="text-primary-600">Collections</span></h2>
                <p class="text-slate-500 font-medium">Curated packs designed for specific professional workflows.</p>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php foreach ($featured_collections as $coll): ?>
                <a href="collections/<?php echo $coll['slug']; ?>" class="group block bg-white p-8 rounded-[2.5rem] border border-slate-200 hover:border-primary-400 hover:shadow-2xl hover:shadow-primary-900/5 transition-all">
                    <div class="flex justify-between items-start mb-6">
                        <div class="w-12 h-12 bg-slate-50 text-slate-400 group-hover:bg-primary-600 group-hover:text-white rounded-2xl flex items-center justify-center transition-colors duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                        <span class="px-3 py-1 bg-slate-50 text-slate-400 rounded-full text-[10px] font-bold uppercase tracking-widest"><?php echo $coll['prompt_count']; ?> Prompts</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3 group-hover:text-primary-600 transition-colors"><?php echo esc($coll['name']); ?></h3>
                    <p class="text-slate-500 text-sm line-clamp-2 leading-relaxed">
                        <?php echo esc($coll['description'] ?: 'A specialized collection of professional AI prompts.'); ?>
                    </p>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Top Contributors -->
    <div class="mb-32">
        <div class="flex items-end justify-between mb-10">
            <div>
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-2">Top <span class="text-primary-600">Contributors</span></h2>
                <p class="text-slate-500 font-medium">The engineers driving the community forward with high-impact prompts.</p>
            </div>
            <a href="leaderboards.php" class="text-sm font-bold text-primary-600 hover:text-primary-700 uppercase tracking-widest flex items-center transition-all group">
                View Leaderboards
                <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
            <?php foreach ($top_authors as $index => $author): ?>
                <a href="u/<?php echo $author['slug']; ?>" class="group bg-white p-6 rounded-[2rem] border border-slate-200 hover:border-primary-400 hover:shadow-xl hover:shadow-primary-900/5 transition-all duration-300 text-center relative overflow-hidden">
                    <!-- Rank Badge -->
                    <div class="absolute top-4 left-4 w-6 h-6 rounded-lg bg-slate-50 text-slate-400 group-hover:bg-primary-600 group-hover:text-white flex items-center justify-center text-[10px] font-black transition-colors">
                        #<?php echo $index + 1; ?>
                    </div>
                    
                    <div class="w-16 h-16 rounded-[1.5rem] bg-primary-50 text-primary-600 flex items-center justify-center text-2xl font-black mx-auto mb-4 group-hover:scale-110 transition-transform duration-300 shadow-sm">
                        <?php echo strtoupper(substr($author['username'], 0, 1)); ?>
                    </div>
                    
                    <h3 class="font-bold text-slate-900 group-hover:text-primary-600 transition-colors mb-1 truncate px-2"><?php echo esc($author['username']); ?></h3>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4"><?php echo number_format($author['public_prompt_count']); ?> Public Prompts</p>
                    
                    <div class="flex items-center justify-center gap-4 py-3 border-t border-slate-50">
                        <div class="text-center">
                            <span class="block text-xs font-black text-slate-900"><?php echo number_format($author['total_views']); ?></span>
                            <span class="block text-[8px] font-bold text-slate-400 uppercase tracking-tighter">Views</span>
                        </div>
                        <div class="text-center">
                            <span class="block text-xs font-black text-primary-600"><?php echo number_format($author['total_copies']); ?></span>
                            <span class="block text-[8px] font-bold text-slate-400 uppercase tracking-tighter">Copies</span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Final CTA -->
    <div class="bg-slate-900 rounded-[3rem] p-12 md:p-20 text-center text-white relative overflow-hidden mb-20">
        <div class="relative z-10">
            <h2 class="text-3xl md:text-5xl font-black mb-8">Ready to Build Your <br>Prompt Intelligence?</h2>
            <p class="text-slate-400 text-lg max-w-xl mx-auto mb-12">
                Join thousands of prompt engineers who are organizing their workflow with Atlas Library.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="register.php" class="btn-primary border-none px-12 py-5 text-lg">Create Free Account</a>
                <a href="login.php" class="px-12 py-5 text-lg font-bold text-white hover:text-primary-400 transition-colors">Sign In to Vault</a>
            </div>
        </div>
        <div class="absolute -bottom-20 -right-20 w-96 h-96 bg-primary-600/20 rounded-full blur-3xl"></div>
        <div class="absolute -top-20 -left-20 w-96 h-96 bg-indigo-600/10 rounded-full blur-3xl"></div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
