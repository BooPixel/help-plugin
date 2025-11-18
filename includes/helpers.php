<?php
/**
 * Helper functions for BooChat Connect
 *
 * @package BooChat_Connect
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get dynamic version based on timestamp to break cache
 *
 * @return string Version number
 */
if (!function_exists('boochat_connect_get_version')) {
    function boochat_connect_get_version() {
    $version = get_option('boochat_connect_cache_version');
    if (empty($version)) {
        $version = time();
        update_option('boochat_connect_cache_version', $version);
    }
    return $version;
    }
}

/**
 * Get language code from WordPress locale
 *
 * @param string $locale WordPress locale (e.g., pt_BR, es_ES, en_US).
 * @return string Language code (en, pt, es)
 */
if (!function_exists('boochat_connect_get_language_from_locale')) {
    function boochat_connect_get_language_from_locale($locale) {
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
}

/**
 * Get translation based on configured language
 *
 * @param string $key Translation key.
 * @param string $default Default value if key not found.
 * @return string Translated string
 */
if (!function_exists('boochat_connect_translate')) {
    function boochat_connect_translate($key, $default = '') {
    // Get configured language or use WordPress locale
    $configured_language = get_option('boochat_connect_language', '');
    
    if (empty($configured_language)) {
        // If no configuration, use WordPress locale
        $wp_locale = get_locale();
        $language = boochat_connect_get_language_from_locale($wp_locale);
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
            'last_year' => 'Last year',
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
            'conversation_history' => 'Conversation History',
            'conversation_history_description' => 'View detailed conversation history between users and the bot.',
            'show_users' => 'Show users:',
            'load_conversations' => 'Load Conversations',
            'select_dates_and_load' => 'Select date range and click "Load Conversations" to view conversation history.',
            'loading_conversations' => 'Loading conversations...',
            'less' => 'Less',
            'more' => 'More',
                   'main_panel' => 'Main Panel',
                   'customization' => 'Customization',
                   'settings' => 'Settings',
                   'statistics' => 'Statistics',
            'api_not_configured_error' => 'API URL not configured. Configure in Help Plugin > Settings.',
            'http_error_message' => 'HTTP %d: %s',
            'empty_message' => 'Empty message.',
            'api_connection_error' => 'Error connecting to the service. Please try again.',
            'server_response_error' => 'Error processing server response.',
            'no_permission' => 'No permission.',
            'security_error' => 'Security error. Please reload the page.',
            'default_settings' => 'Default Settings',
            'language' => 'Language',
            'language_description' => 'Select the plugin language.',
            'language_auto' => 'Auto (WordPress Language)',
            'api_url' => 'API URL',
            'information' => 'Information',
            'status' => 'Status',
            'text_customization' => 'Text Customization',
            'color_customization' => 'Color Customization',
            'customize_text_settings' => 'Customize text and typography settings.',
            'customize_color_settings' => 'Customize color and appearance settings.',
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
            'last_year' => 'Último ano',
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
            'conversation_history' => 'Histórico de Conversas',
            'conversation_history_description' => 'Visualize o histórico detalhado de conversas entre usuários e o bot.',
            'show_users' => 'Mostrar usuários:',
            'load_conversations' => 'Carregar Conversas',
            'select_dates_and_load' => 'Selecione o período e clique em "Carregar Conversas" para visualizar o histórico de conversas.',
            'loading_conversations' => 'Carregando conversas...',
            'less' => 'Menos',
            'more' => 'Mais',
                   'main_panel' => 'Painel Principal',
                   'customization' => 'Customização',
                   'settings' => 'Configurações',
                   'statistics' => 'Estatísticas',
            'api_not_configured_error' => 'URL da API não configurada. Configure em Help Plugin > Configurações.',
            'http_error_message' => 'HTTP %d: %s',
            'empty_message' => 'Mensagem vazia.',
            'api_connection_error' => 'Erro ao conectar com o serviço de atendimento. Tente novamente.',
            'server_response_error' => 'Erro ao processar resposta do servidor.',
            'no_permission' => 'Sem permissão.',
            'security_error' => 'Erro de segurança. Recarregue a página.',
            'default_settings' => 'Configurações Padrão',
            'language' => 'Idioma',
            'language_description' => 'Selecione o idioma do plugin.',
            'language_auto' => 'Automático (Idioma do WordPress)',
            'api_url' => 'URL da API',
            'information' => 'Informações',
            'status' => 'Status',
            'text_customization' => 'Personalização de Texto',
            'color_customization' => 'Personalização de Cores',
            'customize_text_settings' => 'Personalize as configurações de texto e tipografia.',
            'customize_color_settings' => 'Personalize as configurações de cores e aparência.',
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
            'last_year' => 'Último año',
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
            'conversation_history' => 'Historial de Conversaciones',
            'conversation_history_description' => 'Visualiza el historial detallado de conversaciones entre usuarios y el bot.',
            'show_users' => 'Mostrar usuarios:',
            'load_conversations' => 'Cargar Conversaciones',
            'select_dates_and_load' => 'Selecciona el rango de fechas y haz clic en "Cargar Conversaciones" para ver el historial de conversaciones.',
            'loading_conversations' => 'Cargando conversaciones...',
            'less' => 'Menos',
            'more' => 'Más',
                   'main_panel' => 'Panel Principal',
                   'customization' => 'Personalización',
                   'settings' => 'Configuración',
                   'statistics' => 'Estadísticas',
            'api_not_configured_error' => 'URL de la API no configurada. Configura en Help Plugin > Configuración.',
            'http_error_message' => 'HTTP %d: %s',
            'empty_message' => 'Mensaje vacío.',
            'api_connection_error' => 'Error al conectar con el servicio. Inténtalo de nuevo.',
            'server_response_error' => 'Error al procesar la respuesta del servidor.',
            'no_permission' => 'Sin permiso.',
            'security_error' => 'Error de seguridad. Recarga la página.',
            'default_settings' => 'Configuración Predeterminada',
            'language' => 'Idioma',
            'language_description' => 'Selecciona el idioma del plugin.',
            'language_auto' => 'Automático (Idioma de WordPress)',
            'api_url' => 'URL de la API',
            'information' => 'Información',
            'status' => 'Estado',
            'text_customization' => 'Personalización de Texto',
            'color_customization' => 'Personalización de Colores',
            'customize_text_settings' => 'Personaliza la configuración de texto y tipografía.',
            'customize_color_settings' => 'Personaliza la configuración de colores y apariencia.',
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
}

