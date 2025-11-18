<?php
/**
 * Admin sessions page template
 *
 * @package BooChat_Connect
 * @var string $ajax_url AJAX URL.
 * @var string $nonce    Nonce for AJAX requests.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap boochat-connect-wrap">
    <div class="boochat-connect-header">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <p>
            <?php echo esc_html__('Version', 'boochat-connect'); ?> <?php echo esc_html(BOOCHAT_CONNECT_VERSION); ?> • 
            <?php echo esc_html__('AI Chatbot & n8n Automation', 'boochat-connect'); ?>
        </p>
    </div>
    
    <div class="boochat-connect-content">
        <div class="boochat-connect-content-left">
            <div class="boochat-connect-card">
                <h2><?php echo esc_html__('User Sessions', 'boochat-connect'); ?></h2>
                <p><?php echo esc_html__('View all user chat sessions and their details.', 'boochat-connect'); ?></p>
                
                <!-- Filters -->
                <div class="boochat-connect-sessions-filters" style="margin: 20px 0; display: flex; align-items: center; gap: 10px;">
                    <label for="sessions-per-page" style="margin: 0;">
                        <?php echo esc_html__('Show:', 'boochat-connect'); ?>
                    </label>
                    <select id="sessions-per-page" style="margin: 0;">
                        <option value="10">10</option>
                        <option value="20" selected>20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <button type="button" id="refresh-sessions" class="button" style="margin: 0;">
                        <?php echo esc_html__('Refresh', 'boochat-connect'); ?>
                    </button>
                </div>
                
                <!-- Sessions Table -->
                <div id="sessions-container">
                    <p style="text-align: center; color: #646970; padding: 20px;">
                        <span class="spinner is-active" style="float: none; margin: 0 10px 0 0;"></span>
                        <?php echo esc_html__('Loading sessions...', 'boochat-connect'); ?>
                    </p>
                </div>
                
                <!-- Pagination -->
                <div id="sessions-pagination" style="margin-top: 20px; display: none;"></div>
            </div>
        </div>
    </div>
</div>

