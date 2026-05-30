<?php
/**
 * Database Seeder
 * 
 * Populates the database with a rich set of categories, tags, collections,
 * and prompts from seeder.json with intelligent matching and duplication prevention.
 * Ensures ownership by a default 'ziad' user.
 */

require_once 'bootstrap.php';

$db = get_db();

// 1. Ensure Default User exists
$target_username = Env::get('SEEDER_USER', 'ziad');
$target_password = Env::get('SEEDER_PASSWORD', 'zezo6920');

$user = query("SELECT * FROM users WHERE username = ?", [$target_username])->fetch();
if (!$user) {
    echo "Creating default user '{$target_username}'...\n";
    query("INSERT INTO users (username, password_hash) VALUES (?, ?)", [
        $target_username, 
        password_hash($target_password, PASSWORD_DEFAULT)
    ]);
    $user_id = $db->lastInsertId();
} else {
    echo "User '{$target_username}' already exists. Reusing account.\n";
    $user_id = $user['id'];
}

// Function to set current user for model scoped functions
function set_seeder_user($id, $username) {
    if (!isset($_SESSION)) {
        $_SESSION = [];
    }
    $_SESSION['user_id'] = $id;
    $_SESSION['username'] = $username;
}
set_seeder_user($user_id, $target_username);

// Load seeder data
if (!file_exists('seeder.json')) {
    die("Error: seeder.json not found.\n");
}

$seeder_data = json_decode(file_get_contents('seeder.json'), true);
if (!$seeder_data) {
    die("Error: Failed to decode seeder.json.\n");
}

/**
 * Realistic Content Buckets
 */
$realistic_categories = [
    'AI' => ['Prompt Engineering', 'AI Assistants', 'Generative AI', 'LLMs', 'AI Agents', 'Automation', 'RAG', 'Fine Tuning'],
    'Vibe Coding' => ['Vibe Coding', 'Full Stack Development', 'Laravel', 'Vue.js', 'React', 'Next.js', 'Python', 'FastAPI', 'SaaS', 'API Development', 'DevOps', 'System Design'],
    'Image Generation' => ['Midjourney', 'Stable Diffusion', 'Flux', 'AI Photography', 'Cinematic Images', 'Product Photography', 'Character Design', 'Concept Art', 'Photo Restoration', 'Image Enhancement', 'AI Video Generation'],
    'Content Creation' => ['Marketing', 'SEO', 'Copywriting', 'Blogging', 'Social Media', 'YouTube']
];

echo "Seeding Categories...\n";
$category_map = []; // Old ID -> New ID
foreach ($seeder_data['categories'] as $cat) {
    $existing = query("SELECT id FROM categories WHERE name = ? AND user_id = ?", [$cat['name'], $user_id])->fetch();
    if ($existing) {
        $category_map[$cat['id']] = $existing['id'];
    } else {
        query("INSERT INTO categories (name, user_id) VALUES (?, ?)", [$cat['name'], $user_id]);
        $category_map[$cat['id']] = $db->lastInsertId();
    }
}

// Add missing realistic categories
foreach ($realistic_categories as $main => $subs) {
    foreach ($subs as $name) {
        $existing = query("SELECT id FROM categories WHERE name = ? AND user_id = ?", [$name, $user_id])->fetch();
        if (!$existing) {
            query("INSERT INTO categories (name, user_id) VALUES (?, ?)", [$name, $user_id]);
        }
    }
}

echo "Seeding Tags...\n";
$tag_map = []; // Old ID -> New ID
foreach ($seeder_data['tags'] as $tag) {
    $existing = query("SELECT id FROM tags WHERE name = ? AND user_id = ?", [$tag['name'], $user_id])->fetch();
    if ($existing) {
        $tag_map[$tag['id']] = $existing['id'];
    } else {
        query("INSERT INTO tags (name, user_id) VALUES (?, ?)", [$tag['name'], $user_id]);
        $tag_map[$tag['id']] = $db->lastInsertId();
    }
}

// Add more realistic tags based on the categories
foreach ($realistic_categories as $main => $subs) {
    foreach ($subs as $name) {
        $tag_name = strtolower(str_replace(' ', '-', $name));
        $existing = query("SELECT id FROM tags WHERE name = ? AND user_id = ?", [$tag_name, $user_id])->fetch();
        if (!$existing) {
            query("INSERT INTO tags (name, user_id) VALUES (?, ?)", [$tag_name, $user_id]);
        }
    }
}

echo "Seeding Collections...\n";
$collection_map = []; // Old ID -> New ID
foreach ($seeder_data['collections'] as $coll) {
    $existing = query("SELECT id FROM collections WHERE name = ? AND user_id = ?", [$coll['name'], $user_id])->fetch();
    if ($existing) {
        $collection_map[$coll['id']] = $existing['id'];
    } else {
        query("INSERT INTO collections (name, description, user_id) VALUES (?, ?, ?)", [$coll['name'], $coll['description'] ?? '', $user_id]);
        $collection_map[$coll['id']] = $db->lastInsertId();
    }
}

echo "Seeding Prompts...\n";
$prompt_map = []; // Old ID -> New ID
foreach ($seeder_data['prompts'] as $prompt) {
    $existing = query("SELECT id FROM prompts WHERE title = ? AND user_id = ?", [$prompt['title'], $user_id])->fetch();
    if ($existing) {
        $prompt_map[$prompt['id']] = $existing['id'];
        continue;
    }

    $new_cat_id = isset($category_map[$prompt['category_id']]) ? $category_map[$prompt['category_id']] : null;
    
    // Default to public (1) if not specified in seeder.json
    $is_public = isset($prompt['is_public']) ? $prompt['is_public'] : 1;
    
    query("INSERT INTO prompts (title, slug, content, category_id, user_id, is_public, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)", [
        $prompt['title'],
        $prompt['slug'] ?? slugify($prompt['title']),
        $prompt['content'],
        $new_cat_id,
        $user_id,
        $is_public,
        $prompt['created_at'] ?? date('Y-m-d H:i:s'),
        $prompt['updated_at'] ?? date('Y-m-d H:i:s')
    ]);
    $prompt_map[$prompt['id']] = $db->lastInsertId();
}

echo "Linking Tags and Collections...\n";
if (isset($seeder_data['prompt_tags'])) {
    foreach ($seeder_data['prompt_tags'] as $pt) {
        $new_p_id = $prompt_map[$pt['prompt_id']] ?? null;
        $new_t_id = $tag_map[$pt['tag_id']] ?? null;
        if ($new_p_id && $new_t_id) {
            query("INSERT OR IGNORE INTO prompt_tags (prompt_id, tag_id) VALUES (?, ?)", [$new_p_id, $new_t_id]);
        }
    }
}

if (isset($seeder_data['prompt_collections'])) {
    foreach ($seeder_data['prompt_collections'] as $pc) {
        $new_p_id = $prompt_map[$pc['prompt_id']] ?? null;
        $new_c_id = $collection_map[$pc['collection_id']] ?? null;
        if ($new_p_id && $new_c_id) {
            query("INSERT OR IGNORE INTO prompt_collections (prompt_id, collection_id) VALUES (?, ?)", [$new_p_id, $new_c_id]);
        }
    }
}

echo "Seeding complete! All content assigned to user '{$target_username}'.\n";
