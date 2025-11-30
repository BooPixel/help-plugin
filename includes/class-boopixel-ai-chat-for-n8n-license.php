<?php
/**
 * License management for BooPixel AI Chat Connect for n8n PRO
 *
 * @package BooPixel_AI_Chat_For_N8n
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class BooPixel_AI_Chat_For_N8n_License
 */
class BooPixel_AI_Chat_For_N8n_License {
    
    /**
     * License key option name
     *
     * @var string
     */
    private $license_key_option = 'boopixel_ai_chat_for_n8n_license_key';
    
    /**
     * License status option name
     *
     * @var string
     */
    private $license_status_option = 'boopixel_ai_chat_for_n8n_license_status';
    
    /**
     * License expires option name
     *
     * @var string
     */
    private $license_expires_option = 'boopixel_ai_chat_for_n8n_license_expires';
    
    /**
     * License last check option name
     *
     * @var string
     */
    private $license_last_check_option = 'boopixel_ai_chat_for_n8n_license_last_check';
    
    /**
     * API URL for license verification
     *
     * @var string
     */
    private $api_url = 'https://chat.boopixel.com/api/v1/license/';
    
    /**
     * API key option name
     *
     * @var string
     */
    private $api_key_option = 'boopixel_ai_chat_for_n8n_api_key';
    
    /**
     * Get API URL (allows customization)
     *
     * @return string API URL.
     */
    private function get_api_url() {
        $custom_url = get_option('boopixel_ai_chat_for_n8n_license_api_url', '');
        return !empty($custom_url) ? $custom_url : $this->api_url;
    }
    
    /**
     * Get API headers with authentication
     *
     * @return array Headers array with X-API-Key if configured.
     */
    private function get_api_headers() {
        $headers = array(
            'Content-Type' => 'application/json'
        );
        
        $api_key = get_option($this->api_key_option, '');
        if (!empty($api_key)) {
            $headers['X-API-Key'] = $api_key;
        }
        
        return $headers;
    }
    
    /**
     * Check if user has PRO license
     *
     * @param bool $force_check Force API check even if cache is valid.
     * @return bool True if PRO license is active and valid.
     */
    public function is_pro($force_check = false) {
        $license_key = get_option($this->license_key_option, '');
        if (empty($license_key)) {
            return false;
        }
        
        $cache_key = 'boopixel_ai_chat_for_n8n_pro_status';
        $cached_status = get_transient($cache_key);
        
        if (!$force_check && $cached_status !== false) {
            return (bool) $cached_status;
        }
        
        $is_valid = $this->verify_license();
        set_transient($cache_key, $is_valid ? 1 : 0, HOUR_IN_SECONDS);
        
        return $is_valid;
    }
    
    /**
     * Get license key
     *
     * @return string License key or empty string.
     */
    public function get_license_key() {
        return get_option($this->license_key_option, '');
    }
    
    /**
     * Get license status
     *
     * @return string License status.
     */
    public function get_license_status() {
        return get_option($this->license_status_option, 'invalid');
    }
    
    /**
     * Activate license
     *
     * @param string $license_key License key to activate.
     * @return array Response array with success status and message.
     */
    public function activate_license($license_key) {
        if (empty($license_key)) {
            return array(
                'success' => false,
                'message' => esc_html__('License key is required.', 'boopixel-ai-chat-for-n8n')
            );
        }
        
        $api_url = $this->get_api_url() . 'activate';
        $request_body = array(
            'license_key' => sanitize_text_field($license_key),
            'site_url' => esc_url_raw(home_url()),
            'plugin_version' => BOOPIXEL_AI_CHAT_FOR_N8N_VERSION
        );
        
        $headers = array(
            'Content-Type' => 'application/json'
        );
        
        $response = wp_remote_post($api_url, array(
            'timeout' => 30,
            'headers' => $headers,
            'body' => wp_json_encode($request_body)
        ));
        
        boopixel_ai_chat_for_n8n_log_api_request('activate', $api_url, $request_body, $headers, $response);
        
        if (is_wp_error($response)) {
            return array(
                'success' => false,
                'message' => esc_html__('Connection error. Please try again later.', 'boopixel-ai-chat-for-n8n')
            );
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
        $data = json_decode($response_body, true);
        
        if ($response_code !== 200) {
            $error_message = esc_html__('Server error. Please try again later.', 'boopixel-ai-chat-for-n8n');
            if (isset($data['detail'])) {
                $error_message = esc_html($data['detail']);
            } elseif (isset($data['message'])) {
                $error_message = esc_html($data['message']);
            }
            return array(
                'success' => false,
                'message' => $error_message
            );
        }
        
        if (isset($data['success']) && $data['success'] === true) {
            update_option($this->license_key_option, sanitize_text_field($license_key));
            update_option($this->license_status_option, 'valid');
            update_option($this->license_expires_option, isset($data['expires']) ? intval($data['expires']) : (time() + YEAR_IN_SECONDS));
            update_option($this->license_last_check_option, time());
            delete_transient('boopixel_ai_chat_for_n8n_pro_status');
            
            return array(
                'success' => true,
                'message' => esc_html__('License activated successfully!', 'boopixel-ai-chat-for-n8n')
            );
        }
        return array(
            'success' => false,
            'message' => isset($data['message']) ? esc_html($data['message']) : esc_html__('Invalid license key.', 'boopixel-ai-chat-for-n8n')
        );
    }
    
    /**
     * Verify license with API
     *
     * @return bool True if license is valid.
     */
    public function verify_license() {
        $license_key = get_option($this->license_key_option);
        if (empty($license_key)) {
            update_option($this->license_status_option, 'invalid');
            return false;
        }
        
        $api_url = $this->get_api_url() . 'verify';
        $request_body = array(
            'license_key' => sanitize_text_field($license_key),
            'site_url' => esc_url_raw(home_url())
        );
        
        $headers = array(
            'Content-Type' => 'application/json'
        );
        
        $response = wp_remote_post($api_url, array(
            'timeout' => 15,
            'headers' => $headers,
            'body' => wp_json_encode($request_body)
        ));
        
        boopixel_ai_chat_for_n8n_log_api_request('verify', $api_url, $request_body, $headers, $response);
        
        if (is_wp_error($response)) {
            $cached_status = get_option($this->license_status_option, 'invalid');
            return ($cached_status === 'valid' && get_option($this->license_expires_option, 0) > time());
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
        $data = json_decode($response_body, true);
        
        if ($response_code !== 200) {
            $cached_status = get_option($this->license_status_option, 'invalid');
            return ($cached_status === 'valid' && get_option($this->license_expires_option, 0) > time());
        }
        
        if (isset($data['status'])) {
            $status = sanitize_text_field($data['status']);
            update_option($this->license_status_option, $status);
            
            if (isset($data['expires'])) {
                update_option($this->license_expires_option, intval($data['expires']));
            }
            
            update_option($this->license_last_check_option, time());
            
            $is_valid = ($status === 'valid' && get_option($this->license_expires_option, 0) > time());
            set_transient('boopixel_ai_chat_for_n8n_pro_status', $is_valid ? 1 : 0, HOUR_IN_SECONDS);
            
            return $is_valid;
        }
        
        $cached_status = get_option($this->license_status_option, 'invalid');
        return ($cached_status === 'valid' && get_option($this->license_expires_option, 0) > time());
    }
    
    /**
     * Deactivate license
     *
     * @return array Response array with success status and message.
     */
    public function deactivate_license() {
        $license_key = get_option($this->license_key_option);
        if (empty($license_key)) {
            return array(
                'success' => false,
                'message' => esc_html__('No license key found.', 'boopixel-ai-chat-for-n8n')
            );
        }
        
        $api_url = $this->get_api_url() . 'deactivate';
        $request_body = array(
            'license_key' => sanitize_text_field($license_key),
            'site_url' => esc_url_raw(home_url())
        );
        
        $headers = array(
            'Content-Type' => 'application/json'
        );
        
        $response = wp_remote_post($api_url, array(
            'timeout' => 30,
            'headers' => $headers,
            'body' => wp_json_encode($request_body)
        ));
        
        boopixel_ai_chat_for_n8n_log_api_request('deactivate', $api_url, $request_body, $headers, $response);
        
        delete_option($this->license_key_option);
        delete_option($this->license_status_option);
        delete_option($this->license_expires_option);
        delete_option($this->license_last_check_option);
        delete_transient('boopixel_ai_chat_for_n8n_pro_status');
        
        if (is_wp_error($response)) {
            return array(
                'success' => true,
                'message' => esc_html__('License deactivated locally. API connection error.', 'boopixel-ai-chat-for-n8n')
            );
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code !== 200) {
            return array(
                'success' => true,
                'message' => esc_html__('License deactivated locally.', 'boopixel-ai-chat-for-n8n')
            );
        }
        
        return array(
            'success' => true,
            'message' => esc_html__('License deactivated successfully.', 'boopixel-ai-chat-for-n8n')
        );
    }
    
    /**
     * Request checkout URL from API
     *
     * @return array Response with checkout URL and session ID.
     */
    public function request_checkout_url() {
        $api_url = $this->get_api_url() . 'create-checkout';
        $return_url = admin_url('admin.php?page=boopixel-ai-chat-for-n8n-pro&payment=success&session_id={CHECKOUT_SESSION_ID}');
        $cancel_url = admin_url('admin.php?page=boopixel-ai-chat-for-n8n-pro&payment=cancel');
        
        $request_body = array(
            'site_url' => esc_url_raw(home_url()),
            'plugin_version' => BOOPIXEL_AI_CHAT_FOR_N8N_VERSION,
            'return_url' => $return_url,
            'cancel_url' => $cancel_url
        );
        
        $headers = array(
            'Content-Type' => 'application/json'
        );
        
        $response = wp_remote_post($api_url, array(
            'timeout' => 30,
            'headers' => $headers,
            'body' => wp_json_encode($request_body)
        ));
        
        boopixel_ai_chat_for_n8n_log_api_request('create-checkout', $api_url, $request_body, $headers, $response);
        
        if (is_wp_error($response)) {
            return array(
                'success' => false,
                'message' => esc_html__('Connection error. Please try again later.', 'boopixel-ai-chat-for-n8n')
            );
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
        $data = json_decode($response_body, true);
        
        if ($response_code !== 200) {
            $error_message = esc_html__('Server error. Please try again later.', 'boopixel-ai-chat-for-n8n');
            if (isset($data['detail'])) {
                $error_message = esc_html($data['detail']);
            } elseif (isset($data['message'])) {
                $error_message = esc_html($data['message']);
            }
            return array(
                'success' => false,
                'message' => $error_message
            );
        }
        
        if (isset($data['checkout_url']) && isset($data['session_id'])) {
            return array(
                'success' => true,
                'checkout_url' => esc_url_raw($data['checkout_url']),
                'session_id' => sanitize_text_field($data['session_id'])
            );
        }
        return array(
            'success' => false,
            'message' => esc_html__('Invalid response from server.', 'boopixel-ai-chat-for-n8n')
        );
    }
    
    /**
     * Verify payment and activate license after checkout
     *
     * @param string $session_id Stripe checkout session ID.
     * @return array Response with success status and license key.
     */
    public function verify_payment_and_activate($session_id) {
        if (empty($session_id)) {
            return array(
                'success' => false,
                'message' => esc_html__('Session ID is required.', 'boopixel-ai-chat-for-n8n')
            );
        }
        
        $api_url = $this->get_api_url() . 'check-payment';
        $request_body = array(
            'session_id' => sanitize_text_field($session_id),
            'site_url' => esc_url_raw(home_url())
        );
        
        $headers = array(
            'Content-Type' => 'application/json'
        );
        
        $response = wp_remote_post($api_url, array(
            'timeout' => 30,
            'headers' => $headers,
            'body' => wp_json_encode($request_body)
        ));
        
        boopixel_ai_chat_for_n8n_log_api_request('check-payment', $api_url, $request_body, $headers, $response);
        
        if (is_wp_error($response)) {
            return array(
                'success' => false,
                'message' => esc_html__('Connection error. Please try again later.', 'boopixel-ai-chat-for-n8n')
            );
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
        $data = json_decode($response_body, true);
        
        if ($response_code !== 200) {
            $error_message = esc_html__('Payment verification failed.', 'boopixel-ai-chat-for-n8n');
            if (isset($data['detail'])) {
                $error_message = esc_html($data['detail']);
            } elseif (isset($data['message'])) {
                $error_message = esc_html($data['message']);
            }
            return array(
                'success' => false,
                'message' => $error_message
            );
        }
        
        if (isset($data['status']) && $data['status'] === 'processing') {
            return array(
                'success' => false,
                'message' => esc_html__('Payment is being processed. Please wait a moment and refresh the page.', 'boopixel-ai-chat-for-n8n')
            );
        }
        
        if (isset($data['status']) && $data['status'] === 'failed') {
            $error_message = isset($data['message']) ? esc_html($data['message']) : esc_html__('Payment was not completed.', 'boopixel-ai-chat-for-n8n');
            return array(
                'success' => false,
                'message' => $error_message
            );
        }
        
        if (isset($data['success']) && $data['success'] === true && isset($data['license_key'])) {
            $license_key = sanitize_text_field($data['license_key']);
            
            update_option($this->license_key_option, $license_key);
            update_option($this->license_last_check_option, time());
            delete_transient('boopixel_ai_chat_for_n8n_pro_status');
            
            $activation = $this->activate_license($license_key);
            
            if ($activation['success']) {
                return array(
                    'success' => true,
                    'license_key' => $license_key,
                    'message' => esc_html__('Payment successful! License activated automatically.', 'boopixel-ai-chat-for-n8n')
                );
            }
            
            update_option($this->license_status_option, 'invalid');
            return $activation;
        }
        
        return array(
            'success' => false,
            'message' => isset($data['message']) ? esc_html($data['message']) : esc_html__('License key not found. Payment may still be processing.', 'boopixel-ai-chat-for-n8n')
        );
    }
}


