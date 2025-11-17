<?php
/**
 * Frontend functionality for BooChat Connect
 *
 * @package BooChat_Connect
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class BooChat_Connect_Frontend
 */
class BooChat_Connect_Frontend {
    
    /**
     * Settings instance
     *
     * @var BooChat_Connect_Settings
     */
    private $settings;
    
    /**
     * Constructor
     *
     * @param BooChat_Connect_Settings $settings Settings instance.
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
        
        $version = boochat_connect_get_version();
        
        wp_enqueue_style(
            'boochat-connect-chat-style',
            BOOCHAT_CONNECT_URL . 'assets/css/chat-style.css',
            array(),
            $version
        );
        
        wp_enqueue_script(
            'boochat-connect-chat-script',
            BOOCHAT_CONNECT_URL . 'assets/js/chat-script.js',
            array('jquery'),
            $version,
            true
        );
        
        // Localize script with AJAX data
        wp_localize_script('boochat-connect-chat-script', 'boochatConnectAjax', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('boochat-connect-chat'),
            'loadingText' => boochat_connect_translate('waiting_response'),
            'errorText' => boochat_connect_translate('error_send_message')
        ));
    }
    
    /**
     * Output custom CSS on frontend
     */
    public function output_custom_css() {
        if (is_admin()) {
            return;
        }
        
        $settings = $this->settings->get_customization_settings();
        $cache_version = boochat_connect_get_version();
        ?>
        <style id="boochat-connect-custom-css" data-version="<?php echo esc_attr($cache_version); ?>">
            .boochat-connect-popup-content {
                background: linear-gradient(135deg, <?php echo esc_attr($settings['primary_color']); ?> 0%, <?php echo esc_attr($settings['secondary_color']); ?> 100%) !important;
            }
            
            .boochat-connect-chat-header {
                background: linear-gradient(135deg, <?php echo esc_attr($settings['primary_color']); ?> 0%, <?php echo esc_attr($settings['secondary_color']); ?> 100%) !important;
            }
            
            .boochat-connect-chat-window {
                background: <?php echo esc_attr($settings['chat_bg_color']); ?> !important;
                font-family: <?php echo esc_attr($settings['font_family']); ?> !important;
                font-size: <?php echo esc_attr($settings['font_size']); ?> !important;
            }
            
            .boochat-connect-chat-body {
                background: <?php echo esc_attr($settings['chat_bg_color']); ?> !important;
            }
            
            .boochat-connect-chat-message p {
                font-family: <?php echo esc_attr($settings['font_family']); ?> !important;
                font-size: <?php echo esc_attr($settings['font_size']); ?> !important;
            }
            
            .boochat-connect-chat-message-admin p {
                color: #333 !important;
            }
            
            .boochat-connect-chat-message-system {
                background: #e9ecef !important;
                color: #333 !important;
            }
            
            .boochat-connect-chat-message-system p {
                color: #333 !important;
            }
            
            .boochat-connect-chat-send {
                background: linear-gradient(135deg, <?php echo esc_attr($settings['primary_color']); ?> 0%, <?php echo esc_attr($settings['secondary_color']); ?> 100%) !important;
            }
            
            .boochat-connect-chat-input {
                font-family: <?php echo esc_attr($settings['font_family']); ?> !important;
                font-size: <?php echo esc_attr($settings['font_size']); ?> !important;
            }
            
            .boochat-connect-chat-message-user {
                color: #e9ecef !important;
            }
            
            .boochat-connect-chat-message-user p {
                color: #e9ecef !important;
            }
        </style>
        <?php
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
        ?>
        <div id="boochat-connect-popup" class="boochat-connect-popup">
            <div class="boochat-connect-popup-content">
                <div class="boochat-connect-popup-header">
                    <span class="boochat-connect-popup-icon">💬</span>
                    <span class="boochat-connect-popup-text"><?php echo esc_html(boochat_connect_translate('need_help')); ?></span>
                </div>
                <button class="boochat-connect-popup-close" aria-label="<?php echo esc_attr(boochat_connect_translate('close')); ?>">&times;</button>
            </div>
        </div>

        <div id="boochat-connect-chat-window" class="boochat-connect-chat-window">
            <div class="boochat-connect-chat-header">
                <div class="boochat-connect-chat-title">
                    <span class="boochat-connect-chat-icon">💬</span>
                    <span><?php echo esc_html($settings['chat_name']); ?></span>
                </div>
                <button class="boochat-connect-chat-close" aria-label="<?php echo esc_attr(boochat_connect_translate('close_chat')); ?>">&times;</button>
            </div>
            <div class="boochat-connect-chat-body">
                <div class="boochat-connect-chat-messages" id="boochat-connect-chat-messages">
                    <div class="boochat-connect-chat-message boochat-connect-chat-message-system">
                        <p><?php echo esc_html($settings['welcome_message']); ?></p>
                    </div>
                </div>
            </div>
            <div class="boochat-connect-chat-footer">
                <form id="boochat-connect-chat-form" class="boochat-connect-chat-form">
                    <input 
                        type="text" 
                        id="boochat-connect-chat-input" 
                        class="boochat-connect-chat-input" 
                        placeholder="<?php echo esc_attr(boochat_connect_translate('type_message')); ?>"
                        autocomplete="off"
                    >
                    <button type="submit" class="boochat-connect-chat-send">
                        <span><?php echo esc_html(boochat_connect_translate('send')); ?></span>
                    </button>
                </form>
            </div>
        </div>
        <?php
    }
}
