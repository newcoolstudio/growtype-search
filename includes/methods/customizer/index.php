<?php

/**
 * Setup
 */
class Growtype_Search_Customizer
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->customizer_available_pages = $this->get_available_pages();
        $this->customizer_available_post_types = $this->get_available_post_types();

        add_action('customize_register', array ($this, 'customizer_init'));
    }

    /**
     * Pages
     */
    function get_available_pages()
    {
        $customizer_available_pages = [];
        $available_pages = get_pages();

        if (!empty($available_pages)) {
            foreach ($available_pages as $single_page) {
                $customizer_available_pages[$single_page->ID] = $single_page->post_title . ' (' . $single_page->post_name . ')';
            }
        }

        if (class_exists('woocommerce')) {
            $customizer_available_pages['single_shop_page'] = 'Single shop page (important: no id)';

            $wc_menu_items = wc_get_account_menu_items();

            foreach ($wc_menu_items as $key => $menu_item) {
                $customizer_available_pages[$key] = 'Account - ' . $menu_item;
            }
        }

        $customizer_available_pages['lost_password_page'] = 'Lost password page (important: no id)';
        $customizer_available_pages['search_results'] = 'Search results (important: no id)';

        /**
         * External pages
         */
        $customizer_available_pages = apply_filters('growtype_search_customizer_extend_available_pages', $customizer_available_pages);

        return $customizer_available_pages;
    }

    /**
     * Post types
     */
    public static function get_available_post_types()
    {
        $all_post_types = get_post_types([
            'public' => true
        ]);

        $post_types = [];
        foreach ($all_post_types as $key => $post_type) {
            $post_types[$key] = $post_type;
        }

        return $post_types;
    }

    function customizer_init($wp_customize)
    {
        require_once GROWTYPE_SEARCH_PATH . 'includes/methods/customizer/components/multiselect.php';

        /**
         * Header section initialize
         */
        $wp_customize->add_section('growtype_search', array (
            "title" => __("Growtype - Search", "growtype-search"),
            "priority" => 100,
        ));

        /**
         *
         */
        $wp_customize->add_setting('growtype_search_enabled',
            array (
                'default' => 1,
                'transport' => 'refresh',
            )
        );

        $wp_customize->add_control('growtype_search_enabled',
            array (
                'label' => esc_html__('Enabled'),
                'type' => 'checkbox',
                'description' => __('Enable/disable search.', 'growtype-search'),
                'section' => 'growtype_search',
            )
        );

        /**
         * Search style
         */
        $wp_customize->add_setting('growtype_search_style',
            array (
                'default' => 'fixed',
                'transport' => 'refresh',
            )
        );

        $wp_customize->add_control('growtype_search_style',
            array (
                'label' => __('Search Style', 'growtype-search'),
                'description' => esc_html__('Choose search style', 'growtype-search'),
                'section' => 'growtype_search',
                'type' => 'select',
                'default' => 'inline',
                'choices' => array (
                    'inline' => __('Inline', 'growtype'),
                    'fixed' => __('Fixed', 'growtype')
                )
            )
        );

        /**
         * Search post types
         */
        $wp_customize->add_setting('growtype_search_post_types_enabled',
            array (
                'default' => 'all',
                'transport' => 'refresh',
            )
        );

        $wp_customize->add_control(
            new Growtype_Search_Multiple_Select(
                $wp_customize,
                'growtype_search_post_types_enabled',
                array (
                    'label' => __('Post types included', 'growtype'),
                    'description' => esc_html__('In which post types search should be conducted.', 'growtype'),
                    'section' => 'growtype_search',
                    'type' => 'select',
                    'choices' => array_merge(['all' => 'All'], $this->customizer_available_post_types)
                )
            )
        );

        /**
         * Search pages
         */
        $wp_customize->add_setting('search_enabled_pages',
            array (
                'default' => 'all',
                'transport' => 'refresh',
            )
        );

        $wp_customize->add_control(
            new Growtype_Search_Multiple_Select(
                $wp_customize, 'search_enabled_pages',
                array (
                    'label' => __('Pages', 'growtype'),
                    'description' => esc_html__('In which pages search available.', 'growtype'),
                    'section' => 'growtype_search',
                    'type' => 'select',
                    'choices' => array_merge(['all' => 'All'], $this->customizer_available_pages)
                )
            )
        );

        /**
         * Ajax search
         */
        $wp_customize->add_setting('growtype_search_ajax_disabled',
            array (
                'default' => 0,
                'transport' => 'refresh',
            )
        );

        $wp_customize->add_control('growtype_search_ajax_disabled',
            array (
                'label' => esc_html__('Ajax Disabled'),
                'type' => 'checkbox',
                'description' => __('Enable/disable ajax search.', 'growtype-search'),
                'section' => 'growtype_search',
            )
        );

        /**
         * Intro text
         */
        $wp_customize->add_setting('growtype_search_intro_text',
            array (
                'default' => 'What are you looking for?',
            )
        );

        $wp_customize->add_control('growtype_search_intro_text',
            array (
                'label' => __('Intro Text', 'growtype-search'),
                'description' => __('This is intro text form search input.'),
                'section' => 'growtype_search',
                'type' => 'text'
            )
        );
    }
}

new Growtype_Search_Customizer();
