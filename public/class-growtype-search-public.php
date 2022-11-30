<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Growtype_Search
 * @subpackage growtype_search/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Growtype_Search
 * @subpackage growtype_search/public
 * @author     Your Name <email@example.com>
 */
class Growtype_Search_Public
{

    const GROWTYPE_SEARCH_AJAX_ACTION = 'growtype_search';

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
     * Initialize the class and set its properties.
     *
     * @param string $growtype_search The name of the plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct($growtype_search, $version)
    {
        $this->growtype_search = $growtype_search;
        $this->version = $version;

        add_action('wp_footer', array ($this, 'add_scripts_to_footer'));
    }

    /***
     *
     */
    function add_scripts_to_footer()
    {
        ?>
        <script type="text/javascript">
            window.growtypeSearch = {
                ajax: "<?php echo get_theme_mod('growtype_search_ajax_disabled') === true ? 'false' : 'true' ?>"
            };
        </script>
        <?php
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public
    function enqueue_styles()
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
        wp_enqueue_style($this->growtype_search, GROWTYPE_SEARCH_URL_PUBLIC . 'styles/growtype-search.css', array (), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public
    function enqueue_scripts()
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
        wp_enqueue_script($this->growtype_search, GROWTYPE_SEARCH_URL_PUBLIC . 'scripts/growtype-search.js', array ('jquery'), $this->version, true);

        wp_localize_script($this->growtype_search, 'growtype_search_ajax', array (
            'url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ajax-nonce'),
            'action' => self::GROWTYPE_SEARCH_AJAX_ACTION
        ));
    }

}
