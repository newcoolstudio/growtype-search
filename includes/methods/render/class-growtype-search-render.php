<?php

class Growtype_Search_Render
{
    public function __construct()
    {
        add_action('growtype_header_inner_before_close', array ($this, 'growtype_header_before_close_callback'));
    }

    function growtype_header_before_close_callback()
    {
        if (growtype_search_enabled()) {
            echo '<div class="growtype-search-form">';
            echo do_shortcode('[growtype_search_btn]');
            if (!growtype_search_form_is_fixed()) {
                echo do_shortcode('[growtype_search_form is_visible_initially="false"]');
            }
            echo '</div>';
        }
    }
}
