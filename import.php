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
                        query("INSERT INTO prompts (title, slug, content, category_id, user_id, is_public, view_count, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)", [
                            $row['title'], 
                            $row['slug'] ?? slugify($row['title']), 
                            $row['content'], 
                            $new_cat_id, 
                            $user_id, 
                            $row['is_public'] ?? 0,
                            $row['view_count'] ?? 0,
                            $row['created_at'], 
                            $row['updated_at']
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

                // Import Prompt Images
                if (!empty($data['prompt_images'])) {
                    foreach ($data['prompt_images'] as $row) {
                        $new_prompt_id = $prompt_map[$row['prompt_id']] ?? null;
                        if ($new_prompt_id) {
                            query("INSERT INTO prompt_images (prompt_id, image_path, created_at) VALUES (?, ?, ?)", [
                                $new_prompt_id, $row['image_path'], $row['created_at']
                            ]);
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

<div class="max-w-3xl mx-auto">
    <div class="mb-12">
        <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight mb-2">Import Library</h1>
        <p class="text-slate-500 text-lg font-medium text-balance">Restore your prompts and organizational data from a backup file.</p>
    </div>

    <div class="form-section shadow-xl shadow-slate-200/40 border-amber-100">
        <div class="form-section-header bg-amber-50/50 border-amber-50">
            <h3 class="form-section-title text-amber-900">Critical: Data Replacement Warning</h3>
            <span class="text-[10px] font-black text-amber-600 bg-white px-2 py-1 rounded-md uppercase tracking-widest border border-amber-100">Action Required</span>
        </div>
        
        <div class="form-body">
            <div class="p-6 bg-amber-50 rounded-2xl border border-amber-100 flex items-start">
                <div class="shrink-0 w-10 h-10 rounded-xl bg-white flex items-center justify-center text-amber-600 shadow-sm border border-amber-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-amber-900 font-bold leading-relaxed">
                        This process will permanently overwrite your current library.
                    </p>
                    <p class="text-xs text-amber-700 mt-1 font-medium leading-relaxed">
                        All prompts, categories, tags, and collections in your account will be deleted and replaced with the contents of the uploaded JSON file.
                    </p>
                </div>
            </div>

            <?php if (isset($error)): ?>
                <div class="p-4 rounded-xl bg-red-50 border border-red-100 text-red-700 text-sm font-bold flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <?php echo esc($error); ?>
                </div>
            <?php endif; ?>

            <form action="import.php" method="POST" enctype="multipart/form-data" class="space-y-10 pt-4">
                <?php echo csrf_input(); ?>
                
                <div class="form-group">
                    <label class="form-label px-1">Select JSON Backup File</label>
                    <div class="relative group">
                        <input id="backup_file" name="backup_file" type="file" class="sr-only" accept=".json" required onchange="document.getElementById('file-name-display').textContent = this.files[0].name">
                        <label for="backup_file" class="flex flex-col items-center justify-center w-full h-64 px-6 transition bg-slate-50 border-2 border-slate-100 border-dashed rounded-3xl cursor-pointer hover:bg-primary-50/30 hover:border-primary-300 focus:outline-none group-hover:scale-[1.01] transition-all">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <div class="w-16 h-16 mb-4 rounded-2xl bg-white flex items-center justify-center text-slate-400 group-hover:text-primary-500 shadow-sm border border-slate-100 transition-colors">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                </div>
                                <p id="file-name-display" class="mb-2 text-sm text-slate-700 font-bold">Click to upload or drag and drop</p>
                                <p class="text-xs text-slate-400 font-medium uppercase tracking-widest">Only JSON files are supported</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="pt-6">
                    <button type="submit" onclick="return confirm('Final confirmation: Are you sure you want to replace your entire vault?')" class="btn-primary w-full py-5 text-lg">
                        Execute Data Restoration
                    </button>
                    <p class="text-center mt-6 text-xs text-slate-400 font-medium">Your current vault remains untouched until you press this button.</p>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
