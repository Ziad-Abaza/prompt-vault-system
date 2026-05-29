<?php
require_once 'bootstrap.php';

if (is_logged_in()) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        try {
            if (register($username, $password)) {
                set_flash('Account created! You can now sign in.');
                redirect('login.php');
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

include 'includes/header.php';
?>

<div class="max-w-md mx-auto py-12 md:py-24">
    <div class="text-center mb-10">
        <div class="w-20 h-20 bg-primary-600 text-white rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-xl shadow-primary-600/20">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
            </svg>
        </div>
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-2">Join the Vault</h1>
        <p class="text-slate-500 font-medium">Create your private workspace today.</p>
    </div>

    <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200 overflow-hidden">
        <div class="p-8 md:p-10">
            <?php if (isset($error)): ?>
                <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-100 text-red-700 text-sm font-bold">
                    <?php echo esc($error); ?>
                </div>
            <?php endif; ?>

            <form action="register.php" method="POST" class="space-y-6">
                <?php echo csrf_input(); ?>
                <div>
                    <label for="username" class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 px-1">Choose Username</label>
                    <input type="text" name="username" id="username" required autofocus 
                        class="block w-full px-5 py-4 bg-slate-50 border-slate-100 rounded-2xl focus:ring-primary-500 focus:bg-white text-slate-900 font-semibold transition-all" 
                        placeholder="yourname">
                </div>
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 px-1">Password</label>
                        <input type="password" name="password" id="password" required 
                            class="block w-full px-5 py-4 bg-slate-50 border-slate-100 rounded-2xl focus:ring-primary-500 focus:bg-white text-slate-900 font-semibold transition-all" 
                            placeholder="min 6 chars">
                    </div>
                    <div>
                        <label for="confirm_password" class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 px-1">Confirm Password</label>
                        <input type="password" name="confirm_password" id="confirm_password" required 
                            class="block w-full px-5 py-4 bg-slate-50 border-slate-100 rounded-2xl focus:ring-primary-500 focus:bg-white text-slate-900 font-semibold transition-all" 
                            placeholder="match password">
                    </div>
                </div>

                <button type="submit" class="w-full flex justify-center py-5 px-4 border border-transparent rounded-2xl shadow-lg shadow-primary-600/20 text-lg font-bold text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all">
                    Register Account
                </button>
            </form>
        </div>
        
        <div class="px-8 py-6 bg-slate-50 border-t border-slate-100 text-center">
            <p class="text-slate-500 text-sm font-medium">
                Already have an account? <a href="login.php" class="text-primary-600 font-bold hover:text-primary-700">Sign in</a>
            </p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
