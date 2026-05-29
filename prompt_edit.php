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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'title' => $_POST['title'] ?? '',
        'content' => $_POST['content'] ?? '',
        'category_id' => $_POST['category_id'] ?: null,
        'tag_ids' => $_POST['tag_ids'] ?? [],
        'collection_ids' => $_POST['collection_ids'] ?? [],
    ];

    // Handle new tags (comma separated)
    if (!empty($_POST['new_tags'])) {
        $new_tag_names = explode(',', $_POST['new_tags']);
        $new_tag_ids = get_or_create_tags_by_names($new_tag_names);
        $data['tag_ids'] = array_merge($data['tag_ids'], $new_tag_ids);
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
        $error = $e->getMessage();
    }
}

// Prepare selected IDs for the form
$selected_tag_ids = $prompt ? array_column($prompt['tags'], 'id') : [];
$selected_collection_ids = $prompt ? array_column($prompt['collections'], 'id') : [];

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

    <?php if (isset($error)): ?>
        <div class="mb-8 p-4 rounded-xl bg-red-50 border border-red-100 text-red-700 text-sm font-medium">
            <?php echo esc($error); ?>
        </div>
    <?php endif; ?>

    <form action="prompt_edit.php<?php echo $id ? '?id=' . $id : ''; ?>" method="POST" class="space-y-10">
        <?php echo csrf_input(); ?>

        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-8 md:p-10 space-y-8">
                <!-- Title Field -->
                <div>
                    <label for="title" class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Prompt Title</label>
                    <input type="text" name="title" id="title" required value="<?php echo esc($prompt['title'] ?? ''); ?>" 
                        class="block w-full px-6 py-4 bg-slate-50 border-slate-100 rounded-2xl focus:ring-primary-500 focus:border-primary-500 text-xl font-bold text-slate-900 transition-all" 
                        placeholder="Give your prompt a descriptive name...">
                </div>

                <!-- Content Field -->
                <div>
                    <label for="content" class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Prompt Content</label>
                    <textarea name="content" id="content" rows="12" required 
                        class="block w-full px-6 py-6 bg-slate-50 border-slate-100 rounded-2xl focus:ring-primary-500 focus:border-primary-500 font-mono text-slate-800 leading-relaxed transition-all" 
                        placeholder="Write or paste your prompt content here..."><?php echo esc($prompt['content'] ?? ''); ?></textarea>
                </div>

                <!-- Organization Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10 pt-4 border-t border-slate-50">
                    <div>
                        <label for="category_id" class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Category</label>
                        <select name="category_id" id="category_id" class="block w-full px-4 py-3 bg-slate-50 border-slate-100 rounded-xl focus:ring-primary-500 focus:border-primary-500 sm:text-sm transition-all">
                            <option value="">No Category</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo ($prompt['category_id'] ?? '') == $cat['id'] ? 'selected' : ''; ?>>
                                    <?php echo esc($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="new_tags" class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Add New Tags</label>
                        <input type="text" name="new_tags" id="new_tags" 
                            class="block w-full px-4 py-3 bg-slate-50 border-slate-100 rounded-xl focus:ring-primary-500 focus:border-primary-500 sm:text-sm transition-all" 
                            placeholder="comma, separated, tags">
                    </div>
                </div>

                <!-- Multi-select Areas -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-4 text-center md:text-left">Select Existing Tags</label>
                        <div class="grid grid-cols-2 gap-2 max-h-48 overflow-y-auto p-2 bg-slate-50 rounded-2xl border border-slate-100">
                            <?php foreach ($tags as $tag): ?>
                                <label class="flex items-center p-3 bg-white rounded-xl border border-slate-100 cursor-pointer hover:border-primary-300 transition-all">
                                    <input type="checkbox" name="tag_ids[]" value="<?php echo $tag['id']; ?>" <?php echo in_array($tag['id'], $selected_tag_ids) ? 'checked' : ''; ?> class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-slate-300 rounded">
                                    <span class="ml-3 text-xs font-semibold text-slate-700 truncate"><?php echo esc($tag['name']); ?></span>
                                </label>
                            <?php endforeach; ?>
                            <?php if (empty($tags)): ?>
                                <p class="col-span-full py-4 text-center text-slate-400 text-xs italic">No tags yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-4 text-center md:text-left">Assign to Collections</label>
                        <div class="grid grid-cols-1 gap-2 max-h-48 overflow-y-auto p-2 bg-slate-50 rounded-2xl border border-slate-100">
                            <?php foreach ($collections as $coll): ?>
                                <label class="flex items-center p-3 bg-white rounded-xl border border-slate-100 cursor-pointer hover:border-primary-300 transition-all">
                                    <input type="checkbox" name="collection_ids[]" value="<?php echo $coll['id']; ?>" <?php echo in_array($coll['id'], $selected_collection_ids) ? 'checked' : ''; ?> class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-slate-300 rounded">
                                    <div class="ml-3 overflow-hidden">
                                        <p class="text-xs font-bold text-slate-900 truncate"><?php echo esc($coll['name']); ?></p>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                            <?php if (empty($collections)): ?>
                                <p class="col-span-full py-4 text-center text-slate-400 text-xs italic">No collections yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="px-8 py-6 bg-slate-50 border-t border-slate-100 flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="order-2 md:order-1">
                    <?php if ($id): ?>
                        <button type="button" onclick="if(confirm('Delete this prompt permanently?')) document.getElementById('delete-form').submit();" class="text-red-500 hover:text-red-700 text-sm font-bold uppercase tracking-widest">
                            Delete Prompt
                        </button>
                    <?php endif; ?>
                </div>
                <div class="flex items-center space-x-4 order-1 md:order-2 w-full md:w-auto">
                    <a href="<?php echo $id ? 'prompt.php?id=' . $id : 'index.php'; ?>" class="flex-grow md:flex-grow-0 text-center px-8 py-3 text-slate-600 font-bold hover:text-slate-900 transition-colors">
                        Discard
                    </a>
                    <button type="submit" class="flex-grow md:flex-grow-0 px-10 py-3 bg-primary-600 text-white font-bold rounded-xl hover:bg-primary-700 shadow-lg shadow-primary-600/20 transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <?php echo $id ? 'Save Changes' : 'Publish Prompt'; ?>
                    </button>
                </div>
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
