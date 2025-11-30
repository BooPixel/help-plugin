<?php
/**
 * API integration for BooPixel AI Chat Connect for n8n
 *
 * @package BooPixel_AI_Chat_For_N8n
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class BooPixel_AI_Chat_For_N8n_API
 */
class BooPixel_AI_Chat_For_N8n_API {
    
    /**
     * Get API URL from options
     *
     * @return string API URL or empty string
     */
    public function get_api_url() {
        return get_option('boopixel_ai_chat_for_n8n_api_url', '');
    }
    
    /**
     * Send message to external API
     *
     * @param string $session_id Unique session ID.
     * @param string $chat_input User message.
     * @return array|WP_Error API response or error
     */
    public function send_message($session_id, $chat_input) {
        $url = $this->get_api_url();
        
        if (empty($url)) {
            return new WP_Error('no_api_url', boopixel_ai_chat_for_n8n_translate('api_not_configured_error', 'API URL not configured. Configure in BooPixel AI Chat Connect for n8n > Settings.'));
        }
        
        $request_body = array(
            'sessionId' => $session_id,
            'action' => 'sendMessage',
            'chatInput' => $chat_input
        );
        
        $headers = array(
            'Content-Type' => 'application/json',
        );
        
        $response = wp_remote_post($url, array(
            'body' => json_encode($request_body),
            'headers' => $headers,
            'timeout' => 30,
            'sslverify' => true,
        ));
        
        boopixel_ai_chat_for_n8n_log_api_request('send_message', $url, $request_body, $headers, $response);
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        
        if ($response_code !== 200) {
            return new WP_Error('http_error', sprintf(boopixel_ai_chat_for_n8n_translate('http_error_message', 'HTTP %d: %s'), $response_code, $body));
        }
        
        return array(
            'body' => $body,
            'response' => array(
                'code' => $response_code
            )
        );
    }
    
    /**
     * Generate unique session ID
     *
     * @return string Session ID
     */
    public function generate_session_id() {
        return bin2hex(random_bytes(16));
    }
}
