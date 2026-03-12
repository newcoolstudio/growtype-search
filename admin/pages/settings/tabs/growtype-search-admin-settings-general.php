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

class Growtype_Search_Admin_Settings_General
{
    public function __construct()
    {
        add_action('admin_init', array($this, 'admin_settings'));
        add_filter('growtype_search_admin_settings_tabs', array($this, 'settings_tab'));
    }

    function settings_tab($tabs)
    {
        $tabs['general'] = 'General';
        return $tabs;
    }

    function admin_settings()
    {
        register_setting('growtype_search_settings_general', 'growtype_search_save_searched_values');

        add_settings_section(
            'growtype_search_general_settings',
            '',
            function () { },
            Growtype_Search_Admin::SETTINGS_PAGE_NAME
        );

        add_settings_field(
            'growtype_search_save_searched_values',
            'Save searched values',
            array($this, 'render_save_searched_values_field'),
            Growtype_Search_Admin::SETTINGS_PAGE_NAME,
            'growtype_search_general_settings'
        );
    }

    function render_save_searched_values_field()
    {
        $value = get_option('growtype_search_save_searched_values', 'on');
?>
<input type="checkbox" name="growtype_search_save_searched_values" <?php checked($value, 'on' ); ?> />
<p class="description">If checked, searched values will be saved to WordPress meta as
    <code>growtype_search_searched_values</code>.</p>
<?php
    }
}