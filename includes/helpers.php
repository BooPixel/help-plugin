<?php
/**
 * Helper functions for BooPixel AI Chat Connect for n8n
 *
 * @package BooPixel_AI_Chat_For_N8n
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
if (!function_exists('boopixel_ai_chat_for_n8n_get_version')) {
    function boopixel_ai_chat_for_n8n_get_version() {
    $version = get_option('boopixel_ai_chat_for_n8n_cache_version');
    if (empty($version)) {
        $version = time();
        update_option('boopixel_ai_chat_for_n8n_cache_version', $version);
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
if (!function_exists('boopixel_ai_chat_for_n8n_get_language_from_locale')) {
    function boopixel_ai_chat_for_n8n_get_language_from_locale($locale) {
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
 * Get translated string using literal strings for proper i18n support
 *
 * @param string $key Translation key.
 * @param string $msgid English text (msgid).
 * @return string Translated string
 */
if (!function_exists('boopixel_ai_chat_for_n8n_get_translated_string')) {
    function boopixel_ai_chat_for_n8n_get_translated_string($key, $msgid) {
        // Use switch to ensure all strings are literal for translators
        // This ensures the translation parser can see all strings
        switch ($msgid) {
            case 'Main Panel':
                return esc_html__('Main Panel', 'boopixel-ai-chat-for-n8n');
            case 'Customization':
                return esc_html__('Customization', 'boopixel-ai-chat-for-n8n');
            case 'Settings':
                return esc_html__('Settings', 'boopixel-ai-chat-for-n8n');
            case 'Statistics':
                return esc_html__('Statistics', 'boopixel-ai-chat-for-n8n');
            case 'Close':
                return esc_html__('Close', 'boopixel-ai-chat-for-n8n');
            case 'Loading...':
                return esc_html__('Loading...', 'boopixel-ai-chat-for-n8n');
            case 'Small':
                return esc_html__('Small', 'boopixel-ai-chat-for-n8n');
            case 'Normal':
                return esc_html__('Normal', 'boopixel-ai-chat-for-n8n');
            case 'Medium':
                return esc_html__('Medium', 'boopixel-ai-chat-for-n8n');
            case 'Large':
                return esc_html__('Large', 'boopixel-ai-chat-for-n8n');
            case 'Extra Large':
                return esc_html__('Extra Large', 'boopixel-ai-chat-for-n8n');
            case 'Less':
                return esc_html__('Less', 'boopixel-ai-chat-for-n8n');
            case 'More':
                return esc_html__('More', 'boopixel-ai-chat-for-n8n');
            case 'Status':
                return esc_html__('Status', 'boopixel-ai-chat-for-n8n');
            case 'Information':
                return esc_html__('Information', 'boopixel-ai-chat-for-n8n');
            case 'Language':
                return esc_html__('Language', 'boopixel-ai-chat-for-n8n');
            case 'Select the plugin language.':
                return esc_html__('Select the plugin language.', 'boopixel-ai-chat-for-n8n');
            case 'Auto (WordPress Language)':
                return esc_html__('Auto (WordPress Language)', 'boopixel-ai-chat-for-n8n');
            case 'Default Settings':
                return esc_html__('Default Settings', 'boopixel-ai-chat-for-n8n');
            case 'Version':
                return esc_html__('Version', 'boopixel-ai-chat-for-n8n');
            case 'AI Chatbot & n8n Automation':
                return esc_html__('AI Chatbot & n8n Automation', 'boopixel-ai-chat-for-n8n');
            case 'Support':
                return esc_html__('Support', 'boopixel-ai-chat-for-n8n');
            case 'Hello! How can we help you today?':
                return esc_html__('Hello! How can we help you today?', 'boopixel-ai-chat-for-n8n');
            case 'Need help?':
                return esc_html__('Need help?', 'boopixel-ai-chat-for-n8n');
            case 'Close chat':
                return esc_html__('Close chat', 'boopixel-ai-chat-for-n8n');
            case 'Type your message...':
                return esc_html__('Type your message...', 'boopixel-ai-chat-for-n8n');
            case 'Send':
                return esc_html__('Send', 'boopixel-ai-chat-for-n8n');
            case 'Waiting for response...':
                return esc_html__('Waiting for response...', 'boopixel-ai-chat-for-n8n');
            case 'Error sending message. Please try again.':
                return esc_html__('Error sending message. Please try again.', 'boopixel-ai-chat-for-n8n');
            case 'API URL not configured. Configure in Help Plugin > Settings.':
                return esc_html__('API URL not configured. Configure in Help Plugin > Settings.', 'boopixel-ai-chat-for-n8n');
            case 'HTTP %d: %s':
                return esc_html__('HTTP %d: %s', 'boopixel-ai-chat-for-n8n');
            case 'Empty message.':
                return esc_html__('Empty message.', 'boopixel-ai-chat-for-n8n');
            case 'Error connecting to the service. Please try again.':
                return esc_html__('Error connecting to the service. Please try again.', 'boopixel-ai-chat-for-n8n');
            case 'Error processing server response.':
                return esc_html__('Error processing server response.', 'boopixel-ai-chat-for-n8n');
            case 'Customizations saved successfully!':
                return esc_html__('Customizations saved successfully!', 'boopixel-ai-chat-for-n8n');
            case 'Chat Customization':
                return esc_html__('Chat Customization', 'boopixel-ai-chat-for-n8n');
            case 'Customize colors and typography of the support chat.':
                return esc_html__('Customize colors and typography of the support chat.', 'boopixel-ai-chat-for-n8n');
            case 'Save Customizations':
                return esc_html__('Save Customizations', 'boopixel-ai-chat-for-n8n');
            case 'Chat Icon':
                return esc_html__('Chat Icon', 'boopixel-ai-chat-for-n8n');
            case 'Upload Icon':
                return esc_html__('Upload Icon', 'boopixel-ai-chat-for-n8n');
            case 'Remove Icon':
                return esc_html__('Remove Icon', 'boopixel-ai-chat-for-n8n');
            case 'Icon displayed in the chat header. Recommended size: 48x48 pixels.':
                return esc_html__('Icon displayed in the chat header. Recommended size: 48x48 pixels.', 'boopixel-ai-chat-for-n8n');
            case 'Name displayed in chat header.':
                return esc_html__('Name displayed in chat header.', 'boopixel-ai-chat-for-n8n');
            case 'Welcome message displayed when chat is opened.':
                return esc_html__('Welcome message displayed when chat is opened.', 'boopixel-ai-chat-for-n8n');
            case 'Primary gradient color (header and buttons).':
                return esc_html__('Primary gradient color (header and buttons).', 'boopixel-ai-chat-for-n8n');
            case 'Secondary gradient color.':
                return esc_html__('Secondary gradient color.', 'boopixel-ai-chat-for-n8n');
            case 'Chat window background color.':
                return esc_html__('Chat window background color.', 'boopixel-ai-chat-for-n8n');
            case 'Message text color.':
                return esc_html__('Message text color.', 'boopixel-ai-chat-for-n8n');
            case 'Font family for the chat.':
                return esc_html__('Font family for the chat.', 'boopixel-ai-chat-for-n8n');
            case 'Font size for messages.':
                return esc_html__('Font size for messages.', 'boopixel-ai-chat-for-n8n');
            case 'Text Customization':
                return esc_html__('Text Customization', 'boopixel-ai-chat-for-n8n');
            case 'Color Customization':
                return esc_html__('Color Customization', 'boopixel-ai-chat-for-n8n');
            case 'Customize text and typography settings.':
                return esc_html__('Customize text and typography settings.', 'boopixel-ai-chat-for-n8n');
            case 'Customize color and appearance settings.':
                return esc_html__('Customize color and appearance settings.', 'boopixel-ai-chat-for-n8n');
            case 'Chat Name':
                return esc_html__('Chat Name', 'boopixel-ai-chat-for-n8n');
            case 'Welcome Message':
                return esc_html__('Welcome Message', 'boopixel-ai-chat-for-n8n');
            case 'Primary Color':
                return esc_html__('Primary Color', 'boopixel-ai-chat-for-n8n');
            case 'Secondary Color':
                return esc_html__('Secondary Color', 'boopixel-ai-chat-for-n8n');
            case 'Chat Background Color':
                return esc_html__('Chat Background Color', 'boopixel-ai-chat-for-n8n');
            case 'Text Color':
                return esc_html__('Text Color', 'boopixel-ai-chat-for-n8n');
            case 'Font':
                return esc_html__('Font', 'boopixel-ai-chat-for-n8n');
            case 'Font Size':
                return esc_html__('Font Size', 'boopixel-ai-chat-for-n8n');
            case 'Settings saved successfully!':
                return esc_html__('Settings saved successfully!', 'boopixel-ai-chat-for-n8n');
            case 'API Settings':
                return esc_html__('API Settings', 'boopixel-ai-chat-for-n8n');
            case 'Configure the customer service API URL.':
                return esc_html__('Configure the customer service API URL.', 'boopixel-ai-chat-for-n8n');
            case 'Save Settings':
                return esc_html__('Save Settings', 'boopixel-ai-chat-for-n8n');
            case 'Complete webhook URL for customer service API.':
                return esc_html__('Complete webhook URL for customer service API.', 'boopixel-ai-chat-for-n8n');
            case 'Configured':
                return esc_html__('Configured', 'boopixel-ai-chat-for-n8n');
            case 'Not configured':
                return esc_html__('Not configured', 'boopixel-ai-chat-for-n8n');
            case 'The API URL will be used to process all support chat messages.':
                return esc_html__('The API URL will be used to process all support chat messages.', 'boopixel-ai-chat-for-n8n');
            case 'API URL':
                return esc_html__('API URL', 'boopixel-ai-chat-for-n8n');
            case 'Interaction Statistics':
                return esc_html__('Interaction Statistics', 'boopixel-ai-chat-for-n8n');
            case 'View chat interactions by period.':
                return esc_html__('View chat interactions by period.', 'boopixel-ai-chat-for-n8n');
            case 'Quick Summary':
                return esc_html__('Quick Summary', 'boopixel-ai-chat-for-n8n');
            case 'Last 24 hours':
                return esc_html__('Last 24 hours', 'boopixel-ai-chat-for-n8n');
            case 'Last 7 days':
                return esc_html__('Last 7 days', 'boopixel-ai-chat-for-n8n');
            case 'Last month':
                return esc_html__('Last month', 'boopixel-ai-chat-for-n8n');
            case 'Select Period':
                return esc_html__('Select Period', 'boopixel-ai-chat-for-n8n');
            case 'Start Date:':
                return esc_html__('Start Date:', 'boopixel-ai-chat-for-n8n');
            case 'End Date:':
                return esc_html__('End Date:', 'boopixel-ai-chat-for-n8n');
            case 'Load Statistics':
                return esc_html__('Load Statistics', 'boopixel-ai-chat-for-n8n');
            case 'Please select start and end dates.':
                return esc_html__('Please select start and end dates.', 'boopixel-ai-chat-for-n8n');
            case 'Start date must be before end date.':
                return esc_html__('Start date must be before end date.', 'boopixel-ai-chat-for-n8n');
            case 'Error loading statistics: ':
                return esc_html__('Error loading statistics: ', 'boopixel-ai-chat-for-n8n');
            case 'Error connecting to server. Please try again.':
                return esc_html__('Error connecting to server. Please try again.', 'boopixel-ai-chat-for-n8n');
            case 'Interactions Chart':
                return esc_html__('Interactions Chart', 'boopixel-ai-chat-for-n8n');
            case 'Interaction Calendar':
                return esc_html__('Interaction Calendar', 'boopixel-ai-chat-for-n8n');
            case 'Visualize user interactions over the past year. Darker colors indicate more interactions.':
                return esc_html__('Visualize user interactions over the past year. Darker colors indicate more interactions.', 'boopixel-ai-chat-for-n8n');
            case 'No permission.':
                return esc_html__('No permission.', 'boopixel-ai-chat-for-n8n');
            case 'Security error. Please reload the page.':
                return esc_html__('Security error. Please reload the page.', 'boopixel-ai-chat-for-n8n');
            case 'You do not have permission to access this page.':
                return esc_html__('You do not have permission to access this page.', 'boopixel-ai-chat-for-n8n');
            case 'Security check failed.':
                return esc_html__('Security check failed.', 'boopixel-ai-chat-for-n8n');
            case 'Security error. Please try again.':
                return esc_html__('Security error. Please try again.', 'boopixel-ai-chat-for-n8n');
            case 'Sessions':
                return esc_html__('Sessions', 'boopixel-ai-chat-for-n8n');
            case 'User Sessions':
                return esc_html__('User Sessions', 'boopixel-ai-chat-for-n8n');
            case 'Upgrade to PRO':
                return esc_html__('Upgrade to PRO', 'boopixel-ai-chat-for-n8n');
            case 'Loading sessions...':
                return esc_html__('Loading sessions...', 'boopixel-ai-chat-for-n8n');
            case 'Error loading sessions: ':
                return esc_html__('Error loading sessions: ', 'boopixel-ai-chat-for-n8n');
            case 'No sessions found.':
                return esc_html__('No sessions found.', 'boopixel-ai-chat-for-n8n');
            case 'Failed to create checkout session.':
                return esc_html__('Failed to create checkout session.', 'boopixel-ai-chat-for-n8n');
            case 'View all user chat sessions and their details.':
                return esc_html__('View all user chat sessions and their details.', 'boopixel-ai-chat-for-n8n');
            case 'Show:':
                return esc_html__('Show:', 'boopixel-ai-chat-for-n8n');
            case 'Refresh':
                return esc_html__('Refresh', 'boopixel-ai-chat-for-n8n');
            default:
                // Fallback: use the msgid directly if not in switch
                // This handles any keys not explicitly listed above
                return esc_html__($msgid, 'boopixel-ai-chat-for-n8n');
        }
    }
}

/**
 * Get translation based on configured language
 *
 * This function maps translation keys to English text (msgid) and uses
 * WordPress gettext functions following WordPress I18n standards.
 *
 * @param string $key Translation key (maps to English text).
 * @param string $default Default value if key not found (used as fallback msgid).
 * @return string Translated string
 */
if (!function_exists('boopixel_ai_chat_for_n8n_translate')) {
    function boopixel_ai_chat_for_n8n_translate($key, $default = '') {
        // Map translation keys to English text (msgid)
        $key_to_msgid = array(
            // Common
            'main_panel' => 'Main Panel',
            'customization' => 'Customization',
            'settings' => 'Settings',
            'statistics' => 'Statistics',
            'close' => 'Close',
            'loading' => 'Loading...',
            'small' => 'Small',
            'normal' => 'Normal',
            'medium' => 'Medium',
            'large' => 'Large',
            'extra_large' => 'Extra Large',
            'less' => 'Less',
            'more' => 'More',
            'status' => 'Status',
            'information' => 'Information',
            'language' => 'Language',
            'language_description' => 'Select the plugin language.',
            'language_auto' => 'Auto (WordPress Language)',
            'default_settings' => 'Default Settings',
            'version' => 'Version',
            'ai_chatbot_automation' => 'AI Chatbot & n8n Automation',
            // Chat
            'chat_name_default' => 'Support',
            'welcome_message_default' => 'Hello! How can we help you today?',
            'need_help' => 'Need help?',
            'close_chat' => 'Close chat',
            'type_message' => 'Type your message...',
            'send' => 'Send',
            'waiting_response' => 'Waiting for response...',
            'error_send_message' => 'Error sending message. Please try again.',
            'api_not_configured_error' => 'API URL not configured. Configure in Help Plugin > Settings.',
            'http_error_message' => 'HTTP %d: %s',
            'empty_message' => 'Empty message.',
            'api_connection_error' => 'Error connecting to the service. Please try again.',
            'server_response_error' => 'Error processing server response.',
            // Customization
            'customization_saved' => 'Customizations saved successfully!',
            'chat_customization' => 'Chat Customization',
            'customize_colors_typography' => 'Customize colors and typography of the support chat.',
            'save_customizations' => 'Save Customizations',
            'chat_icon' => 'Chat Icon',
            'upload_icon' => 'Upload Icon',
            'remove_icon' => 'Remove Icon',
            'icon_displayed_header' => 'Icon displayed in the chat header. Recommended size: 48x48 pixels.',
            'name_displayed_header' => 'Name displayed in chat header.',
            'welcome_message_displayed' => 'Welcome message displayed when chat is opened.',
            'primary_gradient_color' => 'Primary gradient color (header and buttons).',
            'secondary_gradient_color' => 'Secondary gradient color.',
            'chat_window_background' => 'Chat window background color.',
            'message_text_color' => 'Message text color.',
            'font_family_chat' => 'Font family for the chat.',
            'font_size_messages' => 'Font size for messages.',
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
            // Settings
            'settings_saved' => 'Settings saved successfully!',
            'api_settings' => 'API Settings',
            'configure_api_url' => 'Configure the customer service API URL.',
            'save_settings' => 'Save Settings',
            'api_webhook_url' => 'Complete webhook URL for customer service API.',
            'api_configured' => 'Configured',
            'api_not_configured' => 'Not configured',
            'api_will_process' => 'The API URL will be used to process all support chat messages.',
            'api_url' => 'API URL',
            // Statistics
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
            'select_dates' => 'Please select start and end dates.',
            'invalid_date_range' => 'Start date must be before end date.',
            'error_loading_statistics' => 'Error loading statistics: ',
            'error_connecting_server' => 'Error connecting to server. Please try again.',
            'interactions_chart' => 'Interactions Chart',
            'calendar_heatmap' => 'Interaction Calendar',
            'calendar_description' => 'Visualize user interactions over the past year. Darker colors indicate more interactions.',
            // Admin
            'no_permission' => 'No permission.',
            'security_error' => 'Security error. Please reload the page.',
            'no_permission_page' => 'You do not have permission to access this page.',
            'security_check_failed' => 'Security check failed.',
            'security_error_try_again' => 'Security error. Please try again.',
            'sessions' => 'Sessions',
            'user_sessions' => 'User Sessions',
            'upgrade_to_pro' => 'Upgrade to PRO',
            'loading_sessions' => 'Loading sessions...',
            'error_loading_sessions' => 'Error loading sessions: ',
            'no_sessions_found' => 'No sessions found.',
            'failed_checkout_session' => 'Failed to create checkout session.',
            'view_user_sessions' => 'View all user chat sessions and their details.',
            'show' => 'Show:',
            'refresh' => 'Refresh',
        );
        
        // Get the English text (msgid) for this key
        $msgid = isset($key_to_msgid[$key]) ? $key_to_msgid[$key] : ($default ? $default : $key);
        
        // Get configured language
        $configured_language = get_option('boopixel_ai_chat_for_n8n_language', '');
        
        // Use WordPress gettext function with string literal (locale is already set by force_plugin_locale filter)
        // Map each key to its literal string to ensure translators can see all strings
        return boopixel_ai_chat_for_n8n_get_translated_string($key, $msgid);
    }
}

/**
 * Log API request and response details
 *
 * @param string $endpoint_name Name/identifier of the API endpoint (e.g., 'send_message', 'activate').
 * @param string $url Full API URL.
 * @param array  $request_body Request payload (will be JSON encoded).
 * @param array  $headers Request headers (optional).
 * @param array|WP_Error $response WordPress HTTP response or error.
 * @return void
 */
if (!function_exists('boopixel_ai_chat_for_n8n_log_api_request')) {
    function boopixel_ai_chat_for_n8n_log_api_request($endpoint_name, $url, $request_body = array(), $headers = array(), $response = null) {
        $log_prefix = '[BooPixel AI Chat Connect for n8n] [' . $endpoint_name . ']';
        
        // Log request details
        // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- API request logging
        error_log($log_prefix . ' Request URL: ' . $url);
        
        if (!empty($request_body)) {
            // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- API request logging
            error_log($log_prefix . ' Request Body: ' . wp_json_encode($request_body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        }
        
        if (!empty($headers)) {
            // Sanitize headers for logging (remove sensitive data like API keys)
            $log_headers = $headers;
            if (isset($log_headers['X-API-Key'])) {
                $log_headers['X-API-Key'] = substr($log_headers['X-API-Key'], 0, 8) . '...' . substr($log_headers['X-API-Key'], -4);
            }
            // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- API request logging
            error_log($log_prefix . ' Request Headers: ' . wp_json_encode($log_headers, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        }
        
        // Log response details
        if (is_wp_error($response)) {
            // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- API request logging
            error_log($log_prefix . ' Response Error: ' . $response->get_error_message());
            if ($response->get_error_code()) {
                // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- API request logging
                error_log($log_prefix . ' Response Error Code: ' . $response->get_error_code());
            }
        } else {
            $response_code = wp_remote_retrieve_response_code($response);
            $response_body = wp_remote_retrieve_body($response);
            $response_headers = wp_remote_retrieve_headers($response);
            
            // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- API request logging
            error_log($log_prefix . ' Response Code: ' . $response_code);
            
            // Log response body (payload)
            if (!empty($response_body)) {
                // Try to decode and re-encode for better formatting
                $decoded = json_decode($response_body, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- API request logging
                    error_log($log_prefix . ' Response Body (JSON): ' . wp_json_encode($decoded, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
                } else {
                    // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- API request logging
                    error_log($log_prefix . ' Response Body (Raw): ' . $response_body);
                }
            } else {
                // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- API request logging
                error_log($log_prefix . ' Response Body: (empty)');
            }
            
            // Log response headers if available
            if (!empty($response_headers)) {
                // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- API request logging
                error_log($log_prefix . ' Response Headers: ' . wp_json_encode($response_headers->getAll(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
            }
        }
    }
}

