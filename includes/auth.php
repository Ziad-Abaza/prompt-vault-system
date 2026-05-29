<?php
/**
 * Authentication Helper Functions
 */

/**
 * Register a new user.
 * 
 * @param string $username
 * @param string $password
 * @return bool
 */
function register($username, $password) {
    $username = trim($username);
    if (strlen($username) < 3) {
        throw new Exception("Username must be at least 3 characters.");
    }
    if (strlen($password) < 6) {
        throw new Exception("Password must be at least 6 characters.");
    }

    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    try {
        query("INSERT INTO users (username, password_hash) VALUES (?, ?)", [$username, $password_hash]);
        return true;
    } catch (PDOException $e) {
        // SQLITE_CONSTRAINT_UNIQUE is 19, but PDO might return different codes. 
        // 23000 is common for unique violations in many drivers.
        if (strpos($e->getMessage(), 'UNIQUE constraint failed') !== false) {
            throw new Exception("Username already exists.");
        }
        throw $e;
    }
}

/**
 * Attempt to login a user.
 * 
 * @param string $username
 * @param string $password
 * @return bool
 */
function login($username, $password) {
    $user = query("SELECT * FROM users WHERE username = ?", [$username])->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        // Regenerate session ID for security
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        return true;
    }

    return false;
}

/**
 * Logout the current user.
 */
function logout() {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}

/**
 * Check if a user is logged in.
 * 
 * @return bool
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Get the current user's ID.
 * 
 * @return int|null
 */
function get_current_user_id() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get the current user's username.
 * 
 * @return string
 */
function get_current_username() {
    return $_SESSION['username'] ?? '';
}

/**
 * Require a user to be logged in, otherwise redirect to login.
 */
function require_login() {
    if (!is_logged_in()) {
        $current_page = basename($_SERVER['PHP_SELF']);
        if (!in_array($current_page, ['login.php', 'register.php'])) {
            header("Location: login.php");
            exit;
        }
    }
}
