<?php
/**
 * Statistics functionality for BooChat Connect
 *
 * @package BooChat_Connect
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class BooChat_Connect_Statistics
 */
class BooChat_Connect_Statistics {
    
    /**
     * Database instance
     *
     * @var BooChat_Connect_Database
     */
    private $database;
    
    /**
     * Constructor
     *
     * @param BooChat_Connect_Database $database Database instance.
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
        $view_file = BOOCHAT_CONNECT_DIR . 'includes/views/' . $view_name . '.php';
        if (file_exists($view_file)) {
            extract($vars);
            include $view_file;
        }
    }
    
    /**
     * Render statistics page
     */
    public function render_page() {
        $ajax_url = admin_url('admin-ajax.php');
        $nonce = wp_create_nonce('boochat-connect-statistics');
        $today = current_time('Y-m-d');
        
        $this->load_view('admin-statistics', array(
            'ajax_url' => $ajax_url,
            'nonce' => $nonce,
            'today' => $today,
        ));
    }
}
