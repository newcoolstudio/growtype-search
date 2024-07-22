<?php

/**
 * Include custom view
 */
if (!function_exists('growtype_search_include_view')) {
    function growtype_search_include_view($file_path, $variables = array ())
    {
        $fallback_view = GROWTYPE_SEARCH_PATH . 'resources/views/' . str_replace('.', '/', $file_path) . '.php';
        $child_blade_view = get_stylesheet_directory() . '/views/' . GROWTYPE_SEARCH_TEXT_DOMAIN . '/' . str_replace('.', '/', $file_path) . '.blade.php';
        $child_view = get_stylesheet_directory() . '/views/' . GROWTYPE_SEARCH_TEXT_DOMAIN . '/' . str_replace('.', '/', $file_path) . '.php';

        $template_path = $fallback_view;

        if (file_exists($child_blade_view) && function_exists('App\template')) {
            return App\template($child_blade_view, $variables);
        } elseif (file_exists($child_view)) {
            $template_path = $child_view;
        }

        if (file_exists($template_path)) {
            extract($variables);
            ob_start();
            include $template_path;
            $output = ob_get_clean();
        }

        return isset($output) ? $output : '';
    }
}

/**
 * @return string
 */
if (!function_exists('growtype_search_permalink')) {
    function growtype_search_permalink()
    {
        if (class_exists('woocommerce')) {
            if (is_search() && !is_shop()) {
                return home_url('/');
            }

            return get_permalink(wc_get_page_id('shop'));
        }

        return get_permalink();
    }
}

/**
 * @return bool
 */
if (!function_exists('growtype_search_enabled')) {
    function growtype_search_enabled()
    {
        $search_enabled = !get_theme_mod('growtype_search_disabled', false);
        $search_enabled_pages = get_theme_mod('growtype_search_enabled_pages', []);

        if (!empty($search_enabled_pages) && is_array($search_enabled_pages) && isset($search_enabled_pages[0]) && $search_enabled_pages[0] !== 'all') {
            foreach ($search_enabled_pages as $page) {
                $page_id = !empty((int)$page) ? (int)$page : '';
                if (empty($page_id)) {
                    if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], $page) === false) {
                        $search_enabled = false;
                    }
                } elseif (!is_page($page_id)) {
                    $search_enabled = false;
                }
            }
        }

        return apply_filters('growtype_search_enabled', $search_enabled);
    }
}

/**
 * @param $path
 * @return false|string
 */
if (!function_exists('growtype_search_render_svg')) {
    function growtype_search_render_svg($path)
    {
        $url = GROWTYPE_SEARCH_URL_PUBLIC . $path;

        $arrContextOptions = [
            "ssl" => array (
                "verify_peer" => false,
                "verify_peer_name" => false,
            ),
        ];

        $response = file_get_contents(
            $url,
            false,
            stream_context_create($arrContextOptions)
        );

        return $response;
    }
}

/**
 * @return mixed
 */
if (!function_exists('growtype_search_get_post_types')) {
    function growtype_search_get_post_types()
    {
        $posts = get_theme_mod('growtype_search_post_types_included');

        if (empty($posts) || (is_array($posts) && in_array('all', $posts))) {
            $posts = Growtype_Search_Customizer::get_available_post_types();
        }

        return $posts;
    }
}

/**
 * @param $initial_content
 * @param $length
 * @return mixed|string
 */
if (!function_exists('growtype_search_get_limited_content')) {
    function growtype_search_get_limited_content($initial_content, $length = 125, $html_remove = true)
    {
        if (empty($length)) {
            $length = apply_filters('growtype_search_limited_content_length', 125);
        }

        $content = $initial_content;

        if ($html_remove) {
            $content = strip_tags($content);
        }

        if (strlen($initial_content) > $length) {

            $removed_content = str_replace(substr($content, 0, $length), '', $content);

            if (preg_match("/<[^<]+>/", $removed_content, $m) != 0) {
                $content = strip_shortcodes($content);
                $content = strip_tags($content);
            }

            $content = substr($content, 0, $length);
            $content = substr($content, 0, strripos($content, " "));
            $content = trim(preg_replace('/\s+/', ' ', $content));
            $content = !empty($content) ? $content . '...' : '';
        }

        return $content;
    }
}

/**
 * mainly for ajax translations
 */
if (!function_exists('growtype_search_load_textdomain')) {
    function growtype_search_load_textdomain($lang)
    {
        global $q_config;

        if (isset($q_config['locale'][$lang])) {
            load_textdomain('growtype-search', GROWTYPE_SEARCH_PATH . 'languages/growtype-search-' . $q_config['locale'][$lang] . '.mo');
        }
    }
}

/**
 * Growtype Search Result Content
 */
if (!function_exists('growtype_search_result_content')) {
    function growtype_search_result_content($post)
    {
        $post_title = $post->post_title;
        $post_content = $post->post_content;

        if (!empty($post->post_excerpt)) {
            $post_content = $post->post_excerpt;
        }

        $post_content = growtype_search_format_results_content($post_content);

        ?>
        <div class="title"><?php echo $post_title ?></div>
        <div class="content"><?php echo growtype_search_get_limited_content($post_content) ?></div>
        <?php
    }
}

function growtype_search_format_results_content($content, $max_length = 120)
{
    if (strlen($content) > $max_length) {
        return substr($content, 0, $max_length) . '...';
    }

    return $content;
}

function growtype_search_form_is_fixed($args = [])
{
    if (isset($args['search_type']) && $args['search_type'] === 'fixed') {
        return true;
    }

    return get_theme_mod('growtype_search_type') === 'fixed';
}
