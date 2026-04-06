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

        $base_url = admin_url(
            'options-general.php?page=' . Growtype_Search_Admin::SETTINGS_PAGE_NAME . '&tab=stats'
        );

        echo '<h3>Search Statistics</h3>';
        echo '<div class="growtype-search-stats-container">';

        foreach ($this->get_table_definitions() as $table) {
            $this->render_table($table, $base_url);
        }

        echo '</div>';
        $this->render_styles();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Table definitions — add a new entry here to add a new stats source
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Returns all stats table definitions.
     *
     * Each definition is an array with:
     *   - id        (string)   Unique key used for sort URL params (e.g. 'db', 'log')
     *   - title     (string)   Section heading
     *   - actions   (array)    Optional array of ['label' => '', 'url' => '', 'confirm' => '']
     *   - columns   (array)    Column definitions: ['key' => '', 'label' => '', 'sortable' => bool, 'render' => callable|null]
     *   - default_sort (string) Default column key to sort by
     *   - data      (callable) fn($orderby, $order): array — returns rows as arrays or objects
     *
     * @return array
     */
    protected function get_table_definitions(): array
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'growtype_search_stats';

        return [
            // ── Database Stats ───────────────────────────────────────────────
            [
                'id'           => 'db',
                'title'        => 'Database Statistics (Captured Real-time)',
                'actions'      => [
                    [
                        'label'   => 'Clear Database Stats',
                        'url'     => wp_nonce_url(
                            admin_url('options-general.php?page=' . Growtype_Search_Admin::SETTINGS_PAGE_NAME . '&tab=stats&clear_stats=true'),
                            'growtype_search_clear_stats'
                        ),
                        'confirm' => 'Are you sure you want to clear all database statistics?',
                        'style'   => 'color:#d63638;text-decoration:none;',
                    ],
                ],
                'columns'      => [
                    ['key' => 'search_query',  'label' => 'Value',        'sortable' => true],
                    ['key' => 'count',         'label' => 'Count',        'sortable' => true],
                    ['key' => 'user_ids',      'label' => 'User IDs',     'sortable' => false,
                        'render' => fn($row) => !empty($row->user_ids) ? esc_html($row->user_ids) : '—'],
                    ['key' => 'last_searched', 'label' => 'Last Searched','sortable' => true],
                ],
                'default_sort' => 'last_searched',
                'data'         => function (string $orderby, string $order) use ($wpdb, $table_name): array {
                    $allowed = ['search_query', 'count', 'last_searched'];
                    $col     = in_array($orderby, $allowed, true) ? $orderby : 'last_searched';
                    $dir     = $order === 'ASC' ? 'ASC' : 'DESC';
                    // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                    return (array) $wpdb->get_results("SELECT * FROM {$table_name} ORDER BY {$col} {$dir}");
                },
            ],

            // ── Log Stats ────────────────────────────────────────────────────
            [
                'id'           => 'log',
                'title'        => 'Log Statistics (Parsed from .log files)',
                'actions'      => [],
                'columns'      => [
                    ['key' => 'value', 'label' => 'Value', 'sortable' => true],
                    ['key' => 'count', 'label' => 'Count', 'sortable' => true],
                    ['key' => 'status', 'label' => 'Status', 'sortable' => false,
                        'render' => fn($row) => '<span style="display:inline-block;padding:2px 8px;font-size:11px;font-weight:600;background:#dbeafe;color:#1e40af;border-radius:4px;">Log Only</span>'],
                ],
                'default_sort' => 'count',
                'data'         => function (string $orderby, string $order): array {
                    $raw = $this->get_data_from_logs(); // ['term' => count, ...]
                    if (empty($raw)) {
                        return [];
                    }
                    if ($orderby === 'value') {
                        ksort($raw);
                        if ($order === 'DESC') {
                            $raw = array_reverse($raw, true);
                        }
                    } else {
                        arsort($raw);
                        if ($order === 'ASC') {
                            $raw = array_reverse($raw, true);
                        }
                    }
                    // Normalise to plain objects so the renderer is uniform
                    return array_map(
                        fn($value, $count) => (object) ['value' => $value, 'count' => $count, 'status' => ''],
                        array_keys($raw),
                        array_values($raw)
                    );
                },
            ],

            // ── Add future table sources here ─────────────────────────────────
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Generic table renderer — do not edit for individual tables
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Renders a stats table from a definition array.
     */
    protected function render_table(array $table, string $base_url): void
    {
        $id      = $table['id'];
        $param_o = "orderby_{$id}";
        $param_d = "order_{$id}";

        // Read sort state for this table from URL, fall back to default
        $default_sort = $table['default_sort'] ?? ($table['columns'][0]['key'] ?? '');
        $sortable_keys = array_column(array_filter($table['columns'], fn($c) => $c['sortable'] ?? false), 'key');
        $orderby = isset($_GET[$param_o]) && in_array($_GET[$param_o], $sortable_keys, true)
            ? sanitize_key($_GET[$param_o])
            : $default_sort;
        $order   = isset($_GET[$param_d]) && strtoupper($_GET[$param_d]) === 'ASC' ? 'ASC' : 'DESC';
        $flip    = $order === 'ASC' ? 'DESC' : 'ASC';

        // Fetch data
        $rows = ($table['data'])($orderby, $order);

        // Preserve other tables' sort params in links
        $preserved = $this->get_preserved_sort_params($id);

        echo '<div style="margin-bottom:30px;">';
        echo '<h4 style="margin-top:0;">' . esc_html($table['title']) . '</h4>';

        // Action links
        if (!empty($table['actions'])) {
            echo '<p>';
            foreach ($table['actions'] as $action) {
                $onclick = !empty($action['confirm'])
                    ? ' onclick="return confirm(' . esc_attr(json_encode($action['confirm'])) . ');"'
                    : '';
                $style = 'class="button button-link-delete" style="' . esc_attr($action['style'] ?? '') . '"';
                echo '<a href="' . esc_url($action['url']) . '" ' . $style . $onclick . '>'
                    . esc_html($action['label']) . '</a> ';
            }
            echo '</p>';
        }

        echo '<table class="wp-list-table widefat fixed striped"><thead><tr>';

        foreach ($table['columns'] as $col) {
            if (empty($col['sortable'])) {
                echo '<th>' . esc_html($col['label']) . '</th>';
                continue;
            }
            $is_active = $col['key'] === $orderby;
            $arrow     = $is_active ? ($order === 'ASC' ? ' ↑' : ' ↓') : '';
            $next_dir  = $is_active ? $flip : 'DESC';
            $params    = array_merge($preserved, [$param_o => $col['key'], $param_d => $next_dir]);
            $url       = $base_url . '&' . http_build_query($params);
            $style     = $is_active ? 'font-weight:700;text-decoration:underline;' : 'font-weight:600;text-decoration:underline;';
            echo '<th><a href="' . esc_url($url) . '" style="color:inherit;' . $style . '">'
                . esc_html($col['label'] . $arrow) . '</a></th>';
        }

        $col_count = count($table['columns']);
        echo '</tr></thead><tbody>';

        if (empty($rows)) {
            echo '<tr><td colspan="' . $col_count . '">No data found.</td></tr>';
        } else {
            foreach ($rows as $row) {
                echo '<tr>';
                foreach ($table['columns'] as $col) {
                    echo '<td>';
                    if (!empty($col['render']) && is_callable($col['render'])) {
                        echo ($col['render'])($row); // already escaped inside render callbacks
                    } else {
                        $val = is_object($row) ? ($row->{$col['key']} ?? '') : ($row[$col['key']] ?? '');
                        echo esc_html((string) $val);
                    }
                    echo '</td>';
                }
                echo '</tr>';
            }
        }

        echo '</tbody></table></div>';
    }

    /**
     * Returns current sort params for all OTHER tables so they are preserved when
     * clicking a sort link belonging to a different table.
     */
    protected function get_preserved_sort_params(string $exclude_id): array
    {
        $preserved = [];
        foreach ($this->get_table_definitions() as $table) {
            $id = $table['id'];
            if ($id === $exclude_id) {
                continue;
            }
            $po = "orderby_{$id}";
            $pd = "order_{$id}";
            if (isset($_GET[$po])) {
                $preserved[$po] = sanitize_key($_GET[$po]);
            }
            if (isset($_GET[$pd])) {
                $preserved[$pd] = in_array(strtoupper($_GET[$pd]), ['ASC', 'DESC'], true)
                    ? strtoupper($_GET[$pd])
                    : 'DESC';
            }
        }
        return $preserved;
    }

    protected function render_styles(): void
    {
        ?>
        <style>
            .growtype-search-stats-container table { max-width: 100%; margin-top: 10px; }
            .growtype-search-stats-container h4    { font-size: 1.1em; font-weight: 600; color: #23282d; }
            .growtype-search-stats-container th a:hover { color: #0073aa; }
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





