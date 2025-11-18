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

// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- GET parameters from external redirect (Stripe), display only
$boochat_connect_payment_status = isset($_GET['payment']) ? sanitize_text_field(wp_unslash($_GET['payment'])) : '';
// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- GET parameter from external redirect (Stripe), display only
$boochat_connect_license_activated = isset($_GET['license_activated']) && sanitize_text_field(wp_unslash($_GET['license_activated'])) === '1';
// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- GET parameter from external redirect (Stripe), display only
$boochat_connect_license_received = isset($_GET['license_received']) && sanitize_text_field(wp_unslash($_GET['license_received'])) === '1';
// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- GET parameter from external redirect (Stripe), display only
$boochat_connect_needs_api_key = isset($_GET['needs_api_key']) && sanitize_text_field(wp_unslash($_GET['needs_api_key'])) === '1';
?>
<div class="wrap boochat-connect-wrap">
    <div class="boochat-connect-header">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <p>
            <?php echo esc_html(boochat_connect_translate('version', 'Version')); ?> <?php echo esc_html(BOOCHAT_CONNECT_VERSION); ?> • 
            <?php echo esc_html(boochat_connect_translate('ai_chatbot_automation', 'AI Chatbot & n8n Automation')); ?>
        </p>
    </div>
    
    <?php if ($boochat_connect_payment_status === 'success' && $boochat_connect_license_activated): ?>
            <div class="notice notice-success is-dismissible">
                <p><strong><?php echo esc_html__('Payment Successful!', 'boochat-connect'); ?></strong></p>
                <p><?php echo esc_html__('Your PRO license has been activated automatically. You can now access all premium features!', 'boochat-connect'); ?></p>
            </div>
        <?php elseif ($boochat_connect_payment_status === 'success' && $boochat_connect_license_received && $boochat_connect_needs_api_key): ?>
            <div class="notice notice-warning is-dismissible">
                <p><strong><?php echo esc_html__('Payment Successful!', 'boochat-connect'); ?></strong></p>
                <p><?php echo esc_html__('Your license key has been received. To activate your PRO license, please configure your API Key in', 'boochat-connect'); ?> 
                   <a href="<?php echo esc_url(admin_url('admin.php?page=boochat-connect-settings')); ?>"><?php echo esc_html__('Settings', 'boochat-connect'); ?></a>.
                   <?php echo esc_html__('The license will be activated automatically once the API Key is configured.', 'boochat-connect'); ?>
                </p>
            </div>
        <?php elseif ($boochat_connect_payment_status === 'success'): ?>
            <div class="notice notice-info is-dismissible">
                <p><?php echo esc_html__('Payment received. Processing license activation...', 'boochat-connect'); ?></p>
            </div>
        <?php endif; ?>
        
        <?php if ($boochat_connect_payment_status === 'cancel'): ?>
            <div class="notice notice-warning is-dismissible">
                <p><?php echo esc_html__('Payment was cancelled.', 'boochat-connect'); ?></p>
            </div>
        <?php endif; ?>
        
        <?php if ($boochat_connect_payment_status === 'error'): ?>
            <div class="notice notice-error is-dismissible">
                <p>
                    <strong><?php echo esc_html__('Payment Error', 'boochat-connect'); ?></strong>
                    <?php
                    // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- GET parameter from external redirect (Stripe), display only
                    if (isset($_GET['message'])) {
                        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- GET parameter from external redirect (Stripe), sanitized and escaped
                        echo ' - ' . esc_html(urldecode(sanitize_text_field(wp_unslash($_GET['message']))));
                    }
                    ?>
                </p>
            </div>
        <?php endif; ?>
        
    <?php if ($is_pro): ?>
        <div class="boochat-connect-content">
            <div class="boochat-connect-content-left">
                <div class="boochat-connect-card boochat-connect-pro-active">
                    <h2>✅ <?php echo esc_html__('PRO License Active', 'boochat-connect'); ?></h2>
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
            </div>
        </div>
        <?php else: ?>
            
            <div class="boochat-connect-content">
                <!-- Left Column -->
                <div class="boochat-connect-content-left">
                    
                    <!-- Features Card -->
                    <div class="boochat-connect-card">
                        <h2><?php echo esc_html__('PRO Features', 'boochat-connect'); ?></h2>
                        <p><?php echo esc_html__('Unlock advanced features with BooChat Connect PRO.', 'boochat-connect'); ?></p>
                        <div class="boochat-connect-pro-features">
                            <div class="boochat-connect-pro-feature-item">
                                <div class="boochat-connect-pro-feature-icon">📊</div>
                                <div class="boochat-connect-pro-feature-content">
                                    <h3><?php echo esc_html__('Advanced Statistics Dashboard', 'boochat-connect'); ?></h3>
                                    <p><?php echo esc_html__('Make data-driven decisions with powerful analytics at your fingertips. Instantly see your chat performance with quick summaries (24h, 7 days, 30 days) and dive deep into any time period. Discover when your users are most active, identify peak engagement times, and optimize your chat strategy based on real data—not guesswork.', 'boochat-connect'); ?></p>
                                    <ul class="boochat-connect-pro-feature-benefits">
                                        <li><?php echo esc_html__('Discover when your users are most active', 'boochat-connect'); ?></li>
                                        <li><?php echo esc_html__('Identify peak engagement times', 'boochat-connect'); ?></li>
                                        <li><?php echo esc_html__('Optimize your chat strategy based on real data—not guesswork', 'boochat-connect'); ?></li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="boochat-connect-pro-feature-item">
                                <div class="boochat-connect-pro-feature-icon">📈</div>
                                <div class="boochat-connect-pro-feature-content">
                                    <h3><?php echo esc_html__('Interactive Charts & Graphs', 'boochat-connect'); ?></h3>
                                    <p><?php echo esc_html__('See your chat growth story unfold with stunning visual charts. Spot trends instantly, compare performance across different periods, and share beautiful reports with your team. Every click reveals new insights—no complex setup required.', 'boochat-connect'); ?></p>
                                    <ul class="boochat-connect-pro-feature-benefits">
                                        <li><?php echo esc_html__('Spot trends instantly with visual insights', 'boochat-connect'); ?></li>
                                        <li><?php echo esc_html__('Compare performance across different periods', 'boochat-connect'); ?></li>
                                        <li><?php echo esc_html__('Share beautiful reports with your team', 'boochat-connect'); ?></li>
                                        <li><?php echo esc_html__('Every click reveals new insights—no complex setup required', 'boochat-connect'); ?></li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="boochat-connect-pro-feature-item">
                                <div class="boochat-connect-pro-feature-icon">💬</div>
                                <div class="boochat-connect-pro-feature-content">
                                    <h3><?php echo esc_html__('User Sessions & Conversation History', 'boochat-connect'); ?></h3>
                                    <p><?php echo esc_html__('Never lose a conversation again. Access every chat session in one place, review complete transcripts, and understand your customers better. Export everything to JSON or CSV for advanced analysis, compliance, or team training. Turn conversations into actionable business intelligence.', 'boochat-connect'); ?></p>
                                    <ul class="boochat-connect-pro-feature-benefits">
                                        <li><?php echo esc_html__('Access every chat session in one place', 'boochat-connect'); ?></li>
                                        <li><?php echo esc_html__('Review complete conversation transcripts', 'boochat-connect'); ?></li>
                                        <li><?php echo esc_html__('Export to JSON or CSV for advanced analysis', 'boochat-connect'); ?></li>
                                        <li><?php echo esc_html__('Perfect for compliance, team training, and business intelligence', 'boochat-connect'); ?></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- License Activation Card -->
                    <div class="boochat-connect-card">
                        <h2><?php echo esc_html__('Already have a license?', 'boochat-connect'); ?></h2>
                        <p><?php echo esc_html__('Enter your license key below to activate PRO features.', 'boochat-connect'); ?></p>
                        
                        <?php
                        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Display only
                        $boochat_connect_activation_message = isset($_GET['activation']) ? sanitize_text_field(wp_unslash($_GET['activation'])) : '';
                        if ($boochat_connect_activation_message === 'success'):
                        ?>
                            <div class="notice notice-success is-dismissible">
                                <p><?php echo esc_html__('License activated successfully!', 'boochat-connect'); ?></p>
                            </div>
                        <?php elseif ($boochat_connect_activation_message === 'error'): ?>
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
                
                <!-- Right Sidebar -->
                <div class="boochat-connect-sidebar">
                    
                    <!-- Pricing Card -->
                    <div class="boochat-connect-card boochat-connect-sidebar-card">
                        <h2><?php echo esc_html__('Pricing', 'boochat-connect'); ?></h2>
                        <div class="price-card">
                            <div class="price">
                                <span class="currency">$</span>
                                <span class="amount">49</span>
                                <span class="period">/year</span>
                            </div>
                            <p class="price-description">
                                <?php echo esc_html__('One year of PRO features and updates', 'boochat-connect'); ?>
                            </p>
                            
                            <button type="button" id="stripe-checkout-btn" class="button button-primary button-large" style="width: 100%;">
                                <?php echo esc_html__('Buy Now', 'boochat-connect'); ?>
                            </button>
                        </div>
                    </div>
                    
                </div>
            </div>
            
        <?php endif; ?>
        
</div>

