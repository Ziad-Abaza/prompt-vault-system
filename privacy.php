<?php
require_once 'bootstrap.php';

$page_title = "Privacy Policy";
$meta_description = "Read the privacy policy for Atlas Library. We are committed to protecting your data and ensuring complete privacy for your prompt engineering workspace.";
$canonical_url = rtrim(Env::get('APP_URL', ''), '/') . '/privacy.php';

$breadcrumbs = [
    ['name' => 'Home', 'url' => 'public_prompts.php'],
    ['name' => 'Privacy Policy', 'url' => 'privacy.php']
];

include 'includes/header.php';
?>

<div class="max-w-4xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2 text-[10px] font-bold uppercase tracking-widest">
            <li>
                <a href="public_prompts.php" class="text-slate-400 hover:text-primary-600 transition-colors">Home</a>
            </li>
            <li>
                <svg class="h-3 w-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
                </svg>
            </li>
            <li class="text-slate-900">Privacy Policy</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="mb-12">
        <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight mb-4">Privacy <span class="text-primary-600">Policy</span></h1>
        <p class="text-slate-500 text-lg font-medium leading-relaxed">
            Your privacy is our priority. This document outlines how we handle your data and our commitment to your security.
        </p>
    </div>

    <!-- Main Content -->
    <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden mb-12">
        <div class="p-8 md:p-12">
            <div class="prose prose-slate prose-lg max-w-none">
                <div class="flex items-center gap-3 mb-8 pb-8 border-b border-slate-100">
                    <div class="w-10 h-10 rounded-xl bg-green-50 text-green-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <p class="text-sm font-bold text-slate-400 uppercase tracking-widest !m-0">Privacy Overview</p>
                </div>

                <p class="text-slate-600 leading-relaxed">
                    At Atlas Library, we take your privacy seriously. We believe in complete transparency and user data sovereignty.
                </p>
                
                <h2 class="text-2xl font-bold text-slate-900 mt-12 mb-6 flex items-center gap-3">
                    <span class="flex-shrink-0 w-8 h-8 rounded-lg bg-slate-900 text-white flex items-center justify-center text-xs">1</span>
                    Data Ownership
                </h2>
                <div class="pl-11 border-l-2 border-slate-50">
                    <p class="text-slate-600 leading-relaxed">
                        You own 100% of the prompts you create. If you host your own instance of Atlas Library, your data never leaves your server. On our demo instance, we do not access or analyze your private prompts.
                    </p>
                </div>

                <h2 class="text-2xl font-bold text-slate-900 mt-12 mb-6 flex items-center gap-3">
                    <span class="flex-shrink-0 w-8 h-8 rounded-lg bg-slate-900 text-white flex items-center justify-center text-xs">2</span>
                    Security Standards
                </h2>
                <div class="pl-11 border-l-2 border-slate-50">
                    <p class="text-slate-600 leading-relaxed mb-4">
                        We use industry-standard security practices to keep your workspace safe:
                    </p>
                    <ul class="list-disc space-y-2 text-slate-600">
                        <li>Salted password hashing (Bcrypt)</li>
                        <li>CSRF protection on all destructive actions</li>
                        <li>Secure session management</li>
                        <li>Regular dependency updates</li>
                    </ul>
                </div>

                <h2 class="text-2xl font-bold text-slate-900 mt-12 mb-6 flex items-center gap-3">
                    <span class="flex-shrink-0 w-8 h-8 rounded-lg bg-slate-900 text-white flex items-center justify-center text-xs">3</span>
                    Cookies & Tracking
                </h2>
                <div class="pl-11 border-l-2 border-slate-50">
                    <p class="text-slate-600 leading-relaxed">
                        We use essential session cookies only to keep you logged into your private vault. We do not use third-party tracking, advertising cookies, or data-sharing pixels.
                    </p>
                </div>
                
                <div class="mt-20 pt-8 border-t border-slate-100 flex flex-col md:flex-row justify-between items-center gap-4">
                    <p class="text-sm text-slate-400 font-medium">Last updated: June 2, 2026</p>
                    <a href="mailto:privacy@egyitech.com" class="text-xs font-bold uppercase tracking-widest text-primary-600 hover:text-primary-700 transition-colors">Privacy Inquiries</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
