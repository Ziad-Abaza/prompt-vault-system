<?php
require_once 'bootstrap.php';

if (is_logged_in()) {
    redirect('index.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $validator = new Validator($_POST);
    $validator->required('username', 'Username is required.')
              ->required('password', 'Password is required.');

    if ($validator->is_valid()) {
        if (login($_POST['username'], $_POST['password'])) {
            set_flash('Welcome back to your vault!');
            redirect('index.php');
        } else {
            $errors['form'] = 'Invalid username or password.';
        }
    } else {
        $errors = $validator->get_errors();
    }
}

include 'includes/header.php';
?>

<div class="max-w-md mx-auto py-12 md:py-24">
    <div class="text-center mb-10">
        <div class="w-20 h-20 bg-primary-600 text-white rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-xl shadow-primary-600/20 transition-transform hover:scale-105">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
        </div>
        <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight mb-2"><?php echo APP_NAME; ?> Login</h1>
        <p class="text-slate-500 font-medium">Welcome to your professional knowledge workspace.</p>
    </div>

    <div class="form-section shadow-2xl shadow-slate-200/60 border-primary-50">
        <div class="p-10">
            <?php if (isset($errors['form'])): ?>
                <div class="mb-8 p-4 rounded-2xl bg-red-50 border border-red-100 text-red-700 text-sm font-bold flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <?php echo esc($errors['form']); ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST" class="space-y-8">
                <?php echo csrf_input(); ?>
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" id="username" required autofocus 
                        value="<?php echo esc($_POST['username'] ?? ''); ?>"
                        class="form-input <?php echo isset($errors['username']) ? 'border-red-300 ring-4 ring-red-500/5 bg-red-50/30' : ''; ?>" 
                        placeholder="yourname">
                    <?php if (isset($errors['username'])): ?>
                        <p class="form-error"><?php echo esc($errors['username']); ?></p>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" required 
                        class="form-input <?php echo isset($errors['password']) ? 'border-red-300 ring-4 ring-red-500/5 bg-red-50/30' : ''; ?>" 
                        placeholder="••••••••">
                    <?php if (isset($errors['password'])): ?>
                        <p class="form-error"><?php echo esc($errors['password']); ?></p>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn-primary w-full py-5 text-lg">
                    Sign In to Vault
                </button>
            </form>
        </div>
        
        <div class="px-10 py-8 bg-slate-50 border-t border-slate-100 text-center">
            <p class="text-slate-500 text-sm font-medium">
                Need a private vault? <a href="register.php" class="text-primary-600 font-bold hover:text-primary-700 transition-colors underline decoration-primary-200 underline-offset-4 hover:decoration-primary-500">Create an account</a>
            </p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
