<?php
require_once 'bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['backup_file'])) {
    $file = $_FILES['backup_file'];
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        $json = file_get_contents($file['tmp_name']);
        $data = json_decode($json, true);

        if ($data) {
            $db = get_db();
            $db->beginTransaction();

            try {
                // Disable foreign key checks for the import
                $db->exec('PRAGMA foreign_keys = OFF;');

                // Clear existing data
                $db->exec('DELETE FROM prompt_tags');
                $db->exec('DELETE FROM prompt_collections');
                $db->exec('DELETE FROM prompts');
                $db->exec('DELETE FROM categories');
                $db->exec('DELETE FROM tags');
                $db->exec('DELETE FROM collections');

                // Import Categories
                if (!empty($data['categories'])) {
                    foreach ($data['categories'] as $row) {
                        query("INSERT INTO categories (id, name) VALUES (?, ?)", [$row['id'], $row['name']]);
                    }
                }

                // Import Tags
                if (!empty($data['tags'])) {
                    foreach ($data['tags'] as $row) {
                        query("INSERT INTO tags (id, name) VALUES (?, ?)", [$row['id'], $row['name']]);
                    }
                }

                // Import Collections
                if (!empty($data['collections'])) {
                    foreach ($data['collections'] as $row) {
                        query("INSERT INTO collections (id, name, description) VALUES (?, ?, ?)", [$row['id'], $row['name'], $row['description']]);
                    }
                }

                // Import Prompts
                if (!empty($data['prompts'])) {
                    foreach ($data['prompts'] as $row) {
                        query("INSERT INTO prompts (id, title, content, category_id, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)", [
                            $row['id'], $row['title'], $row['content'], $row['category_id'], $row['created_at'], $row['updated_at']
                        ]);
                    }
                }

                // Import Prompt Tags
                if (!empty($data['prompt_tags'])) {
                    foreach ($data['prompt_tags'] as $row) {
                        query("INSERT INTO prompt_tags (prompt_id, tag_id) VALUES (?, ?)", [$row['prompt_id'], $row['tag_id']]);
                    }
                }

                // Import Prompt Collections
                if (!empty($data['prompt_collections'])) {
                    foreach ($data['prompt_collections'] as $row) {
                        query("INSERT INTO prompt_collections (prompt_id, collection_id) VALUES (?, ?)", [$row['prompt_id'], $row['collection_id']]);
                    }
                }

                // Re-enable foreign key checks
                $db->exec('PRAGMA foreign_keys = ON;');
                
                $db->commit();
                set_flash('Import successful. All data restored.');
                redirect('index.php');
            } catch (Exception $e) {
                $db->rollBack();
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
                            <strong>Warning:</strong> Importing a backup will <strong>permanently delete</strong> all current data in the system and replace it with the backup content.
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
                    <button type="submit" onclick="return confirm('Are you absolutely sure? Current data will be lost.')" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        Start Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
