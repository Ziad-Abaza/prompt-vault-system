<?php
/**
 * Database Helper Functions
 */

define('DB_PATH', __DIR__ . '/../data/database.sqlite');

/**
 * Get a PDO database connection.
 * 
 * @return PDO
 */
function get_db() {
    static $pdo = null;

    if ($pdo === null) {
        $db_exists = file_exists(DB_PATH);
        
        // Ensure data directory exists
        if (!is_dir(dirname(DB_PATH))) {
            mkdir(dirname(DB_PATH), 0777, true);
        }

        try {
            $pdo = new PDO('sqlite:' . DB_PATH);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $pdo->exec('PRAGMA foreign_keys = ON;');

            init_database($pdo);
            migrate_database($pdo);
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }

    return $pdo;
}

/**
 * Initialize the database schema.
 * 
 * @param PDO $pdo
 */
function init_database($pdo) {
    $sql = "
    CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL UNIQUE,
        password_hash TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS categories (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        user_id INTEGER,
        UNIQUE(name, user_id),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );

    CREATE TABLE IF NOT EXISTS tags (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        user_id INTEGER,
        UNIQUE(name, user_id),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );

    CREATE TABLE IF NOT EXISTS collections (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        description TEXT,
        user_id INTEGER,
        UNIQUE(name, user_id),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );

    CREATE TABLE IF NOT EXISTS prompts (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        slug TEXT,
        content TEXT NOT NULL,
        category_id INTEGER,
        user_id INTEGER,
        is_public BOOLEAN DEFAULT FALSE,
        view_count INTEGER DEFAULT 0,
        copy_count INTEGER DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );

    CREATE TABLE IF NOT EXISTS prompt_images (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        prompt_id INTEGER NOT NULL,
        image_path TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (prompt_id) REFERENCES prompts(id) ON DELETE CASCADE
    );

    CREATE TABLE IF NOT EXISTS prompt_tags (
        prompt_id INTEGER,
        tag_id INTEGER,
        PRIMARY KEY (prompt_id, tag_id),
        FOREIGN KEY (prompt_id) REFERENCES prompts(id) ON DELETE CASCADE,
        FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
    );

    CREATE TABLE IF NOT EXISTS prompt_collections (
        prompt_id INTEGER,
        collection_id INTEGER,
        PRIMARY KEY (prompt_id, collection_id),
        FOREIGN KEY (prompt_id) REFERENCES prompts(id) ON DELETE CASCADE,
        FOREIGN KEY (collection_id) REFERENCES collections(id) ON DELETE CASCADE
    );

    CREATE INDEX IF NOT EXISTS idx_prompts_title ON prompts(title);
    CREATE INDEX IF NOT EXISTS idx_prompts_slug ON prompts(slug);
    CREATE INDEX IF NOT EXISTS idx_prompts_category ON prompts(category_id);
    CREATE INDEX IF NOT EXISTS idx_prompts_user ON prompts(user_id);
    ";

    $pdo->exec($sql);
}

/**
 * Migrate existing database schema.
 */
function migrate_database($pdo) {
    // Add users table if it doesn't exist
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            password_hash TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
    ");

    // Add user_id column to existing tables if missing
    $tables = ['categories', 'tags', 'collections', 'prompts'];
    foreach ($tables as $table) {
        $columns = $pdo->query("PRAGMA table_info($table)")->fetchAll(PDO::FETCH_COLUMN, 1);
        if (!in_array('user_id', $columns)) {
            try {
                $pdo->exec("ALTER TABLE $table ADD COLUMN user_id INTEGER REFERENCES users(id) ON DELETE CASCADE;");
            } catch (PDOException $e) {
                // Ignore if column already exists
            }
        }
    }

    // Add is_public column to prompts table if missing
    $columns = $pdo->query("PRAGMA table_info(prompts)")->fetchAll(PDO::FETCH_COLUMN, 1);
    if (!in_array('is_public', $columns)) {
        try {
            $pdo->exec("ALTER TABLE prompts ADD COLUMN is_public BOOLEAN DEFAULT FALSE;");
        } catch (PDOException $e) {}
    }

    if (!in_array('slug', $columns)) {
        try {
            $pdo->exec("ALTER TABLE prompts ADD COLUMN slug TEXT;");
            $pdo->exec("CREATE INDEX IF NOT EXISTS idx_prompts_slug ON prompts(slug);");
        } catch (PDOException $e) {}
    }

    if (!in_array('view_count', $columns)) {
        try {
            $pdo->exec("ALTER TABLE prompts ADD COLUMN view_count INTEGER DEFAULT 0;");
        } catch (PDOException $e) {}
    }

    if (!in_array('copy_count', $columns)) {
        try {
            $pdo->exec("ALTER TABLE prompts ADD COLUMN copy_count INTEGER DEFAULT 0;");
        } catch (PDOException $e) {}
    }

    // Create prompt_images table if it doesn't exist
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS prompt_images (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            prompt_id INTEGER NOT NULL,
            image_path TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (prompt_id) REFERENCES prompts(id) ON DELETE CASCADE
        );
    ");
}

/**
 * Execute a query with parameters.
 * 
 * @param string $sql
 * @param array $params
 * @return PDOStatement
 */
function query($sql, $params = []) {
    $db = get_db();
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}
