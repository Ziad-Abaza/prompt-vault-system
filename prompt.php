<?php
require_once 'bootstrap.php';

$id_param = $_GET['id'] ?? null;
if (!$id_param) {
    redirect('index.php');
}

// Extract ID from slug if necessary (e.g. 123-slug)
$id = (int)explode('-', $id_param)[0];

$prompt = get_prompt($id);
if (!$prompt) {
    abort(404);
}

// Increment view count
increment_prompt_view_count($id);

$is_owner = is_logged_in() && $prompt['user_id'] === get_current_user_id();

$page_title = $prompt['title'];
$meta_description = substr(strip_tags($prompt['content']), 0, 155) . '...';
$canonical_url = APP_URL_BASE . '/prompt.php?id=' . $prompt['id'] . '-' . $prompt['slug'];
$og_type = 'article';
$og_image = !empty($prompt['images']) ? APP_URL_BASE . '/' . $prompt['images'][0]['image_path'] : APP_URL_BASE . '/assets/logo.png';

include 'includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-10" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">
            <li>
                <a href="<?php echo APP_URL_BASE; ?>/index.php" class="hover:text-primary-600 transition-colors">Workspace</a>
            </li>
            <li>
                <svg class="h-3 w-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
                </svg>
            </li>
            <li class="text-slate-900 truncate max-w-[150px]"><?php echo esc($prompt['title']); ?></li>
        </ol>
    </nav>

    <div class="flex flex-col lg:flex-row gap-12">
        
        <!-- MAIN CONTENT (Left) -->
        <div class="flex-grow min-w-0">
            <header class="mb-10">
                <div class="flex items-center gap-3 mb-4">
                    <?php if (isset($prompt['category_slug'])): ?>
                        <a href="<?php echo APP_URL_BASE; ?>/prompts/<?php echo esc($prompt['category_slug']); ?>" class="px-3 py-1 bg-primary-50 text-primary-700 rounded-full text-[10px] font-black uppercase tracking-widest border border-primary-100 hover:bg-primary-600 hover:text-white transition-all duration-300">
                            <?php echo esc($prompt['category_name'] ?? 'Uncategorized'); ?>
                        </a>
                    <?php endif; ?>
                    <span class="text-[10px] font-bold text-slate-300 uppercase tracking-widest">
                        Updated <?php echo date('M d, Y', strtotime($prompt['updated_at'])); ?>
                    </span>
                </div>
                <h1 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight leading-[1.1] mb-6">
                    <?php echo esc($prompt['title']); ?>
                </h1>
            </header>

            <!-- PROMPT AREA -->
            <div class="group relative bg-slate-900 rounded-[2.5rem] shadow-2xl shadow-primary-900/20 overflow-hidden mb-12">
                <!-- Mac-style Window Controls -->
                <div class="flex items-center gap-1.5 px-6 py-4 bg-slate-800/50 border-b border-white/5">
                    <div class="w-2.5 h-2.5 rounded-full bg-[#ff5f56]"></div>
                    <div class="w-2.5 h-2.5 rounded-full bg-[#ffbd2e]"></div>
                    <div class="w-2.5 h-2.5 rounded-full bg-[#27c93f]"></div>
                    <span class="ml-4 text-[10px] font-black text-white/20 uppercase tracking-[0.3em]">AI Prompt Terminal</span>
                </div>

                <div class="relative p-8 md:p-12">
                    <pre class="font-mono text-slate-100 text-sm md:text-lg leading-relaxed whitespace-pre-wrap selection:bg-primary-500/30 selection:text-white"><?php echo esc($prompt['content']); ?></pre>
                    
                    <!-- Inner Copy Button -->
                    <button onclick="copyToClipboard(<?php echo esc(json_encode($prompt['content'])); ?>, this, <?php echo $prompt['id']; ?>)" 
                        class="absolute top-6 right-6 p-4 bg-white/10 hover:bg-primary-600 text-white rounded-2xl backdrop-blur-md border border-white/10 transition-all active:scale-95 group/copy" title="Copy to Clipboard">
                        <svg class="w-5 h-5 group-hover/copy:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" /></svg>
                    </button>
                </div>
            </div>

            <!-- TAGS & COLLECTIONS -->
            <?php if (!empty($prompt['tags']) || !empty($prompt['collections'])): ?>
                <div class="flex flex-wrap gap-6 mb-12">
                    <?php if (!empty($prompt['tags'])): ?>
                        <div class="space-y-3">
                            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Metadata Tags</h4>
                            <div class="flex flex-wrap gap-2">
                                <?php foreach ($prompt['tags'] as $tag): ?>
                                    <a href="<?php echo APP_URL_BASE; ?>/prompts/tag/<?php echo esc($tag['slug']); ?>" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 rounded-xl text-xs font-bold hover:border-primary-400 hover:text-primary-600 transition-all">
                                        #<?php echo esc($tag['name']); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($prompt['collections'])): ?>
                        <div class="space-y-3">
                            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Part of Collections</h4>
                            <div class="flex flex-wrap gap-2">
                                <?php foreach ($prompt['collections'] as $coll): ?>
                                    <a href="<?php echo APP_URL_BASE; ?>/collections/<?php echo esc($coll['slug']); ?>" class="px-4 py-2 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-xl text-xs font-bold hover:bg-indigo-600 hover:text-white transition-all">
                                        <?php echo esc($coll['name']); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- IMAGES GALLERY -->
            <?php if (!empty($prompt['images'])): ?>
                <section class="mb-12">
                    <h3 class="text-xl font-black text-slate-900 mb-6">Visual References</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        <?php foreach ($prompt['images'] as $index => $img): ?>
                            <button onclick="openLightbox(<?php echo $index; ?>)" class="relative aspect-square rounded-[2rem] overflow-hidden border border-slate-200 group">
                                <img src="<?php echo APP_URL_BASE . '/' . esc($img['image_path']); ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" alt="Prompt Visual">
                                <div class="absolute inset-0 bg-primary-900/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
        </div>

        <!-- SIDEBAR (Right) -->
        <div class="lg:w-80 flex-shrink-0">
            <div class="sticky top-24 space-y-8">
                
                <!-- PRIMARY ACTIONS -->
                <div class="bg-white rounded-[2.5rem] border border-slate-200 p-8 shadow-sm">
                    <div class="flex flex-col gap-4">
                        <button onclick="copyToClipboard(<?php echo esc(json_encode($prompt['content'])); ?>, this, <?php echo $prompt['id']; ?>)" 
                            class="w-full py-5 bg-primary-600 text-white font-black rounded-2xl hover:bg-primary-700 shadow-xl shadow-primary-600/20 transition-all transform active:scale-[0.98] flex items-center justify-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" /></svg>
                            Copy Prompt
                        </button>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <button onclick="toggleSave(<?php echo $prompt['id']; ?>, this)" 
                                class="py-4 rounded-2xl border border-slate-200 font-bold text-xs transition-all flex flex-col items-center gap-1.5 <?php echo is_prompt_saved($prompt['id']) ? 'text-red-500 bg-red-50 border-red-100' : 'text-slate-500 hover:bg-slate-50'; ?>">
                                <svg class="w-5 h-5" fill="<?php echo is_prompt_saved($prompt['id']) ? 'currentColor' : 'none'; ?>" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                                <span><?php echo is_prompt_saved($prompt['id']) ? 'Saved' : 'Favorite'; ?></span>
                            </button>

                            <?php if ($is_owner): ?>
                                <a href="<?php echo APP_URL_BASE; ?>/prompt_edit.php?id=<?php echo $prompt['id']; ?>" class="py-4 rounded-2xl border border-slate-200 font-bold text-xs text-slate-500 hover:bg-slate-50 transition-all flex flex-col items-center gap-1.5">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    Edit
                                </a>
                            <?php else: ?>
                                <div class="py-4 rounded-2xl bg-slate-50 border border-slate-100 font-bold text-xs text-slate-400 flex flex-col items-center gap-1.5 opacity-60">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                                    Read Only
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- QUICK STATS -->
                    <div class="mt-8 grid grid-cols-3 gap-2 border-t border-slate-50 pt-8">
                        <div class="text-center">
                            <span class="block text-sm font-black text-slate-900"><?php echo number_format($prompt['view_count']); ?></span>
                            <span class="text-[8px] font-black text-slate-400 uppercase tracking-tighter">Views</span>
                        </div>
                        <div class="text-center">
                            <span class="block text-sm font-black text-slate-900"><?php echo number_format($prompt['copy_count']); ?></span>
                            <span class="text-[8px] font-black text-slate-400 uppercase tracking-tighter">Copies</span>
                        </div>
                        <div class="text-center">
                            <span class="block text-sm font-black text-slate-900"><?php echo number_format(get_prompt_save_count($prompt['id'])); ?></span>
                            <span class="text-[8px] font-black text-slate-400 uppercase tracking-tighter">Saves</span>
                        </div>
                    </div>
                </div>

                <!-- AUTHOR CARD -->
                <div class="bg-white rounded-[2.5rem] border border-slate-200 p-8 shadow-sm overflow-hidden relative">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6">About the Author</h4>
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-primary-600 text-white flex items-center justify-center text-xl font-black shadow-lg shadow-primary-600/20 mr-4">
                            <?php echo strtoupper(substr($prompt['author_name'] ?? 'A', 0, 1)); ?>
                        </div>
                        <div class="min-w-0">
                            <a href="<?php echo APP_URL_BASE; ?>/u/<?php echo $prompt['author_slug']; ?>" class="block text-base font-black text-slate-900 hover:text-primary-600 transition-colors truncate">
                                <?php echo esc($prompt['author_name'] ?? 'Atlas User'); ?>
                            </a>
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Premium Contributor</span>
                        </div>
                    </div>
                    
                    <?php if (!empty($prompt['author_bio'])): ?>
                        <p class="text-xs text-slate-500 leading-relaxed line-clamp-3 mb-6"><?php echo esc($prompt['author_bio']); ?></p>
                    <?php endif; ?>

                    <!-- Social Icons -->
                    <div class="flex items-center gap-2">
                        <?php if (!empty($prompt['twitter_handle'])): ?>
                            <a href="https://twitter.com/<?php echo esc($prompt['twitter_handle']); ?>" target="_blank" class="p-2 bg-slate-50 text-slate-400 hover:text-[#1DA1F2] hover:bg-[#1DA1F2]/5 rounded-xl transition-all">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.84 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($prompt['github_handle'])): ?>
                            <a href="https://github.com/<?php echo esc($prompt['github_handle']); ?>" target="_blank" class="p-2 bg-slate-50 text-slate-400 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition-all">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 4.238 9.611 9.647 11.408.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.3 1.98-.45 3-.45s2.04.15 3 .45c2.295-1.552 3.3-1.23 3.3-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.242 21.61 24 17.303 24 12c0-6.627-5.373-12-12-12z"/></svg>
                            </a>
                        <?php endif; ?>
                        <a href="<?php echo APP_URL_BASE; ?>/u/<?php echo $prompt['author_slug']; ?>" class="ml-auto text-[10px] font-black uppercase tracking-widest text-primary-600 hover:text-primary-700 transition-colors">View Profile</a>
                    </div>
                </div>

                <!-- SHARE TOOLKIT -->
                <div class="bg-white rounded-[2.5rem] border border-slate-200 p-8 shadow-sm">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6 text-center">Spread the Intelligence</h4>
                    <div class="flex items-center justify-center gap-3">
                        <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode("Check out this AI prompt: " . $prompt['title']); ?>&url=<?php echo urlencode($canonical_url); ?>" 
                            target="_blank" class="w-12 h-12 flex items-center justify-center bg-slate-50 text-slate-400 hover:text-[#1DA1F2] hover:bg-[#1DA1F2]/5 rounded-2xl transition-all">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.84 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                        </a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode($canonical_url); ?>" 
                            target="_blank" class="w-12 h-12 flex items-center justify-center bg-slate-50 text-slate-400 hover:text-[#0A66C2] hover:bg-[#0A66C2]/5 rounded-2xl transition-all">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                        </a>
                        <button onclick="copyToClipboard('<?php echo $canonical_url; ?>', this)" 
                            class="w-12 h-12 flex items-center justify-center bg-slate-50 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-2xl transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- LIGHTBOX SCRIPT -->
    <?php if (!empty($prompt['images'])): ?>
        <div id="lightbox" class="fixed inset-0 z-50 hidden bg-slate-900/95 backdrop-blur-sm flex items-center justify-center p-4 md:p-10">
            <button onclick="closeLightbox()" class="absolute top-6 right-6 text-white hover:text-primary-400 transition-colors">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            <div class="max-w-5xl max-h-full">
                <img id="lightbox-img" src="" class="max-w-full max-h-[85vh] rounded-lg shadow-2xl object-contain mx-auto">
            </div>
        </div>

        <script>
            const images = <?php echo json_encode(array_map(fn($img) => APP_URL_BASE . '/' . $img['image_path'], $prompt['images'])); ?>;
            let currentIndex = 0;
            function openLightbox(index) {
                currentIndex = index;
                document.getElementById('lightbox-img').src = images[currentIndex];
                document.getElementById('lightbox').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
            function closeLightbox() {
                document.getElementById('lightbox').classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') closeLightbox();
            });
        </script>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
