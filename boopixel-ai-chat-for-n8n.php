<?php
/**
 * Plugin Name: BooPixel AI Chat Connect for n8n
 * Plugin URI: https://boopixel.com/boopixel-ai-chat-for-n8n
 * Description: AI Chatbot & n8n Automation - Modern, lightweight chatbot popup that integrates seamlessly with n8n. Automate workflows, respond in real-time, collect leads, and connect to any AI model or external service. Perfect for 24/7 AI support, sales automation, and smart customer interactions.
 * Version: 1.0.0
 * Author: BooPixel
 * Author URI: https://boopixel.com
 * Developer: BooPixel
 * Developer URI: https://boopixel.com
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: boopixel-ai-chat-for-n8n
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.9
 * Requires PHP: 7.2
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define constants
define('BOOPIXEL_AI_CHAT_FOR_N8N_VERSION', '1.0.0');
define('BOOPIXEL_AI_CHAT_FOR_N8N_DIR', plugin_dir_path(__FILE__));
define('BOOPIXEL_AI_CHAT_FOR_N8N_URL', plugin_dir_url(__FILE__));

// Load helper functions
require_once BOOPIXEL_AI_CHAT_FOR_N8N_DIR . 'includes/helpers.php';

// Load classes
require_once BOOPIXEL_AI_CHAT_FOR_N8N_DIR . 'includes/class-boopixel-ai-chat-for-n8n-database.php';
require_once BOOPIXEL_AI_CHAT_FOR_N8N_DIR . 'includes/class-boopixel-ai-chat-for-n8n-api.php';
require_once BOOPIXEL_AI_CHAT_FOR_N8N_DIR . 'includes/class-boopixel-ai-chat-for-n8n-settings.php';
require_once BOOPIXEL_AI_CHAT_FOR_N8N_DIR . 'includes/class-boopixel-ai-chat-for-n8n-statistics.php';
require_once BOOPIXEL_AI_CHAT_FOR_N8N_DIR . 'includes/class-boopixel-ai-chat-for-n8n-license.php';
require_once BOOPIXEL_AI_CHAT_FOR_N8N_DIR . 'includes/class-boopixel-ai-chat-for-n8n-ajax.php';
require_once BOOPIXEL_AI_CHAT_FOR_N8N_DIR . 'includes/class-boopixel-ai-chat-for-n8n-admin.php';
require_once BOOPIXEL_AI_CHAT_FOR_N8N_DIR . 'includes/class-boopixel-ai-chat-for-n8n-frontend.php';

/**
 * Main plugin class
 */
class BooPixel_AI_Chat_For_N8n {
    
    /**
     * Single instance of the plugin
     *
     * @var BooPixel_AI_Chat_For_N8n
     */
    private static $instance = null;
    
    /**
     * Database instance
     *
     * @var BooPixel_AI_Chat_For_N8n_Database
     */
    private $database;
    
    /**
     * API instance
     *
     * @var BooPixel_AI_Chat_For_N8n_API
     */
    private $api;
    
    /**
     * Settings instance
     *
     * @var BooPixel_AI_Chat_For_N8n_Settings
     */
    private $settings;
    
    /**
     * Statistics instance
     *
     * @var BooPixel_AI_Chat_For_N8n_Statistics
     */
    private $statistics;
    
    /**
     * AJAX instance
     *
     * @var BooPixel_AI_Chat_For_N8n_Ajax
     */
    private $ajax;
    
    /**
     * Admin instance
     *
     * @var BooPixel_AI_Chat_For_N8n_Admin
     */
    private $admin;
    
    /**
     * Frontend instance
     *
     * @var BooPixel_AI_Chat_For_N8n_Frontend
     */
    private $frontend;
    
    /**
     * Get single instance
     *
     * @return BooPixel_AI_Chat_For_N8n Single instance of the plugin
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
        $database = new BooPixel_AI_Chat_For_N8n_Database();
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
     * Verify license periodically
     */
    public function verify_license_periodically() {
        $license = new BooPixel_AI_Chat_For_N8n_License();
        
        // Verify license every 24 hours
        $last_check = get_option('boopixel_ai_chat_for_n8n_license_last_check', 0);
        if (time() - $last_check > 86400) {
            $license->verify_license();
            update_option('boopixel_ai_chat_for_n8n_license_last_check', time());
        }
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
            'boopixel_ai_chat_for_n8n_cache_version',
            'boopixel_ai_chat_for_n8n_language',
            'boopixel_ai_chat_for_n8n_chat_name',
            'boopixel_ai_chat_for_n8n_welcome_message',
            'boopixel_ai_chat_for_n8n_primary_color',
            'boopixel_ai_chat_for_n8n_secondary_color',
            'boopixel_ai_chat_for_n8n_chat_bg_color',
            'boopixel_ai_chat_for_n8n_text_color',
            'boopixel_ai_chat_for_n8n_font_family',
            'boopixel_ai_chat_for_n8n_font_size',
            'boopixel_ai_chat_for_n8n_api_url',
            'boopixel_ai_chat_for_n8n_license_key',
            'boopixel_ai_chat_for_n8n_license_status',
            'boopixel_ai_chat_for_n8n_license_expires',
            'boopixel_ai_chat_for_n8n_license_last_check',
            'boopixel_ai_chat_for_n8n_stripe_secret_key',
            'boopixel_ai_chat_for_n8n_stripe_test_mode',
            'boopixel_ai_chat_for_n8n_pro_price',
            'boopixel_ai_chat_for_n8n_pro_currency'
        );
        
        foreach ($options as $option) {
            delete_option($option);
        }
        
        // Drop database table
        $database = new BooPixel_AI_Chat_For_N8n_Database();
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
        // Schedule license verification
        add_action('wp_loaded', array($this, 'verify_license_periodically'));
        
        // Initialize core components
        $this->database = new BooPixel_AI_Chat_For_N8n_Database();
        $this->api = new BooPixel_AI_Chat_For_N8n_API();
        $this->settings = new BooPixel_AI_Chat_For_N8n_Settings();
        $this->statistics = new BooPixel_AI_Chat_For_N8n_Statistics($this->database);
        
        // Initialize feature components
        $this->ajax = new BooPixel_AI_Chat_For_N8n_Ajax($this->database, $this->api);
        $this->admin = new BooPixel_AI_Chat_For_N8n_Admin($this->settings, $this->api, $this->statistics);
        $this->frontend = new BooPixel_AI_Chat_For_N8n_Frontend($this->settings);
    }
}

/**
 * Load plugin text domain for translations
 * Note: Not needed for WordPress.org plugins since WordPress 4.6
 * WordPress automatically loads translations for plugins in the directory
 */
// Removed load_plugin_textdomain() - WordPress.org handles translations automatically

/**
 * Initialize the plugin
 */
function boopixel_ai_chat_for_n8n_init() {
    return BooPixel_AI_Chat_For_N8n::get_instance();
}

// Register activation, deactivation and uninstall hooks
register_activation_hook(__FILE__, array('BooPixel_AI_Chat_For_N8n', 'activate'));
register_deactivation_hook(__FILE__, array('BooPixel_AI_Chat_For_N8n', 'deactivate'));
register_uninstall_hook(__FILE__, array('BooPixel_AI_Chat_For_N8n', 'uninstall'));

// Initialize the plugin
boopixel_ai_chat_for_n8n_init();
