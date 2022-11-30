function ajax() {
    let is_clicked = true;
    $('.btn-growtype-search-submit').on('click', function (event) {
        event.preventDefault();
        if (is_clicked == true) {
            is_clicked = false;
            setTimeout(function () {
                is_clicked = true;
            }, 2500);
            ajax_search($(this).closest('.growtype-search-form'));
        }
    });

    function ajax_search(form) {
        var searchInput = form.find('.growtype-search-input');

        searchInput.removeClass('is-error');

        $('.growtype-search-wrapper').removeClass('is-loading');

        $('.growtype-search-results').fadeOut();

        if (searchInput.val().length === 0) {
            searchInput.addClass('is-error');
            return;
        }

        $.ajax({
            type: "post",
            context: this,
            dataType: "json",
            url: growtype_search_ajax.url,
            data: {
                action: growtype_search_ajax.action,
                nonce: growtype_search_ajax.nonce,
                search: searchInput.val(),
            },
            beforeSend() {
                $('.growtype-search-wrapper').addClass('is-loading');
            },
            success: function (response) {
                $('.growtype-search-wrapper').removeClass('is-loading');
                $('.growtype-search-results').fadeIn();
                $('.growtype-search-results').html(response['html']);
            }
        });
    }
}

export {ajax};
