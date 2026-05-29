<?php
/**
 * Simple Environment Variable Loader
 */

class Env {
    private static $variables = [];

    public static function load($path) {
        if (!file_exists($path)) {
            if (isset($_SERVER['APP_ENV']) && $_SERVER['APP_ENV'] === 'production') {
                die('.env file missing in production environment.');
            }
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;

            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            // Remove quotes if present
            $value = trim($value, '"\'');

            self::$variables[$name] = $value;
            putenv("$name=$value");
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }

    public static function get($name, $default = null) {
        $value = getenv($name);
        if ($value === false) {
            return $default;
        }

        // Convert common string values to booleans/nulls
        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return null;
        }

        return $value;
    }
}
