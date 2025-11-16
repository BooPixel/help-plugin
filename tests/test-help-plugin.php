<?php
/**
 * Unit tests for Help Plugin
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
     * Test render_admin_page method exists
     */
    public function test_render_admin_page_method_exists() {
        $this->assertTrue(method_exists($this->plugin, 'render_admin_page'));
    }
    
    /**
     * Test enqueue_admin_assets returns early for wrong hook
     */
    public function test_enqueue_admin_assets_returns_early_for_wrong_hook() {
        $result = $this->plugin->enqueue_admin_assets('wrong-hook');
        
        $this->assertNull($result);
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
     * Test render_admin_page outputs HTML
     */
    public function test_render_admin_page_outputs_html() {
        ob_start();
        $this->plugin->render_admin_page();
        $output = ob_get_clean();
        
        $this->assertStringContainsString('<div class="wrap help-plugin-wrap">', $output);
        $this->assertStringContainsString('Help Plugin', $output);
    }
    
    /**
     * Test render_admin_page contains system information
     */
    public function test_render_admin_page_contains_system_info() {
        ob_start();
        $this->plugin->render_admin_page();
        $output = ob_get_clean();
        
        $this->assertStringContainsString('Informações do Sistema', $output);
        $this->assertStringContainsString('Versão do WordPress', $output);
        $this->assertStringContainsString('Versão do PHP', $output);
        $this->assertStringContainsString('Versão do Plugin', $output);
    }
    
    /**
     * Test render_admin_page contains action button
     */
    public function test_render_admin_page_contains_action_button() {
        ob_start();
        $this->plugin->render_admin_page();
        $output = ob_get_clean();
        
        $this->assertStringContainsString('help-plugin-action-btn', $output);
        $this->assertStringContainsString('button button-primary', $output);
    }
}

