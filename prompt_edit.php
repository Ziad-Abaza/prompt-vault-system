<?php
require_once 'bootstrap.php';

$id = $_GET['id'] ?? null;
$prompt = $id ? get_prompt($id) : null;

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

<div class="max-w-3xl mx-auto">
    <div class="md:flex md:items-center md:justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-900"><?php echo $id ? 'Edit Prompt' : 'New Prompt'; ?></h1>
    </div>

    <?php if (isset($error)): ?>
        <div class="mb-4 p-4 rounded-md bg-red-50 text-red-700">
            <?php echo esc($error); ?>
        </div>
    <?php endif; ?>

    <form action="prompt_edit.php<?php echo $id ? '?id=' . $id : ''; ?>" method="POST" class="space-y-6 bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
        <?php echo csrf_input(); ?>

        <div>
            <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
            <input type="text" name="title" id="title" required value="<?php echo esc($prompt['title'] ?? ''); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm">
        </div>

        <div>
            <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
            <textarea name="content" id="content" rows="10" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm"><?php echo esc($prompt['content'] ?? ''); ?></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
                <select name="category_id" id="category_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm">
                    <option value="">None</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo ($prompt['category_id'] ?? '') == $cat['id'] ? 'selected' : ''; ?>>
                            <?php echo esc($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="new_tags" class="block text-sm font-medium text-gray-700">Add Tags (comma separated)</label>
                <input type="text" name="new_tags" id="new_tags" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary sm:text-sm" placeholder="ai, writing, creative">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Existing Tags</label>
                <div class="max-h-40 overflow-y-auto border border-gray-300 rounded-md p-2 space-y-1">
                    <?php foreach ($tags as $tag): ?>
                        <div class="flex items-center">
                            <input type="checkbox" name="tag_ids[]" value="<?php echo $tag['id']; ?>" id="tag_<?php echo $tag['id']; ?>" <?php echo in_array($tag['id'], $selected_tag_ids) ? 'checked' : ''; ?> class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                            <label for="tag_<?php echo $tag['id']; ?>" class="ml-2 text-sm text-gray-700"><?php echo esc($tag['name']); ?></label>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($tags)): ?>
                        <p class="text-sm text-gray-500 italic">No tags created yet.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Collections</label>
                <div class="max-h-40 overflow-y-auto border border-gray-300 rounded-md p-2 space-y-1">
                    <?php foreach ($collections as $coll): ?>
                        <div class="flex items-center">
                            <input type="checkbox" name="collection_ids[]" value="<?php echo $coll['id']; ?>" id="coll_<?php echo $coll['id']; ?>" <?php echo in_array($coll['id'], $selected_collection_ids) ? 'checked' : ''; ?> class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                            <label for="coll_<?php echo $coll['id']; ?>" class="ml-2 text-sm text-gray-700"><?php echo esc($coll['name']); ?></label>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($collections)): ?>
                        <p class="text-sm text-gray-500 italic">No collections created yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="index.php" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                Cancel
            </a>
            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                <?php echo $id ? 'Update Prompt' : 'Create Prompt'; ?>
            </button>
        </div>
    </form>

    <?php if ($id): ?>
        <div class="mt-8 border-t border-gray-200 pt-8">
            <form action="prompt_delete.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this prompt?');">
                <?php echo csrf_input(); ?>
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium">
                    Delete Prompt Permanently
                </button>
            </form>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
