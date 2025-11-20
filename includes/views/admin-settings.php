<?php
/**
 * Admin settings page template
 *
 * @package BooChat_Connect
 * @var bool   $settings_updated Whether settings were just saved.
 * @var string $api_url          API URL.
 * @var string $current_language Current language setting.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

$boochat_connect_wp_locale = get_locale();
$boochat_connect_wp_language = boochat_connect_get_language_from_locale($boochat_connect_wp_locale);
$boochat_connect_wp_language_name = '';
switch ($boochat_connect_wp_language) {
    case 'pt':
        $boochat_connect_wp_language_name = esc_html__('Português', 'boochat-connect');
        break;
    case 'es':
        $boochat_connect_wp_language_name = esc_html__('Español', 'boochat-connect');
        break;
    default:
        $boochat_connect_wp_language_name = esc_html__('English', 'boochat-connect');
}
?>
<div class="wrap boochat-connect-wrap">
    <div class="boochat-connect-header">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    </div>
    
    <?php if ($settings_updated): ?>
        <div class="notice notice-success is-dismissible">
            <p><?php echo esc_html(boochat_connect_translate('settings_saved')); ?></p>
        </div>
    <?php endif; ?>
    
    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" id="boochat-connect-settings-form">
    <?php wp_nonce_field('boochat_connect_save_settings', 'boochat_connect_settings_nonce'); ?>
    <input type="hidden" name="action" value="boochat_connect_save_settings">
    
    <div class="boochat-connect-content boochat-connect-settings-layout">
        <!-- API Settings and Information Cards Row -->
        <div class="boochat-connect-settings-top-row">
            <!-- API Settings Card -->
            <div class="boochat-connect-settings-api">
            <div class="boochat-connect-card">
                <h2><?php echo esc_html(boochat_connect_translate('api_settings')); ?></h2>
                <p><?php echo esc_html(boochat_connect_translate('configure_api_url')); ?></p>
                
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label for="api_url"><?php echo esc_html(boochat_connect_translate('api_url', 'API URL')); ?></label>
                            </th>
                            <td>
                                <input 
                                    type="url" 
                                    id="api_url" 
                                    name="api_url" 
                                    value="<?php echo esc_attr($api_url); ?>" 
                                    class="regular-text"
                                    placeholder="https://example.com/webhook/chat"
                                    required
                                >
                                <p class="description">
                                    <?php echo esc_html(boochat_connect_translate('api_webhook_url')); ?>
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
            <!-- Information Card -->
            <div class="boochat-connect-settings-info">
                <div class="boochat-connect-card">
                <h2><?php echo esc_html(boochat_connect_translate('information', 'Information')); ?></h2>
                <p><?php echo esc_html(boochat_connect_translate('api_will_process')); ?></p>
                <p>
                    <strong><?php echo esc_html(boochat_connect_translate('status', 'Status')); ?>:</strong> 
                    <?php if (!empty($api_url)): ?>
                        <span class="boochat-connect-status-success"><?php echo esc_html(boochat_connect_translate('api_configured')); ?></span>
                    <?php else: ?>
                        <span class="boochat-connect-status-error"><?php echo esc_html(boochat_connect_translate('api_not_configured')); ?></span>
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>
        
        <!-- Default Settings Card -->
        <div class="boochat-connect-settings-default">
            <div class="boochat-connect-card">
                <h2><?php echo esc_html(boochat_connect_translate('default_settings', 'Default Settings')); ?></h2>
                
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label for="language"><?php echo esc_html(boochat_connect_translate('language', 'Language')); ?></label>
                            </th>
                            <td>
                                <select id="language" name="language" class="regular-text">
                                    <option value="" <?php selected($current_language, '', true); ?>><?php echo esc_html(boochat_connect_translate('language_auto', 'Auto (WordPress Language)')); ?> (<?php echo esc_html($boochat_connect_wp_language_name); ?>)</option>
                                    <option value="en" <?php selected($current_language, 'en'); ?>><?php echo esc_html__('English', 'boochat-connect'); ?></option>
                                    <option value="pt" <?php selected($current_language, 'pt'); ?>><?php echo esc_html__('Português', 'boochat-connect'); ?></option>
                                    <option value="es" <?php selected($current_language, 'es'); ?>><?php echo esc_html__('Español', 'boochat-connect'); ?></option>
                                </select>
                                <p class="description">
                                    <?php echo esc_html(boochat_connect_translate('language_description')); ?>
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Single Save Button Card -->
    <div class="boochat-connect-save-button-card">
        <div class="boochat-connect-card boochat-connect-save-card">
            <?php submit_button(boochat_connect_translate('save_settings'), 'primary', 'submit', false); ?>
        </div>
    </div>
    </form>
</div>
