<?php
/**
 * Statistics functionality for BooPixel AI Chat Connect for n8n
 *
 * @package BooPixel_AI_Chat_For_N8n
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class BooPixel_AI_Chat_For_N8n_Statistics
 */
class BooPixel_AI_Chat_For_N8n_Statistics {
    
    /**
     * Database instance
     *
     * @var BooPixel_AI_Chat_For_N8n_Database
     */
    private $database;
    
    /**
     * Constructor
     *
     * @param BooPixel_AI_Chat_For_N8n_Database $database Database instance.
     */
    public function __construct($database) {
        $this->database = $database;
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
     * Render statistics page
     */
    public function render_page() {
        // Statistics page is available to all users
        $ajax_url = admin_url('admin-ajax.php');
        $nonce = wp_create_nonce('boopixel-ai-chat-for-n8n-statistics');
        $today = current_time('Y-m-d');
        $seven_days_ago = gmdate('Y-m-d', strtotime('-7 days', current_time('timestamp')));
        
        $this->load_view('admin-statistics', array(
            'ajax_url' => $ajax_url,
            'nonce' => $nonce,
            'today' => $today,
            'seven_days_ago' => $seven_days_ago,
        ));
    }
}
