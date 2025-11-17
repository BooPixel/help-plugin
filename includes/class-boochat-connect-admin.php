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
            $current_page === 'boochat-connect-settings' ||
            $current_page === 'boochat-connect-statistics' ||
            strpos($hook, 'boochat-connect') !== false
        );
        
        if (!$is_plugin_page) {
            return;
        }
        
        wp_enqueue_style(
            'boochat-connect-admin-style',
            BOOCHAT_CONNECT_URL . 'assets/css/admin-style.css',
            array(),
            BOOCHAT_CONNECT_VERSION
        );
        
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
     * Save customization
     */
    public function save_customization() {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('You do not have permission to access this page.', 'boochat-connect'));
        }
        
        if (!isset($_POST['boochat_connect_customization_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['boochat_connect_customization_nonce'])), 'boochat_connect_save_customization')) {
            wp_die(esc_html__('Security error. Please try again.', 'boochat-connect'));
        }
        
        // Save customization settings
        update_option('boochat_connect_chat_name', sanitize_text_field(wp_unslash($_POST['chat_name'] ?? boochat_connect_translate('chat_name_default'))));
        update_option('boochat_connect_welcome_message', sanitize_textarea_field(wp_unslash($_POST['welcome_message'] ?? boochat_connect_translate('welcome_message_default'))));
        update_option('boochat_connect_primary_color', sanitize_hex_color(wp_unslash($_POST['primary_color'] ?? '#667eea')));
        update_option('boochat_connect_secondary_color', sanitize_hex_color(wp_unslash($_POST['secondary_color'] ?? '#764ba2')));
        update_option('boochat_connect_chat_bg_color', sanitize_hex_color(wp_unslash($_POST['chat_bg_color'] ?? '#ffffff')));
        update_option('boochat_connect_text_color', sanitize_hex_color(wp_unslash($_POST['text_color'] ?? '#333333')));
        update_option('boochat_connect_font_family', sanitize_text_field(wp_unslash($_POST['font_family'] ?? 'Arial, sans-serif')));
        update_option('boochat_connect_font_size', sanitize_text_field(wp_unslash($_POST['font_size'] ?? '14px')));
        
        $this->settings->clear_cache();
        
        wp_safe_redirect(add_query_arg(array(
            'page' => 'boochat-connect',
            'customization-updated' => 'true'
        ), admin_url('admin.php')));
        exit;
    }
    
    /**
     * Save settings
     */
    public function save_settings() {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('You do not have permission to access this page.', 'boochat-connect'));
        }
        
        if (!isset($_POST['boochat_connect_settings_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['boochat_connect_settings_nonce'])), 'boochat_connect_save_settings')) {
            wp_die(esc_html__('Security error. Please try again.', 'boochat-connect'));
        }
        
        $api_url = isset($_POST['api_url']) ? esc_url_raw(wp_unslash($_POST['api_url'])) : '';
        update_option('boochat_connect_api_url', $api_url);
        
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
     * Render admin page (customization)
     */
    public function render_admin_page() {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading GET parameter for display only
        $customization_updated = isset($_GET['customization-updated']) && sanitize_text_field(wp_unslash($_GET['customization-updated'])) === 'true';
        
        $settings = $this->settings->get_customization_settings();
        ?>
        <div class="wrap boochat-connect-wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <?php if ($customization_updated): ?>
                <div class="notice notice-success is-dismissible">
                    <p><?php echo esc_html(boochat_connect_translate('customization_saved')); ?></p>
                </div>
            <?php endif; ?>
            
            <div class="boochat-connect-content">
                <div class="boochat-connect-card">
                    <h2><?php echo esc_html(boochat_connect_translate('chat_customization')); ?></h2>
                    <p><?php echo esc_html(boochat_connect_translate('customize_colors_typography')); ?></p>
                    
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                        <?php wp_nonce_field('boochat_connect_save_customization', 'boochat_connect_customization_nonce'); ?>
                        <input type="hidden" name="action" value="boochat_connect_save_customization">
                        
                        <table class="form-table">
                            <tbody>
                                <tr>
                                    <th scope="row">
                                        <label for="chat_name"><?php echo esc_html(boochat_connect_translate('chat_name')); ?></label>
                                    </th>
                                    <td>
                                        <input 
                                            type="text" 
                                            id="chat_name" 
                                            name="chat_name" 
                                            value="<?php echo esc_attr($settings['chat_name']); ?>" 
                                            class="regular-text"
                                            placeholder="<?php echo esc_attr(boochat_connect_translate('chat_name_default')); ?>"
                                        >
                                        <p class="description">
                                            <?php echo esc_html(boochat_connect_translate('name_displayed_header')); ?>
                                        </p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row">
                                        <label for="welcome_message"><?php echo esc_html(boochat_connect_translate('welcome_message')); ?></label>
                                    </th>
                                    <td>
                                        <textarea 
                                            id="welcome_message" 
                                            name="welcome_message" 
                                            rows="3"
                                            class="large-text"
                                            placeholder="<?php echo esc_attr(boochat_connect_translate('welcome_message_default')); ?>"
                                        ><?php echo esc_textarea($settings['welcome_message']); ?></textarea>
                                        <p class="description">
                                            <?php echo esc_html(boochat_connect_translate('welcome_message_displayed')); ?>
                                        </p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row">
                                        <label for="primary_color"><?php echo esc_html(boochat_connect_translate('primary_color')); ?></label>
                                    </th>
                                    <td>
                                        <input 
                                            type="color" 
                                            id="primary_color" 
                                            name="primary_color" 
                                            value="<?php echo esc_attr($settings['primary_color']); ?>"
                                        >
                                        <p class="description">
                                            <?php echo esc_html(boochat_connect_translate('primary_gradient_color')); ?>
                                        </p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row">
                                        <label for="secondary_color"><?php echo esc_html(boochat_connect_translate('secondary_color')); ?></label>
                                    </th>
                                    <td>
                                        <input 
                                            type="color" 
                                            id="secondary_color" 
                                            name="secondary_color" 
                                            value="<?php echo esc_attr($settings['secondary_color']); ?>"
                                        >
                                        <p class="description">
                                            <?php echo esc_html(boochat_connect_translate('secondary_gradient_color')); ?>
                                        </p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row">
                                        <label for="chat_bg_color"><?php echo esc_html(boochat_connect_translate('chat_bg_color')); ?></label>
                                    </th>
                                    <td>
                                        <input 
                                            type="color" 
                                            id="chat_bg_color" 
                                            name="chat_bg_color" 
                                            value="<?php echo esc_attr($settings['chat_bg_color']); ?>"
                                        >
                                        <p class="description">
                                            <?php echo esc_html(boochat_connect_translate('chat_window_background')); ?>
                                        </p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row">
                                        <label for="text_color"><?php echo esc_html(boochat_connect_translate('text_color')); ?></label>
                                    </th>
                                    <td>
                                        <input 
                                            type="color" 
                                            id="text_color" 
                                            name="text_color" 
                                            value="<?php echo esc_attr($settings['text_color']); ?>"
                                        >
                                        <p class="description">
                                            <?php echo esc_html(boochat_connect_translate('message_text_color')); ?>
                                        </p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row">
                                        <label for="font_family"><?php echo esc_html(boochat_connect_translate('font_family')); ?></label>
                                    </th>
                                    <td>
                                        <select id="font_family" name="font_family" class="regular-text">
                                            <option value="Arial, sans-serif" <?php selected($settings['font_family'], 'Arial, sans-serif'); ?>>Arial</option>
                                            <option value="'Helvetica Neue', Helvetica, sans-serif" <?php selected($settings['font_family'], "'Helvetica Neue', Helvetica, sans-serif"); ?>>Helvetica</option>
                                            <option value="Georgia, serif" <?php selected($settings['font_family'], 'Georgia, serif'); ?>>Georgia</option>
                                            <option value="'Times New Roman', Times, serif" <?php selected($settings['font_family'], "'Times New Roman', Times, serif"); ?>>Times New Roman</option>
                                            <option value="'Courier New', Courier, monospace" <?php selected($settings['font_family'], "'Courier New', Courier, monospace"); ?>>Courier New</option>
                                            <option value="Verdana, sans-serif" <?php selected($settings['font_family'], 'Verdana, sans-serif'); ?>>Verdana</option>
                                            <option value="'Trebuchet MS', sans-serif" <?php selected($settings['font_family'], "'Trebuchet MS', sans-serif"); ?>>Trebuchet MS</option>
                                            <option value="'Open Sans', sans-serif" <?php selected($settings['font_family'], "'Open Sans', sans-serif"); ?>>Open Sans</option>
                                            <option value="'Roboto', sans-serif" <?php selected($settings['font_family'], "'Roboto', sans-serif"); ?>>Roboto</option>
                                        </select>
                                        <p class="description">
                                            <?php echo esc_html(boochat_connect_translate('font_family_chat')); ?>
                                        </p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row">
                                        <label for="font_size"><?php echo esc_html(boochat_connect_translate('font_size')); ?></label>
                                    </th>
                                    <td>
                                        <select id="font_size" name="font_size" class="regular-text">
                                            <option value="12px" <?php selected($settings['font_size'], '12px'); ?>>12px - <?php echo esc_html(boochat_connect_translate('small', 'Small')); ?></option>
                                            <option value="14px" <?php selected($settings['font_size'], '14px'); ?>>14px - <?php echo esc_html(boochat_connect_translate('normal', 'Normal')); ?></option>
                                            <option value="16px" <?php selected($settings['font_size'], '16px'); ?>>16px - <?php echo esc_html(boochat_connect_translate('medium', 'Medium')); ?></option>
                                            <option value="18px" <?php selected($settings['font_size'], '18px'); ?>>18px - <?php echo esc_html(boochat_connect_translate('large', 'Large')); ?></option>
                                            <option value="20px" <?php selected($settings['font_size'], '20px'); ?>>20px - <?php echo esc_html(boochat_connect_translate('extra_large', 'Extra Large')); ?></option>
                                        </select>
                                        <p class="description">
                                            <?php echo esc_html(boochat_connect_translate('font_size_messages')); ?>
                                        </p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <?php submit_button(boochat_connect_translate('save_customizations')); ?>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render settings page
     */
    public function render_settings_page() {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading GET parameter for display only
        $settings_updated = isset($_GET['settings-updated']) && sanitize_text_field(wp_unslash($_GET['settings-updated'])) === 'true';
        
        $api_url = $this->api->get_api_url();
        $current_language = $this->settings->get_language();
        ?>
        <div class="wrap boochat-connect-wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <?php if ($settings_updated): ?>
                <div class="notice notice-success is-dismissible">
                    <p><?php echo esc_html(boochat_connect_translate('settings_saved')); ?></p>
                </div>
            <?php endif; ?>
            
            <div class="boochat-connect-content">
                <div class="boochat-connect-card">
                    <h2><?php echo esc_html(boochat_connect_translate('api_settings')); ?></h2>
                    <p><?php echo esc_html(boochat_connect_translate('configure_api_url')); ?></p>
                    
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                        <?php wp_nonce_field('boochat_connect_save_settings', 'boochat_connect_settings_nonce'); ?>
                        <input type="hidden" name="action" value="boochat_connect_save_settings">
                        
                        <table class="form-table">
                            <tbody>
                                <tr>
                                    <th scope="row">
                                        <label for="language"><?php echo esc_html(boochat_connect_translate('language', 'Language')); ?></label>
                                    </th>
                                    <td>
                                        <?php
                                        $wp_locale = get_locale();
                                        $wp_language = boochat_connect_get_language_from_locale($wp_locale);
                                        $wp_language_name = '';
                                        switch ($wp_language) {
                                            case 'pt':
                                                $wp_language_name = 'Português';
                                                break;
                                            case 'es':
                                                $wp_language_name = 'Español';
                                                break;
                                            default:
                                                $wp_language_name = 'English';
                                        }
                                        ?>
                                        <select id="language" name="language" class="regular-text">
                                            <option value="" <?php selected($current_language, '', true); ?>><?php echo esc_html(boochat_connect_translate('language_auto', 'Auto (WordPress Language)')); ?> (<?php echo esc_html($wp_language_name); ?>)</option>
                                            <option value="en" <?php selected($current_language, 'en'); ?>>English</option>
                                            <option value="pt" <?php selected($current_language, 'pt'); ?>>Português</option>
                                            <option value="es" <?php selected($current_language, 'es'); ?>>Español</option>
                                        </select>
                                        <p class="description">
                                            <?php echo esc_html(boochat_connect_translate('language_description')); ?>
                                            <?php if (empty($current_language)): ?>
                                                <br><strong><?php
                                                    /* translators: %s: WordPress language name */
                                                    printf(esc_html__('Currently using: %s (from WordPress)', 'boochat-connect'), esc_html($wp_language_name));
                                                ?></strong>
                                            <?php else: ?>
                                                <br><strong><?php
                                                    /* translators: %s: Custom language name */
                                                    printf(esc_html__('Currently using: %s (custom)', 'boochat-connect'), esc_html($current_language === 'pt' ? 'Português' : ($current_language === 'es' ? 'Español' : 'English')));
                                                ?></strong>
                                            <?php endif; ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="api_url"><?php echo esc_html(boochat_connect_translate('api_url', 'API URL')); ?></label>
                                    </th>
                                    <td>
                                        <input 
                                            type="url" 
                                            id="api_url" 
                                            name="api_url" 
                                            value="<?php echo esc_attr($api_url); ?>" 
                                            class="regular-text"
                                            placeholder="https://example.com/webhook/chat"
                                            required
                                        >
                                        <p class="description">
                                            <?php echo esc_html(boochat_connect_translate('api_webhook_url')); ?>
                                        </p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <?php submit_button(boochat_connect_translate('save_settings')); ?>
                    </form>
                </div>
                
                <div class="boochat-connect-card">
                    <h3><?php echo esc_html(boochat_connect_translate('information', 'Information')); ?></h3>
                    <p><?php echo esc_html(boochat_connect_translate('api_will_process')); ?></p>
                    <p><strong><?php echo esc_html(boochat_connect_translate('status', 'Status')); ?>:</strong> 
                        <?php if (!empty($api_url)): ?>
                            <span style="color: green;"><?php echo esc_html(boochat_connect_translate('api_configured')); ?></span>
                        <?php else: ?>
                            <span style="color: red;"><?php echo esc_html(boochat_connect_translate('api_not_configured')); ?></span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render statistics page
     */
    public function render_statistics_page() {
        $this->statistics->render_page();
    }
}

