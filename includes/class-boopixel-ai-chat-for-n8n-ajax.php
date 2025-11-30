<?php
/**
 * AJAX handlers for BooPixel AI Chat Connect for n8n
 *
 * @package BooPixel_AI_Chat_For_N8n
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class BooPixel_AI_Chat_For_N8n_Ajax
 */
class BooPixel_AI_Chat_For_N8n_Ajax {
    
    /**
     * Database instance
     *
     * @var BooPixel_AI_Chat_For_N8n_Database
     */
    private $database;
    
    /**
     * API instance
     *
     * @var BooPixel_AI_Chat_For_N8n_API
     */
    private $api;
    
    /**
     * License instance
     *
     * @var BooPixel_AI_Chat_For_N8n_License
     */
    private $license;
    
    /**
     * Constructor
     *
     * @param BooPixel_AI_Chat_For_N8n_Database $database Database instance.
     * @param BooPixel_AI_Chat_For_N8n_API       $api API instance.
     */
    public function __construct($database, $api) {
        $this->database = $database;
        $this->api = $api;
        $this->license = new BooPixel_AI_Chat_For_N8n_License();
        
        // Register AJAX hooks
        add_action('wp_ajax_boopixel_ai_chat_for_n8n_send_message', array($this, 'send_message'));
        add_action('wp_ajax_nopriv_boopixel_ai_chat_for_n8n_send_message', array($this, 'send_message'));
        add_action('wp_ajax_boopixel_ai_chat_for_n8n_get_statistics', array($this, 'get_statistics'));
        add_action('wp_ajax_boopixel_ai_chat_for_n8n_get_conversations', array($this, 'ajax_get_conversations'));
        add_action('wp_ajax_boopixel_ai_chat_for_n8n_get_session_messages', array($this, 'ajax_get_session_messages'));
        add_action('wp_ajax_boopixel_ai_chat_for_n8n_get_sessions', array($this, 'ajax_get_sessions'));
        add_action('wp_ajax_boopixel_ai_chat_for_n8n_get_session_details', array($this, 'ajax_get_session_details'));
        add_action('wp_ajax_boopixel_ai_chat_for_n8n_export_session', array($this, 'ajax_export_session'));
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
        check_ajax_referer('boopixel-ai-chat-for-n8n-chat', 'nonce');
        
        $session_id = isset($_POST['sessionId']) ? sanitize_text_field(wp_unslash($_POST['sessionId'])) : '';
        $chat_input = isset($_POST['chatInput']) ? sanitize_text_field(wp_unslash($_POST['chatInput'])) : '';
        
        if (empty($chat_input)) {
            $this->send_error(boopixel_ai_chat_for_n8n_translate('empty_message', 'Empty message.'));
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
            $this->send_error(boopixel_ai_chat_for_n8n_translate('api_connection_error', 'Error connecting to the service. Please try again.'), $session_id);
            return;
        }
        
        // Extract response body
        $body = isset($response['body']) ? $response['body'] : '';
        $response_code = isset($response['response']['code']) ? $response['response']['code'] : 0;
        
        if ($response_code !== 200) {
            $this->send_error(boopixel_ai_chat_for_n8n_translate('api_connection_error', 'Error connecting to the service. Please try again.'), $session_id);
            return;
        }
        
        $data = json_decode($body, true);
        
        if (json_last_error() !== JSON_ERROR_NONE || !isset($data['output'])) {
            $this->send_error(boopixel_ai_chat_for_n8n_translate('server_response_error', 'Error processing server response.'), $session_id);
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
            wp_send_json_error(array('message' => boopixel_ai_chat_for_n8n_translate('no_permission', 'No permission.')));
            return;
        }
        
        check_ajax_referer('boopixel-ai-chat-for-n8n-statistics', 'nonce');
        
        // Statistics feature is available to all users
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified with check_ajax_referer
        $date_from = isset($_POST['date_from']) ? sanitize_text_field(wp_unslash($_POST['date_from'])) : current_time('Y-m-d');
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified with check_ajax_referer
        $date_to = isset($_POST['date_to']) ? sanitize_text_field(wp_unslash($_POST['date_to'])) : current_time('Y-m-d');
        
        wp_send_json_success(array(
            'summary' => array(
                '1day' => $this->database->get_interactions_count(1),
                '7days' => $this->database->get_interactions_count(7),
                '30days' => $this->database->get_interactions_count(30),
                '365days' => $this->database->get_interactions_count(365)
            ),
            'chart' => $this->database->get_chart_data($date_from, $date_to)
        ));
    }
    
    /**
     * Get sessions list AJAX handler
     *
     * @return void
     */
    public function ajax_get_sessions() {
        check_ajax_referer('boopixel-ai-chat-for-n8n-sessions', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => esc_html__('Insufficient permissions.', 'boopixel-ai-chat-for-n8n')));
            return;
        }
        
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified with check_ajax_referer
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified with check_ajax_referer
        $per_page = isset($_POST['per_page']) ? intval($_POST['per_page']) : 20;
        
        $offset = ($page - 1) * $per_page;
        
        $sessions = $this->database->get_all_sessions($per_page, $offset);
        $total = $this->database->get_sessions_count();
        
        wp_send_json_success(array(
            'sessions' => $sessions,
            'total' => $total,
            'page' => $page,
            'per_page' => $per_page,
            'total_pages' => ceil($total / $per_page)
        ));
    }
    
    /**
     * Get conversation history AJAX handler
     */
    public function ajax_get_conversations() {
        check_ajax_referer('boopixel-ai-chat-for-n8n-statistics', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => esc_html__('No permission.', 'boopixel-ai-chat-for-n8n')));
            return;
        }
        
        // Statistics feature is available to all users
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified with check_ajax_referer
        $date_from = isset($_POST['date_from']) ? sanitize_text_field(wp_unslash($_POST['date_from'])) : '';
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified with check_ajax_referer
        $date_to = isset($_POST['date_to']) ? sanitize_text_field(wp_unslash($_POST['date_to'])) : '';
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified with check_ajax_referer
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified with check_ajax_referer
        $per_page = isset($_POST['per_page']) ? intval($_POST['per_page']) : 20;
        
        $offset = ($page - 1) * $per_page;
        
        $conversations = $this->database->get_conversations($date_from, $date_to, $per_page, $offset);
        $total = $this->database->get_conversations_count($date_from, $date_to);
        
        wp_send_json_success(array(
            'conversations' => $conversations,
            'total' => $total,
            'page' => $page,
            'per_page' => $per_page,
            'total_pages' => ceil($total / $per_page)
        ));
    }
    
    /**
     * Get messages for a specific session AJAX handler
     */
    public function ajax_get_session_messages() {
        check_ajax_referer('boopixel-ai-chat-for-n8n-statistics', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => esc_html__('No permission.', 'boopixel-ai-chat-for-n8n')));
            return;
        }
        
        // Statistics feature is available to all users
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified with check_ajax_referer
        $session_id = isset($_POST['session_id']) ? sanitize_text_field(wp_unslash($_POST['session_id'])) : '';
        
        if (empty($session_id)) {
            wp_send_json_error(array('message' => esc_html__('Session ID is required.', 'boopixel-ai-chat-for-n8n')));
            return;
        }
        
        $messages = $this->database->get_session_messages($session_id);
        
        wp_send_json_success(array(
            'messages' => $messages
        ));
    }
    
    /**
     * Get session details (messages) for sessions page AJAX handler
     *
     * @return void
     */
    public function ajax_get_session_details() {
        check_ajax_referer('boopixel-ai-chat-for-n8n-sessions', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => esc_html__('Insufficient permissions.', 'boopixel-ai-chat-for-n8n')));
            return;
        }
        
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified with check_ajax_referer
        $session_id = isset($_POST['session_id']) ? sanitize_text_field(wp_unslash($_POST['session_id'])) : '';
        
        if (empty($session_id)) {
            wp_send_json_error(array('message' => esc_html__('Session ID is required.', 'boopixel-ai-chat-for-n8n')));
            return;
        }
        
        $messages = $this->database->get_session_messages($session_id);
        
        wp_send_json_success(array(
            'messages' => $messages,
            'session_id' => $session_id
        ));
    }
    
    /**
     * Export session data in JSON or CSV format
     */
    public function ajax_export_session() {
        check_ajax_referer('boopixel-ai-chat-for-n8n-sessions', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => esc_html__('Insufficient permissions.', 'boopixel-ai-chat-for-n8n')));
            return;
        }
        
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified with check_ajax_referer
        $session_id = isset($_POST['session_id']) ? sanitize_text_field(wp_unslash($_POST['session_id'])) : '';
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified with check_ajax_referer
        $format = isset($_POST['format']) ? sanitize_text_field(wp_unslash($_POST['format'])) : 'json';
        
        if (empty($session_id)) {
            wp_send_json_error(array('message' => esc_html__('Session ID is required.', 'boopixel-ai-chat-for-n8n')));
            return;
        }
        
        if (!in_array($format, array('json', 'csv'), true)) {
            wp_send_json_error(array('message' => esc_html__('Invalid format.', 'boopixel-ai-chat-for-n8n')));
            return;
        }
        
        $messages = $this->database->get_session_messages($session_id);
        
        if (empty($messages)) {
            wp_send_json_error(array('message' => esc_html__('No messages found for this session.', 'boopixel-ai-chat-for-n8n')));
            return;
        }
        
        // Prepare session data
        $session_data = array(
            'session_id' => $session_id,
            'export_date' => current_time('mysql'),
            'total_messages' => count($messages),
            'messages' => $messages
        );
        
        if ($format === 'json') {
            $content = wp_json_encode($session_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            $filename = 'session-' . substr($session_id, 0, 20) . '-' . gmdate('Y-m-d') . '.json';
            $mime_type = 'application/json';
        } else {
            // CSV format
            $csv_content = array();
            
            // Add header
            $csv_content[] = array('Session ID', 'Message ID', 'Type', 'Message', 'Date');
            
            // Add messages
            foreach ($messages as $message) {
                $csv_content[] = array(
                    $session_id,
                    $message['id'],
                    $message['message_type'],
                    $message['message'],
                    $message['interaction_date']
                );
            }
            
            // Convert to CSV string
            $content = '';
            foreach ($csv_content as $row) {
                $escaped_row = array();
                foreach ($row as $field) {
                    // Escape quotes and wrap in quotes if contains comma, newline, or quote
                    $field = str_replace('"', '""', $field);
                    if (strpos($field, ',') !== false || strpos($field, "\n") !== false || strpos($field, '"') !== false) {
                        $field = '"' . $field . '"';
                    }
                    $escaped_row[] = $field;
                }
                $content .= implode(',', $escaped_row) . "\n";
            }
            
            $filename = 'session-' . substr($session_id, 0, 20) . '-' . gmdate('Y-m-d') . '.csv';
            $mime_type = 'text/csv';
        }
        
        wp_send_json_success(array(
            'content' => $content,
            'filename' => $filename,
            'mime_type' => $mime_type
        ));
    }
}
