<?php
/**
 * Admin main page template
 *
 * @package BooChat_Connect
 * @var bool   $api_configured Whether API is configured.
 * @var string $api_url        API URL.
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
            <?php echo esc_html(boochat_connect_translate('version', 'Version')); ?> <?php echo esc_html(BOOCHAT_CONNECT_VERSION); ?> • 
            <?php echo esc_html(boochat_connect_translate('ai_chatbot_automation', 'AI Chatbot & n8n Automation')); ?>
        </p>
    </div>
    
    <div class="boochat-connect-content">
        
        <!-- Left Column -->
        <div class="boochat-connect-content-left">
            
            <!-- Welcome Card -->
            <div class="boochat-connect-card boochat-connect-card-hero">
                <div class="boochat-connect-card-hero">
                    <div class="boochat-connect-card-hero-content">
                        <h2>
                            <?php echo esc_html__('Welcome to BooChat Connect', 'boochat-connect'); ?>
                        </h2>
                        <p>
                            <?php echo esc_html__('Modern, lightweight chatbot popup that integrates seamlessly with n8n. Automate workflows, respond in real-time, collect leads, and connect to any AI model or external service.', 'boochat-connect'); ?>
                        </p>
                    </div>
                    <div class="boochat-connect-card-hero-icon">💬</div>
                </div>
            </div>

            <!-- Quick Status Cards -->
            <div class="boochat-connect-status-cards">
                <div class="boochat-connect-card boochat-connect-status-card <?php echo !$api_configured ? 'has-button' : ''; ?>">
                    <div class="boochat-connect-status-card-content">
                        <?php if ($api_configured): ?>
                            <div class="boochat-connect-status-icon success">
                                ✓
                            </div>
                        <?php endif; ?>
                        <div class="boochat-connect-status-info">
                            <h3><?php echo esc_html__('API Status', 'boochat-connect'); ?></h3>
                            <p>
                                <?php echo $api_configured ? esc_html__('Configured', 'boochat-connect') : esc_html__('Not Configured', 'boochat-connect'); ?>
                            </p>
                        </div>
                    </div>
                    <?php if (!$api_configured): ?>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=boochat-connect-settings')); ?>" class="button button-small">
                            <?php echo esc_html__('Configure Now', 'boochat-connect'); ?>
                        </a>
                    <?php endif; ?>
                </div>
                
                <div class="boochat-connect-card boochat-connect-status-card">
                    <div class="boochat-connect-status-card-content">
                        <div class="boochat-connect-status-icon success">
                            ✓
                        </div>
                        <div class="boochat-connect-status-info">
                            <h3><?php echo esc_html__('Chat Widget', 'boochat-connect'); ?></h3>
                            <p><?php echo esc_html__('Active', 'boochat-connect'); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Getting Started -->
            <div class="boochat-connect-card">
                <h2 class="boochat-connect-getting-started">
                    <?php echo esc_html__('Getting Started', 'boochat-connect'); ?>
                </h2>
                <div class="boochat-connect-getting-started-list">
                    <div class="boochat-connect-getting-started-item">
                        <div class="boochat-connect-getting-started-number">1</div>
                        <div class="boochat-connect-getting-started-content">
                            <h3>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=boochat-connect-settings')); ?>">
                                    <?php echo esc_html__('Configure API URL', 'boochat-connect'); ?>
                                </a>
                            </h3>
                            <p>
                                <?php echo esc_html__('Go to Settings and configure your n8n webhook URL or external API endpoint.', 'boochat-connect'); ?>
                            </p>
                        </div>
                    </div>
                    <div class="boochat-connect-getting-started-item">
                        <div class="boochat-connect-getting-started-number">2</div>
                        <div class="boochat-connect-getting-started-content">
                            <h3>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=boochat-connect-customization')); ?>">
                                    <?php echo esc_html__('Customize Appearance', 'boochat-connect'); ?>
                                </a>
                            </h3>
                            <p>
                                <?php echo esc_html__('Personalize colors, fonts, welcome message, and chat name to match your brand.', 'boochat-connect'); ?>
                            </p>
                        </div>
                    </div>
                    <div class="boochat-connect-getting-started-item">
                        <div class="boochat-connect-getting-started-number">3</div>
                        <div class="boochat-connect-getting-started-content">
                            <h3>
                                <a href="<?php echo esc_url(home_url()); ?>" target="_blank" rel="noopener noreferrer">
                                    <?php echo esc_html__('Test the Chat', 'boochat-connect'); ?>
                                </a>
                            </h3>
                            <p>
                                <?php echo esc_html__('Visit your website frontend and test the chat integration.', 'boochat-connect'); ?>
                            </p>
                        </div>
                    </div>
                    <div class="boochat-connect-getting-started-item">
                        <div class="boochat-connect-getting-started-number">4</div>
                        <div class="boochat-connect-getting-started-content">
                            <h3>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=boochat-connect-statistics')); ?>">
                                    <?php echo esc_html__('Monitor Statistics', 'boochat-connect'); ?>
                                </a>
                            </h3>
                            <p>
                                <?php echo esc_html__('Track user interactions and analyze chat usage over time.', 'boochat-connect'); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- API Integration Guide -->
            <div class="boochat-connect-card">
                <h2 class="boochat-connect-api-guide">
                    <?php echo esc_html__('API Integration Guide', 'boochat-connect'); ?>
                </h2>
                <p class="boochat-connect-api-guide">
                    <?php echo esc_html__('Your API endpoint should accept POST requests with the following JSON format:', 'boochat-connect'); ?>
                </p>
                <div class="boochat-connect-code-block">
                    <pre><code>{
  "sessionId": "unique-session-id",
  "action": "sendMessage",
  "chatInput": "user message text"
}</code></pre>
                </div>
                <p class="boochat-connect-api-guide">
                    <?php echo esc_html__('The API should respond with JSON in this format:', 'boochat-connect'); ?>
                </p>
                <div class="boochat-connect-code-block">
                    <pre><code>{
  "output": "bot response message"
}</code></pre>
                </div>
                <div class="boochat-connect-info-box">
                    <p>
                        <strong><?php echo esc_html__('n8n Integration:', 'boochat-connect'); ?></strong>
                        <?php
                        $boochat_connect_link_url = 'https://docs.n8n.io/integrations/builtin/core-nodes/n8n-nodes-langchain.chattrigger/';
                        $boochat_connect_link_text = esc_html__('chat trigger node', 'boochat-connect');
                        $boochat_connect_link_open = '<a href="' . esc_url($boochat_connect_link_url) . '" target="_blank" rel="noopener noreferrer" class="boochat-connect-doc-link">';
                        $boochat_connect_link_close = '</a>';
                        printf(
                            /* translators: %1$s: opening link tag, %2$s: closing link tag */
                            esc_html__('Create a %1$schat trigger node%2$s in n8n and use the webhook URL as your API URL. The webhook will receive chat messages for processing in your n8n workflow.', 'boochat-connect'),
                            wp_kses_post($boochat_connect_link_open),
                            wp_kses_post($boochat_connect_link_close)
                        );
                        ?>
                    </p>
                </div>
            </div>

        </div>

        <!-- Right Column -->
        <div class="boochat-connect-sidebar">
            
            <!-- Quick Links -->
            <div class="boochat-connect-card boochat-connect-sidebar-card">
                <h2><?php echo esc_html__('Quick Links', 'boochat-connect'); ?></h2>
                <div class="boochat-connect-quick-links">
                    <a href="<?php echo esc_url(admin_url('admin.php?page=boochat-connect-customization')); ?>" class="button button-primary button-large">
                        <?php echo esc_html__('Customization', 'boochat-connect'); ?>
                    </a>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=boochat-connect-settings')); ?>" class="button button-primary button-large">
                        <?php echo esc_html__('Settings', 'boochat-connect'); ?>
                    </a>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=boochat-connect-statistics')); ?>" class="button button-primary button-large">
                        <?php echo esc_html__('Statistics', 'boochat-connect'); ?>
                    </a>
                </div>
            </div>

            <!-- Features -->
            <div class="boochat-connect-card boochat-connect-sidebar-card">
                <h2><?php echo esc_html__('Features', 'boochat-connect'); ?></h2>
                <ul class="boochat-connect-features-list">
                    <li>
                        <span class="icon">✓</span>
                        <span class="text"><?php echo esc_html__('Modern responsive design', 'boochat-connect'); ?></span>
                    </li>
                    <li>
                        <span class="icon">✓</span>
                        <span class="text"><?php echo esc_html__('Full customization options', 'boochat-connect'); ?></span>
                    </li>
                    <li>
                        <span class="icon">✓</span>
                        <span class="text"><?php echo esc_html__('n8n & API integration', 'boochat-connect'); ?></span>
                    </li>
                    <li>
                        <span class="icon">✓</span>
                        <span class="text"><?php echo esc_html__('Session management', 'boochat-connect'); ?></span>
                    </li>
                    <li>
                        <span class="icon">✓</span>
                        <span class="text"><?php echo esc_html__('Statistics & analytics', 'boochat-connect'); ?></span>
                    </li>
                    <li>
                        <span class="icon">✓</span>
                        <span class="text"><?php echo esc_html__('Multi-language support', 'boochat-connect'); ?></span>
                    </li>
                    <li>
                        <span class="icon">✓</span>
                        <span class="text"><?php echo esc_html__('Lightweight & fast', 'boochat-connect'); ?></span>
                    </li>
                    <li>
                        <span class="icon">✓</span>
                        <span class="text"><?php echo esc_html__('Mobile-friendly', 'boochat-connect'); ?></span>
                    </li>
                </ul>
            </div>

            <!-- Support & Contact -->
            <div class="boochat-connect-card boochat-connect-sidebar-card">
                <h2><?php echo esc_html__('Support & Contact', 'boochat-connect'); ?></h2>
                <div class="boochat-connect-support-list">
                    <div>
                        <div class="boochat-connect-support-item-label">
                            <?php echo esc_html__('Company', 'boochat-connect'); ?>
                        </div>
                        <div class="boochat-connect-support-item-value">
                            BooPixel
                        </div>
                    </div>
                    <div>
                        <div class="boochat-connect-support-item-label">
                            <?php echo esc_html__('Website', 'boochat-connect'); ?>
                        </div>
                        <div>
                            <a href="https://boopixel.com" target="_blank" rel="noopener noreferrer" class="boochat-connect-support-item-link">
                                boopixel.com →
                            </a>
                        </div>
                    </div>
                    <div>
                        <div class="boochat-connect-support-item-label">
                            <?php echo esc_html__('Documentation', 'boochat-connect'); ?>
                        </div>
                        <div>
                            <a href="https://boopixel.com/boochat-connect" target="_blank" rel="noopener noreferrer" class="boochat-connect-support-item-link">
                                <?php echo esc_html__('View Docs →', 'boochat-connect'); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- License -->
            <div class="boochat-connect-card boochat-connect-license-card">
                <p>
                    <?php echo esc_html__('Licensed under', 'boochat-connect'); ?>
                    <a href="https://www.gnu.org/licenses/gpl-2.0.html" target="_blank" rel="noopener noreferrer">
                        <strong>GPLv2+</strong>
                    </a>
                </p>
            </div>

        </div>
    </div>
</div>
