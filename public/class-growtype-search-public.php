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

        if (defined('GROWTYPE_SEARCH_VERSION')) {
            $this->version = GROWTYPE_SEARCH_VERSION;
        } else {
            $this->version = '1.0.0';
        }

        add_action('wp_footer', array ($this, 'add_scripts_to_footer'));
    }

    /***
     *
     */
    function add_scripts_to_footer()
    {
        ?>
        <script type="text/javascript">
            window.growtype_search = {};
        </script>
        <?php
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        wp_enqueue_style($this->growtype_search, GROWTYPE_SEARCH_URL_PUBLIC . 'styles/growtype-search.css', array (), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        wp_enqueue_script($this->growtype_search, GROWTYPE_SEARCH_URL_PUBLIC . 'scripts/growtype-search.js', array ('jquery'), $this->version, true);

        $ajax_url = admin_url('admin-ajax.php');

        if (class_exists('QTX_Translator')) {
            $ajax_url = admin_url('admin-ajax.php' . '?lang=' . qtranxf_getLanguage());
        }

        wp_localize_script($this->growtype_search, 'growtype_search_ajax', array (
            'url' => $ajax_url,
            'nonce' => wp_create_nonce('ajax-nonce'),
            'action' => self::GROWTYPE_SEARCH_AJAX_ACTION
        ));
    }

}
