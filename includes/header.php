<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prompt Vault System</title>
    <!-- Tailwind CSS Play CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3b82f6',
                        secondary: '#64748b',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen flex flex-col">

    <nav class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="index.php" class="text-2xl font-bold text-primary">PromptVault</a>
                    </div>
                    <?php if (is_logged_in()): ?>
                    <div class="hidden sm:-my-px sm:ml-6 sm:flex sm:space-x-8">
                        <a href="index.php" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">Prompts</a>
                        <a href="categories.php" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">Categories</a>
                        <a href="tags.php" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">Tags</a>
                        <a href="collections.php" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">Collections</a>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="flex items-center space-x-4">
                    <?php if (is_logged_in()): ?>
                        <span class="text-gray-500 text-sm hidden md:inline">Hello, <strong><?php echo esc(get_current_username()); ?></strong></span>
                        <a href="search.php" class="text-gray-500 hover:text-gray-700">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </a>
                        <a href="export.php" class="text-gray-500 hover:text-gray-700 text-sm font-medium">Export</a>
                        <a href="import.php" class="text-gray-500 hover:text-gray-700 text-sm font-medium">Import</a>
                        <a href="logout.php" class="text-red-500 hover:text-red-700 text-sm font-medium">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="text-gray-500 hover:text-gray-700 text-sm font-medium">Sign In</a>
                        <a href="register.php" class="text-primary hover:text-blue-700 text-sm font-medium">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-grow max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 w-full">
        <?php if ($flash = get_flash()): ?>
            <div class="mb-4 p-4 rounded-md <?php echo $flash['type'] === 'error' ? 'bg-red-50 text-red-700' : 'bg-green-50 text-green-700'; ?>">
                <?php echo esc($flash['message']); ?>
            </div>
        <?php endif; ?>
