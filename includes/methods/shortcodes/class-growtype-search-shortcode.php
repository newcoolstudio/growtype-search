<?php

/**
 * Class Growtype_Search_In_Gallery
 */
class Growtype_Search_Shortcode
{
    public function __construct()
    {
        if (!is_admin() && !wp_is_json_request()) {
            add_shortcode('growtype_search_btn', array ($this, 'growtype_search_btn_shortcode'));

            add_shortcode('growtype_search_form', array ($this, 'growtype_search_form_shortcode'));

            add_action('growtype_footer_before_open', array ($this, 'growtype_search_add_body_content'));
        }
    }

    function growtype_search_add_body_content()
    {
        if (growtype_search_form_is_fixed()) {
//            echo do_shortcode('[growtype_search_form]');
        }
    }

    /**
     *
     */
    function growtype_search_form_shortcode($atts)
    {
        $params = array (
            'search_type' => isset($atts['search_type']) && !empty($atts['search_type']) ? $atts['search_type'] : growtype_search_default_search_type(),
            'search_cat' => isset($atts['search_cat']) && !empty($atts['search_cat']) ? $atts['search_cat'] : null,
            'parent_id' => isset($atts['parent_id']) && !empty($atts['parent_id']) ? $atts['parent_id'] : growtype_search_id(),
            'search_input_placeholder' => isset($atts['search_input_placeholder']) && !empty($atts['search_input_placeholder']) ? $atts['search_input_placeholder'] : __('Search...', 'growtype-search'),
            'search_on_load' => isset($atts['search_on_load']) && !empty($atts['search_on_load']) ? $atts['search_on_load'] : 'false',
            'search_on_type' => isset($atts['search_on_type']) && !empty($atts['search_on_type']) ? $atts['search_on_type'] : 'false',
            'visible_results_amount' => isset($atts['visible_results_amount']) && !empty($atts['visible_results_amount']) ? $atts['visible_results_amount'] : '',
            'search_on_empty' => isset($atts['search_on_empty']) && !empty($atts['search_on_empty']) ? $atts['search_on_empty'] : 'false',
            'post_types_included' => isset($atts['post_types_included']) && !empty($atts['post_types_included']) ? $atts['post_types_included'] : '',
            'parent_class' => isset($atts['parent_class']) && !empty($atts['parent_class']) ? $atts['parent_class'] : '',
            'is_hidden_initially' => isset($atts['is_hidden_initially']) ? filter_var($atts['is_hidden_initially'], FILTER_VALIDATE_BOOLEAN) : !growtype_search_enabled(),
        );

        /**
         * Set parent class
         */
        $parent_class = explode(' ', $params['parent_class']);

        $parent_class_fixed = $params['search_type'] === 'fixed' ? 'is-fixed' : '';

        if (empty($parent_class_fixed)) {
            $parent_class_fixed = $params['search_cat'] === 'header' && growtype_search_form_is_fixed($atts) ? 'is-fixed' : '';
        }

        $parent_class = array_merge($parent_class, [
            'growtype-search-wrapper',
            'growtype-search-form-' . $params['parent_id'],
            $params['is_hidden_initially'] ? 'is-hidden-initially' : '',
            $parent_class_fixed,
        ]);

        $parent_class = array_filter($parent_class);

        $params['parent_class'] = implode(' ', $parent_class);

        /**
         * Filter search params
         */
        $params = apply_filters('growtype_search_params', $params);

        /**
         * Output search form
         */
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

    function growtype_search_btn_shortcode($attr)
    {
        $attr['parent_id'] = isset($attr['parent_id']) && !empty($attr['parent_id']) ? $attr['parent_id'] : growtype_search_id();

        ob_start();

        echo growtype_search_include_view('search.trigger.index', $attr);

        $content = ob_get_clean();

        return $content;
    }
}
