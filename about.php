<?php
require_once 'bootstrap.php';

$page_title = "About Atlas Library";
$meta_description = "Learn more about Atlas Library, the professional knowledge management platform for AI prompts. Discover our mission, features, and security practices.";
$canonical_url = APP_URL_BASE . '/about.php';

// Organization Schema
$page_schema = [
    "@type" => "Organization",
    "name" => APP_NAME,
    "url" => APP_URL_BASE . '/',
    "logo" => APP_URL_BASE . '/assets/logo.png',
    "description" => "A professional AI prompt management platform designed for teams and individuals to organize, categorize, and master their LLM workflows.",
    "sameAs" => [
        "https://github.com/egyitech/atlas-library"
    ],
    "contactPoint" => [
        [
            "@type" => "ContactPoint",
            "contactType" => "customer support",
            "email" => "support@egyitech.com",
            "url" => APP_URL_BASE . '/about.php'
        ]
    ]
];

$breadcrumbs = [
    ['name' => 'Home', 'url' => APP_URL_BASE . '/public_prompts.php'],
    ['name' => 'About', 'url' => APP_URL_BASE . '/about.php']
];

include 'includes/header.php';
?>

<div class="max-w-4xl mx-auto py-12 px-4 sm:px-6">
    <!-- Hero Section -->
    <div class="text-center mb-16">
        <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 tracking-tight mb-6">
            Master Your <span class="text-primary-600">Prompt Intelligence</span>
        </h1>
        <p class="text-lg md:text-xl text-slate-600 leading-relaxed max-w-2xl mx-auto">
            Atlas Library is a professional, production-ready knowledge management platform designed for storing, organizing, categorizing, and managing AI prompts.
        </p>
    </div>

    <!-- Core Mission -->
    <div class="prose prose-slate prose-lg max-w-none mb-16">
        <h2 class="text-2xl font-bold text-slate-900 mb-4">Our Mission</h2>
        <p class="text-slate-600 leading-relaxed mb-6">
            In the rapidly evolving landscape of Large Language Models (LLMs), prompt engineering has become a critical skill. However, many developers and teams still rely on scattered spreadsheets, notes, or chat histories to store their valuable prompts.
        </p>
        <p class="text-slate-600 leading-relaxed mb-6">
            <strong>Atlas Library</strong> was built to solve this fragmentation. Our mission is to provide a centralized, secure, and highly portable workspace where you can build, refine, and preserve your prompt library with the same rigor you apply to your source code.
        </p>
    </div>

    <!-- Key Pillars -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
        <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm">
            <div class="w-12 h-12 bg-primary-50 text-primary-600 rounded-2xl flex items-center justify-center mb-6">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-2">Organization</h3>
            <p class="text-sm text-slate-500 leading-relaxed">Advanced categorization, granular tagging, and logical collections to group prompts by workflow.</p>
        </div>
        <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm">
            <div class="w-12 h-12 bg-green-50 text-green-600 rounded-2xl flex items-center justify-center mb-6">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-2">Security</h3>
            <p class="text-sm text-slate-500 leading-relaxed">Built with production-grade security standards, including CSRF protection, SQL injection prevention, and secure sessions.</p>
        </div>
        <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm">
            <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center mb-6">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-2">Portability</h3>
            <p class="text-sm text-slate-500 leading-relaxed">Complete JSON-based import and export systems ensure you always own your data and can move it anywhere.</p>
        </div>
    </div>

    <!-- Who is it for -->
    <div class="bg-slate-900 rounded-[3rem] p-8 md:p-12 mb-16 text-white overflow-hidden relative">
        <div class="relative z-10">
            <h2 class="text-2xl font-bold mb-8">Who is it for?</h2>
            <div class="space-y-6">
                <div class="flex items-start">
                    <div class="mt-1 flex-shrink-0 w-5 h-5 bg-primary-500 rounded-full flex items-center justify-center text-[10px] font-bold">1</div>
                    <div class="ml-4">
                        <h4 class="font-bold text-lg">AI Developers</h4>
                        <p class="text-slate-400 text-sm">Store system prompts, few-shot examples, and model-specific instructions in a searchable format.</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="mt-1 flex-shrink-0 w-5 h-5 bg-primary-500 rounded-full flex items-center justify-center text-[10px] font-bold">2</div>
                    <div class="ml-4">
                        <h4 class="font-bold text-lg">Content Creators</h4>
                        <p class="text-slate-400 text-sm">Organize creative templates for blogging, social media, and video scripting across different AI tools.</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="mt-1 flex-shrink-0 w-5 h-5 bg-primary-500 rounded-full flex items-center justify-center text-[10px] font-bold">3</div>
                    <div class="ml-4">
                        <h4 class="font-bold text-lg">Productivity Hackers</h4>
                        <p class="text-slate-400 text-sm">Build personal "AI brains" to automate repetitive tasks and optimize daily workflows.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="absolute -bottom-20 -right-20 w-64 h-64 bg-primary-600/20 rounded-full blur-3xl"></div>
    </div>

    <!-- Contact & Trust -->
    <div class="text-center">
        <h2 class="text-2xl font-bold text-slate-900 mb-4">Trust & Transparency</h2>
        <p class="text-slate-600 mb-8 max-w-2xl mx-auto">
            Atlas Library is open-source and transparent. We don't hide behind complex cloud agreements; you can host your own instance and keep your prompts completely private.
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="mailto:support@egyitech.com" class="px-8 py-3 bg-primary-600 text-white font-bold rounded-xl hover:bg-primary-700 transition-all shadow-lg shadow-primary-600/20">Contact Support</a>
            <a href="https://github.com/egyitech/atlas-library" target="_blank" class="px-8 py-3 bg-white border border-slate-200 text-slate-700 font-bold rounded-xl hover:bg-slate-50 transition-all">View on GitHub</a>
        </div>
    </div>
</div>

<div class="bg-slate-50 py-12 border-t border-slate-100">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <div class="flex flex-wrap justify-center gap-8 text-xs font-bold uppercase tracking-widest text-slate-400">
            <a href="<?php echo APP_URL_BASE; ?>/privacy.php" class="hover:text-primary-600 transition-colors">Privacy Policy</a>
            <a href="<?php echo APP_URL_BASE; ?>/terms.php" class="hover:text-primary-600 transition-colors">Terms of Service</a>
            <a href="<?php echo APP_URL_BASE; ?>/about.php" class="text-slate-900">About Us</a>
        </div>
        <p class="mt-8 text-[10px] font-medium text-slate-400">
            &copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. Part of the EgyiTech ecosystem.
        </p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
