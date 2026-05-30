<?php
require_once 'bootstrap.php';

// Public access allowed
$filters = [
    'is_public' => true,
    'category_id' => $_GET['category_id'] ?? null,
    'tag_id' => $_GET['tag_id'] ?? null,
    'search' => $_GET['search'] ?? null,
    'sort' => $_GET['sort'] ?? 'newest',
];

// Pagination
$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 12;
$filters['limit'] = $per_page;
$filters['offset'] = ($page - 1) * $per_page;

// Build SQL based on sort
$order_by = "p.created_at DESC";
if ($filters['sort'] === 'oldest') $order_by = "p.created_at ASC";
if ($filters['sort'] === 'popular') $order_by = "p.view_count DESC";
if ($filters['sort'] === 'alphabetical') $order_by = "p.title ASC";

// Get Prompts with sorting
$user_id = get_current_user_id();
$sql = "SELECT p.*, c.name as category_name, u.username as author_name 
        FROM prompts p 
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN users u ON p.user_id = u.id
        WHERE p.is_public = 1";
$params = [];

if (!empty($filters['category_id'])) {
    $sql .= " AND p.category_id = ?";
    $params[] = $filters['category_id'];
}

if (!empty($filters['tag_id'])) {
    $sql .= " AND p.id IN (SELECT prompt_id FROM prompt_tags WHERE tag_id = ?)";
    $params[] = $filters['tag_id'];
}

if (!empty($filters['search'])) {
    $sql .= " AND (p.title LIKE ? OR p.content LIKE ?)";
    $params[] = '%' . $filters['search'] . '%';
    $params[] = '%' . $filters['search'] . '%';
}

$sql .= " ORDER BY $order_by LIMIT ? OFFSET ?";
$params[] = $filters['limit'];
$params[] = $filters['offset'];

$prompts = query($sql, $params)->fetchAll();

// Get total count for pagination
$count_sql = "SELECT COUNT(*) FROM prompts WHERE is_public = 1";
$count_params = [];
if (!empty($filters['category_id'])) {
    $count_sql .= " AND category_id = ?";
    $count_params[] = $filters['category_id'];
}
if (!empty($filters['tag_id'])) {
    $count_sql .= " AND id IN (SELECT prompt_id FROM prompt_tags WHERE tag_id = ?)";
    $count_params[] = $filters['tag_id'];
}
if (!empty($filters['search'])) {
    $count_sql .= " AND (title LIKE ? OR content LIKE ?)";
    $count_params[] = '%' . $filters['search'] . '%';
    $count_params[] = '%' . $filters['search'] . '%';
}
$total_prompts = query($count_sql, $count_params)->fetchColumn();
$total_pages = ceil($total_prompts / $per_page);

// Stats for the hub
$total_public = query("SELECT COUNT(*) FROM prompts WHERE is_public = 1")->fetchColumn();
$total_views = query("SELECT SUM(view_count) FROM prompts WHERE is_public = 1")->fetchColumn() ?: 0;
$total_categories = query("SELECT COUNT(DISTINCT category_id) FROM prompts WHERE is_public = 1")->fetchColumn();

$categories = query("SELECT DISTINCT c.* FROM categories c JOIN prompts p ON c.id = p.category_id WHERE p.is_public = 1 ORDER BY c.name ASC")->fetchAll();
$all_tags = query("SELECT DISTINCT t.* FROM tags t JOIN prompt_tags pt ON t.id = pt.tag_id JOIN prompts p ON pt.prompt_id = p.id WHERE p.is_public = 1 ORDER BY t.name ASC LIMIT 20")->fetchAll();

$page_title = "Discover Public Prompts";
$meta_description = "Browse the world's best AI prompts. A curated marketplace of community-shared prompts for ChatGPT, Claude, Midjourney, and more.";

include 'includes/header.php';
?>

<div class="max-w-7xl mx-auto space-y-12">
    
    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-slate-900 rounded-[3rem] px-8 py-16 md:py-24 text-center">
        <!-- Abstract background elements -->
        <div class="absolute top-0 left-0 w-full h-full opacity-10 pointer-events-none">
            <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[60%] bg-primary-500 blur-[120px] rounded-full"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-[30%] h-[50%] bg-indigo-500 blur-[120px] rounded-full"></div>
        </div>
        
        <div class="relative z-10 max-w-3xl mx-auto">
            <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-black bg-primary-500/10 text-primary-400 uppercase tracking-[0.2em] mb-6 border border-primary-500/20">
                Community Driven
            </span>
            <h1 class="text-4xl md:text-6xl font-black text-white tracking-tight mb-6 leading-tight">
                The Prompt <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-400 to-indigo-400">Marketplace</span>
            </h1>
            <p class="text-lg md:text-xl text-slate-400 font-medium mb-10 leading-relaxed">
                Unlock the full potential of AI with thousands of curated prompts for any task. Browse, learn, and contribute to the library.
            </p>
            
            <form action="public_prompts.php" method="GET" class="relative max-w-2xl mx-auto group">
                <input type="text" name="search" value="<?php echo esc($filters['search']); ?>" 
                    class="w-full h-16 pl-14 pr-32 bg-white/10 border border-white/10 rounded-2xl text-white placeholder-slate-500 focus:ring-4 focus:ring-primary-500/20 focus:border-primary-500/50 focus:bg-white/20 transition-all text-lg font-medium" 
                    placeholder="Search for 'Creative Writer', 'Code Refactor', 'SEO Expert'...">
                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                    <svg class="h-6 w-6 text-slate-500 group-focus-within:text-primary-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <button type="submit" class="absolute right-2 top-2 bottom-2 px-6 bg-primary-600 hover:bg-primary-500 text-white font-bold rounded-xl transition-all shadow-lg shadow-primary-600/20">
                    Search
                </button>
            </form>
        </div>
    </section>

    <!-- Stats Bar -->
    <section class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex flex-col items-center justify-center text-center">
            <span class="text-3xl font-black text-slate-900 mb-1"><?php echo number_format($total_public); ?></span>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Prompts</span>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex flex-col items-center justify-center text-center">
            <span class="text-3xl font-black text-slate-900 mb-1"><?php echo number_format($total_views); ?>+</span>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Views</span>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex flex-col items-center justify-center text-center">
            <span class="text-3xl font-black text-slate-900 mb-1"><?php echo number_format($total_categories); ?></span>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Categories</span>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex flex-col items-center justify-center text-center">
            <span class="text-3xl font-black text-slate-900 mb-1">100%</span>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Open Source</span>
        </div>
    </section>

    <!-- Filter & Sort UI -->
    <section class="flex flex-col lg:flex-row gap-8 items-start">
        
        <!-- Sidebar Filters -->
        <aside class="w-full lg:w-72 shrink-0 space-y-8 sticky top-8">
            <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-6">Categories</h3>
                <nav class="space-y-1">
                    <a href="public_prompts.php" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-bold <?php echo empty($filters['category_id']) ? 'bg-primary-50 text-primary-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'; ?> transition-all">
                        <span>All Categories</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                    <?php foreach ($categories as $cat): ?>
                        <a href="public_prompts.php?category_id=<?php echo $cat['id']; ?>" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-bold <?php echo $filters['category_id'] == $cat['id'] ? 'bg-primary-50 text-primary-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'; ?> transition-all">
                            <span class="truncate"><?php echo esc($cat['name']); ?></span>
                            <svg class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    <?php endforeach; ?>
                </nav>
            </div>

            <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-6">Popular Tags</h3>
                <div class="flex flex-wrap gap-2">
                    <?php foreach ($all_tags as $tag): ?>
                        <a href="public_prompts.php?tag_id=<?php echo $tag['id']; ?>" class="px-3 py-1.5 rounded-xl text-[11px] font-black uppercase tracking-widest transition-all <?php echo $filters['tag_id'] == $tag['id'] ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'bg-slate-50 text-slate-500 border border-slate-100 hover:border-indigo-300 hover:text-indigo-600'; ?>">
                            #<?php echo esc($tag['name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php if (array_filter($filters)): ?>
                <a href="public_prompts.php" class="flex items-center justify-center w-full py-4 rounded-2xl border-2 border-dashed border-slate-200 text-slate-400 font-bold text-sm hover:border-red-300 hover:text-red-500 transition-all">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Clear All Filters
                </a>
            <?php endif; ?>
        </aside>

        <!-- Main Listing -->
        <div class="flex-grow space-y-8">
            
            <!-- Sort and Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-black text-slate-900">
                        <?php 
                        if ($filters['search']) echo 'Search results for "' . esc($filters['search']) . '"';
                        elseif ($filters['category_id']) {
                            $cat_name = array_filter($categories, fn($c) => $c['id'] == $filters['category_id']);
                            echo esc(reset($cat_name)['name'] ?? 'Category') . ' Prompts';
                        }
                        else echo 'All Public Prompts';
                        ?>
                    </h2>
                    <p class="text-sm font-medium text-slate-400 mt-1"><?php echo number_format($total_prompts); ?> items matching your criteria</p>
                </div>
                
                <div class="flex items-center bg-white rounded-2xl border border-slate-200 p-1">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-3">Sort:</span>
                    <select name="sort" onchange="window.location.href = 'public_prompts.php?' + new URLSearchParams({...Object.fromEntries(new URLSearchParams(window.location.search)), sort: this.value}).toString()" 
                        class="bg-transparent text-xs font-bold text-slate-700 border-none focus:ring-0 cursor-pointer pr-8 py-2">
                        <option value="newest" <?php echo $filters['sort'] === 'newest' ? 'selected' : ''; ?>>Newest First</option>
                        <option value="popular" <?php echo $filters['sort'] === 'popular' ? 'selected' : ''; ?>>Most Popular</option>
                        <option value="alphabetical" <?php echo $filters['sort'] === 'alphabetical' ? 'selected' : ''; ?>>Alphabetical</option>
                        <option value="oldest" <?php echo $filters['sort'] === 'oldest' ? 'selected' : ''; ?>>Oldest First</option>
                    </select>
                </div>
            </div>

            <!-- Prompts Grid -->
            <?php if (empty($prompts)): ?>
                <div class="bg-white rounded-[2.5rem] border border-slate-200 p-20 text-center shadow-sm">
                    <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-8 text-slate-300">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 mb-2">No matching prompts</h3>
                    <p class="text-slate-500 font-medium max-w-sm mx-auto mb-10">We couldn't find any prompts matching your current search or filter criteria.</p>
                    <a href="public_prompts.php" class="btn-primary">Browse All Prompts</a>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <?php foreach ($prompts as $p): 
                        $images = get_prompt_images($p['id']);
                        $cover_image = !empty($images) ? $images[0]['image_path'] : null;
                    ?>
                        <article class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm hover:shadow-2xl hover:shadow-slate-200/60 transition-all duration-500 flex flex-col group overflow-hidden border-b-4 border-b-transparent hover:border-b-primary-500">
                            
                            <!-- Card Header/Image -->
                            <div class="relative aspect-[16/9] w-full overflow-hidden bg-slate-100">
                                <?php if ($cover_image): ?>
                                    <img src="<?php echo esc($cover_image); ?>" alt="<?php echo esc($p['title']); ?>" 
                                        loading="lazy"
                                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                                <?php else: ?>
                                    <!-- Dynamic Placeholder -->
                                    <div class="w-full h-full bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center">
                                        <svg class="w-16 h-16 text-slate-300 group-hover:text-primary-200 transition-colors duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="absolute top-6 left-6 flex flex-wrap gap-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black bg-white/90 backdrop-blur text-primary-600 uppercase tracking-widest shadow-sm">
                                        <?php echo esc($p['category_name'] ?? 'Uncategorized'); ?>
                                    </span>
                                </div>
                                
                                <div class="absolute bottom-6 right-6 flex space-x-2 translate-y-4 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300">
                                    <button onclick="copyToClipboard(<?php echo esc(json_encode($p['content'])); ?>, this)" class="p-3 bg-slate-900 text-white rounded-2xl shadow-xl hover:bg-primary-600 transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Card Body -->
                            <div class="p-8 flex-grow flex flex-col">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center text-[10px] font-black mr-3 border-2 border-white shadow-sm">
                                            <?php echo strtoupper(substr($p['author_name'] ?? 'U', 0, 1)); ?>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-xs font-black text-slate-900 leading-none"><?php echo esc($p['author_name'] ?? 'Anonymous'); ?></span>
                                            <span class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-widest"><?php echo date('M j, Y', strtotime($p['created_at'])); ?></span>
                                        </div>
                                    </div>
                                    <div class="flex items-center text-slate-400 text-[11px] font-black uppercase tracking-widest">
                                        <svg class="w-4 h-4 mr-1.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        <?php echo number_format($p['view_count']); ?>
                                    </div>
                                </div>

                                <h2 class="text-2xl font-black text-slate-900 mb-3 leading-tight group-hover:text-primary-600 transition-colors">
                                    <a href="prompt.php?id=<?php echo $p['id'] . '-' . $p['slug']; ?>">
                                        <?php echo esc($p['title']); ?>
                                    </a>
                                </h2>
                                
                                <p class="text-slate-500 text-sm line-clamp-3 mb-8 font-medium leading-relaxed flex-grow">
                                    <?php echo esc(strip_tags($p['content'])); ?>
                                </p>

                                <div class="pt-6 border-t border-slate-50 flex items-center justify-between">
                                    <div class="flex -space-x-2">
                                        <!-- Placeholder for extra badges/tags if needed -->
                                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Premium Prompt</span>
                                    </div>
                                    <a href="prompt.php?id=<?php echo $p['id'] . '-' . $p['slug']; ?>" class="inline-flex items-center text-xs font-black text-primary-600 uppercase tracking-widest hover:text-primary-800 transition-colors">
                                        View Recipe
                                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                    </a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <nav class="mt-16 flex justify-center items-center space-x-2">
                        <?php if ($page > 1): ?>
                            <a href="public_prompts.php?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white text-slate-600 hover:bg-slate-50 border border-slate-200 transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                            </a>
                        <?php endif; ?>
                        
                        <?php 
                        $start = max(1, $page - 2);
                        $end = min($total_pages, $page + 2);
                        for ($i = $start; $i <= $end; $i++): 
                        ?>
                            <a href="public_prompts.php?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" 
                                class="w-12 h-12 flex items-center justify-center rounded-2xl font-black transition-all <?php echo $page == $i ? 'bg-primary-600 text-white shadow-xl shadow-primary-600/30' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200'; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <a href="public_prompts.php?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white text-slate-600 hover:bg-slate-50 border border-slate-200 transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        <?php endif; ?>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- Popular Categories Banner -->
    <section class="bg-indigo-600 rounded-[3rem] p-12 md:p-16 text-center text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 blur-[80px] rounded-full translate-x-1/2 -translate-y-1/2"></div>
        <div class="relative z-10">
            <h2 class="text-3xl md:text-4xl font-black mb-6">Want to share your own prompts?</h2>
            <p class="text-indigo-100 text-lg mb-10 max-w-2xl mx-auto font-medium">Join our community of prompt engineers and start contributing to the world's most organized prompt library.</p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="prompt_edit.php" class="px-10 py-5 bg-white text-indigo-600 font-black rounded-2xl hover:bg-indigo-50 transition-all shadow-xl">Start Creating</a>
                <a href="register.php" class="px-10 py-5 bg-indigo-500 text-white font-black rounded-2xl hover:bg-indigo-400 transition-all border border-indigo-400">Join Community</a>
            </div>
        </div>
    </section>

</div>

<?php include 'includes/footer.php'; ?>
