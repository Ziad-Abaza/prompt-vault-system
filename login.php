<?php
require_once 'bootstrap.php';

if (is_logged_in()) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (login($username, $password)) {
        set_flash('Welcome back to your vault!');
        redirect('index.php');
    } else {
        $error = 'Invalid username or password.';
    }
}

include 'includes/header.php';
?>

<div class="max-w-md mx-auto py-12 md:py-24">
    <div class="text-center mb-10">
        <div class="w-20 h-20 bg-primary-600 text-white rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-xl shadow-primary-600/20">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
        </div>
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-2">Access Your Vault</h1>
        <p class="text-slate-500 font-medium">Please enter your credentials to continue.</p>
    </div>

    <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200 overflow-hidden">
        <div class="p-8 md:p-10">
            <?php if (isset($error)): ?>
                <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-100 text-red-700 text-sm font-bold">
                    <?php echo esc($error); ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST" class="space-y-6">
                <?php echo csrf_input(); ?>
                <div>
                    <label for="username" class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 px-1">Username</label>
                    <input type="text" name="username" id="username" required autofocus 
                        class="block w-full px-5 py-4 bg-slate-50 border-slate-100 rounded-2xl focus:ring-primary-500 focus:bg-white text-slate-900 font-semibold transition-all" 
                        placeholder="yourname">
                </div>
                <div>
                    <label for="password" class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 px-1">Password</label>
                    <input type="password" name="password" id="password" required 
                        class="block w-full px-5 py-4 bg-slate-50 border-slate-100 rounded-2xl focus:ring-primary-500 focus:bg-white text-slate-900 font-semibold transition-all" 
                        placeholder="••••••••">
                </div>

                <button type="submit" class="w-full flex justify-center py-5 px-4 border border-transparent rounded-2xl shadow-lg shadow-primary-600/20 text-lg font-bold text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all">
                    Sign In
                </button>
            </form>
        </div>
        
        <div class="px-8 py-6 bg-slate-50 border-t border-slate-100 text-center">
            <p class="text-slate-500 text-sm font-medium">
                New here? <a href="register.php" class="text-primary-600 font-bold hover:text-primary-700">Create an account</a>
            </p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
