<?php
/**
 * Statistics functionality for BooChat Connect
 *
 * @package BooChat_Connect
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class BooChat_Connect_Statistics
 */
class BooChat_Connect_Statistics {
    
    /**
     * Database instance
     *
     * @var BooChat_Connect_Database
     */
    private $database;
    
    /**
     * Constructor
     *
     * @param BooChat_Connect_Database $database Database instance.
     */
    public function __construct($database) {
        $this->database = $database;
    }
    
    /**
     * Render statistics page
     */
    public function render_page() {
        $ajax_url = admin_url('admin-ajax.php');
        $nonce = wp_create_nonce('boochat-connect-statistics');
        $today = current_time('Y-m-d');
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <div class="boochat-connect-card" style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; box-shadow: 0 1px 1px rgba(0,0,0,.04); margin-top: 20px;">
                <h2><?php echo esc_html(boochat_connect_translate('statistics_interactions')); ?></h2>
                <p><?php echo esc_html(boochat_connect_translate('view_interactions_period')); ?></p>
                
                <!-- Quick Summary -->
                <div style="margin: 30px 0;">
                    <h3><?php echo esc_html(boochat_connect_translate('quick_summary')); ?></h3>
                    <div style="display: flex; gap: 20px; flex-wrap: wrap; margin: 20px 0;">
                        <div style="background: #f0f0f1; padding: 20px; border-radius: 5px; min-width: 180px;">
                            <div style="font-weight: bold; margin-bottom: 10px;"><?php echo esc_html(boochat_connect_translate('last_24_hours')); ?></div>
                            <div id="stats-1day" style="font-size: 32px; font-weight: bold; color: #2271b1;">0</div>
                        </div>
                        <div style="background: #f0f0f1; padding: 20px; border-radius: 5px; min-width: 180px;">
                            <div style="font-weight: bold; margin-bottom: 10px;"><?php echo esc_html(boochat_connect_translate('last_7_days')); ?></div>
                            <div id="stats-7days" style="font-size: 32px; font-weight: bold; color: #2271b1;">0</div>
                        </div>
                        <div style="background: #f0f0f1; padding: 20px; border-radius: 5px; min-width: 180px;">
                            <div style="font-weight: bold; margin-bottom: 10px;"><?php echo esc_html(boochat_connect_translate('last_month')); ?></div>
                            <div id="stats-30days" style="font-size: 32px; font-weight: bold; color: #2271b1;">0</div>
                        </div>
                    </div>
                </div>
                
                <!-- Date Selection -->
                <div style="margin: 30px 0;">
                    <h3><?php echo esc_html(boochat_connect_translate('select_period')); ?></h3>
                    <div style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap; margin: 20px 0;">
                        <div>
                            <label for="date-from" style="display: block; margin-bottom: 5px; font-weight: bold;"><?php echo esc_html(boochat_connect_translate('start_date')); ?></label>
                            <input type="date" id="date-from" value="<?php echo esc_attr($today); ?>" style="padding: 8px 12px; border: 1px solid #8c8f94; border-radius: 4px; font-size: 14px;">
                        </div>
                        <div>
                            <label for="date-to" style="display: block; margin-bottom: 5px; font-weight: bold;"><?php echo esc_html(boochat_connect_translate('end_date')); ?></label>
                            <input type="date" id="date-to" value="<?php echo esc_attr($today); ?>" style="padding: 8px 12px; border: 1px solid #8c8f94; border-radius: 4px; font-size: 14px;">
                        </div>
                        <div>
                            <button type="button" id="load-statistics" class="button button-primary" style="padding: 8px 20px; height: 38px;">
                                <?php echo esc_html(boochat_connect_translate('load_statistics')); ?>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Chart -->
                <div style="margin: 30px 0;">
                    <h3><?php echo esc_html(boochat_connect_translate('interactions_chart')); ?></h3>
                    <div style="background: #f9f9f9; padding: 20px; border: 1px solid #ddd; border-radius: 5px; margin-top: 15px;">
                        <canvas id="interactions-chart" style="max-height: 400px;"></canvas>
                    </div>
                </div>
                
                <!-- Calendar Heatmap -->
                <div style="margin: 30px 0;">
                    <h3><?php echo esc_html(boochat_connect_translate('calendar_heatmap', 'Interaction Calendar')); ?></h3>
                    <p style="margin-bottom: 15px; color: #646970;"><?php echo esc_html(boochat_connect_translate('calendar_description', 'Visualize user interactions over the past year. Darker colors indicate more interactions.')); ?></p>
                    <div id="calendar-heatmap" style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 5px; overflow-x: auto;">
                        <div id="calendar-container"></div>
                        <div style="margin-top: 15px; display: flex; align-items: center; gap: 10px; font-size: 12px; color: #646970;">
                            <span><?php echo esc_html(boochat_connect_translate('less', 'Less')); ?></span>
                            <div style="display: flex; gap: 3px;">
                                <div style="width: 12px; height: 12px; background: #ebedf0; border: 1px solid #ddd; border-radius: 2px;"></div>
                                <div style="width: 12px; height: 12px; background: #c6e48b; border: 1px solid #ddd; border-radius: 2px;"></div>
                                <div style="width: 12px; height: 12px; background: #7bc96f; border: 1px solid #ddd; border-radius: 2px;"></div>
                                <div style="width: 12px; height: 12px; background: #239a3b; border: 1px solid #ddd; border-radius: 2px;"></div>
                                <div style="width: 12px; height: 12px; background: #196127; border: 1px solid #ddd; border-radius: 2px;"></div>
                            </div>
                            <span><?php echo esc_html(boochat_connect_translate('more', 'More')); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <script type="text/javascript">
        var boochatConnectStats = {
            ajaxUrl: <?php echo json_encode($ajax_url); ?>,
            nonce: <?php echo json_encode($nonce); ?>,
            loadStatisticsText: <?php echo json_encode(boochat_connect_translate('load_statistics')); ?>,
            loadingText: <?php echo json_encode(boochat_connect_translate('loading', 'Loading...')); ?>,
            selectDatesText: <?php echo json_encode(boochat_connect_translate('select_dates', 'Please select start and end dates.')); ?>,
            invalidDateRangeText: <?php echo json_encode(boochat_connect_translate('invalid_date_range', 'Start date must be before end date.')); ?>,
            errorLoadingText: <?php echo json_encode(boochat_connect_translate('error_loading_statistics', 'Error loading statistics: ')); ?>,
            errorConnectingText: <?php echo json_encode(boochat_connect_translate('error_connecting_server', 'Error connecting to server. Please try again.')); ?>
        };
        </script>
        <?php
    }
}
