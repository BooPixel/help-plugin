<?php
/**
 * Admin main page template
 *
 * @package BooPixel_AI_Chat_For_N8n
 * @var bool   $api_configured Whether API is configured.
 * @var string $api_url        API URL.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap boochat-connect-wrap">
    <!-- Welcome Banner -->
    <div class="boochat-connect-welcome-banner">
        <h1><?php echo esc_html__('Welcome to BooPixel AI Chat Connect for n8n!', 'boopixel-ai-chat-for-n8n'); ?></h1>
        <p>
            <a href="https://boopixel.com/boochat-connect" target="_blank" rel="noopener noreferrer" class="boochat-connect-welcome-link">
                <?php echo esc_html(boopixel_ai_chat_for_n8n_translate('version', 'Version')); ?> <?php echo esc_html(BOOPIXEL_AI_CHAT_FOR_N8N_VERSION); ?>
            </a>
        </p>
        <button type="button" class="boochat-connect-dismiss-button"><?php echo esc_html__('Dismiss', 'boopixel-ai-chat-for-n8n'); ?></button>
    </div>
    
    <div class="boochat-connect-header">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    </div>
    
    <div class="boochat-connect-content">
        
        <!-- Left Column -->
        <div class="boochat-connect-content-left">
            
            <!-- Welcome Card -->
            <div class="boochat-connect-card boochat-connect-card-hero">
                <div class="boochat-connect-card-hero">
                    <div class="boochat-connect-card-hero-content">
                        <h2>
                            <?php echo esc_html__('Welcome to BooPixel AI Chat Connect for n8n', 'boopixel-ai-chat-for-n8n'); ?>
                        </h2>
                        <p>
                            <?php echo esc_html__('Modern, lightweight chatbot popup that integrates seamlessly with n8n. Automate workflows, respond in real-time, collect leads, and connect to any AI model or external service.', 'boopixel-ai-chat-for-n8n'); ?>
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
                            <h3><?php echo esc_html__('API Status', 'boopixel-ai-chat-for-n8n'); ?></h3>
                            <p>
                                <?php echo $api_configured ? esc_html__('Configured', 'boopixel-ai-chat-for-n8n') : esc_html__('Not Configured', 'boopixel-ai-chat-for-n8n'); ?>
                            </p>
                        </div>
                    </div>
                    <?php if (!$api_configured): ?>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=boopixel-ai-chat-for-n8n-settings')); ?>" class="button button-small">
                            <?php echo esc_html__('Configure Now', 'boopixel-ai-chat-for-n8n'); ?>
                        </a>
                    <?php endif; ?>
                </div>
                
                <div class="boochat-connect-card boochat-connect-status-card">
                    <div class="boochat-connect-status-card-content">
                        <div class="boochat-connect-status-icon success">
                            ✓
                        </div>
                        <div class="boochat-connect-status-info">
                            <h3><?php echo esc_html__('Chat Widget', 'boopixel-ai-chat-for-n8n'); ?></h3>
                            <p><?php echo esc_html__('Active', 'boopixel-ai-chat-for-n8n'); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Getting Started -->
            <div class="boochat-connect-card">
                <h2 class="boochat-connect-getting-started">
                    <?php echo esc_html__('Getting Started', 'boopixel-ai-chat-for-n8n'); ?>
                </h2>
                <div class="boochat-connect-getting-started-list">
                    <div class="boochat-connect-getting-started-item">
                        <div class="boochat-connect-getting-started-number">1</div>
                        <div class="boochat-connect-getting-started-content">
                            <h3>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=boopixel-ai-chat-for-n8n-settings')); ?>">
                                    <?php echo esc_html__('Configure API URL', 'boopixel-ai-chat-for-n8n'); ?>
                                </a>
                            </h3>
                            <p>
                                <?php echo esc_html__('Go to Settings and configure your n8n webhook URL or external API endpoint.', 'boopixel-ai-chat-for-n8n'); ?>
                            </p>
                        </div>
                    </div>
                    <div class="boochat-connect-getting-started-item">
                        <div class="boochat-connect-getting-started-number">2</div>
                        <div class="boochat-connect-getting-started-content">
                            <h3>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=boopixel-ai-chat-for-n8n-customization')); ?>">
                                    <?php echo esc_html__('Customize Appearance', 'boopixel-ai-chat-for-n8n'); ?>
                                </a>
                            </h3>
                            <p>
                                <?php echo esc_html__('Personalize colors, fonts, welcome message, and chat name to match your brand.', 'boopixel-ai-chat-for-n8n'); ?>
                            </p>
                        </div>
                    </div>
                    <div class="boochat-connect-getting-started-item">
                        <div class="boochat-connect-getting-started-number">3</div>
                        <div class="boochat-connect-getting-started-content">
                            <h3>
                                <a href="<?php echo esc_url(home_url()); ?>" target="_blank" rel="noopener noreferrer">
                                    <?php echo esc_html__('Test the Chat', 'boopixel-ai-chat-for-n8n'); ?>
                                </a>
                            </h3>
                            <p>
                                <?php echo esc_html__('Visit your website frontend and test the chat integration.', 'boopixel-ai-chat-for-n8n'); ?>
                            </p>
                        </div>
                    </div>
                    <?php /* <div class="boochat-connect-getting-started-item">
                        <div class="boochat-connect-getting-started-number">4</div>
                        <div class="boochat-connect-getting-started-content">
                            <h3>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=boopixel-ai-chat-for-n8n-statistics')); ?>">
                                    <?php echo esc_html__('Monitor Statistics', 'boopixel-ai-chat-for-n8n'); ?>
                                </a>
                            </h3>
                            <p>
                                <?php echo esc_html__('Track user interactions and analyze chat usage over time.', 'boopixel-ai-chat-for-n8n'); ?>
                            </p>
                        </div>
                    </div> */ ?>
                </div>
            </div>

            <!-- API Integration Guide -->
            <div class="boochat-connect-card">
                <h2 class="boochat-connect-api-guide">
                    <?php echo esc_html__('API Integration Guide', 'boopixel-ai-chat-for-n8n'); ?>
                </h2>
                <p class="boochat-connect-api-guide">
                    <?php echo esc_html__('Your API endpoint should accept POST requests with the following JSON format:', 'boopixel-ai-chat-for-n8n'); ?>
                </p>
                <div class="boochat-connect-code-block">
                    <pre><code>{
  "sessionId": "unique-session-id",
  "action": "sendMessage",
  "chatInput": "user message text"
}</code></pre>
                </div>
                <p class="boochat-connect-api-guide">
                    <?php echo esc_html__('The API should respond with JSON in this format:', 'boopixel-ai-chat-for-n8n'); ?>
                </p>
                <div class="boochat-connect-code-block">
                    <pre><code>{
  "output": "bot response message"
}</code></pre>
                </div>
                <div class="boochat-connect-info-box">
                    <p>
                        <strong><?php echo esc_html__('n8n Integration:', 'boopixel-ai-chat-for-n8n'); ?></strong>
                        <?php
                        $boopixel_ai_chat_for_n8n_link_url = 'https://docs.n8n.io/integrations/builtin/core-nodes/n8n-nodes-langchain.chattrigger/';
                        $boopixel_ai_chat_for_n8n_link_text = esc_html__('chat trigger node', 'boopixel-ai-chat-for-n8n');
                        $boopixel_ai_chat_for_n8n_link_open = '<a href="' . esc_url($boopixel_ai_chat_for_n8n_link_url) . '" target="_blank" rel="noopener noreferrer" class="boochat-connect-doc-link">';
                        $boopixel_ai_chat_for_n8n_link_close = '</a>';
                        printf(
                            /* translators: %1$s: opening link tag, %2$s: closing link tag */
                            esc_html__('Create a %1$schat trigger node%2$s in n8n and use the webhook URL as your API URL. The webhook will receive chat messages for processing in your n8n workflow.', 'boopixel-ai-chat-for-n8n'),
                            wp_kses_post($boopixel_ai_chat_for_n8n_link_open),
                            wp_kses_post($boopixel_ai_chat_for_n8n_link_close)
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
                <h2><?php echo esc_html__('Quick Links', 'boopixel-ai-chat-for-n8n'); ?></h2>
                <div class="boochat-connect-quick-links">
                    <a href="<?php echo esc_url(admin_url('admin.php?page=boopixel-ai-chat-for-n8n-customization')); ?>" class="button button-primary button-large">
                        <?php echo esc_html__('Customization', 'boopixel-ai-chat-for-n8n'); ?>
                    </a>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=boopixel-ai-chat-for-n8n-settings')); ?>" class="button button-primary button-large">
                        <?php echo esc_html__('Settings', 'boopixel-ai-chat-for-n8n'); ?>
                    </a>
                    <?php /* <a href="<?php echo esc_url(admin_url('admin.php?page=boopixel-ai-chat-for-n8n-statistics')); ?>" class="button button-primary button-large">
                        <?php echo esc_html__('Statistics', 'boopixel-ai-chat-for-n8n'); ?>
                    </a> */ ?>
                </div>
            </div>

            <!-- Features -->
            <div class="boochat-connect-card boochat-connect-sidebar-card">
                <h2><?php echo esc_html__('Features', 'boopixel-ai-chat-for-n8n'); ?></h2>
                <ul class="boochat-connect-features-list">
                    <li>
                        <span class="icon">✓</span>
                        <span class="text"><?php echo esc_html__('Modern responsive design', 'boopixel-ai-chat-for-n8n'); ?></span>
                    </li>
                    <li>
                        <span class="icon">✓</span>
                        <span class="text"><?php echo esc_html__('Full customization options', 'boopixel-ai-chat-for-n8n'); ?></span>
                    </li>
                    <li>
                        <span class="icon">✓</span>
                        <span class="text"><?php echo esc_html__('n8n & API integration', 'boopixel-ai-chat-for-n8n'); ?></span>
                    </li>
                    <li>
                        <span class="icon">✓</span>
                        <span class="text"><?php echo esc_html__('Session management', 'boopixel-ai-chat-for-n8n'); ?></span>
                    </li>
                    <?php /* <li>
                        <span class="icon">✓</span>
                        <span class="text"><?php echo esc_html__('Statistics & analytics', 'boopixel-ai-chat-for-n8n'); ?></span>
                    </li> */ ?>
                    <li>
                        <span class="icon">✓</span>
                        <span class="text"><?php echo esc_html__('Multi-language support', 'boopixel-ai-chat-for-n8n'); ?></span>
                    </li>
                    <li>
                        <span class="icon">✓</span>
                        <span class="text"><?php echo esc_html__('Lightweight & fast', 'boopixel-ai-chat-for-n8n'); ?></span>
                    </li>
                    <li>
                        <span class="icon">✓</span>
                        <span class="text"><?php echo esc_html__('Mobile-friendly', 'boopixel-ai-chat-for-n8n'); ?></span>
                    </li>
                </ul>
            </div>

            <!-- Support & Contact -->
            <div class="boochat-connect-card boochat-connect-sidebar-card">
                <h2><?php echo esc_html__('Support & Contact', 'boopixel-ai-chat-for-n8n'); ?></h2>
                <div class="boochat-connect-support-list">
                    <div>
                        <div class="boochat-connect-support-item-label">
                            <?php echo esc_html__('Company', 'boopixel-ai-chat-for-n8n'); ?>
                        </div>
                        <div class="boochat-connect-support-item-value">
                            BooPixel
                        </div>
                    </div>
                    <div>
                        <div class="boochat-connect-support-item-label">
                            <?php echo esc_html__('Website', 'boopixel-ai-chat-for-n8n'); ?>
                        </div>
                        <div>
                            <a href="https://boopixel.com" target="_blank" rel="noopener noreferrer" class="boochat-connect-support-item-link">
                                boopixel.com →
                            </a>
                        </div>
                    </div>
                    <div>
                        <div class="boochat-connect-support-item-label">
                            <?php echo esc_html__('Documentation', 'boopixel-ai-chat-for-n8n'); ?>
                        </div>
                        <div>
                            <a href="https://boopixel.com/boochat-connect" target="_blank" rel="noopener noreferrer" class="boochat-connect-support-item-link">
                                <?php echo esc_html__('View Docs →', 'boopixel-ai-chat-for-n8n'); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- License -->
            <div class="boochat-connect-card boochat-connect-license-card">
                <p>
                    <?php echo esc_html__('Licensed under', 'boopixel-ai-chat-for-n8n'); ?>
                    <a href="https://www.gnu.org/licenses/gpl-2.0.html" target="_blank" rel="noopener noreferrer">
                        <strong>GPLv2+</strong>
                    </a>
                </p>
            </div>

        </div>
    </div>
</div>
