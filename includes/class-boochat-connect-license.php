<?php
/**
 * License management for BooChat Connect PRO
 *
 * @package BooChat_Connect
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class BooChat_Connect_License
 */
class BooChat_Connect_License {
    
    /**
     * License key option name
     *
     * @var string
     */
    private $license_key_option = 'boochat_connect_license_key';
    
    /**
     * License status option name
     *
     * @var string
     */
    private $license_status_option = 'boochat_connect_license_status';
    
    /**
     * License expires option name
     *
     * @var string
     */
    private $license_expires_option = 'boochat_connect_license_expires';
    
    /**
     * License last check option name
     *
     * @var string
     */
    private $license_last_check_option = 'boochat_connect_license_last_check';
    
    /**
     * API URL for license verification
     *
     * @var string
     */
    private $api_url = 'https://boopixel.com/api/boochat-connect/license/';
    
    /**
     * API key option name
     *
     * @var string
     */
    private $api_key_option = 'boochat_connect_api_key';
    
    /**
     * Get API URL (allows customization)
     *
     * @return string API URL.
     */
    private function get_api_url() {
        $custom_url = get_option('boochat_connect_license_api_url', '');
        return !empty($custom_url) ? $custom_url : $this->api_url;
    }
    
    /**
     * Get API headers with authentication
     *
     * @return array Headers array.
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
     * @return bool True if PRO license is active and valid.
     */
    public function is_pro() {
        $status = get_option($this->license_status_option, 'invalid');
        $expires = get_option($this->license_expires_option, 0);
        
        // Verifica se está ativa e não expirada
        if ($status === 'valid' && $expires > time()) {
            return true;
        }
        
        return false;
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
                'message' => esc_html__('License key is required.', 'boochat-connect')
            );
        }
        
        $response = wp_remote_post($this->get_api_url() . 'activate', array(
            'timeout' => 30,
            'headers' => $this->get_api_headers(),
            'body' => wp_json_encode(array(
                'license_key' => sanitize_text_field($license_key),
                'site_url' => esc_url_raw(home_url()),
                'plugin_version' => BOOCHAT_CONNECT_VERSION
            ))
        ));
        
        if (is_wp_error($response)) {
            return array(
                'success' => false,
                'message' => esc_html__('Connection error. Please try again later.', 'boochat-connect')
            );
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code !== 200) {
            return array(
                'success' => false,
                'message' => esc_html__('Server error. Please try again later.', 'boochat-connect')
            );
        }
        
        $data = json_decode(wp_remote_retrieve_body($response), true);
        
        if (isset($data['success']) && $data['success'] === true) {
            update_option($this->license_key_option, sanitize_text_field($license_key));
            update_option($this->license_status_option, 'valid');
            update_option($this->license_expires_option, isset($data['expires']) ? intval($data['expires']) : (time() + YEAR_IN_SECONDS));
            update_option($this->license_last_check_option, time());
            
            return array(
                'success' => true,
                'message' => esc_html__('License activated successfully!', 'boochat-connect')
            );
        }
        
        return array(
            'success' => false,
            'message' => isset($data['message']) ? esc_html($data['message']) : esc_html__('Invalid license key.', 'boochat-connect')
        );
    }
    
    /**
     * Verify license periodically
     *
     * @return bool True if license is valid.
     */
    public function verify_license() {
        $license_key = get_option($this->license_key_option);
        if (empty($license_key)) {
            return false;
        }
        
        $response = wp_remote_post($this->get_api_url() . 'verify', array(
            'timeout' => 30,
            'headers' => $this->get_api_headers(),
            'body' => wp_json_encode(array(
                'license_key' => sanitize_text_field($license_key),
                'site_url' => esc_url_raw(home_url())
            ))
        ));
        
        if (is_wp_error($response)) {
            return false;
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code !== 200) {
            return false;
        }
        
        $data = json_decode(wp_remote_retrieve_body($response), true);
        
        if (isset($data['status'])) {
            update_option($this->license_status_option, sanitize_text_field($data['status']));
            if (isset($data['expires'])) {
                update_option($this->license_expires_option, intval($data['expires']));
            }
            update_option($this->license_last_check_option, time());
            
            return $data['status'] === 'valid';
        }
        
        return false;
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
                'message' => esc_html__('No license key found.', 'boochat-connect')
            );
        }
        
        $response = wp_remote_post($this->get_api_url() . 'deactivate', array(
            'timeout' => 30,
            'headers' => $this->get_api_headers(),
            'body' => wp_json_encode(array(
                'license_key' => sanitize_text_field($license_key),
                'site_url' => esc_url_raw(home_url())
            ))
        ));
        
        if (is_wp_error($response)) {
            return array(
                'success' => false,
                'message' => esc_html__('Connection error. Please try again later.', 'boochat-connect')
            );
        }
        
        // Remove license data regardless of API response
        delete_option($this->license_key_option);
        delete_option($this->license_status_option);
        delete_option($this->license_expires_option);
        delete_option($this->license_last_check_option);
        
        return array(
            'success' => true,
            'message' => esc_html__('License deactivated successfully.', 'boochat-connect')
        );
    }
    
    /**
     * Request checkout URL from API
     *
     * The API will create the Stripe checkout session using its own credentials.
     *
     * @return array Response with checkout URL and session ID.
     */
    public function request_checkout_url() {
        $return_url = admin_url('admin.php?page=boochat-connect-pro&payment=success&session_id={CHECKOUT_SESSION_ID}');
        $cancel_url = admin_url('admin.php?page=boochat-connect-pro&payment=cancel');
        
        $response = wp_remote_post($this->get_api_url() . 'create-checkout', array(
            'timeout' => 30,
            'headers' => $this->get_api_headers(),
            'body' => wp_json_encode(array(
                'site_url' => esc_url_raw(home_url()),
                'plugin_version' => BOOCHAT_CONNECT_VERSION,
                'return_url' => $return_url,
                'cancel_url' => $cancel_url
            ))
        ));
        
        if (is_wp_error($response)) {
            return array(
                'success' => false,
                'message' => esc_html__('Connection error. Please try again later.', 'boochat-connect')
            );
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code !== 200) {
            $error_data = json_decode(wp_remote_retrieve_body($response), true);
            $error_message = isset($error_data['detail']) ? $error_data['detail'] : esc_html__('Server error. Please try again later.', 'boochat-connect');
            return array(
                'success' => false,
                'message' => $error_message
            );
        }
        
        $data = json_decode(wp_remote_retrieve_body($response), true);
        
        if (isset($data['checkout_url']) && isset($data['session_id'])) {
            return array(
                'success' => true,
                'checkout_url' => esc_url_raw($data['checkout_url']),
                'session_id' => sanitize_text_field($data['session_id'])
            );
        }
        
        return array(
            'success' => false,
            'message' => esc_html__('Invalid response from server.', 'boochat-connect')
        );
    }
    
    /**
     * Verify payment and activate license after checkout
     *
     * This is called when user returns from checkout.
     * The API handles the Stripe webhook and generates the license automatically.
     * This method just checks if license was generated and activates it.
     *
     * @param string $session_id Stripe checkout session ID.
     * @return array Response with success status and license key.
     */
    public function verify_payment_and_activate($session_id) {
        if (empty($session_id)) {
            return array(
                'success' => false,
                'message' => esc_html__('Session ID is required.', 'boochat-connect')
            );
        }
        
        // Check payment status with API
        $response = wp_remote_post($this->get_api_url() . 'check-payment', array(
            'timeout' => 30,
            'headers' => $this->get_api_headers(),
            'body' => wp_json_encode(array(
                'session_id' => sanitize_text_field($session_id),
                'site_url' => esc_url_raw(home_url())
            ))
        ));
        
        if (is_wp_error($response)) {
            return array(
                'success' => false,
                'message' => esc_html__('Connection error. Please try again later.', 'boochat-connect')
            );
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code !== 200) {
            return array(
                'success' => false,
                'message' => esc_html__('Payment verification failed.', 'boochat-connect')
            );
        }
        
        $data = json_decode(wp_remote_retrieve_body($response), true);
        
        // If payment is successful and license was generated
        if (isset($data['success']) && $data['success'] === true && isset($data['license_key'])) {
            // Auto-activate the license
            $activation = $this->activate_license($data['license_key']);
            if ($activation['success']) {
                return array(
                    'success' => true,
                    'license_key' => $data['license_key'],
                    'message' => esc_html__('Payment successful! License activated automatically.', 'boochat-connect')
                );
            }
            return $activation;
        }
        
        // Payment might still be processing
        if (isset($data['status']) && $data['status'] === 'processing') {
            return array(
                'success' => false,
                'message' => esc_html__('Payment is being processed. Please wait a moment and refresh the page.', 'boochat-connect')
            );
        }
        
        return array(
            'success' => false,
            'message' => isset($data['message']) ? esc_html($data['message']) : esc_html__('Payment was not completed.', 'boochat-connect')
        );
    }
}

