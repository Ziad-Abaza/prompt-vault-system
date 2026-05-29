<?php
/**
 * Application Bootstrap
 */

// Error reporting for development (should be disabled in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set timezone
date_default_timezone_set('UTC');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load core files
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/models.php';

// Verify CSRF on all POST requests
verify_csrf_or_die();

/**
 * Redirect to a given URL.
 * 
 * @param string $path
 */
function redirect($path) {
    header("Location: $path");
    exit;
}

/**
 * Set a flash message in the session.
 * 
 * @param string $message
 * @param string $type success|error|info
 */
function set_flash($message, $type = 'success') {
    $_SESSION['flash'] = [
        'message' => $message,
        'type' => $type
    ];
}

/**
 * Get and clear the flash message.
 * 
 * @return array|null
 */
function get_flash() {
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}
