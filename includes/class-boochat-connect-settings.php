<?php
/**
 * Settings management for BooChat Connect
 *
 * @package BooChat_Connect
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class BooChat_Connect_Settings
 */
class BooChat_Connect_Settings {
    
    /**
     * Get currently configured language (returns empty if using WordPress)
     *
     * @return string Language code (en, pt, es) or empty string to use WordPress
     */
    public function get_language() {
        return get_option('boochat_connect_language', '');
    }
    
    /**
     * Get effective language (configured or from WordPress)
     *
     * @return string Language code (en, pt, es)
     */
    public function get_effective_language() {
        $configured_language = $this->get_language();
        
        if (empty($configured_language)) {
            // If no configuration, use WordPress locale
            $wp_locale = get_locale();
            return boochat_connect_get_language_from_locale($wp_locale);
        }
        
        return $configured_language;
    }
    
    /**
     * Get customization settings
     *
     * @return array Customization settings
     */
    public function get_customization_settings() {
        $language = $this->get_effective_language();
        
        return array(
            'chat_name' => get_option('boochat_connect_chat_name', boochat_connect_translate('chat_name_default')),
            'welcome_message' => get_option('boochat_connect_welcome_message', boochat_connect_translate('welcome_message_default')),
            'primary_color' => get_option('boochat_connect_primary_color', '#1B8EF0'),
            'secondary_color' => get_option('boochat_connect_secondary_color', '#1B5D98'),
            'chat_bg_color' => get_option('boochat_connect_chat_bg_color', '#ffffff'),
            'text_color' => get_option('boochat_connect_text_color', '#333333'),
            'font_family' => get_option('boochat_connect_font_family', 'Arial, sans-serif'),
            'font_size' => get_option('boochat_connect_font_size', '14px'),
            'language' => $language,
        );
    }
    
    /**
     * Clear cache
     */
    public function clear_cache() {
        // Update cache version to force asset reload
        $new_version = time();
        update_option('boochat_connect_cache_version', $new_version);
        
        // Clear cache of popular plugins
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }
        
        // Clear W3 Total Cache
        if (function_exists('w3tc_flush_all')) {
            w3tc_flush_all();
        }
        
        // Clear WP Super Cache
        if (function_exists('wp_cache_clear_cache')) {
            wp_cache_clear_cache();
        }
        
        // Clear WP Rocket
        if (function_exists('rocket_clean_domain')) {
            rocket_clean_domain();
        }
    }
}
