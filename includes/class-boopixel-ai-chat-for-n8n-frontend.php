<?php
/**
 * Frontend functionality for BooPixel AI Chat Connect for n8n
 *
 * @package BooPixel_AI_Chat_For_N8n
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class BooPixel_AI_Chat_For_N8n_Frontend
 */
class BooPixel_AI_Chat_For_N8n_Frontend {
    
    /**
     * Settings instance
     *
     * @var BooPixel_AI_Chat_For_N8n_Settings
     */
    private $settings;
    
    /**
     * Constructor
     *
     * @param BooPixel_AI_Chat_For_N8n_Settings $settings Settings instance.
     */
    public function __construct($settings) {
        $this->settings = $settings;
        
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_action('wp_footer', array($this, 'render_chat_widget'));
        add_action('wp_head', array($this, 'output_custom_css'));
    }
    
    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        // Only load on frontend, not in admin
        if (is_admin()) {
            return;
        }
        
        $version = boopixel_ai_chat_for_n8n_get_version();
        
        wp_enqueue_style(
            'boopixel-ai-chat-for-n8n-chat-style',
            BOOPIXEL_AI_CHAT_FOR_N8N_URL . 'assets/css/chat-style.css',
            array(),
            $version
        );
        
        wp_enqueue_script(
            'boopixel-ai-chat-for-n8n-chat-script',
            BOOPIXEL_AI_CHAT_FOR_N8N_URL . 'assets/js/chat-script.js',
            array('jquery'),
            $version,
            true
        );
        
        // Localize script with AJAX data
        wp_localize_script('boopixel-ai-chat-for-n8n-chat-script', 'boopixelAiChatForN8nAjax', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('boopixel-ai-chat-for-n8n-chat'),
            'loadingText' => boopixel_ai_chat_for_n8n_translate('waiting_response'),
            'errorText' => boopixel_ai_chat_for_n8n_translate('error_send_message')
        ));
    }
    
    /**
     * Output custom CSS on frontend using wp_add_inline_style
     */
    public function output_custom_css() {
        if (is_admin()) {
            return;
        }
        
        $settings = $this->settings->get_customization_settings();
        $cache_version = boopixel_ai_chat_for_n8n_get_version();
        $gradient = sprintf('linear-gradient(135deg, %s 0%%, %s 100%%)', esc_attr($settings['primary_color']), esc_attr($settings['secondary_color']));
        
        // Build CSS string
        $custom_css = sprintf(
            '.boopixel-ai-chat-for-n8n-popup-content,
            .boopixel-ai-chat-for-n8n-chat-header,
            .boopixel-ai-chat-for-n8n-chat-send {
                background: %s !important;
            }
            
            .boopixel-ai-chat-for-n8n-chat-window,
            .boopixel-ai-chat-for-n8n-chat-body {
                background: %s !important;
            }
            
            .boopixel-ai-chat-for-n8n-chat-window,
            .boopixel-ai-chat-for-n8n-chat-message p,
            .boopixel-ai-chat-for-n8n-chat-input {
                font-family: %s !important;
                font-size: %s !important;
            }
            
            .boopixel-ai-chat-for-n8n-chat-message-admin p,
            .boopixel-ai-chat-for-n8n-chat-message-system,
            .boopixel-ai-chat-for-n8n-chat-message-system p {
                color: #333 !important;
            }
            
            .boopixel-ai-chat-for-n8n-chat-message-system {
                background: #e9ecef !important;
            }
            
            .boopixel-ai-chat-for-n8n-chat-message-user,
            .boopixel-ai-chat-for-n8n-chat-message-user p {
                color: #e9ecef !important;
            }',
            esc_attr($gradient),
            esc_attr($settings['chat_bg_color']),
            esc_attr($settings['font_family']),
            esc_attr($settings['font_size'])
        );
        
        // Add inline style to the enqueued stylesheet
        wp_add_inline_style('boopixel-ai-chat-for-n8n-chat-style', $custom_css);
    }
    
    /**
     * Load view template
     *
     * @param string $view_name View file name (without .php).
     * @param array  $vars      Variables to pass to the view.
     */
    private function load_view($view_name, $vars = array()) {
        $view_file = BOOPIXEL_AI_CHAT_FOR_N8N_DIR . 'includes/views/' . $view_name . '.php';
        if (file_exists($view_file)) {
            extract($vars);
            include $view_file;
        }
    }
    
    /**
     * Render chat widget in footer
     */
    public function render_chat_widget() {
        // Only render on frontend, not in admin
        if (is_admin()) {
            return;
        }
        
        $settings = $this->settings->get_customization_settings();
        
        $this->load_view('frontend-chat', array(
            'settings' => $settings,
        ));
    }
}
