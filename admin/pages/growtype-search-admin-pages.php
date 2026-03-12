<?php

class Growtype_Search_Admin_Pages
{
    public function __construct()
    {
        $this->load_pages();
    }

    public function load_pages()
    {
        /**
         * Settings
         */
        require_once GROWTYPE_SEARCH_PATH . 'admin/pages/settings/growtype-search-admin-settings.php';
        new Growtype_Search_Admin_Settings();
    }
}
