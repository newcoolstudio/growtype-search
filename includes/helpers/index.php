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

/**
 * @return bool
 */
function growtype_search_enabled()
{
    $search_disabled = get_theme_mod('growtype_search_disabled');
    $search_enabled_pages = get_theme_mod('growtype_search_enabled_pages');

    if (!$search_disabled && !empty($search_enabled_pages)) {
        $search_enabled = false;

        if (page_is_among_enabled_pages($search_enabled_pages)) {
            $search_enabled = true;
        }
    }

    return $search_enabled;
}

/**
 * @param $path
 * @return false|string
 */
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

/**
 * @return mixed
 */
function growtype_search_get_post_types()
{
    $posts = get_theme_mod('growtype_search_post_types_included');

    if (empty($posts) || (is_array($posts) && in_array('all', $posts))) {
        $posts = Growtype_Search_Customizer::get_available_post_types();
    }

    return $posts;
}

/**
 * @param $initial_content
 * @param $length
 * @return mixed|string
 */
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

/**
 * mainly for ajax translations
 */
if (!function_exists('growtype_search_load_textdomain')) {
    function growtype_search_load_textdomain($lang)
    {
        load_textdomain('growtype-search', GROWTYPE_SEARCH_PATH . 'languages/growtype-search-' . $lang . '_LT.mo');
    }
}
