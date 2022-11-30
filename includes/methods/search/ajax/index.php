<?php

add_action('wp_ajax_nopriv_' . Growtype_Search_Public::GROWTYPE_SEARCH_AJAX_ACTION, 'growtype_search_ajax_callback');
add_action('wp_ajax_' . Growtype_Search_Public::GROWTYPE_SEARCH_AJAX_ACTION, 'growtype_search_ajax_callback');

function growtype_search_ajax_callback()
{
    if (!wp_verify_nonce($_POST['nonce'], 'ajax-nonce')) {
        die ('Something went wrong');
    }

    $search = isset($_POST['search']) ? $_POST['search'] : '';

    $args = array (
        'post_type' => growtype_search_get_post_types(),
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

            include(GROWTYPE_SEARCH_PATH . 'resources/views/search/ajax/result.php');

        endwhile;

        wp_reset_postdata();
    } else {
        include(GROWTYPE_SEARCH_PATH . 'resources/views/search/ajax/no-result.php');
    }

    $content = [
        'html' => ob_get_clean()
    ];

    $result = json_encode($content);

    echo $result;

    wp_die();
}
