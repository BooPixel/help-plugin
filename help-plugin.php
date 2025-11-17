<?php
/**
 * Plugin Name: BooChat Connect
 * Plugin URI: https://boopixel.com/boochat-connect
 * Description: AI Chatbot & n8n Automation - Modern, lightweight chatbot popup that integrates seamlessly with n8n. Automate workflows, respond in real-time, collect leads, and connect to any AI model or external service. Perfect for 24/7 AI support, sales automation, and smart customer interactions.
 * Version: 1.0.10
 * Author: BooPixel
 * Author URI: https://boopixel.com
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: boochat-connect
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.2
 * Network: false
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define constants
define('HELP_PLUGIN_VERSION', '1.0.10');
define('HELP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('HELP_PLUGIN_URL', plugin_dir_url(__FILE__));

// Dynamic version based on timestamp to break cache
function help_plugin_get_version() {
    $version = get_option('help_plugin_cache_version');
    if (empty($version)) {
        $version = time();
        update_option('help_plugin_cache_version', $version);
    }
    return $version;
}

/**
 * Get language code from WordPress locale
 * 
 * @param string $locale WordPress locale (e.g., pt_BR, es_ES, en_US)
 * @return string Language code (en, pt, es)
 */
function help_plugin_get_language_from_locale($locale) {
    // Map WordPress locales to plugin codes
    $locale_map = array(
        'pt_BR' => 'pt',
        'pt_PT' => 'pt',
        'es_ES' => 'es',
        'es_MX' => 'es',
        'es_AR' => 'es',
        'es_CO' => 'es',
        'es_CL' => 'es',
        'es_PE' => 'es',
        'es_VE' => 'es',
        'en_US' => 'en',
        'en_GB' => 'en',
        'en_CA' => 'en',
        'en_AU' => 'en',
    );
    
    // Check full mapping
    if (isset($locale_map[$locale])) {
        return $locale_map[$locale];
    }
    
    // Check prefix only (pt, es, en)
    $prefix = substr($locale, 0, 2);
    if (in_array($prefix, array('pt', 'es', 'en'))) {
        return $prefix;
    }
    
    // Default: English
    return 'en';
}

/**
 * Get translation based on configured language
 */
function help_plugin_translate($key, $default = '') {
    // Get configured language or use WordPress locale
    $configured_language = get_option('help_plugin_language', '');
    
    if (empty($configured_language)) {
        // If no configuration, use WordPress locale
        $wp_locale = get_locale();
        $language = help_plugin_get_language_from_locale($wp_locale);
    } else {
        $language = $configured_language;
    }
    
    $translations = array(
        'en' => array(
            'chat_name_default' => 'Support',
            'welcome_message_default' => 'Hello! How can we help you today?',
            'need_help' => 'Need help?',
            'close' => 'Close',
            'close_chat' => 'Close chat',
            'type_message' => 'Type your message...',
            'send' => 'Send',
            'waiting_response' => 'Waiting for response...',
            'error_send_message' => 'Error sending message. Please try again.',
            'customization_saved' => 'Customizations saved successfully!',
            'chat_customization' => 'Chat Customization',
            'customize_colors_typography' => 'Customize colors and typography of the support chat.',
            'save_customizations' => 'Save Customizations',
            'name_displayed_header' => 'Name displayed in chat header.',
            'welcome_message_displayed' => 'Welcome message displayed when chat is opened.',
            'primary_gradient_color' => 'Primary gradient color (header and buttons).',
            'secondary_gradient_color' => 'Secondary gradient color.',
            'chat_window_background' => 'Chat window background color.',
            'message_text_color' => 'Message text color.',
            'font_family_chat' => 'Font family for the chat.',
            'font_size_messages' => 'Font size for messages.',
            'settings_saved' => 'Settings saved successfully!',
            'api_settings' => 'API Settings',
            'configure_api_url' => 'Configure the customer service API URL.',
            'save_settings' => 'Save Settings',
            'api_webhook_url' => 'Complete webhook URL for customer service API.',
            'api_configured' => 'Configured',
            'api_not_configured' => 'Not configured',
            'api_will_process' => 'The API URL will be used to process all support chat messages.',
            'statistics_interactions' => 'Interaction Statistics',
            'view_interactions_period' => 'View chat interactions by period.',
            'quick_summary' => 'Quick Summary',
            'last_24_hours' => 'Last 24 hours',
            'last_7_days' => 'Last 7 days',
            'last_month' => 'Last month',
            'select_period' => 'Select Period',
            'start_date' => 'Start Date:',
            'end_date' => 'End Date:',
            'load_statistics' => 'Load Statistics',
            'loading' => 'Loading...',
            'select_dates' => 'Please select start and end dates.',
            'invalid_date_range' => 'Start date must be before end date.',
            'error_loading_statistics' => 'Error loading statistics: ',
            'error_connecting_server' => 'Error connecting to server. Please try again.',
            'interactions_chart' => 'Interactions Chart',
            'calendar_heatmap' => 'Interaction Calendar',
            'calendar_description' => 'Visualize user interactions over the past year. Darker colors indicate more interactions.',
            'less' => 'Less',
            'more' => 'More',
            'main_panel' => 'Main Panel',
            'settings' => 'Settings',
            'statistics' => 'Statistics',
            'api_not_configured_error' => 'API URL not configured. Configure in Help Plugin > Settings.',
            'http_error_message' => 'HTTP %d: %s',
            'empty_message' => 'Empty message.',
            'api_connection_error' => 'Error connecting to the service. Please try again.',
            'server_response_error' => 'Error processing server response.',
            'no_permission' => 'No permission.',
            'security_error' => 'Security error. Please reload the page.',
            'language' => 'Language',
            'language_description' => 'Select the plugin language. If not set, it will use the WordPress site language.',
            'language_auto' => 'Auto (WordPress Language)',
            'api_url' => 'API URL',
            'information' => 'Information',
            'status' => 'Status',
            'chat_name' => 'Chat Name',
            'welcome_message' => 'Welcome Message',
            'primary_color' => 'Primary Color',
            'secondary_color' => 'Secondary Color',
            'chat_bg_color' => 'Chat Background Color',
            'text_color' => 'Text Color',
            'font_family' => 'Font',
            'font_size' => 'Font Size',
            'small' => 'Small',
            'normal' => 'Normal',
            'medium' => 'Medium',
            'large' => 'Large',
            'extra_large' => 'Extra Large',
        ),
        'pt' => array(
            'chat_name_default' => 'Atendimento',
            'welcome_message_default' => 'Olá! Como podemos ajudar você hoje?',
            'need_help' => 'Precisa de ajuda?',
            'close' => 'Fechar',
            'close_chat' => 'Fechar chat',
            'type_message' => 'Digite sua mensagem...',
            'send' => 'Enviar',
            'waiting_response' => 'Aguardando resposta...',
            'error_send_message' => 'Erro ao enviar mensagem. Tente novamente.',
            'customization_saved' => 'Customizações salvas com sucesso!',
            'chat_customization' => 'Customização do Chat',
            'customize_colors_typography' => 'Personalize as cores e tipografia do chat de atendimento.',
            'save_customizations' => 'Salvar Customizações',
            'name_displayed_header' => 'Nome exibido no header do chat.',
            'welcome_message_displayed' => 'Mensagem de boas-vindas exibida quando o chat é aberto.',
            'primary_gradient_color' => 'Cor principal do gradiente (header e botões).',
            'secondary_gradient_color' => 'Cor secundária do gradiente.',
            'chat_window_background' => 'Cor de fundo da janela do chat.',
            'message_text_color' => 'Cor do texto das mensagens.',
            'font_family_chat' => 'Família de fonte para o chat.',
            'font_size_messages' => 'Tamanho da fonte das mensagens.',
            'settings_saved' => 'Configurações salvas com sucesso!',
            'api_settings' => 'Configurações da API',
            'configure_api_url' => 'Configure a URL da API de atendimento ao cliente.',
            'save_settings' => 'Salvar Configurações',
            'api_webhook_url' => 'URL completa do webhook da API de atendimento.',
            'api_configured' => 'Configurada',
            'api_not_configured' => 'Não configurada',
            'api_will_process' => 'A URL da API será usada para processar todas as mensagens do chat de atendimento.',
            'statistics_interactions' => 'Estatísticas de Interações',
            'view_interactions_period' => 'Visualize as interações do chat por período.',
            'quick_summary' => 'Resumo Rápido',
            'last_24_hours' => 'Últimas 24 horas',
            'last_7_days' => 'Últimos 7 dias',
            'last_month' => 'Último mês',
            'select_period' => 'Selecionar Período',
            'start_date' => 'Data Inicial:',
            'end_date' => 'Data Final:',
            'load_statistics' => 'Carregar Estatísticas',
            'loading' => 'Carregando...',
            'select_dates' => 'Por favor, selecione as datas inicial e final.',
            'invalid_date_range' => 'A data inicial deve ser anterior à data final.',
            'error_loading_statistics' => 'Erro ao carregar estatísticas: ',
            'error_connecting_server' => 'Erro ao conectar ao servidor. Tente novamente.',
            'interactions_chart' => 'Gráfico de Interações',
            'calendar_heatmap' => 'Calendário de Interações',
            'calendar_description' => 'Visualize as interações dos usuários ao longo do último ano. Cores mais escuras indicam mais interações.',
            'less' => 'Menos',
            'more' => 'Mais',
            'main_panel' => 'Painel Principal',
            'settings' => 'Configurações',
            'statistics' => 'Estatísticas',
            'api_not_configured_error' => 'URL da API não configurada. Configure em Help Plugin > Configurações.',
            'http_error_message' => 'HTTP %d: %s',
            'empty_message' => 'Mensagem vazia.',
            'api_connection_error' => 'Erro ao conectar com o serviço de atendimento. Tente novamente.',
            'server_response_error' => 'Erro ao processar resposta do servidor.',
            'no_permission' => 'Sem permissão.',
            'security_error' => 'Erro de segurança. Recarregue a página.',
            'language' => 'Idioma',
            'language_description' => 'Selecione o idioma do plugin. Se não definido, usará o idioma do WordPress.',
            'language_auto' => 'Automático (Idioma do WordPress)',
            'api_url' => 'URL da API',
            'information' => 'Informações',
            'status' => 'Status',
            'chat_name' => 'Nome do Chat',
            'welcome_message' => 'Mensagem Inicial',
            'primary_color' => 'Cor Primária',
            'secondary_color' => 'Cor Secundária',
            'chat_bg_color' => 'Cor de Fundo do Chat',
            'text_color' => 'Cor do Texto',
            'font_family' => 'Fonte',
            'font_size' => 'Tamanho da Fonte',
            'small' => 'Pequeno',
            'normal' => 'Normal',
            'medium' => 'Médio',
            'large' => 'Grande',
            'extra_large' => 'Extra Grande',
        ),
        'es' => array(
            'chat_name_default' => 'Atención',
            'welcome_message_default' => '¡Hola! ¿Cómo podemos ayudarte hoy?',
            'need_help' => '¿Necesitas ayuda?',
            'close' => 'Cerrar',
            'close_chat' => 'Cerrar chat',
            'type_message' => 'Escribe tu mensaje...',
            'send' => 'Enviar',
            'waiting_response' => 'Esperando respuesta...',
            'error_send_message' => 'Error al enviar mensaje. Inténtalo de nuevo.',
            'customization_saved' => '¡Personalizaciones guardadas con éxito!',
            'chat_customization' => 'Personalización del Chat',
            'customize_colors_typography' => 'Personaliza los colores y tipografía del chat de atención.',
            'save_customizations' => 'Guardar Personalizaciones',
            'name_displayed_header' => 'Nombre mostrado en el encabezado del chat.',
            'welcome_message_displayed' => 'Mensaje de bienvenida mostrado cuando se abre el chat.',
            'primary_gradient_color' => 'Color principal del degradado (encabezado y botones).',
            'secondary_gradient_color' => 'Color secundario del degradado.',
            'chat_window_background' => 'Color de fondo de la ventana del chat.',
            'message_text_color' => 'Color del texto de los mensajes.',
            'font_family_chat' => 'Familia de fuente para el chat.',
            'font_size_messages' => 'Tamaño de fuente de los mensajes.',
            'settings_saved' => '¡Configuración guardada con éxito!',
            'api_settings' => 'Configuración de la API',
            'configure_api_url' => 'Configura la URL de la API de atención al cliente.',
            'save_settings' => 'Guardar Configuración',
            'api_webhook_url' => 'URL completa del webhook de la API de atención.',
            'api_configured' => 'Configurada',
            'api_not_configured' => 'No configurada',
            'api_will_process' => 'La URL de la API se usará para procesar todos los mensajes del chat de atención.',
            'statistics_interactions' => 'Estadísticas de Interacciones',
            'view_interactions_period' => 'Visualiza las interacciones del chat por período.',
            'quick_summary' => 'Resumen Rápido',
            'last_24_hours' => 'Últimas 24 horas',
            'last_7_days' => 'Últimos 7 días',
            'last_month' => 'Último mes',
            'select_period' => 'Seleccionar Período',
            'start_date' => 'Fecha Inicial:',
            'end_date' => 'Fecha Final:',
            'load_statistics' => 'Cargar Estadísticas',
            'loading' => 'Cargando...',
            'select_dates' => 'Por favor, seleccione las fechas inicial y final.',
            'invalid_date_range' => 'La fecha inicial debe ser anterior a la fecha final.',
            'error_loading_statistics' => 'Error al cargar estadísticas: ',
            'error_connecting_server' => 'Error al conectar al servidor. Inténtelo de nuevo.',
            'interactions_chart' => 'Gráfico de Interacciones',
            'calendar_heatmap' => 'Calendario de Interacciones',
            'calendar_description' => 'Visualiza las interacciones de los usuarios durante el último año. Los colores más oscuros indican más interacciones.',
            'less' => 'Menos',
            'more' => 'Más',
            'main_panel' => 'Panel Principal',
            'settings' => 'Configuración',
            'statistics' => 'Estadísticas',
            'api_not_configured_error' => 'URL de la API no configurada. Configura en Help Plugin > Configuración.',
            'http_error_message' => 'HTTP %d: %s',
            'empty_message' => 'Mensaje vacío.',
            'api_connection_error' => 'Error al conectar con el servicio. Inténtalo de nuevo.',
            'server_response_error' => 'Error al procesar la respuesta del servidor.',
            'no_permission' => 'Sin permiso.',
            'security_error' => 'Error de seguridad. Recarga la página.',
            'language' => 'Idioma',
            'language_description' => 'Selecciona el idioma del plugin. Si no está definido, usará el idioma de WordPress.',
            'language_auto' => 'Automático (Idioma de WordPress)',
            'api_url' => 'URL de la API',
            'information' => 'Información',
            'status' => 'Estado',
            'chat_name' => 'Nombre del Chat',
            'welcome_message' => 'Mensaje Inicial',
            'primary_color' => 'Color Primario',
            'secondary_color' => 'Color Secundario',
            'chat_bg_color' => 'Color de Fondo del Chat',
            'text_color' => 'Color del Texto',
            'font_family' => 'Fuente',
            'font_size' => 'Tamaño de la Fuente',
            'small' => 'Pequeño',
            'normal' => 'Normal',
            'medium' => 'Mediano',
            'large' => 'Grande',
            'extra_large' => 'Extra Grande',
        ),
    );
    
    if (isset($translations[$language][$key])) {
        return $translations[$language][$key];
    }
    
    // Fallback to English if key doesn't exist
    if (isset($translations['en'][$key])) {
        return $translations['en'][$key];
    }
    
    return $default ? $default : $key;
}

/**
 * Main plugin class
 */
class Help_Plugin {
    
    /**
     * Single instance of the plugin
     */
    private static $instance = null;
    
    /**
     * Get single instance
     * 
     * @return Help_Plugin Single instance of the plugin
     */
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Plugin activation hook
     */
    public static function activate() {
        $instance = self::get_instance();
        $instance->create_interactions_table();
        flush_rewrite_rules();
    }
    
    /**
     * Plugin deactivation hook
     */
    public static function deactivate() {
        flush_rewrite_rules();
    }
    
    /**
     * Plugin uninstall hook - Remove all plugin data
     */
    public static function uninstall() {
        global $wpdb;
        
        // Check if user has permission
        if (!current_user_can('activate_plugins')) {
            return;
        }
        
        // Remove all plugin options
        $options = array(
            'help_plugin_cache_version',
            'help_plugin_language',
            'help_plugin_chat_name',
            'help_plugin_welcome_message',
            'help_plugin_primary_color',
            'help_plugin_secondary_color',
            'help_plugin_chat_bg_color',
            'help_plugin_text_color',
            'help_plugin_font_family',
            'help_plugin_font_size',
            'help_plugin_api_url'
        );
        
        foreach ($options as $option) {
            delete_option($option);
        }
        
        // Drop database table
        $table_name = $wpdb->prefix . 'help_plugin_interactions';
        $wpdb->query("DROP TABLE IF EXISTS {$table_name}");
        
        // Clear any cached data
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }
    }
    
    /**
     * Constructor
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
        
        // Process settings form
        add_action('admin_post_help_plugin_save_settings', array($this, 'save_settings'));
        add_action('admin_post_help_plugin_save_customization', array($this, 'save_customization'));
        
        // Add custom CSS on frontend
        add_action('wp_head', array($this, 'output_custom_css'));
    }
    
    /**
     * Get currently configured language (returns empty if using WordPress)
     * 
     * @return string Language code (en, pt, es) or empty string to use WordPress
     */
    private function get_language() {
        return get_option('help_plugin_language', '');
    }
    
    /**
     * Get effective language (configured or from WordPress)
     * 
     * @return string Language code (en, pt, es)
     */
    private function get_effective_language() {
        $configured_language = get_option('help_plugin_language', '');
        
        if (empty($configured_language)) {
            // If no configuration, use WordPress locale
            $wp_locale = get_locale();
            return help_plugin_get_language_from_locale($wp_locale);
        }
        
        return $configured_language;
    }
    
    /**
     * Get customization settings
     */
    private function get_customization_settings() {
        $language = $this->get_effective_language();
        return array(
            'chat_name' => get_option('help_plugin_chat_name', help_plugin_translate('chat_name_default')),
            'welcome_message' => get_option('help_plugin_welcome_message', help_plugin_translate('welcome_message_default')),
            'primary_color' => get_option('help_plugin_primary_color', '#667eea'),
            'secondary_color' => get_option('help_plugin_secondary_color', '#764ba2'),
            'chat_bg_color' => get_option('help_plugin_chat_bg_color', '#ffffff'),
            'text_color' => get_option('help_plugin_text_color', '#333333'),
            'font_family' => get_option('help_plugin_font_family', 'Arial, sans-serif'),
            'font_size' => get_option('help_plugin_font_size', '14px'),
            'language' => $language,
        );
    }
    
    /**
     * Save customizations
     */
    public function save_customization() {
        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to access this page.', 'help-plugin'));
        }
        
        // Verify nonce
        if (!isset($_POST['help_plugin_customization_nonce']) || !wp_verify_nonce($_POST['help_plugin_customization_nonce'], 'help_plugin_save_customization')) {
            wp_die(__('Security error. Please try again.', 'help-plugin'));
        }
        
        // Save customization settings
        $language = $this->get_language();
        update_option('help_plugin_chat_name', sanitize_text_field($_POST['chat_name'] ?? help_plugin_translate('chat_name_default')));
        update_option('help_plugin_welcome_message', sanitize_textarea_field($_POST['welcome_message'] ?? help_plugin_translate('welcome_message_default')));
        update_option('help_plugin_primary_color', sanitize_hex_color($_POST['primary_color'] ?? '#667eea'));
        update_option('help_plugin_secondary_color', sanitize_hex_color($_POST['secondary_color'] ?? '#764ba2'));
        update_option('help_plugin_chat_bg_color', sanitize_hex_color($_POST['chat_bg_color'] ?? '#ffffff'));
        update_option('help_plugin_text_color', sanitize_hex_color($_POST['text_color'] ?? '#333333'));
        update_option('help_plugin_font_family', sanitize_text_field($_POST['font_family'] ?? 'Arial, sans-serif'));
        update_option('help_plugin_font_size', sanitize_text_field($_POST['font_size'] ?? '14px'));
        
        // Update cache version to force asset reload
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
     * Output custom CSS on frontend
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
     * 
     * @return string URL da API ou string vazia se não configurada
     */
    private function get_api_url() {
        return get_option('help_plugin_api_url', '');
    }
    
    /**
     * Save settings
     */
    public function save_settings() {
        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to access this page.', 'help-plugin'));
        }
        
        // Verify nonce
        if (!isset($_POST['help_plugin_settings_nonce']) || !wp_verify_nonce($_POST['help_plugin_settings_nonce'], 'help_plugin_save_settings')) {
            wp_die(__('Security error. Please try again.', 'help-plugin'));
        }
        
        // Save API URL
        $api_url = isset($_POST['api_url']) ? esc_url_raw($_POST['api_url']) : '';
        update_option('help_plugin_api_url', $api_url);
        
        // Save language
        $language = isset($_POST['language']) ? sanitize_text_field($_POST['language']) : '';
        if (empty($language)) {
            // If empty, remove option to use WordPress language
            delete_option('help_plugin_language');
        } elseif (in_array($language, array('en', 'pt', 'es'))) {
            update_option('help_plugin_language', $language);
        }
        
        // Redirect with success message
        wp_redirect(add_query_arg(array(
            'page' => 'help-plugin-settings',
            'settings-updated' => 'true'
        ), admin_url('admin.php')));
        exit;
    }
    
    /**
     * Render settings page
     */
    public function render_settings_page() {
        // Process success message
        $settings_updated = isset($_GET['settings-updated']) && $_GET['settings-updated'] === 'true';
        
        $api_url = $this->get_api_url();
        $current_language = $this->get_language();
        ?>
        <div class="wrap help-plugin-wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <?php if ($settings_updated): ?>
                <div class="notice notice-success is-dismissible">
                    <p><?php echo esc_html(help_plugin_translate('settings_saved')); ?></p>
                </div>
            <?php endif; ?>
            
            <div class="help-plugin-content">
                <div class="help-plugin-card">
                    <h2><?php echo esc_html(help_plugin_translate('api_settings')); ?></h2>
                    <p><?php echo esc_html(help_plugin_translate('configure_api_url')); ?></p>
                    
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                        <?php wp_nonce_field('help_plugin_save_settings', 'help_plugin_settings_nonce'); ?>
                        <input type="hidden" name="action" value="help_plugin_save_settings">
                        
                        <table class="form-table">
                            <tbody>
                                <tr>
                                    <th scope="row">
                                        <label for="language"><?php echo esc_html(help_plugin_translate('language', 'Language')); ?></label>
                                    </th>
                                    <td>
                                        <?php
                                        $wp_locale = get_locale();
                                        $wp_language = help_plugin_get_language_from_locale($wp_locale);
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
                                            <option value="" <?php selected($current_language, '', true); ?>><?php echo esc_html(help_plugin_translate('language_auto', 'Auto (WordPress Language)')); ?> (<?php echo esc_html($wp_language_name); ?>)</option>
                                            <option value="en" <?php selected($current_language, 'en'); ?>>English</option>
                                            <option value="pt" <?php selected($current_language, 'pt'); ?>>Português</option>
                                            <option value="es" <?php selected($current_language, 'es'); ?>>Español</option>
                                        </select>
                                        <p class="description">
                                            <?php echo esc_html(help_plugin_translate('language_description')); ?>
                                            <?php if (empty($current_language)): ?>
                                                <br><strong><?php printf(esc_html__('Currently using: %s (from WordPress)', 'help-plugin'), esc_html($wp_language_name)); ?></strong>
                                            <?php else: ?>
                                                <br><strong><?php printf(esc_html__('Currently using: %s (custom)', 'help-plugin'), esc_html($current_language === 'pt' ? 'Português' : ($current_language === 'es' ? 'Español' : 'English'))); ?></strong>
                                            <?php endif; ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="api_url"><?php echo esc_html(help_plugin_translate('api_url', 'API URL')); ?></label>
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
                                            <?php echo esc_html(help_plugin_translate('api_webhook_url')); ?>
                                        </p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <?php submit_button(help_plugin_translate('save_settings')); ?>
                    </form>
                </div>
                
                <div class="help-plugin-card">
                    <h3><?php echo esc_html(help_plugin_translate('information', 'Information')); ?></h3>
                    <p><?php echo esc_html(help_plugin_translate('api_will_process')); ?></p>
                    <p><strong><?php echo esc_html(help_plugin_translate('status', 'Status')); ?>:</strong> 
                        <?php if (!empty($api_url)): ?>
                            <span style="color: green;"><?php echo esc_html(help_plugin_translate('api_configured')); ?></span>
                        <?php else: ?>
                            <span style="color: red;"><?php echo esc_html(help_plugin_translate('api_not_configured')); ?></span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __('BooChat Connect', 'boochat-connect'),           // Page title
            __('BooChat Connect', 'boochat-connect'),           // Menu title
            'manage_options',                            // Capability
            'help-plugin',                               // Menu slug
            array($this, 'render_admin_page'),          // Callback
            'dashicons-sos',                             // Icon (optional)
            30                                           // Position in menu
        );
        
        // Main submenu (optional - shows the same page)
        add_submenu_page(
            'help-plugin',
            help_plugin_translate('main_panel'),
            help_plugin_translate('main_panel'),
            'manage_options',
            'help-plugin',
            array($this, 'render_admin_page')
        );
        
        // Settings submenu
        add_submenu_page(
            'help-plugin',
            help_plugin_translate('settings'),
            help_plugin_translate('settings'),
            'manage_options',
            'help-plugin-settings',
            array($this, 'render_settings_page')
        );
        
        // Statistics submenu
        add_submenu_page(
            'help-plugin',
            help_plugin_translate('statistics'),
            help_plugin_translate('statistics'),
            'manage_options',
            'help-plugin-statistics',
            array($this, 'render_statistics_page')
        );
    }
    
    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        // Debug: log all hooks to identify the correct one
        error_log("Help Plugin: enqueue_admin_assets called with hook: {$hook}");
        
        // Check if we're on a plugin page by checking the page parameter
        $current_page = isset($_GET['page']) ? $_GET['page'] : '';
        $is_plugin_page = (
            $current_page === 'help-plugin' ||
            $current_page === 'help-plugin-settings' ||
            $current_page === 'help-plugin-statistics' ||
            strpos($hook, 'help-plugin') !== false ||
            strpos($hook, 'boochat-connect') !== false
        );
        
        if (!$is_plugin_page) {
            error_log("Help Plugin: Hook '{$hook}' not matching plugin pages, skipping");
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
        
        // Load Chart.js and statistics scripts only on statistics page
        // Check if we're on the statistics page by checking the page parameter
        $current_page = isset($_GET['page']) ? $_GET['page'] : '';
        $is_statistics_page = ($current_page === 'help-plugin-statistics');
        
        if ($is_statistics_page) {
            error_log("Help Plugin: Loading statistics scripts for hook: {$hook}");
            
            // Load Chart.js with higher priority and ensure it loads
            wp_enqueue_script(
                'chart-js',
                'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js',
                array(),
                '4.4.0',
                false  // Load in header to ensure it's available
            );
            
            // Add inline script to verify Chart.js loading
            add_action('admin_footer', function() use ($hook, $is_statistics_page) {
                if ($is_statistics_page) {
                    ?>
                    <script type="text/javascript">
                    // Verify Chart.js is loaded
                    if (typeof Chart === 'undefined') {
                        console.warn('Chart.js not loaded, attempting to load...');
                        var script = document.createElement('script');
                        script.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js';
                        script.onload = function() {
                            console.log('Chart.js loaded successfully via fallback');
                        };
                        script.onerror = function() {
                            console.error('Failed to load Chart.js from CDN');
                        };
                        document.head.appendChild(script);
                    } else {
                        console.log('Chart.js is available');
                    }
                    </script>
                    <?php
                }
            }, 5);
            
            // Use cache-busting version
            $version = help_plugin_get_version();
            $script_url = HELP_PLUGIN_URL . 'assets/js/statistics-script.js';
            
            error_log("Help Plugin: Enqueuing statistics script: {$script_url} with version: {$version}");
            
            wp_enqueue_script(
                'help-plugin-statistics-script',
                $script_url,
                array('jquery', 'chart-js'),
                $version,
                false  // Load in header to ensure it's available
            );
            
            // Localize script - MUST be called AFTER wp_enqueue_script
            $localize_data = array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('help-plugin-statistics'),
                'loadStatisticsText' => help_plugin_translate('load_statistics'),
                'loadingText' => help_plugin_translate('loading', 'Loading...'),
                'selectDatesText' => help_plugin_translate('select_dates', 'Please select start and end dates.'),
                'invalidDateRangeText' => help_plugin_translate('invalid_date_range', 'Start date must be before end date.'),
                'errorLoadingText' => help_plugin_translate('error_loading_statistics', 'Error loading statistics: '),
                'errorConnectingText' => help_plugin_translate('error_connecting_server', 'Error connecting to server. Please try again.'),
            );
            
            error_log("Help Plugin: Localizing script with data: " . print_r($localize_data, true));
            
            wp_localize_script('help-plugin-statistics-script', 'helpPluginStats', $localize_data);
        } else {
            error_log("Help Plugin: Statistics scripts NOT loaded. Current hook: {$hook}");
        }
    }
    
    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        // Only load on frontend, not in admin
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
        
        // Localize script with AJAX data - IMPORTANT: must be called AFTER wp_enqueue_script
        wp_localize_script('help-plugin-chat-script', 'helpPluginAjax', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('help-plugin-chat'),
            'loadingText' => help_plugin_translate('waiting_response'),
            'errorText' => help_plugin_translate('error_send_message')
        ));
    }
    
    /**
     * Render chat widget in footer
     */
    public function render_chat_widget() {
        // Only render on frontend, not in admin
        if (is_admin()) {
            return;
        }
        ?>
        <div id="help-plugin-popup" class="help-plugin-popup">
            <div class="help-plugin-popup-content">
                <div class="help-plugin-popup-header">
                    <span class="help-plugin-popup-icon">💬</span>
                    <span class="help-plugin-popup-text"><?php echo esc_html(help_plugin_translate('need_help')); ?></span>
                </div>
                <button class="help-plugin-popup-close" aria-label="<?php echo esc_attr(help_plugin_translate('close')); ?>">&times;</button>
            </div>
        </div>

        <div id="help-plugin-chat-window" class="help-plugin-chat-window">
            <div class="help-plugin-chat-header">
                <div class="help-plugin-chat-title">
                    <span class="help-plugin-chat-icon">💬</span>
                    <span><?php echo esc_html($this->get_customization_settings()['chat_name']); ?></span>
                </div>
                <button class="help-plugin-chat-close" aria-label="<?php echo esc_attr(help_plugin_translate('close_chat')); ?>">&times;</button>
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
                        placeholder="<?php echo esc_attr(help_plugin_translate('type_message')); ?>"
                        autocomplete="off"
                    >
                    <button type="submit" class="help-plugin-chat-send">
                        <span><?php echo esc_html(help_plugin_translate('send')); ?></span>
                    </button>
                </form>
            </div>
        </div>
        <?php
    }
    
    /**
     * AJAX handler to send message
     */
    public function ajax_send_message() {
        // Verify nonce for security
        check_ajax_referer('help-plugin-chat', 'nonce');
        
        $session_id = isset($_POST['sessionId']) ? sanitize_text_field($_POST['sessionId']) : '';
        $chat_input = isset($_POST['chatInput']) ? sanitize_text_field($_POST['chatInput']) : '';
        
        if (empty($chat_input)) {
            wp_send_json_error(array('message' => help_plugin_translate('empty_message', 'Empty message.')));
            return;
        }
        
        // If no sessionId, generate a new one
        if (empty($session_id)) {
            $session_id = $this->generate_session_id();
        }
        
        // Log interaction for statistics (with message content)
        $this->log_interaction($session_id, $chat_input);
        
        // Make request to external API
        $response = $this->send_to_api($session_id, $chat_input);
        
        if (is_wp_error($response)) {
            wp_send_json_error(array(
                'message' => help_plugin_translate('api_connection_error', 'Error connecting to the service. Please try again.'),
                'sessionId' => $session_id
            ));
            return;
        }
        
        // Extract response body
        $body = isset($response['body']) ? $response['body'] : '';
        $response_code = isset($response['response']['code']) ? $response['response']['code'] : 0;
        
        if ($response_code !== 200) {
            wp_send_json_error(array(
                'message' => help_plugin_translate('api_connection_error', 'Error connecting to the service. Please try again.'),
                'sessionId' => $session_id
            ));
            return;
        }
        
        $data = json_decode($body, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            wp_send_json_error(array(
                'message' => help_plugin_translate('server_response_error', 'Error processing server response.'),
                'sessionId' => $session_id
            ));
            return;
        }
        
        if (!isset($data['output'])) {
            wp_send_json_error(array(
                'message' => help_plugin_translate('server_response_error', 'Error processing server response.'),
                'sessionId' => $session_id
            ));
            return;
        }
        
        // Log robot response to database
        $this->log_robot_response($session_id, $data['output']);
        
        wp_send_json_success(array(
            'message' => $data['output'],
            'sessionId' => $session_id
        ));
    }
    
    /**
     * Generate unique session ID
     */
    private function generate_session_id() {
        return bin2hex(random_bytes(16));
    }
    
    /**
     * Send message to external API
     * 
     * @param string $session_id Unique session ID
     * @param string $chat_input User message
     * @return array|WP_Error API response or error
     */
    private function send_to_api($session_id, $chat_input) {
        $url = $this->get_api_url();
        
        // Check if URL is configured
        if (empty($url)) {
            return new WP_Error('no_api_url', help_plugin_translate('api_not_configured_error', 'API URL not configured. Configure in Help Plugin > Settings.'));
        }
        
        $response = wp_remote_post($url, array(
            'body' => json_encode(array(
                'sessionId' => $session_id,
                'action' => 'sendMessage',
                'chatInput' => $chat_input
            )),
            'headers' => array(
                'Content-Type' => 'application/json',
            ),
            'timeout' => 30,
            'sslverify' => true,
        ));
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        
        if ($response_code !== 200) {
            return new WP_Error('http_error', sprintf(help_plugin_translate('http_error_message', 'HTTP %d: %s'), $response_code, $body));
        }
        
        // Return response as array compatible with existing code
        return array(
            'body' => $body,
            'response' => array(
                'code' => $response_code
            )
        );
    }
    
    /**
     * Render admin page
     */
    public function render_admin_page() {
        // Process success message
        $customization_updated = isset($_GET['customization-updated']) && $_GET['customization-updated'] === 'true';
        
        $settings = $this->get_customization_settings();
        ?>
        <div class="wrap help-plugin-wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <?php if ($customization_updated): ?>
                <div class="notice notice-success is-dismissible">
                    <p><?php echo esc_html(help_plugin_translate('customization_saved')); ?></p>
                </div>
            <?php endif; ?>
            
            <div class="help-plugin-content">
                <div class="help-plugin-card">
                    <h2><?php echo esc_html(help_plugin_translate('chat_customization')); ?></h2>
                    <p><?php echo esc_html(help_plugin_translate('customize_colors_typography')); ?></p>
                    
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                        <?php wp_nonce_field('help_plugin_save_customization', 'help_plugin_customization_nonce'); ?>
                        <input type="hidden" name="action" value="help_plugin_save_customization">
                        
                        <table class="form-table">
                            <tbody>
                                <tr>
                                    <th scope="row">
                                        <label for="chat_name"><?php echo esc_html(help_plugin_translate('chat_name')); ?></label>
                                    </th>
                                    <td>
                                        <input 
                                            type="text" 
                                            id="chat_name" 
                                            name="chat_name" 
                                            value="<?php echo esc_attr($settings['chat_name']); ?>" 
                                            class="regular-text"
                                            placeholder="<?php echo esc_attr(help_plugin_translate('chat_name_default')); ?>"
                                        >
                                        <p class="description">
                                            <?php echo esc_html(help_plugin_translate('name_displayed_header')); ?>
                                        </p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row">
                                        <label for="welcome_message"><?php echo esc_html(help_plugin_translate('welcome_message')); ?></label>
                                    </th>
                                    <td>
                                        <textarea 
                                            id="welcome_message" 
                                            name="welcome_message" 
                                            rows="3"
                                            class="large-text"
                                            placeholder="<?php echo esc_attr(help_plugin_translate('welcome_message_default')); ?>"
                                        ><?php echo esc_textarea($settings['welcome_message']); ?></textarea>
                                        <p class="description">
                                            <?php echo esc_html(help_plugin_translate('welcome_message_displayed')); ?>
                                        </p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row">
                                        <label for="primary_color"><?php echo esc_html(help_plugin_translate('primary_color')); ?></label>
                                    </th>
                                    <td>
                                        <input 
                                            type="color" 
                                            id="primary_color" 
                                            name="primary_color" 
                                            value="<?php echo esc_attr($settings['primary_color']); ?>"
                                        >
                                        <p class="description">
                                            <?php echo esc_html(help_plugin_translate('primary_gradient_color')); ?>
                                        </p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row">
                                        <label for="secondary_color"><?php echo esc_html(help_plugin_translate('secondary_color')); ?></label>
                                    </th>
                                    <td>
                                        <input 
                                            type="color" 
                                            id="secondary_color" 
                                            name="secondary_color" 
                                            value="<?php echo esc_attr($settings['secondary_color']); ?>"
                                        >
                                        <p class="description">
                                            <?php echo esc_html(help_plugin_translate('secondary_gradient_color')); ?>
                                        </p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row">
                                        <label for="chat_bg_color"><?php echo esc_html(help_plugin_translate('chat_bg_color')); ?></label>
                                    </th>
                                    <td>
                                        <input 
                                            type="color" 
                                            id="chat_bg_color" 
                                            name="chat_bg_color" 
                                            value="<?php echo esc_attr($settings['chat_bg_color']); ?>"
                                        >
                                        <p class="description">
                                            <?php echo esc_html(help_plugin_translate('chat_window_background')); ?>
                                        </p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row">
                                        <label for="text_color"><?php echo esc_html(help_plugin_translate('text_color')); ?></label>
                                    </th>
                                    <td>
                                        <input 
                                            type="color" 
                                            id="text_color" 
                                            name="text_color" 
                                            value="<?php echo esc_attr($settings['text_color']); ?>"
                                        >
                                        <p class="description">
                                            <?php echo esc_html(help_plugin_translate('message_text_color')); ?>
                                        </p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row">
                                        <label for="font_family"><?php echo esc_html(help_plugin_translate('font_family')); ?></label>
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
                                            <?php echo esc_html(help_plugin_translate('font_family_chat')); ?>
                                        </p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th scope="row">
                                        <label for="font_size"><?php echo esc_html(help_plugin_translate('font_size')); ?></label>
                                    </th>
                                    <td>
                                        <select id="font_size" name="font_size" class="regular-text">
                                            <option value="12px" <?php selected($settings['font_size'], '12px'); ?>>12px - <?php echo esc_html(help_plugin_translate('small', 'Small')); ?></option>
                                            <option value="14px" <?php selected($settings['font_size'], '14px'); ?>>14px - <?php echo esc_html(help_plugin_translate('normal', 'Normal')); ?></option>
                                            <option value="16px" <?php selected($settings['font_size'], '16px'); ?>>16px - <?php echo esc_html(help_plugin_translate('medium', 'Medium')); ?></option>
                                            <option value="18px" <?php selected($settings['font_size'], '18px'); ?>>18px - <?php echo esc_html(help_plugin_translate('large', 'Large')); ?></option>
                                            <option value="20px" <?php selected($settings['font_size'], '20px'); ?>>20px - <?php echo esc_html(help_plugin_translate('extra_large', 'Extra Large')); ?></option>
                                        </select>
                                        <p class="description">
                                            <?php echo esc_html(help_plugin_translate('font_size_messages')); ?>
                                        </p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <?php submit_button(help_plugin_translate('save_customizations')); ?>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Log interaction for statistics
     */
    private function log_interaction($session_id, $message = '') {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'help_plugin_interactions';
        
        // Create table if it doesn't exist
        $this->create_interactions_table();
        
        $wpdb->insert(
            $table_name,
            array(
                'session_id' => $session_id,
                'message' => sanitize_textarea_field($message),
                'message_type' => 'user',
                'interaction_date' => current_time('mysql'),
                'created_at' => current_time('mysql')
            ),
            array('%s', '%s', '%s', '%s', '%s')
        );
    }
    
    /**
     * Log robot response for statistics
     */
    private function log_robot_response($session_id, $response = '') {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'help_plugin_interactions';
        
        // Create table if it doesn't exist
        $this->create_interactions_table();
        
        $wpdb->insert(
            $table_name,
            array(
                'session_id' => $session_id,
                'message' => sanitize_textarea_field($response),
                'message_type' => 'robot',
                'interaction_date' => current_time('mysql'),
                'created_at' => current_time('mysql')
            ),
            array('%s', '%s', '%s', '%s', '%s')
        );
    }
    
    /**
     * Create interactions table
     */
    private function create_interactions_table() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'help_plugin_interactions';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            session_id varchar(255) NOT NULL,
            message text,
            message_type varchar(20) DEFAULT 'user',
            interaction_date datetime NOT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY session_id (session_id),
            KEY interaction_date (interaction_date),
            KEY message_type (message_type)
        ) $charset_collate;";
        
        // If table already exists, add columns if they don't exist
        $columns_to_check = array('message', 'message_type');
        
        foreach ($columns_to_check as $column) {
            $column_exists = $wpdb->get_results($wpdb->prepare(
                "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = %s",
                DB_NAME,
                $table_name,
                $column
            ));
            
            if (empty($column_exists)) {
                if ($column === 'message') {
                    $wpdb->query("ALTER TABLE $table_name ADD COLUMN message text AFTER session_id");
                } elseif ($column === 'message_type') {
                    $wpdb->query("ALTER TABLE $table_name ADD COLUMN message_type varchar(20) DEFAULT 'user' AFTER message");
                }
            }
        }
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Render statistics page
     */
    public function render_statistics_page() {
        $ajax_url = admin_url('admin-ajax.php');
        $nonce = wp_create_nonce('help-plugin-statistics');
        $today = current_time('Y-m-d');
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <div class="help-plugin-card" style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; box-shadow: 0 1px 1px rgba(0,0,0,.04); margin-top: 20px;">
                <h2><?php echo esc_html(help_plugin_translate('statistics_interactions')); ?></h2>
                <p><?php echo esc_html(help_plugin_translate('view_interactions_period')); ?></p>
                
                <!-- Quick Summary -->
                <div style="margin: 30px 0;">
                    <h3><?php echo esc_html(help_plugin_translate('quick_summary')); ?></h3>
                    <div style="display: flex; gap: 20px; flex-wrap: wrap; margin: 20px 0;">
                        <div style="background: #f0f0f1; padding: 20px; border-radius: 5px; min-width: 180px;">
                            <div style="font-weight: bold; margin-bottom: 10px;"><?php echo esc_html(help_plugin_translate('last_24_hours')); ?></div>
                            <div id="stats-1day" style="font-size: 32px; font-weight: bold; color: #2271b1;">0</div>
                        </div>
                        <div style="background: #f0f0f1; padding: 20px; border-radius: 5px; min-width: 180px;">
                            <div style="font-weight: bold; margin-bottom: 10px;"><?php echo esc_html(help_plugin_translate('last_7_days')); ?></div>
                            <div id="stats-7days" style="font-size: 32px; font-weight: bold; color: #2271b1;">0</div>
                        </div>
                        <div style="background: #f0f0f1; padding: 20px; border-radius: 5px; min-width: 180px;">
                            <div style="font-weight: bold; margin-bottom: 10px;"><?php echo esc_html(help_plugin_translate('last_month')); ?></div>
                            <div id="stats-30days" style="font-size: 32px; font-weight: bold; color: #2271b1;">0</div>
                        </div>
                    </div>
                </div>
                
                <!-- Date Selection -->
                <div style="margin: 30px 0;">
                    <h3><?php echo esc_html(help_plugin_translate('select_period')); ?></h3>
                    <div style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap; margin: 20px 0;">
                        <div>
                            <label for="date-from" style="display: block; margin-bottom: 5px; font-weight: bold;"><?php echo esc_html(help_plugin_translate('start_date')); ?></label>
                            <input type="date" id="date-from" value="<?php echo esc_attr($today); ?>" style="padding: 8px 12px; border: 1px solid #8c8f94; border-radius: 4px; font-size: 14px;">
                        </div>
                        <div>
                            <label for="date-to" style="display: block; margin-bottom: 5px; font-weight: bold;"><?php echo esc_html(help_plugin_translate('end_date')); ?></label>
                            <input type="date" id="date-to" value="<?php echo esc_attr($today); ?>" style="padding: 8px 12px; border: 1px solid #8c8f94; border-radius: 4px; font-size: 14px;">
                        </div>
                        <div>
                            <button type="button" id="load-statistics" class="button button-primary" style="padding: 8px 20px; height: 38px;">
                                <?php echo esc_html(help_plugin_translate('load_statistics')); ?>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Chart -->
                <div style="margin: 30px 0;">
                    <h3><?php echo esc_html(help_plugin_translate('interactions_chart')); ?></h3>
                    <div style="background: #f9f9f9; padding: 20px; border: 1px solid #ddd; border-radius: 5px; margin-top: 15px;">
                        <canvas id="interactions-chart" style="max-height: 400px;"></canvas>
                    </div>
                </div>
                
                <!-- Calendar Heatmap -->
                <div style="margin: 30px 0;">
                    <h3><?php echo esc_html(help_plugin_translate('calendar_heatmap', 'Interaction Calendar')); ?></h3>
                    <p style="margin-bottom: 15px; color: #646970;"><?php echo esc_html(help_plugin_translate('calendar_description', 'Visualize user interactions over the past year. Darker colors indicate more interactions.')); ?></p>
                    <div id="calendar-heatmap" style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 5px; overflow-x: auto;">
                        <div id="calendar-container"></div>
                        <div style="margin-top: 15px; display: flex; align-items: center; gap: 10px; font-size: 12px; color: #646970;">
                            <span><?php echo esc_html(help_plugin_translate('less', 'Less')); ?></span>
                            <div style="display: flex; gap: 3px;">
                                <div style="width: 12px; height: 12px; background: #ebedf0; border: 1px solid #ddd; border-radius: 2px;"></div>
                                <div style="width: 12px; height: 12px; background: #c6e48b; border: 1px solid #ddd; border-radius: 2px;"></div>
                                <div style="width: 12px; height: 12px; background: #7bc96f; border: 1px solid #ddd; border-radius: 2px;"></div>
                                <div style="width: 12px; height: 12px; background: #239a3b; border: 1px solid #ddd; border-radius: 2px;"></div>
                                <div style="width: 12px; height: 12px; background: #196127; border: 1px solid #ddd; border-radius: 2px;"></div>
                            </div>
                            <span><?php echo esc_html(help_plugin_translate('more', 'More')); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <script type="text/javascript">
        var helpPluginStats = {
            ajaxUrl: <?php echo json_encode($ajax_url); ?>,
            nonce: <?php echo json_encode($nonce); ?>,
            loadStatisticsText: <?php echo json_encode(help_plugin_translate('load_statistics')); ?>,
            loadingText: <?php echo json_encode(help_plugin_translate('loading', 'Loading...')); ?>,
            selectDatesText: <?php echo json_encode(help_plugin_translate('select_dates', 'Please select start and end dates.')); ?>,
            invalidDateRangeText: <?php echo json_encode(help_plugin_translate('invalid_date_range', 'Start date must be before end date.')); ?>,
            errorLoadingText: <?php echo json_encode(help_plugin_translate('error_loading_statistics', 'Error loading statistics: ')); ?>,
            errorConnectingText: <?php echo json_encode(help_plugin_translate('error_connecting_server', 'Error connecting to server. Please try again.')); ?>
        };
        </script>
        <?php
    }
    
    /**
     * AJAX handler to get statistics
     */
    public function ajax_get_statistics() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => help_plugin_translate('no_permission', 'No permission.')));
            return;
        }
        
        check_ajax_referer('help-plugin-statistics', 'nonce');
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'help_plugin_interactions';
        
        $this->create_interactions_table();
        
        $date_from = isset($_POST['date_from']) ? sanitize_text_field($_POST['date_from']) : current_time('Y-m-d');
        $date_to = isset($_POST['date_to']) ? sanitize_text_field($_POST['date_to']) : current_time('Y-m-d');
        
        // Debug log
        error_log("Help Plugin: ajax_get_statistics called - date_from: {$date_from}, date_to: {$date_to}");
        
        // Get statistics
        $stats_1day = $this->get_interactions_count(1);
        $stats_7days = $this->get_interactions_count(7);
        $stats_30days = $this->get_interactions_count(30);
        
        // Get chart data
        $chart_data = $this->get_chart_data($date_from, $date_to);
        
        // Get calendar data
        $calendar_data = $this->get_calendar_data();
        
        // Debug log response
        error_log("Help Plugin: Sending response - 1day: {$stats_1day}, 7days: {$stats_7days}, 30days: {$stats_30days}, chart_labels: " . count($chart_data['labels']));
        
        wp_send_json_success(array(
            'summary' => array(
                '1day' => $stats_1day,
                '7days' => $stats_7days,
                '30days' => $stats_30days
            ),
            'chart' => $chart_data,
            'calendar' => $calendar_data
        ));
    }
    
    /**
     * Get interaction count by days (unique sessions)
     */
    private function get_interactions_count($days) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'help_plugin_interactions';
        
        $this->create_interactions_table();
        
        // Calculate date from (X days ago at 00:00:00)
        $date_from = date('Y-m-d 00:00:00', strtotime("-{$days} days", current_time('timestamp')));
        
        // Use esc_sql for table name
        $table_name_escaped = esc_sql($table_name);
        
        $query = $wpdb->prepare(
            "SELECT COUNT(DISTINCT session_id) FROM `{$table_name_escaped}` WHERE interaction_date >= %s",
            $date_from
        );
        
        $count = $wpdb->get_var($query);
        
        // Debug log
        error_log("Help Plugin: get_interactions_count({$days}) - date_from: {$date_from}, count: " . ($count ? $count : 0));
        
        return intval($count ? $count : 0);
    }
    
    /**
     * Get data for chart
     */
    private function get_chart_data($date_from, $date_to) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'help_plugin_interactions';
        
        $this->create_interactions_table();
        
        $date_from_formatted = date('Y-m-d', strtotime($date_from));
        $date_to_formatted = date('Y-m-d', strtotime($date_to));
        $date_from_start = $date_from_formatted . ' 00:00:00';
        $date_to_end = $date_to_formatted . ' 23:59:59';
        
        // Use esc_sql for table name
        $table_name_escaped = esc_sql($table_name);
        
        $query = $wpdb->prepare(
            "SELECT DATE(interaction_date) as date, COUNT(DISTINCT session_id) as count 
            FROM `{$table_name_escaped}` 
            WHERE interaction_date >= %s AND interaction_date <= %s 
            GROUP BY DATE(interaction_date) 
            ORDER BY date ASC",
            $date_from_start,
            $date_to_end
        );
        
        $results = $wpdb->get_results($query);
        
        // Debug log
        error_log("Help Plugin: get_chart_data - date_from: {$date_from_start}, date_to: {$date_to_end}, results: " . (is_array($results) ? count($results) : 0));
        
        $labels = array();
        $data = array();
        
        // Create map of results
        $results_map = array();
        if ($results && is_array($results)) {
            foreach ($results as $result) {
                if (isset($result->date) && isset($result->count)) {
                    $results_map[$result->date] = intval($result->count);
                    error_log("Help Plugin: Chart data - date: {$result->date}, count: {$result->count}");
                }
            }
        }
        
        // Fill all dates in period
        try {
            $start = new DateTime($date_from_formatted);
            $end = new DateTime($date_to_formatted);
            $interval = new DateInterval('P1D');
            $end->modify('+1 day');
            $period = new DatePeriod($start, $interval, $end);
            
            foreach ($period as $date) {
                $date_str = $date->format('Y-m-d');
                $labels[] = $date->format('d/m');
                $data[] = isset($results_map[$date_str]) ? $results_map[$date_str] : 0;
            }
        } catch (Exception $e) {
            error_log("Help Plugin: Error in get_chart_data - " . $e->getMessage());
            return array('labels' => array(), 'data' => array());
        }
        
        return array('labels' => $labels, 'data' => $data);
    }
    
    /**
     * Get calendar heatmap data (last 365 days)
     */
    private function get_calendar_data() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'help_plugin_interactions';
        
        $this->create_interactions_table();
        
        $date_from = date('Y-m-d 00:00:00', strtotime('-365 days', current_time('timestamp')));
        
        // Use esc_sql for table name
        $table_name_escaped = esc_sql($table_name);
        
        $query = $wpdb->prepare(
            "SELECT DATE(interaction_date) as date, COUNT(DISTINCT session_id) as count 
            FROM `{$table_name_escaped}` 
            WHERE interaction_date >= %s 
            GROUP BY DATE(interaction_date) 
            ORDER BY date ASC",
            $date_from
        );
        
        $results = $wpdb->get_results($query);
        
        // Debug log
        error_log("Help Plugin: get_calendar_data - date_from: {$date_from}, results: " . (is_array($results) ? count($results) : 0));
        
        $calendar_map = array();
        if ($results && is_array($results)) {
            foreach ($results as $result) {
                if (isset($result->date) && isset($result->count)) {
                    $calendar_map[$result->date] = intval($result->count);
                }
            }
        }
        
        return $calendar_map;
    }
}

/**
 * Initialize the plugin
 */
function help_plugin_init() {
    return Help_Plugin::get_instance();
}

// Register activation, deactivation and uninstall hooks
register_activation_hook(__FILE__, array('Help_Plugin', 'activate'));
register_deactivation_hook(__FILE__, array('Help_Plugin', 'deactivate'));
register_uninstall_hook(__FILE__, array('Help_Plugin', 'uninstall'));

// Initialize the plugin
help_plugin_init();

