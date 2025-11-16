<?php
/**
 * Plugin Name: Help Plugin
 * Plugin URI: https://example.com/help-plugin
 * Description: Plugin WordPress com página no painel de controle
 * Version: 1.0.0
 * Author: Seu Nome
 * Author URI: https://example.com
 * License: Proprietary - Commercial License
 * License URI: https://example.com/license
 * Text Domain: help-plugin
 */

// Prevenir acesso direto
if (!defined('ABSPATH')) {
    exit;
}

// Definir constantes
define('HELP_PLUGIN_VERSION', '1.0.0');
define('HELP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('HELP_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Classe principal do plugin
 */
class Help_Plugin {
    
    /**
     * Instância única do plugin
     */
    private static $instance = null;
    
    /**
     * Obter instância única
     */
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Construtor
     */
    private function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }
    
    /**
     * Adicionar menu no admin
     */
    public function add_admin_menu() {
        add_menu_page(
            __('Help Plugin', 'help-plugin'),           // Título da página
            __('Help Plugin', 'help-plugin'),           // Título do menu
            'manage_options',                            // Capability
            'help-plugin',                               // Slug do menu
            array($this, 'render_admin_page'),          // Callback
            'dashicons-sos',                             // Ícone (opcional)
            30                                           // Posição no menu
        );
        
        // Submenu principal (opcional - mostra a mesma página)
        add_submenu_page(
            'help-plugin',
            __('Painel Principal', 'help-plugin'),
            __('Painel Principal', 'help-plugin'),
            'manage_options',
            'help-plugin',
            array($this, 'render_admin_page')
        );
    }
    
    /**
     * Carregar assets do admin
     */
    public function enqueue_admin_assets($hook) {
        // Carregar apenas na página do plugin
        if ($hook !== 'toplevel_page_help-plugin') {
            return;
        }
        
        wp_enqueue_style(
            'help-plugin-admin-style',
            HELP_PLUGIN_URL . 'assets/css/admin-style.css',
            array(),
            HELP_PLUGIN_VERSION
        );
        
        wp_enqueue_script(
            'help-plugin-admin-script',
            HELP_PLUGIN_URL . 'assets/js/admin-script.js',
            array('jquery'),
            HELP_PLUGIN_VERSION,
            true
        );
    }
    
    /**
     * Renderizar página do admin
     */
    public function render_admin_page() {
        ?>
        <div class="wrap help-plugin-wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <div class="help-plugin-content">
                <div class="help-plugin-card">
                    <h2><?php esc_html_e('Bem-vindo ao Help Plugin', 'help-plugin'); ?></h2>
                    <p><?php esc_html_e('Esta é a página principal do plugin no painel de controle do WordPress.', 'help-plugin'); ?></p>
                </div>
                
                <div class="help-plugin-card">
                    <h3><?php esc_html_e('Informações do Sistema', 'help-plugin'); ?></h3>
                    <table class="widefat">
                        <tbody>
                            <tr>
                                <td><strong><?php esc_html_e('Versão do WordPress', 'help-plugin'); ?>:</strong></td>
                                <td><?php echo esc_html(get_bloginfo('version')); ?></td>
                            </tr>
                            <tr>
                                <td><strong><?php esc_html_e('Versão do PHP', 'help-plugin'); ?>:</strong></td>
                                <td><?php echo esc_html(PHP_VERSION); ?></td>
                            </tr>
                            <tr>
                                <td><strong><?php esc_html_e('Versão do Plugin', 'help-plugin'); ?>:</strong></td>
                                <td><?php echo esc_html(HELP_PLUGIN_VERSION); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="help-plugin-card">
                    <h3><?php esc_html_e('Ações', 'help-plugin'); ?></h3>
                    <p>
                        <button type="button" class="button button-primary" id="help-plugin-action-btn">
                            <?php esc_html_e('Clique Aqui', 'help-plugin'); ?>
                        </button>
                    </p>
                    <p id="help-plugin-message" style="display:none; padding: 10px; background: #f0f0f0; border-left: 4px solid #2271b1;">
                        <?php esc_html_e('Ação executada com sucesso!', 'help-plugin'); ?>
                    </p>
                </div>
            </div>
        </div>
        <?php
    }
}

/**
 * Inicializar o plugin
 */
function help_plugin_init() {
    return Help_Plugin::get_instance();
}

// Iniciar o plugin
help_plugin_init();

