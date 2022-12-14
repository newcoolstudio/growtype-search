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
        if (get_theme_mod('growtype_search_disabled')) {
            return '';
        }

        extract(shortcode_atts(array (
            'search_type' => !empty(get_theme_mod('growtype_search_type')) ? get_theme_mod('growtype_search_type') : 'inline',
            'btn_open' => !empty(get_theme_mod('growtype_search_btn_open')) ? 'true' : (get_theme_mod('growtype_search_type') === 'fixed' ? 'true' : 'false'),
            'post_types_included' => !empty(get_theme_mod('growtype_search_post_types_included')) ? get_theme_mod('growtype_search_post_types_included') : 'all',
            'parent_id' => md5(uniqid(rand(), true)),
            'search_on_load' => 'false',
            'visible_results_amount' => '',
            'search_on_empty' => 'false',
            'search_input_placeholder' => __('Search...', 'growtype-search'),
        ), $atts));

        if (is_array($post_types_included)) {
            $post_types_included = implode(',', $post_types_included);
        }

        ob_start();

        if ($btn_open === 'true') {
            include GROWTYPE_SEARCH_PATH . 'resources/views/search/trigger/index.php';
        }

        include GROWTYPE_SEARCH_PATH . 'resources/views/search/form/index.php';

        $content = ob_get_clean();

        $main_values = [
            'parent_id' => $parent_id,
            'static' => [
                'search_on_load' => $search_on_load,
                'visible_results_amount' => $visible_results_amount,
                'search_on_empty' => $search_on_empty,
            ]
        ];

        /**
         * Pass values to frontend
         */
        add_action('wp_footer', function () use ($main_values) { // 🎉
            ?>
            <script type="text/javascript">
                window.growtypeSearch['<?php echo $main_values['parent_id'] ?>'] = {
                    static: <?php echo json_encode($main_values['static']) ?>
                }
            </script>
            <?php
        });

        return $content;
    }
}
