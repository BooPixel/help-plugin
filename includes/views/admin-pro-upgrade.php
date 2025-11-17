<?php
/**
 * PRO Upgrade page template
 *
 * @package BooChat_Connect
 * @var bool   $is_pro          Whether user has PRO license.
 * @var string $license_status  Current license status.
 * @var string $license_key     Current license key (masked).
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Display only
$payment_status = isset($_GET['payment']) ? sanitize_text_field(wp_unslash($_GET['payment'])) : '';
$license_activated = isset($_GET['license_activated']) && sanitize_text_field(wp_unslash($_GET['license_activated'])) === '1';
?>
<div class="wrap boochat-connect-wrap">
    <div class="boochat-connect-pro-upgrade">
        
        <?php if ($payment_status === 'success' && $license_activated): ?>
            <div class="notice notice-success is-dismissible">
                <p><strong><?php echo esc_html__('Payment Successful!', 'boochat-connect'); ?></strong></p>
                <p><?php echo esc_html__('Your PRO license has been activated. You can now access all premium features!', 'boochat-connect'); ?></p>
            </div>
        <?php elseif ($payment_status === 'success'): ?>
            <div class="notice notice-info is-dismissible">
                <p><?php echo esc_html__('Payment received. Processing license activation...', 'boochat-connect'); ?></p>
            </div>
        <?php endif; ?>
        
        <?php if ($payment_status === 'cancel'): ?>
            <div class="notice notice-warning is-dismissible">
                <p><?php echo esc_html__('Payment was cancelled.', 'boochat-connect'); ?></p>
            </div>
        <?php endif; ?>
        
        <?php if ($payment_status === 'error'): ?>
            <div class="notice notice-error is-dismissible">
                <p>
                    <strong><?php echo esc_html__('Payment Error', 'boochat-connect'); ?></strong>
                    <?php
                    // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Display only
                    if (isset($_GET['message'])) {
                        echo ' - ' . esc_html(urldecode(sanitize_text_field(wp_unslash($_GET['message']))));
                    }
                    ?>
                </p>
            </div>
        <?php endif; ?>
        
        <?php if ($is_pro): ?>
            <div class="boochat-connect-card boochat-connect-pro-active">
                <h1>✅ <?php echo esc_html__('PRO License Active', 'boochat-connect'); ?></h1>
                <p><?php echo esc_html__('You have an active PRO license. All premium features are unlocked!', 'boochat-connect'); ?></p>
                
                <div class="license-info">
                    <table class="form-table">
                        <tr>
                            <th><?php echo esc_html__('License Status', 'boochat-connect'); ?></th>
                            <td>
                                <span class="boochat-connect-status-success">
                                    <?php echo esc_html__('Active', 'boochat-connect'); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th><?php echo esc_html__('License Key', 'boochat-connect'); ?></th>
                            <td>
                                <code><?php echo esc_html($license_key); ?></code>
                            </td>
                        </tr>
                    </table>
                    
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin-top: 20px;">
                        <?php wp_nonce_field('boochat_connect_deactivate_license', 'deactivate_nonce'); ?>
                        <input type="hidden" name="action" value="boochat_connect_deactivate_license">
                        <?php submit_button(esc_html__('Deactivate License', 'boochat-connect'), 'secondary'); ?>
                    </form>
                </div>
            </div>
        <?php else: ?>
            
            <div class="boochat-connect-card">
                <h1>🔒 <?php echo esc_html__('Upgrade to PRO', 'boochat-connect'); ?></h1>
                <p class="intro-text">
                    <?php echo esc_html__('Unlock advanced statistics and analytics with BooChat Connect PRO.', 'boochat-connect'); ?>
                </p>
                
                <div class="pro-features">
                    <h2><?php echo esc_html__('PRO Features:', 'boochat-connect'); ?></h2>
                    <ul class="feature-list">
                        <li>✅ <?php echo esc_html__('Advanced Statistics Dashboard', 'boochat-connect'); ?></li>
                        <li>✅ <?php echo esc_html__('Interactive Charts & Graphs', 'boochat-connect'); ?></li>
                        <li>✅ <?php echo esc_html__('Calendar Heatmap Visualization', 'boochat-connect'); ?></li>
                        <li>✅ <?php echo esc_html__('Export Data (CSV/JSON)', 'boochat-connect'); ?></li>
                        <li>✅ <?php echo esc_html__('Email Reports', 'boochat-connect'); ?></li>
                        <li>✅ <?php echo esc_html__('Priority Support', 'boochat-connect'); ?></li>
                        <li>✅ <?php echo esc_html__('Future PRO Features', 'boochat-connect'); ?></li>
                    </ul>
                </div>
                
                <div class="pricing-section">
                    <h2><?php echo esc_html__('Pricing', 'boochat-connect'); ?></h2>
                    <div class="price-card">
                        <h3><?php echo esc_html__('PRO License', 'boochat-connect'); ?></h3>
                        <div class="price">
                            <span class="currency">$</span>
                            <span class="amount">49</span>
                            <span class="period">/year</span>
                        </div>
                        <p class="price-description">
                            <?php echo esc_html__('One year of PRO features and updates', 'boochat-connect'); ?>
                        </p>
                        
                        <button type="button" id="stripe-checkout-btn" class="button button-primary button-large button-hero">
                            <?php echo esc_html__('Buy Now with Stripe', 'boochat-connect'); ?>
                        </button>
                        <p class="description" style="margin-top: 10px; font-size: 12px;">
                            <?php echo esc_html__('Secure payment powered by Stripe', 'boochat-connect'); ?>
                        </p>
                    </div>
                </div>
                
                <div class="license-activation-section">
                    <h2><?php echo esc_html__('Already have a license?', 'boochat-connect'); ?></h2>
                    <p><?php echo esc_html__('Enter your license key below to activate PRO features.', 'boochat-connect'); ?></p>
                    
                    <?php
                    // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Display only
                    $activation_message = isset($_GET['activation']) ? sanitize_text_field(wp_unslash($_GET['activation'])) : '';
                    if ($activation_message === 'success'):
                    ?>
                        <div class="notice notice-success is-dismissible">
                            <p><?php echo esc_html__('License activated successfully!', 'boochat-connect'); ?></p>
                        </div>
                    <?php elseif ($activation_message === 'error'): ?>
                        <div class="notice notice-error is-dismissible">
                            <p><?php echo esc_html__('Failed to activate license. Please check your license key.', 'boochat-connect'); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="license-form">
                        <?php wp_nonce_field('boochat_connect_activate_license', 'license_nonce'); ?>
                        <input type="hidden" name="action" value="boochat_connect_activate_license">
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="license_key"><?php echo esc_html__('License Key', 'boochat-connect'); ?></label>
                                </th>
                                <td>
                                    <input 
                                        type="text" 
                                        id="license_key" 
                                        name="license_key" 
                                        class="regular-text" 
                                        placeholder="<?php echo esc_attr__('Enter your license key', 'boochat-connect'); ?>"
                                        required
                                    >
                                    <p class="description">
                                        <?php echo esc_html__('Enter your PRO license key to activate.', 'boochat-connect'); ?>
                                    </p>
                                </td>
                            </tr>
                        </table>
                        <?php submit_button(esc_html__('Activate License', 'boochat-connect')); ?>
                    </form>
                </div>
            </div>
            
        <?php endif; ?>
        
    </div>
</div>

