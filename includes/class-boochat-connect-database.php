<?php
/**
 * Database operations for BooChat Connect
 *
 * @package BooChat_Connect
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class BooChat_Connect_Database
 */
class BooChat_Connect_Database {
    
    /**
     * Get table name
     *
     * @return string Table name
     */
    private function get_table_name() {
        global $wpdb;
        return $wpdb->prefix . 'boochat_connect_interactions';
    }
    
    /**
     * Create interactions table
     */
    public function create_table() {
        global $wpdb;
        
        $table_name = $this->get_table_name();
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            session_id varchar(255) NOT NULL,
            message text,
            message_type varchar(20) DEFAULT 'user',
            interaction_date datetime NOT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY session_id (session_id),
            KEY interaction_date (interaction_date),
            KEY message_type (message_type)
        ) $charset_collate;";
        
        // If table already exists, add columns if they don't exist
        $columns_to_check = array('message', 'message_type');
        
        foreach ($columns_to_check as $column) {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Required to check table schema
            $column_exists = $wpdb->get_results($wpdb->prepare(
                "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = %s",
                DB_NAME,
                $table_name,
                $column
            ));
            
            if (empty($column_exists)) {
                $table_name_escaped = esc_sql($table_name);
                if ($column === 'message') {
                    // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange -- Table name is escaped with esc_sql(), required for schema update
                    $wpdb->query("ALTER TABLE `{$table_name_escaped}` ADD COLUMN message text AFTER session_id");
                } elseif ($column === 'message_type') {
                    // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange -- Table name is escaped with esc_sql(), required for schema update
                    $wpdb->query("ALTER TABLE `{$table_name_escaped}` ADD COLUMN message_type varchar(20) DEFAULT 'user' AFTER message");
                }
            }
        }
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Log interaction
     *
     * @param string $session_id Session ID.
     * @param string $message Message content.
     * @param string $message_type Message type (user or robot).
     */
    public function log_interaction($session_id, $message = '', $message_type = 'user') {
        global $wpdb;
        
        $table_name = $this->get_table_name();
        $this->create_table();
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Required for custom plugin table
        $wpdb->insert(
            $table_name,
            array(
                'session_id' => $session_id,
                'message' => sanitize_textarea_field($message),
                'message_type' => $message_type,
                'interaction_date' => current_time('mysql'),
                'created_at' => current_time('mysql')
            ),
            array('%s', '%s', '%s', '%s', '%s')
        );
    }
    
    /**
     * Get interaction count by days (unique sessions)
     *
     * @param int $days Number of days.
     * @return int Count of unique sessions
     */
    public function get_interactions_count($days) {
        global $wpdb;
        
        $table_name = $this->get_table_name();
        $this->create_table();
        
        // Calculate date from (X days ago at 00:00:00)
        $date_from = gmdate('Y-m-d 00:00:00', strtotime("-{$days} days", current_time('timestamp')));
        
        // Use esc_sql for table name - cannot use prepare() with table names
        $table_name_escaped = esc_sql($table_name);
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name is escaped with esc_sql()
        $query = "SELECT COUNT(DISTINCT session_id) FROM `{$table_name_escaped}` WHERE interaction_date >= %s";
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.NotPrepared -- Required for custom plugin table statistics, values are prepared correctly
        $count = $wpdb->get_var($wpdb->prepare($query, $date_from));
        
        return intval($count ? $count : 0);
    }
    
    /**
     * Get chart data
     *
     * @param string $date_from Start date.
     * @param string $date_to End date.
     * @return array Chart data with labels and data arrays
     */
    public function get_chart_data($date_from, $date_to) {
        global $wpdb;
        
        $table_name = $this->get_table_name();
        $this->create_table();
        
        $date_from_formatted = gmdate('Y-m-d', strtotime($date_from));
        $date_to_formatted = gmdate('Y-m-d', strtotime($date_to));
        $date_from_start = $date_from_formatted . ' 00:00:00';
        $date_to_end = $date_to_formatted . ' 23:59:59';
        
        // Use esc_sql for table name - cannot use prepare() with table names
        $table_name_escaped = esc_sql($table_name);
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name is escaped with esc_sql()
        $query = "SELECT DATE(interaction_date) as date, COUNT(DISTINCT session_id) as count 
            FROM `{$table_name_escaped}` 
            WHERE interaction_date >= %s AND interaction_date <= %s 
            GROUP BY DATE(interaction_date) 
            ORDER BY date ASC";
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.NotPrepared -- Required for custom plugin table statistics, values are prepared correctly
        $results = $wpdb->get_results($wpdb->prepare($query, $date_from_start, $date_to_end));
        
        $labels = array();
        $data = array();
        
        // Create map of results
        $results_map = array();
        if ($results && is_array($results)) {
            foreach ($results as $result) {
                if (isset($result->date) && isset($result->count)) {
                    $results_map[$result->date] = intval($result->count);
                }
            }
        }
        
        // Fill all dates in period
        try {
            $start = new DateTime($date_from_formatted);
            $end = new DateTime($date_to_formatted);
            $interval = new DateInterval('P1D');
            $end->modify('+1 day');
            $period = new DatePeriod($start, $interval, $end);
            
            foreach ($period as $date) {
                $date_str = $date->format('Y-m-d');
                $labels[] = $date->format('d/m');
                $data[] = isset($results_map[$date_str]) ? $results_map[$date_str] : 0;
            }
        } catch (Exception $e) {
            return array('labels' => array(), 'data' => array());
        }
        
        return array('labels' => $labels, 'data' => $data);
    }
    
    /**
     * Get calendar heatmap data (last 365 days)
     *
     * @return array Calendar data map
     */
    public function get_calendar_data() {
        global $wpdb;
        
        $table_name = $this->get_table_name();
        $this->create_table();
        
        $date_from = gmdate('Y-m-d 00:00:00', strtotime('-365 days', current_time('timestamp')));
        
        // Use esc_sql for table name - cannot use prepare() with table names
        $table_name_escaped = esc_sql($table_name);
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name is escaped with esc_sql()
        $query = "SELECT DATE(interaction_date) as date, COUNT(DISTINCT session_id) as count 
            FROM `{$table_name_escaped}` 
            WHERE interaction_date >= %s 
            GROUP BY DATE(interaction_date) 
            ORDER BY date ASC";
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.NotPrepared -- Required for custom plugin table statistics, values are prepared correctly
        $results = $wpdb->get_results($wpdb->prepare($query, $date_from));
        
        $calendar_map = array();
        if ($results && is_array($results)) {
            foreach ($results as $result) {
                if (isset($result->date) && isset($result->count)) {
                    $calendar_map[$result->date] = intval($result->count);
                }
            }
        }
        
        return $calendar_map;
    }
    
    /**
     * Drop table (for uninstall)
     */
    public function drop_table() {
        global $wpdb;
        
        $table_name = $this->get_table_name();
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange -- Required for plugin uninstall
        $wpdb->query("DROP TABLE IF EXISTS " . esc_sql($table_name));
    }
}

