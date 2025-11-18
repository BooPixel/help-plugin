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

// Check PRO license - redirect if not PRO
$boochat_connect_license = new BooChat_Connect_License();
if (!$boochat_connect_license->is_pro()) {
    ?>
    <script type="text/javascript">
        window.location.href = '<?php echo esc_js(admin_url('admin.php?page=boochat-connect-pro')); ?>';
    </script>
    <?php
    wp_safe_redirect(admin_url('admin.php?page=boochat-connect-pro'));
    exit;
}
?>
<div class="wrap boochat-connect-wrap">
    <div class="boochat-connect-header">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <p>
            <?php echo esc_html(boochat_connect_translate('version', 'Version')); ?> <?php echo esc_html(BOOCHAT_CONNECT_VERSION); ?> • 
            <?php echo esc_html(boochat_connect_translate('ai_chatbot_automation', 'AI Chatbot & n8n Automation')); ?>
        </p>
    </div>
    
    <div class="boochat-connect-content">
        <div class="boochat-connect-content-left">
            <div class="boochat-connect-card">
                <h2><?php echo esc_html(boochat_connect_translate('user_sessions', 'User Sessions')); ?></h2>
                <p><?php echo esc_html(boochat_connect_translate('view_user_sessions', 'View all user chat sessions and their details.')); ?></p>
                
                <!-- Filters -->
                <div class="boochat-connect-sessions-filters" style="margin: 20px 0; display: flex; align-items: center; gap: 10px;">
                    <label for="sessions-per-page" style="margin: 0;">
                        <?php echo esc_html(boochat_connect_translate('show', 'Show:')); ?>
                    </label>
                    <select id="sessions-per-page" style="margin: 0;">
                        <option value="10">10</option>
                        <option value="20" selected>20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <button type="button" id="refresh-sessions" class="button" style="margin: 0;">
                        <?php echo esc_html(boochat_connect_translate('refresh', 'Refresh')); ?>
                    </button>
                </div>
                
                <!-- Sessions Table -->
                <div id="sessions-container">
                    <p style="text-align: center; color: #646970; padding: 20px;">
                        <span class="spinner is-active" style="float: none; margin: 0 10px 0 0;"></span>
                        <?php echo esc_html(boochat_connect_translate('loading_sessions', 'Loading sessions...')); ?>
                    </p>
                </div>
                
                <!-- Pagination -->
                <div id="sessions-pagination" style="margin-top: 20px; display: none;"></div>
            </div>
        </div>
    </div>
</div>

