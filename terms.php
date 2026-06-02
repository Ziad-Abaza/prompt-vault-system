<?php
require_once 'bootstrap.php';

$page_title = "Terms of Service";
$meta_description = "Read the terms of service for Atlas Library. Understand the guidelines for using our AI prompt management platform.";
$canonical_url = rtrim(Env::get('APP_URL', ''), '/') . '/terms.php';

$breadcrumbs = [
    ['name' => 'Home', 'url' => 'public_prompts.php'],
    ['name' => 'Terms', 'url' => 'terms.php']
];

include 'includes/header.php';
?>

<div class="max-w-4xl mx-auto py-12 px-4 sm:px-6">
    <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight mb-8">Terms of Service</h1>
    
    <div class="prose prose-slate prose-lg max-w-none text-slate-600">
        <p class="mb-6">By using Atlas Library, you agree to the following terms.</p>
        
        <h2 class="text-2xl font-bold text-slate-900 mt-8 mb-4">1. License</h2>
        <p class="mb-6">Atlas Library is open-source software provided under the MIT License. You are free to use, modify, and distribute the software for personal or commercial use.</p>

        <h2 class="text-2xl font-bold text-slate-900 mt-8 mb-4">2. Responsibility</h2>
        <p class="mb-6">You are solely responsible for the content you store in your instance. Ensure your prompts comply with the guidelines of the AI tools you use (e.g., OpenAI, Anthropic).</p>

        <h2 class="text-2xl font-bold text-slate-900 mt-8 mb-4">3. No Warranty</h2>
        <p class="mb-6">The software is provided "as is", without warranty of any kind, express or implied.</p>
        
        <p class="mt-12 text-sm text-slate-400">Last updated: June 2, 2026</p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
