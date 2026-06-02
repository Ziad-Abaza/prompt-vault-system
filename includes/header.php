<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="google-site-verification" content="w-9EvKmwgBcTyx95BKBaJO3MeRVoBRLpPUvskDITbzA" />

    <?php
    $final_title = (isset($page_title) ? $page_title . " | " : "") . APP_NAME;
    $default_desc = "Atlas Library - The best tool to store, save, and manage AI prompts. A searchable database for ChatGPT prompts, prompt engineering, and LLM workflow management.";
    $final_desc = $meta_description ?? $default_desc;
    $keywords = "how to organize AI prompts, how to save and manage prompts for ChatGPT, best tool to store AI prompts, searchable database for AI prompts, create your own prompt library, organize prompts for ChatGPT and AI tools, AI prompt management system for developers, cloud based prompt storage system, share and reuse AI prompts easily, centralized AI prompt workspace, AI prompt library, prompt library, prompt database, AI prompts collection, save AI prompts, prompt management tool, prompt organizer, AI prompt manager, prompt storage system, searchable prompt library, Atlas AI prompt library, Atlas prompt manager, Atlas prompt database, Atlas AI workspace, Atlas prompt hub, prompt engineering tools, prompt engineering library, LLM prompt management, AI workflow prompt system, prompt versioning system, prompt engineering platform, structured prompt database, reusable AI prompts system, prompt API management, AI prompt optimization tool";
    
    // Canonical URL Logic
    if (!isset($canonical_url)) {
        $canonical_url = APP_URL_BASE . $_SERVER['REQUEST_URI'];
    }
    ?>

    <title><?php echo esc($final_title); ?></title>
    <meta name="description" content="<?php echo esc($final_desc); ?>">
    <meta name="keywords" content="<?php echo esc($keywords); ?>">
    <link rel="canonical" href="<?php echo esc($canonical_url); ?>">
    <?php if (isset($prev_page_url)): ?>
        <link rel="prev" href="<?php echo esc($prev_page_url); ?>">
    <?php endif; ?>
    <?php if (isset($next_page_url)): ?>
        <link rel="next" href="<?php echo esc($next_page_url); ?>">
    <?php endif; ?>
    <link rel="manifest" href="<?php echo APP_URL_BASE; ?>/site.webmanifest">
    <meta name="theme-color" content="#0e91e9">
    <meta name="csrf-token" content="<?php echo csrf_token(); ?>">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="<?php echo $og_type ?? 'website'; ?>">
    <meta property="og:url" content="<?php echo esc($canonical_url); ?>">
    <meta property="og:title" content="<?php echo esc($final_title); ?>">
    <meta property="og:description" content="<?php echo esc($final_desc); ?>">
    <meta property="og:site_name" content="<?php echo APP_NAME; ?>">
    <?php 
    $og_image = $og_image ?? 'assets/logo.png';
    // Ensure absolute URL for OG image
    if (!str_starts_with($og_image, 'http')) {
        $og_image = APP_URL_BASE . '/' . ltrim($og_image, '/');
    }
    ?>
    <meta property="og:image" content="<?php echo esc($og_image); ?>">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php echo esc($canonical_url); ?>">
    <meta property="twitter:title" content="<?php echo esc($final_title); ?>">
    <meta property="twitter:description" content="<?php echo esc($final_desc); ?>">
    <meta property="twitter:image" content="<?php echo esc($og_image); ?>">

    <!-- Structured Data -->
    <script type="application/ld+json">
        <?php
        $schema = [
            "@context" => "https://schema.org",
            "@graph" => [
                [
                    "@type" => "WebSite",
                    "@id" => APP_URL_BASE . "/#website",
                    "name" => APP_NAME,
                    "url" => APP_URL_BASE . "/",
                    "potentialAction" => [
                        "@type" => "SearchAction",
                        "target" => APP_URL_BASE . "/public_prompts.php?search={search_term_string}",
                        "query-input" => "required name=search_term_string"
                    ]
                ],
                [
                    "@type" => "Organization",
                    "@id" => APP_URL_BASE . "/#organization",
                    "name" => APP_NAME,
                    "url" => APP_URL_BASE . "/",
                    "logo" => [
                        "@type" => "ImageObject",
                        "url" => APP_URL_BASE . "/assets/logo.png"
                    ]
                ]
            ]
        ];

        if (isset($breadcrumbs) && is_array($breadcrumbs)) {
            $breadcrumbList = [
                "@type" => "BreadcrumbList",
                "itemListElement" => []
            ];

            foreach ($breadcrumbs as $i => $bc) {
                $item_url = $bc['url'];
                if (!str_starts_with($item_url, 'http')) {
                    $item_url = APP_URL_BASE . '/' . ltrim($item_url, '/');
                }
                
                $breadcrumbList['itemListElement'][] = [
                    "@type" => "ListItem",
                    "position" => $i + 1,
                    "name" => $bc['name'],
                    "item" => $item_url
                ];
            }
            $schema['@graph'][] = $breadcrumbList;
        }

        // Add page-specific schema if defined
        if (isset($page_schema) && is_array($page_schema)) {
            $schema['@graph'][] = $page_schema;
        }

        echo json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        ?>
    </script>

    <!-- Tailwind CSS Play CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    colors: {
                        primary: {
                            50: '#f0f7ff',
                            100: '#e0effe',
                            200: '#bae0fd',
                            300: '#7cc8fb',
                            400: '#38acf7',
                            500: '#0e91e9',
                            600: '#0274c7',
                            700: '#035ca1',
                            800: '#074e85',
                            900: '#0c426e',
                        },
                        surface: '#f8fafc',
                    }
                }
            },
            plugins: [
                // Tailwind Play CDN includes typography by default if we use the prose classes,
                // but we can explicitly define options here if needed.
            ]
        }
    </script>
    <style type="text/tailwindcss">
        [x-cloak] { display: none !important; }
        
        /* Form Design System */
        @layer components {
            .form-section {
                @apply bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden mb-8;
            }
            .form-section-header {
                @apply px-8 py-5 border-b border-slate-50 bg-slate-50/50 flex justify-between items-center;
            }
            .form-section-title {
                @apply text-sm font-bold text-slate-900 uppercase tracking-widest;
            }
            .form-body {
                @apply p-8 space-y-8;
            }
            .form-group {
                @apply space-y-2;
            }
            .form-label {
                @apply block text-xs font-bold text-slate-500 uppercase tracking-widest px-1 transition-colors;
            }
            .form-group:focus-within .form-label {
                @apply text-primary-600;
            }
            .form-input {
                @apply block w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 focus:bg-white transition-all text-slate-900 font-medium placeholder-slate-300;
            }
            .form-textarea {
                @apply block w-full px-6 py-6 bg-slate-50 border border-slate-100 rounded-3xl focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 focus:bg-white font-mono text-slate-800 leading-relaxed transition-all placeholder-slate-300 resize-y;
            }
            .form-select {
                @apply block w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 focus:bg-white transition-all text-slate-900 font-semibold appearance-none cursor-pointer;
            }
            .form-error {
                @apply text-red-500 text-xs font-bold mt-2 px-1 flex items-center;
            }
            .form-error::before {
                content: "!";
                @apply inline-flex items-center justify-center w-4 h-4 rounded-full bg-red-100 text-red-600 mr-2 text-[10px];
            }
            
            /* Buttons */
            .btn-primary {
                @apply px-10 py-4 bg-primary-600 text-white font-bold rounded-2xl hover:bg-primary-700 shadow-lg shadow-primary-600/20 transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transform active:scale-[0.98];
            }
            .btn-secondary {
                @apply px-10 py-4 bg-white border border-slate-200 text-slate-600 font-bold rounded-2xl hover:bg-slate-50 hover:text-slate-900 transition-all transform active:scale-[0.98];
            }
            .btn-danger-link {
                @apply text-red-500 hover:text-red-700 text-xs font-bold uppercase tracking-widest transition-colors;
            }

            /* Custom Toggle Switch */
            input:checked ~ .dot {
                @apply translate-x-4;
            }
            input:checked ~ div:first-of-type {
                @apply bg-primary-600;
            }
        }

        /* Utilities */
        @layer utilities {
            .line-clamp-2 {
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;  
                overflow: hidden;
            }
        }
    </style>
</head>

<body class="bg-surface text-slate-900 font-sans antialiased min-h-screen flex flex-col md:flex-row">

    <!-- Mobile Header -->
    <header class="md:hidden bg-white border-b border-slate-200 px-4 py-3 flex items-center justify-between sticky top-0 z-50">
        <a href="index.php" class="text-xl font-bold text-primary-600 tracking-tight"><?php echo APP_NAME; ?></a>
        <button id="mobile-menu-toggle" class="p-2 text-slate-500 hover:text-slate-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
            </svg>
        </button>
    </header>

    <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-56 bg-white border-r border-slate-200 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out md:sticky md:top-0 h-screen overflow-y-auto">
        <div class="p-4">
            <a href="<?php echo APP_URL_BASE; ?>/index.php" class="text-xl font-bold text-primary-600 tracking-tight block mb-6 px-2"><?php echo APP_NAME; ?></a>

            <nav class="space-y-0.5">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 px-2">Discovery</p>
                <a href="<?php echo APP_URL_BASE; ?>/index.php" class="flex items-center px-2 py-1.5 text-xs font-bold rounded-lg <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'bg-primary-50 text-primary-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'; ?>">
                    <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V10"></path>
                    </svg>
                    Home
                </a>
                <a href="<?php echo APP_URL_BASE; ?>/public_prompts.php" class="flex items-center px-2 py-1.5 text-xs font-bold rounded-lg <?php echo basename($_SERVER['PHP_SELF']) == 'public_prompts.php' ? 'bg-primary-50 text-primary-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'; ?>">
                    <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                    </svg>
                    Explore Hub
                </a>
                <a href="<?php echo APP_URL_BASE; ?>/search.php" class="flex items-center px-2 py-1.5 text-xs font-bold rounded-lg <?php echo basename($_SERVER['PHP_SELF']) == 'search.php' ? 'bg-primary-50 text-primary-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'; ?>">
                    <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Search
                </a>

                <?php if (is_logged_in()): ?>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-6 mb-1 px-2">Private Vault</p>
                    <a href="<?php echo APP_URL_BASE; ?>/dashboard.php" class="flex items-center px-2 py-1.5 text-xs font-bold rounded-lg <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'bg-primary-50 text-primary-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'; ?>">
                        <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 4v12l-4-2-4 2V4M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        My Prompts
                    </a>
                    <a href="<?php echo APP_URL_BASE; ?>/favorites.php" class="flex items-center px-2 py-1.5 text-xs font-bold rounded-lg <?php echo basename($_SERVER['PHP_SELF']) == 'favorites.php' ? 'bg-primary-50 text-primary-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'; ?>">
                        <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        Favorites
                    </a>
                    <a href="<?php echo APP_URL_BASE; ?>/following.php" class="flex items-center px-2 py-1.5 text-xs font-bold rounded-lg <?php echo basename($_SERVER['PHP_SELF']) == 'following.php' ? 'bg-primary-50 text-primary-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'; ?>">
                        <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Following
                    </a>
                    <a href="<?php echo APP_URL_BASE; ?>/categories.php" class="flex items-center px-2 py-1.5 text-xs font-bold rounded-lg <?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'bg-primary-50 text-primary-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'; ?>">
                        <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                        Categories
                    </a>
                    <a href="<?php echo APP_URL_BASE; ?>/tags.php" class="flex items-center px-2 py-1.5 text-xs font-bold rounded-lg <?php echo basename($_SERVER['PHP_SELF']) == 'tags.php' ? 'bg-primary-50 text-primary-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'; ?>">
                        <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        Tags
                    </a>
                    <a href="<?php echo APP_URL_BASE; ?>/collections.php" class="flex items-center px-2 py-1.5 text-xs font-bold rounded-lg <?php echo basename($_SERVER['PHP_SELF']) == 'collections.php' ? 'bg-primary-50 text-primary-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'; ?>">
                        <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        Collections
                    </a>
                    <a href="<?php echo APP_URL_BASE; ?>/settings.php" class="flex items-center px-2 py-1.5 text-xs font-bold rounded-lg <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'bg-primary-50 text-primary-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'; ?>">
                        <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Settings
                    </a>

                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-6 mb-1 px-2">Data</p>
                    <a href="<?php echo APP_URL_BASE; ?>/export.php" class="flex items-center px-2 py-1.5 text-xs font-bold text-slate-600 rounded-lg hover:bg-slate-50 hover:text-slate-900">
                        <svg class="w-4 h-4 mr-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Export
                    </a>
                    <a href="<?php echo APP_URL_BASE; ?>/import.php" class="flex items-center px-2 py-1.5 text-xs font-bold text-slate-600 rounded-lg hover:bg-slate-50 hover:text-slate-900">
                        <svg class="w-4 h-4 mr-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                        Import
                    </a>
                <?php endif; ?>
            </nav>
        </div>

        <div class="absolute bottom-0 w-full p-4 border-t border-slate-100 bg-white">
            <?php if (is_logged_in()): ?>
                <div class="flex items-center mb-2 px-2">
                    <div class="w-6 h-6 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center text-[10px] font-bold mr-2">
                        <?php echo strtoupper(substr(get_current_username(), 0, 1)); ?>
                    </div>
                    <div class="flex-grow overflow-hidden">
                        <p class="text-xs font-bold text-slate-900 truncate"><?php echo esc(get_current_username()); ?></p>
                    </div>
                </div>
                <a href="<?php echo APP_URL_BASE; ?>/logout.php" class="flex items-center px-2 py-1.5 text-xs font-bold text-red-600 rounded-lg hover:bg-red-50">
                    <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    Logout
                </a>
            <?php else: ?>
                <div class="flex flex-col gap-1 px-2">
                    <a href="<?php echo APP_URL_BASE; ?>/login.php" class="flex items-center py-1.5 text-xs font-bold text-primary-600 hover:text-primary-700">
                        Sign In
                    </a>
                    <a href="<?php echo APP_URL_BASE; ?>/register.php" class="flex items-center py-1.5 text-xs font-bold text-slate-600 hover:text-slate-900">
                        Create Account
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </aside>

    <!-- Content Wrapper -->
    <div class="flex-grow flex flex-col min-h-screen">
        <main class="flex-grow p-4 md:p-6 lg:p-8 max-w-7xl w-full mx-auto">
            <?php if ($flash = get_flash()): ?>
                <div class="mb-8 p-4 rounded-xl border <?php echo $flash['type'] === 'error' ? 'bg-red-50 border-red-100 text-red-700' : 'bg-green-50 border-green-100 text-green-700'; ?> flex items-center">
                    <?php if ($flash['type'] === 'error'): ?>
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    <?php else: ?>
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    <?php endif; ?>
                    <span class="text-sm font-medium"><?php echo esc($flash['message']); ?></span>
                </div>
            <?php endif; ?>
