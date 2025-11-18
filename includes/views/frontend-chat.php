<?php
/**
 * Frontend chat widget template
 *
 * @package BooChat_Connect
 * @var array $settings Customization settings array.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>
<div id="boochat-connect-popup" class="boochat-connect-popup">
    <div class="boochat-connect-popup-content">
        <div class="boochat-connect-popup-header">
            <span class="boochat-connect-popup-icon">💬</span>
            <span class="boochat-connect-popup-text"><?php echo esc_html(boochat_connect_translate('need_help')); ?></span>
        </div>
        <button class="boochat-connect-popup-close" aria-label="<?php echo esc_attr(boochat_connect_translate('close')); ?>">&times;</button>
    </div>
</div>

<div id="boochat-connect-chat-window" class="boochat-connect-chat-window">
    <div class="boochat-connect-chat-header">
        <div class="boochat-connect-chat-title">
            <?php if (!empty($settings['chat_icon'])): ?>
                <img src="<?php echo esc_url($settings['chat_icon']); ?>" alt="<?php echo esc_attr(boochat_connect_translate('chat_icon', 'Chat Icon')); ?>" class="boochat-connect-chat-icon-image">
            <?php else: ?>
            <span class="boochat-connect-chat-icon">💬</span>
            <?php endif; ?>
            <span><?php echo esc_html($settings['chat_name']); ?></span>
        </div>
        <button class="boochat-connect-chat-close" aria-label="<?php echo esc_attr(boochat_connect_translate('close_chat')); ?>">&times;</button>
    </div>
    <div class="boochat-connect-chat-body">
        <div class="boochat-connect-chat-messages" id="boochat-connect-chat-messages">
            <div class="boochat-connect-chat-message boochat-connect-chat-message-system">
                <p><?php echo esc_html($settings['welcome_message']); ?></p>
            </div>
        </div>
    </div>
    <div class="boochat-connect-chat-footer">
        <form id="boochat-connect-chat-form" class="boochat-connect-chat-form">
            <input 
                type="text" 
                id="boochat-connect-chat-input" 
                class="boochat-connect-chat-input" 
                placeholder="<?php echo esc_attr(boochat_connect_translate('type_message')); ?>"
                autocomplete="off"
            >
            <button type="submit" class="boochat-connect-chat-send">
                <span><?php echo esc_html(boochat_connect_translate('send')); ?></span>
            </button>
        </form>
    </div>
</div>

