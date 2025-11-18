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
     * License instance
     *
     * @var BooChat_Connect_License
     */
    private $license;
    
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
        $this->license = new BooChat_Connect_License();
        
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_filter('locale', array($this, 'force_plugin_locale'), 999);
        add_action('admin_init', array($this, 'reload_textdomain'), 1);
        add_filter('plugin_action_links_' . plugin_basename(BOOCHAT_CONNECT_DIR . 'boochat-connect.php'), array($this, 'add_plugin_action_links'));
        add_action('admin_post_boochat_connect_save_settings', array($this, 'save_settings'));
        add_action('admin_post_boochat_connect_save_customization', array($this, 'save_customization'));
        add_action('admin_post_boochat_connect_activate_license', array($this, 'handle_activate_license'));
        add_action('admin_post_boochat_connect_deactivate_license', array($this, 'handle_deactivate_license'));
        add_action('admin_post_boochat_connect_stripe_checkout', array($this, 'handle_stripe_checkout'));
        add_action('admin_init', array($this, 'handle_stripe_return'));
        add_action('wp_ajax_boochat_connect_create_stripe_session', array($this, 'ajax_create_stripe_session'));
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
        
        // Add Statistics menu with PRO badge if not licensed
        $statistics_title = boochat_connect_translate('statistics');
        $statistics_menu_title = $statistics_title;
        $statistics_callback = array($this, 'render_statistics_page');
        
        if (!$this->license->is_pro()) {
            $statistics_menu_title = $statistics_title . ' <span class="boochat-connect-pro-badge">PRO</span>';
            // Change callback to redirect to PRO page
            $statistics_callback = array($this, 'redirect_to_pro_page');
        }
        
        add_submenu_page(
            'boochat-connect',
            $statistics_title,
            $statistics_menu_title,
            'manage_options',
            'boochat-connect-statistics',
            $statistics_callback
        );
        
        // Add Sessions menu with PRO badge if not licensed
        $sessions_title = boochat_connect_translate('sessions', 'Sessions');
        $sessions_menu_title = $sessions_title;
        $sessions_callback = array($this, 'render_sessions_page');
        
        if (!$this->license->is_pro()) {
            $sessions_menu_title = $sessions_title . ' <span class="boochat-connect-pro-badge">PRO</span>';
            // Change callback to redirect to PRO page
            $sessions_callback = array($this, 'redirect_to_pro_page');
        }
        
        add_submenu_page(
            'boochat-connect',
            $sessions_title,
            $sessions_menu_title,
            'manage_options',
            'boochat-connect-sessions',
            $sessions_callback
        );
        
        // Add PRO upgrade page
        add_submenu_page(
            'boochat-connect',
            boochat_connect_translate('upgrade_to_pro', 'Upgrade to PRO'),
            boochat_connect_translate('upgrade_to_pro', 'Upgrade to PRO'),
            'manage_options',
            'boochat-connect-pro',
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
            $current_page === 'boochat-connect' ||
            $current_page === 'boochat-connect-customization' ||
            $current_page === 'boochat-connect-settings' ||
            $current_page === 'boochat-connect-statistics' ||
            $current_page === 'boochat-connect-sessions' ||
            $current_page === 'boochat-connect-pro' ||
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
        
        // Admin main styles (header styles - loaded on all pages)
        wp_enqueue_style(
            'boochat-connect-admin-main',
            BOOCHAT_CONNECT_URL . 'assets/css/admin-main.css',
            array('boochat-connect-admin-style'),
            BOOCHAT_CONNECT_VERSION
        );
        
        if ($current_page === 'boochat-connect-statistics') {
            wp_enqueue_style(
                'boochat-connect-admin-statistics',
                BOOCHAT_CONNECT_URL . 'assets/css/admin-statistics.css',
                array('boochat-connect-admin-style'),
                BOOCHAT_CONNECT_VERSION
            );
        }
        
        // Sessions page styles
        if ($current_page === 'boochat-connect-sessions') {
            wp_enqueue_style(
                'boochat-connect-admin-sessions',
                BOOCHAT_CONNECT_URL . 'assets/css/admin-sessions.css',
                array('boochat-connect-admin-style'),
                BOOCHAT_CONNECT_VERSION
            );
        }
        
        // Settings page styles
        if ($current_page === 'boochat-connect-settings') {
            wp_enqueue_style(
                'boochat-connect-admin-settings',
                BOOCHAT_CONNECT_URL . 'assets/css/admin-settings.css',
                array('boochat-connect-admin-style'),
                BOOCHAT_CONNECT_VERSION
            );
        }
        
        // Customization page styles
        if ($current_page === 'boochat-connect-customization') {
            wp_enqueue_style(
                'boochat-connect-admin-customization',
                BOOCHAT_CONNECT_URL . 'assets/css/admin-customization.css',
                array('boochat-connect-admin-style'),
                BOOCHAT_CONNECT_VERSION
            );
            
            // Enqueue WordPress media uploader
            wp_enqueue_media();
            
            // Enqueue customization script
            wp_enqueue_script(
                'boochat-connect-customization-script',
                BOOCHAT_CONNECT_URL . 'assets/js/admin-customization.js',
                array('jquery'),
                BOOCHAT_CONNECT_VERSION,
                true
            );
            
            // Localize customization script with translations
            wp_localize_script('boochat-connect-customization-script', 'boochatConnectCustomization', array(
                'chooseIcon' => esc_html__('Choose Chat Icon', 'boochat-connect'),
                'useIcon' => esc_html__('Use this icon', 'boochat-connect'),
                'removeIcon' => esc_html__('Remove Icon', 'boochat-connect'),
            ));
        }
        
        // PRO page styles
        if ($current_page === 'boochat-connect-pro') {
            wp_enqueue_style(
                'boochat-connect-admin-main',
                BOOCHAT_CONNECT_URL . 'assets/css/admin-main.css',
                array('boochat-connect-admin-style'),
                BOOCHAT_CONNECT_VERSION
            );
            
            // Enqueue Stripe checkout script
            wp_enqueue_script(
                'boochat-connect-stripe-checkout',
                BOOCHAT_CONNECT_URL . 'assets/js/stripe-checkout.js',
                array('jquery'),
                BOOCHAT_CONNECT_VERSION,
                true
            );
            
            wp_localize_script('boochat-connect-stripe-checkout', 'boochatConnectStripe', array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('boochat-connect-stripe'),
                'proPageUrl' => admin_url('admin.php?page=boochat-connect-pro'),
                'configurationError' => esc_html__('Configuration Error', 'boochat-connect'),
                'loading' => esc_html__('Loading...', 'boochat-connect'),
                'failedCheckout' => esc_html__('Failed to create checkout session. Please try again.', 'boochat-connect'),
                'errorConnecting' => esc_html__('Error connecting to server. Please try again.', 'boochat-connect'),
            ));
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
                'proUpgradeUrl' => admin_url('admin.php?page=boochat-connect-pro'),
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
            wp_die(esc_html(boochat_connect_translate('no_permission_page', 'You do not have permission to access this page.')));
            return false;
        }
        
        if (!isset($_POST[$nonce_field]) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST[$nonce_field])), $nonce_action)) {
            wp_die(esc_html(boochat_connect_translate('security_error_try_again', 'Security error. Please try again.')));
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
        $chat_icon = isset($_POST['chat_icon']) ? esc_url_raw(wp_unslash($_POST['chat_icon'])) : '';
        update_option('boochat_connect_chat_icon', $chat_icon);
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in verify_request() above
        update_option('boochat_connect_chat_name', sanitize_text_field(wp_unslash($_POST['chat_name'] ?? boochat_connect_translate('chat_name_default'))));
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in verify_request() above
        update_option('boochat_connect_welcome_message', sanitize_textarea_field(wp_unslash($_POST['welcome_message'] ?? boochat_connect_translate('welcome_message_default'))));
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in verify_request() above
        update_option('boochat_connect_primary_color', sanitize_hex_color(wp_unslash($_POST['primary_color'] ?? '#1B8EF0')));
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in verify_request() above
        update_option('boochat_connect_secondary_color', sanitize_hex_color(wp_unslash($_POST['secondary_color'] ?? '#1B5D98')));
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
        
        if (!empty($current_page) && strpos($current_page, 'boochat-connect') !== false) {
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
            if ($screen && strpos($screen->id, 'boochat-connect') !== false) {
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
        $configured_language = get_option('boochat_connect_language', '');
        
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
            $language_code = boochat_connect_get_language_from_locale($default_locale);
            
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
            unload_textdomain('boochat-connect');
            // phpcs:ignore PluginCheck.CodeAnalysis.DiscouragedFunctions.load_plugin_textdomainFound -- Required for non-WordPress.org plugins and locale switching
            load_plugin_textdomain(
                'boochat-connect',
                false,
                dirname(plugin_basename(BOOCHAT_CONNECT_DIR . 'boochat-connect.php')) . '/languages'
            );
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
        $nonce = wp_create_nonce('boochat-connect-sessions');
        
        // Enqueue sessions script
        wp_enqueue_script(
            'boochat-connect-sessions',
            BOOCHAT_CONNECT_URL . 'assets/js/sessions-script.js',
            array('jquery'),
            BOOCHAT_CONNECT_VERSION,
            true
        );
        
        wp_localize_script('boochat-connect-sessions', 'boochatConnectSessions', array(
            'exportJsonText' => esc_html__('Export JSON', 'boochat-connect'),
            'exportCsvText' => esc_html__('Export CSV', 'boochat-connect'),
            'ajax_url' => $ajax_url,
            'nonce' => $nonce,
            'loadingText' => boochat_connect_translate('loading_sessions', 'Loading sessions...'),
            'errorLoadingText' => boochat_connect_translate('error_loading_sessions', 'Error loading sessions: '),
            'noSessionsText' => boochat_connect_translate('no_sessions_found', 'No sessions found.')
        ));
        
        include BOOCHAT_CONNECT_DIR . 'includes/views/admin-sessions.php';
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
        $redirect_url = admin_url('admin.php?page=boochat-connect-pro');
        // Use JavaScript as fallback in case PHP redirect doesn't work
        ?>
        <script type="text/javascript">
            window.location.href = '<?php echo esc_js($redirect_url); ?>';
        </script>
        <?php
        wp_safe_redirect($redirect_url);
        exit;
    }
    
    /**
     * Render statistics page
     */
    public function render_statistics_page() {
        // Check if user has PRO license
        if (!$this->license->is_pro()) {
            // Redirect to PRO upgrade page
            wp_safe_redirect(admin_url('admin.php?page=boochat-connect-pro'));
            exit;
        }
        
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
        if (!$this->verify_request('boochat_connect_activate_license', 'license_nonce')) {
            return;
        }
        
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in verify_request() above
        $license_key = isset($_POST['license_key']) ? sanitize_text_field(wp_unslash($_POST['license_key'])) : '';
        
        $result = $this->license->activate_license($license_key);
        
        if ($result['success']) {
            wp_safe_redirect(add_query_arg(array(
                'page' => 'boochat-connect-pro',
                'activation' => 'success'
            ), admin_url('admin.php')));
        } else {
            wp_safe_redirect(add_query_arg(array(
                'page' => 'boochat-connect-pro',
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
        if (!$this->verify_request('boochat_connect_deactivate_license', 'deactivate_nonce')) {
            return;
        }
        
        $result = $this->license->deactivate_license();
        
        wp_safe_redirect(add_query_arg(array(
            'page' => 'boochat-connect-pro',
            'deactivation' => $result['success'] ? 'success' : 'error'
        ), admin_url('admin.php')));
        exit;
    }
    
    /**
     * AJAX handler to create Stripe checkout session
     */
    public function ajax_create_stripe_session() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'boochat-connect-stripe')) {
            wp_send_json_error(array('message' => boochat_connect_translate('security_check_failed', 'Security check failed.')));
            return;
        }
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => boochat_connect_translate('no_permission', 'No permission.')));
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
                'message' => isset($result['message']) ? $result['message'] : boochat_connect_translate('failed_checkout_session', 'Failed to create checkout session.')
            ));
        }
    }
    
    /**
     * Handle checkout request (legacy POST form)
     */
    public function handle_stripe_checkout() {
        if (!$this->verify_request('boochat_connect_stripe_checkout', 'stripe_nonce')) {
            return;
        }
        
        $result = $this->license->request_checkout_url();
        
        if ($result['success'] && isset($result['checkout_url'])) {
            // Redirect to checkout
            wp_safe_redirect($result['checkout_url']);
            exit;
        } else {
            wp_safe_redirect(add_query_arg(array(
                'page' => 'boochat-connect-pro',
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
        if (!isset($_GET['page']) || sanitize_text_field(wp_unslash($_GET['page'])) !== 'boochat-connect-pro') {
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
                'page' => 'boochat-connect-pro',
                'payment' => 'success',
                'license_activated' => '1'
            ), admin_url('admin.php')));
        } else {
            // Check if license key was received but needs API key
            $needs_api_key = isset($result['needs_api_key']) && $result['needs_api_key'] === true;
            
            if ($needs_api_key) {
                wp_safe_redirect(add_query_arg(array(
                    'page' => 'boochat-connect-pro',
                    'payment' => 'success',
                    'license_received' => '1',
                    'needs_api_key' => '1'
                ), admin_url('admin.php')));
            } else {
                wp_safe_redirect(add_query_arg(array(
                    'page' => 'boochat-connect-pro',
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
        $settings_link = '<a href="' . esc_url(admin_url('admin.php?page=boochat-connect-settings')) . '">' . esc_html__('Settings', 'boochat-connect') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }
}

