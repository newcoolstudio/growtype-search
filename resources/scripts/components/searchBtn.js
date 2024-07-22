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
        if (element.parent().find('.growtype-search-wrapper').is(':visible')) {
            element.addClass('is-active');
            element.parent().find('.growtype-search-wrapper').fadeOut();
        } else {
            element.removeClass('is-active');
            element.parent().find('.growtype-search-wrapper').fadeIn();
            element.parent().find('.growtype-search-input').focus();
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
