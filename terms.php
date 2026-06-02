<?php
require_once 'bootstrap.php';

$page_title = "Terms of Service";
$meta_description = "Read the terms of service for Atlas Library. Understand the guidelines for using our AI prompt management platform.";
$canonical_url = rtrim(Env::get('APP_URL', ''), '/') . '/terms.php';

$breadcrumbs = [
    ['name' => 'Home', 'url' => 'public_prompts.php'],
    ['name' => 'Terms of Service', 'url' => 'terms.php']
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
            <li class="text-slate-900">Terms of Service</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="mb-12">
        <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight mb-4">Terms of <span class="text-primary-600">Service</span></h1>
        <p class="text-slate-500 text-lg font-medium leading-relaxed">
            Please read these terms carefully before using Atlas Library. By accessing our platform, you agree to be bound by these guidelines.
        </p>
    </div>

    <!-- Main Content -->
    <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden mb-12">
        <div class="p-8 md:p-12">
            <div class="prose prose-slate prose-lg max-w-none">
                <div class="flex items-center gap-3 mb-8 pb-8 border-b border-slate-100">
                    <div class="w-10 h-10 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <p class="text-sm font-bold text-slate-400 uppercase tracking-widest !m-0">Agreement Overview</p>
                </div>

                <p class="text-slate-600 leading-relaxed">
                    By using Atlas Library, you agree to the following terms. These terms are designed to ensure a safe and productive environment for all prompt engineers and AI developers.
                </p>
                
                <h2 class="text-2xl font-bold text-slate-900 mt-12 mb-6 flex items-center gap-3">
                    <span class="flex-shrink-0 w-8 h-8 rounded-lg bg-slate-900 text-white flex items-center justify-center text-xs">1</span>
                    License & Open Source
                </h2>
                <div class="pl-11 border-l-2 border-slate-50">
                    <p class="text-slate-600 leading-relaxed mb-4">
                        Atlas Library is open-source software provided under the <strong>MIT License</strong>. You are free to use, modify, and distribute the software for personal or commercial use.
                    </p>
                    <p class="text-slate-600 leading-relaxed italic text-sm">
                        * Note: If you are using our hosted demo, specific usage limits may apply to preserve server resources.
                    </p>
                </div>

                <h2 class="text-2xl font-bold text-slate-900 mt-12 mb-6 flex items-center gap-3">
                    <span class="flex-shrink-0 w-8 h-8 rounded-lg bg-slate-900 text-white flex items-center justify-center text-xs">2</span>
                    User Responsibility
                </h2>
                <div class="pl-11 border-l-2 border-slate-50">
                    <p class="text-slate-600 leading-relaxed mb-4">
                        You are solely responsible for the content you store and share in your instance. 
                    </p>
                    <ul class="list-disc space-y-2 text-slate-600 mb-4">
                        <li>Ensure your prompts comply with the guidelines of the AI tools you use (e.g., OpenAI, Anthropic).</li>
                        <li>Do not use the platform to store or generate malicious or prohibited content.</li>
                        <li>Respect the intellectual property of others when copying public prompts.</li>
                    </ul>
                </div>

                <h2 class="text-2xl font-bold text-slate-900 mt-12 mb-6 flex items-center gap-3">
                    <span class="flex-shrink-0 w-8 h-8 rounded-lg bg-slate-900 text-white flex items-center justify-center text-xs">3</span>
                    No Warranty
                </h2>
                <div class="pl-11 border-l-2 border-slate-50">
                    <p class="text-slate-600 leading-relaxed">
                        The software is provided "as is", without warranty of any kind, express or implied. The developers of Atlas Library are not responsible for any data loss or issues resulting from the use of the platform.
                    </p>
                </div>
                
                <div class="mt-20 pt-8 border-t border-slate-100 flex flex-col md:flex-row justify-between items-center gap-4">
                    <p class="text-sm text-slate-400 font-medium">Last updated: June 2, 2026</p>
                    <a href="mailto:support@egyitech.com" class="text-xs font-bold uppercase tracking-widest text-primary-600 hover:text-primary-700 transition-colors">Questions? Contact Support</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
