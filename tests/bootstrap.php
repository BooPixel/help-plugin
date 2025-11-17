<?php
/**
 * Bootstrap file for PHPUnit tests
 */

// Define WordPress constants
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__DIR__) . '/');
}

if (!defined('WP_CONTENT_DIR')) {
    define('WP_CONTENT_DIR', ABSPATH . 'wp-content');
}

if (!defined('WP_PLUGIN_DIR')) {
    define('WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins');
}

if (!defined('DB_NAME')) {
    define('DB_NAME', 'test_db');
}

// Load plugin
require_once dirname(__DIR__) . '/help-plugin.php';

