function searchBtn() {
    jQuery(document).ready(function ($) {
        jQuery('.btn-growtype-search-open').click(function () {
            openSearch(jQuery(this))
        });

        jQuery('.btn-growtype-search-close').click(function (e) {
            e.preventDefault();
            closeSearch(jQuery(this))
        });
    });

    function openSearch(element) {
        let id = element.attr('data-search-id');
        let wrapper = $('#' + id);

        if (!wrapper.length) {
            wrapper = element.parent().find('.growtype-search-wrapper');
        }

        if (wrapper.is(':visible')) {
            element.addClass('is-active');
            wrapper.fadeOut();
        } else {
            element.removeClass('is-active');
            wrapper.fadeIn();
            wrapper.find('.growtype-search-input').focus();
        }
    }

    function closeSearch(element) {
        element.closest('.growtype-search-wrapper').fadeOut();
    }

    document.addEventListener('growtypeHeaderFixedLoaded', function (params) {
        Object.entries(params.detail.clonedScrollableElements).map(function (element) {
            element[1].find('.btn-growtype-search-open').click(function () {
                openSearch(jQuery(this))
            });
            element[1].find('.btn-growtype-search-close').click(function () {
                closeSearch(jQuery(this))
            });
        })
    })
}

export {searchBtn};
