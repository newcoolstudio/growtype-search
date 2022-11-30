<?php

/**
 * This function modifies the main WordPress query to include an array of
 * post types instead of the default 'post' post type.
 *
 * @param object $query The main WordPress query.
 */
add_action('pre_get_posts', 'growtype_search_pre_get_posts');
function growtype_search_pre_get_posts($query)
{
    $search_post_types = growtype_search_get_post_types();

    if (!empty($search_post_types) && $query->is_main_query() && $query->is_search() && !is_admin()) {
        $query->set('post_type', $search_post_types);
    }
}
