<?php
/**
 * Data Models / Logic
 * Scoped by current user_id for private vaults.
 */

// --- Categories ---

function get_categories() {
    return query("SELECT * FROM categories WHERE user_id = ? ORDER BY name ASC", [get_current_user_id()])->fetchAll();
}

function get_category($id) {
    return query("SELECT * FROM categories WHERE id = ? AND user_id = ?", [$id, get_current_user_id()])->fetch();
}

function create_category($name) {
    return query("INSERT INTO categories (name, user_id) VALUES (?, ?)", [$name, get_current_user_id()]);
}

function update_category($id, $name) {
    return query("UPDATE categories SET name = ? WHERE id = ? AND user_id = ?", [$name, $id, get_current_user_id()]);
}

function delete_category($id) {
    return query("DELETE FROM categories WHERE id = ? AND user_id = ?", [$id, get_current_user_id()]);
}

// --- Tags ---

function get_tags() {
    return query("SELECT * FROM tags WHERE user_id = ? ORDER BY name ASC", [get_current_user_id()])->fetchAll();
}

function get_tag($id) {
    return query("SELECT * FROM tags WHERE id = ? AND user_id = ?", [$id, get_current_user_id()])->fetch();
}

function create_tag($name) {
    return query("INSERT INTO tags (name, user_id) VALUES (?, ?)", [$name, get_current_user_id()]);
}

function update_tag($id, $name) {
    return query("UPDATE tags SET name = ? WHERE id = ? AND user_id = ?", [$name, $id, get_current_user_id()]);
}

function delete_tag($id) {
    return query("DELETE FROM tags WHERE id = ? AND user_id = ?", [$id, get_current_user_id()]);
}

/**
 * Given an array of tag names, return an array of tag IDs.
 * Creates tags that don't exist FOR THE CURRENT USER.
 */
function get_or_create_tags_by_names($names) {
    $tag_ids = [];
    $user_id = get_current_user_id();
    foreach ($names as $name) {
        $name = trim($name);
        if (empty($name)) continue;

        $tag = query("SELECT id FROM tags WHERE name = ? AND user_id = ?", [$name, $user_id])->fetch();
        if ($tag) {
            $tag_ids[] = $tag['id'];
        } else {
            query("INSERT INTO tags (name, user_id) VALUES (?, ?)", [$name, $user_id]);
            $tag_ids[] = get_db()->lastInsertId();
        }
    }
    return array_unique($tag_ids);
}

// --- Collections ---

function get_collections() {
    $user_id = get_current_user_id();
    return query("SELECT c.*, (SELECT COUNT(*) FROM prompt_collections pc WHERE pc.collection_id = c.id) as prompt_count 
                 FROM collections c 
                 WHERE c.user_id = ? 
                 ORDER BY c.name ASC", [$user_id])->fetchAll();
}

function get_collection($id) {
    return query("SELECT * FROM collections WHERE id = ? AND user_id = ?", [$id, get_current_user_id()])->fetch();
}

function create_collection($name, $description = '') {
    return query("INSERT INTO collections (name, description, user_id) VALUES (?, ?, ?)", [$name, $description, get_current_user_id()]);
}

function update_collection($id, $name, $description = '') {
    return query("UPDATE collections SET name = ?, description = ? WHERE id = ? AND user_id = ?", [$name, $description, $id, get_current_user_id()]);
}

function delete_collection($id) {
    return query("DELETE FROM collections WHERE id = ? AND user_id = ?", [$id, get_current_user_id()]);
}

function add_prompt_to_collection($prompt_id, $collection_id) {
    // Verify ownership of both
    $prompt = get_prompt($prompt_id);
    $collection = get_collection($collection_id);
    if ($prompt && $collection) {
        return query("INSERT OR IGNORE INTO prompt_collections (prompt_id, collection_id) VALUES (?, ?)", [$prompt_id, $collection_id]);
    }
    return false;
}

function remove_prompt_from_collection($prompt_id, $collection_id) {
    // Verify ownership of both (get_prompt and get_collection already scope by user_id)
    $prompt = get_prompt($prompt_id);
    $collection = get_collection($collection_id);
    if ($prompt && $collection) {
        return query("DELETE FROM prompt_collections WHERE prompt_id = ? AND collection_id = ?", [$prompt_id, $collection_id]);
    }
    return false;
}

// --- Prompts ---

function get_prompts($filters = []) {
    $user_id = get_current_user_id();
    $sql = "SELECT p.*, c.name as category_name, u.username as author_name 
            FROM prompts p 
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN users u ON p.user_id = u.id
            WHERE 1=1";
    $params = [];
    
    if (isset($filters['is_public'])) {
        $sql .= " AND p.is_public = ?";
        $params[] = $filters['is_public'] ? 1 : 0;
    } else {
        $sql .= " AND p.user_id = ?";
        $params[] = $user_id;
    }
    $where = [];

    if (!empty($filters['category_id'])) {
        $where[] = "p.category_id = ?";
        $params[] = $filters['category_id'];
    }

    if (!empty($filters['tag_id'])) {
        $where[] = "p.id IN (SELECT prompt_id FROM prompt_tags pt JOIN tags t ON pt.tag_id = t.id WHERE t.id = ?)";
        $params[] = $filters['tag_id'];
    }

    if (!empty($filters['collection_id'])) {
        $where[] = "p.id IN (SELECT prompt_id FROM prompt_collections pc JOIN collections cl ON pc.collection_id = cl.id WHERE cl.id = ?)";
        $params[] = $filters['collection_id'];
    }

    if (!empty($filters['search'])) {
        $where[] = "(p.title LIKE ? OR p.content LIKE ?)";
        $params[] = '%' . $filters['search'] . '%';
        $params[] = '%' . $filters['search'] . '%';
    }

    if ($where) {
        $sql .= " AND " . implode(" AND ", $where);
    }

    $sql .= " ORDER BY p.created_at DESC";

    if (!empty($filters['limit'])) {
        $sql .= " LIMIT " . (int)$filters['limit'];
        if (!empty($filters['offset'])) {
            $sql .= " OFFSET " . (int)$filters['offset'];
        }
    }

    return query($sql, $params)->fetchAll();
}

function get_prompt($id) {
    $user_id = get_current_user_id();
    $prompt = query("SELECT p.*, c.name as category_name, u.username as author_name 
                     FROM prompts p 
                     LEFT JOIN categories c ON p.category_id = c.id 
                     LEFT JOIN users u ON p.user_id = u.id
                     WHERE p.id = ? AND (p.user_id = ? OR p.is_public = 1)", [$id, $user_id])->fetch();
    
    if ($prompt) {
        $owner_id = $prompt['user_id'];
        $prompt['tags'] = query("SELECT t.* FROM tags t 
                                 JOIN prompt_tags pt ON t.id = pt.tag_id 
                                 WHERE pt.prompt_id = ?", [$id])->fetchAll();
        
        $prompt['collections'] = query("SELECT cl.* FROM collections cl 
                                        JOIN prompt_collections pc ON cl.id = pc.collection_id 
                                        WHERE pc.prompt_id = ?", [$id])->fetchAll();

        $prompt['images'] = get_prompt_images($id);
    }

    return $prompt;
}

function create_prompt($data) {
    $db = get_db();
    $db->beginTransaction();
    $user_id = get_current_user_id();
    $slug = slugify($data['title']);

    try {
        query("INSERT INTO prompts (title, slug, content, category_id, user_id, is_public) VALUES (?, ?, ?, ?, ?, ?)", [
            $data['title'],
            $slug,
            $data['content'],
            $data['category_id'] ?: null,
            $user_id,
            $data['is_public'] ?? 0
        ]);
        $prompt_id = $db->lastInsertId();

        if (!empty($data['tag_ids'])) {
            foreach ($data['tag_ids'] as $tag_id) {
                query("INSERT INTO prompt_tags (prompt_id, tag_id) VALUES (?, ?)", [$prompt_id, $tag_id]);
            }
        }

        if (!empty($data['collection_ids'])) {
            foreach ($data['collection_ids'] as $collection_id) {
                query("INSERT INTO prompt_collections (prompt_id, collection_id) VALUES (?, ?)", [$prompt_id, $collection_id]);
            }
        }

        $db->commit();
        return $prompt_id;
    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    }
}

function update_prompt($id, $data) {
    $db = get_db();
    $db->beginTransaction();
    $user_id = get_current_user_id();
    $slug = slugify($data['title']);

    try {
        // Ensure prompt belongs to user
        $existing = query("SELECT id FROM prompts WHERE id = ? AND user_id = ?", [$id, $user_id])->fetch();
        if (!$existing) {
            throw new Exception("Unauthorized or prompt not found.");
        }

        query("UPDATE prompts SET title = ?, slug = ?, content = ?, category_id = ?, is_public = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND user_id = ?", [
            $data['title'],
            $slug,
            $data['content'],
            $data['category_id'] ?: null,
            $data['is_public'] ?? 0,
            $id,
            $user_id
        ]);

        // Sync tags
        query("DELETE FROM prompt_tags WHERE prompt_id = ?", [$id]);
        if (!empty($data['tag_ids'])) {
            foreach ($data['tag_ids'] as $tag_id) {
                query("INSERT INTO prompt_tags (prompt_id, tag_id) VALUES (?, ?)", [$id, $tag_id]);
            }
        }

        // Sync collections
        query("DELETE FROM prompt_collections WHERE prompt_id = ?", [$id]);
        if (!empty($data['collection_ids'])) {
            foreach ($data['collection_ids'] as $collection_id) {
                query("INSERT INTO prompt_collections (prompt_id, collection_id) VALUES (?, ?)", [$id, $collection_id]);
            }
        }

        $db->commit();
        return true;
    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    }
}

// --- Images ---

function get_prompt_images($prompt_id) {
    return query("SELECT * FROM prompt_images WHERE prompt_id = ? ORDER BY created_at ASC", [$prompt_id])->fetchAll();
}

function add_prompt_image($prompt_id, $image_path) {
    return query("INSERT INTO prompt_images (prompt_id, image_path) VALUES (?, ?)", [$prompt_id, $image_path]);
}

function delete_prompt_image($image_id) {
    $image = query("SELECT * FROM prompt_images WHERE id = ?", [$image_id])->fetch();
    if ($image) {
        // Only allow if user owns the prompt
        $prompt = get_prompt($image['prompt_id']);
        if ($prompt && $prompt['user_id'] === get_current_user_id()) {
            if (file_exists($image['image_path'])) {
                unlink($image['image_path']);
            }
            return query("DELETE FROM prompt_images WHERE id = ?", [$image_id]);
        }
    }
    return false;
}

function increment_prompt_view_count($id) {
    return query("UPDATE prompts SET view_count = view_count + 1 WHERE id = ?", [$id]);
}

function increment_prompt_copy_count($id) {
    return query("UPDATE prompts SET copy_count = copy_count + 1 WHERE id = ?", [$id]);
}

function delete_prompt($id) {
    // Delete image files first
    $images = get_prompt_images($id);
    foreach ($images as $image) {
        if (file_exists($image['image_path'])) {
            unlink($image['image_path']);
        }
    }
    return query("DELETE FROM prompts WHERE id = ? AND user_id = ?", [$id, get_current_user_id()]);
}

/**
 * Seed default categories, tags, and collections for a new user.
 * 
 * @param int $user_id
 */
function seed_user_onboarding($user_id) {
    // 1. Categories
    $categories = [
        'Coding Prompts', 'Debugging', 'Code Review', 'System Design', 
        'API Design', 'Database Design', 'Prompt Engineering', 'AI Agents', 
        'Content Creation', 'Copywriting', 'SEO', 'Marketing', 
        'Social Media', 'Business Strategy', 'Productivity', 'Automation', 
        'Image Generation', 'UI/UX Design', 'Education & Learning'
    ];
    foreach ($categories as $name) {
        query("INSERT OR IGNORE INTO categories (name, user_id) VALUES (?, ?)", [$name, $user_id]);
    }

    // 2. Tags
    $tags = [
        // Technical
        'laravel', 'vue', 'react', 'nodejs', 'api', 'backend', 'frontend', 
        'database', 'sql', 'performance', 'optimization', 'debugging', 'refactor',
        // AI & Automation
        'chatgpt', 'prompt-engineering', 'system-prompt', 'ai-agent', 'llm', 
        'automation', 'workflow',
        // Content & SEO
        'seo', 'copywriting', 'marketing', 'blog', 'email', 'ad-copy', 
        'storytelling', 'social-media',
        // Business
        'startup', 'strategy', 'growth', 'monetization', 'idea', 'planning',
        // Creative
        'image-generation', 'midjourney', 'stable-diffusion', 'branding', 
        'design', 'uiux',
        // Intent
        'beginner', 'advanced', 'production-ready', 'template', 'reusable'
    ];
    foreach ($tags as $name) {
        query("INSERT OR IGNORE INTO tags (name, user_id) VALUES (?, ?)", [$name, $user_id]);
    }

    // 3. Collections
    $collections = [
        ['name' => 'Starter Prompts Pack', 'desc' => 'Essential prompts for beginners to get started with AI.'],
        ['name' => 'AI Beginner Toolkit', 'desc' => 'Foundational tools for new prompt engineers.'],
        ['name' => 'First Time Setup Prompts', 'desc' => 'Prompts to help configure your initial environment.'],
        ['name' => 'Backend Engineering Pack', 'desc' => 'Advanced prompts for server-side logic and architecture.'],
        ['name' => 'Laravel Master Prompts', 'desc' => 'Expert-level prompts for Laravel framework development.'],
        ['name' => 'API Design Pack', 'desc' => 'Best practices and templates for REST/GraphQL API design.'],
        ['name' => 'Debugging Toolkit', 'desc' => 'Systematic prompts for troubleshooting and bug fixing.'],
        ['name' => 'Code Review Pack', 'desc' => 'Automated code review and quality assurance prompts.'],
        ['name' => 'System Prompt Library', 'desc' => 'High-level persona and behavioral definitions for AI agents.'],
        ['name' => 'AI Agent Builders', 'desc' => 'Prompts for designing autonomous agent workflows.'],
        ['name' => 'Multi-Agent Workflows', 'desc' => 'Coordinating complex tasks between multiple AI personas.'],
        ['name' => 'Automation Agents Pack', 'desc' => 'Prompts focused on task automation and background jobs.'],
        ['name' => 'SEO Blog Generator Pack', 'desc' => 'Complete workflow for search-optimized content creation.'],
        ['name' => 'Social Media Growth Pack', 'desc' => 'Engaging prompts for Twitter, LinkedIn, and more.'],
        ['name' => 'Copywriting Conversion Pack', 'desc' => 'High-converting sales copy and marketing scripts.'],
        ['name' => 'Email Marketing Pack', 'desc' => 'Drip campaign and newsletter templates.'],
        ['name' => 'Startup Idea Generator Pack', 'desc' => 'Innovation and brainstorming tools for new ventures.'],
        ['name' => 'Business Strategy Toolkit', 'desc' => 'Analytical prompts for market positioning and SWOT.'],
        ['name' => 'SaaS Planning Pack', 'desc' => 'Strategic planning for software-as-a-service products.'],
        ['name' => 'Monetization Strategies Pack', 'desc' => 'Prompts for revenue model optimization.'],
        ['name' => 'Image Prompt Pack', 'desc' => 'Professional prompts for DALL-E, Midjourney, and SD.'],
        ['name' => 'Branding Generator Pack', 'desc' => 'Identity, logo ideas, and brand voice definition.'],
        ['name' => 'UI/UX Idea Pack', 'desc' => 'User experience flow and interface design brainstorming.'],
        ['name' => 'Ad Creative Pack', 'desc' => 'Creative concepts for digital advertising campaigns.'],
        ['name' => 'Daily Planning Prompts', 'desc' => 'Time management and daily objective setting.'],
        ['name' => 'Workflow Automation Pack', 'desc' => 'Optimizing personal and professional productivity.'],
        ['name' => 'Personal Productivity AI', 'desc' => 'Tailored prompts for focus and habit tracking.']
    ];
    foreach ($collections as $c) {
        query("INSERT OR IGNORE INTO collections (name, description, user_id) VALUES (?, ?, ?)", [$c['name'], $c['desc'], $user_id]);
    }
}

/**
 * Check if a prompt is public without needing user_id.
 */
function is_prompt_public($id) {
    $prompt = query("SELECT is_public FROM prompts WHERE id = ?", [$id])->fetch();
    return $prompt && $prompt['is_public'];
}

/**
 * Get all public prompts for sitemap.
 */
function get_public_prompts() {
    return query("SELECT id, updated_at FROM prompts WHERE is_public = 1 ORDER BY updated_at DESC")->fetchAll();
}
