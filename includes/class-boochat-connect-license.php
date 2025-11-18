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
    private $api_url = 'https://chat.boopixel.com/api/v1/license/';
    
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
     * Used for authenticated endpoints like /activate, /verify, /deactivate.
     * Public endpoints like /create-checkout and /check-payment don't use this.
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
     * Always validates with API (with cache to avoid excessive requests).
     *
     * @param bool $force_check Force API check even if cache is valid.
     * @return bool True if PRO license is active and valid.
     */
    public function is_pro($force_check = false) {
        $license_key = get_option($this->license_key_option, '');
        if (empty($license_key)) {
            return false;
        }
        
        // Use transient cache to avoid excessive API calls (cache for 1 hour)
        $cache_key = 'boochat_connect_pro_status';
        $cached_status = get_transient($cache_key);
        
        // If we have a valid cache and not forcing check, return cached value
        if (!$force_check && $cached_status !== false) {
            return (bool) $cached_status;
        }
        
        // Verify license with API
        $is_valid = $this->verify_license();
        
        // Cache the result for 1 hour
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
     * Endpoint: POST /api/v1/license/activate (Public)
     * No API Key required - validated by license_key + site_url.
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
        
        $api_url = $this->get_api_url() . 'activate';
        $request_body = array(
            'license_key' => sanitize_text_field($license_key),
            'site_url' => esc_url_raw(home_url()),
            'plugin_version' => BOOCHAT_CONNECT_VERSION
        );
        
        // No API Key required - endpoint validates by license_key + site_url
        $headers = array(
            'Content-Type' => 'application/json'
            // No X-API-Key header - endpoint is public
        );
        
        // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Debug logging for API requests
        error_log('[BooChat Connect] [activate] Request URL: ' . $api_url);
        // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Debug logging for API requests
        error_log('[BooChat Connect] [activate] Request Body: ' . wp_json_encode($request_body));
        // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Debug logging for API requests
        error_log('[BooChat Connect] [activate] Headers: ' . wp_json_encode($headers));
        
        $response = wp_remote_post($api_url, array(
            'timeout' => 30,
            'headers' => $headers,
            'body' => wp_json_encode($request_body)
        ));
        
        if (is_wp_error($response)) {
            // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Debug logging for API requests
            error_log('[BooChat Connect] [activate] WP Error: ' . $response->get_error_message());
            return array(
                'success' => false,
                'message' => esc_html__('Connection error. Please try again later.', 'boochat-connect')
            );
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
        $data = json_decode($response_body, true);
        
        // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Debug logging for API requests
        error_log('[BooChat Connect] [activate] Response Code: ' . $response_code);
        // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Debug logging for API requests
        error_log('[BooChat Connect] [activate] Response Body: ' . $response_body);
        
        if ($response_code !== 200) {
            $error_message = esc_html__('Server error. Please try again later.', 'boochat-connect');
            if (isset($data['detail'])) {
                $error_message = esc_html($data['detail']);
            } elseif (isset($data['message'])) {
                $error_message = esc_html($data['message']);
            }
            // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Debug logging for API requests
            error_log('[BooChat Connect] [activate] Error: ' . $error_message);
            return array(
                'success' => false,
                'message' => $error_message
            );
        }
        
        if (isset($data['success']) && $data['success'] === true) {
            // Save license data
            update_option($this->license_key_option, sanitize_text_field($license_key));
            update_option($this->license_status_option, 'valid');
            update_option($this->license_expires_option, isset($data['expires']) ? intval($data['expires']) : (time() + YEAR_IN_SECONDS));
            update_option($this->license_last_check_option, time());
            
            // Clear cache to force fresh API check on next is_pro() call
            delete_transient('boochat_connect_pro_status');
            
            // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Debug logging for API requests
            error_log('[BooChat Connect] [activate] Success: License activated');
            
            return array(
                'success' => true,
                'message' => esc_html__('License activated successfully!', 'boochat-connect')
            );
        }
        
        // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Debug logging for API requests
        error_log('[BooChat Connect] [activate] Error: Invalid response - success not true');
        return array(
            'success' => false,
            'message' => isset($data['message']) ? esc_html($data['message']) : esc_html__('Invalid license key.', 'boochat-connect')
        );
    }
    
    /**
     * Verify license with API
     *
     * Endpoint: POST /api/v1/license/verify (Public)
     * No API Key required - validated by license_key + site_url.
     *
     * @return bool True if license is valid.
     */
    public function verify_license() {
        $license_key = get_option($this->license_key_option);
        if (empty($license_key)) {
            // Clear status if no license key
            update_option($this->license_status_option, 'invalid');
            return false;
        }
        
        $api_url = $this->get_api_url() . 'verify';
        $request_body = array(
            'license_key' => sanitize_text_field($license_key),
            'site_url' => esc_url_raw(home_url())
        );
        
        // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Debug logging for API requests
        error_log('[BooChat Connect] [verify] Request URL: ' . $api_url);
        // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Debug logging for API requests
        error_log('[BooChat Connect] [verify] Request Body: ' . wp_json_encode($request_body));
        
        $response = wp_remote_post($api_url, array(
            'timeout' => 15,
            'headers' => array(
                'Content-Type' => 'application/json'
                // No X-API-Key header - endpoint is public
            ),
            'body' => wp_json_encode($request_body)
        ));
        
        if (is_wp_error($response)) {
            // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Debug logging for API requests
            error_log('[BooChat Connect] [verify] WP Error: ' . $response->get_error_message());
            // On connection error, return cached status if available, otherwise false
            $cached_status = get_option($this->license_status_option, 'invalid');
            return ($cached_status === 'valid' && get_option($this->license_expires_option, 0) > time());
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
        $data = json_decode($response_body, true);
        
        // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Debug logging for API requests
        error_log('[BooChat Connect] [verify] Response Code: ' . $response_code);
        // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Debug logging for API requests
        error_log('[BooChat Connect] [verify] Response Body: ' . $response_body);
        
        if ($response_code !== 200) {
            // On API error, return cached status if available, otherwise false
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
            
            // Update cache with new status
            set_transient('boochat_connect_pro_status', $is_valid ? 1 : 0, HOUR_IN_SECONDS);
            
            // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Debug logging for API requests
            error_log('[BooChat Connect] [verify] License status: ' . $status . ' (valid: ' . ($is_valid ? 'yes' : 'no') . ')');
            
            return $is_valid;
        }
        
        // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Debug logging for API requests
        error_log('[BooChat Connect] [verify] Invalid response - no status field');
        
        // On invalid response, return cached status if available, otherwise false
        $cached_status = get_option($this->license_status_option, 'invalid');
        return ($cached_status === 'valid' && get_option($this->license_expires_option, 0) > time());
    }
    
    /**
     * Deactivate license
     *
     * Endpoint: POST /api/v1/license/deactivate (Public)
     * No API Key required - validated by license_key + site_url.
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
            'headers' => array(
                'Content-Type' => 'application/json'
                // No X-API-Key header - endpoint is public
            ),
            'body' => wp_json_encode(array(
                'license_key' => sanitize_text_field($license_key),
                'site_url' => esc_url_raw(home_url())
            ))
        ));
        
        // Remove license data regardless of API response
        delete_option($this->license_key_option);
        delete_option($this->license_status_option);
        delete_option($this->license_expires_option);
        delete_option($this->license_last_check_option);
        
        // Clear cache
        delete_transient('boochat_connect_pro_status');
        
        if (is_wp_error($response)) {
            return array(
                'success' => true,
                'message' => esc_html__('License deactivated locally. API connection error.', 'boochat-connect')
            );
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code !== 200) {
            return array(
                'success' => true,
                'message' => esc_html__('License deactivated locally.', 'boochat-connect')
            );
        }
        
        return array(
            'success' => true,
            'message' => esc_html__('License deactivated successfully.', 'boochat-connect')
        );
    }
    
    /**
     * Request checkout URL from API
     *
     * Endpoint: POST /api/v1/license/create-checkout (Public)
     * Creates a Stripe checkout session. No authentication required.
     * The API will create the Stripe checkout session using its own credentials.
     *
     * @return array Response with checkout URL and session ID.
     */
    public function request_checkout_url() {
        $api_url = $this->get_api_url() . 'create-checkout';
        $return_url = admin_url('admin.php?page=boochat-connect-pro&payment=success&session_id={CHECKOUT_SESSION_ID}');
        $cancel_url = admin_url('admin.php?page=boochat-connect-pro&payment=cancel');
        
        $request_body = array(
            'site_url' => esc_url_raw(home_url()),
            'plugin_version' => BOOCHAT_CONNECT_VERSION,
            'return_url' => $return_url,
            'cancel_url' => $cancel_url
        );
        
        // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Debug logging for API requests
        error_log('[BooChat Connect] [create-checkout] Request URL: ' . $api_url);
        // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Debug logging for API requests
        error_log('[BooChat Connect] [create-checkout] Request Body: ' . wp_json_encode($request_body));
        
        // Endpoint is public - no API key required
        // This allows users to pay before receiving their license token
        $response = wp_remote_post($api_url, array(
            'timeout' => 30,
            'headers' => array(
                'Content-Type' => 'application/json'
                // No X-API-Key header - endpoint is public
            ),
            'body' => wp_json_encode($request_body)
        ));
        
        if (is_wp_error($response)) {
            // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Debug logging for API requests
            error_log('[BooChat Connect] [create-checkout] WP Error: ' . $response->get_error_message());
            return array(
                'success' => false,
                'message' => esc_html__('Connection error. Please try again later.', 'boochat-connect')
            );
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
        $data = json_decode($response_body, true);
        
        // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Debug logging for API requests
        error_log('[BooChat Connect] [create-checkout] Response Code: ' . $response_code);
        // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Debug logging for API requests
        error_log('[BooChat Connect] [create-checkout] Response Body: ' . $response_body);
        
        if ($response_code !== 200) {
            $error_message = esc_html__('Server error. Please try again later.', 'boochat-connect');
            if (isset($data['detail'])) {
                $error_message = esc_html($data['detail']);
            } elseif (isset($data['message'])) {
                $error_message = esc_html($data['message']);
            }
            // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Debug logging for API requests
            error_log('[BooChat Connect] [create-checkout] Error: ' . $error_message);
            return array(
                'success' => false,
                'message' => $error_message
            );
        }
        
        if (isset($data['checkout_url']) && isset($data['session_id'])) {
            // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Debug logging for API requests
            error_log('[BooChat Connect] [create-checkout] Success: Checkout URL received');
            return array(
                'success' => true,
                'checkout_url' => esc_url_raw($data['checkout_url']),
                'session_id' => sanitize_text_field($data['session_id'])
            );
        }
        
        // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Debug logging for API requests
        error_log('[BooChat Connect] [create-checkout] Error: Invalid response structure');
        return array(
            'success' => false,
            'message' => esc_html__('Invalid response from server.', 'boochat-connect')
        );
    }
    
    /**
     * Verify payment and activate license after checkout
     *
     * Endpoint: POST /api/v1/license/check-payment (Public)
     * Checks payment status and retrieves license_key if payment was successful.
     * Then activates the license using /api/v1/license/activate (Authenticated).
     *
     * Flow:
     * 1. Call check-payment to get license_key
     * 2. If license_key is returned, call activate with X-API-Key
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
        
        // Step 1: Check payment status (public endpoint)
        // Endpoint validates by session_id + site_url (no API key required)
        $api_url = $this->get_api_url() . 'check-payment';
        $request_body = array(
            'session_id' => sanitize_text_field($session_id),
            'site_url' => esc_url_raw(home_url())
        );
        
        // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Debug logging for API requests
        error_log('[BooChat Connect] [check-payment] Request URL: ' . $api_url);
        // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Debug logging for API requests
        error_log('[BooChat Connect] [check-payment] Request Body: ' . wp_json_encode($request_body));
        
        $response = wp_remote_post($api_url, array(
            'timeout' => 30,
            'headers' => array(
                'Content-Type' => 'application/json'
                // No X-API-Key - validated by session_id + site_url
            ),
            'body' => wp_json_encode($request_body)
        ));
        
        if (is_wp_error($response)) {
            // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Debug logging for API requests
            error_log('[BooChat Connect] [check-payment] WP Error: ' . $response->get_error_message());
            return array(
                'success' => false,
                'message' => esc_html__('Connection error. Please try again later.', 'boochat-connect')
            );
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
        $data = json_decode($response_body, true);
        
        // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Debug logging for API requests
        error_log('[BooChat Connect] [check-payment] Response Code: ' . $response_code);
        // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Debug logging for API requests
        error_log('[BooChat Connect] [check-payment] Response Body: ' . $response_body);
        
        // Handle non-200 responses
        if ($response_code !== 200) {
            $error_message = esc_html__('Payment verification failed.', 'boochat-connect');
            if (isset($data['detail'])) {
                $error_message = esc_html($data['detail']);
            } elseif (isset($data['message'])) {
                $error_message = esc_html($data['message']);
            }
            // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Debug logging for API requests
            error_log('[BooChat Connect] [check-payment] Error: ' . $error_message);
            return array(
                'success' => false,
                'message' => $error_message
            );
        }
        
        // Check if payment is still processing
        if (isset($data['status']) && $data['status'] === 'processing') {
            return array(
                'success' => false,
                'message' => esc_html__('Payment is being processed. Please wait a moment and refresh the page.', 'boochat-connect')
            );
        }
        
        // Check if payment failed
        if (isset($data['status']) && $data['status'] === 'failed') {
            $error_message = isset($data['message']) ? esc_html($data['message']) : esc_html__('Payment was not completed.', 'boochat-connect');
            return array(
                'success' => false,
                'message' => $error_message
            );
        }
        
        // Step 2: If payment successful and license_key is returned, activate it automatically
        if (isset($data['success']) && $data['success'] === true && isset($data['license_key'])) {
            $license_key = sanitize_text_field($data['license_key']);
            
            // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Debug logging for API requests
            error_log('[BooChat Connect] [check-payment] License key received: ' . $license_key);
            
            // Save license key first (even if activation fails)
            update_option($this->license_key_option, $license_key);
            
            // Save basic status info even before activation attempt
            // This ensures we have the license key stored even if activation fails
            update_option($this->license_last_check_option, time());
            
            // Clear cache to force fresh API check
            delete_transient('boochat_connect_pro_status');
            
            // Always try to activate automatically (even if API key is not configured)
            // This ensures we attempt activation immediately upon return from Stripe
            // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Debug logging for API requests
            error_log('[BooChat Connect] [verify_payment_and_activate] Attempting to activate license...');
            $activation = $this->activate_license($license_key);
            
            // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Debug logging for API requests
            error_log('[BooChat Connect] [verify_payment_and_activate] Activation result: ' . wp_json_encode($activation));
            
            if ($activation['success']) {
                // Activation successful - status and expires are already saved by activate_license()
                // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Debug logging for API requests
                error_log('[BooChat Connect] [verify_payment_and_activate] License activated successfully');
                return array(
                    'success' => true,
                    'license_key' => $license_key,
                    'message' => esc_html__('Payment successful! License activated automatically.', 'boochat-connect')
                );
            }
            
            // Activation failed (but license_key is already saved)
            // Set status to invalid if activation failed
            update_option($this->license_status_option, 'invalid');
            // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Debug logging for API requests
            error_log('[BooChat Connect] [verify_payment_and_activate] Activation failed - status set to invalid');
            return $activation;
        }
        
        // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Debug logging for API requests
        error_log('[BooChat Connect] [check-payment] No license_key in response');
        
        // No license_key in response
        return array(
            'success' => false,
            'message' => isset($data['message']) ? esc_html($data['message']) : esc_html__('License key not found. Payment may still be processing.', 'boochat-connect')
        );
    }
}


