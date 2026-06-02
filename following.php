<?php
require_once 'bootstrap.php';

// Requires login
if (!is_logged_in()) {
    redirect(APP_URL_BASE . '/login.php');
}

$followed_authors = get_followed_authors();

$page_title = "Authors I Follow";
$breadcrumbs = [
    ['name' => 'Library', 'url' => APP_URL_BASE . '/dashboard.php'],
    ['name' => 'Following', 'url' => APP_URL_BASE . '/following.php']
];

include 'includes/header.php';
?>

<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-10">
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-2">My <span class="text-primary-600">Network</span></h1>
        <p class="text-slate-500 font-medium">Keep track of your favorite prompt engineers and contributors.</p>
    </div>

    <?php if (empty($followed_authors)): ?>
        <div class="py-24 text-center bg-white rounded-[3rem] border border-slate-200 border-dashed">
            <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-primary-50 text-primary-400 mb-6">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
            </div>
            <h3 class="text-2xl font-black text-slate-900 mb-2">You're not following anyone yet</h3>
            <p class="text-slate-500 font-medium max-w-xs mx-auto mb-8 text-balance">Follow contributors from their public profiles to build your network.</p>
            <a href="<?php echo APP_URL_BASE; ?>/public_prompts.php" class="px-8 py-4 bg-primary-600 text-white font-bold rounded-2xl hover:bg-primary-700 transition-all shadow-xl shadow-primary-600/20">Discover Top Authors</a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php foreach ($followed_authors as $author): ?>
                <div class="group bg-white rounded-[2rem] border border-slate-200 p-6 hover:border-primary-300 hover:shadow-xl hover:shadow-primary-900/5 transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center min-w-0">
                            <div class="w-12 h-12 shrink-0 rounded-2xl bg-primary-50 text-primary-600 flex items-center justify-center font-black text-lg mr-4 group-hover:bg-primary-600 group-hover:text-white transition-colors duration-300">
                                <?php echo strtoupper(substr($author['username'], 0, 1)); ?>
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-base font-bold text-slate-900 truncate"><?php echo esc($author['username']); ?></h3>
                                <div class="flex items-center gap-3 mt-1">
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest"><?php echo get_followers_count($author['id']); ?> Followers</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <a href="<?php echo APP_URL_BASE; ?>/u/<?php echo $author['slug']; ?>" class="px-4 py-2 bg-slate-50 text-slate-600 text-[10px] font-bold uppercase tracking-widest rounded-lg hover:bg-primary-50 hover:text-primary-600 transition-all">View Profile</a>
                            <button onclick="toggleFollow(<?php echo $author['id']; ?>, this)" class="p-2 text-slate-400 hover:text-red-500 transition-colors" title="Unfollow">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
    function toggleFollow(authorId, btnElement) {
        const formData = new FormData();
        formData.append('following_id', authorId);
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (csrfToken) formData.append('csrf_token', csrfToken);

        fetch('<?php echo APP_URL_BASE; ?>/ajax_toggle_follow.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.state === 'unfollowed') {
                    // If we're on the following page, we might want to hide the card
                    const card = btnElement.closest('.group');
                    card.style.opacity = '0.5';
                    card.style.pointerEvents = 'none';
                    showToast('Unfollowed user');
                }
            }
        })
        .catch(err => console.error('Follow error:', err));
    }
</script>

<?php include 'includes/footer.php'; ?>
