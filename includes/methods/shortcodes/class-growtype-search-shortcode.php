<?php

/**
 * Class Growtype_Search_In_Gallery
 */
class Growtype_Search_Shortcode
{
    public function __construct()
    {
        if (!is_admin() && !wp_is_json_request()) {
            add_shortcode('growtype_search', array ($this, 'growtype_search_shortcode'));
        }
    }

    /**
     *
     */
    function growtype_search_shortcode($atts)
    {
        if (!get_theme_mod('growtype_search_enabled')) {
            return '';
        }

        extract(shortcode_atts(array (
            'type' => '',
        ), $atts));

        ob_start();

        include GROWTYPE_SEARCH_PATH . 'resources/views/search/trigger/index.php';

        include GROWTYPE_SEARCH_PATH . 'resources/views/search/form/index.php';

        $content = ob_get_clean();

        return $content;
    }
}
