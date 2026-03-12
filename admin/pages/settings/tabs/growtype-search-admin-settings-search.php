<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://growtype.com
 * @since      1.0.0
 *
 * @package    growtype_quiz
 * @subpackage growtype_quiz/admin/partials
 */

class Growtype_Search_Admin_Settings_Search
{
    public function __construct()
    {
        add_action('admin_init', array ($this, 'admin_settings'));
        add_action('admin_init', array ($this, 'handle_clear_stats'));
        add_filter('growtype_search_admin_settings_tabs', array ($this, 'settings_tab'));
        add_action('growtype_search_admin_settings_search_content', array ($this, 'render_stats_table_field'));
    }

    function settings_tab($tabs)
    {
        $tabs['stats'] = 'Stats';
        return $tabs;
    }

    function handle_clear_stats()
    {
        if (isset($_GET['page']) && $_GET['page'] === Growtype_Search_Admin::SETTINGS_PAGE_NAME && isset($_GET['clear_stats']) && $_GET['clear_stats'] == 'true') {
            check_admin_referer('growtype_search_clear_stats');
            
            global $wpdb;
            $table_name = $wpdb->prefix . 'growtype_search_stats';
            $wpdb->query("TRUNCATE TABLE $table_name");
            
            wp_safe_redirect(admin_url('options-general.php?page=' . Growtype_Search_Admin::SETTINGS_PAGE_NAME . '&tab=stats&cleared=true'));
            exit;
        }
    }

    function admin_settings()
    {
        add_settings_section(
            'growtype_search_search_stats_section',
            'Search Statistics',
            function () {
            },
            Growtype_Search_Admin::SETTINGS_PAGE_NAME . '_search'
        );
    }

    function render_stats_table_field()
    {
        if (isset($_GET['cleared']) && $_GET['cleared'] == 'true') {
            echo '<div class="updated"><p>Search statistics cleared.</p></div>';
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'growtype_search_stats';
        $searched_values = $wpdb->get_results("SELECT * FROM $table_name ORDER BY last_searched DESC");
        $log_values = $this->get_data_from_logs();
        ?>
        <h3>Search Statistics</h3>
        <div class="growtype-search-stats-container">
            <div style="margin-bottom: 30px;">
                <h4 style="margin-top: 0;">Database Statistics (Captured Real-time)</h4>
                <p>
                    <a href="<?php echo wp_nonce_url(admin_url('options-general.php?page=' . Growtype_Search_Admin::SETTINGS_PAGE_NAME . '&tab=stats&clear_stats=true'), 'growtype_search_clear_stats'); ?>" class="button button-link-delete" onclick="return confirm('Are you sure you want to clear all database statistics?');" style="color: #d63638; text-decoration: none;">Clear Database Stats</a>
                </p>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                    <tr>
                        <th>Value</th>
                        <th>Count</th>
                        <th>User IDs</th>
                        <th>Last Searched</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($searched_values)) : ?>
                        <tr>
                            <td colspan="4">No database statistics found.</td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($searched_values as $data) : ?>
                            <tr>
                                <td><?php echo esc_html($data->search_query); ?></td>
                                <td><?php echo esc_html($data->count); ?></td>
                                <td><?php echo !empty($data->user_ids) ? esc_html($data->user_ids) : '—'; ?></td>
                                <td><?php echo esc_html($data->last_searched); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div>
                <h4>Log Statistics (Parsed from .log files)</h4>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                    <tr>
                        <th>Value</th>
                        <th>Count</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($log_values)) : ?>
                        <tr>
                            <td colspan="3">No statistics found in log files.</td>
                        </tr>
                    <?php else : ?>
                        <?php
                        arsort($log_values);
                        foreach ($log_values as $value => $count) : ?>
                            <tr>
                                <td><?php echo esc_html($value); ?></td>
                                <td><?php echo esc_html($count); ?></td>
                                <td><span class="inline-block px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded">Log Only</span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <style>
            .growtype-search-stats-container table {
                max-width: 100%;
                margin-top: 10px;
            }
            .growtype-search-stats-container h4 {
                font-size: 1.1em;
                font-weight: 600;
                color: #23282d;
            }
        </style>
        <?php
    }


    /**
     * Get searched values from log files
     *
     * @return array
     */
    public function get_data_from_logs()
    {
        if (!current_user_can('manage_options')) {
            return [];
        }

        // Define the directory where log files are stored
        $log_directory = defined('WP_CONTENT_DIR') ? WP_CONTENT_DIR . '/' : ABSPATH;

        // Check if the directory exists
        if (!is_dir($log_directory)) {
            $log_directory = ABSPATH; // Fallback to ABSPATH
        }

        // Get all log files in the directory
        $log_files = glob($log_directory . '*.log');

        if (empty($log_files)) {
            return [];
        }

        $log_counts = [];

        // Process each log file
        foreach ($log_files as $log_file) {
            // Performance: Skip files larger than 10MB to avoid memory exhaustion
            if (filesize($log_file) > 10 * 1024 * 1024) {
                continue;
            }
            
            $log_content = file_get_contents($log_file);

            // Match lines containing 'growtype_search_ajax_callback' and extract search values
            preg_match_all('/growtype_search_ajax_callback: .*?"s":"(.*?)"/', $log_content, $matches);

            if (isset($matches[1]) && !empty($matches[1])) {
                foreach ($matches[1] as $value) {
                    $search = strtolower(sanitize_text_field($value));
                    if (empty($search)) continue;

                    if (isset($log_counts[$search])) {
                        $log_counts[$search]++;
                    } else {
                        $log_counts[$search] = 1;
                    }
                }
            }
        }

        return $log_counts;
    }
}





