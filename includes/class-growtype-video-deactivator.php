<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Growtype_Video
 * @subpackage growtype_video/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Growtype_Video
 * @subpackage growtype_video/includes
 * @author     Your Name <email@example.com>
 */
class Growtype_Video_Deactivator
{

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function deactivate()
    {
        global $wp_rewrite;
        $wp_rewrite->flush_rules();
    }

}
