<?php
require_once 'bootstrap.php';

// Public access allowed
$filters = [
    'is_public' => true,
    'category_id' => $_GET['category_id'] ?? null,
    'tag_id' => $_GET['tag_id'] ?? null,
    'search' => $_GET['search'] ?? null,
    'sort' => $_GET['sort'] ?? 'trending',
];

// Build SQL based on sort
$order_by = "p.created_at DESC";
if ($filters['sort'] === 'trending') $order_by = "(p.view_count + p.copy_count * 5) DESC";
if ($filters['sort'] === 'popular') $order_by = "p.copy_count DESC";
if ($filters['sort'] === 'newest') $order_by = "p.created_at DESC";

// Get categories for navigation
$categories = query("SELECT DISTINCT c.* FROM categories c JOIN prompts p ON c.id = p.category_id WHERE p.is_public = 1 ORDER BY c.name ASC")->fetchAll();

$page_title = "Explore Prompts";
$meta_description = "Discover high-quality AI prompts in a social-feed style gallery. Browse trending, most-copied, and recently shared prompts.";

include 'includes/header.php';
?>

<div class="max-w-7xl mx-auto">
    
    <!-- Modern Compact Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Explore <span class="text-primary-600">Hub</span></h1>
            <p class="text-slate-500 text-xs font-bold uppercase tracking-widest mt-0.5">Discover trending prompt engineering</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-3">
            <form action="public_prompts.php" method="GET" class="relative group">
                <input type="text" name="search" value="<?php echo esc($filters['search']); ?>" 
                    class="h-10 w-full md:w-64 pl-10 pr-4 bg-white border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all text-xs font-medium" 
                    placeholder="Search feed...">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-slate-400 group-focus-within:text-primary-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </form>
            
            <div class="flex items-center bg-white rounded-xl border border-slate-200 p-1">
                <a href="public_prompts.php?sort=trending" class="px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider transition-all <?php echo $filters['sort'] === 'trending' ? 'bg-slate-900 text-white shadow-md' : 'text-slate-500 hover:text-slate-900'; ?>">Trending</a>
                <a href="public_prompts.php?sort=newest" class="px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider transition-all <?php echo $filters['sort'] === 'newest' ? 'bg-slate-900 text-white shadow-md' : 'text-slate-500 hover:text-slate-900'; ?>">New</a>
                <a href="public_prompts.php?sort=popular" class="px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider transition-all <?php echo $filters['sort'] === 'popular' ? 'bg-slate-900 text-white shadow-md' : 'text-slate-500 hover:text-slate-900'; ?>">Popular</a>
            </div>
        </div>
    </div>

    <!-- Horizontal Category Navigation -->
    <div class="relative mb-8">
        <div class="flex items-center gap-2 overflow-x-auto pb-2 no-scrollbar">
            <a href="public_prompts.php" class="shrink-0 px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition-all <?php echo empty($filters['category_id']) ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/20' : 'bg-white border border-slate-200 text-slate-500 hover:border-primary-400 hover:text-primary-600'; ?>">
                All
            </a>
            <?php foreach ($categories as $cat): ?>
                <a href="public_prompts.php?category_id=<?php echo $cat['id']; ?>" class="shrink-0 px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition-all <?php echo $filters['category_id'] == $cat['id'] ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/20' : 'bg-white border border-slate-200 text-slate-500 hover:border-primary-400 hover:text-primary-600'; ?>">
                    <?php echo esc($cat['name']); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Main Feed Grid -->
    <?php
    $per_page = 24;
    $page = max(1, (int)($_GET['page'] ?? 1));
    $offset = ($page - 1) * $per_page;

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
    if (!empty($filters['search'])) {
        $sql .= " AND (p.title LIKE ? OR p.content LIKE ?)";
        $params[] = '%' . $filters['search'] . '%';
        $params[] = '%' . $filters['search'] . '%';
    }

    $sql .= " ORDER BY $order_by LIMIT ? OFFSET ?";
    $params[] = $per_page;
    $params[] = $offset;

    $prompts = query($sql, $params)->fetchAll();

    if (empty($prompts)): ?>
        <div class="py-20 text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-white border border-slate-100 shadow-sm mb-4">
                <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H4a2 2 0 00-2 2v11a2 2 0 002 2h11M20 13l-4 4m4-4l4 4m-4-4v6m-6-6H7m1-4h.01M11 16h.01"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-slate-900 mb-1">No results found</h3>
            <p class="text-slate-500 text-sm font-medium max-w-xs mx-auto mb-6">Try exploring other categories or search terms.</p>
            <a href="public_prompts.php" class="px-5 py-2.5 bg-primary-600 text-white text-sm font-bold rounded-lg hover:bg-primary-700 transition-colors">Return to Feed</a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
            <?php foreach ($prompts as $prompt): ?>
                <?php include 'includes/prompt_card.php'; ?>
            <?php endforeach; ?>
        </div>

        <!-- Pagination (Simple Feed Style) -->
        <?php
        $count_sql = "SELECT COUNT(*) FROM prompts WHERE is_public = 1";
        $count_params = [];
        if (!empty($filters['category_id'])) {
            $count_sql .= " AND category_id = ?";
            $count_params[] = $filters['category_id'];
        }
        if (!empty($filters['search'])) {
            $count_sql .= " AND (title LIKE ? OR content LIKE ?)";
            $count_params[] = '%' . $filters['search'] . '%';
            $count_params[] = '%' . $filters['search'] . '%';
        }
        $total_prompts = query($count_sql, $count_params)->fetchColumn();
        $total_pages = ceil($total_prompts / $per_page);
        
        if ($total_pages > 1): ?>
            <div class="mt-12 mb-8 flex justify-center">
                <nav class="flex items-center space-x-2">
                    <?php if ($page > 1): ?>
                        <a href="public_prompts.php?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" class="px-5 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-600 font-bold text-[10px] uppercase tracking-wider hover:bg-slate-50 transition-all">Previous</a>
                    <?php endif; ?>
                    
                    <span class="px-4 py-2 text-slate-400 font-bold text-[10px] uppercase tracking-wider">Page <?php echo $page; ?> of <?php echo $total_pages; ?></span>

                    <?php if ($page < $total_pages): ?>
                        <a href="public_prompts.php?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" class="px-5 py-2.5 rounded-xl bg-slate-900 text-white font-bold text-[10px] uppercase tracking-wider hover:bg-primary-600 transition-all shadow-lg shadow-slate-900/20">Next Feed</a>
                    <?php endif; ?>
                </nav>
            </div>
        <?php endif; ?>
    <?php endif; ?>

</div>

<style>
/* Hide scrollbar for Chrome, Safari and Opera */
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
/* Hide scrollbar for IE, Edge and Firefox */
.no-scrollbar {
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
}
</style>

<?php include 'includes/footer.php'; ?>
