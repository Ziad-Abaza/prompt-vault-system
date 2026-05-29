<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?></title>
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

    <!-- Sidebar Navigation -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-64 bg-white border-r border-slate-200 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out md:sticky md:top-0 h-screen overflow-y-auto">
        <div class="p-6">
            <a href="index.php" class="text-2xl font-bold text-primary-600 tracking-tight block mb-8"><?php echo APP_NAME; ?></a>
            
            <nav class="space-y-1">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2 px-3">Main</p>
                <a href="index.php" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'bg-primary-50 text-primary-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'; ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Prompts
                </a>
                <a href="search.php" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg <?php echo basename($_SERVER['PHP_SELF']) == 'search.php' ? 'bg-primary-50 text-primary-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'; ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Search
                </a>
                
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mt-8 mb-2 px-3">Organize</p>
                <a href="categories.php" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg <?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'bg-primary-50 text-primary-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'; ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                    </svg>
                    Categories
                </a>
                <a href="tags.php" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg <?php echo basename($_SERVER['PHP_SELF']) == 'tags.php' ? 'bg-primary-50 text-primary-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'; ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    Tags
                </a>
                <a href="collections.php" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg <?php echo basename($_SERVER['PHP_SELF']) == 'collections.php' ? 'bg-primary-50 text-primary-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'; ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    Collections
                </a>

                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mt-8 mb-2 px-3">Data</p>
                <a href="export.php" class="flex items-center px-3 py-2 text-sm font-medium text-slate-600 rounded-lg hover:bg-slate-50 hover:text-slate-900">
                    <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Export
                </a>
                <a href="import.php" class="flex items-center px-3 py-2 text-sm font-medium text-slate-600 rounded-lg hover:bg-slate-50 hover:text-slate-900">
                    <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    Import
                </a>
            </nav>
        </div>

        <div class="absolute bottom-0 w-full p-6 border-t border-slate-100 bg-white">
            <?php if (is_logged_in()): ?>
                <div class="flex items-center mb-4">
                    <div class="w-8 h-8 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center text-xs font-bold mr-3">
                        <?php echo strtoupper(substr(get_current_username(), 0, 1)); ?>
                    </div>
                    <div class="flex-grow overflow-hidden">
                        <p class="text-sm font-semibold text-slate-900 truncate"><?php echo esc(get_current_username()); ?></p>
                    </div>
                </div>
                <a href="logout.php" class="flex items-center px-3 py-2 text-sm font-medium text-red-600 rounded-lg hover:bg-red-50">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    Logout
                </a>
            <?php else: ?>
                <a href="login.php" class="flex items-center px-3 py-2 text-sm font-medium text-primary-600 rounded-lg hover:bg-primary-50">
                    Sign In
                </a>
            <?php endif; ?>
        </div>
    </aside>

    <!-- Content Wrapper -->
    <div class="flex-grow flex flex-col min-h-screen">
        <main class="flex-grow p-4 md:p-8 lg:p-12 max-w-6xl w-full mx-auto">
            <?php if ($flash = get_flash()): ?>
                <div class="mb-8 p-4 rounded-xl border <?php echo $flash['type'] === 'error' ? 'bg-red-50 border-red-100 text-red-700' : 'bg-green-50 border-green-100 text-green-700'; ?> flex items-center">
                    <?php if ($flash['type'] === 'error'): ?>
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <?php else: ?>
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <?php endif; ?>
                    <span class="text-sm font-medium"><?php echo esc($flash['message']); ?></span>
                </div>
            <?php endif; ?>
