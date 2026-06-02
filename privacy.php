<?php
require_once 'bootstrap.php';

$page_title = "Privacy Policy";
$meta_description = "Read the privacy policy for Atlas Library. We are committed to protecting your data and ensuring complete privacy for your prompt engineering workspace.";
$canonical_url = rtrim(Env::get('APP_URL', ''), '/') . '/privacy.php';

$breadcrumbs = [
    ['name' => 'Home', 'url' => 'public_prompts.php'],
    ['name' => 'Privacy', 'url' => 'privacy.php']
];

include 'includes/header.php';
?>

<div class="max-w-4xl mx-auto py-12 px-4 sm:px-6">
    <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight mb-8">Privacy Policy</h1>
    
    <div class="prose prose-slate prose-lg max-w-none text-slate-600">
        <p class="mb-6">At Atlas Library, we take your privacy seriously. This document outlines how we handle your data.</p>
        
        <h2 class="text-2xl font-bold text-slate-900 mt-8 mb-4">1. Data Ownership</h2>
        <p class="mb-6">You own 100% of the prompts you create. If you host your own instance of Atlas Library, your data never leaves your server.</p>

        <h2 class="text-2xl font-bold text-slate-900 mt-8 mb-4">2. Security</h2>
        <p class="mb-6">We use industry-standard security practices, including salted password hashing (Bcrypt) and CSRF protection, to keep your workspace safe.</p>

        <h2 class="text-2xl font-bold text-slate-900 mt-8 mb-4">3. Cookies</h2>
        <p class="mb-6">We use essential session cookies only to keep you logged into your private vault. We do not use tracking or third-party advertising cookies.</p>
        
        <p class="mt-12 text-sm text-slate-400">Last updated: June 2, 2026</p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
