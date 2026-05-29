<?php
/**
 * Security Helper Functions
 */

// Generate a CSRF token if one doesn't exist
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/**
 * Escape HTML for output to prevent XSS.
 * 
 * @param string|null $string The string to escape.
 * @return string The escaped string.
 */
function esc($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Return the CSRF token.
 * 
 * @return string
 */
function csrf_token() {
    return $_SESSION['csrf_token'];
}

/**
 * Return a hidden CSRF input field for forms.
 * 
 * @return string
 */
function csrf_input() {
    return '<input type="hidden" name="csrf_token" value="' . esc(csrf_token()) . '">';
}

/**
 * Validate a CSRF token from a request.
 * 
 * @param string|null $token The token to validate.
 * @return bool
 */
function validate_csrf($token) {
    if (!$token || !isset($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Ensure the current request has a valid CSRF token if it's a POST.
 */
function verify_csrf_or_die() {
    if (PHP_SAPI !== 'cli' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        if (!validate_csrf($token)) {
            http_response_code(403);
            die('CSRF validation failed.');
        }
    }
}
