<?php
/**
 * Plugin Name: BooChat Connect
 * Plugin URI: https://boopixel.com/boochat-connect
 * Description: AI Chatbot & n8n Automation - Modern, lightweight chatbot popup that integrates seamlessly with n8n. Automate workflows, respond in real-time, collect leads, and connect to any AI model or external service. Perfect for 24/7 AI support, sales automation, and smart customer interactions.
 * Version: 1.0.45
 * Author: BooPixel
 * Author URI: https://boopixel.com
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: boochat-connect
 * Requires at least: 5.0
 * Tested up to: 6.8
 * Requires PHP: 7.2
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define constants
define('BOOCHAT_CONNECT_VERSION', '1.0.45');
define('BOOCHAT_CONNECT_DIR', plugin_dir_path(__FILE__));
define('BOOCHAT_CONNECT_URL', plugin_dir_url(__FILE__));

// Load helper functions
require_once BOOCHAT_CONNECT_DIR . 'includes/helpers.php';

// Load classes
require_once BOOCHAT_CONNECT_DIR . 'includes/class-boochat-connect-database.php';
require_once BOOCHAT_CONNECT_DIR . 'includes/class-boochat-connect-api.php';
require_once BOOCHAT_CONNECT_DIR . 'includes/class-boochat-connect-settings.php';
require_once BOOCHAT_CONNECT_DIR . 'includes/class-boochat-connect-statistics.php';
require_once BOOCHAT_CONNECT_DIR . 'includes/class-boochat-connect-ajax.php';
require_once BOOCHAT_CONNECT_DIR . 'includes/class-boochat-connect-admin.php';
require_once BOOCHAT_CONNECT_DIR . 'includes/class-boochat-connect-frontend.php';

/**
 * Main plugin class
 */
class BooChat_Connect {
    
    /**
     * Single instance of the plugin
     *
     * @var BooChat_Connect
     */
    private static $instance = null;
    
    /**
     * Database instance
     *
     * @var BooChat_Connect_Database
     */
    private $database;
    
    /**
     * API instance
     *
     * @var BooChat_Connect_API
     */
    private $api;
    
    /**
     * Settings instance
     *
     * @var BooChat_Connect_Settings
     */
    private $settings;
    
    /**
     * Statistics instance
     *
     * @var BooChat_Connect_Statistics
     */
    private $statistics;
    
    /**
     * AJAX instance
     *
     * @var BooChat_Connect_Ajax
     */
    private $ajax;
    
    /**
     * Admin instance
     *
     * @var BooChat_Connect_Admin
     */
    private $admin;
    
    /**
     * Frontend instance
     *
     * @var BooChat_Connect_Frontend
     */
    private $frontend;
    
    /**
     * Get single instance
     *
     * @return BooChat_Connect Single instance of the plugin
     */
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Plugin activation hook
     */
    public static function activate() {
        $database = new BooChat_Connect_Database();
        $database->create_table();
        flush_rewrite_rules();
    }
    
    /**
     * Plugin deactivation hook
     */
    public static function deactivate() {
        flush_rewrite_rules();
    }
    
    /**
     * Plugin uninstall hook - Remove all plugin data
     */
    public static function uninstall() {
        // Check if user has permission
        if (!current_user_can('activate_plugins')) {
            return;
        }
        
        // Remove all plugin options
        $options = array(
            'boochat_connect_cache_version',
            'boochat_connect_language',
            'boochat_connect_chat_name',
            'boochat_connect_welcome_message',
            'boochat_connect_primary_color',
            'boochat_connect_secondary_color',
            'boochat_connect_chat_bg_color',
            'boochat_connect_text_color',
            'boochat_connect_font_family',
            'boochat_connect_font_size',
            'boochat_connect_api_url'
        );
        
        foreach ($options as $option) {
            delete_option($option);
        }
        
        // Drop database table
        $database = new BooChat_Connect_Database();
        $database->drop_table();
        
        // Clear any cached data
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        // Initialize core components
        $this->database = new BooChat_Connect_Database();
        $this->api = new BooChat_Connect_API();
        $this->settings = new BooChat_Connect_Settings();
        $this->statistics = new BooChat_Connect_Statistics($this->database);
        
        // Initialize feature components
        $this->ajax = new BooChat_Connect_Ajax($this->database, $this->api);
        $this->admin = new BooChat_Connect_Admin($this->settings, $this->api, $this->statistics);
        $this->frontend = new BooChat_Connect_Frontend($this->settings);
    }
}

/**
 * Initialize the plugin
 */
function boochat_connect_init() {
    return BooChat_Connect::get_instance();
}

// Register activation, deactivation and uninstall hooks
register_activation_hook(__FILE__, array('BooChat_Connect', 'activate'));
register_deactivation_hook(__FILE__, array('BooChat_Connect', 'deactivate'));
register_uninstall_hook(__FILE__, array('BooChat_Connect', 'uninstall'));

// Initialize the plugin
boochat_connect_init();
