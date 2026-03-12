<?php

class Growtype_Search_Admin_Settings
{
    public function __construct()
    {
        $this->load_tabs();

        add_action('admin_menu', array ($this, 'admin_menu_pages'));
        add_action('init', array ($this, 'process_posted_data'));
    }

    /**
     * Register the options page with the Wordpress menu.
     */
    function admin_menu_pages()
    {
        /**
         * Options
         */
        add_options_page(
            'Growtype - Search',
            'Growtype - Search',
            'manage_options',
            Growtype_Search_Admin::SETTINGS_PAGE_NAME,
            array ($this, 'options_page_content'),
            1
        );
    }

    /**
     * @return void
     */
    function options_page_content()
    {
        if (isset($_GET['page']) && $_GET['page'] == Growtype_Search_Admin::SETTINGS_PAGE_NAME) { ?>

            <div class="wrap">

                <h1>Growtype Search - Settings</h1>

                <?php
                if (isset($_GET['updated']) && 'true' == esc_attr($_GET['updated'])) {
                    echo '<div class="updated" ><p>Theme Settings Updated.</p></div>';
                }

                if (isset ($_GET['tab'])) {
                    $this->render_settings_tab_render($_GET['tab']);
                } else {
                    $this->render_settings_tab_render();
                }
                ?>

                <form method="post" action="options.php">
                    <?php

                    if (isset ($_GET['tab'])) {
                        $tab = $_GET['tab'];
                    } else {
                        $tab = Growtype_Search_Admin::SETTINGS_DEFAULT_TAB;
                    }

                    switch ($tab) {
                        case 'general':
                            settings_fields('growtype_search_settings_general');

                            echo '<h3>General settings</h3>';

                            echo '<table class="form-table">';
                            do_settings_fields(Growtype_Search_Admin::SETTINGS_PAGE_NAME, 'growtype_search_general_settings');
                            echo '</table>';

                            break;
                        case 'stats':
                            settings_fields('growtype_search_settings_search');

                            do_action('growtype_search_admin_settings_search_content');

                            break;
                        case 'encryption':

                            settings_fields('growtype_search_settings_encryption');

                            echo '<h3>Encryption Settings</h3>';

                            // Render the re-encryption tool form here
                            do_action('growtype_search_admin_settings_encryption_content');

                            break;
                    }

                    if (!in_array($tab, ['encryption'])) {
                        submit_button();
                    }

                    ?>
                </form>
            </div>

            <?php
        }
    }

    function process_posted_data()
    {
        if (isset($_POST) && !empty($_POST)) {

        }
    }

    function settings_tabs()
    {
        return apply_filters('growtype_search_admin_settings_tabs', []);
    }

    function render_settings_tab_render($current = Growtype_Search_Admin::SETTINGS_DEFAULT_TAB)
    {
        $tabs = $this->settings_tabs();

        echo '<div id="icon-themes" class="icon32"><br></div>';
        echo '<h2 class="nav-tab-wrapper">';
        foreach ($tabs as $tab => $name) {
            $class = ($tab == $current) ? ' nav-tab-active' : '';
            echo "<a class='nav-tab$class' href='?page=growtype-search-settings&tab=$tab'>$name</a>";

        }
        echo '</h2>';
    }

    public function load_tabs()
    {
        include_once GROWTYPE_SEARCH_PATH . 'admin/pages/settings/tabs/growtype-search-admin-settings-general.php';
        new Growtype_Search_Admin_Settings_General();

        include_once GROWTYPE_SEARCH_PATH . 'admin/pages/settings/tabs/growtype-search-admin-settings-search.php';
        new Growtype_Search_Admin_Settings_Search();
    }
}
