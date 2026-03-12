<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://growtype.com
 * @since      1.0.0
 *
 * @package    Growtype_Search
 * @subpackage growtype_search/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Growtype_Search
 * @subpackage growtype_search/admin
 * @author     Growtype
 */
class Growtype_Search_Admin
{
    const SETTINGS_DEFAULT_TAB = 'general';

    const SETTINGS_PAGE_NAME = 'growtype-search-settings';

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $growtype_search The ID of this plugin.
     */
    private $growtype_search;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Traits
     */

    /**
     * Initialize the class and set its properties.
     *
     * @param string $growtype_search The name of this plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct($growtype_search, $version)
    {
        $this->growtype_search = $growtype_search;
        $this->version = $version;

        if (is_admin()) {
            /**
             * Load methods
             */
            add_action('init', array($this, 'add_pages'));
        }
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in growtype_search as all of the hooks are defined
         * in that particular class.
         *
         * The growtype_search will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->growtype_search, plugin_dir_url(__FILE__) . 'css/growtype-search-admin.css', array(), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Growtype_Search_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Growtype_Search_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->growtype_search, plugin_dir_url(__FILE__) . 'js/growtype-search-admin.js', array('jquery'), $this->version, false);
    }

    /**
     * Load the required methods for this plugin.
     *
     */
    public function add_pages()
    {
        /**
         * Plugin settings
         */
        require GROWTYPE_SEARCH_PATH . '/admin/pages/growtype-search-admin-pages.php';
        new Growtype_Search_Admin_Pages();
    }
}