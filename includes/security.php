<?php
/**
 * Security Helper Functions
 */

/**
 * Escape HTML for output to prevent XSS.
 */
function esc($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Return the CSRF token. Generates one if missing.
 */
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Return a hidden CSRF input field for forms.
 */
function csrf_input() {
    return '<input type="hidden" name="csrf_token" value="' . esc(csrf_token()) . '">';
}

/**
 * Validate a CSRF token from a request.
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
            abort(403);
        }
    }
}

/**
 * Input Validation Class
 */
class Validator {
    private $data;
    private $errors = [];

    public function __construct($data) {
        $this->data = $data;
    }

    public function required($field, $message = null) {
        if (empty(trim($this->data[$field] ?? ''))) {
            $this->errors[$field] = $message ?? ucfirst($field) . " is required.";
        }
        return $this;
    }

    public function min($field, $length, $message = null) {
        if (!empty($this->data[$field]) && strlen($this->data[$field]) < $length) {
            $this->errors[$field] = $message ?? ucfirst($field) . " must be at least $length characters.";
        }
        return $this;
    }

    public function max($field, $length, $message = null) {
        if (!empty($this->data[$field]) && strlen($this->data[$field]) > $length) {
            $this->errors[$field] = $message ?? ucfirst($field) . " cannot exceed $length characters.";
        }
        return $this;
    }

    public function get_errors() {
        return $this->errors;
    }

    public function is_valid() {
        return empty($this->errors);
    }
}
