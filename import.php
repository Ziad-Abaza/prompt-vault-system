<?php
require_once 'bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['backup_file'])) {
    $file = $_FILES['backup_file'];
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        $json = file_get_contents($file['tmp_name']);
        $data = json_decode($json, true);

        if ($data) {
            $db = get_db();
            $user_id = get_current_user_id();
            $db->beginTransaction();

            try {
                // Clear existing data for CURRENT USER ONLY
                query("DELETE FROM prompt_tags WHERE prompt_id IN (SELECT id FROM prompts WHERE user_id = ?)", [$user_id]);
                query("DELETE FROM prompt_collections WHERE prompt_id IN (SELECT id FROM prompts WHERE user_id = ?)", [$user_id]);
                query("DELETE FROM prompts WHERE user_id = ?", [$user_id]);
                query("DELETE FROM categories WHERE user_id = ?", [$user_id]);
                query("DELETE FROM tags WHERE user_id = ?", [$user_id]);
                query("DELETE FROM collections WHERE user_id = ?", [$user_id]);

                $cat_map = [];
                $tag_map = [];
                $coll_map = [];
                $prompt_map = [];

                // Import Categories
                if (!empty($data['categories'])) {
                    foreach ($data['categories'] as $row) {
                        query("INSERT INTO categories (name, user_id) VALUES (?, ?)", [$row['name'], $user_id]);
                        $cat_map[$row['id']] = $db->lastInsertId();
                    }
                }

                // Import Tags
                if (!empty($data['tags'])) {
                    foreach ($data['tags'] as $row) {
                        query("INSERT INTO tags (name, user_id) VALUES (?, ?)", [$row['name'], $user_id]);
                        $tag_map[$row['id']] = $db->lastInsertId();
                    }
                }

                // Import Collections
                if (!empty($data['collections'])) {
                    foreach ($data['collections'] as $row) {
                        query("INSERT INTO collections (name, description, user_id) VALUES (?, ?, ?)", [$row['name'], $row['description'], $user_id]);
                        $coll_map[$row['id']] = $db->lastInsertId();
                    }
                }

                // Import Prompts
                if (!empty($data['prompts'])) {
                    foreach ($data['prompts'] as $row) {
                        $new_cat_id = isset($row['category_id']) ? ($cat_map[$row['category_id']] ?? null) : null;
                        query("INSERT INTO prompts (title, content, category_id, user_id, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)", [
                            $row['title'], $row['content'], $new_cat_id, $user_id, $row['created_at'], $row['updated_at']
                        ]);
                        $prompt_map[$row['id']] = $db->lastInsertId();
                    }
                }

                // Import Prompt Tags
                if (!empty($data['prompt_tags'])) {
                    foreach ($data['prompt_tags'] as $row) {
                        $new_prompt_id = $prompt_map[$row['prompt_id']] ?? null;
                        $new_tag_id = $tag_map[$row['tag_id']] ?? null;
                        if ($new_prompt_id && $new_tag_id) {
                            query("INSERT INTO prompt_tags (prompt_id, tag_id) VALUES (?, ?)", [$new_prompt_id, $new_tag_id]);
                        }
                    }
                }

                // Import Prompt Collections
                if (!empty($data['prompt_collections'])) {
                    foreach ($data['prompt_collections'] as $row) {
                        $new_prompt_id = $prompt_map[$row['prompt_id']] ?? null;
                        $new_coll_id = $coll_map[$row['collection_id']] ?? null;
                        if ($new_prompt_id && $new_coll_id) {
                            query("INSERT INTO prompt_collections (prompt_id, collection_id) VALUES (?, ?)", [$new_prompt_id, $new_coll_id]);
                        }
                    }
                }
                
                $db->commit();
                set_flash('Import successful. Your vault has been restored.');
                redirect('index.php');
            } catch (Exception $e) {
                if ($db->inTransaction()) {
                    $db->rollBack();
                }
                $error = 'Import failed: ' . $e->getMessage();
            }
        } else {
            $error = 'Invalid JSON file.';
        }
    } else {
        $error = 'File upload failed.';
    }
}

include 'includes/header.php';
?>

<div class="max-w-2xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Import Backup</h1>

    <div class="bg-white shadow sm:rounded-lg overflow-hidden border border-gray-200">
        <div class="px-4 py-8 sm:p-10">
            <div class="mb-6 p-4 bg-yellow-50 border-l-4 border-yellow-400 text-yellow-700">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm">
                            <strong>Warning:</strong> This will <strong>permanently delete</strong> your current prompts, categories, tags, and collections and replace them with the backup content. This action only affects your account.
                        </p>
                    </div>
                </div>
            </div>

            <?php if (isset($error)): ?>
                <div class="mb-4 p-4 rounded-md bg-red-50 text-red-700">
                    <?php echo esc($error); ?>
                </div>
            <?php endif; ?>

            <form action="import.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                <?php echo csrf_input(); ?>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Select JSON Backup File</label>
                    <div class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="backup_file" class="relative cursor-pointer bg-white rounded-md font-medium text-primary hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary">
                                    <span>Upload a file</span>
                                    <input id="backup_file" name="backup_file" type="file" class="sr-only" accept=".json" required>
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">JSON files only</p>
                        </div>
                    </div>
                </div>

                <div>
                    <button type="submit" onclick="return confirm('Are you absolutely sure? Your current vault data will be replaced.')" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        Start Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
