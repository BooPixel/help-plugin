<?php
/**
 * Database operations for BooPixel AI Chat Connect for n8n
 *
 * @package BooPixel_AI_Chat_For_N8n
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class BooPixel_AI_Chat_For_N8n_Database
 */
class BooPixel_AI_Chat_For_N8n_Database {
    
    /**
     * Get table name
     *
     * @return string Table name
     */
    private function get_table_name() {
        global $wpdb;
        return $wpdb->prefix . 'boopixel_ai_chat_for_n8n_interactions';
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
     * Get conversation history
     *
     * @param string $date_from Start date (Y-m-d).
     * @param string $date_to   End date (Y-m-d).
     * @param int    $limit     Number of conversations to return.
     * @param int    $offset    Offset for pagination.
     * @return array Conversations grouped by session_id.
     */
    public function get_conversations($date_from = '', $date_to = '', $limit = 50, $offset = 0) {
        global $wpdb;
        
        $table_name = $this->get_table_name();
        $this->create_table();
        
        $table_name_escaped = esc_sql($table_name);
        
        // Build WHERE clause
        $where_clauses = array();
        $where_values = array();
        
        if (!empty($date_from)) {
            $where_clauses[] = "interaction_date >= %s";
            $where_values[] = $date_from . ' 00:00:00';
        }
        
        if (!empty($date_to)) {
            $where_clauses[] = "interaction_date <= %s";
            $where_values[] = $date_to . ' 23:59:59';
        }
        
        $where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';
        
        // Get unique sessions with message count
        $sessions_query = "SELECT 
                session_id,
                MIN(interaction_date) as first_interaction,
                MAX(interaction_date) as last_interaction,
                COUNT(*) as message_count
            FROM `{$table_name_escaped}`
            {$where_sql}
            GROUP BY session_id
            ORDER BY last_interaction DESC
            LIMIT %d OFFSET %d";
        
        // Prepare query with proper placeholders
        $query_params = $where_values;
        $query_params[] = $limit;
        $query_params[] = $offset;
        
        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Table name cannot be a placeholder, but query is prepared with placeholders for all user input
        $sessions_query_prepared = $wpdb->prepare($sessions_query, $query_params);
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.NotPrepared,PluginCheck.Security.DirectDB.UnescapedDBParameter -- Query is already prepared with $wpdb->prepare() above, all user input is sanitized and escaped, required for custom plugin table statistics
        $sessions = $wpdb->get_results($sessions_query_prepared);
        
        if (empty($sessions)) {
            return array();
        }
        
        // Get session IDs
        $session_ids = array_map(function($s) {
            return $s->session_id;
        }, $sessions);
        
        // Get all messages for these sessions
        if (empty($session_ids)) {
            return array();
        }
        
        $session_ids_escaped = array_map('esc_sql', $session_ids);
        $session_ids_placeholders = "'" . implode("','", $session_ids_escaped) . "'";
        $messages_query = "SELECT 
                id,
                session_id,
                message,
                message_type,
                interaction_date
            FROM `{$table_name_escaped}`
            WHERE session_id IN ({$session_ids_placeholders})
            ORDER BY interaction_date ASC";
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.NotPrepared,PluginCheck.Security.DirectDB.UnescapedDBParameter -- Required for custom plugin table statistics, session IDs are escaped with esc_sql(), table name is escaped
        $messages = $wpdb->get_results($messages_query);
        
        // Group messages by session
        $conversations = array();
        foreach ($sessions as $session) {
            $conversations[] = array(
                'session_id' => $session->session_id,
                'first_interaction' => $session->first_interaction,
                'last_interaction' => $session->last_interaction,
                'message_count' => intval($session->message_count),
                'messages' => array()
            );
        }
        
        // Add messages to conversations
        foreach ($messages as $message) {
            foreach ($conversations as &$conversation) {
                if ($conversation['session_id'] === $message->session_id) {
                    $conversation['messages'][] = array(
                        'id' => intval($message->id),
                        'message' => $message->message,
                        'message_type' => $message->message_type,
                        'interaction_date' => $message->interaction_date
                    );
                    break;
                }
            }
        }
        
        return $conversations;
    }
    
    /**
     * Get total conversations count
     *
     * @param string $date_from Start date (Y-m-d).
     * @param string $date_to   End date (Y-m-d).
     * @return int Total count.
     */
    public function get_conversations_count($date_from = '', $date_to = '') {
        global $wpdb;
        
        $table_name = $this->get_table_name();
        $this->create_table();
        
        $table_name_escaped = esc_sql($table_name);
        
        // Build WHERE clause
        $where_clauses = array();
        $where_values = array();
        
        if (!empty($date_from)) {
            $where_clauses[] = "interaction_date >= %s";
            $where_values[] = $date_from . ' 00:00:00';
        }
        
        if (!empty($date_to)) {
            $where_clauses[] = "interaction_date <= %s";
            $where_values[] = $date_to . ' 23:59:59';
        }
        
        $where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';
        
        $query = "SELECT COUNT(DISTINCT session_id) as total 
            FROM `{$table_name_escaped}`
            {$where_sql}";
        
        if (!empty($where_values)) {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.NotPrepared,PluginCheck.Security.DirectDB.UnescapedDBParameter -- Query is prepared with $wpdb->prepare() and placeholders, all user input is sanitized, required for custom plugin table statistics
            $result = $wpdb->get_var($wpdb->prepare($query, $where_values));
        } else {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.NotPrepared,PluginCheck.Security.DirectDB.UnescapedDBParameter -- No user input in query, table name is escaped, required for custom plugin table statistics
            $result = $wpdb->get_var($query);
        }
        
        return intval($result);
    }
    
    /**
     * Get all sessions with summary information
     *
     * @param int $limit  Number of sessions to return.
     * @param int $offset Offset for pagination.
     * @return array Sessions array with summary data.
     */
    public function get_all_sessions($limit = 50, $offset = 0) {
        global $wpdb;
        
        $table_name = $this->get_table_name();
        $this->create_table();
        
        $table_name_escaped = esc_sql($table_name);
        
        $query = "SELECT 
                session_id,
                MIN(interaction_date) as first_interaction,
                MAX(interaction_date) as last_interaction,
                COUNT(*) as message_count,
                COUNT(DISTINCT CASE WHEN message_type = 'user' THEN id END) as user_messages,
                COUNT(DISTINCT CASE WHEN message_type = 'robot' THEN id END) as robot_messages
            FROM `{$table_name_escaped}`
            GROUP BY session_id
            ORDER BY last_interaction DESC
            LIMIT %d OFFSET %d";
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.NotPrepared -- Required for custom plugin table statistics
        $sessions = $wpdb->get_results($wpdb->prepare($query, $limit, $offset));
        
        if (empty($sessions)) {
            return array();
        }
        
        return array_map(function($session) {
            return array(
                'session_id' => $session->session_id,
                'first_interaction' => $session->first_interaction,
                'last_interaction' => $session->last_interaction,
                'message_count' => intval($session->message_count),
                'user_messages' => intval($session->user_messages),
                'robot_messages' => intval($session->robot_messages)
            );
        }, $sessions);
    }
    
    /**
     * Get total count of sessions
     *
     * @return int Total number of sessions.
     */
    public function get_sessions_count() {
        global $wpdb;
        
        $table_name = $this->get_table_name();
        $this->create_table();
        
        $table_name_escaped = esc_sql($table_name);
        
        $query = "SELECT COUNT(DISTINCT session_id) as total 
            FROM `{$table_name_escaped}`";
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.NotPrepared -- Required for custom plugin table statistics
        $result = $wpdb->get_var($query);
        
        return intval($result);
    }
    
    /**
     * Get messages for a specific session
     *
     * @param string $session_id Session ID.
     * @return array Messages array.
     */
    public function get_session_messages($session_id) {
        global $wpdb;
        
        $table_name = $this->get_table_name();
        $this->create_table();
        
        $table_name_escaped = esc_sql($table_name);
        $session_id_escaped = esc_sql($session_id);
        
        $query = "SELECT 
                id,
                session_id,
                message,
                message_type,
                interaction_date
            FROM `{$table_name_escaped}`
            WHERE session_id = %s
            ORDER BY interaction_date ASC";
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.NotPrepared -- Required for custom plugin table statistics
        $messages = $wpdb->get_results($wpdb->prepare($query, $session_id_escaped));
        
        $result = array();
        if ($messages && is_array($messages)) {
            foreach ($messages as $message) {
                $result[] = array(
                    'id' => intval($message->id),
                    'message' => $message->message,
                    'message_type' => $message->message_type,
                    'interaction_date' => $message->interaction_date
                );
            }
        }
        
        return $result;
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

