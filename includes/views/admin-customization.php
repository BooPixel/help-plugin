<?php
/**
 * Admin customization page template
 *
 * @package BooChat_Connect
 * @var bool   $customization_updated Whether customization was just saved.
 * @var array  $settings              Customization settings array.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap boochat-connect-wrap">
    <h1><?php echo esc_html(boochat_connect_translate('chat_customization')); ?></h1>
    
    <?php if ($customization_updated): ?>
        <div class="notice notice-success is-dismissible">
            <p><?php echo esc_html(boochat_connect_translate('customization_saved')); ?></p>
        </div>
    <?php endif; ?>
    
    <div class="boochat-connect-content">
        <div class="boochat-connect-card">
            <h2><?php echo esc_html(boochat_connect_translate('chat_customization')); ?></h2>
            <p><?php echo esc_html(boochat_connect_translate('customize_colors_typography')); ?></p>
            
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <?php wp_nonce_field('boochat_connect_save_customization', 'boochat_connect_customization_nonce'); ?>
                <input type="hidden" name="action" value="boochat_connect_save_customization">
                
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label for="chat_name"><?php echo esc_html(boochat_connect_translate('chat_name')); ?></label>
                            </th>
                            <td>
                                <input 
                                    type="text" 
                                    id="chat_name" 
                                    name="chat_name" 
                                    value="<?php echo esc_attr($settings['chat_name']); ?>" 
                                    class="regular-text"
                                    placeholder="<?php echo esc_attr(boochat_connect_translate('chat_name_default')); ?>"
                                >
                                <p class="description">
                                    <?php echo esc_html(boochat_connect_translate('name_displayed_header')); ?>
                                </p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="welcome_message"><?php echo esc_html(boochat_connect_translate('welcome_message')); ?></label>
                            </th>
                            <td>
                                <textarea 
                                    id="welcome_message" 
                                    name="welcome_message" 
                                    rows="3"
                                    class="large-text"
                                    placeholder="<?php echo esc_attr(boochat_connect_translate('welcome_message_default')); ?>"
                                ><?php echo esc_textarea($settings['welcome_message']); ?></textarea>
                                <p class="description">
                                    <?php echo esc_html(boochat_connect_translate('welcome_message_displayed')); ?>
                                </p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="primary_color"><?php echo esc_html(boochat_connect_translate('primary_color')); ?></label>
                            </th>
                            <td>
                                <input 
                                    type="color" 
                                    id="primary_color" 
                                    name="primary_color" 
                                    value="<?php echo esc_attr($settings['primary_color']); ?>"
                                >
                                <p class="description">
                                    <?php echo esc_html(boochat_connect_translate('primary_gradient_color')); ?>
                                </p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="secondary_color"><?php echo esc_html(boochat_connect_translate('secondary_color')); ?></label>
                            </th>
                            <td>
                                <input 
                                    type="color" 
                                    id="secondary_color" 
                                    name="secondary_color" 
                                    value="<?php echo esc_attr($settings['secondary_color']); ?>"
                                >
                                <p class="description">
                                    <?php echo esc_html(boochat_connect_translate('secondary_gradient_color')); ?>
                                </p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="chat_bg_color"><?php echo esc_html(boochat_connect_translate('chat_bg_color')); ?></label>
                            </th>
                            <td>
                                <input 
                                    type="color" 
                                    id="chat_bg_color" 
                                    name="chat_bg_color" 
                                    value="<?php echo esc_attr($settings['chat_bg_color']); ?>"
                                >
                                <p class="description">
                                    <?php echo esc_html(boochat_connect_translate('chat_window_background')); ?>
                                </p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="text_color"><?php echo esc_html(boochat_connect_translate('text_color')); ?></label>
                            </th>
                            <td>
                                <input 
                                    type="color" 
                                    id="text_color" 
                                    name="text_color" 
                                    value="<?php echo esc_attr($settings['text_color']); ?>"
                                >
                                <p class="description">
                                    <?php echo esc_html(boochat_connect_translate('message_text_color')); ?>
                                </p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="font_family"><?php echo esc_html(boochat_connect_translate('font_family')); ?></label>
                            </th>
                            <td>
                                <select id="font_family" name="font_family" class="regular-text">
                                    <option value="Arial, sans-serif" <?php selected($settings['font_family'], 'Arial, sans-serif'); ?>>Arial</option>
                                    <option value="'Helvetica Neue', Helvetica, sans-serif" <?php selected($settings['font_family'], "'Helvetica Neue', Helvetica, sans-serif"); ?>>Helvetica</option>
                                    <option value="Georgia, serif" <?php selected($settings['font_family'], 'Georgia, serif'); ?>>Georgia</option>
                                    <option value="'Times New Roman', Times, serif" <?php selected($settings['font_family'], "'Times New Roman', Times, serif"); ?>>Times New Roman</option>
                                    <option value="'Courier New', Courier, monospace" <?php selected($settings['font_family'], "'Courier New', Courier, monospace"); ?>>Courier New</option>
                                    <option value="Verdana, sans-serif" <?php selected($settings['font_family'], 'Verdana, sans-serif'); ?>>Verdana</option>
                                    <option value="'Trebuchet MS', sans-serif" <?php selected($settings['font_family'], "'Trebuchet MS', sans-serif"); ?>>Trebuchet MS</option>
                                    <option value="'Open Sans', sans-serif" <?php selected($settings['font_family'], "'Open Sans', sans-serif"); ?>>Open Sans</option>
                                    <option value="'Roboto', sans-serif" <?php selected($settings['font_family'], "'Roboto', sans-serif"); ?>>Roboto</option>
                                </select>
                                <p class="description">
                                    <?php echo esc_html(boochat_connect_translate('font_family_chat')); ?>
                                </p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="font_size"><?php echo esc_html(boochat_connect_translate('font_size')); ?></label>
                            </th>
                            <td>
                                <select id="font_size" name="font_size" class="regular-text">
                                    <option value="12px" <?php selected($settings['font_size'], '12px'); ?>>12px - <?php echo esc_html(boochat_connect_translate('small', 'Small')); ?></option>
                                    <option value="14px" <?php selected($settings['font_size'], '14px'); ?>>14px - <?php echo esc_html(boochat_connect_translate('normal', 'Normal')); ?></option>
                                    <option value="16px" <?php selected($settings['font_size'], '16px'); ?>>16px - <?php echo esc_html(boochat_connect_translate('medium', 'Medium')); ?></option>
                                    <option value="18px" <?php selected($settings['font_size'], '18px'); ?>>18px - <?php echo esc_html(boochat_connect_translate('large', 'Large')); ?></option>
                                    <option value="20px" <?php selected($settings['font_size'], '20px'); ?>>20px - <?php echo esc_html(boochat_connect_translate('extra_large', 'Extra Large')); ?></option>
                                </select>
                                <p class="description">
                                    <?php echo esc_html(boochat_connect_translate('font_size_messages')); ?>
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <?php submit_button(boochat_connect_translate('save_customizations')); ?>
            </form>
        </div>
    </div>
</div>

