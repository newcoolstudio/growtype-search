<?php

add_action('wp_ajax_nopriv_' . Growtype_Search_Public::GROWTYPE_SEARCH_AJAX_ACTION, 'growtype_search_ajax_callback');
add_action('wp_ajax_' . Growtype_Search_Public::GROWTYPE_SEARCH_AJAX_ACTION, 'growtype_search_ajax_callback');

function growtype_search_ajax_callback()
{
    if (!wp_verify_nonce($_POST['nonce'], 'ajax-nonce')) {
        die ('Something went wrong');
    }

    $search = isset($_POST['search']) ? $_POST['search'] : '';
    $included_post_types = isset($_POST['included_post_types']) ? $_POST['included_post_types'] : 'all';
    $visible_results_amount = isset($_POST['visible_results_amount']) ? $_POST['visible_results_amount'] : '';

    $args = array (
        'post_type' => explode(',', $included_post_types),
        'post_status' => 'publish',
        'posts_per_page' => -1,
        's' => $search,
    );

    $search_results = new WP_Query($args);

    ob_start();

    if ($search_results->have_posts()) {
        while ($search_results->have_posts()) : $search_results->the_post();
            $post_id = get_the_ID();
            $post = get_post();
            $featured_image = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'medium');
            $featured_image = ($featured_image) ? $featured_image[0] : '';

            $result = growtype_search_include_view('search.ajax.result', [
                'post' => $post
            ]);

            echo apply_filters('growtype_search_result_render', $result, $post);
        endwhile;

        wp_reset_postdata();
    } else {
        echo growtype_search_include_view('search.ajax.no-result');
    }

    $content = [
        'html' => ob_get_clean()
    ];

    echo json_encode($content);

    wp_die();
}
