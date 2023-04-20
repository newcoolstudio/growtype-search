<?php

/**
 * Class Growtype_Search_In_Gallery
 */
class Growtype_Search_Shortcode
{
    public function __construct()
    {
        $this->search_type = !empty(get_theme_mod('growtype_search_type')) ? get_theme_mod('growtype_search_type') : 'inline';

        if (!is_admin() && !wp_is_json_request()) {
            add_shortcode('growtype_search_btn', array ($this, 'growtype_search_btn_shortcode'));

            add_shortcode('growtype_search_form', array ($this, 'growtype_search_form_shortcode'));

            add_action('growtype_footer_before_open', array ($this, 'growtype_search_add_body_content'));
        }
    }

    function growtype_search_add_body_content()
    {
        if ($this->search_type === 'fixed') {
            echo do_shortcode('[growtype_search_form]');
        }
    }

    /**
     *
     */
    function growtype_search_form_shortcode($atts)
    {
        if (get_theme_mod('growtype_search_disabled')) {
            return '';
        }

        extract(shortcode_atts(array (
            'search_type' => $this->search_type,
            'parent_id' => md5(uniqid(rand(), true)),
            'search_input_placeholder' => __('Search...', 'growtype-search'),
            'search_on_load' => 'false',
            'visible_results_amount' => '',
            'search_on_empty' => 'false',
        ), $atts));

        ob_start();

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

    function growtype_search_btn_shortcode()
    {
        ob_start();

        include GROWTYPE_SEARCH_PATH . 'resources/views/search/trigger/index.php';

        $content = ob_get_clean();

        return $content;
    }
}
