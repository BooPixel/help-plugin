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
 * This function maps translation keys to English text (msgid) and uses
 * WordPress gettext functions following WordPress I18n standards.
 *
 * @param string $key Translation key (maps to English text).
 * @param string $default Default value if key not found (used as fallback msgid).
 * @return string Translated string
 */
if (!function_exists('boochat_connect_translate')) {
    function boochat_connect_translate($key, $default = '') {
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
        $configured_language = get_option('boochat_connect_language', '');
        
        // Use WordPress gettext function (locale is already set by force_plugin_locale filter)
        // phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText -- $msgid is dynamically mapped from translation keys
        return esc_html__($msgid, 'boochat-connect');
    }
}

