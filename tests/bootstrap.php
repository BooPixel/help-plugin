<?php
/**
 * Bootstrap file for PHPUnit tests
 */

// Prevent direct access (for test bootstrap, we define ABSPATH if not set)
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__DIR__) . '/');
} else {
    // If ABSPATH is already defined, this might be a direct access attempt
    // Exit only if not in a test environment
    if (!defined('WP_TESTS_FORCE_KNOWN_BUGS')) {
        exit; // Exit if accessed directly outside test context
    }
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
require_once dirname(__DIR__) . '/boopixel-ai-chat-for-n8n.php';

