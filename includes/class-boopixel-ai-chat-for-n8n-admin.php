<?php
/**
 * Admin functionality for BooPixel AI Chat Connect for n8n
 *
 * @package BooPixel_AI_Chat_For_N8n
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class BooPixel_AI_Chat_For_N8n_Admin
 */
class BooPixel_AI_Chat_For_N8n_Admin {
    
    /**
     * Settings instance
     *
     * @var BooPixel_AI_Chat_For_N8n_Settings
     */
    private $settings;
    
    /**
     * API instance
     *
     * @var BooPixel_AI_Chat_For_N8n_API
     */
    private $api;
    
    /**
     * Statistics instance
     *
     * @var BooPixel_AI_Chat_For_N8n_Statistics
     */
    private $statistics;
    
    /**
     * License instance
     *
     * @var BooPixel_AI_Chat_For_N8n_License
     */
    private $license;
    
    /**
     * Constructor
     *
     * @param BooPixel_AI_Chat_For_N8n_Settings  $settings Settings instance.
     * @param BooPixel_AI_Chat_For_N8n_API       $api API instance.
     * @param BooPixel_AI_Chat_For_N8n_Statistics $statistics Statistics instance.
     */
    public function __construct($settings, $api, $statistics) {
        $this->settings = $settings;
        $this->api = $api;
        $this->statistics = $statistics;
        $this->license = new BooPixel_AI_Chat_For_N8n_License();
        
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_filter('locale', array($this, 'force_plugin_locale'), 999);
        add_action('admin_init', array($this, 'reload_textdomain'), 1);
        add_filter('plugin_action_links_' . plugin_basename(BOOPIXEL_AI_CHAT_FOR_N8N_DIR . 'boopixel-ai-chat-for-n8n.php'), array($this, 'add_plugin_action_links'));
        add_action('admin_post_boopixel_ai_chat_for_n8n_save_settings', array($this, 'save_settings'));
        add_action('admin_post_boopixel_ai_chat_for_n8n_save_customization', array($this, 'save_customization'));
        add_action('admin_post_boopixel_ai_chat_for_n8n_activate_license', array($this, 'handle_activate_license'));
        add_action('admin_post_boopixel_ai_chat_for_n8n_deactivate_license', array($this, 'handle_deactivate_license'));
        add_action('admin_post_boopixel_ai_chat_for_n8n_stripe_checkout', array($this, 'handle_stripe_checkout'));
        add_action('admin_init', array($this, 'handle_stripe_return'));
        add_action('wp_ajax_boopixel_ai_chat_for_n8n_create_stripe_session', array($this, 'ajax_create_stripe_session'));
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __('BooPixel AI Chat Connect for n8n', 'boopixel-ai-chat-for-n8n'),
            __('BooPixel AI Chat', 'boopixel-ai-chat-for-n8n'),
            'manage_options',
            'boopixel-ai-chat-for-n8n',
            array($this, 'render_admin_page'),
            'dashicons-sos',
            30
        );
        
        add_submenu_page(
            'boopixel-ai-chat-for-n8n',
            boopixel_ai_chat_for_n8n_translate('main_panel'),
            boopixel_ai_chat_for_n8n_translate('main_panel'),
            'manage_options',
            'boopixel-ai-chat-for-n8n',
            array($this, 'render_admin_page')
        );
        
        add_submenu_page(
            'boopixel-ai-chat-for-n8n',
            boopixel_ai_chat_for_n8n_translate('customization'),
            boopixel_ai_chat_for_n8n_translate('customization'),
            'manage_options',
            'boopixel-ai-chat-for-n8n-customization',
            array($this, 'render_customization_page')
        );
        
        add_submenu_page(
            'boopixel-ai-chat-for-n8n',
            boopixel_ai_chat_for_n8n_translate('settings'),
            boopixel_ai_chat_for_n8n_translate('settings'),
            'manage_options',
            'boopixel-ai-chat-for-n8n-settings',
            array($this, 'render_settings_page')
        );
        
        // Add Statistics menu (available to all users)
        $statistics_title = boopixel_ai_chat_for_n8n_translate('statistics');
        add_submenu_page(
            'boopixel-ai-chat-for-n8n',
            $statistics_title,
            $statistics_title,
            'manage_options',
            'boopixel-ai-chat-for-n8n-statistics',
            array($this, 'render_statistics_page')
        );
        
        // Add Sessions menu (available to all users)
        $sessions_title = boopixel_ai_chat_for_n8n_translate('sessions', 'Sessions');
        add_submenu_page(
            'boopixel-ai-chat-for-n8n',
            $sessions_title,
            $sessions_title,
            'manage_options',
            'boopixel-ai-chat-for-n8n-sessions',
            array($this, 'render_sessions_page')
        );
        
        // Add PRO upgrade page
        add_submenu_page(
            'boopixel-ai-chat-for-n8n',
            boopixel_ai_chat_for_n8n_translate('upgrade_to_pro', 'Upgrade to PRO'),
            boopixel_ai_chat_for_n8n_translate('upgrade_to_pro', 'Upgrade to PRO'),
            'manage_options',
            'boopixel-ai-chat-for-n8n-pro',
            array($this, 'render_pro_upgrade_page')
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
            $current_page === 'boopixel-ai-chat-for-n8n' ||
            $current_page === 'boopixel-ai-chat-for-n8n-customization' ||
            $current_page === 'boopixel-ai-chat-for-n8n-settings' ||
            $current_page === 'boopixel-ai-chat-for-n8n-statistics' ||
            $current_page === 'boopixel-ai-chat-for-n8n-sessions' ||
            $current_page === 'boopixel-ai-chat-for-n8n-pro' ||
            strpos($hook, 'boopixel-ai-chat-for-n8n') !== false
        );
        
        if (!$is_plugin_page) {
            return;
        }
        
        // Base admin styles (always loaded)
        wp_enqueue_style(
            'boopixel-ai-chat-for-n8n-admin-style',
            BOOPIXEL_AI_CHAT_FOR_N8N_URL . 'assets/css/admin-style.css',
            array(),
            BOOPIXEL_AI_CHAT_FOR_N8N_VERSION
        );
        
        // Admin main styles (header styles - loaded on all pages)
        wp_enqueue_style(
            'boopixel-ai-chat-for-n8n-admin-main',
            BOOPIXEL_AI_CHAT_FOR_N8N_URL . 'assets/css/admin-main.css',
            array('boopixel-ai-chat-for-n8n-admin-style'),
            BOOPIXEL_AI_CHAT_FOR_N8N_VERSION
        );
        
        if ($current_page === 'boopixel-ai-chat-for-n8n-statistics') {
            wp_enqueue_style(
                'boopixel-ai-chat-for-n8n-admin-statistics',
                BOOPIXEL_AI_CHAT_FOR_N8N_URL . 'assets/css/admin-statistics.css',
                array('boopixel-ai-chat-for-n8n-admin-style'),
                BOOPIXEL_AI_CHAT_FOR_N8N_VERSION
            );
        }
        
        // Sessions page styles
        if ($current_page === 'boopixel-ai-chat-for-n8n-sessions') {
            wp_enqueue_style(
                'boopixel-ai-chat-for-n8n-admin-sessions',
                BOOPIXEL_AI_CHAT_FOR_N8N_URL . 'assets/css/admin-sessions.css',
                array('boopixel-ai-chat-for-n8n-admin-style'),
                BOOPIXEL_AI_CHAT_FOR_N8N_VERSION
            );
        }
        
        // Settings page styles
        if ($current_page === 'boopixel-ai-chat-for-n8n-settings') {
            wp_enqueue_style(
                'boopixel-ai-chat-for-n8n-admin-settings',
                BOOPIXEL_AI_CHAT_FOR_N8N_URL . 'assets/css/admin-settings.css',
                array('boopixel-ai-chat-for-n8n-admin-style'),
                BOOPIXEL_AI_CHAT_FOR_N8N_VERSION
            );
        }
        
        // Customization page styles
        if ($current_page === 'boopixel-ai-chat-for-n8n-customization') {
            wp_enqueue_style(
                'boopixel-ai-chat-for-n8n-admin-customization',
                BOOPIXEL_AI_CHAT_FOR_N8N_URL . 'assets/css/admin-customization.css',
                array('boopixel-ai-chat-for-n8n-admin-style'),
                BOOPIXEL_AI_CHAT_FOR_N8N_VERSION
            );
            
            // Enqueue WordPress media uploader
            wp_enqueue_media();
            
            // Enqueue customization script
            wp_enqueue_script(
                'boopixel-ai-chat-for-n8n-customization-script',
                BOOPIXEL_AI_CHAT_FOR_N8N_URL . 'assets/js/admin-customization.js',
                array('jquery'),
                BOOPIXEL_AI_CHAT_FOR_N8N_VERSION,
                true
            );
            
            // Localize customization script with translations
            wp_localize_script('boopixel-ai-chat-for-n8n-customization-script', 'boopixelAiChatForN8nCustomization', array(
                'chooseIcon' => esc_html__('Choose Chat Icon', 'boopixel-ai-chat-for-n8n'),
                'useIcon' => esc_html__('Use this icon', 'boopixel-ai-chat-for-n8n'),
                'removeIcon' => esc_html__('Remove Icon', 'boopixel-ai-chat-for-n8n'),
            ));
        }
        
        // PRO page styles
        if ($current_page === 'boopixel-ai-chat-for-n8n-pro') {
            wp_enqueue_style(
                'boopixel-ai-chat-for-n8n-admin-main',
                BOOPIXEL_AI_CHAT_FOR_N8N_URL . 'assets/css/admin-main.css',
                array('boopixel-ai-chat-for-n8n-admin-style'),
                BOOPIXEL_AI_CHAT_FOR_N8N_VERSION
            );
            
            // Enqueue Stripe checkout script
            wp_enqueue_script(
                'boopixel-ai-chat-for-n8n-stripe-checkout',
                BOOPIXEL_AI_CHAT_FOR_N8N_URL . 'assets/js/stripe-checkout.js',
                array('jquery'),
                BOOPIXEL_AI_CHAT_FOR_N8N_VERSION,
                true
            );
            
            wp_localize_script('boopixel-ai-chat-for-n8n-stripe-checkout', 'boopixelAiChatForN8nStripe', array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('boopixel-ai-chat-for-n8n-stripe'),
                'proPageUrl' => admin_url('admin.php?page=boopixel-ai-chat-for-n8n-pro'),
                'configurationError' => esc_html__('Configuration Error', 'boopixel-ai-chat-for-n8n'),
                'loading' => esc_html__('Loading...', 'boopixel-ai-chat-for-n8n'),
                'failedCheckout' => esc_html__('Failed to create checkout session. Please try again.', 'boopixel-ai-chat-for-n8n'),
                'errorConnecting' => esc_html__('Error connecting to server. Please try again.', 'boopixel-ai-chat-for-n8n'),
            ));
        }
        
        wp_enqueue_script(
            'boopixel-ai-chat-for-n8n-admin-script',
            BOOPIXEL_AI_CHAT_FOR_N8N_URL . 'assets/js/admin-script.js',
            array('jquery'),
            BOOPIXEL_AI_CHAT_FOR_N8N_VERSION,
            true
        );
        
        $is_statistics_page = ($current_page === 'boopixel-ai-chat-for-n8n-statistics');
        
        if ($is_statistics_page) {
            wp_enqueue_script(
                'chart-js',
                BOOPIXEL_AI_CHAT_FOR_N8N_URL . 'assets/js/chart.umd.min.js',
                array(),
                '4.4.0',
                false
            );
            
            $version = boopixel_ai_chat_for_n8n_get_version();
            wp_enqueue_script(
                'boopixel-ai-chat-for-n8n-statistics-script',
                BOOPIXEL_AI_CHAT_FOR_N8N_URL . 'assets/js/statistics-script.js',
                array('jquery', 'chart-js'),
                $version,
                false
            );
            
            $localize_data = array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('boopixel-ai-chat-for-n8n-statistics'),
                'loadStatisticsText' => boopixel_ai_chat_for_n8n_translate('load_statistics'),
                'loadingText' => boopixel_ai_chat_for_n8n_translate('loading', 'Loading...'),
                'selectDatesText' => boopixel_ai_chat_for_n8n_translate('select_dates', 'Please select start and end dates.'),
                'invalidDateRangeText' => boopixel_ai_chat_for_n8n_translate('invalid_date_range', 'Start date must be before end date.'),
                'errorLoadingText' => boopixel_ai_chat_for_n8n_translate('error_loading_statistics', 'Error loading statistics: '),
                'errorConnectingText' => boopixel_ai_chat_for_n8n_translate('error_connecting_server', 'Error connecting to server. Please try again.'),
                'proUpgradeUrl' => admin_url('admin.php?page=boopixel-ai-chat-for-n8n-pro'),
            );
            
            wp_localize_script('boopixel-ai-chat-for-n8n-statistics-script', 'boopixelAiChatForN8nStats', $localize_data);
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
            wp_die(esc_html(boopixel_ai_chat_for_n8n_translate('no_permission_page', 'You do not have permission to access this page.')));
            return false;
        }
        
        if (!isset($_POST[$nonce_field]) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST[$nonce_field])), $nonce_action)) {
            wp_die(esc_html(boopixel_ai_chat_for_n8n_translate('security_error_try_again', 'Security error. Please try again.')));
            return false;
        }
        
        return true;
    }
    
    /**
     * Save customization
     */
    public function save_customization() {
        if (!$this->verify_request('boopixel_ai_chat_for_n8n_save_customization', 'boopixel_ai_chat_for_n8n_customization_nonce')) {
            return;
        }
        
        // Save customization settings
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in verify_request() above
        $chat_icon = isset($_POST['chat_icon']) ? esc_url_raw(wp_unslash($_POST['chat_icon'])) : '';
        update_option('boopixel_ai_chat_for_n8n_chat_icon', $chat_icon);
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in verify_request() above
        update_option('boopixel_ai_chat_for_n8n_chat_name', sanitize_text_field(wp_unslash($_POST['chat_name'] ?? boopixel_ai_chat_for_n8n_translate('chat_name_default'))));
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in verify_request() above
        update_option('boopixel_ai_chat_for_n8n_welcome_message', sanitize_textarea_field(wp_unslash($_POST['welcome_message'] ?? boopixel_ai_chat_for_n8n_translate('welcome_message_default'))));
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in verify_request() above
        update_option('boopixel_ai_chat_for_n8n_primary_color', sanitize_hex_color(wp_unslash($_POST['primary_color'] ?? '#1B8EF0')));
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in verify_request() above
        update_option('boopixel_ai_chat_for_n8n_secondary_color', sanitize_hex_color(wp_unslash($_POST['secondary_color'] ?? '#1B5D98')));
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in verify_request() above
        update_option('boopixel_ai_chat_for_n8n_chat_bg_color', sanitize_hex_color(wp_unslash($_POST['chat_bg_color'] ?? '#ffffff')));
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in verify_request() above
        update_option('boopixel_ai_chat_for_n8n_text_color', sanitize_hex_color(wp_unslash($_POST['text_color'] ?? '#333333')));
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in verify_request() above
        update_option('boopixel_ai_chat_for_n8n_font_family', sanitize_text_field(wp_unslash($_POST['font_family'] ?? 'Arial, sans-serif')));
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in verify_request() above
        update_option('boopixel_ai_chat_for_n8n_font_size', sanitize_text_field(wp_unslash($_POST['font_size'] ?? '14px')));
        
        $this->settings->clear_cache();
        
        wp_safe_redirect(add_query_arg(array(
            'page' => 'boopixel-ai-chat-for-n8n-customization',
            'customization-updated' => 'true'
        ), admin_url('admin.php')));
        exit;
    }
    
    /**
     * Save settings
     */
    public function save_settings() {
        if (!$this->verify_request('boopixel_ai_chat_for_n8n_save_settings', 'boopixel_ai_chat_for_n8n_settings_nonce')) {
            return;
        }
        
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in verify_request() above
        $api_url = isset($_POST['api_url']) ? esc_url_raw(wp_unslash($_POST['api_url'])) : '';
        update_option('boopixel_ai_chat_for_n8n_api_url', $api_url);
        
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in verify_request() above
        $language = isset($_POST['language']) ? sanitize_text_field(wp_unslash($_POST['language'])) : '';
        if (empty($language)) {
            delete_option('boopixel_ai_chat_for_n8n_language');
        } elseif (in_array($language, array('en', 'pt', 'es'))) {
            update_option('boopixel_ai_chat_for_n8n_language', $language);
        }
        
        wp_safe_redirect(add_query_arg(array(
            'page' => 'boopixel-ai-chat-for-n8n-settings',
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
        $view_file = BOOPIXEL_AI_CHAT_FOR_N8N_DIR . 'includes/views/' . $view_name . '.php';
        if (file_exists($view_file)) {
            extract($vars);
            include $view_file;
        }
    }
    
    /**
     * Check if current page is a plugin admin page
     * 
     * @param bool $check_screen Whether to check current screen (only in admin_init+).
     * @return bool True if on plugin admin page
     */
    private function is_plugin_admin_page($check_screen = true) {
        if (!is_admin()) {
            return false;
        }
        
        // Check GET parameter (available early)
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Just checking page parameter
        $current_page = isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : '';
        
        if (!empty($current_page) && strpos($current_page, 'boopixel-ai-chat-for-n8n') !== false) {
            return true;
        }
        
        // Check if we're in an AJAX request for the plugin
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Just checking action parameter
        $ajax_action = isset($_REQUEST['action']) ? sanitize_text_field(wp_unslash($_REQUEST['action'])) : '';
        if (!empty($ajax_action) && strpos($ajax_action, 'boochat_connect') !== false) {
            return true;
        }
        
        // Check current screen (only if function exists and check_screen is true)
        if ($check_screen && function_exists('get_current_screen')) {
            $screen = get_current_screen();
            if ($screen && strpos($screen->id, 'boopixel-ai-chat-for-n8n') !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Get target locale based on plugin settings
     * 
     * @param string $default_locale Default WordPress locale.
     * @return string Target locale
     */
    private function get_target_locale($default_locale = '') {
        if (empty($default_locale)) {
            $default_locale = get_locale();
        }
        
        $target_locale = null;
        
        // Get configured language from plugin settings
        $configured_language = get_option('boopixel_ai_chat_for_n8n_language', '');
        
        if (!empty($configured_language)) {
            // Use plugin configured language
            $locale_map = array(
                'en' => 'en_US',
                'pt' => 'pt_BR',
                'es' => 'es_ES',
            );
            
            if (isset($locale_map[$configured_language])) {
                $target_locale = $locale_map[$configured_language];
            }
        } else {
            // Use WordPress locale, but map to supported locales
            $language_code = boopixel_ai_chat_for_n8n_get_language_from_locale($default_locale);
            
            $locale_map = array(
                'en' => 'en_US',
                'pt' => 'pt_BR',
                'es' => 'es_ES',
            );
            
            if (isset($locale_map[$language_code])) {
                $target_locale = $locale_map[$language_code];
            } else {
                // Default to English if locale not supported
                $target_locale = 'en_US';
            }
        }
        
        return $target_locale ? $target_locale : $default_locale;
    }
    
    /**
     * Force plugin locale based on plugin settings or WordPress locale
     * 
     * @param string $locale Current WordPress locale.
     * @return string Modified locale for plugin admin pages.
     */
    public function force_plugin_locale($locale) {
        // Only on plugin admin pages (don't check screen in locale filter - too early)
        if (!$this->is_plugin_admin_page(false)) {
            return $locale;
        }
        
        return $this->get_target_locale($locale);
    }
    
    /**
     * Reload text domain after locale switch
     */
    public function reload_textdomain() {
        if (!$this->is_plugin_admin_page()) {
            return;
        }
        
        $target_locale = $this->get_target_locale();
        $current_locale = get_locale();
        
        // If locale changed, reload text domain
        if ($target_locale !== $current_locale && function_exists('switch_to_locale')) {
            switch_to_locale($target_locale);
            // Reload text domain with new locale
            unload_textdomain('boopixel-ai-chat-for-n8n');
            // Note: load_plugin_textdomain() not needed for WordPress.org plugins since 4.6
            // WordPress automatically loads translations, but we reload for locale switching
            // Only reload if supporting WordPress < 4.6
            if (version_compare(get_bloginfo('version'), '4.6', '<')) {
                load_plugin_textdomain(
                    'boopixel-ai-chat-for-n8n',
                    false,
                    dirname(plugin_basename(BOOPIXEL_AI_CHAT_FOR_N8N_DIR . 'boopixel-ai-chat-for-n8n.php')) . '/languages'
                );
            }
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
     * Render sessions page
     */
    public function render_sessions_page() {
        $ajax_url = admin_url('admin-ajax.php');
        $nonce = wp_create_nonce('boopixel-ai-chat-for-n8n-sessions');
        
        // Enqueue sessions script
        wp_enqueue_script(
            'boopixel-ai-chat-for-n8n-sessions',
            BOOPIXEL_AI_CHAT_FOR_N8N_URL . 'assets/js/sessions-script.js',
            array('jquery'),
            BOOPIXEL_AI_CHAT_FOR_N8N_VERSION,
            true
        );
        
        wp_localize_script('boopixel-ai-chat-for-n8n-sessions', 'boopixelAiChatForN8nSessions', array(
            'exportJsonText' => esc_html__('Export JSON', 'boopixel-ai-chat-for-n8n'),
            'exportCsvText' => esc_html__('Export CSV', 'boopixel-ai-chat-for-n8n'),
            'ajax_url' => $ajax_url,
            'nonce' => $nonce,
            'loadingText' => boopixel_ai_chat_for_n8n_translate('loading_sessions', 'Loading sessions...'),
            'errorLoadingText' => boopixel_ai_chat_for_n8n_translate('error_loading_sessions', 'Error loading sessions: '),
            'noSessionsText' => boopixel_ai_chat_for_n8n_translate('no_sessions_found', 'No sessions found.')
        ));
        
        include BOOPIXEL_AI_CHAT_FOR_N8N_DIR . 'includes/views/admin-sessions.php';
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
     * Redirect to PRO upgrade page
     */
    public function redirect_to_pro_page() {
        $redirect_url = admin_url('admin.php?page=boopixel-ai-chat-for-n8n-pro');
        
        // Enqueue redirect script using wp_add_inline_script
        wp_enqueue_script('jquery');
        $redirect_script = "window.location.href = '" . esc_js($redirect_url) . "';";
        wp_add_inline_script('jquery', $redirect_script);
        
        // Use PHP redirect as primary method
        wp_safe_redirect($redirect_url);
        exit;
    }
    
    /**
     * Render statistics page
     */
    public function render_statistics_page() {
        // Statistics page is available to all users
        $this->statistics->render_page();
    }
    
    /**
     * Render PRO upgrade page
     */
    public function render_pro_upgrade_page() {
        $is_pro = $this->license->is_pro();
        $license_key = $this->license->get_license_key();
        $license_status = $this->license->get_license_status();
        
        // Mask license key for display
        if (!empty($license_key) && strlen($license_key) > 8) {
            $masked_key = substr($license_key, 0, 4) . str_repeat('*', strlen($license_key) - 8) . substr($license_key, -4);
        } else {
            $masked_key = $license_key;
        }
        
        $this->load_view('admin-pro-upgrade', array(
            'is_pro' => $is_pro,
            'license_status' => $license_status,
            'license_key' => $masked_key,
        ));
    }
    
    /**
     * Handle license activation
     */
    public function handle_activate_license() {
        if (!$this->verify_request('boopixel_ai_chat_for_n8n_activate_license', 'license_nonce')) {
            return;
        }
        
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in verify_request() above
        $license_key = isset($_POST['license_key']) ? sanitize_text_field(wp_unslash($_POST['license_key'])) : '';
        
        $result = $this->license->activate_license($license_key);
        
        if ($result['success']) {
            wp_safe_redirect(add_query_arg(array(
                'page' => 'boopixel-ai-chat-for-n8n-pro',
                'activation' => 'success'
            ), admin_url('admin.php')));
        } else {
            wp_safe_redirect(add_query_arg(array(
                'page' => 'boopixel-ai-chat-for-n8n-pro',
                'activation' => 'error',
                'message' => urlencode($result['message'])
            ), admin_url('admin.php')));
        }
        exit;
    }
    
    /**
     * Handle license deactivation
     */
    public function handle_deactivate_license() {
        if (!$this->verify_request('boopixel_ai_chat_for_n8n_deactivate_license', 'deactivate_nonce')) {
            return;
        }
        
        $result = $this->license->deactivate_license();
        
        wp_safe_redirect(add_query_arg(array(
            'page' => 'boopixel-ai-chat-for-n8n-pro',
            'deactivation' => $result['success'] ? 'success' : 'error'
        ), admin_url('admin.php')));
        exit;
    }
    
    /**
     * AJAX handler to create Stripe checkout session
     */
    public function ajax_create_stripe_session() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'boopixel-ai-chat-for-n8n-stripe')) {
            wp_send_json_error(array('message' => boopixel_ai_chat_for_n8n_translate('security_check_failed', 'Security check failed.')));
            return;
        }
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => boopixel_ai_chat_for_n8n_translate('no_permission', 'No permission.')));
            return;
        }
        
        $result = $this->license->request_checkout_url();
        
        if ($result['success'] && isset($result['checkout_url'])) {
            wp_send_json_success(array(
                'checkout_url' => $result['checkout_url'],
                'session_id' => isset($result['session_id']) ? $result['session_id'] : ''
            ));
        } else {
            wp_send_json_error(array(
                'message' => isset($result['message']) ? $result['message'] : boopixel_ai_chat_for_n8n_translate('failed_checkout_session', 'Failed to create checkout session.')
            ));
        }
    }
    
    /**
     * Handle checkout request (legacy POST form)
     */
    public function handle_stripe_checkout() {
        if (!$this->verify_request('boopixel_ai_chat_for_n8n_stripe_checkout', 'stripe_nonce')) {
            return;
        }
        
        $result = $this->license->request_checkout_url();
        
        if ($result['success'] && isset($result['checkout_url'])) {
            // Redirect to checkout
            wp_safe_redirect($result['checkout_url']);
            exit;
        } else {
            wp_safe_redirect(add_query_arg(array(
                'page' => 'boopixel-ai-chat-for-n8n-pro',
                'payment' => 'error',
                'message' => isset($result['message']) ? urlencode($result['message']) : ''
            ), admin_url('admin.php')));
            exit;
        }
    }
    
    /**
     * Handle payment return callback
     */
    public function handle_stripe_return() {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Payment callback verification
        if (!isset($_GET['page']) || sanitize_text_field(wp_unslash($_GET['page'])) !== 'boopixel-ai-chat-for-n8n-pro') {
            return;
        }
        
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Payment callback verification
        if (!isset($_GET['payment']) || sanitize_text_field(wp_unslash($_GET['payment'])) !== 'success') {
            return;
        }
        
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Payment callback verification
        $session_id = isset($_GET['session_id']) ? sanitize_text_field(wp_unslash($_GET['session_id'])) : '';
        
        if (empty($session_id)) {
            return;
        }
        
        // Verify the payment and activate license
        $result = $this->license->verify_payment_and_activate($session_id);
        
        if ($result['success']) {
            wp_safe_redirect(add_query_arg(array(
                'page' => 'boopixel-ai-chat-for-n8n-pro',
                'payment' => 'success',
                'license_activated' => '1'
            ), admin_url('admin.php')));
        } else {
            // Check if license key was received but needs API key
            $needs_api_key = isset($result['needs_api_key']) && $result['needs_api_key'] === true;
            
            if ($needs_api_key) {
                wp_safe_redirect(add_query_arg(array(
                    'page' => 'boopixel-ai-chat-for-n8n-pro',
                    'payment' => 'success',
                    'license_received' => '1',
                    'needs_api_key' => '1'
                ), admin_url('admin.php')));
            } else {
                wp_safe_redirect(add_query_arg(array(
                    'page' => 'boopixel-ai-chat-for-n8n-pro',
                    'payment' => 'error',
                    'message' => isset($result['message']) ? urlencode($result['message']) : ''
                ), admin_url('admin.php')));
            }
        }
        exit;
    }
    
    /**
     * Add Settings action link to plugins page
     *
     * @param array $links Existing action links.
     * @return array Modified action links.
     */
    public function add_plugin_action_links($links) {
        $settings_link = '<a href="' . esc_url(admin_url('admin.php?page=boopixel-ai-chat-for-n8n-settings')) . '">' . esc_html__('Settings', 'boopixel-ai-chat-for-n8n') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }
}

