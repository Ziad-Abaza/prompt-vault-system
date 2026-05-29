<?php
/**
 * Centralized Error Page
 */

// We don't require_once 'bootstrap.php' here because bootstrap calls require_login()
// which might cause a redirect loop if the error happens during auth.
// Instead, we manually load what we need for a stand-alone look.

require_once __DIR__ . '/includes/env.php';
Env::load(__DIR__ . '/.env');
define('APP_NAME', Env::get('APP_NAME', 'Atlas Library'));

function esc($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

$code = $_GET['code'] ?? $_SERVER['REDIRECT_STATUS'] ?? http_response_code();
if ($code < 400 || $code > 599) $code = 404; // Default to 404 if accessed directly or invalid

$errors = [
    400 => [
        'title' => 'Bad Request',
        'message' => 'The server could not understand the request due to invalid syntax.',
        'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'
    ],
    403 => [
        'title' => 'Access Denied',
        'message' => 'You do not have permission to access this resource. This area is restricted.',
        'icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z'
    ],
    404 => [
        'title' => 'Page Not Found',
        'message' => 'The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.',
        'icon' => 'M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
    ],
    405 => [
        'title' => 'Method Not Allowed',
        'message' => 'The method specified in the Request-Line is not allowed for the resource identified by the Request-URI.',
        'icon' => 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636'
    ],
    500 => [
        'title' => 'Internal Server Error',
        'message' => 'The server encountered an internal error or misconfiguration and was unable to complete your request.',
        'icon' => 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4'
    ]
];

$error = $errors[$code] ?? [
    'title' => 'Unexpected Error',
    'message' => 'An unexpected error occurred. Please try again later.',
    'icon' => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
];

$page_title = $code . ' ' . $error['title'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc($page_title); ?> | <?php echo esc(APP_NAME); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased">
    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="max-w-md w-full text-center">
            <div class="mb-8 inline-flex items-center justify-center w-24 h-24 rounded-3xl bg-white shadow-xl shadow-slate-200/50 border border-slate-100 text-primary-600">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="<?php echo $error['icon']; ?>"></path>
                </svg>
            </div>
            
            <h1 class="text-7xl font-black text-slate-200 mb-2"><?php echo $code; ?></h1>
            <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-4"><?php echo esc($error['title']); ?></h2>
            <p class="text-slate-500 font-medium leading-relaxed mb-10 text-balance">
                <?php echo esc($error['message']); ?>
            </p>

            <div class="space-y-3">
                <a href="index.php" class="block w-full py-4 px-6 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl shadow-lg shadow-blue-600/20 transition-all transform active:scale-[0.98]">
                    Back to Library
                </a>
                <button onclick="window.history.back()" class="block w-full py-4 px-6 bg-white border border-slate-200 text-slate-600 font-bold rounded-2xl hover:bg-slate-50 transition-all transform active:scale-[0.98]">
                    Go Back
                </button>
            </div>

            <p class="mt-12 text-xs font-bold text-slate-400 uppercase tracking-widest">
                &copy; <?php echo date('Y'); ?> <?php echo esc(APP_NAME); ?> Security Protocol
            </p>
        </div>
    </div>
</body>
</html>
