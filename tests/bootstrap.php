<?php
/**
 * Bootstrap file for PHPUnit tests
 */

// Define constants for testing
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__FILE__) . '/../vendor/wordpress/wordpress/src/');
}

if (!defined('WP_PLUGIN_DIR')) {
    define('WP_PLUGIN_DIR', dirname(__FILE__) . '/../');
}

if (!defined('WP_PLUGIN_URL')) {
    define('WP_PLUGIN_URL', 'http://example.com/wp-content/plugins/');
}

// Mock WordPress functions if not available
if (!function_exists('__')) {
    function __($text, $domain = 'default') {
        return $text;
    }
}

if (!function_exists('_e')) {
    function _e($text, $domain = 'default') {
        echo $text;
    }
}

if (!function_exists('esc_html')) {
    function esc_html($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('esc_html_e')) {
    function esc_html_e($text, $domain = 'default') {
        echo esc_html($text);
    }
}

if (!function_exists('plugin_dir_path')) {
    function plugin_dir_path($file) {
        return dirname($file) . '/';
    }
}

if (!function_exists('plugin_dir_url')) {
    function plugin_dir_url($file) {
        return 'http://example.com/wp-content/plugins/' . basename(dirname($file)) . '/';
    }
}

if (!function_exists('get_bloginfo')) {
    function get_bloginfo($show = '', $filter = 'raw') {
        if ($show === 'version') {
            return '6.0';
        }
        return '';
    }
}

if (!function_exists('get_admin_page_title')) {
    function get_admin_page_title() {
        return 'Help Plugin';
    }
}

// Load the plugin
require_once dirname(__FILE__) . '/../help-plugin.php';

