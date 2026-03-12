<?php

/**
 * Fired during plugin activation
 *
 * @link       https://growtype.com
 * @since      1.0.0
 *
 * @package    Growtype_Search
 * @subpackage growtype_search/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Growtype_Search
 * @subpackage growtype_search/includes
 * @author     Growtype
 */
class Growtype_Search_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
        global $wpdb;
        global $wp_rewrite;

        $table_name = $wpdb->prefix . 'growtype_search_stats';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            search_query varchar(255) NOT NULL,
            count int(11) DEFAULT 1 NOT NULL,
            user_ids text DEFAULT '' NOT NULL,
            last_searched datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY search_query (search_query)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        update_option('growtype_search_version', GROWTYPE_SEARCH_VERSION);

        $wp_rewrite->flush_rules();
	}

}
