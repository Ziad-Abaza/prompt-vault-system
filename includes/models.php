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
    return query("SELECT * FROM collections WHERE user_id = ? ORDER BY name ASC", [get_current_user_id()])->fetchAll();
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
    $sql = "SELECT p.*, c.name as category_name 
            FROM prompts p 
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.user_id = ?";
    $params = [$user_id];
    $where = [];

    if (!empty($filters['category_id'])) {
        $where[] = "p.category_id = ?";
        $params[] = $filters['category_id'];
    }

    if (!empty($filters['tag_id'])) {
        $where[] = "p.id IN (SELECT prompt_id FROM prompt_tags pt JOIN tags t ON pt.tag_id = t.id WHERE t.id = ? AND t.user_id = ?)";
        $params[] = $filters['tag_id'];
        $params[] = $user_id;
    }

    if (!empty($filters['collection_id'])) {
        $where[] = "p.id IN (SELECT prompt_id FROM prompt_collections pc JOIN collections cl ON pc.collection_id = cl.id WHERE cl.id = ? AND cl.user_id = ?)";
        $params[] = $filters['collection_id'];
        $params[] = $user_id;
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

    return query($sql, $params)->fetchAll();
}

function get_prompt($id) {
    $user_id = get_current_user_id();
    $prompt = query("SELECT p.*, c.name as category_name 
                     FROM prompts p 
                     LEFT JOIN categories c ON p.category_id = c.id 
                     WHERE p.id = ? AND p.user_id = ?", [$id, $user_id])->fetch();
    
    if ($prompt) {
        $prompt['tags'] = query("SELECT t.* FROM tags t 
                                 JOIN prompt_tags pt ON t.id = pt.tag_id 
                                 WHERE pt.prompt_id = ? AND t.user_id = ?", [$id, $user_id])->fetchAll();
        
        $prompt['collections'] = query("SELECT cl.* FROM collections cl 
                                        JOIN prompt_collections pc ON cl.id = pc.collection_id 
                                        WHERE pc.prompt_id = ? AND cl.user_id = ?", [$id, $user_id])->fetchAll();
    }

    return $prompt;
}

function create_prompt($data) {
    $db = get_db();
    $db->beginTransaction();
    $user_id = get_current_user_id();

    try {
        query("INSERT INTO prompts (title, content, category_id, user_id) VALUES (?, ?, ?, ?)", [
            $data['title'],
            $data['content'],
            $data['category_id'] ?: null,
            $user_id
        ]);
        $prompt_id = $db->lastInsertId();

        if (!empty($data['tag_ids'])) {
            foreach ($data['tag_ids'] as $tag_id) {
                // Verify tag ownership
                $tag = query("SELECT id FROM tags WHERE id = ? AND user_id = ?", [$tag_id, $user_id])->fetch();
                if ($tag) {
                    query("INSERT INTO prompt_tags (prompt_id, tag_id) VALUES (?, ?)", [$prompt_id, $tag_id]);
                }
            }
        }

        if (!empty($data['collection_ids'])) {
            foreach ($data['collection_ids'] as $collection_id) {
                // Verify collection ownership
                $coll = query("SELECT id FROM collections WHERE id = ? AND user_id = ?", [$collection_id, $user_id])->fetch();
                if ($coll) {
                    query("INSERT INTO prompt_collections (prompt_id, collection_id) VALUES (?, ?)", [$prompt_id, $collection_id]);
                }
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

    try {
        // Ensure prompt belongs to user
        $existing = query("SELECT id FROM prompts WHERE id = ? AND user_id = ?", [$id, $user_id])->fetch();
        if (!$existing) {
            throw new Exception("Unauthorized or prompt not found.");
        }

        query("UPDATE prompts SET title = ?, content = ?, category_id = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND user_id = ?", [
            $data['title'],
            $data['content'],
            $data['category_id'] ?: null,
            $id,
            $user_id
        ]);

        // Sync tags
        query("DELETE FROM prompt_tags WHERE prompt_id = ?", [$id]);
        if (!empty($data['tag_ids'])) {
            foreach ($data['tag_ids'] as $tag_id) {
                $tag = query("SELECT id FROM tags WHERE id = ? AND user_id = ?", [$tag_id, $user_id])->fetch();
                if ($tag) {
                    query("INSERT INTO prompt_tags (prompt_id, tag_id) VALUES (?, ?)", [$id, $tag_id]);
                }
            }
        }

        // Sync collections
        query("DELETE FROM prompt_collections WHERE prompt_id = ?", [$id]);
        if (!empty($data['collection_ids'])) {
            foreach ($data['collection_ids'] as $collection_id) {
                $coll = query("SELECT id FROM collections WHERE id = ? AND user_id = ?", [$collection_id, $user_id])->fetch();
                if ($coll) {
                    query("INSERT INTO prompt_collections (prompt_id, collection_id) VALUES (?, ?)", [$id, $collection_id]);
                }
            }
        }

        $db->commit();
        return true;
    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    }
}

function delete_prompt($id) {
    return query("DELETE FROM prompts WHERE id = ? AND user_id = ?", [$id, get_current_user_id()]);
}
