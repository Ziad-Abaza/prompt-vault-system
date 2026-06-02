<?php
require_once 'bootstrap.php';

$top_authors = get_top_authors(20);

$page_title = "Community Leaderboards";
$meta_description = "Discover the top prompt engineers in our community. Rank of most influential contributors based on their public prompts, views, and engagement.";
$canonical_url = APP_URL_BASE . '/leaderboards.php';

$breadcrumbs = [
    ['name' => 'Home', 'url' => APP_URL_BASE . '/public_prompts.php'],
    ['name' => 'Community', 'url' => APP_URL_BASE . '/public_prompts.php'],
    ['name' => 'Leaderboards', 'url' => APP_URL_BASE . '/leaderboards.php']
];

include 'includes/header.php';
?>

<div class="max-w-5xl mx-auto">
    <!-- Header -->
    <div class="text-center mb-16">
        <h1 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight mb-4">Community <span class="text-primary-600">Leaderboards</span></h1>
        <p class="text-slate-500 font-medium max-w-xl mx-auto">Recognizing the most influential prompt engineers and contributors in the ecosystem.</p>
    </div>

    <!-- Leaderboard Table -->
    <div class="bg-white rounded-[3rem] border border-slate-200 shadow-xl shadow-slate-200/40 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Rank</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Contributor</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Prompts</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Total Views</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Engagement</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Impact Score</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php foreach ($top_authors as $index => $author): 
                        $rank = $index + 1;
                        $impact_score = ($author['total_views'] + $author['total_copies'] * 5 + $author['total_saves'] * 10);
                        
                        $rank_class = "";
                        $rank_icon = "";
                        if ($rank === 1) { $rank_class = "text-amber-500"; $rank_icon = "👑"; }
                        elseif ($rank === 2) { $rank_class = "text-slate-400"; $rank_icon = "🥈"; }
                        elseif ($rank === 3) { $rank_class = "text-amber-700"; $rank_icon = "🥉"; }
                        else { $rank_class = "text-slate-300"; }
                    ?>
                        <tr class="group hover:bg-slate-50/50 transition-colors">
                            <td class="px-8 py-6 text-center">
                                <span class="text-lg font-black <?php echo $rank_class; ?>">
                                    <?php echo $rank; ?>
                                </span>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-2xl bg-primary-50 text-primary-600 flex items-center justify-center font-black text-sm mr-4 shadow-sm border border-primary-100/50 group-hover:scale-110 transition-transform">
                                        <?php echo strtoupper(substr($author['username'], 0, 1)); ?>
                                    </div>
                                    <div class="flex flex-col min-w-0">
                                        <a href="<?php echo APP_URL_BASE; ?>/u/<?php echo $author['slug']; ?>" class="text-sm font-bold text-slate-900 hover:text-primary-600 transition-colors truncate">
                                            <?php echo esc($author['username']); ?> <?php echo $rank_icon; ?>
                                        </a>
                                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">Member since <?php echo date('M Y', strtotime($author['created_at'])); ?></span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <span class="text-sm font-bold text-slate-700"><?php echo number_format($author['public_prompt_count']); ?></span>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <span class="text-sm font-bold text-slate-700"><?php echo number_format($author['total_views']); ?></span>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <div class="flex flex-col items-center gap-1">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase flex items-center">
                                        <svg class="w-3 h-3 mr-1 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                        <?php echo number_format($author['total_copies']); ?>
                                    </span>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase flex items-center">
                                        <svg class="w-3 h-3 mr-1 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                                        <?php echo number_format($author['total_saves']); ?>
                                    </span>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <span class="inline-flex items-center px-4 py-1.5 rounded-xl bg-primary-600 text-white text-xs font-black shadow-lg shadow-primary-600/20">
                                    <?php echo number_format($impact_score); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-12 bg-slate-900 rounded-[2.5rem] p-8 md:p-12 text-white relative overflow-hidden">
        <div class="relative z-10">
            <h3 class="text-2xl font-black mb-4">How is the score calculated?</h3>
            <p class="text-slate-400 text-sm leading-relaxed max-w-2xl mb-8">
                Our Impact Score is a weighted metric designed to reward high-value community engagement. <br>
                <strong>1 View = 1 pt</strong> &bull; <strong>1 Copy = 5 pts</strong> &bull; <strong>1 Save = 10 pts</strong>
            </p>
            <a href="<?php echo APP_URL_BASE; ?>/register.php" class="inline-flex items-center px-8 py-4 bg-primary-600 text-white font-bold rounded-2xl hover:bg-primary-700 transition-all shadow-xl shadow-primary-600/20">Join the Community</a>
        </div>
        <div class="absolute -bottom-20 -right-20 w-64 h-64 bg-primary-600/20 rounded-full blur-3xl"></div>
    </div>
</div>

<?php include 'includes/header.php'; ?>
