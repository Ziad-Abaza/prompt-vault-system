<?php
require_once 'bootstrap.php';

if (is_logged_in()) {
    redirect('index.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $validator = new Validator($_POST);
    $validator->required('username', 'Please choose a username.')
              ->min('username', 3, 'Username must be at least 3 characters.')
              ->required('password', 'A password is required.')
              ->min('password', 6, 'Password must be at least 6 characters.');

    if ($validator->is_valid()) {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if ($password !== $confirm_password) {
            $errors['confirm_password'] = 'Passwords do not match.';
        } else {
            try {
                if (register($username, $password)) {
                    set_flash('Account created! You can now sign in.');
                    redirect('login.php');
                }
            } catch (Exception $e) {
                $errors['form'] = $e->getMessage();
            }
        }
    } else {
        $errors = $validator->get_errors();
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
        <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight mb-2">Create Account</h1>
        <p class="text-slate-500 font-medium">Join the vault and secure your prompts.</p>
    </div>

    <div class="form-section shadow-xl shadow-slate-200/50">
        <div class="p-10">
            <?php if (isset($errors['form'])): ?>
                <div class="mb-8 p-4 rounded-xl bg-red-50 border border-red-100 text-red-700 text-sm font-bold flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <?php echo esc($errors['form']); ?>
                </div>
            <?php endif; ?>

            <form action="register.php" method="POST" class="space-y-6">
                <?php echo csrf_input(); ?>
                <div>
                    <label for="username" class="form-label px-1">Choose Username</label>
                    <input type="text" name="username" id="username" required autofocus 
                        value="<?php echo esc($_POST['username'] ?? ''); ?>"
                        class="form-input <?php echo isset($errors['username']) ? 'border-red-300 ring-red-100' : ''; ?>" 
                        placeholder="e.g. prompt_wizard">
                    <?php if (isset($errors['username'])): ?>
                        <p class="form-error"><?php echo esc($errors['username']); ?></p>
                    <?php endif; ?>
                </div>
                
                <div>
                    <label for="password" class="form-label px-1">Password</label>
                    <input type="password" name="password" id="password" required 
                        class="form-input <?php echo isset($errors['password']) ? 'border-red-300 ring-red-100' : ''; ?>" 
                        placeholder="min 6 characters">
                    <?php if (isset($errors['password'])): ?>
                        <p class="form-error"><?php echo esc($errors['password']); ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="confirm_password" class="form-label px-1">Confirm Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" required 
                        class="form-input <?php echo isset($errors['confirm_password']) ? 'border-red-300 ring-red-100' : ''; ?>" 
                        placeholder="repeat password">
                    <?php if (isset($errors['confirm_password'])): ?>
                        <p class="form-error"><?php echo esc($errors['confirm_password']); ?></p>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn-primary w-full py-4 pt-4">
                    Register My Vault
                </button>
            </form>
        </div>
        
        <div class="px-10 py-6 bg-slate-50 border-t border-slate-100 text-center">
            <p class="text-slate-500 text-sm font-medium">
                Already have an account? <a href="login.php" class="text-primary-600 font-bold hover:text-primary-700 transition-colors underline decoration-primary-200 underline-offset-4 hover:decoration-primary-500">Sign in instead</a>
            </p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
