<?php
require_once 'bootstrap.php';

// Requires login
if (!is_logged_in()) {
    redirect('login.php');
}

$user_id = get_current_user_id();
$user = query("SELECT * FROM users WHERE id = ?", [$user_id])->fetch();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $validator = new Validator($_POST);
    $validator->max('bio', 500)
              ->max('twitter_handle', 50)
              ->max('github_handle', 50)
              ->url('website_url', 'Please enter a valid website URL.');

    if ($validator->is_valid()) {
        try {
            update_user_profile($user_id, $_POST);
            set_flash('Profile updated successfully.');
            redirect('settings.php');
        } catch (Exception $e) {
            $errors['form'] = $e->getMessage();
        }
    } else {
        $errors = $validator->get_errors();
    }
}

$page_title = "Profile Settings";
$breadcrumbs = [
    ['name' => 'Library', 'url' => 'dashboard.php'],
    ['name' => 'Settings', 'url' => 'settings.php']
];

include 'includes/header.php';
?>

<div class="max-w-4xl mx-auto">
    <!-- Page Header -->
    <div class="mb-10">
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-2">Profile <span class="text-primary-600">Settings</span></h1>
        <p class="text-slate-500 font-medium">Customize your public contributor profile and professional identity.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Sidebar Info -->
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-white rounded-[2.5rem] border border-slate-200 p-8 text-center shadow-sm">
                <div class="w-24 h-24 rounded-[2.5rem] bg-primary-600 text-white flex items-center justify-center text-4xl font-black shadow-xl shadow-primary-600/20 mx-auto mb-6">
                    <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-1"><?php echo esc($user['username']); ?></h3>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6">Community Contributor</p>
                
                <a href="<?php echo APP_URL_BASE; ?>/u/<?php echo $user['slug']; ?>" class="inline-flex items-center text-xs font-bold text-primary-600 hover:text-primary-700 transition-colors">
                    View Public Profile
                    <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                </a>
            </div>

            <div class="bg-slate-900 rounded-[2.5rem] p-8 text-white relative overflow-hidden">
                <div class="relative z-10">
                    <h4 class="font-bold mb-2">Why complete your profile?</h4>
                    <p class="text-xs text-slate-400 leading-relaxed">Adding a bio and social links helps build trust (EEAT) and makes your public prompts more discoverable by the community.</p>
                </div>
                <div class="absolute -bottom-10 -right-10 w-32 h-32 bg-primary-600/20 rounded-full blur-2xl"></div>
            </div>
        </div>

        <!-- Form Section -->
        <div class="lg:col-span-8">
            <form action="settings.php" method="POST" class="space-y-6">
                <?php echo csrf_input(); ?>
                
                <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-8 py-6 border-b border-slate-50 bg-slate-50/50">
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-widest">Public Information</h3>
                    </div>
                    
                    <div class="p-8 space-y-8">
                        <div class="space-y-2">
                            <label for="bio" class="block text-xs font-bold text-slate-500 uppercase tracking-widest px-1">Professional Bio</label>
                            <textarea name="bio" id="bio" rows="4" 
                                class="block w-full px-6 py-5 bg-slate-50 border border-slate-100 rounded-3xl focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 focus:bg-white transition-all text-slate-900 font-medium placeholder-slate-300" 
                                placeholder="Tell the community about your expertise and interests..."><?php echo esc($user['bio']); ?></textarea>
                            <p class="text-[10px] text-slate-400 px-1">Maximum 500 characters. Markdown not supported.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label for="twitter_handle" class="block text-xs font-bold text-slate-500 uppercase tracking-widest px-1">Twitter Username</label>
                                <div class="relative">
                                    <input type="text" name="twitter_handle" id="twitter_handle" 
                                        value="<?php echo esc($user['twitter_handle']); ?>" 
                                        class="block w-full pl-10 pr-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 focus:bg-white transition-all text-slate-900 font-bold placeholder-slate-300" 
                                        placeholder="username">
                                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 font-bold">@</span>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label for="github_handle" class="block text-xs font-bold text-slate-500 uppercase tracking-widest px-1">GitHub Username</label>
                                <input type="text" name="github_handle" id="github_handle" 
                                    value="<?php echo esc($user['github_handle']); ?>" 
                                    class="block w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 focus:bg-white transition-all text-slate-900 font-bold placeholder-slate-300" 
                                    placeholder="your-github">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="website_url" class="block text-xs font-bold text-slate-500 uppercase tracking-widest px-1">Personal Website</label>
                            <input type="url" name="website_url" id="website_url" 
                                value="<?php echo esc($user['website_url']); ?>" 
                                class="block w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 focus:bg-white transition-all text-slate-900 font-bold placeholder-slate-300" 
                                placeholder="https://yourwebsite.com">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-12 py-5 bg-primary-600 text-white font-bold rounded-2xl hover:bg-primary-700 shadow-xl shadow-primary-600/20 transition-all transform active:scale-[0.98]">
                        Save Profile Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
