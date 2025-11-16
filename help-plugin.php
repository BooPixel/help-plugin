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
define('HELP_PLUGIN_API_URL', 'https://boopixel.app.n8n.cloud/webhook/974d9c0b-31f5-4f10-b8f7-ed2cbb9cde26/chat');

// Versão dinâmica baseada em timestamp para quebrar cache
function help_plugin_get_version() {
    $version = get_option('help_plugin_cache_version');
    if (empty($version)) {
        $version = time();
        update_option('help_plugin_cache_version', $version);
    }
    return $version;
}

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
        
        // Frontend hooks
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_action('wp_footer', array($this, 'render_chat_widget'));
        
        // AJAX hooks
        add_action('wp_ajax_help_plugin_send_message', array($this, 'ajax_send_message'));
        add_action('wp_ajax_nopriv_help_plugin_send_message', array($this, 'ajax_send_message'));
        add_action('wp_ajax_help_plugin_get_statistics', array($this, 'ajax_get_statistics'));
        
        // Processar formulário de configurações
        add_action('admin_post_help_plugin_save_settings', array($this, 'save_settings'));
        add_action('admin_post_help_plugin_save_customization', array($this, 'save_customization'));
        
        // Adicionar CSS customizado no frontend
        add_action('wp_head', array($this, 'output_custom_css'));
    }
    
    /**
     * Obter configurações de customização
     */
    private function get_customization_settings() {
        return array(
            'chat_name' => get_option('help_plugin_chat_name', 'Atendimento'),
            'welcome_message' => get_option('help_plugin_welcome_message', 'Olá! Como podemos ajudar você hoje?'),
            'primary_color' => get_option('help_plugin_primary_color', '#667eea'),
            'secondary_color' => get_option('help_plugin_secondary_color', '#764ba2'),
            'chat_bg_color' => get_option('help_plugin_chat_bg_color', '#ffffff'),
            'text_color' => get_option('help_plugin_text_color', '#333333'),
            'font_family' => get_option('help_plugin_font_family', 'Arial, sans-serif'),
            'font_size' => get_option('help_plugin_font_size', '14px'),
        );
    }
    
    /**
     * Salvar customizações
     */
    public function save_customization() {
        // Verificar permissões
        if (!current_user_can('manage_options')) {
            wp_die(__('Você não tem permissão para acessar esta página.', 'help-plugin'));
        }
        
        // Verificar nonce
        if (!isset($_POST['help_plugin_customization_nonce']) || !wp_verify_nonce($_POST['help_plugin_customization_nonce'], 'help_plugin_save_customization')) {
            wp_die(__('Erro de segurança. Tente novamente.', 'help-plugin'));
        }
        
        // Salvar configurações de customização
        update_option('help_plugin_chat_name', sanitize_text_field($_POST['chat_name'] ?? 'Atendimento'));
        update_option('help_plugin_welcome_message', sanitize_textarea_field($_POST['welcome_message'] ?? 'Olá! Como podemos ajudar você hoje?'));
        update_option('help_plugin_primary_color', sanitize_hex_color($_POST['primary_color'] ?? '#667eea'));
        update_option('help_plugin_secondary_color', sanitize_hex_color($_POST['secondary_color'] ?? '#764ba2'));
        update_option('help_plugin_chat_bg_color', sanitize_hex_color($_POST['chat_bg_color'] ?? '#ffffff'));
        update_option('help_plugin_text_color', sanitize_hex_color($_POST['text_color'] ?? '#333333'));
        update_option('help_plugin_font_family', sanitize_text_field($_POST['font_family'] ?? 'Arial, sans-serif'));
        update_option('help_plugin_font_size', sanitize_text_field($_POST['font_size'] ?? '14px'));
        
        // Atualizar versão do cache para forçar reload dos assets
        $new_version = time();
        update_option('help_plugin_cache_version', $new_version);
        
        // Limpar cache de plugins populares
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }
        
        // Limpar cache do W3 Total Cache
        if (function_exists('w3tc_flush_all')) {
            w3tc_flush_all();
        }
        
        // Limpar cache do WP Super Cache
        if (function_exists('wp_cache_clear_cache')) {
            wp_cache_clear_cache();
        }
        
        // Limpar cache do WP Rocket
        if (function_exists('rocket_clean_domain')) {
            rocket_clean_domain();
        }
        
        // Redirecionar com mensagem de sucesso
        wp_redirect(add_query_arg(array(
            'page' => 'help-plugin',
            'customization-updated' => 'true'
        ), admin_url('admin.php')));
        exit;
    }
    
    /**
     * Output CSS customizado no frontend
     */
    public function output_custom_css() {
        if (is_admin()) {
            return;
        }
        
        $settings = $this->get_customization_settings();
        $cache_version = help_plugin_get_version();
        ?>
        <style id="help-plugin-custom-css" data-version="<?php echo esc_attr($cache_version); ?>">
            .help-plugin-popup-content {
                background: linear-gradient(135deg, <?php echo esc_attr($settings['primary_color']); ?> 0%, <?php echo esc_attr($settings['secondary_color']); ?> 100%) !important;
            }
            
            .help-plugin-chat-header {
                background: linear-gradient(135deg, <?php echo esc_attr($settings['primary_color']); ?> 0%, <?php echo esc_attr($settings['secondary_color']); ?> 100%) !important;
            }
            
            .help-plugin-chat-window {
                background: <?php echo esc_attr($settings['chat_bg_color']); ?> !important;
                font-family: <?php echo esc_attr($settings['font_family']); ?> !important;
                font-size: <?php echo esc_attr($settings['font_size']); ?> !important;
            }
            
            .help-plugin-chat-body {
                background: <?php echo esc_attr($settings['chat_bg_color']); ?> !important;
            }
            
            .help-plugin-chat-message p {
                font-family: <?php echo esc_attr($settings['font_family']); ?> !important;
                font-size: <?php echo esc_attr($settings['font_size']); ?> !important;
            }
            
            .help-plugin-chat-message-admin p {
                color: #333 !important;
            }
            
            .help-plugin-chat-message-system {
                background: #e9ecef !important;
                color: #333 !important;
            }
            
            .help-plugin-chat-message-system p {
                color: #333 !important;
            }
            
            .help-plugin-chat-send {
                background: linear-gradient(135deg, <?php echo esc_attr($settings['primary_color']); ?> 0%, <?php echo esc_attr($settings['secondary_color']); ?> 100%) !important;
            }
            
            .help-plugin-chat-input {
                font-family: <?php echo esc_attr($settings['font_family']); ?> !important;
                font-size: <?php echo esc_attr($settings['font_size']); ?> !important;
            }
            
            .help-plugin-chat-message-user {
                color: #e9ecef !important;
            }
            
            .help-plugin-chat-message-user p {
                color: #e9ecef !important;
            }
        </style>
        <?php
    }
    
    /**
     * Obter URL da API do banco de dados
     */
    private function get_api_url() {
        $api_url = get_option('help_plugin_api_url', '');
        // Se não houver URL salva, usar a constante padrão
        if (empty($api_url)) {
            return defined('HELP_PLUGIN_API_URL') ? HELP_PLUGIN_API_URL : '';
        }
        return $api_url;
    }
    
    /**
     * Salvar configurações
     */
    public function save_settings() {
        // Verificar permissões
        if (!current_user_can('manage_options')) {
            wp_die(__('Você não tem permissão para acessar esta página.', 'help-plugin'));
        }
        
        // Verificar nonce
        if (!isset($_POST['help_plugin_settings_nonce']) || !wp_verify_nonce($_POST['help_plugin_settings_nonce'], 'help_plugin_save_settings')) {
            wp_die(__('Erro de segurança. Tente novamente.', 'help-plugin'));
        }
        
        // Salvar URL da API
        $api_url = isset($_POST['api_url']) ? esc_url_raw($_POST['api_url']) : '';
        update_option('help_plugin_api_url', $api_url);
        
        // Redirecionar com mensagem de sucesso
        wp_redirect(add_query_arg(array(
            'page' => 'help-plugin-settings',
            'settings-updated' => 'true'
        ), admin_url('admin.php')));
        exit;
    }
    
    /**
     * Renderizar página de configurações
     */
    public function render_settings_page() {
        // Processar mensagem de sucesso
        $settings_updated = isset($_GET['settings-updated']) && $_GET['settings-updated'] === 'true';
        
        $api_url = $this->get_api_url();
        ?>
        <div class="wrap help-plugin-wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <?php if ($settings_updated): ?>
                <div class="notice notice-success is-dismissible">
                    <p><?php esc_html_e('Configurações salvas com sucesso!', 'help-plugin'); ?></p>
                </div>
            <?php endif; ?>
            
            <div class="help-plugin-content">
                <div class="help-plugin-card">
                    <h2><?php esc_html_e('Configurações da API', 'help-plugin'); ?></h2>
                    <p><?php esc_html_e('Configure a URL da API de atendimento ao cliente.', 'help-plugin'); ?></p>
                    
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                        <?php wp_nonce_field('help_plugin_save_settings', 'help_plugin_settings_nonce'); ?>
                        <input type="hidden" name="action" value="help_plugin_save_settings">
                        
                        <table class="form-table">
                            <tbody>
                                <tr>
                                    <th scope="row">
                                        <label for="api_url"><?php esc_html_e('URL da API', 'help-plugin'); ?></label>
                                    </th>
                                    <td>
                                        <input 
                                            type="url" 
                                            id="api_url" 
                                            name="api_url" 
                                            value="<?php echo esc_attr($api_url); ?>" 
                                            class="regular-text"
                                            placeholder="https://exemplo.com/webhook/chat"
                                            required
                                        >
                                        <p class="description">
                                            <?php esc_html_e('URL completa do webhook da API de atendimento.', 'help-plugin'); ?>
                                        </p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <?php submit_button(__('Salvar Configurações', 'help-plugin')); ?>
                    </form>
                </div>
                
                <div class="help-plugin-card">
                    <h3><?php esc_html_e('Informações', 'help-plugin'); ?></h3>
                    <p><?php esc_html_e('A URL da API será usada para processar todas as mensagens do chat de atendimento.', 'help-plugin'); ?></p>
                    <p><strong><?php esc_html_e('Status:', 'help-plugin'); ?></strong> 
                        <?php if (!empty($api_url)): ?>
                            <span style="color: green;"><?php esc_html_e('Configurada', 'help-plugin'); ?></span>
                        <?php else: ?>
                            <span style="color: red;"><?php esc_html_e('Não configurada', 'help-plugin'); ?></span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
        <?php
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
        
        // Submenu de Configurações
        add_submenu_page(
            'help-plugin',
            __('Configurações', 'help-plugin'),
            __('Configurações', 'help-plugin'),
            'manage_options',
            'help-plugin-settings',
            array($this, 'render_settings_page')
        );
        
        // Submenu de Estatísticas
        add_submenu_page(
            'help-plugin',
            __('Estatísticas', 'help-plugin'),
            __('Estatísticas', 'help-plugin'),
            'manage_options',
            'help-plugin-statistics',
            array($this, 'render_statistics_page')
        );
    }
    
    /**
     * Carregar assets do admin
     */
    public function enqueue_admin_assets($hook) {
        // Carregar nas páginas do plugin
        if ($hook !== 'toplevel_page_help-plugin' && $hook !== 'help-plugin_page_help-plugin-settings' && $hook !== 'help-plugin_page_help-plugin-statistics') {
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
        
        // Carregar Chart.js e scripts de estatísticas apenas na página de estatísticas
        if ($hook === 'help-plugin_page_help-plugin-statistics') {
            wp_enqueue_script(
                'chart-js',
                'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js',
                array(),
                '4.4.0',
                true
            );
            
            wp_enqueue_script(
                'help-plugin-statistics-script',
                HELP_PLUGIN_URL . 'assets/js/statistics-script.js',
                array('jquery', 'chart-js'),
                HELP_PLUGIN_VERSION,
                true
            );
            
            wp_localize_script('help-plugin-statistics-script', 'helpPluginStats', array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('help-plugin-statistics'),
            ));
        }
    }
    
    /**
     * Carregar assets do frontend
     */
    public function enqueue_frontend_assets() {
        // Só carregar no frontend, não no admin
        if (is_admin()) {
            return;
        }
        
        $version = help_plugin_get_version();
        
        wp_enqueue_style(
            'help-plugin-chat-style',
            HELP_PLUGIN_URL . 'assets/css/chat-style.css',
            array(),
            $version
        );
        
        wp_enqueue_script(
            'help-plugin-chat-script',
            HELP_PLUGIN_URL . 'assets/js/chat-script.js',
            array('jquery'),
            $version,
            true
        );
        
        // Localizar script com dados AJAX - IMPORTANTE: deve ser chamado DEPOIS do wp_enqueue_script
        wp_localize_script('help-plugin-chat-script', 'helpPluginAjax', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('help-plugin-chat'),
            'loadingText' => __('Aguardando resposta...', 'help-plugin'),
            'errorText' => __('Erro ao enviar mensagem. Tente novamente.', 'help-plugin')
        ));
    }
    
    /**
     * Renderizar widget de chat no footer
     */
    public function render_chat_widget() {
        // Só renderizar no frontend, não no admin
        if (is_admin()) {
            return;
        }
        ?>
        <div id="help-plugin-popup" class="help-plugin-popup">
            <div class="help-plugin-popup-content">
                <div class="help-plugin-popup-header">
                    <span class="help-plugin-popup-icon">💬</span>
                    <span class="help-plugin-popup-text"><?php esc_html_e('Precisa de ajuda?', 'help-plugin'); ?></span>
                </div>
                <button class="help-plugin-popup-close" aria-label="<?php esc_attr_e('Fechar', 'help-plugin'); ?>">&times;</button>
            </div>
        </div>

        <div id="help-plugin-chat-window" class="help-plugin-chat-window">
            <div class="help-plugin-chat-header">
                <div class="help-plugin-chat-title">
                    <span class="help-plugin-chat-icon">💬</span>
                    <span><?php echo esc_html($this->get_customization_settings()['chat_name']); ?></span>
                </div>
                <button class="help-plugin-chat-close" aria-label="<?php esc_attr_e('Fechar chat', 'help-plugin'); ?>">&times;</button>
            </div>
            <div class="help-plugin-chat-body">
                <div class="help-plugin-chat-messages" id="help-plugin-chat-messages">
                    <div class="help-plugin-chat-message help-plugin-chat-message-system">
                        <p><?php echo esc_html($this->get_customization_settings()['welcome_message']); ?></p>
                    </div>
                </div>
            </div>
            <div class="help-plugin-chat-footer">
                <form id="help-plugin-chat-form" class="help-plugin-chat-form">
                    <input 
                        type="text" 
                        id="help-plugin-chat-input" 
                        class="help-plugin-chat-input" 
                        placeholder="<?php esc_attr_e('Digite sua mensagem...', 'help-plugin'); ?>"
                        autocomplete="off"
                    >
                    <button type="submit" class="help-plugin-chat-send">
                        <span><?php esc_html_e('Enviar', 'help-plugin'); ?></span>
                    </button>
                </form>
            </div>
        </div>
        <?php
    }
    
    /**
     * AJAX handler para enviar mensagem
     */
    public function ajax_send_message() {
        // Verificar nonce para segurança
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'help-plugin-chat')) {
            wp_send_json_error(array('message' => 'Erro de segurança. Recarregue a página.'));
            return;
        }
        
        $session_id = isset($_POST['sessionId']) ? sanitize_text_field($_POST['sessionId']) : '';
        $chat_input = isset($_POST['chatInput']) ? sanitize_text_field($_POST['chatInput']) : '';
        
        if (empty($chat_input)) {
            wp_send_json_error(array('message' => 'Mensagem vazia'));
            return;
        }
        
        // Se não tem sessionId, gerar um novo
        if (empty($session_id)) {
            $session_id = $this->generate_session_id();
        }
        
        // Registrar interação para estatísticas (com conteúdo da mensagem)
        $this->log_interaction($session_id, $chat_input);
        
        // Fazer requisição à API externa
        $response = $this->send_to_api($session_id, $chat_input);
        
        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            wp_send_json_error(array(
                'message' => 'Erro ao conectar com o serviço de atendimento. Tente novamente.',
                'sessionId' => $session_id
            ));
            return;
        }
        
        // Extrair body da resposta
        $body = isset($response['body']) ? $response['body'] : '';
        $response_code = isset($response['response']['code']) ? $response['response']['code'] : 0;
        
        if ($response_code !== 200) {
            wp_send_json_error(array(
                'message' => 'Erro ao conectar com o serviço de atendimento. Tente novamente.',
                'sessionId' => $session_id
            ));
            return;
        }
        
        $data = json_decode($body, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            wp_send_json_error(array(
                'message' => 'Erro ao processar resposta do servidor.',
                'sessionId' => $session_id
            ));
            return;
        }
        
        if (!isset($data['output'])) {
            wp_send_json_error(array(
                'message' => 'Erro ao processar resposta do servidor.',
                'sessionId' => $session_id
            ));
            return;
        }
        
        wp_send_json_success(array(
            'message' => $data['output'],
            'sessionId' => $session_id
        ));
    }
    
    /**
     * Gerar session ID único
     */
    private function generate_session_id() {
        return bin2hex(random_bytes(16));
    }
    
    /**
     * Enviar mensagem para a API externa
     */
    private function send_to_api($session_id, $chat_input) {
        $url = $this->get_api_url();
        
        // Verificar se a URL está configurada
        if (empty($url)) {
            return new WP_Error('no_api_url', 'URL da API não configurada. Configure em Help Plugin > Configurações.');
        }
        
        $post_data = json_encode(array(
            'sessionId' => $session_id,
            'action' => 'sendMessage',
            'chatInput' => $chat_input
        ));
        
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $post_data,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));
        
        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($curl);
        
        curl_close($curl);
        
        // Se houver erro no cURL
        if ($response === false || !empty($curl_error)) {
            return new WP_Error('curl_error', $curl_error ? $curl_error : 'Erro na requisição cURL');
        }
        
        // Se o código HTTP não for 200
        if ($http_code !== 200) {
            return new WP_Error('http_error', 'HTTP ' . $http_code . ': ' . $response);
        }
        
        // Retornar resposta como array compatível com wp_remote_retrieve_body
        return array(
            'body' => $response,
            'response' => array(
                'code' => $http_code
            )
        );
    }
    
    /**
     * Renderizar página do admin
     */
    public function render_admin_page() {
        // Processar mensagem de sucesso
        $customization_updated = isset($_GET['customization-updated']) && $_GET['customization-updated'] === 'true';
        
        $settings = $this->get_customization_settings();
        ?>
        <div class="wrap help-plugin-wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <?php if ($customization_updated): ?>
                <div class="notice notice-success is-dismissible">
                    <p><?php esc_html_e('Customizações salvas com sucesso!', 'help-plugin'); ?></p>
                </div>
            <?php endif; ?>
            
            <div class="help-plugin-content">
                <div class="help-plugin-card">
                    <h2><?php esc_html_e('Customização do Chat', 'help-plugin'); ?></h2>
                    <p><?php esc_html_e('Personalize as cores e tipografia do chat de atendimento.', 'help-plugin'); ?></p>
                    
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                        <?php wp_nonce_field('help_plugin_save_customization', 'help_plugin_customization_nonce'); ?>
                        <input type="hidden" name="action" value="help_plugin_save_customization">
                        
                        <table class="form-table">
                            <tbody>
                                <tr>
                                    <th scope="row">
                                        <label for="chat_name"><?php esc_html_e('Nome do Chat', 'help-plugin'); ?></label>
                                    </th>
                                    <td>
                                        <input 
                                            type="text" 
                                            id="chat_name" 
                                            name="chat_name" 
                                            value="<?php echo esc_attr($settings['chat_name']); ?>" 
                                            class="regular-text"
                                            placeholder="Atendimento"
                                        >
                                        <p class="description">
                                            <?php esc_html_e('Nome exibido no header do chat.', 'help-plugin'); ?>
                                        </p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row">
                                        <label for="welcome_message"><?php esc_html_e('Mensagem Inicial', 'help-plugin'); ?></label>
                                    </th>
                                    <td>
                                        <textarea 
                                            id="welcome_message" 
                                            name="welcome_message" 
                                            rows="3"
                                            class="large-text"
                                            placeholder="Olá! Como podemos ajudar você hoje?"
                                        ><?php echo esc_textarea($settings['welcome_message']); ?></textarea>
                                        <p class="description">
                                            <?php esc_html_e('Mensagem de boas-vindas exibida quando o chat é aberto.', 'help-plugin'); ?>
                                        </p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row">
                                        <label for="primary_color"><?php esc_html_e('Cor Primária', 'help-plugin'); ?></label>
                                    </th>
                                    <td>
                                        <input 
                                            type="color" 
                                            id="primary_color" 
                                            name="primary_color" 
                                            value="<?php echo esc_attr($settings['primary_color']); ?>"
                                        >
                                        <p class="description">
                                            <?php esc_html_e('Cor principal do gradiente (header e botões).', 'help-plugin'); ?>
                                        </p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row">
                                        <label for="secondary_color"><?php esc_html_e('Cor Secundária', 'help-plugin'); ?></label>
                                    </th>
                                    <td>
                                        <input 
                                            type="color" 
                                            id="secondary_color" 
                                            name="secondary_color" 
                                            value="<?php echo esc_attr($settings['secondary_color']); ?>"
                                        >
                                        <p class="description">
                                            <?php esc_html_e('Cor secundária do gradiente.', 'help-plugin'); ?>
                                        </p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row">
                                        <label for="chat_bg_color"><?php esc_html_e('Cor de Fundo do Chat', 'help-plugin'); ?></label>
                                    </th>
                                    <td>
                                        <input 
                                            type="color" 
                                            id="chat_bg_color" 
                                            name="chat_bg_color" 
                                            value="<?php echo esc_attr($settings['chat_bg_color']); ?>"
                                        >
                                        <p class="description">
                                            <?php esc_html_e('Cor de fundo da janela do chat.', 'help-plugin'); ?>
                                        </p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row">
                                        <label for="text_color"><?php esc_html_e('Cor do Texto', 'help-plugin'); ?></label>
                                    </th>
                                    <td>
                                        <input 
                                            type="color" 
                                            id="text_color" 
                                            name="text_color" 
                                            value="<?php echo esc_attr($settings['text_color']); ?>"
                                        >
                                        <p class="description">
                                            <?php esc_html_e('Cor do texto das mensagens.', 'help-plugin'); ?>
                                        </p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row">
                                        <label for="font_family"><?php esc_html_e('Fonte', 'help-plugin'); ?></label>
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
                                            <?php esc_html_e('Família de fonte para o chat.', 'help-plugin'); ?>
                                        </p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row">
                                        <label for="font_size"><?php esc_html_e('Tamanho da Fonte', 'help-plugin'); ?></label>
                                    </th>
                                    <td>
                                        <select id="font_size" name="font_size" class="regular-text">
                                            <option value="12px" <?php selected($settings['font_size'], '12px'); ?>>12px - Pequeno</option>
                                            <option value="14px" <?php selected($settings['font_size'], '14px'); ?>>14px - Normal</option>
                                            <option value="16px" <?php selected($settings['font_size'], '16px'); ?>>16px - Médio</option>
                                            <option value="18px" <?php selected($settings['font_size'], '18px'); ?>>18px - Grande</option>
                                            <option value="20px" <?php selected($settings['font_size'], '20px'); ?>>20px - Extra Grande</option>
                                        </select>
                                        <p class="description">
                                            <?php esc_html_e('Tamanho da fonte das mensagens.', 'help-plugin'); ?>
                                        </p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <?php submit_button(__('Salvar Customizações', 'help-plugin')); ?>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Registrar interação para estatísticas
     */
    private function log_interaction($session_id, $message = '') {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'help_plugin_interactions';
        
        // Criar tabela se não existir
        $this->create_interactions_table();
        
        $wpdb->insert(
            $table_name,
            array(
                'session_id' => $session_id,
                'message' => sanitize_textarea_field($message),
                'interaction_date' => current_time('mysql'),
                'created_at' => current_time('mysql')
            ),
            array('%s', '%s', '%s', '%s')
        );
    }
    
    /**
     * Criar tabela de interações
     */
    private function create_interactions_table() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'help_plugin_interactions';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            session_id varchar(255) NOT NULL,
            message text,
            interaction_date datetime NOT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY session_id (session_id),
            KEY interaction_date (interaction_date)
        ) $charset_collate;";
        
        // Se a tabela já existe, adicionar coluna message se não existir
        $column_exists = $wpdb->get_results($wpdb->prepare(
            "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = 'message'",
            DB_NAME,
            $table_name
        ));
        
        if (empty($column_exists)) {
            $wpdb->query("ALTER TABLE $table_name ADD COLUMN message text AFTER session_id");
        }
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Renderizar página de estatísticas
     */
    public function render_statistics_page() {
        ?>
        <div class="wrap help-plugin-wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <div class="help-plugin-content">
                <div class="help-plugin-card">
                    <h2><?php esc_html_e('Estatísticas de Interações', 'help-plugin'); ?></h2>
                    <p><?php esc_html_e('Visualize as interações do chat por período.', 'help-plugin'); ?></p>
                    
                    <div style="margin: 20px 0;">
                        <h3><?php esc_html_e('Resumo Rápido', 'help-plugin'); ?></h3>
                        <div style="display: flex; gap: 20px; flex-wrap: wrap; margin: 15px 0;">
                            <div style="background: #f0f0f1; padding: 15px; border-radius: 5px; min-width: 150px;">
                                <strong><?php esc_html_e('Últimas 24 horas', 'help-plugin'); ?></strong>
                                <div id="stats-1day" style="font-size: 24px; font-weight: bold; color: #667eea;">-</div>
                            </div>
                            <div style="background: #f0f0f1; padding: 15px; border-radius: 5px; min-width: 150px;">
                                <strong><?php esc_html_e('Últimos 7 dias', 'help-plugin'); ?></strong>
                                <div id="stats-7days" style="font-size: 24px; font-weight: bold; color: #667eea;">-</div>
                            </div>
                            <div style="background: #f0f0f1; padding: 15px; border-radius: 5px; min-width: 150px;">
                                <strong><?php esc_html_e('Último mês', 'help-plugin'); ?></strong>
                                <div id="stats-30days" style="font-size: 24px; font-weight: bold; color: #667eea;">-</div>
                            </div>
                        </div>
                    </div>
                    
                    <div style="margin: 30px 0;">
                        <h3><?php esc_html_e('Selecionar Período', 'help-plugin'); ?></h3>
                        <div style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap; margin: 15px 0;">
                            <div>
                                <label for="date-from" style="display: block; margin-bottom: 5px; font-weight: bold;"><?php esc_html_e('Data Inicial:', 'help-plugin'); ?></label>
                                <input type="date" id="date-from" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                            </div>
                            <div>
                                <label for="date-to" style="display: block; margin-bottom: 5px; font-weight: bold;"><?php esc_html_e('Data Final:', 'help-plugin'); ?></label>
                                <input type="date" id="date-to" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                            </div>
                            <div style="align-self: flex-end;">
                                <button type="button" id="load-statistics" class="button button-primary" style="padding: 8px 20px;">
                                    <?php esc_html_e('Carregar Estatísticas', 'help-plugin'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div style="margin: 30px 0;">
                        <h3><?php esc_html_e('Gráfico de Interações', 'help-plugin'); ?></h3>
                        <div style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
                            <canvas id="interactions-chart" style="max-height: 400px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * AJAX handler para obter estatísticas
     */
    public function ajax_get_statistics() {
        // Verificar permissões
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Sem permissão'));
            return;
        }
        
        // Verificar nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'help-plugin-statistics')) {
            wp_send_json_error(array('message' => 'Erro de segurança'));
            return;
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'help_plugin_interactions';
        
        // Garantir que a tabela existe
        $this->create_interactions_table();
        
        $date_from = isset($_POST['date_from']) ? sanitize_text_field($_POST['date_from']) : '';
        $date_to = isset($_POST['date_to']) ? sanitize_text_field($_POST['date_to']) : '';
        
        // Se não houver datas, usar período padrão
        if (empty($date_from) || empty($date_to)) {
            $date_to = current_time('Y-m-d');
            $date_from = date('Y-m-d', strtotime('-30 days'));
        }
        
        // Buscar estatísticas por período
        $stats_1day = $this->get_interactions_count(1);
        $stats_7days = $this->get_interactions_count(7);
        $stats_30days = $this->get_interactions_count(30);
        
        // Buscar dados detalhados para o gráfico
        $chart_data = $this->get_chart_data($date_from, $date_to);
        
        wp_send_json_success(array(
            'summary' => array(
                '1day' => $stats_1day,
                '7days' => $stats_7days,
                '30days' => $stats_30days
            ),
            'chart' => $chart_data
        ));
    }
    
    /**
     * Obter contagem de interações por dias
     */
    private function get_interactions_count($days) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'help_plugin_interactions';
        
        $date = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        $count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(DISTINCT session_id) FROM $table_name WHERE interaction_date >= %s",
            $date
        ));
        
        return intval($count);
    }
    
    /**
     * Obter dados para o gráfico
     */
    private function get_chart_data($date_from, $date_to) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'help_plugin_interactions';
        
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT DATE(interaction_date) as date, COUNT(DISTINCT session_id) as count 
            FROM $table_name 
            WHERE DATE(interaction_date) >= %s AND DATE(interaction_date) <= %s 
            GROUP BY DATE(interaction_date) 
            ORDER BY date ASC",
            $date_from,
            $date_to
        ));
        
        $labels = array();
        $data = array();
        
        // Criar array completo de datas no período
        $start = new DateTime($date_from);
        $end = new DateTime($date_to);
        $interval = new DateInterval('P1D');
        $period = new DatePeriod($start, $interval, $end->modify('+1 day'));
        
        // Criar mapa de resultados
        $results_map = array();
        foreach ($results as $result) {
            $results_map[$result->date] = intval($result->count);
        }
        
        // Preencher labels e dados
        foreach ($period as $date) {
            $date_str = $date->format('Y-m-d');
            $labels[] = $date->format('d/m');
            $data[] = isset($results_map[$date_str]) ? $results_map[$date_str] : 0;
        }
        
        return array(
            'labels' => $labels,
            'data' => $data
        );
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

