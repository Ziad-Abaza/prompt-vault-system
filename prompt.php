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
$meta_description = substr(strip_tags($prompt['content']), 0, 160);
$canonical_url = rtrim(Env::get('APP_URL', ''), '/') . '/prompt.php?id=' . $id . '-' . $prompt['slug'];

$breadcrumbs = [
    ['name' => 'Library', 'url' => 'index.php'],
    ['name' => $prompt['category_name'] ?? 'Uncategorized', 'url' => 'index.php?category_id=' . ($prompt['category_id'] ?? '')],
    ['name' => $prompt['title'], 'url' => 'prompt.php?id=' . $id . '-' . $prompt['slug']]
];

include 'includes/header.php';
?>

<div class="max-w-5xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2 text-xs font-bold uppercase tracking-wider">
            <li>
                <a href="index.php" class="text-slate-400 hover:text-primary-600 transition-colors">Workspace</a>
            </li>
            <li>
                <svg class="h-3 w-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
                </svg>
            </li>
            <li class="text-slate-900 truncate max-w-[200px]"><?php echo esc($prompt['title']); ?></li>
        </ol>
    </nav>

    <!-- Header Actions -->
    <div class="flex flex-col md:flex-row md:items-start justify-between gap-6 mb-8">
        <div class="flex-grow">
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight leading-tight mb-3">
                <?php echo esc($prompt['title']); ?>
            </h1>
            <div class="flex flex-wrap items-center gap-3">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-primary-50 text-primary-700 uppercase tracking-wider border border-primary-100">
                    <?php echo esc($prompt['category_name'] ?? 'Uncategorized'); ?>
                </span>
                <span class="inline-flex items-center text-slate-400 text-[10px] font-bold uppercase tracking-widest">
                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    By <?php echo esc($prompt['author_name'] ?? 'Unknown'); ?>
                </span>
                <span class="text-slate-300 text-[10px] font-bold uppercase tracking-widest">
                    <?php echo date('M j, Y', strtotime($prompt['updated_at'])); ?>
                </span>
                <span class="inline-flex items-center text-slate-400 text-[10px] font-bold uppercase tracking-widest">
                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    <?php echo number_format($prompt['view_count']); ?>
                </span>
            </div>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <button onclick="copyToClipboard(<?php echo esc(json_encode($prompt['content'])); ?>, this, <?php echo $prompt['id']; ?>)" class="inline-flex items-center px-4 py-2 bg-slate-900 text-white text-xs font-bold rounded-lg hover:bg-slate-800 transition-colors shadow-lg shadow-slate-900/10">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                </svg>
                Copy Prompt
            </button>
            <?php if ($is_owner): ?>
                <a href="prompt_edit.php?id=<?php echo $prompt['id']; ?>" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 text-slate-700 text-xs font-bold rounded-lg hover:bg-slate-50 transition-colors">
                    <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-6 md:p-8">
                    <div class="bg-slate-50 rounded-xl p-5 md:p-6 font-mono text-slate-800 whitespace-pre-wrap leading-relaxed border border-slate-100 text-sm md:text-base">
                        <?php echo esc($prompt['content']); ?>
                    </div>
                </div>
                
                <?php if (!empty($prompt['tags']) || !empty($prompt['collections'])): ?>
                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex flex-col gap-4">
                        <?php if (!empty($prompt['tags'])): ?>
                            <div class="flex flex-wrap gap-1.5">
                                <?php foreach ($prompt['tags'] as $tag): ?>
                                    <a href="index.php?tag_id=<?php echo $tag['id']; ?>" class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-white border border-slate-200 text-slate-500 hover:border-primary-300 hover:text-primary-700 transition-colors">
                                        #<?php echo esc($tag['name']); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($prompt['collections'])): ?>
                            <div class="flex flex-wrap gap-1.5">
                                <?php foreach ($prompt['collections'] as $coll): ?>
                                    <a href="index.php?collection_id=<?php echo $coll['id']; ?>" class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition-colors">
                                        <svg class="w-3 h-3 mr-1.5 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                        </svg>
                                        <?php echo esc($coll['name']); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sidebar / Attachments -->
        <div class="lg:col-span-1 space-y-6">
            <?php if (!empty($prompt['images'])): ?>
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                    <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4">Visual References</h3>
                    <div class="grid grid-cols-2 gap-3" id="prompt-gallery">
                        <?php foreach ($prompt['images'] as $index => $img): ?>
                            <div class="rounded-xl overflow-hidden border border-slate-100 bg-slate-50 aspect-square">
                                <button onclick="openLightbox(<?php echo $index; ?>)" class="block w-full h-full group relative">
                                    <img src="<?php echo esc($img['image_path']); ?>" 
                                         alt="Prompt Visual" 
                                         loading="lazy"
                                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                    <div class="absolute inset-0 bg-slate-900/0 group-hover:bg-slate-900/20 transition-colors flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    </div>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Simple Lightbox Modal -->
                <div id="lightbox" class="fixed inset-0 z-50 hidden bg-slate-900/95 backdrop-blur-sm flex items-center justify-center p-4 md:p-10">
                    <button onclick="closeLightbox()" class="absolute top-6 right-6 text-white hover:text-primary-400 transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                    
                    <button onclick="prevImage()" class="absolute left-6 text-white hover:text-primary-400 transition-colors hidden md:block">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </button>
                    
                    <div class="max-w-5xl max-h-full">
                        <img id="lightbox-img" src="" class="max-w-full max-h-[85vh] rounded-lg shadow-2xl object-contain mx-auto">
                        <p id="lightbox-caption" class="text-center text-slate-400 text-xs font-bold uppercase tracking-widest mt-6"></p>
                    </div>

                    <button onclick="nextImage()" class="absolute right-6 text-white hover:text-primary-400 transition-colors hidden md:block">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>

                <script>
                    const images = <?php echo json_encode(array_column($prompt['images'], 'image_path')); ?>;
                    let currentIndex = 0;

                    function openLightbox(index) {
                        currentIndex = index;
                        updateLightbox();
                        document.getElementById('lightbox').classList.remove('hidden');
                        document.body.style.overflow = 'hidden';
                    }

                    function closeLightbox() {
                        document.getElementById('lightbox').classList.add('hidden');
                        document.body.style.overflow = 'auto';
                    }

                    function updateLightbox() {
                        const img = document.getElementById('lightbox-img');
                        const cap = document.getElementById('lightbox-caption');
                        img.src = images[currentIndex];
                        cap.innerText = `Visual ${currentIndex + 1} of ${images.length}`;
                    }

                    function nextImage() {
                        currentIndex = (currentIndex + 1) % images.length;
                        updateLightbox();
                    }

                    function prevImage() {
                        currentIndex = (currentIndex - 1 + images.length) % images.length;
                        updateLightbox();
                    }

                    // Keyboard support
                    document.addEventListener('keydown', (e) => {
                        if (document.getElementById('lightbox').classList.contains('hidden')) return;
                        if (e.key === 'Escape') closeLightbox();
                        if (e.key === 'ArrowRight') nextImage();
                        if (e.key === 'ArrowLeft') prevImage();
                    });
                </script>
            <?php endif; ?>
        </div>
    </div>

    <!-- Navigation Footer -->
    <div class="mt-12 flex justify-between items-center text-[10px] font-bold uppercase tracking-widest text-slate-400 border-t border-slate-200 pt-8">
        <a href="index.php" class="flex items-center hover:text-primary-600 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Library
        </a>
        <span class="hidden md:inline">Reference ID: #<?php echo $prompt['id']; ?></span>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
