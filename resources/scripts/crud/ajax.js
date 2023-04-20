function ajax() {
    let searchOnEmptyInput = false;

    Object.entries(window.growtypeSearch).map(function (element) {
        let searchId = element[0];
        let searchForm = $('#' + searchId + ' .growtype-search-form');

        let is_clicked = true;
        searchForm.find('.btn-growtype-search-submit').on('click', function (event) {
            event.preventDefault();
            if (is_clicked == true) {
                is_clicked = false;
                setTimeout(function () {
                    is_clicked = true;
                }, 2500);

                if (element[1]['static']['search_on_empty'] === 'true') {
                    searchOnEmptyInput = true;
                }

                ajax_search(searchForm, element[1]);
            }
        });

        if (element[1]['static']['search_on_load'] === 'true') {
            searchOnEmptyInput = true;
            ajax_search(searchForm, element[1]);
        }

        showMoreResults(searchForm);
    });

    function ajax_search(form, settings = null) {
        let searchInput = form.find('.growtype-search-input');
        let searchInputVal = searchInput.val();

        let hasError = false;
        if (searchOnEmptyInput) {
            hasError = false;
        } else if (searchInputVal.length === 0) {
            hasError = true;
        }

        if (hasError) {
            searchInput.addClass('is-error');
            return;
        }

        searchInput.removeClass('is-error');

        form.closest('.growtype-search-wrapper').removeClass('is-loading');
        form.closest('.growtype-search-wrapper').find('.growtype-search-results').fadeOut();
        form.closest('.growtype-search-wrapper').find('.growtype-search-results-actions').fadeOut();

        /**
         * Ga send search value
         */
        if (typeof gtag !== 'undefined') {
            gtag('event', 'search', {'event_category': 'search', 'event_label': searchInputVal});
        }

        $.ajax({
            type: "post",
            context: this,
            dataType: "json",
            url: growtype_search_ajax.url,
            data: {
                action: growtype_search_ajax.action,
                nonce: growtype_search_ajax.nonce,
                search: searchInputVal,
                visible_results_amount: settings['static']['visible_results_amount'] ?? '',
            },
            beforeSend() {
                $('.growtype-search-wrapper').addClass('is-loading');
            },
            success: function (response) {
                searchOnEmptyInput = false;

                form.closest('.growtype-search-wrapper').removeClass('is-loading');
                form.closest('.growtype-search-wrapper').removeClass('is-loading');
                form.closest('.growtype-search-wrapper').find('.growtype-search-results').fadeIn();

                let html = $(response['html'])

                let showActions = false;
                if (settings['static']['visible_results_amount']) {
                    let counter = 0;
                    html.map(function (index, element) {
                        if (element.innerHTML !== undefined) {
                            if (counter >= settings['static']['visible_results_amount']) {
                                $(element).addClass('initialy-is-hidden').hide();
                                showActions = true;
                            }

                            counter++;
                        }
                    });
                }

                if (showActions) {
                    form.closest('.growtype-search-wrapper').find('.growtype-search-results-actions').fadeIn();
                }

                form.closest('.growtype-search-wrapper').find('.growtype-search-results').html(html);
            }
        });
    }

    /**
     *
     */
    function showMoreResults(form) {
        form.closest('.growtype-search-wrapper').find('.growtype-search-results-actions .growtype-search-results-btn').click(function () {
            if ($(this).hasClass('is-active')) {
                $(this).removeClass('is-active')
                $(this).text($(this).attr('data-show-more'));
                $(this).closest('.growtype-search-wrapper').find('.growtype-search-results .initialy-is-hidden').fadeOut();
            } else {
                $(this).addClass('is-active')
                $(this).text($(this).attr('data-show-less'));
                $(this).closest('.growtype-search-wrapper').find('.growtype-search-results .initialy-is-hidden').fadeIn();
            }
        });
    }
}

export {ajax};
