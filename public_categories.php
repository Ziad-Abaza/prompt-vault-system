<?php
require_once 'bootstrap.php';

// Fetch all categories that contain at least one public prompt
$sql = "SELECT c.*, 
        (SELECT COUNT(*) FROM prompts p WHERE p.category_id = c.id AND p.is_public = 1) as public_prompt_count
        FROM categories c
        WHERE (SELECT COUNT(*) FROM prompts p WHERE p.category_id = c.id AND p.is_public = 1) > 0
        ORDER BY public_prompt_count DESC";

$categories = query($sql)->fetchAll();

$page_title = "Browse Prompt Categories";
$meta_description = "Explore AI prompts by category. Find the best templates for Coding, Marketing, Image Generation, and more in our community library.";
$canonical_url = APP_URL_BASE . '/public_categories.php';

$breadcrumbs = [
    ['name' => 'Home', 'url' => APP_URL_BASE . '/public_prompts.php'],
    ['name' => 'Categories', 'url' => APP_URL_BASE . '/public_categories.php']
];

include 'includes/header.php';
?>

<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-12">
        <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight mb-4">Prompt <span class="text-primary-600">Categories</span></h1>
        <p class="text-slate-600 text-lg max-w-3xl leading-relaxed">
            Navigate our library by professional domain. Whether you're looking for engineering patterns or creative inspiration, our structured categories help you find the right intelligence for your project.
        </p>
    </div>

    <!-- Categories Grid -->
    <?php if (empty($categories)): ?>
        <div class="py-24 text-center bg-white rounded-[3rem] border border-slate-200 border-dashed">
            <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-slate-50 text-slate-300 mb-6">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
            </div>
            <h3 class="text-2xl font-black text-slate-900 mb-2">No active categories found</h3>
            <p class="text-slate-500 font-medium max-w-xs mx-auto">We're still organizing our public library. Check back soon!</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($categories as $cat): ?>
                <a href="<?php echo APP_URL_BASE; ?>/prompts/<?php echo $cat['slug']; ?>" class="group bg-white p-8 rounded-[2.5rem] border border-slate-200 hover:border-primary-400 hover:shadow-2xl hover:shadow-primary-900/5 transition-all duration-300 flex flex-col items-center text-center">
                    <div class="w-16 h-16 bg-primary-50 text-primary-600 group-hover:bg-primary-600 group-hover:text-white rounded-[1.5rem] flex items-center justify-center mb-6 transition-all duration-300 transform group-hover:scale-110 group-hover:rotate-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                    </div>
                    
                    <h3 class="text-xl font-black text-slate-900 group-hover:text-primary-600 transition-colors mb-2"><?php echo esc($cat['name']); ?></h3>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]"><?php echo $cat['public_prompt_count']; ?> Prompts</p>
                    
                    <div class="mt-6 flex items-center text-xs font-bold text-primary-600 opacity-0 group-hover:opacity-100 transition-all transform translate-y-2 group-hover:translate-y-0">
                        Explore Category
                        <svg class="w-3.5 h-3.5 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
