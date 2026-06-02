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
    ['name' => 'Home', 'url' => APP_URL_BASE . '/public_prompts.php'],
    ['name' => 'Community', 'url' => APP_URL_BASE . '/public_prompts.php'],
    ['name' => $user['username'], 'url' => APP_URL_BASE . "/u/{$user['slug']}"]
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
                <a href="<?php echo APP_URL_BASE; ?>/public_prompts.php" class="text-slate-400 hover:text-primary-600 transition-colors">Home</a>
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
                    <div class="flex flex-wrap items-center gap-4 text-slate-400 text-xs font-bold uppercase tracking-widest mb-4">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1.5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            Joined <?php echo date('M Y', strtotime($user['created_at'])); ?>
                        </span>
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            <?php echo $total_prompts; ?> Public Prompts
                        </span>
                    </div>

                    <?php if (!empty($user['bio'])): ?>
                        <p class="text-slate-600 text-sm leading-relaxed max-w-2xl mb-6"><?php echo nl2br(esc($user['bio'])); ?></p>
                    <?php endif; ?>

                    <div class="flex flex-wrap items-center gap-3">
                        <?php if (!empty($user['twitter_handle'])): ?>
                            <a href="https://twitter.com/<?php echo esc($user['twitter_handle']); ?>" target="_blank" class="flex items-center px-3 py-1.5 bg-slate-50 border border-slate-100 rounded-lg text-slate-500 hover:text-[#1DA1F2] hover:bg-[#1DA1F2]/5 transition-all text-[10px] font-bold uppercase tracking-widest">
                                <svg class="w-3.5 h-3.5 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.84 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                                Twitter
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($user['github_handle'])): ?>
                            <a href="https://github.com/<?php echo esc($user['github_handle']); ?>" target="_blank" class="flex items-center px-3 py-1.5 bg-slate-50 border border-slate-100 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition-all text-[10px] font-bold uppercase tracking-widest">
                                <svg class="w-3.5 h-3.5 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 4.238 9.611 9.647 11.408.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.3 1.98-.45 3-.45s2.04.15 3 .45c2.295-1.552 3.3-1.23 3.3-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.242 21.61 24 17.303 24 12c0-6.627-5.373-12-12-12z"/></svg>
                                GitHub
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($user['website_url'])): ?>
                            <a href="<?php echo esc($user['website_url']); ?>" target="_blank" class="flex items-center px-3 py-1.5 bg-slate-50 border border-slate-100 rounded-lg text-slate-500 hover:text-primary-600 hover:bg-primary-50 transition-all text-[10px] font-bold uppercase tracking-widest">
                                <svg class="w-3.5 h-3.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                                Website
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <?php if (is_logged_in() && get_current_user_id() !== $user['id']): ?>
                    <button onclick="toggleFollow(<?php echo $user['id']; ?>, this)" 
                        class="px-6 py-3 font-bold rounded-xl transition-all shadow-lg shadow-slate-900/10 text-xs <?php echo is_following($user['id']) ? 'bg-slate-100 text-slate-700' : 'bg-slate-900 text-white hover:bg-primary-600'; ?>">
                        <?php echo is_following($user['id']) ? 'Following' : 'Follow Author'; ?>
                    </button>
                <?php elseif (!is_logged_in()): ?>
                    <a href="<?php echo APP_URL_BASE; ?>/login.php" class="px-6 py-3 bg-slate-900 text-white text-xs font-bold rounded-xl hover:bg-primary-600 transition-all shadow-lg shadow-slate-900/10 text-center">Follow Author</a>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Abstract Deco -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-primary-50 rounded-full blur-3xl -z-0 opacity-40 translate-x-20 -translate-y-20"></div>
    </div>

    <script>
        function toggleFollow(authorId, btnElement) {
            const formData = new FormData();
            formData.append('following_id', authorId);
            
            // Add CSRF token for security
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (csrfToken) {
                formData.append('csrf_token', csrfToken);
            }

            fetch('<?php echo APP_URL_BASE; ?>/ajax_toggle_follow.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.state === 'followed') {
                        btnElement.classList.remove('bg-slate-900', 'text-white', 'hover:bg-primary-600');
                        btnElement.classList.add('bg-slate-100', 'text-slate-700');
                        btnElement.innerText = 'Following';
                        showToast('Now following <?php echo esc($user['username']); ?>');
                    } else {
                        btnElement.classList.add('bg-slate-900', 'text-white', 'hover:bg-primary-600');
                        btnElement.classList.remove('bg-slate-100', 'text-slate-700');
                        btnElement.innerText = 'Follow Author';
                        showToast('Unfollowed <?php echo esc($user['username']); ?>');
                    }
                    
                    // Update follower count in the UI if we add a counter later
                } else if (data.error === 'auth_required') {
                    window.location.href = '<?php echo APP_URL_BASE; ?>/login.php';
                }
            })
            .catch(err => console.error('Follow error:', err));
        }
    </script>

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
                            <a href="<?php echo APP_URL_BASE; ?>/u/<?php echo $user['slug']; ?>?page=<?php echo $page - 1; ?>" class="px-5 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-600 font-bold text-[10px] uppercase tracking-wider hover:bg-slate-50 transition-all">Previous</a>
                        <?php endif; ?>
                        
                        <span class="px-4 py-2 text-slate-400 font-bold text-[10px] uppercase tracking-wider">Page <?php echo $page; ?> of <?php echo $total_pages; ?></span>

                        <?php if ($page < $total_pages): ?>
                            <a href="<?php echo APP_URL_BASE; ?>/u/<?php echo $user['slug']; ?>?page=<?php echo $page + 1; ?>" class="px-5 py-2.5 rounded-xl bg-slate-900 text-white font-bold text-[10px] uppercase tracking-wider hover:bg-primary-600 transition-all shadow-lg shadow-slate-900/20">Next Page</a>
                        <?php endif; ?>
                    </nav>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
