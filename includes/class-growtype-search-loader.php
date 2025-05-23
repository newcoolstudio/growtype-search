<?php

/**
 * Register all actions and filters for the plugin
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Growtype_Search
 * @subpackage growtype_search/includes
 */

use Roots\Sage\Template\Blade;
use Roots\Sage\Template\BladeProvider;

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    Growtype_Search
 * @subpackage growtype_search/includes
 * @author     Your Name <email@example.com>
 */
class Growtype_Search_Loader
{

    /**
     * The array of actions registered with WordPress.
     *
     * @since    1.0.0
     * @access   protected
     * @var      array $actions The actions registered with WordPress to fire when the plugin loads.
     */
    protected $actions;

    /**
     * The array of filters registered with WordPress.
     *
     * @since    1.0.0
     * @access   protected
     * @var      array $filters The filters registered with WordPress to fire when the plugin loads.
     */
    protected $filters;

    /**
     * Initialize the collections used to maintain the actions and filters.
     *
     * @since    1.0.0
     */
    private $loader;

    public function __construct()
    {
        $this->actions = array ();
        $this->filters = array ();

        $this->load_methods();
    }

    /**
     * Add a new action to the collection to be registered with WordPress.
     *
     * @param string $hook The name of the WordPress action that is being registered.
     * @param object $component A reference to the instance of the object on which the action is defined.
     * @param string $callback The name of the function definition on the $component.
     * @param int $priority Optional. The priority at which the function should be fired. Default is 10.
     * @param int $accepted_args Optional. The number of arguments that should be passed to the $callback. Default is 1.
     * @since    1.0.0
     */
    public function add_action($hook, $component, $callback, $priority = 10, $accepted_args = 1)
    {
        $this->actions = $this->add($this->actions, $hook, $component, $callback, $priority, $accepted_args);
    }

    /**
     * Add a new filter to the collection to be registered with WordPress.
     *
     * @param string $hook The name of the WordPress filter that is being registered.
     * @param object $component A reference to the instance of the object on which the filter is defined.
     * @param string $callback The name of the function definition on the $component.
     * @param int $priority Optional. The priority at which the function should be fired. Default is 10.
     * @param int $accepted_args Optional. The number of arguments that should be passed to the $callback. Default is 1
     * @since    1.0.0
     */
    public function add_filter($hook, $component, $callback, $priority = 10, $accepted_args = 1)
    {
        $this->filters = $this->add($this->filters, $hook, $component, $callback, $priority, $accepted_args);
    }

    /**
     * A utility function that is used to register the actions and hooks into a single
     * collection.
     *
     * @param array $hooks The collection of hooks that is being registered (that is, actions or filters).
     * @param string $hook The name of the WordPress filter that is being registered.
     * @param object $component A reference to the instance of the object on which the filter is defined.
     * @param string $callback The name of the function definition on the $component.
     * @param int $priority The priority at which the function should be fired.
     * @param int $accepted_args The number of arguments that should be passed to the $callback.
     * @return   array                                  The collection of actions and filters registered with WordPress.
     * @since    1.0.0
     * @access   private
     */
    private function add($hooks, $hook, $component, $callback, $priority, $accepted_args)
    {

        $hooks[] = array (
            'hook' => $hook,
            'component' => $component,
            'callback' => $callback,
            'priority' => $priority,
            'accepted_args' => $accepted_args
        );

        return $hooks;

    }

    /**
     * Register the filters and actions with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {

        foreach ($this->filters as $hook) {
            add_filter($hook['hook'], array ($hook['component'], $hook['callback']), $hook['priority'], $hook['accepted_args']);
        }

        foreach ($this->actions as $hook) {
            add_action($hook['hook'], array ($hook['component'], $hook['callback']), $hook['priority'], $hook['accepted_args']);
        }

    }

    /**
     * Load the required methods for this plugin.
     *
     */
    private function load_methods()
    {
        /**
         * Helpers
         */
        require_once GROWTYPE_SEARCH_PATH . 'includes/helpers/index.php';

        /**
         * Content
         */
        require_once GROWTYPE_SEARCH_PATH . 'includes/methods/search/index.php';

        /**
         * Customizer
         */
        require_once GROWTYPE_SEARCH_PATH . 'includes/methods/customizer/index.php';

        /**
         * Shortcode
         */
        require_once GROWTYPE_SEARCH_PATH . 'includes/methods/shortcodes/class-growtype-search-shortcode.php';
        $this->loader = new Growtype_Search_Shortcode();

        /**
         * Block
         */
        require_once GROWTYPE_SEARCH_PATH . 'includes/methods/blocks/class-growtype-search-block.php';
        $this->loader = new Growtype_Search_Block();

        /**
         * Render
         */
        require_once GROWTYPE_SEARCH_PATH . 'includes/methods/render/class-growtype-search-render.php';
        $this->loader = new Growtype_Search_Render();
    }
}
