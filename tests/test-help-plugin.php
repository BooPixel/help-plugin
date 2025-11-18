<?php
/**
 * Unit tests for BooChat Connect Plugin
 */

use PHPUnit\Framework\TestCase;

class BooChat_Connect_Test extends TestCase {
    
    /**
     * @var BooChat_Connect
     */
    private $plugin;
    
    /**
     * Set up test environment
     */
    protected function setUp(): void {
        parent::setUp();
        
        // Mock WordPress functions
        if (!function_exists('add_action')) {
            function add_action($hook, $callback, $priority = 10, $accepted_args = 1) {
                return true;
            }
        }
        
        if (!function_exists('add_menu_page')) {
            function add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function, $icon_url = '', $position = null) {
                return true;
            }
        }
        
        if (!function_exists('add_submenu_page')) {
            function add_submenu_page($parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function) {
                return true;
            }
        }
        
        if (!function_exists('wp_enqueue_style')) {
            function wp_enqueue_style($handle, $src = '', $deps = array(), $ver = false, $media = 'all') {
                return true;
            }
        }
        
        if (!function_exists('wp_enqueue_script')) {
            function wp_enqueue_script($handle, $src = '', $deps = array(), $ver = false, $in_footer = false) {
                return true;
            }
        }
        
        if (!function_exists('wp_localize_script')) {
            function wp_localize_script($handle, $object_name, $l10n) {
                return true;
            }
        }
        
        if (!function_exists('admin_url')) {
            function admin_url($path = '') {
                return 'http://example.com/wp-admin/' . $path;
            }
        }
        
        if (!function_exists('plugin_dir_path')) {
            function plugin_dir_path($file) {
                return dirname($file) . '/';
            }
        }
        
        if (!function_exists('plugin_dir_url')) {
            function plugin_dir_url($file) {
                return 'http://example.com/wp-content/plugins/boochat-connect/';
            }
        }
        
        if (!function_exists('get_option')) {
            function get_option($option, $default = false) {
                return $default;
            }
        }
        
        if (!function_exists('update_option')) {
            function update_option($option, $value) {
                return true;
            }
        }
        
        if (!function_exists('current_time')) {
            function current_time($type, $gmt = 0) {
                return gmdate('Y-m-d H:i:s');
            }
        }
        
        if (!function_exists('wp_strip_all_tags')) {
            function wp_strip_all_tags($string, $remove_breaks = false) {
                $string = preg_replace('@<(script|style)[^>]*?>.*?</\\1>@si', '', $string);
                $string = preg_replace('@<[^>]*?>@', '', $string);
                if ($remove_breaks) {
                    $string = preg_replace('/[\r\n\t ]+/', ' ', $string);
                }
                return trim($string);
            }
        }
        
        if (!function_exists('sanitize_text_field')) {
            function sanitize_text_field($str) {
                return trim(wp_strip_all_tags($str));
            }
        }
        
        if (!function_exists('sanitize_textarea_field')) {
            function sanitize_textarea_field($str) {
                return trim($str);
            }
        }
        
        if (!function_exists('sanitize_hex_color')) {
            function sanitize_hex_color($color) {
                return preg_match('/^#[a-f0-9]{6}$/i', $color) ? $color : '#000000';
            }
        }
        
        if (!function_exists('esc_attr')) {
            function esc_attr($text) {
                return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
            }
        }
        
        if (!function_exists('esc_html')) {
            function esc_html($text) {
                return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
            }
        }
        
        if (!function_exists('esc_textarea')) {
            function esc_textarea($text) {
                return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
            }
        }
        
        if (!function_exists('esc_url_raw')) {
            function esc_url_raw($url) {
                return filter_var($url, FILTER_SANITIZE_URL);
            }
        }
        
        if (!function_exists('wp_create_nonce')) {
            function wp_create_nonce($action = -1) {
                return 'test-nonce-' . $action;
            }
        }
        
        if (!function_exists('wp_verify_nonce')) {
            function wp_verify_nonce($nonce, $action = -1) {
                return $nonce === 'test-nonce-' . $action;
            }
        }
        
        if (!function_exists('wp_nonce_field')) {
            function wp_nonce_field($action = -1, $name = '_wpnonce', $referer = true, $echo = true) {
                return '<input type="hidden" name="' . $name . '" value="test-nonce-' . $action . '" />';
            }
        }
        
        if (!function_exists('current_user_can')) {
            function current_user_can($capability) {
                return true;
            }
        }
        
        if (!function_exists('is_admin')) {
            function is_admin() {
                return false;
            }
        }
        
        if (!function_exists('get_admin_page_title')) {
            function get_admin_page_title() {
                return 'BooChat Connect';
            }
        }
        
        if (!function_exists('submit_button')) {
            function submit_button($text = null, $type = 'primary', $name = 'submit', $wrap = true, $other_attributes = null) {
                return '<button type="submit" class="button button-' . $type . '">' . ($text ?: 'Save') . '</button>';
            }
        }
        
        if (!function_exists('get_locale')) {
            function get_locale() {
                return 'en_US';
            }
        }
        
        if (!function_exists('check_ajax_referer')) {
            function check_ajax_referer($action = -1, $query_arg = false, $die = true) {
                return true;
            }
        }
        
        if (!function_exists('wp_send_json_success')) {
            function wp_send_json_success($data = null) {
                return array('success' => true, 'data' => $data);
            }
        }
        
        if (!function_exists('wp_send_json_error')) {
            function wp_send_json_error($data = null) {
                return array('success' => false, 'data' => $data);
            }
        }
        
        if (!function_exists('wp_die')) {
            function wp_die($message = '', $title = '', $args = array()) {
                return;
            }
        }
        
        if (!function_exists('wp_safe_redirect')) {
            function wp_safe_redirect($location, $status = 302) {
                return true;
            }
        }
        
        if (!function_exists('add_query_arg')) {
            function add_query_arg($key, $value = null, $url = null) {
                if (is_array($key)) {
                    $url = $value;
                    $query = http_build_query($key);
                    return $url ? $url . '?' . $query : '?' . $query;
                }
                return $url ? $url . '?' . $key . '=' . $value : '?' . $key . '=' . $value;
            }
        }
        
        if (!function_exists('flush_rewrite_rules')) {
            function flush_rewrite_rules($hard = true) {
                return true;
            }
        }
        
        if (!function_exists('dbDelta')) {
            function dbDelta($queries, $execute = true) {
                return array();
            }
        }
        
        if (!function_exists('delete_option')) {
            function delete_option($option) {
                return true;
            }
        }
        
        if (!function_exists('wp_unslash')) {
            function wp_unslash($value) {
                return stripslashes($value);
            }
        }
        
        if (!function_exists('esc_sql')) {
            function esc_sql($data) {
                return addslashes($data);
            }
        }
        
        if (!function_exists('wp_remote_post')) {
            function wp_remote_post($url, $args = array()) {
                return array(
                    'body' => json_encode(array('output' => 'Test response')),
                    'response' => array('code' => 200)
                );
            }
        }
        
        if (!function_exists('wp_remote_retrieve_response_code')) {
            function wp_remote_retrieve_response_code($response) {
                return isset($response['response']['code']) ? $response['response']['code'] : 200;
            }
        }
        
        if (!function_exists('wp_remote_retrieve_body')) {
            function wp_remote_retrieve_body($response) {
                return isset($response['body']) ? $response['body'] : '';
            }
        }
        
        if (!function_exists('is_wp_error')) {
            function is_wp_error($thing) {
                return false;
            }
        }
        
        // Mock wpdb
        global $wpdb;
        if (!isset($wpdb)) {
            $wpdb = new class {
                public $prefix = 'wp_';
                
                public function insert($table, $data, $format = null) {
                    return true;
                }
                
                public function get_var($query = null) {
                    return 0;
                }
                
                public function get_results($query = null) {
                    return array();
                }
                
                public function prepare($query, ...$args) {
                    return $query;
                }
                
                public function query($query) {
                    return true;
                }
                
                public function get_charset_collate() {
                    return 'DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
                }
            };
        }
        
        if (!function_exists('selected')) {
            function selected($selected, $current = true, $echo = true) {
                $result = ($selected == $current) ? ' selected="selected"' : '';
                if ($echo) {
                    echo esc_attr($result);
                }
                return $result;
            }
        }
        
        if (!defined('ABSPATH')) {
            define('ABSPATH', '/');
        }
        
        if (!defined('DB_NAME')) {
            define('DB_NAME', 'test_db');
        }
        
        // Load plugin file
        if (!class_exists('BooChat_Connect')) {
            require_once dirname(__DIR__) . '/boochat-connect.php';
        }
        
        // Reset singleton instance
        $reflection = new ReflectionClass('BooChat_Connect');
        $instance = $reflection->getProperty('instance');
        $instance->setAccessible(true);
        $instance->setValue(null, null);
        
        $this->plugin = BooChat_Connect::get_instance();
    }
    
    /**
     * Tear down test environment
     */
    protected function tearDown(): void {
        parent::tearDown();
        
        // Reset singleton instance
        $reflection = new ReflectionClass('BooChat_Connect');
        $instance = $reflection->getProperty('instance');
        $instance->setAccessible(true);
        $instance->setValue(null, null);
    }
    
    /**
     * Test singleton pattern
     */
    public function test_get_instance_returns_same_instance() {
        $instance1 = BooChat_Connect::get_instance();
        $instance2 = BooChat_Connect::get_instance();
        
        $this->assertSame($instance1, $instance2);
    }
    
    /**
     * Test get_instance returns BooChat_Connect instance
     */
    public function test_get_instance_returns_boochat_connect_instance() {
        $instance = BooChat_Connect::get_instance();
        
        $this->assertInstanceOf('BooChat_Connect', $instance);
    }
    
    /**
     * Test constructor is private
     */
    public function test_constructor_is_private() {
        $reflection = new ReflectionClass('BooChat_Connect');
        $constructor = $reflection->getConstructor();
        
        $this->assertTrue($constructor->isPrivate());
    }
    
    /**
     * Test constants are defined
     */
    public function test_constants_are_defined() {
        $this->assertTrue(defined('BOOCHAT_CONNECT_VERSION'));
        $this->assertTrue(defined('BOOCHAT_CONNECT_DIR'));
        $this->assertTrue(defined('BOOCHAT_CONNECT_URL'));
    }
    
    /**
     * Test plugin version constant
     */
    public function test_plugin_version_constant() {
        $this->assertNotEmpty(BOOCHAT_CONNECT_VERSION);
        $this->assertIsString(BOOCHAT_CONNECT_VERSION);
    }
    
    /**
     * Test boochat_connect_init function exists
     */
    public function test_boochat_connect_init_function_exists() {
        $this->assertTrue(function_exists('boochat_connect_init'));
    }
    
    /**
     * Test boochat_connect_init returns BooChat_Connect instance
     */
    public function test_boochat_connect_init_returns_boochat_connect_instance() {
        $result = boochat_connect_init();
        
        $this->assertInstanceOf('BooChat_Connect', $result);
    }
    
    /**
     * Test boochat_connect_get_version function exists
     */
    public function test_boochat_connect_get_version_function_exists() {
        $this->assertTrue(function_exists('boochat_connect_get_version'));
    }
    
    /**
     * Test boochat_connect_get_version returns value
     */
    public function test_boochat_connect_get_version_returns_value() {
        $version = boochat_connect_get_version();
        $this->assertNotEmpty($version);
        $this->assertIsString($version);
    }
    
    /**
     * Test boochat_connect_get_language_from_locale function exists
     */
    public function test_boochat_connect_get_language_from_locale_function_exists() {
        $this->assertTrue(function_exists('boochat_connect_get_language_from_locale'));
    }
    
    /**
     * Test boochat_connect_get_language_from_locale returns correct language codes
     */
    public function test_boochat_connect_get_language_from_locale_returns_correct_codes() {
        $this->assertEquals('pt', boochat_connect_get_language_from_locale('pt_BR'));
        $this->assertEquals('es', boochat_connect_get_language_from_locale('es_ES'));
        $this->assertEquals('en', boochat_connect_get_language_from_locale('en_US'));
        $this->assertEquals('en', boochat_connect_get_language_from_locale('en_GB'));
    }
    
    /**
     * Test boochat_connect_get_language_from_locale defaults to English
     */
    public function test_boochat_connect_get_language_from_locale_defaults_to_english() {
        $this->assertEquals('en', boochat_connect_get_language_from_locale('fr_FR'));
        $this->assertEquals('en', boochat_connect_get_language_from_locale('de_DE'));
    }
    
    /**
     * Test boochat_connect_translate function exists
     */
    public function test_boochat_connect_translate_function_exists() {
        $this->assertTrue(function_exists('boochat_connect_translate'));
    }
    
    /**
     * Test boochat_connect_translate returns English translation by default
     */
    public function test_boochat_connect_translate_returns_english_by_default() {
        $result = boochat_connect_translate('chat_name_default');
        $this->assertEquals('Support', $result);
    }
    
    /**
     * Test activate method exists
     */
    public function test_activate_method_exists() {
        $this->assertTrue(method_exists('BooChat_Connect', 'activate'));
    }
    
    /**
     * Test deactivate method exists
     */
    public function test_deactivate_method_exists() {
        $this->assertTrue(method_exists('BooChat_Connect', 'deactivate'));
    }
    
    /**
     * Test uninstall method exists
     */
    public function test_uninstall_method_exists() {
        $this->assertTrue(method_exists('BooChat_Connect', 'uninstall'));
    }
    
    /**
     * Test Database class exists
     */
    public function test_database_class_exists() {
        $this->assertTrue(class_exists('BooChat_Connect_Database'));
    }
    
    /**
     * Test Database create_table method exists
     */
    public function test_database_create_table_method_exists() {
        $database = new BooChat_Connect_Database();
        $this->assertTrue(method_exists($database, 'create_table'));
    }
    
    /**
     * Test Database log_interaction method exists
     */
    public function test_database_log_interaction_method_exists() {
        $database = new BooChat_Connect_Database();
        $this->assertTrue(method_exists($database, 'log_interaction'));
    }
    
    /**
     * Test Database get_interactions_count method exists
     */
    public function test_database_get_interactions_count_method_exists() {
        $database = new BooChat_Connect_Database();
        $this->assertTrue(method_exists($database, 'get_interactions_count'));
    }
    
    /**
     * Test Database get_chart_data method exists
     */
    public function test_database_get_chart_data_method_exists() {
        $database = new BooChat_Connect_Database();
        $this->assertTrue(method_exists($database, 'get_chart_data'));
    }
    
    /**
     * Test Database get_calendar_data method exists
     */
    public function test_database_get_calendar_data_method_exists() {
        $database = new BooChat_Connect_Database();
        $this->assertTrue(method_exists($database, 'get_calendar_data'));
    }
    
    /**
     * Test API class exists
     */
    public function test_api_class_exists() {
        $this->assertTrue(class_exists('BooChat_Connect_API'));
    }
    
    /**
     * Test API get_api_url method exists
     */
    public function test_api_get_api_url_method_exists() {
        $api = new BooChat_Connect_API();
        $this->assertTrue(method_exists($api, 'get_api_url'));
    }
    
    /**
     * Test API send_message method exists
     */
    public function test_api_send_message_method_exists() {
        $api = new BooChat_Connect_API();
        $this->assertTrue(method_exists($api, 'send_message'));
    }
    
    /**
     * Test API generate_session_id method exists
     */
    public function test_api_generate_session_id_method_exists() {
        $api = new BooChat_Connect_API();
        $this->assertTrue(method_exists($api, 'generate_session_id'));
    }
    
    /**
     * Test Settings class exists
     */
    public function test_settings_class_exists() {
        $this->assertTrue(class_exists('BooChat_Connect_Settings'));
    }
    
    /**
     * Test Settings get_customization_settings method exists
     */
    public function test_settings_get_customization_settings_method_exists() {
        $settings = new BooChat_Connect_Settings();
        $this->assertTrue(method_exists($settings, 'get_customization_settings'));
    }
    
    /**
     * Test Settings get_customization_settings returns array
     */
    public function test_settings_get_customization_settings_returns_array() {
        $settings = new BooChat_Connect_Settings();
        $result = $settings->get_customization_settings();
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('chat_name', $result);
        $this->assertArrayHasKey('welcome_message', $result);
        $this->assertArrayHasKey('primary_color', $result);
        $this->assertArrayHasKey('secondary_color', $result);
        $this->assertArrayHasKey('chat_bg_color', $result);
        $this->assertArrayHasKey('text_color', $result);
        $this->assertArrayHasKey('font_family', $result);
        $this->assertArrayHasKey('font_size', $result);
        $this->assertArrayHasKey('language', $result);
    }
    
    /**
     * Test Settings get_language method exists
     */
    public function test_settings_get_language_method_exists() {
        $settings = new BooChat_Connect_Settings();
        $this->assertTrue(method_exists($settings, 'get_language'));
    }
    
    /**
     * Test Settings get_effective_language method exists
     */
    public function test_settings_get_effective_language_method_exists() {
        $settings = new BooChat_Connect_Settings();
        $this->assertTrue(method_exists($settings, 'get_effective_language'));
    }
    
    /**
     * Test Admin class exists
     */
    public function test_admin_class_exists() {
        $this->assertTrue(class_exists('BooChat_Connect_Admin'));
    }
    
    /**
     * Test Statistics class exists
     */
    public function test_statistics_class_exists() {
        $this->assertTrue(class_exists('BooChat_Connect_Statistics'));
    }
    
    /**
     * Test Statistics render_page method exists
     */
    public function test_statistics_render_page_method_exists() {
        $database = new BooChat_Connect_Database();
        $statistics = new BooChat_Connect_Statistics($database);
        $this->assertTrue(method_exists($statistics, 'render_page'));
    }
    
    /**
     * Test Statistics render_page outputs HTML
     */
    public function test_statistics_render_page_outputs_html() {
        $database = new BooChat_Connect_Database();
        $statistics = new BooChat_Connect_Statistics($database);
        
        ob_start();
        $statistics->render_page();
        $output = ob_get_clean();
        
        $this->assertStringContainsString('<div class="wrap">', $output);
        $this->assertStringContainsString('interactions-chart', $output);
    }
    
    /**
     * Test AJAX class exists
     */
    public function test_ajax_class_exists() {
        $this->assertTrue(class_exists('BooChat_Connect_Ajax'));
    }
    
    /**
     * Test Frontend class exists
     */
    public function test_frontend_class_exists() {
        $this->assertTrue(class_exists('BooChat_Connect_Frontend'));
    }
    
    /**
     * Test Database get_interactions_count returns integer
     */
    public function test_database_get_interactions_count_returns_integer() {
        $database = new BooChat_Connect_Database();
        $count = $database->get_interactions_count(1);
        
        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(0, $count);
    }
    
    /**
     * Test Database get_chart_data returns array with labels and data
     */
    public function test_database_get_chart_data_returns_array() {
        $database = new BooChat_Connect_Database();
        $result = $database->get_chart_data('2025-01-01', '2025-01-31');
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('labels', $result);
        $this->assertArrayHasKey('data', $result);
        $this->assertIsArray($result['labels']);
        $this->assertIsArray($result['data']);
    }
    
    /**
     * Test Database get_calendar_data returns array
     */
    public function test_database_get_calendar_data_returns_array() {
        $database = new BooChat_Connect_Database();
        $result = $database->get_calendar_data();
        
        $this->assertIsArray($result);
    }
    
    /**
     * Test API generate_session_id returns string
     */
    public function test_api_generate_session_id_returns_string() {
        $api = new BooChat_Connect_API();
        $session_id = $api->generate_session_id();
        
        $this->assertIsString($session_id);
        $this->assertNotEmpty($session_id);
        $this->assertEquals(32, strlen($session_id)); // 16 bytes = 32 hex chars
    }
    
    /**
     * Test API get_api_url returns string
     */
    public function test_api_get_api_url_returns_string() {
        $api = new BooChat_Connect_API();
        $url = $api->get_api_url();
        
        $this->assertIsString($url);
    }
    
    /**
     * Test boochat_connect_translate returns correct translations
     */
    public function test_boochat_connect_translate_returns_correct_translations() {
        $this->assertEquals('Support', boochat_connect_translate('chat_name_default'));
        $this->assertEquals('Hello! How can we help you today?', boochat_connect_translate('welcome_message_default'));
        $this->assertEquals('Send', boochat_connect_translate('send'));
        $this->assertEquals('Close', boochat_connect_translate('close'));
    }
    
    /**
     * Test boochat_connect_translate returns default when key not found
     */
    public function test_boochat_connect_translate_returns_default_when_key_not_found() {
        $result = boochat_connect_translate('non_existent_key', 'Default Value');
        $this->assertEquals('Default Value', $result);
    }
    
    /**
     * Test Admin class has load_view method
     */
    public function test_admin_has_load_view_method() {
        $admin = new BooChat_Connect_Admin(
            new BooChat_Connect_API(),
            new BooChat_Connect_Settings()
        );
        $this->assertTrue(method_exists($admin, 'load_view'));
    }
    
    /**
     * Test Admin class has verify_request method
     */
    public function test_admin_has_verify_request_method() {
        $admin = new BooChat_Connect_Admin(
            new BooChat_Connect_API(),
            new BooChat_Connect_Settings()
        );
        $this->assertTrue(method_exists($admin, 'verify_request'));
    }
    
    /**
     * Test Statistics class has load_view method
     */
    public function test_statistics_has_load_view_method() {
        $database = new BooChat_Connect_Database();
        $statistics = new BooChat_Connect_Statistics($database);
        $this->assertTrue(method_exists($statistics, 'load_view'));
    }
    
    /**
     * Test Frontend class has load_view method
     */
    public function test_frontend_has_load_view_method() {
        $frontend = new BooChat_Connect_Frontend(
            new BooChat_Connect_Settings()
        );
        $this->assertTrue(method_exists($frontend, 'load_view'));
    }
    
    /**
     * Test template files exist
     */
    public function test_template_files_exist() {
        $template_dir = BOOCHAT_CONNECT_DIR . 'includes/views/';
        $templates = array(
            'admin-main.php',
            'admin-customization.php',
            'admin-settings.php',
            'admin-statistics.php',
            'frontend-chat.php'
        );
        
        foreach ($templates as $template) {
            $this->assertFileExists($template_dir . $template, "Template file {$template} should exist");
        }
    }
    
    /**
     * Test CSS files exist
     */
    public function test_css_files_exist() {
        $css_dir = BOOCHAT_CONNECT_DIR . 'assets/css/';
        $css_files = array(
            'admin-style.css',
            'admin-main.css',
            'admin-statistics.css',
            'chat-style.css'
        );
        
        foreach ($css_files as $css_file) {
            $this->assertFileExists($css_dir . $css_file, "CSS file {$css_file} should exist");
        }
    }
    
    /**
     * Test Admin enqueue_admin_assets method exists
     */
    public function test_admin_enqueue_admin_assets_method_exists() {
        $admin = new BooChat_Connect_Admin(
            new BooChat_Connect_API(),
            new BooChat_Connect_Settings()
        );
        $this->assertTrue(method_exists($admin, 'enqueue_admin_assets'));
    }
    
    /**
     * Test Admin render_admin_page method exists
     */
    public function test_admin_render_admin_page_method_exists() {
        $admin = new BooChat_Connect_Admin(
            new BooChat_Connect_API(),
            new BooChat_Connect_Settings()
        );
        $this->assertTrue(method_exists($admin, 'render_admin_page'));
    }
    
    /**
     * Test Admin render_customization_page method exists
     */
    public function test_admin_render_customization_page_method_exists() {
        $admin = new BooChat_Connect_Admin(
            new BooChat_Connect_API(),
            new BooChat_Connect_Settings()
        );
        $this->assertTrue(method_exists($admin, 'render_customization_page'));
    }
    
    /**
     * Test Admin render_settings_page method exists
     */
    public function test_admin_render_settings_page_method_exists() {
        $admin = new BooChat_Connect_Admin(
            new BooChat_Connect_API(),
            new BooChat_Connect_Settings()
        );
        $this->assertTrue(method_exists($admin, 'render_settings_page'));
    }
    
    /**
     * Test Frontend output_custom_css method exists
     */
    public function test_frontend_output_custom_css_method_exists() {
        $frontend = new BooChat_Connect_Frontend(
            new BooChat_Connect_Settings()
        );
        $this->assertTrue(method_exists($frontend, 'output_custom_css'));
    }
    
    /**
     * Test Frontend render_chat_widget method exists
     */
    public function test_frontend_render_chat_widget_method_exists() {
        $frontend = new BooChat_Connect_Frontend(
            new BooChat_Connect_Settings()
        );
        $this->assertTrue(method_exists($frontend, 'render_chat_widget'));
    }
    
    /**
     * Test AJAX send_error method exists
     */
    public function test_ajax_send_error_method_exists() {
        $database = new BooChat_Connect_Database();
        $api = new BooChat_Connect_API();
        $ajax = new BooChat_Connect_Ajax($database, $api);
        $this->assertTrue(method_exists($ajax, 'send_error'));
    }
    
    /**
     * Test AJAX send_message method exists
     */
    public function test_ajax_send_message_method_exists() {
        $database = new BooChat_Connect_Database();
        $api = new BooChat_Connect_API();
        $ajax = new BooChat_Connect_Ajax($database, $api);
        $this->assertTrue(method_exists($ajax, 'send_message'));
    }
    
    /**
     * Test AJAX get_statistics method exists
     */
    public function test_ajax_get_statistics_method_exists() {
        $database = new BooChat_Connect_Database();
        $api = new BooChat_Connect_API();
        $ajax = new BooChat_Connect_Ajax($database, $api);
        $this->assertTrue(method_exists($ajax, 'get_statistics'));
    }
}
