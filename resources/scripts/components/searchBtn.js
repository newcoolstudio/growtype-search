function searchBtn() {
    jQuery('.btn-growtype-search-open').click(function () {
        if (jQuery('.growtype-search-wrapper').is(':visible')) {
            jQuery(this).addClass('is-active');
            jQuery('.growtype-search-wrapper').fadeOut();
        } else {
            jQuery(this).removeClass('is-active');
            jQuery('.growtype-search-wrapper').fadeIn();
            jQuery('.growtype-search-input').focus();
        }
    });

    jQuery('.btn-growtype-search-close').click(function (e) {
        e.preventDefault();
        jQuery('.growtype-search-wrapper').fadeOut();
    });
}

export {searchBtn};
