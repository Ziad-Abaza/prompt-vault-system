<?php
/**
 * Application Bootstrap
 */

// Load Environment Loader
require_once __DIR__ . '/includes/env.php';
Env::load(__DIR__ . '/.env');

// Define global application name
define('APP_NAME', Env::get('APP_NAME', 'Atlas Library'));

// Error reporting configuration
$debug = Env::get('APP_DEBUG', false);
if ($debug) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Set timezone
date_default_timezone_set('UTC');

// Secure Session Start
if (session_status() === PHP_SESSION_NONE) {
    $secure = Env::get('APP_ENV') === 'production';
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

// Load core files
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/models.php';

// Enforce authentication and CSRF only for web requests
if (php_sapi_name() !== 'cli') {
    require_login();

    // Verify CSRF on all POST requests
    verify_csrf_or_die();
}

/**
 * Set HTTP response code and show the error page.
 */
function abort($code = 404) {
    http_response_code($code);
    $_GET['code'] = $code; // For error.php to pick up
    require __DIR__ . '/error.php';
    exit;
}

/**
 * Redirect to a given URL.
 */
function redirect($path) {
    header("Location: $path");
    exit;
}

/**
 * Set a flash message in the session.
 */
function set_flash($message, $type = 'success') {
    $_SESSION['flash'] = [
        'message' => $message,
        'type' => $type
    ];
}

/**
 * Get and clear the flash message.
 */
function get_flash() {
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}
