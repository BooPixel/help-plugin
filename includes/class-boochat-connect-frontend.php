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
        $gradient = sprintf('linear-gradient(135deg, %s 0%%, %s 100%%)', esc_attr($settings['primary_color']), esc_attr($settings['secondary_color']));
        ?>
        <style id="boochat-connect-custom-css" data-version="<?php echo esc_attr($cache_version); ?>">
            .boochat-connect-popup-content,
            .boochat-connect-chat-header,
            .boochat-connect-chat-send {
                background: <?php echo esc_attr($gradient); ?> !important;
            }
            
            .boochat-connect-chat-window,
            .boochat-connect-chat-body {
                background: <?php echo esc_attr($settings['chat_bg_color']); ?> !important;
            }
            
            .boochat-connect-chat-window,
            .boochat-connect-chat-message p,
            .boochat-connect-chat-input {
                font-family: <?php echo esc_attr($settings['font_family']); ?> !important;
                font-size: <?php echo esc_attr($settings['font_size']); ?> !important;
            }
            
            .boochat-connect-chat-message-admin p,
            .boochat-connect-chat-message-system,
            .boochat-connect-chat-message-system p {
                color: #333 !important;
            }
            
            .boochat-connect-chat-message-system {
                background: #e9ecef !important;
            }
            
            .boochat-connect-chat-message-user,
            .boochat-connect-chat-message-user p {
                color: #e9ecef !important;
            }
        </style>
        <?php
    }
    
    /**
     * Load view template
     *
     * @param string $view_name View file name (without .php).
     * @param array  $vars      Variables to pass to the view.
     */
    private function load_view($view_name, $vars = array()) {
        $view_file = BOOCHAT_CONNECT_DIR . 'includes/views/' . $view_name . '.php';
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
