<?php
/**
 * Unit tests for BooChat Connect Plugin
 */

use PHPUnit\Framework\TestCase;

class Help_Plugin_Test extends TestCase {
    
    /**
     * @var Help_Plugin
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
                return 'http://example.com/wp-content/plugins/help-plugin/';
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
                return date('Y-m-d H:i:s');
            }
        }
        
        if (!function_exists('sanitize_text_field')) {
            function sanitize_text_field($str) {
                return trim(strip_tags($str));
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
        
        if (!function_exists('wp_redirect')) {
            function wp_redirect($location, $status = 302) {
                return true;
            }
        }
        
        if (!function_exists('add_query_arg')) {
            function add_query_arg($key, $value = null, $url = null) {
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
                if ($echo) echo $result;
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
        if (!class_exists('Help_Plugin')) {
            require_once dirname(__DIR__) . '/help-plugin.php';
        }
        
        // Reset singleton instance
        $reflection = new ReflectionClass('Help_Plugin');
        $instance = $reflection->getProperty('instance');
        $instance->setAccessible(true);
        $instance->setValue(null, null);
        
        $this->plugin = Help_Plugin::get_instance();
    }
    
    /**
     * Tear down test environment
     */
    protected function tearDown(): void {
        parent::tearDown();
        
        // Reset singleton instance
        $reflection = new ReflectionClass('Help_Plugin');
        $instance = $reflection->getProperty('instance');
        $instance->setAccessible(true);
        $instance->setValue(null, null);
    }
    
    /**
     * Test singleton pattern
     */
    public function test_get_instance_returns_same_instance() {
        $instance1 = Help_Plugin::get_instance();
        $instance2 = Help_Plugin::get_instance();
        
        $this->assertSame($instance1, $instance2);
    }
    
    /**
     * Test get_instance returns Help_Plugin instance
     */
    public function test_get_instance_returns_help_plugin_instance() {
        $instance = Help_Plugin::get_instance();
        
        $this->assertInstanceOf('Help_Plugin', $instance);
    }
    
    /**
     * Test constructor is private
     */
    public function test_constructor_is_private() {
        $reflection = new ReflectionClass('Help_Plugin');
        $constructor = $reflection->getConstructor();
        
        $this->assertTrue($constructor->isPrivate());
    }
    
    /**
     * Test constants are defined
     */
    public function test_constants_are_defined() {
        $this->assertTrue(defined('HELP_PLUGIN_VERSION'));
        $this->assertTrue(defined('HELP_PLUGIN_DIR'));
        $this->assertTrue(defined('HELP_PLUGIN_URL'));
    }
    
    /**
     * Test plugin version constant
     */
    public function test_plugin_version_constant() {
        $this->assertEquals('1.0.0', HELP_PLUGIN_VERSION);
    }
    
    /**
     * Test help_plugin_init function exists
     */
    public function test_help_plugin_init_function_exists() {
        $this->assertTrue(function_exists('help_plugin_init'));
    }
    
    /**
     * Test help_plugin_init returns Help_Plugin instance
     */
    public function test_help_plugin_init_returns_help_plugin_instance() {
        $result = help_plugin_init();
        
        $this->assertInstanceOf('Help_Plugin', $result);
    }
    
    /**
     * Test help_plugin_get_version function exists
     */
    public function test_help_plugin_get_version_function_exists() {
        $this->assertTrue(function_exists('help_plugin_get_version'));
    }
    
    /**
     * Test add_admin_menu method exists
     */
    public function test_add_admin_menu_method_exists() {
        $this->assertTrue(method_exists($this->plugin, 'add_admin_menu'));
    }
    
    /**
     * Test enqueue_admin_assets method exists
     */
    public function test_enqueue_admin_assets_method_exists() {
        $this->assertTrue(method_exists($this->plugin, 'enqueue_admin_assets'));
    }
    
    /**
     * Test enqueue_frontend_assets method exists
     */
    public function test_enqueue_frontend_assets_method_exists() {
        $this->assertTrue(method_exists($this->plugin, 'enqueue_frontend_assets'));
    }
    
    /**
     * Test render_admin_page method exists
     */
    public function test_render_admin_page_method_exists() {
        $this->assertTrue(method_exists($this->plugin, 'render_admin_page'));
    }
    
    /**
     * Test render_settings_page method exists
     */
    public function test_render_settings_page_method_exists() {
        $this->assertTrue(method_exists($this->plugin, 'render_settings_page'));
    }
    
    /**
     * Test render_statistics_page method exists
     */
    public function test_render_statistics_page_method_exists() {
        $this->assertTrue(method_exists($this->plugin, 'render_statistics_page'));
    }
    
    /**
     * Test render_chat_widget method exists
     */
    public function test_render_chat_widget_method_exists() {
        $this->assertTrue(method_exists($this->plugin, 'render_chat_widget'));
    }
    
    /**
     * Test ajax_send_message method exists
     */
    public function test_ajax_send_message_method_exists() {
        $this->assertTrue(method_exists($this->plugin, 'ajax_send_message'));
    }
    
    /**
     * Test ajax_get_statistics method exists
     */
    public function test_ajax_get_statistics_method_exists() {
        $this->assertTrue(method_exists($this->plugin, 'ajax_get_statistics'));
    }
    
    /**
     * Test save_customization method exists
     */
    public function test_save_customization_method_exists() {
        $this->assertTrue(method_exists($this->plugin, 'save_customization'));
    }
    
    /**
     * Test save_settings method exists
     */
    public function test_save_settings_method_exists() {
        $this->assertTrue(method_exists($this->plugin, 'save_settings'));
    }
    
    /**
     * Test get_customization_settings method exists
     */
    public function test_get_customization_settings_method_exists() {
        $reflection = new ReflectionClass('Help_Plugin');
        $method = $reflection->getMethod('get_customization_settings');
        $method->setAccessible(true);
        
        $settings = $method->invoke($this->plugin);
        
        $this->assertIsArray($settings);
        $this->assertArrayHasKey('chat_name', $settings);
        $this->assertArrayHasKey('welcome_message', $settings);
        $this->assertArrayHasKey('primary_color', $settings);
        $this->assertArrayHasKey('secondary_color', $settings);
        $this->assertArrayHasKey('chat_bg_color', $settings);
        $this->assertArrayHasKey('text_color', $settings);
        $this->assertArrayHasKey('font_family', $settings);
        $this->assertArrayHasKey('font_size', $settings);
    }
    
    /**
     * Test get_api_url method exists
     */
    public function test_get_api_url_method_exists() {
        $reflection = new ReflectionClass('Help_Plugin');
        $method = $reflection->getMethod('get_api_url');
        $method->setAccessible(true);
        
        $url = $method->invoke($this->plugin);
        
        $this->assertIsString($url);
    }
    
    /**
     * Test generate_session_id method exists
     */
    public function test_generate_session_id_method_exists() {
        $reflection = new ReflectionClass('Help_Plugin');
        $method = $reflection->getMethod('generate_session_id');
        $method->setAccessible(true);
        
        $session_id = $method->invoke($this->plugin);
        
        $this->assertIsString($session_id);
        $this->assertNotEmpty($session_id);
    }
    
    /**
     * Test help_plugin_get_language_from_locale function exists
     */
    public function test_help_plugin_get_language_from_locale_function_exists() {
        $this->assertTrue(function_exists('help_plugin_get_language_from_locale'));
    }
    
    /**
     * Test help_plugin_get_language_from_locale returns correct language codes
     */
    public function test_help_plugin_get_language_from_locale_returns_correct_codes() {
        $this->assertEquals('pt', help_plugin_get_language_from_locale('pt_BR'));
        $this->assertEquals('es', help_plugin_get_language_from_locale('es_ES'));
        $this->assertEquals('en', help_plugin_get_language_from_locale('en_US'));
        $this->assertEquals('en', help_plugin_get_language_from_locale('en_GB'));
    }
    
    /**
     * Test help_plugin_get_language_from_locale defaults to English
     */
    public function test_help_plugin_get_language_from_locale_defaults_to_english() {
        $this->assertEquals('en', help_plugin_get_language_from_locale('fr_FR'));
        $this->assertEquals('en', help_plugin_get_language_from_locale('de_DE'));
    }
    
    /**
     * Test help_plugin_translate function exists
     */
    public function test_help_plugin_translate_function_exists() {
        $this->assertTrue(function_exists('help_plugin_translate'));
    }
    
    /**
     * Test help_plugin_translate returns English translation by default
     */
    public function test_help_plugin_translate_returns_english_by_default() {
        $result = help_plugin_translate('chat_name_default');
        $this->assertEquals('Support', $result);
    }
    
    /**
     * Test activate method exists
     */
    public function test_activate_method_exists() {
        $this->assertTrue(method_exists('Help_Plugin', 'activate'));
    }
    
    /**
     * Test deactivate method exists
     */
    public function test_deactivate_method_exists() {
        $this->assertTrue(method_exists('Help_Plugin', 'deactivate'));
    }
    
    /**
     * Test get_language method exists
     */
    public function test_get_language_method_exists() {
        $reflection = new ReflectionClass('Help_Plugin');
        $this->assertTrue($reflection->hasMethod('get_language'));
    }
    
    /**
     * Test get_effective_language method exists
     */
    public function test_get_effective_language_method_exists() {
        $reflection = new ReflectionClass('Help_Plugin');
        $this->assertTrue($reflection->hasMethod('get_effective_language'));
    }
    
    /**
     * Test log_interaction method exists
     */
    public function test_log_interaction_method_exists() {
        $reflection = new ReflectionClass('Help_Plugin');
        $this->assertTrue($reflection->hasMethod('log_interaction'));
    }
    
    /**
     * Test log_robot_response method exists
     */
    public function test_log_robot_response_method_exists() {
        $reflection = new ReflectionClass('Help_Plugin');
        $this->assertTrue($reflection->hasMethod('log_robot_response'));
    }
    
    /**
     * Test create_interactions_table method exists
     */
    public function test_create_interactions_table_method_exists() {
        $reflection = new ReflectionClass('Help_Plugin');
        $this->assertTrue($reflection->hasMethod('create_interactions_table'));
    }
    
    /**
     * Test get_interactions_count method exists
     */
    public function test_get_interactions_count_method_exists() {
        $reflection = new ReflectionClass('Help_Plugin');
        $this->assertTrue($reflection->hasMethod('get_interactions_count'));
    }
    
    /**
     * Test get_chart_data method exists
     */
    public function test_get_chart_data_method_exists() {
        $reflection = new ReflectionClass('Help_Plugin');
        $this->assertTrue($reflection->hasMethod('get_chart_data'));
    }
    
    /**
     * Test render_admin_page outputs HTML
     */
    public function test_render_admin_page_outputs_html() {
        ob_start();
        $this->plugin->render_admin_page();
        $output = ob_get_clean();
        
        $this->assertStringContainsString('<div class="wrap help-plugin-wrap">', $output);
        $this->assertStringContainsString('BooChat Connect', $output);
    }
    
    /**
     * Test render_admin_page contains customization form
     */
    public function test_render_admin_page_contains_customization_form() {
        ob_start();
        $this->plugin->render_admin_page();
        $output = ob_get_clean();
        
        $this->assertStringContainsString('chat_name', $output);
        $this->assertStringContainsString('welcome_message', $output);
        $this->assertStringContainsString('primary_color', $output);
        $this->assertStringContainsString('form', $output);
    }
    
    /**
     * Test render_settings_page outputs HTML
     */
    public function test_render_settings_page_outputs_html() {
        ob_start();
        $this->plugin->render_settings_page();
        $output = ob_get_clean();
        
        $this->assertStringContainsString('<div class="wrap help-plugin-wrap">', $output);
        $this->assertStringContainsString('form', $output);
    }
    
    /**
     * Test render_settings_page contains API URL field
     */
    public function test_render_settings_page_contains_api_url_field() {
        ob_start();
        $this->plugin->render_settings_page();
        $output = ob_get_clean();
        
        $this->assertStringContainsString('api_url', $output);
        $this->assertStringContainsString('language', $output);
    }
    
    /**
     * Test render_statistics_page outputs HTML
     */
    public function test_render_statistics_page_outputs_html() {
        ob_start();
        $this->plugin->render_statistics_page();
        $output = ob_get_clean();
        
        $this->assertStringContainsString('<div class="wrap help-plugin-wrap">', $output);
        $this->assertStringContainsString('interactions-chart', $output);
    }
    
    /**
     * Test render_statistics_page contains statistics elements
     */
    public function test_render_statistics_page_contains_statistics_elements() {
        ob_start();
        $this->plugin->render_statistics_page();
        $output = ob_get_clean();
        
        $this->assertStringContainsString('stats-1day', $output);
        $this->assertStringContainsString('stats-7days', $output);
        $this->assertStringContainsString('stats-30days', $output);
        $this->assertStringContainsString('interactions-chart', $output);
    }
    
    /**
     * Test render_chat_widget outputs HTML
     */
    public function test_render_chat_widget_outputs_html() {
        ob_start();
        $this->plugin->render_chat_widget();
        $output = ob_get_clean();
        
        $this->assertStringContainsString('help-plugin-popup', $output);
        $this->assertStringContainsString('help-plugin-chat-window', $output);
    }
    
    /**
     * Test render_chat_widget contains chat form
     */
    public function test_render_chat_widget_contains_chat_form() {
        ob_start();
        $this->plugin->render_chat_widget();
        $output = ob_get_clean();
        
        $this->assertStringContainsString('help-plugin-chat-form', $output);
        $this->assertStringContainsString('help-plugin-chat-input', $output);
        $this->assertStringContainsString('help-plugin-chat-send', $output);
    }
}
