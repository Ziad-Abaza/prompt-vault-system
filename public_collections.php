<?php
require_once 'bootstrap.php';

// Fetch all collections that contain at least one public prompt
$sql = "SELECT c.*, 
        (SELECT COUNT(*) FROM prompt_collections pc JOIN prompts p ON pc.prompt_id = p.id WHERE pc.collection_id = c.id AND p.is_public = 1) as public_prompt_count,
        u.username as author_name, u.slug as author_slug
        FROM collections c
        LEFT JOIN users u ON c.user_id = u.id
        WHERE (SELECT COUNT(*) FROM prompt_collections pc JOIN prompts p ON pc.prompt_id = p.id WHERE pc.collection_id = c.id AND p.is_public = 1) > 0
        ORDER BY public_prompt_count DESC";

$collections = query($sql)->fetchAll();

$page_title = "Browse Prompt Collections";
$meta_description = "Explore curated AI prompt collections for professional workflows. Discover packs for SEO, Coding, Marketing, and more created by the community.";
$canonical_url = APP_URL_BASE . '/public_collections.php';

$breadcrumbs = [
    ['name' => 'Home', 'url' => APP_URL_BASE . '/public_prompts.php'],
    ['name' => 'Collections', 'url' => APP_URL_BASE . '/public_collections.php']
];

include 'includes/header.php';
?>

<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-12">
        <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight mb-4">Curated <span class="text-primary-600">Collections</span></h1>
        <p class="text-slate-600 text-lg max-w-3xl leading-relaxed">
            Discover hand-picked sets of high-performance prompts. These collections are designed to help you master specific domains, from complex software engineering to high-converting copywriting.
        </p>
    </div>

    <!-- Collections Grid -->
    <?php if (empty($collections)): ?>
        <div class="py-24 text-center bg-white rounded-[3rem] border border-slate-200 border-dashed">
            <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-slate-50 text-slate-300 mb-6">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
            <h3 class="text-2xl font-black text-slate-900 mb-2">No public collections found</h3>
            <p class="text-slate-500 font-medium max-w-xs mx-auto">We're still curating our first community packs. Check back soon!</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($collections as $coll): ?>
                <div class="group bg-white rounded-[2.5rem] border border-slate-200 hover:border-primary-400 hover:shadow-2xl hover:shadow-primary-900/5 transition-all duration-300 flex flex-col h-full overflow-hidden">
                    <div class="p-8 flex flex-col h-full">
                        <div class="flex justify-between items-start mb-6">
                            <div class="w-14 h-14 bg-primary-50 text-primary-600 group-hover:bg-primary-600 group-hover:text-white rounded-2xl flex items-center justify-center transition-colors duration-300">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            </div>
                            <span class="px-4 py-1.5 bg-slate-50 text-slate-400 rounded-full text-[10px] font-black uppercase tracking-widest border border-slate-100"><?php echo $coll['public_prompt_count']; ?> Prompts</span>
                        </div>
                        
                        <div class="flex-grow">
                            <a href="<?php echo APP_URL_BASE; ?>/collections/<?php echo $coll['slug']; ?>" class="block mb-3">
                                <h3 class="text-2xl font-black text-slate-900 group-hover:text-primary-600 transition-colors leading-tight"><?php echo esc($coll['name']); ?></h3>
                            </a>
                            <p class="text-slate-500 text-sm leading-relaxed line-clamp-3 mb-6">
                                <?php echo esc($coll['description'] ?: 'A specialized collection of professional AI prompts curated for peak productivity.'); ?>
                            </p>
                        </div>

                        <div class="mt-auto pt-6 border-t border-slate-50 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-5 h-5 rounded-full bg-slate-100 flex items-center justify-center text-[8px] font-black text-slate-400">
                                    <?php echo strtoupper(substr($coll['author_name'] ?? 'A', 0, 1)); ?>
                                </div>
                                <a href="<?php echo APP_URL_BASE; ?>/u/<?php echo $coll['author_slug']; ?>" class="text-[10px] font-bold text-slate-400 hover:text-primary-600 transition-colors uppercase tracking-widest">
                                    <?php echo esc($coll['author_name'] ?? 'Atlas User'); ?>
                                </a>
                            </div>
                            <a href="<?php echo APP_URL_BASE; ?>/collections/<?php echo $coll['slug']; ?>" class="text-[10px] font-black text-primary-600 uppercase tracking-widest flex items-center hover:text-primary-700 transition-colors">
                                View Pack
                                <svg class="w-3 h-3 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
