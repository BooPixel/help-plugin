<?php
/**
 * Unit tests for BooPixel AI Chat Connect for n8n Plugin
 */

use PHPUnit\Framework\TestCase;

if (!class_exists('WP_Error')) {
    class BooPixel_AI_Chat_For_N8n_Test_WP_Error {
        private $code;
        private $message;
        
        public function __construct($code = '', $message = '') {
            $this->code = $code;
            $this->message = $message;
        }
        
        public function get_error_code() {
            return $this->code;
        }
        
        public function get_error_message() {
            return $this->message;
        }
    }
}

class BooPixel_AI_Chat_For_N8n_Test extends TestCase {
    
    /**
     * Set up test environment
     */
    protected function setUp(): void {
        parent::setUp();
        
        // Mock WordPress functions
        $this->mockWordPressFunctions();
        
        // Load plugin file
        if (!class_exists('BooPixel_AI_Chat_For_N8n')) {
            require_once dirname(__DIR__) . '/boopixel-ai-chat-for-n8n.php';
        }
        
        // Reset singleton instance
        $reflection = new ReflectionClass('BooPixel_AI_Chat_For_N8n');
        $instance = $reflection->getProperty('instance');
        $instance->setAccessible(true);
        $instance->setValue(null, null);
    }
    
    /**
     * Tear down test environment
     */
    protected function tearDown(): void {
        parent::tearDown();
        
        // Reset singleton instance
        $reflection = new ReflectionClass('BooPixel_AI_Chat_For_N8n');
        $instance = $reflection->getProperty('instance');
        $instance->setAccessible(true);
        $instance->setValue(null, null);
    }
    
    /**
     * Mock WordPress functions
     */
    private function mockWordPressFunctions() {
        if (!function_exists('add_action')) {
            function add_action($hook, $callback, $priority = 10, $accepted_args = 1) { return true; }
        }
        if (!function_exists('add_menu_page')) {
            function add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function, $icon_url = '', $position = null) { return true; }
        }
        if (!function_exists('add_submenu_page')) {
            function add_submenu_page($parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function) { return true; }
        }
        if (!function_exists('wp_enqueue_style')) {
            function wp_enqueue_style($handle, $src = '', $deps = array(), $ver = false, $media = 'all') { return true; }
        }
        if (!function_exists('wp_enqueue_script')) {
            function wp_enqueue_script($handle, $src = '', $deps = array(), $ver = false, $in_footer = false) { return true; }
        }
        if (!function_exists('wp_localize_script')) {
            function wp_localize_script($handle, $object_name, $l10n) { return true; }
        }
        if (!function_exists('admin_url')) {
            function admin_url($path = '') { return 'http://example.com/wp-admin/' . $path; }
        }
        if (!function_exists('plugin_dir_path')) {
            function plugin_dir_path($file) { return dirname($file) . '/'; }
        }
        if (!function_exists('plugin_dir_url')) {
            function plugin_dir_url($file) { return 'http://example.com/wp-content/plugins/boochat-connect/'; }
        }
        if (!function_exists('current_time')) {
            function current_time($type, $gmt = 0) { return gmdate('Y-m-d H:i:s'); }
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
            function sanitize_text_field($str) { return trim(wp_strip_all_tags($str)); }
        }
        if (!function_exists('sanitize_textarea_field')) {
            function sanitize_textarea_field($str) { return trim($str); }
        }
        if (!function_exists('sanitize_hex_color')) {
            function sanitize_hex_color($color) { return preg_match('/^#[a-f0-9]{6}$/i', $color) ? $color : '#000000'; }
        }
        if (!function_exists('esc_attr')) {
            function esc_attr($text) { return htmlspecialchars($text, ENT_QUOTES, 'UTF-8'); }
        }
        if (!function_exists('esc_html')) {
            function esc_html($text) { return htmlspecialchars($text, ENT_QUOTES, 'UTF-8'); }
        }
        if (!function_exists('esc_textarea')) {
            function esc_textarea($text) { return htmlspecialchars($text, ENT_QUOTES, 'UTF-8'); }
        }
        if (!function_exists('esc_url_raw')) {
            function esc_url_raw($url) { return filter_var($url, FILTER_SANITIZE_URL); }
        }
        if (!function_exists('wp_create_nonce')) {
            function wp_create_nonce($action = -1) { return 'test-nonce-' . $action; }
        }
        if (!function_exists('wp_verify_nonce')) {
            function wp_verify_nonce($nonce, $action = -1) { return $nonce === 'test-nonce-' . $action; }
        }
        if (!function_exists('wp_nonce_field')) {
            function wp_nonce_field($action = -1, $name = '_wpnonce', $referer = true, $echo = true) {
                return '<input type="hidden" name="' . $name . '" value="test-nonce-' . $action . '" />';
            }
        }
        if (!function_exists('current_user_can')) {
            function current_user_can($capability) { return true; }
        }
        if (!function_exists('is_admin')) {
            function is_admin() { return false; }
        }
        if (!function_exists('get_admin_page_title')) {
            function get_admin_page_title() { return 'BooPixel AI Chat Connect for n8n'; }
        }
        if (!function_exists('submit_button')) {
            function submit_button($text = null, $type = 'primary', $name = 'submit', $wrap = true, $other_attributes = null) {
                return '<button type="submit" class="button button-' . $type . '">' . ($text ?: 'Save') . '</button>';
            }
        }
        if (!function_exists('get_locale')) {
            function get_locale() { return 'en_US'; }
        }
        if (!function_exists('check_ajax_referer')) {
            function check_ajax_referer($action = -1, $query_arg = false, $die = true) { return true; }
        }
        if (!function_exists('wp_send_json_success')) {
            function wp_send_json_success($data = null) { return array('success' => true, 'data' => $data); }
        }
        if (!function_exists('wp_send_json_error')) {
            function wp_send_json_error($data = null) { return array('success' => false, 'data' => $data); }
        }
        if (!function_exists('wp_die')) {
            function wp_die($message = '', $title = '', $args = array()) { return; }
        }
        if (!function_exists('wp_safe_redirect')) {
            function wp_safe_redirect($location, $status = 302) { return true; }
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
            function flush_rewrite_rules($hard = true) { return true; }
        }
        if (!function_exists('dbDelta')) {
            function dbDelta($queries, $execute = true) { return array(); }
        }
        if (!function_exists('delete_option')) {
            function delete_option($option) { return true; }
        }
        if (!function_exists('wp_unslash')) {
            function wp_unslash($value) { return stripslashes($value); }
        }
        if (!function_exists('esc_sql')) {
            function esc_sql($data) { return addslashes($data); }
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
        if (!function_exists('update_option')) {
            function update_option($option, $value) { return true; }
        }
        
        // Specific mocks
        if (!function_exists('get_option')) {
            function get_option($option, $default = false) {
                global $mock_options;
                if (isset($mock_options) && isset($mock_options[$option])) {
                    return $mock_options[$option];
                }
                return $default;
            }
        }
        
        if (!function_exists('wp_remote_post')) {
            function wp_remote_post($url, $args = array()) {
                global $wp_remote_post_override;
                if (isset($wp_remote_post_override)) {
                    return $wp_remote_post_override;
                }
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
        
        if (!function_exists('wp_remote_retrieve_headers')) {
            function wp_remote_retrieve_headers($response) {
                return new class {
                    public function getAll() {
                        return array('Content-Type' => 'application/json');
                    }
                };
            }
        }
        
        if (!function_exists('is_wp_error')) {
            function is_wp_error($thing) {
                return $thing instanceof BooPixel_AI_Chat_For_N8n_Test_WP_Error;
            }
        }
        
        if (!function_exists('wp_json_encode')) {
            function wp_json_encode($data, $options = 0) {
                return json_encode($data, $options);
            }
        }
        
        if (!function_exists('home_url')) {
            function home_url($path = '') {
                return 'http://example.com/' . ltrim($path, '/');
            }
        }
        
        if (!function_exists('get_transient')) {
            function get_transient($transient) {
                return false;
            }
        }
        
        if (!function_exists('set_transient')) {
            function set_transient($transient, $value, $expiration) {
                return true;
            }
        }
        
        if (!function_exists('delete_transient')) {
            function delete_transient($transient) {
                return true;
            }
        }
        
        // Mock wpdb
        global $wpdb;
        if (!isset($wpdb)) {
            $wpdb = new class {
                public $prefix = 'wp_';
                public function insert($table, $data, $format = null) { return true; }
                public function get_var($query = null) { return 0; }
                public function get_results($query = null) { return array(); }
                public function prepare($query, ...$args) { return $query; }
                public function query($query) { return true; }
                public function get_charset_collate() { return 'DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'; }
            };
        }
        
        // Define constants
        if (!defined('ABSPATH')) define('ABSPATH', '/');
        if (!defined('DB_NAME')) define('DB_NAME', 'test_db');
        if (!defined('HOUR_IN_SECONDS')) define('HOUR_IN_SECONDS', 3600);
        if (!defined('YEAR_IN_SECONDS')) define('YEAR_IN_SECONDS', 31536000);
        if (!defined('BOOCHAT_CONNECT_VERSION')) define('BOOCHAT_CONNECT_VERSION', '1.0.0');
    }
    
    /**
     * Test plugin initialization
     */
    public function test_plugin_initialization() {
        $instance1 = BooPixel_AI_Chat_For_N8n::get_instance();
        $instance2 = BooPixel_AI_Chat_For_N8n::get_instance();
        
        $this->assertSame($instance1, $instance2);
        $this->assertInstanceOf('BooPixel_AI_Chat_For_N8n', $instance1);
        $this->assertTrue(defined('BOOCHAT_CONNECT_VERSION'));
    }
    
    /**
     * Test helper functions
     */
    public function test_helper_functions() {
        $this->assertTrue(function_exists('boochat_connect_get_version'));
        $this->assertTrue(function_exists('boochat_connect_get_language_from_locale'));
        $this->assertTrue(function_exists('boochat_connect_translate'));
        $this->assertTrue(function_exists('boochat_connect_log_api_request'));
        
        $this->assertEquals('pt', boochat_connect_get_language_from_locale('pt_BR'));
        $this->assertEquals('es', boochat_connect_get_language_from_locale('es_ES'));
        $this->assertEquals('en', boochat_connect_get_language_from_locale('en_US'));
        $this->assertEquals('en', boochat_connect_get_language_from_locale('fr_FR'));
        
        $this->assertEquals('Support', boochat_connect_translate('chat_name_default'));
        $this->assertEquals('Default Value', boochat_connect_translate('non_existent_key', 'Default Value'));
    }
    
    /**
     * Test API class
     */
    public function test_api_class() {
        $api = new BooPixel_AI_Chat_For_N8n_API();
        
        $this->assertIsString($api->get_api_url());
        
        $session_id = $api->generate_session_id();
        $this->assertIsString($session_id);
        $this->assertEquals(32, strlen($session_id));
        
        // Test error when URL is empty
        global $mock_options;
        $mock_options = array('boochat_connect_api_url' => '');
        $result = $api->send_message('test_session', 'test message');
        $this->assertInstanceOf('BooPixel_AI_Chat_For_N8n_Test_WP_Error', $result);
        $this->assertEquals('no_api_url', $result->get_error_code());
        unset($mock_options);
        
        // Test success
        $mock_options = array('boochat_connect_api_url' => 'https://api.example.com/webhook');
        $result = $api->send_message('test_session', 'test message');
        $this->assertIsArray($result);
        $this->assertArrayHasKey('body', $result);
        $this->assertArrayHasKey('response', $result);
        unset($mock_options);
        
        // Test HTTP error
        $mock_options = array('boochat_connect_api_url' => 'https://api.example.com/webhook');
        global $wp_remote_post_override;
        $wp_remote_post_override = array(
            'body' => 'Error message',
            'response' => array('code' => 500)
        );
        $result = $api->send_message('test_session', 'test message');
        $this->assertInstanceOf('BooPixel_AI_Chat_For_N8n_Test_WP_Error', $result);
        $this->assertEquals('http_error', $result->get_error_code());
        unset($mock_options, $wp_remote_post_override);
    }
    
    /**
     * Test License class
     */
    public function test_license_class() {
        $license = new BooPixel_AI_Chat_For_N8n_License();
        
        $this->assertIsString($license->get_license_key());
        $this->assertIsString($license->get_license_status());
        
        // Test activate with empty key
        $result = $license->activate_license('');
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertNotEmpty($result['message']);
        
        // Test verify_payment_and_activate with empty session
        $result = $license->verify_payment_and_activate('');
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertNotEmpty($result['message']);
    }
    
    /**
     * Test Database class
     */
    public function test_database_class() {
        $database = new BooPixel_AI_Chat_For_N8n_Database();
        
        $count = $database->get_interactions_count(1);
        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(0, $count);
        
        $chart_data = $database->get_chart_data('2025-01-01', '2025-01-31');
        $this->assertIsArray($chart_data);
        $this->assertArrayHasKey('labels', $chart_data);
        $this->assertArrayHasKey('data', $chart_data);
        
        $calendar_data = $database->get_calendar_data();
        $this->assertIsArray($calendar_data);
    }
    
    /**
     * Test Settings class
     */
    public function test_settings_class() {
        $settings = new BooPixel_AI_Chat_For_N8n_Settings();
        
        $customization = $settings->get_customization_settings();
        $this->assertIsArray($customization);
        $this->assertArrayHasKey('chat_name', $customization);
        $this->assertArrayHasKey('welcome_message', $customization);
        $this->assertArrayHasKey('primary_color', $customization);
    }
    
    /**
     * Test Statistics class
     */
    public function test_statistics_class() {
        $database = new BooPixel_AI_Chat_For_N8n_Database();
        $statistics = new BooPixel_AI_Chat_For_N8n_Statistics($database);
        
        ob_start();
        $statistics->render_page();
        $output = ob_get_clean();
        
        $this->assertStringContainsString('<div class="wrap">', $output);
        $this->assertStringContainsString('interactions-chart', $output);
    }
    
    /**
     * Test logging function
     */
    public function test_logging_function() {
        $url = 'https://api.example.com/test';
        $endpoint = 'test_endpoint';
        $request_body = array('key' => 'value');
        $headers = array('X-API-Key' => 'secret12345678901234567890');
        $response = array(
            'body' => json_encode(array('success' => true)),
            'response' => array('code' => 200)
        );
        
        // Should execute without errors
        boochat_connect_log_api_request($endpoint, $url);
        boochat_connect_log_api_request($endpoint, $url, $request_body);
        boochat_connect_log_api_request($endpoint, $url, array(), $headers);
        boochat_connect_log_api_request($endpoint, $url, array(), array(), new BooPixel_AI_Chat_For_N8n_Test_WP_Error('test', 'error'));
        boochat_connect_log_api_request($endpoint, $url, array(), array(), $response);
        
        $this->assertTrue(true);
    }
    
    /**
     * Test essential files exist
     */
    public function test_essential_files_exist() {
        $this->assertFileExists(BOOCHAT_CONNECT_DIR . 'includes/views/admin-main.php');
        $this->assertFileExists(BOOCHAT_CONNECT_DIR . 'includes/views/frontend-chat.php');
        $this->assertFileExists(BOOCHAT_CONNECT_DIR . 'assets/css/chat-style.css');
    }
}
