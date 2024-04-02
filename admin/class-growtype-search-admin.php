<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
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
 * @author     Your Name <email@example.com>
 */
class Growtype_Search_Admin
{

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
//            add_action('admin_menu', array ($this, 'add_custom_options_page'));

            /**
             * Load methods
             */
            $this->load_methods();
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

        wp_enqueue_style($this->growtype_search, plugin_dir_url(__FILE__) . 'css/growtype-search-admin.css', array (), $this->version, 'all');

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

        wp_enqueue_script($this->growtype_search, plugin_dir_url(__FILE__) . 'js/growtype-search-admin.js', array ('jquery'), $this->version, false);
    }

    /**
     * Register the options page with the Wordpress menu.
     */
    function add_custom_options_page()
    {
        add_options_page(
            'Growtype - Video',
            'Growtype - Video',
            'manage_options',
            'growtype-search-settings',
            array ($this, 'growtype_search_settings'),
            1
        );
    }

    /**
     * @param $current
     * @return void
     */
    function growtype_search_settings_tabs($current = 'login')
    {
        $tabs['general'] = 'General';

        if (class_exists('woocompress')) {
            $tabs['woocommerce'] = 'Woocommerce';
        }

        echo '<div id="icon-themes" class="icon32"><br></div>';
        echo '<h2 class="nav-tab-wrapper">';
        foreach ($tabs as $tab => $name) {
            $class = ($tab == $current) ? ' nav-tab-active' : '';
            echo "<a class='nav-tab$class' href='?page=growtype-search-settings&tab=$tab'>$name</a>";

        }
        echo '</h2>';
    }

    /**
     * @return void
     */
    function growtype_search_settings()
    {
        if (isset($_GET['page']) && $_GET['page'] == 'growtype-search-settings') {
            ?>

            <div class="wrap">

                <h1>Growtype - Video settings</h1>

                <?php
                if (isset($_GET['updated']) && 'true' == esc_attr($_GET['updated'])) {
                    echo '<div class="updated" ><p>Theme Settings Updated.</p></div>';
                }

                if (isset ($_GET['tab'])) {
                    $this->growtype_search_settings_tabs($_GET['tab']);
                } else {
                    $this->growtype_search_settings_tabs();
                }
                ?>

                <form id="growtype_search_main_settings" method="post" action="options.php">
                    <?php

                    if (isset ($_GET['tab'])) {
                        $tab = $_GET['tab'];
                    } else {
                        $tab = 'general';
                    }

                    switch ($tab) {
                        case 'general':
                            settings_fields('growtype_search_settings_general');

                            echo '<table class="form-table">';
                            do_settings_fields('growtype-search-settings', 'growtype_search_settings_general');
                            echo '</table>';

                            break;
                    }

                    if ($tab !== 'examples') {
                        submit_button();
                    }

                    ?>
                </form>
            </div>

            <?php
        }
    }

    /**
     * Load the required methods for this plugin.
     *
     */
    private function load_methods()
    {
    }
}
