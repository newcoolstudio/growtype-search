function searchBtn() {
    $('.btn-growtype-search-open').click(function () {
        if ($('.growtype-search-wrapper').is(':visible')) {
            $(this).addClass('is-active');
            $('.growtype-search-wrapper').fadeOut();
        } else {
            $(this).removeClass('is-active');
            $('.growtype-search-wrapper').fadeIn();
            $('.growtype-search-input').focus();
        }
    });

    $('.btn-growtype-search-close').click(function (e) {
        e.preventDefault();
        $('.growtype-search-wrapper').fadeOut();
    });
}

export {searchBtn};
