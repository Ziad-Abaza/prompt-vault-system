<?php
/**
 * Database Seeder
 * 
 * Populates the database with a rich set of categories, tags, collections,
 * and prompts from seeder.json with intelligent matching.
 */

require_once 'bootstrap.php';

// Ensure we have at least one user
$db = get_db();
$user = query("SELECT * FROM users LIMIT 1")->fetch();
if (!$user) {
    echo "No users found. Creating a default 'System' user...\n";
    query("INSERT INTO users (username, password_hash) VALUES (?, ?)", ['system', password_hash('password123', PASSWORD_DEFAULT)]);
    $user_id = $db->lastInsertId();
} else {
    $user_id = $user['id'];
}

// Function to set current user for model scoped functions
function set_seeder_user($id) {
    if (!isset($_SESSION)) {
        $_SESSION = [];
    }
    $_SESSION['user_id'] = $id;
    $_SESSION['username'] = 'system';
}
set_seeder_user($user_id);

echo "Seeding categories...\n";
$category_list = [
    'AI Chat', 'Prompt Engineering', 'Vibe Coding', 'Software Development', 'Web Development', 
    'Laravel', 'PHP', 'JavaScript', 'Python', 'DevOps', 'Cybersecurity', 'Data Science', 
    'Machine Learning', 'Deep Learning', 'Computer Vision', 'AI Agents', 'Automation', 
    'Productivity', 'Marketing', 'SEO', 'Copywriting', 'Social Media', 'Business', 
    'Education', 'Research', 'Image Generation', 'Midjourney', 'Stable Diffusion', 
    'Flux', 'Leonardo AI', 'DALL-E', 'Video Generation', 'UI/UX Design', 'Graphic Design', 
    '3D Design', 'Architecture', 'Photography', 'Photo Restoration', 'Character Design', 
    'Anime', 'Game Development'
];

foreach ($category_list as $name) {
    query("INSERT OR IGNORE INTO categories (name, user_id) VALUES (?, ?)", [$name, $user_id]);
}

echo "Seeding tags...\n";
$tag_list = [
    'ai', 'prompt', 'chatgpt', 'claude', 'gemini', 'openai', 'anthropic', 'llm', 'agent', 
    'automation', 'coding', 'vibe-coding', 'laravel', 'php', 'javascript', 'python', 
    'vuejs', 'react', 'nextjs', 'api', 'backend', 'frontend', 'fullstack', 'seo', 
    'marketing', 'copywriting', 'productivity', 'startup', 'business', 'image-generation', 
    'flux', 'midjourney', 'stable-diffusion', 'dall-e', 'cinematic', 'realistic', 
    'portrait', 'photography', 'restoration', 'enhancement', 'ui', 'ux', 'design', 
    'logo', 'branding', 'youtube', 'tiktok', 'instagram', 'social-media', 'workflow',
    'sql', 'database', 'optimization', 'refactor', 'debugging', 'architecture', 'template'
];

foreach ($tag_list as $name) {
    query("INSERT OR IGNORE INTO tags (name, user_id) VALUES (?, ?)", [$name, $user_id]);
}

echo "Seeding collections...\n";
$collections_list = [
    ['name' => 'Laravel Development', 'desc' => 'Master the Laravel ecosystem with these expert prompts.'],
    ['name' => 'PHP Mastery', 'desc' => 'Deep dives into modern PHP development and best practices.'],
    ['name' => 'Python Automation', 'desc' => 'Automate your life and work with Python and AI.'],
    ['name' => 'JavaScript Essentials', 'desc' => 'From React to Node, the best prompts for JS developers.'],
    ['name' => 'Full Stack Development', 'desc' => 'Prompts for managing both ends of the stack effectively.'],
    ['name' => 'API Development', 'desc' => 'Design, build, and document professional APIs.'],
    ['name' => 'DevOps Toolkit', 'desc' => 'Docker, CI/CD, and infrastructure management prompts.'],
    ['name' => 'AI Agent Prompts', 'desc' => 'Defining and guiding autonomous AI agents.'],
    ['name' => 'Prompt Engineering Library', 'desc' => 'The foundation of successful LLM interactions.'],
    ['name' => 'Productivity AI', 'desc' => 'Boost your daily output with smart AI workflows.'],
    ['name' => 'Business Automation', 'desc' => 'Streamline business processes using generative AI.'],
    ['name' => 'Marketing AI Toolkit', 'desc' => 'Campaign planning, ad copy, and market research.'],
    ['name' => 'Photo Restoration', 'desc' => 'Advanced prompts for repairing and enhancing old photos.'],
    ['name' => 'AI Image Enhancement', 'desc' => 'Upscaling and detailing prompts for generative art.'],
    ['name' => 'Cinematic Photography', 'desc' => 'Generating hyper-realistic and cinematic visuals.'],
    ['name' => 'Portrait Generation', 'desc' => 'Perfecting human features and expressions in AI art.'],
    ['name' => 'Realistic Image Creation', 'desc' => 'Achieving photorealism in Stable Diffusion and Midjourney.'],
    ['name' => 'Character Design', 'desc' => 'Consistent character creation for games and stories.'],
    ['name' => 'Anime Art', 'desc' => 'Stylized prompts for high-quality anime illustrations.'],
    ['name' => 'Product Photography', 'desc' => 'E-commerce style visuals for products.'],
    ['name' => 'Logo Design', 'desc' => 'Minimalist and professional logo generation.'],
    ['name' => 'Branding Assets', 'desc' => 'Full brand identity kits using AI.'],
    ['name' => 'SEO Toolkit', 'desc' => 'Ranking higher on search engines with AI content.'],
    ['name' => 'Blog Writing', 'desc' => 'Long-form content generation and structuring.'],
    ['name' => 'Social Media Content', 'desc' => 'Viral post ideas and captions for all platforms.'],
    ['name' => 'YouTube Growth', 'desc' => 'Scriptwriting and video optimization prompts.'],
    ['name' => 'Copywriting Library', 'desc' => 'High-conversion sales copy and marketing scripts.']
];

foreach ($collections_list as $c) {
    query("INSERT OR IGNORE INTO collections (name, description, user_id) VALUES (?, ?, ?)", [$c['name'], $c['desc'], $user_id]);
}

// Fetch all from DB for matching
$all_categories = query("SELECT id, name FROM categories WHERE user_id = ?", [$user_id])->fetchAll();
$all_tags = query("SELECT id, name FROM tags WHERE user_id = ?", [$user_id])->fetchAll();
$all_collections = query("SELECT id, name FROM collections WHERE user_id = ?", [$user_id])->fetchAll();

// Loading seeder data
if (!file_exists('seeder.json')) {
    die("Error: seeder.json not found.\n");
}

$seeder_data = json_decode(file_get_contents('seeder.json'), true);
if (!$seeder_data || empty($seeder_data['prompts'])) {
    die("Error: No prompts found in seeder.json.\n");
}

echo "Seeding prompts from seeder.json...\n";
$imported_count = 0;

foreach ($seeder_data['prompts'] as $sp) {
    // Skip if already exists by title
    $exists = query("SELECT id FROM prompts WHERE title = ? AND user_id = ?", [$sp['title'], $user_id])->fetch();
    if ($exists) continue;

    $content = $sp['content'];
    $title = $sp['title'];
    $search_text = strtolower($title . ' ' . $content);

    // 1. Match Category
    $best_category_id = null;
    
    // Weighted matching
    $cat_weights = [];
    foreach ($all_categories as $cat) {
        $cat_name = strtolower($cat['name']);
        $weight = 0;
        if (strpos($search_text, $cat_name) !== false) $weight += 10;
        
        // Match individual words if category name is multi-word
        $words = explode(' ', $cat_name);
        if (count($words) > 1) {
            foreach ($words as $word) {
                if (strlen($word) > 3 && strpos($search_text, $word) !== false) $weight += 2;
            }
        }
        if ($weight > 0) $cat_weights[$cat['id']] = $weight;
    }
    
    if ($cat_weights) {
        arsort($cat_weights);
        $best_category_id = key($cat_weights);
    }

    // Fallback regex matches for broad buckets if no high-weight match
    if (!$best_category_id || $cat_weights[$best_category_id] < 5) {
        if (preg_match('/(photo|restore|repair|photograph)/i', $search_text)) $best_category_id = match_cat('Photo Restoration', $all_categories);
        elseif (preg_match('/(image|draw|paint|diffusion|midjourney|flux|art|illustrate)/i', $search_text)) $best_category_id = match_cat('Image Generation', $all_categories);
        elseif (preg_match('/(laravel|php|backend)/i', $search_text)) $best_category_id = match_cat('Laravel', $all_categories);
        elseif (preg_match('/(python|automation|script)/i', $search_text)) $best_category_id = match_cat('Python', $all_categories);
        elseif (preg_match('/(code|software|architect|debug|refactor|program|develop)/i', $search_text)) $best_category_id = match_cat('Software Development', $all_categories);
        elseif (preg_match('/(seo|marketing|blog|post|social|copywrite|content)/i', $search_text)) $best_category_id = match_cat('Content Creation', $all_categories);
    }
    if (!$best_category_id) $best_category_id = match_cat('AI Chat', $all_categories);

    // 2. Match Tags
    $matched_tag_ids = [];
    foreach ($all_tags as $tag) {
        $tag_name = strtolower($tag['name']);
        if (strpos($search_text, $tag_name) !== false) {
            $matched_tag_ids[] = $tag['id'];
        }
    }
    
    // 3. Match Collections
    $matched_coll_ids = [];
    foreach ($all_collections as $coll) {
        $coll_name = strtolower($coll['name']);
        $weight = 0;
        if (strpos($search_text, $coll_name) !== false) $weight += 10;
        
        $words = explode(' ', $coll_name);
        foreach ($words as $word) {
            if (strlen($word) > 3 && strpos($search_text, $word) !== false) $weight += 2;
        }
        
        if ($weight >= 4) {
            $matched_coll_ids[] = $coll['id'];
        }
    }

    // Mark 70% as public for a lively hub
    $is_public = (rand(1, 100) <= 70) ? 1 : 0;

    // Insert Prompt
    try {
        query("INSERT INTO prompts (title, slug, content, category_id, user_id, is_public, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)", [
            $title,
            slugify($title),
            $content,
            $best_category_id,
            $user_id,
            $is_public,
            $sp['created_at'] ?? date('Y-m-d H:i:s'),
            $sp['updated_at'] ?? date('Y-m-d H:i:s')
        ]);
        $prompt_id = $db->lastInsertId();

        // Link Tags
        foreach (array_unique($matched_tag_ids) as $tid) {
            if ($tid) query("INSERT OR IGNORE INTO prompt_tags (prompt_id, tag_id) VALUES (?, ?)", [$prompt_id, $tid]);
        }

        // Link Collections
        foreach (array_unique($matched_coll_ids) as $cid) {
            if ($cid) query("INSERT OR IGNORE INTO prompt_collections (prompt_id, collection_id) VALUES (?, ?)", [$prompt_id, $cid]);
        }

        $imported_count++;
    } catch (Exception $e) {
        echo "Error importing prompt: " . $e->getMessage() . "\n";
    }
}

echo "Seeding complete! Imported $imported_count new prompts.\n";

// Helper functions for matching
function match_cat($name, $list) {
    foreach ($list as $item) {
        if ($item['name'] === $name) return $item['id'];
    }
    return null;
}

function match_tag($name, $list) {
    foreach ($list as $item) {
        if ($item['name'] === $name) return $item['id'];
    }
    return null;
}

function match_coll($name, $list) {
    foreach ($list as $item) {
        if ($item['name'] === $name) return $item['id'];
    }
    return null;
}
