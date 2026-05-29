<?php
/**
 * Data Models / Logic
 */

// --- Categories ---

function get_categories() {
    return query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
}

function get_category($id) {
    return query("SELECT * FROM categories WHERE id = ?", [$id])->fetch();
}

function create_category($name) {
    return query("INSERT INTO categories (name) VALUES (?)", [$name]);
}

function update_category($id, $name) {
    return query("UPDATE categories SET name = ? WHERE id = ?", [$name, $id]);
}

function delete_category($id) {
    return query("DELETE FROM categories WHERE id = ?", [$id]);
}

// --- Tags ---

function get_tags() {
    return query("SELECT * FROM tags ORDER BY name ASC")->fetchAll();
}

function get_tag($id) {
    return query("SELECT * FROM tags WHERE id = ?", [$id])->fetch();
}

function create_tag($name) {
    return query("INSERT INTO tags (name) VALUES (?)", [$name]);
}

function update_tag($id, $name) {
    return query("UPDATE tags SET name = ? WHERE id = ?", [$name, $id]);
}

function delete_tag($id) {
    return query("DELETE FROM tags WHERE id = ?", [$id]);
}

/**
 * Given an array of tag names, return an array of tag IDs.
 * Creates tags that don't exist.
 */
function get_or_create_tags_by_names($names) {
    $tag_ids = [];
    foreach ($names as $name) {
        $name = trim($name);
        if (empty($name)) continue;

        $tag = query("SELECT id FROM tags WHERE name = ?", [$name])->fetch();
        if ($tag) {
            $tag_ids[] = $tag['id'];
        } else {
            query("INSERT INTO tags (name) VALUES (?)", [$name]);
            $tag_ids[] = get_db()->lastInsertId();
        }
    }
    return array_unique($tag_ids);
}

// --- Collections ---

function get_collections() {
    return query("SELECT * FROM collections ORDER BY name ASC")->fetchAll();
}

function get_collection($id) {
    return query("SELECT * FROM collections WHERE id = ?", [$id])->fetch();
}

function create_collection($name, $description = '') {
    return query("INSERT INTO collections (name, description) VALUES (?, ?)", [$name, $description]);
}

function update_collection($id, $name, $description = '') {
    return query("UPDATE collections SET name = ?, description = ? WHERE id = ?", [$name, $description, $id]);
}

function delete_collection($id) {
    return query("DELETE FROM collections WHERE id = ?", [$id]);
}

function add_prompt_to_collection($prompt_id, $collection_id) {
    return query("INSERT OR IGNORE INTO prompt_collections (prompt_id, collection_id) VALUES (?, ?)", [$prompt_id, $collection_id]);
}

function remove_prompt_from_collection($prompt_id, $collection_id) {
    return query("DELETE FROM prompt_collections WHERE prompt_id = ? AND collection_id = ?", [$prompt_id, $collection_id]);
}

// --- Prompts ---

function get_prompts($filters = []) {
    $sql = "SELECT p.*, c.name as category_name 
            FROM prompts p 
            LEFT JOIN categories c ON p.category_id = c.id";
    $params = [];
    $where = [];

    if (!empty($filters['category_id'])) {
        $where[] = "p.category_id = ?";
        $params[] = $filters['category_id'];
    }

    if (!empty($filters['tag_id'])) {
        $where[] = "p.id IN (SELECT prompt_id FROM prompt_tags WHERE tag_id = ?)";
        $params[] = $filters['tag_id'];
    }

    if (!empty($filters['collection_id'])) {
        $where[] = "p.id IN (SELECT prompt_id FROM prompt_collections WHERE collection_id = ?)";
        $params[] = $filters['collection_id'];
    }

    if (!empty($filters['search'])) {
        $where[] = "(p.title LIKE ? OR p.content LIKE ?)";
        $params[] = '%' . $filters['search'] . '%';
        $params[] = '%' . $filters['search'] . '%';
    }

    if ($where) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }

    $sql .= " ORDER BY p.created_at DESC";

    return query($sql, $params)->fetchAll();
}

function get_prompt($id) {
    $prompt = query("SELECT p.*, c.name as category_name 
                     FROM prompts p 
                     LEFT JOIN categories c ON p.category_id = c.id 
                     WHERE p.id = ?", [$id])->fetch();
    
    if ($prompt) {
        $prompt['tags'] = query("SELECT t.* FROM tags t 
                                 JOIN prompt_tags pt ON t.id = pt.tag_id 
                                 WHERE pt.prompt_id = ?", [$id])->fetchAll();
        
        $prompt['collections'] = query("SELECT cl.* FROM collections cl 
                                        JOIN prompt_collections pc ON cl.id = pc.collection_id 
                                        WHERE pc.prompt_id = ?", [$id])->fetchAll();
    }

    return $prompt;
}

function create_prompt($data) {
    $db = get_db();
    $db->beginTransaction();

    try {
        query("INSERT INTO prompts (title, content, category_id) VALUES (?, ?, ?)", [
            $data['title'],
            $data['content'],
            $data['category_id'] ?: null
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

    try {
        query("UPDATE prompts SET title = ?, content = ?, category_id = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?", [
            $data['title'],
            $data['content'],
            $data['category_id'] ?: null,
            $id
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

function delete_prompt($id) {
    return query("DELETE FROM prompts WHERE id = ?", [$id]);
}
