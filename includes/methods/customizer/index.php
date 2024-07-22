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
        $this->customizer_available_pages = ['all' => 'All'] + $this->get_available_pages();

        $this->block_attributes = json_decode(file_get_contents(GROWTYPE_SEARCH_PATH . 'src/block.json'), true)["attributes"] ?? [];

        add_action('customize_register', array ($this, 'customizer_init'));

        add_action('after_setup_theme', function () {
            $this->customizer_available_post_types = ['any' => 'Any'] + $this->get_available_post_types();
        });
    }

    /**
     * @param $options
     * @return array
     * Extract correct form options
     */
    function format_block_attribute_options($options)
    {
        $attributes = [];

        foreach ($options as $key => $attribute) {
            $attributes[$attribute['value']] = $attribute["label"];
        }

        return $attributes;
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

        if (class_exists('Growtype_Extended_Cpt')) {
            $active_pt = Growtype_Extended_Cpt::get_active_post_types();

            if (!empty($active_pt)) {
                foreach ($active_pt as $pt) {
                    $post_types[$pt['value']] = $pt['label'];
                }
            }
        }

        /**
         * WooCommerce
         */
        if (class_exists('WooCommerce') && !in_array('product', $post_types)) {
            $post_types['product'] = 'product';
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
        $wp_customize->add_setting('growtype_search_disabled',
            array (
                'default' => 0,
                'transport' => 'refresh',
            )
        );

        $wp_customize->add_control('growtype_search_disabled',
            array (
                'label' => __('Initially hide search', 'growtype-search'),
                'type' => 'checkbox',
                'description' => __('Hide/show search initially.', 'growtype-search'),
                'section' => 'growtype_search',
            )
        );

        /**
         * Search style
         */
        $wp_customize->add_setting('growtype_search_type',
            array (
                'default' => 'inline',
                'transport' => 'refresh',
            )
        );

        $wp_customize->add_control('growtype_search_type',
            array (
                'label' => __('Search Type', 'growtype-search'),
                'description' => esc_html__('Choose search type', 'growtype-search'),
                'section' => 'growtype_search',
                'type' => 'select',
                'choices' => $this->format_block_attribute_options($this->block_attributes['search_type']['options']),
            )
        );

        /**
         * Search post types
         */
        $wp_customize->add_setting('growtype_search_post_types_included',
            array (
                'default' => 'any',
                'transport' => 'refresh',
            )
        );

        $wp_customize->add_control(
            new Growtype_Search_Multiple_Select(
                $wp_customize,
                'growtype_search_post_types_included',
                array (
                    'label' => __('Post types included', 'growtype'),
                    'description' => esc_html__('In which post types search should be conducted.', 'growtype'),
                    'section' => 'growtype_search',
                    'type' => 'select',
                    'choices' => $this->customizer_available_post_types
                )
            )
        );

        /**
         * Search pages
         */
        $wp_customize->add_setting('growtype_search_enabled_pages',
            array (
                'default' => 'all',
                'transport' => 'refresh',
            )
        );


        $wp_customize->add_control(
            new Growtype_Search_Multiple_Select(
                $wp_customize, 'growtype_search_enabled_pages',
                array (
                    'label' => __('Search Enabled Pages', 'growtype-search'),
                    'description' => __('In which pages search available.', 'growtype-search'),
                    'section' => 'growtype_search',
                    'type' => 'select',
                    'choices' => $this->customizer_available_pages
                )
            )
        );

        /**
         * Search results
         */
        $wp_customize->add_setting('growtype_search_show_results_in_modal',
            array (
                'default' => 0,
                'transport' => 'refresh',
            )
        );

        $wp_customize->add_control('growtype_search_show_results_in_modal',
            array (
                'label' => esc_html__('Results in Modal'),
                'type' => 'checkbox',
                'description' => __('Show search results in modal window.', 'growtype-search'),
                'section' => 'growtype_search',
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
                'description' => __('Disable ajax search.', 'growtype-search'),
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
