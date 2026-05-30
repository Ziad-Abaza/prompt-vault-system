<?php
require_once 'bootstrap.php';

$id = $_GET['id'] ?? null;
$prompt = $id ? get_prompt($id) : null;

if ($id && !$prompt) {
    abort(404);
}

$categories = get_categories();
$tags = get_tags();
$collections = get_collections();
$errors = [];

// Handle image deletion
if ($id && isset($_GET['delete_image'])) {
    if (delete_prompt_image($_GET['delete_image'])) {
        set_flash('Image removed successfully.');
        redirect("prompt_edit.php?id=$id");
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $validator = new Validator($_POST);
    $validator->required('title', 'Please provide a title for your prompt.')
              ->max('title', 255)
              ->required('content', 'The prompt content cannot be empty.');

    if ($validator->is_valid()) {
        $data = [
            'title' => $_POST['title'],
            'content' => $_POST['content'],
            'category_id' => $_POST['category_id'] ?: null,
            'tag_ids' => $_POST['tag_ids'] ?? [],
            'collection_ids' => $_POST['collection_ids'] ?? [],
            'is_public' => isset($_POST['is_public']) ? 1 : 0,
        ];

        // Handle new tags
        if (!empty($_POST['new_tags'])) {
            $new_tag_names = explode(',', $_POST['new_tags']);
            $new_tag_ids = get_or_create_tags_by_names($new_tag_names);
            $data['tag_ids'] = array_unique(array_merge($data['tag_ids'], $new_tag_ids));
        }

        try {
            if ($id) {
                update_prompt($id, $data);
                set_flash('Prompt updated successfully.');
            } else {
                $id = create_prompt($data);
                set_flash('Prompt created successfully.');
            }

            // Handle image uploads
            if (!empty($_FILES['images']['name'][0])) {
                $upload_dir = __DIR__ . '/uploads/';
                
                // Ensure upload directory exists
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $max_size = 10 * 1024 * 1024; // 10MB
                $upload_errors = [];

                foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                    if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                        $file_type = $_FILES['images']['type'][$key];
                        $file_size = $_FILES['images']['size'][$key];

                        if (in_array($file_type, $allowed_types) && $file_size <= $max_size) {
                            $extension = pathinfo($_FILES['images']['name'][$key], PATHINFO_EXTENSION);
                            $filename = uniqid('prompt_' . $id . '_') . '.' . $extension;
                            $target_path = $upload_dir . $filename;
                            $db_path = 'uploads/' . $filename;

                            if (move_uploaded_file($tmp_name, $target_path)) {
                                add_prompt_image($id, $db_path);
                            } else {
                                $upload_errors[] = "Failed to move uploaded file: " . $_FILES['images']['name'][$key];
                            }
                        } else {
                            if (!in_array($file_type, $allowed_types)) {
                                $upload_errors[] = "Invalid file type: " . $_FILES['images']['name'][$key];
                            }
                            if ($file_size > $max_size) {
                                $upload_errors[] = "File too large (max 10MB): " . $_FILES['images']['name'][$key];
                            }
                        }
                    } else if ($_FILES['images']['error'][$key] !== UPLOAD_ERR_NO_FILE) {
                        $upload_errors[] = "Upload error code " . $_FILES['images']['error'][$key] . " for " . $_FILES['images']['name'][$key];
                    }
                }

                if (!empty($upload_errors)) {
                    set_flash('Some images could not be uploaded: ' . implode(', ', $upload_errors), 'error');
                }
            }

            redirect("prompt.php?id=$id");
        } catch (Exception $e) {
            $errors['form'] = $e->getMessage();
        }
    } else {
        $errors = $validator->get_errors();
    }
}

// Prepare selected IDs for the form
$selected_tag_ids = $prompt ? array_column($prompt['tags'], 'id') : ($_POST['tag_ids'] ?? []);
$selected_collection_ids = $prompt ? array_column($prompt['collections'], 'id') : ($_POST['collection_ids'] ?? []);

include 'includes/header.php';
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-12">
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2 text-sm font-medium">
                <li><a href="index.php" class="text-slate-400 hover:text-slate-600 transition-colors font-semibold">Library</a></li>
                <li>
                    <svg class="h-5 w-5 text-slate-300" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </li>
                <li class="text-slate-900 font-bold"><?php echo $id ? 'Edit Prompt' : 'New Prompt'; ?></li>
            </ol>
        </nav>
        <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight"><?php echo $id ? 'Refine Prompt' : 'Create New Prompt'; ?></h1>
        <p class="mt-2 text-slate-500 text-lg font-medium">Build and organize your prompt intelligence.</p>
    </div>

    <?php if (isset($errors['form'])): ?>
        <div class="mb-8 p-4 rounded-2xl bg-red-50 border border-red-100 text-red-700 text-sm font-bold flex items-center">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <?php echo esc($errors['form']); ?>
        </div>
    <?php endif; ?>

    <form action="prompt_edit.php<?php echo $id ? '?id=' . $id : ''; ?>" method="POST" enctype="multipart/form-data" class="space-y-10 pb-24">
        <?php echo csrf_input(); ?>

        <!-- Section 1: Basic Information -->
        <div class="form-section shadow-xl shadow-slate-200/40">
            <div class="form-section-header">
                <h3 class="form-section-title">Step 1: Identity & Classification</h3>
                <span class="text-[10px] font-black text-primary-500 bg-primary-50 px-2 py-1 rounded-md uppercase tracking-widest">Required</span>
            </div>
            <div class="form-body">
                <div class="form-group">
                    <label for="title" class="form-label">Prompt Title</label>
                    <input type="text" name="title" id="title" required value="<?php echo esc($_POST['title'] ?? $prompt['title'] ?? ''); ?>" 
                        class="form-input text-xl font-bold <?php echo isset($errors['title']) ? 'border-red-300 ring-4 ring-red-500/5 bg-red-50/30' : ''; ?>" 
                        placeholder="e.g. Creative Writing Assistant">
                    <?php if (isset($errors['title'])): ?>
                        <p class="form-error"><?php echo esc($errors['title']); ?></p>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="category_id" class="form-label">Primary Category</label>
                    <div class="relative">
                        <select name="category_id" id="category_id" class="form-select">
                            <option value="">No Category (Uncategorized)</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo (($_POST['category_id'] ?? $prompt['category_id'] ?? '') == $cat['id']) ? 'selected' : ''; ?>>
                                    <?php echo esc($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="form-group pt-4">
                    <label class="flex items-center cursor-pointer group">
                        <div class="relative">
                            <input type="checkbox" name="is_public" id="is_public" class="sr-only" <?php echo ($_POST['is_public'] ?? $prompt['is_public'] ?? 0) ? 'checked' : ''; ?>>
                            <div class="w-10 h-6 bg-slate-200 rounded-full shadow-inner transition-colors group-hover:bg-slate-300 peer-checked:bg-primary-500"></div>
                            <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform peer-checked:translate-x-4"></div>
                        </div>
                        <div class="ml-4">
                            <span class="block text-sm font-bold text-slate-900">Public Visibility</span>
                            <span class="block text-xs text-slate-500 font-medium">Allow search engines and non-logged users to view this prompt.</span>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Section 2: Prompt Content -->
        <div class="form-section shadow-xl shadow-slate-200/40">
            <div class="form-section-header">
                <h3 class="form-section-title">Step 2: Prompt Engineering</h3>
                <span class="text-[10px] font-black text-amber-500 bg-amber-50 px-2 py-1 rounded-md uppercase tracking-widest">Core Content</span>
            </div>
            <div class="form-body">
                <div class="form-group">
                    <label for="content" class="form-label px-1">Prompt Instructions / Template</label>
                    <textarea name="content" id="content" rows="18" required 
                        class="form-textarea min-h-[450px] <?php echo isset($errors['content']) ? 'border-red-300 ring-4 ring-red-500/5 bg-red-50/30' : ''; ?>" 
                        placeholder="Define your prompt logic here..."><?php echo esc($_POST['content'] ?? $prompt['content'] ?? ''); ?></textarea>
                    <?php if (isset($errors['content'])): ?>
                        <p class="form-error"><?php echo esc($errors['content']); ?></p>
                    <?php endif; ?>
                    <div class="mt-4 p-4 bg-slate-50 rounded-2xl border border-slate-100 flex items-start">
                        <svg class="w-5 h-5 text-primary-500 mr-3 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-xs text-slate-500 leading-relaxed font-medium">
                            <strong class="text-slate-700">Pro Tip:</strong> Use placeholders like <code class="bg-white border border-slate-200 px-1 rounded text-primary-600">[INPUT]</code> or <code class="bg-white border border-slate-200 px-1 rounded text-primary-600">{{VARIABLE}}</code> to make your prompts reusable.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: Organization & Tags -->
        <div class="form-section shadow-xl shadow-slate-200/40">
            <div class="form-section-header">
                <h3 class="form-section-title">Step 3: Discovery & Tags</h3>
                <span class="text-[10px] font-black text-indigo-500 bg-indigo-50 px-2 py-1 rounded-md uppercase tracking-widest">Metadata</span>
            </div>
            <div class="form-body">
                <div class="form-group">
                    <label for="new_tags" class="form-label px-1">Quick Add Tags</label>
                    <input type="text" name="new_tags" id="new_tags" 
                        class="form-input" 
                        placeholder="Comma separated tags (e.g. creative, workflow, chatgpt)">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div class="form-group">
                        <label class="form-label px-1">Select Tags</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-64 overflow-y-auto p-3 bg-slate-50 rounded-2xl border border-slate-100">
                            <?php foreach ($tags as $tag): ?>
                                <label class="flex items-center p-3 bg-white rounded-xl border border-slate-200 cursor-pointer hover:border-primary-400 hover:bg-primary-50/30 transition-all group/tag">
                                    <input type="checkbox" name="tag_ids[]" value="<?php echo $tag['id']; ?>" <?php echo in_array($tag['id'], $selected_tag_ids) ? 'checked' : ''; ?> class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-slate-300 rounded transition-all group-hover/tag:scale-110">
                                    <span class="ml-3 text-xs font-bold text-slate-600 truncate group-hover/tag:text-primary-700 transition-colors">#<?php echo esc($tag['name']); ?></span>
                                </label>
                            <?php endforeach; ?>
                            <?php if (empty($tags)): ?>
                                <p class="col-span-full py-10 text-center text-slate-400 text-xs italic font-medium">No tags available yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label px-1">Add to Collections</label>
                        <div class="grid grid-cols-1 gap-2 max-h-64 overflow-y-auto p-3 bg-slate-50 rounded-2xl border border-slate-100">
                            <?php foreach ($collections as $coll): ?>
                                <label class="flex items-center p-3 bg-white rounded-xl border border-slate-200 cursor-pointer hover:border-primary-400 hover:bg-primary-50/30 transition-all group/coll">
                                    <input type="checkbox" name="collection_ids[]" value="<?php echo $coll['id']; ?>" <?php echo in_array($coll['id'], $selected_collection_ids) ? 'checked' : ''; ?> class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-slate-300 rounded transition-all group-hover/coll:scale-110">
                                    <div class="ml-3 overflow-hidden">
                                        <p class="text-xs font-black text-slate-700 truncate group-hover/coll:text-primary-700 transition-colors"><?php echo esc($coll['name']); ?></p>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                            <?php if (empty($collections)): ?>
                                <p class="col-span-full py-10 text-center text-slate-400 text-xs italic font-medium">No collections available yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 4: Visuals & Attachments -->
        <div class="form-section shadow-xl shadow-slate-200/40">
            <div class="form-section-header">
                <h3 class="form-section-title">Step 4: Visuals & Attachments</h3>
                <span class="text-[10px] font-black text-rose-500 bg-rose-50 px-2 py-1 rounded-md uppercase tracking-widest">Images</span>
            </div>
            <div class="form-body">
                <div class="form-group">
                    <label for="images" class="form-label px-1">Upload Reference Images</label>
                    <input type="file" name="images[]" id="images" multiple accept="image/*" class="form-input border-dashed border-2 py-8 bg-slate-50/50 hover:bg-white hover:border-primary-400 transition-all">
                    <p class="text-[10px] text-slate-400 font-medium px-1">Supported: JPG, PNG, GIF, WEBP. Max 10MB per image.</p>
                </div>

                <?php if ($prompt && !empty($prompt['images'])): ?>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mt-6">
                        <?php foreach ($prompt['images'] as $img): ?>
                            <div class="relative group aspect-square rounded-2xl overflow-hidden border border-slate-200 bg-slate-50">
                                <img src="<?php echo esc($img['image_path']); ?>" alt="Prompt Attachment" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                <a href="prompt_edit.php?id=<?php echo $id; ?>&delete_image=<?php echo $img['id']; ?>" 
                                   onclick="return confirm('Delete this image?');"
                                   class="absolute top-2 right-2 p-1.5 bg-red-500 text-white rounded-full shadow-lg opacity-0 group-hover:opacity-100 transition-all hover:bg-red-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex flex-col md:flex-row items-center justify-between gap-8 pt-4">
            <div>
                <?php if ($id): ?>
                    <button type="button" onclick="if(confirm('Are you absolutely sure? This prompt will be removed from your library forever.')) document.getElementById('delete-form').submit();" class="btn-danger-link">
                        Delete Prompt Permanently
                    </button>
                <?php endif; ?>
            </div>
            <div class="flex items-center space-x-4 w-full md:w-auto">
                <a href="<?php echo $id ? 'prompt.php?id=' . $id : 'index.php'; ?>" class="btn-secondary flex-grow md:flex-grow-0 text-center">
                    Discard
                </a>
                <button type="submit" class="btn-primary flex-grow md:flex-grow-0 shadow-primary-600/30">
                    <?php echo $id ? 'Save Changes' : 'Publish Prompt'; ?>
                </button>
            </div>
        </div>
    </form>

    <?php if ($id): ?>
        <form id="delete-form" action="prompt_delete.php" method="POST" class="hidden">
            <?php echo csrf_input(); ?>
            <input type="hidden" name="id" value="<?php echo $id; ?>">
        </form>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
