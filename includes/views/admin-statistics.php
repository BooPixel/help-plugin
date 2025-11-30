<?php
/**
 * Admin statistics page template
 *
 * @package BooPixel_AI_Chat_For_N8n
 * @var string $ajax_url      AJAX URL.
 * @var string $nonce         Nonce for AJAX requests.
 * @var string $today         Today's date in Y-m-d format.
 * @var string $seven_days_ago Date 7 days ago in Y-m-d format.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Statistics page is available to all users - no license check needed
?>
<div class="wrap boochat-connect-wrap">
    <div class="boochat-connect-header">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    </div>
    
    <div class="boochat-connect-statistics-layout">
        <!-- Left Column: Charts and Statistics -->
        <div class="boochat-connect-statistics-left">
            <div class="boochat-connect-card boochat-connect-statistics-card">
                <h2><?php echo esc_html(boopixel_ai_chat_for_n8n_translate('statistics_interactions')); ?></h2>
                <p><?php echo esc_html(boopixel_ai_chat_for_n8n_translate('view_interactions_period')); ?></p>
                
                <!-- Quick Summary -->
                <div class="boochat-connect-statistics-section">
                    <h3><?php echo esc_html(boopixel_ai_chat_for_n8n_translate('quick_summary')); ?></h3>
                    <div class="boochat-connect-statistics-summary">
                        <div class="boochat-connect-statistics-box">
                            <div class="boochat-connect-statistics-box-label"><?php echo esc_html(boopixel_ai_chat_for_n8n_translate('last_24_hours')); ?></div>
                            <div id="stats-1day" class="boochat-connect-statistics-box-value">0</div>
                        </div>
                        <div class="boochat-connect-statistics-box">
                            <div class="boochat-connect-statistics-box-label"><?php echo esc_html(boopixel_ai_chat_for_n8n_translate('last_7_days')); ?></div>
                            <div id="stats-7days" class="boochat-connect-statistics-box-value">0</div>
                        </div>
                        <div class="boochat-connect-statistics-box">
                            <div class="boochat-connect-statistics-box-label"><?php echo esc_html(boopixel_ai_chat_for_n8n_translate('last_month')); ?></div>
                            <div id="stats-30days" class="boochat-connect-statistics-box-value">0</div>
                        </div>
                    </div>
                </div>
                
                <!-- Date Selection -->
                <div class="boochat-connect-statistics-section">
                    <h3><?php echo esc_html(boopixel_ai_chat_for_n8n_translate('select_period')); ?></h3>
                    <!-- Message area for errors/success -->
                    <div id="statistics-messages"></div>
                    <div class="boochat-connect-statistics-filters">
                        <div class="boochat-connect-statistics-filter-group">
                            <label for="date-from"><?php echo esc_html(boopixel_ai_chat_for_n8n_translate('start_date')); ?></label>
                            <input type="date" id="date-from" value="<?php echo esc_attr($seven_days_ago); ?>">
                        </div>
                        <div class="boochat-connect-statistics-filter-group">
                            <label for="date-to"><?php echo esc_html(boopixel_ai_chat_for_n8n_translate('end_date')); ?></label>
                            <input type="date" id="date-to" value="<?php echo esc_attr($today); ?>">
                        </div>
                        <div class="boochat-connect-statistics-filter-group">
                            <button type="button" id="load-statistics" class="button button-primary">
                                <?php echo esc_html(boopixel_ai_chat_for_n8n_translate('load_statistics')); ?>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Chart -->
                <div class="boochat-connect-statistics-section">
                    <h3><?php echo esc_html(boopixel_ai_chat_for_n8n_translate('interactions_chart')); ?></h3>
                    <div class="boochat-connect-statistics-chart-container">
                        <canvas id="interactions-chart" class="boochat-connect-statistics-chart"></canvas>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>
