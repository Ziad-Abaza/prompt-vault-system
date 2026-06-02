<?php
require_once 'bootstrap.php';

$slug = $_GET['user'] ?? null;
if (!$slug) {
    redirect('public_prompts.php');
}

$user = query("SELECT * FROM users WHERE slug = ?", [$slug])->fetch();
if (!$user) {
    abort(404);
}

// Pagination setup
$per_page = 24;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $per_page;

// Query public prompts for this user
$sql = "SELECT p.*, c.name as category_name, c.slug as category_slug, u.username as author_name 
        FROM prompts p 
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN users u ON p.user_id = u.id
        WHERE p.is_public = 1 AND p.user_id = ? 
        ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
$prompts = query($sql, [$user['id'], $per_page, $offset])->fetchAll();

// Total count for pagination and meta
$total_prompts = query("SELECT COUNT(*) FROM prompts WHERE is_public = 1 AND user_id = ?", [$user['id']])->fetchColumn();
$total_pages = ceil($total_prompts / $per_page);

$page_title = "{$user['username']} — AI Prompt Engineer Profile";
$meta_description = "Explore the public AI prompt library of {$user['username']}. Discover {$total_prompts} high-quality prompts and creative templates shared by this contributor.";
$canonical_url = rtrim(Env::get('APP_URL', ''), '/') . '/u/' . $user['slug'];

// Breadcrumbs
$breadcrumbs = [
    ['name' => 'Home', 'url' => 'public_prompts.php'],
    ['name' => 'Community', 'url' => 'public_prompts.php'],
    ['name' => $user['username'], 'url' => "u/{$user['slug']}"]
];

$page_schema = [
    "@type" => "ProfilePage",
    "mainEntity" => [
        "@type" => "Person",
        "name" => $user['username'],
        "identifier" => $user['slug'],
        "interactionStatistic" => [
            "@type" => "InteractionCounter",
            "interactionType" => "https://schema.org/WriteAction",
            "userInteractionCount" => $total_prompts
        ]
    ]
];

include 'includes/header.php';
?>

<div class="max-w-7xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2 text-[10px] font-bold uppercase tracking-widest">
            <li>
                <a href="public_prompts.php" class="text-slate-400 hover:text-primary-600 transition-colors">Home</a>
            </li>
            <li>
                <svg class="h-3 w-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
                </svg>
            </li>
            <li class="text-slate-900"><?php echo esc($user['username']); ?></li>
        </ol>
    </nav>

    <!-- Profile Header -->
    <div class="bg-white rounded-[3rem] border border-slate-200 p-8 md:p-12 mb-12 shadow-sm overflow-hidden relative">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-8">
            <div class="flex items-center">
                <div class="w-20 h-20 md:w-24 md:h-24 rounded-[2rem] bg-primary-600 text-white flex items-center justify-center text-3xl font-black shadow-xl shadow-primary-600/20 mr-6 md:mr-8">
                    <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                </div>
                <div>
                    <h1 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight mb-2"><?php echo esc($user['username']); ?></h1>
                    <div class="flex flex-wrap items-center gap-4 text-slate-400 text-xs font-bold uppercase tracking-widest">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1.5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            Joined <?php echo date('M Y', strtotime($user['created_at'])); ?>
                        </span>
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            <?php echo $total_prompts; ?> Public Prompts
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <button class="px-6 py-3 bg-slate-900 text-white text-xs font-bold rounded-xl hover:bg-primary-600 transition-all shadow-lg shadow-slate-900/10">Follow Author</button>
            </div>
        </div>
        
        <!-- Abstract Deco -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-primary-50 rounded-full blur-3xl -z-0 opacity-40 translate-x-20 -translate-y-20"></div>
    </div>

    <!-- User's Public Feed -->
    <div class="mb-8">
        <h2 class="text-xl font-bold text-slate-900 mb-6">Public <span class="text-primary-600">Library</span></h2>
        
        <?php if (empty($prompts)): ?>
            <div class="py-20 text-center bg-white rounded-[2.5rem] border border-slate-100">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-slate-50 border border-slate-100 shadow-sm mb-4">
                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-1">No public prompts yet</h3>
                <p class="text-slate-500 text-sm font-medium">This author hasn't shared any prompts with the community yet.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                <?php foreach ($prompts as $prompt): ?>
                    <?php include 'includes/prompt_card.php'; ?>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="mt-12 mb-8 flex justify-center">
                    <nav class="flex items-center space-x-2">
                        <?php if ($page > 1): ?>
                            <a href="u/<?php echo $user['slug']; ?>?page=<?php echo $page - 1; ?>" class="px-5 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-600 font-bold text-[10px] uppercase tracking-wider hover:bg-slate-50 transition-all">Previous</a>
                        <?php endif; ?>
                        
                        <span class="px-4 py-2 text-slate-400 font-bold text-[10px] uppercase tracking-wider">Page <?php echo $page; ?> of <?php echo $total_pages; ?></span>

                        <?php if ($page < $total_pages): ?>
                            <a href="u/<?php echo $user['slug']; ?>?page=<?php echo $page + 1; ?>" class="px-5 py-2.5 rounded-xl bg-slate-900 text-white font-bold text-[10px] uppercase tracking-wider hover:bg-primary-600 transition-all shadow-lg shadow-slate-900/20">Next Page</a>
                        <?php endif; ?>
                    </nav>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
