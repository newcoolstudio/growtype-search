<?php

class Growtype_Search_Render
{
    public function __construct()
    {
        add_action('growtype_header_inner_before_close', array ($this, 'growtype_header_before_close_callback'), 10);
    }

    function growtype_header_before_close_callback()
    {
        if (growtype_search_enabled()) {

            $id = growtype_search_id();

            echo '<div class="growtype-search-form">';
            echo do_shortcode('[growtype_search_btn parent_id="' . $id . '"]');

            if (!growtype_search_form_is_fixed()) {
                echo do_shortcode('[growtype_search_form search_cat="header" parent_id="' . $id . '" is_visible_initially="true"]');
            }

            echo '</div>';

            if (growtype_search_form_is_fixed()) {
                add_action('wp_footer', function () use ($id) {
                    echo do_shortcode('[growtype_search_form search_cat="header" parent_id="' . $id . '" is_visible_initially="false"]');
                }, 0);
            }
        }
    }
}
