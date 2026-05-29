<?php
require_once 'bootstrap.php';

$id = $_GET['id'] ?? null;
$prompt = $id ? get_prompt($id) : null;

if ($id && !$prompt) {
    set_flash('Prompt not found.', 'error');
    redirect('index.php');
}

$categories = get_categories();
$tags = get_tags();
$collections = get_collections();
$errors = [];

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
                <li><a href="index.php" class="text-slate-400 hover:text-slate-600 transition-colors">Workspace</a></li>
                <li>
                    <svg class="h-5 w-5 text-slate-300" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </li>
                <li class="text-slate-900"><?php echo $id ? 'Edit Prompt' : 'New Prompt'; ?></li>
            </ol>
        </nav>
        <h1 class="text-4xl font-bold text-slate-900 tracking-tight"><?php echo $id ? 'Refine Prompt' : 'Create New Prompt'; ?></h1>
    </div>

    <?php if (isset($errors['form'])): ?>
        <div class="mb-8 p-4 rounded-xl bg-red-50 border border-red-100 text-red-700 text-sm font-medium">
            <?php echo esc($errors['form']); ?>
        </div>
    <?php endif; ?>

    <form action="prompt_edit.php<?php echo $id ? '?id=' . $id : ''; ?>" method="POST" class="space-y-12">
        <?php echo csrf_input(); ?>

        <!-- Section 1: Basic Information -->
        <div class="form-section">
            <div class="px-8 py-6 border-b border-slate-50 bg-slate-50/50">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-widest">Basic Information</h3>
            </div>
            <div class="p-8 space-y-8">
                <div>
                    <label for="title" class="form-label px-1">Prompt Title</label>
                    <input type="text" name="title" id="title" required value="<?php echo esc($_POST['title'] ?? $prompt['title'] ?? ''); ?>" 
                        class="form-input text-xl font-bold <?php echo isset($errors['title']) ? 'border-red-300 ring-red-100' : ''; ?>" 
                        placeholder="e.g. Creative Story Generator">
                    <?php if (isset($errors['title'])): ?>
                        <p class="form-error"><?php echo esc($errors['title']); ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="category_id" class="form-label px-1">Category</label>
                    <select name="category_id" id="category_id" class="form-input">
                        <option value="">No Category (Uncategorized)</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo (($_POST['category_id'] ?? $prompt['category_id'] ?? '') == $cat['id']) ? 'selected' : ''; ?>>
                                <?php echo esc($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- Section 2: Prompt Content -->
        <div class="form-section">
            <div class="px-8 py-6 border-b border-slate-50 bg-slate-50/50 flex justify-between items-center">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-widest">Prompt Content</h3>
                <span class="text-xs font-bold text-slate-400">Core Data</span>
            </div>
            <div class="p-8">
                <textarea name="content" id="content" rows="15" required 
                    class="form-input font-mono text-slate-800 leading-relaxed min-h-[400px] resize-y <?php echo isset($errors['content']) ? 'border-red-300 ring-red-100' : ''; ?>" 
                    placeholder="Write or paste your prompt here..."><?php echo esc($_POST['content'] ?? $prompt['content'] ?? ''); ?></textarea>
                <?php if (isset($errors['content'])): ?>
                    <p class="form-error"><?php echo esc($errors['content']); ?></p>
                <?php endif; ?>
                <p class="mt-4 text-xs text-slate-400 italic font-medium">Use markers like [VAR_NAME] for easy identification of variables in your prompts.</p>
            </div>
        </div>

        <!-- Section 3: Organization & Tags -->
        <div class="form-section">
            <div class="px-8 py-6 border-b border-slate-50 bg-slate-50/50">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-widest">Organization & Discovery</h3>
            </div>
            <div class="p-8 space-y-10">
                <div>
                    <label for="new_tags" class="form-label px-1">Quick Add Tags</label>
                    <input type="text" name="new_tags" id="new_tags" 
                        class="form-input" 
                        placeholder="Type tags separated by commas (e.g. ai, writing, story)">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div>
                        <label class="form-label px-1 text-center md:text-left mb-4">Select Existing Tags</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-60 overflow-y-auto p-3 bg-slate-50 rounded-2xl border border-slate-100">
                            <?php foreach ($tags as $tag): ?>
                                <label class="flex items-center p-3 bg-white rounded-xl border border-slate-100 cursor-pointer hover:border-primary-300 transition-all">
                                    <input type="checkbox" name="tag_ids[]" value="<?php echo $tag['id']; ?>" <?php echo in_array($tag['id'], $selected_tag_ids) ? 'checked' : ''; ?> class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-slate-300 rounded">
                                    <span class="ml-3 text-xs font-bold text-slate-600 truncate">#<?php echo esc($tag['name']); ?></span>
                                </label>
                            <?php endforeach; ?>
                            <?php if (empty($tags)): ?>
                                <p class="col-span-full py-6 text-center text-slate-400 text-xs italic">No tags available.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div>
                        <label class="form-label px-1 text-center md:text-left mb-4">Assign to Collections</label>
                        <div class="grid grid-cols-1 gap-2 max-h-60 overflow-y-auto p-3 bg-slate-50 rounded-2xl border border-slate-100">
                            <?php foreach ($collections as $coll): ?>
                                <label class="flex items-center p-3 bg-white rounded-xl border border-slate-100 cursor-pointer hover:border-primary-300 transition-all">
                                    <input type="checkbox" name="collection_ids[]" value="<?php echo $coll['id']; ?>" <?php echo in_array($coll['id'], $selected_collection_ids) ? 'checked' : ''; ?> class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-slate-300 rounded">
                                    <div class="ml-3 overflow-hidden">
                                        <p class="text-xs font-bold text-slate-900 truncate"><?php echo esc($coll['name']); ?></p>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                            <?php if (empty($collections)): ?>
                                <p class="col-span-full py-6 text-center text-slate-400 text-xs italic">No collections available.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex flex-col md:flex-row items-center justify-between gap-6 pt-6 pb-20">
            <div class="order-2 md:order-1">
                <?php if ($id): ?>
                    <button type="button" onclick="if(confirm('Are you absolutely sure you want to delete this prompt? This cannot be undone.')) document.getElementById('delete-form').submit();" class="text-red-500 hover:text-red-700 text-xs font-bold uppercase tracking-widest transition-colors">
                        Delete Prompt Permanently
                    </button>
                <?php endif; ?>
            </div>
            <div class="flex items-center space-x-4 order-1 md:order-2 w-full md:w-auto">
                <a href="<?php echo $id ? 'prompt.php?id=' . $id : 'index.php'; ?>" class="btn-secondary flex-grow md:flex-grow-0 text-center">
                    Discard
                </a>
                <button type="submit" class="btn-primary flex-grow md:flex-grow-0">
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
