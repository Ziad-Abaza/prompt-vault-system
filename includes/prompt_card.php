<?php
/**
 * Prompt Card Partial
 * Expects $prompt variable to be defined in the parent scope.
 */
if (!isset($prompt) && isset($p)) {
    $prompt = $p; // Handle cases where $p is used in the loop
}

if (isset($prompt)): 
    // Fetch image if not already attached to the prompt array
    if (!isset($prompt['images'])) {
        $prompt['images'] = get_prompt_images($prompt['id']);
    }
    $cover_image = !empty($prompt['images']) ? $prompt['images'][0]['image_path'] : null;
?>
    <div class="group bg-white rounded-xl border border-slate-200 hover:border-primary-300 hover:shadow-lg transition-all duration-200 flex flex-col overflow-hidden">
        <?php if ($cover_image): ?>
            <a href="prompt.php?id=<?php echo $prompt['id'] . '-' . $prompt['slug']; ?>" class="block relative aspect-video overflow-hidden bg-slate-50 border-b border-slate-100">
                <img src="<?php echo esc($cover_image); ?>" alt="<?php echo esc($prompt['title']); ?>" loading="lazy" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
            </a>
        <?php endif; ?>

        <div class="p-4 flex-grow">
            <div class="flex justify-between items-start mb-2">
                <div class="flex flex-wrap gap-1.5">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-primary-50 text-primary-700 uppercase tracking-wider border border-primary-100">
                        <?php echo esc($prompt['category_name'] ?? 'Uncategorized'); ?>
                    </span>
                    <?php if ($prompt['is_public']): ?>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-green-50 text-green-700 uppercase tracking-wider border border-green-100">
                            Public
                        </span>
                    <?php endif; ?>
                </div>
                <div class="flex space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button onclick="copyToClipboard(<?php echo esc(json_encode($prompt['content'])); ?>, this, <?php echo $prompt['id']; ?>)" class="p-1.5 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-md transition-colors" title="Copy Prompt">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                        </svg>
                    </button>
                </div>
            </div>
            
            <a href="prompt.php?id=<?php echo $prompt['id'] . '-' . $prompt['slug']; ?>" class="block group-hover:text-primary-600 transition-colors">
                <h2 class="text-base font-bold text-slate-900 mb-2 leading-tight line-clamp-2"><?php echo esc($prompt['title']); ?></h2>
            </a>
            
            <p class="text-slate-600 text-xs line-clamp-3 leading-normal">
                <?php echo esc(strip_tags($prompt['content'])); ?>
            </p>
        </div>
        
        <div class="px-4 py-3 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                <?php echo date('M j, Y', strtotime($prompt['updated_at'])); ?>
            </span>
            <div class="flex items-center gap-3">
                <?php if (is_logged_in() && $prompt['user_id'] == get_current_user_id()): ?>
                    <a href="prompt_edit.php?id=<?php echo $prompt['id']; ?>" class="text-[10px] font-bold text-slate-400 hover:text-amber-600 uppercase tracking-widest transition-colors">
                        Edit
                    </a>
                <?php endif; ?>
                <a href="prompt.php?id=<?php echo $prompt['id'] . '-' . $prompt['slug']; ?>" class="text-[10px] font-bold text-primary-600 hover:text-primary-800 uppercase tracking-widest flex items-center transition-colors">
                    View
                    <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
<?php endif; ?>
