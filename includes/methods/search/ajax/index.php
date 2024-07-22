<?php

add_action('wp_ajax_nopriv_' . Growtype_Search_Public::GROWTYPE_SEARCH_AJAX_ACTION, 'growtype_search_ajax_callback');
add_action('wp_ajax_' . Growtype_Search_Public::GROWTYPE_SEARCH_AJAX_ACTION, 'growtype_search_ajax_callback');

function growtype_search_ajax_callback()
{
    $search = isset($_REQUEST['search']['s']) ? $_REQUEST['search']['s'] : '';
    $settings_static = isset($_REQUEST['settings_static']) ? $_REQUEST['settings_static'] : [];
    $lang = isset($_REQUEST['lang']) ? $_REQUEST['lang'] : (class_exists('QTX_Translator') ? qtranxf_getLanguage() : 'en');

    $included_post_types = isset($settings_static['post_types_included']) && !empty($settings_static['post_types_included']) ? explode(',', $settings_static['post_types_included']) : growtype_search_get_post_types();

    /**
     * Update language domain
     */
    if (function_exists('qtranxf_getLanguage')) {
        growtype_search_load_textdomain($lang);
    }

    $args = array (
        'post_type' => $included_post_types,
        'post_status' => 'publish',
        'posts_per_page' => -1,
        's' => $search
    );

    error_log(sprintf('growtype_search_ajax_callback: %s', json_encode($args)));

    $args = apply_filters('growtype_search_ajax_args', $args, $_REQUEST);

    $search_results = new WP_Query($args);

    $search_results = apply_filters('growtype_search_ajax_results', $search_results, $args, $_REQUEST);

    ob_start();

    if ($search_results->have_posts()) {
        while ($search_results->have_posts()) : $search_results->the_post();
            $post = get_post();

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
