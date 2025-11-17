<?php
/**
 * AJAX handlers for BooChat Connect
 *
 * @package BooChat_Connect
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class BooChat_Connect_Ajax
 */
class BooChat_Connect_Ajax {
    
    /**
     * Database instance
     *
     * @var BooChat_Connect_Database
     */
    private $database;
    
    /**
     * API instance
     *
     * @var BooChat_Connect_API
     */
    private $api;
    
    /**
     * Constructor
     *
     * @param BooChat_Connect_Database $database Database instance.
     * @param BooChat_Connect_API       $api API instance.
     */
    public function __construct($database, $api) {
        $this->database = $database;
        $this->api = $api;
        
        // Register AJAX hooks
        add_action('wp_ajax_boochat_connect_send_message', array($this, 'send_message'));
        add_action('wp_ajax_nopriv_boochat_connect_send_message', array($this, 'send_message'));
        add_action('wp_ajax_boochat_connect_get_statistics', array($this, 'get_statistics'));
    }
    
    /**
     * Send error response
     *
     * @param string $message Error message.
     * @param string $session_id Session ID.
     */
    private function send_error($message, $session_id = '') {
        $response = array('message' => $message);
        if (!empty($session_id)) {
            $response['sessionId'] = $session_id;
        }
        wp_send_json_error($response);
    }
    
    /**
     * AJAX handler to send message
     */
    public function send_message() {
        // Verify nonce for security
        check_ajax_referer('boochat-connect-chat', 'nonce');
        
        $session_id = isset($_POST['sessionId']) ? sanitize_text_field(wp_unslash($_POST['sessionId'])) : '';
        $chat_input = isset($_POST['chatInput']) ? sanitize_text_field(wp_unslash($_POST['chatInput'])) : '';
        
        if (empty($chat_input)) {
            $this->send_error(boochat_connect_translate('empty_message', 'Empty message.'));
            return;
        }
        
        // If no sessionId, generate a new one
        if (empty($session_id)) {
            $session_id = $this->api->generate_session_id();
        }
        
        // Log interaction for statistics (with message content)
        $this->database->log_interaction($session_id, $chat_input, 'user');
        
        // Make request to external API
        $response = $this->api->send_message($session_id, $chat_input);
        
        if (is_wp_error($response)) {
            $this->send_error(boochat_connect_translate('api_connection_error', 'Error connecting to the service. Please try again.'), $session_id);
            return;
        }
        
        // Extract response body
        $body = isset($response['body']) ? $response['body'] : '';
        $response_code = isset($response['response']['code']) ? $response['response']['code'] : 0;
        
        if ($response_code !== 200) {
            $this->send_error(boochat_connect_translate('api_connection_error', 'Error connecting to the service. Please try again.'), $session_id);
            return;
        }
        
        $data = json_decode($body, true);
        
        if (json_last_error() !== JSON_ERROR_NONE || !isset($data['output'])) {
            $this->send_error(boochat_connect_translate('server_response_error', 'Error processing server response.'), $session_id);
            return;
        }
        
        // Log robot response to database
        $this->database->log_interaction($session_id, $data['output'], 'robot');
        
        wp_send_json_success(array(
            'message' => $data['output'],
            'sessionId' => $session_id
        ));
    }
    
    /**
     * AJAX handler to get statistics
     */
    public function get_statistics() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => boochat_connect_translate('no_permission', 'No permission.')));
            return;
        }
        
        check_ajax_referer('boochat-connect-statistics', 'nonce');
        
        $date_from = isset($_POST['date_from']) ? sanitize_text_field(wp_unslash($_POST['date_from'])) : current_time('Y-m-d');
        $date_to = isset($_POST['date_to']) ? sanitize_text_field(wp_unslash($_POST['date_to'])) : current_time('Y-m-d');
        
        wp_send_json_success(array(
            'summary' => array(
                '1day' => $this->database->get_interactions_count(1),
                '7days' => $this->database->get_interactions_count(7),
                '30days' => $this->database->get_interactions_count(30)
            ),
            'chart' => $this->database->get_chart_data($date_from, $date_to),
            'calendar' => $this->database->get_calendar_data()
        ));
    }
}
