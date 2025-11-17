<?php
/**
 * Admin functionality for BooChat Connect
 *
 * @package BooChat_Connect
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class BooChat_Connect_Admin
 */
class BooChat_Connect_Admin {
    
    /**
     * Settings instance
     *
     * @var BooChat_Connect_Settings
     */
    private $settings;
    
    /**
     * API instance
     *
     * @var BooChat_Connect_API
     */
    private $api;
    
    /**
     * Statistics instance
     *
     * @var BooChat_Connect_Statistics
     */
    private $statistics;
    
    /**
     * Constructor
     *
     * @param BooChat_Connect_Settings  $settings Settings instance.
     * @param BooChat_Connect_API       $api API instance.
     * @param BooChat_Connect_Statistics $statistics Statistics instance.
     */
    public function __construct($settings, $api, $statistics) {
        $this->settings = $settings;
        $this->api = $api;
        $this->statistics = $statistics;
        
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('admin_post_boochat_connect_save_settings', array($this, 'save_settings'));
        add_action('admin_post_boochat_connect_save_customization', array($this, 'save_customization'));
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __('BooChat Connect', 'boochat-connect'),
            __('BooChat Connect', 'boochat-connect'),
            'manage_options',
            'boochat-connect',
            array($this, 'render_admin_page'),
            'dashicons-sos',
            30
        );
        
        add_submenu_page(
            'boochat-connect',
            boochat_connect_translate('main_panel'),
            boochat_connect_translate('main_panel'),
            'manage_options',
            'boochat-connect',
            array($this, 'render_admin_page')
        );
        
        add_submenu_page(
            'boochat-connect',
            boochat_connect_translate('customization'),
            boochat_connect_translate('customization'),
            'manage_options',
            'boochat-connect-customization',
            array($this, 'render_customization_page')
        );
        
        add_submenu_page(
            'boochat-connect',
            boochat_connect_translate('settings'),
            boochat_connect_translate('settings'),
            'manage_options',
            'boochat-connect-settings',
            array($this, 'render_settings_page')
        );
        
        add_submenu_page(
            'boochat-connect',
            boochat_connect_translate('statistics'),
            boochat_connect_translate('statistics'),
            'manage_options',
            'boochat-connect-statistics',
            array($this, 'render_statistics_page')
        );
    }
    
    /**
     * Enqueue admin assets
     *
     * @param string $hook Current admin page hook.
     */
    public function enqueue_admin_assets($hook) {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading GET parameter for display only
        $current_page = isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : '';
        $is_plugin_page = (
            $current_page === 'boochat-connect' ||
            $current_page === 'boochat-connect-customization' ||
            $current_page === 'boochat-connect-settings' ||
            $current_page === 'boochat-connect-statistics' ||
            strpos($hook, 'boochat-connect') !== false
        );
        
        if (!$is_plugin_page) {
            return;
        }
        
        // Base admin styles (always loaded)
        wp_enqueue_style(
            'boochat-connect-admin-style',
            BOOCHAT_CONNECT_URL . 'assets/css/admin-style.css',
            array(),
            BOOCHAT_CONNECT_VERSION
        );
        
        // Page-specific styles
        if ($current_page === 'boochat-connect' || $current_page === '') {
            wp_enqueue_style(
                'boochat-connect-admin-main',
                BOOCHAT_CONNECT_URL . 'assets/css/admin-main.css',
                array('boochat-connect-admin-style'),
                BOOCHAT_CONNECT_VERSION
            );
        }
        
        if ($current_page === 'boochat-connect-statistics') {
            wp_enqueue_style(
                'boochat-connect-admin-statistics',
                BOOCHAT_CONNECT_URL . 'assets/css/admin-statistics.css',
                array('boochat-connect-admin-style'),
                BOOCHAT_CONNECT_VERSION
            );
        }
        
        wp_enqueue_script(
            'boochat-connect-admin-script',
            BOOCHAT_CONNECT_URL . 'assets/js/admin-script.js',
            array('jquery'),
            BOOCHAT_CONNECT_VERSION,
            true
        );
        
        $is_statistics_page = ($current_page === 'boochat-connect-statistics');
        
        if ($is_statistics_page) {
            wp_enqueue_script(
                'chart-js',
                BOOCHAT_CONNECT_URL . 'assets/js/chart.umd.min.js',
                array(),
                '4.4.0',
                false
            );
            
            $version = boochat_connect_get_version();
            wp_enqueue_script(
                'boochat-connect-statistics-script',
                BOOCHAT_CONNECT_URL . 'assets/js/statistics-script.js',
                array('jquery', 'chart-js'),
                $version,
                false
            );
            
            $localize_data = array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('boochat-connect-statistics'),
                'loadStatisticsText' => boochat_connect_translate('load_statistics'),
                'loadingText' => boochat_connect_translate('loading', 'Loading...'),
                'selectDatesText' => boochat_connect_translate('select_dates', 'Please select start and end dates.'),
                'invalidDateRangeText' => boochat_connect_translate('invalid_date_range', 'Start date must be before end date.'),
                'errorLoadingText' => boochat_connect_translate('error_loading_statistics', 'Error loading statistics: '),
                'errorConnectingText' => boochat_connect_translate('error_connecting_server', 'Error connecting to server. Please try again.'),
            );
            
            wp_localize_script('boochat-connect-statistics-script', 'boochatConnectStats', $localize_data);
        }
    }
    
    /**
     * Verify user permission and nonce
     *
     * @param string $nonce_action Nonce action name.
     * @param string $nonce_field  Nonce field name.
     * @return bool True if valid, false otherwise.
     */
    private function verify_request($nonce_action, $nonce_field) {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('You do not have permission to access this page.', 'boochat-connect'));
            return false;
        }
        
        if (!isset($_POST[$nonce_field]) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST[$nonce_field])), $nonce_action)) {
            wp_die(esc_html__('Security error. Please try again.', 'boochat-connect'));
            return false;
        }
        
        return true;
    }
    
    /**
     * Save customization
     */
    public function save_customization() {
        if (!$this->verify_request('boochat_connect_save_customization', 'boochat_connect_customization_nonce')) {
            return;
        }
        
        // Save customization settings
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in verify_request() above
        update_option('boochat_connect_chat_name', sanitize_text_field(wp_unslash($_POST['chat_name'] ?? boochat_connect_translate('chat_name_default'))));
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in verify_request() above
        update_option('boochat_connect_welcome_message', sanitize_textarea_field(wp_unslash($_POST['welcome_message'] ?? boochat_connect_translate('welcome_message_default'))));
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in verify_request() above
        update_option('boochat_connect_primary_color', sanitize_hex_color(wp_unslash($_POST['primary_color'] ?? '#667eea')));
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in verify_request() above
        update_option('boochat_connect_secondary_color', sanitize_hex_color(wp_unslash($_POST['secondary_color'] ?? '#764ba2')));
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in verify_request() above
        update_option('boochat_connect_chat_bg_color', sanitize_hex_color(wp_unslash($_POST['chat_bg_color'] ?? '#ffffff')));
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in verify_request() above
        update_option('boochat_connect_text_color', sanitize_hex_color(wp_unslash($_POST['text_color'] ?? '#333333')));
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in verify_request() above
        update_option('boochat_connect_font_family', sanitize_text_field(wp_unslash($_POST['font_family'] ?? 'Arial, sans-serif')));
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in verify_request() above
        update_option('boochat_connect_font_size', sanitize_text_field(wp_unslash($_POST['font_size'] ?? '14px')));
        
        $this->settings->clear_cache();
        
        wp_safe_redirect(add_query_arg(array(
            'page' => 'boochat-connect-customization',
            'customization-updated' => 'true'
        ), admin_url('admin.php')));
        exit;
    }
    
    /**
     * Save settings
     */
    public function save_settings() {
        if (!$this->verify_request('boochat_connect_save_settings', 'boochat_connect_settings_nonce')) {
            return;
        }
        
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in verify_request() above
        $api_url = isset($_POST['api_url']) ? esc_url_raw(wp_unslash($_POST['api_url'])) : '';
        update_option('boochat_connect_api_url', $api_url);
        
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in verify_request() above
        $language = isset($_POST['language']) ? sanitize_text_field(wp_unslash($_POST['language'])) : '';
        if (empty($language)) {
            delete_option('boochat_connect_language');
        } elseif (in_array($language, array('en', 'pt', 'es'))) {
            update_option('boochat_connect_language', $language);
        }
        
        wp_safe_redirect(add_query_arg(array(
            'page' => 'boochat-connect-settings',
            'settings-updated' => 'true'
        ), admin_url('admin.php')));
        exit;
    }
    
    /**
     * Load view template
     *
     * @param string $view_name View file name (without .php).
     * @param array  $vars      Variables to pass to the view.
     */
    private function load_view($view_name, $vars = array()) {
        $view_file = BOOCHAT_CONNECT_DIR . 'includes/views/' . $view_name . '.php';
        if (file_exists($view_file)) {
            extract($vars);
            include $view_file;
        }
    }
    
    /**
     * Render admin page (main panel)
     */
    public function render_admin_page() {
        $api_url = $this->api->get_api_url();
        $api_configured = !empty($api_url);
        
        $this->load_view('admin-main', array(
            'api_configured' => $api_configured,
            'api_url' => $api_url,
        ));
    }
    
    /**
     * Render customization page
     */
    public function render_customization_page() {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading GET parameter for display only
        $customization_updated = isset($_GET['customization-updated']) && sanitize_text_field(wp_unslash($_GET['customization-updated'])) === 'true';
        
        $settings = $this->settings->get_customization_settings();
        
        $this->load_view('admin-customization', array(
            'customization_updated' => $customization_updated,
            'settings' => $settings,
        ));
    }
    
    /**
     * Render settings page
     */
    public function render_settings_page() {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading GET parameter for display only
        $settings_updated = isset($_GET['settings-updated']) && sanitize_text_field(wp_unslash($_GET['settings-updated'])) === 'true';
        
        $api_url = $this->api->get_api_url();
        $current_language = $this->settings->get_language();
        
        $this->load_view('admin-settings', array(
            'settings_updated' => $settings_updated,
            'api_url' => $api_url,
            'current_language' => $current_language,
        ));
    }
    
    /**
     * Render statistics page
     */
    public function render_statistics_page() {
        $this->statistics->render_page();
    }
}

