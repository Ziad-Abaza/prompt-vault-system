<?php
/**
 * Prompt Card Partial
 * Expects $prompt variable to be defined in the parent scope.
 */
if (!isset($prompt) && isset($p)) {
    $prompt = $p; // Handle cases where $p is used in the loop
}

if (isset($prompt)): 
    // Absolute path base detection
    $card_url_base = rtrim(Env::get('APP_URL', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]"), '/');

    // Fetch image if not already attached to the prompt array
    if (!isset($prompt['images'])) {
        $prompt['images'] = get_prompt_images($prompt['id']);
    }
    $cover_image = !empty($prompt['images']) ? $prompt['images'][0]['image_path'] : null;
?>
    <div class="group relative bg-white rounded-3xl border border-slate-200 hover:border-primary-400 hover:shadow-2xl hover:shadow-primary-900/10 transition-all duration-300 flex flex-col overflow-hidden h-full">
        <!-- Background Image/Pattern Deco -->
        <?php if ($cover_image): ?>
            <div class="absolute inset-0 z-0 opacity-[0.03] group-hover:opacity-[0.07] transition-opacity duration-500">
                <img src="<?php echo esc($cover_image); ?>" class="w-full h-full object-cover filter blur-xl scale-110" alt="">
            </div>
        <?php endif; ?>

        <div class="relative z-10 p-5 flex flex-col h-full">
            <div class="flex justify-between items-start mb-4">
                <div class="flex flex-wrap gap-1.5">
                    <?php if (isset($prompt['category_slug'])): ?>
                        <a href="<?php echo $card_url_base; ?>/prompts/<?php echo esc($prompt['category_slug']); ?>" class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold bg-primary-50 text-primary-700 uppercase tracking-widest border border-primary-100/50 hover:bg-primary-600 hover:text-white hover:border-primary-600 transition-all duration-300">
                            <?php echo esc($prompt['category_name'] ?? 'Uncategorized'); ?>
                        </a>
                    <?php else: ?>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold bg-slate-50 text-slate-500 uppercase tracking-widest border border-slate-100">
                            <?php echo esc($prompt['category_name'] ?? 'Uncategorized'); ?>
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Corner Image Thumbnail -->
                <?php if ($cover_image): ?>
                    <div class="shrink-0 ml-4">
                        <div class="w-12 h-12 rounded-xl overflow-hidden border-2 border-white shadow-sm transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
                            <img src="<?php echo esc($cover_image); ?>" class="w-full h-full object-cover" alt="">
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="flex-grow">
                <a href="<?php echo $card_url_base; ?>/prompt.php?id=<?php echo $prompt['id'] . '-' . $prompt['slug']; ?>" class="block mb-1">
                    <h2 class="text-base font-bold text-slate-900 group-hover:text-primary-600 transition-colors leading-tight line-clamp-2">
                        <?php echo esc($prompt['title']); ?>
                    </h2>
                </a>
                
                <div class="flex items-center gap-1.5 mb-3">
                    <div class="w-4 h-4 rounded-full bg-slate-200 flex items-center justify-center text-[8px] font-black text-slate-500 uppercase">
                        <?php echo strtoupper(substr($prompt['author_name'] ?? 'A', 0, 1)); ?>
                    </div>
                    <a href="<?php echo $card_url_base; ?>/u/<?php echo esc($prompt['author_slug'] ?? slugify($prompt['author_name'])); ?>" class="text-[10px] font-bold text-slate-400 hover:text-primary-600 transition-colors uppercase tracking-widest">
                        <?php echo esc($prompt['author_name'] ?? 'Atlas User'); ?>
                    </a>
                </div>

                <p class="text-slate-500 text-xs line-clamp-3 leading-relaxed mb-4">
                    <?php echo esc(strip_tags($prompt['content'])); ?>
                </p>
            </div>

            <div class="mt-auto pt-4 border-t border-slate-50 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">
                        <?php echo date('M d', strtotime($prompt['updated_at'])); ?>
                    </span>
                    <?php if ($prompt['is_public']): ?>
                        <span class="w-1 h-1 rounded-full bg-green-400"></span>
                        <span class="text-[9px] font-bold text-green-600 uppercase tracking-widest">Public</span>
                    <?php endif; ?>
                </div>

                <div class="flex items-center gap-1">
                    <button onclick="toggleSave(<?php echo $prompt['id']; ?>, this)" 
                        class="p-2 rounded-xl transition-all <?php echo is_prompt_saved($prompt['id']) ? 'text-red-500 bg-red-50' : 'text-slate-400 hover:text-red-500 hover:bg-red-50'; ?>" 
                        title="<?php echo is_prompt_saved($prompt['id']) ? 'Unsave Prompt' : 'Save to My Library'; ?>">
                        <svg class="w-4 h-4" fill="<?php echo is_prompt_saved($prompt['id']) ? 'currentColor' : 'none'; ?>" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </button>

                    <button onclick="copyToClipboard(<?php echo esc(json_encode($prompt['content'])); ?>, this, <?php echo $prompt['id']; ?>)" class="p-2 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-all" title="Copy Prompt">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" /></svg>
                    </button>
                    <?php if (is_logged_in() && $prompt['user_id'] == get_current_user_id()): ?>
                        <a href="<?php echo $card_url_base; ?>/prompt_edit.php?id=<?php echo $prompt['id']; ?>" class="p-2 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-xl transition-all" title="Edit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </a>
                    <?php endif; ?>
                    <a href="<?php echo $card_url_base; ?>/prompt.php?id=<?php echo $prompt['id'] . '-' . $prompt['slug']; ?>" class="p-2 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-all" title="View Full Prompt">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
