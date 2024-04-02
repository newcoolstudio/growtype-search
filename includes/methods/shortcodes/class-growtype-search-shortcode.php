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

        $params = array (
            'search_type' => isset($atts['search_type']) && !empty($atts['search_type']) ? $atts['search_type'] : $this->search_type,
            'parent_id' => isset($atts['parent_id']) && !empty($atts['parent_id']) ? $atts['parent_id'] : md5(uniqid(rand(), true)),
            'search_input_placeholder' => isset($atts['search_input_placeholder']) && !empty($atts['search_input_placeholder']) ? $atts['search_input_placeholder'] : __('Search...', 'growtype-search'),
            'search_on_load' => isset($atts['search_on_load']) && !empty($atts['search_on_load']) ? $atts['search_on_load'] : 'false',
            'search_on_type' => isset($atts['search_on_type']) && !empty($atts['search_on_type']) ? $atts['search_on_type'] : 'false',
            'visible_results_amount' => isset($atts['visible_results_amount']) && !empty($atts['visible_results_amount']) ? $atts['visible_results_amount'] : '',
            'search_on_empty' => isset($atts['search_on_empty']) && !empty($atts['search_on_empty']) ? $atts['search_on_empty'] : 'false',
            'post_types_included' => isset($atts['post_types_included']) && !empty($atts['post_types_included']) ? $atts['post_types_included'] : '',
        );

        $params = apply_filters('growtype_search_params', $params);

        ob_start();

        echo growtype_search_include_view('search.form.index', $params);

        $content = ob_get_clean();

        $main_values = [
            'parent_id' => $params['parent_id'],
            'static' => [
                'search_on_load' => $params['search_on_load'],
                'search_on_type' => $params['search_on_type'],
                'visible_results_amount' => $params['visible_results_amount'],
                'search_on_empty' => $params['search_on_empty'],
                'post_types_included' => $params['post_types_included'],
            ]
        ];

        /**
         * Pass values to frontend
         */
        add_action('wp_footer', function () use ($main_values) { ?>
            <script type="text/javascript">
                window.growtype_search['<?php echo $main_values['parent_id'] ?>'] = {
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
